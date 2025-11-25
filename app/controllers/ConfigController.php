<?php
/**
 * Config Controller
 * System configuration (Superadmin only)
 */
class ConfigController extends Controller {
    
    private Config $configModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->configModel = new Config();
    }
    
    public function index(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $config = $this->configModel->getAll();
        
        $this->view('config/index', [
            'pageTitle' => 'Configuración del Sistema',
            'currentPage' => 'configuracion',
            'config' => $config
        ]);
    }
    
    public function site(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $this->configModel->set('site_name', $this->sanitize($this->getInput('site_name', '')));
                $this->configModel->set('contact_phone', $this->sanitize($this->getInput('contact_phone', '')));
                $this->configModel->set('contact_email', $this->sanitize($this->getInput('contact_email', '')));
                $this->configModel->set('office_hours', $this->sanitize($this->getInput('office_hours', '')));
                $this->configModel->set('address', $this->sanitize($this->getInput('address', '')));
                
                // Handle logo upload
                if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = PUBLIC_PATH . '/img/';
                    $filename = 'logo_' . time() . '_' . basename($_FILES['site_logo']['name']);
                    if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $uploadDir . $filename)) {
                        $this->configModel->set('site_logo', '/img/' . $filename, 'file');
                    }
                }
                
                $success = 'Configuración guardada exitosamente.';
            }
        }
        
        $config = $this->configModel->getAll();
        
        $this->view('config/site', [
            'pageTitle' => 'Configuración del Sitio',
            'currentPage' => 'configuracion',
            'config' => $config,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function email(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $this->configModel->set('smtp_host', $this->sanitize($this->getInput('smtp_host', '')));
                $this->configModel->set('smtp_port', (int) $this->getInput('smtp_port', 587), 'number');
                $this->configModel->set('smtp_user', $this->sanitize($this->getInput('smtp_user', '')));
                
                // Only update password if provided
                $password = $this->getInput('smtp_password', '');
                if (!empty($password)) {
                    $this->configModel->set('smtp_password', $password);
                }
                
                $this->configModel->set('smtp_from_name', $this->sanitize($this->getInput('smtp_from_name', '')));
                
                $success = 'Configuración de correo guardada.';
            }
        }
        
        $config = $this->configModel->getAll();
        
        $this->view('config/email', [
            'pageTitle' => 'Configuración de Correo',
            'currentPage' => 'configuracion',
            'config' => $config,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function styles(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $this->configModel->set('primary_color', $this->sanitize($this->getInput('primary_color', '#1e40af')), 'color');
                $this->configModel->set('secondary_color', $this->sanitize($this->getInput('secondary_color', '#3b82f6')), 'color');
                $this->configModel->set('accent_color', $this->sanitize($this->getInput('accent_color', '#10b981')), 'color');
                
                $success = 'Estilos actualizados.';
            }
        }
        
        $config = $this->configModel->getAll();
        
        $this->view('config/styles', [
            'pageTitle' => 'Configuración de Estilos',
            'currentPage' => 'configuracion',
            'config' => $config,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function payments(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $this->configModel->set('paypal_client_id', $this->sanitize($this->getInput('paypal_client_id', '')));
                
                // Only update secret if provided
                $secret = $this->getInput('paypal_secret', '');
                if (!empty($secret)) {
                    $this->configModel->set('paypal_secret', $secret);
                }
                
                $this->configModel->set('paypal_mode', $this->sanitize($this->getInput('paypal_mode', 'sandbox')));
                
                $success = 'Configuración de pagos guardada.';
            }
        }
        
        $config = $this->configModel->getAll();
        
        $this->view('config/payments', [
            'pageTitle' => 'Configuración de Pagos',
            'currentPage' => 'configuracion',
            'config' => $config,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function api(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $this->configModel->set('qr_api_key', $this->sanitize($this->getInput('qr_api_key', '')));
                $this->configModel->set('whatsapp_api_key', $this->sanitize($this->getInput('whatsapp_api_key', '')));
                $this->configModel->set('google_maps_api_key', $this->sanitize($this->getInput('google_maps_api_key', '')));
                
                $success = 'Configuración de APIs guardada.';
            }
        }
        
        $config = $this->configModel->getAll();
        
        $this->view('config/api', [
            'pageTitle' => 'Configuración de APIs',
            'currentPage' => 'configuracion',
            'config' => $config,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function users(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion']);
        
        $this->redirect('usuarios');
    }
}
