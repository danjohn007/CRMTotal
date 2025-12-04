# Guía de Migración - Reorganización de Contacts, Events y Memberships

## ⚠️ IMPORTANTE - LEA ANTES DE EJECUTAR

Esta migración realiza cambios significativos en la base de datos. **Es obligatorio hacer un backup completo antes de proceder.**

---

## Pre-requisitos

1. **Acceso a la base de datos** con permisos de CREATE, ALTER, DROP
2. **Backup completo** de la base de datos actual
3. **PHP 7.4+** instalado
4. **Acceso al servidor web** para actualizar archivos

---

## Pasos de Migración

### 1. Backup de Base de Datos

```bash
# Crear directorio de backups si no existe
mkdir -p backups

# Ejecutar backup
mysqldump -u [usuario] -p enlaceca_total > backups/enlaceca_total_backup_$(date +%Y%m%d_%H%M%S).sql

# Verificar que el backup se creó correctamente
ls -lh backups/
```

### 2. Revisar el Script de Migración

Revisar el archivo `migration_contacts_events_memberships.sql` para entender los cambios que se aplicarán:

- **Nuevas columnas en `contacts`**: 25+ campos nuevos
- **Actualización de `contact_type`**: Nuevos valores (siem, invitado, funcionario_gobierno)
- **Nuevas membresías**: EMPRENDEDOR, PATROCINADOR OFICIAL, PATROCINADOR AAA, NAMING RIGHTS
- **Actualización de `event_registrations`**: Eliminación de campos obsoletos
- **Triggers, procedimientos y vistas**: Validación automática

### 3. Ejecutar Migración

#### Opción A: Desde línea de comandos
```bash
mysql -u [usuario] -p enlaceca_total < migration_contacts_events_memberships.sql
```

#### Opción B: Desde phpMyAdmin
1. Acceder a phpMyAdmin
2. Seleccionar la base de datos `enlaceca_total`
3. Ir a la pestaña "SQL"
4. Copiar y pegar el contenido de `migration_contacts_events_memberships.sql`
5. Hacer clic en "Continuar"

### 4. Verificar la Migración

Ejecutar las siguientes consultas para verificar:

```sql
-- Verificar nuevas columnas en contacts
SHOW COLUMNS FROM contacts WHERE Field IN ('registration_number', 'renewal_date', 'trade_name', 'business_sector');

-- Verificar nuevos tipos de membresía
SELECT id, name, code, price FROM membership_types WHERE code IN ('EMPRENDEDOR', 'PATROCINADOR_AAA', 'NAMING_RIGHTS');

-- Verificar que el procedimiento fue creado
SHOW PROCEDURE STATUS WHERE Name = 'sp_validate_courtesy_ticket';

-- Verificar que la vista fue creada
SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_enlaceca_total = 'vw_membership_courtesy_eligibility';

-- Verificar triggers
SHOW TRIGGERS WHERE `Trigger` LIKE 'trg_contacts%';

-- Ver estadísticas
SELECT * FROM vw_membership_courtesy_eligibility LIMIT 10;
```

### 5. Actualizar Archivos de Código

Los siguientes archivos ya están actualizados en el repositorio:

- ✅ `app/models/Contact.php`
- ✅ `app/models/Event.php`
- ✅ `app/controllers/EventsController.php`
- ✅ `app/views/events/show.php`
- ✅ `app/views/events/send_whatsapp.php` (nuevo)
- ✅ `app/views/events/send_email.php` (nuevo)
- ✅ `public/index.php` (rutas actualizadas)

**Acción requerida**: Hacer `git pull` en el servidor para obtener los cambios más recientes.

```bash
cd /ruta/del/proyecto
git pull origin main  # o el branch correspondiente
```

### 6. Verificar Funcionalidad

1. **Acceder al sistema**: http://tu-dominio.com
2. **Probar exportación**: Ir a cualquier evento con registros → Clic en "EXPORTAR"
3. **Probar WhatsApp**: Ir a evento → Clic en "ENVIAR WhatsApp"
4. **Probar Email**: Ir a evento → Clic en "EMAIL con QR"

---

## Rollback (En caso de problemas)

Si algo sale mal, restaurar desde el backup:

```bash
# Restaurar base de datos
mysql -u [usuario] -p enlaceca_total < backups/enlaceca_total_backup_[fecha].sql

# Revertir cambios de código (si es necesario)
git checkout [commit-anterior]
```

---

## Cambios Específicos por Tabla

### Tabla `contacts`

