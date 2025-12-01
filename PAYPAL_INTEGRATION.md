# Integración de PayPal para Membresías - Guía de Implementación

## Resumen de Cambios

### 1. Base de Datos
- **Ejecutar este script SQL primero:**
```sql
-- Agregar columna para PayPal Product ID
ALTER TABLE membership_types 
ADD COLUMN IF NOT EXISTS paypal_product_id VARCHAR(100) NULL AFTER is_active;

-- Guardar credenciales de PayPal
UPDATE config SET value = 'AWi0IaxZN-e9TQvSbc0FsZj-vHA9-38fyIBmpbQeELJgjNaRgSrGondGzDGQATilllQAlp0J2BJwJCYL' 
WHERE key_name = 'paypal_client_id';

UPDATE config SET value = 'ELLC6UBm2stHa0CdfvyukrZSnDtsjhxIZBxrqMZI6us4N3IOPVn54dow4RIJZ6dJBpxeMuOBA_KjdmTx' 
WHERE key_name = 'paypal_secret';

UPDATE config SET value = 'sandbox' 
WHERE key_name = 'paypal_mode';
```

### 2. Funcionalidad Implementada

#### Cuando se crea una nueva membresía:
1. Se crea el registro en la base de datos local
2. Automáticamente se crea un producto en el catálogo de PayPal
3. El PayPal Product ID se guarda en la tabla `membership_types`

#### En la vista de detalle de membresía:
1. Si la membresía tiene un PayPal Product ID, se muestra el botón de pago
2. Al hacer clic en el botón:
   - Se crea una orden de pago en PayPal
   - El usuario es redirigido a PayPal para completar el pago
   - Después del pago, se captura automáticamente
   - Se muestra un mensaje de éxito

### 3. Archivos Modificados

- `app/controllers/MembershipsController.php` - Integración completa con PayPal API
- `app/views/memberships/show.php` - Botón de PayPal y JavaScript
- `public/index.php` - Nuevas rutas para crear y capturar pagos
- `config/update_paypal_integration.sql` - Script de actualización de DB

### 4. Nuevos Endpoints API

- `POST /membresias/crear-pago` - Crea una orden de pago en PayPal
- `POST /membresias/capturar-pago` - Captura el pago después de aprobación

### 5. Modo de Operación

Actualmente configurado en modo **SANDBOX** (pruebas)

Para cambiar a producción:
```sql
UPDATE config SET value = 'live' WHERE key_name = 'paypal_mode';
```

### 6. Próximos Pasos Recomendados

1. **Ejecutar el script SQL** en tu base de datos
2. **Crear una nueva membresía** desde `/membresias/nuevo`
3. **Ver la membresía** y probar el botón de PayPal
4. **Usar credenciales de prueba de PayPal** para verificar el flujo completo

### 7. Credenciales de Prueba PayPal Sandbox

Para probar, puedes usar:
- Email: sb-buyer@personal.example.com (crear en PayPal Sandbox)
- Password: (generado en PayPal Sandbox)

### 8. Notas Importantes

- Las credenciales están guardadas de forma segura en la base de datos
- Cada membresía nueva generará automáticamente su producto en PayPal
- El botón solo aparece si hay credenciales configuradas y un product_id válido
- Los pagos en sandbox NO son reales

## Flujo Completo

```
Usuario crea membresía → 
Sistema guarda en DB → 
Sistema crea producto en PayPal → 
Usuario ve membresía con botón → 
Usuario hace clic en PayPal → 
Se crea orden → 
Usuario paga en PayPal → 
Sistema captura pago → 
Confirmación de éxito
```

## Manejo de Errores

- Si falla la creación del producto en PayPal, la membresía se crea igual pero sin botón
- Todos los errores se registran en el log del servidor
- Mensajes de error amigables para el usuario
