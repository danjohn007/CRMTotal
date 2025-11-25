<?php
/**
 * Auth Controller
 * Handles authentication (login/logout)
 */
class AuthController extends Controller {
    
    private Config $configModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->configModel = new Config();
    }
    
    public function login(): void {
        // If already logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }
        
        $error = null;
        $config = $this->configModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido. Por favor, intente de nuevo.';
            } else {
                $email = $this->sanitize($this->getInput('email', ''));
                $password = $this->getInput('password', '');
                
                if (empty($email) || empty($password)) {
                    $error = 'Por favor ingrese email y contraseña.';
                } else {
                    $userModel = new User();
                    $user = $userModel->authenticate($email, $password);
                    
                    if ($user) {
                        if (!$user['is_active']) {
                            $error = 'Su cuenta está desactivada. Contacte al administrador.';
                        } else {
                            // Get role info
                            $userWithRole = $userModel->getWithRole($user['id']);
                            
                            // Set session
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user_name'] = $user['name'];
                            $_SESSION['user_email'] = $user['email'];
                            $_SESSION['user_role'] = $userWithRole['role_name'];
                            $_SESSION['user_role_display'] = $userWithRole['role_display'];
                            
                            // Regenerate session ID for security
                            session_regenerate_id(true);
                            
                            $this->redirect('dashboard');
                        }
                    } else {
                        $error = 'Email o contraseña incorrectos.';
                    }
                }
            }
        }
        
        $this->view('auth/login', [
            'error' => $error,
            'csrf_token' => $this->csrfToken(),
            'config' => $config
        ]);
    }
    
    public function logout(): void {
        // Destroy session
        session_unset();
        session_destroy();
        
        // Start new session for flash message
        session_start();
        $_SESSION['flash_success'] = 'Ha cerrado sesión correctamente.';
        
        $this->redirect('login');
    }
    
    public function register(): void {
        // Public registration (for event registration, etc.)
        $this->view('auth/register', [
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function forgotPassword(): void {
        // If already logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }
        
        $error = null;
        $success = null;
        $config = $this->configModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $email = $this->sanitize($this->getInput('email', ''));
                
                if (empty($email)) {
                    $error = 'Por favor ingrese su correo electrónico.';
                } else {
                    $userModel = new User();
                    $user = $userModel->findByEmail($email);
                    
                    if ($user) {
                        // Generate reset token
                        $token = bin2hex(random_bytes(32));
                        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        
                        // Save token to database
                        $userModel->setResetToken($user['id'], $token, $expires);
                        
                        // Send reset email
                        $resetUrl = BASE_URL . '/restablecer-password?token=' . $token;
                        $emailSent = $this->sendPasswordResetEmail($user, $resetUrl, $config);
                        
                        if ($emailSent) {
                            $success = 'Se ha enviado un correo con instrucciones para restablecer su contraseña.';
                        } else {
                            $error = 'Error al enviar el correo. Verifique la configuración SMTP.';
                        }
                    } else {
                        // Don't reveal if email exists
                        $success = 'Si el correo existe en el sistema, recibirá instrucciones para restablecer su contraseña.';
                    }
                }
            }
        }
        
        $this->view('auth/forgot_password', [
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken(),
            'config' => $config
        ]);
    }
    
    public function resetPassword(): void {
        // If already logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }
        
        $error = null;
        $success = null;
        $config = $this->configModel->getAll();
        $token = $this->sanitize($this->getInput('token', ''));
        
        if (empty($token)) {
            $_SESSION['flash_error'] = 'Token de restablecimiento inválido.';
            $this->redirect('login');
        }
        
        $userModel = new User();
        $user = $userModel->findByResetToken($token);
        
        if (!$user || strtotime($user['reset_token_expires']) < time()) {
            $_SESSION['flash_error'] = 'El enlace de restablecimiento ha expirado o es inválido.';
            $this->redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $password = $this->getInput('password', '');
                $confirmPassword = $this->getInput('confirm_password', '');
                
                if (empty($password)) {
                    $error = 'Por favor ingrese una nueva contraseña.';
                } elseif (strlen($password) < 8) {
                    $error = 'La contraseña debe tener al menos 8 caracteres.';
                } elseif ($password !== $confirmPassword) {
                    $error = 'Las contraseñas no coinciden.';
                } else {
                    // Update password and clear reset token
                    $userModel->updatePassword($user['id'], $password);
                    $userModel->clearResetToken($user['id']);
                    
                    $_SESSION['flash_success'] = 'Su contraseña ha sido restablecida exitosamente. Puede iniciar sesión.';
                    $this->redirect('login');
                }
            }
        }
        
        $this->view('auth/reset_password', [
            'error' => $error,
            'success' => $success,
            'token' => $token,
            'csrf_token' => $this->csrfToken(),
            'config' => $config
        ]);
    }
    
    private function sendPasswordResetEmail(array $user, string $resetUrl, array $config): bool {
        $host = $config['smtp_host'] ?? '';
        $port = (int)($config['smtp_port'] ?? 587);
        $smtpUser = $config['smtp_user'] ?? '';
        $password = $config['smtp_password'] ?? '';
        $fromName = $config['smtp_from_name'] ?? 'CRM CCQ';
        $siteName = $config['site_name'] ?? 'CRM CCQ';
        $primaryColor = $config['primary_color'] ?? '#1e40af';
        
        if (empty($host) || empty($smtpUser) || empty($password)) {
            return false;
        }
        
        $subject = 'Restablecer Contraseña - ' . $siteName;
        $body = $this->getPasswordResetEmailBody($user, $resetUrl, $siteName, $primaryColor);
        
        // Try to send email
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $smtpUser . '>',
            'Reply-To: ' . $smtpUser
        ];
        
        try {
            ini_set('SMTP', $host);
            ini_set('smtp_port', $port);
            return @mail($user['email'], $subject, $body, implode("\r\n", $headers));
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function getPasswordResetEmailBody(array $user, string $resetUrl, string $siteName, string $primaryColor): string {
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
                    <h2 style="color: ' . $primaryColor . ';">Restablecer Contraseña</h2>
                    <p>Hola ' . htmlspecialchars($user['name']) . ',</p>
                    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
                    <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="' . $resetUrl . '" style="background-color: ' . $primaryColor . '; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                            Restablecer Contraseña
                        </a>
                    </div>
                    <p style="font-size: 14px; color: #666;">
                        Este enlace expirará en 1 hora. Si no solicitaste este cambio, puedes ignorar este correo.
                    </p>
                    <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
                    <p style="font-size: 12px; color: #666;">
                        Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:<br>
                        <a href="' . $resetUrl . '" style="color: ' . $primaryColor . ';">' . $resetUrl . '</a>
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }
}
