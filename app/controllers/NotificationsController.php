<?php
/**
 * Notifications Controller
 */
class NotificationsController extends Controller {
    
    private Notification $notificationModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->notificationModel = new Notification();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $notifications = $this->notificationModel->getAll($userId, 100);
        $unreadCount = $this->notificationModel->countUnread($userId);
        
        // Group by date
        $groupedNotifications = [];
        foreach ($notifications as $notification) {
            $date = date('Y-m-d', strtotime($notification['created_at']));
            if (!isset($groupedNotifications[$date])) {
                $groupedNotifications[$date] = [];
            }
            $groupedNotifications[$date][] = $notification;
        }
        
        $this->view('notifications/index', [
            'pageTitle' => 'Notificaciones',
            'currentPage' => 'notificaciones',
            'groupedNotifications' => $groupedNotifications,
            'unreadCount' => $unreadCount,
            'notificationTypes' => $this->getNotificationTypes()
        ]);
    }
    
    public function markRead(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        
        if ($id) {
            $this->notificationModel->markAsRead($id);
        }
        
        // If AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->json(['success' => true]);
        }
        
        $this->redirect('notificaciones');
    }
    
    public function markAllRead(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $this->notificationModel->markAllAsRead($userId);
        
        $_SESSION['flash_success'] = 'Todas las notificaciones marcadas como leídas.';
        $this->redirect('notificaciones');
    }
    
    private function getNotificationTypes(): array {
        return [
            'vencimiento' => ['icon' => '⏰', 'color' => 'yellow', 'label' => 'Vencimiento'],
            'actividad' => ['icon' => '📋', 'color' => 'blue', 'label' => 'Actividad'],
            'no_match' => ['icon' => '🔍', 'color' => 'purple', 'label' => 'Búsqueda'],
            'oportunidad' => ['icon' => '💡', 'color' => 'green', 'label' => 'Oportunidad'],
            'beneficio' => ['icon' => '🎁', 'color' => 'pink', 'label' => 'Beneficio'],
            'sistema' => ['icon' => '⚙️', 'color' => 'gray', 'label' => 'Sistema']
        ];
    }
}
