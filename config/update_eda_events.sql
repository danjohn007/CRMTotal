-- =============================================
-- Migration: Link EDA with Event Ticket Generation
-- Description: Updates to events and event_registrations tables
-- for enhanced ticket management and reporting
-- =============================================

USE crm_ccq;

SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- UPDATE EVENTS TABLE
-- Add room information and differentiate capacity from allowed attendees
-- =============================================

ALTER TABLE `events` 
ADD COLUMN IF NOT EXISTS `room_name` VARCHAR(100) DEFAULT NULL COMMENT 'Nombre del salón donde se realizará el evento',
ADD COLUMN IF NOT EXISTS `room_capacity` INT UNSIGNED DEFAULT NULL COMMENT 'Capacidad total del salón',
ADD COLUMN IF NOT EXISTS `allowed_attendees` INT UNSIGNED DEFAULT NULL COMMENT 'Número de asistentes permitidos (puede ser diferente a la capacidad)',
ADD COLUMN IF NOT EXISTS `has_courtesy_tickets` TINYINT(1) DEFAULT 1 COMMENT 'Si el evento de pago permite cortesías (0=sin cortesías, 1=con cortesías)';

-- Add promotional pricing fields if not exist
ALTER TABLE `events` 
ADD COLUMN IF NOT EXISTS `promo_price` DECIMAL(10,2) DEFAULT 0 COMMENT 'Precio promocional para público general',
ADD COLUMN IF NOT EXISTS `promo_end_date` DATE DEFAULT NULL COMMENT 'Fecha fin de la promoción',
ADD COLUMN IF NOT EXISTS `promo_member_price` DECIMAL(10,2) DEFAULT 0 COMMENT 'Precio promocional para afiliados',
ADD COLUMN IF NOT EXISTS `free_for_affiliates` TINYINT(1) DEFAULT 1 COMMENT 'Si los afiliados tienen acceso gratuito/cortesía';

-- =============================================
-- UPDATE EVENT_REGISTRATIONS TABLE
-- Restructure to properly link tickets with attendees
-- =============================================

-- Add new columns for better attendee tracking
ALTER TABLE `event_registrations`
ADD COLUMN IF NOT EXISTS `registration_code` VARCHAR(50) UNIQUE COMMENT 'Código único del boleto/registro',
ADD COLUMN IF NOT EXISTS `is_guest` TINYINT(1) DEFAULT 0 COMMENT 'Si es invitado (1) o cliente/afiliado (0)',
ADD COLUMN IF NOT EXISTS `guest_type` ENUM('invitado_empresario', 'colaborador') DEFAULT NULL COMMENT 'Tipo de invitado',
ADD COLUMN IF NOT EXISTS `is_owner_representative` TINYINT(1) DEFAULT 1 COMMENT 'Si es el dueño o representante legal (1) o un colaborador (0)',
ADD COLUMN IF NOT EXISTS `parent_registration_id` INT UNSIGNED DEFAULT NULL COMMENT 'ID del registro padre (quien invitó o de qué empresa es colaborador)',
ADD COLUMN IF NOT EXISTS `attendee_name` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del asistente real (cuando difiere del titular)',
ADD COLUMN IF NOT EXISTS `attendee_phone` VARCHAR(20) DEFAULT NULL COMMENT 'Teléfono del asistente real',
ADD COLUMN IF NOT EXISTS `attendee_email` VARCHAR(255) DEFAULT NULL COMMENT 'Email del asistente real',
ADD COLUMN IF NOT EXISTS `tickets` INT DEFAULT 1 COMMENT 'Número de boletos solicitados',
ADD COLUMN IF NOT EXISTS `total_amount` DECIMAL(10,2) DEFAULT 0 COMMENT 'Monto total a pagar',
ADD COLUMN IF NOT EXISTS `qr_code` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del archivo QR code',
ADD COLUMN IF NOT EXISTS `qr_sent` TINYINT(1) DEFAULT 0 COMMENT 'Si el QR fue enviado',
ADD COLUMN IF NOT EXISTS `qr_sent_at` TIMESTAMP NULL COMMENT 'Fecha y hora de envío del QR',
ADD COLUMN IF NOT EXISTS `confirmation_sent` TINYINT(1) DEFAULT 0 COMMENT 'Si la confirmación fue enviada',
ADD COLUMN IF NOT EXISTS `confirmation_sent_at` TIMESTAMP NULL COMMENT 'Fecha y hora de envío de confirmación',
ADD COLUMN IF NOT EXISTS `additional_attendees` JSON COMMENT 'Información de asistentes adicionales';

-- Remove old column if exists (categoria_asistente replaced by guest_type)
-- Note: Using DROP COLUMN IF EXISTS for MySQL 5.7+ compatibility
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'event_registrations' 
    AND column_name = 'categoria_asistente');

