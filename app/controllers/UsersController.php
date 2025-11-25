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
                
                // Validation
                if (empty($email) || empty($password)) {
                    $error = 'Email y contraseña son requeridos.';
                } elseif ($password !== $confirmPassword) {
                    $error = 'Las contraseñas no coinciden.';
                } elseif ($this->userModel->findByEmail($email)) {
                    $error = 'Este email ya está registrado.';
                } else {
                    try {
                        $id = $this->userModel->createUser([
                            'role_id' => (int) $this->getInput('role_id'),
                            'email' => $email,
                            'password' => $password,
                            'name' => $this->sanitize($this->getInput('name', '')),
                            'phone' => $this->sanitize($this->getInput('phone', '')),
                            'whatsapp' => $this->sanitize($this->getInput('whatsapp', '')),
                            'is_active' => (int) $this->getInput('is_active', 1)
                        ]);
                        
                        $_SESSION['flash_success'] = 'Usuario creado exitosamente.';
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
                $data = [
                    'role_id' => (int) $this->getInput('role_id'),
                    'name' => $this->sanitize($this->getInput('name', '')),
                    'phone' => $this->sanitize($this->getInput('phone', '')),
                    'whatsapp' => $this->sanitize($this->getInput('whatsapp', '')),
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
        
        $this->view('users/edit', [
            'pageTitle' => 'Editar Usuario',
            'currentPage' => 'usuarios',
            'user' => $user,
            'roles' => $roles,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
}
