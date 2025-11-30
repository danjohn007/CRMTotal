<?php
/**
 * Affiliates Controller
 * Manages affiliates and digital unique files
 */
class AffiliatesController extends Controller {
    
    private Contact $contactModel;
    private Affiliation $affiliationModel;
    private MembershipType $membershipModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->contactModel = new Contact();
        $this->affiliationModel = new Affiliation();
        $this->membershipModel = new MembershipType();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        // Handle search query
        $search = $this->getInput('search', '');
        $membership = $this->getInput('membership', '');
        
        if (!empty($search)) {
            // Use search function for filtered results
            $affiliates = $this->searchAffiliates($search, $membership);
        } else {
            $affiliates = $this->contactModel->getAffiliates();
            
            // Filter by membership if specified
            if (!empty($membership)) {
                $affiliates = array_filter($affiliates, function($a) use ($membership) {
                    return ($a['membership_code'] ?? '') === $membership;
                });
            }
        }
        
        $expiringAffiliations = $this->affiliationModel->getExpiringSoon(30);
        
        $this->view('affiliates/index', [
            'pageTitle' => 'Afiliados',
            'currentPage' => 'afiliados',
            'affiliates' => $affiliates,
            'expiringCount' => count($expiringAffiliations)
        ]);
    }
    
    /**
     * Search affiliates by name, RFC, phone, WhatsApp, or email
     */
    private function searchAffiliates(string $search, string $membership = ''): array {
        $search = '%' . trim($search) . '%';
        
        $sql = "SELECT c.*, u.name as affiliator_name,
                       a.affiliation_date, a.expiration_date, a.status as affiliation_status,
                       m.name as membership_name, m.code as membership_code
                FROM contacts c 
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id 
                LEFT JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                LEFT JOIN membership_types m ON a.membership_type_id = m.id
                WHERE c.contact_type = 'afiliado'
                AND (c.business_name LIKE :term1 
                     OR c.commercial_name LIKE :term2 
                     OR c.rfc LIKE :term3
                     OR c.phone LIKE :term4
                     OR c.whatsapp LIKE :term5
                     OR c.corporate_email LIKE :term6)";
        
        $params = [
            'term1' => $search,
            'term2' => $search,
            'term3' => $search,
            'term4' => $search,
            'term5' => $search,
            'term6' => $search
        ];
        
        if (!empty($membership)) {
            $sql .= " AND m.code = :membership";
            $params['membership'] = $membership;
        }
        
        $sql .= " ORDER BY c.business_name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function create(): void {
        $this->requireAuth();
        
        $error = null;
        $membershipTypes = $this->membershipModel->getActive();
        $userModel = new User();
        $affiliators = $userModel->getAffiliators();
        
        // Check if converting from prospect
        $prospectId = (int) $this->getInput('prospect_id', 0);
        $prospect = null;
        if ($prospectId > 0) {
            $prospect = $this->contactModel->find($prospectId);
            // Only preload if it's a prospect
            if ($prospect && $prospect['contact_type'] !== 'prospecto') {
                $prospect = null;
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $contactData = $this->getContactFormData();
                $contactData['contact_type'] = 'afiliado';
                
                try {
                    $this->db->beginTransaction();
                    
                    // Create contact
                    $contactId = $this->contactModel->create($contactData);
                    
                    // Create affiliation
                    $membershipId = (int) $this->getInput('membership_type_id');
                    $membership = $this->membershipModel->find($membershipId);
                    
                    $affiliationData = [
                        'contact_id' => $contactId,
                        'membership_type_id' => $membershipId,
                        'affiliate_user_id' => $_SESSION['user_id'],
                        'affiliation_date' => date('Y-m-d'),
                        'expiration_date' => date('Y-m-d', strtotime('+' . ($membership['duration_days'] ?? 360) . ' days')),
                        'status' => 'active',
                        'payment_status' => $this->getInput('payment_status', 'pending'),
                        'amount' => $membership['price'] ?? 0,
                        'payment_method' => $this->sanitize($this->getInput('payment_method', ''))
                    ];
                    
                    $this->affiliationModel->create($affiliationData);
                    $this->contactModel->updateCompletion($contactId);
                    
                    $this->db->commit();
                    
                    $_SESSION['flash_success'] = 'Afiliado creado exitosamente.';
                    $this->redirect('afiliados/' . $contactId);
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $error = 'Error al crear el afiliado: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('affiliates/create', [
            'pageTitle' => 'Nuevo Afiliado',
            'currentPage' => 'afiliados',
            'membershipTypes' => $membershipTypes,
            'affiliators' => $affiliators,
            'prospect' => $prospect,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Afiliado no encontrado.';
            $this->redirect('afiliados');
        }
        
        // Get affiliations history
        $affiliations = $this->affiliationModel->getByContact($id);
        
        // Get activities
        $activityModel = new Activity();
        $activities = $activityModel->getByContact($id);
        
        // Get service contracts
        $serviceContract = new ServiceContract();
        $contracts = $serviceContract->getByContact($id);
        
        $this->view('affiliates/show', [
            'pageTitle' => $contact['business_name'] ?? 'Detalle de Afiliado',
            'currentPage' => 'afiliados',
            'contact' => $contact,
            'affiliations' => $affiliations,
            'activities' => $activities,
            'contracts' => $contracts
        ]);
    }
    
    public function edit(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Afiliado no encontrado.';
            $this->redirect('afiliados');
        }
        
        $error = null;
        $membershipTypes = $this->membershipModel->getActive();
        $userModel = new User();
        $affiliators = $userModel->getAffiliators();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = $this->getContactFormData();
                
                // Handle JSON fields
                $productsSells = $this->getInput('products_sells', '');
                if ($productsSells) {
                    $data['products_sells'] = json_encode(array_filter(array_map('trim', explode(',', $productsSells))));
                }
                
                $productsBuys = $this->getInput('products_buys', '');
                if ($productsBuys) {
                    $data['products_buys'] = json_encode(array_filter(array_map('trim', explode(',', $productsBuys))));
                }
                
                try {
                    $this->contactModel->update($id, $data);
                    $this->contactModel->updateCompletion($id);
                    $_SESSION['flash_success'] = 'Afiliado actualizado exitosamente.';
                    $this->redirect('afiliados/' . $id);
                } catch (Exception $e) {
                    $error = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        // Get current affiliation
        $affiliations = $this->affiliationModel->getByContact($id);
        $currentAffiliation = $affiliations[0] ?? null;
        
        $this->view('affiliates/edit', [
            'pageTitle' => 'Editar Afiliado',
            'currentPage' => 'afiliados',
            'contact' => $contact,
            'currentAffiliation' => $currentAffiliation,
            'membershipTypes' => $membershipTypes,
            'affiliators' => $affiliators,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function digitalFile(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Afiliado no encontrado.';
            $this->redirect('afiliados');
        }
        
        // Full digital file view with comprehensive company dashboard data
        $affiliations = $this->affiliationModel->getByContact($id);
        $currentAffiliation = !empty($affiliations) ? $affiliations[0] : null;
        
        // Calculate days remaining for current affiliation
        $daysRemaining = 0;
        if ($currentAffiliation && !empty($currentAffiliation['expiration_date'])) {
            $daysRemaining = max(0, floor((strtotime($currentAffiliation['expiration_date']) - time()) / 86400));
        }
        
        // Get activities history
        $activityModel = new Activity();
        $activities = $activityModel->getByContact($id);
        
        // Get service contracts (cross & upselling)
        $serviceContract = new ServiceContract();
        $contracts = $serviceContract->getByContact($id);
        
        // Get event registrations and attendance for this contact
        $eventModel = new Event();
        $eventRegistrations = $this->db->fetchAll(
            "SELECT er.*, e.title as event_title, e.start_date, e.location, e.event_type
             FROM event_registrations er
             JOIN events e ON er.event_id = e.id
             WHERE er.guest_email = :email OR er.guest_rfc = :rfc
             ORDER BY e.start_date DESC
             LIMIT 20",
            ['email' => $contact['corporate_email'], 'rfc' => $contact['rfc'] ?? '']
        );
        
        // Get attendance count
        $attendanceCount = $this->db->queryOne(
            "SELECT COUNT(*) as count
             FROM event_registrations er
             WHERE (er.guest_email = :email OR er.guest_rfc = :rfc)
             AND er.attended = 1",
            ['email' => $contact['corporate_email'], 'rfc' => $contact['rfc'] ?? '']
        );
        
        // Calculate total amount paid in the year
        $yearlyPayments = $this->db->queryOne(
            "SELECT COALESCE(SUM(amount), 0) as total
             FROM affiliations
             WHERE contact_id = :id
             AND payment_status = 'paid'
             AND YEAR(affiliation_date) = YEAR(CURDATE())",
            ['id' => $id]
        );
        
        // Get invoice history
        $invoices = $this->db->fetchAll(
            "SELECT * FROM affiliations
             WHERE contact_id = :id
             AND invoice_number IS NOT NULL
             ORDER BY affiliation_date DESC",
            ['id' => $id]
        );
        
        // Get membership benefits if applicable
        $membershipBenefits = null;
        if ($currentAffiliation && !empty($currentAffiliation['membership_type_id'])) {
            $membershipBenefits = $this->membershipModel->find($currentAffiliation['membership_type_id']);
        }
        
        // Get collaborators (contacts linked as colaborador_empresa with same RFC or email domain)
        $emailDomain = '';
        if (!empty($contact['corporate_email'])) {
            $parts = explode('@', $contact['corporate_email']);
            $emailDomain = count($parts) > 1 ? $parts[1] : '';
        }
        
        $collaborators = [];
        if (!empty($emailDomain) || !empty($contact['rfc'])) {
            $collaborators = $this->db->fetchAll(
                "SELECT * FROM contacts
                 WHERE contact_type = 'colaborador_empresa'
                 AND id != :id
                 AND (
                     (corporate_email LIKE :domain AND :domain != '')
                     OR (rfc = :rfc AND :rfc != '')
                 )
                 ORDER BY created_at DESC
                 LIMIT 20",
                ['id' => $id, 'domain' => '%@' . $emailDomain, 'rfc' => $contact['rfc'] ?? '']
            );
        }
        
        $this->view('affiliates/digital_file', [
            'pageTitle' => 'EDA - ' . $contact['business_name'],
            'currentPage' => 'afiliados',
            'contact' => $contact,
            'affiliations' => $affiliations,
            'currentAffiliation' => $currentAffiliation,
            'daysRemaining' => $daysRemaining,
            'activities' => $activities,
            'contracts' => $contracts,
            'eventRegistrations' => $eventRegistrations,
            'attendanceCount' => $attendanceCount['count'] ?? 0,
            'yearlyPayments' => $yearlyPayments['total'] ?? 0,
            'invoices' => $invoices,
            'membershipBenefits' => $membershipBenefits,
            'collaborators' => $collaborators
        ]);
    }
    
    public function expirations(): void {
        $this->requireAuth();
        
        $days = (int) $this->getInput('days', 30);
        $expiringAffiliations = $this->affiliationModel->getExpiringSoon($days);
        
        $this->view('affiliates/expirations', [
            'pageTitle' => 'Vencimientos Próximos',
            'currentPage' => 'afiliados',
            'expiringAffiliations' => $expiringAffiliations,
            'days' => $days
        ]);
    }
    
    public function former(): void {
        $this->requireAuth();
        
        $formerAffiliates = $this->contactModel->getFormerAffiliates();
        
        $this->view('affiliates/former', [
            'pageTitle' => 'Exafiliados',
            'currentPage' => 'afiliados',
            'formerAffiliates' => $formerAffiliates
        ]);
    }
    
    private function getContactFormData(): array {
        return [
            'rfc' => $this->sanitize($this->getInput('rfc', '')),
            'whatsapp' => $this->sanitize($this->getInput('whatsapp', '')),
            'business_name' => $this->sanitize($this->getInput('business_name', '')),
            'commercial_name' => $this->sanitize($this->getInput('commercial_name', '')),
            'owner_name' => $this->sanitize($this->getInput('owner_name', '')),
            'legal_representative' => $this->sanitize($this->getInput('legal_representative', '')),
            'corporate_email' => $this->sanitize($this->getInput('corporate_email', '')),
            'phone' => $this->sanitize($this->getInput('phone', '')),
            'industry' => $this->sanitize($this->getInput('industry', '')),
            'niza_classification' => $this->sanitize($this->getInput('niza_classification', '')),
            'niza_custom_category' => $this->sanitize($this->getInput('niza_custom_category', '')),
            'discount_percentage' => (float) $this->getInput('discount_percentage', 0),
            'commercial_address' => $this->sanitize($this->getInput('commercial_address', '')),
            'fiscal_address' => $this->sanitize($this->getInput('fiscal_address', '')),
            'city' => $this->sanitize($this->getInput('city', 'Santiago de Querétaro')),
            'state' => $this->sanitize($this->getInput('state', 'Querétaro')),
            'postal_code' => $this->sanitize($this->getInput('postal_code', '')),
            'google_maps_url' => $this->sanitize($this->getInput('google_maps_url', '')),
            'website' => $this->sanitize($this->getInput('website', '')),
            'facebook' => $this->sanitize($this->getInput('facebook', '')),
            'instagram' => $this->sanitize($this->getInput('instagram', '')),
            'linkedin' => $this->sanitize($this->getInput('linkedin', '')),
            'twitter' => $this->sanitize($this->getInput('twitter', '')),
            'whatsapp_sales' => $this->sanitize($this->getInput('whatsapp_sales', '')),
            'whatsapp_purchases' => $this->sanitize($this->getInput('whatsapp_purchases', '')),
            'whatsapp_admin' => $this->sanitize($this->getInput('whatsapp_admin', '')),
            'assigned_affiliate_id' => (int) $this->getInput('assigned_affiliate_id', $_SESSION['user_id']),
            'notes' => $this->sanitize($this->getInput('notes', ''))
        ];
    }
}
