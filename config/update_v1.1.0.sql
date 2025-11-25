-- CRM Total - Database Update Script
-- Version: 1.1.0
-- Date: 2025-11-25
-- Description: Adds new modules (Memberships, Financial, Import, Audit, Commercial Requirements)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- UPDATE EXISTING TABLES
-- =============================================

-- Add read_at to notifications table (already exists but ensure it's there)
ALTER TABLE `notifications` 
ADD COLUMN IF NOT EXISTS `read_at` TIMESTAMP NULL AFTER `is_read`;

-- =============================================
-- COMMERCIAL REQUIREMENTS TABLE (NEW)
-- =============================================

CREATE TABLE IF NOT EXISTS `commercial_requirements` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `contact_id` INT UNSIGNED,
    `user_id` INT UNSIGNED NOT NULL COMMENT 'Usuario asignado',
    `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium',
    `status` ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    `due_date` DATE,
    `budget` DECIMAL(12,2) DEFAULT 0,
    `category` VARCHAR(100),
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`),
    INDEX `idx_priority` (`priority`),
    INDEX `idx_due_date` (`due_date`),
    INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB;

-- =============================================
-- UPDATE AUDIT_LOG TABLE (Add user_agent if not exists)
-- =============================================

ALTER TABLE `audit_log` 
ADD COLUMN IF NOT EXISTS `user_agent` TEXT AFTER `ip_address`;

-- =============================================
-- FILLABLE FIELDS FOR NOTIFICATIONS (Add read_at to fillable)
-- =============================================

-- This is handled in code, no SQL needed

-- =============================================
-- ADD AVATAR COLUMN TO USERS IF NOT EXISTS
-- =============================================

-- Already exists in the schema, just ensure it's there
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `avatar` VARCHAR(255) AFTER `whatsapp`;

-- =============================================
-- INSERT SAMPLE COMMERCIAL REQUIREMENTS (Optional - only if users exist)
-- =============================================

-- Only insert sample data if the required users and contacts exist
-- This prevents foreign key constraint violations
INSERT INTO `commercial_requirements` (`title`, `description`, `contact_id`, `user_id`, `priority`, `status`, `due_date`, `budget`, `category`)
SELECT 'Renovación membresía PYME - Comercializadora del Centro', 'Seguimiento a renovación de membresía próxima a vencer', 
       c.id, u.id, 'high', 'pending', DATE_ADD(CURDATE(), INTERVAL 15 DAY), 5000.00, 'renovacion'
FROM users u, contacts c WHERE u.id = 2 AND c.id = 1 LIMIT 1;

INSERT INTO `commercial_requirements` (`title`, `description`, `contact_id`, `user_id`, `priority`, `status`, `due_date`, `budget`, `category`)
SELECT 'Servicio de gestoría - Nuevo prospecto', 'Prospecto interesado en servicio de gestoría', 
       NULL, u.id, 'low', 'pending', DATE_ADD(CURDATE(), INTERVAL 45 DAY), 4000.00, 'servicio'
FROM users u WHERE u.id = 2 LIMIT 1;

-- =============================================
-- ADD AUDIT LOG ENTRIES FOR NEW ACTIONS
-- =============================================

-- Insert initial audit log entry for system update (only if admin user exists)
INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
SELECT u.id, 'system_update', NULL, NULL, '{"version": "1.1.0", "modules_added": ["memberships", "financial", "import", "audit", "requirements"]}', '127.0.0.1', NOW()
FROM users u WHERE u.id = 1 LIMIT 1;

-- =============================================
-- ADD NEW CONFIG ENTRIES
-- =============================================

INSERT INTO `config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('whatsapp_api_key', '', 'text', 'API Key para WhatsApp Business'),
('google_maps_api_key', '', 'text', 'API Key para Google Maps')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES (Optional - for testing)
-- =============================================

-- Check if commercial_requirements table exists
-- SELECT COUNT(*) as table_exists FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'commercial_requirements';

-- Check role table contents
-- SELECT * FROM roles;

-- Check membership types
-- SELECT * FROM membership_types;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Run this script after backing up the database
-- 2. Verify all foreign key relationships are valid
-- 3. Update application files to the new version
-- 4. Clear any application cache if applicable
-- 5. Test all new modules functionality
