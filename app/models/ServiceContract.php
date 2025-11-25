<?php
/**
 * Service Contract Model
 * Manages service contracts for cross-selling and upselling
 */
class ServiceContract extends Model {
    protected string $table = 'service_contracts';
    protected array $fillable = [
        'contact_id', 'service_id', 'affiliate_user_id',
        'contract_date', 'amount', 'status', 'payment_status',
        'invoice_number', 'notes'
    ];
    
    public function getByContact(int $contactId): array {
        $sql = "SELECT sc.*, s.name as service_name, s.category
                FROM {$this->table} sc
                JOIN services s ON sc.service_id = s.id
                WHERE sc.contact_id = :contact_id
                ORDER BY sc.contract_date DESC";
        return $this->raw($sql, ['contact_id' => $contactId]);
    }
    
    public function getByAffiliate(int $affiliateId): array {
        $sql = "SELECT sc.*, s.name as service_name, s.category,
                       c.business_name, c.commercial_name
                FROM {$this->table} sc
                JOIN services s ON sc.service_id = s.id
                JOIN contacts c ON sc.contact_id = c.id
                WHERE sc.affiliate_user_id = :affiliate_id
                ORDER BY sc.contract_date DESC";
        return $this->raw($sql, ['affiliate_id' => $affiliateId]);
    }
    
    public function getRevenue(string $period = 'month'): float {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total
                FROM {$this->table}
                WHERE payment_status = 'paid'";
        
        if ($period === 'month') {
            $sql .= " AND MONTH(contract_date) = MONTH(CURDATE()) AND YEAR(contract_date) = YEAR(CURDATE())";
        } elseif ($period === 'year') {
            $sql .= " AND YEAR(contract_date) = YEAR(CURDATE())";
        }
        
        $result = $this->raw($sql);
        return (float) ($result[0]['total'] ?? 0);
    }
    
    public function getStatsByCategory(): array {
        $sql = "SELECT s.category, COUNT(*) as count, COALESCE(SUM(sc.amount), 0) as total
                FROM {$this->table} sc
                JOIN services s ON sc.service_id = s.id
                WHERE sc.payment_status = 'paid'
                AND YEAR(sc.contract_date) = YEAR(CURDATE())
                GROUP BY s.category
                ORDER BY total DESC";
        return $this->raw($sql);
    }
    
    public function getAffiliatesWithoutServices(): array {
        $sql = "SELECT c.id, c.business_name, c.commercial_name, 
                       a.expiration_date, m.name as membership_name
                FROM contacts c
                JOIN affiliations a ON c.id = a.contact_id AND a.status = 'active'
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN service_contracts sc ON c.id = sc.contact_id
                WHERE c.contact_type = 'afiliado'
                AND sc.id IS NULL
                ORDER BY c.business_name
                LIMIT 50";
        return $this->raw($sql);
    }
}
