<?php
/**
 * INSTALADOR AUTOMÁTICO DE FPDF
 * Ejecuta este archivo una vez desde el navegador para instalar FPDF
 */

set_time_limit(300); // 5 minutos

echo "<html><head><meta charset='UTF-8'><style>
body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
.success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 10px 0; }
.error { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0; }
.info { background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; margin: 10px 0; }
.step { padding: 10px; margin: 10px 0; background: #f8f9fa; }
</style></head><body>";

echo "<h1>📦 Instalador de FPDF para Generación de PDFs</h1>";

// Paso 1: Crear directorio libs si no existe
echo "<div class='step'><strong>Paso 1:</strong> Creando directorio libs/</div>";
$libsDir = __DIR__ . '/../libs';
if (!file_exists($libsDir)) {
    if (mkdir($libsDir, 0755, true)) {
        echo "<div class='success'>✅ Directorio libs/ creado</div>";
    } else {
        echo "<div class='error'>❌ Error: No se pudo crear el directorio libs/</div>";
        die("</body></html>");
    }
} else {
    echo "<div class='info'>ℹ️ Directorio libs/ ya existe</div>";
}

// Paso 2: Descargar FPDF
echo "<div class='step'><strong>Paso 2:</strong> Descargando FPDF desde fpdf.org...</div>";
$fpdfZipUrl = 'http://www.fpdf.org/en/download/fpdf185.zip';
$zipFile = $libsDir . '/fpdf.zip';

$fpdfContent = @file_get_contents($fpdfZipUrl);
if ($fpdfContent === false) {
    echo "<div class='error'>❌ Error: No se pudo descargar FPDF. Verifica tu conexión a internet.</div>";
    die("</body></html>");
}

if (file_put_contents($zipFile, $fpdfContent)) {
    echo "<div class='success'>✅ FPDF descargado correctamente (" . number_format(strlen($fpdfContent)) . " bytes)</div>";
} else {
    echo "<div class='error'>❌ Error: No se pudo guardar el archivo ZIP</div>";
    die("</body></html>");
}

// Paso 3: Descomprimir
echo "<div class='step'><strong>Paso 3:</strong> Descomprimiendo archivo...</div>";
$zip = new ZipArchive();
if ($zip->open($zipFile) === TRUE) {
    // Extraer a directorio temporal
    $tempDir = $libsDir . '/temp_extract';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }
    $zip->extractTo($tempDir);
    $zip->close();
    
    // Verificar si fpdf.php está directamente en el directorio temporal
    $fpdfSourceDir = null;
    if (file_exists($tempDir . '/fpdf.php')) {
        // Los archivos se extrajeron directamente sin carpeta contenedora
        $fpdfSourceDir = $tempDir;
    } else {
        // Buscar dentro de subdirectorios
        $items = scandir($tempDir);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..') {
                $itemPath = $tempDir . '/' . $item;
                if (is_dir($itemPath) && file_exists($itemPath . '/fpdf.php')) {
                    $fpdfSourceDir = $itemPath;
                    break;
                }
            }
        }
    }
    
    if ($fpdfSourceDir) {
        // Crear directorio destino
        $targetDir = $libsDir . '/fpdf185';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Copiar todos los archivos
        $files = scandir($fpdfSourceDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $source = $fpdfSourceDir . '/' . $file;
                $dest = $targetDir . '/' . $file;
                if (is_dir($source)) {
                    // Copiar directorio recursivamente
                    copiarDirectorio($source, $dest);
                } else {
                    copy($source, $dest);
                }
            }
        }
        
        // Limpiar temporal
        eliminarDirectorio($tempDir);
        
        echo "<div class='success'>✅ Archivo descomprimido y organizado en libs/fpdf185/</div>";
    } else {
        echo "<div class='error'>❌ Error: No se encontró fpdf.php en el archivo descargado</div>";
        // Mostrar contenido para debug
        echo "<div class='step'>Contenido extraído:</div><ul>";
        $items = scandir($tempDir);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..') {
                echo "<li>" . $item . "</li>";
            }
        }
        echo "</ul>";
        die("</body></html>");
    }
} else {
    echo "<div class='error'>❌ Error: No se pudo descomprimir el archivo ZIP</div>";
    die("</body></html>");
}

// Paso 4: Limpiar archivo ZIP
echo "<div class='step'><strong>Paso 4:</strong> Limpiando archivos temporales...</div>";
if (unlink($zipFile)) {
    echo "<div class='success'>✅ Archivo ZIP eliminado</div>";
}

// Paso 5: Verificar instalación
echo "<div class='step'><strong>Paso 5:</strong> Verificando instalación...</div>";
$fpdfFile = $libsDir . '/fpdf185/fpdf.php';
if (file_exists($fpdfFile)) {
    echo "<div class='success'>✅ FPDF instalado correctamente en: " . $fpdfFile . "</div>";
    echo "<div class='success' style='margin-top: 30px;'>
        <h2>🎉 ¡Instalación Completada!</h2>
        <p>Ahora puedes generar PDFs automáticamente.</p>
        <p><strong>Siguiente paso:</strong></p>
        <ul>
            <li>Ve a: <a href='list_courtesy_emails.php'>list_courtesy_emails.php</a></li>
            <li>Los boletos ahora se descargarán como PDFs reales</li>
        </ul>
        <p style='margin-top: 20px;'><strong>⚠️ IMPORTANTE:</strong> Por seguridad, elimina este archivo instalador después de usarlo.</p>
    </div>";
} else {
    echo "<div class='error'>❌ Error: No se encontró fpdf.php después de la instalación</div>";
    echo "<div class='step'>Buscando archivos instalados...</div>";
    if (is_dir($libsDir)) {
        echo "<ul>";
        $contents = scandir($libsDir);
        foreach ($contents as $item) {
            if ($item !== '.' && $item !== '..') {
                $itemPath = $libsDir . '/' . $item;
                echo "<li>" . $item;
                if (is_dir($itemPath)) {
                    echo " (directorio)<ul>";
                    $subItems = scandir($itemPath);
                    foreach ($subItems as $subItem) {
                        if ($subItem !== '.' && $subItem !== '..') {
                            echo "<li>" . $subItem . "</li>";
                        }
                    }
                    echo "</ul>";
                }
                echo "</li>";
            }
        }
        echo "</ul>";
    }
}

// Funciones auxiliares
function copiarDirectorio($source, $dest) {
    if (!is_dir($dest)) {
        mkdir($dest, 0755, true);
    }
    $items = scandir($source);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..') {
            $s = $source . '/' . $item;
            $d = $dest . '/' . $item;
            if (is_dir($s)) {
                copiarDirectorio($s, $d);
            } else {
                copy($s, $d);
            }
        }
    }
}

function eliminarDirectorio($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..') {
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                eliminarDirectorio($path);
            } else {
                unlink($path);
            }
        }
    }
    rmdir($dir);
}

echo "</body></html>";
?>
