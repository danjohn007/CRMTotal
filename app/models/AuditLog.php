<?php
/**
 * AuditLog Model
 * Manages audit log entries
 */
class AuditLog extends Model {
    protected string $table = 'audit_log';
    protected array $fillable = [
        'user_id', 'action', 'table_name', 'record_id',
        'old_values', 'new_values', 'ip_address', 'user_agent'
    ];
    
    public function log(int $userId, string $action, string $table = null, int $recordId = null, 
                        array $oldValues = null, array $newValues = null): int {
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    public function getRecent(int $limit = 50): array {
        $sql = "SELECT al.*, u.name as user_name, u.email as user_email
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT :limit";
        return $this->raw($sql, ['limit' => $limit]);
    }
    
    public function getByUser(int $userId, int $limit = 50): array {
        $sql = "SELECT al.*
                FROM {$this->table} al
                WHERE al.user_id = :user_id
                ORDER BY al.created_at DESC
                LIMIT :limit";
        return $this->raw($sql, ['user_id' => $userId, 'limit' => $limit]);
    }
    
    public function getByTable(string $table, int $recordId = null): array {
        $sql = "SELECT al.*, u.name as user_name
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.table_name = :table_name";
        $params = ['table_name' => $table];
        
        if ($recordId) {
            $sql .= " AND al.record_id = :record_id";
            $params['record_id'] = $recordId;
        }
        
        $sql .= " ORDER BY al.created_at DESC LIMIT 100";
        return $this->raw($sql, $params);
    }
    
    public function getByAction(string $action): array {
        $sql = "SELECT al.*, u.name as user_name
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.action = :action
                ORDER BY al.created_at DESC
                LIMIT 100";
        return $this->raw($sql, ['action' => $action]);
    }
    
    public function getByDateRange(string $startDate, string $endDate): array {
        $sql = "SELECT al.*, u.name as user_name
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE DATE(al.created_at) BETWEEN :start_date AND :end_date
                ORDER BY al.created_at DESC";
        return $this->raw($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
    
    public function getStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total_actions,
                    COUNT(DISTINCT user_id) as unique_users,
                    COUNT(DISTINCT action) as unique_actions,
                    COUNT(DISTINCT table_name) as tables_affected
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        return $this->rawOne($sql) ?? [];
    }
    
    public function getActionsSummary(): array {
        $sql = "SELECT action, COUNT(*) as count
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY action
                ORDER BY count DESC
                LIMIT 20";
        return $this->raw($sql);
    }
    
    public function getUserActivity(): array {
        $sql = "SELECT u.id, u.name, u.email, COUNT(al.id) as action_count
                FROM users u
                LEFT JOIN {$this->table} al ON u.id = al.user_id 
                    AND al.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY u.id
                ORDER BY action_count DESC
                LIMIT 20";
        return $this->raw($sql);
    }
}
