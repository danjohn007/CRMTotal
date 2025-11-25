<?php
/**
 * Config Model (System Configuration)
 */
class Config extends Model {
    protected string $table = 'config';
    protected array $fillable = [
        'config_key', 'config_value', 'config_type', 'description'
    ];
    
    public function get(string $key, $default = null) {
        $config = $this->findBy('config_key', $key);
        if (!$config) {
            return $default;
        }
        
        return match($config['config_type']) {
            'boolean' => filter_var($config['config_value'], FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($config['config_value']) ? (float) $config['config_value'] : $default,
            'json' => json_decode($config['config_value'], true) ?? $default,
            default => $config['config_value']
        };
    }
    
    public function set(string $key, $value, string $type = 'text'): bool {
        $existing = $this->findBy('config_key', $key);
        
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        }
        
        if ($existing) {
            $this->db->update($this->table, [
                'config_value' => $value,
                'config_type' => $type
            ], 'id = :id', ['id' => $existing['id']]);
        } else {
            $this->db->insert($this->table, [
                'config_key' => $key,
                'config_value' => $value,
                'config_type' => $type
            ]);
        }
        
        return true;
    }
    
    public function getAll(): array {
        $configs = $this->all();
        $result = [];
        
        foreach ($configs as $config) {
            $result[$config['config_key']] = $this->get($config['config_key']);
        }
        
        return $result;
    }
    
    public function getGroup(string $prefix): array {
        $sql = "SELECT * FROM {$this->table} WHERE config_key LIKE :prefix";
        $configs = $this->raw($sql, ['prefix' => $prefix . '%']);
        $result = [];
        
        foreach ($configs as $config) {
            $key = str_replace($prefix . '_', '', $config['config_key']);
            $result[$key] = $this->get($config['config_key']);
        }
        
        return $result;
    }
}

/**
 * Search Log Model
 */
class SearchLog extends Model {
    protected string $table = 'search_logs';
    protected array $fillable = [
        'search_term', 'searcher_type', 'searcher_contact_id',
        'results_count', 'is_no_match', 'ip_address', 'user_agent'
    ];
    
    public function log(string $term, int $resultsCount, string $searcherType = 'publico', int $contactId = null): int {
        return $this->create([
            'search_term' => $term,
            'searcher_type' => $searcherType,
            'searcher_contact_id' => $contactId,
            'results_count' => $resultsCount,
            'is_no_match' => $resultsCount === 0 ? 1 : 0,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    public function getNoMatches(): array {
        $sql = "SELECT search_term, COUNT(*) as count, MAX(created_at) as last_search
                FROM {$this->table}
                WHERE is_no_match = 1
                GROUP BY search_term
                ORDER BY count DESC, last_search DESC
                LIMIT 50";
        return $this->raw($sql);
    }
    
    public function getPopularSearches(int $limit = 20): array {
        $sql = "SELECT search_term, COUNT(*) as count
                FROM {$this->table}
                WHERE is_no_match = 0
                GROUP BY search_term
                ORDER BY count DESC
                LIMIT {$limit}";
        return $this->raw($sql);
    }
    
    public function getSearchStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total_searches,
                    SUM(CASE WHEN is_no_match = 1 THEN 1 ELSE 0 END) as no_match_count,
                    COUNT(DISTINCT search_term) as unique_terms
                FROM {$this->table}
                WHERE MONTH(created_at) = MONTH(CURDATE())";
        return $this->rawOne($sql);
    }
}

/**
 * Membership Type Model
 */
class MembershipType extends Model {
    protected string $table = 'membership_types';
    protected array $fillable = [
        'name', 'code', 'price', 'duration_days', 'benefits', 'is_active'
    ];
    
    public function getActive(): array {
        return $this->where('is_active', 1);
    }
    
    public function findByCode(string $code): ?array {
        return $this->findBy('code', $code);
    }
}

/**
 * Role Model
 */
class Role extends Model {
    protected string $table = 'roles';
    protected array $fillable = ['name', 'display_name', 'description', 'permissions'];
}
