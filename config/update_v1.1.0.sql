-- CRM Total - Database Update Script
-- Version: 1.1.0
-- Date: 2025-11-25
-- Adaptado: Compatible MySQL (sin errores por columnas duplicadas)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- UPDATE EXISTING TABLES
-- =============================================

-- Si la columna ya existe en la tabla, deja comentada la línea.
-- Si NO existe, descomenta y ejecuta.

-- Add read_at to notifications table (solo si no existe)
-- ALTER TABLE `notifications` 
-- ADD COLUMN `read_at` TIMESTAMP NULL AFTER `is_read`;

-- Add user_agent to audit_log table (solo si no existe)
-- ALTER TABLE `audit_log` 
-- ADD COLUMN `user_agent` TEXT AFTER `ip_address`;

-- Add avatar to users table (solo si no existe)
-- ALTER TABLE `users` 
-- ADD COLUMN `avatar` VARCHAR(255) AFTER `whatsapp`;

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
-- FILLABLE FIELDS FOR NOTIFICATIONS (no SQL needed)
-- =============================================

-- =============================================
-- INSERT SAMPLE COMMERCIAL REQUIREMENTS (optional)
-- =============================================
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
-- VERIFICATION QUERIES (optional for testing)
-- =============================================

-- SELECT COUNT(*) as table_exists FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'commercial_requirements';
-- SELECT * FROM roles;
-- SELECT * FROM membership_types;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Ejecuta este script después de respaldar la base de datos
-- 2. Verifica relaciones de llaves foráneas
-- 3. Actualiza los archivos de aplicación
-- 4. Borra el caché de la aplicación si aplica
-- 5. Prueba toda la funcionalidad nueva de los módulos
