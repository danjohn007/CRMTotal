<?php
/**
 * GENERAR BOLETO PDF CON QR
 * Genera un PDF descargable para un boleto de cortesía
 */

require_once __DIR__ . '/../config/database.php';

// Get registration ID
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

// Generate QR Code URL
$qrData = "https://enlacecanaco.org/crmtotal/evento/verificar/" . $ticket['registration_code'];
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);

// Display HTML ticket (auto-print on load)
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Boleto - <?php echo htmlspecialchars($ticket['guest_name']); ?></title>
    <script>
        // Auto-open print dialog when page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
        
        // Close window after printing or canceling
        window.addEventListener('afterprint', function() {
            // Optional: close window after printing
            // window.close();
        });
    </script>
    <style>
        @page { size: A4; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
        }
        .print-btn {
            text-align: center;
            padding: 15px;
            background: #f0f0f0;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin: 0 5px;
        }
        .btn:hover { background: #5568d3; }
        @media print {
            body { padding: 0; background: white; }
            .print-btn { display: none; }
        }
        .ticket {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 { font-size: 28px; margin-bottom: 8px; }
        .header p { font-size: 16px; opacity: 0.9; }
        .content { padding: 30px; }
        .qr-section {
            text-align: center;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .qr-section img {
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .qr-section .code {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
            margin-top: 12px;
            font-family: monospace;
        }
        .courtesy-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 40px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 18px 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }
        .info-item .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 4px;
        }
        .info-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .instructions {
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-top: 18px;
        }
        .instructions h3 {
            color: #856404;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .instructions p {
            color: #856404;
            line-height: 1.5;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            padding: 18px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="print-btn">
        <button class="btn" onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
        <button class="btn" onclick="window.close()">✖️ Cerrar</button>
    </div>
    
    <div class="ticket">
        <div class="header">
            <h1>🎫 BOLETO DE ACCESO</h1>
            <p><?php echo htmlspecialchars($ticket['event_name']); ?></p>
        </div>
        
        <div class="content">
            <div class="qr-section">
                <img src="<?php echo $qrUrl; ?>" alt="QR Code" width="280" height="280">
                <div class="code"><?php echo htmlspecialchars($ticket['registration_code']); ?></div>
            </div>
            
            <div class="courtesy-badge">
                ✅ BOLETO DE CORTESÍA - SIN CARGO
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
                <h3>📋 INSTRUCCIONES</h3>
                <p>
                    <strong>1.</strong> Presente este código QR al ingresar al evento<br>
                    <strong>2.</strong> Puede mostrarlo desde su dispositivo móvil o impreso<br>
                    <strong>3.</strong> Llegue 15 minutos antes del inicio<br>
                    <strong>4.</strong> Este boleto es personal e intransferible
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>ENLACE CA - Cámara de Comercio de Querétaro</strong></p>
            <p>info@enlacecanaco.org</p>
        </div>
    </div>
</body>
</html>
