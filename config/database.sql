-- CRM Total - Cámara de Comercio de Querétaro
-- Database Schema and Sample Data
-- MySQL 5.7 Compatible

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Create database
CREATE DATABASE IF NOT EXISTS crm_ccq CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crm_ccq;

-- =============================================
-- CONFIGURATION TABLES
-- =============================================

-- System Configuration
CREATE TABLE IF NOT EXISTS `config` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `config_key` VARCHAR(100) NOT NULL UNIQUE,
    `config_value` TEXT,
    `config_type` ENUM('text', 'number', 'boolean', 'json', 'color', 'file') DEFAULT 'text',
    `description` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- USER AND AUTHENTICATION TABLES
-- =============================================

-- User Roles
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `display_name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `permissions` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Users
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT UNSIGNED NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20),
    `whatsapp` VARCHAR(20),
    `avatar` VARCHAR(255),
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB;

-- =============================================
-- MEMBERSHIP TYPES
-- =============================================

CREATE TABLE IF NOT EXISTS `membership_types` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `price` DECIMAL(10,2) NOT NULL,
    `duration_days` INT DEFAULT 360,
    `benefits` JSON,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- DIGITAL UNIQUE FILE (Expediente Digital Único)
-- =============================================

-- Main table for contacts/companies
CREATE TABLE IF NOT EXISTS `contacts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rfc` VARCHAR(13) UNIQUE,
    `whatsapp` VARCHAR(20),
    `contact_type` ENUM('afiliado', 'exafiliado', 'prospecto', 'nuevo_usuario', 'funcionario', 'publico_general', 'consejero_propietario', 'consejero_invitado', 'mesa_directiva') DEFAULT 'nuevo_usuario',
    `business_name` VARCHAR(255),
    `commercial_name` VARCHAR(255),
    `owner_name` VARCHAR(255),
    `legal_representative` VARCHAR(255),
    `corporate_email` VARCHAR(255),
    `phone` VARCHAR(20),
    `industry` VARCHAR(100),
    `niza_classification` VARCHAR(10),
    `products_sells` JSON COMMENT '4 principales productos que vende',
    `products_buys` JSON COMMENT '2 principales productos que compra',
    `discount_percentage` DECIMAL(5,2) DEFAULT 0,
    `commercial_address` TEXT,
    `fiscal_address` TEXT,
    `city` VARCHAR(100),
    `state` VARCHAR(100) DEFAULT 'Querétaro',
    `postal_code` VARCHAR(10),
    `google_maps_url` VARCHAR(500),
    `website` VARCHAR(255),
    `facebook` VARCHAR(255),
    `instagram` VARCHAR(255),
    `linkedin` VARCHAR(255),
    `twitter` VARCHAR(255),
    `whatsapp_sales` VARCHAR(20),
    `whatsapp_purchases` VARCHAR(20),
    `whatsapp_admin` VARCHAR(20),
    `profile_completion` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Porcentaje de completitud 0-100',
    `completion_stage` ENUM('A', 'B', 'C') DEFAULT 'A',
    `assigned_affiliate_id` INT UNSIGNED COMMENT 'Afiliador asignado',
    `source_channel` ENUM('chatbot', 'alta_directa', 'evento_gratuito', 'evento_pagado', 'buscador', 'jefatura_comercial') DEFAULT 'alta_directa',
    `notes` TEXT,
    `is_validated` TINYINT(1) DEFAULT 0 COMMENT 'Para consejeros y mesa directiva',
    `validated_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`assigned_affiliate_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`validated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_contact_type` (`contact_type`),
    INDEX `idx_rfc` (`rfc`),
    INDEX `idx_whatsapp` (`whatsapp`)
) ENGINE=InnoDB;

-- Branches/Sucursales
CREATE TABLE IF NOT EXISTS `contact_branches` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contact_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255),
    `address` TEXT,
    `phone` VARCHAR(20),
    `google_maps_url` VARCHAR(500),
    `is_main` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- AFFILIATIONS / MEMBERSHIPS
-- =============================================

CREATE TABLE IF NOT EXISTS `affiliations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contact_id` INT UNSIGNED NOT NULL,
    `membership_type_id` INT UNSIGNED NOT NULL,
    `affiliate_user_id` INT UNSIGNED COMMENT 'Usuario afiliador que realizó la venta',
    `affiliation_date` DATE NOT NULL,
    `expiration_date` DATE NOT NULL,
    `status` ENUM('active', 'expired', 'cancelled', 'pending_payment') DEFAULT 'active',
    `payment_status` ENUM('paid', 'pending', 'partial') DEFAULT 'pending',
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50),
    `payment_reference` VARCHAR(100),
    `invoice_number` VARCHAR(50),
    `invoice_status` ENUM('invoiced', 'pending', 'not_required') DEFAULT 'pending',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`membership_type_id`) REFERENCES `membership_types`(`id`),
    FOREIGN KEY (`affiliate_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_expiration` (`expiration_date`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB;

-- =============================================
-- EVENTS SYSTEM
-- =============================================

CREATE TABLE IF NOT EXISTS `events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `event_type` ENUM('interno', 'externo', 'terceros') NOT NULL,
    `category` VARCHAR(100),
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NOT NULL,
    `location` VARCHAR(255),
    `address` TEXT,
    `google_maps_url` VARCHAR(500),
    `is_online` TINYINT(1) DEFAULT 0,
    `online_url` VARCHAR(500),
    `max_capacity` INT UNSIGNED,
    `is_paid` TINYINT(1) DEFAULT 0,
    `price` DECIMAL(10,2) DEFAULT 0,
    `member_price` DECIMAL(10,2) DEFAULT 0,
    `registration_url` VARCHAR(255) UNIQUE,
    `image` VARCHAR(255),
    `status` ENUM('draft', 'published', 'cancelled', 'completed') DEFAULT 'draft',
    `target_audiences` JSON COMMENT 'afiliados, prospectos, publico, etc.',
    `created_by` INT UNSIGNED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Event Registrations
