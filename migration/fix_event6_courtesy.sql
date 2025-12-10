-- ============================================================================
-- FIX EVENT 6 COURTESY TICKETS FOR MEMBERS
-- ============================================================================
-- Members (except SIEM) who registered for Event 6 were incorrectly charged
-- because the system wasn't ready when event opened. This fixes their status
-- to courtesy (free tickets) as per business rules.
--
-- Expected: 16 rows affected
-- - 12 BÁSICA members
-- - 3 PYME members  
-- - 1 PREMIER member
-- ============================================================================

-- Step 1: View current status BEFORE update
SELECT 
    COUNT(*) as total_to_fix,
    mt.name as membership_type,
    er.payment_status,
    er.is_courtesy_ticket
FROM event_registrations er
INNER JOIN contacts c ON er.contact_id = c.id
INNER JOIN membership_types mt ON c.membership_type_id = mt.id
WHERE er.event_id = 6 
  AND c.membership_type_id != 19  -- Exclude SIEM
  AND (er.payment_status IN ('pending', 'free') OR er.is_courtesy_ticket = 0)
GROUP BY mt.name, er.payment_status, er.is_courtesy_ticket;

-- Expected output:
-- 12 | Membresía Básica | pending | 0
-- 1  | Membresía PREMIER | pending | 0
-- 3  | Membresía PYME | pending | 0
-- 3  | Membresía Básica | free | 0
-- 1  | Membresía PREMIER | free | 0


-- Step 2: UPDATE to courtesy status (ALL 20 members)
UPDATE event_registrations er
INNER JOIN contacts c ON er.contact_id = c.id
SET 
    er.payment_status = 'courtesy',
    er.is_courtesy_ticket = 1,
    er.total_amount = 0.00
WHERE er.event_id = 6 
  AND c.membership_type_id != 19  -- Exclude SIEM (they must pay)
  AND er.payment_status IN ('pending', 'free');  -- Fix both pending and free

-- Expected: 20 rows affected


-- Step 3: Verify the update
SELECT 
    er.id,
    er.registration_code,
    er.guest_name,
    er.payment_status,
    er.total_amount,
    er.is_courtesy_ticket,
    c.business_name,
    mt.name as membership_type
FROM event_registrations er
INNER JOIN contacts c ON er.contact_id = c.id
INNER JOIN membership_types mt ON c.membership_type_id = mt.id
WHERE er.event_id = 6 
  AND c.membership_type_id != 19
  AND er.payment_status = 'courtesy'
ORDER BY mt.name, er.guest_name;

-- Should show 16 records all with:
-- payment_status = 'courtesy'
-- total_amount = 0.00
-- is_courtesy_ticket = 1


-- Step 4: Summary report
SELECT 
    'TOTAL' as category,
    COUNT(*) as count,
    SUM(CASE WHEN payment_status = 'courtesy' THEN 1 ELSE 0 END) as courtesy_tickets,
    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as still_pending
FROM event_registrations er
INNER JOIN contacts c ON er.contact_id = c.id
WHERE er.event_id = 6 AND c.membership_type_id != 19

UNION ALL

SELECT 
    mt.name as category,
    COUNT(*) as count,
    SUM(CASE WHEN payment_status = 'courtesy' THEN 1 ELSE 0 END) as courtesy_tickets,
    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as still_pending
FROM event_registrations er
INNER JOIN contacts c ON er.contact_id = c.id
INNER JOIN membership_types mt ON c.membership_type_id = mt.id
WHERE er.event_id = 6 AND c.membership_type_id != 19
GROUP BY mt.name
ORDER BY category;

-- Expected final result:
-- All 16 members should show courtesy_tickets=count, still_pending=0
