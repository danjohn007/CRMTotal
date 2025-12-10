<?php
/**
 * DESCARGAR TODOS LOS BOLETOS
 * Genera un archivo ZIP con todos los PDFs de boletos de cortesía
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

// Create temporary directory for PDFs
$tempDir = sys_get_temp_dir() . '/courtesy_tickets_' . time();
mkdir($tempDir);

// Generate each PDF
foreach ($tickets as $ticket) {
    $qrData = "https://enlacecanaco.org/crmtotal/evento/verificar/" . $ticket['registration_code'];
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
    
    // Generate HTML for PDF
    $html = generateTicketHTML($ticket, $qrUrl);
    
    // Save as HTML file (browsers can print to PDF)
    $filename = sanitizeFilename($ticket['guest_name']) . '_' . $ticket['registration_code'] . '.html';
    file_put_contents($tempDir . '/' . $filename, $html);
}

// Create ZIP file
$zipFile = $tempDir . '.zip';
$zip = new ZipArchive();

if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
    $files = glob($tempDir . '/*.html');
    foreach ($files as $file) {
        $zip->addFile($file, basename($file));
    }
    $zip->close();
    
    // Send ZIP to browser
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="boletos_cortesia_evento_' . $eventId . '.zip"');
    header('Content-Length: ' . filesize($zipFile));
    readfile($zipFile);
    
    // Clean up
    array_map('unlink', $files);
    rmdir($tempDir);
    unlink($zipFile);
} else {
    die('Error al crear archivo ZIP');
}

function sanitizeFilename($name) {
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    return substr($name, 0, 50);
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
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
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
