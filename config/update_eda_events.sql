-- =============================================
-- Migration: Link EDA with Event Ticket Generation
-- Description: Updates to events and event_registrations tables
-- for enhanced ticket management and reporting
-- NOTE: Rewritten to be compatible with MySQL versions that
--       do NOT support "ADD COLUMN IF NOT EXISTS" or
--       "CREATE INDEX IF NOT EXISTS".
-- =============================================

-- USE crm_ccq;

SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- UPDATE EVENTS TABLE
-- Add room information and differentiate capacity from allowed attendees
-- =============================================

-- Add room_name
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'events' 
                     AND column_name = 'room_name');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `events` ADD COLUMN `room_name` VARCHAR(100) DEFAULT NULL COMMENT 'Nombre del salón donde se realizará el evento'",
    "SELECT 'Column room_name already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add room_capacity
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'events' 
                     AND column_name = 'room_capacity');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `events` ADD COLUMN `room_capacity` INT UNSIGNED DEFAULT NULL COMMENT 'Capacidad total del salón'",
    "SELECT 'Column room_capacity already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add allowed_attendees
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'events' 
                     AND column_name = 'allowed_attendees');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `events` ADD COLUMN `allowed_attendees` INT UNSIGNED DEFAULT NULL COMMENT 'Número de asistentes permitidos (puede ser diferente a la capacidad)'",
    "SELECT 'Column allowed_attendees already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add has_courtesy_tickets
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'events' 
                     AND column_name = 'has_courtesy_tickets');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `events` ADD COLUMN `has_courtesy_tickets` TINYINT(1) DEFAULT 1 COMMENT 'Si el evento de pago permite cortesías (0=sin cortesías, 1=con cortesías)'",
    "SELECT 'Column has_courtesy_tickets already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add promotional pricing fields
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'events' 
                     AND column_name = 'promo_price');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `events` ADD COLUMN `promo_price` DECIMAL(10,2) DEFAULT 0 COMMENT 'Precio promocional para público general'",
    "SELECT 'Column promo_price already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'events' 
                     AND column_name = 'promo_end_date');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `events` ADD COLUMN `promo_end_date` DATE DEFAULT NULL COMMENT 'Fecha fin de la promoción'",
    "SELECT 'Column promo_end_date already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'events' 
                     AND column_name = 'promo_member_price');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `events` ADD COLUMN `promo_member_price` DECIMAL(10,2) DEFAULT 0 COMMENT 'Precio promocional para afiliados'",
    "SELECT 'Column promo_member_price already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'events' 
                     AND column_name = 'free_for_affiliates');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `events` ADD COLUMN `free_for_affiliates` TINYINT(1) DEFAULT 1 COMMENT 'Si los afiliados tienen acceso gratuito/cortesía'",
    "SELECT 'Column free_for_affiliates already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- UPDATE EVENT_REGISTRATIONS TABLE
-- Restructure to properly link tickets with attendees
-- =============================================

-- registration_code column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'registration_code');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `registration_code` VARCHAR(50) DEFAULT NULL COMMENT 'Código único del boleto/registro'",
    "SELECT 'Column registration_code already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ensure unique index for registration_code (create if not exists)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND index_name = 'ux_registration_code');
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'registration_code');
SET @query = IF(@col_exists = 1 AND @idx_exists = 0,
    "CREATE UNIQUE INDEX `ux_registration_code` ON `event_registrations`(`registration_code`)",
    "SELECT 'Unique index ux_registration_code already exists or column missing'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- is_guest
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'is_guest');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `is_guest` TINYINT(1) DEFAULT 0 COMMENT 'Si es invitado (1) o cliente/afiliado (0)'",
    "SELECT 'Column is_guest already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- guest_type
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'guest_type');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `guest_type` ENUM('invitado_empresario','colaborador') DEFAULT NULL COMMENT 'Tipo de invitado'",
    "SELECT 'Column guest_type already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- is_owner_representative
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'is_owner_representative');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `is_owner_representative` TINYINT(1) DEFAULT 1 COMMENT 'Si es el dueño o representante legal (1) o un colaborador (0)'",
    "SELECT 'Column is_owner_representative already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- parent_registration_id
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'parent_registration_id');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `parent_registration_id` INT UNSIGNED DEFAULT NULL COMMENT 'ID del registro padre (quien invitó o de qué empresa es colaborador)'",
    "SELECT 'Column parent_registration_id already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- attendee_name
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'attendee_name');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `attendee_name` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del asistente real (cuando difiere del titular)'",
    "SELECT 'Column attendee_name already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- attendee_phone
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'attendee_phone');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `attendee_phone` VARCHAR(20) DEFAULT NULL COMMENT 'Teléfono del asistente real'",
    "SELECT 'Column attendee_phone already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- attendee_email
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'attendee_email');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `attendee_email` VARCHAR(255) DEFAULT NULL COMMENT 'Email del asistente real'",
    "SELECT 'Column attendee_email already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tickets
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'tickets');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `tickets` INT DEFAULT 1 COMMENT 'Número de boletos solicitados'",
    "SELECT 'Column tickets already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- total_amount
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'total_amount');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `total_amount` DECIMAL(10,2) DEFAULT 0 COMMENT 'Monto total a pagar'",
    "SELECT 'Column total_amount already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- qr_code
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'qr_code');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `qr_code` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del archivo QR code'",
    "SELECT 'Column qr_code already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- qr_sent
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'qr_sent');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `qr_sent` TINYINT(1) DEFAULT 0 COMMENT 'Si el QR fue enviado'",
    "SELECT 'Column qr_sent already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- qr_sent_at
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'qr_sent_at');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `qr_sent_at` TIMESTAMP NULL COMMENT 'Fecha y hora de envío del QR'",
    "SELECT 'Column qr_sent_at already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- confirmation_sent
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'confirmation_sent');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `confirmation_sent` TINYINT(1) DEFAULT 0 COMMENT 'Si la confirmación fue enviada'",
    "SELECT 'Column confirmation_sent already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- confirmation_sent_at
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'confirmation_sent_at');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `confirmation_sent_at` TIMESTAMP NULL COMMENT 'Fecha y hora de envío de confirmación'",
    "SELECT 'Column confirmation_sent_at already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- additional_attendees (JSON)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'additional_attendees');
