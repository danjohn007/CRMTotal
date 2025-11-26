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
                ]
            ]);
        } else {
            $this->json([
                'success' => false,
                'company' => null
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
    
    /**
     * Send email with file attachment using PHP mail()
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (plain text)
     * @param string $attachmentPath Full path to attachment file
     * @param string $attachmentName Filename for attachment
     * @param string $attachmentMimeType MIME type of attachment
     * @param Config $configModel Config model instance for settings
     * @return bool Whether the email was sent successfully
     */
    private function sendEmailWithAttachment(
        string $to, 
        string $subject, 
        string $body, 
        string $attachmentPath, 
        string $attachmentName, 
        string $attachmentMimeType,
        Config $configModel
    ): bool {
        // Validate attachment exists
        if (!file_exists($attachmentPath)) {
            error_log("Attachment file not found: " . $attachmentPath);
            return false;
        }
        
        // Read and encode attachment
        $attachmentContent = file_get_contents($attachmentPath);
        if ($attachmentContent === false) {
            error_log("Failed to read attachment: " . $attachmentPath);
            return false;
        }
        $attachmentEncoded = chunk_split(base64_encode($attachmentContent));
        
        // Generate boundary with better entropy
        $boundary = uniqid('boundary_', true);
        
        // Prepare headers
        $fromName = $configModel->get('smtp_from_name', 'CRM CCQ');
        $replyTo = $configModel->get('contact_email', 'info@camaradecomercioqro.mx');
        
        $headers = "From: {$fromName} <noreply@camaradecomercioqro.mx>\r\n";
        $headers .= "Reply-To: {$replyTo}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
        
        // Build email message with attachment
        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $body . "\r\n\r\n";
        
        // Add attachment
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: {$attachmentMimeType}; name=\"{$attachmentName}\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"{$attachmentName}\"\r\n\r\n";
        $message .= $attachmentEncoded . "\r\n";
        $message .= "--{$boundary}--";
        
        // Send email
        return mail($to, $subject, $message, $headers);
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
            if (!$qrContent || !file_put_contents($qrPath, $qrContent)) {
                error_log("Failed to generate QR code for registration: " . $registrationId);
                return;
            }
            
            // Update database with QR filename
            $this->db->update('event_registrations', [
                'qr_code' => $qrFilename
            ], 'id = :id', ['id' => $registrationId]);
            
            // Send QR code email with attachment
            $to = $registrationData['guest_email'];
            $subject = "Código QR de Acceso - " . $event['title'];
            
            $emailBody = "Hola " . htmlspecialchars($registrationData['guest_name']) . ",\n\n";
            $emailBody .= "¡Tu pago ha sido confirmado!\n\n";
            $emailBody .= "Adjunto encontrarás tu código QR de acceso al evento:\n\n";
            $emailBody .= "📅 " . htmlspecialchars($event['title']) . "\n";
            $emailBody .= "📍 " . ($event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['location'] ?? '')) . "\n";
            $emailBody .= "🕐 " . date('d/m/Y H:i', strtotime($event['start_date'])) . " hrs\n";
            $emailBody .= "🎫 Boletos: " . $registrationData['tickets'] . "\n\n";
            $emailBody .= "Presenta este código QR en el evento para registrar tu asistencia.\n\n";
            $emailBody .= "También puedes descargar tu QR desde:\n";
            $emailBody .= BASE_URL . '/uploads/qr/' . $qrFilename . "\n\n";
            $emailBody .= "Código de registro: " . $registrationCode . "\n\n";
            $emailBody .= "Te esperamos!\n\n";
            $emailBody .= "Cámara de Comercio de Querétaro\n";
            $emailBody .= BASE_URL;
            
            // Send email with QR code attachment
            $this->sendEmailWithAttachment(
                $to,
                $subject,
                $emailBody,
                $qrPath,
                $qrFilename,
                'image/png',
                $configModel
            );
            
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
}
