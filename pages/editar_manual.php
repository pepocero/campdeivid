<?php
// Script independiente para editar rutas directamente
require_once '../users/init.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

if(!hasPerm([2,4], $user->data()->id)) {
    die("Acceso denegado");
}

// Habilitar visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===== TIPOS DE PAISAJES REALISTAS PARA ESPAÑA =====
$tipos_paisajes = [
    // PAISAJES MONTAÑOSOS Y COMBINACIONES
    'Montañas y bosques' => 'Montañas y bosques',
    'Montañas y valles' => 'Montañas y valles', 
    'Montañas y lagos' => 'Montañas y lagos',
    'Sierra y bosques mediterráneos' => 'Sierra y bosques mediterráneos',
    'Alta montaña y prados' => 'Alta montaña y prados',
    'Cordillera cantábrica' => 'Cordillera cantábrica',
    'Pirineos y valles' => 'Pirineos y valles',
    
    // PAISAJES COSTEROS Y COMBINACIONES  
    'Costa y acantilados' => 'Costa y acantilados',
    'Costa y playas vírgenes' => 'Costa y playas vírgenes',
    'Costa mediterránea' => 'Costa mediterránea',
    'Costa atlántica' => 'Costa atlántica',
    'Rías y acantilados' => 'Rías y acantilados',
    'Costa y dunas' => 'Costa y dunas',
    
    // PAISAJES DESÉRTICOS Y ÁRIDOS
    'Desierto y badlands' => 'Desierto y badlands',
    'Estepa y llanuras' => 'Estepa y llanuras', 
    'Paisaje volcánico árido' => 'Paisaje volcánico árido',
    'Mesetas y barrancos' => 'Mesetas y barrancos',
    'Cañones y ramblas' => 'Cañones y ramblas',
    
    // PAISAJES DE BOSQUES Y VALLES
    'Bosques atlánticos' => 'Bosques atlánticos',
    'Bosques mediterráneos' => 'Bosques mediterráneos',
    'Hayedos y montaña' => 'Hayedos y montaña',
    'Pinares y sierra' => 'Pinares y sierra',
    'Valle y viñedos' => 'Valle y viñedos',
    'Valle y cerezos' => 'Valle y cerezos',
    'Dehesas y encinas' => 'Dehesas y encinas',
    
    // PAISAJES MIXTOS Y SINGULARES
    'Campo y pueblos blancos' => 'Campo y pueblos blancos',
    'Llanuras cerealistas' => 'Llanuras cerealistas',
    'Humedales y marismas' => 'Humedales y marismas',
    'Volcánico y laurisilva' => 'Volcánico y laurisilva',
    'Karst y dolinas' => 'Karst y dolinas',
    'Ruta jacobea histórica' => 'Ruta jacobea histórica',
    'Transpirenaica' => 'Transpirenaica',
    'Alpujarra granadina' => 'Alpujarra granadina'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edición de Ruta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/nhlsx7jkin6voponazn6x5mjea8yt6w7zn7ir3dwvu33jr4w/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .editor-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 25px;
            margin-bottom: 30px;
        }
        .section-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #495057;
        }
        .form-group label {
            font-weight: 600;
            color: #495057;
        }
        .status-message {
            border-left: 4px solid #17a2b8;
            padding: 10px 15px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }
        .status-success {
            border-left-color: #28a745;
        }
        .status-error {
            border-left-color: #dc3545;
        }
        .file-preview {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .btn-action {
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 20px;
        }
        /* Estilos para la sección de ofertas */
        .oferta-section {
            background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
            border: 2px solid #ffeaa7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(255, 193, 7, 0.1);
        }
        .precio-preview {
            font-size: 1.3em;
            font-weight: bold;
            margin-top: 15px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .precio-original {
            text-decoration: line-through;
            color: #6c757d;
            margin-right: 10px;
            font-size: 0.9em;
        }
        .precio-oferta {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.2em;
        }
        .badge-oferta {
            background: linear-gradient(45deg, #dc3545, #ff6b6b);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            margin-left: 10px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        /* Estilos para TinyMCE */
        .tox-tinymce {
            border-radius: 6px !important;
        }
        .debug-section {
            background-color: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        
        /* Estilos para el selector de paisajes */
        .paisaje-select {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            min-height: 45px;
        }
        .paisaje-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            border-color: #20c997;
            background: #fff;
        }
        .paisaje-select optgroup {
            font-weight: bold;
            color: #495057;
            background-color: #f8f9fa;
            padding: 8px 0 4px 0;
            margin: 4px 0;
        }
        .paisaje-select option {
            padding: 6px 12px;
            font-weight: normal;
            color: #495057;
            background-color: #fff;
        }
        .paisaje-select option:checked {
            background-color: #28a745;
            color: white;
        }
        .paisaje-icon {
            margin-right: 8px;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        echo "<div class='editor-container'>";
        echo "<h2 class='mb-4 text-center'><i class='fas fa-edit'></i> Edición Manual de Rutas <small class='text-muted'>(Con TinyMCE)</small></h2>";

        // Obtener ID de la ruta a editar
        $ruta_id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if(!$ruta_id) {
            echo "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Error: ID de ruta no especificado</div>";
            echo "<div class='text-center mt-4'><a href='nueva_ruta.php' class='btn btn-primary'><i class='fas fa-arrow-left'></i> Volver a la lista de rutas</a></div>";
            echo "</div>";
            die();
        }

        try {
            $db = DB::getInstance();
            
            // Buscar la ruta
            echo "<div class='status-message'><i class='fas fa-search'></i> Buscando ruta con ID: $ruta_id</div>";
            $ruta = $db->query("SELECT * FROM aa_rutas WHERE id = ?", [$ruta_id])->first();
            
            if(!$ruta) {
                echo "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Error: Ruta no encontrada</div>";
                echo "<div class='text-center mt-4'><a href='nueva_ruta.php' class='btn btn-primary'><i class='fas fa-arrow-left'></i> Volver a la lista de rutas</a></div>";
                echo "</div>";
                die();
            }
            
            echo "<div class='status-message status-success'><i class='fas fa-check-circle'></i> Ruta encontrada: <strong>{$ruta->nombre}</strong> (ID: {$ruta->id})</div>";
            
            // DEBUG: Verificar campos de oferta
            echo "<div class='debug-section'>";
            echo "<h5>🔍 DEBUG - Verificando campos de oferta:</h5>";
            echo "<strong>Plan actual:</strong> <span style='background: yellow; padding: 2px 5px;'>{$ruta->plan}</span><br>";
            echo "<strong>Precio actual:</strong> {$ruta->precio}€<br>";
            echo "<strong>Paisaje actual:</strong> <span style='background: lightgreen; padding: 2px 5px;'>{$ruta->paisaje}</span><br>";
            echo "<strong>Campos disponibles:</strong> ";
            $propiedades = get_object_vars($ruta);
            foreach($propiedades as $key => $value) {
                $color = in_array($key, ['en_oferta', 'porcentaje_oferta']) ? 'color: red; font-weight: bold;' : '';
                echo "<span style='$color'>$key</span>, ";
            }
            echo "<br>";
            
            if (property_exists($ruta, 'en_oferta')) {
                echo "<strong>✅ en_oferta:</strong> " . ($ruta->en_oferta ?? '0') . "<br>";
            } else {
                echo "<strong>❌ Campo 'en_oferta' no existe</strong><br>";
            }
            
            if (property_exists($ruta, 'porcentaje_oferta')) {
                echo "<strong>✅ porcentaje_oferta:</strong> " . ($ruta->porcentaje_oferta ?? '0') . "<br>";
            } else {
                echo "<strong>❌ Campo 'porcentaje_oferta' no existe</strong><br>";
            }
            echo "</div>";
            
            // Configuración de directorios
            $abs_us_root = isset($abs_us_root) ? $abs_us_root : '';
            $us_url_root = isset($us_url_root) ? $us_url_root : '';
            $upload_image_dir = $abs_us_root.$us_url_root.'images/rutas/';
            $upload_gpx_dir = $abs_us_root.$us_url_root.'gpx/';
            
            // Procesar datos del formulario
            if(isset($_POST['editar_submit'])) {
                echo "<div class='status-message'><i class='fas fa-cog fa-spin'></i> Procesando datos del formulario...</div>";
                
                // Validar que el paisaje seleccionado esté en la lista permitida
                if(!empty($_POST['paisaje']) && !array_key_exists($_POST['paisaje'], $tipos_paisajes)) {
                    echo "<div class='alert alert-danger'>Tipo de paisaje inválido</div>";
                } else {
                    // Campos básicos a actualizar
                    $fields = [
                        'nombre' => $_POST['nombre'],
                        'descripcion' => $_POST['descripcion'],
                        'nivel' => $_POST['nivel'],
                        'plan' => $_POST['plan'],
                        'paisaje' => $_POST['paisaje'],
                        'precio' => $_POST['plan'] == 'Premium' ? floatval($_POST['precio']) : 0,
                        'distancia' => floatval($_POST['distancia']),
                        'tiempo' => $_POST['tiempo'],
                        'destacados' => NULL, // Campo obsoleto - siempre NULL
                        'descripcion_completa' => $_POST['descripcion_completa'],
                        // Nuevos campos para ofertas
                        'en_oferta' => isset($_POST['en_oferta']) ? 1 : 0,
                        'porcentaje_oferta' => isset($_POST['en_oferta']) ? floatval($_POST['porcentaje_oferta']) : 0
                    ];
                    
                    echo "<div class='debug-section'>";
                    echo "<h5>📝 Datos a guardar:</h5>";
                    echo "<strong>paisaje:</strong> " . $fields['paisaje'] . "<br>";
                    echo "<strong>en_oferta:</strong> " . $fields['en_oferta'] . "<br>";
                    echo "<strong>porcentaje_oferta:</strong> " . $fields['porcentaje_oferta'] . "<br>";
                    echo "<strong>destacados:</strong> NULL (campo eliminado)<br>";
                    echo "</div>";
                    
                    // Procesar imagen si se subió una nueva
                    if(!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                        echo "<div class='status-message'><i class='fas fa-image'></i> Procesando nueva imagen...</div>";
                        $file_ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                        if(in_array($file_ext, ['jpg','jpeg','png','webp'])) {
                            $new_filename = 'ruta_'.strtolower(str_replace(' ','_',$_POST['nombre'])).'.'.$file_ext;
                            $target_path = $upload_image_dir.$new_filename;
                            // Eliminar imagen anterior si existe
                            if(!empty($ruta->imagen) && file_exists($abs_us_root.$us_url_root.$ruta->imagen)) {
                                echo "<div class='status-message'><i class='fas fa-trash-alt'></i> Eliminando imagen anterior: " . basename($ruta->imagen) . "</div>";
                                unlink($abs_us_root.$us_url_root.$ruta->imagen);
                            }
                            // Mover archivo subido a la carpeta de imágenes
                            if(move_uploaded_file($_FILES['imagen']['tmp_name'], $target_path)) {
                                echo "<div class='status-message status-success'><i class='fas fa-check-circle'></i> Imagen subida correctamente: {$new_filename}</div>";                             
                                $fields['imagen'] = '../images/rutas/'.$new_filename;
                            } else {
                                echo "<div class='status-message status-error'><i class='fas fa-times-circle'></i> Error subiendo imagen</div>";
                            }
                        } else {
                            echo "<div class='status-message status-error'><i class='fas fa-times-circle'></i> Formato de imagen inválido</div>";
                        }
                    } else {
                        echo "<div class='status-message'><i class='fas fa-info-circle'></i> Manteniendo imagen actual</div>";
                    }
                    
                    // Generar nombre base para archivos GPX
                    $base_filename = 'ruta_'.strtolower(str_replace(' ','_',$_POST['nombre']));
                    
                    // Procesar GPX BASE si se subió uno nuevo
                    if(!empty($_FILES['gpx_base']['name']) && $_FILES['gpx_base']['error'] === UPLOAD_ERR_OK) {
                        echo "<div class='status-message'><i class='fas fa-file-alt'></i> Procesando nuevo archivo GPX base...</div>";
                        $file_ext = strtolower(pathinfo($_FILES['gpx_base']['name'], PATHINFO_EXTENSION));
                        if($file_ext === 'gpx') {
                            $new_filename = $base_filename . '.gpx';
                            $target_path = $upload_gpx_dir . 'base/' . $new_filename;
                            
                            // PRIMERO intentar subir el nuevo archivo
                            if(move_uploaded_file($_FILES['gpx_base']['tmp_name'], $target_path)) {
                                echo "<div class='status-message status-success'><i class='fas fa-check-circle'></i> Archivo GPX base subido correctamente: {$new_filename}</div>";
                                
                                // SOLO DESPUÉS eliminar el anterior si es diferente al nuevo
                                if(!empty($ruta->gpx) && $ruta->gpx !== 'gpx/base/'.$new_filename) {
                                    $old_file_path = $abs_us_root.$us_url_root.$ruta->gpx;
                                    if(file_exists($old_file_path)) {
                                        echo "<div class='status-message'><i class='fas fa-trash-alt'></i> Eliminando GPX anterior: " . basename($ruta->gpx) . "</div>";
                                        unlink($old_file_path);
                                    }
                                }
                                
                                $fields['gpx'] = 'gpx/base/'.$new_filename;
                            } else {
                                echo "<div class='status-message status-error'><i class='fas fa-times-circle'></i> Error subiendo GPX base - manteniendo archivo actual</div>";
                            }
                        } else {
                            echo "<div class='status-message status-error'><i class='fas fa-times-circle'></i> El archivo GPX debe ser .gpx</div>";
                        }
                    } else {
                        echo "<div class='status-message'><i class='fas fa-info-circle'></i> Manteniendo archivo GPX base actual</div>";
                    }
                    
                    // Marcar tiene_extras en base a la existencia del archivo
                    $tiene_extras = 0;
                    
                    // Procesar GPX EXTRAS si es Premium y se subió uno nuevo
                    if($_POST['plan'] == 'Premium') {
                        echo "<div class='status-message'><i class='fas fa-star'></i> La ruta es Premium, verificando archivos extras...</div>";
                        
                        if(!empty($_FILES['gpx_extras']['name']) && $_FILES['gpx_extras']['error'] === UPLOAD_ERR_OK) {
                            echo "<div class='status-message'><i class='fas fa-file-alt'></i> Procesando nuevo archivo GPX extras...</div>";
                            $file_ext = strtolower(pathinfo($_FILES['gpx_extras']['name'], PATHINFO_EXTENSION));
                            if($file_ext === 'gpx') {
                                $new_filename = $base_filename . '_extras.gpx';
                                $target_path = $upload_gpx_dir . 'extras/' . $new_filename;
                                
                                // PRIMERO intentar subir el nuevo archivo
                                if(move_uploaded_file($_FILES['gpx_extras']['tmp_name'], $target_path)) {
                                    echo "<div class='status-message status-success'><i class='fas fa-check-circle'></i> Archivo GPX extras subido correctamente: {$new_filename}</div>";
                                    $tiene_extras = 1;
                                    
                                    // SOLO DESPUÉS eliminar archivo extras anterior si es diferente
                                    $gpx_base_filename = !empty($ruta->gpx) ? $ruta->gpx : '';
                                    if(!empty($gpx_base_filename)) {
                                        $old_base_filename = basename($gpx_base_filename);
                                        $old_gpx_base_name = pathinfo($old_base_filename, PATHINFO_FILENAME);
                                        $old_gpx_extension = pathinfo($old_base_filename, PATHINFO_EXTENSION);
                                        $old_extras_filename = $old_gpx_base_name . '_extras.' . $old_gpx_extension;
                                        
                                        // Solo eliminar si el nombre del archivo anterior es diferente al nuevo
                                        if($old_extras_filename !== $new_filename) {
                                            $old_extras_path = $upload_gpx_dir . 'extras/' . $old_extras_filename;
                                            if(file_exists($old_extras_path)) {
                                                echo "<div class='status-message'><i class='fas fa-trash-alt'></i> Eliminando GPX extras anterior: {$old_extras_filename}</div>";
                                                unlink($old_extras_path);
                                            }
                                        }
                                    }
                                } else {
                                    echo "<div class='status-message status-error'><i class='fas fa-times-circle'></i> Error subiendo GPX extras - manteniendo archivo actual</div>";
                                }
                            } else {
                                echo "<div class='status-message status-error'><i class='fas fa-times-circle'></i> El archivo GPX extras debe ser .gpx</div>";
                            }
                        } else {
                            // Verificar si ya existe un archivo de extras
                            $gpx_base_filename = !empty($fields['gpx']) ? $fields['gpx'] : $ruta->gpx;
                            $base_filename = basename($gpx_base_filename);
                            $gpx_base_name = pathinfo($base_filename, PATHINFO_FILENAME);
                            $gpx_extension = pathinfo($base_filename, PATHINFO_EXTENSION);
                            $extras_path = $upload_gpx_dir . 'extras/' . $gpx_base_name . '_extras.' . $gpx_extension;
                            
                            if(file_exists($extras_path)) {
                                echo "<div class='status-message status-success'><i class='fas fa-check-circle'></i> Archivo GPX extras existente: {$gpx_base_name}_extras.{$gpx_extension}</div>";
                                $tiene_extras = 1;
                            } else {
                                echo "<div class='status-message'><i class='fas fa-info-circle'></i> No se encontró archivo GPX extras</div>";
                            }
                        }
                    }
                    
                    // Actualizar campo tiene_extras
                    $fields['tiene_extras'] = $tiene_extras;
                    
                    // Actualizar en la base de datos
                    echo "<div class='status-message'><i class='fas fa-database'></i> Actualizando información en la base de datos...</div>";
                    $update = $db->update('aa_rutas', $ruta_id, $fields);
                    
                    if($update) {
                        echo "<div class='alert alert-success text-center'><i class='fas fa-check-circle'></i> Ruta actualizada exitosamente</div>";
                    } else {
                        echo "<div class='alert alert-danger text-center'><i class='fas fa-times-circle'></i> Error al actualizar la ruta</div>";
                    }
                }
                
                echo "<div class='text-center mt-4'><a href='nueva_ruta.php' class='btn btn-primary btn-action'><i class='fas fa-arrow-left'></i> Volver a la lista de rutas</a></div>";
                
            } else {
                // Mostrar formulario de edición
                ?>
                <form method="post" enctype="multipart/form-data" class="mb-4">
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-white">
                            <h4 class="mb-0"><i class="fas fa-route"></i> Editar: <?= $ruta->nombre ?></h4>
                        </div>
                        <div class="card-body">
                            <!-- Información Básica -->
                            <h5 class="section-header"><i class="fas fa-info-circle"></i> Información Básica</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre de la Ruta</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($ruta->nombre) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="nivel">Nivel de Dificultad</label>
                                        <select class="form-control" id="nivel" name="nivel" required>
                                            <?php foreach(['Piloto nuevo', 'Domando Curvas', 'Maestro del Asfalto'] as $nivel): ?>
                                                <option value="<?= $nivel ?>" <?= ($ruta->nivel == $nivel) ? 'selected' : '' ?>><?= $nivel ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- ✅ NUEVO: Selector de Paisajes Realistas -->
                                    <div class="form-group">
                                        <label for="paisaje"><i class="fas fa-mountain"></i> Tipo de Paisaje</label>
                                        <select class="form-control paisaje-select" id="paisaje" name="paisaje" required>
                                            <option value="">-- Seleccionar tipo de paisaje --</option>
                                            
                                            <optgroup label="🏔️ Paisajes Montañosos">
                                                <option value="Montañas y bosques" <?= ($ruta->paisaje == 'Montañas y bosques') ? 'selected' : '' ?>>Montañas y bosques</option>
                                                <option value="Montañas y valles" <?= ($ruta->paisaje == 'Montañas y valles') ? 'selected' : '' ?>>Montañas y valles</option>
                                                <option value="Montañas y lagos" <?= ($ruta->paisaje == 'Montañas y lagos') ? 'selected' : '' ?>>Montañas y lagos</option>
                                                <option value="Sierra y bosques mediterráneos" <?= ($ruta->paisaje == 'Sierra y bosques mediterráneos') ? 'selected' : '' ?>>Sierra y bosques mediterráneos</option>
                                                <option value="Alta montaña y prados" <?= ($ruta->paisaje == 'Alta montaña y prados') ? 'selected' : '' ?>>Alta montaña y prados</option>
                                                <option value="Cordillera cantábrica" <?= ($ruta->paisaje == 'Cordillera cantábrica') ? 'selected' : '' ?>>Cordillera cantábrica</option>
                                                <option value="Pirineos y valles" <?= ($ruta->paisaje == 'Pirineos y valles') ? 'selected' : '' ?>>Pirineos y valles</option>
                                            </optgroup>
                                            
                                            <optgroup label="🌊 Paisajes Costeros">
                                                <option value="Costa y acantilados" <?= ($ruta->paisaje == 'Costa y acantilados') ? 'selected' : '' ?>>Costa y acantilados</option>
                                                <option value="Costa y playas vírgenes" <?= ($ruta->paisaje == 'Costa y playas vírgenes') ? 'selected' : '' ?>>Costa y playas vírgenes</option>
                                                <option value="Costa mediterránea" <?= ($ruta->paisaje == 'Costa mediterránea') ? 'selected' : '' ?>>Costa mediterránea</option>
                                                <option value="Costa atlántica" <?= ($ruta->paisaje == 'Costa atlántica') ? 'selected' : '' ?>>Costa atlántica</option>
                                                <option value="Rías y acantilados" <?= ($ruta->paisaje == 'Rías y acantilados') ? 'selected' : '' ?>>Rías y acantilados</option>
                                                <option value="Costa y dunas" <?= ($ruta->paisaje == 'Costa y dunas') ? 'selected' : '' ?>>Costa y dunas</option>
                                            </optgroup>
                                            
                                            <optgroup label="🏜️ Paisajes Áridos y Desérticos">
                                                <option value="Desierto y badlands" <?= ($ruta->paisaje == 'Desierto y badlands') ? 'selected' : '' ?>>Desierto y badlands</option>
                                                <option value="Estepa y llanuras" <?= ($ruta->paisaje == 'Estepa y llanuras') ? 'selected' : '' ?>>Estepa y llanuras</option>
                                                <option value="Paisaje volcánico árido" <?= ($ruta->paisaje == 'Paisaje volcánico árido') ? 'selected' : '' ?>>Paisaje volcánico árido</option>
                                                <option value="Mesetas y barrancos" <?= ($ruta->paisaje == 'Mesetas y barrancos') ? 'selected' : '' ?>>Mesetas y barrancos</option>
                                                <option value="Cañones y ramblas" <?= ($ruta->paisaje == 'Cañones y ramblas') ? 'selected' : '' ?>>Cañones y ramblas</option>
                                            </optgroup>
                                            
                                            <optgroup label="🌲 Bosques y Valles">
                                                <option value="Bosques atlánticos" <?= ($ruta->paisaje == 'Bosques atlánticos') ? 'selected' : '' ?>>Bosques atlánticos</option>
                                                <option value="Bosques mediterráneos" <?= ($ruta->paisaje == 'Bosques mediterráneos') ? 'selected' : '' ?>>Bosques mediterráneos</option>
                                                <option value="Hayedos y montaña" <?= ($ruta->paisaje == 'Hayedos y montaña') ? 'selected' : '' ?>>Hayedos y montaña</option>
                                                <option value="Pinares y sierra" <?= ($ruta->paisaje == 'Pinares y sierra') ? 'selected' : '' ?>>Pinares y sierra</option>
                                                <option value="Valle y viñedos" <?= ($ruta->paisaje == 'Valle y viñedos') ? 'selected' : '' ?>>Valle y viñedos</option>
                                                <option value="Valle y cerezos" <?= ($ruta->paisaje == 'Valle y cerezos') ? 'selected' : '' ?>>Valle y cerezos</option>
                                                <option value="Dehesas y encinas" <?= ($ruta->paisaje == 'Dehesas y encinas') ? 'selected' : '' ?>>Dehesas y encinas</option>
                                            </optgroup>
                                            
                                            <optgroup label="🏞️ Paisajes Especiales">
                                                <option value="Campo y pueblos blancos" <?= ($ruta->paisaje == 'Campo y pueblos blancos') ? 'selected' : '' ?>>Campo y pueblos blancos</option>
                                                <option value="Llanuras cerealistas" <?= ($ruta->paisaje == 'Llanuras cerealistas') ? 'selected' : '' ?>>Llanuras cerealistas</option>
                                                <option value="Humedales y marismas" <?= ($ruta->paisaje == 'Humedales y marismas') ? 'selected' : '' ?>>Humedales y marismas</option>
                                                <option value="Volcánico y laurisilva" <?= ($ruta->paisaje == 'Volcánico y laurisilva') ? 'selected' : '' ?>>Volcánico y laurisilva</option>
                                                <option value="Karst y dolinas" <?= ($ruta->paisaje == 'Karst y dolinas') ? 'selected' : '' ?>>Karst y dolinas</option>
                                                <option value="Ruta jacobea histórica" <?= ($ruta->paisaje == 'Ruta jacobea histórica') ? 'selected' : '' ?>>Ruta jacobea histórica</option>
                                                <option value="Transpirenaica" <?= ($ruta->paisaje == 'Transpirenaica') ? 'selected' : '' ?>>Transpirenaica</option>
                                                <option value="Alpujarra granadina" <?= ($ruta->paisaje == 'Alpujarra granadina') ? 'selected' : '' ?>>Alpujarra granadina</option>
                                            </optgroup>
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Elige la combinación de paisajes que mejor describa tu ruta
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="plan">Tipo de Plan</label>
                                        <select class="form-control" id="plan" name="plan" required>
                                            <option value="Gratis" <?= ($ruta->plan == 'Gratis') ? 'selected' : '' ?>>Gratis</option>
                                            <option value="Premium" <?= ($ruta->plan == 'Premium') ? 'selected' : '' ?>>Premium</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group" id="precioGroup">
                                        <label for="precio">Precio (€)</label>
                                        <input type="number" class="form-control" id="precio" name="precio" value="<?= $ruta->precio ?>" step="0.01" min="0">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="distancia">Distancia (km)</label>
                                                <input type="number" class="form-control" id="distancia" name="distancia" value="<?= $ruta->distancia ?>" step="0.1" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tiempo">Tiempo Estimado</label>
                                                <input type="text" class="form-control" id="tiempo" name="tiempo" value="<?= htmlspecialchars($ruta->tiempo) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- NUEVA SECCIÓN: Gestión de Ofertas -->
                            <div id="ofertasGroup" class="oferta-section">
                                <h5 class="section-header"><i class="fas fa-percent"></i> 🎯 Gestión de Ofertas Especiales</h5>
                                
                                <!-- Mensaje informativo que cambia según el plan -->
                                <div id="mensajeOferta" class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> 
                                    <span id="textoMensaje">Las ofertas están disponibles solo para rutas Premium</span>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input type="checkbox" class="form-check-input" id="en_oferta" name="en_oferta" 
                                                <?= (property_exists($ruta, 'en_oferta') && $ruta->en_oferta) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="en_oferta">
                                                <i class="fas fa-tag"></i> <strong>Activar oferta especial</strong>
                                            </label>
                                        </div>
                                        
                                        <div class="form-group" id="porcentajeGroup">
                                            <label for="porcentaje_oferta"><i class="fas fa-percent"></i> Porcentaje de Descuento (%)</label>
                                            <input type="number" class="form-control" id="porcentaje_oferta" name="porcentaje_oferta" 
                                                   value="<?= property_exists($ruta, 'porcentaje_oferta') ? ($ruta->porcentaje_oferta ?? '0') : '0' ?>" 
                                                   min="0" max="99" step="1">
                                            <small class="form-text text-muted">Entre 1% y 99%</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="precio-preview" id="precioPreview">
                                            <i class="fas fa-calculator"></i> <strong>Vista previa del precio:</strong>
                                            <div id="precioCalculado" class="mt-2">
                                                <span class="text-muted">Selecciona un precio para ver la vista previa</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Descripciones -->
                            <h5 class="section-header mt-4"><i class="fas fa-align-left"></i> Descripciones</h5>
                            <div class="form-group">
                                <label for="descripcion">Descripción Corta</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="2" maxlength="255" required><?= htmlspecialchars($ruta->descripcion) ?></textarea>
                                <small class="form-text text-muted">Máximo 255 caracteres</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="editor"><i class="fas fa-edit"></i> Descripción Completa <small class="text-muted">(Editor TinyMCE)</small></label>
                                <textarea class="form-control" id="editor" name="descripcion_completa" rows="12" required><?= $ruta->descripcion_completa ?? '' ?></textarea>
                                <small class="form-text text-muted">Editor enriquecido con opciones de formato</small>
                            </div>
                            
                            <!-- 🚫 ELIMINADO: Campo de puntos destacados -->
                            
                            <!-- Archivos -->
                            <h5 class="section-header mt-4"><i class="fas fa-file-upload"></i> Archivos</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="imagen">Imagen de Portada</label>
                                        <?php if(!empty($ruta->imagen)): ?>
                                            <div class="file-preview">
                                                <img src="<?= $ruta->imagen ?>" alt="Vista previa" class="img-thumbnail mb-2" style="max-height: 150px;">
                                                <div class="small text-muted">Imagen actual: <?= basename($ruta->imagen) ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*">
                                            <label class="custom-file-label" for="imagen">Seleccionar nueva imagen...</label>
                                        </div>
                                        <small class="form-text text-muted">Dejar vacío para mantener la imagen actual</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gpx_base">Archivo GPX Base</label>
                                        <?php if(!empty($ruta->gpx)): ?>
                                            <div class="file-preview">
                                                <div class="small text-muted">Archivo actual: <?= basename($ruta->gpx) ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="gpx_base" name="gpx_base" accept=".gpx">
                                            <label class="custom-file-label" for="gpx_base">Seleccionar nuevo GPX base...</label>
                                        </div>
                                        <small class="form-text text-muted">Dejar vacío para mantener el archivo actual</small>
                                    </div>
                                    
                                    <div class="form-group" id="extrasGroup">
                                        <label for="gpx_extras">Archivo GPX Extras</label>
                                        <?php 
                                        // Verificar si existe archivo de extras
                                        $tieneExtrasFile = false;
                                        if(!empty($ruta->gpx)) {
                                            $base_filename = basename($ruta->gpx);
                                            $gpx_base_name = pathinfo($base_filename, PATHINFO_FILENAME);
                                            $gpx_extension = pathinfo($base_filename, PATHINFO_EXTENSION);
                                            $extras_path = $upload_gpx_dir . 'extras/' . $gpx_base_name . '_extras.' . $gpx_extension;
                                            $tieneExtrasFile = file_exists($extras_path);
                                            
                                            if($tieneExtrasFile) {
                                                echo "<div class='file-preview'>";
                                                echo "<div class='small text-muted'>Archivo extras actual: {$gpx_base_name}_extras.{$gpx_extension} <span class='badge badge-success'>Subido</span></div>";
                                                echo "</div>";
                                            } else {
                                                echo "<div class='file-preview'>";
                                                echo "<div class='small text-muted'>No hay archivo de extras <span class='badge badge-secondary'>No encontrado</span></div>";
                                                echo "</div>";
                                            }
                                        }
                                        ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="gpx_extras" name="gpx_extras" accept=".gpx">
                                            <label class="custom-file-label" for="gpx_extras">Seleccionar nuevo GPX extras...</label>
                                        </div>
                                        <small class="form-text text-muted">Solo para rutas Premium. Opcional.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" name="editar_submit" class="btn btn-warning btn-lg btn-action mr-2">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="nueva_ruta.php" class="btn btn-secondary btn-lg btn-action">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('🚀 Script iniciado - VERSIÓN CON TinyMCE y Selector de Paisajes');
                    
                    // Inicializar TinyMCE
                    tinymce.init({
                        selector: '#editor',
                        height: 400,
                        language: 'es',
                        plugins: [
                            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                            'insertdatetime', 'media', 'table', 'help', 'wordcount'
                        ],
                        toolbar: 'undo redo | blocks | ' +
                            'bold italic forecolor | alignleft aligncenter ' +
                            'alignright alignjustify | bullist numlist outdent indent | ' +
                            'removeformat | help',
                        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
                        setup: function (editor) {
                            editor.on('change', function () {
                                editor.save();
                            });
                        },
                        branding: false,
                        promotion: false
                    });
                    
                    // Elementos del DOM
                    var planSelect = document.getElementById('plan');
                    var precioGroup = document.getElementById('precioGroup');
                    var extrasGroup = document.getElementById('extrasGroup');
                    var ofertasGroup = document.getElementById('ofertasGroup');
                    var enOfertaCheck = document.getElementById('en_oferta');
                    var porcentajeGroup = document.getElementById('porcentajeGroup');
                    var porcentajeInput = document.getElementById('porcentaje_oferta');
                    var precioInput = document.getElementById('precio');
                    var precioCalculado = document.getElementById('precioCalculado');
                    var mensajeOferta = document.getElementById('mensajeOferta');
                    var textoMensaje = document.getElementById('textoMensaje');
                    
                    console.log('📝 Elementos encontrados:', {
                        planSelect: !!planSelect,
                        enOfertaCheck: !!enOfertaCheck,
                        porcentajeInput: !!porcentajeInput,
                        precioInput: !!precioInput,
                        precioCalculado: !!precioCalculado,
                        ofertasGroup: !!ofertasGroup,
                        mensajeOferta: !!mensajeOferta
                    });
                    
                    function actualizarCampos() {
                        console.log('🔄 Actualizando campos, plan:', planSelect.value);
                        var esPremium = planSelect.value === 'Premium';
                        
                        // Mostrar/ocultar campos básicos según el plan
                        if(esPremium) {
                            if(precioGroup) precioGroup.style.display = 'block';
                            if(extrasGroup) extrasGroup.style.display = 'block';
                        } else {
                            if(precioGroup) precioGroup.style.display = 'none';
                            if(extrasGroup) extrasGroup.style.display = 'none';
                        }
                        
                        // NUEVO: La sección de ofertas SIEMPRE se muestra, pero se habilita/deshabilita
                        if(ofertasGroup) {
                            ofertasGroup.style.display = 'block'; // Siempre visible
                            
                            if(esPremium) {
                                ofertasGroup.style.opacity = '1';
                                ofertasGroup.style.background = 'linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%)';
                                enOfertaCheck.disabled = false;
                                porcentajeInput.disabled = false;
                                if(mensajeOferta) {
                                    mensajeOferta.className = 'alert alert-success mb-3';
                                    textoMensaje.innerHTML = '<i class="fas fa-check-circle"></i> ¡Ofertas disponibles! Puedes configurar descuentos para esta ruta Premium.';
                                }
                            } else {
                                ofertasGroup.style.opacity = '0.6';
                                ofertasGroup.style.background = '#f8f9fa';
                                enOfertaCheck.disabled = true;
                                porcentajeInput.disabled = true;
                                enOfertaCheck.checked = false; // Desmarcar si no es premium
                                // Resetear validación cuando no es premium
                                porcentajeInput.removeAttribute('required');
                                porcentajeInput.setAttribute('min', '0');
                                porcentajeInput.value = '0';
                                if(mensajeOferta) {
                                    mensajeOferta.className = 'alert alert-warning mb-3';
                                    textoMensaje.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Las ofertas solo están disponibles para rutas Premium. Cambia el plan a Premium para habilitar esta función.';
                                }
                            }
                        }
                        
                        actualizarPrecioPreview();
                    }
                    
                    function actualizarOferta() {
                        console.log('🏷️ Actualizando oferta, checked:', enOfertaCheck.checked, 'disabled:', enOfertaCheck.disabled);
                        
                        // Si el checkbox está deshabilitado (plan no premium), ocultar porcentaje
                        if(enOfertaCheck.disabled) {
                            porcentajeGroup.style.display = 'none';
                            // Resetear validación cuando está deshabilitado
                            porcentajeInput.removeAttribute('required');
                            porcentajeInput.setAttribute('min', '0');
                        } else {
                            // Si está habilitado, mostrar/ocultar según esté marcado
                            if(enOfertaCheck.checked) {
                                porcentajeGroup.style.display = 'block';
                                // Activar validación cuando está visible y habilitado
                                porcentajeInput.setAttribute('min', '1');
                                porcentajeInput.setAttribute('required', 'required');
                            } else {
                                porcentajeGroup.style.display = 'none';
                                // Quitar validación cuando está oculto
                                porcentajeInput.removeAttribute('required');
                                porcentajeInput.setAttribute('min', '0');
                                porcentajeInput.value = '0'; // Resetear valor
                            }
                        }
                        actualizarPrecioPreview();
                    }
                    
                    function actualizarPrecioPreview() {
                        var precio = parseFloat(precioInput.value) || 0;
                        var porcentaje = parseFloat(porcentajeInput.value) || 0;
                        var esOferta = enOfertaCheck.checked && !enOfertaCheck.disabled;
                        var esPremium = planSelect.value === 'Premium';
                        
                        console.log('💰 Calculando precio:', {precio, porcentaje, esOferta, esPremium});
                        
                        if (!esPremium) {
                            precioCalculado.innerHTML = '<span class="text-success"><i class="fas fa-gift"></i> Gratis</span><div class="small text-muted">Las ofertas solo están disponibles para rutas Premium</div>';
                            return;
                        }
                        
                        if (precio === 0) {
                            precioCalculado.innerHTML = '<span class="text-success"><i class="fas fa-gift"></i> Gratis</span>';
                            return;
                        }
                        
                        if (esOferta && porcentaje > 0) {
                            var precioConDescuento = precio - (precio * porcentaje / 100);
                            var ahorro = precio - precioConDescuento;
                            
                            precioCalculado.innerHTML = 
                                '<div><span class="precio-original">' + precio.toFixed(2) + '€</span>' +
                                '<span class="precio-oferta">' + precioConDescuento.toFixed(2) + '€</span>' +
                                '<span class="badge-oferta">-' + porcentaje + '%</span></div>' +
                                '<div class="small text-success mt-1"><i class="fas fa-piggy-bank"></i> Ahorras: ' + ahorro.toFixed(2) + '€</div>';
                        } else {
                            precioCalculado.innerHTML = '<span class="text-primary"><i class="fas fa-euro-sign"></i> ' + precio.toFixed(2) + '€</span>';
                        }
                    }
                    
                    // Inicializar
                    actualizarCampos();
                    actualizarOferta();
                    
                    // Eventos
                    if (planSelect) planSelect.addEventListener('change', actualizarCampos);
                    if (enOfertaCheck) enOfertaCheck.addEventListener('change', actualizarOferta);
                    if (porcentajeInput) porcentajeInput.addEventListener('input', actualizarPrecioPreview);
                    if (precioInput) precioInput.addEventListener('input', actualizarPrecioPreview);
                    
                    // Actualizar nombre del archivo seleccionado
                    document.querySelectorAll('.custom-file-input').forEach(function(input) {
                        input.addEventListener('change', function() {
                            var fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo';
                            this.nextElementSibling.textContent = fileName;
                        });
                    });
                    
                    // Sincronizar TinyMCE antes del envío del formulario
                    var form = document.querySelector('form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            // Sincronizar TinyMCE
                            if (tinymce.get('editor')) {
                                tinymce.get('editor').save();
                            }
                            
                            // Validar campo de porcentaje si está visible
                            if (enOfertaCheck.checked && !enOfertaCheck.disabled) {
                                var porcentaje = parseFloat(porcentajeInput.value) || 0;
                                if (porcentaje < 1 || porcentaje > 99) {
                                    e.preventDefault();
                                    alert('El porcentaje de descuento debe estar entre 1% y 99%');
                                    porcentajeInput.focus();
                                    return false;
                                }
                            } else {
                                // Si no está visible, asegurar que el valor sea 0
                                porcentajeInput.value = '0';
                                porcentajeInput.removeAttribute('required');
                            }
                        });
                    }
                    
                    console.log('✅ Script inicializado correctamente con TinyMCE y Selector de Paisajes');
                });
                </script>
                <?php
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<h4><i class='fas fa-exclamation-triangle'></i> Error</h4>";
            echo "<p>Se produjo un error: " . $e->getMessage() . "</p>";
            echo "<pre class='bg-light p-3 mt-3 small'>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
            echo "<div class='text-center mt-4'><a href='nueva_ruta.php' class='btn btn-primary btn-action'><i class='fas fa-arrow-left'></i> Volver a la lista de rutas</a></div>";
        }
        
        echo "</div>"; // Cierre de editor-container
        ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>