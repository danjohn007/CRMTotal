# CRM Total - Cámara de Comercio de Querétaro

Sistema de Gestión de Relaciones con Clientes (CRM) desarrollado para la Cámara de Comercio de Querétaro. Este sistema permite gestionar afiliaciones, prospectos, eventos, y toda la operación comercial de la cámara.

![PHP](https://img.shields.io/badge/PHP-8.0+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg)

## 📋 Características Principales

### Módulos del Sistema

- **Dashboard Inteligente por Perfil**: Métricas personalizadas para cada tipo de usuario
- **Gestión de Prospectos**: 6 canales de captación (Chatbot, Alta directa, Eventos, Buscador, etc.)
- **Gestión de Afiliados**: Expediente Digital Único con etapas de completitud (25%, 35%, 100%)
- **Sistema de Eventos**: Creación y gestión de eventos internos, externos y de terceros
- **Agenda/Calendario**: Gestión de actividades (llamadas, WhatsApp, emails, visitas)
- **Customer Journey**: Visualización del proceso comercial y oportunidades de upselling/cross-selling
- **Buscador Inteligente**: Búsqueda de proveedores afiliados con sistema NO MATCH
- **Sistema de Notificaciones**: Alertas de vencimientos, actividades y oportunidades
- **Módulo de Reportes**: Comerciales, financieros y operativos
- **Configuración del Sistema**: Sitio, correo, estilos, pagos y APIs

### Perfiles de Usuario

- **Superadmin**: Acceso total al sistema
- **Dirección**: Dashboard ejecutivo con métricas generales
- **Jefe Comercial**: Gestión de equipo de ventas
- **Afiliador**: Prospección y seguimiento de clientes
- **Contabilidad**: Facturación y reportes financieros
- **Consejero/Mesa Directiva**: Vista de métricas mensuales

## 🛠️ Tecnologías

- **Backend**: PHP 8.0+ (puro, sin framework)
- **Base de datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3 (Tailwind CSS), JavaScript
- **Gráficas**: Chart.js, ApexCharts
- **Calendario**: FullCalendar.js
- **Interactividad**: Alpine.js

## 📦 Requisitos

- PHP 8.0 o superior
- MySQL 5.7 o superior
- Apache 2.4+ con mod_rewrite habilitado
- Extensiones PHP: PDO, PDO_MySQL, JSON, Session

## 🚀 Instalación

### 1. Clonar o descargar el repositorio

```bash
git clone https://github.com/danjohn007/CRMTotal.git
cd CRMTotal
```

### 2. Configurar el Virtual Host de Apache

Agrega la siguiente configuración a tu archivo de configuración de Apache o crea un archivo `.conf`:

```apache
<VirtualHost *:80>
    ServerName crm.local
    DocumentRoot /ruta/a/CRMTotal/public
    
    <Directory /ruta/a/CRMTotal/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Alternativa: Subdirectorio**

Si prefieres instalar en un subdirectorio (ej: `http://tuservidor.com/crm/`), simplemente copia la carpeta a tu directorio web y la URL base se detectará automáticamente.

### 3. Crear la base de datos

```bash
mysql -u root -p
```

```sql
CREATE DATABASE crm_ccq CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Importar el esquema y datos de ejemplo

```bash
mysql -u root -p crm_ccq < config/database.sql
```

### 5. Configurar las credenciales de la base de datos

Edita el archivo `config/database.php`:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'crm_ccq');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_CHARSET', 'utf8mb4');
```

### 6. Verificar permisos

Asegúrate de que el servidor web tenga permisos de lectura en todos los archivos:

```bash
chmod -R 755 /ruta/a/CRMTotal
chmod -R 777 /ruta/a/CRMTotal/public/uploads # Si existe
```

### 7. Verificar la instalación

Visita en tu navegador:

```
http://tu-dominio/test.php
```

Este archivo verificará:
- Conexión a la base de datos
- URL base detectada
- Estado de las extensiones PHP necesarias

## 🔐 Credenciales de Acceso

### Usuario Administrador
- **Email**: `admin@camaradecomercioqro.mx`
- **Contraseña**: `Admin123!`

### Usuarios de Ejemplo (contraseña: `Admin123!`)
| Email | Rol |
|-------|-----|
| `direccion@camaradecomercioqro.mx` | Dirección |
| `jefe.comercial@camaradecomercioqro.mx` | Jefe Comercial |
| `ventas1@camaradecomercioqro.mx` | Afiliador |
| `ventas2@camaradecomercioqro.mx` | Afiliador |
| `contabilidad@camaradecomercioqro.mx` | Contabilidad |

## 📁 Estructura del Proyecto

```
CRMTotal/
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── core/           # Clases base (Router, Controller, Model, Database)
│   ├── models/         # Modelos de datos
│   └── views/          # Vistas (templates)
│       ├── affiliates/
│       ├── agenda/
│       ├── auth/
│       ├── config/
│       ├── dashboard/
│       ├── errors/
│       ├── events/
│       ├── journey/
│       ├── layouts/
│       ├── notifications/
│       ├── prospects/
│       ├── reports/
│       ├── search/
│       └── users/
├── config/
│   ├── config.php      # Configuración general
│   ├── database.php    # Configuración de BD
│   └── database.sql    # Esquema y datos de ejemplo
├── public/
│   ├── .htaccess      # Rewrite rules
│   ├── index.php      # Entry point
│   └── test.php       # Test de conexión
├── .htaccess          # Redirección a public/
└── README.md
```

## 🔗 URLs del Sistema

El sistema utiliza URLs amigables. Principales rutas:

| Ruta | Descripción |
|------|-------------|
| `/` | Página principal |
| `/login` | Inicio de sesión |
| `/dashboard` | Dashboard (según rol) |
| `/prospectos` | Gestión de prospectos |
| `/afiliados` | Gestión de afiliados |
| `/eventos` | Gestión de eventos |
| `/agenda` | Calendario de actividades |
| `/journey` | Customer Journey |
| `/buscador` | Buscador inteligente |
| `/notificaciones` | Centro de notificaciones |
| `/reportes` | Centro de reportes |
| `/usuarios` | Gestión de usuarios (admin) |
| `/configuracion` | Configuración del sistema (admin) |

## ⚙️ Configuración del Sistema

El módulo de configuración permite ajustar:

- **Sitio y Logotipo**: Nombre, logo, teléfonos y horarios
- **Correo Electrónico**: Configuración SMTP para envío de correos
- **Estilos y Colores**: Personalización de colores del sistema
- **Pasarela de Pagos**: Configuración de PayPal
- **APIs Externas**: WhatsApp, Google Maps, QR

## 📊 Tipos de Membresía

| Tipo | Código | Precio | Duración |
|------|--------|--------|----------|
| Básica | BASICA | $2,500 | 360 días |
| PYME | PYME | $5,000 | 360 días |
| Premier | PREMIER | $15,000 | 360 días |
| Patrocinador | PATROCINADOR | $50,000 | 360 días |

## 🔒 Seguridad

- Autenticación mediante sesiones con `password_hash()`
- Protección contra SQL Injection mediante PDO prepared statements
- Sanitización de inputs con `htmlspecialchars()`
- Cookies de sesión configuradas con HttpOnly y Secure
- Sistema de permisos por rol

## 📝 Datos de Ejemplo

El archivo `database.sql` incluye datos de ejemplo del estado de Querétaro:
- 10 contactos de ejemplo (afiliados, prospectos, funcionarios)
- 7 afiliaciones de ejemplo
- 4 eventos
- 5 actividades de agenda
- 7 servicios
- Notificaciones y logs de búsqueda

## 🐛 Solución de Problemas

### Error de conexión a base de datos
1. Verifica las credenciales en `config/database.php`
2. Asegúrate de que MySQL esté corriendo
3. Verifica que la base de datos exista

### Error 404 en todas las páginas
1. Verifica que mod_rewrite esté habilitado: `a2enmod rewrite`
2. Asegúrate de que AllowOverride esté en All
3. Reinicia Apache: `service apache2 restart`

### Página en blanco
1. Habilita los errores en `config/config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

## 📜 Licencia

Este proyecto es privado y pertenece a la Cámara de Comercio de Querétaro.

## 👥 Contacto

Cámara de Comercio de Querétaro
- **Dirección**: Av. 5 de Febrero No. 412, Centro, 76000 Santiago de Querétaro, Qro.
- **Teléfono**: 442 212 0035
- **Email**: info@camaradecomercioqro.mx
- **Web**: https://www.camaradecomercioqro.mx

