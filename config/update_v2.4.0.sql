-- CRM Total - Database Update Script
-- Version: 2.4.0
-- Date: 2025-11-29
-- Description: Enhanced afiliador dashboard, company digital file dashboard,
--              improved sales tracking and statistics

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- NO SCHEMA CHANGES REQUIRED FOR THIS UPDATE
-- =============================================
-- This update includes only application-level changes:
-- 1. Enhanced afiliador (vendedor) dashboard with:
--    - Today's appointments with WhatsApp and contact links
--    - New assigned prospects (from events, chatbot, manual)
--    - Sales statistics (yesterday, current week, last week, last month, year)
--    - Charts for weekly sales visualization
--    - Upcoming agenda (next 7 days)
--    - Quick action buttons for WhatsApp, calls, and expediente access
-- 
-- 2. Company digital file (expediente digital único) dashboard with:
--    - Days remaining in affiliation (365 days - today)
--    - Membership status and benefits
--    - Event registrations and attendance history
--    - Annual payment totals
--    - Invoice history
--    - Collaborators list
--    - Products/services sold and bought

-- =============================================
-- VERIFY EXISTING STRUCTURES
-- =============================================
-- The following should already exist from previous updates:
-- - contacts.position (v2.3.0)
-- - contacts.contact_type includes 'colaborador_empresa' (v1.6.0)
-- - event_registrations.attendee_position (v1.6.0)
-- - event_registrations.attendee_email (v1.6.0)
-- - event_registrations.parent_registration_id (v2.1.0)

-- =============================================
-- AUDIT LOG FOR VERSION UPDATE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"2.4.0","changes":["Enhanced afiliador dashboard with sales charts and appointments","Company digital file dashboard with benefits and event history","New model methods for sales statistics by period","Improved upcoming agenda and prospect management"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- DEPLOYMENT NOTES
-- =============================================
-- After deploying this update:
-- 
-- 1. Enhanced Afiliador Dashboard:
--    Navigate to Dashboard > Afiliador and verify:
--    a. Today's appointments section with WhatsApp links
--    b. New assigned prospects (last 7 days) with action buttons
--    c. Sales summary cards (yesterday, week, month, year)
--    d. Weekly sales chart
--    e. Upcoming agenda for next 7 days
--    f. Goals progress bars
--    g. Quick action buttons for common tasks
-- 
-- 2. Company Digital File Dashboard:
--    Navigate to Afiliados > [Select] > Ver Expediente Digital and verify:
--    a. Days remaining counter (365 - days since affiliation)
--    b. Membership status and amount
--    c. Events attended count
--    d. Total paid in the year
--    e. Company information (RFC, owner, address, WhatsApp, website)
--    f. Products sold/bought lists
--    g. Membership benefits display
--    h. Event registration history table
--    i. Cross/upselling purchases
--    j. Action history with results and next actions
--    k. Invoice history in sidebar
--    l. Collaborators list
--    m. Quick action buttons (WhatsApp, Email, Call, Document Action)
-- 
-- 3. New Model Methods Added:
--    Affiliation Model:
--    - getSalesStatsByPeriod($userId, $period) - Get sales by period
--    - getWeeklySalesChart($userId) - Get daily sales for current week
--    - getMonthlySalesChart($userId) - Get monthly sales for current year
--    
--    Activity Model:
--    - getTodayAppointments($userId) - Get today's appointments
--    - getUpcoming($userId, $range) - Get upcoming activities
--    
--    Contact Model:
--    - getNewAssignedProspects($affiliatorId) - Get new prospects
--    - getProspectsByChannel($affiliatorId, $channel) - Filter by channel
--    - getCompanyDashboardData($contactId) - Get company dashboard data
-- 
-- 4. Files Modified:
--    - app/models/Affiliation.php
--    - app/models/Activity.php
--    - app/models/Contact.php
--    - app/controllers/DashboardController.php
--    - app/controllers/AffiliatesController.php
--    - app/views/dashboard/afiliador.php (replaced)
--    - app/views/affiliates/digital_file.php (created)
--    - config/update_v2.4.0.sql (this file)
-- 
-- 5. Test the Following Flows:
--    a. Log in as afiliador role and check dashboard
--    b. Navigate to an affiliate's digital file
--    c. Verify all action buttons work (WhatsApp, Email, Call)
--    d. Create a new activity from the quick actions
--    e. Check sales charts display correctly

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- Check version update in audit log:
-- SELECT * FROM audit_log WHERE action = 'schema_update' ORDER BY created_at DESC LIMIT 1;
-- 
-- Check affiliations for sales data:
-- SELECT affiliate_user_id, DATE(affiliation_date), COUNT(*), SUM(amount) 
-- FROM affiliations 
-- WHERE payment_status = 'paid' 
-- GROUP BY affiliate_user_id, DATE(affiliation_date) 
-- ORDER BY DATE(affiliation_date) DESC 
-- LIMIT 10;
-- 
-- Check event attendance:
-- SELECT c.business_name, COUNT(er.id) as registrations, SUM(er.attended) as attended
-- FROM contacts c
-- JOIN event_registrations er ON er.guest_email = c.corporate_email
-- GROUP BY c.id
-- ORDER BY registrations DESC
-- LIMIT 10;

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================
-- This update contains no schema changes.
-- To rollback, revert the following files to their previous versions:
-- - app/models/Affiliation.php
-- - app/models/Activity.php
-- - app/models/Contact.php
-- - app/controllers/DashboardController.php
-- - app/controllers/AffiliatesController.php
-- - app/views/dashboard/afiliador.php
-- - app/views/affiliates/digital_file.php
-- 
-- DELETE FROM audit_log WHERE new_values LIKE '%version":"2.4.0%';
