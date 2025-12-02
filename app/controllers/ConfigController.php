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
                    // Ensure upload directory exists
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
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
                // QR Configuration
                $this->configModel->set('qr_api_provider', $this->sanitize($this->getInput('qr_api_provider', 'google')));
                $this->configModel->set('qr_size', $this->sanitize($this->getInput('qr_size', '350')));
                $this->configModel->set('qr_api_key', $this->sanitize($this->getInput('qr_api_key', '')));
                
                // Shelly Relay Configuration
                $this->configModel->set('shelly_enabled', $this->getInput('shelly_enabled', '0'));
                $this->configModel->set('shelly_url', $this->sanitize($this->getInput('shelly_url', '')));
                $this->configModel->set('shelly_channel', $this->sanitize($this->getInput('shelly_channel', '0')));
                
                // Other APIs
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
    
    public function testEmail(): void {
        // Disable error output to prevent HTML in JSON response
        @ini_set('display_errors', '0');
        error_reporting(0);
        
        // Clean any output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        try {
            $config = $this->configModel->getAll();
            
            // Validate SMTP configuration
            if (empty($config['smtp_host']) || empty($config['smtp_user']) || empty($config['smtp_password'])) {
                $this->json(['success' => false, 'message' => 'Configuración SMTP incompleta. Por favor configure el servidor, usuario y contraseña.']);
            }
            
            $testEmail = $this->sanitize($this->getInput('test_email', $config['smtp_user']));
            
            if (empty($testEmail)) {
                $this->json(['success' => false, 'message' => 'Por favor proporcione un correo de destino.']);
            }
            
            // Send test email using SMTP
            $result = $this->sendEmail(
                $testEmail,
                'Correo de Prueba - CRM CCQ',
                $this->getTestEmailBody($config),
                $config
            );
            
            if ($result['success']) {
                $this->json(['success' => true, 'message' => 'Correo de prueba enviado exitosamente a ' . $testEmail]);
            } else {
                $this->json(['success' => false, 'message' => 'Error al enviar: ' . ($result['error'] ?? 'Error desconocido')]);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    private function sendEmail(string $to, string $subject, string $body, array $config): array {
        $host = $config['smtp_host'] ?? '';
        $port = (int)($config['smtp_port'] ?? 587);
        $user = $config['smtp_user'] ?? '';
        $password = $config['smtp_password'] ?? '';
        $fromName = $config['smtp_from_name'] ?? 'CRM CCQ';
        
        // Determine secure type: ssl for port 465, tls for port 587, or use config value
        $configSecure = $config['smtp_secure'] ?? '';
        if (!empty($configSecure)) {
            $secure = $configSecure;
        } else {
            // Auto-detect based on port
            $secure = ($port == 465) ? 'ssl' : 'tls';
        }
        
        // Use direct SMTP connection
        return $this->sendSmtpEmail($to, $subject, $body, $host, $port, $user, $password, $fromName, $secure);
    }
    
    private function sendSmtpEmail(string $to, string $subject, string $body, string $host, int $port, string $user, string $password, string $fromName, string $secure = 'tls'): array {
        try {
            // Build connection string with SSL/TLS support
            $protocol = ($secure === 'ssl') ? 'ssl://' : '';
            $connectionString = $protocol . $host;
            
            // Create context for SSL
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            // Connect to SMTP server
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
                $errorMsg = $errstr ? "$errstr ($errno)" : "Error de conexión ($errno)";
                return ['success' => false, 'error' => "No se pudo conectar al servidor SMTP: $errorMsg. Verifica host=$host, port=$port, secure=$secure"];
            }
            
            // Get server greeting (read all 220 lines)
            $greetingLines = 0;
            while ($line = fgets($socket, 515)) {
                $greetingLines++;
                // Check if this is the last line of greeting (220 instead of 220-)
                if (strlen($line) >= 4 && $line[3] === ' ') {
                    break;
                }
                // Safety limit
                if ($greetingLines > 20) {
                    fclose($socket);
                    return ['success' => false, 'error' => 'Error: Saludo del servidor demasiado largo'];
                }
            }
            
            // Send EHLO
            fputs($socket, "EHLO " . $host . "\r\n");
            $response = $this->getSmtpResponse($socket);
            
            // Start TLS if port is 587 and not using SSL
            if ($port === 587 && $secure === 'tls') {
                fputs($socket, "STARTTLS\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) === '220') {
                    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                    fputs($socket, "EHLO " . $host . "\r\n");
                    $response = $this->getSmtpResponse($socket);
                }
            }
            
            // Authentication - Try AUTH PLAIN first, then AUTH LOGIN
            $authenticated = false;
            
            // Try AUTH PLAIN
            $authString = base64_encode("\0" . $user . "\0" . $password);
            fputs($socket, "AUTH PLAIN $authString\r\n");
            $response = fgets($socket, 515);
            
            if (substr($response, 0, 3) === '235') {
                $authenticated = true;
            } else {
                // Try AUTH LOGIN as fallback
                fputs($socket, "AUTH LOGIN\r\n");
                $response = fgets($socket, 515);
                
                if (substr($response, 0, 3) === '334') {
                    fputs($socket, base64_encode($user) . "\r\n");
                    $response = fgets($socket, 515);
                    
                    if (substr($response, 0, 3) === '334') {
                        fputs($socket, base64_encode($password) . "\r\n");
                        $response = fgets($socket, 515);
                        
                        if (substr($response, 0, 3) === '235') {
                            $authenticated = true;
                        }
                    }
                }
            }
            
            if (!$authenticated) {
                fclose($socket);
                return ['success' => false, 'error' => 'Error de autenticación SMTP. Verifica usuario y contraseña: ' . $response];
            }
            
            // Send email
            fputs($socket, "MAIL FROM:<$user>\r\n");
            $response = fgets($socket, 515);
            
            if (substr($response, 0, 3) !== '250') {
                fclose($socket);
                return ['success' => false, 'error' => 'Error en MAIL FROM: ' . $response];
            }
            
            fputs($socket, "RCPT TO:<$to>\r\n");
            $response = fgets($socket, 515);
            
            if (substr($response, 0, 3) !== '250') {
                fclose($socket);
                return ['success' => false, 'error' => 'Error en RCPT TO: ' . $response];
            }
            
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 515);
            
            if (substr($response, 0, 3) !== '354') {
                fclose($socket);
                return ['success' => false, 'error' => 'Error en DATA: ' . $response];
            }
            
            // Email content
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
            
            if (substr($response, 0, 3) !== '250') {
                fclose($socket);
                return ['success' => false, 'error' => 'Error al enviar mensaje: ' . $response];
            }
            
            fputs($socket, "QUIT\r\n");
            $response = fgets($socket, 515);
            fclose($socket);
            
            return ['success' => true, 'message' => 'Correo enviado exitosamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Excepción: ' . $e->getMessage()];
        }
    }
    
    private function getSmtpResponse($socket): string {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }
    
    private function getTestEmailBody(array $config): string {
        $siteName = $config['site_name'] ?? 'CRM CCQ';
        $primaryColor = $config['primary_color'] ?? '#1e40af';
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background-color: ' . $primaryColor . '; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h1 style="color: white; margin: 0;">' . htmlspecialchars($siteName) . '</h1>
                </div>
                <div style="background-color: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;">
                    <h2 style="color: ' . $primaryColor . ';">¡Correo de Prueba Exitoso!</h2>
                    <p>Este es un correo de prueba enviado desde el sistema CRM.</p>
                    <p>Si recibiste este mensaje, la configuración SMTP está funcionando correctamente.</p>
                    <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
                    <p style="font-size: 12px; color: #666;">
                        Fecha y hora: ' . date('Y-m-d H:i:s') . '<br>
                        Servidor: ' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }
}
