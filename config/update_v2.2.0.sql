-- CRM Total - Database Update Script
-- Version: 2.2.0
-- Date: 2025-11-28
-- Description: Attendance control modal improvements, email styling with logo,
--              contact_type display fix, button styling improvements

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- NO SCHEMA CHANGES REQUIRED FOR THIS UPDATE
-- =============================================
-- This update includes only application-level changes:
-- 1. Added modal dialogs for 'Escanear QR' and 'Ingresar Manual' buttons
--    in the attendance control module
-- 2. Fixed contact_type display in attendance list to show actual
--    contact_type from DB instead of always showing "Dueño/Representante"
-- 3. Added system logo to all notification email templates
-- 4. Fixed button styles in email templates using proper inline CSS
--    for better email client compatibility
-- 5. The "Cargo" field already exists (attendee_position) from v1.6.0

-- =============================================
-- VERIFY EXISTING COLUMNS
-- =============================================
-- The following columns should already exist from previous updates:
-- - event_registrations.attendee_position (added in v1.6.0)
-- - event_registrations.parent_registration_id (added in v2.1.0)
-- - contacts.contact_type enum includes 'colaborador_empresa' (added in v1.6.0)

-- =============================================
-- AUDIT LOG FOR VERSION UPDATE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"2.2.0","changes":["Added modal dialogs for QR Scanner and Manual Entry buttons","Fixed contact_type display in attendance list","Added system logo to email templates","Fixed button styles in emails for better compatibility","Escape key closes modals"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- DEPLOYMENT NOTES
-- =============================================
-- After deploying this update:
-- 
-- 1. Verify the following improvements:
--    a. Navigate to any event's attendance control page
--    b. Click "Escanear QR" - should open a modal dialog
--    c. Click "Ingresar Manual" - should open a modal dialog
--    d. Press Escape key - should close any open modal
--    e. Check that attendance list shows correct contact_type badges
--    f. Verify additional attendees show "Asistente Adicional" type
--    g. Verify guests show their guest_type (INVITADO, FUNCIONARIO PÚBLICO, etc.)
-- 
-- 2. Email Template Improvements:
--    a. All emails now include the system logo (if configured)
--    b. Configure the logo in Configuración > Sistema > site_logo
--    c. Buttons use proper inline CSS for email client compatibility
--    d. Colors use configurable primary_color, secondary_color, accent_color
-- 
-- 3. Attendance Control Contact Types:
--    The following contact types are now displayed:
--    - Afiliado (green)
--    - Exafiliado (orange)
--    - Prospecto (blue)
--    - Nuevo Usuario (gray)
--    - Funcionario (indigo)
--    - Público General (gray)
--    - Colaborador (cyan)
--    - Invitado (purple)
--    - Consejero (yellow)
--    - Mesa Directiva (red)
--    - Dueño/Representante (green)
--    - Asistente Adicional (cyan)
-- 
-- 4. Files Modified:
--    - app/views/events/attendance.php
--    - app/controllers/EventsController.php
--    - app/controllers/ApiController.php
--    - config/update_v2.2.0.sql

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- SELECT * FROM audit_log WHERE action = 'schema_update' ORDER BY created_at DESC LIMIT 1;
-- 
-- Check contact types in existing registrations:
-- SELECT er.id, er.guest_name, er.is_guest, er.guest_type, 
--        er.is_owner_representative, er.parent_registration_id, c.contact_type
-- FROM event_registrations er
-- LEFT JOIN contacts c ON er.contact_id = c.id
-- LIMIT 10;

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================
-- This update contains no schema changes.
-- To rollback, revert the following files to their previous versions:
-- - app/views/events/attendance.php
-- - app/controllers/EventsController.php
-- - app/controllers/ApiController.php
-- 
-- DELETE FROM audit_log WHERE new_values LIKE '%version":"2.2.0%';
