-- CRM Total - Database Update Script
-- Version: 1.3.0
-- Date: 2025-11-25
-- Description: Adds event catalogs, user address field, event registrations enhancements, tickets column

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- USER TABLE ENHANCEMENTS
-- =============================================

-- Add address column to users table (optional field)
ALTER TABLE `users`
ADD COLUMN `address` TEXT NULL AFTER `whatsapp`;

-- =============================================
-- EVENT CATALOGS
-- =============================================

-- Event Type Catalog
CREATE TABLE IF NOT EXISTS `event_type_catalog` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Event Category Catalog
CREATE TABLE IF NOT EXISTS `event_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default event types
INSERT INTO `event_type_catalog` (`code`, `name`, `description`) VALUES
('interno', 'Evento Interno CCQ', 'Eventos organizados internamente por la Cámara de Comercio'),
('externo', 'Evento Externo', 'Eventos externos donde participa la Cámara'),
('terceros', 'Evento de Terceros', 'Eventos organizados por terceros')
ON DUPLICATE KEY UPDATE `name` = `name`;

-- Insert default event categories
INSERT INTO `event_categories` (`name`, `description`) VALUES
('Networking', 'Eventos de networking y contactos empresariales'),
('Capacitación', 'Cursos y talleres de formación'),
('Conferencia', 'Conferencias y charlas magistrales'),
('Webinar', 'Seminarios y eventos en línea'),
('Foro', 'Foros de discusión y debate'),
('Exposición', 'Ferias y exposiciones comerciales'),
('Asamblea', 'Asambleas y reuniones institucionales'),
('Social', 'Eventos sociales y celebraciones')
ON DUPLICATE KEY UPDATE `description` = `description`;

-- =============================================
-- EVENT REGISTRATIONS ENHANCEMENTS
-- =============================================

-- Add tickets column to event_registrations
ALTER TABLE `event_registrations`
ADD COLUMN `tickets` INT UNSIGNED DEFAULT 1 AFTER `guest_rfc`;

-- =============================================
-- UPLOAD DIRECTORIES
-- =============================================

-- Note: Create the uploads/events directory manually or via PHP
-- mkdir -p public/uploads/events
-- chmod 755 public/uploads/events

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
SELECT 1, 'schema_update', NULL, NULL, 
    '{"version": "1.3.0", "changes": ["user_address_field", "event_catalogs", "event_registrations_tickets"]}', 
    '127.0.0.1', NOW()
WHERE EXISTS (SELECT 1 FROM users WHERE id = 1);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================

-- Verify users table has address column
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'address';

-- Verify event catalogs exist
-- SHOW TABLES LIKE 'event_type_catalog';
-- SHOW TABLES LIKE 'event_categories';

-- Verify event_registrations has tickets column
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'event_registrations' AND COLUMN_NAME = 'tickets';

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================

-- ALTER TABLE `users` DROP COLUMN `address`;
-- DROP TABLE IF EXISTS `event_type_catalog`;
-- DROP TABLE IF EXISTS `event_categories`;
-- ALTER TABLE `event_registrations` DROP COLUMN `tickets`;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Backup database before running this script
-- 2. Run after deploying the new PHP code
-- 3. Create uploads/events directory with proper permissions
-- 4. Test event creation with image upload
-- 5. Test user creation with welcome email
-- 6. Test import functionality after preview
