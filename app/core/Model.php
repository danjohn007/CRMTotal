<?php
/**
 * Base Model Class
 * All models extend this class
 */
abstract class Model {
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    public function findBy(string $column, $value): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        return $this->db->fetch($sql, ['value' => $value]);
    }
    
    public function all(): array {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }
    
    public function where(string $column, $value): array {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        return $this->db->fetchAll($sql, ['value' => $value]);
    }
    
    public function create(array $data): int {
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        return $this->db->insert($this->table, $filteredData);
    }
    
    public function update(int $id, array $data): int {
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        return $this->db->update($this->table, $filteredData, "{$this->primaryKey} = :id", ['id' => $id]);
    }
    
    public function delete(int $id): int {
        return $this->db->delete($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }
    
    public function count(string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $result = $this->db->fetch($sql, $params);
        return (int) ($result['count'] ?? 0);
    }
    
    public function paginate(int $page = 1, int $perPage = 10, string $where = '', array $params = []): array {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $items = $this->db->fetchAll($sql, $params);
        $total = $this->count($where, $params);
        
        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    protected function raw(string $sql, array $params = []): array {
        return $this->db->fetchAll($sql, $params);
    }
    
    protected function rawOne(string $sql, array $params = []): ?array {
        return $this->db->fetch($sql, $params);
    }
    
    public function execute(string $sql, array $params = []): int {
        return $this->db->execute($sql, $params);
    }
}
