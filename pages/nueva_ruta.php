<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

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

if(!hasPerm([2,4], $user->data()->id)) {
    Session::flash('error', 'Acceso denegado');
    Redirect::to('index.php');
}

// Configuración de directorios
$upload_image_dir = $abs_us_root.$us_url_root.'images/rutas/';
$upload_gpx_dir = $abs_us_root.$us_url_root.'gpx/';

// Crear estructura de directorios GPX si no existen
$gpx_subdirs = ['base', 'extras'];
foreach ($gpx_subdirs as $subdir) {
    $dir = $upload_gpx_dir . $subdir;
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Variables globales
$errors = [];
$mostrar_formulario = false;

try {
    $db = DB::getInstance();
    
    // ✅ MOVIDO AQUÍ: Añadir campos si no existen en la tabla (DESPUÉS de instanciar $db)
    try {
        $db->query("SHOW COLUMNS FROM aa_rutas LIKE 'tiene_extras'");
        if($db->count() === 0) {
            $db->query("ALTER TABLE aa_rutas ADD COLUMN tiene_extras TINYINT(1) NOT NULL DEFAULT 0");
        }
        $db->query("SHOW COLUMNS FROM aa_compras LIKE 'opcion_extras'");
        if($db->count() === 0) {
            $db->query("ALTER TABLE aa_compras ADD COLUMN opcion_extras TINYINT(1) NOT NULL DEFAULT 0");
        }
        // Verificar si existen los campos de ofertas
        $db->query("SHOW COLUMNS FROM aa_rutas LIKE 'en_oferta'");
        if($db->count() === 0) {
            $db->query("ALTER TABLE aa_rutas ADD COLUMN en_oferta TINYINT(1) NOT NULL DEFAULT 0");
        }
        $db->query("SHOW COLUMNS FROM aa_rutas LIKE 'porcentaje_oferta'");
        if($db->count() === 0) {
            $db->query("ALTER TABLE aa_rutas ADD COLUMN porcentaje_oferta DECIMAL(5,2) NOT NULL DEFAULT 0.00");
        }
    } catch (Exception $e) {
        // Ignorar errores de BD si ya existen los campos
    }
    
    // Manejar acción de eliminar
    if(Input::get('action') && Input::get('id')) {
        $ruta_id = Input::get('id');
        $ruta = $db->query("SELECT * FROM aa_rutas WHERE id = ?", [$ruta_id])->first();
        
        if($ruta && Input::get('action') == 'delete') {
            // Eliminar archivo de imagen
            if(!empty($ruta->imagen) && file_exists($abs_us_root.$us_url_root.$ruta->imagen)) {
                unlink($abs_us_root.$us_url_root.$ruta->imagen);
            }
            
            // Eliminar archivos GPX
            if(!empty($ruta->gpx)) {
                $base_filename = basename($ruta->gpx);
                $gpx_base_name = pathinfo($base_filename, PATHINFO_FILENAME);
                $gpx_extension = pathinfo($base_filename, PATHINFO_EXTENSION);
                
                // Eliminar GPX base
                if(file_exists($abs_us_root.$us_url_root.$ruta->gpx)) {
                    unlink($abs_us_root.$us_url_root.$ruta->gpx);
                }
                
                // Eliminar GPX extras
                $extras_path = $upload_gpx_dir . 'extras/' . $gpx_base_name . '_extras.' . $gpx_extension;
                if(file_exists($extras_path)) {
                    unlink($extras_path);
                }
            }
            
            $db->delete('aa_rutas', ['id' => $ruta_id]);
            Session::flash('success', 'Ruta eliminada exitosamente');
            Redirect::to('nueva_ruta.php');
        }
    }

    // Procesar envío de formulario para nueva ruta
    if(Input::exists()) {
        echo "<div class='alert alert-info'>🔄 Procesando datos del formulario...</div>";
        
        $required = [
            'nombre' => 'Nombre', 
            'descripcion' => 'Descripción',
            'nivel' => 'Nivel',
            'plan' => 'Plan',
            'paisaje' => 'Paisaje',
            'distancia' => 'Distancia',
            'tiempo' => 'Tiempo estimado',
            'descripcion_completa' => 'Descripción completa'
        ];
        
        // Solo requerir precio si es plan Premium
        if(Input::get('plan') == 'Premium') {
            $required['precio'] = 'Precio';
        }
        
        foreach($required as $field => $name) {
            if(empty(Input::get($field))) {
                $errors[] = "$name es requerido";
                echo "<div class='alert alert-warning'>❌ Campo faltante: $name</div>";
            }
        }

        // Validar que el paisaje seleccionado esté en la lista permitida
        if(!empty(Input::get('paisaje')) && !array_key_exists(Input::get('paisaje'), $tipos_paisajes)) {
            $errors[] = "Tipo de paisaje inválido";
        }

        // Procesar imagen
        $imagen_path = '';
        if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            echo "<div class='alert alert-info'>📸 Procesando imagen...</div>";
            $file_ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            if(in_array($file_ext, ['jpg','jpeg','png','webp'])) {
                $new_filename = 'ruta_'.strtolower(str_replace(' ','_',Input::get('nombre'))).'.'.$file_ext;
                $target_path = $upload_image_dir.$new_filename;
                
                if(move_uploaded_file($_FILES['imagen']['tmp_name'], $target_path)) {
                    $imagen_path = '../images/rutas/'.$new_filename;
                    echo "<div class='alert alert-success'>✅ Imagen subida: $new_filename</div>";
                } else {
                    $errors[] = "Error subiendo imagen";
                    echo "<div class='alert alert-danger'>❌ Error subiendo imagen</div>";
                }
            } else {
                $errors[] = "Formato de imagen inválido";
            }
        } else {
            $errors[] = "Se requiere una imagen";
            echo "<div class='alert alert-warning'>❌ No se encontró imagen o hubo error en la subida</div>";
        }

        // Generar nombres de archivo base para GPX
        $base_filename = 'ruta_'.strtolower(str_replace(' ','_',Input::get('nombre')));
        $isPremium = (Input::get('plan') == 'Premium');

        // Procesar GPX BASE
        $gpx_path = '';
        if(isset($_FILES['gpx_base']) && $_FILES['gpx_base']['error'] === UPLOAD_ERR_OK) {
            echo "<div class='alert alert-info'>🗺️ Procesando GPX base...</div>";
            $file_ext = strtolower(pathinfo($_FILES['gpx_base']['name'], PATHINFO_EXTENSION));
            if($file_ext === 'gpx') {
                $new_filename = $base_filename . '.gpx';
                $target_path = $upload_gpx_dir . 'base/' . $new_filename;
                
                if(move_uploaded_file($_FILES['gpx_base']['tmp_name'], $target_path)) {
                    $gpx_path = 'gpx/base/'.$new_filename;
                    echo "<div class='alert alert-success'>✅ GPX base subido: $new_filename</div>";
                } else {
                    $errors[] = "Error subiendo GPX base";
                    echo "<div class='alert alert-danger'>❌ Error subiendo GPX base</div>";
                }
            } else {
                $errors[] = "El archivo GPX debe ser .gpx";
            }
        } else {
            $errors[] = "Se requiere archivo GPX base";
            echo "<div class='alert alert-warning'>❌ No se encontró GPX base o hubo error en la subida</div>";
        }

        // Para rutas premium, procesar el GPX de extras (opcional)
        $tiene_extras = 0;
        
        if($isPremium) {
            // Procesar GPX EXTRAS (si se subió un archivo)
            if(isset($_FILES['gpx_extras']) && $_FILES['gpx_extras']['error'] === UPLOAD_ERR_OK) {
                echo "<div class='alert alert-info'>⭐ Procesando GPX extras...</div>";
                $file_ext = strtolower(pathinfo($_FILES['gpx_extras']['name'], PATHINFO_EXTENSION));
                if($file_ext === 'gpx') {
                    $new_filename = $base_filename . '_extras.gpx';
                    $target_path = $upload_gpx_dir . 'extras/' . $new_filename;
                    
                    if(move_uploaded_file($_FILES['gpx_extras']['tmp_name'], $target_path)) {
                        // Marcar que hay extras disponibles
                        $tiene_extras = 1;
                        echo "<div class='alert alert-success'>✅ GPX extras subido: $new_filename</div>";
                    } else {
                        $errors[] = "Error subiendo GPX con extras";
                        echo "<div class='alert alert-danger'>❌ Error subiendo GPX extras</div>";
                    }
                } else {
                    $errors[] = "El archivo GPX de extras debe ser .gpx";
                }
            } else {
                echo "<div class='alert alert-info'>ℹ️ No se subió GPX extras (opcional)</div>";
            }
        }

        // Validaciones numéricas
        if(Input::get('plan') == 'Premium' && (!is_numeric(Input::get('precio')) || Input::get('precio') < 0)) {
            $errors[] = "Precio inválido";
        }
        if(!is_numeric(Input::get('distancia')) || Input::get('distancia') < 0) $errors[] = "Distancia inválida";

        // Guardar nueva ruta
        if(empty($errors)) {
            echo "<div class='alert alert-success'>💾 Guardando ruta en base de datos...</div>";
            
            $fields = [
                'nombre' => Input::get('nombre'),
                'descripcion' => Input::get('descripcion'),
                'nivel' => Input::get('nivel'),
                'imagen' => $imagen_path,
                'plan' => Input::get('plan'),
                'paisaje' => Input::get('paisaje'),
                'precio' => Input::get('plan') == 'Premium' ? Input::get('precio') : 0,
                'gpx' => $gpx_path,
                'distancia' => Input::get('distancia'),
                'tiempo' => Input::get('tiempo'),
                'destacados' => null,  // ✅ Campo BD presente pero no usado en formulario
                'descripcion_completa' => strip_tags(Input::get('descripcion_completa'), '<p><br><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><a>'), // ✅ Permitir solo etiquetas básicas
                'tiene_extras' => $tiene_extras,
                'en_oferta' => 0,  // ✅ NUEVO: Campo por defecto
                'porcentaje_oferta' => 0.00  // ✅ NUEVO: Campo por defecto
            ];
            
            // Debug: Mostrar los datos que se van a insertar
            echo "<div class='alert alert-info'><strong>Datos a insertar:</strong><br>";
            foreach($fields as $key => $value) {
                echo "$key: $value<br>";
            }
            echo "</div>";
            
            try {
                $result = $db->insert('aa_rutas', $fields);
                $new_id = $db->lastId();
                echo "<div class='alert alert-success'>✅ Ruta creada exitosamente con ID: $new_id</div>";
                Session::flash('success', 'Ruta creada exitosamente');
                echo "<script>setTimeout(function(){ window.location.href = 'nueva_ruta.php'; }, 3000);</script>";
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>❌ Error insertando en BD: " . $e->getMessage() . "</div>";
                $errors[] = "Error de base de datos: " . $e->getMessage();
            }
        } else {
            echo "<div class='alert alert-danger'><strong>Errores encontrados:</strong><br>";
            foreach($errors as $error) {
                echo "• $error<br>";
            }
            echo "</div>";
        }
    }
} catch(Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error general: " . $e->getMessage() . "</div>";
    $errors[] = "Error: ".$e->getMessage();
}

