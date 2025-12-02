<?php
/**
 * Contact Model
 * Manages the Expediente Digital Afiliado (EDA)
 */
class Contact extends Model {
    protected string $table = 'contacts';
    protected array $fillable = [
        'rfc', 'person_type', 'whatsapp', 'contact_type', 'business_name', 'commercial_name',
        'owner_name', 'legal_representative', 'corporate_email', 'phone',
        'position', 'industry', 'niza_classification', 'niza_custom_category', 'products_sells', 'products_buys',
        'discount_percentage', 'commercial_address', 'fiscal_address', 'city',
        'state', 'postal_code', 'google_maps_url', 'website', 'facebook',
        'instagram', 'linkedin', 'twitter', 'whatsapp_sales', 'whatsapp_purchases',
        'whatsapp_admin', 'profile_completion', 'completion_stage',
        'assigned_affiliate_id', 'source_channel', 'notes', 'is_validated', 'validated_by'
    ];
    
    // Threshold for complete profile (65% completion is considered full)
    private const FULL_PROFILE_THRESHOLD = 65;
    
    public function findByRfc(string $rfc): ?array {
        return $this->findBy('rfc', $rfc);
    }
    
    public function findByWhatsapp(string $whatsapp): ?array {
        return $this->findBy('whatsapp', $whatsapp);
    }
    
    public function identify(string $identifier): ?array {
        // Try RFC first, then WhatsApp, then email, then business name (razón social)
        $contact = $this->findByRfc($identifier);
        if (!$contact) {
            $contact = $this->findByWhatsapp($identifier);
        }
        if (!$contact) {
            $contact = $this->findBy('corporate_email', $identifier);
        }
        if (!$contact) {
            // Search by business name (razón social) - case insensitive
            $sql = "SELECT * FROM {$this->table} 
                    WHERE LOWER(business_name) = LOWER(:identifier) 
                    OR LOWER(commercial_name) = LOWER(:identifier)
                    LIMIT 1";
            $contact = $this->rawOne($sql, ['identifier' => $identifier]);
        }
        return $contact;
    }
    
    public function getProspects(int $affiliatorId = null): array {
        $sql = "SELECT c.*, u.name as affiliator_name 
                FROM {$this->table} c 
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id 
                WHERE c.contact_type = 'prospecto'";
        $params = [];
        
        if ($affiliatorId) {
            $sql .= " AND c.assigned_affiliate_id = :affiliator_id";
            $params['affiliator_id'] = $affiliatorId;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        return $this->raw($sql, $params);
    }
    
    public function getAffiliates(): array {
        $sql = "SELECT c.*, u.name as affiliator_name,
                       a.affiliation_date, a.expiration_date, a.status as affiliation_status,
                       m.name as membership_name, m.code as membership_code
                FROM {$this->table} c 
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id 
                LEFT JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                LEFT JOIN membership_types m ON a.membership_type_id = m.id
                WHERE c.contact_type = 'afiliado'
                ORDER BY c.business_name";
        return $this->raw($sql);
    }
    
    public function getFormerAffiliates(): array {
        $sql = "SELECT c.*, u.name as affiliator_name
                FROM {$this->table} c 
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id 
                WHERE c.contact_type = 'exafiliado'
                ORDER BY c.business_name";
        return $this->raw($sql);
    }
    
    public function getByType(string $type): array {
        return $this->where('contact_type', $type);
    }
    
    public function getByChannel(string $channel): array {
        return $this->where('source_channel', $channel);
    }
    
    public function getExpiringAffiliations(int $days = 30): array {
        $sql = "SELECT c.*, a.expiration_date, a.id as affiliation_id,
                       m.name as membership_name, u.name as affiliator_name
                FROM {$this->table} c 
                JOIN affiliations a ON c.id = a.contact_id
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON c.assigned_affiliate_id = u.id
                WHERE a.status = 'active' 
                AND a.expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                ORDER BY a.expiration_date";
        return $this->raw($sql, ['days' => $days]);
    }
    
    public function search(string $term, string $userType = 'publico'): array {
        $term = trim($term);
        
        // If term is empty or too short, return empty
        if (strlen($term) < 2) {
            return [];
        }
        
        // Phrase matching: split search term into words and search for each
        // This provides better results than exact matching
        $words = preg_split('/\s+/', $term);
        // Filter words but keep at least 1 character words if they're meaningful (like initials)
        $words = array_filter($words, function($w) { return strlen($w) >= 1; });
        
        // Build search conditions for each word (phrase matching)
        $params = [];
        $conditions = [];
        
        // Only use word matching if we have words of reasonable length
        $meaningfulWords = array_filter($words, function($w) { return strlen($w) >= 2; });
        
        if (!empty($meaningfulWords)) {
            foreach ($meaningfulWords as $i => $word) {
                $wordPattern = '%' . $word . '%';
                $paramKey = 'word' . $i;
                $params[$paramKey] = $wordPattern;
                
                $conditions[] = "(c.business_name LIKE :{$paramKey} 
                         OR c.commercial_name LIKE :{$paramKey}
                         OR c.industry LIKE :{$paramKey}
                         OR JSON_SEARCH(c.products_sells, 'one', :{$paramKey}) IS NOT NULL)";
            }
        }
        
        // Also allow exact RFC, phone, WhatsApp, email search
        $exactTerm = '%' . $term . '%';
        $params['exact_term'] = $exactTerm;
        
        // Base query - affiliates only
        // Searches by: Name (business/commercial), RFC, Phone, WhatsApp, Email
        $sql = "SELECT c.id, c.business_name, c.commercial_name, c.industry,
                       c.products_sells, c.city, c.phone, c.website, c.contact_type,
                       c.rfc, c.whatsapp, c.corporate_email
                FROM {$this->table} c 
                WHERE c.contact_type = 'afiliado'
                AND (";
        
        // Build the search condition
        if (!empty($conditions)) {
            $sql .= "(" . implode(" AND ", $conditions) . ") OR ";
        }
        
        $sql .= "c.rfc LIKE :exact_term
                    OR c.phone LIKE :exact_term
                    OR c.whatsapp LIKE :exact_term
                    OR c.corporate_email LIKE :exact_term
                    OR c.business_name LIKE :exact_term
                    OR c.commercial_name LIKE :exact_term
                )";
        
        $sql .= " ORDER BY c.business_name LIMIT 50";
        
        return $this->raw($sql, $params);
    }
    
