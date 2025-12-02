# Implementation Guide: EDA Event Integration

This guide documents the implementation of enhancements to link the Digital Unique File (EDA - Expediente Digital Afiliado) with event ticket generation and comprehensive reporting.

## Overview

This update enhances the event management system to:
1. Link EDA records with event registrations
2. Implement RFC-based validation (Persona Física vs Moral)
3. Track ticket generation, attendance, and no-shows
4. Provide comprehensive event metrics and reporting
5. Add room/venue management capabilities

## Database Migration

### Running the Migration

**IMPORTANT**: Backup your database before running the migration!

```bash
# Backup database
mysqldump -u root -p crm_ccq > backup_$(date +%Y%m%d_%H%M%S).sql

# Run migration
mysql -u root -p crm_ccq < config/update_eda_events.sql
```

### What the Migration Does

1. **Events Table Updates**:
   - Adds `room_name` (VARCHAR 100) - Name of the venue/room
   - Adds `room_capacity` (INT) - Total capacity of the room
   - Adds `allowed_attendees` (INT) - Number of registrations allowed (can differ from capacity)
   - Adds `has_courtesy_tickets` (TINYINT) - Whether paid events offer courtesy tickets
   - Adds promotional pricing fields if not present

2. **Event Registrations Table Updates**:
   - Adds `registration_code` (VARCHAR 50, UNIQUE) - Unique ticket identifier
   - Adds `is_guest` (TINYINT) - Whether registrant is a guest
   - Adds `guest_type` (ENUM) - Type of guest (invitado_empresario, colaborador)
   - Adds `is_owner_representative` (TINYINT) - Whether registrant is owner/legal rep
   - Adds `parent_registration_id` (INT) - Links to inviting/parent registration
   - Adds `attendee_name`, `attendee_phone`, `attendee_email` - Actual attendee info
   - Adds `tickets` (INT) - Number of tickets requested
   - Adds `total_amount` (DECIMAL) - Total payment amount
   - Adds QR code tracking fields
   - Removes deprecated `categoria_asistente` and `attendee_position` columns

3. **Contacts Table Updates**:
   - Adds new contact types: `patrocinador`, `mesa_directiva`, `invitado`, `colaborador_empresa`
   - Differentiates `consejero_propietario` and `consejero_invitado`

4. **Creates Supporting Tables**:
   - `event_categories` - For event categorization
   - `event_type_catalog` - For event type definitions

### Verification

After running the migration, verify the changes:

```sql
-- Check events table structure
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'crm_ccq' AND TABLE_NAME = 'events'
ORDER BY ORDINAL_POSITION;

-- Check event_registrations table structure
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'crm_ccq' AND TABLE_NAME = 'event_registrations'
ORDER BY ORDINAL_POSITION;

-- Verify no duplicate registration codes
SELECT registration_code, COUNT(*) as count 
FROM event_registrations 
WHERE registration_code IS NOT NULL
GROUP BY registration_code 
HAVING count > 1;
```

## Key Features Implemented

### 1. RFC Validation

The registration form now requires RFC as the first field and validates:

- **Persona Moral**: 12 characters (3-4 letters + 6 digits + 3 alphanumeric)
- **Persona Física**: 13 characters (4 letters + 6 digits + 3 alphanumeric)

Real-time validation provides immediate feedback and auto-detects person type.

### 2. Field Order and Auto-Fill

Registration form field order:
1. **RFC** (required, validated)
2. **Razón Social** (business name)
3. **Nombre del Empresario/Representante Legal**
   - For Persona Física: Auto-syncs with Razón Social
   - For Persona Moral: Manual entry required
4. **WhatsApp** (required, 10 digits)
5. **Correo Corporativo** (required)

### 3. Search Enhancements

The lookup/search function now searches by:
- RFC
- WhatsApp
- Email
- **Razón Social (Business Name)** ← NEW

### 4. Event Room Management

Event creation/edit forms now include:
- **Room Name**: Name of the venue/salon
- **Room Capacity**: Total capacity of the room
- **Allowed Attendees**: Number of registrations permitted (may differ from capacity)

This allows differentiation between physical capacity and registration limits.

### 5. Target Audiences

Enhanced audience selection with:
- **TODOS** checkbox to select all options
- Separate categories for:
  - Consejeros Propietarios
  - Consejeros Invitados
  - Patrocinadores
  - Mesa Directiva

### 6. Comprehensive Event Reporting

New `/reportes/eventos` page provides:

#### Overall Metrics
- Total events (paid vs free)
- Total tickets generated
- Total attendance
- No-show rate and percentage

#### Paid Events Breakdown
- Courtesy tickets vs Paid tickets
- Attendance/no-show for each category

#### Top 50 Attending Businesses
- Ranked by attendance count
- Shows RFC, total attendance, events attended
- Breakdown by paid/free events

#### Metrics by Type and Category
- Attendance rates by event type (interno, publico, terceros)
- Attendance rates by event category

### 7. Courtesy Ticket Rules

For paid events with courtesy tickets enabled:
- **Maximum 1 courtesy** per business (identified by RFC/email)
- Only for **active affiliates**
- Only for **owner/legal representative** (`is_owner_representative = 1`)
- Collaborators and guests must pay

