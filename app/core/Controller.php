<?php
/**
 * Base Controller Class
 * All controllers extend this class
 */
abstract class Controller {
    protected array $params = [];
    protected ?Database $db = null;
    
    public function __construct(array $params = []) {
        $this->params = $params;
        $this->db = Database::getInstance();
    }
    
    protected function view(string $template, array $data = []): void {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $template . '.php';
        
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            // Check if layout should be used
            $layoutFile = APP_PATH . '/views/layouts/main.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            throw new Exception("View {$template} not found");
        }
    }
    
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect(string $url): void {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }
    
    protected function isAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }
    
    protected function requireAuth(): void {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }
    }
    
    protected function requireRole(array $allowedRoles): void {
        $this->requireAuth();
        $userRole = $_SESSION['user_role'] ?? '';
        if (!in_array($userRole, $allowedRoles)) {
            $this->redirect('dashboard');
        }
    }
    
    protected function getCurrentUser(): ?array {
        if (!$this->isAuthenticated()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
    
    protected function getInput(string $key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    protected function sanitize(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    protected function csrfToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    protected function validateCsrf(): bool {
        $token = $this->getInput('csrf_token');
        return $token && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
