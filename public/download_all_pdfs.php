<?php
/**
 * GENERAR TODOS LOS BOLETOS EN PDF Y DESCARGAR COMO ZIP
 * Genera PDFs reales usando FPDF
 */

require_once __DIR__ . '/../config/database.php';

// Check if FPDF is installed
$fpdfPath = __DIR__ . '/../libs/fpdf185/fpdf.php';
if (!file_exists($fpdfPath)) {
    die('<h1>FPDF no está instalado</h1>
    <p>Para generar PDFs automáticamente, necesitas instalar FPDF.</p>
    <p><a href="install_fpdf.php" style="background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">📦 Instalar FPDF Ahora</a></p>');
}

require_once $fpdfPath;

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
$tempDir = sys_get_temp_dir() . '/boletos_pdf_' . time();
mkdir($tempDir, 0755, true);

// Generate each PDF file
foreach ($tickets as $ticket) {
    $qrData = "https://enlacecanaco.org/crmtotal/evento/verificar/" . $ticket['registration_code'];
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
    
    // Download QR image
    $qrImage = @file_get_contents($qrUrl);
    $qrTempFile = $tempDir . '/qr_' . $ticket['registration_code'] . '.png';
    if ($qrImage) {
        file_put_contents($qrTempFile, $qrImage);
    }
    
    // Generate PDF
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 24);
    
    // Header with gradient effect (simulated with colors)
    $pdf->SetFillColor(102, 126, 234);
    $pdf->Rect(0, 0, 210, 50, 'F');
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetY(15);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'BOLETO DE ACCESO'), 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 14);
    $pdf->SetY(30);
    $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $ticket['event_name']), 0, 1, 'C');
    
    // Courtesy badge
    $pdf->SetY(55);
    $pdf->SetFillColor(16, 185, 129);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'BOLETO DE CORTESIA - SIN CARGO'), 0, 1, 'C', true);
    
    // QR Code
    if (file_exists($qrTempFile)) {
        $pdf->SetY(75);
        $pdf->Image($qrTempFile, 75, 75, 60, 60);
    }
    
    // Registration code
    $pdf->SetY(140);
    $pdf->SetFont('Courier', 'B', 14);
    $pdf->SetTextColor(102, 126, 234);
    $pdf->Cell(0, 8, $ticket['registration_code'], 0, 1, 'C');
    
    // Info table
    $pdf->SetY(155);
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetTextColor(0, 0, 0);
    
    $fields = [
        ['NOMBRE', $ticket['guest_name']],
        ['EMAIL', $ticket['guest_email']],
        ['EMPRESA', $ticket['business_name']],
        ['TIPO', $ticket['membership_type']],
        ['FECHA', $ticket['event_date']],
        ['HORA', $ticket['event_time'] . ' hrs'],
        ['LUGAR', $ticket['event_location']]
    ];
    
    foreach ($fields as $field) {
        $pdf->SetFillColor(102, 126, 234);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $field[0]), 1, 0, 'L', true);
        
        $pdf->SetFillColor(248, 249, 250);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(140, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $field[1]), 1, 1, 'L', true);
    }
    
    // Instructions
    $pdf->SetY($pdf->GetY() + 5);
    $pdf->SetFillColor(255, 243, 205);
    $pdf->SetTextColor(133, 100, 4);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', '📋 INSTRUCCIONES'), 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 
        "1. Presente este código QR al ingresar al evento\n" .
        "2. Puede mostrarlo desde su dispositivo móvil o impreso\n" .
        "3. Llegue 15 minutos antes del inicio\n" .
        "4. Este boleto es personal e intransferible"
    ));
    
    // Footer
    $pdf->SetY(-20);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 5, 'ENLACE CA - Camara de Comercio de Queretaro', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 5, 'info@enlacecanaco.org', 0, 1, 'C');
    
    // Save PDF
    $filename = sanitizeFilename($ticket['guest_name']) . '_' . $ticket['registration_code'] . '.pdf';
    $pdf->Output('F', $tempDir . '/' . $filename);
    
    // Clean up QR temp file
    if (file_exists($qrTempFile)) {
        @unlink($qrTempFile);
    }
}

// Create ZIP file
$zipFile = $tempDir . '_boletos.zip';
$zip = new ZipArchive();

if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
    $files = glob($tempDir . '/*.pdf');
    foreach ($files as $file) {
        $zip->addFile($file, basename($file));
    }
    $zip->close();
    
    // Send ZIP to browser
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="boletos_evento_' . $eventId . '.zip"');
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
    $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    return substr($name, 0, 50);
}
?>
