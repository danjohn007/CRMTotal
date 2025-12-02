<?php
/**
 * Script de prueba directa SMTP
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Prueba de conexión SMTP</h2>\n";

// Configuración
$host = 'enlacecanaco.org';
$port = 465;
$user = 'crm@enlacecanaco.org';
$password = 'Danjohn007';
$to = 'danielhrzz970@gmail.com';
$secure = 'ssl';

echo "<strong>Configuración:</strong><br>\n";
echo "Host: $host<br>\n";
echo "Puerto: $port<br>\n";
echo "Usuario: $user<br>\n";
echo "Seguridad: $secure<br>\n";
echo "Destinatario: $to<br>\n";
echo "<hr>\n";

// Paso 1: Verificar que las funciones existen
echo "<strong>Paso 1: Verificando funciones...</strong><br>\n";
if (function_exists('stream_socket_client')) {
    echo "✅ stream_socket_client disponible<br>\n";
} else {
    echo "❌ stream_socket_client NO disponible<br>\n";
}

if (function_exists('stream_context_create')) {
    echo "✅ stream_context_create disponible<br>\n";
} else {
    echo "❌ stream_context_create NO disponible<br>\n";
}
echo "<hr>\n";

// Paso 2: Intentar conexión
echo "<strong>Paso 2: Intentando conexión...</strong><br>\n";

$protocol = ($secure === 'ssl') ? 'ssl://' : '';
$connectionString = $protocol . $host;

echo "Connection string: $connectionString:$port<br>\n";

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
    echo "❌ <span style='color:red;'>Error al conectar: $errstr ($errno)</span><br>\n";
    
    // Intentar sin SSL
    echo "<br><strong>Intentando conexión sin SSL...</strong><br>\n";
    $socket = @stream_socket_client(
        "$host:$port",
        $errno,
        $errstr,
        30,
        STREAM_CLIENT_CONNECT
    );
    
    if (!$socket) {
        echo "❌ <span style='color:red;'>Error sin SSL: $errstr ($errno)</span><br>\n";
        die();
    } else {
        echo "✅ Conexión sin SSL exitosa<br>\n";
    }
} else {
    echo "✅ <span style='color:green;'>Conexión SSL exitosa</span><br>\n";
}
echo "<hr>\n";

// Paso 3: Leer saludo del servidor (todas las líneas 220)
echo "<strong>Paso 3: Saludo del servidor...</strong><br>\n";
$greetingLines = 0;
while ($line = fgets($socket, 515)) {
    $greetingLines++;
    echo "Respuesta: <code>" . trim($line) . "</code><br>\n";
    
    // Check if this is the last line of greeting
    if (strlen($line) >= 4 && $line[3] === ' ') {
        break;
    }
    
    if ($greetingLines > 20) {
        echo "❌ <span style='color:red;'>Demasiadas líneas en saludo</span><br>\n";
        fclose($socket);
        die();
    }
}
echo "✅ Saludo completo ($greetingLines líneas)<br>\n";
echo "<hr>\n";

// Paso 4: EHLO
echo "<strong>Paso 4: Enviando EHLO...</strong><br>\n";
fputs($socket, "EHLO $host\r\n");
echo "→ EHLO $host<br>\n";
$response = '';
$lineCount = 0;
while ($line = fgets($socket, 515)) {
    $response .= $line;
    $lineCount++;
    echo "← <code>" . trim($line) . "</code><br>\n";
    // Continue reading until we get a line with space at position 3 (250 xxx instead of 250-xxx)
    if (strlen($line) >= 4 && $line[3] === ' ') {
        break;
    }
    // Safety limit
    if ($lineCount > 50) {
        break;
    }
}
echo "Total de líneas EHLO leídas: $lineCount<br>\n";
echo "<hr>\n";

// Paso 5: Autenticación con AUTH PLAIN
echo "<strong>Paso 5: Autenticación (AUTH PLAIN)...</strong><br>\n";
$authString = base64_encode("\0" . $user . "\0" . $password);
echo "→ AUTH PLAIN [credentials_base64]<br>\n";
fputs($socket, "AUTH PLAIN $authString\r\n");
$response = fgets($socket, 515);
echo "← <code>$response</code><br>\n";

if (substr($response, 0, 3) !== '235') {
    echo "❌ <span style='color:red;'>Error de autenticación - Usuario o contraseña incorrectos</span><br>\n";
    echo "Nota: AUTH PLAIN falló. Verifica las credenciales.<br>\n";
    fclose($socket);
    die();
}

echo "✅ <span style='color:green;'>Autenticación exitosa con AUTH PLAIN</span><br>\n";
echo "<hr>\n";

// Paso 6: Enviar correo de prueba
echo "<strong>Paso 6: Enviando correo de prueba...</strong><br>\n";

fputs($socket, "MAIL FROM:<$user>\r\n");
$response = fgets($socket, 515);
echo "MAIL FROM: <code>$response</code><br>\n";

fputs($socket, "RCPT TO:<$to>\r\n");
$response = fgets($socket, 515);
echo "RCPT TO: <code>$response</code><br>\n";

fputs($socket, "DATA\r\n");
$response = fgets($socket, 515);
echo "DATA: <code>$response</code><br>\n";

$message = "From: CRM CCQ <$user>\r\n";
$message .= "To: $to\r\n";
$message .= "Subject: Prueba SMTP Directa\r\n";
$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
$message .= "Date: " . date('r') . "\r\n\r\n";
$message .= "Este es un correo de prueba enviado directamente via SMTP.\r\n";
$message .= "Si recibes este mensaje, la configuración SMTP está funcionando correctamente.\r\n";
$message .= ".\r\n";

fputs($socket, $message);
$response = fgets($socket, 515);
echo "Envío: <code>$response</code><br>\n";

if (substr($response, 0, 3) === '250') {
    echo "✅ <span style='color:green; font-size:18px;'><strong>CORREO ENVIADO EXITOSAMENTE</strong></span><br>\n";
} else {
    echo "❌ <span style='color:red;'>Error al enviar</span><br>\n";
}

fputs($socket, "QUIT\r\n");
fclose($socket);

echo "<hr>\n";
echo "<strong>Prueba completada</strong><br>\n";
?>
