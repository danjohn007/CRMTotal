# Deployment Checklist - Contacts, Events & Memberships Reorganization

## Pre-Deployment

### 1. Backup (CRITICAL - DO NOT SKIP!)
- [ ] Create full database backup
  ```bash
  mysqldump -u [usuario] -p enlaceca_total > backups/enlaceca_total_$(date +%Y%m%d_%H%M%S).sql
  ```
- [ ] Verify backup file exists and has content
  ```bash
  ls -lh backups/
  head -20 backups/enlaceca_total_*.sql
  ```
- [ ] Store backup in safe location (off-server)

### 2. Test Environment Validation
- [ ] Clone production database to test environment
- [ ] Run migration on test database
- [ ] Verify all tables, triggers, procedures created successfully
- [ ] Test new features in test environment
- [ ] Verify no errors in application logs

### 3. Code Deployment Preparation
- [ ] Review all changed files in PR
- [ ] Ensure git repository is clean
  ```bash
  git status
  git pull origin main
  ```

---

## Deployment Steps

### Step 1: Database Migration (15-30 minutes)

#### A. Execute Migration Script
```bash
mysql -u [usuario] -p enlaceca_total < migration_contacts_events_memberships.sql
```

#### B. Verify Migration Success
```sql
-- Check new columns
SHOW COLUMNS FROM contacts WHERE Field IN ('registration_number', 'trade_name', 'business_sector');

-- Check new membership types
SELECT id, name, code, price FROM membership_types 
WHERE code IN ('EMPRENDEDOR', 'PATROCINADOR_AAA', 'NAMING_RIGHTS');

-- Check triggers
SHOW TRIGGERS WHERE `Trigger` LIKE 'trg_contacts%';

-- Check procedure
SHOW PROCEDURE STATUS WHERE Name = 'sp_validate_courtesy_ticket';

-- Check view
SELECT COUNT(*) FROM vw_membership_courtesy_eligibility;
```

#### C. Expected Results
- ✅ 25+ new columns in contacts table
- ✅ 4 new membership types (EMPRENDEDOR, PATROCINADOR_OFICIAL, PATROCINADOR_AAA, NAMING_RIGHTS)
- ✅ 2 triggers (insert and update)
- ✅ 1 stored procedure (sp_validate_courtesy_ticket)
- ✅ 1 function (fn_get_next_upselling_step)
- ✅ 1 view (vw_membership_courtesy_eligibility)

### Step 2: Code Deployment (5 minutes)

```bash
cd /ruta/del/proyecto
git fetch origin
git checkout main  # or your branch
git pull origin main
```

#### Files to Verify:
- [ ] `app/models/Contact.php` - Updated
- [ ] `app/models/Event.php` - Updated
- [ ] `app/controllers/EventsController.php` - Updated
- [ ] `app/views/events/show.php` - Updated
- [ ] `app/views/events/send_whatsapp.php` - New
- [ ] `app/views/events/send_email.php` - New
- [ ] `public/index.php` - Updated (routes)

### Step 3: Configuration (2 minutes)

#### Set WhatsApp Country Code (if not Mexico)
```sql
INSERT INTO config (key, value, description) 
VALUES ('whatsapp_country_code', '52', 'Country code for WhatsApp bulk messaging (default: Mexico)')
ON DUPLICATE KEY UPDATE value = '52';
```

Change '52' to your country code if needed.

### Step 4: Permissions Check (1 minute)

Ensure web server has write permissions:
```bash
chmod 755 public/uploads/
chmod 644 app/views/events/*.php
```

### Step 5: Restart Services (1 minute)

```bash
# Apache
sudo service apache2 restart

# Nginx + PHP-FPM
sudo service nginx restart
sudo service php-fpm restart
```

---

## Post-Deployment Verification

### 1. Smoke Tests (5 minutes)

- [ ] **Login**: Access the system and login
- [ ] **Events List**: Navigate to `/eventos` - verify page loads
- [ ] **Event Detail**: Click on any event with registrations
- [ ] **New Buttons**: Verify 3 new buttons appear (EXPORTAR, WhatsApp, EMAIL)

### 2. Feature Tests (15 minutes)

#### A. Export Functionality
- [ ] Click "EXPORTAR" button
- [ ] Verify CSV file downloads
- [ ] Open CSV and verify columns:
  - Código Registro, Nombre, Email, WhatsApp, etc.
