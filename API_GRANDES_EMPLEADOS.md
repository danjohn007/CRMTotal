# API para Grandes Empleados

Permite que [grandesempleados.com](https://grandesempleados.com) confirme por RFC si una empresa es
**afiliada vigente** de la Cámara de Comercio de Querétaro. Las empresas afiliadas obtienen consultas
sin costo en Grandes Empleados sin enviar comprobantes.

También la usa el formulario **público** de registro de empresas de Grandes Empleados para autollenar los
datos principales de la empresa cuando el RFC ya existe en el CRM, aunque no sea afiliada vigente
(prospecto, exafiliado, afiliación vencida…), para que se registre rápido y se le invite a afiliarse.

## Activar

0. Ejecuta `config/update_v2.9.0.sql` en la base (tabla `api_request_log` para el registro y los límites).
1. *Configuración → APIs → Grandes Empleados*: marca **Generar token** y guarda.
2. Copia el token y la URL que aparece debajo (`https://<este-dominio>/api/v1/afiliacion`).
3. En Grandes Empleados: *Administración → Cámaras de Comercio → CANACO*, pega la URL y el token y guarda.

Para desconectar, marca **Revocar el token**. Generar uno nuevo invalida el anterior.

## Contrato

```
GET /api/v1/afiliacion?rfc=IDI180615AB3
Authorization: Bearer <token>
```

Tres respuestas `200` (las claves `success`, `afiliada` y los datos de la afiliación son los de antes;
`encontrada` y los datos de la empresa son nuevos):

```jsonc
// Afiliada vigente
{"success": true, "afiliada": true, "encontrada": true, "rfc": "IDI180615AB3",
 "razon_social": "…", "nombre_comercial": "…", "giro": "…", "codigo_postal": "76000",
 "estado": "Querétaro", "municipio": "Santiago de Querétaro", "sitio_web": "…",
 "domicilio": "…",                       // solo persona moral (RFC de 12)
 "numero_afiliacion": "…", "membresia": "…", "fecha_afiliacion": "2026-01-15", "fecha_vencimiento": "2027-01-15"}

// Existe en el CRM pero no es afiliada vigente
{"success": true, "afiliada": false, "encontrada": true, "rfc": "IDI180615AB3",
 "razon_social": "…", "nombre_comercial": "…", "giro": "…", "codigo_postal": "…",
 "estado": "…", "municipio": "…", "sitio_web": "…",
 "domicilio": "…"}                       // solo persona moral (RFC de 12)

// No existe en el CRM
{"success": true, "afiliada": false, "encontrada": false, "rfc": "IDI180615AB3"}
```

- `401` token ausente o incorrecto · `422` RFC inválido · `429` demasiadas consultas

"Afiliada" significa `contacts.contact_type = 'afiliado'` con una afiliación `status = 'active'` y
`expiration_date >= hoy`. "Encontrada" significa que hay un contacto con ese RFC (cualquier
`contact_type`); si hay más de uno, se toma el afiliado y luego el actualizado más recientemente.

### Datos de la empresa (columnas de `contacts`)

| Campo | Columna | Notas |
|---|---|---|
| `razon_social` | `business_name` | |
| `nombre_comercial` | `commercial_name` | si está vacío, `trade_name` |
| `giro` | `business_sector` (GIRO) | si está vacío, `industry` |
| `codigo_postal` | `postal_code` | |
| `estado` | `state` | |
| `municipio` | `city` | el CRM no tiene columna de municipio; `city` es lo más cercano |
| `sitio_web` | `website` | |
| `domicilio` | `fiscal_address` | si está vacío, `commercial_address`; solo persona moral |

Los valores vacíos se envían como `null` (también `razon_social`/`nombre_comercial` de una afiliada,
que antes podían llegar como `""`). Las entidades HTML con que el CRM guarda los textos (`&amp;`…) se
decodifican.

### Privacidad

- **Nunca** se envían nombres del dueño o representante legal, correos, teléfonos, WhatsApp, redes
  sociales, notas ni ningún otro dato personal, sea la empresa afiliada o no.
- **Persona moral** (RFC de 12 caracteres): datos de la empresa **con** `domicilio`.
- **Persona física** (RFC de 13 caracteres): datos de la empresa **sin** `domicilio` (la clave no se
  envía), porque el domicilio de una persona física es un dato personal.

Código: `ApiController::verifyAffiliation()`, `Contact::getCompanyDataByRfc()`,
`Contact::getCurrentAffiliationByRfc()` y la ruta en `public/index.php`. El `.htaccess` de `public/` ya
reenvía el encabezado `Authorization`.

## Registro y límites

- Cada consulta queda en `api_request_log`: endpoint, IP, RFC (o hash del correo/teléfono) y resultado
  (en `afiliacion`: `afiliada`, `no_afiliada` = existe pero no vigente, `no_encontrada`, `no_autorizado`,
  `rfc_invalido`, `limite`).
- `api/v1/afiliacion`: 600 consultas por hora por IP; después responde `429`. Las consultas del formulario
  público de Grandes Empleados salen de su servidor, así que comparten este límite con su sincronización diaria.
- `api/buscar-empresa` (formulario de registro a eventos): 30 búsquedas por hora por IP. Ya no busca por
  razón social, y si se busca por RFC no devuelve correo, teléfono, WhatsApp ni dueño (sí razón social y si
  es afiliado activo). Por correo o teléfono sigue autollenando el formulario.
- Si la tabla todavía no existe, la API funciona igual pero sin registro ni límites.