    public function calculateProfileCompletion(array $contact): int {
        $completion = 0;
        
        // Stage A - 25%
        if (!empty($contact['rfc']) || !empty($contact['whatsapp'])) $completion += 5;
        if (!empty($contact['business_name'])) $completion += 5;
        if (!empty($contact['owner_name'])) $completion += 5;
        if (!empty($contact['corporate_email'])) $completion += 5;
        if (!empty($contact['phone'])) $completion += 5;
        
        // Stage B - 35% (10 more)
        if (!empty($contact['industry'])) $completion += 2;
        if (!empty($contact['products_sells'])) $completion += 3;
        if (!empty($contact['products_buys'])) $completion += 2;
        if (!empty($contact['commercial_address'])) $completion += 2;
        if (!empty($contact['fiscal_address'])) $completion += 1;
        
        // Stage C - 70% (35 more)
        if (!empty($contact['whatsapp_sales'])) $completion += 8;
        if (!empty($contact['whatsapp_purchases'])) $completion += 7;
        if (!empty($contact['whatsapp_admin'])) $completion += 5;
        if (!empty($contact['website'])) $completion += 5;
        if (!empty($contact['facebook']) || !empty($contact['instagram']) || !empty($contact['linkedin'])) $completion += 5;
        if (!empty($contact['google_maps_url'])) $completion += 5;
        
        // Bonus for full profile
        if ($completion >= self::FULL_PROFILE_THRESHOLD) {
            $completion = 100;
        }
        
        return min(100, $completion);
    }
    
    public function updateCompletion(int $id): void {
        $contact = $this->find($id);
        if ($contact) {
            $completion = $this->calculateProfileCompletion($contact);
            $stage = 'A';
            if ($completion >= 35) $stage = 'B';
            if ($completion >= 70) $stage = 'C';
            
            $this->update($id, [
                'profile_completion' => $completion,
                'completion_stage' => $stage
            ]);
        }
    }
    
    public function getStatsByType(): array {
        $sql = "SELECT contact_type, COUNT(*) as count 
                FROM {$this->table} 
                GROUP BY contact_type";
        return $this->raw($sql);
    }
    
    public function getStatsByChannel(): array {
        $sql = "SELECT source_channel, COUNT(*) as count 
                FROM {$this->table} 
                WHERE contact_type IN ('prospecto', 'afiliado')
                GROUP BY source_channel";
        return $this->raw($sql);
    }
    
    /**
     * Get newly assigned prospects for an affiliator (from last 7 days)
     * Includes source channel (chatbot, events, manual)
     * @param int $affiliatorId
     * @return array
     */
    public function getNewAssignedProspects(int $affiliatorId): array {
        $sql = "SELECT c.*, 
                       DATEDIFF(CURDATE(), DATE(c.created_at)) as days_ago
                FROM {$this->table} c 
                WHERE c.contact_type = 'prospecto'
                AND c.assigned_affiliate_id = :affiliator_id
                AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                ORDER BY c.created_at DESC
                LIMIT 20";
        return $this->raw($sql, ['affiliator_id' => $affiliatorId]);
    }
    
    /**
     * Get prospects by source channel for an affiliator
     * @param int $affiliatorId
     * @param string $channel
     * @return array
     */
    public function getProspectsByChannel(int $affiliatorId, string $channel): array {
        $sql = "SELECT c.* 
                FROM {$this->table} c 
                WHERE c.contact_type = 'prospecto'
                AND c.assigned_affiliate_id = :affiliator_id
                AND c.source_channel = :channel
                ORDER BY c.created_at DESC";
        return $this->raw($sql, ['affiliator_id' => $affiliatorId, 'channel' => $channel]);
    }
    
    /**
     * Get company/contact with full affiliation details for company dashboard
     * @param int $contactId
     * @return array|null
     */
    public function getCompanyDashboardData(int $contactId): ?array {
        $sql = "SELECT c.*, 
                       a.id as affiliation_id,
                       a.affiliation_date,
                       a.expiration_date,
                       a.status as affiliation_status,
                       a.payment_status,
                       a.amount as affiliation_amount,
                       a.invoice_number,
                       a.invoice_status,
                       m.name as membership_name,
                       m.code as membership_code,
                       m.benefits as membership_benefits,
                       DATEDIFF(a.expiration_date, CURDATE()) as days_remaining
                FROM {$this->table} c
                LEFT JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                LEFT JOIN membership_types m ON a.membership_type_id = m.id
                WHERE c.id = :contact_id";
        return $this->rawOne($sql, ['contact_id' => $contactId]);
    }
}
