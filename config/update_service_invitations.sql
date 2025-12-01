-- =====================================================
-- Script de actualización para invitaciones de servicios
-- Cross-selling (Stage 4 del Customer Journey)
-- =====================================================

-- Tabla de invitaciones de servicios (Cross-selling)
CREATE TABLE IF NOT EXISTS `service_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `affiliation_id` int(11) NOT NULL,
  `invitation_date` datetime NOT NULL,
  `invitation_type` enum('whatsapp','email','phone','in_person') NOT NULL DEFAULT 'whatsapp',
  `whatsapp_message` text DEFAULT NULL,
  `email_subject` varchar(255) DEFAULT NULL,
  `email_message` text DEFAULT NULL,
  `contact_whatsapp` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `sent_by_user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `affiliation_id` (`affiliation_id`),
  KEY `sent_by_user_id` (`sent_by_user_id`),
  KEY `invitation_date` (`invitation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla de detalle de servicios en invitaciones (many-to-many)
-- Sin foreign keys para evitar problemas de compatibilidad
CREATE TABLE IF NOT EXISTS `service_invitation_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invitation_id` int(11) NOT NULL COMMENT 'ID de la invitación en service_invitations',
  `service_id` int(11) NOT NULL COMMENT 'ID del servicio en services',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `invitation_id` (`invitation_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Detalle de servicios ofrecidos en cada invitación';
