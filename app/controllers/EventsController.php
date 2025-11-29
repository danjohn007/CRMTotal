<?php
/**
 * Events Controller
 * Manages events (internal, external, third-party)
 */
class EventsController extends Controller {
    
    // Constants for ticket limits
    private const MAX_TICKETS_PER_REGISTRATION = 5;
    private const GUEST_TICKET_LIMIT = 1;
    
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
        $qrCode = null;
        $registrationCode = null;
        $totalAmount = 0;
        
        // Get PayPal configuration
        $paypalClientId = $this->configModel->get('paypal_client_id', '');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate anti-spam
            $expectedSum = (int) base64_decode($this->getInput('expected_sum', ''));
            $userSum = (int) $this->getInput('spam_check', 0);
            
            if ($userSum !== $expectedSum) {
                $error = 'La verificación anti-spam es incorrecta. Por favor, intenta de nuevo.';
            } else {
                $isGuest = (int) $this->getInput('is_guest', 0);
                // Guests can only register limited tickets - enforce on server side
                $tickets = $isGuest 
                    ? self::GUEST_TICKET_LIMIT 
                    : max(1, min(self::MAX_TICKETS_PER_REGISTRATION, (int) $this->getInput('tickets', 1)));
                $isOwnerRepresentative = (int) $this->getInput('is_owner_representative', 1);
                $isActiveAffiliateInput = (int) $this->getInput('is_active_affiliate', 0);
                $guestType = $this->sanitize($this->getInput('guest_type', ''));
                
                $registrationData = [
                    'guest_name' => $this->sanitize($this->getInput('name', '')),
                    'guest_email' => $this->sanitize($this->getInput('email', '')),
                    'guest_phone' => $this->sanitize($this->getInput('phone', '')),
                    'guest_rfc' => $this->sanitize($this->getInput('rfc', '')),
                    'tickets' => $tickets,
                    'is_guest' => $isGuest,
                    'is_owner_representative' => $isOwnerRepresentative,
                    'guest_type' => $isGuest ? $guestType : null,
                    'payment_status' => $event['is_paid'] ? 'pending' : 'free'
                ];
                
                // If not owner/representative, get attendee details
                if (!$isOwnerRepresentative && !$isGuest) {
                    $registrationData['attendee_name'] = $this->sanitize($this->getInput('attendee_name', ''));
                    $registrationData['attendee_position'] = $this->sanitize($this->getInput('attendee_position', ''));
                    $registrationData['attendee_phone'] = $this->sanitize($this->getInput('attendee_phone', ''));
                    $registrationData['attendee_email'] = $this->sanitize($this->getInput('attendee_email', ''));
                }
                
                // Get additional attendees data
                $additionalAttendees = $this->getInput('additional_attendees', []);
                if (is_array($additionalAttendees)) {
                    $registrationData['additional_attendees'] = json_encode($additionalAttendees);
                }
                
                // Validate guest type is required when registering as guest
                if ($isGuest && empty($guestType)) {
                    $error = 'Debes seleccionar un tipo de invitado.';
                }
                
                // Validate phone (10 digits)
                if (!$error && !empty($registrationData['guest_phone']) && !preg_match('/^\d{10}$/', $registrationData['guest_phone'])) {
                    $error = 'El teléfono debe tener exactamente 10 dígitos.';
                }
                
                if (!$error) {
                    // Always verify affiliate status on server side for security
                    // Frontend data (is_active_affiliate) cannot be trusted as it can be manipulated
                    $isActiveAffiliate = $this->eventModel->isActiveAffiliate($registrationData['guest_email']);
                    
                    // Get company ID if registering as affiliate (for courtesy ticket check)
                    $contactModel = new Contact();
                    $existingContact = $contactModel->findBy('corporate_email', $registrationData['guest_email']);
                    $companyId = $existingContact['id'] ?? null;
                    
                    // Calculate total amount with presale pricing logic
                    // Check if we're within the presale period
                    $isPresalePeriod = false;
                    if (!empty($event['promo_end_date'])) {
                        $promoEndDate = strtotime($event['promo_end_date']);
                        $now = time();
                        $isPresalePeriod = ($now <= $promoEndDate);
                    }
                    
                    // Determine price per ticket based on affiliate status and presale period
                    // Priority: 1. Promo Member Price, 2. Member Price, 3. Promo Price, 4. Regular Price
                    $pricePerTicket = (float) ($event['price'] ?? 0);
                    
                    if ($isActiveAffiliate && !$isGuest) {
                        // Affiliate pricing
                        if ($isPresalePeriod && (float) ($event['promo_member_price'] ?? 0) > 0) {
                            // Presale affiliate price
                            $pricePerTicket = (float) $event['promo_member_price'];
                        } elseif ((float) ($event['member_price'] ?? 0) > 0) {
                            // Regular affiliate price
                            $pricePerTicket = (float) $event['member_price'];
                        }
                    } else {
                        // Non-affiliate pricing (public/guest)
                        if ($isPresalePeriod && (float) ($event['promo_price'] ?? 0) > 0) {
                            // Presale public price
                            $pricePerTicket = (float) $event['promo_price'];
                        }
                        // else use regular price
                    }
                    
                    $freeTickets = 0;
                    
                    if ($event['is_paid']) {
                        // Check for courtesy ticket eligibility
                        // Only owner/representative of active affiliate gets ONE free ticket
                        // Check if this company already received a courtesy ticket for this event
                        if (!$isGuest && $isActiveAffiliate && ($event['free_for_affiliates'] ?? 1) && $isOwnerRepresentative) {
                            // Check if this company (by RFC or email) already got a courtesy ticket
                            $hasCourtesyTicket = $this->eventModel->hasCourtesyTicket($id, $registrationData['guest_email'], $registrationData['guest_rfc'] ?? null);
                            if (!$hasCourtesyTicket) {
                                $freeTickets = 1; // One courtesy ticket for active affiliates
                            }
                        }
                        $totalAmount = ($tickets - $freeTickets) * $pricePerTicket;
                        if ($totalAmount < 0) $totalAmount = 0;
                        
                        if ($totalAmount == 0) {
                            $registrationData['payment_status'] = 'free';
                        }
                    } else {
                        $totalAmount = 0;
                        $registrationData['payment_status'] = 'free';
                    }
                    
                    // Store total amount in registration
                    $registrationData['total_amount'] = $totalAmount;
                    
                    // Multiple registrations are now allowed
                    try {
                        $registrationId = $this->eventModel->registerAttendee($id, $registrationData);
                        
                        // Handle contact creation based on registration type
                        if (!$existingContact) {
                            if ($isGuest) {
                                // Create contact entry for guest as 'invitado'
                                $contactModel->create([
                                    'corporate_email' => $registrationData['guest_email'],
                                    'phone' => $registrationData['guest_phone'],
                                    'owner_name' => $registrationData['guest_name'],
                                    'contact_type' => 'invitado',
                                    'source_channel' => $event['is_paid'] ? 'evento_pagado' : 'evento_gratuito',
                                    'profile_completion' => 15,
                                    'completion_stage' => 'A'
                                ]);
                            } else {
                                // Determine contact type based on registration
                                $contactType = 'prospecto';
                                if (!$isOwnerRepresentative && isset($registrationData['attendee_name'])) {
                                    $contactType = 'colaborador_empresa';
                                }
                                
                                // Create new contact from registration
                                $contactModel->create([
                                    'rfc' => $registrationData['guest_rfc'] ?? null,
                                    'corporate_email' => $registrationData['guest_email'],
                                    'phone' => $registrationData['guest_phone'],
                                    'owner_name' => $registrationData['guest_name'],
                                    'contact_type' => $contactType,
                                    'source_channel' => $event['is_paid'] ? 'evento_pagado' : 'evento_gratuito',
                                    'profile_completion' => 25,
                                    'completion_stage' => 'A'
                                ]);
                            }
                        }
                        
                        // Create contact record for the event attendee (when not owner/representative)
                        if (!$isGuest && !$isOwnerRepresentative && !empty($registrationData['attendee_email'])) {
                            $existingAttendeeContact = $contactModel->findBy('corporate_email', $registrationData['attendee_email']);
                            if (!$existingAttendeeContact) {
                                $contactModel->create([
                                    'corporate_email' => $registrationData['attendee_email'],
                                    'phone' => $registrationData['attendee_phone'] ?? '',
                                    'owner_name' => $registrationData['attendee_name'],
                                    'position' => $registrationData['attendee_position'] ?? null,
                                    'contact_type' => 'colaborador_empresa',
                                    'source_channel' => $event['is_paid'] ? 'evento_pagado' : 'evento_gratuito',
                                    'profile_completion' => 15,
                                    'completion_stage' => 'A'
                                ]);
                            }
                        }
                        
                        // Create individual registrations for additional attendees
                        // This allows them to have their own QR codes and appear individually in attendance control
                        $additionalRegistrationIds = [];
                        if (!empty($additionalAttendees) && is_array($additionalAttendees)) {
                            foreach ($additionalAttendees as $attendee) {
                                if (!empty($attendee['email'])) {
                                    // Check for existing contact
                                    $existingAttendee = $contactModel->findBy('corporate_email', $attendee['email']);
                                    if (!$existingAttendee) {
                                        $contactModel->create([
                                            'corporate_email' => $this->sanitize($attendee['email']),
                                            'phone' => $this->sanitize($attendee['phone'] ?? ''),
                                            'owner_name' => $this->sanitize($attendee['name'] ?? ''),
                                            'contact_type' => 'colaborador_empresa',
                                            'source_channel' => $event['is_paid'] ? 'evento_pagado' : 'evento_gratuito',
                                            'profile_completion' => 15,
                                            'completion_stage' => 'A'
                                        ]);
                                    }
                                    
                                    // Create individual registration for this additional attendee
                                    $additionalRegData = [
                                        'guest_name' => $this->sanitize($attendee['name'] ?? ''),
                                        'guest_email' => $this->sanitize($attendee['email']),
                                        'guest_phone' => $this->sanitize($attendee['phone'] ?? ''),
                                        'guest_rfc' => $registrationData['guest_rfc'] ?? null, // Inherit RFC from main registration
                                        'tickets' => 1, // Each additional attendee gets 1 ticket
                                        'is_guest' => 0,
                                        'is_owner_representative' => 0,
                                        'parent_registration_id' => $registrationId,
                                        'payment_status' => $registrationData['payment_status'] // Same payment status as main registration
                                    ];
                                    
                                    $additionalRegId = $this->eventModel->registerAttendee($id, $additionalRegData);
                                    $additionalRegistrationIds[] = [
                                        'id' => $additionalRegId,
                                        'data' => $additionalRegData
                                    ];
                                }
                            }
                        }
                        
                        // Send confirmation email
                        $this->sendConfirmationEmail($registrationId, $event, $registrationData);
                        
                        // If payment is free, generate and send QR code immediately
                        if ($registrationData['payment_status'] === 'free') {
                            $this->generateAndSendQR($registrationId, $event, $registrationData);
                            
                            // Generate and send QR codes for additional attendees
                            foreach ($additionalRegistrationIds as $additionalReg) {
                                $this->generateAndSendQR($additionalReg['id'], $event, $additionalReg['data']);
                            }
                            
                            // Get the QR code filename for display
                            $regData = $this->db->queryOne(
                                "SELECT registration_code, qr_code FROM event_registrations WHERE id = :id",
                                ['id' => $registrationId]
                            );
                            $qrCode = $regData['qr_code'] ?? null;
                            $registrationCode = $regData['registration_code'] ?? null;
                            
                            $success = '¡Registro exitoso! Tu código QR de acceso se muestra a continuación. Se ha enviado una copia a: ' . $registrationData['guest_email'];
                        } else {
                            $success = '¡Registro exitoso! Te hemos enviado un correo de confirmación con el enlace de pago a: ' . $registrationData['guest_email'];
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
            'registrationEmail' => $registrationData['guest_email'] ?? null,
            'qrCode' => $qrCode,
            'registrationCode' => $registrationCode,
            'totalAmount' => $totalAmount,
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
    
    /**
     * Public payment page for pending event registrations
     */
    public function payment(): void {
        $code = $this->params['code'] ?? '';
        
        if (empty($code)) {
            $_SESSION['flash_error'] = 'Código de registro no válido.';
            $this->redirect('');
        }
        
        // Get registration with event details
        $registration = $this->eventModel->getRegistrationByCode($code);
        
        if (!$registration) {
            $_SESSION['flash_error'] = 'Registro no encontrado.';
            $this->redirect('');
        }
        
        // Get full event details
        $event = $this->eventModel->find($registration['event_id']);
        
        if (!$event) {
            $_SESSION['flash_error'] = 'Evento no encontrado.';
            $this->redirect('');
        }
        
        // Check if already paid
        if ($registration['payment_status'] === 'paid' || $registration['payment_status'] === 'free') {
            // Redirect to ticket page
            $this->redirect('evento/boleto/' . $code);
        }
        
        // Get PayPal configuration
        $paypalClientId = $this->configModel->get('paypal_client_id', '');
        
        $this->view('events/payment', [
            'pageTitle' => 'Pago - ' . $event['title'],
            'event' => $event,
            'registration' => $registration,
            'paypalClientId' => $paypalClientId,
            'csrf_token' => $this->csrfToken()
        ]);
    }
    
    /**
     * Public printable ticket page
     */
    public function printableTicket(): void {
        $code = $this->params['code'] ?? '';
        
        if (empty($code)) {
            $_SESSION['flash_error'] = 'Código de registro no válido.';
            $this->redirect('');
        }
        
        // Get registration with event details
        $registration = $this->eventModel->getRegistrationByCode($code);
        
        if (!$registration) {
            $_SESSION['flash_error'] = 'Registro no encontrado.';
            $this->redirect('');
        }
        
        // Get full event details
        $event = $this->eventModel->find($registration['event_id']);
        
        if (!$event) {
            $_SESSION['flash_error'] = 'Evento no encontrado.';
            $this->redirect('');
        }
        
        // Check payment status - only show ticket if paid or free
        if ($registration['payment_status'] !== 'paid' && $registration['payment_status'] !== 'free') {
            // Redirect to payment page
            $this->redirect('evento/pago/' . $code);
        }
        
        // Get contact info for owner_name and legal_representative if available
        $contactModel = new Contact();
        $contact = null;
        if (!empty($registration['contact_id'])) {
            $contact = $contactModel->find($registration['contact_id']);
        } elseif (!empty($registration['guest_email'])) {
            $contact = $contactModel->findBy('corporate_email', $registration['guest_email']);
        }
        
        $this->view('events/printable_ticket', [
            'pageTitle' => 'Boleto de Acceso - ' . $event['title'],
            'event' => $event,
            'registration' => $registration,
            'contact' => $contact,
            'configModel' => $this->configModel
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
            'promo_member_price' => (float) $this->getInput('promo_member_price', 0),
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
            
            // Check if this is a pending payment email or a confirmation email
            $isPendingPayment = ($event['is_paid'] && $registrationData['payment_status'] === 'pending');
            
            if ($isPendingPayment) {
                // PENDING PAYMENT EMAIL - HTML Template
                $subject = "Registro Pendiente - " . $event['title'];
                $amountToPay = $registrationData['total_amount'] ?? ($event['price'] * $registrationData['tickets']);
                $paymentUrl = BASE_URL . '/evento/pago/' . $registrationCode;
                
                $body = $this->buildPendingPaymentEmailTemplate($event, $registrationData, $registrationCode, $amountToPay, $paymentUrl);
            } else {
                // FREE/COURTESY EMAIL - Will be sent via generateAndSendQR with QR code
                // This is just a basic confirmation for tracking purposes
                $subject = "Confirmación de Registro - " . $event['title'];
                
                $body = $this->buildConfirmationEmailTemplate($event, $registrationData, $registrationCode);
            }
            
            // Send HTML email
            $headers = "From: " . ($this->configModel->get('smtp_from_name', 'CRM CCQ')) . " <noreply@camaradecomercioqro.mx>\r\n";
            $headers .= "Reply-To: " . ($this->configModel->get('contact_email', 'info@camaradecomercioqro.mx')) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            mail($to, $subject, $body, $headers);
            
            // Update confirmation sent flag
            $this->eventModel->updateConfirmationSent($registrationId);
        } catch (Exception $e) {
            // Log error but don't fail the registration
            error_log("Error sending confirmation email: " . $e->getMessage());
        }
    }
    
    /**
     * Build HTML template for pending payment email
     */
    private function buildPendingPaymentEmailTemplate(array $event, array $registrationData, string $registrationCode, float $amount, string $paymentUrl): string {
        $eventDate = date('d/m/Y', strtotime($event['start_date']));
        $eventTime = date('H:i', strtotime($event['start_date'])) . ' - ' . date('H:i', strtotime($event['end_date']));
        $location = $event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['location'] ?? '');
        // guest_name field contains either person name or company name depending on how the user registered
        $guestName = htmlspecialchars($registrationData['guest_name']);
        $eventTitle = htmlspecialchars($event['title']);
        $tickets = (int) $registrationData['tickets'];
        $formattedAmount = number_format($amount, 2);
        
        // Get system colors from config
        $primaryColor = $this->configModel->get('primary_color', '#1e40af');
        $secondaryColor = $this->configModel->get('secondary_color', '#3b82f6');
        $accentColor = $this->configModel->get('accent_color', '#10b981');
        
        // Get logo URL
        $siteLogo = $this->configModel->get('site_logo', '');
        $logoHtml = '';
        if (!empty($siteLogo)) {
            $logoUrl = BASE_URL . $siteLogo;
            $logoHtml = '<img src="' . htmlspecialchars($logoUrl) . '" alt="Logo" style="max-height: 60px; max-width: 200px;">';
        } else {
            $logoHtml = '<span style="color: white; font-weight: bold; font-size: 18px;">CÁMARA DE COMERCIO DE QUERÉTARO</span>';
        }
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Pendiente - {$eventTitle}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa;">
    <!-- Header with Logo -->
    <div style="background-color: {$primaryColor}; padding: 20px; text-align: center;">
        {$logoHtml}
    </div>
    
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 40px;">
        <!-- Greeting -->
        <h1 style="font-size: 28px; color: #333; margin: 0 0 20px 0; font-weight: bold;">Hola {$guestName},</h1>
        
        <p style="font-size: 16px; color: #333; line-height: 1.6; margin: 0 0 30px 0;">
            Tu registro para el evento <strong>{$eventTitle}</strong> ha sido recibido exitosamente.
        </p>
        
        <!-- Event Info Box -->
        <div style="border: 2px solid {$primaryColor}; border-radius: 15px; padding: 30px; margin: 30px 0;">
            <h2 style="color: {$primaryColor}; text-align: center; font-size: 22px; margin: 0 0 25px 0;">Información del Evento</h2>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Evento:</span> {$eventTitle}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Fecha:</span> {$eventDate}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Hora:</span> {$eventTime}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Ubicación:</span> {$location}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Nombre:</span> {$guestName}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Empresa/Razón Social:</span> {$guestName}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Boletos Solicitados:</span> {$tickets}
            </div>
        </div>
        
        <!-- Payment Section -->
        <div style="background-color: #fff9e6; border: 2px solid #f5a623; border-radius: 15px; padding: 30px; margin: 30px 0; text-align: center;">
            <h3 style="color: #f5a623; font-size: 20px; margin: 0 0 15px 0;">
                ⚠️ Pago Pendiente
            </h3>
            
            <p style="color: #333; font-size: 16px; font-weight: bold; margin: 0 0 20px 0;">
                Para completar tu registro y recibir tus boletos, debes realizar el pago de:
            </p>
            
            <p style="color: {$primaryColor}; font-size: 42px; font-weight: bold; margin: 0 0 25px 0;">
                \${$formattedAmount} MXN
            </p>
            
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{$paymentUrl}" style="height:50px;v-text-anchor:middle;width:250px;" arcsize="10%" stroke="f" fillcolor="{$primaryColor}">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:sans-serif;font-size:18px;font-weight:bold;">💳 Realizar Pago Ahora</center>
            </v:roundrect>
            <![endif]-->
            <!--[if !mso]><!-->
            <a href="{$paymentUrl}" style="display: inline-block; background-color: {$primaryColor}; color: #ffffff !important; text-decoration: none; padding: 18px 45px; border-radius: 12px; font-size: 18px; font-weight: bold; mso-hide: all;">
                💳 Realizar Pago Ahora
            </a>
            <!--<![endif]-->
            
            <p style="color: #666; font-size: 14px; margin: 25px 0 0 0;">
                También puedes acceder al enlace de pago desde tu código de registro:<br>
                <strong style="color: #333;">{$registrationCode}</strong>
            </p>
        </div>
        
        <!-- Important Notes -->
        <div style="margin: 30px 0;">
            <p style="font-weight: bold; color: #333; font-size: 16px; margin: 0 0 10px 0;">Importante:</p>
            <ul style="color: #666; font-size: 14px; line-height: 1.8; margin: 0; padding-left: 20px;">
                <li>Guarda este correo electrónico</li>
                <li>Completa tu pago para recibir tus boletos digitales</li>
                <li>Una vez pagado, recibirás un correo con tus boletos</li>
            </ul>
        </div>
        
        <p style="color: #333; font-size: 16px; margin: 30px 0;">¡Nos vemos en el evento!</p>
    </div>
    
    <!-- Footer -->
    <div style="background-color: #333; padding: 25px; text-align: center;">
        <p style="color: white; font-size: 18px; margin: 0;">Solución Digital desarrollada por&nbsp;<a href="https://www.impactosdigitales.com/" style="color: #4da6ff; text-decoration: none;">ID</a></p>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Build HTML template for confirmation email (basic, without QR)
     */
    private function buildConfirmationEmailTemplate(array $event, array $registrationData, string $registrationCode): string {
        $eventDate = date('d/m/Y', strtotime($event['start_date']));
        $eventTime = date('H:i', strtotime($event['start_date'])) . ' - ' . date('H:i', strtotime($event['end_date']));
        $location = $event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['location'] ?? '');
        $guestName = htmlspecialchars($registrationData['guest_name']);
        $eventTitle = htmlspecialchars($event['title']);
        $tickets = (int) $registrationData['tickets'];
        
        // Get system colors from config
        $primaryColor = $this->configModel->get('primary_color', '#1e40af');
        
        // Get logo URL
        $siteLogo = $this->configModel->get('site_logo', '');
        $logoHtml = '';
        if (!empty($siteLogo)) {
            $logoUrl = BASE_URL . $siteLogo;
            $logoHtml = '<img src="' . htmlspecialchars($logoUrl) . '" alt="Logo" style="max-height: 60px; max-width: 200px;">';
        } else {
            $logoHtml = '<span style="color: white; font-weight: bold; font-size: 18px;">CÁMARA DE COMERCIO DE QUERÉTARO</span>';
        }
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso - {$eventTitle}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa;">
    <!-- Header with Logo -->
    <div style="background-color: {$primaryColor}; padding: 20px; text-align: center;">
        {$logoHtml}
    </div>
    
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 40px;">
        <h1 style="font-size: 28px; color: #333; margin: 0 0 20px 0;">Hola {$guestName},</h1>
        
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            Tu registro para el evento <strong>{$eventTitle}</strong> ha sido recibido exitosamente.
        </p>
        
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            En unos momentos recibirás otro correo con tu código QR de acceso.
        </p>
        
        <div style="border: 2px solid {$primaryColor}; border-radius: 15px; padding: 30px; margin: 30px 0;">
            <h2 style="color: {$primaryColor}; text-align: center; font-size: 22px; margin: 0 0 25px 0;">Información del Evento</h2>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Evento:</span> {$eventTitle}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Fecha:</span> {$eventDate}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Hora:</span> {$eventTime}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Ubicación:</span> {$location}
            </div>
            
            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <span style="color: {$primaryColor}; font-weight: bold;">Boletos:</span> {$tickets}
            </div>
        </div>
        
        <p style="color: #333; font-size: 16px;">Código de registro: <strong>{$registrationCode}</strong></p>
        
        <p style="color: #333; font-size: 16px; margin: 30px 0;">¡Te esperamos!</p>
    </div>
    
    <!-- Footer -->
    <div style="background-color: #333; padding: 25px; text-align: center;">
        <p style="color: white; font-size: 18px; margin: 0;">Solución Digital desarrollada por&nbsp;<a href="https://www.impactosdigitales.com/" style="color: #4da6ff; text-decoration: none;">ID</a></p>
    </div>
</body>
</html>
HTML;
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
            
            // Data to encode in QR
            $qrData = BASE_URL . '/evento/verificar/' . $qrRegistrationCode;
            
            // Create QR directory
            $qrDir = PUBLIC_PATH . '/uploads/qr/';
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0750, true);
            }
            
            $qrFilename = 'qr_' . $qrRegistrationCode . '.png';
            $qrPath = $qrDir . $qrFilename;
            
            // Get QR provider from config
            $qrProvider = $this->configModel->get('qr_api_provider', 'local');
            $qrSize = (int) $this->configModel->get('qr_size', 350);
            
            $qrContent = null;
            
            // Try multiple QR generation methods
            if ($qrProvider === 'google' || $qrProvider === 'qrserver') {
                // Try QR Server API first (more reliable than deprecated Google Charts)
                $qrServerUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$qrSize}x{$qrSize}&data=" . urlencode($qrData);
                $qrContent = @file_get_contents($qrServerUrl);
                
                // If QR Server fails, try Google Charts as backup
                if (!$qrContent && $qrProvider === 'google') {
                    $googleUrl = "https://chart.googleapis.com/chart?cht=qr&chs={$qrSize}x{$qrSize}&chl=" . urlencode($qrData);
                    $qrContent = @file_get_contents($googleUrl);
                }
            }
            
            // If API methods fail or provider is 'local', use local PHP generation
            if (!$qrContent) {
                $qrContent = $this->generateLocalQR($qrData, $qrSize);
            }
            
            // Save QR code if generated
            if ($qrContent) {
                file_put_contents($qrPath, $qrContent);
                
                // Update database with QR filename
                $this->db->update('event_registrations', [
                    'qr_code' => $qrFilename
                ], 'id = :id', ['id' => $registrationId]);
            } else {
                error_log("QR generation failed for registration: " . $qrRegistrationCode);
                return;
            }
            
            // Send QR code email with HTML template
            $subject = "Boleto de Acceso - " . $event['title'];
            
            // Build the HTML email with QR code embedded
            $body = $this->buildAccessTicketEmailTemplate($event, $registrationData, $qrRegistrationCode, $qrFilename);
            
            // Send HTML email
            $headers = "From: " . ($this->configModel->get('smtp_from_name', 'CRM CCQ')) . " <noreply@camaradecomercioqro.mx>\r\n";
            $headers .= "Reply-To: " . ($this->configModel->get('contact_email', 'info@camaradecomercioqro.mx')) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            // Send email to primary and optionally to attendee
            $this->sendToRegistrantEmails($registrationData, $subject, $body, $headers);
            
            // Update QR sent flag
            $this->eventModel->updateQRSent($registrationId);
        } catch (Exception $e) {
            // Log error but don't fail
            error_log("Error generating/sending QR code: " . $e->getMessage());
        }
    }
    
