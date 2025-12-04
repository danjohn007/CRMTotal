-- Migration for Menu Reorganization and Field Updates
-- Date: 2025-12-04
-- Description: Updates role permissions, adds cross-selling and upselling automation

-- ===================================================================
-- 1. Update Role Descriptions and Permissions
-- ===================================================================

-- Update VENDEDOR (afiliador) role - ID 4
UPDATE `roles` 
SET `display_name` = 'Vendedor',
    `description` = 'Ejecutivo de ventas con acceso a Prospectos, EDA y Agenda Comercial',
    `permissions` = JSON_OBJECT(
        'dashboard', true,
        'prospects', true,
        'affiliates', true,
        'eda', true,
        'agenda_comercial', true,
        'search', true,
        'reports', true
    )
WHERE `id` = 4;

-- Update DIRECCIÓN role - ID 2
UPDATE `roles`
SET `description` = 'Director General con acceso completo a gestión y configuración',
    `permissions` = JSON_OBJECT(
        'dashboard', true,
        'eda', true,
        'events', true,
        'memberships', true,
        'search', true,
        'reports', true,
        'financial', true,
        'users', true,
        'import', true,
        'config', true
    )
WHERE `id` = 2;

-- Update JEFE COMERCIAL role - ID 3
UPDATE `roles`
SET `description` = 'Jefe del área comercial con acceso a gestión de ventas y usuarios',
    `permissions` = JSON_OBJECT(
        'dashboard', true,
        'prospects', true,
        'eda', true,
        'events', true,
        'memberships', true,
        'agenda_comercial', true,
        'search', true,
        'reports', true,
        'financial', true,
        'users', true,
        'import', true
    )
WHERE `id` = 3;

-- Update CONTABILIDAD role - ID 5
UPDATE `roles`
SET `description` = 'Área contable con acceso a reportes financieros',
    `permissions` = JSON_OBJECT(
        'dashboard', true,
        'reports', true,
        'financial', true
    )
WHERE `id` = 5;

-- Update CONSEJERO role - ID 6
UPDATE `roles`
SET `description` = 'Consejero propietario con acceso a métricas y buscador',
    `permissions` = JSON_OBJECT(
        'dashboard', true,
        'reports_view', true,
        'search', true
    )
WHERE `id` = 6;

-- Update MESA DIRECTIVA role - ID 7
UPDATE `roles`
SET `description` = 'Miembro de mesa directiva con acceso a métricas y buscador',
    `permissions` = JSON_OBJECT(
        'dashboard', true,
        'reports_view', true,
        'search', true
    )
WHERE `id` = 7;

-- ===================================================================
-- 2. Create Activity Types Table for Automated Activities
-- ===================================================================

CREATE TABLE IF NOT EXISTS `activity_types` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Insert activity types
INSERT INTO `activity_types` (`name`, `icon`, `description`) VALUES
('visita', '🏢', 'Visita a sus instalaciones'),
('whatsapp', '💬', 'Enviar mensaje por WhatsApp'),
('email', '📧', 'Enviar correo electrónico'),
('factura', '🧾', 'Enviar factura o comprobante de pago'),
('documentacion', '📎', 'Adjuntar documentación al EDA'),
('alta_afiliado', '✅', 'Dar de alta como afiliado'),
('cross_selling', '🎯', 'Oportunidad de Cross-Selling (automático)'),
('upselling', '📈', 'Oportunidad de Up-Selling (automático)');

-- ===================================================================
-- 3. Create Automated Opportunities Table
-- ===================================================================

CREATE TABLE IF NOT EXISTS `automated_opportunities` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `opportunity_type` enum('cross_selling','upselling') COLLATE utf8_unicode_ci NOT NULL,
  `scheduled_date` date NOT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_contact_id` (`contact_id`),
  KEY `idx_scheduled_date` (`scheduled_date`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_auto_opp_contact` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ===================================================================
-- 4. Add Missing Fields to Contacts Table (if not exist)
-- ===================================================================

-- Add niza_custom_category if not exists
ALTER TABLE `contacts` 
ADD COLUMN IF NOT EXISTS `niza_custom_category` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL 
AFTER `niza_classification`;

-- Add description field if not exists
ALTER TABLE `contacts`
ADD COLUMN IF NOT EXISTS `description` TEXT COLLATE utf8_unicode_ci DEFAULT NULL
AFTER `industry`;

-- Add whatsapp_sales if not exists
ALTER TABLE `contacts`
ADD COLUMN IF NOT EXISTS `whatsapp_sales` VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL
AFTER `whatsapp`;

-- Add whatsapp_purchases if not exists
ALTER TABLE `contacts`
ADD COLUMN IF NOT EXISTS `whatsapp_purchases` VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL
AFTER `whatsapp_sales`;

-- Add whatsapp_admin if not exists
ALTER TABLE `contacts`
ADD COLUMN IF NOT EXISTS `whatsapp_admin` VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL
AFTER `whatsapp_purchases`;

-- ===================================================================
-- 5. Create Stored Procedure for Auto-generating Opportunities
-- ===================================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS `generate_automated_opportunities`$$

