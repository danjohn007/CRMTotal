-- CRM Total - Database Update Script
-- Version: 2.3.0
-- Date: 2025-11-29
-- Description: Add position field to contacts table, fix email delivery to attendees,
--              improve button styling, fix additional attendee type classification

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD POSITION FIELD TO CONTACTS TABLE
-- =============================================
-- This field stores the job position/cargo of collaborators (colaborador_empresa)
-- when they are registered as event attendees

SET @column_exists = (SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'contacts' 
    AND COLUMN_NAME = 'position');

SET @sql = IF(@column_exists = 0,
    'ALTER TABLE `contacts` ADD COLUMN `position` VARCHAR(100) NULL COMMENT "Position/job title for company collaborators" AFTER `phone`;',
    'SELECT ''Column position already exists in contacts table, skipping...'' AS message;'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- VERIFY EXISTING CONTACT_TYPE ENUM
-- =============================================
-- Ensure 'colaborador_empresa' is a valid contact_type value
-- This should already exist from v1.6.0, but verify it

-- Check current enum values for contact_type
-- SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'contact_type';

-- =============================================
-- AUDIT LOG FOR VERSION UPDATE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"2.3.0","changes":["Added position field to contacts table","Email now sent to both company email and attendee email","Blue buttons now use configurable primary color","Additional attendees properly classified as colaborador","Event attendees registered as colaborador_empresa contacts"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- DEPLOYMENT NOTES
-- =============================================
-- After deploying this update:
-- 
-- 1. Database Changes:
--    a. New field: contacts.position (VARCHAR 100) - stores job title/cargo
--    b. Used when creating contact records for event attendees
-- 
-- 2. Email Improvements:
--    a. Digital tickets with QR codes now sent to BOTH:
--       - Company email (guest_email)
--       - Attendee email (attendee_email) when different
--    b. Ensures the actual event attendee receives their ticket
-- 
-- 3. Button Styling:
--    a. All blue buttons (bg-blue-600) now use the configured primary color
--    b. Hover states use the configured secondary color
--    c. Changes apply to the entire authenticated admin area
-- 
-- 4. Attendance Control:
--    a. Additional attendees (child registrations) now properly show as "Colaborador"
--    b. Priority order: Child registration > Guest > Attendee not owner > Contact type > Owner
-- 
-- 5. Contact Creation:
--    a. Event attendees (when not owner/representative) are saved as contacts
--    b. Contact type: 'colaborador_empresa'
--    c. Position field populated from attendee_position
-- 
-- 6. Files Modified:
--    - app/controllers/EventsController.php
--    - app/controllers/ApiController.php
--    - app/views/events/attendance.php
--    - app/views/layouts/main.php
--    - app/models/Contact.php
--    - config/update_v2.3.0.sql

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- Check if position column was added:
-- SHOW COLUMNS FROM contacts LIKE 'position';
-- 
-- Check version update in audit log:
-- SELECT * FROM audit_log WHERE action = 'schema_update' ORDER BY created_at DESC LIMIT 1;
-- 
-- Check contacts with position:
-- SELECT id, owner_name, corporate_email, contact_type, position 
-- FROM contacts 
-- WHERE position IS NOT NULL 
-- LIMIT 10;

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================
-- ALTER TABLE `contacts` DROP COLUMN `position`;
-- DELETE FROM audit_log WHERE new_values LIKE '%version":"2.3.0%';
