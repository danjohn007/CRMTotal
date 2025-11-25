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
        
        $affiliates = $this->contactModel->getAffiliates();
        $expiringAffiliations = $this->affiliationModel->getExpiringSoon(30);
        
        $this->view('affiliates/index', [
            'pageTitle' => 'Afiliados',
            'currentPage' => 'afiliados',
            'affiliates' => $affiliates,
            'expiringCount' => count($expiringAffiliations)
        ]);
    }
    
    public function create(): void {
        $this->requireAuth();
        
        $error = null;
        $membershipTypes = $this->membershipModel->getActive();
        $userModel = new User();
        $affiliators = $userModel->getAffiliators();
        
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
        
        // Full digital file view
        $affiliations = $this->affiliationModel->getByContact($id);
        $activityModel = new Activity();
        $activities = $activityModel->getByContact($id);
        $serviceContract = new ServiceContract();
        $contracts = $serviceContract->getByContact($id);
        
        $this->view('affiliates/digital_file', [
            'pageTitle' => 'Expediente Digital - ' . $contact['business_name'],
            'currentPage' => 'afiliados',
            'contact' => $contact,
            'affiliations' => $affiliations,
            'activities' => $activities,
            'contracts' => $contracts
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
