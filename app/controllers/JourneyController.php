<?php
/**
 * Journey Controller
 * Customer Journey Visualization with 6 Defined Stages
 * 
 * Stage 1: Expediente Digital Afiliado (EDA) - Basic registration (RFC, owner/rep legal, razón social, nombre comercial, dirección, WhatsApp)
 * Stage 2: Products/Services - What the merchant sells and buys
 * Stage 3: Payment - Invoice attachment, expiration date, benefits enablement, seller assignment, free event attendance tracking
 * Stage 4: Cross-selling - Salon rental, marketing services, paid events
 * Stage 5: Up-selling - Membership upgrade invitations (2x per year minimum): Pyme → Visionario → Premier → Patrocinador
 * Stage 6: Council - Eligibility after 2+ years continuous affiliation
 */
class JourneyController extends Controller {
    
    private Contact $contactModel;
    private Affiliation $affiliationModel;
    private Service $serviceModel;
    private ServiceContract $contractModel;
    
    /**
     * Journey stage definitions
     */
    private const JOURNEY_STAGES = [
        1 => [
            'name' => 'EDA',
            'icon' => '📋',
            'description' => 'Expediente Digital Afiliado: RFC, propietario, razón social, nombre comercial, dirección, WhatsApp',
            'color' => 'blue'
        ],
        2 => [
            'name' => 'Productos/Servicios',
            'icon' => '📦',
            'description' => 'Registro de productos/servicios que ofrece el comerciante o empresario',
            'color' => 'cyan'
        ],
        3 => [
            'name' => 'Facturación',
            'icon' => '💳',
            'description' => 'Registro de pago, factura, vencimiento, habilitación de beneficios, asignación de vendedor',
            'color' => 'green'
        ],
        4 => [
            'name' => 'Cross-Selling',
            'icon' => '🎯',
            'description' => 'Servicios adicionales: renta de salones, marketing, eventos pagados',
            'color' => 'purple'
        ],
        5 => [
            'name' => 'Up-Selling',
            'icon' => '📈',
            'description' => 'Upgrade de membresía: Pyme → Visionario → Premier → Patrocinador (2 invitaciones/año)',
            'color' => 'yellow'
        ],
        6 => [
            'name' => 'Consejo',
            'icon' => '🏛️',
            'description' => 'Elegibilidad para consejo: mínimo 2 años de afiliación ininterrumpida',
            'color' => 'gold'
        ]
    ];
    
    public function __construct(array $params = []) {
        parent::__construct($params);
        $this->contactModel = new Contact();
        $this->affiliationModel = new Affiliation();
        $this->serviceModel = new Service();
        $this->contractModel = new ServiceContract();
    }
    
    public function index(): void {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        // Get journey stages statistics (6 stages)
        $journeyStageStats = $this->getJourneyStageStats();
        $typeStats = $this->contactModel->getStatsByType();
        $channelStats = $this->contactModel->getStatsByChannel();
        
        // Get services categories
        $serviceCategories = $this->serviceModel->getCategories();
        $servicesByCategory = $this->contractModel->getStatsByCategory();
        
        // Get upselling opportunities (affiliates who can upgrade)
        $upsellingOpportunities = $this->getUpsellingOpportunities();
        
        // Get cross-selling opportunities
        $crosssellingOpportunities = $this->getCrosssellingOpportunities();
        
        // Get council eligible affiliates (2+ years)
        $councilEligible = $this->getCouncilEligibleAffiliates();
        
        // Get pending upselling invitations count
        $pendingUpsellingInvitations = $this->getPendingUpsellingInvitations();
        
        // Get free event attendance stats
        $freeEventStats = $this->getFreeEventAttendanceStats();
        
        $this->view('journey/index', [
            'pageTitle' => 'Customer Journey',
            'currentPage' => 'journey',
            'journeyStages' => self::JOURNEY_STAGES,
            'journeyStageStats' => $journeyStageStats,
            'typeStats' => $typeStats,
            'channelStats' => $channelStats,
            'serviceCategories' => $serviceCategories,
            'servicesByCategory' => $servicesByCategory,
            'upsellingOpportunities' => $upsellingOpportunities,
            'crosssellingOpportunities' => $crosssellingOpportunities,
            'councilEligible' => $councilEligible,
            'pendingUpsellingInvitations' => $pendingUpsellingInvitations,
            'freeEventStats' => $freeEventStats
        ]);
    }
    
