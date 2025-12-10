<?php
/**
 * DESCARGAR BOLETO COMO PDF REAL
 * Usa DomPDF o mPDF para generar PDF verdadero
 */

require_once __DIR__ . '/../config/database.php';

$regId = (int)($_GET['id'] ?? 0);

if ($regId === 0) {
    die('ID de registro inválido');
}

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

// Get ticket data
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
    WHERE er.id = :id
");

$stmt->execute(['id' => $regId]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Boleto no encontrado');
}

// QR Code URL
$qrData = "https://enlacecanaco.org/crmtotal/evento/verificar/" . $ticket['registration_code'];
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($qrData);

// Download QR image as base64
$qrImage = @file_get_contents($qrUrl);
if ($qrImage) {
    $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);
} else {
    $qrBase64 = '';
}

// Generate HTML for PDF
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 20mm; }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }
        .header p {
            margin: 0;
            font-size: 18px;
        }
        .badge {
            background: #10b981;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
        }
        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            margin: 20px 0;
        }
        .qr-section img {
            max-width: 300px;
            border: 4px solid white;
        }
        .code {
            font-family: monospace;
            font-size: 20px;
            color: #667eea;
            font-weight: bold;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        .label {
            background: #667eea;
            color: white;
            font-weight: bold;
            width: 30%;
        }
        .value {
            background: #f8f9fa;
        }
        .instructions {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .instructions h3 {
            color: #856404;
            margin: 0 0 10px 0;
        }
        .instructions p {
            color: #856404;
            margin: 0;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎫 BOLETO DE ACCESO</h1>
        <p>' . htmlspecialchars($ticket['event_name']) . '</p>
    </div>
    
    <div class="badge">✅ BOLETO DE CORTESÍA - SIN CARGO</div>
    
    <div class="qr-section">';
    
if ($qrBase64) {
    $html .= '<img src="' . $qrBase64 . '" alt="QR Code">';
}

$html .= '
        <div class="code">' . htmlspecialchars($ticket['registration_code']) . '</div>
    </div>
    
    <table>
        <tr>
            <td class="label">NOMBRE</td>
            <td class="value">' . htmlspecialchars($ticket['guest_name']) . '</td>
        </tr>
        <tr>
            <td class="label">EMAIL</td>
            <td class="value">' . htmlspecialchars($ticket['guest_email']) . '</td>
        </tr>
        <tr>
            <td class="label">EMPRESA</td>
            <td class="value">' . htmlspecialchars($ticket['business_name']) . '</td>
        </tr>
        <tr>
            <td class="label">TIPO</td>
            <td class="value">' . htmlspecialchars($ticket['membership_type']) . '</td>
        </tr>
        <tr>
            <td class="label">FECHA</td>
            <td class="value">' . $ticket['event_date'] . '</td>
        </tr>
        <tr>
            <td class="label">HORA</td>
            <td class="value">' . $ticket['event_time'] . ' hrs</td>
        </tr>
        <tr>
            <td class="label">LUGAR</td>
            <td class="value">' . htmlspecialchars($ticket['event_location']) . '</td>
        </tr>
    </table>
    
    <div class="instructions">
        <h3>📋 INSTRUCCIONES</h3>
        <p>
            <strong>1.</strong> Presente este código QR al ingresar al evento<br>
            <strong>2.</strong> Puede mostrarlo desde su dispositivo móvil o impreso<br>
            <strong>3.</strong> Llegue 15 minutos antes del inicio<br>
            <strong>4.</strong> Este boleto es personal e intransferible
        </p>
    </div>
    
    <div class="footer">
        <p><strong>ENLACE CA - Cámara de Comercio de Querétaro</strong></p>
        <p>info@enlacecanaco.org</p>
    </div>
</body>
</html>';

// Try to use wkhtmltopdf if available (command line tool)
$wkhtmltopdf = '/usr/bin/wkhtmltopdf'; // or /usr/local/bin/wkhtmltopdf
if (file_exists($wkhtmltopdf)) {
    $tempHtml = tempnam(sys_get_temp_dir(), 'ticket_') . '.html';
    $tempPdf = tempnam(sys_get_temp_dir(), 'ticket_') . '.pdf';
    
    file_put_contents($tempHtml, $html);
    
    exec("$wkhtmltopdf $tempHtml $tempPdf 2>&1", $output, $return);
    
    if ($return === 0 && file_exists($tempPdf)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="boleto_' . $ticket['registration_code'] . '.pdf"');
        readfile($tempPdf);
        
        unlink($tempHtml);
        unlink($tempPdf);
        exit;
    }
}

// Fallback: Use browser's print-to-PDF with auto-trigger
header('Content-Type: text/html; charset=UTF-8');
echo $html;
echo '<script>
window.onload = function() {
    setTimeout(function() {
        window.print();
    }, 500);
};
</script>';
?>
