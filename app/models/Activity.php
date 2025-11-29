<?php
/**
 * Activity Model (Agenda)
 */
class Activity extends Model {
    protected string $table = 'activities';
    protected array $fillable = [
        'user_id', 'contact_id', 'activity_type', 'title', 'description',
        'scheduled_date', 'completed_date', 'status', 'result',
        'next_action', 'next_action_date', 'priority'
    ];
    
    public function getByUser(int $userId, string $status = null): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.phone, c.whatsapp
                FROM {$this->table} a
                LEFT JOIN contacts c ON a.contact_id = c.id
                WHERE a.user_id = :user_id";
        $params = ['user_id' => $userId];
        
        if ($status) {
            $sql .= " AND a.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY a.scheduled_date";
        return $this->raw($sql, $params);
    }
    
    public function getPending(int $userId): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.phone, c.whatsapp
                FROM {$this->table} a
                LEFT JOIN contacts c ON a.contact_id = c.id
                WHERE a.user_id = :user_id 
                AND a.status IN ('pendiente', 'en_progreso')
                ORDER BY a.priority DESC, a.scheduled_date";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    public function getToday(int $userId): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name, c.phone, c.whatsapp
                FROM {$this->table} a
                LEFT JOIN contacts c ON a.contact_id = c.id
                WHERE a.user_id = :user_id 
                AND DATE(a.scheduled_date) = CURDATE()
                ORDER BY a.scheduled_date";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    public function getByContact(int $contactId): array {
        $sql = "SELECT a.*, u.name as user_name
                FROM {$this->table} a
                JOIN users u ON a.user_id = u.id
                WHERE a.contact_id = :contact_id
                ORDER BY a.scheduled_date DESC";
        return $this->raw($sql, ['contact_id' => $contactId]);
    }
    
    public function getForCalendar(int $userId, string $start, string $end): array {
        $sql = "SELECT a.id, a.title, a.scheduled_date as start, 
                       a.activity_type, a.status, a.priority,
                       c.business_name, c.commercial_name
                FROM {$this->table} a
                LEFT JOIN contacts c ON a.contact_id = c.id
                WHERE a.user_id = :user_id
                AND a.scheduled_date BETWEEN :start AND :end";
        return $this->raw($sql, [
            'user_id' => $userId,
            'start' => $start,
            'end' => $end
        ]);
    }
    
    public function markComplete(int $id, string $result = null): int {
        return $this->update($id, [
            'status' => 'completada',
            'completed_date' => date('Y-m-d H:i:s'),
            'result' => $result
        ]);
    }
    
    public function getOverdue(int $userId): array {
        $sql = "SELECT a.*, c.business_name, c.commercial_name
                FROM {$this->table} a
                LEFT JOIN contacts c ON a.contact_id = c.id
                WHERE a.user_id = :user_id 
                AND a.status = 'pendiente'
                AND a.scheduled_date < NOW()
                ORDER BY a.scheduled_date";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    public function getStats(int $userId): array {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pendiente' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'completada' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'pendiente' AND scheduled_date < NOW() THEN 1 ELSE 0 END) as overdue
                FROM {$this->table}
                WHERE user_id = :user_id
                AND MONTH(scheduled_date) = MONTH(CURDATE())";
        return $this->rawOne($sql, ['user_id' => $userId]);
    }
    
    public function getActivityTypeStats(int $userId): array {
        $sql = "SELECT activity_type, COUNT(*) as count
                FROM {$this->table}
                WHERE user_id = :user_id
                AND MONTH(scheduled_date) = MONTH(CURDATE())
                GROUP BY activity_type";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get today's appointments (citas) with contact details
     * @param int $userId
     * @return array
     */
    public function getTodayAppointments(int $userId): array {
        $sql = "SELECT a.*, 
                       c.business_name, c.commercial_name, c.phone, c.whatsapp, 
                       c.owner_name, c.corporate_email, c.id as contact_id
                FROM {$this->table} a
                LEFT JOIN contacts c ON a.contact_id = c.id
                WHERE a.user_id = :user_id 
                AND DATE(a.scheduled_date) = CURDATE()
                AND a.activity_type IN ('reunion', 'visita', 'llamada')
                ORDER BY a.scheduled_date";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get upcoming activities for the week or until end of month
     * @param int $userId
     * @param string $range 'week' or 'month'
     * @return array
     */
    public function getUpcoming(int $userId, string $range = 'week'): array {
        $endDate = $range === 'month' 
            ? "LAST_DAY(CURDATE())"
            : "DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
            
        $sql = "SELECT a.*, 
                       c.business_name, c.commercial_name, c.phone, c.whatsapp,
                       c.owner_name, c.id as contact_id
                FROM {$this->table} a
                LEFT JOIN contacts c ON a.contact_id = c.id
                WHERE a.user_id = :user_id 
                AND DATE(a.scheduled_date) > CURDATE()
                AND DATE(a.scheduled_date) <= {$endDate}
                AND a.status IN ('pendiente', 'en_progreso')
                ORDER BY a.scheduled_date
                LIMIT 15";
        return $this->raw($sql, ['user_id' => $userId]);
    }
}
