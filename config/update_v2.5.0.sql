-- CRM Total - Database Update Script
-- Version: 2.5.0
-- Date: 2025-11-29
-- Description: Enhanced Customer Journey with 6 defined stages
--              - Stage 1: Expediente Digital Único registration (basic info)
--              - Stage 2: Products/services registration
--              - Stage 3: Payment & benefits enablement
--              - Stage 4: Cross-selling opportunities
--              - Stage 5: Up-selling with invitation tracking
--              - Stage 6: Council eligibility

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- CUSTOMER JOURNEY STAGE TRACKING
-- =============================================

-- Add journey_stage column to contacts for tracking the 6 stages
ALTER TABLE `contacts` 
ADD COLUMN IF NOT EXISTS `journey_stage` TINYINT UNSIGNED DEFAULT 1 
COMMENT 'Customer Journey stage: 1-Registro, 2-Productos, 3-Facturación, 4-CrossSelling, 5-UpSelling, 6-Consejo'
AFTER `completion_stage`;

-- Add journey_stage_updated to track when stage changed
ALTER TABLE `contacts` 
ADD COLUMN IF NOT EXISTS `journey_stage_updated` TIMESTAMP NULL 
COMMENT 'Last journey stage update timestamp'
AFTER `journey_stage`;

-- =============================================
-- UP-SELLING INVITATION TRACKING
-- =============================================

-- Table to track upselling invitations (minimum 2 per year required)
CREATE TABLE IF NOT EXISTS `upselling_invitations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contact_id` INT UNSIGNED NOT NULL,
    `current_membership_id` INT UNSIGNED NOT NULL COMMENT 'Current membership at time of invitation',
    `target_membership_id` INT UNSIGNED NOT NULL COMMENT 'Proposed upgrade membership',
    `invitation_date` DATETIME NOT NULL COMMENT 'Date and time of invitation sent',
    `invitation_type` ENUM('email', 'whatsapp', 'phone', 'in_person', 'payment_link') DEFAULT 'payment_link',
    `payment_link_url` VARCHAR(500) COMMENT 'Online payment link sent',
    `response_status` ENUM('pending', 'accepted', 'declined', 'no_response') DEFAULT 'pending',
    `response_date` DATETIME NULL,
    `sent_by_user_id` INT UNSIGNED COMMENT 'User who sent the invitation',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`current_membership_id`) REFERENCES `membership_types`(`id`),
    FOREIGN KEY (`target_membership_id`) REFERENCES `membership_types`(`id`),
    FOREIGN KEY (`sent_by_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_contact_year` (`contact_id`, `invitation_date`),
    INDEX `idx_response_status` (`response_status`)
) ENGINE=InnoDB COMMENT='Tracks upselling invitations - minimum 2 per year per affiliate';

-- =============================================
-- FREE EVENT ATTENDANCE TRACKING
-- =============================================

-- Add event category for free events tracking
ALTER TABLE `events` 
MODIFY COLUMN `category` VARCHAR(100) 
COMMENT 'desayuno, open_day, conferencia, feria, exposicion, curso, taller, expo, networking, webinar';

-- Add free event types tracking to event_registrations
ALTER TABLE `event_registrations` 
ADD COLUMN IF NOT EXISTS `event_category` VARCHAR(50) NULL 
COMMENT 'Category for statistics: desayuno, open_day, conferencia, feria, exposicion'
AFTER `payment_status`;

-- =============================================
-- COUNCIL (CONSEJO) ELIGIBILITY TRACKING  
-- =============================================

