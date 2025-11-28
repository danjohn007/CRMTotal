-- CRM Total - Database Update Script
-- Version: 2.1.0
-- Date: 2025-11-28
-- Description: Add guest registration type and parent registration support

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD GUEST_TYPE COLUMN
-- =============================================
-- Add column to identify the type of guest registration

ALTER TABLE `event_registrations`
ADD COLUMN `guest_type` VARCHAR(50) NULL COMMENT 'Type of guest registration' AFTER `is_guest`;

-- =============================================
-- ADD PARENT_REGISTRATION_ID COLUMN
-- =============================================
-- Add column to link guest registrations to their parent registration

ALTER TABLE `event_registrations`
ADD COLUMN `parent_registration_id` INT UNSIGNED NULL COMMENT 'Parent registration ID for guest registrations' AFTER `guest_type`;

-- =============================================
-- CREATE INDEX FOR PARENT_REGISTRATION
-- =============================================
-- Add index for faster lookup of guest registrations by parent

CREATE INDEX `idx_parent_registration` ON `event_registrations`(`parent_registration_id`);

-- =============================================
-- ADD FOREIGN KEY CONSTRAINT
-- =============================================
-- Add foreign key to maintain referential integrity for guest registrations

ALTER TABLE `event_registrations`
ADD CONSTRAINT `fk_parent_registration`
FOREIGN KEY (`parent_registration_id`) REFERENCES `event_registrations`(`id`) ON DELETE SET NULL;

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"2.1.0","changes":["Added guest_type column to event_registrations","Added parent_registration_id column to event_registrations","Added idx_parent_registration index","Added fk_parent_registration foreign key constraint"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- DESCRIBE event_registrations;
-- SHOW INDEX FROM event_registrations;
-- SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 5;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. This update adds support for guest registration types.
-- 2. The guest_type column allows categorizing different types of guest registrations.
-- 3. The parent_registration_id column links guest registrations to their primary registration.
-- 4. The idx_parent_registration index improves query performance for guest lookups.
-- 5. The fk_parent_registration foreign key ensures referential integrity.
