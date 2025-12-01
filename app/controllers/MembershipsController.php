<?php
/**
 * Memberships Controller
 * Manages membership types and member subscriptions
 */
class MembershipsController extends Controller {
    
    private MembershipType $membershipModel;
    private Affiliation $affiliationModel;
    private Config $configModel;
    private string $paypalClientId;
    private string $paypalSecret;
    private string $paypalMode;
    private string $paypalBaseUrl;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->membershipModel = new MembershipType();
        $this->affiliationModel = new Affiliation();
        $this->configModel = new Config();
        
        // Load PayPal credentials
        $this->paypalClientId = $this->configModel->get('paypal_client_id', '');
        $this->paypalSecret = $this->configModel->get('paypal_secret', '');
        $this->paypalMode = $this->configModel->get('paypal_mode', 'sandbox');
        $this->paypalBaseUrl = $this->paypalMode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
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
                        $membershipId = $this->membershipModel->create($data);
                        
                        // Create product in PayPal if credentials are configured
                        if (!empty($this->paypalClientId) && !empty($this->paypalSecret)) {
                            $paypalProductId = $this->createPayPalProduct($data);
                            
                            if ($paypalProductId) {
                                // Save PayPal product ID using raw SQL
                                $sql = "UPDATE membership_types SET paypal_product_id = :paypal_product_id WHERE id = :id";
                                $this->membershipModel->execute($sql, [
                                    'paypal_product_id' => $paypalProductId,
                                    'id' => $membershipId
                                ]);
                            }
                        }
                        
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
            'revenue' => $revenue,
            'paypalClientId' => $this->paypalClientId
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
    
    /**
     * Public payment page for a membership (no login required)
     */
    public function pay(): void {
        $id = (int) ($this->params['id'] ?? 0);
        $membership = $this->membershipModel->find($id);
        
        if (!$membership || !$membership['is_active']) {
            $_SESSION['flash_error'] = 'Membresía no disponible.';
            $this->redirect('');
            return;
        }
        
        // Don't require auth for public payment page
        $this->view('memberships/pay', [
            'pageTitle' => 'Suscribirse a ' . $membership['name'],
            'currentPage' => '',
            'membership' => $membership,
            'paypalClientId' => $this->paypalClientId,
            'shareUrl' => BASE_URL . '/membresias/' . $id . '/pagar'
        ]);
    }
    
    /**
     * Get PayPal Access Token
     */
    private function getPayPalAccessToken(): ?string {
        if (empty($this->paypalClientId) || empty($this->paypalSecret)) {
            return null;
        }
        
        $ch = curl_init($this->paypalBaseUrl . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->paypalClientId . ':' . $this->paypalSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $data['access_token'] ?? null;
        }
        
        return null;
    }
    
    /**
     * Create product and subscription plan in PayPal
     */
    private function createPayPalProduct(array $membershipData): ?string {
        $accessToken = $this->getPayPalAccessToken();
        if (!$accessToken) {
            return null;
        }
        
        // Step 1: Create Product
        $productData = [
            'name' => $membershipData['name'],
            'description' => 'Membresía ' . $membershipData['name'] . ' - Cámara de Comercio de Querétaro',
            'type' => 'SERVICE',
            'category' => 'MEMBERSHIP_CLUBS_AND_ORGANIZATIONS',
            'image_url' => BASE_URL . '/img/logo.png',
            'home_url' => BASE_URL
        ];
        
        $ch = curl_init($this->paypalBaseUrl . '/v1/catalogs/products');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 201) {
            error_log('PayPal Product Creation Failed: ' . $response);
            return null;
        }
        
        $product = json_decode($response, true);
        $productId = $product['id'] ?? null;
        
        if (!$productId) {
            return null;
        }
        
        // Step 2: Create Subscription Plan
        $planId = $this->createPayPalSubscriptionPlan($productId, $membershipData, $accessToken);
        
