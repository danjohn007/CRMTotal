-- ============================================================
-- ACTUALIZAR EMPRESA PARA BOLETOS DE ANDY
-- Agregar campo guest_company y actualizar registros
-- ============================================================

-- Paso 1: Agregar columna guest_company (si ya existe, dará error 1060 - ignorar)
ALTER TABLE event_registrations 
ADD COLUMN guest_company VARCHAR(255) NULL 
AFTER guest_rfc;

-- Paso 3: Actualizar los boletos de andy@impactosdigitales.com con el nombre de empresa
UPDATE event_registrations 
SET guest_company = 'RESPUESTAS OPTIMAS EN MAYOREO S.A. DE C.V.'
WHERE guest_email = 'andy@impactosdigitales.com'
  AND event_id = 6
  AND guest_rfc = 'ROM900628QV1';

-- Paso 4: Verificar la actualización
SELECT 
    id,
    registration_code,
    guest_name,
    guest_email,
    guest_rfc,
    guest_company,
    payment_status,
    is_courtesy_ticket
FROM event_registrations
WHERE guest_email = 'andy@impactosdigitales.com'
  AND event_id = 6
ORDER BY id DESC;

-- Paso 5: Eliminar duplicados (dejar solo 2 registros más recientes)
-- Identificar IDs a eliminar (los 2 más antiguos: 473, 474)
DELETE FROM event_registrations
WHERE id IN (473, 474);

-- Paso 6: Verificación final (debe mostrar solo 2 registros)
SELECT 
    id,
    registration_code,
    guest_name,
    guest_email,
    guest_rfc,
    guest_company,
    payment_status
FROM event_registrations
WHERE guest_email = 'andy@impactosdigitales.com'
  AND event_id = 6
ORDER BY id DESC;
