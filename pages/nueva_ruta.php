<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Añadir campo tiene_extras si no existe en la tabla
try {
    $db->query("SHOW COLUMNS FROM aa_rutas LIKE 'tiene_extras'");
    if($db->count() === 0) {
        $db->query("ALTER TABLE aa_rutas ADD COLUMN tiene_extras TINYINT(1) NOT NULL DEFAULT 0");
    }
    $db->query("SHOW COLUMNS FROM aa_compras LIKE 'opcion_extras'");
    if($db->count() === 0) {
        $db->query("ALTER TABLE aa_compras ADD COLUMN opcion_extras TINYINT(1) NOT NULL DEFAULT 0");
    }
} catch (Exception $e) {
    // Ignorar errores
}

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
            $base_filename = basename($ruta->gpx);
            $gpx_base_name = pathinfo($base_filename, PATHINFO_FILENAME);
            $gpx_extension = pathinfo($base_filename, PATHINFO_EXTENSION);
            
            // Eliminar GPX base
            if(!empty($ruta->gpx) && file_exists($abs_us_root.$us_url_root.$ruta->gpx)) {
                unlink($abs_us_root.$us_url_root.$ruta->gpx);
            }
            
            // Eliminar GPX extras
            $extras_path = $upload_gpx_dir . 'extras/' . $gpx_base_name . '_extras.' . $gpx_extension;
            if(file_exists($extras_path)) {
                unlink($extras_path);
            }
            
            $db->delete('aa_rutas', ['id' => $ruta_id]);
            Session::flash('success', 'Ruta eliminada exitosamente');
            Redirect::to('nueva_ruta.php');
        }
    }

    // Procesar envío de formulario para nueva ruta
    if(Input::exists()) {
        $required = [
            'nombre' => 'Nombre', 
            'descripcion' => 'Descripción',
            'nivel' => 'Nivel',
            'plan' => 'Plan',
            'paisaje' => 'Paisaje',
            'distancia' => 'Distancia',
            'tiempo' => 'Tiempo estimado',
            'destacados' => 'Puntos destacados',
            'descripcion_completa' => 'Descripción completa'
        ];
        
        // Solo requerir precio si es plan Premium
        if(Input::get('plan') == 'Premium') {
            $required['precio'] = 'Precio';
        }
        
        foreach($required as $field => $name) {
            if(empty(Input::get($field))) $errors[] = "$name es requerido";
        }

        // Procesar imagen
        $imagen_path = '';
        if($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $file_ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            if(in_array($file_ext, ['jpg','jpeg','png','webp'])) {
                $new_filename = 'ruta_'.strtolower(str_replace(' ','_',Input::get('nombre'))).'.'.$file_ext;
                $target_path = $upload_image_dir.$new_filename;
                
                if(move_uploaded_file($_FILES['imagen']['tmp_name'], $target_path)) {
                    $imagen_path = '../images/rutas/'.$new_filename;
                } else {
                    $errors[] = "Error subiendo imagen";
                }
            } else {
                $errors[] = "Formato de imagen inválido";
            }
        } else {
            $errors[] = "Se requiere una imagen";
        }

        // Generar nombres de archivo base para GPX
        $base_filename = 'ruta_'.strtolower(str_replace(' ','_',Input::get('nombre')));
        $isPremium = (Input::get('plan') == 'Premium');

        // Procesar GPX BASE
        $gpx_path = '';
        if($_FILES['gpx_base']['error'] === UPLOAD_ERR_OK) {
            $file_ext = strtolower(pathinfo($_FILES['gpx_base']['name'], PATHINFO_EXTENSION));
            if($file_ext === 'gpx') {
                $new_filename = $base_filename . '.gpx';
                $target_path = $upload_gpx_dir . 'base/' . $new_filename;
                
                if(move_uploaded_file($_FILES['gpx_base']['tmp_name'], $target_path)) {
                    $gpx_path = 'gpx/base/'.$new_filename;
                } else {
                    $errors[] = "Error subiendo GPX base";
                }
            } else {
                $errors[] = "El archivo GPX debe ser .gpx";
            }
        } else {
            $errors[] = "Se requiere archivo GPX base";
        }

        // Para rutas premium, procesar el GPX de extras (opcional)
        $tiene_extras = 0;
        
        if($isPremium) {
            // Procesar GPX EXTRAS (si se subió un archivo)
            if(isset($_FILES['gpx_extras']) && $_FILES['gpx_extras']['error'] === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($_FILES['gpx_extras']['name'], PATHINFO_EXTENSION));
                if($file_ext === 'gpx') {
                    $new_filename = $base_filename . '_extras.gpx';
                    $target_path = $upload_gpx_dir . 'extras/' . $new_filename;
                    
                    if(move_uploaded_file($_FILES['gpx_extras']['tmp_name'], $target_path)) {
                        // Marcar que hay extras disponibles
                        $tiene_extras = 1;
                    } else {
                        $errors[] = "Error subiendo GPX con extras";
                    }
                } else {
                    $errors[] = "El archivo GPX de extras debe ser .gpx";
                }
            }
        }

        // Validaciones numéricas
        if(Input::get('plan') == 'Premium' && (!is_numeric(Input::get('precio')) || Input::get('precio') < 0)) {
            $errors[] = "Precio inválido";
        }
        if(!is_numeric(Input::get('distancia')) || Input::get('distancia') < 0) $errors[] = "Distancia inválida";

        // Guardar nueva ruta
        if(empty($errors)) {
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
                'destacados' => Input::get('destacados'),
                'descripcion_completa' => Input::get('descripcion_completa'),
                'tiene_extras' => $tiene_extras
            ];
            
            $db->insert('aa_rutas', $fields);
            $new_id = $db->lastId();
            Session::flash('success', 'Ruta creada exitosamente');
            Redirect::to('nueva_ruta.php');
        }
    }
} catch(Exception $e) {
    $errors[] = "Error: ".$e->getMessage();
}

