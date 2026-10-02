# API para Grandes Empleados

Permite que [grandesempleados.com](https://grandesempleados.com) confirme por RFC si una empresa es
**afiliada vigente** de la Cámara de Comercio de Querétaro. Las empresas afiliadas obtienen consultas
sin costo en Grandes Empleados sin enviar comprobantes.

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

- `200 {"success": true, "afiliada": true, "rfc", "razon_social", "nombre_comercial", "numero_afiliacion", "membresia", "fecha_afiliacion", "fecha_vencimiento"}`
- `200 {"success": true, "afiliada": false, "rfc"}`
- `401` token ausente o incorrecto · `422` RFC inválido

"Afiliada" significa `contacts.contact_type = 'afiliado'` con una afiliación `status = 'active'` y
`expiration_date >= hoy`. La respuesta no incluye datos personales (dueño, correos, teléfonos).

Código: `ApiController::verifyAffiliation()`, `Contact::getCurrentAffiliationByRfc()` y la ruta en
`public/index.php`. El `.htaccess` de `public/` ya reenvía el encabezado `Authorization`.

## Registro y límites

- Cada consulta queda en `api_request_log`: endpoint, IP, RFC (o hash del correo/teléfono) y resultado.
- `api/v1/afiliacion`: 600 consultas por hora por IP; después responde `429`.
- `api/buscar-empresa` (formulario de registro a eventos): 30 búsquedas por hora por IP. Ya no busca por
  razón social, y si se busca por RFC no devuelve correo, teléfono, WhatsApp ni dueño (sí razón social y si
  es afiliado activo). Por correo o teléfono sigue autollenando el formulario.
- Si la tabla todavía no existe, la API funciona igual pero sin registro ni límites.