    /**
     * Send email to registrant's primary email and attendee email (if different)
     * 
     * This method ensures that the digital ticket is delivered to both the company/main
     * registrant email and the actual event attendee (when different from owner).
     * 
     * @param array $registrationData Registration data containing guest_email and attendee_email
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string $headers Email headers
     * @return void
     */
    private function sendToRegistrantEmails(array $registrationData, string $subject, string $body, string $headers): void {
        // Send to primary email (guest_email - company/main registrant)
        $primaryEmail = $registrationData['guest_email'];
        $primaryResult = @mail($primaryEmail, $subject, $body, $headers);
        if (!$primaryResult) {
            error_log("Failed to send ticket email to primary email: " . $primaryEmail);
        }
        
        // Also send to attendee email if different (when attendee is not owner/representative)
        $attendeeEmail = $registrationData['attendee_email'] ?? '';
        if (!empty($attendeeEmail) && strtolower($attendeeEmail) !== strtolower($primaryEmail)) {
            $attendeeResult = @mail($attendeeEmail, $subject, $body, $headers);
            if (!$attendeeResult) {
                error_log("Failed to send ticket email to attendee email: " . $attendeeEmail);
            }
        }
    }
    
    /**
     * Build HTML template for access ticket email with QR code
     * This is the email sent for FREE, COURTESY, or PAID (after payment) registrations
     */
    private function buildAccessTicketEmailTemplate(array $event, array $registrationData, string $registrationCode, string $qrFilename): string {
        $eventDate = date('d/m/Y', strtotime($event['start_date']));
        $eventTime = date('H:i', strtotime($event['start_date'])) . ' - ' . date('H:i', strtotime($event['end_date']));
        $location = $event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['location'] ?? '');
        $address = htmlspecialchars($event['address'] ?? $location);
        // guest_name field contains either person name or company name depending on how the user registered
        $guestName = htmlspecialchars($registrationData['guest_name']);
        $eventTitle = htmlspecialchars($event['title']);
        $tickets = (int) $registrationData['tickets'];
        $qrUrl = BASE_URL . '/uploads/qr/' . $qrFilename;
        $ticketUrl = BASE_URL . '/evento/boleto/' . $registrationCode;
        $contactEmail = htmlspecialchars($this->configModel->get('contact_email', 'contacto@camaradecomercioqro.mx'));
        $contactPhone = htmlspecialchars($this->configModel->get('contact_phone', '4425375301'));
        
