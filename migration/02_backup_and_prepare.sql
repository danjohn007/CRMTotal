-- =====================================================
-- PASO 2: Backup y preparación para migración
-- =====================================================
-- Descripción: Guarda la información de vinculaciones
-- entre contactos y boletos antes de borrar contacts
-- =====================================================

-- Crear tabla temporal con mapeo de boletos vinculados
DROP TABLE IF EXISTS temp_contact_registrations;

CREATE TABLE temp_contact_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rfc VARCHAR(13),
    corporate_email VARCHAR(255),
    business_name VARCHAR(255),
    phone VARCHAR(20),
    old_contact_id INT UNSIGNED NOT NULL,
    registration_id INT UNSIGNED NOT NULL,
    registration_code VARCHAR(50),
    attendee_name VARCHAR(255),
    event_id INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Guardar información de contactos que tienen boletos
INSERT INTO temp_contact_registrations 
(rfc, corporate_email, business_name, phone, old_contact_id, registration_id, registration_code, attendee_name, event_id)
SELECT 
    c.rfc,
    c.corporate_email,
    c.business_name,
    c.phone,
    c.id as old_contact_id,
    er.id as registration_id,
    er.registration_code,
    er.attendee_name,
    er.event_id
FROM contacts c
INNER JOIN event_registrations er ON c.id = er.contact_id
WHERE er.contact_id IS NOT NULL;

-- Mostrar estadísticas ANTES de borrar
SELECT 
    '===== ESTADÍSTICAS ANTES DE MIGRACIÓN =====' as info;

SELECT 
    'Total contactos actuales' as descripcion,
    COUNT(*) as cantidad
FROM contacts
UNION ALL
SELECT 
    'Total boletos con contacto vinculado' as descripcion,
    COUNT(*) as cantidad
FROM event_registrations
WHERE contact_id IS NOT NULL
UNION ALL
SELECT 
    'Boletos guardados en backup' as descripcion,
    COUNT(*) as cantidad
FROM temp_contact_registrations;

-- Mostrar muestra de datos guardados
SELECT 
    '===== MUESTRA DE BOLETOS GUARDADOS =====' as info;
    
SELECT * FROM temp_contact_registrations LIMIT 10;

-- =====================================================
-- PAUSA AQUÍ - VERIFICAR QUE TODO ESTÁ CORRECTO
-- =====================================================
-- Si todo se ve bien, ejecutar el siguiente paso:

-- TRUNCATE contacts (esto borrará todos los contactos)
-- Los boletos quedarán con contact_id = NULL
TRUNCATE TABLE contacts;

-- Verificar que contacts está vacío
SELECT COUNT(*) as contacts_despues_truncate FROM contacts;

-- Verificar que boletos quedaron con contact_id = NULL
SELECT 
    COUNT(*) as boletos_sin_contacto 
FROM event_registrations 
WHERE contact_id IS NULL;

SELECT 
    '===== LISTO PARA MIGRACIÓN =====' as info;
