<?php
/**
 * Script para actualizar el puerto SMTP a 465
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        DB_OPTIONS
    );
    
    echo "<h2>Actualizando configuración SMTP...</h2>\n";
    
    // Actualizar puerto a 465
    $stmt = $pdo->prepare("UPDATE config SET config_value = '465' WHERE config_key = 'smtp_port'");
    $stmt->execute();
    echo "✅ Puerto SMTP actualizado a 465<br>\n";
    
    // Asegurar que smtp_secure está en 'ssl'
    $stmt = $pdo->prepare("UPDATE config SET config_value = 'ssl' WHERE config_key = 'smtp_secure'");
    $stmt->execute();
    echo "✅ Seguridad SMTP configurada a SSL<br>\n";
    
    echo "<br><h3 style='color: green;'>✅ Configuración actualizada correctamente</h3>\n";
    echo "<br><a href='../configuracion/correo'>Ir a Configuración de Correo</a><br>\n";
    echo "<a href='test_smtp.php'>Probar envío directo</a><br>\n";
    echo "<a href='../'>Volver al inicio</a>\n";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error: " . $e->getMessage() . "</h3>\n";
}
?>