        // Return plan ID (we'll use this for subscriptions)
        return $planId;
    }
    
    /**
     * Create subscription plan in PayPal
     */
    private function createPayPalSubscriptionPlan(string $productId, array $membershipData, string $accessToken): ?string {
        // Calculate billing cycles based on duration_days
        $durationDays = (int) $membershipData['duration_days'];
        $frequency = 'YEAR';
        $interval = 1;
        
        // Adjust frequency based on duration
        if ($durationDays <= 31) {
            $frequency = 'MONTH';
            $interval = 1;
        } elseif ($durationDays <= 365) {
            $frequency = 'YEAR';
            $interval = 1;
        }
        
        $planData = [
            'product_id' => $productId,
            'name' => 'Plan ' . $membershipData['name'],
            'description' => 'Suscripción anual a membresía ' . $membershipData['name'],
            'status' => 'ACTIVE',
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => $frequency,
                        'interval_count' => $interval
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0, // 0 = infinite
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => number_format($membershipData['price'], 2, '.', ''),
                            'currency_code' => 'MXN'
                        ]
                    ]
                ]
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => '0',
                    'currency_code' => 'MXN'
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3
            ]
        ];
        
        $ch = curl_init($this->paypalBaseUrl . '/v1/billing/plans');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Prefer: return=representation'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($planData));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 201) {
            $plan = json_decode($response, true);
            return $plan['id'] ?? null;
        }
        
        error_log('PayPal Plan Creation Failed: ' . $response);
        return null;
    }
    
    /**
     * Create subscription for a membership
     */
    public function createSubscription(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 405);
            return;
        }
        
        $membershipId = (int) $this->getInput('membership_id', 0);
        $membership = $this->membershipModel->find($membershipId);
        
        if (!$membership || empty($membership['paypal_product_id'])) {
            $this->json(['error' => 'Membresía no disponible o no configurada'], 404);
            return;
        }
        
        $accessToken = $this->getPayPalAccessToken();
        if (!$accessToken) {
            $this->json(['error' => 'No se pudo autenticar con PayPal'], 500);
            return;
        }
        
        // Create subscription in PayPal
        $subscriptionData = [
            'plan_id' => $membership['paypal_product_id'],
            'application_context' => [
                'brand_name' => 'Cámara de Comercio de Querétaro',
                'locale' => 'es-MX',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'SUBSCRIBE_NOW',
                'return_url' => BASE_URL . '/membresias/' . $membershipId . '/pagar?success=true',
                'cancel_url' => BASE_URL . '/membresias/' . $membershipId . '/pagar?cancelled=true'
            ]
        ];
        
        $ch = curl_init($this->paypalBaseUrl . '/v1/billing/subscriptions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'Prefer: return=representation'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($subscriptionData));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 201) {
            $data = json_decode($response, true);
            $this->json(['success' => true, 'subscriptionId' => $data['id']]);
        } else {
            error_log('PayPal Subscription Creation Failed: ' . $response);
            $this->json(['error' => 'Error al crear suscripción', 'details' => $response], 500);
        }
    }
    
    /**
     * Get subscription details (called after approval)
     */
    public function getSubscription(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 405);
            return;
        }
        
        $subscriptionId = $this->sanitize($this->getInput('subscriptionId', ''));
        
        if (empty($subscriptionId)) {
            $this->json(['error' => 'Subscription ID requerido'], 400);
            return;
        }
        
        $accessToken = $this->getPayPalAccessToken();
        if (!$accessToken) {
            $this->json(['error' => 'No se pudo autenticar con PayPal'], 500);
            return;
        }
        
        $ch = curl_init($this->paypalBaseUrl . '/v1/billing/subscriptions/' . $subscriptionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            $this->json(['success' => true, 'subscription' => $data]);
        } else {
            error_log('PayPal Subscription Get Failed: ' . $response);
            $this->json(['error' => 'Error al obtener suscripción'], 500);
        }
    }
}
