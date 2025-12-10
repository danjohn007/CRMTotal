# 📋 GUÍA DE MIGRACIÓN DE CONTACTOS

## 🚀 Proceso Completo de Migración

Esta guía te ayudará a migrar los contactos del archivo Excel a la base de datos de manera segura.

---

## ⚠️ IMPORTANTE - ANTES DE EMPEZAR

1. ✅ **Verifica que tienes un backup de la base de datos**
2. ✅ **Asegúrate de tener el archivo CSV**: `CONCENTRADO GLOBAL.csv` en la raíz del proyecto
3. ✅ **Verifica que el archivo `config/database.php` tiene la configuración correcta**

---

## 📁 Archivos Incluidos

```
migration/
├── 01_create_siem_membership.sql    - Crea el tipo de membresía SIEM
├── 02_backup_and_prepare.sql        - Respalda y limpia la tabla
├── 03_migrate_contacts.php          - Migra los datos del CSV
├── 04_reconnect_registrations.sql   - Reconecta boletos
├── 05_verify_migration.php          - Verifica el resultado
└── README.md                        - Esta guía
```

---

## 🔄 PASOS DE LA MIGRACIÓN

### **PASO 1: Crear Membresía SIEM**

Ejecuta el script SQL para crear el tipo de membresía SIEM:

```bash
mysql -u tu_usuario -p nombre_bd < migration/01_create_siem_membership.sql
```

**O desde phpMyAdmin:**
- Abre el archivo `01_create_siem_membership.sql`
- Copia y pega el contenido
- Ejecuta

**Resultado esperado:**
```
1 row inserted
```

---

### **PASO 2: Backup y Preparación**

Ejecuta el script de backup:

```bash
mysql -u tu_usuario -p nombre_bd < migration/02_backup_and_prepare.sql
```

**⚠️ IMPORTANTE:** Este script:
1. Guarda información de boletos vinculados
2. **BORRA TODOS LOS CONTACTOS** (TRUNCATE)
3. Los boletos quedan con `contact_id = NULL`

**Verifica el resultado:**
```
- Boletos guardados en backup: X
- Contacts después de truncate: 0
- Boletos sin contacto: X
```

---

### **PASO 3: Migrar Contactos desde CSV**

Ejecuta el script PHP de migración:

```bash
php migration/03_migrate_contacts.php
```

**Proceso:**
- Lee el archivo CSV línea por línea
- Aplica todas las transformaciones
- Inserta en la tabla `contacts`
- Muestra progreso cada 100 registros

**Resultado esperado:**
```
====================================
MIGRACIÓN COMPLETADA
====================================
Total insertados: XXXX
Total errores: X
====================================

ESTADÍSTICAS POR TIPO:
----------------------
SIEM           : XXXXX registros - $XXX,XXX.XX
MEMBRESIA      : XXXXX registros - $XXX,XXX.XX
```

---

### **PASO 4: Reconectar Boletos**

Ejecuta el script de reconexión:

```bash
mysql -u tu_usuario -p nombre_bd < migration/04_reconnect_registrations.sql
```

**Proceso:**
- Busca coincidencias por RFC
- Busca coincidencias por Email
- Actualiza `event_registrations.contact_id` con los nuevos IDs

**Verifica el resultado:**
```
✓ Reconectados por RFC: X
✓ Reconectados por Email: X

Boletos reconectados exitosamente: X
Boletos SIN reconectar: X
```

---

### **PASO 5: Verificar Migración**

Ejecuta el script de verificación:

```bash
php migration/05_verify_migration.php
```

**Genera reporte completo:**
- Estadísticas de contactos
- Distribución por tipo de membresía
- Distribución por vendedor
- Estadísticas de boletos
- Tasa de reconexión
- Calidad de datos

---

## ✅ CRITERIOS DE ÉXITO

La migración fue exitosa si:

1. ✅ Total contactos migrados ≈ Líneas del CSV (menos encabezados)
2. ✅ Tasa de reconexión de boletos = 100% (o muy cercano)
3. ✅ No hay errores críticos
4. ✅ Distribución de datos tiene sentido

---

## 🔧 SOLUCIÓN DE PROBLEMAS

### **Error: "No se encuentra el archivo CSV"**
```
Solución: Verifica que CONCENTRADO GLOBAL.csv está en:
c:\Users\danie\Downloads\CRMTotal\
```

### **Error: "Cannot connect to database"**
```
Solución: Verifica config/database.php:
- Host correcto
- Usuario y contraseña
- Nombre de base de datos
```

### **Algunos boletos no se reconectaron**
```
Causas posibles:
1. RFC cambió entre BD original y Excel
2. Email cambió
3. Contacto no existe en el Excel

Solución:
- Revisar tabla temp_contact_registrations
- Buscar manualmente el contacto correcto
- Actualizar manualmente si es necesario
```

### **Quiero deshacer la migración**
```
Si guardaste un backup:
1. DROP TABLE contacts;
2. Restaurar desde backup
3. Los boletos recuperarán sus vinculaciones originales
```

---

## 🗑️ LIMPIEZA POST-MIGRACIÓN

Si todo salió bien, ejecuta:

```sql
DROP TABLE temp_contact_registrations;
```

---

## 📊 TRANSFORMACIONES APLICADAS

El script aplica estas transformaciones automáticamente:

| **Campo CSV** | **Transformación** | **Campo SQL** |
|---|---|---|
| IMPORTE | `$3,800.00` → `3800.00` | `amount` |
| FECHA RENOVACION | `5/1/2026` → `2026-01-05` | `renewal_date` |
| MES DE RENOVACIÓN | `ENE` → `1` | `renewal_month` |
| VENDEDOR | `MNAVA` → `17` | `assigned_affiliate_id` |
| IMPORTE | Por rango → ID membresía | `membership_type_id` |
| IMPORTE | ≤$1,550 → `SIEM` | `affiliation_type` |
| RFC | Longitud → `fisica/moral` | `person_type` |

---

## 📞 SOPORTE

Si encuentras problemas:
1. Revisa los logs de error
2. Verifica la tabla `temp_contact_registrations`
3. Consulta la sección de Solución de Problemas
4. Si tienes backup, puedes restaurar y reintentar

---

## ✨ ¡MIGRACIÓN COMPLETA!

Una vez verificado todo, tu sistema estará listo con:
- ✅ Todos los contactos del Excel migrados
- ✅ Boletos reconectados
- ✅ Datos transformados correctamente
- ✅ Sistema funcionando normalmente
