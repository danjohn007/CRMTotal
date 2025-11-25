<?php
/**
 * Dashboard Controller
 * Role-based dashboards with metrics
 */
class DashboardController extends Controller {
    
    public function index(): void {
        $this->requireAuth();
        
        $role = $_SESSION['user_role'] ?? 'afiliador';
        
        // Redirect to role-specific dashboard
        switch ($role) {
            case 'superadmin':
            case 'direccion':
                $this->direccion();
                break;
            case 'jefe_comercial':
                $this->comercial();
                break;
            case 'contabilidad':
                $this->contabilidad();
                break;
            case 'consejero':
            case 'mesa_directiva':
                $this->mesaDirectiva();
                break;
            default:
                $this->afiliador();
        }
    }
    
    public function afiliador(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        
        // Load models
        $contactModel = new Contact();
        $affiliationModel = new Affiliation();
        $activityModel = new Activity();
        $notificationModel = new Notification();
        
        // Get metrics for this affiliator
        $prospects = $contactModel->getProspects($userId);
        $affiliations = $affiliationModel->getByAffiliate($userId);
        $todayActivities = $activityModel->getToday($userId);
        $pendingActivities = $activityModel->getPending($userId);
        $overdue = $activityModel->getOverdue($userId);
        $monthlyStats = $affiliationModel->countByAffiliate($userId, 'month');
        $yearlyStats = $affiliationModel->countByAffiliate($userId, 'year');
        $expiringAffiliations = $affiliationModel->getExpiringSoon(30);
        
        // Filter expiring to only this user's affiliates
        $myExpiring = array_filter($expiringAffiliations, fn($a) => ($a['affiliate_id'] ?? 0) == $userId);
        
        // Calculate commission (example: 5% of sales)
        $monthlyCommission = ($monthlyStats['total_amount'] ?? 0) * 0.05;
        
        // Goals
        $newAffiliationsGoal = 20;
        $reaffiliationsGoal = 80;
        
        $this->view('dashboard/afiliador', [
            'pageTitle' => 'Dashboard - Afiliador',
            'currentPage' => 'dashboard',
            'prospects' => $prospects,
            'prospectsCount' => count($prospects),
            'affiliationsCount' => count($affiliations),
            'todayActivities' => $todayActivities,
            'pendingActivities' => $pendingActivities,
            'overdueCount' => count($overdue),
            'monthlyStats' => $monthlyStats,
            'yearlyStats' => $yearlyStats,
            'expiringAffiliations' => $myExpiring,
            'monthlyCommission' => $monthlyCommission,
            'newAffiliationsGoal' => $newAffiliationsGoal,
            'reaffiliationsGoal' => $reaffiliationsGoal,
            'notificationCount' => $notificationModel->countUnread($userId)
        ]);
    }
    
    public function comercial(): void {
        $this->requireAuth();
        $this->requireRole(['jefe_comercial', 'direccion', 'superadmin']);
        
        $userId = $_SESSION['user_id'];
        
        // Load models
        $userModel = new User();
        $contactModel = new Contact();
        $affiliationModel = new Affiliation();
        $activityModel = new Activity();
        $notificationModel = new Notification();
        
        // Get team metrics
        $affiliators = $userModel->getAffiliators();
        $allProspects = $contactModel->getProspects();
        $activeAffiliations = $affiliationModel->getActive();
        $expiringAffiliations = $affiliationModel->getExpiringSoon(30);
        $monthlyStats = $affiliationModel->getMonthlyStats();
        $channelStats = $contactModel->getStatsByChannel();
        
        // Team performance
        $teamPerformance = [];
        foreach ($affiliators as $affiliator) {
            $stats = $affiliationModel->countByAffiliate($affiliator['id'], 'month');
            $actStats = $activityModel->getStats($affiliator['id']);
            $teamPerformance[] = [
                'user' => $affiliator,
                'affiliations' => $stats,
                'activities' => $actStats
            ];
        }
        
        $this->view('dashboard/comercial', [
            'pageTitle' => 'Dashboard - Jefatura Comercial',
            'currentPage' => 'dashboard',
            'affiliators' => $affiliators,
            'teamPerformance' => $teamPerformance,
            'prospectsCount' => count($allProspects),
            'activeAffiliationsCount' => count($activeAffiliations),
            'expiringCount' => count($expiringAffiliations),
            'monthlyStats' => $monthlyStats,
            'channelStats' => $channelStats,
            'notificationCount' => $notificationModel->countUnread($userId)
        ]);
    }
    
