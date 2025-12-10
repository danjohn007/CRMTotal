<?php
/**
 * TEST DE ENVÍO DE EMAIL - DIAGNÓSTICO COMPLETO
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

// Load SMTP config
$smtp_keys = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'smtp_from_name', 'smtp_secure'];
$smtp_config = [];

foreach ($smtp_keys as $key) {
    $stmt = $pdo->prepare("SELECT config_value FROM config WHERE config_key = ?");
    $stmt->execute([$key]);
    $smtp_config[$key] = $stmt->fetchColumn();
}

echo "<html><head><meta charset='UTF-8'></head><body style='font-family: monospace; padding: 20px;'>";
echo "<h2>🔍 Test de Envío de Email - Diagnóstico</h2>";
echo "<hr>";

// Display config
echo "<h3>📋 Configuración SMTP:</h3>";
echo "<pre>";
echo "Host: {$smtp_config['smtp_host']}\n";
echo "Port: {$smtp_config['smtp_port']}\n";
echo "User: {$smtp_config['smtp_user']}\n";
echo "Password: " . str_repeat('*', strlen($smtp_config['smtp_password'])) . "\n";
echo "From Name: {$smtp_config['smtp_from_name']}\n";
echo "Secure: {$smtp_config['smtp_secure']}\n";
echo "</pre>";

// Test email
$test_email = 'danielhrzz970@gmail.com';
$subject = 'Test Email - ' . date('Y-m-d H:i:s');
$body = '<html><body><h1>Email de Prueba</h1><p>Si recibes este mensaje, el sistema funciona correctamente.</p><p>Hora: ' . date('Y-m-d H:i:s') . '</p></body></html>';

echo "<h3>📧 Enviando email de prueba a: <strong>$test_email</strong></h3>";
echo "<div style='background: #f0f0f0; padding: 15px; margin: 10px 0;'>";

$host = $smtp_config['smtp_host'];
$port = (int)$smtp_config['smtp_port'];
$user = $smtp_config['smtp_user'];
$password = $smtp_config['smtp_password'];
$fromName = $smtp_config['smtp_from_name'];
$secure = $smtp_config['smtp_secure'];

try {
    $protocol = ($secure === 'ssl') ? 'ssl://' : '';
    $connectionString = $protocol . $host;
    
    echo "🔌 Conectando a: $connectionString:$port<br>";
    
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
    
    $errno = 0;
    $errstr = '';
    $socket = @stream_socket_client(
        "$connectionString:$port",
        $errno,
        $errstr,
        30,
        STREAM_CLIENT_CONNECT,
        $context
    );
    
    if (!$socket) {
        echo "❌ <strong>ERROR DE CONEXIÓN:</strong> $errstr ($errno)<br>";
        die("</div></body></html>");
    }
    
    echo "✅ Conexión establecida<br><br>";
    
    // Read greeting
    echo "<strong>Paso 1: Lectura del saludo del servidor</strong><br>";
    $greeting_count = 0;
    while ($line = fgets($socket, 515)) {
        $greeting_count++;
        echo "← " . htmlspecialchars(trim($line)) . "<br>";
        if (strlen($line) >= 4 && $line[3] === ' ') break;
        if ($greeting_count > 10) break;
    }
    echo "<br>";
    
    // EHLO
    echo "<strong>Paso 2: Enviando EHLO</strong><br>";
    fputs($socket, "EHLO $host\r\n");
    echo "→ EHLO $host<br>";
    $ehlo_count = 0;
    while ($line = fgets($socket, 515)) {
        $ehlo_count++;
        echo "← " . htmlspecialchars(trim($line)) . "<br>";
        if (strlen($line) >= 4 && $line[3] === ' ') break;
        if ($ehlo_count > 20) break;
    }
    echo "<br>";
    
    // AUTH PLAIN
    echo "<strong>Paso 3: Autenticación AUTH PLAIN</strong><br>";
    $auth_string = base64_encode("\0" . $user . "\0" . $password);
    fputs($socket, "AUTH PLAIN $auth_string\r\n");
    echo "→ AUTH PLAIN [credentials_hidden]<br>";
    $auth_response = fgets($socket, 515);
    echo "← " . htmlspecialchars(trim($auth_response)) . "<br>";
    
    if (strpos($auth_response, '235') === false) {
        echo "❌ <strong>ERROR DE AUTENTICACIÓN</strong><br>";
        fclose($socket);
        die("</div></body></html>");
    }
    echo "✅ Autenticación exitosa<br><br>";
    
    // MAIL FROM
    echo "<strong>Paso 4: MAIL FROM</strong><br>";
    fputs($socket, "MAIL FROM: <$user>\r\n");
    echo "→ MAIL FROM: &lt;$user&gt;<br>";
    $response = fgets($socket, 515);
    echo "← " . htmlspecialchars(trim($response)) . "<br><br>";
    
    // RCPT TO
    echo "<strong>Paso 5: RCPT TO</strong><br>";
    fputs($socket, "RCPT TO: <$test_email>\r\n");
    echo "→ RCPT TO: &lt;$test_email&gt;<br>";
    $response = fgets($socket, 515);
    echo "← " . htmlspecialchars(trim($response)) . "<br><br>";
    
    // DATA
    echo "<strong>Paso 6: DATA</strong><br>";
    fputs($socket, "DATA\r\n");
    echo "→ DATA<br>";
    $response = fgets($socket, 515);
    echo "← " . htmlspecialchars(trim($response)) . "<br><br>";
    
    // Build and send message
    echo "<strong>Paso 7: Enviando mensaje</strong><br>";
    $encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $message = "From: $fromName <$user>\r\n";
    $message .= "To: <$test_email>\r\n";
    $message .= "Subject: $encoded_subject\r\n";
    $message .= "MIME-Version: 1.0\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n";
    $message .= "\r\n";
    $message .= $body . "\r\n";
    $message .= ".\r\n";
    
    fputs($socket, $message);
    echo "→ [mensaje enviado]<br>";
    $response = fgets($socket, 515);
    echo "← " . htmlspecialchars(trim($response)) . "<br>";
    
    if (strpos($response, '250') !== false) {
        echo "<br>✅ <strong style='color: green;'>EMAIL ENVIADO EXITOSAMENTE</strong><br>";
        echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px;'>";
        echo "El email fue aceptado por el servidor SMTP.<br>";
        echo "Revisa tu bandeja de entrada en <strong>$test_email</strong><br>";
        echo "Si no aparece en 2-3 minutos, revisa la carpeta de SPAM.";
        echo "</p>";
    } else {
        echo "<br>❌ <strong style='color: red;'>ERROR AL ENVIAR</strong><br>";
    }
    echo "<br>";
    
    // QUIT
    echo "<strong>Paso 8: Cerrando conexión</strong><br>";
    fputs($socket, "QUIT\r\n");
    echo "→ QUIT<br>";
    $response = fgets($socket, 515);
    echo "← " . htmlspecialchars(trim($response)) . "<br>";
    
    fclose($socket);
    echo "✅ Conexión cerrada<br>";
    
} catch (Exception $e) {
    echo "❌ <strong>EXCEPCIÓN:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "</div>";
echo "<hr>";
echo "<p><a href='send_courtesy_tickets.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>← Volver</a></p>";
echo "</body></html>";
?>
