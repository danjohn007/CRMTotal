<?php
/**
 * Memberships Controller
 * Manages membership types and member subscriptions
 */
class MembershipsController extends Controller {
    
    private MembershipType $membershipModel;
    private Affiliation $affiliationModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->membershipModel = new MembershipType();
        $this->affiliationModel = new Affiliation();
    }
    
    public function index(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial']);
        
        $memberships = $this->membershipModel->getAllWithStats();
        $totalRevenue = $this->membershipModel->getRevenue();
        
        $this->view('memberships/index', [
            'pageTitle' => 'Tipos de Membresía',
            'currentPage' => 'membresias',
            'memberships' => $memberships,
            'totalRevenue' => $totalRevenue
        ]);
    }
    
    public function create(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = [
                    'name' => $this->sanitize($this->getInput('name', '')),
                    'code' => strtoupper($this->sanitize($this->getInput('code', ''))),
                    'price' => (float) $this->getInput('price', 0),
                    'duration_days' => (int) $this->getInput('duration_days', 360),
                    'benefits' => json_encode($this->parseBenefits()),
                    'is_active' => (int) $this->getInput('is_active', 1)
                ];
                
                // Validate code uniqueness
                if ($this->membershipModel->findByCode($data['code'])) {
                    $error = 'El código de membresía ya existe.';
                } else {
                    try {
                        $this->membershipModel->create($data);
                        $_SESSION['flash_success'] = 'Tipo de membresía creado exitosamente.';
                        $this->redirect('membresias');
                    } catch (Exception $e) {
                        $error = 'Error al crear la membresía: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $this->view('memberships/create', [
            'pageTitle' => 'Nueva Membresía',
            'currentPage' => 'membresias',
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial']);
        
        $id = (int) ($this->params['id'] ?? 0);
        $membership = $this->membershipModel->find($id);
        
        if (!$membership) {
            $_SESSION['flash_error'] = 'Membresía no encontrada.';
            $this->redirect('membresias');
        }
        
        // Get affiliations with this membership type
        $affiliations = $this->affiliationModel->where('membership_type_id', $id);
        $revenue = $this->membershipModel->getRevenue($id);
        
        $this->view('memberships/show', [
            'pageTitle' => $membership['name'],
            'currentPage' => 'membresias',
            'membership' => $membership,
            'affiliations' => $affiliations,
            'revenue' => $revenue
        ]);
    }
    
    public function edit(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin']);
        
        $id = (int) ($this->params['id'] ?? 0);
        $membership = $this->membershipModel->find($id);
        
        if (!$membership) {
            $_SESSION['flash_error'] = 'Membresía no encontrada.';
            $this->redirect('membresias');
        }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = [
                    'name' => $this->sanitize($this->getInput('name', '')),
                    'price' => (float) $this->getInput('price', 0),
                    'duration_days' => (int) $this->getInput('duration_days', 360),
                    'benefits' => json_encode($this->parseBenefits()),
                    'characteristics' => json_encode($this->parseCharacteristics()),
                    'is_active' => (int) $this->getInput('is_active', 1)
                ];
                
                try {
                    $this->membershipModel->update($id, $data);
                    $_SESSION['flash_success'] = 'Membresía actualizada exitosamente.';
                    $this->redirect('membresias/' . $id);
                } catch (Exception $e) {
                    $error = 'Error al actualizar: ' . $e->getMessage();
                }
            }
        }
        
        $this->view('memberships/edit', [
            'pageTitle' => 'Editar Membresía',
            'currentPage' => 'membresias',
            'membership' => $membership,
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    private function parseBenefits(): array {
        $benefits = [];
        
        // Predefined benefit keys
        $benefitKeys = [
            'descuento_eventos', 'buscador', 'networking', 
            'capacitaciones', 'asesoria', 'marketing', 'publicidad', 'siem'
        ];
        
        foreach ($benefitKeys as $key) {
            $value = $this->getInput('benefit_' . $key);
            if ($value !== null && $value !== '') {
                if (is_numeric($value)) {
                    $benefits[$key] = (int) $value;
                } elseif ($value === 'true' || $value === '1') {
                    $benefits[$key] = true;
                } else {
                    $benefits[$key] = $value;
                }
            }
        }
        
        // Parse custom benefits
        $customKeys = $this->getInput('custom_benefit_key', []);
        $customValues = $this->getInput('custom_benefit_value', []);
        
        if (is_array($customKeys) && is_array($customValues)) {
            foreach ($customKeys as $index => $key) {
                $key = $this->sanitize(trim($key));
                $value = isset($customValues[$index]) ? trim($customValues[$index]) : '';
                
                if (!empty($key) && $value !== '') {
                    // Convert value types
                    if ($value === 'true') {
                        $benefits[$key] = true;
                    } elseif ($value === 'false') {
                        $benefits[$key] = false;
                    } elseif (is_numeric($value)) {
                        $benefits[$key] = (int) $value;
                    } else {
                        $benefits[$key] = $this->sanitize($value);
                    }
                }
            }
        }
        
        return $benefits;
    }
    
    /**
     * Parse characteristics from form
     */
    private function parseCharacteristics(): array {
        $characteristics = [];
        $charInputs = $this->getInput('characteristic', []);
        
        if (is_array($charInputs)) {
            foreach ($charInputs as $char) {
                $char = $this->sanitize(trim($char));
                if (!empty($char)) {
                    $characteristics[] = $char;
                }
            }
        }
        
        return $characteristics;
    }
}
