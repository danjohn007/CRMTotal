<?php
/**
 * =====================================================
 * PASO 3: Script de migración de contactos desde CSV
 * =====================================================
 * Descripción: Lee el archivo CSV y migra todos los
 * contactos a la tabla contacts con las transformaciones
 * necesarias
 * =====================================================
 */

// Configuración
require_once __DIR__ . '/../config/database.php';

// Ruta del archivo CSV
$csvFile = __DIR__ . '/../CONCENTRADO GLOBAL.csv';

// Verificar que el archivo existe
if (!file_exists($csvFile)) {
    die("ERROR: No se encuentra el archivo CSV: $csvFile\n");
}

// Mapeo de vendedores
$vendedoresMap = [
    'MNAVA' => 17,
    'LGONZALEZ' => 11,
    'MORTEGA' => 12,
    'JPEREZ' => 9,
    'ARUIZ' => 23,
    'SITURBE' => 13,
    'IRUIZ' => 14,
    'MVIZCAYA' => 19,
    'NESTRADA' => 18,
    'CANACO' => 21,
    'CAMARA' => 20,
    'BBARRON' => 22,
    'MCORDOBA' => 6,
    'MSOLIS' => 27,
];

// Mapeo de meses
$mesesMap = [
    'ENE' => 1, 'FEB' => 2, 'MAR' => 3, 'ABR' => 4,
    'MAY' => 5, 'JUN' => 6, 'JUL' => 7, 'AGO' => 8,
    'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DIC' => 12
];

/**
 * Determinar membership_type_id por precio
 */
function determinarMembershipTypeId($precio) {
    $precio = (float) $precio;
    
    if ($precio <= 1550) {
        return 19; // SIEM (cambió de 18 a 19)
    } elseif ($precio >= 1551 && $precio <= 4999) {
        return 1; // BASICA
    } elseif ($precio >= 5000 && $precio <= 9999) {
        return 2; // PYME
    } elseif ($precio >= 10000 && $precio <= 20999) {
        return 3; // PREMIER
    } else {
        return 6; // VISIONARIO/PATROCINADOR
    }
}

/**
 * Determinar affiliation_type por precio
 */
function determinarAffiliationType($precio) {
    $precio = (float) $precio;
    return ($precio <= 1550) ? 'SIEM' : 'MEMBRESIA';
}

/**
 * Determinar contact_type por precio
 */
function determinarContactType($precio) {
    $precio = (float) $precio;
    return ($precio <= 1550) ? 'siem' : 'afiliado';
}

/**
 * Convertir fecha de formato Excel a SQL
 */
function convertirFecha($fecha) {
    if (empty($fecha) || $fecha == 'N') return null;
    
    // Formato: 5/1/2026 → 2026-01-05
    $partes = explode('/', $fecha);
    if (count($partes) == 3) {
        list($mes, $dia, $anio) = $partes;
        return sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
    }
    return null;
}

/**
 * Limpiar precio
 */
function limpiarPrecio($precio) {
    if (empty($precio) || $precio == 'N') return 0.00;
    $precio = str_replace(['$', ','], '', $precio);
    return (float) $precio;
}

/**
 * Determinar tipo de persona por RFC
 */
function determinarTipoPersona($rfc) {
    if (empty($rfc) || $rfc == 'N') return null;
    $len = strlen($rfc);
    return ($len == 13) ? 'fisica' : (($len == 12) ? 'moral' : null);
}

/**
 * Limpiar valor
 */
function limpiarValor($valor) {
    if ($valor === 'N' || $valor === '') return null;
    return trim($valor);
}

