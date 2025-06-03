<?php
// === COPIADO EXACTAMENTE DE nueva_ruta.php ===
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

if(!hasPerm([2,4], $user->data()->id)) {
    Session::flash('error', 'Acceso denegado');
    Redirect::to('index.php');
}

// Configuración de directorios - EXACTAMENTE IGUAL QUE nueva_ruta.php
$upload_image_dir = $abs_us_root.$us_url_root.'images/rutas/';
$upload_gpx_dir = $abs_us_root.$us_url_root.'gpx/';

// Crear estructura de directorios GPX si no existen - EXACTAMENTE IGUAL
$gpx_subdirs = ['base', 'extras'];
foreach ($gpx_subdirs as $subdir) {
    $dir = $upload_gpx_dir . $subdir;
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}
// === FIN COPIA EXACTA ===

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Comparación: Debug vs nueva_ruta.php</h2>";
echo "<p>Este script usa <strong>exactamente las mismas variables</strong> que nueva_ruta.php</p>";

// Mostrar variables exactas
echo "<div style='background:#e3f2fd; padding:15px; margin:10px 0; border-radius:5px;'>";
echo "<h3>📁 Variables EXACTAS de nueva_ruta.php</h3>";
echo "<strong>\$abs_us_root:</strong> '" . ($abs_us_root ?? 'NO DEFINIDO') . "'<br>";
echo "<strong>\$us_url_root:</strong> '" . ($us_url_root ?? 'NO DEFINIDO') . "'<br>";
echo "<strong>\$upload_image_dir:</strong> '" . $upload_image_dir . "'<br>";
echo "<strong>\$upload_gpx_dir:</strong> '" . $upload_gpx_dir . "'<br>";
echo "<strong>Directorio absoluto GPX:</strong> " . realpath($upload_gpx_dir) . "<br>";
echo "<strong>Directorio base existe:</strong> " . (file_exists($upload_gpx_dir . 'base/') ? '✅ SÍ' : '❌ NO') . "<br>";
echo "<strong>Directorio base escribible:</strong> " . (is_writable($upload_gpx_dir . 'base/') ? '✅ SÍ' : '❌ NO') . "<br>";
echo "</div>";

// Simular exactamente el proceso de nueva_ruta.php
if(isset($_POST['test_nueva_ruta'])) {
    echo "<div style='background:#d4edda; padding:15px; margin:10px 0; border-radius:5px;'>";
    echo "<h3>🔄 Simulando EXACTAMENTE el proceso de nueva_ruta.php</h3>";
    
    // Simular Input::get('nombre') - usa el nombre que introduzcas
    $nombre_ruta = $_POST['nombre_ruta'] ?? 'test_ruta';
    echo "<strong>Nombre ruta:</strong> {$nombre_ruta}<br>";
    
    // EXACTAMENTE LA MISMA LÓGICA que nueva_ruta.php línea por línea:
    echo "<h4>1️⃣ Procesar GPX BASE (lógica EXACTA de nueva_ruta.php):</h4>";
    
    // Generar nombres de archivo base para GPX - IGUAL QUE nueva_ruta.php
    $base_filename = 'ruta_'.strtolower(str_replace(' ','_', $nombre_ruta));
    echo "<strong>base_filename:</strong> {$base_filename}<br>";
    
    // Procesar GPX BASE - EXACTAMENTE IGUAL QUE nueva_ruta.php
    $gpx_path = '';
    
    if(!empty($_FILES['gpx_base']['name']) && $_FILES['gpx_base']['error'] === UPLOAD_ERR_OK) {
        echo "<strong>✅ Archivo GPX recibido correctamente</strong><br>";
        echo "<strong>Nombre original:</strong> " . $_FILES['gpx_base']['name'] . "<br>";
        echo "<strong>Tamaño:</strong> " . $_FILES['gpx_base']['size'] . " bytes<br>";
        
        $file_ext = strtolower(pathinfo($_FILES['gpx_base']['name'], PATHINFO_EXTENSION));
        echo "<strong>Extensión:</strong> {$file_ext}<br>";
        
        if($file_ext === 'gpx') {
            echo "<strong>✅ Extensión válida</strong><br>";
            
            $new_filename = $base_filename . '.gpx';
            $target_path = $upload_gpx_dir . 'base/' . $new_filename;
            
            echo "<strong>new_filename:</strong> {$new_filename}<br>";
            echo "<strong>target_path:</strong> {$target_path}<br>";
            echo "<strong>Directorio destino existe:</strong> " . (file_exists(dirname($target_path)) ? '✅ SÍ' : '❌ NO') . "<br>";
            echo "<strong>Directorio destino escribible:</strong> " . (is_writable(dirname($target_path)) ? '✅ SÍ' : '❌ NO') . "<br>";
            
            // EXACTAMENTE la misma llamada que nueva_ruta.php
            if(move_uploaded_file($_FILES['gpx_base']['tmp_name'], $target_path)) {
                echo "<strong>✅ ÉXITO: move_uploaded_file funcionó</strong><br>";
                echo "<strong>Archivo existe después del move:</strong> " . (file_exists($target_path) ? '✅ SÍ' : '❌ NO') . "<br>";
                
                if(file_exists($target_path)) {
                    echo "<strong>Tamaño final:</strong> " . filesize($target_path) . " bytes<br>";
                    $gpx_path = 'gpx/base/'.$new_filename;
                    echo "<strong>gpx_path para BD:</strong> {$gpx_path}<br>";
                    
                    // Limpiar archivo de prueba después de 5 segundos
                    echo "<strong>🧹 Archivo de prueba se eliminará automáticamente</strong><br>";
                    echo "<script>setTimeout(function(){ 
                        fetch('?cleanup=" . urlencode($target_path) . "');
                    }, 5000);</script>";
                } else {
                    echo "<strong>❌ ERROR: move_uploaded_file devolvió TRUE pero el archivo no existe</strong><br>";
                }
            } else {
                echo "<strong>❌ ERROR: move_uploaded_file devolvió FALSE</strong><br>";
                
                // Debug adicional del error
                echo "<h5>🔍 Debug del error move_uploaded_file:</h5>";
                echo "<strong>Archivo temporal existe:</strong> " . (file_exists($_FILES['gpx_base']['tmp_name']) ? '✅ SÍ' : '❌ NO') . "<br>";
                echo "<strong>Archivo temporal es readable:</strong> " . (is_readable($_FILES['gpx_base']['tmp_name']) ? '✅ SÍ' : '❌ NO') . "<br>";
                echo "<strong>Directorio padre existe:</strong> " . (file_exists(dirname($target_path)) ? '✅ SÍ' : '❌ NO') . "<br>";
                echo "<strong>Directorio padre escribible:</strong> " . (is_writable(dirname($target_path)) ? '✅ SÍ' : '❌ NO') . "<br>";
                
                // Intentar crear archivo de prueba
                $test_content = "test";
                if(file_put_contents($target_path, $test_content)) {
                    echo "<strong>✅ SÍ puedo escribir archivos manualmente en el directorio</strong><br>";
                    unlink($target_path);
                } else {
                    echo "<strong>❌ NO puedo escribir archivos manualmente en el directorio</strong><br>";
                }
            }
        } else {
            echo "<strong>❌ ERROR: Extensión inválida</strong><br>";
        }
    } else {
        echo "<strong>❌ ERROR en la subida del archivo</strong><br>";
        if(isset($_FILES['gpx_base'])) {
            echo "<strong>Error code:</strong> " . $_FILES['gpx_base']['error'] . "<br>";
            echo "<strong>Error description:</strong> ";
            switch($_FILES['gpx_base']['error']) {
                case UPLOAD_ERR_INI_SIZE: echo "El archivo excede upload_max_filesize"; break;
                case UPLOAD_ERR_FORM_SIZE: echo "El archivo excede MAX_FILE_SIZE"; break;
                case UPLOAD_ERR_PARTIAL: echo "El archivo se subió parcialmente"; break;
                case UPLOAD_ERR_NO_FILE: echo "No se subió ningún archivo"; break;
                case UPLOAD_ERR_NO_TMP_DIR: echo "Falta directorio temporal"; break;
                case UPLOAD_ERR_CANT_WRITE: echo "Error escribiendo al disco"; break;
                default: echo "Error desconocido";
            }
            echo "<br>";
        } else {
            echo "<strong>_FILES['gpx_base'] no existe</strong><br>";
        }
    }
    
    echo "</div>";
}

