<?php
/**
 * Reports Controller
 * Commercial, Financial, and Operational Reports
 */
class ReportsController extends Controller {
    
    public function index(): void {
        $this->requireAuth();
        
        $this->view('reports/index', [
            'pageTitle' => 'Reportes',
            'currentPage' => 'reportes'
        ]);
    }
    
    public function commercial(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial']);
        
        $affiliationModel = new Affiliation();
        $contactModel = new Contact();
        $userModel = new User();
        
        // Get date range
        $startDate = $this->getInput('start_date', date('Y-01-01'));
        $endDate = $this->getInput('end_date', date('Y-m-d'));
        
        // Channel conversion rates
        $channelStats = $contactModel->getStatsByChannel();
        
        // Monthly affiliations
        $monthlyStats = $affiliationModel->getMonthlyStats();
        
        // Team performance
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
        $this->requireRole(['superadmin', 'direccion', 'contabilidad']);
        
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
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial']);
        
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
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial']);
        
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
