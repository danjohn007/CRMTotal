-- =================================================================== -- 1. Update Role Descriptions and Permissions -- ===================================================================

UPDATE roles SET display_name = 'Vendedor', description = 'Ejecutivo de ventas con acceso a Prospectos, EDA y Agenda Comercial', permissions = JSON_OBJECT( 'dashboard', true, 'prospects', true, 'affiliates', true, 'eda', true, 'agenda_comercial', true, 'search', true, 'reports', true ) WHERE id = 4;

UPDATE roles SET description = 'Director General con acceso completo a gestión y configuración', permissions = JSON_OBJECT( 'dashboard', true, 'eda', true, 'events', true, 'memberships', true, 'search', true, 'reports', true, 'financial', true, 'users', true, 'import', true, 'config', true ) WHERE id = 2;

UPDATE roles SET description = 'Jefe del área comercial con acceso a gestión de ventas y usuarios', permissions = JSON_OBJECT( 'dashboard', true, 'prospects', true, 'eda', true, 'events', true, 'memberships', true, 'agenda_comercial', true, 'search', true, 'reports', true, 'financial', true, 'users', true, 'import', true ) WHERE id = 3;

UPDATE roles SET description = 'Área contable con acceso a reportes financieros', permissions = JSON_OBJECT( 'dashboard', true, 'reports', true, 'financial', true ) WHERE id = 5;

UPDATE roles SET description = 'Consejero propietario con acceso a métricas y buscador', permissions = JSON_OBJECT( 'dashboard', true, 'reports_view', true, 'search', true ) WHERE id = 6;

UPDATE roles SET description = 'Miembro de mesa directiva con acceso a métricas y buscador', permissions = JSON_OBJECT( 'dashboard', true, 'reports_view', true, 'search', true ) WHERE id = 7;

-- =================================================================== -- 2. Create Activity Types Table for Automated Activities -- ===================================================================

