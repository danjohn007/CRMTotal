-- =====================================================
-- PASO 4: Reconectar boletos con nuevos IDs
-- =====================================================
-- Descripción: Actualiza los event_registrations para
-- vincularlos con los nuevos IDs de contacts usando
-- RFC o email como criterio
-- =====================================================

SELECT 
    '===== INICIANDO RECONEXIÓN DE BOLETOS =====' as info;

-- Estadísticas ANTES de reconectar
SELECT 
    'Boletos en backup (a reconectar)' as descripcion,
    COUNT(*) as cantidad
FROM temp_contact_registrations
UNION ALL
SELECT 
    'Contactos migrados' as descripcion,
    COUNT(*) as cantidad
FROM contacts
UNION ALL
SELECT 
    'Boletos sin contacto (antes)' as descripcion,
    COUNT(*) as cantidad
FROM event_registrations
WHERE contact_id IS NULL;

-- =====================================================
-- Reconectar boletos por RFC
-- =====================================================
UPDATE event_registrations er
INNER JOIN temp_contact_registrations tcr ON er.id = tcr.registration_id
INNER JOIN contacts c ON c.rfc = tcr.rfc
SET er.contact_id = c.id
WHERE 
    er.contact_id IS NULL
    AND tcr.rfc IS NOT NULL 
    AND tcr.rfc != ''
    AND tcr.rfc != 'N';

SELECT 
    CONCAT('✓ Reconectados por RFC: ', ROW_COUNT()) as resultado;

-- =====================================================
-- Reconectar boletos por Email (para los que no tienen RFC)
-- =====================================================
UPDATE event_registrations er
INNER JOIN temp_contact_registrations tcr ON er.id = tcr.registration_id
INNER JOIN contacts c ON c.corporate_email = tcr.corporate_email
SET er.contact_id = c.id
WHERE 
    er.contact_id IS NULL
    AND tcr.corporate_email IS NOT NULL 
    AND tcr.corporate_email != ''
    AND tcr.corporate_email != 'N';

SELECT 
    CONCAT('✓ Reconectados por Email: ', ROW_COUNT()) as resultado;

-- =====================================================
-- Estadísticas DESPUÉS de reconectar
-- =====================================================
SELECT 
    '===== RESULTADOS DE RECONEXIÓN =====' as info;

SELECT 
    'Total boletos que tenían contacto' as descripcion,
    COUNT(*) as cantidad
FROM temp_contact_registrations
UNION ALL
SELECT 
    'Boletos reconectados exitosamente' as descripcion,
    COUNT(*) as cantidad
FROM event_registrations er
INNER JOIN temp_contact_registrations tcr ON er.id = tcr.registration_id
WHERE er.contact_id IS NOT NULL
UNION ALL
SELECT 
    'Boletos SIN reconectar (requieren revisión)' as descripcion,
    COUNT(*) as cantidad
FROM event_registrations er
INNER JOIN temp_contact_registrations tcr ON er.id = tcr.registration_id
WHERE er.contact_id IS NULL;

-- =====================================================
-- Mostrar boletos que NO se pudieron reconectar
-- =====================================================
SELECT 
    '===== BOLETOS QUE NO SE RECONECTARON =====' as info;
    
SELECT 
    tcr.old_contact_id,
    tcr.rfc,
    tcr.corporate_email,
    tcr.business_name,
    tcr.registration_code,
    tcr.attendee_name,
    'RFC no encontrado en nuevos contactos o cambió' as posible_razon
FROM temp_contact_registrations tcr
LEFT JOIN event_registrations er ON tcr.registration_id = er.id
WHERE er.contact_id IS NULL
LIMIT 50;

-- =====================================================
-- Verificación final
-- =====================================================
SELECT 
    '===== VERIFICACIÓN FINAL =====' as info;

SELECT 
    'Total boletos en el sistema' as descripcion,
    COUNT(*) as cantidad
FROM event_registrations
UNION ALL
SELECT 
    'Boletos CON contacto vinculado' as descripcion,
    COUNT(*) as cantidad
FROM event_registrations
WHERE contact_id IS NOT NULL
UNION ALL
SELECT 
    'Boletos SIN contacto vinculado' as descripcion,
    COUNT(*) as cantidad
FROM event_registrations
WHERE contact_id IS NULL;

-- =====================================================
-- SI TODO ESTÁ OK, ejecutar esto para limpiar:
-- =====================================================
-- DROP TABLE temp_contact_registrations;

-- =====================================================
-- SI HAY PROBLEMAS, puedes revisar los datos con:
-- =====================================================
-- SELECT * FROM temp_contact_registrations;
-- SELECT * FROM contacts WHERE rfc = 'ALGÚN_RFC';
-- =====================================================

SELECT 
    '===== RECONEXIÓN COMPLETADA =====' as info;
