-- CRM Total - Database Update Script
-- Version: 2.0.0
-- Date: 2025-11-28
-- Description: Event registration improvements, attendance control updates, payment flow enhancements

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- NO SCHEMA CHANGES REQUIRED
-- =============================================
-- This update includes only application-level changes:
-- 1. Moved "Asisto como Invitado" checkbox below tickets field
-- 2. Added "Dueño o Representante Legal" field to public registration form
-- 3. Added print functionality to QR success page and email templates
-- 4. Improved attendance control to show owner_name and legal_representative
-- 5. Fixed QR scanner camera functionality
-- 6. Added public payment and ticket pages
-- 7. Fixed payment link in notification emails
-- 8. Added .htaccess for public QR directory access

-- =============================================
-- ENSURE REQUIRED CONFIG VALUES EXIST
-- =============================================

-- Contact phone for ticket display
INSERT IGNORE INTO `config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('contact_phone', '4425375301', 'text', 'Teléfono de contacto para boletos de eventos');

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"2.0.0","changes":["Moved guest checkbox below tickets","Added owner_name field to registration","Added print functionality to emails and QR display","Improved attendance control to show owner_name from contacts","Fixed QR scanner camera initialization","Added public payment and printable ticket pages","Fixed payment links in notification emails","Added .htaccess for QR directory public access"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- SELECT * FROM config WHERE config_key = 'contact_phone';
-- SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 5;

-- =============================================
-- DEPLOYMENT NOTES
-- =============================================
-- After deploying this update:
-- 
-- 1. Ensure the /public/uploads/qr/ directory exists with 755 permissions
--    - The .htaccess file in this directory allows public access to QR images
-- 
-- 2. Test the following flows:
--    a. Public event registration (free event)
--       - Check "Dueño o Representante Legal" field appears
--       - Check "Asisto como Invitado" is below tickets
--       - Verify print button on QR success page
--    
--    b. Public event registration (paid event)
--       - Complete a test registration
--       - Verify payment link in email works
--       - Complete payment and verify QR is sent
--    
--    c. Attendance Control
--       - Navigate to event attendance control
--       - Check that owner_name and legal_representative are displayed
--       - Test QR scanner camera functionality
--       - Test manual code entry
--    
--    d. Printable ticket page
--       - Access /evento/boleto/{code} with a valid code
--       - Verify print button works
--       - Verify ticket displays correctly
-- 
-- 3. Email templates now include working "Imprimir Boleto" links
--    that redirect to the printable ticket page

-- =============================================
-- ROUTES ADDED
-- =============================================
-- GET /evento/pago/{code}    - Public payment page for pending registrations
-- GET /evento/boleto/{code}  - Public printable ticket page

-- =============================================
-- FILE CHANGES
-- =============================================
-- Modified:
--   - app/views/events/registration.php
--   - app/views/events/attendance.php
--   - app/controllers/EventsController.php
--   - app/controllers/ApiController.php
--   - app/models/Event.php
--   - public/index.php (routes)
-- 
-- Created:
--   - app/views/events/payment.php
--   - app/views/events/printable_ticket.php
--   - public/uploads/qr/.htaccess
--   - public/uploads/qr/.gitkeep
--   - config/update_v2.0.0.sql