Events can be configured to have NO courtesy tickets by setting `has_courtesy_tickets = 0`.

## Usage Guide

### Creating an Event

1. Navigate to **Eventos** → **Crear Evento**
2. Fill in basic information (title, description, dates)
3. **NEW**: Add room information:
   - Room Name: e.g., "Salón Principal"
   - Room Capacity: e.g., 200
   - Allowed Attendees: e.g., 150 (may be less than capacity)
4. Configure pricing (if paid event)
5. **NEW**: Select target audiences using "TODOS" for all or individual selections
6. Save event

### Event Registration Process

1. User enters **RFC first**
2. System validates RFC format and detects person type
3. If found in database, auto-fills information
4. User completes remaining required fields in order:
   - Razón Social
   - Nombre del Empresario/Representante
   - WhatsApp
   - Email
5. For paid events:
   - System checks courtesy eligibility
   - Calculates amount to pay
   - Generates payment link or QR code

### Viewing Event Reports

1. Navigate to **Reportes** → **Eventos**
2. Use filters to:
   - Select specific event
   - Filter by event type
   - Filter by category
3. View comprehensive metrics:
   - Total tickets vs attendance
   - Courtesy vs paid breakdown (for paid events)
   - Top 50 attending businesses
   - Performance by type and category

## API Endpoints

### New/Updated Endpoints

- `GET /reportes/eventos` - Event reports page
- `GET /reportes/eventos?event_id={id}` - Specific event report
- `GET /reportes/eventos?event_type={type}` - Filter by type
- `GET /reportes/eventos?category={cat}` - Filter by category

## Data Integrity Rules

### Enforced by System

1. **Registration Code Uniqueness**: Each `registration_code` is unique
2. **One Courtesy per Business**: For paid events, max 1 courtesy per RFC/email
3. **RFC Format Validation**: Client and server-side validation
4. **WhatsApp Format**: Must be exactly 10 digits
5. **Parent Registration Tracking**: `parent_registration_id` links guests/collaborators to inviter

### Duplicate Prevention

The system prevents duplicate registrations by:
- Unique registration codes
- Validation at form submission
- Database-level unique constraints

## Footer Update

The registration form footer link "Ir al sitio principal" now points to:
```
https://www.camaradecomercioqro.mx/
```

## Testing Checklist

Before deploying to production, verify:

- [ ] Database migration runs successfully
- [ ] RFC validation works for both Persona Física (13 chars) and Moral (12 chars)
- [ ] Auto-fill by RFC works correctly
- [ ] Search by business name (razón social) works
- [ ] Event creation with room fields saves correctly
- [ ] "TODOS" audience selector works
- [ ] Event reports page loads and displays metrics
- [ ] Courtesy ticket limit (1 per business) is enforced
- [ ] Registration code uniqueness is maintained
- [ ] QR codes are generated for free/paid events
- [ ] Footer link points to correct URL

## Rollback Plan

If issues arise after deployment:

1. **Database Rollback**:
```sql
-- Restore from backup
mysql -u root -p crm_ccq < backup_YYYYMMDD_HHMMSS.sql
```

2. **Code Rollback**:
```bash
git revert <commit_hash>
git push origin main
```

3. **Partial Rollback**: If only specific features are problematic:
   - Disable event reports: Remove route in router
   - Revert form changes: Restore previous registration form
   - Keep database changes: They are backward compatible

## Support and Troubleshooting

### Common Issues

**Issue**: RFC validation not working
- **Solution**: Clear browser cache, check JavaScript console for errors

**Issue**: Courtesy ticket not being applied
- **Solution**: Verify:
  - User is active affiliate
  - User selected "Dueño o Representante Legal"
  - No previous courtesy ticket for same RFC/email in this event

**Issue**: Reports showing incorrect data
- **Solution**: Verify migration ran completely, check for data integrity

### Debug Mode

To enable detailed logging for troubleshooting:

```php
// In config/config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Performance Considerations

The new metrics queries use:
- Efficient JOINs and LEFT JOINs
- Aggregation functions (COUNT, SUM)
- Proper indexes on `registration_code`, `attended`, `payment_status`

For large datasets (>10,000 registrations), consider:
- Adding pagination to reports
- Caching frequently-accessed metrics
- Database query optimization

## Security Notes

1. **SQL Injection Prevention**: All queries use PDO prepared statements
2. **Input Sanitization**: RFC, email, and other inputs are sanitized
3. **XSS Prevention**: All output is escaped with `htmlspecialchars()`
4. **CSRF Protection**: Forms include CSRF tokens
5. **RFC Validation**: Pattern matching prevents malformed input

## Future Enhancements

Potential improvements for future versions:

1. **Batch QR Code Generation**: Generate QR codes for all attendees at once
2. **Check-in Mobile App**: Scan QR codes for attendance
3. **Email Templates**: Customizable email templates for confirmations
4. **Advanced Analytics**: Cohort analysis, retention metrics
5. **Export Functionality**: Export reports to Excel/PDF

## Credits

Developed for Cámara de Comercio de Querétaro
- Digital Unique File (EDA) Integration
- Event Management Enhancements
- Comprehensive Reporting System

---

**Version**: 2.9.0  
**Date**: December 2024  
**Migration File**: `config/update_eda_events.sql`
