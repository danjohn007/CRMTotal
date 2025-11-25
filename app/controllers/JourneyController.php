<?php
/**
 * Journey Controller
 * Customer Journey Visualization and Upselling/Cross-selling
 */
class JourneyController extends Controller {
    
    private Contact $contactModel;
    private Affiliation $affiliationModel;
    private Service $serviceModel;
    private ServiceContract $contractModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->contactModel = new Contact();
        $this->affiliationModel = new Affiliation();
        $this->serviceModel = new Service();
        $this->contractModel = new ServiceContract();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        // Get journey stages statistics
        $typeStats = $this->contactModel->getStatsByType();
        $channelStats = $this->contactModel->getStatsByChannel();
        
        // Get services categories
        $serviceCategories = $this->serviceModel->getCategories();
        $servicesByCategory = $this->contractModel->getStatsByCategory();
        
        // Get upselling opportunities (affiliates with basic membership)
        $upsellingOpportunities = $this->getUpsellingOpportunities();
        
        // Get cross-selling opportunities
        $crosssellingOpportunities = $this->getCrosssellingOpportunities();
        
        $this->view('journey/index', [
            'pageTitle' => 'Customer Journey',
            'currentPage' => 'journey',
            'typeStats' => $typeStats,
            'channelStats' => $channelStats,
            'serviceCategories' => $serviceCategories,
            'servicesByCategory' => $servicesByCategory,
            'upsellingOpportunities' => $upsellingOpportunities,
            'crosssellingOpportunities' => $crosssellingOpportunities
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Contacto no encontrado.';
            $this->redirect('journey');
        }
        
        // Get complete journey for this contact
        $affiliations = $this->affiliationModel->getByContact($id);
        $contracts = $this->contractModel->getByContact($id);
        
        $activityModel = new Activity();
        $activities = $activityModel->getByContact($id);
        
        // Calculate journey stage
        $stage = $this->calculateJourneyStage($contact, $affiliations, $contracts);
        
        // Get recommendations
        $recommendations = $this->getRecommendations($contact, $affiliations, $contracts);
        
        $this->view('journey/show', [
            'pageTitle' => 'Customer Journey - ' . ($contact['business_name'] ?? 'Contacto'),
            'currentPage' => 'journey',
            'contact' => $contact,
            'affiliations' => $affiliations,
            'contracts' => $contracts,
            'activities' => $activities,
            'stage' => $stage,
            'recommendations' => $recommendations
        ]);
    }
    
    public function upselling(): void {
        $this->requireAuth();
        
        $opportunities = $this->getUpsellingOpportunities();
        $membershipTypes = (new MembershipType())->getActive();
        
        $this->view('journey/upselling', [
            'pageTitle' => 'Oportunidades de Upselling',
            'currentPage' => 'journey',
            'opportunities' => $opportunities,
            'membershipTypes' => $membershipTypes
        ]);
    }
    
    public function crossselling(): void {
        $this->requireAuth();
        
        $opportunities = $this->getCrosssellingOpportunities();
        $services = $this->serviceModel->getActive();
        $serviceCategories = $this->serviceModel->getCategories();
        
        $this->view('journey/crossselling', [
            'pageTitle' => 'Oportunidades de Cross-selling',
            'currentPage' => 'journey',
            'opportunities' => $opportunities,
            'services' => $services,
            'serviceCategories' => $serviceCategories
        ]);
    }
    
    private function getUpsellingOpportunities(): array {
        // Get affiliates with basic membership that could upgrade
        $sql = "SELECT c.*, a.id as affiliation_id, m.name as current_membership, m.code as membership_code,
                       a.affiliation_date, a.expiration_date, u.name as affiliator_name
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id
                WHERE m.code = 'BASICA'
                AND DATEDIFF(a.expiration_date, CURDATE()) BETWEEN 0 AND 60
                ORDER BY a.expiration_date";
        
        return $this->db->fetchAll($sql);
    }
    
