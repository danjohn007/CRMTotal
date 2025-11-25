<?php
/**
 * User Model
 */
class User extends Model {
    protected string $table = 'users';
    protected array $fillable = [
        'role_id', 'email', 'password', 'name', 'phone', 
        'whatsapp', 'address', 'avatar', 'is_active', 'reset_token', 'reset_token_expires'
    ];
    
    public function findByEmail(string $email): ?array {
        return $this->findBy('email', $email);
    }
    
    public function authenticate(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->db->update($this->table, 
                ['last_login' => date('Y-m-d H:i:s')], 
                'id = :id', 
                ['id' => $user['id']]
            );
            return $user;
        }
        return null;
    }
    
    public function getWithRole(int $id): ?array {
        $sql = "SELECT u.*, r.name as role_name, r.display_name as role_display 
                FROM {$this->table} u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.id = :id";
        return $this->rawOne($sql, ['id' => $id]);
    }
    
    public function getAllWithRoles(): array {
        $sql = "SELECT u.*, r.name as role_name, r.display_name as role_display 
                FROM {$this->table} u 
                JOIN roles r ON u.role_id = r.id 
                ORDER BY u.name";
        return $this->raw($sql);
    }
    
    public function getAffiliators(): array {
        $sql = "SELECT u.* FROM {$this->table} u 
                JOIN roles r ON u.role_id = r.id 
                WHERE r.name = 'afiliador' AND u.is_active = 1
                ORDER BY u.name";
        return $this->raw($sql);
    }
    
    public function createUser(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data);
    }
    
    public function updatePassword(int $id, string $newPassword): int {
        return $this->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }
    
    public function setResetToken(int $id, string $token, string $expires): int {
        return $this->update($id, [
            'reset_token' => $token,
            'reset_token_expires' => $expires
        ]);
    }
    
    public function findByResetToken(string $token): ?array {
        return $this->findBy('reset_token', $token);
    }
    
    public function clearResetToken(int $id): int {
        return $this->update($id, [
            'reset_token' => null,
            'reset_token_expires' => null
        ]);
    }
}
