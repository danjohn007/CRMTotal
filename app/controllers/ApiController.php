<?php
/**
 * API Controller
 * RESTful API endpoints
 */
class ApiController extends Controller {
    
    public function prospects(): void {
        $this->requireAuth();
        
        $contactModel = new Contact();
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        if (in_array($role, ['superadmin', 'direccion', 'jefe_comercial'])) {
            $prospects = $contactModel->getProspects();
        } else {
            $prospects = $contactModel->getProspects($userId);
        }
        
        $this->json([
            'success' => true,
            'data' => $prospects,
            'total' => count($prospects)
        ]);
    }
    
    public function affiliates(): void {
        $this->requireAuth();
        
        $contactModel = new Contact();
        $affiliates = $contactModel->getAffiliates();
        
        $this->json([
            'success' => true,
            'data' => $affiliates,
            'total' => count($affiliates)
        ]);
    }
    
    public function events(): void {
        $this->requireAuth();
        
        $eventModel = new Event();
        $upcoming = $eventModel->getUpcoming(50);
        
        $this->json([
            'success' => true,
            'data' => $upcoming,
            'total' => count($upcoming)
        ]);
    }
    
    public function dashboard(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        
        $contactModel = new Contact();
        $affiliationModel = new Affiliation();
        $activityModel = new Activity();
        $notificationModel = new Notification();
        
        $typeStats = $contactModel->getStatsByType();
        $monthlyStats = $affiliationModel->getMonthlyStats();
        $activityStats = $activityModel->getStats($userId);
        $unreadNotifications = $notificationModel->countUnread($userId);
        
        $this->json([
            'success' => true,
            'data' => [
                'typeStats' => $typeStats,
                'monthlyStats' => $monthlyStats,
                'activityStats' => $activityStats,
                'unreadNotifications' => $unreadNotifications
            ]
        ]);
    }
    
    public function notifications(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $notificationModel = new Notification();
        
        $notifications = $notificationModel->getUnread($userId);
        
        $this->json([
            'success' => true,
            'data' => $notifications,
            'total' => count($notifications)
        ]);
    }
    
    public function search(): void {
        $term = $this->sanitize($this->getInput('q', ''));
        
        if (strlen($term) < 2) {
            $this->json([
                'success' => false,
                'message' => 'Search term too short',
                'data' => []
            ]);
            return;
        }
        
        $contactModel = new Contact();
        $searchLog = new SearchLog();
        
        $searcherType = $this->isAuthenticated() ? 'afiliado' : 'publico';
        $results = $contactModel->search($term, $searcherType);
        
        // Log the search
        $searchLog->log($term, count($results), $searcherType);
        
        $this->json([
            'success' => true,
            'data' => $results,
            'total' => count($results)
        ]);
    }
    
    public function searchCompany(): void {
        // Public API - search company by WhatsApp or RFC
        $identifier = $this->sanitize($this->getInput('q', ''));
        
        if (strlen($identifier) < 3) {
            $this->json([
                'success' => false,
                'message' => 'Identifier too short',
                'company' => null
            ]);
            return;
        }
        
        $contactModel = new Contact();
        $company = $contactModel->identify($identifier);
        
        if ($company) {
            // Check if this is an active affiliate
            $eventModel = new Event();
            $isActiveAffiliate = $eventModel->isActiveAffiliate($company['corporate_email'] ?? '');
            
            // Return only safe fields for public access
            $this->json([
                'success' => true,
                'company' => [
                    'id' => $company['id'],
                    'business_name' => $company['business_name'],
                    'commercial_name' => $company['commercial_name'],
                    'owner_name' => $company['owner_name'],
                    'corporate_email' => $company['corporate_email'],
                    'phone' => $company['phone'],
                    'whatsapp' => $company['whatsapp'],
                    'rfc' => $company['rfc']
                ],
                'is_active_affiliate' => $isActiveAffiliate
            ]);
        } else {
            $this->json([
                'success' => false,
                'company' => null,
                'is_active_affiliate' => false
            ]);
        }
    }
    
    public function verifyEventUrl(): void {
        // Check if event URL is available
        $url = $this->sanitize($this->getInput('url', ''));
        $excludeId = (int) $this->getInput('exclude_id', 0);
        
        if (empty($url)) {
            $this->json(['available' => false]);
            return;
        }
        
        $eventModel = new Event();
        $existing = $eventModel->findByRegistrationUrl($url);
        
        // URL is available if not found or if it belongs to the current event being edited
        $available = !$existing || ($excludeId > 0 && $existing['id'] == $excludeId);
        
        $this->json(['available' => $available]);
    }
    
