<?php
/**
 * Verificar configuración SMTP en la base de datos
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        DB_OPTIONS
    );
    
    echo "<h2>Configuración SMTP actual en la base de datos:</h2>\n";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>\n";
    echo "<tr><th>Clave</th><th>Valor</th></tr>\n";
    
    $smtp_keys = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'smtp_from_name', 'smtp_secure'];
    
    foreach ($smtp_keys as $key) {
        $stmt = $pdo->prepare("SELECT config_value FROM config WHERE config_key = ?");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        
        // Ocultar parcialmente la contraseña
        if ($key === 'smtp_password' && $value) {
            $display_value = substr($value, 0, 3) . '***' . substr($value, -3);
        } else {
            $display_value = $value ?: '<em style="color:red;">No configurado</em>';
        }
        
        echo "<tr><td><strong>$key</strong></td><td>$display_value</td></tr>\n";
    }
    
    echo "</table>\n";
    
    echo "<br><h3>Pruebas:</h3>\n";
    echo "<a href='test_smtp.php' style='display:inline-block; padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px; margin:5px;'>Probar conexión directa</a><br><br>\n";
    echo "<a href='../configuracion/correo' style='display:inline-block; padding:10px 20px; background:#28a745; color:white; text-decoration:none; border-radius:5px; margin:5px;'>Ir a Configuración de Correo</a><br><br>\n";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error: " . $e->getMessage() . "</h3>\n";
}
?>
