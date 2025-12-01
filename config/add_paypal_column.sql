-- Agregar columna paypal_product_id a membership_types
-- Ejecuta este comando en tu base de datos MySQL

ALTER TABLE membership_types ADD COLUMN paypal_product_id VARCHAR(100) NULL;
