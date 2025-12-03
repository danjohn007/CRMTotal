<?php
/**
 * Reports Controller
 * Commercial, Financial, and Operational Reports
 */
class ReportsController extends Controller {
    
    public function index(): void {
        $this->requireAuth();
        
        // Get quick stats for current month
        $affiliationModel = new Affiliation();
        
        // Get total affiliations this month
        $sql = "SELECT COUNT(*) as total_affiliations
                FROM affiliations 
                WHERE MONTH(affiliation_date) = MONTH(CURDATE()) 
                AND YEAR(affiliation_date) = YEAR(CURDATE())";
        $stats = $this->db->queryOne($sql);
        
        // Get new affiliations (first time affiliates) this month
        $sqlNew = "SELECT COUNT(DISTINCT a.contact_id) as new_affiliations
                   FROM affiliations a
                   WHERE MONTH(a.affiliation_date) = MONTH(CURDATE()) 
                   AND YEAR(a.affiliation_date) = YEAR(CURDATE())
                   AND a.contact_id NOT IN (
                       SELECT contact_id FROM affiliations 
                       WHERE affiliation_date < DATE_FORMAT(CURDATE(), '%Y-%m-01')
                   )";
        $newStats = $this->db->queryOne($sqlNew);
        
        // Calculate renewals (total - new)
        $renewals = ($stats['total_affiliations'] ?? 0) - ($newStats['new_affiliations'] ?? 0);
        
        // Get total revenue this month
        $totalRevenueMonth = $affiliationModel->getTotalRevenue('month');
        
        // Get renewal rate
        $renewalRate = $affiliationModel->getRenewalRate();
        
        $this->view('reports/index', [
            'pageTitle' => 'Reportes',
            'currentPage' => 'reportes',
            'newAffiliationsMonth' => $newStats['new_affiliations'] ?? 0,
            'renewalsMonth' => $renewals,
            'totalRevenueMonth' => $totalRevenueMonth,
            'renewalRate' => $renewalRate
        ]);
    }
    