    private function getCrosssellingOpportunities(): array {
        // Get affiliates who haven't contracted any services
        $sql = "SELECT c.*, a.id as affiliation_id, m.name as current_membership,
                       u.name as affiliator_name
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id
                LEFT JOIN service_contracts sc ON c.id = sc.contact_id
                WHERE sc.id IS NULL
                AND c.contact_type = 'afiliado'
                ORDER BY c.business_name
                LIMIT 50";
        
        return $this->db->fetchAll($sql);
    }
    
    private function calculateJourneyStage(array $contact, array $affiliations, array $contracts): array {
        $stage = [
            'current' => 'prospecto',
            'progress' => 0,
            'stages' => [
                'prospectacion' => ['status' => 'pending', 'date' => null],
                'atencion' => ['status' => 'pending', 'date' => null],
                'facturacion' => ['status' => 'pending', 'date' => null],
                'servicio_postventa' => ['status' => 'pending', 'date' => null],
                'upselling' => ['status' => 'pending', 'date' => null]
            ]
        ];
        
        // Check prospectacion
        $stage['stages']['prospectacion']['status'] = 'completed';
        $stage['stages']['prospectacion']['date'] = $contact['created_at'];
        $stage['progress'] = 20;
        
        // Check if affiliated
        if (!empty($affiliations)) {
            $stage['current'] = 'afiliado';
            $stage['stages']['atencion']['status'] = 'completed';
            $stage['stages']['atencion']['date'] = $affiliations[0]['affiliation_date'];
            $stage['progress'] = 40;
            
            // Check payment
            if ($affiliations[0]['payment_status'] === 'paid') {
                $stage['stages']['facturacion']['status'] = 'completed';
                $stage['progress'] = 60;
            }
        }
        
        // Check services
        if (!empty($contracts)) {
            $stage['stages']['servicio_postventa']['status'] = 'completed';
            $stage['progress'] = 80;
        }
        
        // Check for upselling (multiple affiliations or membership upgrade)
        if (count($affiliations) > 1) {
            $stage['stages']['upselling']['status'] = 'completed';
            $stage['progress'] = 100;
        }
        
        return $stage;
    }
    
    private function getRecommendations(array $contact, array $affiliations, array $contracts): array {
        $recommendations = [];
        
        // If prospect, recommend affiliation
        if ($contact['contact_type'] === 'prospecto') {
            $recommendations[] = [
                'type' => 'affiliation',
                'title' => 'Convertir a Afiliado',
                'description' => 'Este prospecto está listo para afiliarse. Ofrecer membresía.',
                'action' => 'afiliados/nuevo?prospect_id=' . $contact['id']
            ];
        }
        
        // If affiliate with basic membership
        if (!empty($affiliations) && ($affiliations[0]['membership_code'] ?? '') === 'BASICA') {
            $recommendations[] = [
                'type' => 'upselling',
                'title' => 'Upgrade a Membresía PYME',
                'description' => 'Ofrecer beneficios adicionales de membresía PYME o PREMIER.',
                'action' => 'journey/upselling'
            ];
        }
        
        // If no services contracted
        if (empty($contracts) && !empty($affiliations)) {
            $recommendations[] = [
                'type' => 'crossselling',
                'title' => 'Ofrecer Servicios Adicionales',
                'description' => 'El afiliado no ha contratado servicios. Ofrecer cursos, salones, marketing.',
                'action' => 'journey/crossselling'
            ];
        }
        
        // If expiring soon
        if (!empty($affiliations)) {
            $daysToExpire = (strtotime($affiliations[0]['expiration_date']) - time()) / 86400;
            if ($daysToExpire <= 30 && $daysToExpire > 0) {
                $recommendations[] = [
                    'type' => 'renewal',
                    'title' => 'Renovación Próxima',
                    'description' => 'Membresía vence en ' . ceil($daysToExpire) . ' días. Contactar para renovación.',
                    'action' => 'afiliados/' . $contact['id']
                ];
            }
        }
        
        return $recommendations;
    }
}
