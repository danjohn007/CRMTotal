# Implementación: Reorganización de Contacts, Events y Memberships

## Estado de Implementación

### ✅ Completado

#### 1. Base de Datos (SQL Migration)
- **Archivo**: `migration_contacts_events_memberships.sql`
- **Descripción**: Script SQL completo para migración de base de datos
- **Características**:
  - Nuevas columnas en tabla `contacts` (registration_number, renewal_date, receipt_date, receipt_number, invoice_number, csf_file, sticker, amount, payment_method, reaffiliation, is_new, seller, affiliation_type, membership_type_id, renewal_month, trade_name, business_sector, description, nice_classification, sales_contact, purchase_contact, branch_count, branch_addresses, services_interest)
  - Actualización del enum `contact_type` (prospecto, afiliado, siem, invitado, funcionario_gobierno, etc.)
  - Validación automática de `person_type` basada en longitud de RFC (12=moral, 13=fisica)
  - Nuevos tipos de membresía (EMPRENDEDOR, PATROCINADOR OFICIAL, PATROCINADOR AAA, NAMING RIGHTS)
  - Eliminación de columnas obsoletas en `event_registrations` (attendee_position, categoría_asistente)
  - Actualización de `payment_status` con opción 'courtesy'
  - Índices para mejorar performance
  - Triggers para validación automática de RFC
  - Procedimiento almacenado `sp_validate_courtesy_ticket`
  - Función `fn_get_next_upselling_step`
  - Vista `vw_membership_courtesy_eligibility`

#### 2. Modelos PHP

##### Contact.php
- **Campos agregados**: Todos los nuevos campos en el array `$fillable`
- **Métodos nuevos**:
  - `validatePersonTypeByRFC()` - Valida y retorna tipo de persona basado en RFC
  - `getNextUpsellingStep()` - Calcula el siguiente paso en el flujo de upselling
  - `getSiemContacts()` - Obtiene contactos tipo SIEM
  - `getInvitados()` - Obtiene invitados (sin RFC)
  - `getFuncionariosGobierno()` - Obtiene funcionarios de gobierno
  - `checkCourtesyEligibility()` - Verifica elegibilidad para cortesías

##### Event.php
- **Métodos de exportación**:
  - `getEmailsForExport()` - Obtiene datos de registros para exportar a CSV
  - `getWhatsAppForMessaging()` - Obtiene números de WhatsApp para mensajería masiva
- **Métodos de validación**:
  - `validateCourtesyTicket()` - Valida si un contacto puede usar cortesía
  - `findDuplicateRegistrations()` - Encuentra códigos de registro duplicados
  - `getEventTypeStatistics()` - Obtiene estadísticas de eventos gratuitos vs pagados

#### 3. Controladores

##### EventsController.php
- **Métodos nuevos**:
  - `exportEmails()` - Exporta emails de registrados a CSV
  - `sendWhatsApp()` - Genera enlaces de WhatsApp para mensajería masiva
  - `sendEmailWithQR()` - Envía emails con código QR a todos los asistentes
  - `sendEventEmail()` - Helper privado para envío de emails
  - `sanitizeFilename()` - Helper para sanitizar nombres de archivo

#### 4. Vistas

##### Nuevas vistas creadas:
- `app/views/events/send_whatsapp.php` - Interfaz para envío masivo de WhatsApp
  - Permite personalizar mensaje con variables {nombre} y {codigo}
  - Genera enlaces individuales para cada contacto
  - Interfaz amigable para abrir WhatsApp Web

- `app/views/events/send_email.php` - Interfaz para envío masivo de emails
  - Personalización de asunto y cuerpo del mensaje
  - Variables disponibles: {nombre}, {codigo}, {evento}
  - Incluye código QR automáticamente

##### Vistas actualizadas:
- `app/views/events/show.php` - Agregados 3 botones:
  1. **EXPORTAR** - Descarga CSV con emails y datos de registrados
  2. **ENVIAR WhatsApp** - Mensajería masiva vía WhatsApp
  3. **EMAIL con QR** - Envío masivo de emails con código QR

#### 5. Rutas
- `public/index.php` - Agregadas rutas:
  - `/eventos/{id}/export-emails` → `EventsController::exportEmails()`
  - `/eventos/{id}/send-whatsapp` → `EventsController::sendWhatsApp()`
  - `/eventos/{id}/send-email` → `EventsController::sendEmailWithQR()`

---

### 🔄 Pendiente

#### 1. Actualización de Formularios de Afiliados
- [ ] Actualizar `app/views/affiliates/create.php` con nuevos campos:
  - registration_number
  - renewal_date, receipt_date
  - receipt_number, invoice_number
  - csf_file (upload de archivo)
  - sticker
  - amount, payment_method
  - reaffiliation, is_new
  - seller
  - affiliation_type
  - membership_type_id (ya existe, verificar)
  - renewal_month
  - trade_name
  - business_sector, description
  - nice_classification
  - sales_contact, purchase_contact
  - branch_count, branch_addresses
  - services_interest

- [ ] Actualizar `app/views/affiliates/edit.php` con los mismos campos

- [ ] Agregar validación de RFC en JavaScript:
  ```javascript
  document.getElementById('rfc').addEventListener('input', function() {
      const rfc = this.value;
      const personTypeField = document.getElementById('person_type');
      
      if (rfc.length === 12) {
          personTypeField.value = 'moral';
      } else if (rfc.length === 13) {
          personTypeField.value = 'fisica';
      }
  });
  ```

