<?php
/**
 * Notification Model
 */
class Notification extends Model {
    protected string $table = 'notifications';
    protected array $fillable = [
        'user_id', 'type', 'title', 'message', 'link',
        'related_id', 'related_type', 'is_read'
    ];
    
    public function getUnread(int $userId): array {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = :user_id AND is_read = 0
                ORDER BY created_at DESC";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    public function getAll(int $userId, int $limit = 50): array {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT {$limit}";
        return $this->raw($sql, ['user_id' => $userId]);
    }
    
    public function markAsRead(int $id): int {
        return $this->update($id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function markAllAsRead(int $userId): int {
        $sql = "UPDATE {$this->table} SET is_read = 1, read_at = NOW()
                WHERE user_id = :user_id AND is_read = 0";
        $this->db->query($sql, ['user_id' => $userId]);
        return 1;
    }
    
    public function countUnread(int $userId): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}
                WHERE user_id = :user_id AND is_read = 0";
        $result = $this->rawOne($sql, ['user_id' => $userId]);
        return (int) ($result['count'] ?? 0);
    }
    
    public function createExpirationAlert(int $userId, int $affiliationId, string $businessName, int $daysLeft): int {
        return $this->create([
            'user_id' => $userId,
            'type' => 'vencimiento',
            'title' => 'Membresía próxima a vencer',
            'message' => "La membresía de {$businessName} vence en {$daysLeft} días",
            'link' => '/afiliados/' . $affiliationId,
            'related_id' => $affiliationId,
            'related_type' => 'affiliation'
        ]);
    }
    
    public function createActivityAlert(int $userId, int $activityId, string $title): int {
        return $this->create([
            'user_id' => $userId,
            'type' => 'actividad',
            'title' => 'Actividad pendiente',
            'message' => $title,
            'link' => '/agenda/' . $activityId,
            'related_id' => $activityId,
            'related_type' => 'activity'
        ]);
    }
    
    public function createNoMatchAlert(int $userId, string $searchTerm): int {
        return $this->create([
            'user_id' => $userId,
            'type' => 'no_match',
            'title' => 'Búsqueda sin resultados',
            'message' => "Un usuario buscó \"{$searchTerm}\" sin resultados - posible prospecto",
            'link' => '/buscador/no-match',
            'related_type' => 'search'
        ]);
    }
    
    public function createOpportunityAlert(int $userId, int $contactId, string $message): int {
        return $this->create([
            'user_id' => $userId,
            'type' => 'oportunidad',
            'title' => 'Nueva oportunidad',
            'message' => $message,
            'link' => '/afiliados/' . $contactId,
            'related_id' => $contactId,
            'related_type' => 'contact'
        ]);
    }
}
