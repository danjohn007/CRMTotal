<?php
/**
 * Role Model
 * Manages user roles
 */
class Role extends Model {
    protected string $table = 'roles';
    protected array $fillable = [
        'name', 'display_name', 'description', 'permissions'
    ];
    
    public function findByName(string $name): ?array {
        return $this->findBy('name', $name);
    }
    
    public function getWithUserCount(): array {
        $sql = "SELECT r.*, COUNT(u.id) as user_count
                FROM {$this->table} r
                LEFT JOIN users u ON r.id = u.role_id
                GROUP BY r.id
                ORDER BY r.id";
        return $this->raw($sql);
    }
    
    public function hasPermission(int $roleId, string $permission): bool {
        $role = $this->find($roleId);
        if (!$role || empty($role['permissions'])) {
            return false;
        }
        
        $permissions = json_decode($role['permissions'], true);
        
        // Superadmin has all permissions
        if (isset($permissions['all']) && $permissions['all'] === true) {
            return true;
        }
        
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }
}
