-- CRM Total - Database Update Script (MySQL 5.7 compatible)
-- Version: 2.9.0
-- Date: 2026-10-02
-- Description: Registro y límite de consultas a la API pública
--              - api/v1/afiliacion (Grandes Empleados)
--              - api/buscar-empresa (registro a eventos)

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `api_request_log` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `query_value` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'RFC consultado, o hash SHA-256 del correo/teléfono',
  `result` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT 'afiliada, no_afiliada, encontrada, no_encontrada, no_autorizado, rfc_invalido, limite',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_api_log_limite` (`endpoint`, `ip_address`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
