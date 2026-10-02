# API para Grandes Empleados

Permite que [grandesempleados.com](https://grandesempleados.com) confirme por RFC si una empresa es
**afiliada vigente** de la Cámara de Comercio de Querétaro. Las empresas afiliadas obtienen consultas
sin costo en Grandes Empleados sin enviar comprobantes.

## Activar

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