CREATE TABLE IF NOT EXISTS `event_registrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT UNSIGNED NOT NULL,
    `contact_id` INT UNSIGNED,
    `guest_name` VARCHAR(255),
    `guest_email` VARCHAR(255),
    `guest_phone` VARCHAR(20),
    `guest_rfc` VARCHAR(13),
    `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `attended` TINYINT(1) DEFAULT 0,
    `attendance_time` TIMESTAMP NULL,
    `payment_status` ENUM('paid', 'pending', 'free') DEFAULT 'free',
    `notes` TEXT,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- AGENDA / ACTIVITIES
-- =============================================

CREATE TABLE IF NOT EXISTS `activities` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL COMMENT 'Usuario que realiza la actividad',
    `contact_id` INT UNSIGNED COMMENT 'Contacto relacionado',
    `activity_type` ENUM('llamada', 'whatsapp', 'email', 'visita', 'reunion', 'seguimiento', 'otro') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `scheduled_date` DATETIME NOT NULL,
    `completed_date` DATETIME,
    `status` ENUM('pendiente', 'en_progreso', 'completada', 'cancelada') DEFAULT 'pendiente',
    `result` TEXT COMMENT 'Resultado de la actividad',
    `next_action` VARCHAR(255) COMMENT 'Siguiente acción a realizar',
    `next_action_date` DATETIME,
    `priority` ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE SET NULL,
    INDEX `idx_scheduled` (`scheduled_date`),
    INDEX `idx_user_status` (`user_id`, `status`)
) ENGINE=InnoDB;

-- =============================================
-- SERVICES (Cross-selling / Up-selling)
-- =============================================

CREATE TABLE IF NOT EXISTS `services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category` ENUM('salon_rental', 'event_organization', 'course', 'conference', 'training', 'marketing_email', 'marketing_videowall', 'marketing_social', 'marketing_platform', 'gestoria', 'tramites', 'otros') NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2),
    `member_price` DECIMAL(10,2),
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Service Contracts
CREATE TABLE IF NOT EXISTS `service_contracts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `contact_id` INT UNSIGNED NOT NULL,
    `service_id` INT UNSIGNED NOT NULL,
    `affiliate_user_id` INT UNSIGNED COMMENT 'Vendedor',
    `contract_date` DATE NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('active', 'completed', 'cancelled', 'pending') DEFAULT 'active',
    `payment_status` ENUM('paid', 'pending', 'partial') DEFAULT 'pending',
    `invoice_number` VARCHAR(50),
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`service_id`) REFERENCES `services`(`id`),
    FOREIGN KEY (`affiliate_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================
