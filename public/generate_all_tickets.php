<?php
/**
 * GENERAR TODOS LOS BOLETOS COMO ARCHIVOS HTML
 * Crea archivos HTML individuales para cada boleto en la carpeta /uploads/boletos/
 */

require_once __DIR__ . '/../config/database.php';

$eventId = (int)($_GET['event_id'] ?? 6);

// Create PDO
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

// Get all courtesy tickets
$stmt = $pdo->prepare("
    SELECT 
        er.id,
        er.registration_code,
        er.guest_name,
        er.guest_email,
        er.guest_phone,
        e.title as event_name,
        DATE_FORMAT(e.start_date, '%d/%m/%Y') as event_date,
        DATE_FORMAT(e.start_date, '%H:%i') as event_time,
        e.location as event_location,
        COALESCE(
            NULLIF(er.guest_company, ''),
            c.business_name,
            'Invitado Especial'
        ) as business_name,
        COALESCE(mt.name, 'Cortesía') as membership_type
    FROM event_registrations er
    INNER JOIN events e ON er.event_id = e.id
    LEFT JOIN contacts c ON er.contact_id = c.id
    LEFT JOIN membership_types mt ON c.membership_type_id = mt.id
    WHERE er.event_id = :event_id
      AND er.payment_status = 'courtesy'
      AND er.is_courtesy_ticket = 1
    ORDER BY er.guest_name
");

$stmt->execute(['event_id' => $eventId]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($tickets)) {
    die('No se encontraron boletos de cortesía para este evento');
}

// Create directory for tickets
$ticketsDir = __DIR__ . '/uploads/boletos_evento_' . $eventId;
if (!file_exists($ticketsDir)) {
    mkdir($ticketsDir, 0755, true);
}

// Generate each ticket HTML file
$generatedFiles = [];
foreach ($tickets as $ticket) {
    $qrData = "https://enlacecanaco.org/crmtotal/evento/verificar/" . $ticket['registration_code'];
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
    
    $html = generateTicketHTML($ticket, $qrUrl);
    
    $filename = sanitizeFilename($ticket['guest_name']) . '_' . $ticket['registration_code'] . '.html';
    $filepath = $ticketsDir . '/' . $filename;
    
    file_put_contents($filepath, $html);
    $generatedFiles[] = [
        'filename' => $filename,
        'name' => $ticket['guest_name'],
        'code' => $ticket['registration_code']
    ];
}

// Generate index HTML with links to all tickets
$indexHtml = generateIndexHTML($generatedFiles, $eventId, count($tickets));
file_put_contents($ticketsDir . '/index.html', $indexHtml);

// Show success message
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Boletos Generados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .ticket-list {
            list-style: none;
            padding: 0;
        }
        .ticket-list li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="success">
        <h2>✅ Boletos Generados Exitosamente</h2>
        <p><strong><?php echo count($generatedFiles); ?> boletos</strong> han sido generados en la carpeta:</p>
        <code><?php echo basename($ticketsDir); ?></code>
    </div>
    
    <div class="info">
        <h3>📂 Cómo acceder a los boletos:</h3>
        <p><strong>Opción 1:</strong> Abre el archivo <code>index.html</code> dentro de la carpeta para ver todos los enlaces.</p>
        <p><strong>Opción 2:</strong> Navega directamente a la carpeta en tu servidor:</p>
        <p><a href="uploads/boletos_evento_<?php echo $eventId; ?>/index.html" class="btn btn-success" target="_blank">
            📋 Ver Índice de Boletos
        </a></p>
    </div>
    
    <h3>📄 Boletos Generados:</h3>
    <ul class="ticket-list">
        <?php foreach ($generatedFiles as $file): ?>
        <li>
            <span><strong><?php echo htmlspecialchars($file['name']); ?></strong> (<?php echo $file['code']; ?>)</span>
            <a href="uploads/boletos_evento_<?php echo $eventId; ?>/<?php echo $file['filename']; ?>" 
               class="btn" target="_blank">Ver Boleto</a>
        </li>
        <?php endforeach; ?>
    </ul>
    
    <p style="margin-top: 30px;">
        <a href="list_courtesy_emails.php" class="btn">← Volver a Lista</a>
    </p>
</body>
</html>
<?php

function sanitizeFilename($name) {
    $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    return substr($name, 0, 50);
}

function generateIndexHTML($files, $eventId, $total) {
    ob_start();
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Índice de Boletos - Evento <?php echo $eventId; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #667eea;
            text-align: center;
        }
        .stats {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stats h2 {
            margin: 0;
            color: #10b981;
        }
        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .ticket-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .ticket-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .ticket-code {
            font-family: monospace;
            color: #667eea;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .btn-open {
            display: block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 12px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-open:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <h1>🎫 Boletos de Cortesía - Evento <?php echo $eventId; ?></h1>
    
    <div class="stats">
        <h2><?php echo $total; ?> Boletos Generados</h2>
        <p>Haz clic en cualquier boleto para abrirlo e imprimirlo</p>
    </div>
    
    <div class="ticket-grid">
        <?php foreach ($files as $file): ?>
        <div class="ticket-card">
            <div class="ticket-name"><?php echo htmlspecialchars($file['name']); ?></div>
            <div class="ticket-code"><?php echo $file['code']; ?></div>
            <a href="<?php echo $file['filename']; ?>" class="btn-open" target="_blank">
                📄 Abrir Boleto
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
    <?php
    return ob_get_clean();
}

function generateTicketHTML($ticket, $qrUrl) {
    ob_start();
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Boleto - <?php echo htmlspecialchars($ticket['guest_name']); ?></title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
        }
        .ticket {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            text-align: center;
            color: white;
        }
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
        }
        .qr-section {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .qr-section img {
            border: 5px solid white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .qr-section .code {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin-top: 15px;
            font-family: monospace;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .info-item .label {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }
        .info-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .courtesy-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        .instructions {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .instructions h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        .instructions p {
            color: #856404;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
        .print-button {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
        }
        .btn-print {
            background: #667eea;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-print:hover {
            background: #5568d3;
        }
        @media print {
            body {
                padding: 0;
                background: white;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimir / Guardar como PDF</button>
    </div>
    
    <div class="ticket">
        <div class="header">
            <h1>🎫 Boleto de Acceso</h1>
            <p><?php echo htmlspecialchars($ticket['event_name']); ?></p>
        </div>
        
        <div class="content">
            <div class="qr-section">
                <img src="<?php echo $qrUrl; ?>" alt="QR Code" width="300" height="300">
                <div class="code"><?php echo htmlspecialchars($ticket['registration_code']); ?></div>
            </div>
            
            <div class="courtesy-badge">
                ✅ Boleto de Cortesía - Sin Cargo
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Nombre</div>
                    <div class="value"><?php echo htmlspecialchars($ticket['guest_name']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="label">Email</div>
                    <div class="value"><?php echo htmlspecialchars($ticket['guest_email']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="label">Fecha</div>
                    <div class="value"><?php echo $ticket['event_date']; ?></div>
                </div>
                
                <div class="info-item">
                    <div class="label">Hora</div>
                    <div class="value"><?php echo $ticket['event_time']; ?> hrs</div>
                </div>
                
                <div class="info-item" style="grid-column: span 2;">
                    <div class="label">Lugar</div>
                    <div class="value"><?php echo htmlspecialchars($ticket['event_location']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="label">Empresa</div>
                    <div class="value"><?php echo htmlspecialchars($ticket['business_name']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="label">Tipo</div>
                    <div class="value"><?php echo htmlspecialchars($ticket['membership_type']); ?></div>
                </div>
            </div>
            
            <div class="instructions">
                <h3>📋 Instrucciones:</h3>
                <p>
                    1. Presente este código QR al ingresar al evento<br>
                    2. Puede mostrarlo desde su dispositivo móvil o impreso<br>
                    3. Llegue 15 minutos antes del inicio del evento<br>
                    4. Este boleto es personal e intransferible
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>ENLACE CA - Cámara de Comercio de Querétaro</p>
            <p>Para cualquier duda, contacte a: info@enlacecanaco.org</p>
        </div>
    </div>
</body>
</html>
    <?php
    return ob_get_clean();
}
?>
