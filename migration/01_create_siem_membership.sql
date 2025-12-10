-- =====================================================
-- PASO 1: Crear tipo de membresía SIEM
-- =====================================================
-- Descripción: Crea el tipo de membresía SIEM (Registro)
-- que se usa para pagos menores a $1,550
-- =====================================================

-- Insertar tipo de membresía SIEM
INSERT INTO `membership_types` 
(`id`, `name`, `code`, `price`, `duration_days`, `benefits`, `characteristics`, `upsell_order`, `is_active`, `created_at`, `paypal_product_id`) 
VALUES 
(19, 'Registro SIEM', 'SIEM', 990.00, 360, 
 '{"asesoria": false, "buscador": true, "networking": false, "capacitaciones": 0, "courtesy_tickets": 0, "descuento_eventos": 0}', 
 '["Registro básico en el sistema", "Acceso limitado al buscador", "Antesala a membresías completas"]', 
 0, 1, NOW(), NULL);

-- Verificar que se creó correctamente
SELECT * FROM membership_types WHERE id = 19;

-- =====================================================
-- Resultado esperado:
-- 1 row inserted
-- =====================================================
