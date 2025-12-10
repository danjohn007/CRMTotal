<?php
/**
 * RESEND QR CODES FOR EVENT 6 COURTESY TICKETS
 * 
 * This script sends email confirmations with QR codes to the 16 members
 * whose tickets were updated from pending to courtesy status in Event 6.
 */

require_once __DIR__ . '/../config/database.php';

// Email configuration (adjust to your SMTP settings)
$smtp_config = [
    'host' => 'smtp.gmail.com',  // Your SMTP host
    'port' => 587,                // Your SMTP port
    'username' => '',             // Your email
    'password' => '',             // Your email password
    'from_email' => 'noreply@enlaceca.org',
    'from_name' => 'ENLACE CA'
];

// Initialize counters
$sent_count = 0;
$error_count = 0;
$errors = [];

echo "<html><head><meta charset='UTF-8'></head><body>";
echo "<h2>🎫 Reenvío de Boletos Cortesía - Evento 6</h2>";
echo "<p>Enviando confirmaciones a miembros actualizados...</p>";
echo "<hr>";

try {
    // Get all Event 6 courtesy tickets (members AND non-members)
    $stmt = $pdo->prepare("
        SELECT 
            er.id,
            er.registration_code,
            er.guest_name,
            er.guest_email,
            er.guest_phone,
            e.name as event_name,
            e.date as event_date,
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
    ");
    
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Total de boletos a reenviar:</strong> " . count($tickets) . "</p>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>
            <th>#</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Empresa</th>
            <th>Membresía</th>
            <th>Estado</th>
          </tr>";
    
    foreach ($tickets as $index => $ticket) {
        $num = $index + 1;
        
        echo "<tr>";
        echo "<td>{$num}</td>";
        echo "<td>{$ticket['registration_code']}</td>";
        echo "<td>{$ticket['guest_name']}</td>";
        echo "<td>{$ticket['guest_email']}</td>";
        echo "<td>{$ticket['business_name']}</td>";
        echo "<td>{$ticket['membership_type']}</td>";
        
        // Validate email
        if (empty($ticket['guest_email']) || !filter_var($ticket['guest_email'], FILTER_VALIDATE_EMAIL)) {
            echo "<td style='color: red;'>❌ Email inválido</td>";
            $error_count++;
            $errors[] = "Registro {$ticket['registration_code']}: Email inválido";
            echo "</tr>";
            continue;
        }
        
        // Generate QR code URL (adjust to your QR generation logic)
        $qr_url = "https://enlaceca.org/events/verify?code=" . urlencode($ticket['registration_code']);
        
        // Prepare email content
        $subject = "🎫 Boleto Cortesía - {$ticket['event_name']}";
        
        // Determine message based on membership status
        $is_member = ($ticket['membership_type'] !== 'Cortesía');
        $greeting = $is_member 
            ? "Como miembro de <strong>{$ticket['membership_type']}</strong>, tiene derecho a un <strong>boleto de cortesía</strong> para nuestro evento:"
            : "Nos complace confirmar su <strong>boleto de cortesía</strong> para nuestro evento:";
        
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
        
        // Email headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$smtp_config['from_name']} <{$smtp_config['from_email']}>",
            "Reply-To: {$smtp_config['from_email']}"
        ];
        
        // Send email (using PHP mail function - adjust if using PHPMailer/SMTP)
        $email_sent = mail(
            $ticket['guest_email'],
            $subject,
            $message,
            implode("\r\n", $headers)
        );
        
        if ($email_sent) {
            echo "<td style='color: green;'>✅ Enviado</td>";
            $sent_count++;
        } else {
            echo "<td style='color: red;'>❌ Error al enviar</td>";
            $error_count++;
            $errors[] = "Registro {$ticket['registration_code']}: Error SMTP";
        }
        
        echo "</tr>";
        
        // Small delay to avoid SMTP rate limits
        usleep(500000); // 0.5 seconds
    }
    
    echo "</table>";
    
    // Summary
    echo "<hr>";
    echo "<h3>📊 Resumen de Envío</h3>";
    echo "<ul>";
    echo "<li><strong>Total procesados:</strong> " . count($tickets) . "</li>";
    echo "<li style='color: green;'><strong>✅ Enviados exitosamente:</strong> {$sent_count}</li>";
    echo "<li style='color: red;'><strong>❌ Errores:</strong> {$error_count}</li>";
    echo "</ul>";
    
    if (!empty($errors)) {
        echo "<h4 style='color: red;'>Errores Encontrados:</h4>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>{$error}</li>";
        }
        echo "</ul>";
    }
    
    if ($sent_count === count($tickets)) {
        echo "<p style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "✅ <strong>¡Proceso completado exitosamente!</strong> Todos los boletos de cortesía fueron enviados.";
        echo "</p>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "❌ <strong>Error de base de datos:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "</body></html>";
?>
