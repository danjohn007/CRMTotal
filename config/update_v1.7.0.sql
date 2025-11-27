-- CRM Total - Database Update Script
-- Version: 1.7.0
-- Date: 2025-11-27
-- Description: Event registration fixes - Guest contact type, QR generation, member pricing

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD 'invitado' TO CONTACTS TABLE contact_type ENUM
-- =============================================

-- Update the contact_type enum to include 'invitado' for event guests
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
    'colaborador_empresa',
    'invitado'
) DEFAULT 'nuevo_usuario';

-- =============================================
-- ADD FIELDS FOR TRACKING COURTESY TICKETS
-- =============================================

-- is_courtesy_ticket (for tracking free tickets given to affiliates)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'is_courtesy_ticket'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `is_courtesy_ticket` TINYINT(1) DEFAULT 0 COMMENT "Boleto de cortesía otorgado" AFTER `total_amount`;',
  'SELECT ''Column is_courtesy_ticket already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- UPDATE QR API CONFIGURATION
-- =============================================

-- Update qr_api_provider description and add qrserver as option
UPDATE `config` SET `config_value` = 'qrserver', `description` = 'Proveedor de API para generación de QR (google, qrserver, local)' 
WHERE `config_key` = 'qr_api_provider' AND `config_value` = 'google';

-- Insert if not exists
INSERT IGNORE INTO `config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('qr_api_provider', 'qrserver', 'text', 'Proveedor de API para generación de QR (google, qrserver, local)');

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"1.7.0","changes":["contacts.contact_type updated to include invitado","event_registrations.is_courtesy_ticket added","qr_api_provider default changed to qrserver"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- DESCRIBE contacts;
-- DESCRIBE event_registrations;
-- SELECT * FROM config WHERE config_key LIKE 'qr_%';
-- SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 5;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Backup database before running this script.
-- 2. Run after deploying the new PHP code.
-- 3. Test guest registration - should now save with contact_type 'invitado'
-- 4. Test affiliate member pricing - active affiliates should get member_price
-- 5. Test courtesy ticket prevention - same company should not get multiple free tickets
-- 6. Verify QR generation works with QR Server API
-- 7. Test email lookup in company search on public registration page
