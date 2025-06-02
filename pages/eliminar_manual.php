<?php
// Script independiente para eliminar rutas directamente
require_once '../users/init.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

if(!hasPerm([2,4], $user->data()->id)) {
    die("Acceso denegado");
}

// Habilitar visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminación de Ruta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 30px;
        }
        .process-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 25px;
        }
        .log-item {
            padding: 8px 15px;
            margin-bottom: 8px;
            border-radius: 4px;
            background-color: #f8f9fa;
        }
        .log-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .log-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .log-info {
            background-color: #e2f0fd;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        .log-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        .process-title {
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .process-status {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .process-success {
            background-color: #d4edda;
            color: #155724;
        }
        .process-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .countdown {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="process-container">
        <h2 class="process-title text-center"><i class="fas fa-trash-alt"></i> Eliminación de Ruta</h2>
        <div class="process-log">

<?php
// Obtener ID de la ruta a eliminar
$ruta_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$error_ocurrido = false;

if(!$ruta_id) {
    echo "<div class='log-item log-error'><i class='fas fa-exclamation-triangle'></i> Error: ID de ruta no especificado</div>";
    $error_ocurrido = true;
} else {
    try {
        $db = DB::getInstance();
        
        // 1. Buscar la ruta
        echo "<div class='log-item log-info'><i class='fas fa-search'></i> Buscando ruta con ID: $ruta_id</div>";
        $ruta = $db->query("SELECT * FROM aa_rutas WHERE id = ?", [$ruta_id])->first();
        
        if(!$ruta) {
            echo "<div class='log-item log-error'><i class='fas fa-exclamation-circle'></i> Error: Ruta no encontrada</div>";
            $error_ocurrido = true;
        } else {
            echo "<div class='log-item log-success'><i class='fas fa-check-circle'></i> Ruta encontrada: {$ruta->nombre} (ID: {$ruta->id})</div>";
            
            // Verificar si hay compras relacionadas con esta ruta
            $compras = $db->query("SELECT * FROM aa_compras WHERE ruta_id = ?", [$ruta_id])->count();
            if($compras > 0) {
                echo "<div class='log-item log-warning'><i class='fas fa-exclamation-triangle'></i> ¡ADVERTENCIA! Esta ruta tiene $compras compras asociadas.</div>";
                echo "<div class='log-item log-info'><i class='fas fa-trash'></i> Eliminando registros de compra primero...</div>";
                
                $db->query("DELETE FROM aa_compras WHERE ruta_id = ?", [$ruta_id]);
                echo "<div class='log-item log-success'><i class='fas fa-check-circle'></i> Compras eliminadas correctamente.</div>";
            }
            
            // 2. Eliminar de la base de datos con SQL directo
            echo "<div class='log-item log-info'><i class='fas fa-database'></i> Intentando eliminar de la base de datos...</div>";
            
            // Usar SQL directo para evitar problemas con el método delete()
            $db->query("DELETE FROM aa_rutas WHERE id = ?", [$ruta_id]);
            
            // Verificar si se eliminó correctamente
            $verificar = $db->query("SELECT * FROM aa_rutas WHERE id = ?", [$ruta_id])->count();
            $deleted = ($verificar === 0);
            
            if (!$deleted) {
                echo "<div class='log-item log-error'><i class='fas fa-times-circle'></i> Error al eliminar de la base de datos. Posible problema con permisos o claves foráneas.</div>";
                $error_ocurrido = true;
            } else {
                echo "<div class='log-item log-success'><i class='fas fa-check-circle'></i> Registro eliminado correctamente de la base de datos.</div>";
                
                // 3. Eliminar archivos (solo si existen)
                $abs_us_root = isset($abs_us_root) ? $abs_us_root : '';
                $us_url_root = isset($us_url_root) ? $us_url_root : '';
                $upload_gpx_dir = $abs_us_root.$us_url_root.'gpx/';
                
                // Eliminar archivo de imagen
                if(!empty($ruta->imagen)) {
                    $ruta_imagen = $abs_us_root.$us_url_root.$ruta->imagen;
                    echo "<div class='log-item log-info'><i class='fas fa-image'></i> Verificando imagen: {$ruta->imagen}</div>";
                    if(file_exists($ruta_imagen)) {
                        echo "<div class='log-item log-info'><i class='fas fa-file-image'></i> Archivo encontrado. Eliminando...</div>";
                        $result = unlink($ruta_imagen);
                        if($result) {
                            echo "<div class='log-item log-success'><i class='fas fa-check-circle'></i> Imagen eliminada correctamente.</div>";
                        } else {
                            echo "<div class='log-item log-error'><i class='fas fa-times-circle'></i> Error al eliminar la imagen.</div>";
                        }
                    } else {
                        echo "<div class='log-item log-warning'><i class='fas fa-exclamation-triangle'></i> Archivo de imagen no encontrado. Omitiendo.</div>";
                    }
                }
                
                // Eliminar archivos GPX
                if(!empty($ruta->gpx)) {
                    $ruta_gpx = $abs_us_root.$us_url_root.$ruta->gpx;
                    $base_filename = basename($ruta->gpx);
                    $gpx_base_name = pathinfo($base_filename, PATHINFO_FILENAME);
                    $gpx_extension = pathinfo($base_filename, PATHINFO_EXTENSION);
                    
                    // Eliminar GPX base
                    echo "<div class='log-item log-info'><i class='fas fa-route'></i> Verificando GPX base: {$ruta->gpx}</div>";
                    if(file_exists($ruta_gpx)) {
                        echo "<div class='log-item log-info'><i class='fas fa-file'></i> Archivo encontrado. Eliminando...</div>";
                        $result = unlink($ruta_gpx);
                        if($result) {
                            echo "<div class='log-item log-success'><i class='fas fa-check-circle'></i> Archivo GPX base eliminado correctamente.</div>";
                        } else {
                            echo "<div class='log-item log-error'><i class='fas fa-times-circle'></i> Error al eliminar el archivo GPX base.</div>";
                        }
                    } else {
                        echo "<div class='log-item log-warning'><i class='fas fa-exclamation-triangle'></i> Archivo GPX base no encontrado. Omitiendo.</div>";
                    }
                    
                    // Verificar si existe archivo de extras
                    $extras_path = $upload_gpx_dir . 'extras/' . $gpx_base_name . '_extras.' . $gpx_extension;
                    echo "<div class='log-item log-info'><i class='fas fa-map-marked-alt'></i> Verificando GPX extras: {$extras_path}</div>";
                    if(file_exists($extras_path)) {
                        echo "<div class='log-item log-info'><i class='fas fa-file-alt'></i> Archivo encontrado. Eliminando...</div>";
                        $result = unlink($extras_path);
                        if($result) {
                            echo "<div class='log-item log-success'><i class='fas fa-check-circle'></i> Archivo GPX extras eliminado correctamente.</div>";
                        } else {
                            echo "<div class='log-item log-error'><i class='fas fa-times-circle'></i> Error al eliminar el archivo GPX extras.</div>";
                        }
                    } else {
                        echo "<div class='log-item log-warning'><i class='fas fa-exclamation-triangle'></i> Archivo GPX extras no encontrado. Omitiendo.</div>";
                    }
                }
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='log-item log-error'><i class='fas fa-exclamation-circle'></i> Error: " . $e->getMessage() . "</div>";
        echo "<div class='log-item log-error'><pre class='small'>" . $e->getTraceAsString() . "</pre></div>";
        $error_ocurrido = true;
    }
}

// Mostrar resultado final
if($error_ocurrido) {
    echo "<div class='process-status process-error'>";
    echo "<h3><i class='fas fa-times-circle'></i> Error en el proceso de eliminación</h3>";
    echo "<p>Se encontraron errores durante el proceso. Revisa los mensajes anteriores para más detalles.</p>";
    echo "<a href='nueva_ruta.php' class='btn btn-primary mt-3'><i class='fas fa-arrow-left'></i> Volver a la lista de rutas</a>";
    echo "</div>";
} else {
    // Configurar mensaje de éxito y redirección
    echo "<div class='process-status process-success'>";
    echo "<h3><i class='fas fa-check-circle'></i> Eliminación Completada</h3>";
    echo "<p>La ruta y todos sus archivos asociados han sido eliminados correctamente.</p>";
    echo "<p>Redirigiendo a la lista de rutas en <span id='countdown' class='countdown'>3</span> segundos...</p>";
    echo "<a href='nueva_ruta.php' class='btn btn-primary mt-2'><i class='fas fa-arrow-left'></i> Volver a la lista de rutas</a>";
    echo "</div>";
    
    // JavaScript para redirección con cuenta regresiva
    echo "<script>
        var seconds = 3;
        var countdownElement = document.getElementById('countdown');
        
        function updateCountdown() {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                window.location.href = 'nueva_ruta.php';
            } else {
                setTimeout(updateCountdown, 1000);
            }
        }
        
        setTimeout(updateCountdown, 1000);
    </script>";
    
    // Configurar mensaje flash para la página a la que se redirige
    Session::flash('success', 'Ruta eliminada exitosamente');
}
?>

        </div>
    </div>
</body>
</html>