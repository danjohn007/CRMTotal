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
        
        // Get registration and event details (including end_date and address for email template)
        $registration = $this->db->queryOne(
            "SELECT er.*, e.title, e.start_date, e.end_date, e.location, e.address, e.is_online 
             FROM event_registrations er 
             JOIN events e ON er.event_id = e.id 
             WHERE er.id = :id",
            ['id' => $registrationId]
        );
        
        if ($registration) {
            $eventData = [
                'title' => $registration['title'],
                'start_date' => $registration['start_date'],
                'end_date' => $registration['end_date'],
                'location' => $registration['location'],
                'address' => $registration['address'],
                'is_online' => $registration['is_online']
            ];
            
            // Generate and send QR code after payment confirmation
            $this->generateAndSendQR($registrationId, $eventData, [
                'guest_name' => $registration['guest_name'],
                'guest_email' => $registration['guest_email'],
                'attendee_email' => $registration['attendee_email'] ?? '',
                'tickets' => $registration['tickets']
            ]);
            
            // Generate and send QR codes for additional attendees (child registrations)
            $additionalRegistrations = $this->db->query(
                "SELECT id, guest_name, guest_email, attendee_email, tickets FROM event_registrations 
                 WHERE parent_registration_id = :parent_id",
                ['parent_id' => $registrationId]
            );
            
            if ($additionalRegistrations) {
                while ($additionalReg = $additionalRegistrations->fetch()) {
                    // Update payment status for child registration
                    $eventModel->updatePaymentStatus($additionalReg['id'], 'paid', $orderId);
                    
                    // Generate and send QR code for this additional attendee
                    $this->generateAndSendQR($additionalReg['id'], $eventData, [
                        'guest_name' => $additionalReg['guest_name'],
                        'guest_email' => $additionalReg['guest_email'],
                        'attendee_email' => $additionalReg['attendee_email'] ?? '',
                        'tickets' => $additionalReg['tickets']
                    ]);
                }
            }
        }
        
        $this->json(['success' => true, 'email' => $registration['guest_email'] ?? '']);
    }
    
    private function generateAndSendQR(int $registrationId, array $event, array $registrationData): void {
        try {
            $configModel = new Config();
            
            // Get registration code
            $registration = $this->db->fetch(
                "SELECT registration_code FROM event_registrations WHERE id = :id", 
                ['id' => $registrationId]
            );
            
            if (!$registration) {
                return;
            }
            
            $registrationCode = $registration['registration_code'];
            
            // Data to encode in QR
            $qrData = BASE_URL . '/evento/verificar/' . $registrationCode;
            
            // Create QR directory
            $qrDir = PUBLIC_PATH . '/uploads/qr/';
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0750, true);
            }
            
            $qrFilename = 'qr_' . $registrationCode . '.png';
            $qrPath = $qrDir . $qrFilename;
            
            // Get QR provider from config
            $qrProvider = $configModel->get('qr_api_provider', 'local');
            $qrSize = (int) $configModel->get('qr_size', 350);
            
            $qrContent = null;
            
            // Try QR Server API (more reliable)
            $qrServerUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$qrSize}x{$qrSize}&data=" . urlencode($qrData);
            
            // Use cURL for more reliable fetching
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $qrServerUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $qrContent = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode !== 200 || !$qrContent) {
                    $qrContent = null;
                }
            }
            
            // Fallback to file_get_contents
            if (!$qrContent) {
                $context = stream_context_create([
                    'http' => ['timeout' => 5],
                    'ssl' => ['verify_peer' => true, 'verify_peer_name' => true]
                ]);
                $qrContent = @file_get_contents($qrServerUrl, false, $context);
            }
            
            // Final fallback: Use local PHP QR code generator
            if (!$qrContent) {
                require_once APP_PATH . '/libs/QRCode.php';
                // Calculate optimal pixel size for desired image dimensions
                $pixelSize = QRCode::calculatePixelSize($qrSize);
                $qrContent = QRCode::generate($qrData, $pixelSize);
            }
            
            // Save QR code if generated
            if ($qrContent) {
                file_put_contents($qrPath, $qrContent);
                
                // Update database with QR filename
                $this->db->update('event_registrations', [
                    'qr_code' => $qrFilename
                ], 'id = :id', ['id' => $registrationId]);
            } else {
                error_log("QR generation failed for registration: " . $registrationCode);
                return;
            }
            
            // Send QR code email with HTML template
            $subject = "Boleto de Acceso - " . $event['title'];
            
            // Build the HTML email with QR code embedded
            $body = $this->buildAccessTicketEmailTemplate($event, $registrationData, $registrationCode, $qrFilename, $configModel);
            
            // Send HTML email
            $headers = "From: " . ($configModel->get('smtp_from_name', 'CRM CCQ')) . " <noreply@camaradecomercioqro.mx>\r\n";
            $headers .= "Reply-To: " . ($configModel->get('contact_email', 'info@camaradecomercioqro.mx')) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            // Send email to primary and optionally to attendee
            $this->sendToRegistrantEmails($registrationData, $subject, $body, $headers);
            
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
    
    /**
     * Send email to registrant's primary email and attendee email (if different)
     * @param array $registrationData Registration data containing guest_email and attendee_email
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string $headers Email headers
     */
    private function sendToRegistrantEmails(array $registrationData, string $subject, string $body, string $headers): void {
        // Send to primary email (guest_email - company/main registrant)
        $primaryEmail = $registrationData['guest_email'];
        mail($primaryEmail, $subject, $body, $headers);
        
        // Also send to attendee email if different (when attendee is not owner/representative)
        $attendeeEmail = $registrationData['attendee_email'] ?? '';
        if (!empty($attendeeEmail) && strtolower($attendeeEmail) !== strtolower($primaryEmail)) {
            mail($attendeeEmail, $subject, $body, $headers);
        }
    }
    
    /**
     * Build HTML template for access ticket email with QR code
     * This is the email sent after payment confirmation for paid events
     */
    private function buildAccessTicketEmailTemplate(array $event, array $registrationData, string $registrationCode, string $qrFilename, Config $configModel): string {
        $eventDate = date('d/m/Y', strtotime($event['start_date']));
        $eventTime = date('H:i', strtotime($event['start_date']));
        if (!empty($event['end_date'])) {
            $eventTime .= ' - ' . date('H:i', strtotime($event['end_date']));
        }
        $location = $event['is_online'] ? 'Evento en línea' : htmlspecialchars($event['location'] ?? '');
        $address = htmlspecialchars($event['address'] ?? $location);
        // guest_name field contains either person name or company name depending on how the user registered
        $guestName = htmlspecialchars($registrationData['guest_name']);
        $eventTitle = htmlspecialchars($event['title']);
        $tickets = (int) $registrationData['tickets'];
        $qrUrl = BASE_URL . '/uploads/qr/' . $qrFilename;
        $ticketUrl = BASE_URL . '/evento/boleto/' . $registrationCode;
        $contactEmail = htmlspecialchars($configModel->get('contact_email', 'contacto@camaradecomercioqro.mx'));
        $contactPhone = htmlspecialchars($configModel->get('contact_phone', '4425375301'));
        
        // Get system colors from config
        $primaryColor = $configModel->get('primary_color', '#1e40af');
        $secondaryColor = $configModel->get('secondary_color', '#3b82f6');
        $accentColor = $configModel->get('accent_color', '#10b981');
        
        // Get logo URL
        $siteLogo = $configModel->get('site_logo', '');
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
        <!-- Payment Confirmation Banner -->
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: center;">
            <p style="color: #155724; font-size: 18px; margin: 0; font-weight: bold;">✓ ¡Pago Confirmado!</p>
            <p style="color: #155724; font-size: 14px; margin: 5px 0 0 0;">Tu boleto de acceso está listo</p>
        </div>
        
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
}