SET @query = IF(@col_exists = 0,
    "ALTER TABLE `event_registrations` ADD COLUMN `additional_attendees` JSON COMMENT 'Información de asistentes adicionales'",
    "SELECT 'Column additional_attendees already exists'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Remove old column if exists (categoria_asistente replaced by guest_type)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'categoria_asistente');

SET @query = IF(@col_exists > 0, 
    'ALTER TABLE `event_registrations` DROP COLUMN `categoria_asistente`', 
    'SELECT "Column categoria_asistente does not exist"');

PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Remove attendee_position column as it's not needed per requirements
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'attendee_position');

SET @query = IF(@col_exists > 0, 
    'ALTER TABLE `event_registrations` DROP COLUMN `attendee_position`', 
    'SELECT "Column attendee_position does not exist"');

PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add foreign key for parent_registration_id if not exists
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'event_registrations' 
    AND constraint_name = 'fk_parent_registration');

SET @query = IF(@fk_exists = 0, 
    'ALTER TABLE `event_registrations` ADD CONSTRAINT `fk_parent_registration` FOREIGN KEY (`parent_registration_id`) REFERENCES `event_registrations`(`id`) ON DELETE SET NULL', 
    'SELECT "Foreign key fk_parent_registration already exists"');

PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create index on registration_code for faster lookups (if missing)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'registration_code');
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND index_name = 'idx_registration_code');
SET @query = IF(@col_exists = 1 AND @idx_exists = 0,
    "CREATE INDEX `idx_registration_code` ON `event_registrations`(`registration_code`)",
    "SELECT 'Index idx_registration_code already exists or column missing'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create index on attended for reporting
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'attended');
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND index_name = 'idx_attended');
SET @query = IF(@col_exists = 1 AND @idx_exists = 0,
    "CREATE INDEX `idx_attended` ON `event_registrations`(`attended`)",
    "SELECT 'Index idx_attended already exists or column missing'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create index on payment_status for filtering
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND column_name = 'payment_status');
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
                   WHERE table_schema = DATABASE() 
                     AND table_name = 'event_registrations' 
                     AND index_name = 'idx_payment_status');
SET @query = IF(@col_exists = 1 AND @idx_exists = 0,
    "CREATE INDEX `idx_payment_status` ON `event_registrations`(`payment_status`)",
    "SELECT 'Index idx_payment_status already exists or column missing'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- UPDATE CONTACTS TABLE
-- Add new contact types for better classification
-- =============================================

-- Modify ENUM to include new types (assumes column exists)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE table_schema = DATABASE()
                     AND table_name = 'contacts'
                     AND column_name = 'contact_type');

SET @query = IF(@col_exists = 1,
    "ALTER TABLE `contacts` MODIFY COLUMN `contact_type` ENUM('afiliado','exafiliado','prospecto','nuevo_usuario','funcionario','publico_general','consejero_propietario','consejero_invitado','patrocinador','mesa_directiva','invitado','colaborador_empresa') DEFAULT 'nuevo_usuario'",
    "SELECT 'contacts.contact_type column not found - skipping MODIFY'");
PREPARE stmt FROM @query; EXECUTE stmt; DEALLOCATE PREPARE stmt;

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
WHERE (`registration_code` IS NULL OR `registration_code` = '');

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
