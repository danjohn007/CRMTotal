<?php
/**
 * Search Log Model
 * Tracks searches in the intelligent provider search
 */
class SearchLog extends Model {
    protected string $table = 'search_logs';
    protected array $fillable = [
        'search_term', 'searcher_type', 'searcher_contact_id',
        'results_count', 'is_no_match', 'ip_address', 'user_agent'
    ];
    
    public function logSearch(string $term, int $resultsCount, string $type = 'publico', int $contactId = null): int {
        return $this->create([
            'search_term' => $term,
            'searcher_type' => $type,
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
                ORDER BY count DESC
                LIMIT 50";
        return $this->raw($sql);
    }
    
    public function getPopularSearches(int $limit = 20): array {
        $sql = "SELECT search_term, COUNT(*) as count
                FROM {$this->table}
                WHERE is_no_match = 0
                GROUP BY search_term
                ORDER BY count DESC
                LIMIT :limit";
        return $this->raw($sql, ['limit' => $limit]);
    }
    
    public function getSearchStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total_searches,
                    SUM(CASE WHEN is_no_match = 1 THEN 1 ELSE 0 END) as no_match_count,
                    SUM(CASE WHEN is_no_match = 0 THEN 1 ELSE 0 END) as with_results
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $result = $this->raw($sql);
        return $result[0] ?? [
            'total_searches' => 0,
            'no_match_count' => 0,
            'with_results' => 0
        ];
    }
    
    public function getByType(string $type): array {
        return $this->where('searcher_type', $type);
    }
}
