<?php
/**
 * Event Model
 */
class Event extends Model {
    protected string $table = 'events';
    protected array $fillable = [
        'title', 'description', 'event_type', 'category', 'start_date',
        'end_date', 'location', 'address', 'google_maps_url', 'is_online',
        'online_url', 'max_capacity', 'room_name', 'room_capacity', 'allowed_attendees',
        'is_paid', 'price', 'promo_price', 'promo_end_date', 'member_price', 
        'promo_member_price', 'free_for_affiliates', 'has_courtesy_tickets',
        'registration_url', 'image', 'status', 'target_audiences', 'created_by'
    ];
    
    public function getUpcoming(int $limit = 10): array {
        $sql = "SELECT e.*, 
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registered_count
                FROM {$this->table} e
                WHERE e.start_date >= NOW() AND e.status = 'published'
                ORDER BY e.start_date
                LIMIT {$limit}";
        return $this->raw($sql);
    }
    
    public function getPast(int $limit = 10): array {
        $sql = "SELECT e.*, 
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registered_count,
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND attended = 1) as attended_count
                FROM {$this->table} e
                WHERE e.end_date < NOW()
                ORDER BY e.start_date DESC
                LIMIT {$limit}";
        return $this->raw($sql);
    }
    
    public function getByType(string $type): array {
        $sql = "SELECT e.*, 
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registered_count
                FROM {$this->table} e
                WHERE e.event_type = :type
                ORDER BY e.start_date DESC";
        return $this->raw($sql, ['type' => $type]);
    }
    
    public function findByRegistrationUrl(string $url): ?array {
        return $this->findBy('registration_url', $url);
    }
    
    /**
     * Check if an email already has an active registration for this event
     * Returns the existing registration if found, null otherwise
     */
    public function hasExistingRegistration(int $eventId, string $email): ?array {
        $sql = "SELECT * FROM event_registrations 
                WHERE event_id = :event_id 
                AND guest_email = :email 
                AND parent_registration_id IS NULL
                ORDER BY registration_date DESC
                LIMIT 1";
        $result = $this->raw($sql, ['event_id' => $eventId, 'email' => $email]);
        return $result ? $result[0] : null;
    }
    
    public function getRegistrations(int $eventId): array {
        // Join with contacts table to get owner_name and legal_representative
        // First try to join by contact_id, if null, try to match by email
        // Only show parent registrations (exclude child registrations to avoid duplicates)
        $sql = "SELECT 
                    er.*,
                    COALESCE(c.business_name, '') as business_name,
                    COALESCE(c.commercial_name, '') as commercial_name,
                    COALESCE(c.contact_type, '') as contact_type,
                    COALESCE(c.owner_name, '') as owner_name,
                    COALESCE(c.legal_representative, '') as legal_representative
                FROM event_registrations er
                LEFT JOIN contacts c ON (
                    er.contact_id = c.id 
                    OR (er.contact_id IS NULL AND er.guest_email = c.corporate_email)
                )
                WHERE er.event_id = :event_id 
                AND (er.parent_registration_id IS NULL OR er.parent_registration_id = 0)
                ORDER BY er.registration_date DESC";
        return $this->raw($sql, ['event_id' => $eventId]);
    }
    
    public function registerAttendee(int $eventId, array $data): int {
        // Generate unique registration code
        $registrationCode = $this->generateRegistrationCode();
        
        return $this->db->insert('event_registrations', array_merge(
            $data,
            [
                'event_id' => $eventId,
                'registration_code' => $registrationCode
            ]
        ));
    }
    
    public function generateRegistrationCode(): string {
        $maxAttempts = 10; // Maximum number of attempts before using fallback
        $attempt = 0;
        
        do {
            // Generate a more unique code using timestamp and random bytes
            // Format: REG-YYYYMMDD-XXXXXX (where X is random hexadecimal)
            $timestamp = date('Ymd');
            $randomPart = strtoupper(bin2hex(random_bytes(3)));
            $code = "REG-{$timestamp}-{$randomPart}";
            
            $stmt = $this->db->query(
                "SELECT id FROM event_registrations WHERE registration_code = :code",
                ['code' => $code]
            );
            $exists = $stmt->fetch();
            
            $attempt++;
        } while (!empty($exists) && $attempt < $maxAttempts);
        
        // If collision persists after max attempts, use Unix timestamp for guaranteed uniqueness
        // This extremely rare scenario only occurs if random_bytes produces 10 consecutive collisions
        if (!empty($exists)) {
            $code = "REG-{$timestamp}-" . strtoupper(bin2hex(random_bytes(4))) . "-" . time();
        }
        
        return $code;
    }
    
    public function getRegistrationByCode(string $code): ?array {
        $sql = "SELECT er.*, e.title as event_title, e.start_date, e.location
                FROM event_registrations er
                JOIN events e ON er.event_id = e.id
                WHERE er.registration_code = :code";
        return $this->rawOne($sql, ['code' => $code]);
    }
    
    public function updateQRSent(int $registrationId): int {
        return $this->db->update('event_registrations', [
            'qr_sent' => 1,
            'qr_sent_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $registrationId]);
    }
    
    public function updateConfirmationSent(int $registrationId): int {
        return $this->db->update('event_registrations', [
            'confirmation_sent' => 1,
            'confirmation_sent_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $registrationId]);
    }
    
    public function isActiveAffiliate(string $email): bool {
        $sql = "SELECT COUNT(*) as count 
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id
                WHERE c.corporate_email = :email 
                AND c.contact_type = 'afiliado'
                AND a.status = 'active'
                AND a.expiration_date >= CURDATE()";
        $result = $this->rawOne($sql, ['email' => $email]);
        return ($result['count'] ?? 0) > 0;
    }
    
    /**
     * Check if a company (by email or RFC) already received a courtesy ticket for this event
     */
    public function hasCourtesyTicket(int $eventId, string $email, ?string $rfc = null): bool {
        // Check by email first
        $sql = "SELECT COUNT(*) as count 
                FROM event_registrations 
                WHERE event_id = :event_id 
                AND guest_email = :email
                AND is_owner_representative = 1
                AND is_guest = 0
                AND payment_status = 'free'";
        $result = $this->rawOne($sql, ['event_id' => $eventId, 'email' => $email]);
        if (($result['count'] ?? 0) > 0) {
            return true;
        }
        
        // Also check by RFC if provided
        if (!empty($rfc)) {
            $sql = "SELECT COUNT(*) as count 
                    FROM event_registrations 
                    WHERE event_id = :event_id 
                    AND guest_rfc = :rfc
                    AND is_owner_representative = 1
                    AND is_guest = 0
                    AND payment_status = 'free'";
            $result = $this->rawOne($sql, ['event_id' => $eventId, 'rfc' => $rfc]);
            if (($result['count'] ?? 0) > 0) {
                return true;
            }
        }
        
        return false;
    }
    
    public function markAttendance(int $registrationId, bool $attended = true): int {
        return $this->db->update('event_registrations', [
            'attended' => $attended ? 1 : 0,
            'attendance_time' => $attended ? date('Y-m-d H:i:s') : null
        ], 'id = :id', ['id' => $registrationId]);
    }
    
    public function getRegistrationCount(int $eventId): int {
        $sql = "SELECT COUNT(*) as count FROM event_registrations WHERE event_id = :id";
        $result = $this->rawOne($sql, ['id' => $eventId]);
        return (int) ($result['count'] ?? 0);
    }
    
    public function getAttendanceCount(int $eventId): int {
        $sql = "SELECT COUNT(*) as count FROM event_registrations WHERE event_id = :id AND attended = 1";
        $result = $this->rawOne($sql, ['id' => $eventId]);
        return (int) ($result['count'] ?? 0);
    }
    
    public function getEventStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total_events,
                    SUM(CASE WHEN is_paid = 1 THEN 1 ELSE 0 END) as paid_events,
                    SUM(CASE WHEN is_paid = 0 THEN 1 ELSE 0 END) as free_events,
                    (SELECT COUNT(*) FROM event_registrations) as total_registrations,
                    (SELECT COUNT(*) FROM event_registrations WHERE attended = 1) as total_attendance
                FROM {$this->table}
                WHERE YEAR(start_date) = YEAR(CURDATE())";
        return $this->rawOne($sql);
    }
    
    public function generateUniqueUrl(string $title): string {
        $base = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $base = trim($base, '-');
        $url = $base;
        $counter = 1;
        
        while ($this->findByRegistrationUrl($url)) {
            $url = $base . '-' . $counter;
            $counter++;
        }
        
        return $url;
    }
    
    public function getCategories(): array {
        $sql = "SELECT * FROM event_categories ORDER BY name";
        try {
            return $this->raw($sql);
        } catch (Exception $e) {
            // Table might not exist yet, return empty array
            return [];
        }
    }
    
    public function getActiveCategories(): array {
        $sql = "SELECT * FROM event_categories WHERE is_active = 1 ORDER BY name";
        try {
            return $this->raw($sql);
        } catch (Exception $e) {
            // Table might not exist yet, return empty array
            return [];
        }
    }
    
    public function getEventTypeCatalog(): array {
        $sql = "SELECT * FROM event_type_catalog WHERE is_active = 1 ORDER BY name";
        try {
            return $this->raw($sql);
        } catch (Exception $e) {
            // Table might not exist yet, return empty array
            return [];
        }
    }
    
    public function updatePaymentStatus(int $registrationId, string $status, ?string $paymentReference = null): int {
        $data = ['payment_status' => $status];
        if ($paymentReference) {
            // Sanitize payment reference - only allow alphanumeric, dashes and underscores
            $safeReference = preg_replace('/[^a-zA-Z0-9\-\_]/', '', $paymentReference);
            $data['notes'] = 'PayPal Order ID: ' . $safeReference;
        }
        return $this->db->update('event_registrations', $data, 'id = :id', ['id' => $registrationId]);
    }
    
    /**
     * Get comprehensive event metrics for reporting
     * Returns total tickets, attendance, no-show percentage, and breakdown by payment type
     * 
     * @param int|null $eventId Specific event ID, or null for all events
     * @param string|null $eventType Filter by event type (interno, publico, terceros)
     * @param string|null $category Filter by category
     * @return array Metrics array with totals and breakdowns
     */
    public function getEventMetrics(?int $eventId = null, ?string $eventType = null, ?string $category = null): array {
        $where = ['1=1'];
        $params = [];
        
        if ($eventId) {
            $where[] = 'e.id = :event_id';
            $params['event_id'] = $eventId;
        }
        
        if ($eventType) {
            $where[] = 'e.event_type = :event_type';
            $params['event_type'] = $eventType;
        }
        
        if ($category) {
            $where[] = 'e.category = :category';
            $params['category'] = $category;
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT 
                    COUNT(DISTINCT e.id) as total_events,
                    SUM(CASE WHEN e.is_paid = 1 THEN 1 ELSE 0 END) as paid_events,
                    SUM(CASE WHEN e.is_paid = 0 THEN 1 ELSE 0 END) as free_events,
                    
                    -- Total tickets/registrations
                    COUNT(er.id) as total_tickets,
                    
                    -- Attendance metrics
                    SUM(CASE WHEN er.attended = 1 THEN 1 ELSE 0 END) as total_attendance,
                    SUM(CASE WHEN er.attended = 0 AND e.end_date < NOW() THEN 1 ELSE 0 END) as total_no_show,
                    
                    -- For paid events: courtesy vs paid breakdown
                    SUM(CASE WHEN e.is_paid = 1 AND er.payment_status = 'free' AND er.is_owner_representative = 1 THEN 1 ELSE 0 END) as courtesy_tickets,
                    SUM(CASE WHEN e.is_paid = 1 AND er.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_tickets,
                    
                    -- Attendance breakdown for paid events
                    SUM(CASE WHEN e.is_paid = 1 AND er.payment_status = 'free' AND er.is_owner_representative = 1 AND er.attended = 1 THEN 1 ELSE 0 END) as courtesy_attended,
                    SUM(CASE WHEN e.is_paid = 1 AND er.payment_status = 'paid' AND er.attended = 1 THEN 1 ELSE 0 END) as paid_attended,
                    
                    -- No-show breakdown for paid events
                    SUM(CASE WHEN e.is_paid = 1 AND er.payment_status = 'free' AND er.is_owner_representative = 1 AND er.attended = 0 AND e.end_date < NOW() THEN 1 ELSE 0 END) as courtesy_no_show,
                    SUM(CASE WHEN e.is_paid = 1 AND er.payment_status = 'paid' AND er.attended = 0 AND e.end_date < NOW() THEN 1 ELSE 0 END) as paid_no_show
                    
                FROM {$this->table} e
                LEFT JOIN event_registrations er ON e.id = er.event_id
                WHERE {$whereClause}";
        
        $result = $this->rawOne($sql, $params);
        
        // Calculate percentages
        $totalTickets = (int) ($result['total_tickets'] ?? 0);
        $totalAttendance = (int) ($result['total_attendance'] ?? 0);
        $totalNoShow = (int) ($result['total_no_show'] ?? 0);
        
        $result['attendance_rate'] = $totalTickets > 0 ? round(($totalAttendance / $totalTickets) * 100, 2) : 0;
        $result['no_show_rate'] = $totalTickets > 0 ? round(($totalNoShow / $totalTickets) * 100, 2) : 0;
        
        return $result;
    }
    
    /**
     * Get top 50 attending businesses (razón social)
     * 
     * @param int|null $eventId Specific event ID, or null for all events
     * @param int $limit Number of results to return (default 50)
     * @return array Array of businesses with attendance count
     */
    public function getTopAttendingBusinesses(?int $eventId = null, int $limit = 50): array {
        $where = 'er.attended = 1';
        $params = [];
        
        if ($eventId) {
            $where .= ' AND e.id = :event_id';
            $params['event_id'] = $eventId;
        }
        
        $sql = "SELECT 
                    COALESCE(c.business_name, er.guest_name, 'Sin razón social') as business_name,
                    c.rfc,
                    COUNT(er.id) as attendance_count,
                    COUNT(DISTINCT e.id) as events_attended,
                    SUM(CASE WHEN e.is_paid = 1 THEN 1 ELSE 0 END) as paid_events_attended,
                    SUM(CASE WHEN e.is_paid = 0 THEN 1 ELSE 0 END) as free_events_attended
                FROM event_registrations er
                INNER JOIN {$this->table} e ON er.event_id = e.id
                LEFT JOIN contacts c ON (er.contact_id = c.id OR er.guest_email = c.corporate_email OR er.guest_rfc = c.rfc)
                WHERE {$where}
                GROUP BY COALESCE(c.business_name, er.guest_name, 'Sin razón social'), c.rfc
                ORDER BY attendance_count DESC, events_attended DESC
                LIMIT {$limit}";
        
        return $this->raw($sql, $params);
    }
    
    /**
     * Get event metrics by category
     * 
     * @return array Array of metrics grouped by event category
     */
    public function getMetricsByCategory(): array {
        $sql = "SELECT 
                    e.category,
                    COUNT(DISTINCT e.id) as total_events,
                    COUNT(er.id) as total_tickets,
                    SUM(CASE WHEN er.attended = 1 THEN 1 ELSE 0 END) as total_attendance,
                    SUM(CASE WHEN er.attended = 0 AND e.end_date < NOW() THEN 1 ELSE 0 END) as total_no_show,
                    ROUND(SUM(CASE WHEN er.attended = 1 THEN 1 ELSE 0 END) / COUNT(er.id) * 100, 2) as attendance_rate
                FROM {$this->table} e
                LEFT JOIN event_registrations er ON e.id = er.event_id
                WHERE e.category IS NOT NULL AND e.category != ''
                GROUP BY e.category
                ORDER BY total_events DESC";
        
        return $this->raw($sql);
    }
    
    /**
     * Get event metrics by type
     * 
     * @return array Array of metrics grouped by event type
     */
    public function getMetricsByType(): array {
        $sql = "SELECT 
                    e.event_type,
                    COUNT(DISTINCT e.id) as total_events,
                    COUNT(er.id) as total_tickets,
                    SUM(CASE WHEN er.attended = 1 THEN 1 ELSE 0 END) as total_attendance,
                    SUM(CASE WHEN er.attended = 0 AND e.end_date < NOW() THEN 1 ELSE 0 END) as total_no_show,
                    ROUND(SUM(CASE WHEN er.attended = 1 THEN 1 ELSE 0 END) / COUNT(er.id) * 100, 2) as attendance_rate
                FROM {$this->table} e
                LEFT JOIN event_registrations er ON e.id = er.event_id
                GROUP BY e.event_type
                ORDER BY total_events DESC";
        
        return $this->raw($sql);
    }
}