// Cleanup automático
if(isset($_GET['cleanup']) && file_exists($_GET['cleanup'])) {
    unlink($_GET['cleanup']);
    echo "<script>console.log('Archivo de prueba eliminado: " . $_GET['cleanup'] . "');</script>";
}

// Formulario que simula exactamente nueva_ruta.php
echo "<div style='background:#d1ecf1; padding:15px; margin:10px 0; border-radius:5px;'>";
echo "<h3>🧪 Simular EXACTAMENTE nueva_ruta.php</h3>";
echo "<p>Este formulario usa la misma lógica paso a paso que nueva_ruta.php:</p>";
echo "<form method='post' enctype='multipart/form-data'>";
echo "<label>Nombre de la ruta:</label><br>";
echo "<input type='text' name='nombre_ruta' value='test_ruta_" . date('His') . "' style='margin:5px 0; padding:5px; width:300px;'><br>";
echo "<label>Archivo GPX Base:</label><br>";
echo "<input type='file' name='gpx_base' accept='.gpx' required style='margin:10px 0;'><br>";
echo "<button type='submit' name='test_nueva_ruta' style='background:#17a2b8; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;'>";
echo "🚀 Simular Proceso Completo";
echo "</button>";
echo "</form>";
echo "</div>";

// Mostrar archivos en base/
echo "<div style='background:#fff3cd; padding:15px; margin:10px 0; border-radius:5px;'>";
echo "<h3>📋 Archivos en /gpx/base/ (actualizado en tiempo real)</h3>";
$archivos_base = glob($upload_gpx_dir . 'base/*.gpx');
if(empty($archivos_base)) {
    echo "<p>No hay archivos .gpx en la carpeta base</p>";
} else {
    echo "<table border='1' style='border-collapse:collapse; width:100%;'>";
    echo "<tr style='background:#6c757d; color:white;'><th style='padding:8px;'>Archivo</th><th style='padding:8px;'>Tamaño</th><th style='padding:8px;'>Fecha</th></tr>";
    foreach($archivos_base as $archivo) {
        $filename = basename($archivo);
        $size = filesize($archivo);
        $fecha = date('Y-m-d H:i:s', filemtime($archivo));
        echo "<tr><td style='padding:8px;'>{$filename}</td><td style='padding:8px;'>{$size} bytes</td><td style='padding:8px;'>{$fecha}</td></tr>";
    }
    echo "</table>";
}
echo "</div>";

echo "<div style='text-align:center; margin:20px 0;'>";
echo "<a href='nueva_ruta.php' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>← Volver a Gestión de Rutas</a>";
echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { font-size: 12px; }
th, td { text-align: left; vertical-align: top; }
</style>