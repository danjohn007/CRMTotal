<?php
/**
 * LISTA DE CORREOS - BOLETOS DE CORTESÍA EVENTO 6
 * 
 * Este script genera una lista de todos los correos que deben recibir
 * su boleto de cortesía para copiar y enviar manualmente.
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

echo "<html><head><meta charset='UTF-8'>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background: #667eea; color: white; }
    tr:hover { background: #f5f5f5; }
    .btn-pdf { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
    }
    .btn-pdf:hover {
        opacity: 0.9;
    }
    .btn-all-pdf {
        background: #10b981;
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        margin: 20px 0;
        display: inline-block;
    }
    .btn-all-pdf:hover {
        background: #059669;
    }
</style>
</head><body>";
echo "<h2>📧 Lista de Correos - Boletos de Cortesía Evento 6</h2>";
echo "<hr>";

try {
    // Get all Event 6 courtesy tickets
    $stmt = $pdo->prepare("
        SELECT 
            er.id,
            er.registration_code,
            er.guest_name,
            er.guest_email,
            er.guest_phone,
            COALESCE(
                NULLIF(er.guest_company, ''),
                c.business_name,
                'Invitado Especial'
            ) as business_name,
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
    
    echo "<p><strong>Total de boletos de cortesía:</strong> " . count($tickets) . "</p>";
    
    // Detailed table
    echo "<h3>📋 Lista Completa</h3>";
    echo "<a href='download_all_pdfs.php?event_id=6' class='btn-all-pdf'>📥 Descargar Todos los Boletos PDF (ZIP)</a>";
    echo "<table>";
    echo "<tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Empresa</th>
            <th>Membresía</th>
            <th>Código Registro</th>
            <th>Boleto PDF</th>
          </tr>";
    
    $emails_list = [];
    
    foreach ($tickets as $index => $ticket) {
        $num = $index + 1;
        $emails_list[] = $ticket['guest_email'];
        
        echo "<tr>";
        echo "<td>{$num}</td>";
        echo "<td>{$ticket['guest_name']}</td>";
        echo "<td><strong>{$ticket['guest_email']}</strong></td>";
        echo "<td>{$ticket['business_name']}</td>";
        echo "<td>{$ticket['membership_type']}</td>";
        echo "<td>{$ticket['registration_code']}</td>";
        echo "<td><a href='download_ticket.php?id={$ticket['id']}' target='_blank' class='btn-pdf'>🖨️ Imprimir</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Email list for copying
    echo "<hr>";
    echo "<h3>📬 Lista de Correos (para copiar)</h3>";
    echo "<p>Copia estos correos separados por comas para envío masivo:</p>";
    echo "<textarea rows='5' style='width: 100%; padding: 10px; font-family: monospace;' onclick='this.select();'>";
    echo implode(', ', $emails_list);
    echo "</textarea>";
    
    echo "<br><br>";
    echo "<p>Copia estos correos separados por punto y coma (;) :</p>";
    echo "<textarea rows='5' style='width: 100%; padding: 10px; font-family: monospace;' onclick='this.select();'>";
    echo implode('; ', $emails_list);
    echo "</textarea>";
    
    echo "<br><br>";
    echo "<p>Copia estos correos uno por línea:</p>";
    echo "<textarea rows='15' style='width: 100%; padding: 10px; font-family: monospace;' onclick='this.select();'>";
    echo implode("\n", $emails_list);
    echo "</textarea>";
    
    // Summary by membership
    echo "<hr>";
    echo "<h3>📊 Resumen por Membresía</h3>";
    $summary = [];
    foreach ($tickets as $ticket) {
        $type = $ticket['membership_type'];
        if (!isset($summary[$type])) {
            $summary[$type] = 0;
        }
        $summary[$type]++;
    }
    
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Tipo Membresía</th><th>Cantidad</th></tr>";
    foreach ($summary as $type => $count) {
        echo "<tr><td>{$type}</td><td><strong>{$count}</strong></td></tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<p style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
    echo "✅ <strong>Lista generada exitosamente.</strong> Total: " . count($tickets) . " correos.";
    echo "</p>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "❌ <strong>Error de base de datos:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "</body></html>";
?>
