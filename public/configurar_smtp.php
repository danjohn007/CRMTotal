<?php
/**
 * Script para configurar SMTP en la base de datos
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        DB_OPTIONS
    );
    
    // Configuración SMTP
    $smtp_config = [
        'smtp_host' => 'enlacecanaco.org',
        'smtp_port' => '465',
        'smtp_user' => 'crm@enlacecanaco.org',
        'smtp_password' => 'Danjohn007',
        'smtp_from_name' => 'CRM CCQ',
        'smtp_secure' => 'ssl' // Para puerto 465
    ];
    
    echo "<h2>Configurando SMTP...</h2>\n";
    
    foreach ($smtp_config as $key => $value) {
        // Verificar si la configuración existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM config WHERE config_key = ?");
        $stmt->execute([$key]);
        $exists = $stmt->fetchColumn() > 0;
        
        if ($exists) {
            // Actualizar
            $stmt = $pdo->prepare("UPDATE config SET config_value = ? WHERE config_key = ?");
            $stmt->execute([$value, $key]);
            echo "✅ Actualizado: $key = $value<br>\n";
        } else {
            // Insertar
            $stmt = $pdo->prepare("INSERT INTO config (config_key, config_value, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$key, $value]);
            echo "✅ Creado: $key = $value<br>\n";
        }
    }
    
    echo "<br><h3 style='color: green;'>✅ Configuración SMTP completada exitosamente</h3>\n";
    echo "<br><a href='../configuracion/correo'>Ver configuración de correo</a><br>\n";
    echo "<a href='../'>Volver al inicio</a>\n";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error: " . $e->getMessage() . "</h3>\n";
}
?>
