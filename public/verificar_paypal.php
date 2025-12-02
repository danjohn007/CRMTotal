<?php
/**
 * Script de verificación de productos PayPal
 * Acceder: http://tu-dominio/verificar_paypal.php
 */

require_once __DIR__ . '/../config/config.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/models/Config.php';

$configModel = new Config();
$paypalClientId = $configModel->get('paypal_client_id', '');
$paypalSecret = $configModel->get('paypal_secret', '');
$paypalMode = $configModel->get('paypal_mode', 'sandbox');
$paypalBaseUrl = $paypalMode === 'live' 
    ? 'https://api-m.paypal.com' 
    : 'https://api-m.sandbox.paypal.com';

// Obtener access token
function getAccessToken($baseUrl, $clientId, $secret) {
    $ch = curl_init($baseUrl . '/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }
    
    return null;
}

// Listar productos
function listProducts($baseUrl, $accessToken) {
    $ch = curl_init($baseUrl . '/v1/catalogs/products?page_size=20');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    
    return ['error' => $response];
}

// Listar planes de suscripción
function listPlans($baseUrl, $accessToken) {
    $ch = curl_init($baseUrl . '/v1/billing/plans?page_size=20');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    
    return ['error' => $response];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación PayPal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        .status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 Verificación de PayPal</h1>
    
    <div class="card">
        <h2>Configuración</h2>
        <div class="info">
            <strong>Modo:</strong> <?php echo strtoupper($paypalMode); ?><br>
            <strong>Base URL:</strong> <?php echo $paypalBaseUrl; ?><br>
            <strong>Client ID:</strong> <?php echo substr($paypalClientId, 0, 20) . '...'; ?>
        </div>
    </div>
    
    <?php
    if (empty($paypalClientId) || empty($paypalSecret)) {
        echo '<div class="card"><div class="error">⚠️ Credenciales de PayPal no configuradas</div></div>';
        exit;
    }
    
    echo '<div class="card">';
    echo '<h2>Autenticación</h2>';
    $accessToken = getAccessToken($paypalBaseUrl, $paypalClientId, $paypalSecret);
    
    if ($accessToken) {
        echo '<div class="success">✅ Token obtenido correctamente</div>';
        
        // Listar productos
        echo '</div><div class="card">';
        echo '<h2>Productos en el Catálogo</h2>';
        $products = listProducts($paypalBaseUrl, $accessToken);
        
        if (isset($products['products']) && count($products['products']) > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Estado</th></tr>';
            foreach ($products['products'] as $product) {
                $status = $product['status'] ?? 'UNKNOWN';
                $badgeClass = $status === 'ACTIVE' ? 'badge-success' : 'badge-warning';
                echo '<tr>';
                echo '<td>' . htmlspecialchars($product['id']) . '</td>';
                echo '<td>' . htmlspecialchars($product['name']) . '</td>';
                echo '<td>' . htmlspecialchars($product['type'] ?? 'N/A') . '</td>';
                echo '<td><span class="badge ' . $badgeClass . '">' . $status . '</span></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="error">❌ No se encontraron productos</div>';
            if (isset($products['error'])) {
                echo '<pre>' . htmlspecialchars(json_encode(json_decode($products['error'], true), JSON_PRETTY_PRINT)) . '</pre>';
            }
        }
        
        // Listar planes
        echo '</div><div class="card">';
        echo '<h2>Planes de Suscripción</h2>';
        $plans = listPlans($paypalBaseUrl, $accessToken);
        
        if (isset($plans['plans']) && count($plans['plans']) > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Product ID</th></tr>';
            foreach ($plans['plans'] as $plan) {
                $status = $plan['status'] ?? 'UNKNOWN';
                $badgeClass = $status === 'ACTIVE' ? 'badge-success' : 'badge-warning';
                echo '<tr>';
                echo '<td>' . htmlspecialchars($plan['id']) . '</td>';
                echo '<td>' . htmlspecialchars($plan['name']) . '</td>';
                echo '<td><span class="badge ' . $badgeClass . '">' . $status . '</span></td>';
                echo '<td>' . htmlspecialchars($plan['product_id'] ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="error">❌ No se encontraron planes de suscripción</div>';
            if (isset($plans['error'])) {
                echo '<pre>' . htmlspecialchars(json_encode(json_decode($plans['error'], true), JSON_PRETTY_PRINT)) . '</pre>';
            }
        }
        
        // Mostrar membresías de la base de datos
        echo '</div><div class="card">';
        echo '<h2>Membresías en la Base de Datos</h2>';
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query('SELECT id, code, name, price, paypal_product_id, is_active FROM membership_types ORDER BY created_at DESC');
        $memberships = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($memberships) > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Precio</th><th>PayPal Product ID</th><th>Estado</th></tr>';
            foreach ($memberships as $membership) {
                $hasPayPal = !empty($membership['paypal_product_id']);
                $badgeClass = $hasPayPal ? 'badge-success' : 'badge-warning';
                $paypalText = $hasPayPal ? $membership['paypal_product_id'] : 'No configurado';
                echo '<tr>';
                echo '<td>' . $membership['id'] . '</td>';
                echo '<td>' . htmlspecialchars($membership['code']) . '</td>';
                echo '<td>' . htmlspecialchars($membership['name']) . '</td>';
                echo '<td>$' . number_format($membership['price'], 2) . '</td>';
                echo '<td><span class="badge ' . $badgeClass . '">' . htmlspecialchars($paypalText) . '</span></td>';
                echo '<td>' . ($membership['is_active'] ? '✅ Activo' : '❌ Inactivo') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="error">❌ No hay membresías en la base de datos</div>';
        }
        
    } else {
        echo '<div class="error">❌ No se pudo obtener el token de acceso. Verifica tus credenciales.</div>';
    }
    echo '</div>';
    ?>
    
    <div class="card">
        <h2>📋 Instrucciones</h2>
        <ol>
            <li><strong>Verifica los productos:</strong> Si ves productos listados, tus membresías se crearon correctamente en PayPal</li>
            <li><strong>Compara con la BD:</strong> Asegúrate de que cada membresía tenga un PayPal Product ID</li>
            <li><strong>Prueba los botones:</strong> Ve a una membresía en el sistema y haz clic en el botón de PayPal</li>
            <li><strong>Modo Sandbox:</strong> Usa las credenciales de prueba de PayPal Sandbox para probar pagos</li>
        </ol>
    </div>
    
    <div class="card">
        <a href="<?php echo BASE_URL; ?>/membresias" style="color: #007bff; text-decoration: none;">← Volver a Membresías</a>
    </div>
</body>
</html>
