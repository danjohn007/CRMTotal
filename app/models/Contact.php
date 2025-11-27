<?php
/**
 * Contact Model
 * Manages the Digital Unique File (Expediente Digital Único)
 */
class Contact extends Model {
    protected string $table = 'contacts';
    protected array $fillable = [
        'rfc', 'whatsapp', 'contact_type', 'business_name', 'commercial_name',
        'owner_name', 'legal_representative', 'corporate_email', 'phone',
        'industry', 'niza_classification', 'products_sells', 'products_buys',
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
        // Try RFC first, then WhatsApp, then phone
        $contact = $this->findByRfc($identifier);
        if (!$contact) {
            $contact = $this->findByWhatsapp($identifier);
        }
        if (!$contact) {
            $contact = $this->findBy('phone', $identifier);
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
        $term = '%' . $term . '%';
        
        // Base query - affiliates only
        // Searches by: Name (business/commercial), RFC, Phone, WhatsApp, Email
        $sql = "SELECT c.id, c.business_name, c.commercial_name, c.industry,
                       c.products_sells, c.city, c.phone, c.website, c.contact_type,
                       c.rfc, c.whatsapp, c.corporate_email
                FROM {$this->table} c 
                WHERE c.contact_type = 'afiliado'
                AND (c.business_name LIKE :term1 
                     OR c.commercial_name LIKE :term2 
                     OR c.rfc LIKE :term3
                     OR c.phone LIKE :term4
                     OR c.whatsapp LIKE :term5
                     OR c.corporate_email LIKE :term6
                     OR c.industry LIKE :term7
                     OR JSON_SEARCH(c.products_sells, 'one', :term8) IS NOT NULL)";
        
        $params = [
            'term1' => $term,
            'term2' => $term,
            'term3' => $term,
            'term4' => $term,
            'term5' => $term,
            'term6' => $term,
            'term7' => $term,
            'term8' => $term
        ];
        
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
}
