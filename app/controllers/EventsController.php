<?php
/**
 * Events Controller
 * Manages events (internal, external, third-party)
 */
class EventsController extends Controller {
    
    private Event $eventModel;
    private Config $configModel;
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->eventModel = new Event();
        $this->configModel = new Config();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $upcoming = $this->eventModel->getUpcoming(20);
        $past = $this->eventModel->getPast(10);
        $stats = $this->eventModel->getEventStats();
        
        $this->view('events/index', [
            'pageTitle' => 'Eventos',
            'currentPage' => 'eventos',
            'upcomingEvents' => $upcoming,
            'pastEvents' => $past,
            'stats' => $stats,
            'eventTypes' => $this->getEventTypes()
        ]);
    }
    
    public function create(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial', 'afiliador']);
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = $this->getFormData();
                $data['created_by'] = $_SESSION['user_id'];
                
                // Handle custom URL or generate one
                $customUrl = $this->sanitize($this->getInput('registration_url', ''));
                if (!empty($customUrl)) {
                    // Validate URL format and availability
                    if (!preg_match('/^[a-z0-9\-]+$/', $customUrl)) {
                        $error = 'La URL solo puede contener letras minúsculas, números y guiones.';
                    } elseif ($this->eventModel->findByRegistrationUrl($customUrl)) {
                        $error = 'Esta URL ya está en uso. Por favor, elige otra.';
                    } else {
                        $data['registration_url'] = $customUrl;
                    }
                } else {
                    $data['registration_url'] = $this->eventModel->generateUniqueUrl($data['title']);
                }
                
                // Handle category (custom or from dropdown)
                $category = $this->getInput('category', '');
                if ($category === '__other__') {
                    $category = $this->sanitize($this->getInput('category_other', ''));
                }
                $data['category'] = $category;
                
                // Handle target audiences
                $audiences = $this->getInput('target_audiences', []);
                if (is_array($audiences)) {
                    $data['target_audiences'] = json_encode($audiences);
                }
                
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $imageResult = $this->handleImageUpload($_FILES['image']);
                    if ($imageResult['success']) {
                        $data['image'] = $imageResult['filename'];
                    } else {
                        $error = $imageResult['error'];
                    }
                }
                
                if (!$error) {
                    try {
                        $id = $this->eventModel->create($data);
                        $_SESSION['flash_success'] = 'Evento creado exitosamente.';
                        $this->redirect('eventos/' . $id);
                    } catch (Exception $e) {
                        $error = 'Error al crear el evento: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $this->view('events/create', [
            'pageTitle' => 'Nuevo Evento',
            'currentPage' => 'eventos',
            'eventTypes' => $this->getEventTypes(),
            'eventCategories' => $this->eventModel->getCategories(),
            'audiences' => $this->getAudiences(),
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $event = $this->eventModel->find($id);
        
        if (!$event) {
            $_SESSION['flash_error'] = 'Evento no encontrado.';
            $this->redirect('eventos');
        }
        
        $registrations = $this->eventModel->getRegistrations($id);
        $registeredCount = $this->eventModel->getRegistrationCount($id);
        $attendedCount = $this->eventModel->getAttendanceCount($id);
        
        $this->view('events/show', [
            'pageTitle' => $event['title'],
            'currentPage' => 'eventos',
            'event' => $event,
            'registrations' => $registrations,
            'registeredCount' => $registeredCount,
            'attendedCount' => $attendedCount,
            'eventTypes' => $this->getEventTypes()
        ]);
    }
    
    public function edit(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $event = $this->eventModel->find($id);
        
        if (!$event) {
            $_SESSION['flash_error'] = 'Evento no encontrado.';
            $this->redirect('eventos');
        }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $data = $this->getFormData();
                
                // Handle custom URL
                $customUrl = $this->sanitize($this->getInput('registration_url', ''));
                if (!empty($customUrl) && $customUrl !== $event['registration_url']) {
                    if (!preg_match('/^[a-z0-9\-]+$/', $customUrl)) {
                        $error = 'La URL solo puede contener letras minúsculas, números y guiones.';
                    } elseif ($this->eventModel->findByRegistrationUrl($customUrl)) {
                        $error = 'Esta URL ya está en uso. Por favor, elige otra.';
                    } else {
                        $data['registration_url'] = $customUrl;
                    }
                }
                
                // Handle target audiences
                $audiences = $this->getInput('target_audiences', []);
                if (is_array($audiences)) {
                    $data['target_audiences'] = json_encode($audiences);
                }
                
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $imageResult = $this->handleImageUpload($_FILES['image']);
                    if ($imageResult['success']) {
                        $data['image'] = $imageResult['filename'];
                        // Delete old image if exists
                        if (!empty($event['image'])) {
                            $oldPath = PUBLIC_PATH . '/uploads/events/' . $event['image'];
                            if (file_exists($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                    } else {
                        $error = $imageResult['error'];
                    }
                }
                
                if (!$error) {
                    try {
                        $this->eventModel->update($id, $data);
                        $_SESSION['flash_success'] = 'Evento actualizado exitosamente.';
                        $this->redirect('eventos/' . $id);
                    } catch (Exception $e) {
                        $error = 'Error al actualizar: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $this->view('events/edit', [
            'pageTitle' => 'Editar Evento',
            'currentPage' => 'eventos',
            'event' => $event,
            'eventTypes' => $this->getEventTypes(),
            'eventCategories' => $this->eventModel->getCategories(),
            'audiences' => $this->getAudiences(),
            'error' => $error,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function registration(): void {
        // Public event registration - try by URL first, then by ID
        $id = (int) ($this->params['id'] ?? 0);
        $event = null;
        
        if ($id > 0) {
            $event = $this->eventModel->find($id);
        }
        
        if (!$event || $event['status'] !== 'published') {
            $_SESSION['flash_error'] = 'Evento no disponible.';
            $this->redirect('');
        }
        
        $error = null;
        $success = null;
        $registrationId = null;
        
        // Get PayPal configuration
        $paypalClientId = $this->configModel->get('paypal_client_id', '');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate anti-spam
            $expectedSum = (int) base64_decode($this->getInput('expected_sum', ''));
            $userSum = (int) $this->getInput('spam_check', 0);
            
            if ($userSum !== $expectedSum) {
                $error = 'La verificación anti-spam es incorrecta. Por favor, intenta de nuevo.';
            } else {
                $tickets = max(1, min(5, (int) $this->getInput('tickets', 1)));
                
                $registrationData = [
                    'guest_name' => $this->sanitize($this->getInput('name', '')),
                    'guest_email' => $this->sanitize($this->getInput('email', '')),
                    'guest_phone' => $this->sanitize($this->getInput('phone', '')),
                    'guest_rfc' => $this->sanitize($this->getInput('rfc', '')),
                    'tickets' => $tickets,
                    'payment_status' => $event['is_paid'] ? 'pending' : 'free'
                ];
                
                // Validate phone (10 digits)
                if (!empty($registrationData['guest_phone']) && !preg_match('/^\d{10}$/', $registrationData['guest_phone'])) {
                    $error = 'El teléfono debe tener exactamente 10 dígitos.';
                }
                
                if (!$error) {
                    // Check if this is an active affiliate and event offers free access
                    $isActiveAffiliate = $this->eventModel->isActiveAffiliate($registrationData['guest_email']);
                    if ($event['free_for_affiliates'] && $isActiveAffiliate && $tickets === 1) {
                        // First ticket is free for active affiliates
                        $registrationData['payment_status'] = 'free';
                    }
                    
                    // Multiple registrations are now allowed
                    try {
                        $registrationId = $this->eventModel->registerAttendee($id, $registrationData);
                        
                        // Check if this email belongs to a contact - convert to prospect
                        $contactModel = new Contact();
                        $existingContact = $contactModel->findBy('corporate_email', $registrationData['guest_email']);
                        
                        if (!$existingContact && $registrationData['guest_rfc']) {
                            // Create new prospect from registration
                            $contactModel->create([
                                'rfc' => $registrationData['guest_rfc'],
                                'corporate_email' => $registrationData['guest_email'],
                                'phone' => $registrationData['guest_phone'],
                                'owner_name' => $registrationData['guest_name'],
                                'contact_type' => 'prospecto',
                                'source_channel' => $event['is_paid'] ? 'evento_pagado' : 'evento_gratuito',
                                'profile_completion' => 25,
                                'completion_stage' => 'A'
                            ]);
                        }
                        
                        // Send confirmation email
                        $this->sendConfirmationEmail($registrationId, $event, $registrationData);
                        
                        // If payment is free, generate and send QR code immediately
                        if ($registrationData['payment_status'] === 'free') {
                            $this->generateAndSendQR($registrationId, $event, $registrationData);
                            $success = '¡Registro exitoso! Te hemos enviado un correo con tu código QR de acceso.';
                        } else {
                            $success = '¡Registro exitoso! Te hemos enviado un correo de confirmación con el enlace de pago.';
                        }
                    } catch (Exception $e) {
                        $error = 'Error en el registro: ' . $e->getMessage();
                    }
                }
            }
        }
        
        $this->view('events/registration', [
            'pageTitle' => 'Registro - ' . $event['title'],
            'event' => $event,
            'error' => $error,
            'success' => $success,
            'registrationId' => $registrationId,
            'paypalClientId' => $paypalClientId,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    public function publicRegistration(): void {
        // Public event registration by friendly URL
        $url = $this->params['url'] ?? '';
        $event = $this->eventModel->findByRegistrationUrl($url);
        
        if (!$event || $event['status'] !== 'published') {
            $_SESSION['flash_error'] = 'Evento no disponible.';
            $this->redirect('');
        }
        
        // Redirect to the registration method with the event ID
        $this->params['id'] = $event['id'];
        $this->registration();
    }
    
    public function attendance(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $event = $this->eventModel->find($id);
        
        if (!$event) {
            $_SESSION['flash_error'] = 'Evento no encontrado.';
            $this->redirect('eventos');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registrationId = (int) $this->getInput('registration_id');
            $attended = (bool) $this->getInput('attended');
            
            $this->eventModel->markAttendance($registrationId, $attended);
            $this->json(['success' => true]);
        }
        
        $registrations = $this->eventModel->getRegistrations($id);
        
        $this->view('events/attendance', [
            'pageTitle' => 'Control de Asistencia - ' . $event['title'],
            'currentPage' => 'eventos',
            'event' => $event,
            'registrations' => $registrations,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    private function handleImageUpload(array $file): array {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Formato de imagen no permitido. Use JPG, PNG o GIF.'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'La imagen excede el tamaño máximo de 5MB.'];
        }
        
        // Validate file extension from original filename
        $originalExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($originalExtension, $allowedExtensions)) {
            return ['success' => false, 'error' => 'Extensión de archivo no permitida.'];
        }
        
        // Create upload directory if not exists with restrictive permissions
        $uploadDir = PUBLIC_PATH . '/uploads/events/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0750, true);
        }
        
        // Map MIME type to safe extension
        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        
        // Use extension based on detected MIME type, not user input
        $safeExtension = $extensionMap[$file['type']] ?? 'jpg';
        
        // Generate unique filename with safe extension
        $filename = 'event_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $safeExtension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'error' => 'Error al guardar la imagen.'];
    }
    
    private function getFormData(): array {
        return [
            'title' => $this->sanitize($this->getInput('title', '')),
            'description' => $this->sanitize($this->getInput('description', '')),
            'event_type' => $this->sanitize($this->getInput('event_type', 'interno')),
            'category' => $this->sanitize($this->getInput('category', '')),
            'start_date' => $this->getInput('start_date', ''),
            'end_date' => $this->getInput('end_date', ''),
            'location' => $this->sanitize($this->getInput('location', '')),
            'address' => $this->sanitize($this->getInput('address', '')),
            'google_maps_url' => $this->sanitize($this->getInput('google_maps_url', '')),
            'is_online' => (int) $this->getInput('is_online', 0),
            'online_url' => $this->sanitize($this->getInput('online_url', '')),
            'max_capacity' => (int) $this->getInput('max_capacity', 0),
            'is_paid' => (int) $this->getInput('is_paid', 0),
            'price' => (float) $this->getInput('price', 0),
            'promo_price' => (float) $this->getInput('promo_price', 0),
            'promo_end_date' => $this->getInput('promo_end_date', null),
            'member_price' => (float) $this->getInput('member_price', 0),
            'free_for_affiliates' => (int) $this->getInput('free_for_affiliates', 1),
            'status' => $this->sanitize($this->getInput('status', 'draft'))
        ];
    }
    
    private function getEventTypes(): array {
        // Get from database catalog or return defaults
        $types = $this->eventModel->getEventTypeCatalog();
        if (!empty($types)) {
            $result = [];
            foreach ($types as $type) {
                $result[$type['code']] = $type['name'];
            }
            return $result;
        }
        
        return [
            'interno' => 'Evento Interno CCQ',
            'publico' => 'Evento Público',
            'terceros' => 'Evento de Terceros'
        ];
    }
    
    private function getAudiences(): array {
        return [
            'afiliado' => 'Afiliados',
            'prospecto' => 'Prospectos',
            'exafiliado' => 'Exafiliados',
            'publico' => 'Público en General',
            'funcionario' => 'Funcionarios',
            'consejero' => 'Consejeros'
        ];
    }
    
    private function sendConfirmationEmail(int $registrationId, array $event, array $registrationData): void {
        try {
            // Get registration code from database
            $regCodeResult = $this->db->fetch(
                "SELECT registration_code FROM event_registrations WHERE id = :id", 
                ['id' => $registrationId]
            );
            
            if (empty($regCodeResult)) {
                return;
            }
            
            $registrationCode = $regCodeResult['registration_code'];
            
            $to = $registrationData['guest_email'];
            $subject = "Confirmación de Registro - " . $event['title'];
            
            // Build email body
            $body = "Hola " . htmlspecialchars($registrationData['guest_name']) . ",\n\n";
            $body .= "Gracias por registrarte al evento:\n\n";
            $body .= "📅 " . htmlspecialchars($event['title']) . "\n";
            $body .= "📍 " . ($event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['location'] ?? '')) . "\n";
            $body .= "🕐 " . date('d/m/Y H:i', strtotime($event['start_date'])) . " hrs\n";
            $body .= "🎫 Boletos: " . $registrationData['tickets'] . "\n\n";
            
            if ($event['is_paid'] && $registrationData['payment_status'] === 'pending') {
                $paymentUrl = BASE_URL . '/evento/pago/' . $registrationCode;
                $body .= "💳 COMPLETAR PAGO\n";
                $body .= "Para confirmar tu asistencia, completa el pago en el siguiente enlace:\n";
                $body .= $paymentUrl . "\n\n";
                $body .= "Monto a pagar: $" . number_format($event['price'] * $registrationData['tickets'], 2) . " MXN\n\n";
            }
            
            $body .= "Código de registro: " . $registrationCode . "\n\n";
            $body .= "Te esperamos!\n\n";
            $body .= "Cámara de Comercio de Querétaro\n";
            $body .= BASE_URL;
            
            // Send email using PHP mail() - in production, use PHPMailer or similar
            $headers = "From: " . ($this->configModel->get('smtp_from_name', 'CRM CCQ')) . " <noreply@camaradecomercioqro.mx>\r\n";
            $headers .= "Reply-To: " . ($this->configModel->get('contact_email', 'info@camaradecomercioqro.mx')) . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            mail($to, $subject, $body, $headers);
            
            // Update confirmation sent flag
            $this->eventModel->updateConfirmationSent($registrationId);
        } catch (Exception $e) {
            // Log error but don't fail the registration
            error_log("Error sending confirmation email: " . $e->getMessage());
        }
    }
    
    private function generateAndSendQR(int $registrationId, array $event, array $registrationData): void {
        try {
            // Get registration code from database
            $regCodeResult = $this->db->fetch(
                "SELECT registration_code FROM event_registrations WHERE id = :id", 
                ['id' => $registrationId]
            );
            
            if (empty($regCodeResult)) {
                return;
            }
            
            $qrRegistrationCode = $regCodeResult['registration_code'];
            
            // Generate QR code using Google Charts API
            // NOTE: This API is deprecated. For production, migrate to endroid/qr-code:
            // composer require endroid/qr-code
            // See: https://github.com/endroid/qr-code
            $qrData = BASE_URL . '/evento/verificar/' . $qrRegistrationCode;
            $qrImageUrl = "https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=" . urlencode($qrData);
            
            // Download QR code image
            $qrDir = PUBLIC_PATH . '/uploads/qr/';
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0750, true);
            }
            
            $qrFilename = 'qr_' . $qrRegistrationCode . '.png';
            $qrPath = $qrDir . $qrFilename;
            
            // Download and save QR code
            $qrContent = @file_get_contents($qrImageUrl);
            if ($qrContent) {
                file_put_contents($qrPath, $qrContent);
                
                // Update database with QR filename
                $this->db->update('event_registrations', [
                    'qr_code' => $qrFilename
                ], 'id = :id', ['id' => $registrationId]);
            }
            
            // Send QR code email
            $to = $registrationData['guest_email'];
            $subject = "Código QR de Acceso - " . $event['title'];
            
            $body = "Hola " . htmlspecialchars($registrationData['guest_name']) . ",\n\n";
            $body .= "¡Tu pago ha sido confirmado!\n\n";
            $body .= "Adjunto encontrarás tu código QR de acceso al evento:\n\n";
            $body .= "📅 " . htmlspecialchars($event['title']) . "\n";
            $body .= "📍 " . ($event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['location'] ?? '')) . "\n";
            $body .= "🕐 " . date('d/m/Y H:i', strtotime($event['start_date'])) . " hrs\n";
            $body .= "🎫 Boletos: " . $registrationData['tickets'] . "\n\n";
            $body .= "Presenta este código QR en el evento para registrar tu asistencia.\n\n";
            $body .= "También puedes descargar tu QR desde:\n";
            $body .= BASE_URL . '/uploads/qr/' . $qrFilename . "\n\n";
            $body .= "Código de registro: " . $qrRegistrationCode . "\n\n";
            $body .= "Te esperamos!\n\n";
            $body .= "Cámara de Comercio de Querétaro\n";
            $body .= BASE_URL;
            
            // Send email
            $headers = "From: " . ($this->configModel->get('smtp_from_name', 'CRM CCQ')) . " <noreply@camaradecomercioqro.mx>\r\n";
            $headers .= "Reply-To: " . ($this->configModel->get('contact_email', 'info@camaradecomercioqro.mx')) . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            mail($to, $subject, $body, $headers);
            
            // Update QR sent flag
            $this->eventModel->updateQRSent($registrationId);
        } catch (Exception $e) {
            // Log error but don't fail
            error_log("Error generating/sending QR code: " . $e->getMessage());
        }
    }
    
    public function categories(): void {
        $this->requireAuth();
        $this->requireRole(['superadmin', 'direccion', 'jefe_comercial']);
        
        $error = null;
        $success = null;
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrf()) {
                $error = 'Token de seguridad inválido.';
            } else {
                $action = $this->getInput('action', '');
                
                if ($action === 'create') {
                    $name = $this->sanitize($this->getInput('name', ''));
                    $description = $this->sanitize($this->getInput('description', ''));
                    $color = $this->sanitize($this->getInput('color', '#3b82f6'));
                    
                    if (!empty($name)) {
                        $this->db->insert('event_categories', [
                            'name' => $name,
                            'description' => $description,
                            'color' => $color,
                            'is_active' => 1
                        ]);
                        $success = 'Categoría creada exitosamente.';
                    } else {
                        $error = 'El nombre es requerido.';
                    }
                } elseif ($action === 'toggle') {
                    $id = (int) $this->getInput('id', 0);
                    if ($id > 0) {
                        $category = $this->db->queryOne("SELECT is_active FROM event_categories WHERE id = :id", ['id' => $id]);
                        if ($category) {
                            $this->db->update('event_categories', [
                                'is_active' => $category['is_active'] ? 0 : 1
                            ], 'id = :id', ['id' => $id]);
                            $success = 'Estado de categoría actualizado.';
                        }
                    }
                } elseif ($action === 'delete') {
                    $id = (int) $this->getInput('id', 0);
                    if ($id > 0) {
                        $this->db->delete('event_categories', 'id = :id', ['id' => $id]);
                        $success = 'Categoría eliminada.';
                    }
                }
            }
        }
        
        // Get all categories
        $categories = $this->eventModel->getCategories();
        
        $this->view('events/categories', [
            'pageTitle' => 'Categorías de Eventos',
            'currentPage' => 'eventos',
            'categories' => $categories,
            'error' => $error,
            'success' => $success,
            'csrf_token' => $this->csrfToken()
        ]);
    }
}
