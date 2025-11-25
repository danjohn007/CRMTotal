-- CRM Total - Database Update Script
-- Version: 1.2.0
-- Date: 2025-11-25
-- Description: Adds password recovery support and adapts affiliates to CCQ CSV template

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- USER PASSWORD RECOVERY
-- =============================================

-- Add reset token columns to users table
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(64) NULL AFTER `last_login`,
ADD COLUMN IF NOT EXISTS `reset_token_expires` DATETIME NULL AFTER `reset_token`;

-- Add index for reset token lookups
CREATE INDEX IF NOT EXISTS `idx_reset_token` ON `users` (`reset_token`);

-- =============================================
-- AFFILIATIONS ENHANCEMENTS
-- =============================================

-- Add receipt number and sticker number to affiliations
ALTER TABLE `affiliations`
ADD COLUMN IF NOT EXISTS `receipt_number` VARCHAR(50) NULL AFTER `invoice_number`,
ADD COLUMN IF NOT EXISTS `sticker_number` VARCHAR(50) NULL AFTER `receipt_number`,
ADD COLUMN IF NOT EXISTS `affiliation_type` ENUM('MEMBRESIA', 'SIEM', 'OTRO') DEFAULT 'MEMBRESIA' AFTER `sticker_number`;

-- =============================================
-- CONTACTS ENHANCEMENTS
-- =============================================

-- Ensure all required columns exist in contacts table
-- These should already exist but verifying for completeness

-- The contacts table already has all required fields for the CCQ template:
-- business_name -> EMPRESA / RAZON SOCIAL
-- rfc -> RFC
-- corporate_email -> EMAIL
-- phone -> TELÉFONO
-- owner_name -> REPRESENTANTE
-- commercial_address -> DIRECCIÓN COMERCIAL
-- fiscal_address -> DIRECCIÓN FISCAL
-- industry -> SECTOR
-- commercial_name -> CATEGORÍA

-- =============================================
-- MEMBERSHIP TYPES SEED DATA VERIFICATION
-- =============================================

-- Ensure SIEM membership type exists for imports
INSERT INTO `membership_types` (`name`, `code`, `price`, `duration_days`, `benefits`, `is_active`)
SELECT 'SIEM', 'SIEM', 0.00, 360, '{"siem": true}', 1
WHERE NOT EXISTS (SELECT 1 FROM `membership_types` WHERE `code` = 'SIEM');

-- =============================================
-- INDEXES FOR PERFORMANCE
-- =============================================

-- Add index for business name searches in imports
CREATE INDEX IF NOT EXISTS `idx_business_name` ON `contacts` (`business_name`);

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
SELECT 1, 'schema_update', NULL, NULL, 
    '{"version": "1.2.0", "changes": ["password_recovery", "ccq_csv_template_support", "affiliations_enhancements"]}', 
    '127.0.0.1', NOW()
WHERE EXISTS (SELECT 1 FROM users WHERE id = 1);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================

-- Verify users table has reset token columns
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('reset_token', 'reset_token_expires');

-- Verify affiliations table has new columns
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'affiliations' AND COLUMN_NAME IN ('receipt_number', 'sticker_number', 'affiliation_type');

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Backup database before running this script
-- 2. Run after deploying the new PHP code
-- 3. Test password recovery with a test user
-- 4. Test CSV import with the CCQ template
-- 5. Verify all membership types are properly mapped