#### 2. Actualización del Controlador de Afiliados
- [ ] Modificar `AffiliatesController::create()` para:
  - Procesar nuevos campos del formulario
  - Validar RFC y establecer person_type automáticamente
  - Guardar archivo CSF si se proporciona
  - Procesar JSON fields (branch_addresses, services_interest)

- [ ] Modificar `AffiliatesController::edit()` con la misma lógica

#### 3. Testing
- [ ] Probar validación de RFC y person_type
- [ ] Probar creación de contacto tipo SIEM
- [ ] Probar flujo de upselling: SIEM → Prospecto → Afiliado
- [ ] Probar límite de cortesías (máximo 1 por membresía elegible)
- [ ] Probar exportación de emails a CSV
- [ ] Probar generación de enlaces de WhatsApp
- [ ] Probar envío masivo de emails con QR
- [ ] Verificar no duplicados en registration_code
- [ ] Probar estadísticas de eventos gratuitos vs pagados

#### 4. Migración de Datos
- [ ] Ejecutar script SQL en base de datos de producción
- [ ] Verificar integridad de datos después de migración
- [ ] Actualizar datos existentes según sea necesario:
  - Sincronizar trade_name con commercial_name
  - Sincronizar business_sector con industry
  - Sincronizar nice_classification con niza_classification
  - Establecer seller basado en assigned_affiliate_id

#### 5. Documentación
- [ ] Actualizar README.md con nuevas características
- [ ] Documentar flujo de upselling en detalle
- [ ] Crear manual de usuario para nuevas funcionalidades
- [ ] Documentar configuración de SMTP para envío de emails

---

## Instrucciones de Uso

### Para ejecutar la migración:

1. **Backup de la base de datos**:
   ```bash
   mysqldump -u usuario -p enlaceca_total > backup_$(date +%Y%m%d).sql
   ```

2. **Ejecutar script de migración**:
   ```bash
   mysql -u usuario -p enlaceca_total < migration_contacts_events_memberships.sql
   ```

3. **Verificar ejecución**:
   ```sql
   SHOW COLUMNS FROM contacts;
   SELECT * FROM membership_types WHERE code IN ('EMPRENDEDOR', 'PATROCINADOR_AAA', 'NAMING_RIGHTS');
   SELECT * FROM vw_membership_courtesy_eligibility LIMIT 10;
   ```

### Para usar las nuevas funcionalidades:

#### Exportar Emails de Evento
1. Ir a la página del evento: `/eventos/{id}`
2. En la sección "Comunicación Masiva", hacer clic en **EXPORTAR**
3. Se descargará un archivo CSV con todos los registros

#### Enviar WhatsApp Masivo
1. Ir a la página del evento: `/eventos/{id}`
2. Hacer clic en **ENVIAR WhatsApp**
3. Personalizar el mensaje usando variables {nombre} y {codigo}
4. Generar enlaces
5. Hacer clic en cada enlace para abrir WhatsApp con el mensaje pre-cargado

#### Enviar Email con QR
1. Ir a la página del evento: `/eventos/{id}`
2. Hacer clic en **EMAIL con QR**
3. Personalizar asunto y mensaje usando variables {nombre}, {codigo}, {evento}
4. Hacer clic en "Enviar Correos"
5. El sistema enviará emails a todos los registrados con su código QR

### Validación de Cortesías

El sistema valida automáticamente:
- Solo eventos pagados pueden tener cortesías
- Solo afiliados pueden recibir cortesías
- Solo membresías elegibles (BÁSICA, PYME, EMPRENDEDOR, VISIONARIO, PREMIER, PATROCINADOR OFICIAL, PATROCINADOR AAA, NAMING RIGHTS)
- Máximo 1 cortesía por afiliado (global, no por evento)

---

## Flujo de Upselling

```
SIEM → PROSPECTO → BÁSICA → PYME/EMPRENDEDOR → VISIONARIO → PREMIER → PATROCINADOR OFICIAL → PATROCINADOR AAA → NAMING RIGHTS
```

El sistema puede calcular automáticamente el siguiente paso usando la función `fn_get_next_upselling_step()` o el método del modelo `Contact::getNextUpsellingStep()`.

---

## Notas Importantes

1. **WhatsApp Country Code**: El código de país para WhatsApp se obtiene de la configuración (`whatsapp_country_code`). Por defecto es '52' (México). Para cambiar, agregar el valor en la tabla `config`.

2. **Email Sending**: La implementación actual usa `mail()` de PHP. Para producción se recomienda usar PHPMailer o SwiftMailer para mayor confiabilidad.

3. **RFC Validation**: El sistema valida automáticamente el tipo de persona:
   - 12 caracteres = Persona Moral (usar business_name y legal_representative)
   - 13 caracteres = Persona Física (usar owner_name)

4. **Invitados**: Contactos tipo "invitado" no requieren RFC, solo email, whatsapp y nombre.

5. **Cortesías**: Las cortesías son un beneficio global del afiliado, no por evento. Una vez usada, no puede usar otra cortesía hasta su siguiente renovación.

6. **Códigos de Registro**: Cada registration_code debe ser único. El sistema previene duplicados automáticamente.

7. **SIEM**: Contactos tipo SIEM son el punto de entrada al funnel de ventas. Deben ser promovidos a prospecto y luego a afiliado.

---

## Soporte y Contacto

Para preguntas o problemas con la implementación, contactar al equipo de desarrollo.

**Fecha de última actualización**: 2025-12-04