    public function show(): void {
        $this->requireAuth();
        
        $id = (int) ($this->params['id'] ?? 0);
        $contact = $this->contactModel->find($id);
        
        if (!$contact) {
            $_SESSION['flash_error'] = 'Contacto no encontrado.';
            $this->redirect('journey');
        }
        
        // Get complete journey for this contact
        $affiliations = $this->affiliationModel->getByContact($id);
        $contracts = $this->contractModel->getByContact($id);
        
        $activityModel = new Activity();
        $activities = $activityModel->getByContact($id);
        
        // Calculate journey stage (6 stages)
        $stage = $this->calculateJourneyStage($contact, $affiliations, $contracts);
        
        // Get upselling invitations for this contact
        $upsellingInvitations = $this->getContactUpsellingInvitations($id);
        
        // Get free event attendance
        $freeEventAttendance = $this->getContactFreeEventAttendance($id, $contact);
        
        // Check council eligibility
        $councilEligibility = $this->checkCouncilEligibility($id, $affiliations);
        
        // Get recommendations
        $recommendations = $this->getRecommendations($contact, $affiliations, $contracts, $upsellingInvitations, $councilEligibility);
        
        $this->view('journey/show', [
            'pageTitle' => 'Customer Journey - ' . ($contact['business_name'] ?? 'Contacto'),
            'currentPage' => 'journey',
            'journeyStages' => self::JOURNEY_STAGES,
            'contact' => $contact,
            'affiliations' => $affiliations,
            'contracts' => $contracts,
            'activities' => $activities,
            'stage' => $stage,
            'recommendations' => $recommendations,
            'upsellingInvitations' => $upsellingInvitations,
            'freeEventAttendance' => $freeEventAttendance,
            'councilEligibility' => $councilEligibility
        ]);
    }
    
    public function upselling(): void {
        $this->requireAuth();
        
        $opportunities = $this->getUpsellingOpportunities();
        $membershipTypes = (new MembershipType())->getActive();
        $invitationsThisYear = $this->getUpsellingInvitationsThisYear();
        
        $this->view('journey/upselling', [
            'pageTitle' => 'Oportunidades de Upselling',
            'currentPage' => 'journey',
            'journeyStages' => self::JOURNEY_STAGES,
            'opportunities' => $opportunities,
            'membershipTypes' => $membershipTypes,
            'invitationsThisYear' => $invitationsThisYear
        ]);
    }
    
    public function crossselling(): void {
        $this->requireAuth();
        
        $opportunities = $this->getCrosssellingOpportunities();
        $services = $this->serviceModel->getActive();
        $serviceCategories = $this->serviceModel->getCategories();
        
        $this->view('journey/crossselling', [
            'pageTitle' => 'Oportunidades de Cross-selling',
            'currentPage' => 'journey',
            'journeyStages' => self::JOURNEY_STAGES,
            'opportunities' => $opportunities,
            'services' => $services,
            'serviceCategories' => $serviceCategories
        ]);
    }
    
    /**
     * Council eligibility view - Stage 6
     */
    public function council(): void {
        $this->requireAuth();
        
        $eligibleAffiliates = $this->getCouncilEligibleAffiliates();
        $currentCouncilMembers = $this->getCurrentCouncilMembers();
        
        $this->view('journey/council', [
            'pageTitle' => 'Elegibilidad para Consejo',
            'currentPage' => 'journey',
            'journeyStages' => self::JOURNEY_STAGES,
            'eligibleAffiliates' => $eligibleAffiliates,
            'currentCouncilMembers' => $currentCouncilMembers
        ]);
    }
    
    /**
     * Send upselling invitation with WhatsApp and email support
     * Documents the message sent, date and time
     */
    public function sendUpsellingInvitation(): void {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('journey/upselling');
        }
        
