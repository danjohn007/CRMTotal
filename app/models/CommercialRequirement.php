<?php
/**
 * Commercial Requirement Model
 * Manages commercial requirements/opportunities
 */
class CommercialRequirement extends Model {
    protected string $table = 'commercial_requirements';
    protected array $fillable = [
        'title', 'description', 'contact_id', 'user_id', 'priority',
        'status', 'due_date', 'budget', 'category', 'notes'
    ];
    
    public function getAll(string $status = null): array {
        $sql = "SELECT cr.*, c.business_name, c.commercial_name, u.name as user_name
                FROM {$this->table} cr
                LEFT JOIN contacts c ON cr.contact_id = c.id
                LEFT JOIN users u ON cr.user_id = u.id";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE cr.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY cr.priority DESC, cr.due_date ASC";
        return $this->raw($sql, $params);
    }
    
    public function getByUser(int $userId, string $status = null): array {
        $sql = "SELECT cr.*, c.business_name, c.commercial_name
                FROM {$this->table} cr
                LEFT JOIN contacts c ON cr.contact_id = c.id
                WHERE cr.user_id = :user_id";
        $params = ['user_id' => $userId];
        
        if ($status) {
            $sql .= " AND cr.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY cr.priority DESC, cr.due_date ASC";
        return $this->raw($sql, $params);
    }
    
    public function getByContact(int $contactId): array {
        $sql = "SELECT cr.*, u.name as user_name
                FROM {$this->table} cr
                LEFT JOIN users u ON cr.user_id = u.id
                WHERE cr.contact_id = :contact_id
                ORDER BY cr.created_at DESC";
        return $this->raw($sql, ['contact_id' => $contactId]);
    }
    
    public function getPending(): array {
        return $this->getAll('pending');
    }
    
    public function getOverdue(): array {
        $sql = "SELECT cr.*, c.business_name, c.commercial_name, u.name as user_name
                FROM {$this->table} cr
                LEFT JOIN contacts c ON cr.contact_id = c.id
                LEFT JOIN users u ON cr.user_id = u.id
                WHERE cr.status = 'pending' 
                AND cr.due_date < CURDATE()
                ORDER BY cr.due_date ASC";
        return $this->raw($sql);
    }
    
    public function getStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'pending' AND due_date < CURDATE() THEN 1 ELSE 0 END) as overdue
                FROM {$this->table}";
        return $this->rawOne($sql) ?? [];
    }
    
    public function getByCategory(): array {
        $sql = "SELECT category, COUNT(*) as count
                FROM {$this->table}
                WHERE status IN ('pending', 'in_progress')
                GROUP BY category
                ORDER BY count DESC";
        return $this->raw($sql);
    }
}
