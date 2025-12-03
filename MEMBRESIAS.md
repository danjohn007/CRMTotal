# Especificaciones CRM - Cámara de Comercio Querétaro

## 1. Tabla CONTACTS - Estructura Completa

### Tipos de Contacto (contact_type)
La columna `contact_type` diferencia entre:
- **prospecto**: Contacto potencial sin afiliación
- **afiliado**: Miembro activo de la cámara
- **registro**: Contacto registrado en eventos
- **siem**: Registro del Sistema de Información Empresarial Mexicano
- **invitado**: Asistente a eventos (no requiere RFC, solo email, whatsapp, nombre)
- **funcionario de gobierno**: Representante de gobierno

### Tipo de Persona (person_type)
Clasifica a 2 tipos según el RFC:
- **persona moral**: RFC de 12 caracteres
  - Relacionado con `business_name` (razón social)
  - Relacionado con `legal_representative` (representante legal)
- **persona física**: RFC de 13 caracteres
  - Relacionado con `owner_name` (nombre del dueño)

### Estructura Completa de Columnas - Tabla CONTACTS

#### Información de Registro
- `registration_number` - REGISTRO (No. Mes)
- `renewal_date` - FECHA RENOVACION
- `receipt_date` - FECHA RECIBO
- `receipt_number` - # DE RECIBO
- `invoice_number` - # DE FACTURA
- `csf_file` - Constancia de Situación Fiscal (archivo adjunto CSF)
- `sticker` - ENGOMADO

#### Información Fiscal y Legal
- `business_name` - Razón social
- `amount` - IMPORTE
- `payment_method` - METODO DE PAGO
- `reaffiliation` - Reafiliación (boolean)
- `is_new` - Nueva (boolean)
- `seller` - VENDEDOR (assigned_affiliate_id)
- `affiliation_type` - TIPO DE AFILIACIÓN
- `membership_type_id` - TIPO DE MEMBRESÍA (FK a membership_types)
- `fiscal_address` - DIRECCIÓN FISCAL
- `renewal_month` - MES DE RENOVACIÓN
- `rfc` - RFC (UNIQUE, nullable)
- `email` - EMAIL
- `whatsapp` - WHATSAPP
- `phone` - TELÉFONO
- `contact_name` - CONTACTO
- `trade_name` - Nombre comercial
- `owner_name` - Dueño (persona física)
- `legal_representative` - Representante legal (persona moral)

#### Información Comercial
- `business_sector` - GIRO
- `description` - DESCRIPCION
- `commercial_address` - DIRECCION COMERCIAL
- `nice_classification` - CLASIFICACIÓN NIZA
- `sales_contact` - Contacto de ventas
- `products_sells` - 4 principales productos que vende (JSON array)
- `purchase_contact` - Contacto de compras
- `products_buy` - 2 principales productos que compra (JSON array)
- `branch_count` - Numero de sucursales
- `branch_addresses` - Dirección sucursales (JSON array)
- `contact_type` - Tipo de contacto (prospecto, afiliado, registro, siem, invitado, funcionario)
- `services_interest` - Servicios de interés (JSON array)

---

## 2. Tipos de Membresías

### Categorías Disponibles
1. **BÁSICA** - Membresía inicial
2. **PYME** - Pequeña y Mediana Empresa
3. **EMPRENDEDOR** - Para startups y emprendedores
4. **VISIONARIO** - Membresía premium
5. **PREMIER** - Membresía premium plus
6. **PATROCINADOR OFICIAL** - Patrocinio estándar
7. **PATROCINADOR AAA** - Patrocinio premium
8. **NAMING RIGHTS** - Patrocinio elite

### Beneficio de Cortesías
Las membresías **BÁSICA, PYME, EMPRENDEDOR, VISIONARIO, PREMIER, PATROCINADOR OFICIAL, PATROCINADOR AAA, NAMING RIGHTS** tienen derecho a **1 cortesía máximo** en eventos pagados (`is_courtesy_tickets = true`).

---

## 3. Estrategia de Upselling

### Flujo de Upselling
El upselling inicia con la categoría **REGISTRO SIEM**, relacionada con el **EDA (Event-Driven Architecture)**:

1. **REGISTRO SIEM** → **PROSPECTO** → **BÁSICA**
2. **BÁSICA** → **PYME** o **EMPRENDEDOR**
3. **PYME/EMPRENDEDOR** → **VISIONARIO**
4. **VISIONARIO** → **PREMIER**
5. **PREMIER** → **PATROCINADOR OFICIAL**
6. **PATROCINADOR OFICIAL** → **PATROCINADOR AAA**
7. **PATROCINADOR AAA** → **NAMING RIGHTS**

### Actualización del EDA
El EDA debe incluir:
- Registro SIEM como punto de entrada
- Todos los campos de la tabla Contacts
- Transiciones entre estados de contacto
- Triggers para notificaciones de upselling
- Eventos de renovación y reafiliación

---

## 4. Tabla EVENT_REGISTRATIONS

### Relación con Contacts
La tabla `event_registrations` se relaciona con `contacts` de la siguiente manera:

- `attendee_phone` (event_registrations) = `whatsapp` (contacts)
- `attendee_email` (event_registrations) = `email` (contacts)
- `is_courtesy_tickets` = true **exclusivamente** para TIPO DE MEMBRESÍA listadas arriba

### Estructura Actualizada EVENT_REGISTRATIONS

