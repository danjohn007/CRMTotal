-- ============================================================================
-- Migration Script: Reorganización de Contacts, Events y Memberships
-- Fecha: 2025-12-04
-- Referencia: MEMBRESIAS.md
-- ============================================================================

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- PARTE 1: ACTUALIZACIÓN DE TABLA CONTACTS
-- ============================================================================

-- Agregar nuevas columnas a la tabla contacts
ALTER TABLE `contacts` 
  ADD COLUMN `registration_number` INT UNSIGNED NULL COMMENT 'No. REGISTRO (No. Mes)' AFTER `id`,
  ADD COLUMN `renewal_date` DATE NULL COMMENT 'FECHA RENOVACION' AFTER `registration_number`,
  ADD COLUMN `receipt_date` DATE NULL COMMENT 'FECHA RECIBO' AFTER `renewal_date`,
  ADD COLUMN `receipt_number` VARCHAR(50) NULL COMMENT 'No. DE RECIBO' AFTER `receipt_date`,
  ADD COLUMN `invoice_number` VARCHAR(50) NULL COMMENT 'No. DE FACTURA' AFTER `receipt_number`,
  ADD COLUMN `csf_file` VARCHAR(255) NULL COMMENT 'Constancia de Situación Fiscal (archivo adjunto CSF)' AFTER `invoice_number`,
  ADD COLUMN `sticker` VARCHAR(50) NULL COMMENT 'ENGOMADO' AFTER `csf_file`,
  ADD COLUMN `amount` DECIMAL(10,2) NULL DEFAULT 0.00 COMMENT 'IMPORTE' AFTER `sticker`,
  ADD COLUMN `payment_method` VARCHAR(50) NULL COMMENT 'METODO DE PAGO' AFTER `amount`,
  ADD COLUMN `reaffiliation` TINYINT(1) DEFAULT 0 COMMENT 'Reafiliación' AFTER `payment_method`,
  ADD COLUMN `is_new` TINYINT(1) DEFAULT 0 COMMENT 'Nueva' AFTER `reaffiliation`,
  ADD COLUMN `seller` INT UNSIGNED NULL COMMENT 'VENDEDOR (assigned_affiliate_id)' AFTER `is_new`,
  ADD COLUMN `affiliation_type` VARCHAR(50) NULL COMMENT 'TIPO DE AFILIACIÓN' AFTER `seller`,
  ADD COLUMN `membership_type_id` INT UNSIGNED NULL COMMENT 'TIPO DE MEMBRESÍA (FK a membership_types)' AFTER `affiliation_type`,
  ADD COLUMN `renewal_month` TINYINT UNSIGNED NULL COMMENT 'MES DE RENOVACIÓN (1-12)' AFTER `membership_type_id`,
  ADD COLUMN `trade_name` VARCHAR(255) NULL COMMENT 'Nombre comercial' AFTER `renewal_month`,
  ADD COLUMN `business_sector` VARCHAR(100) NULL COMMENT 'GIRO' AFTER `trade_name`,
  ADD COLUMN `description` TEXT NULL COMMENT 'DESCRIPCION' AFTER `business_sector`,
  ADD COLUMN `nice_classification` VARCHAR(10) NULL COMMENT 'CLASIFICACIÓN NIZA' AFTER `description`,
  ADD COLUMN `sales_contact` VARCHAR(255) NULL COMMENT 'Contacto de ventas' AFTER `nice_classification`,
  ADD COLUMN `purchase_contact` VARCHAR(255) NULL COMMENT 'Contacto de compras' AFTER `sales_contact`,
  ADD COLUMN `branch_count` INT UNSIGNED DEFAULT 0 COMMENT 'Numero de sucursales' AFTER `purchase_contact`,
  ADD COLUMN `branch_addresses` JSON NULL COMMENT 'Dirección sucursales (JSON array)' AFTER `branch_count`,
  ADD COLUMN `services_interest` JSON NULL COMMENT 'Servicios de interés (JSON array)' AFTER `branch_addresses`;