SET @query = IF(@col_exists > 0, 
    'ALTER TABLE `event_registrations` DROP COLUMN `categoria_asistente`', 
    'SELECT "Column categoria_asistente does not exist"');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Remove attendee_position column as it's not needed per requirements
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'event_registrations' 
    AND column_name = 'attendee_position');

SET @query = IF(@col_exists > 0, 
    'ALTER TABLE `event_registrations` DROP COLUMN `attendee_position`', 
    'SELECT "Column attendee_position does not exist"');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key for parent_registration_id if not exists
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'event_registrations' 
    AND constraint_name = 'fk_parent_registration');

SET @query = IF(@fk_exists = 0, 
    'ALTER TABLE `event_registrations` ADD CONSTRAINT `fk_parent_registration` 
     FOREIGN KEY (`parent_registration_id`) REFERENCES `event_registrations`(`id`) ON DELETE SET NULL', 
    'SELECT "Foreign key fk_parent_registration already exists"');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create index on registration_code for faster lookups
CREATE INDEX IF NOT EXISTS `idx_registration_code` ON `event_registrations`(`registration_code`);

-- Create index on attended for reporting
CREATE INDEX IF NOT EXISTS `idx_attended` ON `event_registrations`(`attended`);

-- Create index on payment_status for filtering
CREATE INDEX IF NOT EXISTS `idx_payment_status` ON `event_registrations`(`payment_status`);

-- =============================================
-- UPDATE CONTACTS TABLE
-- Add new contact types for better classification
-- =============================================

-- Update contact_type ENUM to include new types
ALTER TABLE `contacts` 
MODIFY COLUMN `contact_type` ENUM(
    'afiliado', 
    'exafiliado', 
    'prospecto', 
    'nuevo_usuario', 
    'funcionario', 
    'publico_general', 
    'consejero_propietario', 
    'consejero_invitado', 
    'patrocinador',
    'mesa_directiva',
    'invitado',
    'colaborador_empresa'
) DEFAULT 'nuevo_usuario';

-- =============================================
-- CREATE EVENT CATEGORIES TABLE (if not exists)
-- =============================================

CREATE TABLE IF NOT EXISTS `event_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `color` VARCHAR(7) DEFAULT '#3b82f6',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- CREATE EVENT TYPE CATALOG TABLE (if not exists)
-- =============================================

CREATE TABLE IF NOT EXISTS `event_type_catalog` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default event types if table is empty
INSERT INTO `event_type_catalog` (`code`, `name`, `description`, `is_active`) 
SELECT * FROM (
    SELECT 'interno' as code, 'Evento Interno CCQ' as name, 'Eventos organizados internamente por la Cámara' as description, 1 as is_active
    UNION ALL
    SELECT 'publico', 'Evento Público', 'Eventos abiertos al público en general', 1
    UNION ALL
    SELECT 'terceros', 'Evento de Terceros', 'Eventos organizados por terceros', 1
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM `event_type_catalog` LIMIT 1);

-- =============================================
-- DATA MIGRATION
-- Migrate existing registration_code if not present
-- =============================================

-- For existing records without registration_code, generate one
UPDATE `event_registrations` 
SET `registration_code` = CONCAT('REG-', DATE_FORMAT(registration_date, '%Y%m%d'), '-', UPPER(SUBSTRING(MD5(CONCAT(id, RAND())), 1, 6)))
WHERE `registration_code` IS NULL OR `registration_code` = '';

-- Ensure all records have proper default values for new columns
UPDATE `event_registrations` 
SET `is_guest` = 0 
WHERE `is_guest` IS NULL;

UPDATE `event_registrations` 
SET `is_owner_representative` = 1 
WHERE `is_owner_representative` IS NULL;

UPDATE `event_registrations` 
SET `tickets` = 1 
WHERE `tickets` IS NULL OR `tickets` = 0;

UPDATE `event_registrations` 
SET `qr_sent` = 0 
WHERE `qr_sent` IS NULL;

UPDATE `event_registrations` 
SET `confirmation_sent` = 0 
WHERE `confirmation_sent` IS NULL;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- Run these to verify the migration was successful
-- =============================================

-- Check events table structure
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events'
-- ORDER BY ORDINAL_POSITION;

-- Check event_registrations table structure
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations'
-- ORDER BY ORDINAL_POSITION;

-- Check for duplicate registration codes (should return 0)
-- SELECT registration_code, COUNT(*) as count 
-- FROM event_registrations 
-- WHERE registration_code IS NOT NULL
-- GROUP BY registration_code 
-- HAVING count > 1;

SELECT 'Migration completed successfully!' as Status;
