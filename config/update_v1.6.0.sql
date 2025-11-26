-- CRM Total - Database Update Script
-- Version: 1.6.0
-- Date: 2025-11-26
-- Description: Updated event registration form fields

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD NEW REGISTRATION FIELDS TO EVENT REGISTRATIONS
-- =============================================

-- razon_social (Business name)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'razon_social'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `razon_social` VARCHAR(255) COMMENT "Razón social de la empresa" AFTER `guest_rfc`;',
  'SELECT ''Column razon_social already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- nombre_empresario (Business owner name / Representative name)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'nombre_empresario'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `nombre_empresario` VARCHAR(255) COMMENT "Nombre del empresario o representante" AFTER `razon_social`;',
  'SELECT ''Column nombre_empresario already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- nombre_asistente (Attendee name)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'nombre_asistente'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `nombre_asistente` VARCHAR(255) COMMENT "Nombre del asistente al evento" AFTER `nombre_empresario`;',
  'SELECT ''Column nombre_asistente already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- VERSION TRACKING
-- =============================================

INSERT INTO `config` (`key`, `value`, `description`) VALUES 
    ('db_update_1.6.0', 
     '{"version": "1.6.0", "changes": ["event_registrations.razon_social", "event_registrations.nombre_empresario", "event_registrations.nombre_asistente"]}', 
     'Database update 1.6.0 applied')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = CURRENT_TIMESTAMP;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- Run these queries to verify the update was applied correctly:
-- DESCRIBE event_registrations;

-- =============================================
-- ROLLBACK (if needed)
-- =============================================
-- ALTER TABLE `event_registrations` DROP COLUMN `razon_social`;
-- ALTER TABLE `event_registrations` DROP COLUMN `nombre_empresario`;
-- ALTER TABLE `event_registrations` DROP COLUMN `nombre_asistente`;
-- DELETE FROM `config` WHERE `key` = 'db_update_1.6.0';
