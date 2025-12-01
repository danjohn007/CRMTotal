<?php
/**
 * MembershipType Model
 * Manages membership types
 */
class MembershipType extends Model {
    protected string $table = 'membership_types';
    protected array $fillable = [
        'name', 'code', 'price', 'duration_days', 'benefits', 'characteristics', 'is_active', 'paypal_product_id'
    ];
    
    // Membership hierarchy (lowest to highest tier)
    // Higher tiers inherit benefits from lower tiers
    private const MEMBERSHIP_HIERARCHY = [
        'BASICA' => 1,
        'PYME' => 2,
        'VISIONARIO' => 3,
        'PREMIER' => 4,
        'PATROCINADOR' => 5
    ];
    
    public function getActive(): array {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY price";
        return $this->raw($sql);
    }
    
    public function findByCode(string $code): ?array {
        return $this->findBy('code', $code);
    }
    
    public function getAllWithStats(): array {
        $sql = "SELECT m.*, 
                       COUNT(a.id) as total_affiliations,
                       SUM(CASE WHEN a.status = 'active' THEN 1 ELSE 0 END) as active_affiliations
                FROM {$this->table} m
                LEFT JOIN affiliations a ON m.id = a.membership_type_id
                GROUP BY m.id
                ORDER BY m.price";
        return $this->raw($sql);
    }
    
    public function getRevenue(int $membershipId = null): float {
        $sql = "SELECT COALESCE(SUM(a.amount), 0) as total 
                FROM affiliations a 
                WHERE a.payment_status = 'paid'";
        $params = [];
        
        if ($membershipId) {
            $sql .= " AND a.membership_type_id = :membership_id";
            $params['membership_id'] = $membershipId;
        }
        
        $result = $this->rawOne($sql, $params);
        return (float) ($result['total'] ?? 0);
    }
    
    /**
     * Get all benefits for a membership type including inherited benefits
     * If a membership is Patrocinador, it inherits Premier, PYME, and Básica benefits
     * @param string $membershipCode
     * @return array Combined benefits from this membership and all lower tiers
     */
    public function getInheritedBenefits(string $membershipCode): array {
        $currentLevel = self::MEMBERSHIP_HIERARCHY[strtoupper($membershipCode)] ?? 0;
        
        if ($currentLevel === 0) {
            // Unknown membership, just return its own benefits
            $membership = $this->findByCode($membershipCode);
            return $membership ? json_decode($membership['benefits'] ?? '{}', true) : [];
        }
        
        // Get all memberships at or below this level
        $allBenefits = [];
        $sql = "SELECT code, benefits FROM {$this->table} WHERE is_active = 1 ORDER BY price ASC";
        $memberships = $this->raw($sql);
        
        foreach ($memberships as $membership) {
            $level = self::MEMBERSHIP_HIERARCHY[strtoupper($membership['code'])] ?? 0;
            if ($level > 0 && $level <= $currentLevel) {
                $membershipBenefits = json_decode($membership['benefits'] ?? '{}', true);
                // Merge benefits (higher tier values override lower tier values)
                foreach ($membershipBenefits as $key => $value) {
                    // For numeric values, take the higher value
                    // For boolean values, if any tier has it, include it
                    if (isset($allBenefits[$key])) {
                        if (is_numeric($value) && is_numeric($allBenefits[$key])) {
                            $allBenefits[$key] = max($value, $allBenefits[$key]);
                        } elseif ($value === 'ilimitadas' || $allBenefits[$key] === 'ilimitadas') {
                            $allBenefits[$key] = 'ilimitadas';
                        } elseif (is_bool($value) || $value === true) {
                            $allBenefits[$key] = true;
                        } else {
                            $allBenefits[$key] = $value;
                        }
                    } else {
                        $allBenefits[$key] = $value;
                    }
                }
            }
        }
        
        return $allBenefits;
    }
    
    /**
     * Get membership tier level
     * @param string $code
     * @return int
     */
    public function getTierLevel(string $code): int {
        return self::MEMBERSHIP_HIERARCHY[strtoupper($code)] ?? 0;
    }
    
    /**
     * Get all membership codes below a certain tier (for inheritance)
     * @param string $code
     * @return array
     */
    public function getLowerTierCodes(string $code): array {
        $currentLevel = self::MEMBERSHIP_HIERARCHY[strtoupper($code)] ?? 0;
        $lowerCodes = [];
        
        foreach (self::MEMBERSHIP_HIERARCHY as $membershipCode => $level) {
            if ($level < $currentLevel) {
                $lowerCodes[] = $membershipCode;
            }
        }
        
        return $lowerCodes;
    }
}