CREATE TABLE IF NOT EXISTS activity_types ( id int(10) UNSIGNED NOT NULL AUTO_INCREMENT, name varchar(100) COLLATE utf8_unicode_ci NOT NULL, icon varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL, description text COLLATE utf8_unicode_ci, is_active tinyint(1) NOT NULL DEFAULT 1, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO activity_types (name, icon, description) VALUES ('visita', '🏢', 'Visita a sus instalaciones'), ('whatsapp', '💬', 'Enviar mensaje por WhatsApp'), ('email', '📧', 'Enviar correo electrónico'), ('factura', '🧾', 'Enviar factura o comprobante de pago'), ('documentacion', '📎', 'Adjuntar documentación al EDA'), ('alta_afiliado', '✅', 'Dar de alta como afiliado'), ('cross_selling', '🎯', 'Oportunidad de Cross-Selling (automático)'), ('upselling', '📈', 'Oportunidad de Up-Selling (automático)');

-- =================================================================== -- 3. Create Automated Opportunities Table -- ===================================================================

CREATE TABLE IF NOT EXISTS automated_opportunities ( id int(10) UNSIGNED NOT NULL AUTO_INCREMENT, contact_id int(10) UNSIGNED NOT NULL, opportunity_type enum('cross_selling','upselling') COLLATE utf8_unicode_ci NOT NULL, scheduled_date date NOT NULL, status enum('pending','completed','cancelled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending', notes text COLLATE utf8_unicode_ci, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, completed_at timestamp NULL DEFAULT NULL, PRIMARY KEY (id), KEY idx_contact_id (contact_id), KEY idx_scheduled_date (scheduled_date), KEY idx_status (status), CONSTRAINT fk_auto_opp_contact FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- =================================================================== -- 4. Add Missing Fields to Contacts Table (MySQL 5.7 safe) -- =================================================================== -- Este bloque usa information_schema + PREPARE/EXECUTE para añadir solo las columnas que falten. -- Requiere poder consultar information_schema.COLUMNS y usar PREPARE/EXECUTE (normalmente permitido). -- Si tu host bloquea accesso a information_schema, ejecuta manualmente SHOW COLUMNS FROM contacts LIKE 'nombre' antes de ejecutar.

SET @schema = DATABASE();

-- niza_custom_category SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'niza_custom_category'; SET @sql = IF(@cnt = 0, 'ALTER TABLE contacts ADD COLUMN niza_custom_category VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL AFTER niza_classification', 'SELECT ''column niza_custom_category already exists'''); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- description SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'description'; SET @sql = IF(@cnt = 0, 'ALTER TABLE contacts ADD COLUMN description TEXT COLLATE utf8_unicode_ci DEFAULT NULL AFTER industry', 'SELECT ''column description already exists'''); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- whatsapp_sales SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'whatsapp_sales'; SET @sql = IF(@cnt = 0, 'ALTER TABLE contacts ADD COLUMN whatsapp_sales VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL AFTER whatsapp', 'SELECT ''column whatsapp_sales already exists'''); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- whatsapp_purchases SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'whatsapp_purchases'; SET @sql = IF(@cnt = 0, 'ALTER TABLE contacts ADD COLUMN whatsapp_purchases VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL AFTER whatsapp_sales', 'SELECT ''column whatsapp_purchases already exists'''); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- whatsapp_admin SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @schema AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'whatsapp_admin'; SET @sql = IF(@cnt = 0, 'ALTER TABLE contacts ADD COLUMN whatsapp_admin VARCHAR(15) COLLATE utf8_unicode_ci DEFAULT NULL AFTER whatsapp_purchases', 'SELECT ''column whatsapp_admin already exists'''); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =================================================================== -- 5. (OMITIDO) Stored procedure and daily event creation (privilegios elevados) -- =================================================================== -- En muchos hostings compartidos CREATE PROCEDURE y CREATE EVENT no están permitidos. -- Si tienes un administrador que pueda ejecutar con mayor privilegio, pídeles el archivo completo con PROCEDURE+EVENT. -- Abajo hay instrucciones para generación manual inicial sin PROCEDURE/EVENT.

-- =================================================================== -- 6. Manual initial generation of some automated opportunities (one-shot) -- =================================================================== -- Ejecuta estas consultas una vez para crear las oportunidades iniciales si lo deseas. -- No generan ciclos repetitivos (para eso se necesitaría EVENT o una tarea en cron que ejecute el script regularmente).

-- 1) First Cross-Selling (affiliation + 42 days) within [-180d, +90d] window INSERT INTO automated_opportunities (contact_id, opportunity_type, scheduled_date, notes) SELECT c.id, 'cross_selling', DATE_ADD(a.affiliation_date, INTERVAL 42 DAY), 'Oportunidad automática de Cross-Selling - Ciclo cada 6 semanas' FROM contacts c JOIN affiliations a ON c.id = a.contact_id WHERE c.contact_type = 'afiliado' AND a.status = 'active' AND a.affiliation_date IS NOT NULL AND DATE_ADD(a.affiliation_date, INTERVAL 42 DAY) BETWEEN DATE_SUB(CURDATE(), INTERVAL 180 DAY) AND DATE_ADD(CURDATE(), INTERVAL 90 DAY) AND NOT EXISTS ( SELECT 1 FROM automated_opportunities ao WHERE ao.contact_id = c.id AND ao.opportunity_type = 'cross_selling' AND ao.scheduled_date = DATE_ADD(a.affiliation_date, INTERVAL 42 DAY) );

-- 2) Upselling 1 (affiliation + 56 days) if date <= today INSERT INTO automated_opportunities (contact_id, opportunity_type, scheduled_date, notes) SELECT c.id, 'upselling', DATE_ADD(a.affiliation_date, INTERVAL 56 DAY), 'Primera oportunidad automática de Up-Selling (8 semanas)' FROM contacts c JOIN affiliations a ON c.id = a.contact_id WHERE c.contact_type = 'afiliado' AND a.status = 'active' AND a.affiliation_date IS NOT NULL AND DATE_ADD(a.affiliation_date, INTERVAL 56 DAY) <= CURDATE() AND NOT EXISTS ( SELECT 1 FROM automated_opportunities ao WHERE ao.contact_id = c.id AND ao.opportunity_type = 'upselling' AND ao.scheduled_date = DATE_ADD(a.affiliation_date, INTERVAL 56 DAY) );

-- 3) Upselling 2 (affiliation + 238 days) if date <= today INSERT INTO automated_opportunities (contact_id, opportunity_type, scheduled_date, notes) SELECT c.id, 'upselling', DATE_ADD(a.affiliation_date, INTERVAL 238 DAY), 'Segunda oportunidad automática de Up-Selling (34 semanas)' FROM contacts c JOIN affiliations a ON c.id = a.contact_id WHERE c.contact_type = 'afiliado' AND a.status = 'active' AND a.affiliation_date IS NOT NULL AND DATE_ADD(a.affiliation_date, INTERVAL 238 DAY) <= CURDATE() AND NOT EXISTS ( SELECT 1 FROM automated_opportunities ao WHERE ao.contact_id = c.id AND ao.opportunity_type = 'upselling' AND ao.scheduled_date = DATE_ADD(a.affiliation_date, INTERVAL 238 DAY) );

-- =================================================================== -- 7. Final summary (opcional) -- ===================================================================

SELECT 'Migration completed (safe/shared-hosting mode)' AS Status; SELECT COUNT(*) AS 'Total Automated Opportunities' FROM automated_opportunities;