#### Nuevas columnas:
- `registration_number` - Número de registro mensual
- `renewal_date`, `receipt_date` - Fechas de renovación y recibo
- `receipt_number`, `invoice_number` - Números de comprobantes
- `csf_file` - Archivo de Constancia de Situación Fiscal
- `sticker` - Número de engomado
- `amount`, `payment_method` - Información de pago
- `reaffiliation`, `is_new` - Flags de estado
- `seller` - Vendedor asignado
- `affiliation_type` - Tipo de afiliación
- `membership_type_id` - FK a membership_types
- `renewal_month` - Mes de renovación (1-12)
- `trade_name` - Nombre comercial
- `business_sector` - Sector empresarial (GIRO)
- `description` - Descripción del negocio
- `nice_classification` - Clasificación NIZA
- `sales_contact`, `purchase_contact` - Contactos de ventas/compras
- `branch_count` - Número de sucursales
- `branch_addresses` - Direcciones de sucursales (JSON)
- `services_interest` - Servicios de interés (JSON)

#### Enum actualizado:
```sql
contact_type ENUM(
    'prospecto',
    'afiliado', 
    'exafiliado',
    'siem',              -- NUEVO
    'invitado',          -- NUEVO
    'funcionario_gobierno', -- NUEVO
    ...
)
```

### Tabla `membership_types`

#### Nuevas membresías:
- **EMPRENDEDOR** ($2,500) - Para startups
- **PATROCINADOR OFICIAL** ($50,000) - Patrocinio nivel medio
- **PATROCINADOR AAA** ($199,000) - Patrocinio premium
- **NAMING RIGHTS** ($500,000) - Patrocinio elite

Todas incluyen beneficio de 1 cortesía en eventos pagados.

### Tabla `event_registrations`

#### Columnas eliminadas:
- `attendee_position` ❌
- `categoría_asistente` ❌

#### Enum actualizado:
```sql
payment_status ENUM('paid', 'pending', 'free', 'courtesy')  -- 'courtesy' es NUEVO
```

---

## Nuevas Funcionalidades

### 1. Exportación de Emails (CSV)

**Ruta**: `/eventos/{id}/export-emails`

Exporta un archivo CSV con:
- Código de registro
- Nombre del asistente
- Email
- WhatsApp
- Tipo de registro
- Estado de pago
- Cortesía (Sí/No)
- Asistió (Sí/No)
- Razón social
- RFC
- Tipo de membresía

### 2. Mensajería Masiva por WhatsApp

**Ruta**: `/eventos/{id}/send-whatsapp`

Características:
- Mensaje personalizado con variables {nombre} y {codigo}
- Genera enlaces individuales para cada contacto
- Formato: `https://wa.me/52[número]?text=[mensaje]`
- Interface amigable para abrir WhatsApp Web

### 3. Email Masivo con QR

**Ruta**: `/eventos/{id}/send-email`

Características:
- Asunto y mensaje personalizables
- Variables: {nombre}, {codigo}, {evento}
- Incluye código QR automáticamente
- Envío secuencial a todos los registrados

### 4. Validación de Cortesías

**Procedimiento**: `sp_validate_courtesy_ticket`

Validaciones:
- ✓ Solo eventos pagados
- ✓ Solo afiliados vigentes
- ✓ Solo membresías elegibles
- ✓ Máximo 1 cortesía por afiliado (global)

Membresías elegibles:
- BÁSICA
- PYME
- EMPRENDEDOR
- VISIONARIO
- PREMIER
- PATROCINADOR OFICIAL
- PATROCINADOR AAA
- NAMING RIGHTS

### 5. Flujo de Upselling

**Función**: `fn_get_next_upselling_step`

Flujo completo:
```
SIEM → PROSPECTO → BÁSICA → PYME/EMPRENDEDOR → VISIONARIO → 
PREMIER → PATROCINADOR OFICIAL → PATROCINADOR AAA → NAMING RIGHTS
```

---

## Solución de Problemas

### Error: "Table already has column"
Significa que algunas columnas ya existen. Revisar el script y comentar las líneas correspondientes.

### Error: "Unknown column in field list"
Los archivos de código no están sincronizados. Hacer `git pull` y verificar versiones.

### Error: "Duplicate entry for key"
Puede haber datos duplicados. Ejecutar:
```sql
SELECT registration_code, COUNT(*) 
FROM event_registrations 
WHERE registration_code IS NOT NULL 
GROUP BY registration_code 
HAVING COUNT(*) > 1;
```

### Error: SMTP al enviar emails
Verificar configuración en tabla `config`:
- `smtp_from_email`
- `smtp_from_name`

**Nota**: La implementación actual usa la función `mail()` de PHP. Para producción, se recomienda:
1. Instalar y configurar PHPMailer o SwiftMailer
2. Actualizar el método `sendEventEmail()` en EventsController
3. Configurar SMTP en el servidor o usar un servicio como SendGrid/Mailgun

---

## Contacto y Soporte

Para problemas o dudas durante la migración:
- Revisar logs del sistema: `tail -f /var/log/mysql/error.log`
- Revisar logs de PHP: `tail -f /var/log/apache2/error.log`
- Contactar al equipo de desarrollo

**Última actualización**: 2025-12-04
