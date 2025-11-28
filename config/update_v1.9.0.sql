-- CRM Total - Database Update Script
-- Version: 1.9.0
-- Date: 2025-11-28
-- Description: Improved QR code generation reliability

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- UPDATE QR CONFIGURATION
-- Change default provider to 'local' for more reliability
-- The system now has a pure PHP fallback QR generator
-- =============================================

-- Update existing configuration to use local generation as fallback
UPDATE `config` SET `config_value` = 'local', `description` = 'Proveedor de QR (qrserver, google, local). Sistema usa fallback local si API externa falla.' 
WHERE `config_key` = 'qr_api_provider';

-- Insert if not exists with local as default
INSERT IGNORE INTO `config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('qr_api_provider', 'local', 'text', 'Proveedor de QR (qrserver, google, local). Sistema usa fallback local si API externa falla.');

-- =============================================
-- ENSURE QR_SIZE CONFIGURATION EXISTS
-- =============================================

INSERT IGNORE INTO `config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('qr_size', '350', 'number', 'Tamaño del código QR en píxeles');

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"1.9.0","changes":["Added local PHP QR code generator as fallback","QR generation now works without external API access","Improved QR generation reliability for all event registrations"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- SELECT * FROM config WHERE config_key LIKE 'qr_%';
-- SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 5;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. This update adds a local PHP QR code generator that works without external API access.
-- 2. The system will now:
--    a. First try the external QR API (qrserver.com)
--    b. If that fails, use the local PHP generator as fallback
-- 3. This ensures QR codes are always generated for successful registrations.
-- 4. No database schema changes are required.
-- 5. Test event registration for both free and paid events.
-- 6. Verify QR codes are generated and can be scanned in Attendance Control.

-- =============================================
-- QR CODE GENERATION FLOW
-- =============================================
-- For FREE events:
--   1. User registers
--   2. Registration is saved to database
--   3. QR code is generated immediately
--   4. QR code is saved to /uploads/qr/
--   5. Confirmation email with QR code is sent
--   6. QR code is displayed on registration success page
--
-- For PAID events:
--   1. User registers
--   2. Registration is saved with payment_status = 'pending'
--   3. Pending payment email is sent
--   4. After payment confirmation:
--      - Payment status updated to 'paid'
--      - QR code is generated
--      - Access ticket email with QR code is sent
--
-- For COURTESY tickets (active affiliates):
--   1. Same as FREE events
--   2. payment_status is set to 'free' if total = 0