- [ ] Verify data is complete and accurate

#### B. WhatsApp Messaging
- [ ] Click "ENVIAR WhatsApp" button
- [ ] Enter test message with variables {nombre} and {codigo}
- [ ] Click "Generar Enlaces de WhatsApp"
- [ ] Verify links are generated
- [ ] Click one link - verify WhatsApp opens with personalized message

#### C. Email with QR
- [ ] Click "EMAIL con QR" button
- [ ] Customize subject and message
- [ ] Send to a test email address first
- [ ] Verify email is received with QR code

### 3. Database Validation (5 minutes)

```sql
-- Check contact types are working
SELECT contact_type, COUNT(*) as count 
FROM contacts 
GROUP BY contact_type;

-- Check membership eligibility
SELECT * FROM vw_membership_courtesy_eligibility LIMIT 10;

-- Check for duplicate registration codes (should be 0)
SELECT registration_code, COUNT(*) as count
FROM event_registrations
WHERE registration_code IS NOT NULL
GROUP BY registration_code
HAVING count > 1;
```

### 4. Log Monitoring (10 minutes)

Monitor for errors:
```bash
# PHP errors
tail -f /var/log/apache2/error.log

# MySQL errors  
tail -f /var/log/mysql/error.log

# Application logs (if any)
tail -f /var/log/crm/app.log
```

---

## Troubleshooting

### Issue: Migration fails with "Table already has column"
**Solution**: Some columns already exist. Comment out those ALTER TABLE lines in migration script.

### Issue: "Unknown column in field list"
**Solution**: 
1. Verify migration completed successfully
2. Check if all columns were added: `SHOW COLUMNS FROM contacts;`
3. Restart PHP-FPM/Apache

### Issue: Export button downloads empty CSV
**Solution**:
1. Check event has registrations
2. Verify database query: `SELECT * FROM event_registrations WHERE event_id = [id];`
3. Check PHP error logs

### Issue: WhatsApp links not working
**Solution**:
1. Verify country code is set correctly in config
2. Check phone numbers have correct format in database
3. Test with different phone number formats

### Issue: Emails not sending
**Solution**:
1. Check SMTP configuration in config table
2. Verify PHP mail() is enabled: `php -i | grep mail`
3. Consider upgrading to PHPMailer for better reliability
4. Check email server logs

### Issue: Courtesy validation not working
**Solution**:
1. Verify stored procedure exists: `SHOW PROCEDURE STATUS WHERE Name = 'sp_validate_courtesy_ticket';`
2. Check membership type is eligible
3. Verify contact is an active affiliate

---

## Rollback Procedure

### If Critical Issues Occur:

#### 1. Restore Database
```bash
mysql -u [usuario] -p enlaceca_total < backups/enlaceca_total_[fecha].sql
```

#### 2. Revert Code
```bash
git log --oneline  # Find commit before migration
git checkout [commit-hash]
git push origin main --force  # Only if necessary
```

#### 3. Restart Services
```bash
sudo service apache2 restart
```

---

## Success Criteria

- ✅ All migration SQL statements executed successfully
- ✅ New columns visible in contacts table
- ✅ New membership types created
- ✅ 3 new buttons visible in event detail page
- ✅ CSV export downloads successfully
- ✅ WhatsApp links generate correctly
- ✅ Emails send successfully
- ✅ No errors in application logs
- ✅ No duplicate registration codes
- ✅ Courtesy validation working correctly

---

## Post-Deployment Monitoring (First 24 Hours)

### Monitor:
- [ ] Error logs every 2 hours
- [ ] Database performance (slow query log)
- [ ] User feedback and reports
- [ ] Export/messaging feature usage

### Metrics to Track:
- Number of exports performed
- Number of WhatsApp messages sent
- Number of emails sent
- Any courtesy ticket validation errors
- Page load times for events

---

## Support Contacts

**Development Team**: [contact info]
**Database Admin**: [contact info]
**System Admin**: [contact info]

---

## Sign-Off

- [ ] Database backup completed and verified
- [ ] Migration executed successfully on test environment
- [ ] Migration executed successfully on production
- [ ] Code deployed and verified
- [ ] All features tested and working
- [ ] Monitoring in place
- [ ] Documentation updated

**Deployed by**: ___________________
**Date/Time**: ___________________
**Verified by**: ___________________

---

## Notes

(Add any deployment-specific notes here)