-- Actualizar la columna contact_type con los nuevos valores
ALTER TABLE `contacts` 
  MODIFY COLUMN `contact_type` ENUM(
    'prospecto',
    'afiliado',
    'exafiliado',
    'siem',
    'invitado',
    'funcionario_gobierno',
    'nuevo_usuario',
    'funcionario',
    'publico_general',
    'consejero_propietario',
    'consejero_invitado',
    'patrocinador',
    'mesa_directiva',
    'colaborador_empresa'
  ) DEFAULT 'nuevo_usuario' COMMENT 'Tipo de contacto';

-- Copiar datos de columnas existentes a las nuevas columnas equivalentes
UPDATE `contacts` SET 
  `trade_name` = COALESCE(`commercial_name`, `business_name`),
  `business_sector` = `industry`,
  `nice_classification` = `niza_classification`,
  `seller` = `assigned_affiliate_id`
WHERE `trade_name` IS NULL OR `business_sector` IS NULL;

-- Agregar índices para mejorar el rendimiento
ALTER TABLE `contacts` 
  ADD INDEX `idx_registration_number` (`registration_number`),
  ADD INDEX `idx_renewal_date` (`renewal_date`),
  ADD INDEX `idx_contact_type` (`contact_type`),
  ADD INDEX `idx_membership_type_id` (`membership_type_id`),
  ADD INDEX `idx_seller` (`seller`);

-- ============================================================================
-- PARTE 2: ACTUALIZACIÓN DE TABLA MEMBERSHIP_TYPES
-- ============================================================================

-- Verificar si las membresías necesarias existen, si no, crearlas
INSERT IGNORE INTO `membership_types` (`name`, `code`, `price`, `duration_days`, `upsell_order`, `is_active`, `benefits`, `characteristics`) VALUES
  ('Membresía Básica', 'BASICA', 3000.00, 360, 1, 1, 
   '{"asesoria": true, "buscador": true, "networking": true, "descuento_eventos": 10, "courtesy_tickets": 1}',
   '["Acceso al buscador de proveedores", "Eventos de networking", "1 cortesía en eventos pagados"]'),
  ('Membresía PYME', 'PYME', 5000.00, 360, 2, 1,
   '{"asesoria": true, "buscador": true, "networking": true, "capacitaciones": 2, "descuento_eventos": 20, "courtesy_tickets": 1}',
   '["Acceso al buscador de proveedores", "Eventos de networking", "2 capacitaciones incluidas", "Asesoría empresarial", "1 cortesía en eventos pagados"]'),
  ('Membresía Emprendedor', 'EMPRENDEDOR', 2500.00, 360, 2, 1,
   '{"asesoria": true, "buscador": true, "networking": true, "capacitaciones": 1, "descuento_eventos": 15, "courtesy_tickets": 1}',
   '["Acceso al buscador", "Eventos de networking", "1 capacitación incluida", "Asesoría básica", "1 cortesía en eventos pagados"]'),
  ('Membresía Visionario', 'VISIONARIO', 8000.00, 365, 3, 1,
   '{"asesoria": true, "buscador": true, "networking": true, "capacitaciones": 5, "descuento_eventos": 30, "courtesy_tickets": 1}',
   '["Acceso premium al buscador", "Networking VIP", "5 capacitaciones", "Asesoría avanzada", "1 cortesía en eventos pagados"]'),
  ('Membresía Premier', 'PREMIER', 10000.00, 360, 4, 1,
   '{"asesoria": true, "buscador": true, "marketing": true, "networking": true, "capacitaciones": "ilimitadas", "descuento_eventos": 30, "courtesy_tickets": 1}',
   '["Capacitaciones ilimitadas", "Marketing incluido", "Asesoría empresarial", "Eventos de networking VIP", "1 cortesía en eventos pagados"]'),
  ('Patrocinador Oficial', 'PATROCINADOR_OFICIAL', 50000.00, 360, 5, 1,
   '{"siem": true, "asesoria": true, "buscador": true, "marketing": true, "networking": true, "publicidad": true, "capacitaciones": "ilimitadas", "descuento_eventos": 40, "courtesy_tickets": 1}',
   '["Todos los beneficios Premier", "Publicidad en eventos", "Mención en comunicados", "Mesa preferente", "1 cortesía en eventos pagados"]'),
  ('Patrocinador AAA', 'PATROCINADOR_AAA', 199000.00, 360, 6, 1,
   '{"siem": true, "asesoria": true, "buscador": true, "marketing": true, "networking": true, "publicidad": true, "capacitaciones": "ilimitadas", "descuento_eventos": 50, "courtesy_tickets": 1}',
   '["Todos los beneficios Premier", "Publicidad destacada", "Mesa preferente en eventos", "Descuento máximo en servicios", "1 cortesía en eventos pagados"]'),
  ('Naming Rights', 'NAMING_RIGHTS', 500000.00, 360, 7, 1,
   '{"siem": true, "asesoria": true, "buscador": true, "marketing": true, "networking": true, "publicidad": true, "capacitaciones": "ilimitadas", "descuento_eventos": 100, "courtesy_tickets": 1, "naming_rights": true}',
   '["Todos los beneficios AAA", "Nombre del evento con marca", "Máxima visibilidad", "Beneficios exclusivos", "1 cortesía en eventos pagados"]')
