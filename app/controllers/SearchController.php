<?php
/**
 * Search Controller
 * Intelligent provider search (Buscador Inteligente)
 */
class SearchController extends Controller {
    
    private Contact $contactModel;
    private SearchLog $searchLog;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->contactModel = new Contact();
        $this->searchLog = new SearchLog();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $noMatchList = $this->searchLog->getNoMatches();
        $popularSearches = $this->searchLog->getPopularSearches();
        $stats = $this->searchLog->getSearchStats();
        
        $this->view('search/index', [
            'pageTitle' => 'Buscador Inteligente',
            'currentPage' => 'buscador',
            'noMatchList' => $noMatchList,
            'popularSearches' => $popularSearches,
            'stats' => $stats
        ]);
    }
    
    public function results(): void {
        $term = $this->sanitize($this->getInput('q', ''));
        
        if (empty($term)) {
            $this->redirect('buscador');
        }
        
        // Determine searcher type
        $searcherType = 'publico';
        $contactId = null;
        
        if ($this->isAuthenticated()) {
            $searcherType = 'afiliado'; // Logged in users are considered affiliates
        }
        
        // Perform search
        $results = $this->contactModel->search($term, $searcherType);
        
        // Log the search
        $this->searchLog->log($term, count($results), $searcherType, $contactId);
        
        // If no results, create notification for affiliators
        if (empty($results)) {
            $this->createNoMatchNotifications($term);
        }
        
        $this->view('search/results', [
            'pageTitle' => 'Resultados de Búsqueda',
            'currentPage' => 'buscador',
            'term' => $term,
            'results' => $results,
            'searcherType' => $searcherType
        ]);
    }
    
    public function noMatch(): void {
        $this->requireAuth();
        
        $noMatchList = $this->searchLog->getNoMatches();
        
        $this->view('search/no_match', [
            'pageTitle' => 'Búsquedas sin Resultados (NO MATCH)',
            'currentPage' => 'buscador',
            'noMatchList' => $noMatchList
        ]);
    }
    
    private function createNoMatchNotifications(string $term): void {
        // Get all affiliators and create notification
        $userModel = new User();
        $notificationModel = new Notification();
        $affiliators = $userModel->getAffiliators();
        
        // Only notify the first few affiliators to avoid spam
        $notifyUsers = array_slice($affiliators, 0, 3);
        
        foreach ($notifyUsers as $user) {
            $notificationModel->createNoMatchAlert($user['id'], $term);
        }
    }
}
