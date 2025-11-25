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
