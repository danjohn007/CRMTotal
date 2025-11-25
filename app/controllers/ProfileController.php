<?php
/**
 * Profile Controller
 * Manages user profile settings
 */
class ProfileController extends Controller {
    
    private User $userModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->userModel = new User();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $user = $this->userModel->getWithRole($_SESSION['user_id']);
        
        $this->view('profile/index', [
            'pageTitle' => 'Mi Perfil',
            'currentPage' => 'perfil',
            'user' => $user,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function update(): void {
        $this->requireAuth();
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $userId = $_SESSION['user_id'];
                
                $data = [
                    'name' => $this->sanitize($this->getInput('name', '')),
                    'phone' => $this->sanitize($this->getInput('phone', '')),
                    'whatsapp' => $this->sanitize($this->getInput('whatsapp', ''))
                ];
                
                // Handle avatar upload
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $this->handleAvatarUpload($_FILES['avatar'], $userId);
                    if ($uploadResult['success']) {
                        $data['avatar'] = $uploadResult['path'];
                    } else {
                        $error = $uploadResult['error'];
                    }
                }
                
                if (!$error) {
                    try {
                        $this->userModel->update($userId, $data);
                        
                        // Update session name
                        $_SESSION['user_name'] = $data['name'];
                        
                        $success = 'Perfil actualizado correctamente.';
                    } catch (Exception $e) {
                        $error = 'Error al actualizar el perfil: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $user = $this->userModel->getWithRole($_SESSION['user_id']);
        
        $this->view('profile/index', [
            'pageTitle' => 'Mi Perfil',
            'currentPage' => 'perfil',
            'user' => $user,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function changePassword(): void {
        $this->requireAuth();
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $currentPassword = $this->getInput('current_password', '');
                $newPassword = $this->getInput('new_password', '');
                $confirmPassword = $this->getInput('confirm_password', '');
                
                // Validate current password
                $user = $this->userModel->find($_SESSION['user_id']);
                
                if (!password_verify($currentPassword, $user['password'])) {
                    $error = 'La contraseña actual es incorrecta.';
                } elseif (strlen($newPassword) < 8) {
                    $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Las contraseñas no coinciden.';
                } else {
                    try {
                        $this->userModel->updatePassword($_SESSION['user_id'], $newPassword);
                        $success = 'Contraseña actualizada correctamente.';
                    } catch (Exception $e) {
                        $error = 'Error al actualizar la contraseña: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $user = $this->userModel->getWithRole($_SESSION['user_id']);
        
        $this->view('profile/password', [
            'pageTitle' => 'Cambiar Contraseña',
            'currentPage' => 'perfil',
            'user' => $user,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    private function handleAvatarUpload(array $file, int $userId): array {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        // Validate file type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WEBP.'];
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 2MB.'];
        }
        
        // Create upload directory if not exists
        $uploadDir = PUBLIC_PATH . '/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => '/uploads/avatars/' . $filename];
        }
        
        return ['success' => false, 'error' => 'Error al subir el archivo.'];
    }
}
