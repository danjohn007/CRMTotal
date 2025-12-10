<?php
/**
 * Migración de Contactos - Versión Web
 * Ejecutar desde navegador: http://tu-dominio.com/migrate_contacts_web.php
 */

set_time_limit(0); // Sin límite de tiempo
ini_set('memory_limit', '512M'); // Aumentar memoria

// Configuración de base de datos
require_once __DIR__ . '/../config/database.php';

// Conectar a la base de datos
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Conexión a base de datos exitosa<br><br>";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

// Ruta del CSV
$csvPath = __DIR__ . '/CONCENTRADO GLOBAL.csv';

if (!file_exists($csvPath)) {
    die("❌ No se encuentra el archivo: " . $csvPath);
}

echo "📁 Archivo CSV encontrado<br>";
echo "📊 Iniciando migración...<br><br>";
flush();

// Mapeo de vendedores (código Excel -> user_id)
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
    'MSOLIS' => 27
];

// Función para convertir fecha de Excel a MySQL
function convertirFecha($fechaExcel) {
    if (empty($fechaExcel)) return null;
    
    // Formato esperado: "5/1/2026" -> "2026-01-05"
    $partes = explode('/', $fechaExcel);
    if (count($partes) === 3) {
        $mes = str_pad($partes[0], 2, '0', STR_PAD_LEFT);
        $dia = str_pad($partes[1], 2, '0', STR_PAD_LEFT);
        $anio = $partes[2];
        return "$anio-$mes-$dia";
    }
    return null;
}

// Función para convertir precio "$3,800.00" -> 3800.00
function convertirPrecio($precio) {
    if (empty($precio)) return 0;
    $precio = str_replace(['$', ','], '', $precio);
    return floatval($precio);
}

// Función para convertir mes a número
function convertirMes($mes) {
    $meses = [
        'ENE' => 1, 'FEB' => 2, 'MAR' => 3, 'ABR' => 4,
        'MAY' => 5, 'JUN' => 6, 'JUL' => 7, 'AGO' => 8,
        'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DIC' => 12
    ];
    return $meses[strtoupper($mes)] ?? null;
}

// Función para determinar membership_type_id según precio
function determinarMembershipTypeId($monto) {
    if ($monto <= 1550) return 19; // SIEM
    if ($monto <= 4999) return 1;  // BASICA
    if ($monto <= 9999) return 2;  // PYME
    if ($monto <= 20999) return 3; // PREMIER
    return 6; // VISIONARIO (>= 21000)
}

// Abrir archivo CSV
$handle = fopen($csvPath, 'r');
if (!$handle) {
    die("❌ No se pudo abrir el archivo CSV");
}

// Saltar las primeras 9 líneas (encabezados y estadísticas)
for ($i = 0; $i < 9; $i++) {
    fgetcsv($handle, 0, ',');
}

echo "📋 Archivo preparado, iniciando lectura de datos...<br><br>";
flush();

// Preparar INSERT
$sql = "INSERT INTO contacts (
    registration_number, renewal_date, membership_type_id, affiliation_type,
    assigned_affiliate_id, business_name, owner_name, rfc, 
    commercial_address, city, state, postal_code, 
    phone, whatsapp, corporate_email, industry, 
    renewal_month, payment_method, invoice_number,
    amount, person_type, seller,
    created_at, updated_at
) VALUES (
    :registration_number, :renewal_date, :membership_type_id, :affiliation_type,
    :assigned_affiliate_id, :business_name, :owner_name, :rfc,
    :commercial_address, :city, :state, :postal_code,
    :phone, :whatsapp, :corporate_email, :industry,
    :renewal_month, :payment_method, :invoice_number,
    :amount, :person_type, :seller,
    NOW(), NOW()
)";

$stmt = $pdo->prepare($sql);

$insertados = 0;
$errores = 0;
$linea = 1;

