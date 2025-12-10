<?php
/**
 * =====================================================
 * PASO 5: Verificar resultado de la migración
 * =====================================================
 * Descripción: Script para verificar que todo salió
 * correctamente y generar reporte
 * =====================================================
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "====================================\n";
    echo "REPORTE DE VERIFICACIÓN DE MIGRACIÓN\n";
    echo "====================================\n\n";
    
    // 1. Estadísticas generales de contacts
    echo "1. ESTADÍSTICAS DE CONTACTS:\n";
    echo "----------------------------\n";
    
    $result = $db->query("
        SELECT 
            'Total contactos migrados' as descripcion,
            COUNT(*) as cantidad
        FROM contacts
        UNION ALL
        SELECT 
            'Contactos tipo SIEM',
            COUNT(*)
        FROM contacts
        WHERE affiliation_type = 'SIEM'
        UNION ALL
        SELECT 
            'Contactos tipo MEMBRESIA',
            COUNT(*)
        FROM contacts
        WHERE affiliation_type = 'MEMBRESIA'
    ");
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-35s: %d\n", $row['descripcion'], $row['cantidad']);
    }
    
    echo "\n";
    
    // 2. Distribución por tipo de membresía
    echo "2. DISTRIBUCIÓN POR TIPO DE MEMBRESÍA:\n";
    echo "--------------------------------------\n";
    
    $result = $db->query("
        SELECT 
            mt.name as tipo_membresia,
            COUNT(c.id) as cantidad,
            SUM(c.amount) as total_importe
        FROM contacts c
        LEFT JOIN membership_types mt ON c.membership_type_id = mt.id
        GROUP BY c.membership_type_id, mt.name
        ORDER BY cantidad DESC
    ");
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf(
            "%-25s: %5d contactos - $%s\n",
            $row['tipo_membresia'] ?? 'SIN TIPO',
            $row['cantidad'],
            number_format($row['total_importe'], 2)
        );
    }
    
    echo "\n";
    
    // 3. Distribución por vendedor
    echo "3. DISTRIBUCIÓN POR VENDEDOR:\n";
    echo "----------------------------\n";
    
    $result = $db->query("
        SELECT 
            u.name as vendedor,
            COUNT(c.id) as cantidad,
            SUM(c.amount) as total_ventas
        FROM contacts c
        LEFT JOIN users u ON c.assigned_affiliate_id = u.id
        GROUP BY c.assigned_affiliate_id, u.name
        ORDER BY cantidad DESC
    ");
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf(
            "%-15s: %5d ventas - $%s\n",
            $row['vendedor'] ?? 'SIN ASIGNAR',
            $row['cantidad'],
            number_format($row['total_ventas'], 2)
        );
    }
    
    echo "\n";
    
    // 4. Estadísticas de boletos
    echo "4. ESTADÍSTICAS DE BOLETOS:\n";
    echo "---------------------------\n";
    
    $result = $db->query("
        SELECT 
            'Total boletos en el sistema' as descripcion,
            COUNT(*) as cantidad
        FROM event_registrations
        UNION ALL
        SELECT 
            'Boletos CON contacto vinculado',
            COUNT(*)
        FROM event_registrations
        WHERE contact_id IS NOT NULL
        UNION ALL
        SELECT 
            'Boletos SIN contacto (huérfanos)',
            COUNT(*)
        FROM event_registrations
        WHERE contact_id IS NULL
    ");
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-35s: %d\n", $row['descripcion'], $row['cantidad']);
    }
    
    echo "\n";
    
    // 5. Verificar si existe tabla temporal
    $result = $db->query("SHOW TABLES LIKE 'temp_contact_registrations'");
    $existeTemp = $result->rowCount() > 0;
    
    if ($existeTemp) {
        echo "5. VERIFICACIÓN DE RECONEXIÓN:\n";
        echo "------------------------------\n";
        
        $result = $db->query("
            SELECT 
                COUNT(*) as total_en_backup,
                SUM(CASE WHEN er.contact_id IS NOT NULL THEN 1 ELSE 0 END) as reconectados,
                SUM(CASE WHEN er.contact_id IS NULL THEN 1 ELSE 0 END) as sin_reconectar
            FROM temp_contact_registrations tcr
            LEFT JOIN event_registrations er ON tcr.registration_id = er.id
        ");
        
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $porcentaje = $row['total_en_backup'] > 0 
            ? ($row['reconectados'] / $row['total_en_backup']) * 100 
            : 0;
        
        echo "Total en backup:      {$row['total_en_backup']}\n";
        echo "Reconectados:         {$row['reconectados']}\n";
        echo "Sin reconectar:       {$row['sin_reconectar']}\n";
        echo sprintf("Tasa de éxito:        %.2f%%\n", $porcentaje);
        
        if ($row['sin_reconectar'] > 0) {
            echo "\n⚠️  HAY BOLETOS SIN RECONECTAR\n";
            echo "    Ejecuta esta consulta para ver detalles:\n";
            echo "    SELECT * FROM temp_contact_registrations tcr\n";
            echo "    LEFT JOIN event_registrations er ON tcr.registration_id = er.id\n";
            echo "    WHERE er.contact_id IS NULL;\n";
        } else {
            echo "\n✓ TODOS LOS BOLETOS FUERON RECONECTADOS EXITOSAMENTE\n";
            echo "  Puedes ejecutar: DROP TABLE temp_contact_registrations;\n";
        }
    } else {
        echo "5. Tabla temporal ya fue eliminada\n";
    }
    
    echo "\n";
    
    // 6. Verificar datos problemáticos
    echo "6. VERIFICACIÓN DE CALIDAD DE DATOS:\n";
    echo "------------------------------------\n";
    
    $result = $db->query("
        SELECT 
            'Contactos sin RFC' as descripcion,
            COUNT(*) as cantidad
        FROM contacts
        WHERE rfc IS NULL OR rfc = ''
        UNION ALL
        SELECT 
            'Contactos sin email',
            COUNT(*)
        FROM contacts
        WHERE corporate_email IS NULL OR corporate_email = ''
        UNION ALL
        SELECT 
            'Contactos sin vendedor asignado',
            COUNT(*)
        FROM contacts
        WHERE assigned_affiliate_id IS NULL
        UNION ALL
        SELECT 
            'Contactos sin tipo de membresía',
            COUNT(*)
        FROM contacts
        WHERE membership_type_id IS NULL
    ");
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-40s: %d\n", $row['descripcion'], $row['cantidad']);
    }
    
    echo "\n====================================\n";
    echo "VERIFICACIÓN COMPLETADA\n";
    echo "====================================\n";
    
} catch (Exception $e) {
    die("ERROR: " . $e->getMessage() . "\n");
}
