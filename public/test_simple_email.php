<?php
/**
 * TEST SIMPLE - Usando el mismo método de EventsController
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';

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

// Get config directly from database
$config = [];
$smtp_keys = ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'smtp_from_name', 'smtp_secure'];
foreach ($smtp_keys as $key) {
    $stmt = $pdo->prepare("SELECT config_value FROM config WHERE config_key = ?");
    $stmt->execute([$key]);
    $config[$key] = $stmt->fetchColumn();
}

$host = $config['smtp_host'] ?? '';
$port = (int)($config['smtp_port'] ?? 465);
$user = $config['smtp_user'] ?? '';
$password = $config['smtp_password'] ?? '';
$fromName = $config['smtp_from_name'] ?? 'CRM CCQ';
$secure = $config['smtp_secure'] ?? 'ssl';

$to = 'danielhrzz970@gmail.com';
$subject = 'Prueba Simple - ' . date('H:i:s');
$body = '<html><body><h1>Test Simple</h1><p>Hora: ' . date('Y-m-d H:i:s') . '</p></body></html>';

echo "<h2>Enviando email de prueba...</h2>";
echo "Destinatario: $to<br>";
echo "Asunto: $subject<br><br>";

try {
    $protocol = ($secure === 'ssl') ? 'ssl://' : '';
    $connectionString = $protocol . $host;
    
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
    
    $socket = @stream_socket_client(
        "$connectionString:$port",
        $errno,
        $errstr,
        30,
        STREAM_CLIENT_CONNECT,
        $context
    );
    
    if (!$socket) {
        die("Error de conexión: $errstr");
    }
    
    // Read greeting
    $greetingLines = 0;
    while ($line = fgets($socket, 515)) {
        $greetingLines++;
        if (strlen($line) >= 4 && $line[3] === ' ') break;
        if ($greetingLines > 20) break;
    }
    
    // EHLO
    fputs($socket, "EHLO $host\r\n");
    while ($line = fgets($socket, 515)) {
        if (strlen($line) >= 4 && $line[3] === ' ') break;
    }
    
    // AUTH PLAIN
    $authString = base64_encode("\0" . $user . "\0" . $password);
    fputs($socket, "AUTH PLAIN $authString\r\n");
    $response = fgets($socket, 515);
    
    if (substr($response, 0, 3) !== '235') {
        die("Error de autenticación");
    }
    
    // MAIL FROM
    fputs($socket, "MAIL FROM:<$user>\r\n");
    fgets($socket, 515);
    
    // RCPT TO
    fputs($socket, "RCPT TO:<$to>\r\n");
    fgets($socket, 515);
    
    // DATA
    fputs($socket, "DATA\r\n");
    fgets($socket, 515);
    
    // MENSAJE - Exactamente como EventsController
    $message = "From: $fromName <$user>\r\n";
    $message .= "To: $to\r\n";
    $message .= "Subject: $subject\r\n";
    $message .= "MIME-Version: 1.0\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Date: " . date('r') . "\r\n\r\n";
    $message .= $body . "\r\n";
    $message .= ".\r\n";
    
    fputs($socket, $message);
    $response = fgets($socket, 515);
    
    echo "Respuesta del servidor: " . htmlspecialchars(trim($response)) . "<br><br>";
    
    if (substr($response, 0, 3) === '250') {
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px;'>";
        echo "<h3>✅ EMAIL ENVIADO EXITOSAMENTE</h3>";
        echo "<p>Revisa tu bandeja en <strong>danielhrzz970@gmail.com</strong></p>";
        echo "<p>Asunto: <strong>$subject</strong></p>";
        echo "<p>Si recibiste el del 2 de diciembre, este también debería llegar en 1-3 minutos.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 5px;'>";
        echo "<h3>❌ ERROR AL ENVIAR</h3>";
        echo "</div>";
    }
    
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
