-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-12-2025 a las 09:59:53
-- Versión del servidor: 5.7.23-23
-- Versión de PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `enlaceca_total`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activities`
--

CREATE TABLE `activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'Usuario que realiza la actividad',
  `contact_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Contacto relacionado',
  `activity_type` enum('llamada','whatsapp','email','visita','reunion','seguimiento','invitacion','prospectacion','captura','factura','otro') COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `scheduled_date` datetime NOT NULL,
  `completed_date` datetime DEFAULT NULL,
  `status` enum('pendiente','en_progreso','completada','cancelada') COLLATE utf8_unicode_ci DEFAULT 'pendiente',
  `result` text COLLATE utf8_unicode_ci COMMENT 'Resultado de la actividad',
  `next_action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Siguiente acción a realizar',
  `next_action_date` datetime DEFAULT NULL,
  `priority` enum('baja','media','alta','urgente') COLLATE utf8_unicode_ci DEFAULT 'media',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `contact_id`, `activity_type`, `title`, `description`, `scheduled_date`, `completed_date`, `status`, `result`, `next_action`, `next_action_date`, `priority`, `created_at`, `updated_at`) VALUES
(1, 2, 4, 'llamada', 'Llamada de seguimiento prospecto', 'Dar seguimiento a solicitud de información sobre membresía PYME', '2025-01-10 10:00:00', NULL, 'pendiente', NULL, 'Enviar cotización por email', NULL, 'alta', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(2, 2, 1, 'visita', 'Visita de renovación', 'Visitar al cliente para renovación de membresía', '2025-01-08 11:00:00', NULL, 'completada', NULL, NULL, NULL, 'media', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(3, 3, 8, 'email', 'Envío de información', 'Enviar información de membresías y beneficios', '2025-01-05 09:00:00', NULL, 'completada', NULL, 'Llamar para confirmar recepción', NULL, 'media', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(4, 2, 7, 'whatsapp', 'Recordatorio evento', 'Recordar sobre evento de networking', '2025-01-18 16:00:00', NULL, 'pendiente', NULL, NULL, NULL, 'baja', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(5, 3, 5, 'reunion', 'Reunión patrocinio', 'Revisar beneficios de patrocinador y proponer mejoras', '2025-01-12 14:00:00', NULL, 'pendiente', NULL, 'Preparar presentación', NULL, 'alta', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(6, 1, 450, 'llamada', 'Llamada de seguimiento', '', '2025-12-01 10:10:00', NULL, 'pendiente', '', '', '2025-12-02 10:11:00', 'media', '2025-12-01 16:11:06', '2025-12-01 16:11:06'),
(7, 9, 90, 'llamada', 'Visita Comercial', 'Upgrade de membresía', '2025-12-01 11:00:00', NULL, 'pendiente', '', '', NULL, 'alta', '2025-12-01 17:01:52', '2025-12-01 17:01:52'),
(8, 8, 713, 'whatsapp', '¿Ya se registró al evento del próximo jueves?', 'Recuerde el brunch con nuestro presidente municipal y el secretario de desarrollo económico', '2025-12-01 15:10:00', '2025-12-01 17:09:21', 'completada', '', '', NULL, 'alta', '2025-12-01 21:04:00', '2025-12-01 23:09:21'),
(9, 9, 31, 'llamada', 'Visita al restaurante Chilis', '', '2025-12-01 17:50:00', NULL, 'pendiente', '', '', NULL, 'media', '2025-12-01 23:57:12', '2025-12-01 23:57:12'),
(10, 8, 713, 'whatsapp', 'Invitar al evento de Mujeres Empresarias', 'Enviar liga de registro', '2025-12-05 09:01:00', NULL, 'pendiente', '', 'Confirmar su registro en la liga del evento', '2025-12-08 22:00:00', 'alta', '2025-12-02 00:25:48', '2025-12-02 00:25:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `affiliations`
--

CREATE TABLE `affiliations` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `membership_type_id` int(10) UNSIGNED NOT NULL,
  `affiliate_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Usuario afiliador que realizó la venta',
  `affiliation_date` date NOT NULL,
  `expiration_date` date NOT NULL,
  `status` enum('active','expired','cancelled','pending_payment') COLLATE utf8_unicode_ci DEFAULT 'active',
  `payment_status` enum('paid','pending','partial') COLLATE utf8_unicode_ci DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sticker_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `affiliation_type` enum('MEMBRESIA','SIEM','OTRO') COLLATE utf8_unicode_ci DEFAULT 'MEMBRESIA',
  `invoice_status` enum('invoiced','pending','not_required') COLLATE utf8_unicode_ci DEFAULT 'pending',
  `invoice_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Path to attached invoice file',
  `benefits_enabled_date` date DEFAULT NULL COMMENT 'Date when benefits were enabled',
  `closed_by_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'User who closed the sale (may differ from affiliate_user_id)',
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `affiliations`
--

INSERT INTO `affiliations` (`id`, `contact_id`, `membership_type_id`, `affiliate_user_id`, `affiliation_date`, `expiration_date`, `status`, `payment_status`, `amount`, `payment_method`, `payment_reference`, `invoice_number`, `receipt_number`, `sticker_number`, `affiliation_type`, `invoice_status`, `invoice_file`, `benefits_enabled_date`, `closed_by_user_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, '2024-01-15', '2025-01-10', 'active', 'paid', 5000.00, 'Transferencia', NULL, NULL, NULL, NULL, 'MEMBRESIA', 'invoiced', NULL, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(2, 2, 3, 2, '2024-02-20', '2025-02-15', 'active', 'paid', 15000.00, 'Tarjeta', NULL, NULL, NULL, NULL, 'MEMBRESIA', 'invoiced', NULL, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(3, 3, 1, 3, '2024-03-10', '2025-03-05', 'active', 'paid', 2500.00, 'Efectivo', NULL, NULL, NULL, NULL, 'MEMBRESIA', 'invoiced', NULL, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(4, 5, 4, 3, '2024-01-01', '2024-12-27', 'active', 'paid', 50000.00, 'Transferencia', NULL, NULL, NULL, NULL, 'MEMBRESIA', 'invoiced', NULL, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(5, 6, 2, 2, '2023-06-15', '2024-06-10', 'expired', 'paid', 5000.00, 'Transferencia', NULL, NULL, NULL, NULL, 'MEMBRESIA', 'invoiced', NULL, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(6, 7, 3, 2, '2024-04-01', '2025-03-27', 'active', 'paid', 15000.00, 'Tarjeta', NULL, NULL, NULL, NULL, 'MEMBRESIA', 'invoiced', NULL, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(7, 9, 1, 2, '2024-05-20', '2025-05-15', 'active', 'paid', 2500.00, 'Efectivo', NULL, NULL, NULL, NULL, 'MEMBRESIA', 'pending', NULL, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(8, 713, 3, 1, '2025-11-25', '2026-11-20', 'active', 'paid', 15000.00, 'Transferencia', NULL, '34r43252452542', NULL, NULL, 'MEMBRESIA', 'pending', NULL, NULL, NULL, '', '2025-11-25 19:11:35', '2025-11-30 15:30:08'),
(9, 715, 1, 1, '2025-11-25', '2026-11-20', 'active', 'paid', 2500.00, 'Efectivo', NULL, NULL, NULL, NULL, 'MEMBRESIA', 'pending', NULL, NULL, NULL, NULL, '2025-11-25 20:19:57', '2025-11-25 20:19:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `table_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `record_id` int(10) UNSIGNED DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'system_update', NULL, NULL, NULL, '{\"version\": \"1.1.0\", \"modules_added\": [\"memberships\", \"financial\", \"import\", \"audit\", \"requirements\"]}', '127.0.0.1', NULL, '2025-11-25 05:10:25'),
(2, 1, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"password_recovery\", \"ccq_csv_template_support\", \"affiliations_enhancements\"], \"version\": \"1.2.0\"}', '127.0.0.1', NULL, '2025-11-25 06:21:48'),
(3, 1, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"user_address_field\", \"event_catalogs\", \"event_registrations_tickets\"], \"version\": \"1.3.0\"}', '127.0.0.1', NULL, '2025-11-25 13:02:56'),
(4, 1, 'import_companies', 'contacts', 0, NULL, '{\"type\": \"prospecto\", \"errors\": 1, \"imported\": 0}', '187.145.46.170', NULL, '2025-11-25 13:24:01'),
(5, 1, 'import_companies', 'contacts', 0, NULL, '{\"type\": \"afiliado\", \"errors\": 1, \"imported\": 879}', '187.145.46.170', NULL, '2025-11-25 13:25:02'),
(6, 1, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"financial_categories\", \"financial_transactions\", \"requirement_categories\", \"search_indexes\"], \"version\": \"1.4.0\"}', '127.0.0.1', NULL, '2025-11-25 15:34:10'),
(7, 1, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"events.free_for_affiliates\", \"event_registrations.tickets\", \"event_registrations.qr_code\", \"event_registrations.qr_sent\", \"event_registrations.confirmation_sent\", \"event_registrations.registration_code\", \"indexes\"], \"version\": \"1.5.0\"}', '127.0.0.1', NULL, '2025-11-25 20:04:23'),
(8, 1, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"event_registrations.razon_social\", \"event_registrations.nombre_empresario_representante\", \"event_registrations.nombre_asistente\", \"event_registrations.categoria_asistente\", \"event_registrations.email_asistente\", \"event_registrations.whatsapp_asistente\", \"event_registrations.requiere_pago\", \"indexes_for_performance\"], \"version\": \"1.6.0\"}', '127.0.0.1', NULL, '2025-11-25 22:48:14'),
(9, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"events.promo_price\", \"events.promo_end_date\", \"events.event_type updated\", \"contacts.contact_type updated\", \"event_categories table\", \"event_type_catalog table\", \"event_registrations fields\", \"config additions\"], \"version\": \"1.6.0\"}', '127.0.0.1', NULL, '2025-11-27 13:18:05'),
(10, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"events.promo_price\", \"events.promo_end_date\", \"events.event_type updated\", \"contacts.contact_type updated\", \"event_categories table\", \"event_type_catalog table\", \"event_registrations.is_guest\", \"event_registrations.is_owner_representative\", \"event_registrations.attendee_name\", \"event_registrations.attendee_position\", \"event_registrations.attendee_phone\", \"event_registrations.attendee_email\", \"event_registrations.additional_attendees\", \"event_registrations.total_amount\", \"config additions\"], \"version\": \"1.6.0\"}', '127.0.0.1', NULL, '2025-11-27 15:56:56'),
(11, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"contacts.contact_type updated to include invitado\", \"event_registrations.is_courtesy_ticket added\", \"qr_api_provider default changed to qrserver\"], \"version\": \"1.7.0\"}', '127.0.0.1', NULL, '2025-11-27 19:17:55'),
(12, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"events.promo_member_price added (Precio Preventa Afiliado)\", \"event_registrations.email_type_sent added\", \"Guest ticket restriction enforced (1 ticket max for guests)\", \"HTML email templates for pending payment and access tickets\", \"Presale pricing logic with 4 price tiers\"], \"version\": \"1.8.0\"}', '127.0.0.1', NULL, '2025-11-27 23:23:29'),
(13, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Added local PHP QR code generator as fallback\", \"QR generation now works without external API access\", \"Improved QR generation reliability for all event registrations\"], \"version\": \"1.9.0\"}', '127.0.0.1', NULL, '2025-11-28 00:27:28'),
(14, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Moved guest checkbox below tickets\", \"Added owner_name field to registration\", \"Added print functionality to emails and QR display\", \"Improved attendance control to show owner_name from contacts\", \"Fixed QR scanner camera initialization\", \"Added public payment and printable ticket pages\", \"Fixed payment links in notification emails\", \"Added .htaccess for QR directory public access\"], \"version\": \"2.0.0\"}', '127.0.0.1', NULL, '2025-11-28 17:24:50'),
(15, NULL, 'schema_update', 'event_registrations', NULL, NULL, '{\"changes\": [\"Added guest_type column for guest type selection (INVITADO, FUNCIONARIO PÚBLICO, OTRO)\", \"Added parent_registration_id column for additional attendee individual reg\"], \"version\": \"2.1.0\"}', '127.0.0.1', NULL, '2025-11-28 19:46:15'),
(16, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Added modal dialogs for QR Scanner and Manual Entry buttons\", \"Fixed contact_type display in attendance list\", \"Added system logo to email templates\", \"Fixed button styles in emails for better compatibility\", \"Escape key closes modals\"], \"version\": \"2.2.0\"}', '127.0.0.1', NULL, '2025-11-28 21:24:51'),
(17, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Added position field to contacts table\", \"Email now sent to both company email and attendee email\", \"Blue buttons now use configurable primary color\", \"Additional attendees properly classified as colaborador\", \"Event attendees registered as colaborador_empresa contacts\"], \"version\": \"2.3.0\"}', '127.0.0.1', NULL, '2025-11-29 03:34:37'),
(18, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Added position field to contacts table\", \"Email now sent to both company email and attendee email\", \"Blue buttons now use configurable primary color\", \"Additional attendees properly classified as colaborador\", \"Event attendees registered as colaborador_empresa contacts\"], \"version\": \"2.3.0\"}', '127.0.0.1', NULL, '2025-11-29 05:38:34'),
(19, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Enhanced afiliador dashboard with sales charts and appointments\", \"Company digital file dashboard with benefits and event history\", \"New model methods for sales statistics by period\", \"Improved upcoming agenda and prospect management\"], \"version\": \"2.4.0\"}', '127.0.0.1', NULL, '2025-11-29 05:39:21'),
(20, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Enhanced Customer Journey with 6 stages\", \"Upselling invitation tracking table\", \"Council eligibility tracking\", \"Invoice attachment support\", \"Benefits enablement date\"], \"version\": \"2.5.0\"}', '127.0.0.1', NULL, '2025-11-29 08:46:57'),
(21, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Renamed Expediente Digital Único to Expediente Digital Afiliado (EDA)\", \"Added person_type (fisica/moral) based on RFC length\", \"Complete NIZA 45-class classification with OTRA CATEGORÍA option\", \"Enhanced upselling invitations with WhatsApp and email messaging\", \"Added message content tracking for upselling invitations\"], \"version\": \"2.6.0\"}', '127.0.0.1', NULL, '2025-11-30 17:23:10'),
(22, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Created unified Agenda y Acciones Comerciales section\", \"Merged Agenda, Notifications, and Commercial Requirements functionality\", \"Added user_activity_log table for tracking off-hours work\", \"Added motivational_messages table\", \"Extended activity types for affiliator workflow\", \"Added performance_goals table\", \"Updated sidebar navigation\"], \"version\": \"2.7.0\", \"deprecated\": [\"Standalone Agenda section (routes still work for backward compatibility)\", \"Standalone Notifications section (merged into unified section)\", \"Standalone Requerimientos section (merged into unified section)\"]}', '127.0.0.1', NULL, '2025-11-30 18:39:10'),
(23, NULL, 'schema_update', NULL, NULL, NULL, '{\"changes\": [\"Added SIEM category to services for Up Selling\", \"Added characteristics field to membership_types\", \"Added prospecto and evento notification types\", \"Added Visionario membership type\", \"Updated membership benefits inheritance logic\", \"Enhanced search to use phrase matching\", \"Enhanced expedientes view with search and affiliator filter\", \"Enhanced notifications dropdown in header with categories\"], \"version\": \"2.8.0\"}', '127.0.0.1', NULL, '2025-12-01 12:24:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `benefit_usage`
--

CREATE TABLE `benefit_usage` (
  `id` int(10) UNSIGNED NOT NULL,
  `affiliation_id` int(10) UNSIGNED NOT NULL,
  `benefit_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `usage_date` date NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `commercial_requirements`
--

CREATE TABLE `commercial_requirements` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `contact_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'Usuario asignado',
  `priority` enum('low','medium','high') COLLATE utf8_unicode_ci DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8_unicode_ci DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT '0.00',
  `category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `commercial_requirements`
--

INSERT INTO `commercial_requirements` (`id`, `title`, `description`, `contact_id`, `user_id`, `priority`, `status`, `due_date`, `budget`, `category`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Renovación membresía PYME - Comercializadora del Centro', 'Seguimiento a renovación de membresía próxima a vencer', 1, 2, 'high', 'pending', '2025-12-09', 5000.00, 'renovacion', NULL, '2025-11-25 05:10:25', '2025-11-25 05:10:25'),
(2, 'Servicio de gestoría - Nuevo prospecto', 'Prospecto interesado en servicio de gestoría', NULL, 2, 'low', 'pending', '2026-01-08', 4000.00, 'servicio', NULL, '2025-11-25 05:10:25', '2025-11-25 05:10:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config`
--

CREATE TABLE `config` (
  `id` int(10) UNSIGNED NOT NULL,
  `config_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `config_value` text COLLATE utf8_unicode_ci,
  `config_type` enum('text','number','boolean','json','color','file') COLLATE utf8_unicode_ci DEFAULT 'text',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `config`
--

INSERT INTO `config` (`id`, `config_key`, `config_value`, `config_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'CRM Cámara de Comercio de Querétaro', 'text', 'Nombre del sitio', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(2, 'site_logo', '/img/logo_1764687086_logoCanaco2025.png', 'file', 'Logo del sitio', '2025-11-25 02:36:56', '2025-12-02 14:51:26'),
(3, 'primary_color', '#a1bf37', 'color', 'Color primario del sistema', '2025-11-25 02:36:56', '2025-11-25 11:40:42'),
(4, 'secondary_color', '#5e8539', 'color', 'Color secundario', '2025-11-25 02:36:56', '2025-11-25 11:40:42'),
(5, 'accent_color', '#89ab37', 'color', 'Color de acento', '2025-11-25 02:36:56', '2025-11-25 11:40:42'),
(6, 'contact_phone', '442 212 0035', 'text', 'Teléfono principal', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(7, 'contact_email', 'info@camaradecomercioqro.mx', 'text', 'Correo de contacto', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(8, 'office_hours', 'Lunes a Viernes 9:00 - 18:00', 'text', 'Horario de atención', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(9, 'address', 'Av. 5 de Febrero No. 412, Centro, 76000 Santiago de Querétaro, Qro.', 'text', 'Dirección', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(10, 'paypal_client_id', 'AWi0IaxZN-e9TQvSbc0FsZj-vHA9-38fyIBmpbQeELJgjNaRgSrGondGzDGQATilllQAlp0J2BJwJCYL', 'text', 'PayPal Client ID', '2025-11-25 02:36:56', '2025-11-25 17:04:40'),
(11, 'paypal_secret', 'ELLC6UBm2stHa0CdfvyukrZSnDtsjhxIZBxrqMZI6us4N3IOPVn54dow4RIJZ6dJBpxeMuOBA_KjdmTx', 'text', 'PayPal Secret', '2025-11-25 02:36:56', '2025-11-25 17:04:40'),
(12, 'paypal_mode', 'live', 'text', 'PayPal Mode (sandbox/live)', '2025-11-25 02:36:56', '2025-11-25 12:32:51'),
(13, 'smtp_host', 'enlacecanaco.org', 'text', 'SMTP Host', '2025-11-25 02:36:56', '2025-11-25 05:23:03'),
(14, 'smtp_port', '587', 'number', 'SMTP Port', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(15, 'smtp_user', 'crm@enlacecanaco.org', 'text', 'SMTP User', '2025-11-25 02:36:56', '2025-11-25 05:23:03'),
(16, 'smtp_password', 'Danjohn007', 'text', 'SMTP Password', '2025-11-25 02:36:56', '2025-11-25 05:23:03'),
(17, 'smtp_from_name', 'CRM CCQ', 'text', 'Nombre remitente de correos', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(18, 'qr_api_key', '', 'text', 'API Key para QR masivos', '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(19, 'whatsapp_api_key', '', 'text', 'API Key para WhatsApp Business', '2025-11-25 05:10:25', '2025-11-25 05:10:25'),
(20, 'google_maps_api_key', '', 'text', 'API Key para Google Maps', '2025-11-25 05:10:25', '2025-11-25 05:10:25'),
(21, 'qr_api_provider', 'qrserver', 'text', 'Proveedor de QR (qrserver, local). Sistema usa fallback local si API externa falla.', '2025-11-27 13:18:05', '2025-11-28 01:35:48'),
(22, 'qr_size', '350', 'text', 'Tamaño del código QR en píxeles', '2025-11-27 13:18:05', '2025-11-27 13:19:46'),
(23, 'shelly_enabled', '0', 'text', 'Habilitar integración con Shelly Relay', '2025-11-27 13:18:05', '2025-11-27 13:19:46'),
(24, 'shelly_url', '', 'text', 'URL de la API de Shelly Relay', '2025-11-27 13:18:05', '2025-11-27 13:18:05'),
(25, 'shelly_channel', '0', 'text', 'Canal del Shelly Relay a controlar (0-3)', '2025-11-27 13:18:05', '2025-11-27 13:18:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacts`
--

CREATE TABLE `contacts` (
  `id` int(10) UNSIGNED NOT NULL,
  `rfc` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `person_type` enum('fisica','moral') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Type based on RFC: fisica (13 chars, owner) or moral (12 chars, legal rep)',
  `whatsapp` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_type` enum('afiliado','exafiliado','prospecto','nuevo_usuario','funcionario','publico_general','consejero_propietario','consejero_invitado','patrocinador','mesa_directiva','invitado','colaborador_empresa') COLLATE utf8_unicode_ci DEFAULT 'nuevo_usuario',
  `business_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commercial_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `legal_representative` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `corporate_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Position/job title for company collaborators',
  `industry` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `niza_classification` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `niza_custom_category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Custom NIZA category description when class 99 (OTRA) is selected',
  `products_sells` json DEFAULT NULL COMMENT '4 principales productos que vende',
  `products_buys` json DEFAULT NULL COMMENT '2 principales productos que compra',
  `discount_percentage` decimal(5,2) DEFAULT '0.00',
  `commercial_address` text COLLATE utf8_unicode_ci,
  `fiscal_address` text COLLATE utf8_unicode_ci,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8_unicode_ci DEFAULT 'Querétaro',
  `postal_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_maps_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `whatsapp_sales` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `whatsapp_purchases` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `whatsapp_admin` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile_completion` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'Porcentaje de completitud 0-100',
  `completion_stage` enum('A','B','C') COLLATE utf8_unicode_ci DEFAULT 'A',
  `journey_stage` tinyint(3) UNSIGNED DEFAULT '1' COMMENT 'Customer Journey stage: 1-Registro, 2-Productos, 3-Facturación, 4-CrossSelling, 5-UpSelling, 6-Consejo',
  `journey_stage_updated` timestamp NULL DEFAULT NULL COMMENT 'Last journey stage update timestamp',
  `assigned_affiliate_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Afiliador asignado',
  `source_channel` enum('chatbot','alta_directa','evento_gratuito','evento_pagado','buscador','jefatura_comercial') COLLATE utf8_unicode_ci DEFAULT 'alta_directa',
  `notes` text COLLATE utf8_unicode_ci,
  `is_validated` tinyint(1) DEFAULT '0' COMMENT 'Para consejeros y mesa directiva',
  `validated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `contacts`
--

INSERT INTO `contacts` (`id`, `rfc`, `person_type`, `whatsapp`, `contact_type`, `business_name`, `commercial_name`, `owner_name`, `legal_representative`, `corporate_email`, `phone`, `position`, `industry`, `niza_classification`, `niza_custom_category`, `products_sells`, `products_buys`, `discount_percentage`, `commercial_address`, `fiscal_address`, `city`, `state`, `postal_code`, `google_maps_url`, `website`, `facebook`, `instagram`, `linkedin`, `twitter`, `whatsapp_sales`, `whatsapp_purchases`, `whatsapp_admin`, `profile_completion`, `completion_stage`, `journey_stage`, `journey_stage_updated`, `assigned_affiliate_id`, `source_channel`, `notes`, `is_validated`, `validated_by`, `created_at`, `updated_at`) VALUES
(1, 'QRO0001010ABC', 'fisica', '4421112233', 'afiliado', 'Comercializadora del Centro SA de CV', 'ComercioQro', 'Juan Pérez García', 'Juan Pérez García', 'contacto@comercioqro.mx', '442 111 2233', NULL, 'Comercio', '35', NULL, '[\"Abarrotes\", \"Productos de limpieza\", \"Papelería\", \"Plásticos\"]', '[\"Productos al mayoreo\", \"Servicios de transporte\"]', 0.00, 'Calle Corregidora #123, Centro, Querétaro', 'Calle Corregidora #123, Centro, Querétaro', 'Santiago de Querétaro', 'Querétaro', '76000', NULL, 'www.comercioqro.mx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 'C', 1, NULL, 2, 'alta_directa', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(2, 'TEC0002020DEF', 'fisica', '4422223344', 'afiliado', 'Tecnología Queretana SA de CV', 'TecQro', 'Miguel Ángel Rodríguez', 'Miguel Ángel Rodríguez', 'info@tecqro.com', '442 222 3344', NULL, 'Tecnología', '9', NULL, '[\"Computadoras\", \"Redes\", \"Servidores\", \"Software\"]', '[\"Componentes electrónicos\", \"Licencias de software\"]', 0.00, 'Av. Constituyentes #456, Centro Sur, Querétaro', 'Av. Constituyentes #456, Centro Sur, Querétaro', 'Santiago de Querétaro', 'Querétaro', '76040', NULL, 'www.tecqro.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 'C', 1, NULL, 2, 'evento_gratuito', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(3, 'ALI0003030GHI', 'fisica', '4423334455', 'afiliado', 'Alimentos del Bajío SA de CV', 'AliBajío', 'Rosa María López', 'Rosa María López', 'ventas@alibajio.mx', '442 333 4455', NULL, 'Alimentos', '29', NULL, '[\"Productos lácteos\", \"Carnes frías\", \"Conservas\", \"Bebidas\"]', '[\"Empaques\", \"Materias primas\"]', 0.00, 'Parque Industrial El Marqués, Querétaro', 'Parque Industrial El Marqués, Querétaro', 'El Marqués', 'Querétaro', '76246', NULL, 'www.alibajio.mx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 70, 'C', 1, NULL, 3, 'alta_directa', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(4, 'SER0004040JKL', 'fisica', '4424445566', 'prospecto', 'Servicios Profesionales Qro SC', 'ServPro', 'Francisco Torres Vega', 'Francisco Torres Vega', 'contacto@servproqro.com', '442 444 5566', NULL, 'Servicios', '35', NULL, '[\"Consultoría\", \"Asesoría legal\", \"Contabilidad\", \"Capacitación\"]', '[\"Software especializado\", \"Equipos de oficina\"]', 0.00, 'Blvd. Bernardo Quintana #200, Centro', 'Blvd. Bernardo Quintana #200, Centro', 'Santiago de Querétaro', 'Querétaro', '76050', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 35, 'B', 1, NULL, 2, 'chatbot', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(5, 'IND0005050MNO', 'fisica', '4425556677', 'afiliado', 'Industrias Queretanas SA de CV', 'IndQro', 'Alberto Méndez Ruiz', 'Alberto Méndez Ruiz', 'info@indqro.com.mx', '442 555 6677', NULL, 'Manufactura', '7', NULL, '[\"Maquinaria industrial\", \"Refacciones\", \"Mantenimiento\", \"Instalaciones\"]', '[\"Acero\", \"Componentes importados\"]', 0.00, 'Parque Industrial Querétaro, El Marqués', 'Parque Industrial Querétaro, El Marqués', 'El Marqués', 'Querétaro', '76246', NULL, 'www.indqro.com.mx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 'C', 1, NULL, 3, 'jefatura_comercial', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(6, 'HOT0006060PQR', 'fisica', '4426667788', 'exafiliado', 'Hotelería Queretana SA de CV', 'HotelQro', 'Carmen Gutiérrez', 'Carmen Gutiérrez', 'reservas@hotelqro.mx', '442 666 7788', NULL, 'Turismo', '43', NULL, '[\"Hospedaje\", \"Eventos\", \"Restaurante\", \"Tours\"]', '[\"Alimentos perecederos\", \"Productos de limpieza\"]', 0.00, 'Av. Universidad #500, Centro', 'Av. Universidad #500, Centro', 'Santiago de Querétaro', 'Querétaro', '76010', NULL, 'www.hotelqro.mx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 'C', 1, NULL, 2, 'alta_directa', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(7, 'MED0007070STU', 'fisica', '4427778899', 'afiliado', 'Centro Médico Querétaro SA de CV', 'MediQro', 'Dr. Luis Fernández', 'Dr. Luis Fernández', 'citas@mediqro.com', '442 777 8899', NULL, 'Salud', '44', NULL, '[\"Consultas médicas\", \"Análisis clínicos\", \"Cirugías\", \"Rehabilitación\"]', '[\"Medicamentos\", \"Equipo médico\"]', 0.00, 'Av. 5 de Febrero #1000, Centro', 'Av. 5 de Febrero #1000, Centro', 'Santiago de Querétaro', 'Querétaro', '76000', NULL, 'www.mediqro.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 70, 'C', 1, NULL, 2, 'buscador', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(8, 'REST0008080VW', 'fisica', '4428889900', 'prospecto', 'Restaurantes del Centro SC', 'RestCentro', 'Chef Manuel García', 'Manuel García Pérez', 'contacto@restcentro.mx', '442 888 9900', NULL, 'Alimentos y Bebidas', '43', NULL, '[\"Comida mexicana\", \"Catering\", \"Banquetes\", \"Cafetería\"]', '[\"Alimentos frescos\", \"Bebidas\"]', 0.00, 'Andador 5 de Mayo #50, Centro', 'Andador 5 de Mayo #50, Centro', 'Santiago de Querétaro', 'Querétaro', '76000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, 3, 'evento_pagado', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(9, 'AUTO0009090YZ', 'fisica', '4429990011', 'afiliado', 'Automotriz Querétaro SA de CV', 'AutoQro', 'Jorge Ramírez López', 'Jorge Ramírez López', 'ventas@autoqro.mx', '442 999 0011', NULL, 'Automotriz', '12', NULL, '[\"Venta de autos\", \"Servicio mecánico\", \"Refacciones\", \"Accesorios\"]', '[\"Vehículos nuevos\", \"Refacciones importadas\"]', 0.00, 'Av. Tecnológico #800, Centro Norte', 'Av. Tecnológico #800, Centro Norte', 'Santiago de Querétaro', 'Querétaro', '76030', NULL, 'www.autoqro.mx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 'C', 1, NULL, 2, 'alta_directa', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(10, 'CONS0010010BC', 'fisica', '4420001122', 'funcionario', 'Secretaría de Desarrollo Económico', 'SEDEQ', 'Lic. Ana María Soto', NULL, 'contacto@sedeq.gob.mx', '442 000 1122', NULL, 'Gobierno', NULL, NULL, NULL, NULL, 0.00, 'Palacio de Gobierno, Centro', NULL, 'Santiago de Querétaro', 'Querétaro', '76000', NULL, 'www.queretaro.gob.mx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 35, 'B', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-25 02:36:56', '2025-11-30 17:23:10'),
(11, 'DLI931201M19', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA LIVERPOOL SA DE CV', NULL, 'SANDRA LUZ MURGA GOMEZ', NULL, 'elopeza@liverpool.com.mx', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(12, 'JCR680921L80', 'moral', NULL, 'afiliado', 'JOSE CHAVEZ RUIZ E HIJOS', NULL, 'GERARDO ALBARRAN', NULL, 'llanteraqueretanaqro@hotmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(13, 'MEPC820803HD4', 'fisica', NULL, 'afiliado', 'MENDOZA PIMENTEL CUSTODIO', NULL, 'CUSTODIO MENDOZA PIMENTEL', NULL, 'fenixtransmisiones@outlook.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(14, 'VQU040820K65', 'moral', NULL, 'afiliado', 'VERSAFLEX QUERETARO SA DE CV', NULL, 'IVONNE NIEVES', NULL, 'admonistracion@lonasversaflex.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(15, 'CBB150724294', 'moral', NULL, 'afiliado', 'CLEANING BRANDS DEL BAJIO S.A. DE C.V.', NULL, 'LIC. MIGUEL ANGEL TINAJERO', NULL, 'ventas@cleaning-brands.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(16, 'BIM011108DJ5', 'moral', NULL, 'afiliado', 'BIMBO SA DE CV', NULL, 'YARELI MUÑOZ CHAVEZ', NULL, 'yareli.muñoz@grupobimbo.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(17, 'BES160503J91', 'moral', NULL, 'afiliado', 'BP ESTACIONES DE SERVICIOS ENERGETICOS SA DE CV', NULL, 'MIGUEL ANGEL PATIÑO', NULL, 'ameasys@csma.com.mx', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(18, 'EIML750104D56', 'fisica', NULL, 'afiliado', 'MA. DE LA LUZ ESPINOZA MURILLO', NULL, 'MA DE LA LUZ MURILLO', NULL, 'luz_espinosa1@yahoo.com.mx', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(19, 'HCP1707286E4', 'moral', NULL, 'afiliado', 'HC PROMEDICAL SA DE CV', NULL, 'IGNACIO ANDRADE', NULL, 'iandrade@hcpromedical.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(20, 'CSC931026M35', 'moral', NULL, 'afiliado', 'CONSUMOS Y SURTIDOS PARA LA CONSTRUCCION', NULL, 'LIC. ANTONIO', NULL, 'consurtidos@hotmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(21, 'MDE120612LD8', 'moral', NULL, 'afiliado', 'MACRO DISTRIBUIDORA ELECTRICA MRJ SA DE CV', NULL, 'LIC. ANTONIO PEREZ', NULL, 'recursoshumanos@macro_MRJ.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(22, 'OEMJ750225D96', 'fisica', NULL, 'afiliado', 'JOSE JUAN OLVERA NUÑEZ', NULL, 'JUAN A GUERRERO', NULL, 'josejuanolvera66@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(23, 'LUTM850622QG0', 'fisica', NULL, 'afiliado', 'MARITZA DELFINA LARA TORRES', NULL, 'ALEJANDRA MARTINEZ', NULL, 'carlos_luna7@live.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(24, 'LAV981026JIA', 'moral', NULL, 'afiliado', 'LLAVANTE', NULL, 'MARIO VILLAFAÑA', NULL, 'avanteplateros@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(25, 'GARF020127', NULL, NULL, 'afiliado', 'MARIA FERNANDA GARCIA RIOS', NULL, 'MARIA FERNANDA GARCIA', NULL, 'mgarciarico7@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(26, 'GUSM880301', NULL, NULL, 'afiliado', 'MARISSA GUILLEN SANCHEZ', NULL, 'MARISSA GUILLEN', NULL, 'maichita_152@hotmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(27, 'MOS0780516', NULL, NULL, 'afiliado', 'OSVALDO MORALES SILVA', NULL, 'OSVALDO M.S', NULL, 'cuarentero37@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(28, 'LOMP601102', NULL, NULL, 'afiliado', 'PATRICIA LOPEZ MONTES', NULL, 'PATRICIA MONTES', NULL, 'ninfatanuzfl@hotmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(29, 'MARJ770430FL6', 'fisica', NULL, 'afiliado', 'JAVIER MARTINEZ RODRIGUEZ', NULL, 'JAVIER HERNNDEZ RODRIGUEZ', NULL, 'aceros_elpanteraqoutlook.es', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(30, 'GAJY920523HM2', 'fisica', NULL, 'afiliado', 'YOLANDA GARCIA JIMENEZ', NULL, 'YOLANDA GARCIA', NULL, 'mclaminas.sureste@hotmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(31, 'ROPJ800619CV8', 'fisica', NULL, 'afiliado', 'JUVENAL DE JESUS ROGEL PEREZ', NULL, 'JUVENAL ROGEL', NULL, 'rotec.climasyservicios@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(32, 'HEVJ930717G31', 'fisica', NULL, 'afiliado', 'JESUS ALEJANDRO HERNANDEZ VIEIRA', NULL, 'ALEJANDRO SANCHEZ', NULL, 'jes.he.v93@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(33, 'AOCJ860108E21', 'fisica', NULL, 'afiliado', 'JOSE DE JESUS AGUILAR CASTRO', NULL, 'IVONNE FLORES', NULL, 'tiendaescobedo@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(34, 'RAGJ930609', NULL, NULL, 'afiliado', 'JULIO CESAR RAMOS GONZALEZ', NULL, 'JULIO C RAMOS', NULL, 'juanrms538@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(35, 'MECA631020', NULL, NULL, 'afiliado', 'ABEL MEJIA CARDENAS', NULL, 'ABEL MEJIA', NULL, 'abelmejiacar@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(36, 'METE890427', NULL, NULL, 'afiliado', 'EDGAR MEJIA TOVAR', NULL, 'EDGAR MEJIA', NULL, 'edgarmejiaemt@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(37, 'RCA801209MD8', 'moral', NULL, 'afiliado', 'REFACCIONARIA CALIFORNIA S.A DE C.V.', NULL, 'LIC JUAN A. VERTIZ/ LIC CARLOS A.', NULL, 'cpr.gestor@corprama.com.mx', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(38, 'LAE1709145C4', 'moral', NULL, 'afiliado', 'LUDICOS Y EXTERIORES SA DE CV', NULL, 'MARIBEL GONZÁLEZ', NULL, 'mgonzalez@jogare.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(39, 'LDI191021HW1', 'moral', NULL, 'afiliado', 'LIFEX DISTRIBUCION SAPI DE CV', NULL, 'ROSALINDA SORDOLEMUS', NULL, 'rsordolemus@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(40, 'DHC000615LN8', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA HIDRAULICA DEL CENTRO', NULL, 'JUANA GUERRERO', NULL, 'comprasdhcsa@gmail.com', NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 22, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(41, 'CDC060928EA6', 'moral', NULL, 'afiliado', 'CORPORATIVO DULCERO DEL CENTRO', NULL, 'CAROLINA-JONATHAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(42, 'CLO990423UX8', 'moral', NULL, 'afiliado', 'LA CASA DE LAS LOMAS', NULL, 'LIC. GABRIELA HUERTA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(43, 'GEC230919VS9', 'moral', NULL, 'afiliado', 'GDEXPO COLCHONES COLCHONES', NULL, 'LIC. GABRIELA HUERTA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(44, 'MOC051020MT8', 'moral', NULL, 'afiliado', 'MAVI DE OCCIDENTE/ EL PUEBLITO', NULL, 'YUBICELI ARIZMENDI', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(45, 'DME920612EE8', 'moral', NULL, 'afiliado', 'DYNAGEAR DE MEXICO', NULL, 'ING MARTIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(46, 'AEOR65091', NULL, NULL, 'afiliado', 'MA REMEDIOS ANGELES UGALDE', NULL, 'REMEDIOS A OLALDE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(47, 'DLI931201MI9', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA LIVERPOOL SA DE CV', NULL, 'FRANCISCO OMAR MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(48, 'BIO130826PP3', 'moral', NULL, 'afiliado', 'BIOTONER S DE RL DE CV', NULL, 'CP RAUL LUNA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(49, 'MJS0609252B4', 'moral', NULL, 'afiliado', 'MODA JOVEN SFERA DE MESXICO SA DE CV', NULL, 'ALEJANDRA P MORENO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(50, 'CAN940705AM7', 'moral', NULL, 'afiliado', 'COMERCIAL ANFORAMA SA DE CV', NULL, 'SR ANTONIO CRUZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(51, 'PMU940317114', 'moral', NULL, 'afiliado', 'PROMOTORA MUSICAL SA DE CV', NULL, 'GERARDO BERNARD', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(52, 'TCU950113C58', 'moral', NULL, 'afiliado', 'TIENDAS CUADRA SA DE CV', NULL, 'CRISTIAN LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(53, 'BTB9508118Y6', 'moral', NULL, 'afiliado', 'BARI HORA TRAJES DE BAÑO SA DE CV', NULL, 'MARIA FERNANDA CASTILLO V.', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(54, 'MCF850801GR3', 'moral', NULL, 'afiliado', 'MANUFACTURA DE CALZADO FINO SA DE CV', NULL, 'MARCO LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(55, 'DVC850325FH5', 'moral', NULL, 'afiliado', 'DIST. Y VENDEDORES DEL CENTRO', NULL, 'BEATRIZ MONTOYA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(56, 'CCO9911037E9', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA COQUETA SA DE CV', NULL, 'MONICA GUERRERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(57, 'MOC0510204T8', 'moral', NULL, 'afiliado', 'MAVI DE OCCIDENTE SA DE CV', NULL, 'LIC. ANDREA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(58, 'MOC051020', NULL, NULL, 'afiliado', 'MAVIC DE OCCIDENTE SA DE CV', NULL, 'CAROLINA LUCERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(59, 'SUK931216N38', 'moral', NULL, 'afiliado', 'SUKARNE', NULL, 'KAREN MENDOZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(60, 'VINS870221A22', 'fisica', NULL, 'afiliado', 'JUAN JOSE VILLARREAL NOVA', NULL, 'NADIA MTZ ARROYO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(61, 'MOC051020NT8', 'moral', NULL, 'afiliado', 'MAVI DE OCCIDENTE', NULL, 'VICTOR VALLE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(62, 'LEH1281050426', 'fisica', NULL, 'afiliado', 'RENE LEAL HERNANDEZ', NULL, 'KAREN RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(63, 'FCA980701L75', 'moral', NULL, 'afiliado', 'FABRICAS DE CALZADO ANDREA', NULL, 'NOHEMI CHAVEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(64, 'FCO181218N45', 'moral', NULL, 'afiliado', 'FACHADISMO COMERCIALIZACION', NULL, 'ANA REYES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(65, 'EBS200319DG2', 'moral', NULL, 'afiliado', 'EQUIPOS Y BOMBAS SYA', NULL, 'JUAN ANTONIO SUAREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(66, 'PGX130405PQ0', 'moral', NULL, 'afiliado', 'PVC G3', NULL, 'ADRIANA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(67, 'VAOA860520', NULL, NULL, 'afiliado', 'ADRIANA VALLADARE OLVVERA', NULL, 'ADRIANA VALLADARES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(68, 'CPA190501LR9', 'moral', NULL, 'afiliado', 'CARPI PONEL S DE RL DE CV', NULL, 'EDUARDO RAMIREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(69, 'PUM920401H18', 'moral', NULL, 'afiliado', 'PROVEEDORA UNIVERSAL DE MANGUERAS', NULL, 'JOHANA BELTRON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(70, 'TIM0901129EH4', 'fisica', NULL, 'afiliado', 'TECNOLOGIA INDUSTRIAL MEVI', NULL, 'LAURA TRUJILLO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(71, 'GOPV721113N81', 'fisica', NULL, 'afiliado', 'VERONICA GONZALEZ PARRA', NULL, 'ADRIANA QUEVEDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(72, 'AIAP6604296R6', 'fisica', NULL, 'afiliado', 'PATRICIA ARVIZU AVILA', NULL, 'TATY ARVIZU', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(73, 'VIC711030R47', 'moral', NULL, 'afiliado', 'VICMA SA', NULL, 'ARTURO SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(74, 'APL920424PD5', 'moral', NULL, 'afiliado', 'ACRILICOS PLATITEC SA DE CV', NULL, 'ANGELICA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(75, 'STA811216GR5', 'moral', NULL, 'afiliado', 'EL SURTIDOR DEL TAPICERO SA DE CV', NULL, 'GERARDO GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(76, 'CAAR6108097P6', 'fisica', NULL, 'afiliado', 'ROMAN CAMPOS ALBARRAN', NULL, 'ROMAN CAMPOS ALBARRAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(77, 'SAN791101NV9', 'moral', NULL, 'afiliado', 'SANCHEZ SA DE CV', NULL, 'VICTOR AGUIRRE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(78, 'YOFY710603MC6', 'fisica', NULL, 'afiliado', 'YAZMIN YONG FONSECA', NULL, 'YAZMIN YONG', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(79, 'CLM9407017W4', 'moral', NULL, 'afiliado', 'CRISA LIBBEY MEXICO S DE RL DE CV', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(80, 'GJA091117BJ6', 'moral', NULL, 'afiliado', 'GRUPO JARBUS SA DE CV', NULL, 'JESSICA HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(81, 'MEX010913Q41', 'moral', NULL, 'afiliado', 'MEDICAMENTOS EXCLUSIVOS SA DE CV', NULL, 'MAGDALENA RIOFRIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(82, 'TCU950113C56', 'moral', NULL, 'afiliado', 'TIENDAS CUADRA SA DE CV', NULL, 'VIRIDIANA RESENDIZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(83, 'PEY1102087U', NULL, NULL, 'afiliado', 'PEYCASH SA DE CV', NULL, 'CASANDRA ALVAREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(84, 'CME220205UD7', 'moral', NULL, 'afiliado', 'CARCAMOVIL MEXICO SA DE CV', NULL, 'CASANDRA ALVAREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(85, 'SCH211108135', 'moral', NULL, 'afiliado', 'SPA LIMPIEZA E HIGIENE', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(86, 'CSC920619BN3', 'moral', NULL, 'afiliado', 'CARNES SELCTAS LOS CORREA', NULL, 'GABY CORREA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(87, 'LOG106208LX5', 'moral', NULL, 'afiliado', 'LOGICAMEX S DE RL DE CV', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(88, 'OLK051216J90', 'moral', NULL, 'afiliado', 'OLKISA SA DE CV', NULL, 'JAVIER RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(89, 'TDM140507669', 'moral', NULL, 'afiliado', 'TRANSMISIONES Y DIFERENCIALES', NULL, 'ITZEL ANTONIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(90, 'CNO930113K12', 'moral', NULL, 'afiliado', 'SIGMA FOODSERVICE COMERCIAL', NULL, 'GABRIELA MORENO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(91, 'MIB820517DS8', 'moral', NULL, 'afiliado', 'MATERIALES INDUSTRIALES DEL BAJIO', NULL, 'ENRIQUE BARRERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(92, 'SPI851203E80', 'moral', NULL, 'afiliado', 'SPIN SA DE CV', NULL, 'LIC. RAQUEL ANGEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(93, 'CLUG771114SUZ', 'fisica', NULL, 'afiliado', 'GRISELDA CRUZ LOPEZ', NULL, 'GRISELDA CRUZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(94, 'JFM000901BF6', 'moral', NULL, 'afiliado', 'JAMES FARRELL MEXICO S DE RL DE CV', NULL, 'LIC. LORALY', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(95, 'SQC850528RU8', 'moral', NULL, 'afiliado', 'SURTIDOR QUIMICO DEL CENTRO', NULL, 'C.P. MONICA CHAVEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(96, 'DGQ9611278N9', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA GOBA DE QUERETARO', NULL, 'LIC ANGELICA JIMENEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(97, 'GAPJ20502EI3', 'moral', NULL, 'afiliado', 'JOSEFINA GARCIA PALLARES', NULL, 'JOSEFINA GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(98, 'AOCR980607366', 'fisica', NULL, 'afiliado', 'RICKY HANSON ANTONIO CIGARROA', NULL, 'RICKY HANSON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(99, 'CME910715UB9', 'moral', NULL, 'afiliado', 'COSTCO DE MEXICO SA DE CV', NULL, 'LIC. AIME ALMANZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(100, 'CMB9802181M3', 'moral', NULL, 'afiliado', 'CENTRO METALICO DEL BAJÍO SA DE CV', NULL, 'C.P. ALEJANDRO LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(101, 'PDE010131BM2', 'moral', NULL, 'afiliado', 'PRODUCTOS DEPORTIVOS SA DE CV', NULL, 'LIC BRICIA NOYOLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(102, 'AMF940707EP6', 'moral', NULL, 'afiliado', 'ALMACENES ANFORA SA DE CV', NULL, 'LIC. DULCE LIPRANDI', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(103, 'ANF940707EP6', 'moral', NULL, 'afiliado', 'SURTODO SA DE CV', NULL, 'LIC DULCE LIPRANDI', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(104, 'LOHC700603G22', 'fisica', NULL, 'afiliado', 'JOSE CARLOS LOPEZ HURTADO', NULL, 'JOSE CARLOS LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(105, 'RIVR870809D25', 'fisica', NULL, 'afiliado', 'REYNA GUADALUPE RICO', NULL, 'REYNA GUADALUPE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(106, 'VMI050120AU6', 'moral', NULL, 'afiliado', 'MICRAS INTERNACIONAL SA DE CV', NULL, 'EDGAR CRUZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(107, 'DIN971119GG6', 'moral', NULL, 'afiliado', 'DISTRIBUCION INTERCERAMIC SA DE CV', NULL, 'BLANCA G MARTINEZ MONERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(108, 'IBA1811296X8', 'moral', NULL, 'afiliado', 'INGENIERIA EN BASCULAS S DE RL DE CV', NULL, 'ING BENJAMIN FRAGOSO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(109, 'OECL590618Q21', 'fisica', NULL, 'afiliado', 'JOSE LUIS ARMANDO OLVERA CABRERA', NULL, 'JOSE LUIS OLVERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(110, 'COS030113782', 'moral', NULL, 'afiliado', 'COSTITX SA DE CV', NULL, 'ALEXIA VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(111, 'DIV980601NG0', 'moral', NULL, 'afiliado', 'DIVERTICALZADOS', NULL, 'ALEXIA VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(112, 'IPI871022QHA', 'moral', NULL, 'afiliado', 'INDUSTRIAS PIAGUI SA DE CV', NULL, 'ALEXIA VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(113, 'GARJ790425TF3', 'fisica', NULL, 'afiliado', 'JUAN CARLOS GARCIA RODRIGUEZ', NULL, 'JUAN CARLOS GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(114, 'TEC090415F14', 'moral', NULL, 'afiliado', 'TECNOALIMENTOS', NULL, 'NAHUM FUENTES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(115, 'LOM0610166', NULL, NULL, 'afiliado', 'L`OCCITANE MEXICO SA DE CV', NULL, 'YENI MORELOS VARGAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(116, 'CACY710921FD0', 'fisica', NULL, 'afiliado', 'YOLANDA CALVO CORONADO', NULL, 'YOLANDO CALVO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(117, 'ABO950601e03', 'moral', NULL, 'afiliado', 'ALIMENTOS BOLONIA SA DE CV', NULL, 'FERANDO SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(118, 'RORS880801118', 'fisica', NULL, 'afiliado', 'SAUL ROSAS RIVERA', NULL, 'SAUL ROSAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(119, 'TTI961202IM1', 'moral', NULL, 'afiliado', 'TONY TIENDAS SA DE CV', NULL, 'LIC. TRINIDAD', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(120, 'AME970109GQ0', 'moral', NULL, 'afiliado', 'AUTOZONE DE MEXICO S DE RL DE CV', NULL, 'LIC. ANA JIMENEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(121, 'IGE050429R11', 'moral', NULL, 'afiliado', 'IMS GEAR', NULL, 'MARY CABALLERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(122, 'FCA9901297P6', 'moral', NULL, 'afiliado', 'FIRST CASH', NULL, 'ROSARIO SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10');
INSERT INTO `contacts` (`id`, `rfc`, `person_type`, `whatsapp`, `contact_type`, `business_name`, `commercial_name`, `owner_name`, `legal_representative`, `corporate_email`, `phone`, `position`, `industry`, `niza_classification`, `niza_custom_category`, `products_sells`, `products_buys`, `discount_percentage`, `commercial_address`, `fiscal_address`, `city`, `state`, `postal_code`, `google_maps_url`, `website`, `facebook`, `instagram`, `linkedin`, `twitter`, `whatsapp_sales`, `whatsapp_purchases`, `whatsapp_admin`, `profile_completion`, `completion_stage`, `journey_stage`, `journey_stage_updated`, `assigned_affiliate_id`, `source_channel`, `notes`, `is_validated`, `validated_by`, `created_at`, `updated_at`) VALUES
(123, 'DDA150323HW3', 'moral', NULL, 'afiliado', 'DEPOSITO DENTAL AZUL', NULL, 'ANTONIO ESTRELLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(124, 'AUSC730202', NULL, NULL, 'afiliado', 'CESAR AGUILAR SANCHEZ', NULL, 'CESAR AGUILAR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(125, 'DPU991209HW4', 'moral', NULL, 'afiliado', 'DON PULCRO', NULL, 'MIRIAM RAMIREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(126, 'PCQ900419346', 'moral', NULL, 'afiliado', 'PLOMERIA Y CERAMICA DE QUERETARO', NULL, 'ANGEL SALMERON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(127, 'CME1605117U2', 'moral', NULL, 'afiliado', 'CONGELAIRE DE MEXICO', NULL, 'NELLY GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(128, 'UAM130508KA6', 'moral', NULL, 'afiliado', 'UNDER ARMOUR MEXICO', NULL, 'VICTOR HUGO MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(129, 'PMS811203QE6', 'moral', NULL, 'afiliado', 'PRODUCTOS METALICOS STEELE SA DE CV', NULL, 'LIC. FERNANDA OROPEZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(130, 'TOJR760106BX9', 'fisica', NULL, 'afiliado', 'JOSE RICARDO THOMAS JIMENEZ', NULL, 'RICARDO THOMAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(131, 'FES840823HH0', 'moral', NULL, 'afiliado', 'FARMACOS ESPECIALIZADOS SA DE CV', NULL, 'NOELI DE LA LUZ HERA CANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(132, 'NOV160219CN7', 'moral', NULL, 'afiliado', 'NOVA-CHEM', NULL, 'JUAN ANTONIO VALERIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(133, 'FTE010207TR6', 'moral', NULL, 'afiliado', 'FAZA TECNOLOGIA SA DE CV', NULL, 'VIRGINIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(134, 'DMA1210254T9', 'moral', NULL, 'afiliado', 'DELKAS Y MAS SA DE CV', NULL, 'LIC. YOLANDA MENDOZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(135, 'EURJ51194AHA', 'moral', NULL, 'afiliado', 'J. JESUS ESQUIVIAS RODRIGUEZ', NULL, 'JESUS ESQUIVIAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(136, 'BABE650204CR0', 'fisica', NULL, 'afiliado', 'BARAJAS BOUQUET FRANCO ARMANDO', NULL, 'LIC. FRANCISCO BARAJAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(137, 'TCQ090402RK3', 'moral', NULL, 'afiliado', 'TEMPLADORA DE CRISTALES DE QUERETARO SA DE CV', NULL, 'LIC. FRANCISCO BARAJAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(138, 'APR020320LY9', 'moral', NULL, 'afiliado', 'ALARMAS PROTEKTOR SA DE CV', NULL, 'LIC. DULCE MILLAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(139, 'AEG8908113C6', 'moral', NULL, 'afiliado', 'AUTOMOTRIZ EGARAMA SA DE CV', NULL, 'LIC. GABRIEL DE LA ROSA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(140, 'BME9809145R1', 'moral', NULL, 'afiliado', 'BEGHELLI DE MEXICO SA DE CV', NULL, 'LIC EDNA OJEDA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(141, 'STM2002216E5', 'moral', NULL, 'afiliado', 'SEMILLAS TAKI DE MEXICO', NULL, 'ITZEL OSORIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(142, 'GME020514FK3', 'moral', NULL, 'afiliado', 'THE GUND COMPANY MEXICO', NULL, 'ANIBAL CONTRERAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(143, 'CRM9307216Z4', 'moral', NULL, 'afiliado', 'CORPORACION RAYMON DE MEXICO', NULL, 'AMARA PEREA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(144, 'RMA150423SM1', 'moral', NULL, 'afiliado', 'RAMECSA MAQUINARIA', NULL, 'BLANCA RAMOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(145, 'EIQ921023UY2', 'moral', NULL, 'afiliado', 'ELEMEX ILUMINACION QUERETARO', NULL, 'JULIETA HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(146, 'LGO840413SD2', 'moral', NULL, 'afiliado', 'LIBRERIAS GONVILL', NULL, 'MANUEL GUTIERREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(147, 'PSV181206533', 'moral', NULL, 'afiliado', 'PINTURAS SUQRO', NULL, 'FERNANDO SALDIVAR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(148, 'OEPJ8912037R0', 'fisica', NULL, 'afiliado', 'JOSE JAVIER ORTEGA PEREZ', NULL, 'LUZ PEREZ RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(149, 'CAR960214TE3', 'moral', NULL, 'afiliado', 'GRUPO MAZA INTERNACIONAL', NULL, 'MA CRISTINA AGUAYO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(150, 'MUJA680616T67', 'fisica', NULL, 'afiliado', 'ANA LAURA MUJICA JONGUITUD', NULL, 'AMELIA DE JESUS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(151, 'AJA9812073P3', 'moral', NULL, 'afiliado', 'AUTOPARTES JALAPA', NULL, 'OSCAR MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(152, 'GACM430223AJ5', 'fisica', NULL, 'afiliado', 'MANUEL GOMEZ CANO', NULL, 'MANUEL EL GAMEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(153, 'BIM920922A53', 'moral', NULL, 'afiliado', 'BROTHER INTERNATIONAL DEL MEXICO', NULL, 'JOHANNA MENDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(154, 'EMA160930JA5', 'moral', NULL, 'afiliado', 'STARKEND TECHNOLOGIES', NULL, 'ROCIO LARA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(155, 'TVN7407019H3', 'moral', NULL, 'afiliado', 'TUBERIAS Y VALVULAS DEL NOROESTE', NULL, 'SURA ALAMILLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(156, 'PMU940317H14', 'moral', NULL, 'afiliado', 'PROMOTORA MUSICAL SA DE CV', NULL, 'LUIS HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(157, 'MSK181210PN0', 'moral', NULL, 'afiliado', 'MANHATTAN SKMX S DE RL CV', NULL, 'ELIZABETH', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(158, 'ITE051012PU8', 'moral', NULL, 'afiliado', 'INTER TENIS SA DE CV', NULL, 'ELIZABETH', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(159, 'SME010913TS3', 'moral', NULL, 'afiliado', 'SBCBSG COMPANY DE MEXICO', NULL, 'GERARDO HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(160, 'PFR950119CR9', 'moral', NULL, 'afiliado', 'PRODUCTOS FINOS EN REPOSTERIA SA', NULL, 'EDUARDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(161, 'VCO160727BC0', 'moral', NULL, 'afiliado', 'VAQCSA CORREGIDORA S.A.P.I DE CV', NULL, 'LIC. VERONICA GUTIERREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(162, 'SFO180328N30', 'moral', NULL, 'afiliado', 'SHUKA FOODS SAPI DE CV', NULL, 'LIC. ALICIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(163, 'ROCA910503RW3', 'fisica', NULL, 'afiliado', 'ALIZZETT RODRIGUEZ CARREÑO', NULL, 'ALIZZETT', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(164, 'AEBA880521MN3', 'fisica', NULL, 'afiliado', 'ARTURO ARELLANO', NULL, 'ARTURO ARELLANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(165, 'DRT1103169J1', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA ROCHA TULA PACHUCA S.A DE C.V', NULL, 'JESUS ROCHA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(166, 'DCE160530K82', 'moral', NULL, 'afiliado', 'DTC CENTRO SA DE CV', NULL, 'MANUEL SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(167, 'VASE910719G65', 'fisica', NULL, 'afiliado', 'ESTEFANY VARGAS SANCHEZ', NULL, 'MIRIAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(168, 'GVI091230PP0', 'moral', NULL, 'afiliado', 'GRUPO VITRO PANEL S.A DE C.V', NULL, 'SARAI TORRES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(169, 'RRM890222E59', 'moral', NULL, 'afiliado', 'REPRESENTACIONES DEL REAL MONTEMAYOR DE QUERÉTARO S.A DE C.V', NULL, 'JOSE PIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(170, 'DRQ870430A90', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA DEL REAL DE QUERETARO', NULL, 'JOSE PIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(171, 'PAM920311AR9', 'moral', NULL, 'afiliado', 'PROCESADORA DE ALIMENTOS MEXICANOS S.A DE C.V', NULL, 'RUTH SILVA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(172, 'PEAT960203T31', 'fisica', NULL, 'afiliado', 'TAHALLIYE LUCIA PERDOMO', NULL, 'YOMHA PERDOMO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(173, 'STM1302217U8', 'moral', NULL, 'afiliado', 'SUMINISTROS TACTICOS DE MEXICO', NULL, 'SANDRA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(174, 'VGA150626F20', 'moral', NULL, 'afiliado', 'AV GAUGE &amp; FIXTURE DE MEXICO S. DE R.L DE C.V', NULL, 'LIC. ALONDRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(175, 'MMO1512306G5', 'moral', NULL, 'afiliado', 'MACIAS MOTORS SA DE CV', NULL, 'EDUARDO MACIAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(176, 'MEG030708DN1', 'moral', NULL, 'afiliado', 'MEGAELECTRIC S.A DE C.V', NULL, 'FANNY', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(177, 'EME910610G1A', 'moral', NULL, 'afiliado', 'LA EUROPEA MÉXICO S.A DE C.V', NULL, 'DANIELCRUZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(178, 'PEO00M18TA', NULL, NULL, 'afiliado', 'PALETS EMPAQUES Y EMBALAJES', NULL, 'DANIEL VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(179, 'MPM150907UR9', 'moral', NULL, 'afiliado', 'MYTEX POLYMERS DE MEXICO', NULL, 'ALEJANDRA NAJERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(180, 'HCQ011121493', 'moral', NULL, 'afiliado', 'HERRAMIENTAS COMERCIALES DE QUERETARO', NULL, 'SARA LEYVA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(181, 'PSI8906083F8', 'moral', NULL, 'afiliado', 'PROVEEDORA DE SEGURIDAD INDUSTRIAL DEL GOLFO', NULL, 'SOLEDAD ADAME', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(182, 'ZMC960801538', 'moral', NULL, 'afiliado', 'ZARA MEXICO CONTRATO 1A EN P', NULL, 'LIC. VERONICA BONOLAA ROJAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(183, 'ZHM040609ES5', 'moral', NULL, 'afiliado', 'ZARA HOME MEXICO SA DE CV', NULL, 'LIC. VERONICA BONOLAA ROJAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(184, 'BME0004112J6', 'moral', NULL, 'afiliado', 'ITX RETAIL MEXICO, S.A. DE C.V.', NULL, 'LIC. VERONICA BONOLAA ROJAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(185, 'CFC110121742', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA FARMACEUTICA', NULL, 'NORA ALBARRAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(186, 'DEC150529C30', 'moral', NULL, 'afiliado', 'DECOELECTRICOS SA DE CV', NULL, 'VICTOR ESPINOZA GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(187, 'PSA210630AJ7', 'moral', NULL, 'afiliado', 'PROMOTORA SALUDNAT SA DE CV', NULL, 'YURIDIA MESINO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(188, 'FLP970514QW7', 'moral', NULL, 'afiliado', 'FOREVER LIVING PRODUCTS MEXICO S DE RL DE CV', NULL, 'FERNANDA ANGELES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(189, 'AEM151124N36', 'moral', NULL, 'afiliado', 'ADMINISTRACION DE EMPRESAS AL MENUDEO SA DE CV', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(190, 'POS010724F83', 'moral', NULL, 'afiliado', 'SULO MEXICO SA DE CV', NULL, 'ALEJANDRA URIBE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(191, 'VMM110919Q16', 'moral', NULL, 'afiliado', 'VALIANT M &amp; T DE MEXICO SA DE CV', NULL, 'LAURA BARROSO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(192, 'VNA130626220', 'moral', NULL, 'afiliado', 'VITA NOVA AGRICOLA S DE RL DE CV', NULL, 'ING. KURT GONZALEZ FRITCHE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(193, 'SQX981027RY5', 'moral', NULL, 'afiliado', 'SUPER Q SA DE CV', NULL, 'ING. BEATRIZ MALTOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(194, 'RCP0611249P0', 'moral', NULL, 'afiliado', 'ROTO CRISTALES Y PARTES SA DE CV', NULL, 'MARISOL RAMIREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(195, 'CPM061128GT0', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA DE PESCADOS Y MARISCOS EL CHARAL SA DE CV', NULL, 'MARISOL RAMIREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(196, 'GAL0412143E9', 'moral', NULL, 'afiliado', 'GRUPO ALMONI SA DE CV', NULL, 'MEHRDAD MIKAERIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(197, 'MOSS870824RI1', 'fisica', NULL, 'afiliado', 'SAUL MORALES SANCHEZ', NULL, 'SAUL MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(198, 'NEO020315MQ2', 'moral', NULL, 'afiliado', 'NOECHEM', NULL, 'QFB OLGA NELI MELLADO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(199, 'MAQM9405128Q9', 'fisica', NULL, 'afiliado', 'WINNERS MARK', NULL, 'MAXIMILIANO MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(200, 'TIQ9911037N8', 'moral', NULL, 'afiliado', 'TROQUELES INDUSTRIALES QUERETARO', NULL, 'ALEJANDRA LUNA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(201, 'VCM140106319', 'moral', NULL, 'afiliado', 'VISSCHER CARAVELLE', NULL, 'MARIBEL SALGADO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(202, 'FRH020515825', 'moral', NULL, 'afiliado', 'FREHENOSA', NULL, 'DIANA RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(203, 'AAZ180529FI8', 'moral', NULL, 'afiliado', 'AGP AZTECA', NULL, 'MISAEL CASTRO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(204, 'SME751021B90', 'moral', NULL, 'afiliado', 'FREUDENBERRG NOK SEALING T', NULL, 'ROSALBA CENTENO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(205, 'WME990817VE0', 'moral', NULL, 'afiliado', 'DE WIT DE MEXICO', NULL, 'ESTELA HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(206, 'AUMG920912RW3', 'fisica', NULL, 'afiliado', 'Gissler Iván Aguirre', NULL, 'GISSLER IVAN AGUIRRE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(207, 'CJL0609129B2', 'moral', NULL, 'afiliado', 'CAMIONES JAPONESES DE LEON', NULL, 'JESUS ROBLES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(208, 'EICF691230591', 'fisica', NULL, 'afiliado', 'FERNANDO ESPINOSA CAMACHO', NULL, 'LIC. VERONICA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(209, 'TPB110131JD2', 'moral', NULL, 'afiliado', 'TRACTOPARTES Y BUSES DE QUERETARO', NULL, 'SOFIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(210, 'MDI070604A20', 'moral', NULL, 'afiliado', 'CLAUDIA ARAIZA CASTILLO', NULL, 'CLAUDIA ARAIZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(211, 'AADG8310189KA', 'fisica', NULL, 'afiliado', 'GILBERTO NEPOMUSENO DE ALBA DGUEZ', NULL, 'GILBERTO GUADERRAMA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(212, 'PHM790601SXA', 'moral', NULL, 'afiliado', 'PARTES Y HERRAMIENTAS Y MAQUINADOS', NULL, 'CAROLINA GUTIERREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(213, 'CHE050708RH4', 'moral', NULL, 'afiliado', 'CONSORCIO HERMES', NULL, 'LILIA CORDERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(214, 'MAQ070730838', 'moral', NULL, 'afiliado', 'maquirentas', NULL, 'NADIA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(215, 'RIR770414GA2', 'moral', NULL, 'afiliado', 'REPRESENTACIONES INDUSTRIALES', NULL, 'EDITH HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(216, 'HMN171201HZ7', 'moral', NULL, 'afiliado', 'HERRAMIENTAS Y MAQUINARIA', NULL, 'ARTURO NUÑEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(217, 'NTE0510249JA', 'moral', NULL, 'afiliado', 'NC TECH', NULL, 'ANGELICA PEREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(218, 'PCZ071128UM9', 'moral', NULL, 'afiliado', 'PRODUCTOS DE CONSUMO Z', NULL, 'CARMEN ZAMUDIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(219, 'TWC1407158R6', 'moral', NULL, 'afiliado', 'TONIC WORD CENTER', NULL, 'GUADALUPE I BARRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(220, 'EU100114647', NULL, NULL, 'afiliado', 'EUROTOOLS', NULL, 'JAIME MONTES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(221, 'EME980420TB9', 'moral', NULL, 'afiliado', 'ENFIL DE MEXICO', NULL, 'CP JOSE LUIS OLVERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(222, 'BLO940712RA9', 'moral', NULL, 'afiliado', 'BOUTIQUE LOB SA DE CV', NULL, 'MA. DE LOURDES BORREGO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(223, 'DJU890724BUO', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA JUGUETRON', NULL, 'SILVIA RAMIREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(224, 'AFO8311086M2', 'moral', NULL, 'afiliado', 'ACEROS FORTUNA', NULL, 'ANA PATRICIA PLAZOLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(225, 'RSQ130613MV6', 'moral', NULL, 'afiliado', 'REFACCIONES SANITARIAS QRO', NULL, 'HECTOR ACOSTA ANDRACA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(226, 'ROHJ500314BG9', 'fisica', NULL, 'afiliado', 'Josefina Robles Hernández', NULL, 'SR. ESTABAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(227, 'GAOA541026969', 'fisica', NULL, 'afiliado', 'ANGEL MARIA GARCIA', NULL, 'BIANCA MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(228, 'GOBN840710TQ1', 'fisica', NULL, 'afiliado', 'NATALIE XIASU GONZALEZ ROBLES', NULL, 'NATALIE GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(229, 'METE8402137B0', 'fisica', NULL, 'afiliado', 'ERIKA DANIELA MERA TAPIA', NULL, 'ERIKA DANIELA MERA TAPIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(230, 'JAU061106P1', NULL, NULL, 'afiliado', 'JASMAN AUTOMOTRIZ', NULL, 'JUANITA / SRA. PUEBLITO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(231, 'HAU18417686', NULL, NULL, 'afiliado', 'HAHN AUTOMATION S DE RL DE CV', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(232, 'SABR570916R18', 'fisica', NULL, 'afiliado', 'ROGELIO SAN JUAN BENITEZ', NULL, 'ROGELIO SAN JUAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(233, 'ESN200811D91', 'moral', NULL, 'afiliado', 'ESNILOS SA DE CV', NULL, 'ALBERTO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(234, 'VAGS020308646', 'fisica', NULL, 'afiliado', 'SEBASTIAN VALDES GUILLEN', NULL, 'GLORIA GUILLEN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(235, 'LOS230331E43', 'moral', NULL, 'afiliado', 'LOSEMPAQ', NULL, 'JULEM SCHWARZ ETCHEVERRY', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(236, 'DJU890724BU0', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA JUGUETRON', NULL, 'MOISES ANAYA VILLAREAL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(237, 'DIHC670217BG1', 'fisica', NULL, 'afiliado', 'CARMEN DIAZ HURTADO', NULL, 'CARMEN DIAZ HURTADO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(238, 'CSO680801P93', 'moral', NULL, 'afiliado', 'CASA SOMMER', NULL, 'JUAN RAMON GARCÍA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(239, 'ODE86042557UA', 'fisica', NULL, 'afiliado', 'OPTICAS DEVLYN SA DE CV', NULL, 'C.P ELSA PERDOMO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(240, 'FFF250228BM1', 'moral', NULL, 'afiliado', 'FROZEN AND FIRE FUSION', NULL, 'ING. LUIS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(241, 'OIM8721121118', 'fisica', NULL, 'afiliado', 'BERENICE ORTIZ', NULL, 'MARIELA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(242, 'JSU100326MW9', 'moral', NULL, 'afiliado', 'JAD SUMINISTROS', NULL, 'VANESSA BARRIENTOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(243, 'SQS100218DT0', 'moral', NULL, 'afiliado', 'EL SURTODOR QUERETANO', NULL, 'MARIA EUGENIA UGALDE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(244, 'CME770429QL1', 'moral', NULL, 'afiliado', 'CENTRIFUGADOS MEXICANOS', NULL, 'LORENA HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(245, 'ROSM900307T85', 'fisica', NULL, 'afiliado', 'JOSE MARGARITO ROSALES SANCHEZ', NULL, 'MARGARITA ROSALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(246, 'DEL930630BA0', 'moral', NULL, 'afiliado', 'DELAVAL SA DE CV', NULL, 'LIC. ALBERTO GUERRERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(247, 'DUN090914IWA', 'moral', NULL, 'afiliado', 'DELCAS UNIFORMES SA DE CV', NULL, 'LIC. LUPITA ZARAZUA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(248, 'ORI140311PY0', 'moral', NULL, 'afiliado', 'OPERADORA RIGGAL', NULL, 'RAUL GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(249, 'TOGD8407016Z7', 'fisica', '4421490600', 'afiliado', 'DAYANE JANET TORANZO GLEZ', 'LOS DOGOS', 'DAYANE TORANZO', '', '', '4421490600', NULL, 'COMERCIO', '43', '', '[\"RESTAURANTE\", \"CATERING\", \"ORGANIZACION DE EVENTOS\", \"BOX LUNCH\"]', '[\"ABARROTES\", \"MANTENIMIENTO DE EQUIPOS\"]', 0.00, 'HACIENDA ESCOLASTICAS 312 COL. JARDINES DE LA HACIENDA', 'HACIENDA ESCOLASTICAS 312 COL. JARDINES DE LA HACIENDA', 'Santiago de Querétaro', 'Querétaro', '76180', '', '', '', '', '', '', '4421490600', '', '', 38, 'B', 1, NULL, 7, 'alta_directa', 'Servicios de interés: networking, gestoria\n\n', 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:37:17'),
(250, 'AAIH810111LU9', 'fisica', NULL, 'afiliado', 'HORTENCIA EDIRH ALCAYA I', NULL, 'EDITH ALCAYA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(251, 'BME200610JA2', 'moral', NULL, 'afiliado', 'BRASIMEX MADERAS Y EMBALAJEOS', NULL, 'MARIA ORTEGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(252, 'MME781114247', 'moral', NULL, 'afiliado', 'MITUTOYO', NULL, 'SANDRA ZETINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10');
INSERT INTO `contacts` (`id`, `rfc`, `person_type`, `whatsapp`, `contact_type`, `business_name`, `commercial_name`, `owner_name`, `legal_representative`, `corporate_email`, `phone`, `position`, `industry`, `niza_classification`, `niza_custom_category`, `products_sells`, `products_buys`, `discount_percentage`, `commercial_address`, `fiscal_address`, `city`, `state`, `postal_code`, `google_maps_url`, `website`, `facebook`, `instagram`, `linkedin`, `twitter`, `whatsapp_sales`, `whatsapp_purchases`, `whatsapp_admin`, `profile_completion`, `completion_stage`, `journey_stage`, `journey_stage_updated`, `assigned_affiliate_id`, `source_channel`, `notes`, `is_validated`, `validated_by`, `created_at`, `updated_at`) VALUES
(253, 'CAC130528DA4', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA ACERLUM', NULL, 'JESUS MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(254, 'JIRN850316V25', 'fisica', NULL, 'afiliado', 'NAYELI JIMENES RIOS', NULL, 'JORGE SEGURO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(255, 'BWC180810B10', 'moral', NULL, 'afiliado', 'BZ WIRE AND CABLES', NULL, 'MA. FERNANDA GUERRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(256, 'GNO9007036F5', 'moral', NULL, 'afiliado', 'GRUPO NOVEM', NULL, 'GUSTAVO ORTEGA M.', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(257, 'DUSV931008UG3', 'fisica', NULL, 'afiliado', 'DETAIL GARAGE', NULL, 'NOE DURAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(258, 'LUAL730715MT9', 'fisica', NULL, 'afiliado', 'JOSE LUIS LUNA', NULL, 'JOSE LUIS MORENO RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(259, 'ELE160629GX1', 'moral', NULL, 'afiliado', 'ELECTRIVAGO', NULL, 'IRIS VARGAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(260, 'DCJ030226FJA', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA COMERCIAL JAFRA SA DE CV', NULL, 'LIC. DIEGO ASTORGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(261, 'GYO9512159PA', 'moral', NULL, 'afiliado', 'GRUPO YOMAR SA DE CV', NULL, 'LIC. CLUDIA ROBLES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(262, 'SIG5931016GW6', 'fisica', NULL, 'afiliado', 'SAYURI SHIMODA GARCIA', NULL, 'SAYURI', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(263, 'FCA990211965', 'moral', NULL, 'afiliado', 'frenos y cluth avila', NULL, 'RICARDO AVILA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(264, 'EAV160830N83', 'moral', NULL, 'afiliado', 'ESPECIALISTAS EN ACCESOS BASCULARES', NULL, 'LUIS VEGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(265, 'NME081219RG7', 'moral', NULL, 'afiliado', 'NOVALIMENTOS', NULL, 'MIGUEL ANGEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(266, 'VUL801110DN4', 'moral', NULL, 'afiliado', 'VULCAFRIO', NULL, 'MAYRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(267, 'OEHM860726C52', 'fisica', NULL, 'afiliado', 'MARIA MELISSA IVONE ORNELAS', NULL, 'C,P ALEJANDRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(268, 'AITJ9203272Z3', 'fisica', NULL, 'afiliado', 'JUAN LUIS ARIAS TOVAR', NULL, 'JUAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(269, 'NIT0305053H3', 'moral', NULL, 'afiliado', 'NITROPISO', NULL, 'FEDERICO AVILA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(270, 'AL&amp;010509', 'fisica', NULL, 'afiliado', 'ALEX LYON', NULL, 'ALICIA MONTES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(271, 'IQU160510C17', 'moral', NULL, 'afiliado', 'INOX QUERETARO', NULL, 'SAIRI PARAM', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(272, 'CSO1212193L1', 'moral', NULL, 'afiliado', 'CORREO SOLUCION', NULL, 'MIRIAM SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(273, 'FER8506034X7', 'moral', NULL, 'afiliado', 'FERRECABSA SA DE CV', NULL, 'LIC. STEPHANIE S.', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(274, 'FMA180316JM8', 'moral', NULL, 'afiliado', 'FSS MANU-DESING SA DE CV', NULL, 'CP. ROCIO ZUÑIGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(275, 'ROVR611120JL5', 'fisica', NULL, 'afiliado', 'JOSE ROGELIO RODRIGUEZ VARELA', NULL, 'JOSE ROGELIO RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(276, 'VABC480107517', 'fisica', NULL, 'afiliado', 'CLEMENTINA VAZQUEZ BERUMEN', NULL, 'CLEMENTINA VAZQUEZ B', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(277, 'PPA020617TP2', 'moral', NULL, 'afiliado', 'PROVEEDORA DE PARTES AUTOMOTRIC', NULL, 'CP. SILVIA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(278, 'DIR8010112S9', 'moral', NULL, 'afiliado', 'DIREB SA DE CV', NULL, 'MARTHA PATRICIA BRAVO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(279, 'ACO960619AY9', 'moral', NULL, 'afiliado', 'ACEROS Y CORRUGADOS SA DE CV', NULL, 'CP ROSY GIL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(280, 'FER940310HD1', 'moral', NULL, 'afiliado', 'FEREM SA DE CV', NULL, 'BERNABE AVILA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(281, 'PALG690715U33', 'fisica', NULL, 'afiliado', 'GUILLERMO PLATA LOPEZ', NULL, 'MARIBEL ROCHA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(282, 'PIN020921FL5', 'moral', NULL, 'afiliado', 'PREMEZCLAS INTERNACIONALES', NULL, 'CP. GRECIA FIGUEROA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(283, 'MOE030116LEA', 'moral', NULL, 'afiliado', 'MOELTEK SA DE CV', NULL, 'PABLO CESAR MARTINEZ J.', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(284, 'GOEO861004GG9', 'fisica', NULL, 'afiliado', 'OSCAR FCO GONZALEZ ESCOTO', NULL, 'OSCAR F. GONZALEZ ESCOTO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(285, 'UME1601182W1', 'moral', NULL, 'afiliado', 'USA COVERS', NULL, 'KARINA TORRES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(286, 'KAB980129BC4', 'moral', NULL, 'afiliado', 'KLINGSPOR ABRASIVOS', NULL, 'IGNACIO SALCEDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(287, 'AIN100219N22', 'moral', NULL, 'afiliado', 'AFILADOS INTERTOOL', NULL, 'LIC. PILAR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(288, 'CAB020207IV4', 'moral', NULL, 'afiliado', 'CONCRETOS ABC', NULL, 'CP. MAURICIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(289, 'Z&amp;S060802', 'fisica', NULL, 'afiliado', 'ZSCHIMER', NULL, 'MIRNA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(290, 'VAGO840506FS5', 'fisica', NULL, 'afiliado', 'OSCAR VALDEZ GUADARRAMA', NULL, 'YETI JADE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(291, 'CME960830HB4', 'moral', NULL, 'afiliado', 'cerraco mexico', NULL, 'LIC. ERIKA PACHECO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(292, 'TCE100601C60', 'moral', NULL, 'afiliado', 'TIENDAS CERRAJES', NULL, 'LIC. ERIKA PACHECO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(293, 'RAVA941219487', 'fisica', NULL, 'afiliado', 'ANDREA RAMIRE VAZQUEZ', NULL, 'ANDREA RAMIREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(294, 'ASS140708F72', 'moral', NULL, 'afiliado', 'AMERICAN SPORT SCHOES', NULL, 'LIC. ERICK', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(295, 'PLA180509S97', 'moral', NULL, 'afiliado', 'PLASMAN S DE RL DE CV', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(296, 'OCO030116UR4', 'moral', NULL, 'afiliado', 'ONUS COMERCIAL', NULL, 'MAYRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(297, 'EXE050427KB6', 'moral', NULL, 'afiliado', 'SAMES NORTEAMERICA', NULL, 'ERIKA SALAZAR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(298, 'SEC9108274P0', 'moral', NULL, 'afiliado', 'SISTEMAS ECOLOGICOS', NULL, 'CRISTINA SALTILLO/MARICELA PEÑA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(299, 'GZI860210EQ3', 'moral', NULL, 'afiliado', 'GRUPO ZET', NULL, 'ARANZA ORTEGON SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(300, 'VENF700114A39', 'fisica', NULL, 'afiliado', 'José Fernando Vega Navarrete', NULL, 'SR. JOSE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(301, 'RAIN5801125K5', 'fisica', NULL, 'afiliado', 'NOEMI RANGEL INFANTE', NULL, 'MARIA LUISA NUÑEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(302, 'MMO300912MA1', 'moral', NULL, 'afiliado', 'MERCADO DE MAQUINAS PARA OFICINA', NULL, 'FEDERICO MUÑOZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(303, 'MARO770829719', 'fisica', NULL, 'afiliado', 'OSVALDO MARTINEZ RIOS', NULL, 'OSVALDO MENDOZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(304, 'AME131202SW3', 'moral', NULL, 'afiliado', 'ROYAL BRINKMAN MEXICO', NULL, 'MARCELA DIAZ/JOEL LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(305, 'DPN030512M26', 'moral', NULL, 'afiliado', 'DIVISION Y PLAFONES NEXT', NULL, 'CAROLINA RAMOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(306, 'PAM1710249Y2', 'moral', NULL, 'afiliado', 'PXI AUTOMOTIVE', NULL, 'ROGELIO FERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(307, 'WPA131224FHA', 'moral', NULL, 'afiliado', 'WLATER PACK', NULL, 'LETICIA ELIZONDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(308, 'AVE950810H96', 'moral', NULL, 'afiliado', 'AVEPSA', NULL, 'GERARDO MORA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(309, 'FAJK50210RA2', 'moral', NULL, 'afiliado', 'KARINA FRANCISCO JUQAREZ', NULL, 'KARINA FRANCISCO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(310, 'RORJ7211231I9', 'fisica', NULL, 'afiliado', 'JOSE JUAN RGUEZ RIOS', NULL, 'JOSE JUAN RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(311, 'GORJ830226N98', 'fisica', NULL, 'afiliado', 'JORGE GLZ RAMIREZ', NULL, 'JORGE GONZALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(312, 'ROGE6009301Q3', 'fisica', NULL, 'afiliado', 'MA ELSA ROSALES GUAJARDO', NULL, 'PATRICIA MEJIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(313, 'ROLA850618RJ9', 'fisica', NULL, 'afiliado', 'MA AMANDA RGUEZ LOZADA', NULL, 'AMANDA RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(314, 'ARR1509235W2', 'moral', NULL, 'afiliado', 'ACEROS DE ALTO RENDIMIENTO', NULL, 'VERONICA MEDINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(315, 'NME021223398', 'moral', NULL, 'afiliado', 'NESPRESO MEXICO SA DE CV', NULL, 'LIC. LANDY RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(316, 'DABJ6310148V9', 'fisica', NULL, 'afiliado', 'JESUS DANIELLS BARRERA', NULL, 'JESUS DANIELLS BARRERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(317, 'PIAH6112241H1', 'fisica', NULL, 'afiliado', 'HERMINIA PIÑA ALVAREZ', NULL, 'MA. DE LA LUZ MEDINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(318, 'CNC830430AF5', 'moral', NULL, 'afiliado', 'CAMARA DE COMERCIO DE MONTERREY', NULL, 'LIC. EDGAR LURIAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(319, 'CNC830430AF6', 'moral', NULL, 'afiliado', 'CAMARA DE COMERCIO DE MONTERREY', NULL, 'LIC. EDGAR LURIAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(320, 'FSA011016L44', 'moral', NULL, 'afiliado', 'FERREACEROS SAMER SA DE CV', NULL, 'HERMELINDA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(321, 'CTR040420KE8', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA TREGA SA DE CV', NULL, 'ANA LAURA TREJO/LIC. ERIKA TREJO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(322, 'BME050930QDA', 'moral', NULL, 'afiliado', 'BIXA DE MEXICO', NULL, 'LIC. ALONSO GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(323, 'GEC140609F9A', 'moral', NULL, 'afiliado', 'GRUPO EVOLUCION 101', NULL, 'WALDIR SUCHAY', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(324, 'UME150723SQ7', 'moral', NULL, 'afiliado', 'UNIFORMES A LA MEDIDA', NULL, 'ANA ANDERSON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(325, 'ECE830923MJ2', 'moral', NULL, 'afiliado', 'EMPACADORA CELAYA', NULL, 'VERONICA ALPIZAR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(326, 'SUG980615NM1', 'moral', NULL, 'afiliado', 'SUGENTE SA DE CV', NULL, 'VERONICA ALPIZAR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(327, 'OVE050902L62', 'moral', NULL, 'afiliado', 'OSTAN VETERINARIA', NULL, 'ANA LUISA MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(328, 'SAMR9011204W9', 'fisica', NULL, 'afiliado', 'RAQUEL SANCHEZ', NULL, 'JOSE SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(329, 'IDC17121522A', 'moral', NULL, 'afiliado', 'INGENIERIA Y DISENO', NULL, 'TOMAS SALAS SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(330, 'IPS210708GE3', 'moral', NULL, 'afiliado', 'IMPULSORA DE PRODUCTOS SALUDABLES', NULL, 'ERIK LAGUNA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(331, 'JMA0908105Q9', 'moral', NULL, 'afiliado', 'JFC MAQUINADOS', NULL, 'ROGELIO FLORES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(332, 'NME140805DS8', 'moral', NULL, 'afiliado', 'NEOHYUNDAY MEXICO', NULL, 'SANDRA ALEMAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(333, 'CABM701031N22', 'fisica', NULL, 'afiliado', 'MARTHA EVELIA CABEZA DE VACA BUSTAMANTE', NULL, 'MARTHA CABEZA DE VACA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(334, 'MSM071218J69', 'moral', NULL, 'afiliado', 'MAQUINADOS Y SERVICIOS MUÑOZ SA DE CV', NULL, 'MA. DE JESUS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(335, 'GPG870312998', 'moral', NULL, 'afiliado', 'GRUPO PAPELERO GTRREZ', NULL, 'ABUNDIO BELTRAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(336, 'LRE010713PG6', 'moral', NULL, 'afiliado', 'LAO REFACCIONES', NULL, 'LAURA A MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(337, 'SSC840823JT3', 'moral', NULL, 'afiliado', 'SYSTEMAS Y SERVICIOS DE COMUNICACION', NULL, 'ING. DANIEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(338, 'TAC200814EB5', 'moral', NULL, 'afiliado', 'TECNOLOGIA EN ACRILICO', NULL, 'JUAN CARLOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(339, 'PTO190129HG6', 'moral', NULL, 'afiliado', 'PUSH TOU', NULL, 'ADOLFO ROMERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(340, 'NBA990514B73', 'moral', NULL, 'afiliado', 'NUTRIMENTOS BALANCEADOS', NULL, 'CP. FATIMA SALVADOR LOREDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(341, 'HPL0305093B7', 'moral', NULL, 'afiliado', 'HAISO PLASTICOS', NULL, 'CP. DANIEL GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(342, 'SMX190725RT8', 'moral', NULL, 'afiliado', 'SITEXPERT MX', NULL, 'ERICKA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(343, 'FHA160810R96', 'moral', NULL, 'afiliado', 'FABRICA DE HIELO APODACA SA DE CV', NULL, 'LIC. YADIRA RIOS/ FRANCISCO OCAMPO/OSVALDO SEVILLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(344, 'FARF820308J86', 'fisica', NULL, 'afiliado', 'FRANCO RESENDIZ FRANCISCO', NULL, 'JAKIE FLORES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(345, 'FARM8307029U0', 'fisica', NULL, 'afiliado', 'FRANCO RESENDIZ MYRNA', NULL, 'JAKIE FLORES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(346, 'FARK870219JKA', 'fisica', NULL, 'afiliado', 'FRANCO RESENDIZ KARLA', NULL, 'JAKIE FLORES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(347, 'REPE590213NY0', 'fisica', NULL, 'afiliado', 'RESENDIZ PEREZ EVA', NULL, 'JAKIE FLORES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(348, 'VT&amp;041110', 'fisica', NULL, 'afiliado', 'VICA T 8 D', NULL, 'MARISOL VIZCAYA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(349, 'PEGN060330S68', 'fisica', NULL, 'afiliado', 'NAYELI PENA GALLEGOS', NULL, 'JOSE NICOLAS PEÑA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(350, 'SCQ170907NG6', 'moral', NULL, 'afiliado', 'STEEL CONSTRUCTIONS QUERETARO', NULL, 'C.P. ANA RESENDIZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(351, 'SIHA661004JS2', 'fisica', NULL, 'afiliado', 'ARCELIA SIFUENTES HDEZ', NULL, 'ARACELI FUENTES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(352, 'PCB000426TG6', 'moral', NULL, 'afiliado', 'PROVEEDORA DECLIMAS DEL BAJIO', NULL, 'BERENCIE ROJAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(353, 'KAI160913JC1', 'moral', NULL, 'afiliado', 'KAMFRI AIR', NULL, 'LINA LINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(354, 'COBL6803204A3', 'fisica', NULL, 'afiliado', 'LORENZO CORTES BEDOLLA', NULL, 'LORENZO CORTES B.', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(355, 'SDE040413G69', 'moral', NULL, 'afiliado', 'SOLUCION DIGITAL EMPRESARIAL', NULL, 'LA. MA. GUADALUPE SERVIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(356, 'ALO140110S78', 'moral', NULL, 'afiliado', 'PREPOXI S DE RL', NULL, 'CARMEN SUAREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(357, 'MAQ900629HZ8', 'moral', NULL, 'afiliado', 'MAQUISOFT', NULL, 'LUPITA ALVARADO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(358, 'AAMG860128Q79', 'fisica', NULL, 'afiliado', 'FERRE 27', NULL, 'GERSON ALARCON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(359, 'EMMA620824', NULL, NULL, 'afiliado', 'AURELIANO HERNANDEZ', NULL, 'AURELIANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(360, 'IIS200221PK6', 'moral', NULL, 'afiliado', 'ISMAH INDUSTRIAL', NULL, 'JOSUE ARTEAGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(361, 'MIN181018TM6', 'moral', NULL, 'afiliado', 'MZC INDUSTRIALSUPPLY', NULL, 'ANA ISABEL LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(362, 'PCO30830JT3', NULL, NULL, 'afiliado', 'PROCESOS CONTROLADOS', NULL, 'LIC. RAYMUNDO BONILLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(363, 'GORG701130LI0', 'fisica', NULL, 'afiliado', 'GABRIELA GLEZ RUIZ', NULL, 'LUIS ROJAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(364, 'FOTJ970213TD2', 'fisica', NULL, 'afiliado', 'JESUS FLORES TELLEZ', NULL, 'SAMUEL FLORES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(365, 'DEL9112172G8', 'moral', NULL, 'afiliado', 'DELECTRIC', NULL, 'LIC. SERGIO MENA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(366, 'HMQ220819P42', 'moral', NULL, 'afiliado', 'HERRAJES Y MATERIALES DE QUERTARO', NULL, 'MONICA PADILLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(367, 'SER9501098Q3', 'moral', NULL, 'afiliado', 'SERVIHIGIENE', NULL, 'c.p. MATY MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(368, 'EOML491228QG5', 'fisica', NULL, 'afiliado', 'JOSE LUIS ESCORCIA MAYER', NULL, 'JOSE LUIS ESCORERIA MAYER', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(369, 'CIM0710049M6', 'moral', NULL, 'afiliado', 'CARCOUSTICS INDUSTRIAL DE MEXICO', NULL, 'C.P CARMEN RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(370, 'WGR920403GN2', 'moral', NULL, 'afiliado', 'WATA GROUP', NULL, 'ELIZABETH TAPIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(371, 'MARJ95101BZ4', 'moral', NULL, 'afiliado', 'JUAN PABLO MANDUJANO RIOS', NULL, 'JUAN PABLO M', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(372, 'UQR190506UYA', 'moral', NULL, 'afiliado', 'ULTRACOLOR QRO', NULL, 'MARIZ OLY GARMICA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(373, 'RGM140828H96', 'moral', NULL, 'afiliado', 'RAMON GARCIA MEX', NULL, 'MARIBEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(374, 'ECL120315TI2', 'moral', NULL, 'afiliado', 'ENERGIA COMBUSTIBLE Y LUBRICANTES', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(375, 'ECL120315TI3', 'moral', NULL, 'afiliado', 'ENERGIA COMBUSTIBLE Y LUBRICANTES', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(376, 'GAS160707IL4', 'moral', NULL, 'afiliado', 'GRUPO A005', NULL, 'OLGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(377, 'EGM2010087X8', 'moral', NULL, 'afiliado', 'EXTRUSIONES G.M. SA DE CV', NULL, 'RAUL GAYTAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(378, 'WMB10504FM3', NULL, NULL, 'afiliado', 'WURTH INDUSTRY DE MEXICO', NULL, 'LIC. LILIANA GALLEGOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(379, 'CAS120208AF1', 'moral', NULL, 'afiliado', 'CLARK Y ASOCIADOS', NULL, 'BRENDA MUÑOZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(380, 'PAT880112D35', 'moral', NULL, 'afiliado', 'PRODUCCIONES DE ALTA TECNOLOGIA', NULL, 'LIC. CLAUDIA GUDIÑO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(381, 'GAHD930818SP5', 'fisica', NULL, 'afiliado', 'DIEGO ULISES GARCIA HERNANDEZ', NULL, 'DIEGO ULISES GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(382, 'SEEG880516BP5', 'fisica', NULL, 'afiliado', 'GEOVANNY OLIVER SERRATO ESPARZA', NULL, 'GEOVANNY OLIVER SERRATO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(383, 'NITO870727LB3', 'fisica', NULL, 'afiliado', 'OMAR SALVADOR NIEVES TORRES', NULL, 'OMAR SALVADOR NIEVES T.', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(384, 'LPA2401236G7', 'moral', NULL, 'afiliado', 'LF PANADERIA DE ARTE', NULL, 'OSCAR GONZALEZ ESCOTO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10');
INSERT INTO `contacts` (`id`, `rfc`, `person_type`, `whatsapp`, `contact_type`, `business_name`, `commercial_name`, `owner_name`, `legal_representative`, `corporate_email`, `phone`, `position`, `industry`, `niza_classification`, `niza_custom_category`, `products_sells`, `products_buys`, `discount_percentage`, `commercial_address`, `fiscal_address`, `city`, `state`, `postal_code`, `google_maps_url`, `website`, `facebook`, `instagram`, `linkedin`, `twitter`, `whatsapp_sales`, `whatsapp_purchases`, `whatsapp_admin`, `profile_completion`, `completion_stage`, `journey_stage`, `journey_stage_updated`, `assigned_affiliate_id`, `source_channel`, `notes`, `is_validated`, `validated_by`, `created_at`, `updated_at`) VALUES
(385, 'PAE920709D75', 'moral', NULL, 'afiliado', 'PROVEEDORA DE AISLANTES ELECTRICOS', NULL, 'LIC MORMA BAHENA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(386, 'GHI911125TI0', 'moral', NULL, 'afiliado', 'GRUPO HILCO SA DE CV', NULL, 'AIDE MORENO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(387, 'MORA730725PS9', 'fisica', NULL, 'afiliado', 'Alejandra de la Mora Rosiles', NULL, 'ALEJANDRA DE LA MORA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(388, 'PCA030218BH1', 'moral', NULL, 'afiliado', 'PEOPLE CARE', NULL, 'JOSEFINA CUEVAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(389, 'ICO0103149T8', 'moral', NULL, 'afiliado', 'THE IDEA COLLECTION', NULL, 'LETICIA DIAZ DE LEON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(390, 'TACA020803', NULL, NULL, 'afiliado', 'DANIELA ISABEL TALAVERACRUZ', NULL, 'DANIELA TAVERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(391, 'CURG910819KSA', 'fisica', NULL, 'afiliado', 'GUADALUPE CARMEN CRUZ', NULL, 'GUADALUPE CARMEN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(392, 'UAMJ701031G40', 'fisica', NULL, 'afiliado', 'JORGE ANTONIO UGALDE MENDOZA', NULL, 'JORGE ANTONIO UGALDE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(393, 'GHI91125TI0', NULL, NULL, 'afiliado', 'HOSPITALIDAD CORREGIDORA DE QRO', NULL, 'MELISSA LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(394, 'PDE010131B42', 'moral', NULL, 'afiliado', 'PRODUCTOS DEPORTIVOS SA DE CV', NULL, 'LIC. BRICIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(395, 'pejm850507666', 'fisica', NULL, 'afiliado', 'moises perea juarez', NULL, 'MARTHA LUGO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(396, 'AEOV821019K51', 'fisica', NULL, 'afiliado', 'Victor Hugo Arreola Ovalle', NULL, 'VALERIA CARVAJAL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(397, 'CORJ931027EW9', 'fisica', NULL, 'afiliado', 'Jesus Colorado Reyes', NULL, 'JESUS COLORADO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(398, 'HERG860901158', 'fisica', NULL, 'afiliado', 'Gabriela De Los Remedios Hernandez Ruiz', NULL, 'MARIA GUZMAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(399, 'SOGA471021UM8', 'fisica', NULL, 'afiliado', 'Alvaro Soto Gonzalez', NULL, 'ALVARO SOTO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(400, 'GRA220913E47', 'moral', NULL, 'afiliado', 'GRAZIE', NULL, 'RICARDO VELEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(401, 'GGA110623R89', 'moral', NULL, 'afiliado', 'GRUPO GASOLINERO ALFA', NULL, 'NATALIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(402, 'PRO020416FN7', 'moral', NULL, 'afiliado', 'PROLIMPIEZA', NULL, 'LETICIA ARANDA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(403, 'PME0706268Z0', 'moral', NULL, 'afiliado', 'DURAC MEXICO', NULL, 'LIC. AZUCENA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(404, 'PLQ9002286Q6', 'moral', NULL, 'afiliado', 'PRODUCTOS LACTEOS LA QUINTA', NULL, 'LIC. PAMELA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(405, 'PIBA890905229', 'fisica', NULL, 'afiliado', 'JOSE ALEJANDRO PILAR BALTAZAR', NULL, 'ALEJANDRO PILOR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(406, 'SCI080724FD9', 'moral', NULL, 'afiliado', 'INIX COMERCIAL SA DE CV', NULL, 'LIC. EDGAR ZARAGOZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(407, 'PHQ030102GR5', 'moral', NULL, 'afiliado', 'PERFILES Y HERREJES DE QUERETARO', NULL, 'GABRIELA VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(408, 'PHQ030102GR6', 'moral', NULL, 'afiliado', 'PERFILES Y HERREJES DE QUERETARO', NULL, 'GABRIELA VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(409, 'ASP600721PX5', 'moral', NULL, 'afiliado', 'ACERO SUECO PALME SAPI', NULL, 'C.P. DENISSE RIVERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(410, 'TOOJ8911215R0', 'fisica', NULL, 'afiliado', 'JESUS ORLANDO TOVAR OSTOS', NULL, 'JESUS TOVAR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(411, 'HLVN920777D13', 'fisica', NULL, 'afiliado', 'NATALIA HURTADO VAZQUEZ', NULL, 'JACOB HURTADO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(412, 'ATAM8407218X7', 'fisica', NULL, 'afiliado', 'MIGUEL ADRIAN ARIAS', NULL, 'sherlin', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(413, 'RHM950313RZ4', 'moral', NULL, 'afiliado', 'R.S HUGHES', NULL, 'ALEJANDRO CHAVEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(414, 'MOMA6009195J3', 'fisica', NULL, 'afiliado', 'ADAN MORALES MEJIA', NULL, 'ANA MONDRAGON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(415, 'SUP2008131P0', 'moral', NULL, 'afiliado', 'SUPRACARE SA DE CV', NULL, 'ILSE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(416, 'CUJO701130GJ2', 'fisica', NULL, 'afiliado', 'OSCAR CRUZ JIMENEZ', NULL, 'OSCAR CRUZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(417, 'HURJ5801103UQ', 'fisica', NULL, 'afiliado', 'JOSE HURTADO RAMIREZ', NULL, 'JOSE HURTADO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(418, 'AERF580205390', 'fisica', NULL, 'afiliado', 'JOSE FELIPE ARELLANO ROMERO', NULL, 'FELIPE ARELLANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(419, 'BEMR930705826', 'fisica', NULL, 'afiliado', 'RIGE BEJARANO MURGUIA', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(420, 'TVA1708037A4', 'moral', NULL, 'afiliado', 'TIENDAS VA', NULL, 'MARY CARMEN MORALES REYES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(421, 'SMT60520R93', NULL, NULL, 'afiliado', 'SERVTHERM DE MEXICO', NULL, 'ANGEL GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(422, 'GAR1105271A6', 'moral', NULL, 'afiliado', 'GARPABA', NULL, 'GERARDO GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(423, 'SERA9005118G4', 'fisica', NULL, 'afiliado', 'ABRAHAM SERRATO ROSAS', NULL, 'ABRAHAM SERRATO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(424, 'OGI140828JG7', 'moral', NULL, 'afiliado', 'OPERADORA GASTRONOMICA IKE', NULL, 'RAUL GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(425, 'PRO97060597A', 'moral', NULL, 'afiliado', 'PROMEGI S.A. DE C.V.', NULL, 'IRVING BRUNO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(426, 'WME9003078U2', 'moral', NULL, 'afiliado', 'WURTH MEXICO', NULL, 'EDUARDO GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(427, 'RER050221JR0', 'moral', NULL, 'afiliado', 'RIVERLAND ERGONOMIC', NULL, 'ROXANA J. GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(428, 'MSE040112C74', 'moral', NULL, 'afiliado', 'MERCHANDISING SERVICES', NULL, 'ISAAC MARTIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(429, 'KAR160211115', 'moral', NULL, 'afiliado', 'CARNES PREMIUM XO', NULL, 'C.P JUAN MANUEL M.', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(430, 'CEQ240102SE4', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA EXTIN DE QUEREETARO SA DE CV', NULL, 'ROSALIA NUÑEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(431, 'NPC040412T92', 'moral', NULL, 'afiliado', 'NEEW PRODUCTS CONNECTION', NULL, 'BLANCA ESTELA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(432, 'CSP930709E87', 'moral', NULL, 'afiliado', 'COMERCIAL SPORT', NULL, 'INGRID MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(433, 'VIOS961016DL9', 'fisica', NULL, 'afiliado', 'SOFIA VILLACANA', NULL, 'SOFIA VILLACAÑA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(434, 'WOMR631212GF3', 'fisica', NULL, 'afiliado', 'RAFAEL WOOLFER MARTICORENA', NULL, 'RAFAEL WOOLFER', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(435, 'AQU8408017A3', 'moral', NULL, 'afiliado', 'AUTOMOVILES DE QUERETARO', NULL, 'C.P. JOSE ANTONIO ANTONIO GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(436, 'FEBG951226', NULL, NULL, 'afiliado', 'JOSE GABRIEL FELIX BARRIENTOS', NULL, 'JOSE GABRIEL FELIX', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(437, 'JAU0611063P1', 'moral', NULL, 'afiliado', 'JASMAN AUTOMOTRIZ SA DE CV', NULL, 'ALFONSO CAJIGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(438, 'GOTB721126381', 'fisica', NULL, 'afiliado', 'BEATRIZ GONZALEZ TORRES', NULL, 'BEATRIZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(439, 'PAMA480906', NULL, NULL, 'afiliado', 'ANTONIO PATIÑO MORENO', NULL, 'ANTONIO PATIÑO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(440, 'CPM191107GC6', 'moral', NULL, 'afiliado', 'CHEER PACK MEXICO', NULL, 'MANUEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(441, 'PRQ9708022N1', 'moral', NULL, 'afiliado', 'PINTURAS Y RECUBRIMIENTOS DE QUERETARO', NULL, 'FABIAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(442, 'ANX931215CQ8', 'moral', NULL, 'afiliado', 'AGROSRVICIOS DEL NOIRTE', NULL, 'VERONICA IBARRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(443, 'CSA160328QCA', 'moral', NULL, 'afiliado', 'CENADURIAS SANTIAGO', NULL, 'JAVIER MENDOZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(444, 'ENO8910131AA', 'moral', NULL, 'afiliado', 'EXCEL DEL NORTE', NULL, 'RICARDO CABALLERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(445, 'CQU131128TM9', 'moral', NULL, 'afiliado', 'CENADURIAS DE QUERETARO', NULL, 'GERENTE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(446, 'AUGA871015DF5', 'fisica', NULL, 'afiliado', 'ALEJANDRO HIRAM AGUILAR CORDILLO', NULL, 'JORGE A BELTRAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(447, 'AEHJ8502278S5', 'fisica', NULL, 'afiliado', 'JUAN CAROL ARREDONDO', NULL, 'TERESA ARREDONDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(448, 'PEO1910723QOC', 'fisica', NULL, 'afiliado', 'PEDRO ALBERTO RODRIGUEZ', NULL, 'PEDRO A. RODRIGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(449, 'LOH5570413H9Y', 'fisica', NULL, 'afiliado', 'SUSANA LOPEZ HINOJOSA', NULL, 'SUSANA LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(450, 'AAC091219LQ9', 'moral', '', 'afiliado', 'ABACO ABASTECEDORA DE LA CONSTRUCCION S.A. DE C.V.', '', 'ING. GERARDO CARBADILLO', '', NULL, NULL, NULL, 'COMERCIO', NULL, NULL, '[]', '[]', 0.00, '', '', 'Santiago de Querétaro', 'Querétaro', NULL, NULL, '', NULL, NULL, NULL, NULL, '', '', NULL, 22, 'A', 1, NULL, 1, 'alta_directa', '', 0, NULL, '2025-11-25 13:25:01', '2025-12-01 16:10:37'),
(451, 'F0LJ760309Q8A', 'fisica', NULL, 'afiliado', 'JUAN ANTONIO FLORES', NULL, 'JUAN ANTONIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(452, 'CC8605231N4', NULL, NULL, 'afiliado', 'CADENA COMERCIAL OXXO', NULL, 'MAYELI BEDOLLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(453, 'CUFR550324UM4', 'fisica', NULL, 'afiliado', 'RAFAEL CRUZ FAJARDO', NULL, 'SANDY OLVERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(454, 'RLA061214287', 'moral', NULL, 'afiliado', 'RINES Y LLANTAS AVILA', NULL, 'CP. EUGENIO GALICIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(455, 'CUGE871210TB3', 'fisica', NULL, 'afiliado', 'ELIZABETH CRUZ GRACIA', NULL, 'ELIZABETH CRUZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(456, 'JAGS840419M34', 'fisica', NULL, 'afiliado', 'SANDRA JARDINEZ GUERRERO', NULL, 'SANDRA JARDINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(457, 'RASJ870801330', 'fisica', NULL, 'afiliado', 'JORGE MANUEL RMZ DE SANTIAGO', NULL, 'JORGE M. RAMIREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(458, 'GAHD710717K66', 'fisica', NULL, 'afiliado', 'DON CUCO DANIEL CARMEN GARCIA', NULL, 'DANIEL CARMEN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(459, 'CMA8911176A4', 'moral', NULL, 'afiliado', 'CONIN MATERIALES', NULL, 'LIC. LUIS VEGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(460, 'EMA801028C14', 'moral', NULL, 'afiliado', 'EXCAVACIONES Y MATERIALES', NULL, 'LIC. LUIS VEGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(461, 'EME050706VA4', 'moral', NULL, 'afiliado', 'ENGIL DE MEXICO SA DE CV', NULL, 'C.P FILIBERTO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(462, 'FOOS640627RA7', 'fisica', NULL, 'afiliado', 'MARIA DEL SOCORRO FOL', NULL, 'C.P SOCORRO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(463, 'RESL860302VB6', 'fisica', NULL, 'afiliado', 'LIZBETH RESENDIZ SERVIN', NULL, 'LIC. LIZBETH RESENDIZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(464, 'RJO611027RE1', 'moral', NULL, 'afiliado', 'RESISTENCIAS JOV SA DE CV', NULL, 'LIC. ESTHER EVEGUIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(465, 'FEX2206094J9', 'moral', NULL, 'afiliado', 'FRESH EXPORTATION', NULL, 'MAGALY ZAMORA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(466, 'PAQ911223HN8', 'moral', NULL, 'afiliado', 'PLACA Y ACERO DE QUERETARO', NULL, 'TERE JIMENEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(467, 'FCO000803UC1', 'moral', NULL, 'afiliado', 'Fradma Comercial', NULL, 'C.P IRASEMA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(468, 'MS1190305KD3', 'moral', NULL, 'afiliado', 'MECALOR SOLUCIONES', NULL, 'GERARDO CANELO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(469, 'TIP8109219J4', 'moral', NULL, 'afiliado', 'TALLER INDUSTRIAL PIONEROI SA DE CV', NULL, 'MIGUE MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(470, 'ECA170216RPA', 'moral', NULL, 'afiliado', 'ESTRUCTURAS Y COMPONENTES DE ACERO', NULL, 'MIGUE MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(471, 'LOBP590509NH3', 'fisica', NULL, 'afiliado', 'PATRICIA LOPEZ BELTRAN', NULL, 'PATY LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(472, 'SACE990313JT4', 'fisica', NULL, 'afiliado', 'EDGAR SANCHEZ CEPEDA', NULL, 'EDGAR CEPEDA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(473, 'DIMV710605C8A', 'fisica', NULL, 'afiliado', 'VERONICA DIAZ MARTINEZ', NULL, 'VERONICA DIAS MTZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(474, 'HEMR640320DV5', 'fisica', NULL, 'afiliado', 'ROSALINA HERNANDEZ MARTINEZ', NULL, 'ROBERTO MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(475, 'CAAA761223F85', 'fisica', NULL, 'afiliado', 'ALFONZO CATANEDA ARGUELLES', NULL, 'ALFONSO CASTAÑEDA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(476, 'AAGS571112616', 'fisica', NULL, 'afiliado', 'FERNANDO ANSTACIO', NULL, 'SUSANA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(477, 'MIRR970428TX1', 'fisica', NULL, 'afiliado', 'REY DAVID MIER', NULL, 'REY DAVID', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(478, 'IOGJ771220N72', 'fisica', NULL, 'afiliado', 'JUAN CARLOS ILLOLDI GONZALEZ', NULL, 'LIC. ISABEL TREJO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(479, 'YIGS990212I22', 'fisica', NULL, 'afiliado', 'SANTIAGO IRIRS GONZALEZ', NULL, 'ANGELICA GONZALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(480, 'CAVV960523QRA', 'fisica', NULL, 'afiliado', 'VICTOR MANUEL CABRERA VEGA', NULL, 'VICTOR M. CABRERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(481, 'GHC170425M60', 'moral', NULL, 'afiliado', 'GCM HERRAMIENTAS Y COMPONENTES', NULL, 'ING. KATIA JONGUITUD', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(482, 'PPR060131MS6', 'moral', NULL, 'afiliado', 'POLIURETANOS Y PLASTICOS REFORZADOS', NULL, 'ING. KATIA JONGUITUD', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(483, 'GCM100317B39', 'moral', NULL, 'afiliado', 'GLOBAL COMPOSITES MANUFACTURING', NULL, 'ING. KATIA JONGUITUD', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(484, 'CIT30628920', NULL, NULL, 'afiliado', 'COMERCIALIZADORA DE INNOVACION', NULL, 'LIC ANA BRETON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(485, 'PBQ210408Q9A', 'moral', NULL, 'afiliado', 'PROYECTOS BROX DE QRO', NULL, 'OSCAR TERAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(486, 'OOOH960507512', 'fisica', NULL, 'afiliado', 'Héctor Emmanuel Oropeza', NULL, 'LIC. ERICKA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(487, 'VAGO840505FS5', 'fisica', NULL, 'afiliado', 'LF PANIFICADORA DE ARTE SA DE CV', NULL, 'ROSY BARRIENTOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(488, 'CCA081015HLA', 'moral', NULL, 'afiliado', 'FERRIONI INTERNAZIONALE SA DE CV', NULL, 'OSCAR ADRIAN MOGUEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(489, 'MABD980921NG8', 'fisica', NULL, 'afiliado', 'DAVID ROMMEL MAGOS BADILLO', NULL, 'DAVID ROMMEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(490, 'LAJD930831U6A', 'fisica', NULL, 'afiliado', 'DALIA CAROLOINA cALDERON jARQUIN', NULL, 'DALIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(491, 'VAUL9402011V4', 'fisica', NULL, 'afiliado', 'LARISSA ANDREA VAZQUEZ', NULL, 'CESAR URIBE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(492, 'IAHJ680628', NULL, NULL, 'afiliado', 'JUAN MARTIN IBARRA', NULL, 'JUAN MARTIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(493, 'UAMJ17916314B', 'fisica', NULL, 'afiliado', 'JUAN FRANCISCO UGALDE', NULL, 'JUAN FRANCISCO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(495, 'VAGS971221EO', 'moral', NULL, 'afiliado', 'JOSE SEBASTIAN VALDES GUERRERO', NULL, 'SEBASTIAN VALDES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(496, 'UME230221CG2', 'moral', NULL, 'afiliado', 'UHLMANN MEXICO', NULL, 'SILVERIO MANIERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(497, 'TOCR700225C86', 'fisica', NULL, 'afiliado', 'RICARDO TOWNSEND', NULL, 'IVONE PEREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(498, 'CEN180605309', 'moral', NULL, 'afiliado', 'CONCEPTOS Y ENTORNOS', NULL, 'ALEJANDRO GUZMAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(499, 'IQU930901P49', 'moral', NULL, 'afiliado', 'DAVID RAMIREZ ANGUIANO', NULL, 'INGRID', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(500, 'IAL830622P68', 'moral', NULL, 'afiliado', 'INDUSTRIAL DE ALAMBRES', NULL, 'JOSUE JONATHAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(501, 'NHO191119LD1', 'moral', NULL, 'afiliado', 'NAUHOME', NULL, 'ANGELICA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(502, 'JIGA6510125F4', 'fisica', NULL, 'afiliado', 'ARMANDO JIMÉNEZ GTZ', NULL, 'ANGELICA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(503, 'CGM160630H4', NULL, NULL, 'afiliado', 'COMERCIALIZADORA GM', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(504, 'DCI850718EL8', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA Y CONCVERTIDORA', NULL, 'DAVID RUFINO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(505, 'GTM141020JF7', 'moral', NULL, 'afiliado', 'GO TRADEMX', NULL, 'ALFREDO MANZANO D.', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(506, 'PZEP721015A25', 'fisica', NULL, 'afiliado', 'PATRICIA PEREZ RESENDIZ', NULL, 'MA. ARELLANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(507, 'PESV551223587', 'fisica', NULL, 'afiliado', 'VICTORIAPEREZ SOTELO', NULL, 'VICTORIA PEREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(508, 'OIN1903149LA', 'moral', NULL, 'afiliado', 'OPTIGRUP INDUSTRIAL', NULL, 'CP. GLORIA OSORIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(509, 'ARF1706212D4', 'moral', NULL, 'afiliado', 'ARFACOM', NULL, 'ING. FERNANDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(510, 'METE840213780', 'fisica', NULL, 'afiliado', 'ERIKA DANIELA MERA TAPIA', NULL, 'ERIKA DANIELA MERA TAPIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(511, 'CCP960702S63', 'moral', NULL, 'afiliado', 'CENTRAL DE COLORES PLASTICOS', NULL, 'MAGUILUZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(512, 'MAU9707183J2', 'moral', NULL, 'afiliado', 'MAUSOLEUM', NULL, 'DANIELA MOLINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(513, 'BAUZZ0405JV0', 'moral', NULL, 'afiliado', 'BAUMER AUTOMATIZACION', NULL, 'IGOR PINTO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(514, 'SSU220203B7A', 'moral', NULL, 'afiliado', 'EPL HOSPITALITY GROUP', NULL, 'XOCHITL GOMEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(515, 'MCA090526HSA', 'moral', NULL, 'afiliado', 'MANOS CAOACES', NULL, 'Gaby Diezmarina', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(516, 'EUR890525UF9', 'moral', NULL, 'afiliado', 'EUROQVIP', NULL, 'LIC FERNANDA URBINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(517, 'AAFE940615Q71', 'fisica', NULL, 'afiliado', 'ERIKA ALAVEZ FARFAN', NULL, 'DOLORES FARFAN/ERIKA ALAVEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10');
INSERT INTO `contacts` (`id`, `rfc`, `person_type`, `whatsapp`, `contact_type`, `business_name`, `commercial_name`, `owner_name`, `legal_representative`, `corporate_email`, `phone`, `position`, `industry`, `niza_classification`, `niza_custom_category`, `products_sells`, `products_buys`, `discount_percentage`, `commercial_address`, `fiscal_address`, `city`, `state`, `postal_code`, `google_maps_url`, `website`, `facebook`, `instagram`, `linkedin`, `twitter`, `whatsapp_sales`, `whatsapp_purchases`, `whatsapp_admin`, `profile_completion`, `completion_stage`, `journey_stage`, `journey_stage_updated`, `assigned_affiliate_id`, `source_channel`, `notes`, `is_validated`, `validated_by`, `created_at`, `updated_at`) VALUES
(518, 'DRI070702N25', 'moral', NULL, 'afiliado', 'DUAS RODAS INDUSTRIAL DE MEXICO', NULL, 'ALEJANDRO LORENZO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(519, 'VCR18004210H5', 'fisica', NULL, 'afiliado', 'CARLOS MANUEL VELAZQUEZ ROCILLO', NULL, 'ERICK', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(520, 'AEBG901209SL3', 'fisica', NULL, 'afiliado', 'GEORGINA ARELLANO BERNAL', NULL, 'GEORGINA ARELLANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(521, 'MME171108F3A', 'moral', NULL, 'afiliado', 'MISUMI MEXICO', NULL, 'ALEJANDRO HERRERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(522, 'INO141203SN3', 'moral', NULL, 'afiliado', 'INDUSTRIAS NOVACERAMICO', NULL, 'MIGUEL NEGRETE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(523, 'STS1705318C6', 'moral', NULL, 'afiliado', 'RIBALEC', NULL, 'LIZETH ACEVEDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(524, 'RME2008076K2', 'moral', NULL, 'afiliado', 'BLANCA VILA VENTAYOL', NULL, 'DANIEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(525, 'GTR1909231V0', 'moral', NULL, 'afiliado', 'JUAN JESUS FLORES LEON', NULL, 'LIC. VALERIO BRISEÑO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(526, 'FO63790404440', 'fisica', NULL, 'afiliado', 'JOSE LUIS SOTO OLIVARES', NULL, 'MARIANA GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(527, 'GUJM9601023U9', 'fisica', NULL, 'afiliado', 'INGENIERIA DISENO Y CONTROL DE AUTOMATIZACION', NULL, 'CINDYA JUAREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(528, 'CNB150514IB2', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA DE NICHOS', NULL, 'DANIELA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(529, 'OEZM700720ARA', 'fisica', NULL, 'afiliado', 'MARGARITA OLVERA', NULL, 'MARGARITA OLVERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(530, 'POCC900216116', 'fisica', NULL, 'afiliado', 'CELESTINA POBLANO', NULL, 'HECTOR MANUEL BONILLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(531, 'GUOG541211TN9', 'fisica', NULL, 'afiliado', 'GILBERTO GUEMES OLIVARES', NULL, 'GILBERTO GUEMES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(532, 'RAPM931027UC8', 'fisica', NULL, 'afiliado', 'MARCO ANTONIO RAMIEZ POLO', NULL, 'MARCO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(533, 'ROCJ870826', NULL, NULL, 'afiliado', 'JONATHAN ROJAS CORREA', NULL, 'FELIX HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(534, 'GULF790201BL1', 'fisica', NULL, 'afiliado', 'FEDERICO GUILLERMO LUIS', NULL, 'FEDERICO GUILLERMO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(535, 'TEC070807FC4', 'moral', NULL, 'afiliado', 'TECNISOLDADURAS', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(536, 'RETD9012086SA', 'fisica', NULL, 'afiliado', 'RESENDIZ TOMAS DIANA', NULL, 'DIANA MONICA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(537, 'PELC7203216TA', 'fisica', NULL, 'afiliado', 'CLAUDIA YCELA', NULL, 'CLAUDIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(538, 'YABL750 517IQ', 'fisica', NULL, 'afiliado', 'LORENA YANEZ', NULL, 'LORENA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(539, 'MEMA631081K1A', 'fisica', NULL, 'afiliado', 'Martin Raul Mejia', NULL, 'SR. MARTIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(540, 'NICL7310109Q8', 'fisica', NULL, 'afiliado', 'LILIANA EUGENIA NIETO CAMPOS', NULL, 'GABRIEL JASSO MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(541, 'JUCH821222K48', 'fisica', NULL, 'afiliado', 'HILDA IVONNE JUAREZ CHAVEZ', NULL, 'LIC. HILDA IVONNE JUAREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(542, 'ECS180508HE2', 'moral', NULL, 'afiliado', 'EMPRESA CULTURAL SUSTENTABLE LA NORIA', NULL, 'JUDITH GUERRERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(543, 'CAOJ730830K65', 'fisica', NULL, 'afiliado', 'CHAPARRO OPBRGON JUAN GAABRIEL', NULL, 'JUAN GABRIEL CHAPARRO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(544, 'FCO0310234W3', 'moral', NULL, 'afiliado', 'LA FERRE COMERCIALIZADORA SA DE CV', NULL, 'C.P ANA MARIA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(545, 'PFL860506256', 'moral', NULL, 'afiliado', 'POTENCIA FLUIDA', NULL, 'NADIA SLAINAS VALDES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(546, 'EAMJ9003073D6', 'fisica', NULL, 'afiliado', 'JULIO DAVID ESCAMILLA MATA', NULL, 'JD. ESCAMILLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(547, 'PIV1920131KM7', 'fisica', NULL, 'afiliado', 'ISABEL PIZANO VICENTE', NULL, 'ISABEL PIZANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(548, 'MUCB9707312R6', 'fisica', NULL, 'afiliado', 'BENJAMIN MUNOZ SANCHEZ', NULL, 'XIMENA RAMIREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(549, 'NUS3700331DM6', 'fisica', NULL, 'afiliado', 'BENJAMIN MUNOZ SANCHEZ', NULL, 'LOURDES BALDERAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(550, 'BLE010828IP0', 'moral', NULL, 'afiliado', 'BICICLETA LEON', NULL, 'FATIMA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(551, 'CAD130201GB6', 'moral', NULL, 'afiliado', 'CARBO ADITIVOS', NULL, 'ANA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(552, 'PRO101203V33', 'moral', NULL, 'afiliado', 'PROBELHER', NULL, 'WENDY OCHOA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(553, 'VED021212H81', 'moral', NULL, 'afiliado', 'VEDILAB', NULL, 'BEATRIZ SERRANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(554, 'ROEC680628JG9', 'fisica', NULL, 'afiliado', 'MARIA DEL CARMEN', NULL, 'MARIA DEL CARMEN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(555, 'CGA2109290F5', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA GAO', NULL, 'PATRICIA SANTA MARIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(556, 'URG210329TQ7', 'moral', NULL, 'afiliado', 'URGI', NULL, 'BRENDA CASTILLO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(557, 'CACX910922G27', 'fisica', NULL, 'afiliado', 'ALEJANDRA CHAVEZ CIVARRUBIAS', NULL, 'ALEJANDRA CHAVEZ ALBERTO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(558, 'VEVM561278VJA', 'fisica', NULL, 'afiliado', 'VICTOR RICARDO VERGARA MEDINA', NULL, 'VICTOR VERGARA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(559, 'OOCE790821QV4', 'fisica', NULL, 'afiliado', 'ELIZABETH NURHY OSORNIO CHAPARRO', NULL, 'ELIZABETH OSORNIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(560, 'OOCA810226HM0', 'fisica', NULL, 'afiliado', 'ANGELICA OSORNIO CHAPARRO', NULL, 'ELIZABETH OSORNIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(561, 'OOCR931217QU5', 'fisica', NULL, 'afiliado', 'JOSE ROBERTO OSORNIO CHAPARRO', NULL, 'ELIZABETH OSORNIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(562, 'MCA220831J33', 'moral', NULL, 'afiliado', 'MARY CATAMI SA DE CV', NULL, 'MARYBEL DEL CARMEN TAMEZ MIER', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(563, 'PECJ990618', NULL, NULL, 'afiliado', 'JUAN JOSE PEREZ DE LA CRUZ', NULL, 'JUAN JOSE PEREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-25 13:25:01'),
(564, 'PST0605031A4', 'moral', NULL, 'afiliado', 'PREMIUM STEEL', NULL, 'ANGELICA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(565, 'REL1609213W5', 'moral', NULL, 'afiliado', 'RL ELECTRO SA DE CV/ROBERTO ROSAS PEREZ', NULL, 'ROBERTO ROSAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(566, 'RMO940308B7A', 'moral', NULL, 'afiliado', 'RAVISA MOTORS SA DE CV', NULL, 'LIC. GERARDO ROBLEDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(567, 'PFI140411FK1', 'moral', NULL, 'afiliado', 'PETRO FIGUES', NULL, 'ALFREDO ORTA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(568, 'FAU050921HJ9', 'moral', NULL, 'afiliado', 'FERCO AUTOPARTES', NULL, 'NORI GOMEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(569, 'CAO240412189', 'moral', NULL, 'afiliado', 'COMERCIAL DE ACEROS OSC S DE RL DE CV', NULL, 'ELIZABETH OSORNIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(570, 'VACR770510D46', 'fisica', NULL, 'afiliado', 'RAUL DAVID VALBUENA CASTRO', NULL, 'NATALIE GONZALEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(571, 'AOMJ590101110', 'fisica', NULL, 'afiliado', 'ABRAHAM ARROYO MARQUEZ', NULL, 'ABRAHAM', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(572, 'GCI060417B34', 'moral', NULL, 'afiliado', 'GLOBIN CONTROL', NULL, 'CELESTE CERVANTES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(573, 'PRP210913897', 'moral', NULL, 'afiliado', 'PARAM RECUBRIMIENTOS', NULL, 'HUGO CORREA RIVERA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(574, 'CAMR960907KR8', 'fisica', NULL, 'afiliado', 'ADOLFO CASTAÑEDA', NULL, 'ADOLFO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(575, 'HCI1220912419', 'fisica', NULL, 'afiliado', 'HIPÉR CHINA', NULL, 'MONICA HDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(576, 'MUHI910207540', 'fisica', NULL, 'afiliado', 'IDALIA DEL SOCORRO MUNIZ', NULL, 'IDALIA MUÑIZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(577, 'SES7402068M2', 'moral', NULL, 'afiliado', 'SERVICACERO ESPECIALES', NULL, 'ALDO ROJAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(578, 'SCO960426CFA', 'moral', NULL, 'afiliado', 'SERVI CARNES OCCIDENTE', NULL, '987 111 50 61', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(579, 'ROCJ890404RE5', 'fisica', NULL, 'afiliado', 'JOEL ARTURO', NULL, 'JOEL ARTURO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(580, 'ING190208GW4', 'moral', NULL, 'afiliado', 'INGENICAM', NULL, 'SILVIA JIMENEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(581, 'TORR741109TY2', 'fisica', NULL, 'afiliado', 'RICARDO TORRES RICARDEZ', NULL, 'RICARDO TORRES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(582, 'GMP230426UFA', 'moral', NULL, 'afiliado', 'GHINO MEXICO PRECISION', NULL, 'JORGE VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(583, 'FSM990708526', 'moral', NULL, 'afiliado', 'FILTROS Y SERVICIOS DE MEXICO', NULL, 'MARICELA LEON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(584, 'DAZ221006KG1', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA AZTK', NULL, 'ERNESTO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(585, 'HEMD760615HN4', 'fisica', NULL, 'afiliado', 'DANIEL HDZ MEZA (EXPRESION VISUAL)', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(586, 'DOP220531C23', 'moral', NULL, 'afiliado', 'DISTRIBUIDORA oPTIVAL', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(587, 'SAJJ900409L19', 'fisica', NULL, 'afiliado', 'JESUS MAURICIO SALINAS', NULL, 'MAURICIO SALINAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(588, 'MOBD8310216D3', 'fisica', NULL, 'afiliado', 'DULCE MA IVETTE MORALES BALTAZAR', NULL, 'IVETTE MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(589, 'JMS090717EI8', 'moral', NULL, 'afiliado', 'JANOME MEXICO', NULL, 'RODRIGO CASTAÑEDA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(590, 'AAF1107272S1', 'moral', NULL, 'afiliado', 'ABASTECEDORA AVE FENIX', NULL, 'C.P GABRIELA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(591, 'ADE2008249U2', 'moral', NULL, 'afiliado', 'AGUA DESIONADA ECOPURA', NULL, 'ING. ARTURO GRANADOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(592, 'FBQ040818660', 'moral', NULL, 'afiliado', 'FERRE BAZTAN DE QUERETARO', NULL, 'JORGE VIEYRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(593, 'NANB740410341', 'fisica', NULL, 'afiliado', 'BERNARDO NAVARRO MATERAS', NULL, 'BERNARDO NAVARRO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(594, 'RTE2102095Y6', 'moral', NULL, 'afiliado', 'REYNOLDS TECHNOLOGIES', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(595, 'AMS1212101T9', 'moral', NULL, 'afiliado', 'AEE MARINE SERVICES SA DE CV', NULL, 'ERICK SALGADO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(596, 'EIQ201013L34', 'moral', NULL, 'afiliado', 'EGA INDUSTRIAL DE QUERETARO', NULL, 'ING. SERGIO ORDUÑA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(597, 'GAQ040526SZ1', 'moral', NULL, 'afiliado', 'GRUPO AQRUALITA', NULL, 'ANA LILIA MALAGON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(598, 'EMC191213CIA', 'moral', NULL, 'afiliado', 'EGA DE MEXICO', NULL, 'ANDREA ALVA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(599, 'SEM15505317RA', 'fisica', NULL, 'afiliado', 'IGNACIO SERRANO', NULL, 'IGNACIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(600, 'DOMH5201111G9', 'fisica', NULL, 'afiliado', 'HIGINIO DOMINGUEZ', NULL, 'HIQUINIO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(601, 'BOL09111398A', 'moral', NULL, 'afiliado', 'OSCAR BOTELLO LEAL', NULL, 'LEONARDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(602, 'SOAM5606106D3', 'fisica', NULL, 'afiliado', 'MARGARITA SOLANO', NULL, 'SEBASTIAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(603, 'BOL097111398A', 'fisica', NULL, 'afiliado', 'OSCAR BOTELLO LEAL', NULL, 'LEONARDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(604, 'NSM2002191H4', 'moral', NULL, 'afiliado', 'NOVA SOLUCIONES M M', NULL, 'JOSUE MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(605, 'ZSE020821RV8', 'moral', NULL, 'afiliado', 'ZAPATERIA SENDA', NULL, 'YAMILET NAVIDAD', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(606, 'SIC14030543A', 'moral', NULL, 'afiliado', 'SOLUCIONES INNOVACION Y COMERCIO', NULL, 'VALERIA GUZMAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(607, 'SAU220704TY6', 'moral', NULL, 'afiliado', 'SACASA AUTOMOTRIZ', NULL, 'ITZA BARRALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(608, 'HEQ1010081N4', 'moral', NULL, 'afiliado', 'HERRAMIENTAS ESPECIALIZADOS', NULL, 'LIC LAURA GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(609, 'DOAE830721PB8', 'fisica', NULL, 'afiliado', 'ERIKA JAZMIN DOMINGUEZ ALVAREZ', NULL, 'ERIKA DOMINGUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(610, 'AEMJ820915ALA', 'fisica', NULL, 'afiliado', 'JUAN CARLOS ALESSANDRINI', NULL, 'JUAN CARLOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(611, 'GFE000627QW0', 'moral', NULL, 'afiliado', 'GAFI FERRELECTRICO', NULL, 'ENRIQUE MAYA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(612, 'MCA9204083T8', 'moral', NULL, 'afiliado', 'MAQUINADOS CADIPSA', NULL, 'FCO DE SANTIAGO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(613, 'RIN140707JVA', 'moral', NULL, 'afiliado', 'RICHEL INVERNADEROS', NULL, 'LIC. MARISSA VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(614, 'DICL550703TP4', 'fisica', NULL, 'afiliado', 'JOSE LUIS DIAZ CERVANTES', NULL, 'ANAHI SINACHE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(615, 'PCN020703685', 'moral', NULL, 'afiliado', 'PRODUCCION CNC DE CV', NULL, 'JAQUELINE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(616, 'MAYC801003IV2', 'fisica', NULL, 'afiliado', 'CRISTIAN MARTINEZ YAÑEZ', NULL, 'CRISTIAN MTZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(617, 'ICO1902186C3', 'moral', NULL, 'afiliado', 'INX CONSULTORES', NULL, 'LI. RAFAEL MENEJES GOMEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:01', '2025-11-30 17:23:10'),
(618, 'AAHA920510T86', 'fisica', NULL, 'afiliado', 'ALFREDO AMADOR HERNANDEZ', NULL, 'ALFREDO AMADOR', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(619, 'MKA180410A53', 'moral', NULL, 'afiliado', 'MOLDES KAST', NULL, 'ALEJANDRA KAPLUN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(620, 'PAHR891226574', 'fisica', NULL, 'afiliado', 'RAFAEL ESTEBAN PALOMINO', NULL, 'RAFAEL PALOMINO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(621, 'TOAA860902TW5', 'fisica', NULL, 'afiliado', 'TOWS ALONSO ARN', NULL, 'ARN DOUGLAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(622, 'BSY04111773A', 'moral', NULL, 'afiliado', 'BIOCHEM SYSTEMS', NULL, 'LIC JUAREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(623, 'SACK770604JK3', 'fisica', NULL, 'afiliado', 'KAREN SANCHEZ CASTAÑEDA', NULL, 'KAREN SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(624, 'GME000724NL9', 'moral', NULL, 'afiliado', 'GROB MEXICO', NULL, 'ANAHI LIMON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(625, 'CPA0505309K3', 'moral', NULL, 'afiliado', 'CALZADO PAMPLONA', NULL, 'AMERICA PEREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(626, 'TFM9804032D2', 'moral', NULL, 'afiliado', 'TMD FRICTION', NULL, 'ANGELES GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(627, 'CON0305305G8', 'moral', NULL, 'afiliado', 'CONVAMEX', NULL, 'JAQUELINE MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(628, 'MACL800724P29', 'fisica', NULL, 'afiliado', 'JOSE LUIS MANDUJANO', NULL, 'ALMA ARIAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(629, 'JUBJ9701284UA', 'fisica', NULL, 'afiliado', 'MOENI', NULL, 'JORGE GERARDO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(630, 'ATQ990115KC5', 'moral', NULL, 'afiliado', 'ACEROS TRANSFORMADOS', NULL, 'ROSALINA PEREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(631, 'RAD8909307U0', 'moral', NULL, 'afiliado', 'RADEC', NULL, 'LUIS MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(632, 'IPI180427HM4', 'moral', NULL, 'afiliado', 'INGENIERIA DE PLASTICOS E IMPRESIIONES S DE RL DE CV', NULL, 'ARACELI RESENDIZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(633, 'BEPR791001JD5', 'fisica', NULL, 'afiliado', 'REGINA MARIA BECERRA PEREZ', NULL, 'REGINA BECERRA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(634, 'BAMR8304013G7', 'fisica', NULL, 'afiliado', 'REBECA BARCENAS MELGORE', NULL, 'REBECA BARCENAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(635, 'MEX010323CH7', 'moral', NULL, 'afiliado', 'MULTICOCINAS', NULL, 'NETZA MTZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(636, 'REX920309UD3', 'moral', NULL, 'afiliado', 'REXY', NULL, 'NORMA TAMAYO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(637, 'CAF880309V9A', 'moral', NULL, 'afiliado', 'CENTRO AUTOMTRIZ FUTURAMA', NULL, 'ABIMAEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(638, 'CDP9501269M5', 'moral', NULL, 'afiliado', 'DPORTENIS', NULL, 'HIRAM HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(639, 'RIR210306RS2', 'moral', NULL, 'afiliado', 'REPAIR AND INDUSTRIAL RECOVERY', NULL, 'FABIOLA FELICIANO SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(640, 'PEG230523948', 'moral', NULL, 'afiliado', 'PROYECTOS ESPECIALIZADOS GASOLINEROS SA DE CV', NULL, 'CLAUDIA JARERO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(641, 'RAAC861231R2', 'moral', NULL, 'afiliado', 'CARLOS AGUSTIN RAMIREZ AVILA', NULL, 'CARLOS AGUSTIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(642, 'LOSR931024U48', 'fisica', NULL, 'afiliado', 'RODOLFO AVNER', NULL, 'RODOLFO LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(643, 'RAML960912PRA', 'fisica', NULL, 'afiliado', 'LUIS ULISES RAMIREZ', NULL, 'LUIS ULISES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(644, 'EIDA8909043ZA', 'fisica', NULL, 'afiliado', 'ANTONIO DE JESUS', NULL, 'EMIRETH MORALES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(645, 'MCN080806F96', 'moral', NULL, 'afiliado', 'MARPOSS', NULL, 'PAMELA SANTILLANA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(646, 'AERB6810177N5', 'fisica', NULL, 'afiliado', 'CARNICERIA MIRIAM', NULL, 'MIRIAM', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(647, 'MEX130221BE5', 'moral', NULL, 'afiliado', 'MEXTAPE', NULL, 'FLORENCIO BELTRAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(648, 'GACP920 512QG', 'fisica', NULL, 'afiliado', 'BOTIQUE IZA', NULL, 'PAOLA GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(649, 'CUVA930326GC1', 'fisica', NULL, 'afiliado', 'ALINE MICHELLE CRUZ VELAZQUEZ', NULL, 'ALINE CRUZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10');
INSERT INTO `contacts` (`id`, `rfc`, `person_type`, `whatsapp`, `contact_type`, `business_name`, `commercial_name`, `owner_name`, `legal_representative`, `corporate_email`, `phone`, `position`, `industry`, `niza_classification`, `niza_custom_category`, `products_sells`, `products_buys`, `discount_percentage`, `commercial_address`, `fiscal_address`, `city`, `state`, `postal_code`, `google_maps_url`, `website`, `facebook`, `instagram`, `linkedin`, `twitter`, `whatsapp_sales`, `whatsapp_purchases`, `whatsapp_admin`, `profile_completion`, `completion_stage`, `journey_stage`, `journey_stage_updated`, `assigned_affiliate_id`, `source_channel`, `notes`, `is_validated`, `validated_by`, `created_at`, `updated_at`) VALUES
(650, 'GURI8002065TA', 'fisica', NULL, 'afiliado', 'ISRAEL GUERREO RIVERA', NULL, 'ROSA BARRERA OLGUIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(651, 'GAL2209276T2', 'moral', NULL, 'afiliado', 'GALVALOS', NULL, 'GABRIEL AVALOS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(652, 'SIN240503187', 'moral', NULL, 'afiliado', 'SM INDUCCION', NULL, 'ARACELI SOZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(653, 'FBE020818MD7', 'moral', NULL, 'afiliado', 'farmacia bermica', NULL, 'VERONICA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(654, 'AQM080531NX7', 'moral', NULL, 'afiliado', 'AIR QUALITY', NULL, 'JUAN MANUEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(655, 'SUPR690923112', 'fisica', NULL, 'afiliado', 'RODOLFO SUAREZ PADILLA', NULL, 'RODOLFO SUAREZ PADILLA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(656, 'RARJ700717GR8', 'fisica', NULL, 'afiliado', 'J.SANTOS RANGEL RODRIGUEZ', NULL, 'SANTOS RANGEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(657, 'GDI140508UA9', 'moral', NULL, 'afiliado', 'gs distribucion', NULL, 'DIEGO GIZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(658, 'MME160812J15', 'moral', NULL, 'afiliado', 'MINISO', NULL, 'JOAQUIN LOZANO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(659, 'KLM970416US9', 'moral', NULL, 'afiliado', 'KERN LIEBERS MEXICO', NULL, 'DIDIER RANGEL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(660, 'CAQ740111DA3', 'moral', NULL, 'afiliado', 'COMERCIAL AGROPECUARIO DE QUERETARO', NULL, 'LIC. JOSE ANTONIO NUÑEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(661, 'IHE090514PD3', 'moral', NULL, 'afiliado', 'IMPORTADORA HECLOVA', NULL, 'NOHEMI SERVIN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(662, 'FRE101123RV8', 'moral', NULL, 'afiliado', 'FRESHELL', NULL, 'GABRIELA RUIZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(663, 'GJV231110690', 'moral', NULL, 'afiliado', 'GRUPO JVLP', NULL, 'GERARDO LOLO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(664, 'MECS940103CG3', 'fisica', NULL, 'afiliado', 'SERGIO MEDINA CHAVEZ', NULL, 'SERGIO MEDINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(665, 'PME960701GG0', 'moral', NULL, 'afiliado', 'PRAXAIR MEXICO', NULL, 'NESTOR ABREHAM', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(666, 'RNA1611163E3', 'moral', NULL, 'afiliado', 'REINER NAFTA', NULL, 'YAGO HERNANZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(667, 'CRI080722NR2', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA DE REPUESTOS INDUSTRIALES', NULL, 'BEATRIZ VELA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(668, 'ELHM930428859', 'fisica', NULL, 'afiliado', 'MARITZEL ELIZALDE HERNANDEZ', NULL, 'MARITZEL ELIZALDE', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(669, 'SFB090513Q4', NULL, NULL, 'afiliado', 'SEMILLAS Y FORRAJES DEL BAJIO', NULL, 'FELIX GUZMAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-25 13:25:02'),
(670, 'DDE200622DYA', 'moral', NULL, 'afiliado', 'GEYSA DISENO EQUIPOS SERVICIOS', NULL, 'RH. SONIA GOMEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(671, 'EVE150717LW3', 'moral', NULL, 'afiliado', 'EXPERIENCIA VETERINARIA', NULL, 'YOLANDA ORGANIZTA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(672, 'JNF191219IA4', 'moral', NULL, 'afiliado', 'JRM NEGOCIOS FLUIDOS', NULL, NULL, NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(673, 'RICA9407088Y7', 'fisica', NULL, 'afiliado', 'ALEJANDRA RICO CALDERON', NULL, 'ALEJANDRA RICO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(674, 'RICJ8909061KA', 'fisica', NULL, 'afiliado', 'JESSICA ANGELES RICO CALDERON', NULL, 'ALEJANDRO RICO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(675, 'IWO140604610', 'moral', NULL, 'afiliado', 'INNOVATION WORSHOP', NULL, 'VALERIA SARAHI MEDINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(676, 'OBC9905057Y4', 'moral', NULL, 'afiliado', 'OERLIKON BALZERS COUTING', NULL, 'CP. ROSALBA SANCHEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(677, 'MCO851106AG0', 'moral', NULL, 'afiliado', 'MATERIALES CONSTRUCENTRO', NULL, 'C.P RAYMUNDO GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(678, 'EAZ9911183Y7', 'moral', NULL, 'afiliado', 'EXPO AZULEJO', NULL, 'ALEJANDRA LOPEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(679, 'CISC980224H78', 'fisica', NULL, 'afiliado', 'CHRISFIELD SALGADO', NULL, 'CHRISTIAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(680, 'TEE170627TP3', 'moral', NULL, 'afiliado', 'TECNOLOGIAS PARA ENVASADO', NULL, 'ANTONIO ROBLES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(681, 'CPS24050ZVD8', 'moral', NULL, 'afiliado', 'COMERCIALIZADORA DE PRODUCTOS SERVIA-ALL', NULL, 'FERNANDA GARCIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(682, 'VAGA010724EL7', 'fisica', NULL, 'afiliado', 'ANA PAOLA VAZQUEZ', NULL, 'ANA PAOLA VAZQUEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(683, 'ISO180913254', 'moral', NULL, 'afiliado', 'INNOVATELIA SOLUCIONES', NULL, 'MARIANA QUEZADA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(684, 'LBE1812065H7', 'moral', NULL, 'afiliado', 'LINARAND BELTECH', NULL, 'ADRIANA VEGA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(685, 'MOSE950512F78', 'fisica', NULL, 'afiliado', 'EMILIO MORENO SOBREYRA', NULL, 'EMILIO MORENO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(686, 'BIZL7709176L1', 'fisica', NULL, 'afiliado', 'LOURDES DEL CARMEN BRIONES ZAVALA', NULL, 'LOURDES BRIONES', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(687, 'CSM030620TJ2', 'moral', '', 'afiliado', 'LA CAPITAL DE SELLADA DEL MUNDO', '', 'JOSE ROBERTO ROMAN', '', NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, '', '', 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(688, 'GLE2007033K1', 'moral', NULL, 'afiliado', 'GRUPO CONSTRUCTOR ELECTRICO ISSEMEX', NULL, 'C.P ERANDY', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(689, 'PABE630916NU1', 'fisica', NULL, 'afiliado', 'EDGAR MIGUEL PACHECO BURLE', NULL, 'C.P ELIZABETH MOLINA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(690, 'EPM1404169A2', 'moral', NULL, 'afiliado', 'EXCALIBUR PLASTICS MEXICO', NULL, 'LIC. HITRON', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(691, 'PAA9709257J4', 'moral', NULL, 'afiliado', 'PROCESOS AMBIENTALES ALFA', NULL, 'SANDRA HERNANDEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(692, 'SME051013BQ2', 'moral', NULL, 'afiliado', 'TIENDAS FIX', NULL, 'NELLY MEJIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(693, 'PCI2212142J9', 'moral', NULL, 'afiliado', 'PROVEDORA COMERCIAL I2', NULL, 'LUIS PEREA MARTINEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(694, 'SAD190924H65', 'moral', NULL, 'afiliado', 'SISTEMA DE ALMACENAJE DASA', NULL, 'LUIS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(695, 'ASC120815UY8', 'moral', NULL, 'afiliado', 'ARES SUPPLY CHAIN', NULL, 'PAOLA ROSAS', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(696, 'SNI100217P11', 'moral', NULL, 'afiliado', 'SPACIO DE NICHOS', NULL, 'SAUL RICO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(697, 'AEMC711020A89', 'fisica', NULL, 'afiliado', 'CLAUDIA ELIZABETH ALEJO MORALES', NULL, 'PEDRO GOMEZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(698, 'HOCI870108P60', 'fisica', NULL, 'afiliado', 'IVAN HORTA', NULL, 'IVAN', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(699, 'HME250404LC2', 'moral', NULL, 'afiliado', 'HANSA FLEX MEXICO', NULL, 'MIGUEL CARVAJAL', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(700, 'CECJ011113786', 'fisica', NULL, 'afiliado', 'FRUTERIA CASTOÑO', NULL, 'JAVIER', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(701, 'ATC980728481', 'moral', NULL, 'afiliado', 'AIR TECHNOGY CORPORATION', NULL, 'JOSE ANTONIO ALMARAZ MEJIA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(702, 'PEMA030404INO', 'fisica', NULL, 'afiliado', 'ANA CAMILA PEREZ MARTOS', NULL, 'RODRIGO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(703, 'TMI9206099U8', 'moral', NULL, 'afiliado', 'TOC MAQUINADOS INDUSTRIALES', NULL, 'ROBERTO ALVAREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(704, 'NOM001101LTB', 'moral', NULL, 'afiliado', 'KLINGELNBERG MEXICO SA DE CV', NULL, 'C.P. ALFREDO BALANZA', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(705, 'MIRO91120TJ6', 'moral', NULL, 'afiliado', 'MANTOS IMPERMEABILIZANTES ROBVE', NULL, 'C.P. DULCE PEREZ', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(706, 'AMB101008NT3', 'moral', NULL, 'afiliado', 'AMBSIL', NULL, 'LI. ALICIA BRAVO', NULL, NULL, NULL, NULL, 'COMERCIO', NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Santiago de Querétaro', 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 17, 'A', 1, NULL, 1, 'alta_directa', NULL, 0, NULL, '2025-11-25 13:25:02', '2025-11-30 17:23:10'),
(713, 'EID150522K85', 'moral', '4422198567', 'afiliado', 'EXPERIENCIA DE IMPACTOS DIGITALES', 'IMPACTOS DIGITALES', 'Andrés Raso Ríos', 'Andrés Raso Ríos', 'andy@impactosdigitales.com', '4422956843', NULL, 'Servicios', '', NULL, '[\"Desarrollo de software y apps móviles\", \"Diseño de páginas web y tiendas en línea\", \"Cursos de capacitación en el manejo de redes sociales\", \"IA para negocios\", \"Marketing Digital\"]', '[\"Hosting y dominios\", \"Productos de limpieza de oficinas\"]', 0.00, 'Paseo de la Constitución # 100 interior 3 Villas del Parque', 'Paseo de la Constitución # 100 interior 3 Villas del Parque', 'Santiago de Querétaro', 'Querétaro', '', '', 'https://impactosdigitales.com', '', '', '', '', '4422198567', '4422198567', '', 55, 'B', 1, NULL, 2, 'alta_directa', 'Servicios de interés: networking, marketing, gestoria', 0, NULL, '2025-11-25 19:11:35', '2025-11-30 17:23:10'),
(715, 'PAMJ021105V85', 'fisica', '4427198013', 'afiliado', 'Jehú Pacheco', 'Jehú Pacheco', 'Jehú Pacheco', 'Jehú Pacheco', '422169938@derecho.unam.mx', '', NULL, 'Alimentos', '', NULL, NULL, NULL, 0.00, '', '', 'Santiago de Querétaro', 'Querétaro', '', '', '', '', '', '', '', '', '', '', 22, 'A', 1, NULL, 7, 'alta_directa', '', 0, NULL, '2025-11-25 20:19:57', '2025-11-30 17:23:10'),
(716, 'RARD7909214H6', 'fisica', '4425986318', 'prospecto', 'ID', '', 'Dan Jonathan Raso Rios', '', 'danjohn007@hotmail.com', '4424865389', NULL, '', NULL, NULL, NULL, NULL, 0.00, '', '', 'Santiago de Querétaro', 'Querétaro', '', '', 'https://impactosdigitales.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30, 'A', 1, NULL, 3, 'evento_pagado', '', 0, NULL, '2025-11-25 23:12:39', '2025-11-30 17:23:10'),
(717, 'ABC123456XYZ', 'moral', NULL, 'prospecto', 'UNAM', NULL, 'ALBERTO CUELLAR', NULL, 'soportecrm@camaradecomercioqro.mx', '4427198013', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-26 20:08:14', '2025-11-30 17:23:10'),
(719, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Javier ID', NULL, 'javier@id.com', '2971927239', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-27 17:54:48', '2025-11-27 17:54:48'),
(720, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Eva Balderas', NULL, 'evalderass13@hotmail.com', '4423810010', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-27 18:14:15', '2025-11-27 18:14:15'),
(722, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Graciela Ríos', NULL, 'chelitario@hotmail.com', '1298689126', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-27 20:00:52', '2025-11-27 20:00:52'),
(723, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Luis Raso', NULL, 'hola@residencial.digital', '9861239816', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-27 20:07:54', '2025-11-27 20:07:54'),
(724, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Jaime Jimenez', NULL, 'contacto@residencial.digital', '2153873512', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-27 20:18:56', '2025-11-27 20:18:56'),
(725, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Juanita', NULL, 'juanita@id.com', '7631681921', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-27 20:18:56', '2025-11-27 20:18:56'),
(726, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Graciela Medina', NULL, 'gacela5mariposa@gmail.com', '2635126767', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-27 23:30:27', '2025-11-27 23:30:27'),
(728, NULL, NULL, NULL, 'invitado', NULL, NULL, 'prueba', NULL, 'robert.forever21@gmail.com', '4421111111', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-27 23:37:43', '2025-11-27 23:37:43'),
(729, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Nadia Kenia', NULL, 'contacto@idindustrial.com.mx', '3628176328', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-27 23:46:10', '2025-11-27 23:46:10'),
(730, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Fulanito Perez', NULL, 'jehu121@outlook.es', '4421456789', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-27 23:49:56', '2025-11-27 23:49:56'),
(731, '', NULL, NULL, 'prospecto', NULL, NULL, 'Fernando Alberto Sandoval González', NULL, 'myf.sandoval.gonzalez@gmail.com', '4423328696', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 02:01:07', '2025-11-28 02:01:07'),
(732, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Wendoly Licona', NULL, 'wen@impactosdigitales.com', '2838998921', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 02:01:30', '2025-11-28 02:01:30'),
(733, 'RARA771202PB6', 'fisica', NULL, 'prospecto', NULL, NULL, 'Andrés Raso Ríos', NULL, 'andyraso@yahoo.com', '4422153397', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 02:25:26', '2025-11-30 17:23:10'),
(734, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Graciela M', NULL, 'software@impactosdigitales.com', '8357889311', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-28 02:26:35', '2025-11-28 02:26:35'),
(737, 'Fsu130806aj0', 'moral', NULL, 'prospecto', NULL, NULL, 'Sara Meza Maldonado', NULL, 'saramezam@gmail.com', '4423220646', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 02:50:39', '2025-11-30 17:23:10'),
(738, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Patricia Benson', NULL, 'patbenson1133@gmail.com', '4423913409', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 02:50:48', '2025-11-28 02:50:48'),
(739, 'Rhi120730ss8', 'moral', NULL, 'prospecto', NULL, NULL, 'Hill &amp; Co', NULL, 'taguilar@hillco.com.mx', '4421859754', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 02:50:52', '2025-11-30 17:23:10'),
(740, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Alberto López Ariza', NULL, 'tentacionesflores@yahoo.com.mx', '4424130355', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 02:50:52', '2025-11-28 02:50:52'),
(741, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Setria, S.A. de C.V.', NULL, 'jesus_sinecio@setria.com.mx', '4423519220', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 03:26:27', '2025-11-28 03:26:27'),
(742, 'HEHL7109018Y3', 'fisica', NULL, 'prospecto', NULL, NULL, 'Lima Servicios Inmobiliarios.', NULL, 'lilianherrera30@yahoo.com.mx', '4423228981', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 03:51:06', '2025-11-30 17:23:10'),
(743, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Jonathan Rios', NULL, 'tecnologia@idindustrial.com.mx', '8763186838', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-28 12:50:26', '2025-11-28 12:50:26'),
(745, 'CENTRODEESTUD', 'fisica', NULL, 'prospecto', NULL, NULL, 'Luis Núñez Salinas / CENTRO DE ESTUDIOS LONDRES QUERETARO / Sociedad Civil', NULL, 'l.nunez@udelondresqueretaro.com.mx', '4421300205', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 13:58:31', '2025-11-30 17:23:10'),
(746, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'José Antonio Ugalde Guerrero', NULL, 'a.ugalde@udelondresqueretaro.com.mx', '4421096690', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 13:58:31', '2025-11-28 13:58:31'),
(747, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Angélica González', NULL, 'agonzalez@grupoconcepto.com', '4421524998', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 19:15:07', '2025-11-28 19:15:07'),
(751, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Maria de los Angeles Garduño Mazy', NULL, 'maria.garduno@gorditasqueretanas.com', '4424798869', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 19:31:01', '2025-11-28 19:31:01'),
(752, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Gisela sainz', NULL, 'janeth_sainz@hotmail.com', '5545231240', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 19:34:36', '2025-11-28 19:34:36'),
(753, 'ONL210325D15', 'moral', NULL, 'prospecto', NULL, NULL, 'Operadora de Negocios LEVI', NULL, 'diana@onlevi.mx', '4421288283', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 19:49:06', '2025-11-30 17:23:10'),
(754, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Hector Villanueva Kuri', NULL, 'hector@onlevi.mx', '4423432128', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 19:49:06', '2025-11-28 19:49:06'),
(755, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Fabián Hütt Tigrillo Holding', NULL, 'tigrillosproduccion@gmail.com', '4121014446', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 19:50:12', '2025-11-28 19:50:12'),
(756, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Roberto Hernández castellanos', NULL, 'bbeto.look@gmail.com', '4424612256', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 19:54:39', '2025-11-28 19:54:39'),
(757, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Valita', NULL, 'anacristina@impactosdigitales.com', '1288988998', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-28 19:59:15', '2025-11-28 19:59:15'),
(758, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Ana', NULL, 'asistente@impactosdigitales.com', '9873937983', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-28 19:59:15', '2025-11-28 19:59:15'),
(759, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Eduardo Antonio Montes Granados', NULL, 'e.montes@mavemp.com', '4461339091', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-28 19:59:40', '2025-11-28 19:59:40'),
(760, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Rosario De la paz Abarca', NULL, 'r.delapaz@sferasolutions.com.mx', '4461339083', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-28 20:00:42', '2025-11-28 20:00:42'),
(763, 'Vebej721031gg', 'fisica', NULL, 'prospecto', NULL, NULL, 'Juan Velasco', NULL, 'gerente.comercial@tiendasaem.com', '4427213245', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 20:59:48', '2025-11-30 17:23:10'),
(764, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Jorge Arturo Carnaya Leissa', NULL, 'jcarnaya@me.com', '4421143440', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 21:01:50', '2025-11-28 21:01:50'),
(765, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Aranday Y Asociados', NULL, 'cesararanday@gmail.com', '4423599327', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 21:03:51', '2025-11-28 21:03:51'),
(766, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Don Bocol ( Alimentos Precongelados de la Huasteca', NULL, 'donbocolmx@gmail.com', '8341260673', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 21:13:27', '2025-11-28 21:13:27'),
(767, 'MAR960105E93', 'moral', NULL, 'prospecto', NULL, NULL, 'Marcozer SA de CV', NULL, 'aaronruvalcaba@marcozer.com.mx', '4423591266', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 21:18:24', '2025-11-30 17:23:10'),
(768, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Jane Rosas', NULL, 'jane@impactosdigitales.com', '8757857854', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 21:29:28', '2025-11-28 21:29:28'),
(769, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Alejandro Balderas', NULL, 'alejandro@impactosdigitales.com', '5787575921', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 21:29:28', '2025-11-28 21:29:28'),
(770, 'AUBM430611UjJ', 'fisica', NULL, 'prospecto', NULL, NULL, 'Unión empresas seguridad privada', NULL, 'servicorp10@hotmsil.com', '4422672486', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 22:01:53', '2025-11-30 17:23:10'),
(773, NULL, NULL, NULL, 'invitado', NULL, NULL, 'ELIZABETH RAMIREZ VILLANUEVA', NULL, 'elizarave21@gmail.com', '4461036790', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-28 22:19:22', '2025-11-28 22:19:22'),
(776, 'VAQ191015R42', 'moral', NULL, 'prospecto', NULL, NULL, 'MA DOLORES FARFAN PONS', NULL, 'vacoinalafarqro@gmail.com', '4422501366', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 00:48:07', '2025-11-30 17:23:10'),
(777, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Erika alavez farfan', NULL, 'elcantodelasranas@gmail.com', '4423531491', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 00:48:07', '2025-11-29 00:48:07'),
(778, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Gerardo Enrique Alavez Hernandez', NULL, 'santocantokaraoje@gmail.com', '4421072645', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 00:48:07', '2025-11-29 00:48:07'),
(779, NULL, NULL, NULL, 'invitado', NULL, NULL, 'VIDA ELITE AGENTES DE SEGUROS', NULL, 'aaguilarcruz@gmail.com', '4425020736', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 00:54:29', '2025-11-29 00:54:29'),
(780, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Kombucha Martha Trascend', NULL, 'marthacabezadevaca@gmail.com', '4422635465', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 00:59:32', '2025-11-29 00:59:32'),
(781, NULL, NULL, NULL, 'invitado', NULL, NULL, 'HOLDA JULIETA VEGA PAEZ', NULL, 'hjvegap@gmail.com', '5516670311', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 01:00:25', '2025-11-29 01:00:25'),
(782, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Ximena flores', NULL, 'ximenaftorres199@gmail.com', '4425107060', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 01:01:35', '2025-11-29 01:01:35'),
(783, NULL, NULL, NULL, 'invitado', NULL, NULL, 'SOS Al Rescate de tus ventas', NULL, 'alrescate.detusventas@comovendermas.com.mx', '4424050685', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 01:05:21', '2025-11-29 01:05:21'),
(784, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Karla Marcela Uribe Delgado', NULL, 'karlamarcelauribe@gmail.com', '4427150449', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 01:15:12', '2025-11-29 01:15:12'),
(785, 'AUBM430611UJA', 'fisica', NULL, 'prospecto', NULL, NULL, 'Mario Aguilar', NULL, 'servicorp@gmail.com', '4422672486', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 01:21:26', '2025-11-30 17:23:10'),
(786, NULL, NULL, NULL, 'invitado', NULL, NULL, 'SILVIA MERCEDES TRUJILLO SERVIN', NULL, 'viptravelqro@gmail.com', '4424499624', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 01:22:07', '2025-11-29 01:22:07'),
(787, 'RUGJ5001279S0', 'fisica', NULL, 'prospecto', NULL, NULL, 'Arturo Ruiz', NULL, 'arturoruizg@gmail.com', '4424884831', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 01:27:32', '2025-11-30 17:23:10'),
(788, NULL, NULL, NULL, 'invitado', NULL, NULL, 'SILVIA MERCEDES TRUJILLO SERVIN', NULL, 'strujilloderuiz@gmail.com', '4424499624', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 01:32:50', '2025-11-29 01:32:50'),
(789, NULL, NULL, NULL, 'invitado', NULL, NULL, 'CDN Mujeres de Empresa', NULL, 'smartinez_0405@gmail.com', '4421220764', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 02:06:49', '2025-11-29 02:06:49'),
(790, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Sergio Buenrostro Rodriguez/ international lean six sig a', NULL, 'Queretaro@ilssg.org', '4423179572', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 02:12:56', '2025-11-29 02:12:56'),
(791, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Claudia Eloisa Fuentes García', NULL, 'claudiafuentesgarcia@gmail.com', '5534448653', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 02:22:35', '2025-11-29 02:22:35'),
(792, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Lucía Mejia', NULL, 'apps@impactosdigitales.com', '3332341242', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 02:49:23', '2025-11-29 02:49:23'),
(793, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Lupita Rosas', NULL, 'emarketing@impactosdigitales.com', '8723575817', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 03:40:00', '2025-11-29 03:40:00'),
(794, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Especialista en gimnasios', NULL, 'jaquilinda1002@gmail.com', '4425825980', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 05:00:09', '2025-11-29 05:00:09'),
(795, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Universidad Atenas', NULL, 'direccion@atenas.edu.mx', '4422302231', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 14:39:17', '2025-11-29 14:39:17'),
(797, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Francisco Vargas', NULL, 'direccioncomercial@tiendasaem.com', '4423597535', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 17:43:12', '2025-11-29 17:43:12'),
(798, 'DBV840717386', 'moral', NULL, 'prospecto', NULL, NULL, 'Distribuidora Volkswagen del Bajio S.A. de C.V.', NULL, 'ernesto.mancera@camionesbajio.com', '4425121347', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 17:49:13', '2025-11-30 17:23:10'),
(799, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'ADRIANA RUIZ GARCIA', NULL, 'adriana.ruiz@vw-camionesbajio.com', '4427835060', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 17:49:13', '2025-11-29 17:49:13'),
(800, 'PARA750406CR2', 'fisica', NULL, 'prospecto', NULL, NULL, 'Corporativo Paredes', NULL, 'gcparedes97@hotmail.com', '4422810713', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 17:58:29', '2025-11-30 17:23:10'),
(802, 'RARE8611072H0', 'fisica', NULL, 'prospecto', NULL, NULL, 'International lean six sigma', NULL, 'admisiones.queretaro@ilssg.org', '5534004041', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 20:01:15', '2025-11-30 17:23:10'),
(803, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Guillermo Justo Tapia', NULL, 'gjusto65@gmail.com', '4462188037', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 22:01:07', '2025-11-29 22:01:07'),
(804, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Guillermina Hernández', NULL, 'g.hernandez28@outlook.com', '4421199994', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-29 23:09:56', '2025-11-29 23:09:56'),
(805, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Gabriela Ruiz Ruiz', NULL, 'ventas2@fabricantesderacks.com.mx', '4425401290', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-11-30 03:54:06', '2025-11-30 03:54:06');
INSERT INTO `contacts` (`id`, `rfc`, `person_type`, `whatsapp`, `contact_type`, `business_name`, `commercial_name`, `owner_name`, `legal_representative`, `corporate_email`, `phone`, `position`, `industry`, `niza_classification`, `niza_custom_category`, `products_sells`, `products_buys`, `discount_percentage`, `commercial_address`, `fiscal_address`, `city`, `state`, `postal_code`, `google_maps_url`, `website`, `facebook`, `instagram`, `linkedin`, `twitter`, `whatsapp_sales`, `whatsapp_purchases`, `whatsapp_admin`, `profile_completion`, `completion_stage`, `journey_stage`, `journey_stage_updated`, `assigned_affiliate_id`, `source_channel`, `notes`, `is_validated`, `validated_by`, `created_at`, `updated_at`) VALUES
(806, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'ing. Adrian Cortes Lozada', NULL, 'adrian@limainmobiliaria.com.mx', '4461036150', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-30 04:12:36', '2025-11-30 04:12:36'),
(807, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Corporativo IEDU', NULL, 'corporativo@institutoiedu.com', '5538977742', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-30 15:49:21', '2025-11-30 15:49:21'),
(808, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Corporativo Iedu', NULL, 'ivan.orzco24@gmail.com', '5538977742', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-11-30 15:51:13', '2025-11-30 15:51:13'),
(809, 'GOTM860908FV2', NULL, NULL, 'prospecto', NULL, NULL, 'MONICA LIZBETH GOMEZ TAPIA', NULL, 'ml.gomez.tapia@gmail.com', '4424953035', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 00:55:24', '2025-12-01 00:55:24'),
(810, 'FOEK770128RT6', NULL, NULL, 'prospecto', NULL, NULL, 'Quiropractico Juriqulla', NULL, 'kaflo_22@yahoo.com', '5534449014', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-12-01 02:51:40', '2025-12-01 02:51:40'),
(811, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Miel Gallardo', NULL, 'keidagallardoo@gmail.com', '4422317676', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 15:15:03', '2025-12-01 15:15:03'),
(812, 'LAC200624MV4', NULL, '4421234567', 'prospecto', 'Asesoría Legal', '', 'Juan Francisco Mena Vega', '', 'juan.mena@weadvise.mx', '4421229205', NULL, '', NULL, NULL, NULL, NULL, 0.00, '', '', 'Santiago de Querétaro', 'Querétaro', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, 9, 'evento_gratuito', '', 0, NULL, '2025-12-01 15:41:28', '2025-12-01 17:03:54'),
(813, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Karla Kassandra de la Vega de la Garza', NULL, 'karla.delavega@weadvise.mx', '4428510010', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 15:41:28', '2025-12-01 15:41:28'),
(814, 'GCI190703RG7', NULL, NULL, 'prospecto', NULL, NULL, 'Grupo Comercial e Integral ONR de México', NULL, 'edgar.rodriguez@onr.mx', '4428688751', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-12-01 17:19:01', '2025-12-01 17:19:01'),
(816, NULL, NULL, NULL, 'invitado', NULL, NULL, 'AMEXME', NULL, 'pao_soria@hotmail.com', '4421878883', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 18:10:56', '2025-12-01 18:10:56'),
(817, NULL, NULL, NULL, 'invitado', NULL, NULL, 'AMEXME', NULL, 'presidenta.cap.queretaro@gmail.com', '4421878883', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 18:12:15', '2025-12-01 18:12:15'),
(820, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Iliana Sánchez Villanueva', NULL, 'ilisanchez@inna.mx', '4424458051', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 19:26:09', '2025-12-01 19:26:09'),
(821, 'Pebj871010qy3', NULL, NULL, 'prospecto', NULL, NULL, 'Jimmy Javier peña Baltazar', NULL, 'jimmyelcontratista@gmail.com', '4423795309', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 19:31:44', '2025-12-01 19:31:44'),
(822, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Shima Avelino', NULL, 'avelino.cedillo.gina@gmail.com', '4428499050', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 19:32:46', '2025-12-01 19:32:46'),
(823, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Corruempaques', NULL, 'ycaballero@corruempaques.com', '4421730862', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 19:34:55', '2025-12-01 19:34:55'),
(824, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Gina Cedillo', NULL, 'ginaave75@hotmail.com', '4427491275', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 19:36:50', '2025-12-01 19:36:50'),
(825, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Industria Marmolera Queretana S. De R.L. de C.V.', NULL, 'ernesto.ernests@gmail.com', '4423016895', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 20:59:12', '2025-12-01 20:59:12'),
(826, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Instituto Dicormo', NULL, 'direccion@dicormo.com', '4432790608', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 21:03:58', '2025-12-01 21:03:58'),
(827, 'MOHO040411HQT', NULL, '3333333333', 'prospecto', 'fiscal', 'prueba', 'prueba', '', 'prueba@gmail.com', '4444444444', NULL, 'giro', NULL, NULL, NULL, NULL, 0.00, 'Prueba', '', 'Santiago de Querétaro', 'Querétaro', '75555', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 29, 'A', 1, NULL, 1, 'alta_directa', 'ninguna por hacker', 0, NULL, '2025-12-01 21:44:46', '2025-12-01 21:44:46'),
(828, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Isabel Bonilla', NULL, 'isabel.bonilla@cesba-queretaro.edu.mx', '4424716505', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 22:08:47', '2025-12-01 22:08:47'),
(829, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Mis Soluciones', NULL, 'asanchez@missoluciones.mx', '4424120012', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 22:27:11', '2025-12-01 22:27:11'),
(830, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Ale Kornhauser/ Helados Gina', NULL, 'eventosheladosgina@gmail.com', '4424238613', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 22:45:56', '2025-12-01 22:45:56'),
(831, 'BIN210902322', NULL, NULL, 'prospecto', NULL, NULL, 'BEEHOKS INDUSTRIAS', NULL, 'jlfernandez@beehoks.com', '4423448875', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 22:53:20', '2025-12-01 22:53:20'),
(834, 'CACC8410267Q0', NULL, NULL, 'prospecto', NULL, NULL, 'Cesar Chavez Cornejo / CC Seguros', NULL, 'ccseguros3@gmail.com', '4424538159', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 22:57:27', '2025-12-01 22:57:27'),
(835, NULL, NULL, NULL, 'invitado', NULL, NULL, 'MexiCases', NULL, 'ceo@mexicases.com', '4421412241', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 22:58:10', '2025-12-01 22:58:10'),
(836, NULL, NULL, NULL, 'invitado', NULL, NULL, 'FUMINEITOR S.A', NULL, 'fumineitor1993@hotmail.com', '4422048783', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 23:03:46', '2025-12-01 23:03:46'),
(837, 'JGL200619DX1', NULL, NULL, 'prospecto', NULL, NULL, 'Sends Logística Aduanal', NULL, 'comercial@sends.com.mx', '4427744415', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 23:12:00', '2025-12-01 23:12:00'),
(839, 'Nusl710505387', NULL, NULL, 'prospecto', NULL, NULL, 'Luís Eduardo Nuñez Silva /', NULL, 'lui.hanei@gmail.com', '4424140632', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 23:33:27', '2025-12-01 23:33:27'),
(841, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Y.K.H. Management &amp; Solutions', NULL, 'yankofk@gmail.com', '4423210809', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 23:51:50', '2025-12-01 23:51:50'),
(842, 'SMM160929EV8', NULL, NULL, 'prospecto', NULL, NULL, 'Soluciones Migura', NULL, 'asantos@solucionesmigura.com', '5540842166', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-01 23:55:37', '2025-12-01 23:55:37'),
(843, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Ricardo García Rodríguez', NULL, 'capbusiness.contacto@gmail.com', '4423280033', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 00:51:16', '2025-12-02 00:51:16'),
(844, 'TTS1508018L0', NULL, NULL, 'prospecto', NULL, NULL, 'Telener360', NULL, 'doam@telener360.com', '4422597343', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 00:58:21', '2025-12-02 00:58:21'),
(845, 'OILA610613R66', NULL, NULL, 'prospecto', NULL, NULL, 'OLA TIRES', NULL, 'ssllqro@gmail.com', '4422260689', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 01:07:05', '2025-12-02 01:07:05'),
(846, 'AIRA750920B11', NULL, NULL, 'prospecto', NULL, NULL, 'Adla Athie/Grupo Kyza', NULL, 'ceo@grupokyza.com', '5554196809', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 01:20:37', '2025-12-02 01:20:37'),
(847, NULL, NULL, NULL, 'colaborador_empresa', NULL, NULL, 'Jorge Ceballos', NULL, 'tecnologia@grupokyza.com', '5548876011', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 01:20:37', '2025-12-02 01:20:37'),
(848, 'CIT130628920', NULL, NULL, 'prospecto', NULL, NULL, 'CIT FESTO', NULL, 'ana.breton@comercializadora-it.com', '4423944787', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 02:24:16', '2025-12-02 02:24:16'),
(849, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Silvia Rodríguez Díaz', NULL, 'silvia.rdz@live.com.mx', '4441262017', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 02:49:11', '2025-12-02 02:49:11'),
(850, 'OIAJ910404AJ7', NULL, NULL, 'prospecto', NULL, NULL, 'OLA TIRES', NULL, 'ja.olatirrs001@gmail.com', '4424790400', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 03:07:02', '2025-12-02 03:07:02'),
(851, 'AALL810103CP3', NULL, NULL, 'prospecto', NULL, NULL, 'Lapstec Querétaro', NULL, 'lalmaguer81@gmail.com', '4423380483', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 03:27:21', '2025-12-02 03:27:21'),
(852, 'GII160527S96', NULL, NULL, 'prospecto', NULL, NULL, 'GMC Ingeniería', NULL, 'jorge.garcia@gmcingenieria.mx', '5518010714', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 05:04:57', '2025-12-02 05:04:57'),
(853, NULL, NULL, NULL, 'invitado', NULL, NULL, 'Aaron Chavez Diaz Gonzalez', NULL, 'achavez@avilacommunications.com.mx', '4427514299', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_pagado', NULL, 0, NULL, '2025-12-02 05:36:15', '2025-12-02 05:36:15'),
(854, NULL, NULL, NULL, 'invitado', NULL, NULL, 'GUILLERMO JESUS', NULL, 'guille2gonza@hotmail.com', '4422022750', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 13:54:27', '2025-12-02 13:54:27'),
(856, NULL, NULL, NULL, 'invitado', NULL, NULL, 'QTequeñitos', NULL, 'jesusorlando20@gmail.com', '5586764497', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 'Querétaro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 'A', 1, NULL, NULL, 'evento_gratuito', NULL, 0, NULL, '2025-12-02 15:54:33', '2025-12-02 15:54:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_branches`
--

CREATE TABLE `contact_branches` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_maps_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `council_members`
--

CREATE TABLE `council_members` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `member_type` enum('propietario','invitado') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Council member type',
  `start_date` date NOT NULL COMMENT 'Start date in council',
  `end_date` date DEFAULT NULL COMMENT 'End date (null if still active)',
  `position` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Position in council',
  `status` enum('active','inactive','pending_approval') COLLATE utf8_unicode_ci DEFAULT 'pending_approval',
  `approved_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'User who approved the membership',
  `approval_date` datetime DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Council members - requires 2+ years of continuous affiliation';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `event_type` enum('interno','publico','terceros') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'interno',
  `category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'desayuno, open_day, conferencia, feria, exposicion, curso, taller, expo, networking, webinar',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `google_maps_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT '0',
  `online_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_capacity` int(10) UNSIGNED DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `promo_price` decimal(10,2) DEFAULT '0.00' COMMENT 'Precio de preventa/promoción',
  `promo_end_date` datetime DEFAULT NULL COMMENT 'Fecha límite de preventa (anterior al evento)',
  `member_price` decimal(10,2) DEFAULT '0.00',
  `promo_member_price` decimal(10,2) DEFAULT '0.00' COMMENT 'Precio de preventa para afiliados',
  `free_for_affiliates` tinyint(1) DEFAULT '1' COMMENT 'Si está activo, afiliados vigentes obtienen 1 acceso gratis',
  `registration_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('draft','published','cancelled','completed') COLLATE utf8_unicode_ci DEFAULT 'draft',
  `target_audiences` json DEFAULT NULL COMMENT 'afiliados, prospectos, publico, etc.',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `room_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nombre del salón donde se realizará el evento',
  `room_capacity` int(10) UNSIGNED DEFAULT NULL COMMENT 'Capacidad total del salón',
  `allowed_attendees` int(10) UNSIGNED DEFAULT NULL COMMENT 'Número de asistentes permitidos (puede ser diferente a la capacidad)',
  `has_courtesy_tickets` tinyint(1) DEFAULT '1' COMMENT 'Si el evento de pago permite cortesías (0=sin cortesías, 1=con cortesías)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_type`, `category`, `start_date`, `end_date`, `location`, `address`, `google_maps_url`, `is_online`, `online_url`, `max_capacity`, `is_paid`, `price`, `promo_price`, `promo_end_date`, `member_price`, `promo_member_price`, `free_for_affiliates`, `registration_url`, `image`, `status`, `target_audiences`, `created_by`, `created_at`, `updated_at`, `room_name`, `room_capacity`, `allowed_attendees`, `has_courtesy_tickets`) VALUES
(1, 'Networking Empresarial Enero 2025', 'Evento de networking para socios y prospectos de la Cámara de Comercio', 'interno', 'Networking', '2025-01-20 18:00:00', '2025-01-20 21:00:00', 'Salón Principal CCQ', 'Av. 5 de Febrero No. 412, Centro', NULL, 0, NULL, NULL, 0, 0.00, 0.00, NULL, 0.00, 0.00, 1, 'networking-enero-2025', NULL, 'published', '[\"afiliado\", \"prospecto\"]', 1, '2025-11-25 02:36:56', '2025-11-25 02:36:56', NULL, NULL, NULL, 1),
(2, 'Curso: Marketing Digital para PyMEs', 'Aprende estrategias de marketing digital efectivas para tu negocio', 'interno', 'Capacitación', '2025-02-05 09:00:00', '2025-02-05 14:00:00', 'Aula de Capacitación CCQ', 'Av. 5 de Febrero No. 412, Centro', NULL, 0, NULL, NULL, 1, 1500.00, 1275.00, '2025-01-29 09:00:00', 1000.00, 850.00, 1, 'marketing-digital-pymes', NULL, 'published', '[\"afiliado\", \"prospecto\", \"publico\"]', 1, '2025-11-25 02:36:56', '2025-11-27 23:23:29', NULL, NULL, NULL, 1),
(3, 'Foro de Desarrollo Económico 2025', 'Principales tendencias económicas para el sector empresarial', '', 'Conferencia', '2026-03-15 10:00:00', '2026-03-15 18:00:00', 'Centro de Congresos Querétaro', 'Blvd. Bernardo Quintana', '', 0, '', 0, 1, 500.00, 425.00, '2026-03-08 10:00:00', 0.00, 350.00, 1, 'foro-economico-2025', 'event_1764075982_dcda250f9160ab00.png', 'draft', '[\"afiliado\", \"publico\", \"funcionario\"]', 4, '2025-11-25 02:36:56', '2025-11-27 23:23:29', NULL, NULL, NULL, 1),
(4, 'Webinar: Nuevas Regulaciones Fiscales 2025', 'Todo lo que necesitas saber sobre los cambios fiscales', 'interno', 'Webinar', '2025-01-30 17:00:00', '2025-01-30 18:30:00', 'Online', NULL, NULL, 1, NULL, NULL, 0, 0.00, 0.00, NULL, 0.00, 0.00, 1, 'webinar-fiscal-2025', NULL, 'published', '[\"afiliado\", \"prospecto\"]', 1, '2025-11-25 02:36:56', '2025-11-25 02:36:56', NULL, NULL, NULL, 1),
(5, 'Posada Open Day', 'Negocios, amistad y espíritu navideño: la mejor combinación para cerrar el año con magia de los comercios afiliados a nuestro organismo empresarial.', 'interno', 'Networking', '2025-11-28 06:28:00', '2025-11-29 10:28:00', '', '', '', 1, '', 100, 0, 0.00, 0.00, NULL, 0.00, 0.00, 1, 'posada-open-day', NULL, 'draft', '[\"publico\"]', 1, '2025-11-25 12:28:52', '2025-11-25 12:28:52', NULL, NULL, NULL, 1),
(6, 'POSADA OPEN DAY', 'Porque los negocios también se celebran.\r\nÚnete a nosotros en una noche llena de conexión, alegría y espíritu emprendedor.\r\nNegocios, amistad y espíritu navideño: la mejor combinación para cerrar el año con magia.\r\nPaga antes del 05 de diciembre y obtén el precio de preventa $ 500 pesos', 'interno', 'Networking', '2025-12-10 19:00:00', '2025-12-10 23:14:00', 'Salón Presidentes', 'Luis Vega y Monroy # 405 Col. Balaustradas', 'https://www.google.com/maps/place/Canaco/@20.5763867,-100.3845014,17z/data=!3m1!4b1!4m6!3m5!1s0x85d344c51461cc3f:0x3a5c3e33a3170734!8m2!3d20.5763817!4d-100.3819265!16s%2Fg%2F1tdvdy8s?entry=ttu&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;g_ep=EgoyMDI1MTExNy4wIKXMDSoASAFQAw%3D%3D', 0, '', 250, 1, 850.00, 500.00, '2025-12-03 19:00:00', 500.00, 500.00, 1, 'openday', 'event_1764629724_11fea04a6f04bc3d.jpg', 'published', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\"]', 1, '2025-11-25 13:15:03', '2025-12-01 22:55:24', NULL, NULL, NULL, 1),
(7, 'PRUEBA EVENTO GRATUITO', 'PRUEBA PARA BOLETO', 'terceros', 'Conferencia', '2025-11-30 10:59:00', '2025-11-30 12:00:00', '', '', '', 0, '', 100, 0, 0.00, 0.00, NULL, 0.00, 0.00, 1, 'gratuito', 'event_1764232804_eaa04a660795c93a.jpg', 'published', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\"]', 1, '2025-11-26 17:00:33', '2025-11-27 08:40:04', NULL, NULL, NULL, 1),
(8, 'prueba evento tipo open day', '', 'terceros', '', '2025-11-30 19:00:00', '2025-11-30 20:16:00', '', '', '', 0, '', 0, 1, 850.00, 722.50, '2025-11-23 19:00:00', 500.00, 425.00, 1, 'prueba-evento-tipo-open-day', 'event_1764184631_12999121863e9f2c.jpg', 'draft', '[]', 1, '2025-11-26 19:17:11', '2025-11-27 23:23:29', NULL, NULL, NULL, 1),
(9, 'Open Prueba', 'Porque los negocios también se celebran.\r\nÚnete a nosotros en una noche llena de conexión, alegría y espíritu emprendedor.\r\nNegocios, amistad y espíritu navideño: la mejor combinación para cerrar el año con magia.\r\nPaga antes del 05 de diciembre y obtén el precio de preventa $ 500 pesos', 'terceros', 'Networking', '2025-12-07 02:56:00', '2025-12-07 06:56:00', 'Salón Presidentes', 'Canaco', 'https://maps.app.goo.gl/Y7Ub2hDXby8LwSHy5', 0, '', 0, 1, 0.00, 0.00, '2025-11-30 02:56:00', 0.00, 0.00, 1, 'open-prueba', 'event_1764249723_f3c066c95d308958.png', 'published', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"consejero\"]', 1, '2025-11-27 09:11:16', '2025-11-27 23:23:29', NULL, NULL, NULL, 1),
(10, 'Evento con Preventa', 'y lector QR', 'publico', 'Exposición', '2025-12-27 07:34:00', '2025-12-27 13:34:00', '', '', '', 1, '', 10, 1, 10.00, 8.00, '2025-12-20 07:34:00', 7.00, 5.00, 1, 'con-preventa', 'event_1764359368_8602f16db208bd65.png', 'published', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\", \"patrocinador_mesa\", \"colaborador_empresa\"]', 1, '2025-11-27 13:35:26', '2025-11-28 19:49:28', NULL, NULL, NULL, 1),
(11, 'BRUNCH con Felifer Macias', 'Te invitamos a una mañana exclusiva de conexión y diálogo con el alcalde de Querétaro Felifer Macías. \r\nUn espacio para conversar, conectar y fortalecer nuestra comunidad empresarial.', 'terceros', 'Brunch', '2025-12-04 09:00:00', '2025-12-04 11:00:00', 'Salón Presidentes', 'Av. Luis Vega y Monroy #405, Quinta Balaustradas, Santiago de Querétaro, Qro.', 'https://maps.app.goo.gl/xSgmxEmhgJf9Vzm28', 0, '', 200, 0, 0.00, 0.00, '0000-00-00 00:00:00', 0.00, 0.00, 1, 'brunchconfelifer', 'event_1764262244_3e54480698d92e2a.jpg', 'draft', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\", \"patrocinador_mesa\", \"colaborador_empresa\"]', 1, '2025-11-27 16:50:44', '2025-11-27 16:50:44', NULL, NULL, NULL, 1),
(12, 'BRUNCH con Felifer Macías', 'Te invitamos a una mañana exclusiva de conexión y diálogo con el alcalde de Querétaro Felifer Macías.\r\nUn espacio para conversar, conectar y fortalecer nuestra comunidad empresarial.', 'terceros', 'Foro', '2025-12-04 08:30:00', '2025-12-04 11:30:00', 'Salón Presidentes', 'Av. Luis Vega y Monroy #405, Quinta Balaustradas, Santiago de Querétaro, Qro.', 'https://maps.app.goo.gl/xSgmxEmhgJf9Vzm28', 0, '', 250, 0, 0.00, 0.00, '0000-00-00 00:00:00', 0.00, 0.00, 1, 'brunchconfelifermacias', 'event_1764630373_01afa39e2ca49cb6.jpg', 'published', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\", \"patrocinador_mesa\"]', 1, '2025-11-27 16:58:15', '2025-12-01 23:06:13', NULL, NULL, NULL, 1),
(13, 'Confirmar Preventa', '20 pesos', 'terceros', 'Jóvenes Empresarios', '2025-12-26 14:31:00', '2025-11-27 18:32:00', '', '', '', 0, '', 0, 1, 100.00, 15.00, '2025-11-30 14:33:00', 10.00, 0.00, 1, 'confirmar-preventa', NULL, 'published', '[\"afiliado\", \"exafiliado\", \"funcionario\"]', 1, '2025-11-27 20:35:23', '2025-11-28 02:25:25', NULL, NULL, NULL, 1),
(14, '¿Quieres vender tus productos en el extranjero?', 'Ven a la platica informativa \r\nLa Cámara de Comercio y SEDESU te lleva a Barcelona, Texas y Canada', 'publico', 'Conferencia', '2025-12-01 17:00:00', '2025-12-01 18:00:00', 'Salón Presidentes', 'Av. Luis Vega y Monroy #405, Quinta Balaustradas, Santiago de Querétaro, Qro.', 'https://www.google.com/maps/place/Canaco/@20.5763867,-100.3845014,17z/data=!3m1!4b1!4m6!3m5!1s0x85d344c51461cc3f:0x3a5c3e33a3170734!8m2!3d20.5763817!4d-100.3819265!16s%2Fg%2F1tdvdy8s?entry=ttu&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;g_ep=EgoyMDI1MTExNy4wIKXMDSoASAFQAw%3D%3D', 0, '', 100, 0, 0.00, 0.00, '0000-00-00 00:00:00', 0.00, 0.00, 1, 'vendeenelextranjero', 'event_1764276300_2cd797dafee2fecc.jpg', 'draft', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\", \"patrocinador_mesa\", \"colaborador_empresa\"]', 1, '2025-11-27 20:45:00', '2025-11-27 20:45:00', NULL, NULL, NULL, 1),
(15, '¿Quieres vender tus productos en el extranjero?', '¡Ven a la platica informativa! \r\nLa Cámara de Comercio y SEDESU te lleva a Barcelona, Texas, y Canada', 'terceros', 'Conferencia', '2025-12-01 17:00:00', '2025-12-01 18:00:00', 'Salón Presidentes', 'Av. Luis Vega y Monroy #405, Quinta Balaustradas, Santiago de Querétaro, Qro.', 'https://maps.app.goo.gl/xSgmxEmhgJf9Vzm28', 0, '', 100, 0, 0.00, 0.00, '0000-00-00 00:00:00', 0.00, 0.00, 1, 'venderenelextranjero', 'event_1764628995_fea3db75c770beca.jpg', 'published', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\", \"patrocinador_mesa\", \"colaborador_empresa\"]', 1, '2025-11-27 23:17:32', '2025-12-01 22:43:15', NULL, NULL, NULL, 1),
(16, 'Tutorial de evento gratuito', 'Prueba para registrarse a eventos gratuitos', 'terceros', '', '2025-12-02 11:07:00', '2025-12-02 00:07:00', 'Salón Presidentes', 'Av. Luis Vega y Monroy #405, Quinta Balaustradas, Santiago de Querétaro, Qro.', 'https://www.google.com/maps/place/Canaco/@20.5763867,-100.3845014,17z/data=!3m1!4b1!4m6!3m5!1s0x85d344c51461cc3f:0x3a5c3e33a3170734!8m2!3d20.5763817!4d-100.3819265!16s%2Fg%2F1tdvdy8s?entry=ttu&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;g_ep=EgoyMDI1MTExNy4wIKXMDSoASAFQAw%3D%3D', 0, '', 100, 0, 0.00, 0.00, '0000-00-00 00:00:00', 0.00, 0.00, 1, 'tutorialgratis', 'event_1764608916_2ce47a24d0e3d3a1.jpg', 'published', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\", \"patrocinador_mesa\", \"colaborador_empresa\"]', 10, '2025-12-01 17:08:36', '2025-12-01 17:08:36', NULL, NULL, NULL, 1),
(17, 'Tutorial de evento de Pago con acceso gratis para afiliados', '', 'publico', 'Networking', '2025-12-02 12:01:00', '2025-12-02 13:01:00', 'Salón Presidentes', 'Av. Luis Vega y Monroy #405, Quinta Balaustradas, Santiago de Querétaro, Qro.', 'https://www.google.com/maps/place/Canaco/@20.5763867,-100.3845014,17z/data=!3m1!4b1!4m6!3m5!1s0x85d344c51461cc3f:0x3a5c3e33a3170734!8m2!3d20.5763817!4d-100.3819265!16s%2Fg%2F1tdvdy8s?entry=ttu&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;g_ep=EgoyMDI1MTExNy4wIKXMDSoASAFQAw%3D%3D', 0, '', 100, 1, 2.00, 0.00, '0000-00-00 00:00:00', 1.00, 0.00, 1, 'pagocon1acceso', 'event_1764612143_074d00a5fd8ec761.jpg', 'published', '[\"afiliado\", \"prospecto\", \"exafiliado\", \"publico\", \"funcionario\", \"consejero\", \"patrocinador_mesa\", \"colaborador_empresa\"]', 10, '2025-12-01 18:02:23', '2025-12-01 18:02:23', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `event_categories`
--

CREATE TABLE `event_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `color` varchar(7) COLLATE utf8_unicode_ci DEFAULT '#3b82f6',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `event_categories`
--

INSERT INTO `event_categories` (`id`, `name`, `description`, `color`, `is_active`, `created_at`) VALUES
(1, 'Networking', 'Eventos de networking y contactos empresariales', '#3b82f6', 1, '2025-11-25 13:02:56'),
(2, 'Capacitación', 'Cursos y talleres de formación', '#3b82f6', 1, '2025-11-25 13:02:56'),
(3, 'Conferencia', 'Conferencias y charlas magistrales', '#3b82f6', 1, '2025-11-25 13:02:56'),
(4, 'Webinar', 'Seminarios y eventos en línea', '#3b82f6', 1, '2025-11-25 13:02:56'),
(5, 'Foro', 'Foros de discusión y debate', '#3b82f6', 1, '2025-11-25 13:02:56'),
(6, 'Exposición', 'Ferias y exposiciones comerciales', '#3b82f6', 1, '2025-11-25 13:02:56'),
(7, 'Asamblea', 'Asambleas y reuniones institucionales', '#3b82f6', 1, '2025-11-25 13:02:56'),
(8, 'Social', 'Eventos sociales y celebraciones', '#3b82f6', 1, '2025-11-25 13:02:56'),
(9, 'Comercial', 'Exposiciones y ferias comerciales', '#6366f1', 1, '2025-11-27 13:18:05'),
(10, 'Otro', 'Otros tipos de eventos', '#6b7280', 1, '2025-11-27 13:18:05'),
(23, 'Jóvenes Empresarios', 'Eventos de Jovem', '#84d20f', 1, '2025-11-27 16:17:59'),
(24, 'Mujeres Empresarias', 'Afiliadas dueñas de negocio', '#f73bf1', 1, '2025-12-01 20:17:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `registration_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Código único de registro',
  `is_guest` tinyint(1) DEFAULT '0' COMMENT 'Asiste como invitado',
  `guest_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Type of guest: INVITADO, FUNCIONARIO PÚBLICO, OTRO',
  `parent_registration_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Link to parent registration for additional attendees',
  `is_owner_representative` tinyint(1) DEFAULT '1' COMMENT 'Es dueño o representante legal',
  `attendee_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nombre del asistente al evento',
  `attendee_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'WhatsApp/Teléfono del asistente',
  `attendee_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Correo del asistente',
  `additional_attendees` json DEFAULT NULL COMMENT 'Información de asistentes adicionales',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT 'Monto total a pagar',
  `is_courtesy_ticket` tinyint(1) DEFAULT '0' COMMENT 'Boleto de cortesía otorgado',
  `event_id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED DEFAULT NULL,
  `guest_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `guest_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `guest_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `guest_rfc` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Razón social de la empresa/entidad',
  `nombre_empresario` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nombre del empresario o representante',
  `nombre_empresario_representante` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nombre del empresario o representante legal',
  `nombre_asistente` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nombre del asistente (obligatorio para boleto)',
  `email_asistente` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Email del asistente cuando no es el propietario',
  `whatsapp_asistente` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'WhatsApp del asistente cuando no es el propietario',
  `requiere_pago` tinyint(1) DEFAULT '0' COMMENT 'Indica si el asistente debe pagar (true cuando asistente != propietario)',
  `tickets` int(10) UNSIGNED DEFAULT '1' COMMENT 'Número de boletos comprados',
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(1) DEFAULT '0',
  `attendance_time` timestamp NULL DEFAULT NULL,
  `payment_status` enum('paid','pending','free') COLLATE utf8_unicode_ci DEFAULT 'free',
  `event_category` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Category for statistics: desayuno, open_day, conferencia, feria, exposicion',
  `qr_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nombre del archivo QR generado',
  `qr_sent` tinyint(1) DEFAULT '0' COMMENT 'Si el QR fue enviado por email',
  `qr_sent_at` timestamp NULL DEFAULT NULL COMMENT 'Fecha de envío del QR',
  `email_type_sent` enum('pending_payment','confirmation','access_ticket') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tipo de email enviado',
  `confirmation_sent` tinyint(1) DEFAULT '0' COMMENT 'Si el correo de confirmación fue enviado',
  `confirmation_sent_at` timestamp NULL DEFAULT NULL COMMENT 'Fecha de envío de confirmación',
  `notes` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `event_registrations`
--

INSERT INTO `event_registrations` (`id`, `registration_code`, `is_guest`, `guest_type`, `parent_registration_id`, `is_owner_representative`, `attendee_name`, `attendee_phone`, `attendee_email`, `additional_attendees`, `total_amount`, `is_courtesy_ticket`, `event_id`, `contact_id`, `guest_name`, `guest_email`, `guest_phone`, `guest_rfc`, `razon_social`, `nombre_empresario`, `nombre_empresario_representante`, `nombre_asistente`, `email_asistente`, `whatsapp_asistente`, `requiere_pago`, `tickets`, `registration_date`, `attended`, `attendance_time`, `payment_status`, `event_category`, `qr_code`, `qr_sent`, `qr_sent_at`, `email_type_sent`, `confirmation_sent`, `confirmation_sent_at`, `notes`) VALUES
(1, 'REG-00000001', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0.00, 0, 6, NULL, 'Alimentos del Bajío SA de CV', 'ventas@alibajio.mx', '4423334455', 'ALI0003030GHI', NULL, NULL, NULL, 'Alimentos del Bajío SA de CV', NULL, NULL, 0, 3, '2025-11-25 13:21:56', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(30, 'REG-20251126-66ACE3', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0.00, 0, 6, NULL, 'ING ANDRES RASO', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', 'EXPERIENCIA DE IMPACTOS DIGITALES', 'ING ANDRES RASO', NULL, 'ING ANDRES RASO', NULL, NULL, 0, 1, '2025-11-26 21:30:02', 0, NULL, 'free', NULL, 'qr_REG-20251126-66ACE3.png', 1, '2025-11-26 21:30:03', NULL, 1, '2025-11-26 21:30:02', NULL),
(35, 'REG-20251127-3ABAED', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0.00, 0, 7, NULL, 'Dan Jonathan Raso Rios', 'danjohn007@hotmail.com', '4425986318', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 08:31:39', 0, NULL, 'free', NULL, NULL, 1, '2025-11-27 08:31:39', NULL, 1, '2025-11-27 08:31:39', NULL),
(36, 'REG-20251127-0451FB', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0.00, 0, 7, NULL, 'Dan Jonathan Raso Rios', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 08:33:24', 0, NULL, 'free', NULL, NULL, 1, '2025-11-27 08:33:24', NULL, 1, '2025-11-27 08:33:24', NULL),
(37, 'REG-20251127-31083A', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0.00, 0, 7, NULL, 'Dan Jonathan Raso Rios', 'danjohn007@hotmail.com', '4425986318', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 08:40:14', 0, NULL, 'free', NULL, NULL, 1, '2025-11-27 08:40:14', NULL, 1, '2025-11-27 08:40:14', NULL),
(38, 'REG-20251127-E95E8E', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0.00, 0, 9, NULL, 'Dan Jonathan Raso Rios', 'danjohn007@hotmail.com', '2139761291', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 3, '2025-11-27 09:33:06', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 09:33:06', NULL),
(39, 'REG-20251127-27D693', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0.00, 0, 9, NULL, 'Dan Jonathan Raso Rios', 'danjohn007@hotmail.com', '2139761291', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 3, '2025-11-27 10:46:20', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 10:46:20', NULL),
(40, 'REG-20251127-FA3021', 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0.00, 0, 10, NULL, 'Dan Jonathan Raso Rios', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 3, '2025-11-27 13:36:21', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 13:36:21', NULL),
(41, 'REG-20251127-F72EAC', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 17:23:01', 0, NULL, 'free', NULL, NULL, 1, '2025-11-27 17:23:01', NULL, 1, '2025-11-27 17:23:01', NULL),
(42, 'REG-20251127-9A0172', 1, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Javier ID\", \"email\": \"javier@id.com\", \"phone\": \"2971927239\"}}', 2000.00, 0, 10, NULL, 'Luis Morales', 'luis@id.com', '7917907907', '', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-27 17:54:48', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 17:54:48', NULL),
(43, 'REG-20251127-EA9531', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Eva Balderas\", \"email\": \"evalderass13@hotmail.com\", \"phone\": \"4423810010\"}}', 2000.00, 0, 10, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-27 18:14:15', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 18:14:15', NULL),
(44, 'REG-20251127-077B31', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 1000.00, 0, 10, NULL, 'Jane Rosas', 'jane@impactosdigitales.com', '4421083970', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 18:19:04', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 18:19:04', NULL),
(45, 'REG-20251127-CD254C', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 1000.00, 0, 10, NULL, 'Alimentos del Bajío SA de CV', 'ventas@alibajio.mx', '4423334455', 'ALI0003030GHI', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 18:25:07', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 18:25:07', NULL),
(46, 'REG-20251127-626C5E', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 18:26:31', 0, NULL, 'free', NULL, NULL, 1, '2025-11-27 18:26:31', NULL, 1, '2025-11-27 18:26:31', NULL),
(47, 'REG-20251127-058CCB', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 18:35:58', 0, NULL, 'free', NULL, NULL, 1, '2025-11-27 18:35:59', NULL, 1, '2025-11-27 18:35:58', NULL),
(48, 'REG-20251127-AD2F78', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 18:40:55', 0, NULL, 'free', NULL, NULL, 1, '2025-11-27 18:40:55', NULL, 1, '2025-11-27 18:40:55', NULL),
(50, 'REG-20251127-A941F9', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Jehú Pacheco', '422169938@derecho.unam.mx', '4427198013', 'PAMJ021105V85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 19:38:56', 0, NULL, 'free', NULL, 'qr_REG-20251127-A941F9.png', 1, '2025-11-27 19:38:57', NULL, 1, '2025-11-27 19:38:56', NULL),
(54, 'REG-20251127-B7AA43', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Graciela Ríos\", \"email\": \"chelitario@hotmail.com\", \"phone\": \"1298689126\"}}', 0.00, 0, 12, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-27 20:00:52', 0, NULL, 'free', NULL, 'qr_REG-20251127-B7AA43.png', 1, '2025-11-27 20:00:53', NULL, 1, '2025-11-27 20:00:52', NULL),
(56, 'REG-20251127-17AEE2', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Luis Raso\", \"email\": \"hola@residencial.digital\", \"phone\": \"9861239816\"}}', 10.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-27 20:07:54', 0, NULL, 'paid', NULL, 'qr_REG-20251127-17AEE2.png', 1, '2025-11-27 20:11:57', NULL, 1, '2025-11-27 20:07:54', 'PayPal Order ID: 1M0148814M537821A'),
(57, 'REG-20251127-870646', 1, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Juanita\", \"email\": \"juanita@id.com\", \"phone\": \"7631681921\"}}', 0.00, 0, 12, NULL, 'Jaime Jimenez', 'contacto@residencial.digital', '2153873512', '', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-27 20:18:56', 0, NULL, 'free', NULL, 'qr_REG-20251127-870646.png', 1, '2025-11-27 20:18:57', NULL, 1, '2025-11-27 20:18:56', NULL),
(60, 'REG-20251127-08A83C', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 15.00, 0, 10, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:32:09', 0, NULL, 'paid', NULL, 'qr_REG-20251127-08A83C.png', 1, '2025-11-27 23:38:29', NULL, 1, '2025-11-27 23:32:09', 'PayPal Order ID: 00650768GM814240X'),
(61, 'REG-20251127-369D62', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 7, NULL, 'Jehú Pacheco', '422169938@derecho.unam.mx', '4427198013', 'PAMJ021105V85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:35:11', 0, NULL, 'free', NULL, 'qr_REG-20251127-369D62.png', 1, '2025-11-27 23:35:12', NULL, 1, '2025-11-27 23:35:11', NULL),
(62, 'REG-20251127-4D4916', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 7, NULL, 'Prueba CRM', 'robert.forever21@gmail.com', '4427198013', 'PAMJ021105V85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:37:10', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(63, 'REG-20251127-A52454', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 7, NULL, 'prueba', 'robert.forever21@gmail.com', '4421111111', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:37:43', 0, NULL, 'free', NULL, 'qr_REG-20251127-A52454.png', 1, '2025-11-27 23:37:44', NULL, 1, '2025-11-27 23:37:43', NULL),
(64, 'REG-20251127-7C9A43', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Saul Hernandez\", \"email\": \"robert.forever21@gmail.com\", \"phone\": \"4428888888\"}}', 5.00, 0, 10, NULL, 'Jehú Pacheco', '422169938@derecho.unam.mx', '4427198013', 'PAMJ021105V85', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-27 23:41:18', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 23:41:18', NULL),
(66, 'REG-20251127-752142', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:43:09', 1, '2025-11-27 23:48:16', 'free', NULL, 'qr_REG-20251127-752142.png', 1, '2025-11-27 23:43:10', NULL, 1, '2025-11-27 23:43:09', NULL),
(67, 'REG-20251127-D6E8F9', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Nadia Kenia', 'contacto@idindustrial.com.mx', '3628176328', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:46:10', 1, '2025-11-27 23:46:36', 'free', NULL, 'qr_REG-20251127-D6E8F9.png', 1, '2025-11-27 23:46:11', NULL, 1, '2025-11-27 23:46:10', NULL),
(68, 'REG-20251127-92CC26', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 15.00, 0, 10, NULL, 'Fulanito Perez', 'jehu121@outlook.es', '4421456789', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:49:56', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 23:49:56', NULL),
(69, 'REG-20251127-E78FA1', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 15.00, 0, 10, NULL, 'Fulanito Perez', 'robert.forever21@gmail.com', '4421456789', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:51:55', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 23:51:55', NULL),
(70, 'REG-20251127-60E5F1', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 20.00, 0, 13, NULL, 'pruebawebcanaco', 'soportecrm@camaradecomercioqro.mx', '4421234567', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-27 23:54:17', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-27 23:54:17', NULL),
(71, 'REG-20251127-6A44CF', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 00:29:45', 0, NULL, 'free', NULL, 'qr_REG-20251127-6A44CF.png', 1, '2025-11-28 00:29:46', NULL, 1, '2025-11-28 00:29:45', NULL),
(72, 'REG-20251127-5F3947', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 5.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 00:33:45', 0, NULL, 'paid', NULL, 'qr_REG-20251127-5F3947.png', 1, '2025-11-28 00:36:21', NULL, 1, '2025-11-28 00:33:45', 'PayPal Order ID: 1EE0365569866153D'),
(73, 'REG-20251127-B059CC', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 7, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 01:33:17', 0, NULL, 'free', NULL, 'qr_REG-20251127-B059CC.png', 1, '2025-11-28 01:33:18', NULL, 1, '2025-11-28 01:33:17', NULL),
(74, 'REG-20251127-B4C611', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 7, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 01:34:27', 0, NULL, 'free', NULL, 'qr_REG-20251127-B4C611.png', 1, '2025-11-28 01:34:27', NULL, 1, '2025-11-28 01:34:27', NULL),
(75, 'REG-20251127-6CB57C', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 7, NULL, 'Sanchez Lopez', 'hola@residencial.digital', '1328981392', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 01:36:48', 0, NULL, 'free', NULL, 'qr_REG-20251127-6CB57C.png', 1, '2025-11-28 01:36:49', NULL, 1, '2025-11-28 01:36:48', NULL),
(76, 'REG-20251127-63E7AD', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 01:57:31', 0, NULL, 'free', NULL, 'qr_REG-20251127-63E7AD.png', 1, '2025-11-28 01:57:32', NULL, 1, '2025-11-28 01:57:32', NULL),
(77, 'REG-20251127-5D74E8', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Fernando Alberto Sandoval González', 'myf.sandoval.gonzalez@gmail.com', '4423328696', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 02:01:07', 0, NULL, 'free', NULL, 'qr_REG-20251127-5D74E8.png', 1, '2025-11-28 02:01:08', NULL, 1, '2025-11-28 02:01:07', NULL),
(78, 'REG-20251127-56CEFC', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Wendoly Licona', 'wen@impactosdigitales.com', '2838998921', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 02:01:30', 0, NULL, 'free', NULL, 'qr_REG-20251127-56CEFC.png', 1, '2025-11-28 02:01:31', NULL, 1, '2025-11-28 02:01:30', NULL),
(79, 'REG-20251127-C38484', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 02:22:06', 0, NULL, 'free', NULL, 'qr_REG-20251127-C38484.png', 1, '2025-11-28 02:22:07', NULL, 1, '2025-11-28 02:22:06', NULL),
(80, 'REG-20251127-E935EA', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 02:24:12', 0, NULL, 'free', NULL, 'qr_REG-20251127-E935EA.png', 1, '2025-11-28 02:24:13', NULL, 1, '2025-11-28 02:24:12', NULL),
(81, 'REG-20251127-820C9C', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Andrés Raso Ríos', 'andyraso@yahoo.com', '4422153397', 'RARA771202PB6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 02:25:26', 0, NULL, 'free', NULL, 'qr_REG-20251127-820C9C.png', 1, '2025-11-28 02:25:27', NULL, 1, '2025-11-28 02:25:26', NULL),
(82, 'REG-20251127-7EAD98', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Graciela M\", \"email\": \"software@impactosdigitales.com\", \"phone\": \"8357889311\"}}', 10.00, 0, 13, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-28 02:26:35', 0, NULL, 'paid', NULL, 'qr_REG-20251127-7EAD98.png', 1, '2025-11-28 02:28:54', NULL, 1, '2025-11-28 02:26:35', 'PayPal Order ID: 9D65406162949680L'),
(83, 'REG-20251127-263003', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'DAYANE JANET TORANZO GLZ', 'dayanetoranzo@gmail.com', '4421490600', 'TOGD8407016Z7', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 02:48:10', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(84, 'REG-20251127-27A0D5', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"José Antonio Ugalde Guerrero \", \"email\": \"a.ugalde@udelondresqueretaro.com.mx\", \"phone\": \"4422472181\"}}', 0.00, 0, 12, NULL, 'Luis Núñez Salinas Londres Universidad', 'l.nunez@udelondresqueretaro.com.mx', '4421300205', '', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-28 02:48:12', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(85, 'REG-20251127-124E68', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Sara Meza Maldonado', 'saramezam@gmail.com', '4423220646', 'Fsu130806aj0', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 02:50:39', 0, NULL, 'free', NULL, 'qr_REG-20251127-124E68.png', 1, '2025-11-28 02:50:40', NULL, 1, '2025-11-28 02:50:39', NULL),
(86, 'REG-20251127-BF3E2A', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Patricia Benson', 'patbenson1133@gmail.com', '4423913409', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 02:50:48', 0, NULL, 'free', NULL, 'qr_REG-20251127-BF3E2A.png', 1, '2025-11-28 02:50:49', NULL, 1, '2025-11-28 02:50:48', NULL),
(87, 'REG-20251127-24F2D9', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Alberto López Ariza \", \"email\": \"tentacionesflores@yahoo.com.mx\", \"phone\": \"4424130355\"}}', 0.00, 0, 12, NULL, 'Hill &amp; Co', 'taguilar@hillco.com.mx', '4421859754', 'Rhi120730ss8', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-28 02:50:52', 0, NULL, 'free', NULL, 'qr_REG-20251127-24F2D9.png', 1, '2025-11-28 02:50:53', NULL, 1, '2025-11-28 02:50:52', NULL),
(89, 'REG-20251127-633102', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Setria, S.A. de C.V.', 'jesus_sinecio@setria.com.mx', '4423519220', 'SET030619LU9', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 03:26:27', 0, NULL, 'free', NULL, 'qr_REG-20251127-633102.png', 1, '2025-11-28 03:26:28', NULL, 1, '2025-11-28 03:26:27', NULL),
(90, 'REG-20251127-747311', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 03:47:29', 0, NULL, 'free', NULL, 'qr_REG-20251127-747311.png', 1, '2025-11-28 03:47:30', NULL, 1, '2025-11-28 03:47:29', NULL),
(91, 'REG-20251127-DA1484', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 03:50:05', 0, NULL, 'free', NULL, 'qr_REG-20251127-DA1484.png', 1, '2025-11-28 03:50:06', NULL, 1, '2025-11-28 03:50:05', NULL),
(92, 'REG-20251127-BC9CA5', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 03:50:57', 0, NULL, 'free', NULL, 'qr_REG-20251127-BC9CA5.png', 1, '2025-11-28 03:50:57', NULL, 1, '2025-11-28 03:50:57', NULL),
(93, 'REG-20251127-ABDBA0', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Lima Servicios Inmobiliarios.', 'lilianherrera30@yahoo.com.mx', '4423228981', 'HEHL7109018Y3', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 03:51:06', 0, NULL, 'free', NULL, 'qr_REG-20251127-ABDBA0.png', 1, '2025-11-28 03:51:07', NULL, 1, '2025-11-28 03:51:06', NULL),
(94, 'REG-20251128-80355A', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Osias', 'hola@residencial.digital', '2222444444', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 11:07:52', 0, NULL, 'free', NULL, 'qr_REG-20251128-80355A.png', 1, '2025-11-28 11:07:53', NULL, 1, '2025-11-28 11:07:52', NULL),
(95, 'REG-20251128-A4CE6C', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 11:28:44', 0, NULL, 'free', NULL, 'qr_REG-20251128-A4CE6C.png', 1, '2025-11-28 11:28:45', NULL, 1, '2025-11-28 11:28:44', NULL),
(96, 'REG-20251128-DA55E9', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 15.00, 0, 10, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 12:38:21', 0, NULL, 'paid', NULL, 'qr_REG-20251128-DA55E9.png', 1, '2025-11-28 12:41:49', NULL, 1, '2025-11-28 12:38:21', 'PayPal Order ID: 4A878582K77693354'),
(97, 'REG-20251128-8804AE', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 5.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 12:42:10', 0, NULL, 'paid', NULL, 'qr_REG-20251128-8804AE.png', 1, '2025-11-28 12:45:29', NULL, 1, '2025-11-28 12:42:10', 'PayPal Order ID: 43L061376S101342B'),
(98, 'REG-20251128-BAF13D', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 15.00, 0, 10, NULL, 'Jonathan Rios', 'tecnologia@idindustrial.com.mx', '8763186838', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 12:50:26', 0, NULL, 'paid', NULL, 'qr_REG-20251128-BAF13D.png', 1, '2025-11-28 13:08:57', NULL, 1, '2025-11-28 12:50:26', 'PayPal Order ID: 08L06322EE884002K'),
(99, 'REG-20251128-00BB23', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 13:28:53', 0, NULL, 'free', NULL, 'qr_REG-20251128-00BB23.png', 1, '2025-11-28 13:28:54', NULL, 1, '2025-11-28 13:28:53', NULL),
(100, 'REG-20251128-716161', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"José Antonio Ugalde Guerrero\", \"email\": \"a.ugalde@udelondresqueretaro.com.mx\", \"phone\": \"4421096690\"}}', 0.00, 0, 12, NULL, 'Luis Núñez Salinas / CENTRO DE ESTUDIOS LONDRES QUERETARO / Sociedad Civil', 'l.nunez@udelondresqueretaro.com.mx', '4421300205', '', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-28 13:53:40', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(101, 'REG-20251128-1BDC11', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"José Antonio Ugalde Guerrero\", \"email\": \"a.ugalde@udelondresqueretaro.com.mx\", \"phone\": \"4421096690\"}}', 0.00, 0, 12, NULL, 'Luis Núñez Salinas / CENTRO DE ESTUDIOS LONDRES QUERETARO / Sociedad Civil', 'l.nunez@udelondresqueretaro.com.mx', '4421300205', 'CENTRODEESTUD', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-28 13:58:31', 0, NULL, 'free', NULL, 'qr_REG-20251128-1BDC11.png', 1, '2025-11-28 13:58:32', NULL, 1, '2025-11-28 13:58:31', NULL),
(102, 'REG-20251128-83D085', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 8.00, 0, 10, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 17:47:12', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-28 17:47:13', NULL),
(103, 'REG-20251128-7A127D', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 8.00, 0, 10, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 17:48:33', 0, NULL, 'paid', NULL, 'qr_REG-20251128-7A127D.png', 1, '2025-11-28 18:04:52', NULL, 1, '2025-11-28 17:48:33', 'PayPal Order ID: 195211537M018133K'),
(104, 'REG-20251128-970F85', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 5.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 17:53:05', 0, NULL, 'paid', NULL, 'qr_REG-20251128-970F85.png', 1, '2025-11-28 17:55:10', NULL, 1, '2025-11-28 17:53:05', 'PayPal Order ID: 56W45231LE8506908'),
(105, 'REG-20251128-812E0A', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 10, NULL, 'Jehú Pacheco', '422169938@derecho.unam.mx', '4427198013', 'PAMJ021105V85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 17:53:24', 0, NULL, 'free', NULL, 'qr_REG-20251128-812E0A.png', 1, '2025-11-28 17:53:25', NULL, 1, '2025-11-28 17:53:24', NULL),
(106, 'REG-20251128-3CCF0F', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 5.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 17:55:15', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-28 17:55:15', NULL),
(107, 'REG-20251128-5B4613', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 18:03:00', 0, NULL, 'free', NULL, 'qr_REG-20251128-5B4613.png', 1, '2025-11-28 18:03:01', NULL, 1, '2025-11-28 18:03:00', NULL),
(108, 'REG-20251128-8F0236', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 8.00, 0, 10, NULL, 'Eleazar Moreno', 'robert.forever21@gmail.com', '4428071499', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 18:03:52', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-28 18:03:52', NULL),
(109, 'REG-20251128-F8344D', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'Angélica González', 'agonzalez@grupoconcepto.com', '4421524998', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:15:07', 0, NULL, 'free', NULL, 'qr_REG-20251128-F8344D.png', 1, '2025-11-28 19:15:08', NULL, 1, '2025-11-28 19:15:07', NULL),
(110, 'REG-20251128-24973B', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'GRUPO CONCEPTO', 'agonzalez@grupoconcepto.com', '4421524998', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:17:45', 0, NULL, 'free', NULL, 'qr_REG-20251128-24973B.png', 1, '2025-11-28 19:17:46', NULL, 1, '2025-11-28 19:17:45', NULL),
(111, 'REG-20251128-611F95', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'Gisela', 'janeth_sainz@hotmail.com', '5545231240', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:26:53', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(112, 'REG-20251128-B445A0', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'Empresa Cultural Sustentable la Noria', 'lanoriatallerdearte@gmail.com', '4424469377', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:28:04', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(113, 'REG-20251128-45D2B3', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Empresa cultural sustentable la Noria', 'lanoriatallerdearte@gmail.com', '4424469377', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:29:18', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(114, 'REG-20251128-D9837E', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Maria de los Angeles Garduño Mazy', 'maria.garduno@gorditasqueretanas.com', '4424798869', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:31:01', 0, NULL, 'free', NULL, 'qr_REG-20251128-D9837E.png', 1, '2025-11-28 19:31:02', NULL, 1, '2025-11-28 19:31:01', NULL),
(115, 'REG-20251128-E07DAA', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'Andrés Raso Ríos', 'andyraso@yahoo.com', '4422153397', 'RARA771202PB6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:31:05', 0, NULL, 'free', NULL, 'qr_REG-20251128-E07DAA.png', 1, '2025-11-28 19:31:06', NULL, 1, '2025-11-28 19:31:05', NULL),
(116, 'REG-20251128-AF3DA7', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'Gisela sainz', 'janeth_sainz@hotmail.com', '5545231240', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:34:36', 0, NULL, 'free', NULL, 'qr_REG-20251128-AF3DA7.png', 1, '2025-11-28 19:34:37', NULL, 1, '2025-11-28 19:34:36', NULL),
(117, 'REG-20251128-C5D0E4', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Hector Villanueva Kuri\", \"email\": \"hector@onlevi.mx\", \"phone\": \"4423432128\"}}', 0.00, 0, 12, NULL, 'Operadora de Negocios LEVI', 'diana@onlevi.mx', '4421288283', 'ONL210325D15', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-28 19:49:06', 0, NULL, 'free', NULL, 'qr_REG-20251128-C5D0E4.png', 1, '2025-11-28 19:49:07', NULL, 1, '2025-11-28 19:49:06', NULL),
(118, 'REG-20251128-498C52', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 8.00, 0, 10, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:49:55', 0, NULL, 'paid', NULL, 'qr_REG-20251128-498C52.png', 1, '2025-11-28 19:52:03', NULL, 1, '2025-11-28 19:49:55', 'PayPal Order ID: 8G312575K0761045C'),
(119, 'REG-20251128-FC4F29', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Fabián Hütt Tigrillo Holding', 'tigrillosproduccion@gmail.com', '4121014446', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:50:12', 0, NULL, 'free', NULL, 'qr_REG-20251128-FC4F29.png', 1, '2025-11-28 19:50:13', NULL, 1, '2025-11-28 19:50:12', NULL),
(120, 'REG-20251128-4AA8D3', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Roberto Hernández castellanos', 'bbeto.look@gmail.com', '4424612256', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:54:39', 0, NULL, 'free', NULL, 'qr_REG-20251128-4AA8D3.png', 1, '2025-11-28 19:54:40', NULL, 1, '2025-11-28 19:54:39', NULL),
(121, 'REG-20251128-27C994', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Valita\", \"email\": \"anacristina@impactosdigitales.com\", \"phone\": \"1288988998\"}, \"2\": {\"name\": \"Ana \", \"email\": \"asistente@impactosdigitales.com\", \"phone\": \"9873937983\"}}', 15.00, 0, 10, NULL, 'EXPERIENCIA DE IMPACTOS DIGITALES', 'administracion@impactosdigitales.com', '4422198567', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 3, '2025-11-28 19:59:15', 0, NULL, 'paid', NULL, 'qr_REG-20251128-27C994.png', 1, '2025-11-28 20:01:38', NULL, 1, '2025-11-28 19:59:15', 'PayPal Order ID: 4Y54885073317702W'),
(122, 'REG-20251128-56650F', 0, NULL, 121, 0, NULL, NULL, NULL, NULL, 0.00, 0, 10, NULL, 'Valita', 'anacristina@impactosdigitales.com', '1288988998', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:59:15', 0, NULL, 'paid', NULL, 'qr_REG-20251128-56650F.png', 1, '2025-11-28 20:01:39', NULL, 0, NULL, 'PayPal Order ID: 4Y54885073317702W'),
(123, 'REG-20251128-0731DA', 0, NULL, 121, 0, NULL, NULL, NULL, NULL, 0.00, 0, 10, NULL, 'Ana', 'asistente@impactosdigitales.com', '9873937983', 'EID150522K85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:59:15', 0, NULL, 'paid', NULL, 'qr_REG-20251128-0731DA.png', 1, '2025-11-28 20:01:39', NULL, 0, NULL, 'PayPal Order ID: 4Y54885073317702W'),
(124, 'REG-20251128-9E750B', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'Eduardo Antonio Montes Granados', 'e.montes@mavemp.com', '4461339091', 'EAS221207CU4', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 19:59:40', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-28 19:59:40', NULL),
(125, 'REG-20251128-B46039', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'Rosario De la paz Abarca', 'r.delapaz@sferasolutions.com.mx', '4461339083', 'EAS221207CU4', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 20:00:42', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-28 20:00:42', NULL),
(126, 'REG-20251128-05B90A', 1, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Roberto Hernández castellanos', 'bbeto.look@gmail.com', '4424612256', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 20:02:03', 0, NULL, 'free', NULL, 'qr_REG-20251128-05B90A.png', 1, '2025-11-28 20:02:04', NULL, 1, '2025-11-28 20:02:03', NULL),
(127, 'REG-20251128-DC1255', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Judith Guerrero', 'lanoriatallerdearte@gmail.com', '4424469377', 'ECS180508HE2', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 20:42:27', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(129, 'REG-20251128-649C17', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Juan Velasco', 'gerente.comercial@tiendasaem.com', '4427213245', 'Vebej721031gg', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 20:59:48', 0, NULL, 'free', NULL, 'qr_REG-20251128-649C17.png', 1, '2025-11-28 20:59:49', NULL, 1, '2025-11-28 20:59:48', NULL),
(130, 'REG-20251128-A39A1F', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Jorge Arturo Carnaya Leissa', 'jcarnaya@me.com', '4421143440', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 21:01:50', 0, NULL, 'free', NULL, 'qr_REG-20251128-A39A1F.png', 1, '2025-11-28 21:01:51', NULL, 1, '2025-11-28 21:01:50', NULL),
(131, 'REG-20251128-018D6D', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Aranday Y Asociados', 'cesararanday@gmail.com', '4423599327', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 21:03:51', 0, NULL, 'free', NULL, 'qr_REG-20251128-018D6D.png', 1, '2025-11-28 21:03:52', NULL, 1, '2025-11-28 21:03:51', NULL),
(132, 'REG-20251128-06D499', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Jorge Arturo Carnaya Leissa', 'jcarnaya@me.com', '4421143440', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 21:12:51', 0, NULL, 'free', NULL, 'qr_REG-20251128-06D499.png', 1, '2025-11-28 21:12:52', NULL, 1, '2025-11-28 21:12:51', NULL),
(133, 'REG-20251128-B5B547', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Don Bocol ( Alimentos Precongelados de la Huasteca', 'donbocolmx@gmail.com', '8341260673', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 21:13:27', 0, NULL, 'free', NULL, 'qr_REG-20251128-B5B547.png', 1, '2025-11-28 21:13:28', NULL, 1, '2025-11-28 21:13:27', NULL),
(134, 'REG-20251128-763126', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Marcozer SA de CV', 'aaronruvalcaba@marcozer.com.mx', '4423591266', 'MAR960105E93', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 21:18:24', 0, NULL, 'free', NULL, 'qr_REG-20251128-763126.png', 1, '2025-11-28 21:18:25', NULL, 1, '2025-11-28 21:18:24', NULL),
(135, 'REG-20251128-AB05DC', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Jane Rosas\", \"email\": \"jane@impactosdigitales.com\", \"phone\": \"8757857854\"}, \"2\": {\"name\": \"Alejandro Balderas\", \"email\": \"alejandro@impactosdigitales.com\", \"phone\": \"5787575921\"}}', 0.00, 0, 7, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 3, '2025-11-28 21:29:28', 0, NULL, 'free', NULL, 'qr_REG-20251128-AB05DC.png', 1, '2025-11-28 21:29:29', NULL, 1, '2025-11-28 21:29:28', NULL),
(136, 'REG-20251128-EE6476', 0, NULL, 135, 0, NULL, NULL, NULL, NULL, 0.00, 0, 7, NULL, 'Jane Rosas', 'jane@impactosdigitales.com', '8757857854', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 21:29:28', 0, NULL, 'free', NULL, 'qr_REG-20251128-EE6476.png', 1, '2025-11-28 21:29:30', NULL, 0, NULL, NULL),
(137, 'REG-20251128-DC596C', 0, NULL, 135, 0, NULL, NULL, NULL, NULL, 0.00, 0, 7, NULL, 'Alejandro Balderas', 'alejandro@impactosdigitales.com', '5787575921', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 21:29:28', 0, NULL, 'free', NULL, 'qr_REG-20251128-DC596C.png', 1, '2025-11-28 21:29:31', NULL, 0, NULL, NULL),
(138, 'REG-20251128-12E19F', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 7, NULL, 'Andrés Raso Ríos', 'andyraso@yahoo.com', '4422153397', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 21:41:47', 0, NULL, 'free', NULL, 'qr_REG-20251128-12E19F.png', 1, '2025-11-28 21:41:48', NULL, 1, '2025-11-28 21:41:47', NULL),
(140, 'REG-20251128-131A76', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Unión empresas seguridad privada', 'servicorp10@hotmsil.com', '4422672486', 'AUBM430611UjJ', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 22:01:53', 0, NULL, 'free', NULL, 'qr_REG-20251128-131A76.png', 1, '2025-11-28 22:01:54', NULL, 1, '2025-11-28 22:01:53', NULL),
(142, 'REG-20251128-8AE9A3', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'DAYANE JANET TORANZO GLZ', 'dayanetoranzo@gmail.com', '4421490600', 'TOGD8407016Z7', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 22:18:31', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(143, 'REG-20251128-5AE699', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'ELIZABETH RAMIREZ VILLANUEVA', 'elizarave21@gmail.com', '4461036790', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 22:19:22', 0, NULL, 'free', NULL, 'qr_REG-20251128-5AE699.png', 1, '2025-11-28 22:19:23', NULL, 1, '2025-11-28 22:19:22', NULL),
(144, 'REG-20251128-6DD87D', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Jorge Arturo Carnaya Leissa', 'jcarnaya@me.com', '4421143440', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 23:06:21', 0, NULL, 'free', NULL, 'qr_REG-20251128-6DD87D.png', 1, '2025-11-28 23:06:22', NULL, 1, '2025-11-28 23:06:21', NULL),
(145, 'REG-20251128-0005F5', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Judith Guerrero', 'lanoriatallerdearte@gmail.com', '4424469377', 'ECS180508HE2', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-28 23:57:30', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(146, 'REG-20251128-1D4B4A', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Erika Alavez Farfan\", \"email\": \"elcantodelasranas@gmail.com\", \"phone\": \"4423531491\"}, \"2\": {\"name\": \"Gerardo Enrique Alavez Hernández \", \"email\": \"vacoinalafarqro@gmail.com\", \"phone\": \"4421072645\"}}', 0.00, 0, 12, NULL, 'Ma Dolores Farfán Pons', 'lolisfarfan@gmail.com', '4422501366', 'AAFE940615Q71', NULL, NULL, NULL, '', NULL, NULL, 0, 3, '2025-11-29 00:39:50', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(147, 'REG-20251128-929A6B', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Erika alavez farfan\", \"email\": \"elcantodelasranas@gmail.com\", \"phone\": \"4423531491\"}, \"2\": {\"name\": \"Gerardo Enrique Alavez Hernandez\", \"email\": \"santocantokaraoje@gmail.com\", \"phone\": \"4421072645\"}}', 0.00, 0, 12, NULL, 'MA DOLORES FARFAN PONS', 'vacoinalafarqro@gmail.com', '4422501366', 'VAQ191015R42', NULL, NULL, NULL, '', NULL, NULL, 0, 3, '2025-11-29 00:48:07', 0, NULL, 'free', NULL, 'qr_REG-20251128-929A6B.png', 1, '2025-11-29 00:48:08', NULL, 1, '2025-11-29 00:48:07', NULL),
(148, 'REG-20251128-F39D66', 0, NULL, 147, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Erika alavez farfan', 'elcantodelasranas@gmail.com', '4423531491', 'VAQ191015R42', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 00:48:07', 0, NULL, 'free', NULL, 'qr_REG-20251128-F39D66.png', 1, '2025-11-29 00:48:09', NULL, 0, NULL, NULL),
(149, 'REG-20251128-C6643A', 0, NULL, 147, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Gerardo Enrique Alavez Hernandez', 'santocantokaraoje@gmail.com', '4421072645', 'VAQ191015R42', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 00:48:07', 0, NULL, 'free', NULL, 'qr_REG-20251128-C6643A.png', 1, '2025-11-29 00:48:10', NULL, 0, NULL, NULL),
(150, 'REG-20251128-56101B', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Erika alavez farfan\", \"email\": \"elcantodelasranas@gmail.com\", \"phone\": \"4423531491\"}, \"2\": {\"name\": \"Gerardo Enrique Alavez Hernandez\", \"email\": \"santocantokaraoje@gmail.com\", \"phone\": \"4421072645\"}}', 0.00, 0, 12, NULL, 'MA DOLORES FARFAN PONS', 'vacoinalafarqro@gmail.com', '4422501366', 'VAQ191015R42', NULL, NULL, NULL, '', NULL, NULL, 0, 3, '2025-11-29 00:48:10', 0, NULL, 'free', NULL, 'qr_REG-20251128-56101B.png', 1, '2025-11-29 00:48:11', NULL, 1, '2025-11-29 00:48:10', NULL),
(151, 'REG-20251128-CE9EB2', 0, NULL, 150, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Erika alavez farfan', 'elcantodelasranas@gmail.com', '4423531491', 'VAQ191015R42', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 00:48:10', 0, NULL, 'free', NULL, 'qr_REG-20251128-CE9EB2.png', 1, '2025-11-29 00:48:12', NULL, 0, NULL, NULL),
(152, 'REG-20251128-33090B', 0, NULL, 150, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Gerardo Enrique Alavez Hernandez', 'santocantokaraoje@gmail.com', '4421072645', 'VAQ191015R42', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 00:48:10', 0, NULL, 'free', NULL, 'qr_REG-20251128-33090B.png', 1, '2025-11-29 00:48:12', NULL, 0, NULL, NULL),
(153, 'REG-20251128-A06F08', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'VIDA ELITE AGENTES DE SEGUROS', 'aaguilarcruz@gmail.com', '4425020736', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 00:54:29', 0, NULL, 'free', NULL, 'qr_REG-20251128-A06F08.png', 1, '2025-11-29 00:54:30', NULL, 1, '2025-11-29 00:54:29', NULL),
(154, 'REG-20251128-D17060', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Kombucha Martha Trascend', 'marthacabezadevaca@gmail.com', '4422635465', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 00:59:32', 0, NULL, 'free', NULL, 'qr_REG-20251128-D17060.png', 1, '2025-11-29 00:59:33', NULL, 1, '2025-11-29 00:59:32', NULL),
(155, 'REG-20251128-084B1F', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'HOLDA JULIETA VEGA PAEZ', 'hjvegap@gmail.com', '5516670311', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:00:25', 0, NULL, 'free', NULL, 'qr_REG-20251128-084B1F.png', 1, '2025-11-29 01:00:26', NULL, 1, '2025-11-29 01:00:25', NULL),
(156, 'REG-20251128-9CEAA7', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'HOLDA JULIETA VEGA PAEZ', 'hjvegap@gmail.com', '5516670311', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:00:26', 0, NULL, 'free', NULL, 'qr_REG-20251128-9CEAA7.png', 1, '2025-11-29 01:00:27', NULL, 1, '2025-11-29 01:00:26', NULL),
(157, 'REG-20251128-EB8DA4', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Kombucha Martha Trascend', 'marthacabezadevaca@gmail.com', '4422635465', 'CABM701031N22', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:00:44', 0, NULL, 'free', NULL, 'qr_REG-20251128-EB8DA4.png', 1, '2025-11-29 01:00:45', NULL, 1, '2025-11-29 01:00:44', NULL),
(158, 'REG-20251128-4F695C', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Ximena flores', 'ximenaftorres199@gmail.com', '4425107060', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:01:35', 0, NULL, 'free', NULL, 'qr_REG-20251128-4F695C.png', 1, '2025-11-29 01:01:36', NULL, 1, '2025-11-29 01:01:35', NULL),
(159, 'REG-20251128-03FC60', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Ximena flores', 'ximenaftorres199@gmail.com', '4425107060', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:01:36', 0, NULL, 'free', NULL, 'qr_REG-20251128-03FC60.png', 1, '2025-11-29 01:01:37', NULL, 1, '2025-11-29 01:01:36', NULL),
(160, 'REG-20251128-C12578', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'SOS Al Rescate de tus ventas', 'alrescate.detusventas@comovendermas.com.mx', '4424050685', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:05:21', 0, NULL, 'free', NULL, 'qr_REG-20251128-C12578.png', 1, '2025-11-29 01:05:22', NULL, 1, '2025-11-29 01:05:21', NULL),
(161, 'REG-20251128-32DBA4', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Karla Marcela Uribe Delgado', 'karlamarcelauribe@gmail.com', '4427150449', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:15:12', 0, NULL, 'free', NULL, 'qr_REG-20251128-32DBA4.png', 1, '2025-11-29 01:15:13', NULL, 1, '2025-11-29 01:15:12', NULL),
(162, 'REG-20251128-D74EE7', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Maestro Ugalde \", \"email\": \"servicorp@gmail.com\", \"phone\": \"4422472181\"}}', 0.00, 0, 12, NULL, 'Mario Aguilar', 'servicorp@gmail.com', '4422672486', 'AUBM430611UJA', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-29 01:21:26', 0, NULL, 'free', NULL, 'qr_REG-20251128-D74EE7.png', 1, '2025-11-29 01:21:27', NULL, 1, '2025-11-29 01:21:26', NULL),
(163, 'REG-20251128-5C03A8', 0, NULL, 162, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Maestro Ugalde', 'servicorp@gmail.com', '4422472181', 'AUBM430611UJA', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:21:26', 0, NULL, 'free', NULL, 'qr_REG-20251128-5C03A8.png', 1, '2025-11-29 01:21:28', NULL, 0, NULL, NULL),
(164, 'REG-20251128-89B545', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'SILVIA MERCEDES TRUJILLO SERVIN', 'viptravelqro@gmail.com', '4424499624', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:22:07', 0, NULL, 'free', NULL, 'qr_REG-20251128-89B545.png', 1, '2025-11-29 01:22:08', NULL, 1, '2025-11-29 01:22:07', NULL),
(165, 'REG-20251128-9C167B', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Silvia Trujillo \", \"email\": \"viptravelqro@gmail.com\", \"phone\": \"4424499624\"}}', 0.00, 0, 12, NULL, 'Arturo Ruiz', 'arturoruizg@gmail.com', '4424884831', 'RUGJ5001279S0', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-29 01:27:32', 0, NULL, 'free', NULL, 'qr_REG-20251128-9C167B.png', 1, '2025-11-29 01:27:33', NULL, 1, '2025-11-29 01:27:32', NULL),
(166, 'REG-20251128-C2259F', 0, NULL, 165, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Silvia Trujillo', 'viptravelqro@gmail.com', '4424499624', 'RUGJ5001279S0', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:27:32', 0, NULL, 'free', NULL, 'qr_REG-20251128-C2259F.png', 1, '2025-11-29 01:27:34', NULL, 0, NULL, NULL),
(167, 'REG-20251128-F152EE', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'SILVIA MERCEDES TRUJILLO SERVIN', 'strujilloderuiz@gmail.com', '4424499624', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 01:32:50', 0, NULL, 'free', NULL, 'qr_REG-20251128-F152EE.png', 1, '2025-11-29 01:32:51', NULL, 1, '2025-11-29 01:32:50', NULL),
(168, 'REG-20251128-2B2E32', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'CDN Mujeres de Empresa', 'smartinez_0405@gmail.com', '4421220764', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 02:06:49', 0, NULL, 'free', NULL, 'qr_REG-20251128-2B2E32.png', 1, '2025-11-29 02:06:50', NULL, 1, '2025-11-29 02:06:49', NULL),
(169, 'REG-20251128-F90253', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Sergio Buenrostro Rodriguez/ international lean six sig a', 'Queretaro@ilssg.org', '4423179572', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 02:12:56', 0, NULL, 'free', NULL, 'qr_REG-20251128-F90253.png', 1, '2025-11-29 02:12:57', NULL, 1, '2025-11-29 02:12:56', NULL),
(170, 'REG-20251128-6E9D5E', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Claudia Eloisa Fuentes García', 'claudiafuentesgarcia@gmail.com', '5534448653', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 02:22:35', 0, NULL, 'free', NULL, 'qr_REG-20251128-6E9D5E.png', 1, '2025-11-29 02:22:36', NULL, 1, '2025-11-29 02:22:35', NULL),
(171, 'REG-20251128-6FA0BF', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Lucía Mejia', 'apps@impactosdigitales.com', '3332341242', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 02:49:23', 0, NULL, 'free', NULL, 'qr_REG-20251128-6FA0BF.png', 1, '2025-11-29 02:49:24', NULL, 1, '2025-11-29 02:49:23', NULL),
(173, 'REG-20251128-20E66F', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Lupita Rosas\", \"email\": \"emarketing@impactosdigitales.com\", \"phone\": \"8723575817\"}}', 0.00, 0, 12, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-29 03:40:00', 0, NULL, 'free', NULL, 'qr_REG-20251128-20E66F.png', 1, '2025-11-29 03:40:01', NULL, 1, '2025-11-29 03:40:00', NULL),
(174, 'REG-20251128-71F55C', 0, NULL, 173, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Lupita Rosas', 'emarketing@impactosdigitales.com', '8723575817', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 03:40:00', 1, '2025-11-29 06:14:33', 'free', NULL, 'qr_REG-20251128-71F55C.png', 1, '2025-11-29 03:40:02', NULL, 0, NULL, NULL),
(175, 'REG-20251128-756D54', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Especialista en gimnasios', 'jaquilinda1002@gmail.com', '4425825980', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 05:00:09', 0, NULL, 'free', NULL, 'qr_REG-20251128-756D54.png', 1, '2025-11-29 05:00:10', NULL, 1, '2025-11-29 05:00:09', NULL),
(176, 'REG-20251129-ED39C7', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'ID', 'danjohn007@hotmail.com', '4424865389', 'RARD7909214H6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 06:03:11', 1, '2025-11-29 06:11:55', 'free', NULL, 'qr_REG-20251129-ED39C7.png', 1, '2025-11-29 06:03:12', NULL, 1, '2025-11-29 06:03:11', NULL),
(177, 'REG-20251129-C01FD2', 1, 'OTRO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Universidad Atenas', 'direccion@atenas.edu.mx', '4422302231', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 14:39:17', 0, NULL, 'free', NULL, 'qr_REG-20251129-C01FD2.png', 1, '2025-11-29 14:39:18', NULL, 1, '2025-11-29 14:39:17', NULL),
(178, 'REG-20251129-FF2E32', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Judith Guerrero', 'lanoriatallerdearte@gmail.com', '4424469377', 'ECS180508HE2', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 16:25:22', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(179, 'REG-20251129-8B76F1', 1, 'OTRO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Francisco Vargas', 'direccioncomercial@tiendasaem.com', '4423597535', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 17:43:12', 0, NULL, 'free', NULL, 'qr_REG-20251129-8B76F1.png', 1, '2025-11-29 17:43:13', NULL, 1, '2025-11-29 17:43:12', NULL),
(180, 'REG-20251129-588535', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"ADRIANA RUIZ GARCIA\", \"email\": \"adriana.ruiz@vw-camionesbajio.com\", \"phone\": \"4427835060\"}}', 0.00, 0, 12, NULL, 'Distribuidora Volkswagen del Bajio S.A. de C.V.', 'ernesto.mancera@camionesbajio.com', '4425121347', 'DBV840717386', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-29 17:49:13', 0, NULL, 'free', NULL, 'qr_REG-20251129-588535.png', 1, '2025-11-29 17:49:14', NULL, 1, '2025-11-29 17:49:13', NULL);
INSERT INTO `event_registrations` (`id`, `registration_code`, `is_guest`, `guest_type`, `parent_registration_id`, `is_owner_representative`, `attendee_name`, `attendee_phone`, `attendee_email`, `additional_attendees`, `total_amount`, `is_courtesy_ticket`, `event_id`, `contact_id`, `guest_name`, `guest_email`, `guest_phone`, `guest_rfc`, `razon_social`, `nombre_empresario`, `nombre_empresario_representante`, `nombre_asistente`, `email_asistente`, `whatsapp_asistente`, `requiere_pago`, `tickets`, `registration_date`, `attended`, `attendance_time`, `payment_status`, `event_category`, `qr_code`, `qr_sent`, `qr_sent_at`, `email_type_sent`, `confirmation_sent`, `confirmation_sent_at`, `notes`) VALUES
(181, 'REG-20251129-95A293', 0, NULL, 180, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'ADRIANA RUIZ GARCIA', 'adriana.ruiz@vw-camionesbajio.com', '4427835060', 'DBV840717386', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 17:49:13', 0, NULL, 'free', NULL, 'qr_REG-20251129-95A293.png', 1, '2025-11-29 17:49:15', NULL, 0, NULL, NULL),
(182, 'REG-20251129-53CC5D', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Corporativo Paredes', 'gcparedes97@hotmail.com', '4422810713', 'PARA750406CR2', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 17:58:29', 0, NULL, 'free', NULL, 'qr_REG-20251129-53CC5D.png', 1, '2025-11-29 17:58:30', NULL, 1, '2025-11-29 17:58:29', NULL),
(183, 'REG-20251129-E2923E', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'DAYANE JANET TORANZO GLZ', 'dayanetoranzo@gmail.com', '4421490600', 'TOGD8407016Z7', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 18:28:41', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(184, 'REG-20251129-C30346', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'International lean six sigma', 'admisiones.queretaro@ilssg.org', '5534004041', 'RARE8611072H0', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 20:01:15', 0, NULL, 'free', NULL, 'qr_REG-20251129-C30346.png', 1, '2025-11-29 20:01:16', NULL, 1, '2025-11-29 20:01:15', NULL),
(185, 'REG-20251129-99182A', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Guillermo Justo Tapia', 'gjusto65@gmail.com', '4462188037', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 22:01:07', 0, NULL, 'free', NULL, 'qr_REG-20251129-99182A.png', 1, '2025-11-29 22:01:08', NULL, 1, '2025-11-29 22:01:07', NULL),
(186, 'REG-20251129-AC773D', 1, 'OTRO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Guillermina Hernández', 'g.hernandez28@outlook.com', '4421199994', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 23:09:56', 0, NULL, 'free', NULL, 'qr_REG-20251129-AC773D.png', 1, '2025-11-29 23:09:57', NULL, 1, '2025-11-29 23:09:56', NULL),
(187, 'REG-20251129-8F2966', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'SOS Al Rescate de tus ventas', 'alrescate.detusventas@comovendermas.com.mx', '4424050685', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-29 23:19:28', 0, NULL, 'free', NULL, 'qr_REG-20251129-8F2966.png', 1, '2025-11-29 23:19:29', NULL, 1, '2025-11-29 23:19:28', NULL),
(188, 'REG-20251129-D24487', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'SOS Al Rescate de tus ventas', 'alrescate.detusventas@comovendermas.com.mx', '4424050685', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 03:41:40', 0, NULL, 'free', NULL, 'qr_REG-20251129-D24487.png', 1, '2025-11-30 03:41:41', NULL, 1, '2025-11-30 03:41:40', NULL),
(189, 'REG-20251129-309F1F', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'Gabriela Ruiz Ruiz', 'ventas2@fabricantesderacks.com.mx', '4425401290', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 03:54:06', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-30 03:54:07', NULL),
(190, 'REG-20251129-E81F1D', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"ing. Adrian Cortes Lozada\", \"email\": \"adrian@limainmobiliaria.com.mx\", \"phone\": \"4461036150\"}}', 0.00, 0, 12, NULL, 'Lima servicio Inmobiliarios', 'lilianherrera30@yahoo.com.mx', '4423228981', 'HEHL7109018Y3', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-30 04:12:36', 0, NULL, 'free', NULL, 'qr_REG-20251129-E81F1D.png', 1, '2025-11-30 04:12:37', NULL, 1, '2025-11-30 04:12:36', NULL),
(191, 'REG-20251129-EDAB3C', 0, NULL, 190, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'ing. Adrian Cortes Lozada', 'adrian@limainmobiliaria.com.mx', '4461036150', 'HEHL7109018Y3', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 04:12:36', 0, NULL, 'free', NULL, 'qr_REG-20251129-EDAB3C.png', 1, '2025-11-30 04:12:38', NULL, 0, NULL, NULL),
(192, 'REG-20251129-72777D', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"ing. Adrian Cortes Lozada\", \"email\": \"adrian@limainmobiliaria.com.mx\", \"phone\": \"4461036150\"}}', 0.00, 0, 12, NULL, 'Lima servicio Inmobiliarios', 'lilianherrera30@yahoo.com.mx', '4423228981', 'HEHL7109018Y3', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-11-30 04:12:38', 0, NULL, 'free', NULL, 'qr_REG-20251129-72777D.png', 1, '2025-11-30 04:12:39', NULL, 1, '2025-11-30 04:12:38', NULL),
(193, 'REG-20251129-402E3C', 0, NULL, 192, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'ing. Adrian Cortes Lozada', 'adrian@limainmobiliaria.com.mx', '4461036150', 'HEHL7109018Y3', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 04:12:38', 0, NULL, 'free', NULL, 'qr_REG-20251129-402E3C.png', 1, '2025-11-30 04:12:40', NULL, 0, NULL, NULL),
(194, 'REG-20251129-7BA036', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'SOS Al Rescate de tus ventas', 'alrescate.detusventas@comovendermas.com.mx', '4424050685', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 04:25:12', 0, NULL, 'free', NULL, 'qr_REG-20251129-7BA036.png', 1, '2025-11-30 04:25:13', NULL, 1, '2025-11-30 04:25:12', NULL),
(195, 'REG-20251130-BE4F36', 1, 'OTRO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Corporativo IEDU', 'corporativo@institutoiedu.com', '5538977742', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 15:49:21', 0, NULL, 'free', NULL, 'qr_REG-20251130-BE4F36.png', 1, '2025-11-30 15:49:22', NULL, 1, '2025-11-30 15:49:21', NULL),
(196, 'REG-20251130-084A71', 1, 'OTRO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Corporativo Iedu', 'ivan.orzco24@gmail.com', '5538977742', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 15:51:13', 0, NULL, 'free', NULL, 'qr_REG-20251130-084A71.png', 1, '2025-11-30 15:51:14', NULL, 1, '2025-11-30 15:51:13', NULL),
(198, 'REG-20251130-E413FC', 1, 'OTRO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Guillermina Hernández', 'g.hernandez28@outlook.com', '4421199994', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 18:44:22', 0, NULL, 'free', NULL, 'qr_REG-20251130-E413FC.png', 1, '2025-11-30 18:44:23', NULL, 1, '2025-11-30 18:44:22', NULL),
(199, 'REG-20251130-49D2C2', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'ROTEC Climas y Servicios', 'rotec.climasyservicios@gmail.com', '6461291695', 'ROPJ800619CV8', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-11-30 21:33:52', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-11-30 21:33:52', NULL),
(200, 'REG-20251130-5DF0C2', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'MONICA LIZBETH GOMEZ TAPIA', 'ml.gomez.tapia@gmail.com', '4424953035', 'GOTM860908FV2', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 00:55:24', 0, NULL, 'free', NULL, 'qr_REG-20251130-5DF0C2.png', 1, '2025-12-01 00:55:25', NULL, 1, '2025-12-01 00:55:24', NULL),
(201, 'REG-20251130-8C4A9C', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'Quiropractico Juriqulla', 'kaflo_22@yahoo.com', '5534449014', 'FOEK770128RT6', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 02:51:40', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-12-01 02:51:40', NULL),
(202, 'REG-20251201-EA9CCB', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Aranday Y Asociados', 'cesararanday@gmail.com', '4423599327', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 13:05:15', 0, NULL, 'free', NULL, 'qr_REG-20251201-EA9CCB.png', 1, '2025-12-01 13:05:16', NULL, 1, '2025-12-01 13:05:15', NULL),
(203, 'REG-20251201-7D4B33', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'ROTEC Climas y Servicios', 'rotec.climasyservicios@gmail.com', '6461291695', 'ROPJ800619CV8', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 14:23:30', 0, NULL, 'paid', NULL, 'qr_REG-20251201-7D4B33.png', 1, '2025-12-01 14:25:34', NULL, 1, '2025-12-01 14:23:31', 'PayPal Order ID: 9ST00400LB4165324'),
(204, 'REG-20251201-42F02D', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 15, NULL, 'Miel Gallardo', 'keidagallardoo@gmail.com', '4422317676', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 15:15:03', 0, NULL, 'free', NULL, 'qr_REG-20251201-42F02D.png', 1, '2025-12-01 15:15:04', NULL, 1, '2025-12-01 15:15:03', NULL),
(205, 'REG-20251201-24A8EE', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Karla Kassandra de la Vega de la Garza\", \"email\": \"karla.delavega@weadvise.mx\", \"phone\": \"4428510010\"}}', 0.00, 0, 12, NULL, 'Juan Francisco Mena Vega', 'juan.mena@weadvise.mx', '4421229205', 'LAC200624MV4', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-12-01 15:41:28', 0, NULL, 'free', NULL, 'qr_REG-20251201-24A8EE.png', 1, '2025-12-01 15:41:29', NULL, 1, '2025-12-01 15:41:28', NULL),
(206, 'REG-20251201-828ACD', 0, NULL, 205, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Karla Kassandra de la Vega de la Garza', 'karla.delavega@weadvise.mx', '4428510010', 'LAC200624MV4', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 15:41:28', 0, NULL, 'free', NULL, 'qr_REG-20251201-828ACD.png', 1, '2025-12-01 15:41:30', NULL, 0, NULL, NULL),
(207, 'REG-20251201-9E727C', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'Grupo Comercial e Integral ONR de México', 'edgar.rodriguez@onr.mx', '4428688751', 'GCI190703RG7', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 17:19:01', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-12-01 17:19:01', NULL),
(208, 'REG-20251201-0F7918', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 16, NULL, 'Jehú Pacheco', '422169938@derecho.unam.mx', '4427198013', 'ABC123456XYZ', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 17:35:29', 0, NULL, 'free', NULL, 'qr_REG-20251201-0F7918.png', 1, '2025-12-01 17:35:30', NULL, 1, '2025-12-01 17:35:29', NULL),
(209, 'REG-20251201-330EAE', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 16, NULL, 'Roberto Cisneros', 'soportecrm@camaradecomercioqro.mx', '4421234567', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 17:52:33', 0, NULL, 'free', NULL, 'qr_REG-20251201-330EAE.png', 1, '2025-12-01 17:52:34', NULL, 1, '2025-12-01 17:52:33', NULL),
(210, 'REG-20251201-1515BE', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'NOECHEM', 'ventas@neochem.mx', '4423551828', 'NEO020315MQ2', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 18:05:58', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(211, 'REG-20251201-6EE2B6', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'AMEXME', 'pao_soria@hotmail.com', '4421878883', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 18:10:56', 0, NULL, 'free', NULL, 'qr_REG-20251201-6EE2B6.png', 1, '2025-12-01 18:10:57', NULL, 1, '2025-12-01 18:10:57', NULL),
(212, 'REG-20251201-C96DF0', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'AMEXME', 'presidenta.cap.queretaro@gmail.com', '4421878883', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 18:12:15', 0, NULL, 'free', NULL, 'qr_REG-20251201-C96DF0.png', 1, '2025-12-01 18:12:16', NULL, 1, '2025-12-01 18:12:15', NULL),
(213, 'REG-20251201-936B80', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 17, NULL, 'Jehú Pacheco', '422169938@derecho.unam.mx', '4427198013', 'PAMJ021105V85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 18:19:10', 0, NULL, 'free', NULL, 'qr_REG-20251201-936B80.png', 1, '2025-12-01 18:19:11', NULL, 1, '2025-12-01 18:19:11', NULL),
(214, 'REG-20251201-C785A9', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 1.00, 0, 17, NULL, 'Jehú Pacheco', '422169938@derecho.unam.mx', '4427198013', 'PAMJ021105V85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 18:31:32', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-12-01 18:31:32', NULL),
(215, 'REG-20251201-2CEE87', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'DAYANE JANET TORANZO GLZ', 'dayanetoranzo@gmail.com', '4421490600', 'TOGD8407016Z7', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 18:32:58', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(216, 'REG-20251201-E3145E', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 2.00, 0, 17, NULL, 'Jehú Pacheco', 'soportecrm@camaradecomercioqro.mx', '4427198013', 'PAMJ021105V85', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 18:35:32', 0, NULL, 'pending', NULL, NULL, 0, NULL, NULL, 1, '2025-12-01 18:35:32', NULL),
(217, 'REG-20251201-877640', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Iliana Sánchez Villanueva', 'ilisanchez@inna.mx', '4424458051', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 19:26:09', 0, NULL, 'free', NULL, 'qr_REG-20251201-877640.png', 1, '2025-12-01 19:26:10', NULL, 1, '2025-12-01 19:26:09', NULL),
(218, 'REG-20251201-0ED232', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Jimmy Javier peña Baltazar', 'jimmyelcontratista@gmail.com', '4423795309', 'Pebj871010qy3', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 19:31:44', 0, NULL, 'free', NULL, 'qr_REG-20251201-0ED232.png', 1, '2025-12-01 19:31:45', NULL, 1, '2025-12-01 19:31:44', NULL),
(219, 'REG-20251201-EDA19B', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Shima Avelino', 'avelino.cedillo.gina@gmail.com', '4428499050', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 19:32:46', 0, NULL, 'free', NULL, 'qr_REG-20251201-EDA19B.png', 1, '2025-12-01 19:32:47', NULL, 1, '2025-12-01 19:32:46', NULL),
(220, 'REG-20251201-8F11CC', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Corruempaques', 'ycaballero@corruempaques.com', '4421730862', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 19:34:55', 0, NULL, 'free', NULL, 'qr_REG-20251201-8F11CC.png', 1, '2025-12-01 19:34:56', NULL, 1, '2025-12-01 19:34:55', NULL),
(221, 'REG-20251201-A81EF2', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Gina Cedillo', 'ginaave75@hotmail.com', '4427491275', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 19:36:50', 0, NULL, 'free', NULL, 'qr_REG-20251201-A81EF2.png', 1, '2025-12-01 19:36:51', NULL, 1, '2025-12-01 19:36:50', NULL),
(222, 'REG-20251201-97DC58', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Industria Marmolera Queretana S. De R.L. de C.V.', 'ernesto.ernests@gmail.com', '4423016895', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 20:59:12', 0, NULL, 'free', NULL, 'qr_REG-20251201-97DC58.png', 1, '2025-12-01 20:59:13', NULL, 1, '2025-12-01 20:59:12', NULL),
(223, 'REG-20251201-39FD56', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Instituto Dicormo', 'direccion@dicormo.com', '4432790608', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 21:03:58', 0, NULL, 'free', NULL, 'qr_REG-20251201-39FD56.png', 1, '2025-12-01 21:03:59', NULL, 1, '2025-12-01 21:03:58', NULL),
(224, 'REG-20251201-05864E', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Instituto Dicormo', 'direccion@dicormo.com', '4432790608', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 21:03:59', 0, NULL, 'free', NULL, 'qr_REG-20251201-05864E.png', 1, '2025-12-01 21:04:00', NULL, 1, '2025-12-01 21:03:59', NULL),
(225, 'REG-20251201-BCA90E', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Isabel Bonilla', 'isabel.bonilla@cesba-queretaro.edu.mx', '4424716505', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:08:47', 0, NULL, 'free', NULL, 'qr_REG-20251201-BCA90E.png', 1, '2025-12-01 22:08:48', NULL, 1, '2025-12-01 22:08:47', NULL),
(226, 'REG-20251201-24A880', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Aranday Y Asociados', 'cesararanday@gmail.com', '4423599327', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:12:41', 0, NULL, 'free', NULL, 'qr_REG-20251201-24A880.png', 1, '2025-12-01 22:12:42', NULL, 1, '2025-12-01 22:12:41', NULL),
(227, 'REG-20251201-15F19E', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Aranday Y Asociados', 'cesararanday@gmail.com', '4423599327', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:17:44', 0, NULL, 'free', NULL, 'qr_REG-20251201-15F19E.png', 1, '2025-12-01 22:17:45', NULL, 1, '2025-12-01 22:17:45', NULL),
(228, 'REG-20251201-22AA2A', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Aranday Y Asociados', 'cesararanday@gmail.com', '4423599327', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:17:50', 0, NULL, 'free', NULL, 'qr_REG-20251201-22AA2A.png', 1, '2025-12-01 22:17:51', NULL, 1, '2025-12-01 22:17:50', NULL),
(230, 'REG-20251201-2E974B', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Mis Soluciones', 'asanchez@missoluciones.mx', '4424120012', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:27:11', 0, NULL, 'free', NULL, 'qr_REG-20251201-2E974B.png', 1, '2025-12-01 22:27:11', NULL, 1, '2025-12-01 22:27:11', NULL),
(231, 'REG-20251201-CEE020', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Mis Soluciones', 'asanchez@missoluciones.mx', '4424120012', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:27:42', 0, NULL, 'free', NULL, 'qr_REG-20251201-CEE020.png', 1, '2025-12-01 22:27:43', NULL, 1, '2025-12-01 22:27:43', NULL),
(232, 'REG-20251201-5ECD1D', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Ale Kornhauser/ Helados Gina', 'eventosheladosgina@gmail.com', '4424238613', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:45:56', 0, NULL, 'free', NULL, 'qr_REG-20251201-5ECD1D.png', 1, '2025-12-01 22:45:57', NULL, 1, '2025-12-01 22:45:56', NULL),
(233, 'REG-20251201-B77DBA', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'BEEHOKS INDUSTRIAS', 'jlfernandez@beehoks.com', '4423448875', 'BIN210902322', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:53:20', 0, NULL, 'free', NULL, 'qr_REG-20251201-B77DBA.png', 1, '2025-12-01 22:53:21', NULL, 1, '2025-12-01 22:53:20', NULL),
(234, 'REG-20251201-9B8CBB', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Marisol Amador \", \"email\": \"aahalfredo@hotmail.com\", \"phone\": \"4421412241\"}}', 0.00, 0, 12, NULL, 'MexiCases', 'ceo@mexicases.com', '4421412241', 'AAHA920510T86', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-12-01 22:56:35', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(235, 'REG-20251201-EED0C2', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'MexiCases', 'ceo@mexicases.com', '4421412241', 'AAHA920510T86', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:57:14', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(236, 'REG-20251201-5A13EF', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Cesar Chavez Cornejo / CC Seguros', 'ccseguros3@gmail.com', '4424538159', 'CACC8410267Q0', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:57:27', 0, NULL, 'free', NULL, 'qr_REG-20251201-5A13EF.png', 1, '2025-12-01 22:57:28', NULL, 1, '2025-12-01 22:57:27', NULL),
(237, 'REG-20251201-D719C3', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'MexiCases', 'ceo@mexicases.com', '4421412241', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 22:58:10', 0, NULL, 'free', NULL, 'qr_REG-20251201-D719C3.png', 1, '2025-12-01 22:58:11', NULL, 1, '2025-12-01 22:58:10', NULL),
(238, 'REG-20251201-90C925', 1, 'OTRO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'FUMINEITOR S.A', 'fumineitor1993@hotmail.com', '4422048783', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 23:03:46', 0, NULL, 'free', NULL, 'qr_REG-20251201-90C925.png', 1, '2025-12-01 23:03:47', NULL, 1, '2025-12-01 23:03:46', NULL),
(239, 'REG-20251201-31A2BE', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Sends Logística Aduanal', 'comercial@sends.com.mx', '4427744415', 'JGL200619DX1', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 23:12:00', 0, NULL, 'free', NULL, 'qr_REG-20251201-31A2BE.png', 1, '2025-12-01 23:12:01', NULL, 1, '2025-12-01 23:12:00', NULL),
(240, 'REG-20251201-04C38F', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'NOECHEM', 'ventas@neochem.mx', '4423551828', 'NEO020315MQ2', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 23:32:16', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(241, 'REG-20251201-05580E', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Maria Lugo Uribe\", \"email\": \"lui.hanei@gmail.com\", \"phone\": \"4421570242\"}}', 0.00, 0, 12, NULL, 'Luís Eduardo Nuñez Silva /', 'lui.hanei@gmail.com', '4424140632', 'Nusl710505387', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-12-01 23:33:27', 0, NULL, 'free', NULL, 'qr_REG-20251201-05580E.png', 1, '2025-12-01 23:33:28', NULL, 1, '2025-12-01 23:33:27', NULL),
(242, 'REG-20251201-7272A5', 0, NULL, 241, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Maria Lugo Uribe', 'lui.hanei@gmail.com', '4421570242', 'Nusl710505387', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 23:33:27', 0, NULL, 'free', NULL, 'qr_REG-20251201-7272A5.png', 1, '2025-12-01 23:33:29', NULL, 0, NULL, NULL),
(243, 'REG-20251201-187B08', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Y.K.H. Management &amp; Solutions', 'yankofk@gmail.com', '4423210809', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 23:51:50', 0, NULL, 'free', NULL, 'qr_REG-20251201-187B08.png', 1, '2025-12-01 23:51:51', NULL, 1, '2025-12-01 23:51:50', NULL),
(244, 'REG-20251201-A2D767', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Soluciones Migura', 'asantos@solucionesmigura.com', '5540842166', 'SMM160929EV8', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-01 23:55:37', 0, NULL, 'free', NULL, 'qr_REG-20251201-A2D767.png', 1, '2025-12-01 23:55:38', NULL, 1, '2025-12-01 23:55:37', NULL),
(245, 'REG-20251201-CC010B', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Ricardo García Rodríguez', 'capbusiness.contacto@gmail.com', '4423280033', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 00:51:16', 0, NULL, 'free', NULL, 'qr_REG-20251201-CC010B.png', 1, '2025-12-02 00:51:17', NULL, 1, '2025-12-02 00:51:16', NULL),
(246, 'REG-20251201-6E1C8F', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Telener360', 'doam@telener360.com', '4422597343', 'TTS1508018L0', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 00:58:21', 0, NULL, 'free', NULL, 'qr_REG-20251201-6E1C8F.png', 1, '2025-12-02 00:58:22', NULL, 1, '2025-12-02 00:58:21', NULL),
(247, 'REG-20251201-61203B', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'OLA TIRES', 'ssllqro@gmail.com', '4422260689', 'OILA610613R66', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 01:07:05', 0, NULL, 'free', NULL, 'qr_REG-20251201-61203B.png', 1, '2025-12-02 01:07:06', NULL, 1, '2025-12-02 01:07:05', NULL),
(248, 'REG-20251201-51DDE6', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'OLA TIRES', 'ssllqro@gmail.com', '4422260689', 'OILA610613R66', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 01:07:06', 0, NULL, 'free', NULL, 'qr_REG-20251201-51DDE6.png', 1, '2025-12-02 01:07:06', NULL, 1, '2025-12-02 01:07:06', NULL),
(249, 'REG-20251201-E1A979', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"Jorge Ceballos\", \"email\": \"tecnologia@grupokyza.com\", \"phone\": \"5548876011\"}}', 0.00, 0, 12, NULL, 'Adla Athie/Grupo Kyza', 'ceo@grupokyza.com', '5554196809', 'AIRA750920B11', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-12-02 01:20:37', 0, NULL, 'free', NULL, 'qr_REG-20251201-E1A979.png', 1, '2025-12-02 01:20:38', NULL, 1, '2025-12-02 01:20:37', NULL),
(250, 'REG-20251201-FE3033', 0, NULL, 249, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'Jorge Ceballos', 'tecnologia@grupokyza.com', '5548876011', 'AIRA750920B11', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 01:20:37', 0, NULL, 'free', NULL, 'qr_REG-20251201-FE3033.png', 1, '2025-12-02 01:20:39', NULL, 0, NULL, NULL),
(251, 'REG-20251201-25E1A5', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'CIT FESTO', 'ana.breton@comercializadora-it.com', '4423944787', 'CIT130628920', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 02:24:16', 0, NULL, 'free', NULL, 'qr_REG-20251201-25E1A5.png', 1, '2025-12-02 02:24:17', NULL, 1, '2025-12-02 02:24:16', NULL),
(252, 'REG-20251201-4FBF41', 0, NULL, NULL, 1, NULL, NULL, NULL, '{\"1\": {\"name\": \"José Antonio Ugalde Guerrero \", \"email\": \"a.ugalde@udelondresqueretaro.com.mx\", \"phone\": \"4422472181\"}}', 0.00, 0, 12, NULL, 'Luis Núñez Salinas Londres Universidad', 'l.nunez@udelondresqueretaro.com.mx', '4421300205', '', NULL, NULL, NULL, '', NULL, NULL, 0, 2, '2025-12-02 02:27:02', 0, NULL, 'free', NULL, 'qr_REG-20251201-4FBF41.png', 1, '2025-12-02 02:27:03', NULL, 1, '2025-12-02 02:27:02', NULL),
(253, 'REG-20251201-287BB3', 0, NULL, 252, 0, NULL, NULL, NULL, NULL, 0.00, 0, 12, NULL, 'José Antonio Ugalde Guerrero', 'a.ugalde@udelondresqueretaro.com.mx', '4422472181', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 02:27:02', 0, NULL, 'free', NULL, 'qr_REG-20251201-287BB3.png', 1, '2025-12-02 02:27:04', NULL, 0, NULL, NULL),
(254, 'REG-20251201-13FD53', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Silvia Rodríguez Díaz', 'silvia.rdz@live.com.mx', '4441262017', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 02:49:11', 0, NULL, 'free', NULL, 'qr_REG-20251201-13FD53.png', 1, '2025-12-02 02:49:12', NULL, 1, '2025-12-02 02:49:11', NULL),
(255, 'REG-20251201-D48F9D', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'OLA TIRES', 'ja.olatirrs001@gmail.com', '4424790400', 'OIAJ910404AJ7', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 03:07:02', 0, NULL, 'free', NULL, 'qr_REG-20251201-D48F9D.png', 1, '2025-12-02 03:07:03', NULL, 1, '2025-12-02 03:07:02', NULL),
(256, 'REG-20251201-87BD3A', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Lapstec Querétaro', 'lalmaguer81@gmail.com', '4423380483', 'AALL810103CP3', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 03:27:21', 0, NULL, 'free', NULL, 'qr_REG-20251201-87BD3A.png', 1, '2025-12-02 03:27:21', NULL, 1, '2025-12-02 03:27:21', NULL),
(257, 'REG-20251201-35EF76', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'GMC Ingeniería', 'jorge.garcia@gmcingenieria.mx', '5518010714', 'GII160527S96', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 05:04:57', 0, NULL, 'free', NULL, 'qr_REG-20251201-35EF76.png', 1, '2025-12-02 05:04:58', NULL, 1, '2025-12-02 05:04:57', NULL),
(258, 'REG-20251201-E939C5', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 500.00, 0, 6, NULL, 'Aaron Chavez Diaz Gonzalez', 'achavez@avilacommunications.com.mx', '4427514299', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 05:36:15', 0, NULL, 'paid', NULL, 'qr_REG-20251201-E939C5.png', 1, '2025-12-02 05:39:13', NULL, 1, '2025-12-02 05:36:15', 'PayPal Order ID: 8LE61598A4728680N'),
(259, 'REG-20251202-1F2031', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'OLA TIRES', 'ssllqro@gmail.com', '4422260689', 'OILA610613R66', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 10:34:31', 0, NULL, 'free', NULL, 'qr_REG-20251202-1F2031.png', 1, '2025-12-02 10:34:32', NULL, 1, '2025-12-02 10:34:31', NULL),
(260, 'REG-20251202-585CFE', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'GUILLERMO JESUS', 'guille2gonza@hotmail.com', '4422022750', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 13:54:27', 0, NULL, 'free', NULL, 'qr_REG-20251202-585CFE.png', 1, '2025-12-02 13:54:28', NULL, 1, '2025-12-02 13:54:27', NULL),
(261, 'REG-20251202-2111E5', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'Grupo Comercial e Integral ONR de México', 'edgar.rodriguez@onr.mx', '4428688751', 'GCI190703RG7', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 15:00:22', 0, NULL, 'free', NULL, 'qr_REG-20251202-2111E5.png', 1, '2025-12-02 15:00:23', NULL, 1, '2025-12-02 15:00:22', NULL),
(262, 'REG-20251202-40CB94', 0, NULL, NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'QTequeñitos', 'jesusorlando20@gmail.com', '5586764497', 'Tooj8911215R0', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 15:53:33', 0, NULL, 'free', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL),
(263, 'REG-20251202-D4D3CF', 1, 'INVITADO', NULL, 1, NULL, NULL, NULL, '[]', 0.00, 0, 12, NULL, 'QTequeñitos', 'jesusorlando20@gmail.com', '5586764497', '', NULL, NULL, NULL, '', NULL, NULL, 0, 1, '2025-12-02 15:54:33', 0, NULL, 'free', NULL, 'qr_REG-20251202-D4D3CF.png', 1, '2025-12-02 15:54:34', NULL, 1, '2025-12-02 15:54:33', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `event_type_catalog`
--

CREATE TABLE `event_type_catalog` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `event_type_catalog`
--

INSERT INTO `event_type_catalog` (`id`, `code`, `name`, `description`, `is_active`, `created_at`) VALUES
(1, 'interno', 'Evento Interno CCQ', 'Eventos organizados en las instalaciones de la Cámara de Comercio de Querétaro. Pueden ser con costo o gratuitos', 1, '2025-11-25 13:02:56'),
(2, 'externo', 'Evento Externo', 'Eventos organizados fuera de las instalaciones de la Cámara (ejemplo Expo Industrial y Comercial, Expo Regreso a Clases)', 1, '2025-11-25 13:02:56'),
(3, 'terceros', 'Evento de Terceros', 'Eventos organizados por terceros con acceso gratuito o preferencial a nuestros agremiados', 1, '2025-11-25 13:02:56'),
(4, 'publico', 'Evento Público', 'Eventos abiertos a afiliados y público general GRATUITOS', 1, '2025-11-27 13:18:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `financial_categories`
--

CREATE TABLE `financial_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('ingreso','egreso') COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `financial_categories`
--

INSERT INTO `financial_categories` (`id`, `name`, `type`, `description`, `is_active`, `created_at`) VALUES
(1, 'Membresías', 'ingreso', 'Pagos por membresías y afiliaciones', 1, '2025-11-25 15:32:28'),
(2, 'Servicios', 'ingreso', 'Ingresos por servicios adicionales', 1, '2025-11-25 15:32:28'),
(3, 'Eventos', 'ingreso', 'Ingresos por eventos y capacitaciones', 1, '2025-11-25 15:32:28'),
(4, 'Patrocinios', 'ingreso', 'Ingresos por patrocinios', 1, '2025-11-25 15:32:28'),
(5, 'Renta de Salones', 'ingreso', 'Ingresos por renta de espacios', 1, '2025-11-25 15:32:28'),
(6, 'Otros Ingresos', 'ingreso', 'Otros ingresos no categorizados', 1, '2025-11-25 15:32:28'),
(7, 'Nómina', 'egreso', 'Gastos de nómina y salarios', 1, '2025-11-25 15:32:28'),
(8, 'Servicios Básicos', 'egreso', 'Luz, agua, teléfono, internet', 1, '2025-11-25 15:32:28'),
(9, 'Materiales', 'egreso', 'Materiales de oficina y consumibles', 1, '2025-11-25 15:32:28'),
(10, 'Mantenimiento', 'egreso', 'Gastos de mantenimiento', 1, '2025-11-25 15:32:28'),
(11, 'Marketing', 'egreso', 'Gastos de publicidad y marketing', 1, '2025-11-25 15:32:28'),
(12, 'Otros Egresos', 'egreso', 'Otros gastos no categorizados', 1, '2025-11-25 15:32:28'),
(13, 'Membresías', 'ingreso', 'Pagos por membresías y afiliaciones', 1, '2025-11-25 15:34:10'),
(14, 'Servicios', 'ingreso', 'Ingresos por servicios adicionales', 1, '2025-11-25 15:34:10'),
(15, 'Eventos', 'ingreso', 'Ingresos por eventos y capacitaciones', 1, '2025-11-25 15:34:10'),
(16, 'Patrocinios', 'ingreso', 'Ingresos por patrocinios', 1, '2025-11-25 15:34:10'),
(17, 'Renta de Salones', 'ingreso', 'Ingresos por renta de espacios', 1, '2025-11-25 15:34:10'),
(18, 'Otros Ingresos', 'ingreso', 'Otros ingresos no categorizados', 1, '2025-11-25 15:34:10'),
(19, 'Nómina', 'egreso', 'Gastos de nómina y salarios', 1, '2025-11-25 15:34:10'),
(20, 'Servicios Básicos', 'egreso', 'Luz, agua, teléfono, internet', 1, '2025-11-25 15:34:10'),
(21, 'Materiales', 'egreso', 'Materiales de oficina y consumibles', 1, '2025-11-25 15:34:10'),
(22, 'Mantenimiento', 'egreso', 'Gastos de mantenimiento', 1, '2025-11-25 15:34:10'),
(23, 'Marketing', 'egreso', 'Gastos de publicidad y marketing', 1, '2025-11-25 15:34:10'),
(24, 'Otros Egresos', 'egreso', 'Otros gastos no categorizados', 1, '2025-11-25 15:34:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `financial_transactions`
--

CREATE TABLE `financial_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `reference` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Invoice number, receipt, etc.',
  `notes` text COLLATE utf8_unicode_ci,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membership_types`
--

CREATE TABLE `membership_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_days` int(11) DEFAULT '360',
  `benefits` json DEFAULT NULL,
  `characteristics` json DEFAULT NULL COMMENT 'List of membership characteristics (features)',
  `upsell_order` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'Order in upselling hierarchy: 1=Pyme, 2=Visionario, 3=Premier, 4=Patrocinador',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paypal_product_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `membership_types`
--

INSERT INTO `membership_types` (`id`, `name`, `code`, `price`, `duration_days`, `benefits`, `characteristics`, `upsell_order`, `is_active`, `created_at`, `paypal_product_id`) VALUES
(1, 'Membresía Básica', 'BASICA', 3000.00, 360, '{\"asesoria\": true, \"buscador\": true, \"networking\": true, \"descuento_eventos\": 10}', '[\"Acceso al buscador de proveedores\", \"Eventos de networking\"]', 0, 1, '2025-11-25 02:36:56', NULL),
(2, 'Membresía PYME', 'PYME', 5000.00, 360, '{\"asesoria\": true, \"buscador\": true, \"networking\": true, \"capacitaciones\": 2, \"descuento_eventos\": 20}', '[\"Acceso al buscador de proveedores\", \"Eventos de networking\", \"2 capacitaciones incluidas\", \"Asesoría empresarial\"]', 1, 1, '2025-11-25 02:36:56', NULL),
(3, 'Membresía PREMIER', 'PREMIER', 10000.00, 360, '{\"asesoria\": true, \"buscador\": true, \"marketing\": true, \"networking\": true, \"capacitaciones\": \"ilimitadas\", \"descuento_eventos\": 30}', '[\"Capacitaciones ilimitadas\", \"Marketing incluido\", \"Asesoría empresarial\", \"Eventos de networking VIP\"]', 3, 1, '2025-11-25 02:36:56', NULL),
(4, 'Patrocinador AAA', 'PATROCINADOR', 199000.00, 360, '{\"siem\": true, \"asesoria\": true, \"buscador\": true, \"marketing\": true, \"networking\": true, \"publicidad\": true, \"capacitaciones\": \"ilimitadas\", \"descuento_eventos\": 50}', '[\"Todos los beneficios Premier\", \"Publicidad destacada\", \"Mesa preferente en eventos\", \"Descuento máximo en servicios\"]', 4, 1, '2025-11-25 02:36:56', NULL),
(6, 'Patrocinador oficial', 'VISIONARIO', 99000.00, 365, '{\"asesoria\": true, \"buscador\": true, \"networking\": true, \"capacitaciones\": 8, \"descuento_eventos\": 50}', NULL, 2, 1, '2025-11-29 08:46:57', NULL),
(13, 'prueba2.3', 'FCFVBN', 5.00, 360, '{\"asesoria\": true, \"publicidad\": true}', NULL, 0, 1, '2025-12-01 21:32:14', 'P-89E38521CJ336505GNEXASXQ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivational_messages`
--

CREATE TABLE `motivational_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `context` enum('morning','afternoon','evening','off_hours','achievement','milestone') COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Emoji or icon',
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Motivational messages for commercial team';

--
-- Volcado de datos para la tabla `motivational_messages`
--

INSERT INTO `motivational_messages` (`id`, `context`, `icon`, `title`, `message`, `is_active`, `created_at`) VALUES
(1, 'morning', '☀️', '¡Buenos días!', 'Comienza el día con energía. Cada llamada es una oportunidad de éxito.', 1, '2025-11-30 18:39:09'),
(2, 'morning', '?', '¡A conquistar el día!', 'Las mañanas productivas generan resultados extraordinarios.', 1, '2025-11-30 18:39:09'),
(3, 'morning', '?', '¡Momento de crear!', 'Tu primer contacto del día puede ser el cierre más importante del mes.', 1, '2025-11-30 18:39:09'),
(4, 'morning', '?', '¡Nuevo día, nuevas oportunidades!', 'Hoy es el día perfecto para superar tus metas.', 1, '2025-11-30 18:39:09'),
(5, 'afternoon', '⚡', '¡Mantén el ritmo!', 'El mediodía es perfecto para dar seguimiento a tus prospectos más calientes.', 1, '2025-11-30 18:39:09'),
(6, 'afternoon', '?', '¡Enfócate en los objetivos!', 'Cada acción cuenta. Estás más cerca de tu meta de lo que crees.', 1, '2025-11-30 18:39:09'),
(7, 'afternoon', '?', '¡Energía al máximo!', 'Este es el momento para cerrar las oportunidades pendientes.', 1, '2025-11-30 18:39:09'),
(8, 'evening', '?', '¡Cierra el día con fuerza!', 'Las últimas horas son oro. Un seguimiento ahora puede marcar la diferencia.', 1, '2025-11-30 18:39:09'),
(9, 'evening', '✨', '¡Sprint final!', 'Aprovecha las últimas horas. Los mejores cierres ocurren al final del día.', 1, '2025-11-30 18:39:09'),
(10, 'evening', '?', '¡Última hora productiva!', 'Un email más, una llamada más. El éxito está en los detalles.', 1, '2025-11-30 18:39:09'),
(11, 'off_hours', '?', '¡Compromiso Excepcional!', 'Tu dedicación fuera del horario laboral demuestra un compromiso extraordinario con nuestros objetivos.', 1, '2025-11-30 18:39:09'),
(12, 'off_hours', '?', '¡Esfuerzo Reconocido!', 'Trabajar fuera de horario muestra tu pasión. Tu esfuerzo no pasa desapercibido.', 1, '2025-11-30 18:39:09'),
(13, 'off_hours', '?', '¡Dedicación Ejemplar!', 'Los grandes logros requieren dedicación extra. ¡Sigue adelante!', 1, '2025-11-30 18:39:09'),
(14, 'off_hours', '?', '¡Trabajo Inspirador!', 'Tu compromiso es un ejemplo para todo el equipo. ¡Gracias por tu dedicación!', 1, '2025-11-30 18:39:09'),
(15, 'achievement', '?', '¡Felicitaciones!', 'Has alcanzado un nuevo hito. Tu esfuerzo está dando frutos.', 1, '2025-11-30 18:39:09'),
(16, 'achievement', '?', '¡Eres el mejor!', 'Tu rendimiento este mes ha sido excepcional.', 1, '2025-11-30 18:39:09'),
(17, 'achievement', '?', '¡Meta cumplida!', 'Has demostrado que con dedicación todo es posible.', 1, '2025-11-30 18:39:09'),
(18, 'milestone', '?', '¡Crecimiento constante!', 'Tu progreso este mes muestra un patrón de mejora continua.', 1, '2025-11-30 18:39:09'),
(19, 'milestone', '?', '¡En el camino correcto!', 'Estás muy cerca de alcanzar tu objetivo mensual.', 1, '2025-11-30 18:39:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niza_classifications`
--

CREATE TABLE `niza_classifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `class_number` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `keywords` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `niza_classifications`
--

INSERT INTO `niza_classifications` (`id`, `class_number`, `name`, `description`, `keywords`) VALUES
(12, '1', 'Productos químicos', 'Productos químicos para la industria, la ciencia y la fotografía, así como para la agricultura, la horticultura y la silvicultura; resinas artificiales en bruto, materias plásticas en bruto; abonos para el suelo; composiciones extintoras; preparaciones para el temple y la soldadura; sustancias químicas para conservar alimentos; materias curtientes; adhesivos para la industria.', '[\"química\", \"industrial\", \"fertilizantes\", \"adhesivos\", \"resinas\"]'),
(13, '2', 'Pinturas, barnices, lacas', 'Pinturas, barnices, lacas; productos antioxidantes y productos para conservar la madera; materias tintóreas; mordientes; resinas naturales en bruto; metales en hojas y en polvo para pintores, decoradores, impresores y artistas.', '[\"pintura\", \"barniz\", \"laca\", \"colorantes\", \"tintes\"]'),
(14, '3', 'Productos de limpieza y cosméticos', 'Preparaciones para blanquear y otras sustancias para lavar la ropa; preparaciones para limpiar, pulir, desengrasar y raspar; jabones; productos de perfumería, aceites esenciales, cosméticos, lociones capilares; dentífricos.', '[\"cosméticos\", \"limpieza\", \"jabón\", \"perfumes\", \"belleza\"]'),
(15, '4', 'Aceites y combustibles industriales', 'Aceites y grasas para uso industrial; lubricantes; productos para absorber, regar y asentar el polvo; combustibles (incluida la gasolina para motores) y materias de alumbrado; velas y mechas de iluminación.', '[\"aceites\", \"lubricantes\", \"combustibles\", \"gasolina\", \"velas\"]'),
(16, '5', 'Productos farmacéuticos', 'Productos farmacéuticos y veterinarios; productos higiénicos y sanitarios para uso médico; alimentos y sustancias dietéticas para uso médico o veterinario; complementos alimenticios para personas o animales; emplastos, material para apósitos; material para empastes e improntas dentales; desinfectantes; productos para eliminar animales dañinos; fungicidas, herbicidas.', '[\"farmacéuticos\", \"medicinas\", \"veterinarios\", \"sanitarios\", \"desinfectantes\"]'),
(17, '6', 'Metales comunes', 'Metales comunes y sus aleaciones; materiales de construcción metálicos; construcciones transportables metálicas; materiales metálicos para vías férreas; cables e hilos metálicos no eléctricos; artículos de cerrajería y ferretería metálicos; tubos y tuberías metálicos; cajas de caudales; productos metálicos no comprendidos en otras clases; minerales metalíferos.', '[\"metales\", \"construcción\", \"ferretería\", \"cerrajería\", \"acero\"]'),
(18, '7', 'Máquinas y máquinas herramientas', 'Máquinas y máquinas herramientas; motores (excepto motores para vehículos terrestres); acoplamientos y elementos de transmisión (excepto para vehículos terrestres); instrumentos agrícolas que no sean accionados manualmente; incubadoras de huevos; distribuidores automáticos.', '[\"máquinas\", \"motores\", \"herramientas\", \"industrial\", \"maquinaria\"]'),
(19, '8', 'Herramientas e instrumentos manuales', 'Herramientas e instrumentos de mano accionados manualmente; artículos de cuchillería, tenedores y cucharas; armas blancas; maquinillas de afeitar.', '[\"cuchillería\", \"herramientas\", \"navajas\", \"tijeras\", \"instrumentos\"]'),
(20, '9', 'Aparatos científicos y electrónicos', 'Aparatos e instrumentos científicos, náuticos, geodésicos, fotográficos, cinematográficos, ópticos, de pesaje, de medición, de señalización, de control (inspección), de salvamento y de enseñanza; aparatos e instrumentos de conducción, distribución, transformación, acumulación, regulación o control de la electricidad; aparatos de grabación, transmisión o reproducción de sonido o imágenes; soportes de registro magnéticos, discos acústicos; discos compactos, DVD y otros soportes de grabación digitales; mecanismos para aparatos de previo pago; cajas registradoras, máquinas de calcular, equipos de procesamiento de datos, ordenadores; software; extintores.', '[\"electrónica\", \"computadoras\", \"software\", \"tecnología\", \"audio\", \"video\"]'),
(21, '10', 'Aparatos médicos', 'Aparatos e instrumentos quirúrgicos, médicos, odontológicos y veterinarios, así como miembros, ojos y dientes artificiales; artículos ortopédicos; material de sutura.', '[\"médico\", \"quirúrgico\", \"dental\", \"ortopédico\", \"instrumentos médicos\"]'),
(22, '11', 'Aparatos de iluminación y calefacción', 'Aparatos de alumbrado, de calefacción, de producción de vapor, de cocción, de refrigeración, de secado, de ventilación, de distribución de agua e instalaciones sanitarias.', '[\"iluminación\", \"calefacción\", \"refrigeración\", \"ventilación\", \"sanitarios\"]'),
(23, '12', 'Vehículos', 'Vehículos; aparatos de locomoción terrestre, aérea o acuática.', '[\"vehículos\", \"automóviles\", \"transporte\", \"motos\", \"bicicletas\"]'),
(24, '13', 'Armas de fuego', 'Armas de fuego; municiones y proyectiles; explosivos; fuegos artificiales.', '[\"armas\", \"municiones\", \"explosivos\", \"pirotecnia\"]'),
(25, '14', 'Metales preciosos y joyería', 'Metales preciosos y sus aleaciones; artículos de joyería, bisutería, piedras preciosas; artículos de relojería e instrumentos cronométricos.', '[\"joyería\", \"relojes\", \"oro\", \"plata\", \"piedras preciosas\"]'),
(26, '15', 'Instrumentos musicales', 'Instrumentos musicales.', '[\"música\", \"instrumentos\", \"guitarra\", \"piano\", \"percusión\"]'),
(27, '16', 'Papel y artículos de papelería', 'Papel, cartón y artículos de estas materias no comprendidos en otras clases; productos de imprenta; artículos de encuadernación; fotografías; artículos de papelería; adhesivos (pegamentos) de papelería o para uso doméstico; material para artistas; pinceles; máquinas de escribir y artículos de oficina (excepto muebles); material de instrucción o material didáctico (excepto aparatos); materias plásticas para embalar (no comprendidas en otras clases); caracteres de imprenta; clichés de imprenta.', '[\"papelería\", \"imprenta\", \"papel\", \"oficina\", \"encuadernación\"]'),
(28, '17', 'Caucho y plásticos', 'Caucho, gutapercha, goma, amianto, mica y productos de estas materias no comprendidos en otras clases; productos en materias plásticas semielaboradas; materiales para calafatear, estopar y aislar; tubos flexibles no metálicos.', '[\"caucho\", \"plástico\", \"aislantes\", \"empaques\", \"mangueras\"]'),
(29, '18', 'Artículos de cuero', 'Cuero y cuero de imitación, y productos de estas materias no comprendidos en otras clases; pieles de animales; baúles y maletas; paraguas y sombrillas; bastones; fustas, arneses y artículos de guarnicionería.', '[\"cuero\", \"bolsas\", \"maletas\", \"carteras\", \"pieles\"]'),
(30, '19', 'Materiales de construcción no metálicos', 'Materiales de construcción no metálicos; tubos rígidos no metálicos para la construcción; asfalto, pez y betún; construcciones transportables no metálicas; monumentos no metálicos.', '[\"construcción\", \"cemento\", \"concreto\", \"asfalto\", \"ladrillos\"]'),
(31, '20', 'Muebles', 'Muebles, espejos, marcos; productos de madera, corcho, caña, junco, mimbre, cuerno, hueso, marfil, ballena, concha, ámbar, nácar, espuma de mar, sucedáneos de todas estas materias o de materias plásticas, no comprendidos en otras clases.', '[\"muebles\", \"madera\", \"decoración\", \"espejos\", \"marcos\"]'),
(32, '21', 'Utensilios domésticos', 'Utensilios y recipientes para uso doméstico y culinario; peines y esponjas; cepillos; materiales para fabricar cepillos; material de limpieza; lana de acero; vidrio en bruto o semielaborado (excepto el vidrio utilizado en la construcción); artículos de cristalería, porcelana y loza no comprendidos en otras clases.', '[\"utensilios\", \"cocina\", \"hogar\", \"cristalería\", \"porcelana\"]'),
(33, '22', 'Cuerdas y fibras textiles', 'Cuerdas, cordeles, redes, tiendas de campaña, toldos, velas de navegación, sacos y bolsas (no comprendidos en otras clases); materiales de acolchado y relleno (excepto de caucho o de materias plásticas); materias textiles fibrosas en bruto.', '[\"cuerdas\", \"textiles\", \"lonas\", \"tiendas\", \"fibras\"]'),
(34, '23', 'Hilos para uso textil', 'Hilos para uso textil.', '[\"hilos\", \"textil\", \"costura\", \"bordado\"]'),
(35, '24', 'Productos textiles', 'Tejidos y productos textiles no comprendidos en otras clases; ropa de cama; ropa de mesa.', '[\"telas\", \"textiles\", \"ropa de cama\", \"mantelería\", \"cortinas\"]'),
(36, '25', 'Prendas de vestir', 'Prendas de vestir, calzado, artículos de sombrerería.', '[\"ropa\", \"calzado\", \"vestimenta\", \"moda\", \"sombreros\"]'),
(37, '26', 'Mercería y accesorios', 'Encajes y bordados, cintas y cordones; botones, ganchos y ojetes, alfileres y agujas; flores artificiales.', '[\"mercería\", \"botones\", \"encajes\", \"bordados\", \"accesorios\"]'),
(38, '27', 'Alfombras y tapices', 'Alfombras, felpudos, esteras, linóleo y otros revestimientos de suelos; tapicerías murales que no sean de materias textiles.', '[\"alfombras\", \"tapetes\", \"pisos\", \"tapicería\"]'),
(39, '28', 'Juegos y juguetes', 'Juegos y juguetes; artículos de gimnasia y deporte no comprendidos en otras clases; decoraciones para árboles de Navidad.', '[\"juguetes\", \"deportes\", \"juegos\", \"gimnasia\", \"navidad\"]'),
(40, '29', 'Carne, pescado, productos lácteos', 'Carne, pescado, carne de ave y carne de caza; extractos de carne; frutas y verduras, hortalizas y legumbres en conserva, congeladas, secas y cocidas; jaleas, confituras, compotas; huevos; leche y productos lácteos; aceites y grasas comestibles.', '[\"alimentos\", \"carne\", \"lácteos\", \"conservas\", \"aceites comestibles\"]'),
(41, '30', 'Café, té, productos de panadería', 'Café, té, cacao y sucedáneos del café; arroz; tapioca y sagú; harinas y preparaciones a base de cereales; pan, productos de pastelería y de confitería; helados; azúcar, miel, jarabe de melaza; levadura, polvos de hornear; sal; mostaza; vinagre, salsas (condimentos); especias; hielo.', '[\"café\", \"panadería\", \"pastelería\", \"cereales\", \"condimentos\"]'),
(42, '31', 'Productos agrícolas y semillas', 'Granos y productos agrícolas, hortícolas y forestales, no comprendidos en otras clases; animales vivos; frutas y verduras, hortalizas y legumbres frescas; semillas; plantas y flores naturales; alimentos para animales; malta.', '[\"agricultura\", \"semillas\", \"plantas\", \"animales\", \"horticultura\"]'),
(43, '32', 'Bebidas no alcohólicas', 'Cervezas; aguas minerales y gaseosas, y otras bebidas sin alcohol; bebidas de frutas y zumos de frutas; siropes y otras preparaciones para elaborar bebidas.', '[\"bebidas\", \"refrescos\", \"agua\", \"cerveza\", \"jugos\"]'),
(44, '33', 'Bebidas alcohólicas', 'Bebidas alcohólicas (excepto cervezas).', '[\"vinos\", \"licores\", \"tequila\", \"mezcal\", \"destilados\"]'),
(45, '34', 'Tabaco y artículos para fumadores', 'Tabaco; artículos para fumadores; cerillas.', '[\"tabaco\", \"cigarros\", \"puros\", \"encendedores\"]'),
(46, '35', 'Publicidad y negocios', 'Publicidad; gestión de negocios comerciales; administración comercial; trabajos de oficina.', '[\"publicidad\", \"marketing\", \"administración\", \"negocios\", \"comercio\"]'),
(47, '36', 'Seguros y finanzas', 'Seguros; operaciones financieras; operaciones monetarias; negocios inmobiliarios.', '[\"finanzas\", \"seguros\", \"banca\", \"inmobiliaria\", \"inversiones\"]'),
(48, '37', 'Construcción y reparaciones', 'Servicios de construcción; servicios de reparación; servicios de instalación.', '[\"construcción\", \"reparación\", \"instalación\", \"mantenimiento\", \"remodelación\"]'),
(49, '38', 'Telecomunicaciones', 'Telecomunicaciones.', '[\"telecomunicaciones\", \"internet\", \"telefonía\", \"comunicaciones\", \"redes\"]'),
(50, '39', 'Transporte y almacenamiento', 'Transporte; embalaje y almacenamiento de mercancías; organización de viajes.', '[\"transporte\", \"logística\", \"almacenamiento\", \"viajes\", \"mensajería\"]'),
(51, '40', 'Tratamiento de materiales', 'Tratamiento de materiales.', '[\"manufactura\", \"procesamiento\", \"tratamiento\", \"reciclaje\", \"impresión\"]'),
(52, '41', 'Educación y entretenimiento', 'Educación; formación; servicios de entretenimiento; actividades deportivas y culturales.', '[\"educación\", \"capacitación\", \"entretenimiento\", \"deportes\", \"cultura\"]'),
(53, '42', 'Servicios científicos y tecnológicos', 'Servicios científicos y tecnológicos, así como servicios de investigación y diseño en estos ámbitos; servicios de análisis e investigación industrial; diseño y desarrollo de equipos informáticos y de software.', '[\"tecnología\", \"software\", \"investigación\", \"diseño\", \"informática\"]'),
(54, '43', 'Servicios de restauración y hospedaje', 'Servicios de restauración (alimentación); hospedaje temporal.', '[\"restaurantes\", \"hoteles\", \"hospedaje\", \"catering\", \"alimentación\"]'),
(55, '44', 'Servicios médicos y de belleza', 'Servicios médicos; servicios veterinarios; tratamientos de higiene y de belleza para personas o animales; servicios de agricultura, horticultura y silvicultura.', '[\"médicos\", \"salud\", \"belleza\", \"veterinaria\", \"spa\"]'),
(56, '45', 'Servicios jurídicos y de seguridad', 'Servicios jurídicos; servicios de seguridad para la protección de bienes y personas; servicios personales y sociales prestados por terceros para satisfacer necesidades individuales.', '[\"legal\", \"abogados\", \"seguridad\", \"notaría\", \"servicios personales\"]'),
(57, '99', 'OTRA CATEGORÍA', 'Categoría personalizada para actividades no clasificadas en las clases 1-45 de NIZA. El usuario deberá especificar la categoría.', '[\"otra\", \"otro\", \"personalizada\", \"custom\", \"especial\"]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('vencimiento','actividad','no_match','oportunidad','beneficio','sistema','cross_selling','up_selling','evento','prospecto','felicitacion') COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `related_id` int(10) UNSIGNED DEFAULT NULL,
  `related_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source_section` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Original section: agenda, requirements, notifications',
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `related_id`, `related_type`, `source_section`, `is_read`, `read_at`, `created_at`) VALUES
(1, 2, 'vencimiento', 'Membresía próxima a vencer', 'La membresía de Hotelería Queretana vence en 7 días', '/afiliados/6', 6, 'affiliation', 'notifications', 0, NULL, '2025-11-25 02:36:56'),
(2, 2, 'actividad', 'Actividad pendiente', 'Tienes una llamada programada para hoy a las 10:00', '/agenda/1', 1, 'activity', 'notifications', 0, NULL, '2025-11-25 02:36:56'),
(3, 3, 'oportunidad', 'Nueva oportunidad de cross-selling', 'Industrias Queretanas podría estar interesado en marketing', '/journey/upselling', 5, 'contact', 'notifications', 0, NULL, '2025-11-25 02:36:56'),
(4, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"impresión 3d\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 02:36:56'),
(5, 3, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"dan\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 11:54:04'),
(6, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"dan\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 11:54:04'),
(7, 3, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"compur\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:20'),
(8, 7, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"compur\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:20'),
(9, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"compur\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:20'),
(10, 3, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"compu\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:22'),
(11, 7, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"compu\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:22'),
(12, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"compu\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:22'),
(13, 3, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"computadora\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:25'),
(14, 7, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"computadora\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:25'),
(15, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"computadora\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:25'),
(16, 3, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"impresión\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:37'),
(17, 7, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"impresión\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:37'),
(18, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"impresión\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-25 13:28:37'),
(19, 3, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"diseño páginas web\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 15:52:04'),
(20, 7, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"diseño páginas web\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 15:52:04'),
(21, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"diseño páginas web\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 15:52:04'),
(22, 3, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"diseño páginas web y tiendas en línea\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 15:52:12'),
(23, 7, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"diseño páginas web y tiendas en línea\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 15:52:12'),
(24, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"diseño páginas web y tiendas en línea\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 15:52:12'),
(25, 3, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"CATERING\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 17:20:57'),
(26, 7, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"CATERING\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 17:20:57'),
(27, 2, 'no_match', 'Búsqueda sin resultados', 'Un usuario buscó \"CATERING\" sin resultados - posible prospecto', '/buscador/no-match', NULL, 'search', 'notifications', 0, NULL, '2025-11-30 17:20:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `performance_goals`
--

CREATE TABLE `performance_goals` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `goal_type` enum('activities','contacts','affiliations','revenue') COLLATE utf8_unicode_ci NOT NULL,
  `target_value` decimal(12,2) NOT NULL,
  `current_value` decimal(12,2) DEFAULT '0.00',
  `period` enum('daily','weekly','monthly','quarterly','yearly') COLLATE utf8_unicode_ci DEFAULT 'monthly',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_achieved` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Performance goals for sales team';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requirement_categories`
--

CREATE TABLE `requirement_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `requirement_categories`
--

INSERT INTO `requirement_categories` (`id`, `name`, `code`, `description`, `is_active`, `created_at`) VALUES
(1, 'Nueva Membresía', 'membresia', 'Solicitudes para nuevas membresías', 1, '2025-11-25 15:32:28'),
(2, 'Renovación', 'renovacion', 'Renovaciones de membresías existentes', 1, '2025-11-25 15:32:28'),
(3, 'Servicio Adicional', 'servicio', 'Solicitudes de servicios adicionales', 1, '2025-11-25 15:32:28'),
(4, 'Evento', 'evento', 'Requerimientos relacionados con eventos', 1, '2025-11-25 15:32:28'),
(5, 'Capacitación', 'capacitacion', 'Solicitudes de cursos y capacitaciones', 1, '2025-11-25 15:32:28'),
(6, 'Marketing', 'marketing', 'Requerimientos de marketing y publicidad', 1, '2025-11-25 15:32:28'),
(7, 'Gestoría', 'gestoria', 'Servicios de gestoría y trámites', 1, '2025-11-25 15:32:28'),
(8, 'Otro', 'otro', 'Otros requerimientos', 1, '2025-11-25 15:32:28'),
(17, 'Comercial', 'comercial', 'Solicitud de servicios y/o Productos', 1, '2025-11-25 15:40:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `permissions` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `permissions`, `created_at`) VALUES
(1, 'superadmin', 'Super Administrador', 'Acceso completo al sistema', '{\"all\": true}', '2025-11-25 02:36:56'),
(2, 'direccion', 'Dirección', 'Director General o Gerente', '{\"users\": true, \"reports\": true, \"dashboard\": true}', '2025-11-25 02:36:56'),
(3, 'jefe_comercial', 'Jefe Comercial', 'Jefatura del área comercial', '{\"events\": true, \"reports\": true, \"dashboard\": true, \"prospects\": true, \"affiliates\": true}', '2025-11-25 02:36:56'),
(4, 'afiliador', 'Afiliador', 'Ejecutivo de ventas/afiliaciones', '{\"events\": true, \"dashboard\": true, \"prospects\": true, \"affiliates\": true}', '2025-11-25 02:36:56'),
(5, 'contabilidad', 'Contabilidad', 'Área contable y facturación', '{\"invoices\": true, \"dashboard\": true, \"affiliates\": true}', '2025-11-25 02:36:56'),
(6, 'consejero', 'Consejero', 'Consejero propietario o invitado', '{\"dashboard\": true, \"reports_view\": true}', '2025-11-25 02:36:56'),
(7, 'mesa_directiva', 'Mesa Directiva', 'Miembro de mesa directiva', '{\"dashboard\": true, \"reports_view\": true}', '2025-11-25 02:36:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `search_logs`
--

CREATE TABLE `search_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `search_term` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `searcher_type` enum('afiliado','publico','exafiliado') COLLATE utf8_unicode_ci DEFAULT 'publico',
  `searcher_contact_id` int(10) UNSIGNED DEFAULT NULL,
  `results_count` int(11) DEFAULT '0',
  `is_no_match` tinyint(1) DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `search_logs`
--

INSERT INTO `search_logs` (`id`, `search_term`, `searcher_type`, `searcher_contact_id`, `results_count`, `is_no_match`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'tecnología computadoras', 'publico', NULL, 1, 0, '192.168.1.100', NULL, '2025-11-25 02:36:56'),
(2, 'alimentos lácteos', 'afiliado', NULL, 1, 0, '192.168.1.101', NULL, '2025-11-25 02:36:56'),
(3, 'impresión 3d', 'publico', NULL, 0, 1, '192.168.1.102', NULL, '2025-11-25 02:36:56'),
(4, 'servicio de limpieza industrial', 'publico', NULL, 0, 1, '192.168.1.103', NULL, '2025-11-25 02:36:56'),
(5, 'asesoría legal', 'afiliado', NULL, 1, 0, '192.168.1.104', NULL, '2025-11-25 02:36:56'),
(6, 'fabricación moldes', 'publico', NULL, 0, 1, '192.168.1.105', NULL, '2025-11-25 02:36:56'),
(7, 'dan', 'afiliado', NULL, 0, 1, '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 11:54:04'),
(8, 'compur', 'afiliado', NULL, 0, 1, '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:28:20'),
(9, 'compu', 'afiliado', NULL, 0, 1, '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:28:22'),
(10, 'computadora', 'afiliado', NULL, 0, 1, '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:28:25'),
(11, 'impresión', 'afiliado', NULL, 0, 1, '187.145.46.170', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 13:28:37'),
(12, 'diseño páginas web', 'afiliado', NULL, 0, 1, '189.180.154.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-30 15:52:04'),
(13, 'diseño páginas web y tiendas en línea', 'afiliado', NULL, 0, 1, '189.180.154.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-30 15:52:12'),
(14, 'Marketing Digital', 'afiliado', NULL, 1, 0, '189.180.154.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-30 15:52:28'),
(15, 'Desarrollo de software y apps móviles', 'afiliado', NULL, 1, 0, '189.180.154.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-30 15:52:49'),
(16, 'Marketing Digital', 'afiliado', NULL, 1, 0, '189.180.154.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-30 17:20:23'),
(17, 'CATERING', 'afiliado', NULL, 0, 1, '189.180.154.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-30 17:20:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `category` enum('salon_rental','event_organization','course','conference','training','marketing_email','marketing_videowall','marketing_social','marketing_platform','gestoria','tramites','siem','otros') COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `services`
--

INSERT INTO `services` (`id`, `category`, `name`, `description`, `price`, `member_price`, `is_active`, `created_at`) VALUES
(1, 'salon_rental', 'Renta de Salón Principal', 'Salón para 100 personas con equipo audiovisual', 8000.00, 5000.00, 1, '2025-11-25 02:36:56'),
(2, 'salon_rental', 'Renta de Sala de Juntas', 'Sala ejecutiva para 15 personas', 2500.00, 1500.00, 1, '2025-11-25 02:36:56'),
(3, 'course', 'Curso de Liderazgo Empresarial', 'Programa de 20 horas de desarrollo gerencial', 5000.00, 3500.00, 1, '2025-11-25 02:36:56'),
(4, 'marketing_email', 'Campaña de Email Marketing', 'Diseño y envío a base de datos de afiliados', 3000.00, 2000.00, 1, '2025-11-25 02:36:56'),
(5, 'marketing_social', 'Publicación en Redes Sociales CCQ', 'Post patrocinado en redes de la Cámara', 1500.00, 1000.00, 1, '2025-11-25 02:36:56'),
(6, 'gestoria', 'Gestoría Licencia de Funcionamiento', 'Trámite completo de licencia municipal', 4000.00, 3000.00, 1, '2025-11-25 02:36:56'),
(7, 'event_organization', 'Organización de Evento Corporativo', 'Planeación y ejecución de evento empresarial', 15000.00, 12000.00, 1, '2025-11-25 02:36:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_contracts`
--

CREATE TABLE `service_contracts` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED NOT NULL,
  `service_type` enum('salon','marketing','curso','taller','expo','other') COLLATE utf8_unicode_ci DEFAULT 'other' COMMENT 'Quick service type classification',
  `affiliate_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Vendedor',
  `contract_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('active','completed','cancelled','pending') COLLATE utf8_unicode_ci DEFAULT 'active',
  `payment_status` enum('paid','pending','partial') COLLATE utf8_unicode_ci DEFAULT 'pending',
  `invoice_number` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_invitations`
--

CREATE TABLE `service_invitations` (
  `id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `affiliation_id` int(11) NOT NULL,
  `invitation_date` datetime NOT NULL,
  `invitation_type` enum('whatsapp','email','phone','in_person') NOT NULL DEFAULT 'whatsapp',
  `whatsapp_message` text,
  `email_subject` varchar(255) DEFAULT NULL,
  `email_message` text,
  `contact_whatsapp` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `sent_by_user_id` int(11) NOT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_invitation_details`
--

CREATE TABLE `service_invitation_details` (
  `id` int(11) NOT NULL,
  `invitation_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `upselling_invitations`
--

CREATE TABLE `upselling_invitations` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `current_membership_id` int(10) UNSIGNED NOT NULL COMMENT 'Current membership at time of invitation',
  `target_membership_id` int(10) UNSIGNED NOT NULL COMMENT 'Proposed upgrade membership',
  `invitation_date` datetime NOT NULL COMMENT 'Date and time of invitation sent',
  `invitation_type` enum('email','whatsapp','phone','in_person','payment_link') COLLATE utf8_unicode_ci DEFAULT 'payment_link',
  `payment_link_url` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Online payment link sent',
  `whatsapp_message` text COLLATE utf8_unicode_ci COMMENT 'WhatsApp message content sent',
  `email_subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Email subject if sent via email',
  `email_message` text COLLATE utf8_unicode_ci COMMENT 'Email message content sent',
  `contact_whatsapp` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'WhatsApp number message was sent to',
  `contact_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Email address message was sent to',
  `response_status` enum('pending','accepted','declined','no_response') COLLATE utf8_unicode_ci DEFAULT 'pending',
  `response_date` datetime DEFAULT NULL,
  `sent_by_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'User who sent the invitation',
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Tracks upselling invitations - minimum 2 per year per affiliate';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `reset_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `role_id`, `email`, `password`, `name`, `phone`, `whatsapp`, `address`, `avatar`, `is_active`, `last_login`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin@camaradecomercioqro.mx', '$2y$10$huLRfjnK74btfZwRuCADuOwo2n4G5ehLIliTRdE3o3J87kcGFtSuq', 'Administrador Sistema', '442 212 0035', '', NULL, '/uploads/avatars/avatar_1_1764072540.png', 1, '2025-12-02 15:57:05', NULL, NULL, '2025-11-25 02:36:56', '2025-12-02 15:57:05'),
(2, 4, 'ventas1@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María González Pérez', '442 555 0001', '4421234567', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(3, 4, 'ventas2@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos Hernández López', '442 555 0002', '4421234568', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(4, 3, 'jefe.comercial@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Roberto Martínez Silva', '442 555 0003', '4421234569', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(5, 2, 'direccion@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana Lucia Ramírez Torres', '442 555 0004', '4421234570', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(6, 5, 'contabilidad@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia Sánchez Moreno', '442 555 0005', '4421234571', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-25 02:36:56', '2025-11-25 02:36:56'),
(7, 4, 'danjohn007@hotmail.com', '$2y$10$5Vm7aRsnv88xtLd5OXtd7.6xmwLZjfuq6yWBb7Yb2OTQm0FEygGkK', 'Jonathan Rios', '1238211991', '1213432344', NULL, NULL, 1, '2025-11-29 05:43:26', '07a25a5c621c939227e8188d31fca989b730acdf9130738e648932ea7bee9211', '2025-11-25 07:15:28', '2025-11-25 11:59:25', '2025-11-29 05:43:26'),
(8, 1, 'andy@impactosdigitales.com', '$2y$10$rMvYF6HmpEa0vR.fEjAKAOBsTGIfgLGbDD.G3PzClU6HCpdd8cNWy', 'Andrés Raso', '4422956843', '4422198567', '', NULL, 1, '2025-12-02 01:17:37', NULL, NULL, '2025-11-25 12:05:09', '2025-12-02 01:17:37'),
(9, 4, 'jperez@camaradecomercioqro.mx', '$2y$10$uM345HNGDFAXAC99GAq8c.lUHFEJZyYuxjfqqyoaw/bMFjRV2iwaO', 'JPEREZ', '', '4422068737', '', NULL, 1, '2025-12-01 23:23:23', NULL, NULL, '2025-12-01 16:21:56', '2025-12-01 23:23:23'),
(10, 3, 'comercializacion@camaradecomercioqro.mx', '$2y$10$UDudQQApPaNB3dbYm7mPp.0xX9a92lGaGhPOqM5iDAjSEGTXIqNzC', 'BBARRON', '', '4426017321', '', NULL, 1, '2025-12-01 17:04:44', NULL, NULL, '2025-12-01 16:57:39', '2025-12-01 17:04:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_activity_log`
--

CREATE TABLE `user_activity_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Action type: access_commercial_agenda, create_activity, send_whatsapp, etc.',
  `related_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID of related entity (activity, contact, etc.)',
  `metadata` json DEFAULT NULL COMMENT 'Additional action metadata',
  `is_outside_hours` tinyint(1) DEFAULT '0' COMMENT 'Was action performed outside work hours (9am-6pm Mon-Fri)',
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Tracks user activity for performance metrics and off-hours recognition';

--
-- Volcado de datos para la tabla `user_activity_log`
--

INSERT INTO `user_activity_log` (`id`, `user_id`, `action`, `related_id`, `metadata`, `is_outside_hours`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-11-30 18:44:06'),
(2, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-11-30 18:53:54'),
(3, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-11-30 18:54:29'),
(4, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-11-30 19:02:32'),
(5, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-11-30 20:00:32'),
(6, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-12-01 12:26:15'),
(7, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-12-01 12:26:34'),
(8, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-12-01 12:27:20'),
(9, 1, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-12-01 13:43:18'),
(10, 1, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 16:11:06'),
(11, 9, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 16:26:11'),
(12, 10, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 16:58:20'),
(13, 10, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 16:58:31'),
(14, 10, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 16:59:25'),
(15, 10, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 17:00:22'),
(16, 9, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 17:00:53'),
(17, 9, 'create_activity', NULL, NULL, 0, NULL, NULL, '2025-12-01 17:01:52'),
(18, 9, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 17:01:52'),
(19, 10, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 17:02:14'),
(20, 10, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 17:04:47'),
(21, 1, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 17:33:53'),
(22, 1, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 18:18:14'),
(23, 8, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 20:23:06'),
(24, 8, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 21:01:54'),
(25, 8, 'create_activity', NULL, NULL, 0, NULL, NULL, '2025-12-01 21:04:00'),
(26, 8, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 21:04:00'),
(27, 8, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 21:04:11'),
(28, 8, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 23:08:59'),
(29, 8, 'complete_activity', 8, NULL, 0, NULL, NULL, '2025-12-01 23:09:21'),
(30, 8, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 23:09:21'),
(31, 9, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 23:57:12'),
(32, 9, 'access_commercial_agenda', NULL, NULL, 0, NULL, NULL, '2025-12-01 23:58:46'),
(33, 8, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-12-02 00:25:48'),
(34, 8, 'access_commercial_agenda', NULL, NULL, 1, NULL, NULL, '2025-12-02 00:26:09');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `idx_scheduled` (`scheduled_date`),
  ADD KEY `idx_user_status` (`user_id`,`status`);

--
-- Indices de la tabla `affiliations`
--
ALTER TABLE `affiliations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `membership_type_id` (`membership_type_id`),
  ADD KEY `affiliate_user_id` (`affiliate_user_id`),
  ADD KEY `idx_expiration` (`expiration_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indices de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`);

--
-- Indices de la tabla `benefit_usage`
--
ALTER TABLE `benefit_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `affiliation_id` (`affiliation_id`);

--
-- Indices de la tabla `commercial_requirements`
--
ALTER TABLE `commercial_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indices de la tabla `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indices de la tabla `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rfc` (`rfc`),
  ADD KEY `assigned_affiliate_id` (`assigned_affiliate_id`),
  ADD KEY `validated_by` (`validated_by`),
  ADD KEY `idx_contact_type` (`contact_type`),
  ADD KEY `idx_rfc` (`rfc`),
  ADD KEY `idx_whatsapp` (`whatsapp`),
  ADD KEY `idx_business_name` (`business_name`),
  ADD KEY `idx_contacts_whatsapp` (`whatsapp`),
  ADD KEY `idx_contacts_corporate_email` (`corporate_email`),
  ADD KEY `idx_contacts_phone` (`phone`);

--
-- Indices de la tabla `contact_branches`
--
ALTER TABLE `contact_branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`);

--
-- Indices de la tabla `council_members`
--
ALTER TABLE `council_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_member_type` (`member_type`);

--
-- Indices de la tabla `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_url` (`registration_url`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `event_categories`
--
ALTER TABLE `event_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_code` (`registration_code`),
  ADD UNIQUE KEY `ux_registration_code` (`registration_code`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `idx_guest_email` (`guest_email`),
  ADD KEY `idx_guest_rfc` (`guest_rfc`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_event_date` (`event_id`,`registration_date`),
  ADD KEY `idx_nombre_asistente` (`nombre_asistente`),
  ADD KEY `idx_parent_registration` (`parent_registration_id`),
  ADD KEY `idx_registration_code` (`registration_code`),
  ADD KEY `idx_attended` (`attended`);

--
-- Indices de la tabla `event_type_catalog`
--
ALTER TABLE `event_type_catalog`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `financial_categories`
--
ALTER TABLE `financial_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indices de la tabla `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_transaction_date` (`transaction_date`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indices de la tabla `membership_types`
--
ALTER TABLE `membership_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `motivational_messages`
--
ALTER TABLE `motivational_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `niza_classifications`
--
ALTER TABLE `niza_classifications`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`);

--
-- Indices de la tabla `performance_goals`
--
ALTER TABLE `performance_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_period` (`user_id`,`period`,`start_date`);

--
-- Indices de la tabla `requirement_categories`
--
ALTER TABLE `requirement_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `search_logs`
--
ALTER TABLE `search_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `searcher_contact_id` (`searcher_contact_id`),
  ADD KEY `idx_no_match` (`is_no_match`);

--
-- Indices de la tabla `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `service_contracts`
--
ALTER TABLE `service_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `affiliate_user_id` (`affiliate_user_id`);

--
-- Indices de la tabla `service_invitations`
--
ALTER TABLE `service_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `affiliation_id` (`affiliation_id`),
  ADD KEY `sent_by_user_id` (`sent_by_user_id`),
  ADD KEY `invitation_date` (`invitation_date`);

--
-- Indices de la tabla `service_invitation_details`
--
ALTER TABLE `service_invitation_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invitation_id` (`invitation_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indices de la tabla `upselling_invitations`
--
ALTER TABLE `upselling_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `current_membership_id` (`current_membership_id`),
  ADD KEY `target_membership_id` (`target_membership_id`),
  ADD KEY `sent_by_user_id` (`sent_by_user_id`),
  ADD KEY `idx_contact_year` (`contact_id`,`invitation_date`),
  ADD KEY `idx_response_status` (`response_status`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_reset_token_unique` (`reset_token`),
  ADD KEY `role_id` (`role_id`);

--
-- Indices de la tabla `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_outside_hours` (`user_id`,`is_outside_hours`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `affiliations`
--
ALTER TABLE `affiliations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `benefit_usage`
--
ALTER TABLE `benefit_usage`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `commercial_requirements`
--
ALTER TABLE `commercial_requirements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `config`
--
ALTER TABLE `config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=857;

--
-- AUTO_INCREMENT de la tabla `contact_branches`
--
ALTER TABLE `contact_branches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `council_members`
--
ALTER TABLE `council_members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `event_categories`
--
ALTER TABLE `event_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT de la tabla `event_type_catalog`
--
ALTER TABLE `event_type_catalog`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `financial_categories`
--
ALTER TABLE `financial_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `financial_transactions`
--
ALTER TABLE `financial_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `membership_types`
--
ALTER TABLE `membership_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `motivational_messages`
--
ALTER TABLE `motivational_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `niza_classifications`
--
ALTER TABLE `niza_classifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `performance_goals`
--
ALTER TABLE `performance_goals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `requirement_categories`
--
ALTER TABLE `requirement_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `search_logs`
--
ALTER TABLE `search_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `service_contracts`
--
ALTER TABLE `service_contracts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `service_invitations`
--
ALTER TABLE `service_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `service_invitation_details`
--
ALTER TABLE `service_invitation_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `upselling_invitations`
--
ALTER TABLE `upselling_invitations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `user_activity_log`
--
ALTER TABLE `user_activity_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `affiliations`
--
ALTER TABLE `affiliations`
  ADD CONSTRAINT `affiliations_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `affiliations_ibfk_2` FOREIGN KEY (`membership_type_id`) REFERENCES `membership_types` (`id`),
  ADD CONSTRAINT `affiliations_ibfk_3` FOREIGN KEY (`affiliate_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `benefit_usage`
--
ALTER TABLE `benefit_usage`
  ADD CONSTRAINT `benefit_usage_ibfk_1` FOREIGN KEY (`affiliation_id`) REFERENCES `affiliations` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `commercial_requirements`
--
ALTER TABLE `commercial_requirements`
  ADD CONSTRAINT `commercial_requirements_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `commercial_requirements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`assigned_affiliate_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contacts_ibfk_2` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `contact_branches`
--
ALTER TABLE `contact_branches`
  ADD CONSTRAINT `contact_branches_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `council_members`
--
ALTER TABLE `council_members`
  ADD CONSTRAINT `council_members_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `council_members_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_parent_registration` FOREIGN KEY (`parent_registration_id`) REFERENCES `event_registrations` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD CONSTRAINT `financial_transactions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `financial_categories` (`id`),
  ADD CONSTRAINT `financial_transactions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `performance_goals`
--
ALTER TABLE `performance_goals`
  ADD CONSTRAINT `performance_goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `search_logs`
--
ALTER TABLE `search_logs`
  ADD CONSTRAINT `search_logs_ibfk_1` FOREIGN KEY (`searcher_contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `service_contracts`
--
ALTER TABLE `service_contracts`
  ADD CONSTRAINT `service_contracts_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_contracts_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `service_contracts_ibfk_3` FOREIGN KEY (`affiliate_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `service_invitation_details`
--
ALTER TABLE `service_invitation_details`
  ADD CONSTRAINT `service_invitation_details_ibfk_1` FOREIGN KEY (`invitation_id`) REFERENCES `service_invitations` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `upselling_invitations`
--
ALTER TABLE `upselling_invitations`
  ADD CONSTRAINT `upselling_invitations_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upselling_invitations_ibfk_2` FOREIGN KEY (`current_membership_id`) REFERENCES `membership_types` (`id`),
  ADD CONSTRAINT `upselling_invitations_ibfk_3` FOREIGN KEY (`target_membership_id`) REFERENCES `membership_types` (`id`),
  ADD CONSTRAINT `upselling_invitations_ibfk_4` FOREIGN KEY (`sent_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD CONSTRAINT `user_activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
