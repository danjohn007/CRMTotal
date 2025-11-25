<?php
/**
 * Users Controller
 * User management
 */
class UsersController extends Controller {
    
    private User $userModel;
    private Role $roleModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->userModel = new User();
        $this->roleModel = new Role();
    }
    
    public function index(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion']);
        
        $users = $this->userModel->getAllWithRoles();
        
        $this->view('users/index', [
            'pageTitle' => 'Usuarios',
            'currentPage' => 'usuarios',
            'users' => $users
        ]);
    }
    
    public function create(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $error = null;
        $roles = $this->roleModel->all();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $email = $this->sanitize($this->getInput('email', ''));
                $password = $this->getInput('password', '');
                $confirmPassword = $this->getInput('confirm_password', '');
                $phone = $this->sanitize($this->getInput('phone', ''));
                $whatsapp = $this->sanitize($this->getInput('whatsapp', ''));
                
                // Validation
                if (empty($email) || empty($password)) {
                    $error = 'Email y contraseña son requeridos.';
                } elseif ($password !== $confirmPassword) {
                    $error = 'Las contraseñas no coinciden.';
                } elseif (!empty($phone) && !preg_match('/^\d{10}$/', $phone)) {
                    $error = 'El teléfono debe tener exactamente 10 dígitos.';
                } elseif (!empty($whatsapp) && !preg_match('/^\d{10}$/', $whatsapp)) {
                    $error = 'El WhatsApp debe tener exactamente 10 dígitos.';
                } elseif ($this->userModel->findByEmail($email)) {
                    $error = 'Este email ya está registrado.';
                } else {
                    try {
                        $userName = $this->sanitize($this->getInput('name', ''));
                        
                        $id = $this->userModel->createUser([
                            'role_id' => (int) $this->getInput('role_id'),
                            'email' => $email,
                            'password' => $password,
                            'name' => $userName,
                            'phone' => $phone,
                            'whatsapp' => $whatsapp,
                            'address' => $this->sanitize($this->getInput('address', '')),
                            'is_active' => (int) $this->getInput('is_active', 1)
                        ]);
                        
                        // Send welcome email
                        $this->sendWelcomeEmail($email, $userName, $password);
                        
                        $_SESSION['flash_success'] = 'Usuario creado exitosamente. Se ha enviado un correo de bienvenida.';
                        $this->redirect('usuarios');
                    } catch (Exception $e) {
                        $error = 'Error al crear el usuario: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $this->view('users/create', [
            'pageTitle' => 'Nuevo Usuario',
            'currentPage' => 'usuarios',
            'roles' => $roles,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion']);
        
        $id = (int) ($this->params['id'] ?? 0);
        $user = $this->userModel->getWithRole($id);
        
        if (!$user) {
            $_SESSION['flash_error'] = 'Usuario no encontrado.';
            $this->redirect('usuarios');
        }
        
        // Get user statistics
        $affiliationModel = new Affiliation();
        $activityModel = new Activity();
        
        $affiliationStats = $affiliationModel->countByAffiliate($id, 'year');
        $activityStats = $activityModel->getStats($id);
        
        $this->view('users/show', [
            'pageTitle' => 'Detalle de Usuario',
            'currentPage' => 'usuarios',
            'user' => $user,
            'affiliationStats' => $affiliationStats,
            'activityStats' => $activityStats
        ]);
    }
    
    public function edit(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $id = (int) ($this->params['id'] ?? 0);
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $_SESSION['flash_error'] = 'Usuario no encontrado.';
            $this->redirect('usuarios');
        }
        
        $error = null;
        $roles = $this->roleModel->all();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $phone = $this->sanitize($this->getInput('phone', ''));
                $whatsapp = $this->sanitize($this->getInput('whatsapp', ''));
                
                // Validate phone/whatsapp
                if (!empty($phone) && !preg_match('/^\d{10}$/', $phone)) {
                    $error = 'El teléfono debe tener exactamente 10 dígitos.';
                } elseif (!empty($whatsapp) && !preg_match('/^\d{10}$/', $whatsapp)) {
                    $error = 'El WhatsApp debe tener exactamente 10 dígitos.';
                }
                
                if (!$error) {
                    $data = [
                        'role_id' => (int) $this->getInput('role_id'),
                        'name' => $this->sanitize($this->getInput('name', '')),
                        'phone' => $phone,
                        'whatsapp' => $whatsapp,
                        'address' => $this->sanitize($this->getInput('address', '')),
                        'is_active' => (int) $this->getInput('is_active', 1)
                    ];
                    
                    // Check if email changed and is unique
                    $email = $this->sanitize($this->getInput('email', ''));
                    if ($email !== $user['email']) {
                        if ($this->userModel->findByEmail($email)) {
                            $error = 'Este email ya está registrado.';
                        } else {
                            $data['email'] = $email;
                        }
                    }
                    
                    // Update password if provided
                    $password = $this->getInput('password', '');
                    if (!empty($password)) {
                        $confirmPassword = $this->getInput('confirm_password', '');
                        if ($password !== $confirmPassword) {
                            $error = 'Las contraseñas no coinciden.';
                        } else {
                            $this->userModel->updatePassword($id, $password);
                        }
                    }
                    
                    if (!$error) {
                        try {
                            $this->userModel->update($id, $data);
                            $_SESSION['flash_success'] = 'Usuario actualizado exitosamente.';
                            $this->redirect('usuarios');
                        } catch (Exception $e) {
                            $error = 'Error al actualizar: ' . $e->getMessage();
                        }
                    }
                }
            }
        }
        
        $this->view('users/edit', [
            'pageTitle' => 'Editar Usuario',
            'currentPage' => 'usuarios',
            'user' => $user,
            'roles' => $roles,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    private function sendWelcomeEmail(string $email, string $name, string $password): bool {
        $configModel = new Config();
        
        $smtpHost = $configModel->get('smtp_host', '');
        $smtpPort = $configModel->get('smtp_port', 587);
        $smtpUser = $configModel->get('smtp_user', '');
        $smtpPassword = $configModel->get('smtp_password', '');
        $fromName = $configModel->get('smtp_from_name', 'CRM CCQ');
        
        // If no SMTP configured, skip email (but log the attempt)
        if (empty($smtpHost) || empty($smtpUser)) {
            error_log("Welcome email for {$email} - SMTP not configured");
            return false;
        }
        
        // Sanitize name for use in email body
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        
        // Sanitize from name to prevent header injection
        $safeFromName = preg_replace('/[\r\n]/', '', $fromName);
        $safeFromName = preg_replace('/[^\p{L}\p{N}\s\-\_\.]/u', '', $safeFromName);
        
        $subject = "Bienvenido al CRM CCQ - Credenciales de acceso";
        $body = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #1e40af;'>¡Bienvenido al CRM de la Cámara de Comercio de Querétaro!</h2>
                
                <p>Hola <strong>{$safeName}</strong>,</p>
                
                <p>Tu cuenta ha sido creada exitosamente. A continuación, encontrarás tus credenciales de acceso:</p>
                
                <div style='background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                    <p><strong>URL de acceso:</strong> " . htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') . "/login</p>
                    <p><strong>Correo electrónico:</strong> " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>
                    <p><strong>Contraseña temporal:</strong> " . htmlspecialchars($password, ENT_QUOTES, 'UTF-8') . "</p>
                </div>
                
                <p style='color: #dc2626;'><strong>Importante:</strong> Por seguridad, te recomendamos cambiar tu contraseña después del primer inicio de sesión.</p>
                
                <p>Si tienes alguna duda o necesitas asistencia, no dudes en contactarnos.</p>
                
                <p>Saludos cordiales,<br>
                <strong>Equipo CRM CCQ</strong></p>
                
                <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                <p style='font-size: 12px; color: #6b7280;'>
                    Este es un mensaje automático. Por favor, no responda a este correo.
                </p>
            </div>
        </body>
        </html>
        ";
        
        // Use PHP's mail function with sanitized headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $safeFromName . ' <' . filter_var($smtpUser, FILTER_SANITIZE_EMAIL) . '>',
            'Reply-To: ' . filter_var($smtpUser, FILTER_SANITIZE_EMAIL),
            'X-Mailer: PHP/' . phpversion()
        ];
        
        return mail($email, $subject, $body, implode("\r\n", $headers));
    }
}
