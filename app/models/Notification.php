<?php
/**
 * Notification Model
 */
class Notification extends Model {
    protected string $table = 'notifications';
    protected array $fillable = [
        'user_id', 'type', 'title', 'message', 'link',
        'related_id', 'related_type', 'is_read', 'source_section'
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
    
    /**
     * Create a new prospect notification
     */
    public function createProspectAlert(int $userId, int $contactId, string $prospectName, string $channel): int {
        $channelLabels = [
            'chatbot' => 'Chatbot',
            'evento_gratuito' => 'Evento Gratuito',
            'evento_pagado' => 'Evento Pagado',
            'alta_directa' => 'Alta Directa',
            'buscador' => 'Buscador',
            'jefatura_comercial' => 'Jefatura Comercial'
        ];
        $channelLabel = $channelLabels[$channel] ?? $channel;
        
        return $this->create([
            'user_id' => $userId,
            'type' => 'prospecto',
            'title' => 'Nuevo prospecto asignado',
            'message' => "{$prospectName} - Fuente: {$channelLabel}",
            'link' => '/prospectos/' . $contactId,
            'related_id' => $contactId,
            'related_type' => 'contact'
        ]);
    }
    
    /**
     * Create an event notification
     */
    public function createEventAlert(int $userId, int $eventId, string $eventTitle): int {
        return $this->create([
            'user_id' => $userId,
            'type' => 'evento',
            'title' => 'Nuevo evento creado',
            'message' => $eventTitle,
            'link' => '/eventos/' . $eventId,
            'related_id' => $eventId,
            'related_type' => 'event'
        ]);
    }
    
    /**
     * Create an urgent action notification
     */
    public function createUrgentActionAlert(int $userId, int $activityId, string $title, string $message): int {
        return $this->create([
            'user_id' => $userId,
            'type' => 'actividad',
            'title' => '⚡ ' . $title,
            'message' => $message,
            'link' => '/agenda-comercial/editar/' . $activityId,
            'related_id' => $activityId,
            'related_type' => 'activity'
        ]);
    }
    
    /**
     * Get notifications grouped by type
     */
    public function getGroupedByType(int $userId): array {
        $notifications = $this->getUnread($userId);
        $grouped = [];
        
        foreach ($notifications as $notif) {
            $type = $notif['type'] ?? 'sistema';
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $notif;
        }
        
        return $grouped;
    }
}