-- NOTIFICATIONS
-- =============================================

CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('vencimiento', 'actividad', 'no_match', 'oportunidad', 'beneficio', 'sistema') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT,
    `link` VARCHAR(255),
    `related_id` INT UNSIGNED,
    `related_type` VARCHAR(50),
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_read` (`user_id`, `is_read`)
) ENGINE=InnoDB;

-- =============================================
-- INTELLIGENT SEARCH / NO MATCH
-- =============================================

CREATE TABLE IF NOT EXISTS `search_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `search_term` VARCHAR(255) NOT NULL,
    `searcher_type` ENUM('afiliado', 'publico', 'exafiliado') DEFAULT 'publico',
    `searcher_contact_id` INT UNSIGNED,
    `results_count` INT DEFAULT 0,
    `is_no_match` TINYINT(1) DEFAULT 0,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`searcher_contact_id`) REFERENCES `contacts`(`id`) ON DELETE SET NULL,
    INDEX `idx_no_match` (`is_no_match`)
) ENGINE=InnoDB;

-- =============================================
-- BENEFITS USAGE
-- =============================================

CREATE TABLE IF NOT EXISTS `benefit_usage` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `affiliation_id` INT UNSIGNED NOT NULL,
    `benefit_name` VARCHAR(255) NOT NULL,
    `usage_date` DATE NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`affiliation_id`) REFERENCES `affiliations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- AUDIT LOG
-- =============================================

CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED,
    `action` VARCHAR(100) NOT NULL,
    `table_name` VARCHAR(100),
    `record_id` INT UNSIGNED,
    `old_values` JSON,
    `new_values` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_table_record` (`table_name`, `record_id`)
) ENGINE=InnoDB;

-- =============================================
-- NIZA CLASSIFICATION
-- =============================================

