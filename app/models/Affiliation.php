<?php
/**
 * Affiliation Model
 */
class Affiliation extends Model {
    protected string $table = 'affiliations';
    protected array $fillable = [
        'contact_id', 'membership_type_id', 'affiliate_user_id',
        'affiliation_date', 'expiration_date', 'status', 'payment_status',
        'amount', 'payment_method', 'payment_reference', 'invoice_number',
        'invoice_status', 'notes'
    ];
    
    public function getByContact(int $contactId): array {
        $sql = "SELECT a.*, m.name as membership_name, m.code as membership_code,
                       u.name as affiliate_name
                FROM {$this->table} a
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON a.affiliate_user_id = u.id
                WHERE a.contact_id = :contact_id
                ORDER BY a.affiliation_date DESC";
        return $this->raw($sql, ['contact_id' => $contactId]);
    }
    
    public function getActive(): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.rfc,
                       m.name as membership_name, u.name as affiliate_name
                FROM {$this->table} a
                JOIN contacts c ON a.contact_id = c.id
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON a.affiliate_user_id = u.id
                WHERE a.status = 'active'
                ORDER BY a.expiration_date";
        return $this->raw($sql);
    }
    
    public function getExpiringSoon(int $days = 30): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.rfc, c.phone, c.corporate_email,
                       m.name as membership_name, u.name as affiliate_name, u.id as affiliate_id
                FROM {$this->table} a
                JOIN contacts c ON a.contact_id = c.id
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON a.affiliate_user_id = u.id
                WHERE a.status = 'active'
                AND a.expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                ORDER BY a.expiration_date";
        return $this->raw($sql, ['days' => $days]);
    }
    
    public function getExpired(): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.rfc,
                       m.name as membership_name, u.name as affiliate_name
                FROM {$this->table} a
                JOIN contacts c ON a.contact_id = c.id
                JOIN membership_types m ON a.membership_type_id = m.id
                LEFT JOIN users u ON a.affiliate_user_id = u.id
                WHERE a.status = 'expired' OR (a.status = 'active' AND a.expiration_date < CURDATE())
                ORDER BY a.expiration_date DESC";
        return $this->raw($sql);
    }
    
    public function getByAffiliate(int $userId): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name,
                       m.name as membership_name
                FROM {$this->table} a
                JOIN contacts c ON a.contact_id = c.id
                JOIN membership_types m ON a.membership_type_id = m.id
                WHERE a.affiliate_user_id = :user_id
                ORDER BY a.affiliation_date DESC";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    public function countByStatus(): array {
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status";
        return $this->raw($sql);
    }
    
    public function countByAffiliate(int $userId, string $period = 'month'): array {
        $dateCondition = $period === 'year' 
            ? "YEAR(affiliation_date) = YEAR(CURDATE())"
            : "MONTH(affiliation_date) = MONTH(CURDATE()) AND YEAR(affiliation_date) = YEAR(CURDATE())";
            
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN contact_id IN (SELECT id FROM contacts WHERE contact_type = 'afiliado' AND created_at >= DATE_SUB(affiliation_date, INTERVAL 1 DAY)) THEN 1 ELSE 0 END) as new_affiliations,
                    SUM(amount) as total_amount
                FROM {$this->table}
                WHERE affiliate_user_id = :user_id AND {$dateCondition}";
        return $this->rawOne($sql, ['user_id' => $userId]);
    }
    
    public function getMonthlyStats(int $year = null): array {
        $year = $year ?? date('Y');
        $sql = "SELECT 
                    MONTH(affiliation_date) as month,
                    COUNT(*) as count,
                    SUM(amount) as total
                FROM {$this->table}
                WHERE YEAR(affiliation_date) = :year
                GROUP BY MONTH(affiliation_date)
                ORDER BY month";
        return $this->raw($sql, ['year' => $year]);
    }
    
    public function getTotalRevenue(string $period = 'month'): float {
        $dateCondition = match($period) {
            'year' => "YEAR(affiliation_date) = YEAR(CURDATE())",
            'week' => "WEEK(affiliation_date) = WEEK(CURDATE()) AND YEAR(affiliation_date) = YEAR(CURDATE())",
            default => "MONTH(affiliation_date) = MONTH(CURDATE()) AND YEAR(affiliation_date) = YEAR(CURDATE())"
        };
        
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM {$this->table} WHERE {$dateCondition} AND payment_status = 'paid'";
        $result = $this->rawOne($sql);
        return (float) ($result['total'] ?? 0);
    }
    
    public function getRenewalRate(): float {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM {$this->table} WHERE status = 'active') as active,
                    (SELECT COUNT(*) FROM {$this->table} WHERE status = 'expired') as expired";
        $result = $this->rawOne($sql);
        $total = $result['active'] + $result['expired'];
        return $total > 0 ? round(($result['active'] / $total) * 100, 1) : 0;
    }
    
    /**
     * Get sales statistics for a specific user by period
     * @param int $userId
     * @param string $period 'today', 'yesterday', 'current_week', 'last_week', 'current_month', 'last_month', 'year'
     * @return array
     */
    public function getSalesStatsByPeriod(int $userId, string $period): array {
        $dateCondition = match($period) {
            'today' => "DATE(affiliation_date) = CURDATE()",
            'yesterday' => "DATE(affiliation_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)",
            'current_week' => "YEARWEEK(affiliation_date, 1) = YEARWEEK(CURDATE(), 1)",
            'last_week' => "YEARWEEK(affiliation_date, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)",
            'current_month' => "MONTH(affiliation_date) = MONTH(CURDATE()) AND YEAR(affiliation_date) = YEAR(CURDATE())",
            'last_month' => "MONTH(affiliation_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(affiliation_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))",
            'year' => "YEAR(affiliation_date) = YEAR(CURDATE())",
            default => "DATE(affiliation_date) = CURDATE()"
        };
        
        $sql = "SELECT 
                    COUNT(*) as count,
                    COALESCE(SUM(amount), 0) as amount
                FROM {$this->table}
                WHERE affiliate_user_id = :user_id 
                AND {$dateCondition}
                AND payment_status = 'paid'";
        return $this->rawOne($sql, ['user_id' => $userId]) ?? ['count' => 0, 'amount' => 0];
    }
    
    /**
     * Get daily sales for the current week for charts
     * @param int $userId
     * @return array
     */
    public function getWeeklySalesChart(int $userId): array {
        $sql = "SELECT 
                    DATE(affiliation_date) as date,
                    DAYOFWEEK(affiliation_date) as day_num,
                    COUNT(*) as count,
                    COALESCE(SUM(amount), 0) as amount
                FROM {$this->table}
                WHERE affiliate_user_id = :user_id
                AND YEARWEEK(affiliation_date, 1) = YEARWEEK(CURDATE(), 1)
                AND payment_status = 'paid'
                GROUP BY DATE(affiliation_date)
                ORDER BY date";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get monthly sales for the current year for charts
     * @param int $userId
     * @return array
     */
    public function getMonthlySalesChart(int $userId): array {
        $sql = "SELECT 
                    MONTH(affiliation_date) as month,
                    COUNT(*) as count,
                    COALESCE(SUM(amount), 0) as amount
                FROM {$this->table}
                WHERE affiliate_user_id = :user_id
                AND YEAR(affiliation_date) = YEAR(CURDATE())
                AND payment_status = 'paid'
                GROUP BY MONTH(affiliation_date)
                ORDER BY month";
        return $this->raw($sql, ['user_id' => $userId]);
    }
}
