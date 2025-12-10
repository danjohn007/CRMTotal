<?php
/**
 * ENVÍO DE BOLETOS DE CORTESÍA - EVENTO 6
 * Interfaz con vista previa y confirmación de envío
 */

require_once __DIR__ . '/../config/database.php';

// Create PDO connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        DB_OPTIONS
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Load SMTP configuration from database
function getSmtpConfig($pdo) {
    $smtp_keys = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'smtp_from_name', 'smtp_secure'];
    $config = [];
    
    foreach ($smtp_keys as $key) {
        $stmt = $pdo->prepare("SELECT config_value FROM config WHERE config_key = ?");
        $stmt->execute([$key]);
        $config[$key] = $stmt->fetchColumn();
    }
    
    return $config;
}

$smtp_config = getSmtpConfig($pdo);

// SMTP Email sending function
function sendSmtpEmail(string $to, string $subject, string $body, string $host, int $port, string $user, string $password, string $fromName, string $secure = 'tls'): array {
    try {
        $protocol = ($secure === 'ssl') ? 'ssl://' : '';
        $connectionString = $protocol . $host;
        
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client(
            "$connectionString:$port",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            return ['success' => false, 'error' => "Conexión fallida: $errstr"];
        }
        
        // Read greeting (may be multiple lines)
        while ($line = fgets($socket, 515)) {
            if (strlen($line) >= 4 && $line[3] === ' ') break;
        }
        
        // EHLO
        fputs($socket, "EHLO " . $host . "\r\n");
        while ($line = fgets($socket, 515)) {
            if (strlen($line) >= 4 && $line[3] === ' ') break;
        }
        
        // STARTTLS for port 587
        if ($port == 587 && $secure === 'tls') {
            fputs($socket, "STARTTLS\r\n");
            fgets($socket, 515);
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            fputs($socket, "EHLO " . $host . "\r\n");
            while ($line = fgets($socket, 515)) {
                if (strlen($line) >= 4 && $line[3] === ' ') break;
            }
        }
        
        // AUTH PLAIN (compatible with your server)
        $auth_string = base64_encode("\0" . $user . "\0" . $password);
        fputs($socket, "AUTH PLAIN $auth_string\r\n");
        $auth_response = fgets($socket, 515);
        
        if (strpos($auth_response, '235') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'Autenticación fallida: ' . trim($auth_response)];
        }
        
        // MAIL FROM
        fputs($socket, "MAIL FROM: <$user>\r\n");
        fgets($socket, 515);
        
        // RCPT TO
        fputs($socket, "RCPT TO: <$to>\r\n");
        fgets($socket, 515);
        
        // DATA
        fputs($socket, "DATA\r\n");
        fgets($socket, 515);
        
        // Build message (exactly like EventsController)
        $message = "From: $fromName <$user>\r\n";
        $message .= "To: $to\r\n";
        $message .= "Subject: $subject\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Date: " . date('r') . "\r\n\r\n";
        $message .= $body . "\r\n";
        $message .= ".\r\n";
        
        fputs($socket, $message);
        fgets($socket, 515);
        
        // QUIT
        fputs($socket, "QUIT\r\n");
        fgets($socket, 515);
        
        fclose($socket);
        
        return ['success' => true];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Handle AJAX send request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_emails') {
    header('Content-Type: application/json');
    
    $sent_count = 0;
    $error_count = 0;
    $errors = [];
    
    try {
        // Get all courtesy tickets
        $stmt = $pdo->prepare("
            SELECT 
                er.id,
                er.registration_code,
                er.guest_name,
                'danielhrzz970@gmail.com' as guest_email,
                er.guest_phone,
                e.title as event_name,
                DATE_FORMAT(e.start_date, '%d/%m/%Y %H:%i') as event_date,
                e.location as event_location,
                COALESCE(c.business_name, 'Invitado Especial') as business_name,
                COALESCE(mt.name, 'Cortesía') as membership_type
            FROM event_registrations er
            INNER JOIN events e ON er.event_id = e.id
            LEFT JOIN contacts c ON er.contact_id = c.id
            LEFT JOIN membership_types mt ON c.membership_type_id = mt.id
            WHERE er.event_id = 6 
              AND er.payment_status = 'courtesy'
              AND er.is_courtesy_ticket = 1
            ORDER BY 
                CASE WHEN c.id IS NULL THEN 1 ELSE 0 END,
                mt.name, 
                er.guest_name
            LIMIT 1
        ");
        
        $stmt->execute();
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($tickets as $ticket) {
            // Validate email
            if (empty($ticket['guest_email']) || !filter_var($ticket['guest_email'], FILTER_VALIDATE_EMAIL)) {
                $error_count++;
                $errors[] = "{$ticket['guest_name']}: Email inválido";
                continue;
            }
            
            // Generate QR code URL
            $qr_url = "https://enlaceca.org/events/verify?code=" . urlencode($ticket['registration_code']);
            
            // Determine if member
            $is_member = ($ticket['membership_type'] !== 'Cortesía');
            $greeting = $is_member 
                ? "Como miembro de <strong>{$ticket['membership_type']}</strong>, tiene derecho a un <strong>boleto de cortesía</strong> para nuestro evento:"
                : "Nos complace confirmar su <strong>boleto de cortesía</strong> para nuestro evento:";
            
            // Prepare email content
            $subject = "🎫 Boleto Cortesía - {$ticket['event_name']}";
            
            $message = "
            <html>
            <head><meta charset='UTF-8'></head>
            <body style='font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #2c3e50;'>¡Bienvenido al Evento!</h2>
                    
                    <p>Estimado/a <strong>{$ticket['guest_name']}</strong>,</p>
                    
                    <p>{$greeting}</p>
                    
                    <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <h3 style='margin-top: 0;'>{$ticket['event_name']}</h3>
                        <p><strong>📅 Fecha:</strong> {$ticket['event_date']}</p>
                        <p><strong>📍 Lugar:</strong> {$ticket['event_location']}</p>
                        <p><strong>🎫 Código:</strong> {$ticket['registration_code']}</p>
                        " . ($is_member ? "<p><strong>💼 Empresa:</strong> {$ticket['business_name']}</p>" : "") . "
                    </div>
                    
                    <p>Su código QR para acceder al evento:</p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <img src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$qr_url}' 
                             alt='QR Code' style='border: 2px solid #ddd; padding: 10px;'>
                    </div>
                    
                    <p style='background: #d4edda; padding: 10px; border-radius: 5px; border-left: 4px solid #28a745;'>
                        ✅ <strong>Boleto Cortesía</strong> - Sin cargo" . ($is_member ? " (beneficio de membresía)" : "") . "
                    </p>
                    
                    <p>Por favor, presente este código QR al ingresar al evento.</p>
                    
                    <p style='color: #6c757d; font-size: 12px; margin-top: 30px;'>
                        Si tiene alguna pregunta, no dude en contactarnos.<br>
                        ENLACE CA - Cámara de Comercio
                    </p>
                </div>
            </body>
            </html>
            ";
            
            // Send email using SMTP
            $email_result = sendSmtpEmail(
                $ticket['guest_email'],
                $subject,
                $message,
                $smtp_config['smtp_host'],
                (int)$smtp_config['smtp_port'],
                $smtp_config['smtp_user'],
                $smtp_config['smtp_password'],
                $smtp_config['smtp_from_name'],
                $smtp_config['smtp_secure'] ?: 'tls'
            );
            
            if ($email_result['success']) {
                $sent_count++;
            } else {
                $error_count++;
                $errors[] = "{$ticket['guest_name']}: {$email_result['error']}";
            }
            
            usleep(500000); // 0.5 seconds delay
        }
        
        echo json_encode([
            'success' => true,
            'sent_count' => $sent_count,
            'error_count' => $error_count,
            'errors' => $errors,
            'total' => count($tickets)
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    
    exit;
}

// Get courtesy tickets for preview
try {
    $stmt = $pdo->prepare("
        SELECT 
            er.id,
            er.registration_code,
            er.guest_name,
            er.guest_email,
            er.guest_phone,
            COALESCE(c.business_name, 'Invitado Especial') as business_name,
            COALESCE(mt.name, 'Cortesía') as membership_type
        FROM event_registrations er
        LEFT JOIN contacts c ON er.contact_id = c.id
        LEFT JOIN membership_types mt ON c.membership_type_id = mt.id
        WHERE er.event_id = 6 
          AND er.payment_status = 'courtesy'
          AND er.is_courtesy_ticket = 1
        ORDER BY 
            CASE WHEN c.id IS NULL THEN 1 ELSE 0 END,
            mt.name, 
            er.guest_name
    ");
    
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Boletos de Cortesía - Evento 6</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .summary-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        
        .summary-box h2 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        
        .stat-item .label {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .stat-item .value {
            color: #667eea;
            font-size: 24px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }
        
        thead {
            background: #667eea;
            color: white;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .email-col {
            color: #667eea;
            font-weight: 500;
        }
        
        .membership-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-basica {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-pyme {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .badge-premier {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .badge-cortesia {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .btn {
            padding: 15px 40px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        #loading {
            display: none;
            text-align: center;
            padding: 30px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        #result {
            display: none;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 Envío de Boletos de Cortesía</h1>
            <p>Evento 6 - Sistema de Confirmación</p>
        </div>
        
        <div class="content">
            <div class="summary-box">
                <h2>📊 Resumen de Envío</h2>
                <div class="stats">
                    <div class="stat-item">
                        <div class="label">Total de Boletos</div>
                        <div class="value"><?php echo count($tickets); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label">Membresías BÁSICA</div>
                        <div class="value"><?php echo count(array_filter($tickets, fn($t) => $t['membership_type'] === 'Membresía Básica')); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label">Membresías PYME</div>
                        <div class="value"><?php echo count(array_filter($tickets, fn($t) => $t['membership_type'] === 'Membresía PYME')); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label">Membresías PREMIER</div>
                        <div class="value"><?php echo count(array_filter($tickets, fn($t) => $t['membership_type'] === 'Membresía PREMIER')); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="label">Invitados Especiales</div>
                        <div class="value"><?php echo count(array_filter($tickets, fn($t) => $t['membership_type'] === 'Cortesía')); ?></div>
                    </div>
                </div>
            </div>
            
            <h2 style="margin-bottom: 20px;">📋 Lista de Destinatarios</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Empresa</th>
                        <th>Tipo</th>
                        <th>Código</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $index => $ticket): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($ticket['guest_name']); ?></td>
                        <td class="email-col"><?php echo htmlspecialchars($ticket['guest_email']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['business_name']); ?></td>
                        <td>
                            <?php 
                            $badge_class = 'badge-cortesia';
                            if ($ticket['membership_type'] === 'Membresía Básica') $badge_class = 'badge-basica';
                            if ($ticket['membership_type'] === 'Membresía PYME') $badge_class = 'badge-pyme';
                            if ($ticket['membership_type'] === 'Membresía PREMIER') $badge_class = 'badge-premier';
                            ?>
                            <span class="membership-badge <?php echo $badge_class; ?>">
                                <?php echo htmlspecialchars($ticket['membership_type']); ?>
                            </span>
                        </td>
                        <td style="font-family: monospace; font-size: 12px;">
                            <?php echo htmlspecialchars($ticket['registration_code']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="action-buttons">
                <button id="sendBtn" class="btn btn-primary" onclick="sendEmails()">
                    📧 Enviar Boletos a Todos
                </button>
                <a href="list_courtesy_emails.php" class="btn btn-secondary">
                    📋 Ver Lista de Correos
                </a>
            </div>
            
            <div id="loading">
                <div class="spinner"></div>
                <p><strong>Enviando correos...</strong></p>
                <p>Por favor espere, esto puede tomar unos momentos.</p>
            </div>
            
            <div id="result"></div>
        </div>
    </div>
    
    <script>
        function sendEmails() {
            if (!confirm('¿Está seguro de enviar los boletos a los <?php echo count($tickets); ?> destinatarios?\n\nEsta acción no se puede deshacer.')) {
                return;
            }
            
            document.getElementById('sendBtn').disabled = true;
            document.getElementById('loading').style.display = 'block';
            document.getElementById('result').style.display = 'none';
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=send_emails'
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading').style.display = 'none';
                
                const resultDiv = document.getElementById('result');
                resultDiv.style.display = 'block';
                
                if (data.success) {
                    resultDiv.className = 'success';
                    let html = `
                        <h3>✅ Envío Completado</h3>
                        <p><strong>Enviados exitosamente:</strong> ${data.sent_count} de ${data.total}</p>
                    `;
                    
                    if (data.error_count > 0) {
                        html += `<p><strong>Errores:</strong> ${data.error_count}</p>`;
                        html += '<ul>';
                        data.errors.forEach(error => {
                            html += `<li>${error}</li>`;
                        });
                        html += '</ul>';
                    }
                    
                    resultDiv.innerHTML = html;
                } else {
                    resultDiv.className = 'error';
                    resultDiv.innerHTML = `
                        <h3>❌ Error en el Envío</h3>
                        <p>${data.error}</p>
                    `;
                    document.getElementById('sendBtn').disabled = false;
                }
            })
            .catch(error => {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('sendBtn').disabled = false;
                
                const resultDiv = document.getElementById('result');
                resultDiv.style.display = 'block';
                resultDiv.className = 'error';
                resultDiv.innerHTML = `
                    <h3>❌ Error de Conexión</h3>
                    <p>${error.message}</p>
                `;
            });
        }
    </script>
</body>
</html>