ON DUPLICATE KEY UPDATE 
  `benefits` = VALUES(`benefits`),
  `characteristics` = VALUES(`characteristics`),
  `upsell_order` = VALUES(`upsell_order`);

-- ============================================================================
-- PARTE 3: ACTUALIZACIÓN DE TABLA EVENT_REGISTRATIONS
-- ============================================================================

-- Eliminar columnas obsoletas si existen
ALTER TABLE `event_registrations` 
  DROP COLUMN IF EXISTS `attendee_position`,
  DROP COLUMN IF EXISTS `categoría_asistente`;

-- Modificar el campo payment_status para incluir 'courtesy'
ALTER TABLE `event_registrations` 
  MODIFY COLUMN `payment_status` ENUM('paid', 'pending', 'free', 'courtesy') DEFAULT 'free' COMMENT 'Estado del pago';

-- Asegurar que registration_code sea único y no nulo para registros válidos
ALTER TABLE `event_registrations` 
  MODIFY COLUMN `registration_code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código único de registro (boleto)',
  MODIFY COLUMN `is_courtesy_ticket` TINYINT(1) DEFAULT 0 COMMENT 'Boleto de cortesía otorgado (máx 1 por membresía elegible)';

-- Agregar índice para verificación de cortesías por contacto y evento
ALTER TABLE `event_registrations` 
  ADD INDEX `idx_courtesy_check` (`contact_id`, `event_id`, `is_courtesy_ticket`),
  ADD INDEX `idx_registration_code` (`registration_code`),
  ADD INDEX `idx_event_contact` (`event_id`, `contact_id`),
  ADD INDEX `idx_payment_status` (`payment_status`);

-- ============================================================================
-- PARTE 4: LIMPIEZA DE DATOS Y VALIDACIONES
-- ============================================================================

-- Eliminar registros duplicados de event_registrations manteniendo el más reciente
DELETE t1 FROM event_registrations t1
INNER JOIN event_registrations t2 
WHERE t1.id < t2.id 
  AND t1.registration_code = t2.registration_code 
  AND t1.registration_code IS NOT NULL 
  AND t1.registration_code != '';

-- Actualizar person_type basado en la longitud del RFC
UPDATE `contacts` 
SET `person_type` = CASE 
  WHEN LENGTH(`rfc`) = 12 THEN 'moral'
  WHEN LENGTH(`rfc`) = 13 THEN 'fisica'
  ELSE `person_type`
END
WHERE `rfc` IS NOT NULL AND `rfc` != '';

-- Asegurar que los contactos tipo 'siem' estén correctamente marcados
UPDATE `contacts` 
SET `contact_type` = 'siem' 
WHERE `contact_type` = 'nuevo_usuario' 
  AND `notes` LIKE '%SIEM%' 
  AND `contact_type` != 'afiliado';

-- ============================================================================
-- PARTE 5: CREAR VISTA PARA ANÁLISIS DE MEMBRESÍAS Y CORTESÍAS
-- ============================================================================

CREATE OR REPLACE VIEW `vw_membership_courtesy_eligibility` AS
SELECT 
  c.id AS contact_id,
  c.business_name,
  c.commercial_name,
  c.contact_type,
  c.membership_type_id,
  mt.name AS membership_name,
  mt.code AS membership_code,
  CASE 
    WHEN mt.code IN ('BASICA', 'PYME', 'EMPRENDEDOR', 'VISIONARIO', 'PREMIER', 
                     'PATROCINADOR_OFICIAL', 'PATROCINADOR_AAA', 'NAMING_RIGHTS') THEN 1
    ELSE 0
  END AS courtesy_eligible,
  (SELECT COUNT(*) 
   FROM event_registrations er 
   WHERE er.contact_id = c.id 
     AND er.is_courtesy_ticket = 1
     AND er.payment_status = 'courtesy') AS courtesy_tickets_used
FROM contacts c
LEFT JOIN membership_types mt ON c.membership_type_id = mt.id
WHERE c.contact_type = 'afiliado';

-- ============================================================================
-- PARTE 6: CREAR PROCEDIMIENTO PARA VALIDAR CORTESÍAS
-- ============================================================================

DELIMITER //

CREATE PROCEDURE `sp_validate_courtesy_ticket`(
  IN p_contact_id INT UNSIGNED,
  IN p_event_id INT UNSIGNED,
  OUT p_is_valid BOOLEAN,
  OUT p_message VARCHAR(255)
)
BEGIN
  DECLARE v_membership_code VARCHAR(20);
  DECLARE v_courtesy_count INT;
  DECLARE v_is_paid_event TINYINT(1);
  
  -- Obtener el código de membresía del contacto
  SELECT mt.code INTO v_membership_code
  FROM contacts c
  JOIN membership_types mt ON c.membership_type_id = mt.id
  WHERE c.id = p_contact_id AND c.contact_type = 'afiliado';
  
  -- Verificar si el evento es pagado
  SELECT is_paid INTO v_is_paid_event
  FROM events
  WHERE id = p_event_id;
  
  -- Si el evento no es pagado, no se permite cortesía
  IF v_is_paid_event = 0 THEN
    SET p_is_valid = FALSE;
    SET p_message = 'Las cortesías solo aplican para eventos pagados';
  -- Si no tiene membresía elegible
  ELSEIF v_membership_code NOT IN ('BASICA', 'PYME', 'EMPRENDEDOR', 'VISIONARIO', 
                                    'PREMIER', 'PATROCINADOR_OFICIAL', 
                                    'PATROCINADOR_AAA', 'NAMING_RIGHTS') THEN
    SET p_is_valid = FALSE;
    SET p_message = 'La membresía actual no tiene derecho a cortesías';
  ELSE
    -- Contar cortesías ya utilizadas por el contacto
    SELECT COUNT(*) INTO v_courtesy_count
    FROM event_registrations
    WHERE contact_id = p_contact_id 
      AND is_courtesy_ticket = 1
      AND payment_status = 'courtesy';
    
    -- Validar límite de 1 cortesía
    IF v_courtesy_count >= 1 THEN
      SET p_is_valid = FALSE;
      SET p_message = 'Ya utilizó su cortesía disponible (máximo 1 por membresía)';
    ELSE
      SET p_is_valid = TRUE;
      SET p_message = 'Cortesía válida';
    END IF;
  END IF;
END //

DELIMITER ;

-- ============================================================================
-- PARTE 7: CREAR FUNCIÓN PARA CALCULAR PRÓXIMO PASO DE UPSELLING
-- ============================================================================

DELIMITER //

CREATE FUNCTION `fn_get_next_upselling_step`(p_contact_type VARCHAR(50), p_membership_code VARCHAR(20))
RETURNS VARCHAR(100)
DETERMINISTIC
BEGIN
  DECLARE v_next_step VARCHAR(100);
  
  -- Flujo de upselling según MEMBRESIAS.md
  IF p_contact_type = 'siem' THEN
    SET v_next_step = 'PROSPECTO';
  ELSEIF p_contact_type = 'prospecto' THEN
    SET v_next_step = 'AFILIADO - BÁSICA';
  ELSEIF p_membership_code = 'BASICA' THEN
    SET v_next_step = 'PYME o EMPRENDEDOR';
  ELSEIF p_membership_code IN ('PYME', 'EMPRENDEDOR') THEN
    SET v_next_step = 'VISIONARIO';
  ELSEIF p_membership_code = 'VISIONARIO' THEN
    SET v_next_step = 'PREMIER';
  ELSEIF p_membership_code = 'PREMIER' THEN
    SET v_next_step = 'PATROCINADOR OFICIAL';
  ELSEIF p_membership_code = 'PATROCINADOR_OFICIAL' THEN
    SET v_next_step = 'PATROCINADOR AAA';
  ELSEIF p_membership_code = 'PATROCINADOR_AAA' THEN
    SET v_next_step = 'NAMING RIGHTS';
  ELSEIF p_membership_code = 'NAMING_RIGHTS' THEN
    SET v_next_step = 'MÁXIMO NIVEL ALCANZADO';
  ELSE
    SET v_next_step = 'NO DEFINIDO';
  END IF;
  
  RETURN v_next_step;
END //

DELIMITER ;

-- ============================================================================
-- PARTE 8: TRIGGERS PARA VALIDACIÓN AUTOMÁTICA
-- ============================================================================

-- Trigger para validar RFC y person_type en INSERT
DELIMITER //

DROP TRIGGER IF EXISTS `trg_contacts_validate_rfc_insert` //

CREATE TRIGGER `trg_contacts_validate_rfc_insert`
BEFORE INSERT ON `contacts`
FOR EACH ROW
BEGIN
  IF NEW.rfc IS NOT NULL AND NEW.rfc != '' THEN
    IF LENGTH(NEW.rfc) = 12 THEN
      SET NEW.person_type = 'moral';
    ELSEIF LENGTH(NEW.rfc) = 13 THEN
      SET NEW.person_type = 'fisica';
    END IF;
  END IF;
END //

DELIMITER ;

-- Trigger para validar RFC y person_type en UPDATE
DELIMITER //

DROP TRIGGER IF EXISTS `trg_contacts_validate_rfc_update` //

CREATE TRIGGER `trg_contacts_validate_rfc_update`
BEFORE UPDATE ON `contacts`
FOR EACH ROW
BEGIN
  IF NEW.rfc IS NOT NULL AND NEW.rfc != '' THEN
    IF LENGTH(NEW.rfc) = 12 THEN
      SET NEW.person_type = 'moral';
    ELSEIF LENGTH(NEW.rfc) = 13 THEN
      SET NEW.person_type = 'fisica';
    END IF;
  END IF;
END //

DELIMITER ;

-- ============================================================================
-- PARTE 9: RESTAURAR VERIFICACIÓN DE CLAVES FORÁNEAS
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- FIN DE LA MIGRACIÓN
-- ============================================================================

-- Verificar la migración
SELECT 'Migración completada exitosamente' AS Status;

-- Mostrar estadísticas
SELECT 
  'Contactos totales' AS Metrica,
  COUNT(*) AS Valor
FROM contacts
UNION ALL
SELECT 
  'Eventos totales',
  COUNT(*)
FROM events
UNION ALL
SELECT 
  'Registros de eventos',
  COUNT(*)
FROM event_registrations
UNION ALL
SELECT 
  'Tipos de membresías',
  COUNT(*)
FROM membership_types
WHERE is_active = 1;