// Procesar cada fila
while (($fila = fgetcsv($handle, 0, ',')) !== false) {
    $linea++;
    
    try {
        // Mapeo según el CSV real (ver línea 9 del archivo):
        // Columnas: 1=REGISTRO, 2=No.Mes, 3=FECHA RENOVACION, 4=FECHA RECIBO, 5=RECIBO, 6=FACTURA, 7=CSF, 8=ENGOMADO,
        // 9=Razón social, 12=IMPORTE, 13=METODO PAGO, 16=VENDEDOR, 17=TIPO AFILIACIÓN, 18=TIPO MEMBRESÍA,
        // 20=DIRECCIÓN FISCAL, 21=MES RENOVACIÓN, 22=RFC, 23=EMAIL, 24=WHATSAPP, 25=TELEFONO, 26=CONTACTO
        
        $numeroRegistro = trim($fila[1] ?? ''); // No. Mes
        $fechaRenovacion = convertirFecha($fila[3] ?? ''); // FECHA RENOVACION
        $razonSocial = trim($fila[9] ?? ''); // Razón social
        $montoStr = trim($fila[12] ?? ''); // IMPORTE
        $metodoPago = trim($fila[13] ?? ''); // METODO DE PAGO
        $vendedorCodigo = trim($fila[16] ?? ''); // VENDEDOR
        $direccion = trim($fila[20] ?? ''); // DIRECCIÓN FISCAL
        $mesRenovacionStr = trim($fila[21] ?? ''); // MES DE RENOVACIÓN
        $rfc = strtoupper(trim($fila[22] ?? '')); // RFC
        $correo = strtolower(trim($fila[23] ?? '')); // EMAIL
        $celular = trim($fila[24] ?? ''); // WHATSAPP
        $telefono = trim($fila[25] ?? ''); // TELEFONO
        $nombreContacto = trim($fila[26] ?? ''); // CONTACTO
        $giro = trim($fila[29] ?? ''); // GIRO
        
        $mesRegistro = convertirMes($mesRenovacionStr);
        $ciudad = 'Santiago de Querétaro';
        $estado = 'Querétaro';
        $cp = null;
        $colonia = null;
        
        // Convertir monto y determinar tipo de membresía
        $monto = convertirPrecio($montoStr);
        $membershipTypeId = determinarMembershipTypeId($monto);
        
        // Obtener ID del vendedor
        $assignedAffiliateId = $vendedoresMap[$vendedorCodigo] ?? null;
        
        // Determinar tipo de persona por longitud de RFC
        $personType = (strlen($rfc) === 12) ? 'moral' : 'fisica';
        
        // Ejecutar INSERT
        $stmt->execute([
            ':registration_number' => $numeroRegistro ?: null,
            ':renewal_date' => $fechaRenovacion,
            ':membership_type_id' => $membershipTypeId,
            ':affiliation_type' => 'socio',
            ':assigned_affiliate_id' => $assignedAffiliateId,
            ':business_name' => $razonSocial ?: null,
            ':owner_name' => $nombreContacto ?: null,
            ':rfc' => $rfc ?: null,
            ':commercial_address' => $direccion ?: null,
            ':city' => $ciudad ?: null,
            ':state' => $estado ?: null,
            ':postal_code' => $cp ?: null,
            ':phone' => $telefono ?: null,
            ':whatsapp' => $celular ?: null,
            ':corporate_email' => $correo ?: null,
            ':industry' => $giro ?: null,
            ':renewal_month' => $mesRegistro,
            ':payment_method' => $metodoPago ?: null,
            ':invoice_number' => trim($fila[6] ?? '') ?: null, // # DE FACTURA
            ':amount' => $monto,
            ':person_type' => $personType,
            ':seller' => $assignedAffiliateId
        ]);
        
        $insertados++;
        
        // Mostrar progreso cada 100 registros
        if ($insertados % 100 === 0) {
            echo "✅ Insertados: $insertados<br>";
            flush();
        }
        
    } catch (Exception $e) {
        $errores++;
        echo "⚠️ Error en línea $linea: " . $e->getMessage() . "<br>";
        flush();
    }
}

fclose($handle);

// Resumen final
echo "<br><hr><br>";
echo "<h2>📊 RESUMEN DE MIGRACIÓN</h2>";
echo "✅ <strong>Registros insertados:</strong> $insertados<br>";
echo "⚠️ <strong>Errores:</strong> $errores<br>";
echo "📝 <strong>Total procesado:</strong> " . ($insertados + $errores) . "<br><br>";

// Estadísticas por membership type
echo "<h3>Distribución por Tipo de Membresía:</h3>";
$stats = $pdo->query("
    SELECT 
        mt.name,
        COUNT(*) as total
    FROM contacts c
    INNER JOIN membership_types mt ON c.membership_type_id = mt.id
    GROUP BY mt.id, mt.name
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($stats as $stat) {
    echo "• {$stat['name']}: {$stat['total']}<br>";
}

echo "<br><h3>✅ Migración completada exitosamente!</h3>";
?>