if(Input::get('new')) {
    $mostrar_formulario = true;
}

// ✅ GENERADOR DE OPCIONES DE TIEMPO PARA EL CAMPO TIEMPO
$opciones_tiempo = [];
for ($horas = 1; $horas <= 20; $horas++) {
    // Hora exacta (ej: "1 hora", "2 horas")
    if ($horas == 1) {
        $opciones_tiempo[] = "1 hora";
    } else {
        $opciones_tiempo[] = "$horas horas";
    }
    
    // Hora y media (ej: "1 hora 30 minutos", "2 horas 30 minutos")
    if ($horas < 20) { // No agregar "20 horas 30 minutos"
        if ($horas == 1) {
            $opciones_tiempo[] = "1 hora 30 minutos";
        } else {
            $opciones_tiempo[] = "$horas horas 30 minutos";
        }
    }
}
?>

<style>
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

/* ✅ NUEVO: Estilos para ofertas */
.oferta-badge {
    position: absolute;
    top: 5px;
    left: 5px;
    background: linear-gradient(45deg, #ff6b6b, #ff4757);
    color: white;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    z-index: 10;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.precio-original {
    text-decoration: line-through;
    color: #6c757d;
    font-size: 0.9em;
}

.precio-oferta {
    color: #dc3545;
    font-weight: bold;
}

.precio-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
}

/* ✅ CSS adicional para personalizar el acordeón */

.accordion-button.bg-primary {
    border: none;
}
.accordion-button.bg-primary:not(.collapsed) {
    /* Color secundario al expandir */
    background-color: #ff6b00 !important;
    color: white !important;
}
.accordion-button.bg-primary:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
.accordion-button.bg-primary::after {
    filter: brightness(0) invert(1); /* Hace la flecha blanca */
}
</style>

<div class="container py-4">
    
    <!-- ✅ ACORDEÓN: Panel de Administración -->
<div class="accordion mb-4" id="accordionAdminRutas">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingAdminRutas">
            <button class="accordion-button collapsed bg-primary text-white" type="button" 
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminRutas" 
                    aria-expanded="false" aria-controls="collapseAdminRutas">
                <i class="fas fa-cogs me-2"></i> Administrar Rutas
                <span class="badge bg-light text-dark ms-2">
                    <?php 
                    // Contar rutas para mostrar en el badge
                    $db = DB::getInstance(); 
                    $count_rutas = $db->query("SELECT COUNT(*) as total FROM aa_rutas")->first();
                    echo $count_rutas->total ?? 0;
                    ?> rutas
                </span>
            </button>
        </h2>
        <div id="collapseAdminRutas" class="accordion-collapse collapse" 
             aria-labelledby="headingAdminRutas" data-bs-parent="#accordionAdminRutas">
            <div class="accordion-body p-0">
                <!-- Contenido original del card-body -->
                <div class="card-body">
                    <?php if(Session::exists('success')): ?>
                        <div class="alert alert-success"><?= Session::flash('success') ?></div>
                    <?php endif; ?>
                    <?php if(Session::exists('error')): ?>
                        <div class="alert alert-danger"><?= Session::flash('error') ?></div>
                    <?php endif; ?>
                    
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                        <?php 
                        // ✅ MEJORADO: Consulta que incluye campos de oferta
                        $db = DB::getInstance(); 
                        $rutas = $db->query("SELECT id, nombre, imagen, plan, nivel, paisaje, precio, en_oferta, porcentaje_oferta FROM aa_rutas ORDER BY id DESC")->results();
                        if(empty($rutas)): 
                        ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                No hay rutas disponibles. ¡Crea tu primera ruta!
                            </div>
                        </div>
                        <?php else: ?>
                        <?php foreach($rutas as $r): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="position-relative">
                                    <!-- ✅ NUEVO: Badge de oferta si está en oferta -->
                                    <?php if($r->en_oferta && $r->porcentaje_oferta > 0): ?>
                                    <div class="oferta-badge">
                                        <i class="fas fa-fire"></i> -<?= $r->porcentaje_oferta ?>%
                                    </div>
                                    <?php endif; ?>
                                    
                                    <a href='ruta_detalle.php?id=<?= $r->id ?>'>
                                        <img src="<?= $r->imagen ?: '../images/placeholder.jpg' ?>" class="card-img-top" alt="<?= $r->nombre ?>" style="height: 100px; object-fit: cover;">
                                    </a>
                                    <div class="position-absolute top-0 end-0 m-1">
                                        <span class="badge <?= $r->plan == 'Premium' ? 'bg-danger' : 'bg-secondary' ?>">
                                            <?= $r->plan ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title text-truncate mb-1" title="<?= $r->nombre ?>"><?= $r->nombre ?></h6>
                                    <small class="text-muted">
                                        <?php 
                                        // Mapeo de niveles antiguos a nuevos
                                        $nivel_mostrado = $r->nivel;
                                        if ($nivel_mostrado == 'Novato') $nivel_mostrado = 'Piloto nuevo';
                                        if ($nivel_mostrado == 'Intermedio') $nivel_mostrado = 'Domando Curvas';
                                        if ($nivel_mostrado == 'Experto') $nivel_mostrado = 'Maestro del Asfalto';
                                        ?>
                                        <span class="badge <?= in_array($nivel_mostrado, ['Piloto nuevo', 'Novato']) ? 'bg-info' : 
                                                           (in_array($nivel_mostrado, ['Domando Curvas', 'Intermedio']) ? 'bg-warning text-dark' : 'bg-danger') ?>">
                                            <?= $nivel_mostrado ?>
                                        </span>
                                        
                                        <!-- ✅ MEJORADO: Mostrar precio con oferta si aplica -->
                                        <?php if($r->plan == 'Premium'): ?>
                                            <?php if($r->en_oferta && $r->porcentaje_oferta > 0): ?>
                                                <div class="precio-container">
                                                    <span class="badge bg-secondary precio-original">
                                                        <?= number_format($r->precio, 2) ?>€
                                                    </span>
                                                    <span class="badge bg-danger precio-oferta">
                                                        <?php 
                                                        $precio_con_descuento = $r->precio * (1 - ($r->porcentaje_oferta / 100));
                                                        echo number_format($precio_con_descuento, 2);
                                                        ?>€
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <?= number_format($r->precio, 2) ?>€
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-success">Gratis</span>
                                        <?php endif; ?>
                                    </small>
                                    <div class="d-flex justify-content-between mt-2">
                                        <a href="editar_manual.php?id=<?= $r->id ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        
                                        <a href="galeria_ruta.php?id=<?= $r->id ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-images"></i> Galería
                                        </a>
                                        
                                        <a href="eliminar_manual.php?id=<?= $r->id ?>" 
                                           onclick="return confirm('¿Estás seguro de querer eliminar esta ruta permanentemente?')" 
                                           class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Botón Nueva Ruta -->
    <div class="mb-4 text-right">
        <a href="?new=true" id="btnNuevaRuta" class="btn btn-success">
            <i class="fas fa-plus"></i> Crear Nueva Ruta
        </a>
    </div>

    <!-- Formulario para Nueva Ruta -->
    <?php if($mostrar_formulario): ?>
    <!-- Incluir TinyMCE desde CDN con API key personalizada -->
    <script src="https://cdn.tiny.cloud/1/nhlsx7jkin6voponazn6x5mjea8yt6w7zn7ir3dwvu33jr4w/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="text-white">
                <i class="fas fa-plus-circle"></i> Crear Nueva Ruta
            </h3>
        </div>
        
        <div class="card-body">
            <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach($errors as $error): ?>
                    <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" action="nueva_ruta.php" id="formNuevaRuta">
                <input type="hidden" name="token" value="<?= Token::generate() ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <!-- Columna Izquierda -->
                        <div class="form-group">
                            <label>Nombre de la Ruta</label>
                            <input type="text" name="nombre" class="form-control" required value="<?= Input::get('nombre') ?? '' ?>">
                        </div>
                        
                        <!-- ✅ NUEVO: Selector de Paisajes Realistas -->
                        <div class="form-group">
                            <label for="paisaje"><i class="fas fa-mountain paisaje-icon"></i>Tipo de Paisaje</label>
                            <select class="form-control paisaje-select" id="paisaje" name="paisaje" required>
                                <option value="">-- Seleccionar tipo de paisaje --</option>
                                
                                <optgroup label="🏔️ Paisajes Montañosos">
                                    <option value="Montañas y bosques" <?= Input::get('paisaje') == 'Montañas y bosques' ? 'selected' : '' ?>>Montañas y bosques</option>
                                    <option value="Montañas y valles" <?= Input::get('paisaje') == 'Montañas y valles' ? 'selected' : '' ?>>Montañas y valles</option>
                                    <option value="Montañas y lagos" <?= Input::get('paisaje') == 'Montañas y lagos' ? 'selected' : '' ?>>Montañas y lagos</option>
                                    <option value="Sierra y bosques mediterráneos" <?= Input::get('paisaje') == 'Sierra y bosques mediterráneos' ? 'selected' : '' ?>>Sierra y bosques mediterráneos</option>
                                    <option value="Alta montaña y prados" <?= Input::get('paisaje') == 'Alta montaña y prados' ? 'selected' : '' ?>>Alta montaña y prados</option>
                                    <option value="Cordillera cantábrica" <?= Input::get('paisaje') == 'Cordillera cantábrica' ? 'selected' : '' ?>>Cordillera cantábrica</option>
                                    <option value="Pirineos y valles" <?= Input::get('paisaje') == 'Pirineos y valles' ? 'selected' : '' ?>>Pirineos y valles</option>
                                </optgroup>
                                
                                <optgroup label="🌊 Paisajes Costeros">
                                    <option value="Costa y acantilados" <?= Input::get('paisaje') == 'Costa y acantilados' ? 'selected' : '' ?>>Costa y acantilados</option>
                                    <option value="Costa y playas vírgenes" <?= Input::get('paisaje') == 'Costa y playas vírgenes' ? 'selected' : '' ?>>Costa y playas vírgenes</option>
                                    <option value="Costa mediterránea" <?= Input::get('paisaje') == 'Costa mediterránea' ? 'selected' : '' ?>>Costa mediterránea</option>
                                    <option value="Costa atlántica" <?= Input::get('paisaje') == 'Costa atlántica' ? 'selected' : '' ?>>Costa atlántica</option>
                                    <option value="Rías y acantilados" <?= Input::get('paisaje') == 'Rías y acantilados' ? 'selected' : '' ?>>Rías y acantilados</option>
                                    <option value="Costa y dunas" <?= Input::get('paisaje') == 'Costa y dunas' ? 'selected' : '' ?>>Costa y dunas</option>
                                </optgroup>
                                
                                <optgroup label="🏜️ Paisajes Áridos y Desérticos">
                                    <option value="Desierto y badlands" <?= Input::get('paisaje') == 'Desierto y badlands' ? 'selected' : '' ?>>Desierto y badlands</option>
                                    <option value="Estepa y llanuras" <?= Input::get('paisaje') == 'Estepa y llanuras' ? 'selected' : '' ?>>Estepa y llanuras</option>
                                    <option value="Paisaje volcánico árido" <?= Input::get('paisaje') == 'Paisaje volcánico árido' ? 'selected' : '' ?>>Paisaje volcánico árido</option>
                                    <option value="Mesetas y barrancos" <?= Input::get('paisaje') == 'Mesetas y barrancos' ? 'selected' : '' ?>>Mesetas y barrancos</option>
                                    <option value="Cañones y ramblas" <?= Input::get('paisaje') == 'Cañones y ramblas' ? 'selected' : '' ?>>Cañones y ramblas</option>
                                </optgroup>
                                
                                <optgroup label="🌲 Bosques y Valles">
                                    <option value="Bosques atlánticos" <?= Input::get('paisaje') == 'Bosques atlánticos' ? 'selected' : '' ?>>Bosques atlánticos</option>
                                    <option value="Bosques mediterráneos" <?= Input::get('paisaje') == 'Bosques mediterráneos' ? 'selected' : '' ?>>Bosques mediterráneos</option>
                                    <option value="Hayedos y montaña" <?= Input::get('paisaje') == 'Hayedos y montaña' ? 'selected' : '' ?>>Hayedos y montaña</option>
                                    <option value="Pinares y sierra" <?= Input::get('paisaje') == 'Pinares y sierra' ? 'selected' : '' ?>>Pinares y sierra</option>
                                    <option value="Valle y viñedos" <?= Input::get('paisaje') == 'Valle y viñedos' ? 'selected' : '' ?>>Valle y viñedos</option>
                                    <option value="Valle y cerezos" <?= Input::get('paisaje') == 'Valle y cerezos' ? 'selected' : '' ?>>Valle y cerezos</option>
                                    <option value="Dehesas y encinas" <?= Input::get('paisaje') == 'Dehesas y encinas' ? 'selected' : '' ?>>Dehesas y encinas</option>
                                </optgroup>
                                
                                <optgroup label="🏞️ Paisajes Especiales">
                                    <option value="Campo y pueblos blancos" <?= Input::get('paisaje') == 'Campo y pueblos blancos' ? 'selected' : '' ?>>Campo y pueblos blancos</option>
                                    <option value="Llanuras cerealistas" <?= Input::get('paisaje') == 'Llanuras cerealistas' ? 'selected' : '' ?>>Llanuras cerealistas</option>
                                    <option value="Humedales y marismas" <?= Input::get('paisaje') == 'Humedales y marismas' ? 'selected' : '' ?>>Humedales y marismas</option>
                                    <option value="Volcánico y laurisilva" <?= Input::get('paisaje') == 'Volcánico y laurisilva' ? 'selected' : '' ?>>Volcánico y laurisilva</option>
                                    <option value="Karst y dolinas" <?= Input::get('paisaje') == 'Karst y dolinas' ? 'selected' : '' ?>>Karst y dolinas</option>
                                    <option value="Ruta jacobea histórica" <?= Input::get('paisaje') == 'Ruta jacobea histórica' ? 'selected' : '' ?>>Ruta jacobea histórica</option>
                                    <option value="Transpirenaica" <?= Input::get('paisaje') == 'Transpirenaica' ? 'selected' : '' ?>>Transpirenaica</option>
                                    <option value="Alpujarra granadina" <?= Input::get('paisaje') == 'Alpujarra granadina' ? 'selected' : '' ?>>Alpujarra granadina</option>
                                </optgroup>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Elige la combinación de paisajes que mejor describa tu ruta
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label>Imagen de Portada</label>
                            <div class="custom-file">
                                <input type="file" name="imagen" class="custom-file-input" 
                                       accept="image/*" required>
                                <label class="custom-file-label">Seleccionar imagen</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Columna Derecha -->
                        <div class="form-group">
                            <label>Nivel de Dificultad</label>
                            <select name="nivel" class="form-control" required>
                                <?php foreach(['Piloto nuevo','Domando Curvas','Maestro del Asfalto'] as $nivel): ?>
                                <option value="<?= $nivel ?>" <?= Input::get('nivel') == $nivel ? 'selected' : '' ?>><?= $nivel ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Tipo de Plan</label>
                            <select name="plan" class="form-control" id="planSelect" required>
                                <option value="Gratis" <?= Input::get('plan') == 'Gratis' ? 'selected' : '' ?>>Gratis</option>
                                <option value="Premium" <?= Input::get('plan') == 'Premium' ? 'selected' : '' ?>>Premium</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Precio (€)</label>
                                    <input type="number" name="precio" step="0.01" 
                                           class="form-control" id="precioInput" value="<?= Input::get('precio') ?? '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Distancia (km)</label>
                                    <input type="number" name="distancia" step="0.1" 
                                           class="form-control" required value="<?= Input::get('distancia') ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campo de Tiempo Estimado con Desplegable -->
<div class="form-group">
    <label>Tiempo Estimado</label>
    <select name="tiempo" class="form-control" required>
        <option value="">-- Seleccionar tiempo estimado --</option>
        <?php foreach($opciones_tiempo as $tiempo_opcion): ?>
            <option value="<?= $tiempo_opcion ?>" 
                    <?= (Input::get('tiempo') == $tiempo_opcion) ? 'selected' : '' ?>>
                <?= $tiempo_opcion ?>
            </option>
        <?php endforeach; ?>
    </select>
    <small class="form-text text-muted">
        <i class="fas fa-clock"></i> Tiempo estimado total del recorrido
    </small>
</div>
                    </div>
                </div>

                <!-- Campos Adicionales -->
                <div class="form-group">
                    <label>Descripción Corta (máx. 255 caracteres)</label>
                    <textarea name="descripcion" class="form-control" rows="2" 
                              maxlength="255" required><?= Input::get('descripcion') ?? '' ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Descripción Completa</label>
                    <textarea id="editor" name="descripcion_completa" class="form-control" 
                              rows="8" required><?= Input::get('descripcion_completa') ?? '' ?></textarea>
                </div>

                <!-- Archivos GPX -->
                <div class="card mt-4 mb-4" id="gpxSection">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Archivos GPX</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Estructura de archivos GPX:
                            <ul class="mb-0">
                                <li><strong>Base:</strong> Ruta básica (requerido para todas las rutas)</li>
                                <li><strong>Extras:</strong> Ruta con puntos adicionales (opcional, solo para premium)</li>
                            </ul>
                        </div>

                        <!-- GPX Base (siempre visible) -->
                        <div class="form-group">
                            <label><i class="fas fa-route"></i> Archivo GPX Base</label>
                            <div class="custom-file">
                                <input type="file" name="gpx_base" class="custom-file-input" 
                                       accept=".gpx" required>
                                <label class="custom-file-label">Seleccionar archivo GPX base</label>
                            </div>
                        </div>

                        <!-- GPX Extras (solo para premium) -->
                        <div id="gpxPremiumSection" class="mt-4" style="display: none;">
                            <h6 class="border-bottom pb-2 mb-3">Archivo GPX con extras (opcional)</h6>
                            
                            <div class="form-group">
                                <label><i class="fas fa-star"></i> Archivo GPX con Extras</label>
                                <div class="custom-file">
                                    <input type="file" name="gpx_extras" class="custom-file-input" 
                                           accept=".gpx">
                                    <label class="custom-file-label">
                                        Seleccionar archivo GPX con extras
                                    </label>
                                </div>
                                <small class="form-text text-muted">Archivo con la ruta completa y puntos adicionales</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="text-center mt-4">
                    <button type="submit" id="btnGuardarRuta" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Guardar Ruta
                    </button>
                    <a href="nueva_ruta.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Script específico para el formulario -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    
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
        
        // Agregar manejador para el envío del formulario
        document.getElementById('formNuevaRuta').addEventListener('submit', function(e) {
            // Asegurarse de que TinyMCE guarde su contenido en el textarea
            tinymce.triggerSave();
            
            // Verificar si el editor tiene contenido
            var editorContent = document.getElementById('editor').value;
            if (!editorContent) {
                e.preventDefault();
                alert('Por favor, completa la descripción completa de la ruta.');
                return false;
            }
        });
        
        // Maneja la interacción con el formulario
        var planSelect = document.getElementById('planSelect');
        var precioInput = document.getElementById('precioInput');
        var gpxPremiumSection = document.getElementById('gpxPremiumSection');
        
        // Función para actualizar el formulario según el tipo de plan
        function actualizarFormulario() {
            if (planSelect.value === 'Premium') {
                precioInput.required = true;
                precioInput.closest('.form-group').style.display = 'block';
                gpxPremiumSection.style.display = 'block';
            } else {
                precioInput.required = false;
                precioInput.value = '0';
                gpxPremiumSection.style.display = 'none';
            }
        }
        
        // Inicializar estado
        actualizarFormulario();
        
        // Asignar evento al cambio de plan
        planSelect.addEventListener('change', actualizarFormulario);
        
        // Mostrar nombres de archivos seleccionados
        document.querySelectorAll('.custom-file-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo';
                this.nextElementSibling.textContent = fileName;
            });
        });
        
        
        
        // Agregar ID al botón para poder referenciarlo
        document.querySelector('button[type="submit"]').id = 'btnGuardarRuta';
        
        // Asegurarnos de que el botón "Guardar Ruta" funcione correctamente
        document.getElementById('btnGuardarRuta').addEventListener('click', function(e) {
            // Asegurarse de que TinyMCE guarde su contenido en el textarea
            tinymce.triggerSave();
            
            // El resto del procesamiento se hará por el evento submit del formulario
            console.log('Botón Guardar Ruta clickeado, formulario listo para enviar');
        });
    });
    </script>
    <?php endif; ?>
</div>

<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; ?>