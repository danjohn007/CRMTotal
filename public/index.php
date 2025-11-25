<?php
/**
 * CRM Total - Cámara de Comercio de Querétaro
 * Entry Point
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Autoload core classes
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/core/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Initialize Router
$router = new Router();

// Define Routes
// Authentication
$router->add('', ['controller' => 'home', 'action' => 'index']);
$router->add('login', ['controller' => 'auth', 'action' => 'login']);
$router->add('logout', ['controller' => 'auth', 'action' => 'logout']);
$router->add('register', ['controller' => 'auth', 'action' => 'register']);
$router->add('recuperar-password', ['controller' => 'auth', 'action' => 'forgotPassword']);
$router->add('restablecer-password', ['controller' => 'auth', 'action' => 'resetPassword']);

// Dashboard
$router->add('dashboard', ['controller' => 'dashboard', 'action' => 'index']);
$router->add('dashboard/afiliador', ['controller' => 'dashboard', 'action' => 'afiliador']);
$router->add('dashboard/comercial', ['controller' => 'dashboard', 'action' => 'comercial']);
$router->add('dashboard/direccion', ['controller' => 'dashboard', 'action' => 'direccion']);
$router->add('dashboard/contabilidad', ['controller' => 'dashboard', 'action' => 'contabilidad']);

// Prospects
$router->add('prospectos', ['controller' => 'prospects', 'action' => 'index']);
$router->add('prospectos/nuevo', ['controller' => 'prospects', 'action' => 'create']);
$router->add('prospectos/{id}', ['controller' => 'prospects', 'action' => 'show']);
$router->add('prospectos/{id}/editar', ['controller' => 'prospects', 'action' => 'edit']);
$router->add('prospectos/{id}/eliminar', ['controller' => 'prospects', 'action' => 'delete']);
$router->add('prospectos/canal/{channel}', ['controller' => 'prospects', 'action' => 'byChannel']);

// Affiliates (Afiliados)
$router->add('afiliados', ['controller' => 'affiliates', 'action' => 'index']);
$router->add('afiliados/nuevo', ['controller' => 'affiliates', 'action' => 'create']);
$router->add('afiliados/{id}', ['controller' => 'affiliates', 'action' => 'show']);
$router->add('afiliados/{id}/editar', ['controller' => 'affiliates', 'action' => 'edit']);
$router->add('afiliados/{id}/expediente', ['controller' => 'affiliates', 'action' => 'digitalFile']);
$router->add('afiliados/vencimientos', ['controller' => 'affiliates', 'action' => 'expirations']);
$router->add('afiliados/exafiliados', ['controller' => 'affiliates', 'action' => 'former']);

// Events
$router->add('eventos', ['controller' => 'events', 'action' => 'index']);
$router->add('eventos/nuevo', ['controller' => 'events', 'action' => 'create']);
$router->add('eventos/{id}', ['controller' => 'events', 'action' => 'show']);
$router->add('eventos/{id}/editar', ['controller' => 'events', 'action' => 'edit']);
$router->add('eventos/{id}/registro', ['controller' => 'events', 'action' => 'registration']);
$router->add('eventos/{id}/asistencia', ['controller' => 'events', 'action' => 'attendance']);

// Agenda / Calendar
$router->add('agenda', ['controller' => 'agenda', 'action' => 'index']);
$router->add('agenda/nueva', ['controller' => 'agenda', 'action' => 'create']);
$router->add('agenda/{id}', ['controller' => 'agenda', 'action' => 'show']);
$router->add('agenda/{id}/editar', ['controller' => 'agenda', 'action' => 'edit']);
$router->add('agenda/api/eventos', ['controller' => 'agenda', 'action' => 'apiEvents']);

// Intelligent Search (Buscador)
$router->add('buscador', ['controller' => 'search', 'action' => 'index']);
$router->add('buscador/resultados', ['controller' => 'search', 'action' => 'results']);
$router->add('buscador/no-match', ['controller' => 'search', 'action' => 'noMatch']);

// Customer Journey
$router->add('journey', ['controller' => 'journey', 'action' => 'index']);
$router->add('journey/{id}', ['controller' => 'journey', 'action' => 'show']);
$router->add('journey/upselling', ['controller' => 'journey', 'action' => 'upselling']);
$router->add('journey/crossselling', ['controller' => 'journey', 'action' => 'crossselling']);

// Notifications
$router->add('notificaciones', ['controller' => 'notifications', 'action' => 'index']);
$router->add('notificaciones/marcar-leida/{id}', ['controller' => 'notifications', 'action' => 'markRead']);

// Reports
$router->add('reportes', ['controller' => 'reports', 'action' => 'index']);
$router->add('reportes/comerciales', ['controller' => 'reports', 'action' => 'commercial']);
$router->add('reportes/financieros', ['controller' => 'reports', 'action' => 'financial']);
$router->add('reportes/operativos', ['controller' => 'reports', 'action' => 'operational']);

// Configuration (Superadmin)
$router->add('configuracion', ['controller' => 'config', 'action' => 'index']);
$router->add('configuracion/sitio', ['controller' => 'config', 'action' => 'site']);
$router->add('configuracion/correo', ['controller' => 'config', 'action' => 'email']);
$router->add('configuracion/correo/probar', ['controller' => 'config', 'action' => 'testEmail']);
$router->add('configuracion/estilos', ['controller' => 'config', 'action' => 'styles']);
$router->add('configuracion/pagos', ['controller' => 'config', 'action' => 'payments']);
$router->add('configuracion/api', ['controller' => 'config', 'action' => 'api']);
$router->add('configuracion/usuarios', ['controller' => 'config', 'action' => 'users']);

// Profile Management
$router->add('perfil', ['controller' => 'profile', 'action' => 'index']);
$router->add('perfil/actualizar', ['controller' => 'profile', 'action' => 'update']);
$router->add('perfil/password', ['controller' => 'profile', 'action' => 'changePassword']);

// Users Management
$router->add('usuarios', ['controller' => 'users', 'action' => 'index']);
$router->add('usuarios/nuevo', ['controller' => 'users', 'action' => 'create']);
$router->add('usuarios/{id}', ['controller' => 'users', 'action' => 'show']);
$router->add('usuarios/{id}/editar', ['controller' => 'users', 'action' => 'edit']);

// Memberships
$router->add('membresias', ['controller' => 'memberships', 'action' => 'index']);
$router->add('membresias/nuevo', ['controller' => 'memberships', 'action' => 'create']);
$router->add('membresias/{id}', ['controller' => 'memberships', 'action' => 'show']);
$router->add('membresias/{id}/editar', ['controller' => 'memberships', 'action' => 'edit']);

// Financial Module
$router->add('financiero', ['controller' => 'financial', 'action' => 'index']);
$router->add('financiero/pagos', ['controller' => 'financial', 'action' => 'payments']);
$router->add('financiero/facturas', ['controller' => 'financial', 'action' => 'invoices']);
$router->add('financiero/reporte', ['controller' => 'financial', 'action' => 'report']);
$router->add('financiero/registrar-pago', ['controller' => 'financial', 'action' => 'recordPayment']);
$router->add('financiero/generar-factura', ['controller' => 'financial', 'action' => 'generateInvoice']);

// Import Module
$router->add('importar', ['controller' => 'import', 'action' => 'index']);
$router->add('importar/procesar', ['controller' => 'import', 'action' => 'process']);
$router->add('importar/plantilla', ['controller' => 'import', 'action' => 'template']);

// Audit Module
$router->add('auditoria', ['controller' => 'audit', 'action' => 'index']);
$router->add('auditoria/buscar', ['controller' => 'audit', 'action' => 'search']);
$router->add('auditoria/actividad', ['controller' => 'audit', 'action' => 'activity']);
$router->add('auditoria/usuario/{id}', ['controller' => 'audit', 'action' => 'byUser']);
$router->add('auditoria/tabla', ['controller' => 'audit', 'action' => 'byTable']);

// Commercial Requirements
$router->add('requerimientos', ['controller' => 'requirements', 'action' => 'index']);
$router->add('requerimientos/nuevo', ['controller' => 'requirements', 'action' => 'create']);
$router->add('requerimientos/mis-requerimientos', ['controller' => 'requirements', 'action' => 'myRequirements']);
$router->add('requerimientos/actualizar-estado', ['controller' => 'requirements', 'action' => 'updateStatus']);
$router->add('requerimientos/{id}', ['controller' => 'requirements', 'action' => 'show']);
$router->add('requerimientos/{id}/editar', ['controller' => 'requirements', 'action' => 'edit']);

// API Endpoints
$router->add('api/prospectos', ['controller' => 'api', 'action' => 'prospects']);
$router->add('api/afiliados', ['controller' => 'api', 'action' => 'affiliates']);
$router->add('api/eventos', ['controller' => 'api', 'action' => 'events']);
$router->add('api/dashboard', ['controller' => 'api', 'action' => 'dashboard']);
$router->add('api/notificaciones', ['controller' => 'api', 'action' => 'notifications']);
$router->add('api/buscar', ['controller' => 'api', 'action' => 'search']);

// Dispatch the route
$url = $_GET['url'] ?? '';

try {
    $router->dispatch($url);
} catch (Exception $e) {
    $code = $e->getCode() ?: 500;
    http_response_code($code);
    
    if ($code === 404) {
        require_once APP_PATH . '/views/errors/404.php';
    } else {
        echo "Error: " . $e->getMessage();
    }
}