    public function confirmEventPayment(): void {
        // Receive payment confirmation from PayPal
        $input = json_decode(file_get_contents('php://input'), true);
        
        $registrationId = (int) ($input['registration_id'] ?? 0);
        $orderId = $this->sanitize($input['order_id'] ?? '');
        $payerEmail = $this->sanitize($input['payer_email'] ?? '');
        
        if (!$registrationId || !$orderId) {
            $this->json(['success' => false, 'message' => 'Invalid data'], 400);
            return;
        }
        
        $eventModel = new Event();
        $eventModel->updatePaymentStatus($registrationId, 'paid', $orderId);
        
        // Get registration and event details
        $registration = $this->db->queryOne(
            "SELECT er.*, e.title, e.start_date, e.location, e.is_online 
             FROM event_registrations er 
             JOIN events e ON er.event_id = e.id 
             WHERE er.id = :id",
            ['id' => $registrationId]
        );
        
        if ($registration) {
            // Generate and send QR code after payment confirmation
            $this->generateAndSendQR($registrationId, [
                'title' => $registration['title'],
                'start_date' => $registration['start_date'],
                'location' => $registration['location'],
                'is_online' => $registration['is_online']
            ], [
                'guest_name' => $registration['guest_name'],
                'guest_email' => $registration['guest_email'],
                'tickets' => $registration['tickets']
            ]);
        }
        
        $this->json(['success' => true]);
    }
    
    private function generateAndSendQR(int $registrationId, array $event, array $registrationData): void {
        try {
            $configModel = new Config();
            
            // Get registration code
            $registration = $this->db->queryOne(
                "SELECT registration_code FROM event_registrations WHERE id = :id", 
                ['id' => $registrationId]
            );
            
            if (!$registration) {
                return;
            }
            
            $registrationCode = $registration['registration_code'];
            
            // Generate QR code using Google Charts API
            // NOTE: This API is deprecated. For production, migrate to endroid/qr-code
            // TODO: Extract QR generation to a shared service class
            $qrData = BASE_URL . '/evento/verificar/' . $registrationCode;
            $qrImageUrl = "https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=" . urlencode($qrData);
            
            // Download QR code image
            $qrDir = PUBLIC_PATH . '/uploads/qr/';
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0750, true);
            }
            
            $qrFilename = 'qr_' . $registrationCode . '.png';
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
            $body .= "Código de registro: " . $registrationCode . "\n\n";
            $body .= "Te esperamos!\n\n";
            $body .= "Cámara de Comercio de Querétaro\n";
            $body .= BASE_URL;
            
            // Send email
            $headers = "From: " . ($configModel->get('smtp_from_name', 'CRM CCQ')) . " <noreply@camaradecomercioqro.mx>\r\n";
            $headers .= "Reply-To: " . ($configModel->get('contact_email', 'info@camaradecomercioqro.mx')) . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            mail($to, $subject, $body, $headers);
            
            // Update QR sent flag
            $this->db->update('event_registrations', [
                'qr_sent' => 1,
                'qr_sent_at' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $registrationId]);
        } catch (Exception $e) {
            // Log error but don't fail
            error_log("Error generating/sending QR code: " . $e->getMessage());
        }
    }
    
    public function validateEventQR(): void {
        // Validate QR code for event attendance
        $input = json_decode(file_get_contents('php://input'), true);
        
        $code = $this->sanitize($input['code'] ?? '');
        $eventId = (int) ($input['event_id'] ?? 0);
        
        if (empty($code) || $eventId <= 0) {
            $this->json(['success' => false, 'message' => 'Datos inválidos'], 400);
            return;
        }
        
        $eventModel = new Event();
        
        // Get registration by code
        $registration = $eventModel->getRegistrationByCode($code);
        
        if (!$registration) {
            $this->json(['success' => false, 'message' => 'Código QR no encontrado']);
            return;
        }
        
        // Verify the registration belongs to this event
        if ($registration['event_id'] != $eventId) {
            $this->json(['success' => false, 'message' => 'Este código no corresponde a este evento']);
            return;
        }
        
        // Check if already attended
        $alreadyAttended = (bool) $registration['attended'];
        
        // Mark attendance if not already attended
        if (!$alreadyAttended) {
            $eventModel->markAttendance($registration['id'], true);
        }
        
        $this->json([
            'success' => true,
            'registration' => [
                'id' => $registration['id'],
                'guest_name' => $registration['guest_name'],
                'guest_email' => $registration['guest_email'],
                'tickets' => $registration['tickets'] ?? 1,
                'already_attended' => $alreadyAttended
            ]
        ]);
    }
}
