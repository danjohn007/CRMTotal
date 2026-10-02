<?php
/**
 * ApiRequestLog Model
 * Registro de las consultas a la API pública y límite de peticiones por IP
 * (tabla api_request_log, config/update_v2.9.0.sql).
 *
 * Si la tabla todavía no existe, no registra ni limita: la API sigue funcionando.
 */
class ApiRequestLog extends Model {
    protected string $table = 'api_request_log';
    protected array $fillable = ['endpoint', 'ip_address', 'query_value', 'result'];

    public function log(string $endpoint, ?string $queryValue, string $result): void {
        try {
            $this->db->insert($this->table, [
                'endpoint' => $endpoint,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'query_value' => $queryValue !== null ? substr($queryValue, 0, 64) : null,
                'result' => $result,
            ]);
        } catch (Throwable $e) {
            error_log('api_request_log no disponible: ' . $e->getMessage());
        }
    }

    /** true si la IP ya hizo $max peticiones a este endpoint en los últimos $minutes minutos. */
    public function exceeded(string $endpoint, int $max, int $minutes): bool {
        try {
            $row = $this->db->fetch(
                "SELECT COUNT(*) AS total FROM {$this->table}
                 WHERE endpoint = :endpoint AND ip_address = :ip
                   AND created_at >= DATE_SUB(NOW(), INTERVAL {$minutes} MINUTE)",
                ['endpoint' => $endpoint, 'ip' => $_SERVER['REMOTE_ADDR'] ?? '']
            );
            return (int) ($row['total'] ?? 0) >= $max;
        } catch (Throwable $e) {
            return false;
        }
    }
}
