<?php
/**
 * Expedientes Controller
 * Manages the "Expediente Digital Único" multi-stage registration
 * for the Afiliador level
 */
class ExpedientesController extends Controller {
    
    private Contact $contactModel;
    private Affiliation $affiliationModel;
    private MembershipType $membershipModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->contactModel = new Contact();
        $this->affiliationModel = new Affiliation();
        $this->membershipModel = new MembershipType();
    }
    
    /**
     * List all expedientes digitales for the current affiliator
     */
    public function index(): void {
        $this->requireAuth();
        
        // Validate session variables
        $userRole = isset($_SESSION['user_role']) ? (string)$_SESSION['user_role'] : '';
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        
        // Validate role is one of allowed values
        $allowedRoles = ['superadmin', 'direccion', 'jefe_comercial', 'afiliador', 'contabilidad', 'consejero', 'mesa_directiva'];
        if (!in_array($userRole, $allowedRoles)) {
            $userRole = '';
        }
        
        // Get affiliates based on user role
        if (in_array($userRole, ['superadmin', 'direccion', 'jefe_comercial'])) {
            $affiliates = $this->contactModel->getAffiliates();
        } else {
            // For afiliador, only show their assigned affiliates
            $affiliates = $this->db->fetchAll(
                "SELECT c.*, u.name as affiliator_name,
                        a.affiliation_date, a.expiration_date, a.status as affiliation_status,
                        m.name as membership_name, m.code as membership_code
                 FROM contacts c 
                 LEFT JOIN users u ON c.assigned_affiliate_id = u.id 
                 LEFT JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                 LEFT JOIN membership_types m ON a.membership_type_id = m.id
                 WHERE c.contact_type = 'afiliado' 
                 AND c.assigned_affiliate_id = :user_id
                 ORDER BY c.business_name",
                ['user_id' => $userId]
            );
        }
        
        // Calculate stats for the view
        $totalAffiliates = count($affiliates);
        $incompleteExpedientes = 0;
        foreach ($affiliates as $affiliate) {
            if (($affiliate['profile_completion'] ?? 0) < 100) {
                $incompleteExpedientes++;
            }
        }
        
        $this->view('expedientes/index', [
            'pageTitle' => 'Expedientes Digitales Únicos',
            'currentPage' => 'expedientes',
            'affiliates' => $affiliates,
            'totalAffiliates' => $totalAffiliates,
            'incompleteExpedientes' => $incompleteExpedientes
        ]);
    }
    
    /**
     * Show individual expediente with modal info
     */
    public function show(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Expediente no encontrado.';
            $this->redirect('expedientes');
        }
        
        // Get affiliations history
        $affiliations = $this->affiliationModel->getByContact($id);
        $currentAffiliation = !empty($affiliations) ? $affiliations[0] : null;
        
        // Calculate days remaining
        $daysRemaining = 0;
        if ($currentAffiliation && !empty($currentAffiliation['expiration_date'])) {
            $daysRemaining = max(0, floor((strtotime($currentAffiliation['expiration_date']) - time()) / 86400));
        }
        
        // Get activities
        $activityModel = new Activity();
        $activities = $activityModel->getByContact($id);
        
        // Get service contracts (cross & upselling)
        $serviceContract = new ServiceContract();
        $contracts = $serviceContract->getByContact($id);
        
        // Get event registrations
        $eventRegistrations = $this->db->fetchAll(
            "SELECT er.*, e.title as event_title, e.start_date, e.location, e.event_type
             FROM event_registrations er
             JOIN events e ON er.event_id = e.id
             WHERE er.guest_email = :email OR er.guest_rfc = :rfc
             ORDER BY e.start_date DESC
             LIMIT 20",
            ['email' => $contact['corporate_email'] ?? '', 'rfc' => $contact['rfc'] ?? '']
        );
        
        // Get attendance count
        $attendanceCount = $this->db->queryOne(
            "SELECT COUNT(*) as count
             FROM event_registrations er
             WHERE (er.guest_email = :email OR er.guest_rfc = :rfc)
             AND er.attended = 1",
            ['email' => $contact['corporate_email'] ?? '', 'rfc' => $contact['rfc'] ?? '']
        );
        
        // Get benefit usage count
        if ($currentAffiliation) {
            $benefitUsage = $this->db->fetchAll(
                "SELECT * FROM benefit_usage WHERE affiliation_id = :affiliation_id ORDER BY usage_date DESC",
                ['affiliation_id' => $currentAffiliation['id'] ?? 0]
            );
        } else {
            $benefitUsage = [];
        }
        
        // Get membership benefits
        $membershipBenefits = null;
        if ($currentAffiliation && !empty($currentAffiliation['membership_type_id'])) {
            $membershipBenefits = $this->membershipModel->find($currentAffiliation['membership_type_id']);
        }
        
        // Calculate profile stages completion
        $stageA = $this->calculateStageACompletion($contact);
        $stageB = $this->calculateStageBCompletion($contact);
        $stageC = $this->calculateStageCCompletion($contact, $currentAffiliation);
        
        $this->view('expedientes/show', [
            'pageTitle' => 'Expediente Digital - ' . ($contact['business_name'] ?? ''),
            'currentPage' => 'expedientes',
            'contact' => $contact,
            'affiliations' => $affiliations,
            'currentAffiliation' => $currentAffiliation,
            'daysRemaining' => $daysRemaining,
            'activities' => $activities,
            'contracts' => $contracts,
            'eventRegistrations' => $eventRegistrations,
            'attendanceCount' => $attendanceCount['count'] ?? 0,
            'benefitUsage' => $benefitUsage,
            'membershipBenefits' => $membershipBenefits,
            'stageA' => $stageA,
            'stageB' => $stageB,
            'stageC' => $stageC
        ]);
    }
    
    /**
     * Edit Stage A of expediente (25%)
     * RFC, owner/legal rep, business name, commercial name, address, whatsapp
     */
    public function editStageA(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Expediente no encontrado.';
            $this->redirect('expedientes');
        }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = [
                    'rfc' => $this->sanitize($this->getInput('rfc', '')),
                    'owner_name' => $this->sanitize($this->getInput('owner_name', '')),
                    'legal_representative' => $this->sanitize($this->getInput('legal_representative', '')),
                    'business_name' => $this->sanitize($this->getInput('business_name', '')),
                    'commercial_name' => $this->sanitize($this->getInput('commercial_name', '')),
                    'commercial_address' => $this->sanitize($this->getInput('commercial_address', '')),
                    'fiscal_address' => $this->sanitize($this->getInput('fiscal_address', '')),
                    'whatsapp' => $this->sanitize($this->getInput('whatsapp', '')),
                ];
                
                try {
                    $this->contactModel->update($id, $data);
                    $this->contactModel->updateCompletion($id);
                    
                    $_SESSION['flash_success'] = 'Etapa 1 actualizada exitosamente.';
                    
                    // Redirect to next stage or back to show
                    if ($this->getInput('next_stage')) {
                        $this->redirect('expedientes/' . $id . '/etapa-b');
                    } else {
                        $this->redirect('expedientes/' . $id);
                    }
                } catch (Exception $e) {
                    $error = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        $stageA = $this->calculateStageACompletion($contact);
        
        $this->view('expedientes/edit_stage_a', [
            'pageTitle' => 'Expediente Digital - Etapa 1',
            'currentPage' => 'expedientes',
            'contact' => $contact,
            'stageA' => $stageA,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Edit Stage B of expediente (35%)
     * Sales contact, purchasing contact, branches, website, products sold/bought
     */
    public function editStageB(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Expediente no encontrado.';
            $this->redirect('expedientes');
        }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                // Check if sales contact is same as owner
                $sameAsOwner = $this->getInput('sales_same_as_owner', '');
                
                $salesName = $this->sanitize($this->getInput('sales_contact_name', ''));
                $salesEmail = $this->sanitize($this->getInput('sales_contact_email', ''));
                $salesWhatsapp = $this->sanitize($this->getInput('whatsapp_sales', ''));
                
                if ($sameAsOwner === '1') {
                    $salesName = $contact['owner_name'] ?? $contact['legal_representative'] ?? '';
                    $salesEmail = $contact['corporate_email'] ?? '';
                    $salesWhatsapp = $contact['whatsapp'] ?? '';
                }
                
                // Handle products arrays - ensure they are valid arrays before processing
                $productsSells = $this->getInput('products_sells', []);
                $productsBuys = $this->getInput('products_buys', []);
                
                if (is_array($productsSells)) {
                    $productsSells = array_filter(array_map(function($item) {
                        return $this->sanitize(trim((string)$item));
                    }, $productsSells));
                } elseif (is_string($productsSells)) {
                    $productsSells = array_filter(array_map(function($item) {
                        return $this->sanitize(trim((string)$item));
                    }, explode(',', $productsSells)));
                } else {
                    $productsSells = [];
                }
                
                if (is_array($productsBuys)) {
                    $productsBuys = array_filter(array_map(function($item) {
                        return $this->sanitize(trim((string)$item));
                    }, $productsBuys));
                } elseif (is_string($productsBuys)) {
                    $productsBuys = array_filter(array_map(function($item) {
                        return $this->sanitize(trim((string)$item));
                    }, explode(',', $productsBuys)));
                } else {
                    $productsBuys = [];
                }
                
                // Ensure arrays are valid before slicing
                $productsSells = is_array($productsSells) ? array_values($productsSells) : [];
                $productsBuys = is_array($productsBuys) ? array_values($productsBuys) : [];
                
                $data = [
                    'whatsapp_sales' => $salesWhatsapp,
                    'whatsapp_purchases' => $this->sanitize($this->getInput('whatsapp_purchases', '')),
                    'website' => $this->sanitize($this->getInput('website', '')),
                    'products_sells' => json_encode(array_slice($productsSells, 0, 4)),
                    'products_buys' => json_encode(array_slice($productsBuys, 0, 2)),
                ];
                
                try {
                    $this->contactModel->update($id, $data);
                    $this->contactModel->updateCompletion($id);
                    
                    $_SESSION['flash_success'] = 'Etapa 2 actualizada exitosamente.';
                    
                    if ($this->getInput('next_stage')) {
                        $this->redirect('expedientes/' . $id . '/etapa-c');
                    } else {
                        $this->redirect('expedientes/' . $id);
                    }
                } catch (Exception $e) {
                    $error = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        // Get branches
        $branches = $this->db->fetchAll(
            "SELECT * FROM contact_branches WHERE contact_id = :id ORDER BY is_main DESC, name",
            ['id' => $id]
        );
        
        $stageB = $this->calculateStageBCompletion($contact);
        
        $this->view('expedientes/edit_stage_b', [
            'pageTitle' => 'Expediente Digital - Etapa 2',
            'currentPage' => 'expedientes',
            'contact' => $contact,
            'branches' => $branches,
            'stageB' => $stageB,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Edit Stage C of expediente (40%)
     * Affiliation date, CSF/invoice, services of interest
     */
    public function editStageC(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Expediente no encontrado.';
            $this->redirect('expedientes');
        }
        
        $affiliations = $this->affiliationModel->getByContact($id);
        $currentAffiliation = !empty($affiliations) ? $affiliations[0] : null;
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                // Update affiliation info if there is a current one
                if ($currentAffiliation) {
                    $affiliationData = [
                        'invoice_number' => $this->sanitize($this->getInput('invoice_number', '')),
                        'notes' => $this->sanitize($this->getInput('affiliation_notes', ''))
                    ];
                    
                    $this->affiliationModel->update($currentAffiliation['id'], $affiliationData);
                }
                
                // Update contact notes with services of interest
                $servicesInterest = $this->getInput('services_interest', []);
                if (is_array($servicesInterest)) {
                    // Sanitize each service interest value
                    $servicesInterest = array_map(function($s) {
                        return $this->sanitize((string)$s);
                    }, $servicesInterest);
                    $servicesInterest = implode(', ', $servicesInterest);
                }
                
                $contactNotes = $contact['notes'] ?? '';
                if (!empty($servicesInterest)) {
                    // Remove existing services of interest section if present to avoid duplication
                    $contactNotes = preg_replace('/^Servicios de interés:.*?\n\n/s', '', $contactNotes);
                    $contactNotes = "Servicios de interés: " . $servicesInterest . "\n\n" . $contactNotes;
                }
                
                $this->contactModel->update($id, ['notes' => $contactNotes]);
                $this->contactModel->updateCompletion($id);
                
                $_SESSION['flash_success'] = 'Expediente Digital Único completado exitosamente.';
                $this->redirect('expedientes/' . $id);
            }
        }
        
        // Get available services
        $services = $this->db->fetchAll(
            "SELECT * FROM services WHERE is_active = 1 ORDER BY category, name"
        );
        
        $stageC = $this->calculateStageCCompletion($contact, $currentAffiliation);
        
        $this->view('expedientes/edit_stage_c', [
            'pageTitle' => 'Expediente Digital - Etapa 3',
            'currentPage' => 'expedientes',
            'contact' => $contact,
            'currentAffiliation' => $currentAffiliation,
            'services' => $services,
            'stageC' => $stageC,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Calculate Stage A completion (25% of total)
     */
    private function calculateStageACompletion(array $contact): array {
        $fields = [
            'rfc' => !empty($contact['rfc']),
            'owner_name' => !empty($contact['owner_name']) || !empty($contact['legal_representative']),
            'business_name' => !empty($contact['business_name']),
            'commercial_name' => !empty($contact['commercial_name']),
            'commercial_address' => !empty($contact['commercial_address']) || !empty($contact['fiscal_address']),
            'whatsapp' => !empty($contact['whatsapp'])
        ];
        
        $completed = count(array_filter($fields));
        $total = count($fields);
        $percentage = ($completed / $total) * 25;
        
        return [
            'fields' => $fields,
            'completed' => $completed,
            'total' => $total,
            'percentage' => round($percentage, 1),
            'stage_complete' => $completed === $total
        ];
    }
    
    /**
     * Calculate Stage B completion (35% of total)
     */
    private function calculateStageBCompletion(array $contact): array {
        $productsSells = [];
        $productsBuys = [];
        
        if (!empty($contact['products_sells'])) {
            $productsSells = json_decode($contact['products_sells'], true) ?: [];
        }
        if (!empty($contact['products_buys'])) {
            $productsBuys = json_decode($contact['products_buys'], true) ?: [];
        }
        
        $fields = [
            'whatsapp_sales' => !empty($contact['whatsapp_sales']),
            'whatsapp_purchases' => !empty($contact['whatsapp_purchases']),
            'website' => !empty($contact['website']),
            'products_sells' => count($productsSells) >= 1,
            'products_buys' => count($productsBuys) >= 1
        ];
        
        $completed = count(array_filter($fields));
        $total = count($fields);
        $percentage = ($completed / $total) * 35;
        
        return [
            'fields' => $fields,
            'completed' => $completed,
            'total' => $total,
            'percentage' => round($percentage, 1),
            'stage_complete' => $completed === $total
        ];
    }
    
    /**
     * Calculate Stage C completion (40% of total)
     */
    private function calculateStageCCompletion(array $contact, ?array $affiliation): array {
        $fields = [
            'affiliation_date' => !empty($affiliation['affiliation_date']),
            'invoice_or_csf' => !empty($affiliation['invoice_number'])
        ];
        
        $completed = count(array_filter($fields));
        $total = count($fields);
        $percentage = ($completed / $total) * 40;
        
        return [
            'fields' => $fields,
            'completed' => $completed,
            'total' => $total,
            'percentage' => round($percentage, 1),
            'stage_complete' => $completed === $total
        ];
    }
}
