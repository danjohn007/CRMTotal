<?php
/**
 * Verificación de Migración - Versión Web
 * Ejecutar desde navegador: http://tu-dominio.com/verify_migration_web.php
 */

set_time_limit(0);

// Configuración de base de datos
require_once __DIR__ . '/../config/database.php';

// Conectar a la base de datos
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<h1>📊 REPORTE DE VERIFICACIÓN DE MIGRACIÓN</h1>";
    echo "<hr><br>";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

// 1. Total de contactos migrados
echo "<h2>1️⃣ Total de Contactos Migrados</h2>";
$total = $pdo->query("SELECT COUNT(*) as total FROM contacts")->fetch(PDO::FETCH_ASSOC);
echo "<p style='font-size: 24px; color: green;'><strong>✅ {$total['total']} contactos</strong></p>";
echo "<hr><br>";

// 2. Distribución por tipo de membresía
echo "<h2>2️⃣ Distribución por Tipo de Membresía</h2>";
$membresias = $pdo->query("
    SELECT 
        mt.name as tipo_membresia,
        COUNT(*) as cantidad,
        CONCAT('$', FORMAT(SUM(c.amount), 2)) as monto_total,
        CONCAT('$', FORMAT(AVG(c.amount), 2)) as monto_promedio
    FROM contacts c
    INNER JOIN membership_types mt ON c.membership_type_id = mt.id
    GROUP BY mt.id, mt.name
    ORDER BY cantidad DESC
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #4CAF50; color: white;'>
        <th>Tipo de Membresía</th>
        <th>Cantidad</th>
        <th>Monto Total</th>
        <th>Monto Promedio</th>
      </tr>";

foreach ($membresias as $m) {
    echo "<tr>";
    echo "<td><strong>{$m['tipo_membresia']}</strong></td>";
    echo "<td style='text-align: center;'>{$m['cantidad']}</td>";
    echo "<td style='text-align: right;'>{$m['monto_total']}</td>";
    echo "<td style='text-align: right;'>{$m['monto_promedio']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><br>";

// 3. Distribución por vendedor
echo "<h2>3️⃣ Distribución por Vendedor</h2>";
try {
    $vendedores = $pdo->query("
        SELECT 
            IFNULL(u.name, CONCAT('Vendedor ID: ', c.assigned_affiliate_id)) as vendedor,
            COUNT(*) as cantidad,
            CONCAT('$', FORMAT(SUM(c.amount), 2)) as monto_total,
            CONCAT('$', FORMAT(AVG(c.amount), 2)) as monto_promedio
        FROM contacts c
        LEFT JOIN users u ON c.assigned_affiliate_id = u.id
        GROUP BY c.assigned_affiliate_id, u.name
        ORDER BY cantidad DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color: red;'>⚠️ Error en consulta de vendedores: " . $e->getMessage() . "</p>";
    echo "<p>Continuando con el resto del reporte...</p><br><hr><br>";
    $vendedores = [];
}

if (count($vendedores) > 0) {
    echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #2196F3; color: white;'>
            <th>Vendedor</th>
            <th>Cantidad</th>
            <th>Monto Total</th>
            <th>Monto Promedio</th>
          </tr>";

    foreach ($vendedores as $v) {
        echo "<tr>";
        echo "<td><strong>{$v['vendedor']}</strong></td>";
        echo "<td style='text-align: center;'>{$v['cantidad']}</td>";
        echo "<td style='text-align: right;'>{$v['monto_total']}</td>";
        echo "<td style='text-align: right;'>{$v['monto_promedio']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>⚠️ No se pudieron cargar los datos de vendedores.</p>";
}
echo "<br><hr><br>";

// 4. Distribución por tipo de persona
echo "<h2>4️⃣ Distribución por Tipo de Persona</h2>";
$personas = $pdo->query("
    SELECT 
        IFNULL(person_type, 'No Especificado') as tipo_persona,
        COUNT(*) as cantidad,
        CONCAT(ROUND((COUNT(*) * 100.0 / {$total['total']}), 2), '%') as porcentaje
    FROM contacts
    GROUP BY person_type
    ORDER BY cantidad DESC
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #FF9800; color: white;'>
        <th>Tipo de Persona</th>
        <th>Cantidad</th>
        <th>Porcentaje</th>
      </tr>";

foreach ($personas as $p) {
    echo "<tr>";
    echo "<td><strong>{$p['tipo_persona']}</strong></td>";
    echo "<td style='text-align: center;'>{$p['cantidad']}</td>";
    echo "<td style='text-align: center;'>{$p['porcentaje']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><br>";

// 5. Registros sin RFC
echo "<h2>5️⃣ Calidad de Datos</h2>";
$sinRFC = $pdo->query("SELECT COUNT(*) as sin_rfc FROM contacts WHERE rfc IS NULL OR rfc = ''")->fetch(PDO::FETCH_ASSOC);
$conRFC = $total['total'] - $sinRFC['sin_rfc'];

echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #9C27B0; color: white;'>
        <th>Indicador</th>
        <th>Cantidad</th>
        <th>Porcentaje</th>
      </tr>";
echo "<tr>";
echo "<td><strong>Contactos con RFC</strong></td>";
echo "<td style='text-align: center; color: green;'>{$conRFC}</td>";
echo "<td style='text-align: center;'>" . round(($conRFC * 100.0 / $total['total']), 2) . "%</td>";
echo "</tr>";
echo "<tr>";
echo "<td><strong>Contactos sin RFC</strong></td>";
echo "<td style='text-align: center; color: red;'>{$sinRFC['sin_rfc']}</td>";
echo "<td style='text-align: center;'>" . round(($sinRFC['sin_rfc'] * 100.0 / $total['total']), 2) . "%</td>";
echo "</tr>";
echo "</table>";
echo "<br><hr><br>";

// 6. Distribución por método de pago
echo "<h2>6️⃣ Distribución por Método de Pago</h2>";
$metodosPago = $pdo->query("
    SELECT 
        IFNULL(payment_method, 'No Especificado') as metodo,
        COUNT(*) as cantidad,
        CONCAT('$', FORMAT(SUM(amount), 2)) as monto_total
    FROM contacts
    GROUP BY payment_method
    ORDER BY cantidad DESC
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #607D8B; color: white;'>
        <th>Método de Pago</th>
        <th>Cantidad</th>
        <th>Monto Total</th>
      </tr>";

foreach ($metodosPago as $mp) {
    echo "<tr>";
    echo "<td><strong>{$mp['metodo']}</strong></td>";
    echo "<td style='text-align: center;'>{$mp['cantidad']}</td>";
    echo "<td style='text-align: right;'>{$mp['monto_total']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><br>";

// 7. Distribución por mes de renovación
echo "<h2>7️⃣ Distribución por Mes de Renovación</h2>";
$meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
$renovaciones = $pdo->query("
    SELECT 
        IFNULL(renewal_month, 0) as mes,
        COUNT(*) as cantidad
    FROM contacts
    GROUP BY renewal_month
    ORDER BY renewal_month
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #3F51B5; color: white;'>
        <th>Mes</th>
        <th>Cantidad</th>
      </tr>";

foreach ($renovaciones as $r) {
    $mesNombre = ($r['mes'] >= 1 && $r['mes'] <= 12) ? $meses[$r['mes'] - 1] : 'No Especificado';
    echo "<tr>";
    echo "<td><strong>{$mesNombre}</strong></td>";
    echo "<td style='text-align: center;'>{$r['cantidad']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><br>";

// 8. Resumen de montos
echo "<h2>8️⃣ Resumen Financiero</h2>";
$resumenFinanciero = $pdo->query("
    SELECT 
        COUNT(*) as total_registros,
        CONCAT('$', FORMAT(SUM(amount), 2)) as monto_total,
        CONCAT('$', FORMAT(AVG(amount), 2)) as monto_promedio,
        CONCAT('$', FORMAT(MIN(amount), 2)) as monto_minimo,
        CONCAT('$', FORMAT(MAX(amount), 2)) as monto_maximo
    FROM contacts
")->fetch(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #4CAF50; color: white;'>
        <th>Indicador</th>
        <th>Valor</th>
      </tr>";
echo "<tr><td><strong>Total Registros</strong></td><td style='text-align: right;'>{$resumenFinanciero['total_registros']}</td></tr>";
echo "<tr><td><strong>Monto Total</strong></td><td style='text-align: right; color: green; font-size: 18px;'><strong>{$resumenFinanciero['monto_total']}</strong></td></tr>";
echo "<tr><td><strong>Monto Promedio</strong></td><td style='text-align: right;'>{$resumenFinanciero['monto_promedio']}</td></tr>";
echo "<tr><td><strong>Monto Mínimo</strong></td><td style='text-align: right;'>{$resumenFinanciero['monto_minimo']}</td></tr>";
echo "<tr><td><strong>Monto Máximo</strong></td><td style='text-align: right;'>{$resumenFinanciero['monto_maximo']}</td></tr>";
echo "</table>";
echo "<br><hr><br>";

// 9. Top 10 Industrias
echo "<h2>9️⃣ Top 10 Industrias</h2>";
$industrias = $pdo->query("
    SELECT 
        IFNULL(industry, 'No Especificado') as industria,
        COUNT(*) as cantidad
    FROM contacts
    GROUP BY industry
    ORDER BY cantidad DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #E91E63; color: white;'>
        <th>Industria</th>
        <th>Cantidad</th>
      </tr>";

foreach ($industrias as $i) {
    echo "<tr>";
    echo "<td><strong>{$i['industria']}</strong></td>";
    echo "<td style='text-align: center;'>{$i['cantidad']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><br>";

echo "<h2 style='color: green;'>✅ VERIFICACIÓN COMPLETADA EXITOSAMENTE</h2>";
echo "<p><strong>Fecha de verificación:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
