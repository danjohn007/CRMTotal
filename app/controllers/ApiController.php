<?php
/**
 * API Controller
 * RESTful API endpoints
 */
class ApiController extends Controller {
    
    public function prospects(): void {
        $this->requireAuth();
        
        $contactModel = new Contact();
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        if (in_array($role, ['superadmin', 'direccion', 'jefe_comercial'])) {
            $prospects = $contactModel->getProspects();
        } else {
            $prospects = $contactModel->getProspects($userId);
        }
        
        $this->json([
            'success' => true,
            'data' => $prospects,
            'total' => count($prospects)
        ]);
    }
    
    public function affiliates(): void {
        $this->requireAuth();
        
        $contactModel = new Contact();
        $affiliates = $contactModel->getAffiliates();
        
        $this->json([
            'success' => true,
            'data' => $affiliates,
            'total' => count($affiliates)
        ]);
    }
    
    public function events(): void {
        $this->requireAuth();
        
        $eventModel = new Event();
        $upcoming = $eventModel->getUpcoming(50);
        
        $this->json([
            'success' => true,
            'data' => $upcoming,
            'total' => count($upcoming)
        ]);
    }
    
    public function dashboard(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        
        $contactModel = new Contact();
        $affiliationModel = new Affiliation();
        $activityModel = new Activity();
        $notificationModel = new Notification();
        
        $typeStats = $contactModel->getStatsByType();
        $monthlyStats = $affiliationModel->getMonthlyStats();
        $activityStats = $activityModel->getStats($userId);
        $unreadNotifications = $notificationModel->countUnread($userId);
        
        $this->json([
            'success' => true,
            'data' => [
                'typeStats' => $typeStats,
                'monthlyStats' => $monthlyStats,
                'activityStats' => $activityStats,
                'unreadNotifications' => $unreadNotifications
            ]
        ]);
    }
    
    public function notifications(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $notificationModel = new Notification();
        
        $notifications = $notificationModel->getUnread($userId);
        
        $this->json([
            'success' => true,
            'data' => $notifications,
            'total' => count($notifications)
        ]);
    }
    
    public function search(): void {
        $term = $this->sanitize($this->getInput('q', ''));
        
        if (strlen($term) < 2) {
            $this->json([
                'success' => false,
                'message' => 'Search term too short',
                'data' => []
            ]);
            return;
        }
        
        $contactModel = new Contact();
        $searchLog = new SearchLog();
        
        $searcherType = $this->isAuthenticated() ? 'afiliado' : 'publico';
        $results = $contactModel->search($term, $searcherType);
        
        // Log the search
        $searchLog->log($term, count($results), $searcherType);
        
        $this->json([
            'success' => true,
            'data' => $results,
            'total' => count($results)
        ]);
    }
}