        // Get system colors from config
        $primaryColor = $this->configModel->get('primary_color', '#1e40af');
        $secondaryColor = $this->configModel->get('secondary_color', '#3b82f6');
        $accentColor = $this->configModel->get('accent_color', '#10b981');
        
        // Get logo URL
        $siteLogo = $this->configModel->get('site_logo', '');
        $logoHtml = '';
        if (!empty($siteLogo)) {
            $logoUrl = BASE_URL . $siteLogo;
            $logoHtml = '<img src="' . htmlspecialchars($logoUrl) . '" alt="Logo" style="max-height: 60px; max-width: 200px;">';
        } else {
            $logoHtml = '<div style="background-color: white; display: inline-block; padding: 10px; border-radius: 5px;"><span style="color: ' . $primaryColor . '; font-weight: bold; font-size: 12px;">CÁMARA<br>DE COMERCIO<br>DE QUERÉTARO</span></div>';
        }
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleto de Acceso - {$eventTitle}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa;">
    <!-- Header with Logo -->
    <div style="background-color: {$primaryColor}; padding: 20px; text-align: center;">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left; vertical-align: middle;">
                    {$logoHtml}
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <!--[if mso]>
                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{$ticketUrl}" style="height:40px;v-text-anchor:middle;width:150px;" arcsize="10%" stroke="f" fillcolor="{$accentColor}">
                        <w:anchorlock/>
                        <center style="color:#ffffff;font-family:sans-serif;font-size:14px;font-weight:bold;">🖨️ Imprimir Boleto</center>
                    </v:roundrect>
                    <![endif]-->
                    <!--[if !mso]><!-->
                    <a href="{$ticketUrl}" style="background-color: {$accentColor}; color: #ffffff !important; padding: 12px 24px; border-radius: 5px; font-weight: bold; display: inline-block; text-decoration: none; mso-hide: all;">
                        🖨️ Imprimir Boleto
                    </a>
                    <!--<![endif]-->
                </td>
            </tr>
        </table>
    </div>
    
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border: 1px solid #e0e0e0;">
        <!-- Header Title -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: {$primaryColor}; font-size: 24px; margin: 0; font-weight: bold;">BOLETO DE ACCESO</h1>
            <p style="color: #666; font-size: 14px; margin: 5px 0 0 0;">Personal e Intransferible</p>
        </div>
        
