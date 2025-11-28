-- CRM Total - Database Update Script
-- Version: 2.1.0
-- Date: 2025-11-28
-- Description: Event registration improvements - RFC mandatory, guest types, 
--              individual QR codes for additional attendees, attendance control fixes

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD GUEST TYPE COLUMN TO EVENT REGISTRATIONS
-- =============================================
-- This column stores the type of guest when registering as "Invitado"
-- Values: INVITADO, FUNCIONARIO PÚBLICO, OTRO

ALTER TABLE `event_registrations` 
ADD COLUMN `guest_type` VARCHAR(50) NULL DEFAULT NULL 
COMMENT 'Type of guest: INVITADO, FUNCIONARIO PÚBLICO, OTRO' 
AFTER `is_guest`;

-- =============================================
-- ADD PARENT REGISTRATION ID FOR ADDITIONAL ATTENDEES
-- =============================================
-- This column links additional attendee registrations to their parent registration
-- Allows for individual QR codes and attendance tracking for each additional attendee

ALTER TABLE `event_registrations` 
ADD COLUMN `parent_registration_id` INT UNSIGNED NULL DEFAULT NULL 
COMMENT 'Link to parent registration for additional attendees' 
AFTER `guest_type`;

-- Add foreign key for parent registration (self-referencing)
-- Note: Check if constraint already exists before adding
SET @constraint_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'event_registrations' 
    AND CONSTRAINT_NAME = 'fk_parent_registration'
);

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `event_registrations` ADD CONSTRAINT `fk_parent_registration` FOREIGN KEY (`parent_registration_id`) REFERENCES `event_registrations`(`id`) ON DELETE CASCADE',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for parent registration lookup
-- Note: Removed IF NOT EXISTS for better compatibility
CREATE INDEX `idx_parent_registration` ON `event_registrations` (`parent_registration_id`);

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    'event_registrations',
    NULL,
    '{"version":"2.1.0","changes":["Added guest_type column for guest type selection (INVITADO, FUNCIONARIO PÚBLICO, OTRO)","Added parent_registration_id column for additional attendee individual reg"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- Run these queries to verify the update was applied correctly:
-- 
-- SHOW COLUMNS FROM event_registrations LIKE 'guest_type';
-- SHOW COLUMNS FROM event_registrations LIKE 'parent_registration_id';
-- SELECT * FROM audit_log WHERE action = 'schema_update' ORDER BY created_at DESC LIMIT 1;

-- =============================================
-- DEPLOYMENT NOTES
-- =============================================
-- After deploying this update:
-- 
-- 1. Application Changes Applied:
--    a. RFC field is now mandatory in public event registration unless "Asisto como Invitado" is checked
--    b. When "Asisto como Invitado" is checked, a mandatory dropdown appears with options:
--       - INVITADO
--       - FUNCIONARIO PÚBLICO
--       - OTRO
--    c. Changed "Dueño o Representante Legal" checkbox label to "¿Dueño, Socio o Representante Legal?"
--    d. Success messages now include the email address where confirmation was sent
--    e. Footer text changed from "Estrategia Digital desarrollada por ID" to 
--       "Solución Digital desarrollada por ID" in:
--       - All email templates
--       - System footer in admin panel
--       - Login page footer
--    f. Additional attendees now get individual registrations with their own QR codes
--    g. Additional attendees appear as individual rows in attendance control
--    h. QR Scanner and Manual Entry buttons fixed in attendance control
--    i. Non-affiliated companies with RFC now receive QR email after payment
-- 
-- 2. Test the following flows:
--    a. Public event registration (free event) - non-guest with RFC
--       - Verify RFC is required
--       - Complete registration and check QR is sent to email
--    
--    b. Public event registration - guest mode
--       - Check "Asisto como Invitado"
--       - Verify guest type dropdown appears and is required
--       - Verify RFC field is hidden
--    
--    c. Registration with additional attendees
--       - Select 3+ tickets
--       - Fill additional attendee information
--       - Verify each attendee receives their own QR email
--       - Check attendance control shows individual rows for each attendee
--    
--    d. Paid event registration
--       - Complete payment
--       - Verify QR email is sent (including for non-affiliated companies with RFC)
--       - Verify success message shows email address
--    
--    e. Attendance Control
--       - Navigate to event attendance control
--       - Test "Escanear QR" button - camera should activate
--       - Test "Ingresar Manual" button - input field should appear
--       - Verify additional attendees appear as individual rows
-- 
-- 3. Footer Text Updated in:
--    - app/controllers/EventsController.php (3 email templates)
--    - app/controllers/ApiController.php (1 email template)
--    - app/views/layouts/main.php (admin footer)
--    - app/views/auth/login.php (login page footer)

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================
-- To rollback this update, run:
-- 
-- ALTER TABLE `event_registrations` DROP FOREIGN KEY IF EXISTS `fk_parent_registration`;
-- ALTER TABLE `event_registrations` DROP COLUMN `guest_type`;
-- ALTER TABLE `event_registrations` DROP COLUMN `parent_registration_id`;
-- DELETE FROM `audit_log` WHERE new_values LIKE '%version":"2.1.0%';
