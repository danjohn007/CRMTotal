-- CRM Total - Database Update Script
-- Version: 1.6.0
-- Date: 2025-11-25
-- Description: Event registration enhancements - RFC validation, attendee classification, and payment logic

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- OPTIMIZE DATABASE QUERIES TO PREVENT TIMEOUTS
-- =============================================

-- Add index to improve query performance on event_registrations table
SET @idx := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND INDEX_NAME = 'idx_event_date'
);
SET @alter := IF(
  @idx = 0,
  'CREATE INDEX `idx_event_date` ON `event_registrations`(`event_id`, `registration_date`);',
  'SELECT ''Index already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- ADD NEW FIELDS FOR DETAILED EVENT REGISTRATION
-- =============================================

-- RazonSocial: Business name of the company/entity
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'razon_social'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `razon_social` VARCHAR(255) COMMENT "Razón social de la empresa/entidad" AFTER `guest_rfc`;',
  'SELECT ''Column already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- NombreEmpresarioRepresentante: Name of the business owner or legal representative
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'nombre_empresario_representante'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `nombre_empresario_representante` VARCHAR(255) COMMENT "Nombre del empresario o representante legal" AFTER `razon_social`;',
  'SELECT ''Column already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- NombreAsistente: Name of the attendee (REQUIRED for ticket issuance)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'nombre_asistente'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `nombre_asistente` VARCHAR(255) NOT NULL COMMENT "Nombre del asistente (obligatorio para boleto)" AFTER `nombre_empresario_representante`;',
  'SELECT ''Column already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- CategoriaAsistente: Category of the attendee (Socio, Empleado, etc.)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'categoria_asistente'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `categoria_asistente` ENUM("propietario", "socio", "empleado", "publico_general", "otro") DEFAULT NULL COMMENT "Categoría del asistente cuando no es el dueño" AFTER `nombre_asistente`;',
  'SELECT ''Column already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- EmailAsistente: Email of the attendee (when different from company email)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'email_asistente'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `email_asistente` VARCHAR(255) COMMENT "Email del asistente cuando no es el propietario" AFTER `categoria_asistente`;',
  'SELECT ''Column already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- WhatsAppAsistente: WhatsApp number of the attendee
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'whatsapp_asistente'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `whatsapp_asistente` VARCHAR(20) COMMENT "WhatsApp del asistente cuando no es el propietario" AFTER `email_asistente`;',
  'SELECT ''Column already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- RequierePago: Boolean flag to indicate if payment is required
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'requiere_pago'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `requiere_pago` TINYINT(1) DEFAULT 0 COMMENT "Indica si el asistente debe pagar (true cuando asistente != propietario)" AFTER `whatsapp_asistente`;',
  'SELECT ''Column already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- MIGRATE EXISTING DATA
-- =============================================

-- Populate nombre_asistente with guest_name for existing records
UPDATE `event_registrations`
SET `nombre_asistente` = COALESCE(`guest_name`, 'Asistente')
WHERE `nombre_asistente` IS NULL OR `nombre_asistente` = '';

-- Set categoria_asistente to 'propietario' for existing records
UPDATE `event_registrations`
SET `categoria_asistente` = 'propietario'
WHERE `categoria_asistente` IS NULL;

-- =============================================
-- IMPROVE RFC FIELD FOR VALIDATION
-- =============================================

-- Update RFC field to ensure proper validation
-- Note: RFC validation (12-13 chars) will be handled in application layer

-- =============================================
-- ADD INDEXES FOR BETTER PERFORMANCE
-- =============================================

-- Index on nombre_asistente for searching
SET @idx := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND INDEX_NAME = 'idx_nombre_asistente'
);
SET @alter := IF(
  @idx = 0,
  'CREATE INDEX `idx_nombre_asistente` ON `event_registrations`(`nombre_asistente`);',
  'SELECT ''Index already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Index on categoria_asistente for filtering
SET @idx := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND INDEX_NAME = 'idx_categoria_asistente'
);
SET @alter := IF(
  @idx = 0,
  'CREATE INDEX `idx_categoria_asistente` ON `event_registrations`(`categoria_asistente`);',
  'SELECT ''Index already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

-- Check if audit_log table exists before inserting
SET @audit_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'audit_log'
);

SET @audit_insert := IF(
  @audit_exists > 0,
  'INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
   SELECT
       COALESCE((SELECT id FROM users ORDER BY id LIMIT 1), NULL),
       ''schema_update'',
       NULL,
       NULL,
       ''{"version": "1.6.0", "changes": ["event_registrations.razon_social", "event_registrations.nombre_empresario_representante", "event_registrations.nombre_asistente", "event_registrations.categoria_asistente", "event_registrations.email_asistente", "event_registrations.whatsapp_asistente", "event_registrations.requiere_pago", "indexes_for_performance"]}'',
       ''127.0.0.1'',
       NOW();',
  'SELECT ''Audit log table does not exist, skipping...'';'
);

PREPARE stmt FROM @audit_insert; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- DESCRIBE event_registrations;
-- SHOW INDEX FROM event_registrations;
-- SELECT id, nombre_asistente, categoria_asistente, requiere_pago FROM event_registrations LIMIT 5;

-- =============================================
-- ROLLBACK SECTION (if needed)
-- =============================================
-- ALTER TABLE `event_registrations` DROP COLUMN `razon_social`;
-- ALTER TABLE `event_registrations` DROP COLUMN `nombre_empresario_representante`;
-- ALTER TABLE `event_registrations` DROP COLUMN `nombre_asistente`;
-- ALTER TABLE `event_registrations` DROP COLUMN `categoria_asistente`;
-- ALTER TABLE `event_registrations` DROP COLUMN `email_asistente`;
-- ALTER TABLE `event_registrations` DROP COLUMN `whatsapp_asistente`;
-- ALTER TABLE `event_registrations` DROP COLUMN `requiere_pago`;
-- DROP INDEX `idx_event_date` ON `event_registrations`;
-- DROP INDEX `idx_nombre_asistente` ON `event_registrations`;
-- DROP INDEX `idx_categoria_asistente` ON `event_registrations`;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Backup database before running this script
-- 2. This script is safe to run multiple times (idempotent)
-- 3. Existing registrations will be migrated automatically
-- 4. RFC validation (12-13 chars) is now REQUIRED in the application
-- 5. NombreAsistente is REQUIRED for all new registrations
-- 6. Payment logic: requiere_pago = true when NombreAsistente != NombreEmpresarioRepresentante
