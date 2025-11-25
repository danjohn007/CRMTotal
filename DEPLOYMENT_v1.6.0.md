# Deployment Guide - Version 1.6.0
## Event Registration Module Updates

### Overview
This update enhances the event registration module with:
1. **Timeout Issue Fix**: Optimized database connections to prevent 60-second timeout errors
2. **RFC Validation**: Required field with strict validation (12 chars for Moral, 13 for Physical)
3. **Enhanced Registration Fields**: Detailed company and attendee information
4. **Attendee Classification**: Automatic detection when attendee differs from company owner
5. **Payment Logic**: Free tickets when attendee matches owner/representative

---

## Pre-Deployment Checklist

- [ ] Backup database: `mysqldump -u [user] -p [database] > backup_pre_v1.6.0.sql`
- [ ] Backup application files
- [ ] Verify PHP version >= 8.0
- [ ] Verify MySQL version >= 5.7
- [ ] Test database connection

---

## Deployment Steps

### Step 1: Apply Database Updates

Run the migration script to add new fields:

```bash
mysql -u [username] -p [database_name] < config/update_v1.6.0.sql
```

**What this does:**
- Adds performance indexes to prevent timeouts
- Adds new fields to `event_registrations` table:
  - `razon_social` - Business name
  - `nombre_empresario_representante` - Owner/legal representative name
  - `nombre_asistente` - Attendee name (REQUIRED)
  - `categoria_asistente` - Attendee category (propietario, socio, empleado, etc.)
  - `email_asistente` - Attendee email (when different from company)
  - `whatsapp_asistente` - Attendee WhatsApp
  - `requiere_pago` - Payment required flag
- Migrates existing data automatically

### Step 2: Verify Database Changes

```sql
-- Verify new columns exist
DESCRIBE event_registrations;

-- Check indexes
SHOW INDEX FROM event_registrations;

-- Verify data migration
SELECT id, nombre_asistente, categoria_asistente, requiere_pago 
FROM event_registrations 
LIMIT 5;
```

### Step 3: Deploy Application Code

The following files have been updated:
- `app/core/Database.php` - Connection timeout optimization
- `app/models/Event.php` - RFC validation and payment logic
- `app/controllers/EventsController.php` - Enhanced registration processing
- `app/views/events/registration.php` - New form fields and validation
- `config/database.php` - Connection timeout settings

No additional configuration is required if using the existing database credentials.

---

## Testing Guide

### Test 1: RFC Validation

**Persona Física (13 characters)**
- Input: `FOBL910724G35` ✓ Should be valid
- Input: `FOBL910724` ✗ Should show error
- Input: `fobl910724g35` ✓ Should auto-convert to uppercase

**Persona Moral (12 characters)**
- Input: `SMM040902AD3` ✓ Should be valid
- Input: `SM040902AD3` ✗ Should show error (only 11 chars)

### Test 2: Registration Flow - Owner as Attendee

1. Navigate to event registration page
2. Fill in RFC: `SMM040902AD3`
3. Fill in Razón Social: `Mi Empresa SA de CV`
4. Fill in Nombre Empresario: `Juan Pérez López`
5. Fill in Email: `contacto@miempresa.com`
6. Fill in Phone: `4421234567`
7. Fill in Nombre Asistente: `Juan Pérez López` (same as owner)
8. Submit form

**Expected Result:**
- Registration successful
- `requiere_pago` = 0 (free ticket for owner)
- Guest fields should NOT appear
- Success message displayed

### Test 3: Registration Flow - Different Attendee

1. Navigate to event registration page
2. Fill in RFC: `SMM040902AD3`
3. Fill in Nombre Empresario: `Juan Pérez López`
4. Fill in Nombre Asistente: `María González Silva` (different from owner)
5. Notice guest fields appear automatically
6. Select Categoría Asistente: `Empleado`
7. Fill in Email Asistente: `maria@miempresa.com`
8. Fill in WhatsApp Asistente: `4429876543`
9. Submit form

**Expected Result:**
- Registration successful
- `requiere_pago` = 1 (payment required)
- Payment notice shown for paid events
- Success message with payment instructions (if event is paid)

### Test 4: Form Validation

**Test invalid inputs:**
- Empty RFC → Should show "RFC es obligatorio"
- RFC with 10 chars → Should show length error
- Phone with 9 digits → Should show "10 dígitos" error
- Empty Nombre Asistente → Should show required error
- Different attendee without categoria → Should show category error

### Test 5: Company Lookup (if company exists in database)