        <!-- Event Title -->
        <div style="background-color: #f5f5f5; border-top: 3px solid {$primaryColor}; border-bottom: 3px solid {$primaryColor}; padding: 15px; text-align: center; margin: 20px 0;">
            <h2 style="color: {$primaryColor}; font-size: 22px; margin: 0; font-weight: bold;">{$eventTitle}</h2>
        </div>
        
        <!-- Event Details -->
        <div style="display: table; width: 100%; margin: 20px 0;">
            <div style="display: table-row;">
                <div style="display: table-cell; width: 50%; padding: 5px 10px;">
                    <span style="color: {$primaryColor};">📅</span> <strong>{$eventDate}</strong>
                </div>
                <div style="display: table-cell; width: 50%; padding: 5px 10px;">
                    <span style="color: {$primaryColor};">🕐</span> {$eventTime}
                </div>
            </div>
        </div>
        
        <div style="margin: 10px 0; color: #666;">
            <span style="color: {$primaryColor};">📍</span> {$address}
        </div>
        
        <!-- Attendee Info and QR Code -->
        <div style="display: table; width: 100%; margin: 30px 0;">
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 20px;">
                <h3 style="color: #333; font-size: 14px; margin: 0 0 15px 0; text-transform: uppercase; border-bottom: 1px solid #ddd; padding-bottom: 5px;">ASISTENTE</h3>
                
