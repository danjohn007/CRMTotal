-- CRM Total - Database Update Script
-- Version: 1.6.0
-- Date: 2025-11-27
-- Description: Event CCQ adjustments - Promotion price, event types, target audiences, company collaborators

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD PROMOTION PRICE AND PROMO END DATE TO EVENTS
-- =============================================

-- promo_price
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'promo_price'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `events` ADD COLUMN `promo_price` DECIMAL(10,2) DEFAULT 0 COMMENT "Precio de preventa/promoción" AFTER `price`;',
  'SELECT ''Column promo_price already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- promo_end_date
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'promo_end_date'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `events` ADD COLUMN `promo_end_date` DATETIME NULL COMMENT "Fecha límite de preventa (anterior al evento)" AFTER `promo_price`;',
  'SELECT ''Column promo_end_date already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- UPDATE EVENT TYPE ENUM TO INCLUDE 'publico' INSTEAD OF 'externo'
-- =============================================

-- Note: MySQL ENUM modification requires recreating the column
-- This updates existing 'externo' values to 'publico' (semantically correct mapping)
UPDATE `events` SET `event_type` = 'publico' WHERE `event_type` = 'externo';

-- Modify the ENUM to include 'publico'
ALTER TABLE `events` MODIFY COLUMN `event_type` ENUM('interno', 'publico', 'terceros') NOT NULL DEFAULT 'interno';

-- =============================================
-- ADD 'colaborador_empresa' TO CONTACTS TABLE
-- =============================================

-- Update the contact_type enum to include 'colaborador_empresa'
ALTER TABLE `contacts` MODIFY COLUMN `contact_type` ENUM(
    'afiliado', 
    'exafiliado', 
    'prospecto', 
    'nuevo_usuario', 
    'funcionario', 
    'publico_general', 
    'consejero_propietario', 
    'consejero_invitado', 
    'mesa_directiva',
    'colaborador_empresa'
) DEFAULT 'nuevo_usuario';

-- =============================================
-- CREATE EVENT CATEGORIES TABLE
-- =============================================

CREATE TABLE IF NOT EXISTS `event_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `color` VARCHAR(7) DEFAULT '#3b82f6',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default categories
INSERT IGNORE INTO `event_categories` (`name`, `description`, `color`) VALUES
('Networking', 'Eventos de networking empresarial', '#3b82f6'),
('Capacitación', 'Cursos y talleres', '#10b981'),
('Conferencia', 'Conferencias y pláticas', '#8b5cf6'),
('Webinar', 'Eventos en línea', '#f59e0b'),
('Social', 'Eventos sociales y celebraciones', '#ec4899'),
('Comercial', 'Exposiciones y ferias comerciales', '#6366f1'),
('Otro', 'Otros tipos de eventos', '#6b7280');

-- =============================================
-- CREATE EVENT TYPE CATALOG TABLE
-- =============================================

CREATE TABLE IF NOT EXISTS `event_type_catalog` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default event types
INSERT IGNORE INTO `event_type_catalog` (`code`, `name`, `description`) VALUES
('interno', 'Evento Interno CCQ', 'Eventos organizados por la Cámara de Comercio'),
('publico', 'Evento Público', 'Eventos abiertos al público general'),
('terceros', 'Evento de Terceros', 'Eventos organizados por terceros con apoyo de CCQ');

-- =============================================
-- ADD GUEST AS INVITEE FIELDS TO EVENT REGISTRATIONS
-- =============================================

-- is_guest (attending as guest/invitee)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'is_guest'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `is_guest` TINYINT(1) DEFAULT 0 COMMENT "Asiste como invitado" AFTER `registration_code`;',
  'SELECT ''Column is_guest already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- is_owner_representative
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'is_owner_representative'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `is_owner_representative` TINYINT(1) DEFAULT 1 COMMENT "Es dueño o representante legal" AFTER `is_guest`;',
  'SELECT ''Column is_owner_representative already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- attendee_name
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'attendee_name'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `attendee_name` VARCHAR(255) NULL COMMENT "Nombre del asistente al evento" AFTER `is_owner_representative`;',
  'SELECT ''Column attendee_name already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- attendee_position
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'attendee_position'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `attendee_position` VARCHAR(100) NULL COMMENT "Cargo del asistente" AFTER `attendee_name`;',
  'SELECT ''Column attendee_position already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- ADD QR CONFIGURATION SETTINGS
-- =============================================

INSERT IGNORE INTO `config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('qr_api_provider', 'google', 'text', 'Proveedor de API para generación de QR (google, qrserver, local)'),
('qr_size', '350', 'number', 'Tamaño del código QR en píxeles'),
('shelly_enabled', '0', 'boolean', 'Habilitar integración con Shelly Relay'),
('shelly_url', '', 'text', 'URL de la API de Shelly Relay'),
('shelly_channel', '0', 'text', 'Canal del Shelly Relay a controlar (0-3)');

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version": "1.6.0", "changes": ["events.promo_price", "events.promo_end_date", "events.event_type updated", "contacts.contact_type updated", "event_categories table", "event_type_catalog table", "event_registrations.is_guest", "event_registrations.is_owner_representative", "event_registrations.attendee_name", "event_registrations.attendee_position", "QR and Shelly config settings"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- DESCRIBE events;
-- DESCRIBE contacts;
-- DESCRIBE event_registrations;
-- SELECT * FROM event_categories;
-- SELECT * FROM event_type_catalog;
-- SELECT * FROM config WHERE config_key LIKE 'qr_%' OR config_key LIKE 'shelly_%';

-- =============================================
-- ROLLBACK SECTION (USE WITH CAUTION)
-- =============================================
-- ALTER TABLE `events` DROP COLUMN `promo_price`;
-- ALTER TABLE `events` DROP COLUMN `promo_end_date`;
-- ALTER TABLE `events` MODIFY COLUMN `event_type` ENUM('interno', 'externo', 'terceros') NOT NULL;
-- ALTER TABLE `event_registrations` DROP COLUMN `is_guest`;
-- ALTER TABLE `event_registrations` DROP COLUMN `is_owner_representative`;
-- ALTER TABLE `event_registrations` DROP COLUMN `attendee_name`;
-- ALTER TABLE `event_registrations` DROP COLUMN `attendee_position`;
-- DROP TABLE IF EXISTS `event_categories`;
-- DROP TABLE IF EXISTS `event_type_catalog`;
-- DELETE FROM `config` WHERE config_key IN ('qr_api_provider', 'qr_size', 'shelly_enabled', 'shelly_url', 'shelly_channel');

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Backup database before running this script
-- 2. Run after deploying the new PHP code
-- 3. Test event creation with new promo_price and promo_end_date fields
-- 4. Test prospect editing functionality
-- 5. Test affiliate conversion with preloaded data
-- 6. Verify QR validation in event attendance
-- 7. Test API configuration page with new QR and Shelly settings