if(Input::get('new')) {
    $mostrar_formulario = true;
}
?>

<div class="container py-4">
    
    <!-- Panel de Administración -->
    <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="text-white"><i class="fas fa-cogs"></i> Administrar Rutas</h4>
        </div>
        <div class="card-body">
            <?php if(Session::exists('success')): ?>
                <div class="alert alert-success"><?= Session::flash('success') ?></div>
            <?php endif; ?>
            <?php if(Session::exists('error')): ?>
                <div class="alert alert-danger"><?= Session::flash('error') ?></div>
            <?php endif; ?>
            
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                <?php 
                // Forzar a obtener datos frescos de la base de datos
                $db = DB::getInstance(); 
                $rutas = $db->query("SELECT id, nombre, imagen, plan, nivel, paisaje, precio FROM aa_rutas ORDER BY nombre")->results();
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
                                <span class="badge <?= $r->plan == 'Premium' ? 'bg-secondary text-dark' : 'bg-success' ?>">
                                    <?= $r->plan == 'Premium' ? number_format($r->precio, 2) . '€' : 'Gratis' ?>
                                </span>
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
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Paisaje</label>
                            <input type="text" name="paisaje" class="form-control" required>
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
                                <option value="<?= $nivel ?>"><?= $nivel ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Tipo de Plan</label>
                            <select name="plan" class="form-control" id="planSelect" required>
                                <option value="Gratis">Gratis</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Precio (€)</label>
                                    <input type="number" name="precio" step="0.01" 
                                           class="form-control" id="precioInput">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Distancia (km)</label>
                                    <input type="number" name="distancia" step="0.1" 
                                           class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Tiempo Estimado</label>
                            <input type="text" name="tiempo" class="form-control"
                                   placeholder="Ej: 3 horas" required>
                        </div>
                    </div>
                </div>

                <!-- Campos Adicionales -->
                <div class="form-group">
                    <label>Descripción Corta (máx. 255 caracteres)</label>
                    <textarea name="descripcion" class="form-control" rows="2" 
                              maxlength="255" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Descripción Completa</label>
                    <textarea id="editor" name="descripcion_completa" class="form-control" 
                              rows="8" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Puntos Destacados (separar con comas)</label>
                    <textarea name="destacados" id="puntosDestacados" class="form-control" rows="2" 
                              required></textarea>
                    <small class="form-text text-muted">Cada punto comenzará automáticamente con mayúscula</small>
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
        // Inicializar TinyMCE en el campo de descripción completa
        tinymce.init({
            selector: '#editor',
            height: 400,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                     'bold italic backcolor | alignleft aligncenter ' +
                     'alignright alignjustify | bullist numlist outdent indent | ' +
                     'removeformat | fontselect fontsizeselect | help',
            font_formats: 'Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats',
            fontsize_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            language: 'es',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-langs/langs/es.js',
            branding: false,
            promotion: false,
            browser_spellcheck: true,
            contextmenu: false,
            setup: function(editor) {
                // Asegurarse de que TinyMCE guarde el contenido antes del envío
                editor.on('change', function() {
                    tinymce.triggerSave();
                });
            }
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
        
        // Formatear automáticamente los puntos destacados
        var puntosDestacados = document.getElementById('puntosDestacados');
        if (puntosDestacados) {
            puntosDestacados.addEventListener('input', function() {
                // Guardar la posición del cursor
                var start = this.selectionStart;
                var end = this.selectionEnd;
                
                // Obtener el texto actual
                var textoOriginal = this.value;
                var textoModificado = textoOriginal;
                
                // Eliminar puntos antes de comas
                textoModificado = textoModificado.replace(/\.\s*,/g, ',');
                
                // Ajustar posición del cursor si se eliminaron puntos antes de la posición actual
                var offset = 0;
                for (var i = 0; i < start; i++) {
                    // Si había un punto antes de una coma y se eliminó
                    if (i < textoOriginal.length && i < textoModificado.length) {
                        if (textoOriginal.charAt(i) === '.' && 
                            i + 1 < textoOriginal.length && 
                            textoOriginal.charAt(i + 1) === ',' &&
                            textoModificado.charAt(i) === ',') {
                            offset--;
                        }
                    }
                }
                
                // Contar espacios que se añadirán después de comas sin espacios
                var comaSinEspacio = /,([^\s])/g;
                var match;
                var espaciosAñadidos = 0;
                
                while ((match = comaSinEspacio.exec(textoModificado.slice(0, start + offset))) !== null) {
                    if (match.index < start + offset) {
                        espaciosAñadidos++;
                    }
                }
                
                // Capitalizar la primera letra después de cada coma
                textoModificado = textoModificado.replace(/,\s*([a-z])/g, function(match, letter) {
                    return ', ' + letter.toUpperCase();
                });
                
                // Asegurar que hay un espacio después de cada coma
                textoModificado = textoModificado.replace(/,([^\s])/g, ', $1');
                
                // Capitalizar la primera letra de todo el texto
                if (textoModificado.length > 0) {
                    textoModificado = textoModificado.charAt(0).toUpperCase() + textoModificado.slice(1);
                }
                
                // Actualizar el valor del textarea
                this.value = textoModificado;
                
                // Restaurar la posición del cursor, ajustando por los cambios realizados
                this.selectionStart = start + offset + espaciosAñadidos;
                this.selectionEnd = end + offset + espaciosAñadidos;
            });
        }
        
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