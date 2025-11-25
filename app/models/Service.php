<?php
/**
 * Service Model (for cross-selling / up-selling)
 */
class Service extends Model {
    protected string $table = 'services';
    protected array $fillable = [
        'category', 'name', 'description', 'price', 'member_price', 'is_active'
    ];
    
    public function getByCategory(string $category): array {
        $sql = "SELECT * FROM {$this->table}
                WHERE category = :category AND is_active = 1
                ORDER BY name";
        return $this->raw($sql, ['category' => $category]);
    }
    
    public function getActive(): array {
        $sql = "SELECT * FROM {$this->table}
                WHERE is_active = 1
                ORDER BY category, name";
        return $this->raw($sql);
    }
    
    public function getCategories(): array {
        return [
            'salon_rental' => 'Renta de Salones',
            'event_organization' => 'Organización de Eventos',
            'course' => 'Cursos',
            'conference' => 'Conferencias',
            'training' => 'Capacitaciones',
            'marketing_email' => 'Marketing - Email',
            'marketing_videowall' => 'Marketing - Videowall',
            'marketing_social' => 'Marketing - Redes Sociales',
            'marketing_platform' => 'Marketing - Plataforma',
            'gestoria' => 'Gestoría',
            'tramites' => 'Trámites',
            'otros' => 'Otros Servicios'
        ];
    }
}

/**
 * Service Contract Model
 */
class ServiceContract extends Model {
    protected string $table = 'service_contracts';
    protected array $fillable = [
        'contact_id', 'service_id', 'affiliate_user_id', 'contract_date',
        'amount', 'status', 'payment_status', 'invoice_number', 'notes'
    ];
    
    public function getByContact(int $contactId): array {
        $sql = "SELECT sc.*, s.name as service_name, s.category,
                       u.name as affiliate_name
                FROM {$this->table} sc
                JOIN services s ON sc.service_id = s.id
                LEFT JOIN users u ON sc.affiliate_user_id = u.id
                WHERE sc.contact_id = :contact_id
                ORDER BY sc.contract_date DESC";
        return $this->raw($sql, ['contact_id' => $contactId]);
    }
    
    public function getByAffiliate(int $userId): array {
        $sql = "SELECT sc.*, s.name as service_name, s.category,
                       c.business_name, c.commercial_name
                FROM {$this->table} sc
                JOIN services s ON sc.service_id = s.id
                JOIN contacts c ON sc.contact_id = c.id
                WHERE sc.affiliate_user_id = :user_id
                ORDER BY sc.contract_date DESC";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    public function getRevenue(string $period = 'month'): float {
        $dateCondition = match($period) {
            'year' => "YEAR(contract_date) = YEAR(CURDATE())",
            'week' => "WEEK(contract_date) = WEEK(CURDATE()) AND YEAR(contract_date) = YEAR(CURDATE())",
            default => "MONTH(contract_date) = MONTH(CURDATE()) AND YEAR(contract_date) = YEAR(CURDATE())"
        };
        
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE {$dateCondition} AND payment_status = 'paid'";
        $result = $this->rawOne($sql);
        return (float) ($result['total'] ?? 0);
    }
    
    public function getStatsByCategory(): array {
        $sql = "SELECT s.category, COUNT(*) as count, SUM(sc.amount) as total
                FROM {$this->table} sc
                JOIN services s ON sc.service_id = s.id
                WHERE YEAR(sc.contract_date) = YEAR(CURDATE())
                GROUP BY s.category";
        return $this->raw($sql);
    }
}