        if (!$this->validateCsrf()) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido.';
            $this->redirect('journey/upselling');
        }
        
        $contactId = (int) $this->getInput('contact_id');
        $currentMembershipId = (int) $this->getInput('current_membership_id');
        $targetMembershipId = (int) $this->getInput('target_membership_id');
        $invitationType = $this->sanitize($this->getInput('invitation_type', 'payment_link'));
        $paymentLinkUrl = $this->sanitize($this->getInput('payment_link_url', ''));
        $notes = $this->sanitize($this->getInput('notes', ''));
        
        // Get WhatsApp message and email content
        $whatsappMessage = $this->sanitize($this->getInput('whatsapp_message', ''));
        $emailSubject = $this->sanitize($this->getInput('email_subject', ''));
        $emailMessage = $this->sanitize($this->getInput('email_message', ''));
        $contactWhatsapp = $this->sanitize($this->getInput('contact_whatsapp', ''));
        $contactEmail = $this->sanitize($this->getInput('contact_email', ''));
        
        // Validate invitation type specific requirements
        $validInvitationTypes = ['payment_link', 'email', 'whatsapp', 'phone', 'in_person'];
        if (!in_array($invitationType, $validInvitationTypes)) {
            $invitationType = 'payment_link';
        }
        
        try {
            $sql = "INSERT INTO upselling_invitations 
                    (contact_id, current_membership_id, target_membership_id, invitation_date, 
                     invitation_type, payment_link_url, whatsapp_message, email_subject, email_message,
                     contact_whatsapp, contact_email, sent_by_user_id, notes)
                    VALUES (:contact_id, :current_membership_id, :target_membership_id, NOW(),
                            :invitation_type, :payment_link_url, :whatsapp_message, :email_subject, :email_message,
                            :contact_whatsapp, :contact_email, :sent_by_user_id, :notes)";
            
            $this->db->execute($sql, [
                'contact_id' => $contactId,
                'current_membership_id' => $currentMembershipId,
                'target_membership_id' => $targetMembershipId,
                'invitation_type' => $invitationType,
                'payment_link_url' => $paymentLinkUrl,
                'whatsapp_message' => $whatsappMessage,
                'email_subject' => $emailSubject,
                'email_message' => $emailMessage,
                'contact_whatsapp' => $contactWhatsapp,
                'contact_email' => $contactEmail,
                'sent_by_user_id' => $_SESSION['user_id'],
                'notes' => $notes
            ]);
            
            // Update journey stage if applicable
            $this->updateContactJourneyStage($contactId, 5);
            
            // Log the action for audit
            $invitationId = $this->db->lastInsertId();
            $this->logUpsellingInvitation($invitationId, $invitationType, $contactId);
            
            $_SESSION['flash_success'] = 'Invitación de upselling enviada y documentada correctamente.';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al enviar la invitación: ' . $e->getMessage();
        }
        
        $this->redirect('journey/upselling');
    }
    
    /**
     * Send service/cross-selling invitation
     * Documents the services offered, date and time
     */
    public function sendServiceInvitation(): void {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('journey/crossselling');
        }
        
        if (!$this->validateCsrf()) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido.';
            $this->redirect('journey/crossselling');
        }
        
        $contactId = (int) $this->getInput('contact_id');
        $affiliationId = (int) $this->getInput('affiliation_id');
        $serviceIds = $this->getInput('service_ids', []);
        $invitationType = $this->sanitize($this->getInput('invitation_type', 'whatsapp'));
        $notes = $this->sanitize($this->getInput('notes', ''));
        
        // Get contact method details
        $whatsappMessage = $this->sanitize($this->getInput('whatsapp_message', ''));
        $emailSubject = $this->sanitize($this->getInput('email_subject', ''));
        $emailMessage = $this->sanitize($this->getInput('email_message', ''));
        $contactWhatsapp = $this->sanitize($this->getInput('contact_whatsapp', ''));
        $contactEmail = $this->sanitize($this->getInput('contact_email', ''));
        
        // Validate at least one service selected
        if (empty($serviceIds) || !is_array($serviceIds)) {
            $_SESSION['flash_error'] = 'Debe seleccionar al menos un servicio.';
            $this->redirect('journey/crossselling');
        }
        
        try {
            // Insert service invitation
            $sql = "INSERT INTO service_invitations 
                    (contact_id, affiliation_id, invitation_date, invitation_type, 
                     whatsapp_message, email_subject, email_message, contact_whatsapp, 
                     contact_email, sent_by_user_id, notes)
                    VALUES (:contact_id, :affiliation_id, NOW(), :invitation_type,
                            :whatsapp_message, :email_subject, :email_message, :contact_whatsapp,
                            :contact_email, :sent_by_user_id, :notes)";
            
            $this->db->execute($sql, [
                'contact_id' => $contactId,
                'affiliation_id' => $affiliationId,
                'invitation_type' => $invitationType,
                'whatsapp_message' => $whatsappMessage,
                'email_subject' => $emailSubject,
                'email_message' => $emailMessage,
                'contact_whatsapp' => $contactWhatsapp,
                'contact_email' => $contactEmail,
                'sent_by_user_id' => $_SESSION['user_id'],
                'notes' => $notes
            ]);
            
            $invitationId = $this->db->lastInsertId();
            
            // Insert service invitation details (many-to-many)
            foreach ($serviceIds as $serviceId) {
                $sql = "INSERT INTO service_invitation_details (invitation_id, service_id)
                        VALUES (:invitation_id, :service_id)";
                $this->db->execute($sql, [
                    'invitation_id' => $invitationId,
                    'service_id' => (int) $serviceId
                ]);
            }
            
            // Update journey stage if applicable
            $this->updateContactJourneyStage($contactId, 4);
            
            $_SESSION['flash_success'] = 'Invitación de servicios enviada y documentada correctamente.';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al enviar la invitación: ' . $e->getMessage();
        }
        
        $this->redirect('journey/crossselling');
    }
    
    /**
     * Log upselling invitation to audit log
     */
    private function logUpsellingInvitation(int $invitationId, string $invitationType, int $contactId): void {
        try {
            $sql = "INSERT INTO audit_log (user_id, action, table_name, record_id, new_values, ip_address, created_at)
                    VALUES (:user_id, :action, :table_name, :record_id, :new_values, :ip_address, NOW())";
            
            $this->db->execute($sql, [
                'user_id' => $_SESSION['user_id'],
                'action' => 'upselling_invitation_sent',
                'table_name' => 'upselling_invitations',
                'record_id' => $invitationId,
                'new_values' => json_encode([
                    'invitation_type' => $invitationType,
                    'contact_id' => $contactId,
                    'sent_at' => date('Y-m-d H:i:s'),
                    'sent_by' => $_SESSION['user_name'] ?? 'Unknown'
                ]),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {
            // Log error but don't block the main operation
            error_log('Failed to log upselling invitation audit: ' . $e->getMessage());
        }
    }
    
    private function getUpsellingOpportunities(): array {
        // Get affiliates who can upgrade - ordered by upsell_order hierarchy
        $sql = "SELECT c.*, a.id as affiliation_id, a.membership_type_id as current_membership_type_id,
                       m.name as current_membership, m.code as membership_code,
                       m.upsell_order, a.affiliation_date, a.expiration_date, u.name as affiliator_name,
                       (SELECT COUNT(*) FROM upselling_invitations ui 
                        WHERE ui.contact_id = c.id AND YEAR(ui.invitation_date) = YEAR(CURDATE())) as invitations_this_year
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id
                WHERE m.upsell_order < 4
                AND m.upsell_order > 0
                ORDER BY m.upsell_order, a.expiration_date";
        
        return $this->db->fetchAll($sql);
    }
    
    private function getCrosssellingOpportunities(): array {
        // Get affiliates who haven't contracted any services (Stage 4)
        $sql = "SELECT c.*, a.id as affiliation_id, m.name as current_membership,
                       u.name as affiliator_name,
                       COALESCE((SELECT SUM(sc2.amount) FROM service_contracts sc2 WHERE sc2.contact_id = c.id), 0) as total_services_amount
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id
                LEFT JOIN service_contracts sc ON c.id = sc.contact_id
                WHERE sc.id IS NULL
                AND c.contact_type = 'afiliado'
                ORDER BY c.business_name
                LIMIT 50";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get statistics for each journey stage
     */
    private function getJourneyStageStats(): array {
        $sql = "SELECT 
                    COALESCE(journey_stage, 1) as stage,
                    COUNT(*) as count
                FROM contacts
                WHERE contact_type IN ('prospecto', 'afiliado', 'exafiliado')
                GROUP BY COALESCE(journey_stage, 1)
                ORDER BY stage";
        
        $results = $this->db->fetchAll($sql);
        
        // Initialize all 6 stages with 0 count
        $stats = [];
        for ($i = 1; $i <= 6; $i++) {
            $stats[$i] = 0;
        }
        
        foreach ($results as $row) {
            $stage = (int) $row['stage'];
            if ($stage >= 1 && $stage <= 6) {
                $stats[$stage] = (int) $row['count'];
            }
        }
        
        return $stats;
    }
    
    /**
     * Get affiliates eligible for council (2+ years continuous affiliation)
     */
    private function getCouncilEligibleAffiliates(): array {
        $sql = "SELECT c.*, 
                       MIN(a.affiliation_date) as first_affiliation_date,
                       DATEDIFF(CURDATE(), MIN(a.affiliation_date)) / 365.0 as years_affiliated,
                       (SELECT cm.status FROM council_members cm WHERE cm.contact_id = c.id ORDER BY cm.created_at DESC LIMIT 1) as council_status
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id
                WHERE c.contact_type = 'afiliado'
                GROUP BY c.id
                HAVING years_affiliated >= 2
                ORDER BY years_affiliated DESC
                LIMIT 50";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get current council members
     */
    private function getCurrentCouncilMembers(): array {
        $sql = "SELECT cm.*, c.business_name, c.commercial_name, c.owner_name, c.legal_representative
                FROM council_members cm
                JOIN contacts c ON cm.contact_id = c.id
                WHERE cm.status = 'active'
                ORDER BY cm.member_type, cm.start_date";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get pending upselling invitations that need follow-up
     */
    private function getPendingUpsellingInvitations(): array {
        $sql = "SELECT ui.*, c.business_name, c.commercial_name, 
                       m1.name as current_membership, m2.name as target_membership
                FROM upselling_invitations ui
                JOIN contacts c ON ui.contact_id = c.id
                JOIN membership_types m1 ON ui.current_membership_id = m1.id
                JOIN membership_types m2 ON ui.target_membership_id = m2.id
                WHERE ui.response_status = 'pending'
                AND ui.invitation_date > DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY ui.invitation_date DESC
                LIMIT 20";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get upselling invitations for a specific contact
     */
    private function getContactUpsellingInvitations(int $contactId): array {
        $sql = "SELECT ui.*, m1.name as current_membership, m2.name as target_membership,
                       u.name as sent_by_name
                FROM upselling_invitations ui
                JOIN membership_types m1 ON ui.current_membership_id = m1.id
                JOIN membership_types m2 ON ui.target_membership_id = m2.id
                LEFT JOIN users u ON ui.sent_by_user_id = u.id
                WHERE ui.contact_id = :contact_id
                ORDER BY ui.invitation_date DESC";
        
        return $this->db->fetchAll($sql, ['contact_id' => $contactId]);
    }
    
    /**
     * Get upselling invitations per contact this year
     */
    private function getUpsellingInvitationsThisYear(): array {
        $sql = "SELECT contact_id, COUNT(*) as count
                FROM upselling_invitations
                WHERE YEAR(invitation_date) = YEAR(CURDATE())
                GROUP BY contact_id";
        
        $results = $this->db->fetchAll($sql);
        $invitations = [];
        foreach ($results as $row) {
            $invitations[$row['contact_id']] = (int) $row['count'];
        }
        return $invitations;
    }
    
    /**
     * Get free event attendance statistics
     */
    private function getFreeEventAttendanceStats(): array {
        $sql = "SELECT 
                    COALESCE(e.category, 'otros') as category,
                    COUNT(*) as total_registrations,
                    SUM(CASE WHEN er.attended = 1 THEN 1 ELSE 0 END) as total_attended
                FROM event_registrations er
                JOIN events e ON er.event_id = e.id
                WHERE e.is_paid = 0
                AND YEAR(e.start_date) = YEAR(CURDATE())
                GROUP BY COALESCE(e.category, 'otros')
                ORDER BY total_attended DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get free event attendance for a specific contact
     */
    private function getContactFreeEventAttendance(int $contactId, array $contact): array {
        $sql = "SELECT er.*, e.title, e.category, e.start_date, e.location
                FROM event_registrations er
                JOIN events e ON er.event_id = e.id
                WHERE e.is_paid = 0
                AND (er.contact_id = :contact_id 
                     OR er.guest_email = :email 
                     OR er.guest_rfc = :rfc)
                AND er.attended = 1
                ORDER BY e.start_date DESC
                LIMIT 20";
        
        return $this->db->fetchAll($sql, [
            'contact_id' => $contactId,
            'email' => $contact['corporate_email'] ?? '',
            'rfc' => $contact['rfc'] ?? ''
        ]);
    }
    
    /**
     * Check council eligibility for a contact
     */
    private function checkCouncilEligibility(int $contactId, array $affiliations): array {
        if (empty($affiliations)) {
            return [
                'eligible' => false,
                'years_affiliated' => 0,
                'years_required' => 2,
                'first_affiliation' => null,
                'council_status' => null
            ];
        }
        
        // Get first affiliation date
        $sql = "SELECT MIN(affiliation_date) as first_date FROM affiliations WHERE contact_id = :id";
        $result = $this->db->queryOne($sql, ['id' => $contactId]);
        $firstDate = $result['first_date'] ?? null;
        
        if (!$firstDate) {
            return [
                'eligible' => false,
                'years_affiliated' => 0,
                'years_required' => 2,
                'first_affiliation' => null,
                'council_status' => null
            ];
        }
        
        $yearsAffiliated = (time() - strtotime($firstDate)) / (365.25 * 24 * 60 * 60);
        
        // Check current council status
        $sql = "SELECT * FROM council_members WHERE contact_id = :id ORDER BY created_at DESC LIMIT 1";
        $councilStatus = $this->db->queryOne($sql, ['id' => $contactId]);
        
        return [
            'eligible' => $yearsAffiliated >= 2,
            'years_affiliated' => round($yearsAffiliated, 1),
            'years_required' => 2,
            'first_affiliation' => $firstDate,
            'council_status' => $councilStatus
        ];
    }
    
    /**
     * Get the next membership level based on current upsell_order
     */
    private function getNextMembershipLevel(int $currentUpsellOrder): ?array {
        if ($currentUpsellOrder >= 4) {
            return null; // Already at highest level
        }
        
        $sql = "SELECT id, name, code, price, upsell_order 
                FROM membership_types 
                WHERE upsell_order > :current_order 
                AND is_active = 1 
                ORDER BY upsell_order ASC 
                LIMIT 1";
        
        return $this->db->queryOne($sql, ['current_order' => $currentUpsellOrder]);
    }
    
    /**
     * Update contact's journey stage
     */
    private function updateContactJourneyStage(int $contactId, int $newStage): void {
        if ($newStage < 1 || $newStage > 6) {
            return;
        }
        
        // Only update if moving forward (don't go backwards)
        $sql = "UPDATE contacts 
                SET journey_stage = GREATEST(COALESCE(journey_stage, 1), :new_stage),
                    journey_stage_updated = NOW()
                WHERE id = :id";
        
        $this->db->execute($sql, ['id' => $contactId, 'new_stage' => $newStage]);
    }
    
    private function calculateJourneyStage(array $contact, array $affiliations, array $contracts): array {
        $stage = [
            'current' => 1,
            'current_name' => 'Registro Expediente',
            'progress' => 0,
            'stages' => []
        ];
        
        // Initialize all 6 stages
        foreach (self::JOURNEY_STAGES as $num => $stageInfo) {
            $stage['stages'][$num] = [
                'name' => $stageInfo['name'],
                'icon' => $stageInfo['icon'],
                'description' => $stageInfo['description'],
                'color' => $stageInfo['color'],
                'status' => 'pending',
                'date' => null
            ];
        }
        
        // Stage 1: Expediente Digital Único - Basic registration
        // Completed when the contact has basic identification data
        // Requires at least: (RFC or WhatsApp) AND (owner or legal_representative) AND business_name
        $hasIdentifier = !empty($contact['rfc']) || !empty($contact['whatsapp']);
        $hasOwner = !empty($contact['owner_name']) || !empty($contact['legal_representative']);
        $hasBusinessName = !empty($contact['business_name']);
        $stage1Complete = $hasIdentifier && $hasOwner && $hasBusinessName;
        
        if ($stage1Complete) {
            $stage['stages'][1]['status'] = 'completed';
            $stage['stages'][1]['date'] = $contact['created_at'];
            $stage['current'] = 1;
            $stage['progress'] = 17;
        }
        
        // Stage 2: Products/Services registration
        $hasProducts = !empty($contact['products_sells']) && $contact['products_sells'] !== '[]';
        if ($hasProducts) {
            $stage['stages'][2]['status'] = 'completed';
            $stage['stages'][2]['date'] = $contact['updated_at'];
            $stage['current'] = 2;
            $stage['progress'] = 34;
        }
        
        // Stage 3: Payment - Invoice, expiration, benefits, seller assignment
        if (!empty($affiliations)) {
            $currentAff = $affiliations[0];
            if ($currentAff['payment_status'] === 'paid') {
                $stage['stages'][3]['status'] = 'completed';
                $stage['stages'][3]['date'] = $currentAff['affiliation_date'];
                $stage['current'] = 3;
                $stage['progress'] = 50;
            }
        }
        
        // Stage 4: Cross-selling - Has contracted additional services
        if (!empty($contracts)) {
            $stage['stages'][4]['status'] = 'completed';
            $stage['stages'][4]['date'] = $contracts[0]['contract_date'] ?? $contracts[0]['created_at'];
            $stage['current'] = 4;
            $stage['progress'] = 67;
        }
        
        // Stage 5: Up-selling - Has upgraded membership
        if (count($affiliations) > 1) {
            // Check if there was a membership upgrade
            $hasUpgrade = false;
            for ($i = 0; $i < count($affiliations) - 1; $i++) {
                $currentOrder = $affiliations[$i]['upsell_order'] ?? 0;
                $previousOrder = $affiliations[$i + 1]['upsell_order'] ?? 0;
                if ($currentOrder > $previousOrder) {
                    $hasUpgrade = true;
                    $stage['stages'][5]['status'] = 'completed';
                    $stage['stages'][5]['date'] = $affiliations[$i]['affiliation_date'];
                    break;
                }
            }
            if ($hasUpgrade) {
                $stage['current'] = 5;
                $stage['progress'] = 84;
            }
        }
        
        // Stage 6: Council eligibility - 2+ years continuous affiliation
        if (!empty($affiliations)) {
            $firstAffDate = end($affiliations)['affiliation_date'];
            $yearsAffiliated = (time() - strtotime($firstAffDate)) / (365.25 * 24 * 60 * 60);
            
            if ($yearsAffiliated >= 2) {
                // Check if they are in council
                $councilSql = "SELECT status FROM council_members WHERE contact_id = :id AND status = 'active' LIMIT 1";
                $councilResult = $this->db->queryOne($councilSql, ['id' => $contact['id']]);
                
                if ($councilResult) {
                    $stage['stages'][6]['status'] = 'completed';
                    $stage['current'] = 6;
                    $stage['progress'] = 100;
                } else {
                    $stage['stages'][6]['status'] = 'eligible';
                }
            }
        }
        
        $stage['current_name'] = self::JOURNEY_STAGES[$stage['current']]['name'];
        
        return $stage;
    }
    
    private function getRecommendations(array $contact, array $affiliations, array $contracts, array $upsellingInvitations = [], array $councilEligibility = []): array {
        $recommendations = [];
        
        // If prospect, recommend affiliation (Stage 1 incomplete)
        if ($contact['contact_type'] === 'prospecto') {
            $recommendations[] = [
                'type' => 'stage1',
                'stage' => 1,
                'title' => 'Completar Expediente Digital',
                'description' => 'Este prospecto necesita completar su expediente digital. Registrar RFC, propietario, y datos de contacto.',
                'action' => 'afiliados/nuevo?prospect_id=' . $contact['id'],
                'priority' => 'high'
            ];
        }
        
        // Stage 2: Products/Services
        $hasProducts = !empty($contact['products_sells']) && $contact['products_sells'] !== '[]';
        if (!$hasProducts && $contact['contact_type'] === 'afiliado') {
            $recommendations[] = [
                'type' => 'stage2',
                'stage' => 2,
                'title' => 'Registrar Productos/Servicios',
                'description' => 'Completar el registro de los productos o servicios que ofrece para el buscador y chatbot.',
                'action' => 'afiliados/' . $contact['id'] . '/editar',
                'priority' => 'medium'
            ];
        }
        
        // Stage 3: Check payment status
        if (!empty($affiliations) && $affiliations[0]['payment_status'] !== 'paid') {
            $recommendations[] = [
                'type' => 'stage3',
                'stage' => 3,
                'title' => 'Registro de Pago Pendiente',
                'description' => 'Pendiente de registrar pago, adjuntar factura y habilitar beneficios.',
                'action' => 'afiliados/' . $contact['id'],
                'priority' => 'high'
            ];
        }
        
        // Stage 4: Cross-selling - If no services contracted
        if (empty($contracts) && !empty($affiliations) && $affiliations[0]['payment_status'] === 'paid') {
            $recommendations[] = [
                'type' => 'stage4',
                'stage' => 4,
                'title' => 'Ofrecer Servicios Adicionales',
                'description' => 'El afiliado no ha contratado servicios adicionales. Ofrecer renta de salones, marketing, cursos.',
                'action' => 'journey/crossselling',
                'priority' => 'medium'
            ];
        }
        
        // Stage 5: Up-selling - Check if needs invitation (2x per year)
        if (!empty($affiliations)) {
            $currentMembership = $affiliations[0]['membership_code'] ?? '';
            $currentUpsellOrder = $affiliations[0]['upsell_order'] ?? 0;
            
            // Get the next membership level from the database based on upsell_order
            $nextMembership = $this->getNextMembershipLevel($currentUpsellOrder);
            
            if ($nextMembership) {
                // Count invitations this year
                $invitationsThisYear = count(array_filter($upsellingInvitations, function($inv) {
                    return date('Y', strtotime($inv['invitation_date'])) === date('Y');
                }));
                
                if ($invitationsThisYear < 2) {
                    $recommendations[] = [
                        'type' => 'stage5',
                        'stage' => 5,
                        'title' => 'Enviar Invitación de Upgrade',
                        'description' => 'El afiliado tiene ' . $invitationsThisYear . '/2 invitaciones de upgrade este año. Ofrecer ' . $nextMembership['name'] . '.',
                        'action' => 'journey/upselling',
                        'priority' => $invitationsThisYear === 0 ? 'high' : 'medium'
                    ];
                }
            }
        }
        
        // Stage 6: Council eligibility
        if (!empty($councilEligibility) && $councilEligibility['eligible'] && !$councilEligibility['council_status']) {
            $recommendations[] = [
                'type' => 'stage6',
                'stage' => 6,
                'title' => 'Elegible para Consejo',
                'description' => 'El afiliado tiene ' . $councilEligibility['years_affiliated'] . ' años de afiliación y es elegible para el consejo de la cámara.',
                'action' => 'journey/council',
                'priority' => 'low'
            ];
        }
        
        // Renewal reminder
        if (!empty($affiliations)) {
            $daysToExpire = (strtotime($affiliations[0]['expiration_date']) - time()) / 86400;
            if ($daysToExpire <= 30 && $daysToExpire > 0) {
                $recommendations[] = [
                    'type' => 'renewal',
                    'stage' => 3,
                    'title' => 'Renovación Próxima',
                    'description' => 'Membresía vence en ' . ceil($daysToExpire) . ' días. Contactar para renovación.',
                    'action' => 'afiliados/' . $contact['id'],
                    'priority' => 'high'
                ];
            }
        }
        
        // Sort by priority
        usort($recommendations, function($a, $b) {
            $priorityOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
            return ($priorityOrder[$a['priority']] ?? 2) - ($priorityOrder[$b['priority']] ?? 2);
        });
        
        return $recommendations;
    }
}