    public function direccion(): void {
        $this->requireAuth();
        $this->requireRole(['direccion', 'superadmin']);
        
        $userId = $_SESSION['user_id'];
        
        // Load models
        $userModel = new User();
        $contactModel = new Contact();
        $affiliationModel = new Affiliation();
        $eventModel = new Event();
        $serviceContract = new ServiceContract();
        $notificationModel = new Notification();
        
        // High-level metrics
        $typeStats = $contactModel->getStatsByType();
        $affiliationStats = $affiliationModel->countByStatus();
        $monthlyAffiliationStats = $affiliationModel->getMonthlyStats();
        $renewalRate = $affiliationModel->getRenewalRate();
        $eventStats = $eventModel->getEventStats();
        
        // Revenue
        $affiliationRevenueMonth = $affiliationModel->getTotalRevenue('month');
        $affiliationRevenueYear = $affiliationModel->getTotalRevenue('year');
        $servicesRevenueMonth = $serviceContract->getRevenue('month');
        $servicesRevenueYear = $serviceContract->getRevenue('year');
        $servicesByCategory = $serviceContract->getStatsByCategory();
        
        // Team performance summary
        $affiliators = $userModel->getAffiliators();
        $teamSummary = [];
        foreach ($affiliators as $affiliator) {
            $stats = $affiliationModel->countByAffiliate($affiliator['id'], 'month');
            $teamSummary[] = [
                'name' => $affiliator['name'],
                'affiliations' => $stats['total'] ?? 0,
                'revenue' => $stats['total_amount'] ?? 0
            ];
        }
        
        $this->view('dashboard/direccion', [
            'pageTitle' => 'Dashboard - Dirección',
            'currentPage' => 'dashboard',
            'typeStats' => $typeStats,
            'affiliationStats' => $affiliationStats,
            'monthlyStats' => $monthlyAffiliationStats,
            'renewalRate' => $renewalRate,
            'eventStats' => $eventStats,
            'affiliationRevenueMonth' => $affiliationRevenueMonth,
            'affiliationRevenueYear' => $affiliationRevenueYear,
            'servicesRevenueMonth' => $servicesRevenueMonth,
            'servicesRevenueYear' => $servicesRevenueYear,
            'servicesByCategory' => $servicesByCategory,
            'teamSummary' => $teamSummary,
            'notificationCount' => $notificationModel->countUnread($userId)
        ]);
    }
    
    public function contabilidad(): void {
        $this->requireAuth();
        $this->requireRole(['contabilidad', 'direccion', 'superadmin']);
        
        $userId = $_SESSION['user_id'];
        
        // Load models
        $affiliationModel = new Affiliation();
        $serviceContract = new ServiceContract();
        $notificationModel = new Notification();
        
        // Financial metrics
        $activeAffiliations = $affiliationModel->getActive();
        $affiliationStats = $affiliationModel->countByStatus();
        $monthlyStats = $affiliationModel->getMonthlyStats();
        
        // Pending invoices (affiliations pending invoice)
        $pendingInvoices = array_filter($activeAffiliations, fn($a) => $a['invoice_status'] === 'pending');
        
        // Revenue breakdown
        $affiliationRevenueMonth = $affiliationModel->getTotalRevenue('month');
        $servicesRevenueMonth = $serviceContract->getRevenue('month');
        
        $this->view('dashboard/contabilidad', [
            'pageTitle' => 'Dashboard - Contabilidad',
            'currentPage' => 'dashboard',
            'activeAffiliations' => $activeAffiliations,
            'affiliationStats' => $affiliationStats,
            'monthlyStats' => $monthlyStats,
            'pendingInvoicesCount' => count($pendingInvoices),
            'affiliationRevenueMonth' => $affiliationRevenueMonth,
            'servicesRevenueMonth' => $servicesRevenueMonth,
            'notificationCount' => $notificationModel->countUnread($userId)
        ]);
    }
    
    public function mesaDirectiva(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        
        // Load models
        $contactModel = new Contact();
        $affiliationModel = new Affiliation();
        $notificationModel = new Notification();
        
        // Summary metrics for board members
        $typeStats = $contactModel->getStatsByType();
        $affiliationStats = $affiliationModel->countByStatus();
        $renewalRate = $affiliationModel->getRenewalRate();
        
        // Count affiliates and prospects
        $affiliatesCount = 0;
        $prospectsCount = 0;
        foreach ($typeStats as $stat) {
            if ($stat['contact_type'] === 'afiliado') $affiliatesCount = $stat['count'];
            if ($stat['contact_type'] === 'prospecto') $prospectsCount = $stat['count'];
        }
        
        $this->view('dashboard/mesa_directiva', [
            'pageTitle' => 'Dashboard - Mesa Directiva',
            'currentPage' => 'dashboard',
            'affiliatesCount' => $affiliatesCount,
            'prospectsCount' => $prospectsCount,
            'renewalRate' => $renewalRate,
            'affiliationStats' => $affiliationStats,
            'notificationCount' => $notificationModel->countUnread($userId)
        ]);
    }
}