#### Campos Principales
- `registration_code` - Código único del boleto (QR code)
- `event_id` - FK a events
- `contact_id` - FK a contacts (nullable para invitados)
- `is_guest` - Boolean (indica si es invitado)
- `guest_type` - Tipo de invitado (eliminar attendee_position y categoría_asistente)
- `attendee_name` - Nombre del asistente
- `attendee_phone` - WhatsApp del asistente
- `attendee_email` - Email del asistente
- `registration_date` - Fecha de registro
- `payment_status` - Estado del pago (paid, pending, free, courtesy)
- `amount_paid` - Monto pagado
- `is_courtesy_tickets` - Cortesía (solo para membresías elegibles)
- `attended` - Asistencia confirmada (boolean)
- `attendance_time` - Hora de registro de asistencia
- `parent_registration_id` - FK auto-referencial (para acompañantes)

### Reglas de Validación

#### Eventos Gratuitos
- `payment_status = 'free'`
- No aplican cortesías
- Registrar número de boletos generados
- Registrar asistencias/inasistencias para métricas

#### Eventos Pagados
- `payment_status = 'paid' | 'pending' | 'courtesy'`
- **Máximo 1 cortesía** por afiliado con membresía elegible
- Validar `is_courtesy_tickets` solo para membresías: BÁSICA, PYME, EMPRENDEDOR, VISIONARIO, PREMIER, PATROCINADOR OFICIAL, PATROCINADOR AAA, NAMING RIGHTS

#### Regla de Unicidad
- Cada `registration_code` (boleto) debe estar enlazado a **un único** `attendee_name`
- No duplicados: validar que no existan múltiples registros con el mismo código
- Un registro puede tener acompañantes vinculados por `parent_registration_id`

---

## 5. Funcionalidad de Eventos

### Vista de Evento (events/show.php)

#### Sección de Estadísticas
Mantener funcionalidad actual de estadísticas del evento.

#### 3 Nuevos Botones (debajo de estadísticas)

##### 1. IMPORTAR
- Descargar lista de emails de todos los registrados
- Formato: CSV o Excel
- Columnas: nombre, email, whatsapp, tipo_registro, payment_status

##### 2. ENVIAR WhatsApp
- Enviar mensaje masivo a todos los WhatsApp registrados
- Incluir link del evento
- Incluir recordatorio con fecha/hora
- Template personalizable

##### 3. EMAIL
- Enviar correo electrónico a todos los asistentes
- Adjuntar boleto QR code (`registration_code`)
- Template con información del evento
- Opción de incluir: mapa, agenda, instrucciones

### Prevención de Duplicados
- Validar en `Event.php` modelo:
  ```php
  // No duplicados en registration_code
  WHERE registration_code IS NOT NULL 
  GROUP BY registration_code
  HAVING COUNT(*) = 1
  ```
- Al registrar nuevo asistente, verificar que `registration_code` no exista
- Al listar registros, filtrar `WHERE (parent_registration_id IS NULL OR parent_registration_id = 0)`

---

## 6. Métricas y Reportes

### Eventos Gratuitos
- Total de boletos generados
- Asistencias confirmadas
- Inasistencias
- Tasa de asistencia: `(asistencias / boletos) * 100`

### Eventos Pagados
- Total de boletos vendidos
- Total de cortesías otorgadas (máx 1 por afiliado)
- Ingresos totales: `SUM(amount_paid WHERE payment_status = 'paid')`
- Boletos pendientes de pago
- Tasa de conversión: `(pagados / total_registros) * 100`

### Reportes por Membresía
- Asistencia por tipo de membresía
- Uso de cortesías por membresía
- Conversión de eventos a nuevas afiliaciones

---

## 7. Reglas de Negocio

### RFC
- Persona moral: 12 caracteres → usar `business_name` y `legal_representative`
- Persona física: 13 caracteres → usar `owner_name`
- Nullable para invitados y registros sin RFC
- UNIQUE constraint (permite múltiples NULL)

### Cortesías
- **Solo 1 cortesía máximo** por afiliado en eventos pagados
- Solo para membresías: BÁSICA, PYME, EMPRENDEDOR, VISIONARIO, PREMIER, PATROCINADOR OFICIAL, PATROCINADOR AAA, NAMING RIGHTS
- Validar antes de permitir `is_courtesy_tickets = true`

### Contact Types
- **invitado**: No requiere RFC, solo email, whatsapp, nombre
- **siem**: Entrada al funnel de upselling
- **prospecto**: Siguiente paso después de SIEM
- **afiliado**: Contacto con membresía activa

### Eliminaciones
- ❌ Eliminar columna `attendee_position`
- ❌ Eliminar columna `categoría_asistente`
- ✅ Reemplazar con `guest_type` en `event_registrations`

---

## 8. Próximos Pasos de Implementación

1. **Migración de Base de Datos**
   - Crear script SQL para agregar nuevas columnas a `contacts`
   - Actualizar tabla `event_registrations` con nuevos campos
   - Migrar datos existentes

2. **Actualizar Modelos PHP**
   - `Contact.php`: agregar getters/setters para nuevos campos
   - `Event.php`: actualizar `getRegistrations()` con validaciones
   - Crear métodos para exportar emails y enviar notificaciones

3. **Actualizar Controladores**
   - `AffiliatesController.php`: validación de RFC por tipo de persona
   - `EventsController.php`: implementar 3 nuevos botones
   - `ProspectsController.php`: integrar flujo SIEM → Prospecto

4. **Actualizar Vistas**
   - `events/show.php`: agregar 3 botones (IMPORTAR, WhatsApp, EMAIL)
   - `affiliates/create.php`: campos nuevos de contacts
   - `affiliates/edit.php`: validación person_type

5. **Testing**
   - Validar regla de 1 cortesía máximo
   - Verificar no duplicados en `registration_code`
   - Probar envío masivo de WhatsApp y Email