CREATE PROCEDURE `generate_automated_opportunities`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_contact_id INT;
    DECLARE v_affiliation_date DATE;
    DECLARE v_cross_selling_date DATE;
    DECLARE v_upselling_date1 DATE;
    DECLARE v_upselling_date2 DATE;
    
    -- Cursor to get all active affiliates with affiliation date
    DECLARE affiliate_cursor CURSOR FOR
        SELECT c.id, a.affiliation_date
        FROM contacts c
        INNER JOIN affiliations a ON c.id = a.contact_id
        WHERE c.contact_type = 'afiliado' 
        AND a.status = 'active'
        AND a.affiliation_date IS NOT NULL;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN affiliate_cursor;
    
    read_loop: LOOP
        FETCH affiliate_cursor INTO v_contact_id, v_affiliation_date;
        
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Calculate cross-selling opportunities (every 6 weeks = 42 days)
        -- Only generate opportunities for the last 6 months and next 3 months
        SET v_cross_selling_date = DATE_ADD(v_affiliation_date, INTERVAL 42 DAY);
        
        -- Create cross-selling opportunities that haven't been created yet
        -- Limit to opportunities within a reasonable timeframe to avoid performance issues
        WHILE v_cross_selling_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY) DO
            -- Only create if within the last 180 days or future
            IF v_cross_selling_date >= DATE_SUB(CURDATE(), INTERVAL 180 DAY) THEN
                INSERT INTO automated_opportunities (contact_id, opportunity_type, scheduled_date, notes)
                SELECT v_contact_id, 'cross_selling', v_cross_selling_date, 
                       CONCAT('Oportunidad automática de Cross-Selling - Ciclo cada 6 semanas')
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM automated_opportunities
                    WHERE contact_id = v_contact_id
                    AND opportunity_type = 'cross_selling'
                    AND scheduled_date = v_cross_selling_date
                );
            END IF;
            
            SET v_cross_selling_date = DATE_ADD(v_cross_selling_date, INTERVAL 42 DAY);
        END WHILE;
        
        -- Calculate upselling opportunities
        -- First upselling: 8 weeks after affiliation (56 days)
        SET v_upselling_date1 = DATE_ADD(v_affiliation_date, INTERVAL 56 DAY);
        
        -- Second upselling: 34 weeks after affiliation (238 days)
        SET v_upselling_date2 = DATE_ADD(v_affiliation_date, INTERVAL 238 DAY);
        
        -- Create first upselling opportunity if date has passed
        IF v_upselling_date1 <= CURDATE() THEN
            INSERT INTO automated_opportunities (contact_id, opportunity_type, scheduled_date, notes)
            SELECT v_contact_id, 'upselling', v_upselling_date1,
                   'Primera oportunidad automática de Up-Selling (8 semanas)'
            FROM DUAL
            WHERE NOT EXISTS (
                SELECT 1 FROM automated_opportunities
                WHERE contact_id = v_contact_id
                AND opportunity_type = 'upselling'
                AND scheduled_date = v_upselling_date1
            );
        END IF;
        
        -- Create second upselling opportunity if date has passed
        IF v_upselling_date2 <= CURDATE() THEN
            INSERT INTO automated_opportunities (contact_id, opportunity_type, scheduled_date, notes)
            SELECT v_contact_id, 'upselling', v_upselling_date2,
                   'Segunda oportunidad automática de Up-Selling (34 semanas)'
            FROM DUAL
            WHERE NOT EXISTS (
                SELECT 1 FROM automated_opportunities
                WHERE contact_id = v_contact_id
                AND opportunity_type = 'upselling'
                AND scheduled_date = v_upselling_date2
            );
        END IF;
        
    END LOOP;
    
    CLOSE affiliate_cursor;
END$$

DELIMITER ;

-- ===================================================================
-- 6. Create Event to Run Automated Opportunity Generation Daily
-- ===================================================================

-- Enable event scheduler if not already enabled
SET GLOBAL event_scheduler = ON;

-- Drop existing event if exists
DROP EVENT IF EXISTS `daily_opportunity_generation`;

-- Create daily event to generate opportunities
-- Runs at 2:00 AM daily to minimize impact on business hours
-- Adjust INTERVAL 2 HOUR to change the time (e.g., INTERVAL 3 HOUR for 3 AM)
CREATE EVENT `daily_opportunity_generation`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY + INTERVAL 2 HOUR
DO
  CALL generate_automated_opportunities();

-- ===================================================================
-- 7. Update Channel Labels
-- ===================================================================

-- Note: Channel label changes (Jefatura -> Reasignaciones) are handled in views
-- The database value 'jefatura_comercial' remains the same for backwards compatibility

-- ===================================================================
-- END OF MIGRATION
-- ===================================================================

-- Run initial opportunity generation
CALL generate_automated_opportunities();

-- Display summary
SELECT 'Migration completed successfully!' AS Status;
SELECT COUNT(*) AS 'Total Automated Opportunities Created' FROM automated_opportunities;