-- Table to track council membership and eligibility
CREATE TABLE IF NOT EXISTS `council_members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contact_id` INT UNSIGNED NOT NULL,
    `member_type` ENUM('propietario', 'invitado') NOT NULL COMMENT 'Council member type',
    `start_date` DATE NOT NULL COMMENT 'Start date in council',
    `end_date` DATE NULL COMMENT 'End date (null if still active)',
    `position` VARCHAR(100) COMMENT 'Position in council',
    `status` ENUM('active', 'inactive', 'pending_approval') DEFAULT 'pending_approval',
    `approved_by` INT UNSIGNED COMMENT 'User who approved the membership',
    `approval_date` DATETIME NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_member_type` (`member_type`)
) ENGINE=InnoDB COMMENT='Council members - requires 2+ years of continuous affiliation';

-- =============================================
-- PAYMENT AND INVOICE TRACKING ENHANCEMENTS
-- =============================================

-- Add invoice attachment field to affiliations
ALTER TABLE `affiliations` 
ADD COLUMN IF NOT EXISTS `invoice_file` VARCHAR(255) NULL 
COMMENT 'Path to attached invoice file'
AFTER `invoice_status`;

-- Add benefits enablement date
ALTER TABLE `affiliations` 
ADD COLUMN IF NOT EXISTS `benefits_enabled_date` DATE NULL 
COMMENT 'Date when benefits were enabled'
AFTER `invoice_file`;

-- Add seller assignment tracking
ALTER TABLE `affiliations` 
ADD COLUMN IF NOT EXISTS `closed_by_user_id` INT UNSIGNED NULL 
COMMENT 'User who closed the sale (may differ from affiliate_user_id)'
AFTER `benefits_enabled_date`;

-- =============================================
-- SERVICE CONTRACT TRACKING FOR CROSS-SELLING
-- =============================================

-- Add category-specific fields to track all payments
ALTER TABLE `service_contracts` 
ADD COLUMN IF NOT EXISTS `service_type` ENUM('salon', 'marketing', 'curso', 'taller', 'expo', 'other') DEFAULT 'other'
COMMENT 'Quick service type classification'
AFTER `service_id`;

-- =============================================
-- MEMBERSHIP TYPE UPDATES FOR UP-SELLING HIERARCHY
-- =============================================

-- Add upselling order to membership types
ALTER TABLE `membership_types` 
ADD COLUMN IF NOT EXISTS `upsell_order` TINYINT UNSIGNED DEFAULT 0 
COMMENT 'Order in upselling hierarchy: 1=Pyme, 2=Visionario, 3=Premier, 4=Patrocinador'
AFTER `benefits`;

-- Update existing membership types with upsell order
UPDATE `membership_types` SET `upsell_order` = 0 WHERE `code` = 'BASICA';
UPDATE `membership_types` SET `upsell_order` = 1 WHERE `code` = 'PYME';
UPDATE `membership_types` SET `upsell_order` = 3 WHERE `code` = 'PREMIER';
UPDATE `membership_types` SET `upsell_order` = 4 WHERE `code` = 'PATROCINADOR';

-- Insert Visionario membership if not exists
INSERT INTO `membership_types` (`name`, `code`, `price`, `duration_days`, `benefits`, `upsell_order`, `is_active`) 
SELECT 'Membresía Visionario', 'VISIONARIO', 10000.00, 360, 
       '{"descuento_eventos": 25, "buscador": true, "networking": true, "capacitaciones": 5, "asesoria": true, "marketing_basico": true}', 
       2, 1
WHERE NOT EXISTS (SELECT 1 FROM `membership_types` WHERE `code` = 'VISIONARIO');

-- Ensure VISIONARIO has correct upsell_order even if it already existed
UPDATE `membership_types` SET `upsell_order` = 2 WHERE `code` = 'VISIONARIO';

-- =============================================
-- AUDIT LOG FOR VERSION UPDATE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    '{"version":"2.5.0","changes":["Enhanced Customer Journey with 6 stages","Upselling invitation tracking table","Council eligibility tracking","Invoice attachment support","Benefits enablement date tracking","Seller assignment tracking","Membership upsell hierarchy"]}',
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- DEPLOYMENT NOTES
-- =============================================
-- After deploying this update:
-- 
-- 1. Customer Journey now has 6 defined stages:
--    Stage 1: Expediente Digital Único - Basic registration
--             (RFC, owner/rep legal, razón social, nombre comercial, dirección, WhatsApp)
--    Stage 2: Products/Services - What the merchant sells and buys
--    Stage 3: Payment - Invoice attachment, expiration date, benefits enablement, 
--             seller assignment, free event attendance tracking
--    Stage 4: Cross-selling - Salon rental, marketing services, paid events
--    Stage 5: Up-selling - Membership upgrade invitations (2x per year minimum)
--             Pyme → Visionario → Premier → Patrocinador
--    Stage 6: Council - Eligibility after 2+ years continuous affiliation
-- 
-- 2. New Tables:
--    - upselling_invitations: Tracks all upgrade invitations (date, time, payment link)
--    - council_members: Tracks council membership with eligibility requirements
-- 
-- 3. Enhanced Fields:
--    - contacts.journey_stage: Current customer journey stage (1-6)
--    - affiliations.invoice_file: Path to attached invoice
--    - affiliations.benefits_enabled_date: When benefits were activated
--    - affiliations.closed_by_user_id: Sales closer assignment
--    - membership_types.upsell_order: Hierarchy for upgrade recommendations
-- 
-- 4. Membership Hierarchy (upsell_order):
--    0 = Básica (no upselling)
--    1 = Pyme
--    2 = Visionario (NEW)
--    3 = Premier
--    4 = Patrocinador
-- 
-- 5. Council Eligibility Requirements:
--    - Minimum 2 years of continuous affiliation
--    - No gaps in membership
--    - Can be propietario (voting) or invitado (non-voting)

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- Check journey stages distribution:
-- SELECT journey_stage, COUNT(*) FROM contacts GROUP BY journey_stage;
-- 
-- Check upselling invitations per contact this year:
-- SELECT contact_id, COUNT(*) as invitations_this_year
-- FROM upselling_invitations
-- WHERE YEAR(invitation_date) = YEAR(CURDATE())
-- GROUP BY contact_id;
-- 
-- Check council eligible affiliates (2+ years continuous):
-- SELECT c.id, c.business_name, MIN(a.affiliation_date) as first_affiliation,
--        DATEDIFF(CURDATE(), MIN(a.affiliation_date)) / 365 as years_affiliated
-- FROM contacts c
-- JOIN affiliations a ON c.id = a.contact_id
-- WHERE c.contact_type = 'afiliado'
-- GROUP BY c.id
-- HAVING years_affiliated >= 2;
-- 
-- Check membership upsell hierarchy:
-- SELECT code, name, price, upsell_order FROM membership_types ORDER BY upsell_order;

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================
-- ALTER TABLE contacts DROP COLUMN IF EXISTS journey_stage;
-- ALTER TABLE contacts DROP COLUMN IF EXISTS journey_stage_updated;
-- DROP TABLE IF EXISTS upselling_invitations;
-- DROP TABLE IF EXISTS council_members;
-- ALTER TABLE affiliations DROP COLUMN IF EXISTS invoice_file;
-- ALTER TABLE affiliations DROP COLUMN IF EXISTS benefits_enabled_date;
-- ALTER TABLE affiliations DROP COLUMN IF EXISTS closed_by_user_id;
-- ALTER TABLE service_contracts DROP COLUMN IF EXISTS service_type;
-- ALTER TABLE membership_types DROP COLUMN IF EXISTS upsell_order;
-- DELETE FROM membership_types WHERE code = 'VISIONARIO';
-- DELETE FROM audit_log WHERE new_values LIKE '%version":"2.5.0%';
