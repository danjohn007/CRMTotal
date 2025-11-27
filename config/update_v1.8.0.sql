-- CRM Total - Database Update Script
-- Version: 1.8.0
-- Date: 2025-11-27
-- Description: Event pricing improvements, email templates, and guest ticket restrictions

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD PROMO_MEMBER_PRICE FIELD TO EVENTS
-- This is the presale price for affiliate members
-- =============================================

-- promo_member_price (Precio Preventa Afiliado)
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'promo_member_price'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `events` ADD COLUMN `promo_member_price` DECIMAL(10,2) DEFAULT 0 COMMENT "Precio de preventa para afiliados" AFTER `member_price`;',
  'SELECT ''Column promo_member_price already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- UPDATE free_for_affiliates COLUMN IF NOT EXISTS
-- This controls whether affiliates get a courtesy ticket
-- =============================================

SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'free_for_affiliates'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `events` ADD COLUMN `free_for_affiliates` TINYINT(1) DEFAULT 1 COMMENT "Afiliados obtienen 1 boleto gratis" AFTER `promo_member_price`;',
  'SELECT ''Column free_for_affiliates already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- ADD EMAIL TEMPLATE TRACKING COLUMNS
-- =============================================

-- Track which type of email was sent
SET @col := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'email_type_sent'
);
SET @alter := IF(
  @col = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `email_type_sent` ENUM(''pending_payment'', ''confirmation'', ''access_ticket'') NULL COMMENT "Tipo de email enviado" AFTER `qr_sent_at`;',
  'SELECT ''Column email_type_sent already exists, skipping...'';'
);
PREPARE stmt FROM @alter; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- UPDATE EVENT PRICING DESCRIPTION IN SAMPLE DATA
-- Show example of all 4 price types
-- =============================================

-- Update existing sample events to demonstrate pricing tiers
UPDATE `events` SET 
    `promo_price` = CASE 
        WHEN `price` > 0 THEN `price` * 0.85  -- 15% off for presale
        ELSE 0 
    END,
    `promo_member_price` = CASE 
        WHEN `member_price` > 0 THEN `member_price` * 0.85  -- 15% off member presale
        WHEN `price` > 0 THEN `price` * 0.70  -- 30% off for member presale if no member price
        ELSE 0 
    END,
    `promo_end_date` = DATE_SUB(`start_date`, INTERVAL 7 DAY)  -- Presale ends 7 days before event
WHERE `is_paid` = 1 AND `promo_end_date` IS NULL;

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"1.8.0","changes":["events.promo_member_price added (Precio Preventa Afiliado)","event_registrations.email_type_sent added","Guest ticket restriction enforced (1 ticket max for guests)","HTML email templates for pending payment and access tickets","Presale pricing logic with 4 price tiers"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES (uncomment to test)
-- =============================================
-- DESCRIBE events;
-- DESCRIBE event_registrations;
-- SELECT id, title, price, promo_price, member_price, promo_member_price, promo_end_date FROM events WHERE is_paid = 1;
-- SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 5;

-- =============================================
-- PRICING STRUCTURE DOCUMENTATION
-- =============================================
-- The system now supports 4 different price tiers for paid events:
-- 
-- 1. price (Costo del Evento)
--    Regular price for public/non-affiliate attendees
--    
-- 2. promo_price (Precio de Preventa)
--    Presale price for public attendees, valid until promo_end_date
--    
-- 3. member_price (Precio Afiliados)
--    Regular price for active affiliate members
--    
-- 4. promo_member_price (Precio Preventa Afiliado)
--    Presale price for active affiliate members, valid until promo_end_date
--
-- Price selection logic (in order of priority):
-- - If affiliate AND within presale period: use promo_member_price
-- - If affiliate AND after presale: use member_price
-- - If non-affiliate AND within presale period: use promo_price  
-- - If non-affiliate AND after presale: use price
--
-- Courtesy ticket rules:
-- - Only owner/representative of active affiliates gets 1 free ticket
-- - Same company (by email or RFC) cannot get multiple courtesy tickets
-- - Guests (is_guest=1) CANNOT request additional tickets
-- - Guests are limited to 1 ticket only

-- =============================================
-- EMAIL TEMPLATE TYPES
-- =============================================
-- The system now sends different HTML email templates:
--
-- 1. PENDING PAYMENT (pending_payment)
--    - Sent when registration requires payment
--    - Shows event info, amount due, and PayPal button
--    - Yellow/orange warning styling
--
-- 2. CONFIRMATION (confirmation)  
--    - Basic confirmation for free events
--    - Indicates QR code will be sent separately
--
-- 3. ACCESS TICKET (access_ticket)
--    - Sent for FREE, COURTESY, or PAID (after payment) registrations
--    - Contains QR code embedded in email body
--    - Green header styling matching CCQ branding
--    - Includes attendee details and instructions

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Backup database before running this script.
-- 2. Run after deploying the new PHP code.
-- 3. Test event creation with all 4 price tiers.
-- 4. Test presale pricing by creating an event with promo_end_date in the future.
-- 5. Test guest registration - should be limited to 1 ticket.
-- 6. Test affiliate registration - courtesy ticket for owner/representative.
-- 7. Verify HTML emails are sent with proper formatting.
-- 8. Test pending payment email has correct PayPal link.
-- 9. Test access ticket email shows QR code.