1. Click on "Buscar" with WhatsApp or RFC
2. Verify autocomplete fills:
   - Razón Social
   - Nombre Empresario
   - Email
   - Phone
   - RFC
3. Verify RFC validation triggers automatically

---

## Troubleshooting

### Issue: Timeout Error Still Occurs

**Solution:**
1. Check MySQL server status: `systemctl status mysql`
2. Verify max_execution_time in php.ini: `max_execution_time = 120`
3. Check MySQL connection timeout: `SHOW VARIABLES LIKE 'wait_timeout';`
4. Increase if needed: `SET GLOBAL wait_timeout=120;`

### Issue: RFC Validation Not Working

**Solution:**
1. Clear browser cache
2. Verify JavaScript is enabled
3. Check browser console for errors (F12)
4. Ensure form is not being cached

### Issue: Guest Fields Not Appearing

**Solution:**
1. Verify both `nombre_asistente` and `nombre_empresario_representante` are filled
2. Check JavaScript console for errors
3. Ensure names are exactly the same (case-insensitive) to hide guest fields

### Issue: Database Migration Fails

**Solution:**
1. Check if columns already exist: `DESCRIBE event_registrations;`
2. Run migration again (it's idempotent - safe to run multiple times)
3. Check MySQL error log: `tail -f /var/log/mysql/error.log`
4. Verify user has ALTER table permissions

---

## Rollback Instructions

If you need to rollback the database changes:

```sql
-- Rollback SQL (use with caution)
ALTER TABLE `event_registrations` DROP COLUMN `razon_social`;
ALTER TABLE `event_registrations` DROP COLUMN `nombre_empresario_representante`;
ALTER TABLE `event_registrations` DROP COLUMN `nombre_asistente`;
ALTER TABLE `event_registrations` DROP COLUMN `categoria_asistente`;
ALTER TABLE `event_registrations` DROP COLUMN `email_asistente`;
ALTER TABLE `event_registrations` DROP COLUMN `whatsapp_asistente`;
ALTER TABLE `event_registrations` DROP COLUMN `requiere_pago`;
DROP INDEX `idx_event_date` ON `event_registrations`;
DROP INDEX `idx_nombre_asistente` ON `event_registrations`;
DROP INDEX `idx_categoria_asistente` ON `event_registrations`;
```

To rollback application files:
```bash
git checkout [previous-commit-hash]
```

---

## Performance Improvements

This update includes several performance optimizations:

1. **Database Indexes**: Added indexes on frequently queried columns
2. **Connection Pooling**: Optimized PDO connection with proper timeouts
3. **Query Optimization**: Session timeouts configured to prevent long-running queries
4. **Form Validation**: Client-side validation reduces server load

**Expected Performance:**
- Event registration form load: < 2 seconds
- Form submission processing: < 5 seconds
- No more 60-second timeout errors

---

## Security Considerations

1. **RFC Validation**: Server-side and client-side validation prevent invalid data
2. **Input Sanitization**: All inputs are sanitized using `htmlspecialchars()`
3. **SQL Injection Prevention**: PDO prepared statements used throughout
4. **CSRF Protection**: Token validation on form submission
5. **XSS Prevention**: Output escaping in all views

---

## Post-Deployment Verification

After deployment, verify:

- [ ] Event registration form loads without errors
- [ ] RFC validation works for both Persona Física and Moral
- [ ] Guest fields appear/hide correctly based on attendee name
- [ ] Payment logic calculates correctly (free vs paid)
- [ ] Email notifications still work
- [ ] QR code generation still functions
- [ ] Registration appears in event dashboard
- [ ] No PHP errors in error log: `tail -f /path/to/php-error.log`

---

## Support

If you encounter issues:

1. Check the troubleshooting section above
2. Review application error logs
3. Verify database migration completed successfully
4. Test with different browsers to rule out client-side issues

For critical issues, contact the development team with:
- Error messages (from browser console and server logs)
- Steps to reproduce
- Browser and PHP versions
- Database dump of a test registration record

---

## Next Steps

After successful deployment:

1. Monitor error logs for 24-48 hours
2. Gather user feedback on new form fields
3. Track registration completion rates
4. Monitor database performance
5. Consider adding unit tests for RFC validation

---

## Version History

- **v1.6.0** (2025-11-25): Event registration enhancements
  - Fixed timeout issue
  - Added RFC validation
  - Added detailed registration fields
  - Added attendee classification logic
  - Added payment requirement logic

---

Last Updated: 2025-11-25