CREATE TABLE IF NOT EXISTS `niza_classifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `class_number` VARCHAR(5) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `keywords` JSON
) ENGINE=InnoDB;

-- =============================================
-- INSERT DEFAULT DATA
-- =============================================

-- Roles
INSERT INTO `roles` (`name`, `display_name`, `description`, `permissions`) VALUES
('superadmin', 'Super Administrador', 'Acceso completo al sistema', '{"all": true}'),
('direccion', 'Dirección', 'Director General o Gerente', '{"dashboard": true, "reports": true, "users": true}'),
('jefe_comercial', 'Jefe Comercial', 'Jefatura del área comercial', '{"dashboard": true, "prospects": true, "affiliates": true, "events": true, "reports": true}'),
('afiliador', 'Afiliador', 'Ejecutivo de ventas/afiliaciones', '{"dashboard": true, "prospects": true, "affiliates": true, "events": true}'),
('contabilidad', 'Contabilidad', 'Área contable y facturación', '{"dashboard": true, "affiliates": true, "invoices": true}'),
('consejero', 'Consejero', 'Consejero propietario o invitado', '{"dashboard": true, "reports_view": true}'),
('mesa_directiva', 'Mesa Directiva', 'Miembro de mesa directiva', '{"dashboard": true, "reports_view": true}');

-- Membership Types
INSERT INTO `membership_types` (`name`, `code`, `price`, `duration_days`, `benefits`) VALUES
('Membresía Básica', 'BASICA', 2500.00, 360, '{"descuento_eventos": 10, "buscador": true, "networking": true}'),
('Membresía PYME', 'PYME', 5000.00, 360, '{"descuento_eventos": 20, "buscador": true, "networking": true, "capacitaciones": 2, "asesoria": true}'),
('Membresía PREMIER', 'PREMIER', 15000.00, 360, '{"descuento_eventos": 30, "buscador": true, "networking": true, "capacitaciones": "ilimitadas", "asesoria": true, "marketing": true}'),
('Patrocinador', 'PATROCINADOR', 50000.00, 360, '{"descuento_eventos": 50, "buscador": true, "networking": true, "capacitaciones": "ilimitadas", "asesoria": true, "marketing": true, "publicidad": true}');

-- System Configuration
INSERT INTO `config` (`config_key`, `config_value`, `config_type`, `description`) VALUES
('site_name', 'CRM Cámara de Comercio de Querétaro', 'text', 'Nombre del sitio'),
('site_logo', NULL, 'file', 'Logo del sitio'),
('primary_color', '#1e40af', 'color', 'Color primario del sistema'),
('secondary_color', '#3b82f6', 'color', 'Color secundario'),
('accent_color', '#10b981', 'color', 'Color de acento'),
('contact_phone', '442 212 0035', 'text', 'Teléfono principal'),
('contact_email', 'info@camaradecomercioqro.mx', 'text', 'Correo de contacto'),
('office_hours', 'Lunes a Viernes 9:00 - 18:00', 'text', 'Horario de atención'),
('address', 'Av. 5 de Febrero No. 412, Centro, 76000 Santiago de Querétaro, Qro.', 'text', 'Dirección'),
('paypal_client_id', '', 'text', 'PayPal Client ID'),
('paypal_secret', '', 'text', 'PayPal Secret'),
('paypal_mode', 'sandbox', 'text', 'PayPal Mode (sandbox/live)'),
('smtp_host', '', 'text', 'SMTP Host'),
('smtp_port', '587', 'number', 'SMTP Port'),
('smtp_user', '', 'text', 'SMTP User'),
('smtp_password', '', 'text', 'SMTP Password'),
('smtp_from_name', 'CRM CCQ', 'text', 'Nombre remitente de correos'),
('qr_api_key', '', 'text', 'API Key para QR masivos');

-- Default Admin User (password: Admin123!)
INSERT INTO `users` (`role_id`, `email`, `password`, `name`, `phone`) VALUES
(1, 'admin@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Sistema', '442 212 0035');

-- Sample Affiliators
INSERT INTO `users` (`role_id`, `email`, `password`, `name`, `phone`, `whatsapp`) VALUES
(4, 'ventas1@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María González Pérez', '442 555 0001', '4421234567'),
(4, 'ventas2@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos Hernández López', '442 555 0002', '4421234568'),
(3, 'jefe.comercial@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Roberto Martínez Silva', '442 555 0003', '4421234569'),
(2, 'direccion@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana Lucia Ramírez Torres', '442 555 0004', '4421234570'),
(5, 'contabilidad@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia Sánchez Moreno', '442 555 0005', '4421234571');

-- Sample Contacts (Affiliates) from Querétaro
INSERT INTO `contacts` (`rfc`, `whatsapp`, `contact_type`, `business_name`, `commercial_name`, `owner_name`, `legal_representative`, `corporate_email`, `phone`, `industry`, `niza_classification`, `products_sells`, `products_buys`, `commercial_address`, `fiscal_address`, `city`, `postal_code`, `website`, `profile_completion`, `completion_stage`, `assigned_affiliate_id`, `source_channel`) VALUES
('QRO0001010ABC', '4421112233', 'afiliado', 'Comercializadora del Centro SA de CV', 'ComercioQro', 'Juan Pérez García', 'Juan Pérez García', 'contacto@comercioqro.mx', '442 111 2233', 'Comercio', '35', '["Abarrotes", "Productos de limpieza", "Papelería", "Plásticos"]', '["Productos al mayoreo", "Servicios de transporte"]', 'Calle Corregidora #123, Centro, Querétaro', 'Calle Corregidora #123, Centro, Querétaro', 'Santiago de Querétaro', '76000', 'www.comercioqro.mx', 100, 'C', 2, 'alta_directa'),

('TEC0002020DEF', '4422223344', 'afiliado', 'Tecnología Queretana SA de CV', 'TecQro', 'Miguel Ángel Rodríguez', 'Miguel Ángel Rodríguez', 'info@tecqro.com', '442 222 3344', 'Tecnología', '9', '["Computadoras", "Redes", "Servidores", "Software"]', '["Componentes electrónicos", "Licencias de software"]', 'Av. Constituyentes #456, Centro Sur, Querétaro', 'Av. Constituyentes #456, Centro Sur, Querétaro', 'Santiago de Querétaro', '76040', 'www.tecqro.com', 100, 'C', 2, 'evento_gratuito'),

('ALI0003030GHI', '4423334455', 'afiliado', 'Alimentos del Bajío SA de CV', 'AliBajío', 'Rosa María López', 'Rosa María López', 'ventas@alibajio.mx', '442 333 4455', 'Alimentos', '29', '["Productos lácteos", "Carnes frías", "Conservas", "Bebidas"]', '["Empaques", "Materias primas"]', 'Parque Industrial El Marqués, Querétaro', 'Parque Industrial El Marqués, Querétaro', 'El Marqués', '76246', 'www.alibajio.mx', 70, 'C', 3, 'alta_directa'),

('SER0004040JKL', '4424445566', 'prospecto', 'Servicios Profesionales Qro SC', 'ServPro', 'Francisco Torres Vega', 'Francisco Torres Vega', 'contacto@servproqro.com', '442 444 5566', 'Servicios', '35', '["Consultoría", "Asesoría legal", "Contabilidad", "Capacitación"]', '["Software especializado", "Equipos de oficina"]', 'Blvd. Bernardo Quintana #200, Centro', 'Blvd. Bernardo Quintana #200, Centro', 'Santiago de Querétaro', '76050', NULL, 35, 'B', 2, 'chatbot'),

('IND0005050MNO', '4425556677', 'afiliado', 'Industrias Queretanas SA de CV', 'IndQro', 'Alberto Méndez Ruiz', 'Alberto Méndez Ruiz', 'info@indqro.com.mx', '442 555 6677', 'Manufactura', '7', '["Maquinaria industrial", "Refacciones", "Mantenimiento", "Instalaciones"]', '["Acero", "Componentes importados"]', 'Parque Industrial Querétaro, El Marqués', 'Parque Industrial Querétaro, El Marqués', 'El Marqués', '76246', 'www.indqro.com.mx', 100, 'C', 3, 'jefatura_comercial'),

('HOT0006060PQR', '4426667788', 'exafiliado', 'Hotelería Queretana SA de CV', 'HotelQro', 'Carmen Gutiérrez', 'Carmen Gutiérrez', 'reservas@hotelqro.mx', '442 666 7788', 'Turismo', '43', '["Hospedaje", "Eventos", "Restaurante", "Tours"]', '["Alimentos perecederos", "Productos de limpieza"]', 'Av. Universidad #500, Centro', 'Av. Universidad #500, Centro', 'Santiago de Querétaro', '76010', 'www.hotelqro.mx', 100, 'C', 2, 'alta_directa'),

('MED0007070STU', '4427778899', 'afiliado', 'Centro Médico Querétaro SA de CV', 'MediQro', 'Dr. Luis Fernández', 'Dr. Luis Fernández', 'citas@mediqro.com', '442 777 8899', 'Salud', '44', '["Consultas médicas", "Análisis clínicos", "Cirugías", "Rehabilitación"]', '["Medicamentos", "Equipo médico"]', 'Av. 5 de Febrero #1000, Centro', 'Av. 5 de Febrero #1000, Centro', 'Santiago de Querétaro', '76000', 'www.mediqro.com', 70, 'C', 2, 'buscador'),

('REST0008080VWX', '4428889900', 'prospecto', 'Restaurantes del Centro SC', 'RestCentro', 'Chef Manuel García', 'Manuel García Pérez', 'contacto@restcentro.mx', '442 888 9900', 'Alimentos y Bebidas', '43', '["Comida mexicana", "Catering", "Banquetes", "Cafetería"]', '["Alimentos frescos", "Bebidas"]', 'Andador 5 de Mayo #50, Centro', 'Andador 5 de Mayo #50, Centro', 'Santiago de Querétaro', '76000', NULL, 25, 'A', 3, 'evento_pagado'),

('AUTO0009090YZA', '4429990011', 'afiliado', 'Automotriz Querétaro SA de CV', 'AutoQro', 'Jorge Ramírez López', 'Jorge Ramírez López', 'ventas@autoqro.mx', '442 999 0011', 'Automotriz', '12', '["Venta de autos", "Servicio mecánico", "Refacciones", "Accesorios"]', '["Vehículos nuevos", "Refacciones importadas"]', 'Av. Tecnológico #800, Centro Norte', 'Av. Tecnológico #800, Centro Norte', 'Santiago de Querétaro', '76030', 'www.autoqro.mx', 100, 'C', 2, 'alta_directa'),

('CONS0010010BCD', '4420001122', 'funcionario', 'Secretaría de Desarrollo Económico', 'SEDEQ', 'Lic. Ana María Soto', NULL, 'contacto@sedeq.gob.mx', '442 000 1122', 'Gobierno', NULL, NULL, NULL, 'Palacio de Gobierno, Centro', NULL, 'Santiago de Querétaro', '76000', 'www.queretaro.gob.mx', 35, 'B', NULL, 'evento_gratuito');

-- Sample Affiliations
INSERT INTO `affiliations` (`contact_id`, `membership_type_id`, `affiliate_user_id`, `affiliation_date`, `expiration_date`, `status`, `payment_status`, `amount`, `payment_method`, `invoice_status`) VALUES
(1, 2, 2, '2024-01-15', '2025-01-10', 'active', 'paid', 5000.00, 'Transferencia', 'invoiced'),
(2, 3, 2, '2024-02-20', '2025-02-15', 'active', 'paid', 15000.00, 'Tarjeta', 'invoiced'),
(3, 1, 3, '2024-03-10', '2025-03-05', 'active', 'paid', 2500.00, 'Efectivo', 'invoiced'),
(5, 4, 3, '2024-01-01', '2024-12-27', 'active', 'paid', 50000.00, 'Transferencia', 'invoiced'),
(6, 2, 2, '2023-06-15', '2024-06-10', 'expired', 'paid', 5000.00, 'Transferencia', 'invoiced'),
(7, 3, 2, '2024-04-01', '2025-03-27', 'active', 'paid', 15000.00, 'Tarjeta', 'invoiced'),
(9, 1, 2, '2024-05-20', '2025-05-15', 'active', 'paid', 2500.00, 'Efectivo', 'pending');

-- Sample Events
INSERT INTO `events` (`title`, `description`, `event_type`, `category`, `start_date`, `end_date`, `location`, `address`, `is_online`, `is_paid`, `price`, `member_price`, `registration_url`, `status`, `target_audiences`, `created_by`) VALUES
('Networking Empresarial Enero 2025', 'Evento de networking para socios y prospectos de la Cámara de Comercio', 'interno', 'Networking', '2025-01-20 18:00:00', '2025-01-20 21:00:00', 'Salón Principal CCQ', 'Av. 5 de Febrero No. 412, Centro', 0, 0, 0, 0, 'networking-enero-2025', 'published', '["afiliado", "prospecto"]', 1),
('Curso: Marketing Digital para PyMEs', 'Aprende estrategias de marketing digital efectivas para tu negocio', 'interno', 'Capacitación', '2025-02-05 09:00:00', '2025-02-05 14:00:00', 'Aula de Capacitación CCQ', 'Av. 5 de Febrero No. 412, Centro', 0, 1, 1500.00, 1000.00, 'marketing-digital-pymes', 'published', '["afiliado", "prospecto", "publico"]', 1),
('Foro de Desarrollo Económico 2025', 'Principales tendencias económicas para el sector empresarial', 'externo', 'Conferencia', '2025-03-15 10:00:00', '2025-03-15 18:00:00', 'Centro de Congresos Querétaro', 'Blvd. Bernardo Quintana', 0, 1, 500.00, 0, 'foro-economico-2025', 'draft', '["afiliado", "funcionario", "publico"]', 4),
('Webinar: Nuevas Regulaciones Fiscales 2025', 'Todo lo que necesitas saber sobre los cambios fiscales', 'interno', 'Webinar', '2025-01-30 17:00:00', '2025-01-30 18:30:00', 'Online', NULL, 1, 0, 0, 0, 'webinar-fiscal-2025', 'published', '["afiliado", "prospecto"]', 1);

-- Sample Activities
INSERT INTO `activities` (`user_id`, `contact_id`, `activity_type`, `title`, `description`, `scheduled_date`, `status`, `priority`, `next_action`) VALUES
(2, 4, 'llamada', 'Llamada de seguimiento prospecto', 'Dar seguimiento a solicitud de información sobre membresía PYME', '2025-01-10 10:00:00', 'pendiente', 'alta', 'Enviar cotización por email'),
(2, 1, 'visita', 'Visita de renovación', 'Visitar al cliente para renovación de membresía', '2025-01-08 11:00:00', 'completada', 'media', NULL),
(3, 8, 'email', 'Envío de información', 'Enviar información de membresías y beneficios', '2025-01-05 09:00:00', 'completada', 'media', 'Llamar para confirmar recepción'),
(2, 7, 'whatsapp', 'Recordatorio evento', 'Recordar sobre evento de networking', '2025-01-18 16:00:00', 'pendiente', 'baja', NULL),
(3, 5, 'reunion', 'Reunión patrocinio', 'Revisar beneficios de patrocinador y proponer mejoras', '2025-01-12 14:00:00', 'pendiente', 'alta', 'Preparar presentación');

-- Sample Services
INSERT INTO `services` (`category`, `name`, `description`, `price`, `member_price`) VALUES
('salon_rental', 'Renta de Salón Principal', 'Salón para 100 personas con equipo audiovisual', 8000.00, 5000.00),
('salon_rental', 'Renta de Sala de Juntas', 'Sala ejecutiva para 15 personas', 2500.00, 1500.00),
('course', 'Curso de Liderazgo Empresarial', 'Programa de 20 horas de desarrollo gerencial', 5000.00, 3500.00),
('marketing_email', 'Campaña de Email Marketing', 'Diseño y envío a base de datos de afiliados', 3000.00, 2000.00),
('marketing_social', 'Publicación en Redes Sociales CCQ', 'Post patrocinado en redes de la Cámara', 1500.00, 1000.00),
('gestoria', 'Gestoría Licencia de Funcionamiento', 'Trámite completo de licencia municipal', 4000.00, 3000.00),
('event_organization', 'Organización de Evento Corporativo', 'Planeación y ejecución de evento empresarial', 15000.00, 12000.00);

-- Sample Notifications
INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `link`, `related_id`, `related_type`) VALUES
(2, 'vencimiento', 'Membresía próxima a vencer', 'La membresía de Hotelería Queretana vence en 7 días', '/afiliados/6', 6, 'affiliation'),
(2, 'actividad', 'Actividad pendiente', 'Tienes una llamada programada para hoy a las 10:00', '/agenda/1', 1, 'activity'),
(3, 'oportunidad', 'Nueva oportunidad de cross-selling', 'Industrias Queretanas podría estar interesado en marketing', '/journey/upselling', 5, 'contact'),
(2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó "impresión 3d" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search');

-- Sample Search Logs (including NO MATCH)
INSERT INTO `search_logs` (`search_term`, `searcher_type`, `results_count`, `is_no_match`, `ip_address`) VALUES
('tecnología computadoras', 'publico', 1, 0, '192.168.1.100'),
('alimentos lácteos', 'afiliado', 1, 0, '192.168.1.101'),
('impresión 3d', 'publico', 0, 1, '192.168.1.102'),
('servicio de limpieza industrial', 'publico', 0, 1, '192.168.1.103'),
('asesoría legal', 'afiliado', 1, 0, '192.168.1.104'),
('fabricación moldes', 'publico', 0, 1, '192.168.1.105');

-- Niza Classifications (Basic Set)
INSERT INTO `niza_classifications` (`class_number`, `name`, `description`) VALUES
('1', 'Productos químicos', 'Productos químicos para la industria, ciencia, fotografía'),
('7', 'Máquinas y máquinas herramienta', 'Máquinas, máquinas herramienta, motores'),
('9', 'Aparatos e instrumentos científicos', 'Aparatos científicos, náuticos, geodésicos, fotográficos'),
('12', 'Vehículos', 'Vehículos, aparatos de locomoción terrestre, aérea o acuática'),
('29', 'Carne, pescado, aves y caza', 'Carne, pescado, productos lácteos, aceites'),
('35', 'Publicidad y negocios', 'Publicidad, gestión de negocios comerciales'),
('36', 'Seguros y finanzas', 'Seguros, operaciones financieras'),
('41', 'Educación y entretenimiento', 'Educación, formación, entretenimiento'),
('42', 'Servicios científicos y tecnológicos', 'Servicios científicos, investigación'),
('43', 'Servicios de restauración', 'Servicios de restauración, hospedaje temporal'),
('44', 'Servicios médicos', 'Servicios médicos, veterinarios, higiénicos');

SET FOREIGN_KEY_CHECKS = 1;