// Conectar a la base de datos
try {
    $db = Database::getInstance()->getConnection();
    
    echo "====================================\n";
    echo "INICIANDO MIGRACIÓN DE CONTACTOS\n";
    echo "====================================\n\n";
    
    // Abrir archivo CSV
    $file = fopen($csvFile, 'r');
    if (!$file) {
        die("ERROR: No se puede abrir el archivo CSV\n");
    }
    
    // Configurar para UTF-8
    $bom = fread($file, 3);
    if ($bom !== "\xEF\xBB\xBF") {
        rewind($file);
    }
    
    // Saltar las primeras 8 líneas (encabezados y totales)
    for ($i = 0; $i < 9; $i++) {
        fgetcsv($file);
    }
    
    // Preparar statement
    $stmt = $db->prepare("
        INSERT INTO contacts (
            registration_number, renewal_date, receipt_date, receipt_number, invoice_number,
            csf_file, sticker, amount, payment_method, reaffiliation, is_new,
            affiliation_type, membership_type_id, renewal_month, trade_name, business_sector,
            description, niza_classification, sales_contact, purchase_contact, branch_count,
            rfc, person_type, whatsapp, contact_type, business_name, commercial_name,
            owner_name, legal_representative, corporate_email, phone, industry,
            products_sells, products_buys, commercial_address, fiscal_address, city, state,
            website, assigned_affiliate_id, source_channel, created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, NOW(), NOW()
        )
    ");
    
    $insertados = 0;
    $errores = 0;
    $linea = 10; // Empezamos en línea 10 (después de encabezados)
    
    while (($data = fgetcsv($file)) !== false) {
        $linea++;
        
        // Saltar líneas vacías
        if (empty($data[9])) continue; // business_name está en columna 9
        
        try {
            // Extraer datos del CSV
            $registrationNumber = limpiarValor($data[2]);
            $renewalDate = convertirFecha(limpiarValor($data[3]));
            $receiptDate = convertirFecha(limpiarValor($data[4]));
            $receiptNumber = limpiarValor($data[5]);
            $invoiceNumber = limpiarValor($data[6]);
            $csfFile = limpiarValor($data[7]);
            $sticker = limpiarValor($data[8]);
            $businessName = limpiarValor($data[9]);
            $amount = limpiarPrecio($data[12]);
            $paymentMethod = limpiarValor($data[13]);
            $actualizacion = limpiarValor($data[14]) == '1' ? 1 : 0;
            $nueva = limpiarValor($data[15]) == '1' ? 1 : 0;
            $vendedor = limpiarValor($data[16]);
            $tipoAfiliacion = limpiarValor($data[17]);
            $tipoMembresia = limpiarValor($data[18]);
            $renewalMonth = isset($mesesMap[limpiarValor($data[21])]) ? $mesesMap[limpiarValor($data[21])] : null;
            $rfc = limpiarValor($data[22]);
            $email = limpiarValor($data[23]);
            $whatsapp = limpiarValor($data[24]);
            $phone = limpiarValor($data[25]);
            $contacto = limpiarValor($data[26]);
            $nombreComercial = limpiarValor($data[27]);
            $representante = limpiarValor($data[28]);
            $giro = limpiarValor($data[29]);
            $descripcion = limpiarValor($data[30]);
            $direccionComercial = limpiarValor($data[33]);
            $direccionFiscal = limpiarValor($data[20]);
            $clasificacionNiza = limpiarValor($data[35]);
            $contactoVentas = limpiarValor($data[37]);
            $productsSells = limpiarValor($data[38]);
            $contactoCompras = limpiarValor($data[39]);
            $productsBuys = limpiarValor($data[40]);
            $numeroSucursales = limpiarValor($data[41]) ? (int)limpiarValor($data[41]) : 0;
            $website = limpiarValor($data[42]);
            $contactType = limpiarValor($data[43]);
            
            // Determinar campos calculados
            $membershipTypeId = determinarMembershipTypeId($amount);
            $affiliationType = determinarAffiliationType($amount);
            $contactTypeCalculado = determinarContactType($amount);
            $personType = determinarTipoPersona($rfc);
            $assignedAffiliateId = isset($vendedoresMap[$vendedor]) ? $vendedoresMap[$vendedor] : null;
            
            // Convertir productos a JSON si existen
            $productsSellsJson = $productsSells ? json_encode([$productsSells]) : null;
            $productsBuysJson = $productsBuys ? json_encode([$productsBuys]) : null;
            
            // Ejecutar INSERT
            $stmt->execute([
                $registrationNumber, $renewalDate, $receiptDate, $receiptNumber, $invoiceNumber,
                $csfFile, $sticker, $amount, $paymentMethod, $actualizacion, $nueva,
                $affiliationType, $membershipTypeId, $renewalMonth, $nombreComercial, $giro,
                $descripcion, $clasificacionNiza, $contactoVentas, $contactoCompras, $numeroSucursales,
                $rfc, $personType, $whatsapp, $contactTypeCalculado, $businessName, $nombreComercial,
                $contacto, $representante, $email, $phone, $giro,
                $productsSellsJson, $productsBuysJson, $direccionComercial, $direccionFiscal, 'Santiago de Querétaro', 'Querétaro',
                $website, $assignedAffiliateId, 'alta_directa'
            ]);
            
            $insertados++;
            
            // Mostrar progreso cada 100 registros
            if ($insertados % 100 == 0) {
                echo "Insertados: $insertados registros...\n";
            }
            
        } catch (Exception $e) {
            $errores++;
            echo "ERROR en línea $linea: " . $e->getMessage() . "\n";
            
            // Continuar con el siguiente registro
            continue;
        }
    }
    
    fclose($file);
    
    echo "\n====================================\n";
    echo "MIGRACIÓN COMPLETADA\n";
    echo "====================================\n";
    echo "Total insertados: $insertados\n";
    echo "Total errores: $errores\n";
    echo "====================================\n\n";
    
    // Mostrar estadísticas
    $result = $db->query("
        SELECT 
            affiliation_type,
            COUNT(*) as cantidad,
            SUM(amount) as total_importe
        FROM contacts 
        GROUP BY affiliation_type
    ");
    
    echo "ESTADÍSTICAS POR TIPO:\n";
    echo "----------------------\n";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf(
            "%-15s: %5d registros - $%s\n",
            $row['affiliation_type'],
            $row['cantidad'],
            number_format($row['total_importe'], 2)
        );
    }
    
    echo "\n¡LISTO! Ahora ejecuta: 04_reconnect_registrations.sql\n";
    
} catch (Exception $e) {
    die("ERROR FATAL: " . $e->getMessage() . "\n");
}
