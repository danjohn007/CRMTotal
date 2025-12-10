-- ============================================================
-- CREAR BOLETOS DE CORTESÍA ADICIONALES
-- Para: Williams Gómez Vázquez y Jesús Emmanuel Duarte Moreno
-- Empresa: RESPUESTAS OPTIMAS EN MAYOREO (RFC: ROM900628QV1)
-- Email destino: andy@impactosdigitales.com
-- ============================================================

-- Paso 1: Verificar el contact_id de RESPUESTAS OPTIMAS EN MAYOREO
SELECT 
    id,
    business_name,
    rfc,
    corporate_email,
    membership_type_id
FROM contacts
WHERE rfc = 'ROM900628QV1';

-- RESULTADO: RFC no encontrado en la base de datos
-- Se crearán los boletos como "Invitados Especiales" (contact_id = NULL)


-- Paso 2: Crear boleto para Williams Gómez Vázquez
INSERT INTO event_registrations (
    event_id,
    registration_code,
    guest_name,
    guest_email,
    guest_rfc,
    guest_phone,
    payment_status,
    is_courtesy_ticket,
    total_amount,
    tickets,
    contact_id
) VALUES (
    6,
    CONCAT('REG-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', UPPER(SUBSTRING(MD5(RAND()), 1, 6))),
    'Williams Gómez Vázquez',
    'andy@impactosdigitales.com',
    'ROM900628QV1',
    '',
    'courtesy',
    1,
    0.00,
    1,
    NULL  -- Invitado especial (empresa no registrada en CRM)
);

-- Paso 3: Crear boleto para Jesús Emmanuel Duarte Moreno
INSERT INTO event_registrations (
    event_id,
    registration_code,
    guest_name,
    guest_email,
    guest_rfc,
    guest_phone,
    payment_status,
    is_courtesy_ticket,
    total_amount,
    tickets,
    contact_id
) VALUES (
    6,
    CONCAT('REG-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', UPPER(SUBSTRING(MD5(RAND()), 1, 6))),
    'Jesús Emmanuel Duarte Moreno',
    'andy@impactosdigitales.com',
    'ROM900628QV1',
    '',
    'courtesy',
    1,
    0.00,
    1,
    NULL  -- Invitado especial (empresa no registrada en CRM)
);

-- Paso 4: Verificar los boletos creados
SELECT 
    er.id,
    er.registration_code,
    er.guest_name,
    er.guest_email,
    er.guest_rfc,
    er.payment_status,
    er.is_courtesy_ticket,
    er.total_amount,
    COALESCE(c.business_name, 'Sin empresa vinculada') as business_name
FROM event_registrations er
LEFT JOIN contacts c ON er.contact_id = c.id
WHERE er.guest_email = 'andy@impactosdigitales.com'
  AND er.event_id = 6
ORDER BY er.id DESC;

-- Paso 5: Resumen total de boletos de cortesía Evento 6
SELECT 
    COUNT(*) as total_courtesy_tickets,
    SUM(CASE WHEN guest_email = 'andy@impactosdigitales.com' THEN 1 ELSE 0 END) as andy_tickets
FROM event_registrations
WHERE event_id = 6
  AND payment_status = 'courtesy'
  AND is_courtesy_ticket = 1;
