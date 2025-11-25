<?php
/**
 * MembershipType Model
 * Manages membership types
 */
class MembershipType extends Model {
    protected string $table = 'membership_types';
    protected array $fillable = [
        'name', 'code', 'price', 'duration_days', 'benefits', 'is_active'
    ];
    
    public function getActive(): array {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY price";
        return $this->raw($sql);
    }
    
    public function findByCode(string $code): ?array {
        return $this->findBy('code', $code);
    }
    
    public function getAllWithStats(): array {
        $sql = "SELECT m.*, 
                       COUNT(a.id) as total_affiliations,
                       SUM(CASE WHEN a.status = 'active' THEN 1 ELSE 0 END) as active_affiliations
                FROM {$this->table} m
                LEFT JOIN affiliations a ON m.id = a.membership_type_id
                GROUP BY m.id
                ORDER BY m.price";
        return $this->raw($sql);
    }
    
    public function getRevenue(int $membershipId = null): float {
        $sql = "SELECT COALESCE(SUM(a.amount), 0) as total 
                FROM affiliations a 
                WHERE a.payment_status = 'paid'";
        $params = [];
        
        if ($membershipId) {
            $sql .= " AND a.membership_type_id = :membership_id";
            $params['membership_id'] = $membershipId;
        }
        
        $result = $this->rawOne($sql, $params);
        return (float) ($result['total'] ?? 0);
    }
}
