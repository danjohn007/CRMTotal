-- CRM Total - Database Update Script (MySQL 5.7 compatible)
-- Version: 2.8.0
-- Date: 2025-12-01
-- Description: Enhanced Features Update
--              - SIEM category for Up Selling
--              - Membership characteristics field
--              - Enhanced notification types (prospecto, evento)
--              - Improved search phrase matching support

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- ADD CHARACTERISTICS COLUMN TO MEMBERSHIP TYPES
-- =============================================

-- Check if column exists before adding
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'membership_types' 
                   AND COLUMN_NAME = 'characteristics');

SET @add_col = IF(@col_exists = 0,
    "ALTER TABLE `membership_types`
    ADD COLUMN `characteristics` JSON NULL 
    COMMENT 'List of membership characteristics (features)'
    AFTER `benefits`",
    "SELECT 'characteristics column already exists' AS status"
);

PREPARE stmt FROM @add_col;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- UPDATE SERVICES CATEGORY ENUM TO INCLUDE SIEM
-- =============================================

-- Check if SIEM already in category ENUM
SET @service_category = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'services' 
                         AND COLUMN_NAME = 'category');

SET @siem_needed = IF(@service_category LIKE '%siem%', 0, 1);

SET @services_sql = IF(@siem_needed = 1,
    "ALTER TABLE `services`
    MODIFY COLUMN `category` ENUM(
        'salon_rental', 
        'event_organization', 
        'course', 
        'conference', 
        'training', 
        'marketing_email', 
        'marketing_videowall', 
        'marketing_social', 
        'marketing_platform', 
        'gestoria', 
        'tramites',
        'siem',
        'otros'
    ) NOT NULL",
    "SELECT 'SIEM category already exists' AS status"
);

PREPARE stmt FROM @services_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- UPDATE NOTIFICATIONS TYPE ENUM
-- =============================================

-- Check if notification types already updated with prospecto
SET @notif_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'notifications' 
                   AND COLUMN_NAME = 'type');

SET @notif_alter_needed = IF(@notif_type LIKE '%prospecto%', 0, 1);

SET @notif_sql = IF(@notif_alter_needed = 1,
    "ALTER TABLE `notifications`
    MODIFY COLUMN `type` ENUM(
        'vencimiento', 
        'actividad', 
        'no_match', 
        'oportunidad', 
        'beneficio', 
        'sistema',
        'cross_selling',
        'up_selling',
        'evento',
        'prospecto',
        'felicitacion'
    ) NOT NULL",
    "SELECT 'Notification types already include prospecto' AS status"
);

PREPARE stmt FROM @notif_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- INSERT VISIONARIO MEMBERSHIP TYPE (if not exists)
-- =============================================

INSERT INTO `membership_types` (`name`, `code`, `price`, `duration_days`, `benefits`, `characteristics`, `is_active`)
SELECT 'Membresía Visionario', 'VISIONARIO', 8000.00, 360, 
       '{"descuento_eventos": 25, "buscador": true, "networking": true, "capacitaciones": 5, "asesoria": true, "marketing": true}',
       '["Acceso prioritario a eventos", "Directorio destacado", "5 capacitaciones incluidas", "Asesoría empresarial"]',
       1
WHERE NOT EXISTS (SELECT 1 FROM `membership_types` WHERE `code` = 'VISIONARIO');

-- Update existing membership types with characteristics if null
UPDATE `membership_types` 
SET `characteristics` = '["Acceso al buscador de proveedores", "Eventos de networking"]'
WHERE `code` = 'BASICA' AND (`characteristics` IS NULL OR `characteristics` = '[]');

UPDATE `membership_types` 
SET `characteristics` = '["Acceso al buscador de proveedores", "Eventos de networking", "2 capacitaciones incluidas", "Asesoría empresarial"]'
WHERE `code` = 'PYME' AND (`characteristics` IS NULL OR `characteristics` = '[]');

UPDATE `membership_types` 
SET `characteristics` = '["Capacitaciones ilimitadas", "Marketing incluido", "Asesoría empresarial", "Eventos de networking VIP"]'
WHERE `code` = 'PREMIER' AND (`characteristics` IS NULL OR `characteristics` = '[]');

UPDATE `membership_types` 
SET `characteristics` = '["Todos los beneficios Premier", "Publicidad destacada", "Mesa preferente en eventos", "Descuento máximo en servicios"]'
WHERE `code` = 'PATROCINADOR' AND (`characteristics` IS NULL OR `characteristics` = '[]');

-- =============================================
-- UPDATE BENEFITS TO INCLUDE SIEM WHERE APPLICABLE
-- =============================================

-- Add SIEM benefit to Patrocinador membership
UPDATE `membership_types`
SET `benefits` = JSON_SET(COALESCE(`benefits`, '{}'), '$.siem', true)
WHERE `code` = 'PATROCINADOR';

-- =============================================
-- AUDIT LOG FOR VERSION UPDATE
-- =============================================

INSERT INTO `audit_log` (`user_id`, `action`, `table_name`, `record_id`, `new_values`, `ip_address`, `created_at`)
VALUES (
    NULL,
    'schema_update',
    NULL,
    NULL,
    JSON_OBJECT(
        'version', '2.8.0',
        'changes', JSON_ARRAY(
            'Added SIEM category to services for Up Selling',
            'Added characteristics field to membership_types',
            'Added prospecto and evento notification types',
            'Added Visionario membership type',
            'Updated membership benefits inheritance logic',
            'Enhanced search to use phrase matching',
            'Enhanced expedientes view with search and affiliator filter',
            'Enhanced notifications dropdown in header with categories'
        )
    ),
    '127.0.0.1',
    NOW()
);

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- DEPLOYMENT NOTES
-- =============================================
-- After deploying this update:
-- 
-- 1. SIEM Category:
--    - New service category 'siem' available for Up Selling
--    - Patrocinador members automatically get SIEM benefits
-- 
-- 2. Membership Hierarchy:
--    - BASICA (Tier 1) < PYME (Tier 2) < VISIONARIO (Tier 3) < PREMIER (Tier 4) < PATROCINADOR (Tier 5)
--    - Higher tiers inherit benefits from all lower tiers
--    - Patrocinador gets all PREMIER benefits + SIEM + Publicidad
-- 
-- 3. Expedientes (EDA) Enhancements:
--    - Search now supports phrase matching (multiple words)
--    - Filter by status (complete/incomplete)
--    - Filter by affiliator (sales rep)
--    - Shows payment status column
--    - Shows vendedor (affiliator) column
-- 
-- 4. Notifications:
--    - Enhanced dropdown in header with categorized view
--    - Categories: Nuevos Prospectos, Empresas por Vencer, Búsquedas Sin Resultado, 
--                  Nuevos Eventos, Acciones Urgentes, Oportunidades, Sistema
-- 
-- 5. Membership Editing:
--    - Dynamic benefits (add/remove custom benefits)
--    - Dynamic characteristics (add/remove features)

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- Check SIEM category:
-- SELECT * FROM services WHERE category = 'siem';
--
-- Check membership characteristics:
-- SELECT code, name, characteristics FROM membership_types;
--
-- Check notification types:
-- SELECT DISTINCT type FROM notifications;
--
-- Check Visionario membership:
-- SELECT * FROM membership_types WHERE code = 'VISIONARIO';
