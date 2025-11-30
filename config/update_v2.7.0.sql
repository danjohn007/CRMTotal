-- CRM Total - Database Update Script (MySQL 5.7 compatible)
-- Version: 2.7.0
-- Date: 2025-11-30
-- Description: Agenda y Acciones Comerciales - Unified Section
--              Merges: Agenda, Notifications, and Commercial Requirements
--              - User activity tracking (including off-hours work)
--              - Motivational messages system
--              - Enhanced activity types for affiliator workflow

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- USER ACTIVITY LOG TABLE
-- Tracks user actions including off-hours work
-- =============================================

CREATE TABLE IF NOT EXISTS `user_activity_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `action` VARCHAR(100) NOT NULL COMMENT 'Action type: access_commercial_agenda, create_activity, send_whatsapp, etc.',
    `related_id` INT UNSIGNED NULL COMMENT 'ID of related entity (activity, contact, etc.)',
    `metadata` JSON NULL COMMENT 'Additional action metadata',
    `is_outside_hours` TINYINT(1) DEFAULT 0 COMMENT 'Was action performed outside work hours (9am-6pm Mon-Fri)',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_outside_hours` (`user_id`, `is_outside_hours`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB COMMENT='Tracks user activity for performance metrics and off-hours recognition';

-- =============================================
-- MOTIVATIONAL MESSAGES TABLE
-- =============================================

CREATE TABLE IF NOT EXISTS `motivational_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `context` ENUM('morning', 'afternoon', 'evening', 'off_hours', 'achievement', 'milestone') NOT NULL,
    `icon` VARCHAR(10) NOT NULL COMMENT 'Emoji or icon',
    `title` VARCHAR(100) NOT NULL,
    `message` TEXT NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Motivational messages for commercial team';

-- Insert default motivational messages
INSERT INTO `motivational_messages` (`context`, `icon`, `title`, `message`) VALUES
-- Morning messages (9am - 12pm)
('morning', '☀️', '¡Buenos días!', 'Comienza el día con energía. Cada llamada es una oportunidad de éxito.'),
('morning', '🚀', '¡A conquistar el día!', 'Las mañanas productivas generan resultados extraordinarios.'),
('morning', '💡', '¡Momento de crear!', 'Tu primer contacto del día puede ser el cierre más importante del mes.'),
('morning', '🌟', '¡Nuevo día, nuevas oportunidades!', 'Hoy es el día perfecto para superar tus metas.'),

-- Afternoon messages (12pm - 3pm)
('afternoon', '⚡', '¡Mantén el ritmo!', 'El mediodía es perfecto para dar seguimiento a tus prospectos más calientes.'),
('afternoon', '🎯', '¡Enfócate en los objetivos!', 'Cada acción cuenta. Estás más cerca de tu meta de lo que crees.'),
('afternoon', '💪', '¡Energía al máximo!', 'Este es el momento para cerrar las oportunidades pendientes.'),

-- Evening messages (3pm - 6pm)
('evening', '🌅', '¡Cierra el día con fuerza!', 'Las últimas horas son oro. Un seguimiento ahora puede marcar la diferencia.'),
('evening', '✨', '¡Sprint final!', 'Aprovecha las últimas horas. Los mejores cierres ocurren al final del día.'),
('evening', '🏆', '¡Última hora productiva!', 'Un email más, una llamada más. El éxito está en los detalles.'),

-- Off-hours messages (outside 9am-6pm Mon-Fri)
('off_hours', '🌟', '¡Compromiso Excepcional!', 'Tu dedicación fuera del horario laboral demuestra un compromiso extraordinario con nuestros objetivos.'),
('off_hours', '🏆', '¡Esfuerzo Reconocido!', 'Trabajar fuera de horario muestra tu pasión. Tu esfuerzo no pasa desapercibido.'),
('off_hours', '💪', '¡Dedicación Ejemplar!', 'Los grandes logros requieren dedicación extra. ¡Sigue adelante!'),
('off_hours', '🌙', '¡Trabajo Inspirador!', 'Tu compromiso es un ejemplo para todo el equipo. ¡Gracias por tu dedicación!'),

-- Achievement messages
('achievement', '🎉', '¡Felicitaciones!', 'Has alcanzado un nuevo hito. Tu esfuerzo está dando frutos.'),
('achievement', '🥇', '¡Eres el mejor!', 'Tu rendimiento este mes ha sido excepcional.'),
('achievement', '🏅', '¡Meta cumplida!', 'Has demostrado que con dedicación todo es posible.'),

-- Milestone messages
('milestone', '📈', '¡Crecimiento constante!', 'Tu progreso este mes muestra un patrón de mejora continua.'),
('milestone', '🎯', '¡En el camino correcto!', 'Estás muy cerca de alcanzar tu objetivo mensual.');

-- =============================================
-- EXTENDED ACTIVITY TYPES
-- Add new activity types for affiliator workflow
-- =============================================

-- The activities table already uses ENUM, so we need to modify it
-- This adds new activity types for the affiliator workflow

ALTER TABLE `activities` 
MODIFY COLUMN `activity_type` ENUM(
    'llamada', 
    'whatsapp', 
    'email', 
    'visita', 
    'reunion', 
    'seguimiento', 
    'invitacion',           -- NEW: Sending invitations
    'prospectacion',        -- NEW: Territory prospecting
    'captura',              -- NEW: Prospect capture
    'factura',              -- NEW: Invoice request
    'otro'
) NOT NULL;

-- =============================================
-- ADD SOURCE TRACKING TO NOTIFICATIONS
-- =============================================

ALTER TABLE `notifications`
ADD COLUMN `source_section` VARCHAR(50) NULL 
COMMENT 'Original section: agenda, requirements, notifications'
AFTER `related_type`;

-- Update existing notifications to set source
UPDATE `notifications` SET `source_section` = 'notifications' WHERE `source_section` IS NULL;

-- =============================================
-- ADD NOTIFICATION TYPE FOR NEW UNIFIED SECTION
-- =============================================

ALTER TABLE `notifications`
MODIFY COLUMN `type` ENUM(
    'vencimiento', 
    'actividad', 
    'no_match', 
    'oportunidad', 
    'beneficio', 
    'sistema',
    'cross_selling',        -- NEW: Cross-selling opportunity
    'up_selling',           -- NEW: Up-selling opportunity
    'evento',               -- NEW: Event notification
    'prospecto',            -- NEW: New prospect notification
    'felicitacion'          -- NEW: Congratulation message
) NOT NULL;

-- =============================================
-- PERFORMANCE GOALS TABLE (Optional for tracking targets)
-- =============================================

CREATE TABLE IF NOT EXISTS `performance_goals` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `goal_type` ENUM('activities', 'contacts', 'affiliations', 'revenue') NOT NULL,
    `target_value` DECIMAL(12,2) NOT NULL,
    `current_value` DECIMAL(12,2) DEFAULT 0,
    `period` ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly') DEFAULT 'monthly',
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_achieved` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_period` (`user_id`, `period`, `start_date`)
) ENGINE=InnoDB COMMENT='Performance goals for sales team';

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
        'version', '2.7.0',
        'changes', JSON_ARRAY(
            'Created unified Agenda y Acciones Comerciales section',
            'Merged Agenda, Notifications, and Commercial Requirements functionality',
            'Added user_activity_log table for tracking off-hours work',
            'Added motivational_messages table',
            'Extended activity types for affiliator workflow',
            'Added performance_goals table',
            'Updated sidebar navigation'
        ),
        'deprecated', JSON_ARRAY(
            'Standalone Agenda section (routes still work for backward compatibility)',
            'Standalone Notifications section (merged into unified section)',
            'Standalone Requerimientos section (merged into unified section)'
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
-- 1. Agenda y Acciones Comerciales:
--    - New unified section at /agenda-comercial
--    - Combines: Agenda, Notifications, Commercial Requirements
--    - Main objectives:
--      * Daily/weekly/monthly action planning
--      * Prospect visibility and tracking
--      * Cross & up selling opportunities
--      * Quick contact actions (WhatsApp, Email)
--      * Event invitations
--      * Performance metrics with motivational messages
-- 
-- 2. Activity Types for Affiliators:
--    - invitacion: Sending invitations to events
--    - prospectacion: Territory prospecting
--    - captura: Capturing new prospects
--    - factura: Invoice requests
-- 
-- 3. Off-Hours Tracking:
--    - Tracks user activity outside 9am-6pm Mon-Fri
--    - Visible to: jefe_comercial, direccion, superadmin, mesa_directiva, consejeros
--    - Shows recognition messages for dedicated employees
-- 
-- 4. Priority Order for Affiliators:
--    Priority 1: New prospects with RFC + WhatsApp (from events)
--    Priority 2: Follow-ups to existing prospects
--    Priority 3: Cross & up selling opportunities
-- 
-- 5. Navigation Changes:
--    - Removed: Agenda, Notificaciones, Requerimientos from sidebar
--    - Added: "Agenda Comercial" unified section
--    - Legacy routes still work for backward compatibility

-- =============================================
-- VERIFICATION QUERIES
-- =============================================
-- Check user activity log:
-- SELECT u.name, COUNT(*) as total_actions, SUM(is_outside_hours) as off_hours_actions 
-- FROM user_activity_log ual JOIN users u ON ual.user_id = u.id 
-- GROUP BY u.id, u.name ORDER BY off_hours_actions DESC;
--
-- Check motivational messages:
-- SELECT context, COUNT(*) FROM motivational_messages WHERE is_active = 1 GROUP BY context;
--
-- Check new activity types usage:
-- SELECT activity_type, COUNT(*) FROM activities GROUP BY activity_type;

-- =============================================
-- ROLLBACK SCRIPT (if needed)
-- =============================================
-- DROP TABLE IF EXISTS `user_activity_log`;
-- DROP TABLE IF EXISTS `motivational_messages`;
-- DROP TABLE IF EXISTS `performance_goals`;
-- 
-- ALTER TABLE `activities` 
-- MODIFY COLUMN `activity_type` ENUM('llamada', 'whatsapp', 'email', 'visita', 'reunion', 'seguimiento', 'otro') NOT NULL;
--
-- ALTER TABLE `notifications` DROP COLUMN `source_section`;
--
-- ALTER TABLE `notifications`
-- MODIFY COLUMN `type` ENUM('vencimiento', 'actividad', 'no_match', 'oportunidad', 'beneficio', 'sistema') NOT NULL;
--
-- DELETE FROM audit_log WHERE new_values LIKE '%"version":"2.7.0"%';
