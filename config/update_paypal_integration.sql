-- Update for PayPal Integration
-- IMPORTANTE: Este script crea las tablas necesarias si no existen

-- Crear tabla config si no existe
CREATE TABLE IF NOT EXISTS `config` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key_name` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT,
    `type` VARCHAR(50) DEFAULT 'text',
    `description` VARCHAR(255),
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Crear tabla membership_types si no existe
CREATE TABLE IF NOT EXISTS `membership_types` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `price` DECIMAL(10,2) NOT NULL,
    `duration_days` INT DEFAULT 360,
    `benefits` JSON,
    `is_active` TINYINT(1) DEFAULT 1,
    `paypal_product_id` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert PayPal config keys if they don't exist
INSERT INTO config (key_name, value, type, description) 
VALUES 
    ('paypal_client_id', 'AWi0IaxZN-e9TQvSbc0FsZj-vHA9-38fyIBmpbQeELJgjNaRgSrGondGzDGQATilllQAlp0J2BJwJCYL', 'text', 'PayPal Client ID'),
    ('paypal_secret', 'ELLC6UBm2stHa0CdfvyukrZSnDtsjhxIZBxrqMZI6us4N3IOPVn54dow4RIJZ6dJBpxeMuOBA_KjdmTx', 'text', 'PayPal Secret'),
    ('paypal_mode', 'sandbox', 'text', 'PayPal Mode (sandbox/live)')
ON DUPLICATE KEY UPDATE 
    value = VALUES(value);
