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

// Expedientes Digitales Únicos (Afiliador Level)
$router->add('expedientes', ['controller' => 'expedientes', 'action' => 'index']);
$router->add('expedientes/{id}', ['controller' => 'expedientes', 'action' => 'show']);
$router->add('expedientes/{id}/etapa-a', ['controller' => 'expedientes', 'action' => 'editStageA']);
$router->add('expedientes/{id}/etapa-b', ['controller' => 'expedientes', 'action' => 'editStageB']);
$router->add('expedientes/{id}/etapa-c', ['controller' => 'expedientes', 'action' => 'editStageC']);

// Events
$router->add('eventos', ['controller' => 'events', 'action' => 'index']);
$router->add('eventos/nuevo', ['controller' => 'events', 'action' => 'create']);
$router->add('eventos/categorias', ['controller' => 'events', 'action' => 'categories']);
$router->add('eventos/{id}', ['controller' => 'events', 'action' => 'show']);
$router->add('eventos/{id}/editar', ['controller' => 'events', 'action' => 'edit']);
$router->add('eventos/{id}/registro', ['controller' => 'events', 'action' => 'registration']);
$router->add('eventos/{id}/asistencia', ['controller' => 'events', 'action' => 'attendance']);

// Public Event Registration (friendly URL)
$router->add('evento/{url}', ['controller' => 'events', 'action' => 'publicRegistration']);

// Public Event Payment (friendly URL)
$router->add('evento/pago/{code}', ['controller' => 'events', 'action' => 'payment']);

// Printable Event Ticket (public access)
$router->add('evento/boleto/{code}', ['controller' => 'events', 'action' => 'printableTicket']);

// Agenda / Calendar (Legacy - redirects to new section)
$router->add('agenda', ['controller' => 'agenda', 'action' => 'index']);
$router->add('agenda/nueva', ['controller' => 'agenda', 'action' => 'create']);
$router->add('agenda/{id}', ['controller' => 'agenda', 'action' => 'show']);
$router->add('agenda/{id}/editar', ['controller' => 'agenda', 'action' => 'edit']);
$router->add('agenda/api/eventos', ['controller' => 'agenda', 'action' => 'apiEvents']);

// Commercial Agenda (Agenda y Acciones Comerciales) - NEW UNIFIED SECTION
$router->add('agenda-comercial', ['controller' => 'commercial-agenda', 'action' => 'index']);
$router->add('agenda-comercial/hoy', ['controller' => 'commercial-agenda', 'action' => 'today']);
$router->add('agenda-comercial/semana', ['controller' => 'commercial-agenda', 'action' => 'week']);
$router->add('agenda-comercial/mes', ['controller' => 'commercial-agenda', 'action' => 'month']);
$router->add('agenda-comercial/nueva', ['controller' => 'commercial-agenda', 'action' => 'create']);
$router->add('agenda-comercial/{id}/editar', ['controller' => 'commercial-agenda', 'action' => 'edit']);
$router->add('agenda-comercial/api/eventos', ['controller' => 'commercial-agenda', 'action' => 'apiEvents']);
$router->add('agenda-comercial/enviar-whatsapp', ['controller' => 'commercial-agenda', 'action' => 'sendWhatsapp']);
$router->add('agenda-comercial/enviar-email', ['controller' => 'commercial-agenda', 'action' => 'sendEmail']);
$router->add('agenda-comercial/notificaciones', ['controller' => 'commercial-agenda', 'action' => 'notifications']);
$router->add('agenda-comercial/notificacion/{id}/leida', ['controller' => 'commercial-agenda', 'action' => 'markNotificationRead']);
$router->add('agenda-comercial/notificaciones/marcar-todas', ['controller' => 'commercial-agenda', 'action' => 'markAllNotificationsRead']);
$router->add('agenda-comercial/metricas', ['controller' => 'commercial-agenda', 'action' => 'metrics']);

// Intelligent Search (Buscador)
$router->add('buscador', ['controller' => 'search', 'action' => 'index']);
$router->add('buscador/resultados', ['controller' => 'search', 'action' => 'results']);
$router->add('buscador/no-match', ['controller' => 'search', 'action' => 'noMatch']);