                <p style="margin: 8px 0; font-size: 14px;"><strong>Nombre:</strong><br>{$guestName}</p>
                <p style="margin: 8px 0; font-size: 14px;"><strong>Empresa:</strong><br>{$guestName}</p>
                <p style="margin: 8px 0; font-size: 14px;"><strong>Boletos:</strong> {$tickets}</p>
            </div>
            <div style="display: table-cell; width: 50%; vertical-align: top; text-align: center;">
                <h3 style="color: #333; font-size: 14px; margin: 0 0 15px 0; text-transform: uppercase;">CÓDIGO QR</h3>
                <img src="{$qrUrl}" alt="Código QR" style="width: 180px; height: 180px; border: 1px solid #ddd;">
                <p style="color: {$primaryColor}; font-size: 12px; font-family: monospace; margin: 10px 0 0 0; word-break: break-all;">{$registrationCode}</p>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div style="text-align: center; padding: 20px 0; border-top: 1px solid #ddd; color: #666; font-size: 13px;">
            <p style="margin: 5px 0;">✉️ {$contactEmail} | 📞 {$contactPhone}</p>
            <p style="margin: 5px 0;">🔒 Política de Privacidad</p>
        </div>
    </div>
    
    <!-- Instructions -->
    <div style="max-width: 600px; margin: 0 auto; background-color: #f8f9fa; padding: 25px 30px; border: 1px solid #e0e0e0; border-top: none;">
        <h3 style="color: {$primaryColor}; font-size: 16px; margin: 0 0 15px 0;">ℹ️ Instrucciones</h3>
        <ul style="color: #333; font-size: 14px; line-height: 1.8; margin: 0; padding-left: 20px;">
            <li>Imprime este boleto o guárdalo en tu dispositivo móvil</li>
            <li>Llega con 15 minutos de anticipación</li>
            <li>Presenta tu código QR en la entrada del evento</li>
            <li>Si tienes problemas, contacta al organizador</li>
        </ul>
    </div>
    
    <!-- Footer -->
    <div style="background-color: #333; padding: 25px; text-align: center;">
        <p style="color: white; font-size: 18px; margin: 0;">Solución Digital desarrollada por&nbsp;<a href="https://www.impactosdigitales.com/" style="color: #4da6ff; text-decoration: none;">ID</a></p>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Generate QR code locally using PHP GD library
     * This is a simple implementation that doesn't require external libraries
     */
    private function generateLocalQR(string $data, int $size = 350): ?string {
        // Try external API first with cURL
        $qrServerUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);
        
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $qrServerUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Shorter timeout
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $content) {
                return $content;
            }
        }
        
        // Fallback to file_get_contents with shorter timeout
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => false
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ]);
        
        $content = @file_get_contents($qrServerUrl, false, $context);
        if ($content) {
            return $content;
        }
        
        // Final fallback: Use local PHP QR code generator
        require_once APP_PATH . '/libs/QRCode.php';
        
        // Calculate optimal pixel size for desired image dimensions
        $pixelSize = QRCode::calculatePixelSize($size);
        
        $qrContent = QRCode::generate($data, $pixelSize);
        if ($qrContent) {
            return $qrContent;
        }
        
        return null;
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
