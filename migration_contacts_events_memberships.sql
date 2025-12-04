-- ============================================================================
-- Migration Script: Reorganización de Contacts, Events y Memberships
-- Fecha: 2025-12-04 (regenerado para ejecutar sin errores en entornos con schema parcial)
-- Referencia: MEMBRESIAS.md
-- ============================================================================

-- NOTA: Este script intenta ser idempotente: crea columnas e índices sólo si no existen,
-- usando consultas a INFORMATION_SCHEMA y sentencias preparadas con cadenas correctamente escapadas.
-- Prueba primero en staging antes de aplicar en producción.

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- PARTE 1: ACTUALIZACIÓN DE TABLA CONTACTS (columnas + enum + copias)
-- ============================================================================

SET @tbl := 'contacts';
SET @db := DATABASE();

-- registration_number
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'registration_number';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `registration_number` INT UNSIGNED NULL COMMENT ''No. REGISTRO (No. Mes)'';',
  'SELECT ''column registration_number already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- renewal_date
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'renewal_date';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `renewal_date` DATE NULL COMMENT ''FECHA RENOVACION'';',
  'SELECT ''column renewal_date already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- receipt_date
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'receipt_date';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `receipt_date` DATE NULL COMMENT ''FECHA RECIBO'';',
  'SELECT ''column receipt_date already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- receipt_number
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'receipt_number';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `receipt_number` VARCHAR(50) NULL COMMENT ''No. DE RECIBO'';',
  'SELECT ''column receipt_number already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- invoice_number
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'invoice_number';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `invoice_number` VARCHAR(50) NULL COMMENT ''No. DE FACTURA'';',
  'SELECT ''column invoice_number already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- csf_file
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'csf_file';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `csf_file` VARCHAR(255) NULL COMMENT ''Constancia de Situación Fiscal (archivo adjunto CSF)'';',
  'SELECT ''column csf_file already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- sticker
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'sticker';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `sticker` VARCHAR(50) NULL COMMENT ''ENGOMADO'';',
  'SELECT ''column sticker already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- amount
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'amount';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `amount` DECIMAL(10,2) NULL DEFAULT 0.00 COMMENT ''IMPORTE'';',
  'SELECT ''column amount already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- payment_method
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'payment_method';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `payment_method` VARCHAR(50) NULL COMMENT ''METODO DE PAGO'';',
  'SELECT ''column payment_method already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- reaffiliation
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'reaffiliation';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `reaffiliation` TINYINT(1) DEFAULT 0 COMMENT ''Reafiliación'';',
  'SELECT ''column reaffiliation already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- is_new
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'is_new';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `is_new` TINYINT(1) DEFAULT 0 COMMENT ''Nueva'';',
  'SELECT ''column is_new already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- seller
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'seller';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `seller` INT UNSIGNED NULL COMMENT ''VENDEDOR (assigned_affiliate_id)'';',
  'SELECT ''column seller already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- affiliation_type
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'affiliation_type';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `affiliation_type` VARCHAR(50) NULL COMMENT ''TIPO DE AFILIACIÓN'';',
  'SELECT ''column affiliation_type already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- membership_type_id
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'membership_type_id';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `membership_type_id` INT UNSIGNED NULL COMMENT ''TIPO DE MEMBRESÍA (FK a membership_types)'';',
  'SELECT ''column membership_type_id already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- renewal_month
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'renewal_month';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `renewal_month` TINYINT UNSIGNED NULL COMMENT ''MES DE RENOVACIÓN (1-12)'';',
  'SELECT ''column renewal_month already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- trade_name
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'trade_name';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `trade_name` VARCHAR(255) NULL COMMENT ''Nombre comercial'';',
  'SELECT ''column trade_name already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- business_sector
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'business_sector';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `business_sector` VARCHAR(100) NULL COMMENT ''GIRO'';',
  'SELECT ''column business_sector already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- description
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'description';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `description` TEXT NULL COMMENT ''DESCRIPCION'';',
  'SELECT ''column description already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- nice_classification
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'nice_classification';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `nice_classification` VARCHAR(10) NULL COMMENT ''CLASIFICACIÓN NIZA'';',
  'SELECT ''column nice_classification already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- sales_contact
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'sales_contact';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `sales_contact` VARCHAR(255) NULL COMMENT ''Contacto de ventas'';',
  'SELECT ''column sales_contact already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- purchase_contact
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'purchase_contact';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `purchase_contact` VARCHAR(255) NULL COMMENT ''Contacto de compras'';',
  'SELECT ''column purchase_contact already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- branch_count
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'branch_count';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `branch_count` INT UNSIGNED DEFAULT 0 COMMENT ''Numero de sucursales'';',
  'SELECT ''column branch_count already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- branch_addresses
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'branch_addresses';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `branch_addresses` JSON NULL COMMENT ''Dirección sucursales (JSON array)'';',
  'SELECT ''column branch_addresses already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- services_interest
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'services_interest';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `contacts` ADD COLUMN `services_interest` JSON NULL COMMENT ''Servicios de interés (JSON array)'';',
  'SELECT ''column services_interest already exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- contact_type: ADD o MODIFY con ENUM (cadena escapada)
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'contact_type';

