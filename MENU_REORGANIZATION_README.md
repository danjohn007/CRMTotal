# Menu Reorganization and System Updates

## Overview
This update reorganizes the CRM menu items based on user roles, updates field requirements for prospects, enhances EDA completion calculations, and implements automated cross-selling and up-selling features.

## Changes Made

### 1. Menu Reorganization by Role

The sidebar menu has been reorganized to show different items based on user roles:

#### **VENDEDOR (Role ID 4 - afiliador)**
- Dashboard
- Prospectos
- EDA
- Agenda Comercial
- Buscador Proveedores
- Métricas y KPI's

#### **DIRECCIÓN (Role ID 2 - direccion)**
- Dashboard
- EDA
- Eventos
- Membresías
- Buscador Proveedores
- Métricas y KPI's
- Reportes financieros
- Usuarios
- Importar
- Configuración

#### **JEFE COMERCIAL (Role ID 3 - jefe_comercial)**
- Dashboard
- Prospectos
- EDA
- Eventos
- Membresías
- Agenda Comercial
- Buscador Proveedores
- Métricas y KPI's
- Reportes financieros
- Usuarios
- Importar

#### **CONTABILIDAD (Role ID 5 - contabilidad)**
- Dashboard
- Métricas y KPI's
- Reportes financieros

#### **CONSEJERO Y MESA DIRECTIVA (Role ID 6,7 - consejero, mesa_directiva)**
- Dashboard
- Métricas y KPI's
- Buscador Proveedores

#### **SUPERADMIN (Role ID 1 - superadmin)**
- All functionalities (complete access)

### 2. Menu Label Changes

- **"Buscador"** → **"Buscador Proveedores"**
- **"Reportes"** → **"Métricas y KPI's"**
- **"Financiero"** → **"Reportes financieros"**
- **"Jefatura"** (in channel labels) → **"Reasignaciones"**
- **"Detalle de Prospecto"** (page title) → **"Detalle Prospectos"**

### 3. Prospects Table Column Reordering

The prospects list table columns have been reordered as follows:
1. Canal
2. WhatsApp
3. Nombre de Contacto
4. Razón Social & RFC
5. Fecha
6. Nombre Afiliador
7. Acciones (Ver-Editar-Afiliar)

### 4. Updated Required Fields for Prospects

#### Required Fields (marked with *):
- RFC
- Nombre comercial
- WhatsApp
- Nombre del Encargado/Recepcionista/Representante Legal
- Razón Social
- Industria / Giro (Clasificación Niza)

#### Optional Fields:
- Email
- Teléfono Oficina
- Dirección Fiscal
- Dirección Comercial
- Notas

### 5. EDA Completion Calculation (100%)

The EDA completion calculation has been updated to include all required fields across three stages:

#### **Stage A (25% of total)** - Basic Information
- RFC
- Razón social
- Nombre comercial
- Nombre del encargado/representante legal
- WhatsApp
- Industria / Giro
- Teléfono
- Email

#### **Stage B (35% of total)** - Business Details
- Dirección fiscal
- Dirección comercial
- Descripción
- Clasificación Niza
- WhatsApp ventas (contacto de ventas)
- WhatsApp compras (contacto de compras)
- 4 principales productos que vende
- 2 principales productos que compra

#### **Stage C (40% of total)** - Affiliation Details
- Constancia de Situación Fiscal (archivo CSF)
- ENGOMADO
- Importe
- Método de pago
- Tipo (Reafiliación/Nueva)
- Vendedor asignado
- Tipo de afiliación
- Tipo de membresía
- Mes de renovación
- Fecha de afiliación

### 6. Enhanced Activity Button

The "Nueva Actividad" button has been replaced with an **ACTIVIDAD** dropdown menu that includes:

1. 🏢 **Visita a sus instalaciones** - Programar fecha, hora y notas
2. 💬 **Mandar WhatsApp** - Programar fecha, hora y notas
3. 📧 **Mandar email** - Programar fecha, hora y notas
4. 🧾 **Mandar factura o comprobante de pago** - Programar fecha, hora y notas
5. 📎 **Adjuntar documentación al EDA** - O a su sección Prospectos
6. ✅ **Dar de alta como afiliado** - Terminar llenado de perfil

### 7. Automated Cross-Selling and Up-Selling

#### Cross-Selling (Automático)
- **Frequency**: Every 6 weeks (42 days)
- **Starting from**: Affiliation date
- **Documentation**: Each action is automatically documented

#### Up-Selling (Automático)
- **First Up-Selling**: 8 weeks (56 days) after affiliation
- **Second Up-Selling**: 34 weeks (238 days) after affiliation
- **Frequency**: Twice per year per affiliate

## Installation Instructions

### Step 1: Apply Database Migration

Run the SQL migration file to update the database:

```bash
mysql -u your_username -p your_database_name < migration_menu_reorganization.sql
```

