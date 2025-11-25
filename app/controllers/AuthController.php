<?php
/**
 * Auth Controller
 * Handles authentication (login/logout)
 */
class AuthController extends Controller {
    
    public function login(): void {
        // If already logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('dashboard');
        }
        
        $error = null;
        
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
            'csrf_token' => $this->csrfToken()
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
}
