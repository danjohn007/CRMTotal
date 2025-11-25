<?php
/**
 * Event Model
 */
class Event extends Model {
    protected string $table = 'events';
    protected array $fillable = [
        'title', 'description', 'event_type', 'category', 'start_date',
        'end_date', 'location', 'address', 'google_maps_url', 'is_online',
        'online_url', 'max_capacity', 'is_paid', 'price', 'member_price',
        'registration_url', 'image', 'status', 'target_audiences', 'created_by'
    ];
    
    public function getUpcoming(int $limit = 10): array {
        $sql = "SELECT e.*, 
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registered_count
                FROM {$this->table} e
                WHERE e.start_date >= NOW() AND e.status = 'published'
                ORDER BY e.start_date
                LIMIT {$limit}";
        return $this->raw($sql);
    }
    
    public function getPast(int $limit = 10): array {
        $sql = "SELECT e.*, 
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registered_count,
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id AND attended = 1) as attended_count
                FROM {$this->table} e
                WHERE e.end_date < NOW()
                ORDER BY e.start_date DESC
                LIMIT {$limit}";
        return $this->raw($sql);
    }
    
    public function getByType(string $type): array {
        $sql = "SELECT e.*, 
                       (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registered_count
                FROM {$this->table} e
                WHERE e.event_type = :type
                ORDER BY e.start_date DESC";
        return $this->raw($sql, ['type' => $type]);
    }
    
    public function findByRegistrationUrl(string $url): ?array {
        return $this->findBy('registration_url', $url);
    }
    
    public function getRegistrations(int $eventId): array {
        $sql = "SELECT er.*, c.business_name, c.commercial_name, c.contact_type
                FROM event_registrations er
                LEFT JOIN contacts c ON er.contact_id = c.id
                WHERE er.event_id = :event_id
                ORDER BY er.registration_date";
        return $this->raw($sql, ['event_id' => $eventId]);
    }
    
    public function registerAttendee(int $eventId, array $data): int {
        return $this->db->insert('event_registrations', array_merge(
            $data,
            ['event_id' => $eventId]
        ));
    }
    
    public function markAttendance(int $registrationId, bool $attended = true): int {
        return $this->db->update('event_registrations', [
            'attended' => $attended ? 1 : 0,
            'attendance_time' => $attended ? date('Y-m-d H:i:s') : null
        ], 'id = :id', ['id' => $registrationId]);
    }
    
    public function getRegistrationCount(int $eventId): int {
        $sql = "SELECT COUNT(*) as count FROM event_registrations WHERE event_id = :id";
        $result = $this->rawOne($sql, ['id' => $eventId]);
        return (int) ($result['count'] ?? 0);
    }
    
    public function getAttendanceCount(int $eventId): int {
        $sql = "SELECT COUNT(*) as count FROM event_registrations WHERE event_id = :id AND attended = 1";
        $result = $this->rawOne($sql, ['id' => $eventId]);
        return (int) ($result['count'] ?? 0);
    }
    
    public function getEventStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total_events,
                    SUM(CASE WHEN is_paid = 1 THEN 1 ELSE 0 END) as paid_events,
                    SUM(CASE WHEN is_paid = 0 THEN 1 ELSE 0 END) as free_events,
                    (SELECT COUNT(*) FROM event_registrations) as total_registrations,
                    (SELECT COUNT(*) FROM event_registrations WHERE attended = 1) as total_attendance
                FROM {$this->table}
                WHERE YEAR(start_date) = YEAR(CURDATE())";
        return $this->rawOne($sql);
    }
    
    public function generateUniqueUrl(string $title): string {
        $base = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $base = trim($base, '-');
        $url = $base;
        $counter = 1;
        
        while ($this->findByRegistrationUrl($url)) {
            $url = $base . '-' . $counter;
            $counter++;
        }
        
        return $url;
    }
}