SET @enum_list := '''prospecto'',''afiliado'',''exafiliado'',''siem'',''invitado'',''funcionario_gobierno'',''nuevo_usuario'',''funcionario'',''publico_general'',''consejero_propietario'',''consejero_invitado'',''patrocinador'',''mesa_directiva'',''colaborador_empresa''';

SET @sql = IF(@cnt = 0,
  CONCAT('ALTER TABLE `contacts` ADD COLUMN `contact_type` ENUM(', @enum_list, ') DEFAULT ''nuevo_usuario'' COMMENT ''Tipo de contacto'';'),
  CONCAT('ALTER TABLE `contacts` MODIFY COLUMN `contact_type` ENUM(', @enum_list, ') DEFAULT ''nuevo_usuario'' COMMENT ''Tipo de contacto'';')
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Copiar datos de columnas existentes a las nuevas columnas equivalentes (solo si existen las columnas origen/destino)
SELECT COUNT(*) INTO @has_trade_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'trade_name';
SELECT COUNT(*) INTO @has_commercial_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'commercial_name';
SELECT COUNT(*) INTO @has_business_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'business_name';

SET @sql = IF(@has_trade_name = 1 AND (@has_commercial_name = 1 OR @has_business_name = 1),
  'UPDATE `contacts` SET `trade_name` = COALESCE(NULLIF(`commercial_name`, ''''), NULLIF(`business_name`, '''')) WHERE `trade_name` IS NULL;',
  'SELECT ''skip trade_name copy'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @has_business_sector FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'business_sector';
SELECT COUNT(*) INTO @has_industry FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'industry';

SET @sql = IF(@has_business_sector = 1 AND @has_industry = 1,
  'UPDATE `contacts` SET `business_sector` = `industry` WHERE `business_sector` IS NULL;',
  'SELECT ''skip business_sector copy'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @has_nice_classification FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'nice_classification';
SELECT COUNT(*) INTO @has_niza_classification FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'niza_classification';

SET @sql = IF(@has_nice_classification = 1 AND @has_niza_classification = 1,
  'UPDATE `contacts` SET `nice_classification` = `niza_classification` WHERE `nice_classification` IS NULL;',
  'SELECT ''skip nice_classification copy'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @has_seller FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'seller';
SELECT COUNT(*) INTO @has_assigned_affiliate FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'assigned_affiliate_id';

SET @sql = IF(@has_seller = 1 AND @has_assigned_affiliate = 1,
  'UPDATE `contacts` SET `seller` = `assigned_affiliate_id` WHERE `seller` IS NULL;',
  'SELECT ''skip seller copy'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Agregar índices para mejorar el rendimiento (solo si no existen)
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_registration_number';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `contacts` ADD INDEX `idx_registration_number` (`registration_number`);', 'SELECT ''idx_registration_number exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_renewal_date';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `contacts` ADD INDEX `idx_renewal_date` (`renewal_date`);', 'SELECT ''idx_renewal_date exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_contact_type';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `contacts` ADD INDEX `idx_contact_type` (`contact_type`);', 'SELECT ''idx_contact_type exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_membership_type_id';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `contacts` ADD INDEX `idx_membership_type_id` (`membership_type_id`);', 'SELECT ''idx_membership_type_id exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_seller';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `contacts` ADD INDEX `idx_seller` (`seller`);', 'SELECT ''idx_seller exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================================
-- PARTE 2: ACTUALIZACIÓN DE TABLA MEMBERSHIP_TYPES (inserciones de referencia)
-- ============================================================================

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
   '{"siem": true, "asesoria": true, "buscador": true, "marketing": true, "networking": true, "publicidad": true, "capacitaciones": "ilimitadas", "descuento_eventos": 100, "courtesy_tickets": 1}',
   '["Todos los beneficios AAA", "Nombre del evento con marca", "Máxima visibilidad", "Beneficios exclusivos", "1 cortesía en eventos pagados"]')
ON DUPLICATE KEY UPDATE 
  `benefits` = VALUES(`benefits`),
  `characteristics` = VALUES(`characteristics`),
  `upsell_order` = VALUES(`upsell_order`);

-- ============================================================================
-- PARTE 3: ACTUALIZACIÓN DE TABLA EVENT_REGISTRATIONS (columnas, unicidad y índices)
-- ============================================================================

SET @tbl := 'event_registrations';

-- Drop columna attendee_position si existe
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'attendee_position';
SET @sql = IF(@cnt = 1,
  'ALTER TABLE `event_registrations` DROP COLUMN `attendee_position`;',
  'SELECT ''attendee_position not present'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Drop columna categoría_asistente si existe
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'categoría_asistente';
SET @sql = IF(@cnt = 1,
  'ALTER TABLE `event_registrations` DROP COLUMN `categoría_asistente`;',
  'SELECT ''categoría_asistente not present'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Modificar/crear payment_status para incluir 'courtesy'
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'payment_status';
SET @desired_payment_status := 'ENUM(''paid'',''pending'',''free'',''courtesy'') DEFAULT ''free'' COMMENT ''Estado del pago''';
SET @sql = IF(@cnt = 0,
  CONCAT('ALTER TABLE `event_registrations` ADD COLUMN `payment_status` ', @desired_payment_status, ';'),
  CONCAT('ALTER TABLE `event_registrations` MODIFY COLUMN `payment_status` ', @desired_payment_status, ';')
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Asegurar registration_code: crear si no existe, normalizar valores y establecer UNIQUE
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'registration_code';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `registration_code` VARCHAR(255) NULL COMMENT ''Código único de registro (boleto)'';',
  'SELECT ''registration_code present'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Normalizar valores NULL/vacíos
UPDATE `event_registrations` 
SET `registration_code` = CONCAT('REG-', id, '-', UNIX_TIMESTAMP())
WHERE `registration_code` IS NULL OR `registration_code` = '';

-- Eliminar duplicados conservando el más reciente
DELETE t1 FROM event_registrations t1
INNER JOIN event_registrations t2 
  ON t1.registration_code = t2.registration_code
WHERE t1.id < t2.id
  AND t1.registration_code IS NOT NULL
  AND t1.registration_code != '';

-- Ajustar tamaño de columna y exigir NOT NULL
SET @sql = 'ALTER TABLE `event_registrations` MODIFY COLUMN `registration_code` VARCHAR(50) NOT NULL COMMENT ''Código único de registro (boleto)'';';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- is_courtesy_ticket: crear o modificar
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'is_courtesy_ticket';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `event_registrations` ADD COLUMN `is_courtesy_ticket` TINYINT(1) DEFAULT 0 COMMENT ''Boleto de cortesía otorgado (máx 1 por membresía elegible)'';',
  'ALTER TABLE `event_registrations` MODIFY COLUMN `is_courtesy_ticket` TINYINT(1) DEFAULT 0 COMMENT ''Boleto de cortesía otorgado (máx 1 por membresía elegible)'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Crear índices si no existen
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_courtesy_check';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `event_registrations` ADD INDEX `idx_courtesy_check` (`contact_id`, `event_id`, `is_courtesy_ticket`);', 'SELECT ''idx_courtesy_check exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_registration_code';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `event_registrations` ADD INDEX `idx_registration_code` (`registration_code`);', 'SELECT ''idx_registration_code exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_event_contact';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `event_registrations` ADD INDEX `idx_event_contact` (`event_id`, `contact_id`);', 'SELECT ''idx_event_contact exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'idx_payment_status';
SET @sql = IF(@cnt = 0, 'ALTER TABLE `event_registrations` ADD INDEX `idx_payment_status` (`payment_status`);', 'SELECT ''idx_payment_status exists'';');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- UNIQUE registration_code
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'uniq_registration_code';
SET @sql = IF(@cnt = 0,
  'ALTER TABLE `event_registrations` ADD CONSTRAINT `uniq_registration_code` UNIQUE (`registration_code`);',
  'SELECT ''uniq_registration_code exists'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================================
-- PARTE 4: LIMPIEZA DE DATOS Y VALIDACIONES GENERALES
-- ============================================================================

-- Actualizar person_type basado en la longitud del RFC (si las columnas existen)
SELECT COUNT(*) INTO @has_rfc FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'rfc';
SELECT COUNT(*) INTO @has_person_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'person_type';

SET @sql = IF(@has_rfc = 1 AND @has_person_type = 1,
  'UPDATE `contacts` SET `person_type` = CASE WHEN CHAR_LENGTH(`rfc`) = 12 THEN ''moral'' WHEN CHAR_LENGTH(`rfc`) = 13 THEN ''fisica'' ELSE `person_type` END WHERE `rfc` IS NOT NULL AND `rfc` != '''';',
  'SELECT ''skip person_type update'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Asegurar que los contactos tipo 'siem' estén correctamente marcados (si existen columnas)
SELECT COUNT(*) INTO @has_notes FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'notes';
SELECT COUNT(*) INTO @has_contact_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'contacts' AND COLUMN_NAME = 'contact_type';

SET @sql = IF(@has_notes = 1 AND @has_contact_type = 1,
  'UPDATE `contacts` SET `contact_type` = ''siem'' WHERE `contact_type` = ''nuevo_usuario'' AND `notes` LIKE ''%SIEM%'' AND `contact_type` != ''afiliado'';',
  'SELECT ''skip siem marking'';'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

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
DROP PROCEDURE IF EXISTS `sp_validate_courtesy_ticket` //
CREATE PROCEDURE `sp_validate_courtesy_ticket`(
  IN p_contact_id INT UNSIGNED,
  IN p_event_id INT UNSIGNED,
  OUT p_is_valid BOOLEAN,
  OUT p_message VARCHAR(255)
)
BEGIN
  DECLARE v_membership_code VARCHAR(20) DEFAULT NULL;
  DECLARE v_courtesy_count INT DEFAULT 0;
  DECLARE v_is_paid_event TINYINT(1) DEFAULT 0;
  
  SELECT mt.code INTO v_membership_code
  FROM contacts c
  JOIN membership_types mt ON c.membership_type_id = mt.id
  WHERE c.id = p_contact_id AND c.contact_type = 'afiliado'
  LIMIT 1;
  
  SELECT is_paid INTO v_is_paid_event
  FROM events
  WHERE id = p_event_id
  LIMIT 1;
  
  IF v_is_paid_event = 0 THEN
    SET p_is_valid = FALSE;
    SET p_message = 'Las cortesías solo aplican para eventos pagados';
  ELSEIF v_membership_code NOT IN ('BASICA', 'PYME', 'EMPRENDEDOR', 'VISIONARIO', 
                                    'PREMIER', 'PATROCINADOR_OFICIAL', 
                                    'PATROCINADOR_AAA', 'NAMING_RIGHTS') THEN
    SET p_is_valid = FALSE;
    SET p_message = 'La membresía actual no tiene derecho a cortesías';
  ELSE
    SELECT COUNT(*) INTO v_courtesy_count
    FROM event_registrations
    WHERE contact_id = p_contact_id 
      AND is_courtesy_ticket = 1
      AND payment_status = 'courtesy';
    
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
DROP FUNCTION IF EXISTS `fn_get_next_upselling_step` //
CREATE FUNCTION `fn_get_next_upselling_step`(p_contact_type VARCHAR(50), p_membership_code VARCHAR(20))
RETURNS VARCHAR(100)
DETERMINISTIC
BEGIN
  DECLARE v_next_step VARCHAR(100);
  
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
-- PARTE 8: TRIGGERS PARA VALIDACIÓN AUTOMÁTICA (RFC -> person_type)
-- ============================================================================

DELIMITER //
DROP TRIGGER IF EXISTS `trg_contacts_validate_rfc_insert` //
CREATE TRIGGER `trg_contacts_validate_rfc_insert`
BEFORE INSERT ON `contacts`
FOR EACH ROW
BEGIN
  IF NEW.rfc IS NOT NULL AND NEW.rfc != '' THEN
    IF CHAR_LENGTH(NEW.rfc) = 12 THEN
      SET NEW.person_type = 'moral';
    ELSEIF CHAR_LENGTH(NEW.rfc) = 13 THEN
      SET NEW.person_type = 'fisica';
    END IF;
  END IF;
END //
DROP TRIGGER IF EXISTS `trg_contacts_validate_rfc_update` //
CREATE TRIGGER `trg_contacts_validate_rfc_update`
BEFORE UPDATE ON `contacts`
FOR EACH ROW
BEGIN
  IF NEW.rfc IS NOT NULL AND NEW.rfc != '' THEN
    IF CHAR_LENGTH(NEW.rfc) = 12 THEN
      SET NEW.person_type = 'moral';
    ELSEIF CHAR_LENGTH(NEW.rfc) = 13 THEN
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

SELECT 'Migración preparada y ejecutada (comprobaciones idempotentes aplicadas) - revisa logs/outputs' AS Status;