Or import via phpMyAdmin:
1. Open phpMyAdmin
2. Select your database
3. Go to "Import" tab
4. Choose the file `migration_menu_reorganization.sql`
5. Click "Go"

### Step 2: Verify Event Scheduler

The migration creates a daily event to generate automated opportunities. Verify that the MySQL event scheduler is enabled:

```sql
SHOW VARIABLES LIKE 'event_scheduler';
```

If it shows 'OFF', enable it:

```sql
SET GLOBAL event_scheduler = ON;
```

To make it permanent, add to your MySQL configuration file (my.cnf or my.ini):
```
[mysqld]
event_scheduler=ON
```

### Step 3: Clear Cache (if applicable)

If your application uses any caching mechanism, clear the cache:

```bash
# Example for generic PHP cache
rm -rf cache/*
```

### Step 4: Test the Changes

1. **Test Role-Based Menu Access**:
   - Log in with different user roles
   - Verify that each role sees only their authorized menu items

2. **Test Prospect Forms**:
   - Create a new prospect
   - Verify required fields are enforced
   - Edit an existing prospect

3. **Test Activity Dropdown**:
   - Open a prospect detail page
   - Click the "ACTIVIDAD" button
   - Verify all activity types are displayed

4. **Test Automated Opportunities**:
   - Check the `automated_opportunities` table
   - Verify opportunities are being created for existing affiliates
   - Wait 24 hours for the daily event to run and create new opportunities

## Database Schema Changes

### New Tables

#### `activity_types`
Stores the different types of activities available:
- id
- name (visita, whatsapp, email, factura, documentacion, alta_afiliado, cross_selling, upselling)
- icon
- description
- is_active
- created_at

#### `automated_opportunities`
Tracks automated cross-selling and up-selling opportunities:
- id
- contact_id (foreign key to contacts)
- opportunity_type (cross_selling, upselling)
- scheduled_date
- status (pending, completed, cancelled)
- notes
- created_at
- completed_at

### New Stored Procedure

**`generate_automated_opportunities()`**
- Automatically generates cross-selling opportunities every 6 weeks
- Creates up-selling opportunities at 8 weeks and 34 weeks
- Runs daily via MySQL event scheduler
- Prevents duplicate opportunity creation

### Updated Tables

#### `contacts`
New columns added (if not exist):
- `niza_custom_category` - Custom Niza classification
- `description` - Business description
- `whatsapp_sales` - Sales contact WhatsApp
- `whatsapp_purchases` - Purchases contact WhatsApp
- `whatsapp_admin` - Admin contact WhatsApp

## Files Modified

1. **app/views/layouts/main.php** - Menu reorganization by role
2. **app/controllers/ProspectsController.php** - Page title update
3. **app/views/prospects/index.php** - Column reordering
4. **app/views/prospects/create.php** - Required fields update
5. **app/views/prospects/edit.php** - Required fields update
6. **app/views/prospects/show.php** - Activity dropdown enhancement
7. **app/controllers/ExpedientesController.php** - EDA completion calculation
8. **app/views/dashboard/comercial.php** - Label change (Jefatura → Reasignaciones)
9. **app/views/dashboard/afiliador.php** - Label change
10. **app/views/reports/commercial.php** - Label change
11. **app/views/journey/index.php** - Label change

## Notes and Recommendations

### Performance Considerations

1. **Event Scheduler**: The daily event runs at 2 AM. Adjust the time in the migration file if needed:
   ```sql
   STARTS CURRENT_DATE + INTERVAL 1 DAY + INTERVAL 2 HOUR  -- Change HOUR value
   ```

2. **Large Datasets**: If you have thousands of affiliates, the stored procedure may take several minutes on first run.

### Monitoring

Monitor the automated opportunities:

```sql
-- Check pending opportunities
SELECT * FROM automated_opportunities WHERE status = 'pending';

-- Check opportunities by type
SELECT opportunity_type, COUNT(*) as count 
FROM automated_opportunities 
GROUP BY opportunity_type;

-- Check recent opportunities
SELECT * FROM automated_opportunities 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_at DESC;
```

### Backup

Before applying the migration, **always backup your database**:

```bash
mysqldump -u your_username -p your_database_name > backup_before_migration.sql
```

## Support

If you encounter any issues:

1. Check the MySQL error log
2. Verify the event scheduler is running: `SHOW PROCESSLIST;`
3. Manually run the stored procedure: `CALL generate_automated_opportunities();`
4. Check for foreign key constraint errors
5. Ensure all required fields exist in the contacts table

## Future Enhancements

Potential future improvements:
- Email notifications for automated opportunities
- Dashboard widget showing upcoming opportunities
- Configurable frequency for cross-selling and up-selling
- Integration with WhatsApp API for automated messages
- Reports on conversion rates from automated opportunities

---

**Version**: 1.0  
**Date**: December 4, 2025  
**Compatibility**: CRM Total v1.x
