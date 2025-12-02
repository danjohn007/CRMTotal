<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        DB_OPTIONS
    );
    
    echo "<h2>Actualizando configuración SMTP...</h2>";
    
    // Actualizar puerto a 465
    $stmt = $pdo->prepare("UPDATE config SET config_value = '465' WHERE id = 14");
    $result = $stmt->execute();
    
    if ($result) {
        echo "✅ Puerto actualizado correctamente a 465<br>";
        echo "Filas afectadas: " . $stmt->rowCount() . "<br>";
    } else {
        echo "❌ No se pudo actualizar<br>";
    }
    
    // Verificar que smtp_secure exista, si no, crearlo
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM config WHERE config_key = 'smtp_secure'");
    $stmt->execute();
    $exists = $stmt->fetchColumn();
    
    if (!$exists) {
        echo "<br>Creando configuración smtp_secure...<br>";
        $stmt = $pdo->prepare("INSERT INTO config (config_key, config_value, config_type, description) VALUES ('smtp_secure', 'ssl', 'text', 'Tipo de seguridad SMTP (ssl/tls)')");
        $stmt->execute();
        echo "✅ smtp_secure creado<br>";
    } else {
        $stmt = $pdo->prepare("UPDATE config SET config_value = 'ssl' WHERE config_key = 'smtp_secure'");
        $stmt->execute();
        echo "✅ smtp_secure actualizado a SSL<br>";
    }
    
    // Verificar valores finales
    $stmt = $pdo->prepare("SELECT config_key, config_value FROM config WHERE config_key IN ('smtp_host', 'smtp_port', 'smtp_user', 'smtp_secure') ORDER BY config_key");
    $stmt->execute();
    $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<br><h3>Configuración actual:</h3>";
    echo "<table border='1' cellpadding='5'>";
    foreach ($configs as $cfg) {
        echo "<tr><td><strong>{$cfg['config_key']}</strong></td><td>{$cfg['config_value']}</td></tr>";
    }
    echo "</table>";
    
    echo "<br><h2 style='color:green;'>✅ ¡Actualización exitosa!</h2>";
    echo "<a href='ver_config_smtp.php' style='padding:10px; background:#007bff; color:white; text-decoration:none; border-radius:5px; margin:5px; display:inline-block;'>Verificar configuración completa</a><br><br>";
    echo "<a href='test_smtp.php' style='padding:10px; background:#28a745; color:white; text-decoration:none; border-radius:5px; margin:5px; display:inline-block;'>Probar conexión directa</a><br><br>";
    echo "<a href='../configuracion/correo' style='padding:10px; background:#17a2b8; color:white; text-decoration:none; border-radius:5px; margin:5px; display:inline-block;'>Probar envío desde el sistema</a>";
    
} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Error: " . $e->getMessage() . "</h3>";
}
?>
