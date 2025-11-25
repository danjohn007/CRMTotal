# CRM Total - Actualización v1.5.0

## Mejoras de Registro de Eventos

Esta actualización implementa las siguientes mejoras al sistema de eventos:

### 1. Acceso Gratuito para Afiliados Vigentes

- Se agregó una casilla de verificación preseleccionada en el formulario de creación/edición de eventos
- Cuando está activa, los afiliados con membresía vigente reciben automáticamente 1 acceso gratis
- Se puede desactivar si el evento no incluye esta cortesía

**Campo agregado a la tabla `events`:**
- `free_for_affiliates` TINYINT(1) DEFAULT 1

### 2. Múltiples Registros del Mismo Usuario

- Ahora se permite que un mismo correo o RFC pueda comprar múltiples boletos para un evento
- Se eliminó la restricción que impedía registros duplicados
- Cada registro es único y recibe su propio código de confirmación

**Campo agregado a la tabla `event_registrations`:**
- `tickets` INT UNSIGNED DEFAULT 1 - Número de boletos en el registro
- `registration_code` VARCHAR(20) UNIQUE - Código único de registro

### 3. Correos de Confirmación

Se envían dos correos automáticamente:

#### A) Correo de Confirmación Inicial
- Se envía inmediatamente después del registro
- Incluye:
  - Detalles del evento (fecha, hora, ubicación)
  - Código de registro único
  - Enlace de pago (si el evento es de pago)
  - Número de boletos comprados

#### B) Correo con Código QR
- Se envía después de confirmar el pago (o inmediatamente si el evento es gratuito)
- Incluye:
  - Código QR para verificación de asistencia
  - Link para descargar el QR
  - Código de registro
  - Detalles del evento

**Campos agregados a la tabla `event_registrations`:**
- `qr_code` VARCHAR(255) - Nombre del archivo QR
- `qr_sent` TINYINT(1) DEFAULT 0 - Bandera de envío de QR
- `qr_sent_at` TIMESTAMP NULL - Fecha de envío del QR
- `confirmation_sent` TINYINT(1) DEFAULT 0 - Bandera de confirmación
- `confirmation_sent_at` TIMESTAMP NULL - Fecha de envío de confirmación

### 4. Vista Previa de Imagen en Formato Cuadrado

- Las imágenes de eventos ahora se muestran en formato cuadrado (aspect-ratio: 1:1)
- Mejor visualización en dispositivos móviles
- Se mantiene la proporción y se recorta con `object-cover`

### 5. Generación de Códigos QR

- Se genera automáticamente un código QR por cada registro
- El QR contiene un enlace único para verificar la asistencia
- Los QR se almacenan en `/public/uploads/qr/`
- Formato: `qr_REG-XXXXXXXX.png`

## Instalación

1. **Backup de la base de datos:**
   ```bash
   mysqldump -u usuario -p crm_ccq > backup_pre_v1.5.0.sql
   ```

2. **Ejecutar el script de actualización:**
   ```bash
   mysql -u usuario -p crm_ccq < config/update_v1.5.0.sql
   ```

3. **Verificar que los cambios se aplicaron correctamente:**
   ```sql
   DESCRIBE events;
   DESCRIBE event_registrations;
   SHOW INDEX FROM event_registrations;
   ```

4. **Crear directorio para códigos QR:**
   ```bash
   mkdir -p public/uploads/qr
   chmod 750 public/uploads/qr
   ```

5. **Configurar envío de correos (opcional pero recomendado):**
   - Actualizar configuración SMTP en la tabla `config`
   - O configurar PHPMailer para producción

## Pruebas Recomendadas

1. **Crear un nuevo evento:**
   - Verificar que la casilla "Acceso gratuito para afiliados vigentes" esté marcada por defecto
   - Cambiar el estado de la casilla y guardar

2. **Registrar un afiliado vigente:**
   - Usar el correo de un afiliado activo
   - Verificar que el pago sea "free" si el evento tiene `free_for_affiliates = 1`
   - Verificar que se reciba el correo con QR inmediatamente

3. **Registrar un usuario no afiliado:**
   - Usar un correo que no esté en la base de datos
   - Verificar que se reciba el correo de confirmación con enlace de pago
   - Completar el pago y verificar que se reciba el QR

4. **Múltiples registros:**
   - Registrarse dos veces con el mismo correo
   - Verificar que ambos registros se guarden correctamente
   - Cada uno debe tener su propio código de registro y QR

5. **Verificar formato de imagen:**
   - Acceder a la página pública de registro de un evento
   - Verificar que la imagen se muestre en formato cuadrado
   - Probar en dispositivo móvil

## Notas Técnicas

### Generación de QR
Actualmente se usa Google Charts API para generar códigos QR. Para producción, considerar:
- Migrar a biblioteca PHP como `endroid/qr-code`
- Configurar API propia de generación de QR
- Implementar caché de QR generados

### Envío de Correos
El sistema usa `mail()` de PHP. Para producción, se recomienda:
- Configurar PHPMailer con SMTP
- Usar servicio de terceros (SendGrid, AWS SES, etc.)
- Implementar cola de correos para envíos masivos

### Almacenamiento de QR
Los archivos QR se guardan en `public/uploads/qr/`. Considerar:
- Implementar limpieza automática de QR antiguos
- Mover a CDN o almacenamiento externo
- Implementar generación bajo demanda

## Rollback

Si necesitas revertir los cambios:

```sql
-- Ejecutar las instrucciones al final de update_v1.5.0.sql
ALTER TABLE `events` DROP COLUMN `free_for_affiliates`;
ALTER TABLE `event_registrations` DROP COLUMN `tickets`;
ALTER TABLE `event_registrations` DROP COLUMN `qr_code`;
ALTER TABLE `event_registrations` DROP COLUMN `qr_sent`;
ALTER TABLE `event_registrations` DROP COLUMN `qr_sent_at`;
ALTER TABLE `event_registrations` DROP COLUMN `confirmation_sent`;
ALTER TABLE `event_registrations` DROP COLUMN `confirmation_sent_at`;
ALTER TABLE `event_registrations` DROP COLUMN `registration_code`;
DROP INDEX `idx_guest_email` ON `event_registrations`;
DROP INDEX `idx_guest_rfc` ON `event_registrations`;
DROP INDEX `idx_registration_code` ON `event_registrations`;
DROP INDEX `idx_payment_status` ON `event_registrations`;
```

## Soporte

Para reportar problemas o dudas sobre esta actualización:
- Email: soporte@camaradecomercioqro.mx
- Teléfono: 442 212 0035