    public function commercial(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial', 'afiliador']);
        
        $affiliationModel = new Affiliation();
        $contactModel = new Contact();
        $userModel = new User();
        $isAfiliador = $_SESSION['user_role'] === 'afiliador';
        $userId = $_SESSION['user_id'];
        
        // Get date range
        $startDate = $this->getInput('start_date', date('Y-01-01'));
        $endDate = $this->getInput('end_date', date('Y-m-d'));
        
        // Channel conversion rates (filtered by afiliador if applicable)
        if ($isAfiliador) {
            // Get stats only for contacts assigned to this afiliador
            $sql = "SELECT 
                        source_channel,
                        COUNT(*) as count,
                        SUM(CASE WHEN contact_type = 'afiliado' THEN 1 ELSE 0 END) as converted,
                        ROUND((SUM(CASE WHEN contact_type = 'afiliado' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as conversion_rate
                    FROM contacts
                    WHERE assigned_affiliate_id = :user_id
                    AND source_channel IS NOT NULL
                    GROUP BY source_channel
                    ORDER BY conversion_rate DESC";
            $channelStats = $this->db->query($sql, ['user_id' => $userId])->fetchAll();
        } else {
            $channelStats = $contactModel->getStatsByChannel();
        }
        
        // Monthly affiliations (only afiliador's if afiliador)
        $monthlyStats = $affiliationModel->getMonthlyStats();
        
        // Team performance (only own stats if afiliador)
        if ($isAfiliador) {
            $stats = $affiliationModel->countByAffiliate($userId, 'year');
            $teamPerformance = [[
                'name' => $_SESSION['user_name'],
                'total' => $stats['total'] ?? 0,
                'new' => $stats['new_affiliations'] ?? 0,
                'revenue' => $stats['total_amount'] ?? 0
            ]];
        } else {
            $affiliators = $userModel->getAffiliators();
            $teamPerformance = [];
            foreach ($affiliators as $affiliator) {
                $stats = $affiliationModel->countByAffiliate($affiliator['id'], 'year');
                $teamPerformance[] = [
                    'name' => $affiliator['name'],
                    'total' => $stats['total'] ?? 0,
                    'new' => $stats['new_affiliations'] ?? 0,
                    'revenue' => $stats['total_amount'] ?? 0
                ];
            }
        }
        
        // Renewal rate
        $renewalRate = $affiliationModel->getRenewalRate();
        
        $this->view('reports/commercial', [
            'pageTitle' => 'Reportes Comerciales',
            'currentPage' => 'reportes',
            'channelStats' => $channelStats,
            'monthlyStats' => $monthlyStats,
            'teamPerformance' => $teamPerformance,
            'renewalRate' => $renewalRate,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    
    public function financial(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'contabilidad', 'afiliador']);
        
        $affiliationModel = new Affiliation();
        $serviceContract = new ServiceContract();
        
        // Revenue breakdown
        $affiliationRevenueMonth = $affiliationModel->getTotalRevenue('month');
        $affiliationRevenueYear = $affiliationModel->getTotalRevenue('year');
        $servicesRevenueMonth = $serviceContract->getRevenue('month');
        $servicesRevenueYear = $serviceContract->getRevenue('year');
        
        // Monthly breakdown
        $monthlyStats = $affiliationModel->getMonthlyStats();
        
        // Services by category
        $servicesByCategory = $serviceContract->getStatsByCategory();
        
        $this->view('reports/financial', [
            'pageTitle' => 'Reportes Financieros',
            'currentPage' => 'reportes',
            'affiliationRevenueMonth' => $affiliationRevenueMonth,
            'affiliationRevenueYear' => $affiliationRevenueYear,
            'servicesRevenueMonth' => $servicesRevenueMonth,
            'servicesRevenueYear' => $servicesRevenueYear,
            'monthlyStats' => $monthlyStats,
            'servicesByCategory' => $servicesByCategory
        ]);
    }
    
    public function operational(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial', 'afiliador']);
        
        $contactModel = new Contact();
        $eventModel = new Event();
        $searchLog = new SearchLog();
        
        // Profile completion stats
        $typeStats = $contactModel->getStatsByType();
        
        // Event stats
        $eventStats = $eventModel->getEventStats();
        
        // Search stats
        $searchStats = $searchLog->getSearchStats();
        $noMatches = $searchLog->getNoMatches();
        
        $this->view('reports/operational', [
            'pageTitle' => 'Reportes Operativos',
            'currentPage' => 'reportes',
            'typeStats' => $typeStats,
            'eventStats' => $eventStats,
            'searchStats' => $searchStats,
            'noMatches' => array_slice($noMatches, 0, 10)
        ]);
    }
    
    public function events(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial', 'afiliador']);
        
        $eventModel = new Event();
        
        // Get filter parameters
        $eventId = (int) $this->getInput('event_id', 0);
        $eventType = $this->sanitize($this->getInput('event_type', ''));
        $category = $this->sanitize($this->getInput('category', ''));
        
        // Get comprehensive event metrics
        $metrics = $eventModel->getEventMetrics(
            $eventId ?: null,
            $eventType ?: null,
            $category ?: null
        );
        
        // Get top 50 attending businesses
        $topBusinesses = $eventModel->getTopAttendingBusinesses($eventId ?: null, 50);
        
        // Get metrics by category and type
        $metricsByCategory = $eventModel->getMetricsByCategory();
        $metricsByType = $eventModel->getMetricsByType();
        
        // Get events list for filter dropdown
        $allEvents = $eventModel->all();
        
        // Get unique categories for filter
        $categories = array_unique(array_filter(array_column($allEvents, 'category')));
        sort($categories);
        
        $this->view('reports/events', [
            'pageTitle' => 'Reportes de Eventos',
            'currentPage' => 'reportes',
            'metrics' => $metrics,
            'topBusinesses' => $topBusinesses,
            'metricsByCategory' => $metricsByCategory,
            'metricsByType' => $metricsByType,
            'allEvents' => $allEvents,
            'categories' => $categories,
            'selectedEventId' => $eventId,
            'selectedEventType' => $eventType,
            'selectedCategory' => $category,
            'eventTypes' => [
                'interno' => 'Evento Interno CCQ',
                'publico' => 'Evento Público',
                'terceros' => 'Evento de Terceros'
            ]
        ]);
    }
}
