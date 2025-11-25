-- CRM Total - Database Update Script
-- Version: 1.4.0
-- Date: 2025-11-25
-- Description: Adds financial categories, transactions, and requirement categories tables
--              Also fixes search functionality and form validations

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- FINANCIAL CATEGORIES TABLE
-- =============================================

-- Categories for financial transactions (income/expense types)
CREATE TABLE IF NOT EXISTS `financial_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('ingreso', 'egreso') NOT NULL,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_type` (`type`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB;

-- Insert default financial categories
INSERT INTO `financial_categories` (`name`, `type`, `description`) VALUES
('Membresías', 'ingreso', 'Pagos por membresías y afiliaciones'),
('Servicios', 'ingreso', 'Ingresos por servicios adicionales'),
('Eventos', 'ingreso', 'Ingresos por eventos y capacitaciones'),
('Patrocinios', 'ingreso', 'Ingresos por patrocinios'),
('Renta de Salones', 'ingreso', 'Ingresos por renta de espacios'),
('Otros Ingresos', 'ingreso', 'Otros ingresos no categorizados'),
('Nómina', 'egreso', 'Gastos de nómina y salarios'),
('Servicios Básicos', 'egreso', 'Luz, agua, teléfono, internet'),
('Materiales', 'egreso', 'Materiales de oficina y consumibles'),
('Mantenimiento', 'egreso', 'Gastos de mantenimiento'),
('Marketing', 'egreso', 'Gastos de publicidad y marketing'),
('Otros Egresos', 'egreso', 'Otros gastos no categorizados')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- =============================================
-- FINANCIAL TRANSACTIONS TABLE
-- =============================================

-- Transactions for tracking income/expense movements
CREATE TABLE IF NOT EXISTS `financial_transactions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `transaction_date` DATE NOT NULL,
    `reference` VARCHAR(100) COMMENT 'Invoice number, receipt, etc.',
    `notes` TEXT,
    `created_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `financial_categories`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_transaction_date` (`transaction_date`),
    INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB;

-- =============================================
-- REQUIREMENT CATEGORIES TABLE
-- =============================================

-- Categories for commercial requirements
CREATE TABLE IF NOT EXISTS `requirement_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_code` (`code`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB;

-- Insert default requirement categories
INSERT INTO `requirement_categories` (`name`, `code`, `description`) VALUES
('Nueva Membresía', 'membresia', 'Solicitudes para nuevas membresías'),
('Renovación', 'renovacion', 'Renovaciones de membresías existentes'),
('Servicio Adicional', 'servicio', 'Solicitudes de servicios adicionales'),
('Evento', 'evento', 'Requerimientos relacionados con eventos'),
('Capacitación', 'capacitacion', 'Solicitudes de cursos y capacitaciones'),
('Marketing', 'marketing', 'Requerimientos de marketing y publicidad'),
('Gestoría', 'gestoria', 'Servicios de gestoría y trámites'),
('Otro', 'otro', 'Otros requerimientos')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- =============================================
-- INDEXES FOR IMPROVED SEARCH PERFORMANCE
-- =============================================

-- Add indexes for search functionality on contacts table
CREATE INDEX IF NOT EXISTS `idx_contacts_whatsapp` ON `contacts` (`whatsapp`);
CREATE INDEX IF NOT EXISTS `idx_contacts_corporate_email` ON `contacts` (`corporate_email`);
CREATE INDEX IF NOT EXISTS `idx_contacts_phone` ON `contacts` (`phone`);

-- =============================================
-- AUDIT LOG FOR SCHEMA CHANGE
-- =============================================

-- Insert audit log entry for schema update (uses first available user or NULL)
INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
SELECT 
    COALESCE((SELECT id FROM users ORDER BY id LIMIT 1), NULL),
    'schema_update', 
    NULL, 
    NULL, 
    '{"version": "1.4.0", "changes": ["financial_categories", "financial_transactions", "requirement_categories", "search_indexes"]}', 
    '127.0.0.1', 
    NOW();

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- VERIFICATION QUERIES
-- =============================================

-- Verify financial_categories table exists
-- SHOW TABLES LIKE 'financial_categories';
-- SELECT COUNT(*) FROM financial_categories;

-- Verify financial_transactions table exists
-- SHOW TABLES LIKE 'financial_transactions';

-- Verify requirement_categories table exists
-- SHOW TABLES LIKE 'requirement_categories';
-- SELECT COUNT(*) FROM requirement_categories;

-- Verify indexes on contacts table
-- SHOW INDEX FROM contacts WHERE Key_name LIKE 'idx_contacts_%';

-- =============================================
-- SAMPLE TRANSACTIONS (optional - for testing)
-- =============================================

-- Uncomment to insert sample transactions for testing
-- INSERT INTO `financial_transactions` (`category_id`, `description`, `amount`, `transaction_date`, `reference`, `created_by`)
-- SELECT 1, 'Pago membresía ejemplo', 5000.00, CURDATE(), 'FAC-001', 1
-- WHERE EXISTS (SELECT 1 FROM financial_categories WHERE id = 1);

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================

-- DROP TABLE IF EXISTS `financial_transactions`;
-- DROP TABLE IF EXISTS `financial_categories`;
-- DROP TABLE IF EXISTS `requirement_categories`;
-- DROP INDEX `idx_contacts_whatsapp` ON `contacts`;
-- DROP INDEX `idx_contacts_corporate_email` ON `contacts`;
-- DROP INDEX `idx_contacts_phone` ON `contacts`;

-- =============================================
-- NOTES FOR DEPLOYMENT
-- =============================================
-- 1. Backup database before running this script
-- 2. Run after deploying the new PHP code
-- 3. Test financial module: categories, transactions, and reports
-- 4. Test requirements module: category management
-- 5. Test affiliates search with new indexes
-- 6. Verify form validations for WhatsApp and phone fields
