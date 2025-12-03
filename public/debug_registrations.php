<?php
// Debug script to check event registrations
require_once __DIR__ . '/../config/database.php';

$eventId = 19; // Change this to your event ID

$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);

// Get all registrations for this event
$stmt = $db->prepare("
    SELECT 
        id,
        guest_name,
        guest_email,
        guest_phone,
        registration_code,
        payment_status,
        parent_registration_id,
        tickets,
        registration_date
    FROM event_registrations
    WHERE event_id = :event_id
    ORDER BY registration_date DESC
");
$stmt->execute(['event_id' => $eventId]);
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Registros para Evento ID: {$eventId}</h2>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Email</th>
    <th>Teléfono</th>
    <th>Código</th>
    <th>Estado Pago</th>
    <th>Parent ID</th>
    <th>Boletos</th>
    <th>Fecha</th>
</tr>";

$duplicates = [];
$emails = [];

foreach ($registrations as $reg) {
    $email = $reg['guest_email'];
    if (isset($emails[$email])) {
        $emails[$email]++;
        $duplicates[] = $email;
    } else {
        $emails[$email] = 1;
    }
    
    $bgColor = '';
    if ($reg['parent_registration_id']) {
        $bgColor = 'background-color: #ffffcc;'; // Yellow for child registrations
    }
    if (in_array($email, $duplicates)) {
        $bgColor = 'background-color: #ffcccc;'; // Red for duplicates
    }
    
    echo "<tr style='{$bgColor}'>";
    echo "<td>{$reg['id']}</td>";
    echo "<td>{$reg['guest_name']}</td>";
    echo "<td>{$reg['guest_email']}</td>";
    echo "<td>{$reg['guest_phone']}</td>";
    echo "<td>{$reg['registration_code']}</td>";
    echo "<td>{$reg['payment_status']}</td>";
    echo "<td>" . ($reg['parent_registration_id'] ?: '-') . "</td>";
    echo "<td>{$reg['tickets']}</td>";
    echo "<td>{$reg['registration_date']}</td>";
    echo "</tr>";
}

echo "</table>";

echo "<br><h3>Resumen:</h3>";
echo "<p>Total de registros: " . count($registrations) . "</p>";
echo "<p>Registros principales (sin parent_id): " . count(array_filter($registrations, fn($r) => !$r['parent_registration_id'])) . "</p>";
echo "<p>Registros secundarios (con parent_id): " . count(array_filter($registrations, fn($r) => $r['parent_registration_id'])) . "</p>";

if ($duplicates) {
    echo "<p style='color: red;'>⚠️ Emails duplicados encontrados:</p>";
    echo "<ul>";
    foreach (array_unique($duplicates) as $email) {
        echo "<li>{$email} - {$emails[$email]} registros</li>";
    }
    echo "</ul>";
}

echo "<p style='color: #888; font-size: 12px;'>Leyenda: Amarillo = Registro secundario (acompañante) | Rojo = Email duplicado</p>";
?>
