<?php
/**
 * CRM Total - Connection Test
 * Cámara de Comercio de Querétaro
 * 
 * This file tests database connection and base URL configuration
 */

// Error display for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/../config/config.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - CRM CCQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                🔧 CRM Total - Test de Conexión
            </h1>
            
            <!-- Base URL Test -->
            <div class="mb-6 p-4 rounded-lg border-2 <?php echo defined('BASE_URL') ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50'; ?>">
                <h2 class="font-semibold text-lg mb-2">📍 URL Base</h2>
                <?php if (defined('BASE_URL')): ?>
                    <p class="text-green-700">✅ URL Base configurada correctamente</p>
                    <code class="block mt-2 p-2 bg-gray-100 rounded text-sm"><?php echo BASE_URL; ?></code>
                <?php else: ?>
                    <p class="text-red-700">❌ URL Base no configurada</p>
                <?php endif; ?>
            </div>
            
            <!-- PHP Version Test -->
            <div class="mb-6 p-4 rounded-lg border-2 <?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'border-green-500 bg-green-50' : 'border-yellow-500 bg-yellow-50'; ?>">
                <h2 class="font-semibold text-lg mb-2">🐘 Versión de PHP</h2>
                <?php if (version_compare(PHP_VERSION, '7.4.0', '>=')): ?>
                    <p class="text-green-700">✅ PHP <?php echo PHP_VERSION; ?> (Requerido: 7.4+)</p>
                <?php else: ?>
                    <p class="text-yellow-700">⚠️ PHP <?php echo PHP_VERSION; ?> (Recomendado: 7.4+)</p>
                <?php endif; ?>
            </div>
            
            <!-- PDO Extension Test -->
            <div class="mb-6 p-4 rounded-lg border-2 <?php echo extension_loaded('pdo_mysql') ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50'; ?>">
                <h2 class="font-semibold text-lg mb-2">🔌 Extensión PDO MySQL</h2>
                <?php if (extension_loaded('pdo_mysql')): ?>
                    <p class="text-green-700">✅ PDO MySQL instalado</p>
                <?php else: ?>
                    <p class="text-red-700">❌ PDO MySQL no instalado</p>
                <?php endif; ?>
            </div>
            
            <!-- Database Connection Test -->
            <div class="mb-6 p-4 rounded-lg border-2" id="db-test">
                <h2 class="font-semibold text-lg mb-2">💾 Conexión a Base de Datos</h2>
                <?php
                try {
                    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
                    echo '<p class="text-green-700">✅ Conexión al servidor MySQL exitosa</p>';
                    
                    // Check if database exists
                    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
                    if ($stmt->rowCount() > 0) {
                        echo '<p class="text-green-700 mt-2">✅ Base de datos "' . DB_NAME . '" existe</p>';
                        
                        // Connect to specific database
                        $pdo->exec("USE " . DB_NAME);
                        
                        // Check tables
                        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                        if (count($tables) > 0) {
                            echo '<p class="text-green-700 mt-2">✅ ' . count($tables) . ' tablas encontradas</p>';
                            echo '<div class="mt-2 text-sm text-gray-600">';
                            echo '<strong>Tablas:</strong> ' . implode(', ', $tables);
                            echo '</div>';
                        } else {
                            echo '<p class="text-yellow-700 mt-2">⚠️ Base de datos vacía - Ejecutar database.sql</p>';
                        }
                    } else {
                        echo '<p class="text-yellow-700 mt-2">⚠️ Base de datos "' . DB_NAME . '" no existe</p>';
                        echo '<p class="text-sm text-gray-600 mt-1">Ejecuta el archivo config/database.sql para crearla</p>';
                    }
                    
                    echo '<script>document.getElementById("db-test").classList.add("border-green-500", "bg-green-50");</script>';
                } catch (PDOException $e) {
                    echo '<p class="text-red-700">❌ Error de conexión: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<div class="mt-2 text-sm text-gray-600">';
                    echo '<strong>Configuración actual:</strong><br>';
                    echo 'Host: ' . DB_HOST . '<br>';
                    echo 'Database: ' . DB_NAME . '<br>';
                    echo 'User: ' . DB_USER . '<br>';
                    echo '</div>';
                    echo '<script>document.getElementById("db-test").classList.add("border-red-500", "bg-red-50");</script>';
                }
                ?>
            </div>
            
            <!-- Path Configuration -->
            <div class="mb-6 p-4 rounded-lg border-2 border-blue-500 bg-blue-50">
                <h2 class="font-semibold text-lg mb-2">📁 Configuración de Rutas</h2>
                <div class="text-sm space-y-1">
                    <p><strong>ROOT_PATH:</strong> <code class="bg-gray-100 px-1 rounded"><?php echo ROOT_PATH; ?></code></p>
                    <p><strong>APP_PATH:</strong> <code class="bg-gray-100 px-1 rounded"><?php echo APP_PATH; ?></code></p>
                    <p><strong>PUBLIC_PATH:</strong> <code class="bg-gray-100 px-1 rounded"><?php echo PUBLIC_PATH; ?></code></p>
                </div>
            </div>
            
            <!-- Required Extensions -->
            <div class="mb-6 p-4 rounded-lg border-2 border-gray-300 bg-gray-50">
                <h2 class="font-semibold text-lg mb-2">🧩 Extensiones PHP</h2>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <?php
                    $extensions = ['pdo', 'pdo_mysql', 'json', 'session', 'mbstring', 'openssl', 'curl'];
                    foreach ($extensions as $ext):
                        $loaded = extension_loaded($ext);
                    ?>
                    <div class="<?php echo $loaded ? 'text-green-700' : 'text-red-700'; ?>">
                        <?php echo $loaded ? '✅' : '❌'; ?> <?php echo $ext; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Next Steps -->
            <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-4">
                <h2 class="font-semibold text-lg mb-2">📋 Próximos Pasos</h2>
                <ol class="list-decimal list-inside text-sm space-y-1 text-gray-700">
                    <li>Importar <code class="bg-gray-100 px-1 rounded">config/database.sql</code> en MySQL</li>
                    <li>Editar <code class="bg-gray-100 px-1 rounded">config/database.php</code> con tus credenciales</li>
                    <li>Acceder a <a href="<?php echo BASE_URL; ?>/login" class="text-blue-600 hover:underline"><?php echo BASE_URL; ?>/login</a></li>
                    <li>Usuario: <code class="bg-gray-100 px-1 rounded">admin@camaradecomercioqro.mx</code></li>
                    <li>Contraseña: <code class="bg-gray-100 px-1 rounded">Admin123!</code></li>
                </ol>
            </div>
            
            <div class="mt-6 text-center">
                <a href="<?php echo BASE_URL; ?>/" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Ir al Sistema →
                </a>
            </div>
        </div>
        
        <p class="text-center text-gray-500 mt-4 text-sm">
            CRM Total v<?php echo APP_VERSION; ?> - Cámara de Comercio de Querétaro
        </p>
    </div>
</body>
</html>