// Customer Journey
$router->add('journey', ['controller' => 'journey', 'action' => 'index']);
$router->add('journey/upselling', ['controller' => 'journey', 'action' => 'upselling']);
$router->add('journey/crossselling', ['controller' => 'journey', 'action' => 'crossselling']);
$router->add('journey/council', ['controller' => 'journey', 'action' => 'council']);
$router->add('journey/sendUpsellingInvitation', ['controller' => 'journey', 'action' => 'sendUpsellingInvitation']);
$router->add('journey/sendServiceInvitation', ['controller' => 'journey', 'action' => 'sendServiceInvitation']);
$router->add('journey/{id}', ['controller' => 'journey', 'action' => 'show']);

// Notifications
$router->add('notificaciones', ['controller' => 'notifications', 'action' => 'index']);
$router->add('notificaciones/marcar-leida/{id}', ['controller' => 'notifications', 'action' => 'markRead']);

// Reports
$router->add('reportes', ['controller' => 'reports', 'action' => 'index']);
$router->add('reportes/comerciales', ['controller' => 'reports', 'action' => 'commercial']);
$router->add('reportes/financieros', ['controller' => 'reports', 'action' => 'financial']);
$router->add('reportes/operativos', ['controller' => 'reports', 'action' => 'operational']);
$router->add('reportes/eventos', ['controller' => 'reports', 'action' => 'events']);

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
// API endpoints (must be before dynamic routes)
$router->add('membresias/crear-suscripcion', ['controller' => 'memberships', 'action' => 'createSubscription']);
$router->add('membresias/obtener-suscripcion', ['controller' => 'memberships', 'action' => 'getSubscription']);
$router->add('membresias/crear-pago', ['controller' => 'memberships', 'action' => 'createOrder']);
$router->add('membresias/capturar-pago', ['controller' => 'memberships', 'action' => 'captureOrder']);
// Dynamic routes (must be after static routes)
$router->add('membresias/{id}/pagar', ['controller' => 'memberships', 'action' => 'pay']);
$router->add('membresias/{id}/editar', ['controller' => 'memberships', 'action' => 'edit']);
$router->add('membresias/{id}', ['controller' => 'memberships', 'action' => 'show']);

// Financial Module
$router->add('financiero', ['controller' => 'financial', 'action' => 'index']);
$router->add('financiero/pagos', ['controller' => 'financial', 'action' => 'payments']);
$router->add('financiero/facturas', ['controller' => 'financial', 'action' => 'invoices']);
$router->add('financiero/reporte', ['controller' => 'financial', 'action' => 'report']);
$router->add('financiero/registrar-pago', ['controller' => 'financial', 'action' => 'recordPayment']);
$router->add('financiero/generar-factura', ['controller' => 'financial', 'action' => 'generateInvoice']);
$router->add('financiero/categorias', ['controller' => 'financial', 'action' => 'categories']);
$router->add('financiero/movimientos', ['controller' => 'financial', 'action' => 'transactions']);
$router->add('financiero/reporte-movimientos', ['controller' => 'financial', 'action' => 'transactionsReport']);

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
$router->add('requerimientos/categorias', ['controller' => 'requirements', 'action' => 'categories']);
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
$router->add('api/buscar-empresa', ['controller' => 'api', 'action' => 'searchCompany']);
$router->add('api/eventos/verificar-url', ['controller' => 'api', 'action' => 'verifyEventUrl']);
$router->add('api/eventos/confirmar-pago', ['controller' => 'api', 'action' => 'confirmEventPayment']);
$router->add('api/eventos/validar-qr', ['controller' => 'api', 'action' => 'validateEventQR']);

// Dispatch the route
$url = $_GET['url'] ?? '';

try {
    $router->dispatch($url);
} catch (Exception $e) {
    $code = $e->getCode();
    
    // Ensure code is a valid HTTP status code
    if (!is_int($code) || $code < 100 || $code > 599) {
        $code = 500;
    }
    
    http_response_code($code);
    
    if ($code === 404) {
        require_once APP_PATH . '/views/errors/404.php';
    } else {
        echo "Error: " . $e->getMessage();
    }
}
