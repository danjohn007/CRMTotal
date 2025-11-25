-- CRM Total - Database Update Script
-- Version: 1.5.0
-- Date: 2025-11-25
-- Description: Event registration improvements
--              - Free access for active affiliates
--              - Multiple registrations per email/RFC
--              - QR code support
--              - Email confirmations

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD FREE ACCESS FOR AFFILIATES TO EVENTS
-- =============================================

ALTER TABLE `events` 
ADD COLUMN `free_for_affiliates` TINYINT(1) DEFAULT 1 COMMENT 'Si está activo, afiliados vigentes obtienen 1 acceso gratis' 
AFTER `member_price`;

-- =============================================
-- ADD TICKETS TRACKING TO EVENT REGISTRATIONS
-- =============================================

-- Add tickets column if it doesn't exist
ALTER TABLE `event_registrations`
ADD COLUMN IF NOT EXISTS `tickets` INT UNSIGNED DEFAULT 1 COMMENT 'Número de boletos comprados'
AFTER `guest_rfc`;

-- =============================================
-- ADD QR CODE SUPPORT TO EVENT REGISTRATIONS
-- =============================================

ALTER TABLE `event_registrations`
ADD COLUMN `qr_code` VARCHAR(255) COMMENT 'Nombre del archivo QR generado'
AFTER `payment_status`,
ADD COLUMN `qr_sent` TINYINT(1) DEFAULT 0 COMMENT 'Si el QR fue enviado por email'
AFTER `qr_code`,
ADD COLUMN `qr_sent_at` TIMESTAMP NULL COMMENT 'Fecha de envío del QR'
AFTER `qr_sent`,
ADD COLUMN `confirmation_sent` TINYINT(1) DEFAULT 0 COMMENT 'Si el correo de confirmación fue enviado'
AFTER `qr_sent_at`,
ADD COLUMN `confirmation_sent_at` TIMESTAMP NULL COMMENT 'Fecha de envío de confirmación'
AFTER `confirmation_sent`;

-- =============================================
-- ADD UNIQUE REGISTRATION CODE FOR TRACKING
-- =============================================

ALTER TABLE `event_registrations`
ADD COLUMN `registration_code` VARCHAR(20) UNIQUE COMMENT 'Código único de registro'
AFTER `id`;

-- Generate registration codes for existing records
UPDATE `event_registrations` 
SET `registration_code` = CONCAT('REG-', LPAD(id, 8, '0'))
WHERE `registration_code` IS NULL;

-- =============================================
-- UPDATE INDEXES FOR BETTER PERFORMANCE
-- =============================================

-- Remove unique constraint on email if it exists (to allow multiple registrations)
-- Note: This may fail if the constraint doesn't exist, which is fine
-- ALTER TABLE `event_registrations` DROP INDEX `unique_email_event`;

-- Add index for better search performance
CREATE INDEX `idx_guest_email` ON `event_registrations` (`guest_email`);
CREATE INDEX `idx_guest_rfc` ON `event_registrations` (`guest_rfc`);
CREATE INDEX `idx_registration_code` ON `event_registrations` (`registration_code`);
CREATE INDEX `idx_payment_status` ON `event_registrations` (`payment_status`);

-- =============================================
-- UPDATE EXISTING EVENTS
-- =============================================

-- Set free_for_affiliates to 1 (true) for all existing events by default
UPDATE `events` 
SET `free_for_affiliates` = 1 
WHERE `free_for_affiliates` IS NULL;

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
SELECT 
    COALESCE((SELECT id FROM users ORDER BY id LIMIT 1), NULL),
    'schema_update', 
    NULL, 
    NULL, 
    '{"version": "1.5.0", "changes": ["events.free_for_affiliates", "event_registrations.tickets", "event_registrations.qr_code", "event_registrations.qr_sent", "event_registrations.confirmation_sent", "event_registrations.registration_code", "indexes"]}', 
    '127.0.0.1', 
    NOW();

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================

-- DESCRIBE events;
-- DESCRIBE event_registrations;
-- SHOW INDEX FROM event_registrations;
-- SELECT registration_code, guest_email FROM event_registrations LIMIT 5;

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================

-- ALTER TABLE `events` DROP COLUMN `free_for_affiliates`;
-- ALTER TABLE `event_registrations` DROP COLUMN `tickets`;
-- ALTER TABLE `event_registrations` DROP COLUMN `qr_code`;
-- ALTER TABLE `event_registrations` DROP COLUMN `qr_sent`;
-- ALTER TABLE `event_registrations` DROP COLUMN `qr_sent_at`;
-- ALTER TABLE `event_registrations` DROP COLUMN `confirmation_sent`;
-- ALTER TABLE `event_registrations` DROP COLUMN `confirmation_sent_at`;
-- ALTER TABLE `event_registrations` DROP COLUMN `registration_code`;
-- DROP INDEX `idx_guest_email` ON `event_registrations`;
-- DROP INDEX `idx_guest_rfc` ON `event_registrations`;
-- DROP INDEX `idx_registration_code` ON `event_registrations`;
-- DROP INDEX `idx_payment_status` ON `event_registrations`;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Backup database before running this script
-- 2. Run after deploying the new PHP code
-- 3. Test event creation with free_for_affiliates checkbox
-- 4. Test multiple registrations from same email/RFC
-- 5. Test email confirmation and QR code generation
-- 6. Verify mobile display of event images
