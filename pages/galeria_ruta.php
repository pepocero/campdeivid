<?php
require_once '../users/init.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

if(!hasPerm([2,4], $user->data()->id)) {
    die("Acceso denegado");
}

// Obtener ID de la ruta
$ruta_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if(!$ruta_id) {
    Session::flash('error', 'ID de ruta no especificado');
    Redirect::to('nueva_ruta.php');
}

try {
    $db = DB::getInstance();
    
    // Verificar que la ruta existe
    $ruta = $db->query("SELECT * FROM aa_rutas WHERE id = ?", [$ruta_id])->first();
    if(!$ruta) {
        Session::flash('error', 'Ruta no encontrada');
        Redirect::to('nueva_ruta.php');
    }
    
    // Crear tabla de galería si no existe
    $db->query("CREATE TABLE IF NOT EXISTS aa_rutas_galeria (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ruta_id INT NOT NULL,
        imagen VARCHAR(255) NOT NULL,
        orden INT NOT NULL DEFAULT 0,
        descripcion VARCHAR(255),
        fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ruta_id) REFERENCES aa_rutas(id) ON DELETE CASCADE
    )");
    
    // Configuración de directorios
    $abs_us_root = isset($abs_us_root) ? $abs_us_root : '';
    $us_url_root = isset($us_url_root) ? $us_url_root : '';
    
    // Crear nombre de carpeta de galería
    $ruta_nombre_limpio = strtolower(str_replace(' ', '_', $ruta->nombre));
    $gallery_dir_name = $ruta_nombre_limpio . '_gallery';
    $gallery_path = $abs_us_root.$us_url_root.'images/rutas/'.$gallery_dir_name.'/';
    $gallery_url = 'images/rutas/'.$gallery_dir_name.'/';
    
    // Crear directorio de galería si no existe
    if (!file_exists($gallery_path)) {
        mkdir($gallery_path, 0755, true);
    }
    
    $success_messages = [];
    $error_messages = [];
    
    // Procesar eliminación de imagen
    if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $imagen_id = intval($_GET['delete']);
        $imagen = $db->query("SELECT * FROM aa_rutas_galeria WHERE id = ? AND ruta_id = ?", [$imagen_id, $ruta_id])->first();
        
        if($imagen) {
            // Eliminar archivo físico
            $imagen_path = $abs_us_root.$us_url_root.$imagen->imagen;
            if(file_exists($imagen_path)) {
                unlink($imagen_path);
            }
            
            // Eliminar de la base de datos
            $db->delete('aa_rutas_galeria', ['id' => $imagen_id]);
            $success_messages[] = "Imagen eliminada correctamente";
        }
        
        // Redireccionar para evitar reenvío de formulario
        Redirect::to("galeria_ruta.php?id=$ruta_id");
    }
    
    // Procesar subida de imágenes (AJAX)
    if(isset($_POST['subir_imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
        $uploaded_count = 0;
        $total_files = count($_FILES['imagenes']['name']);
        
        for($i = 0; $i < $total_files; $i++) {
            if($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION));
                
                if(in_array($file_ext, ['jpg','jpeg','png','webp'])) {
                    // Generar nombre único para el archivo
                    $timestamp = time();
                    $random = mt_rand(100, 999);
                    $new_filename = "img_{$timestamp}_{$random}_{$i}.{$file_ext}";
                    $target_path = $gallery_path . $new_filename;
                    
                    if(move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $target_path)) {
                        // Obtener el próximo número de orden
                        $orden_result = $db->query("SELECT MAX(orden) as max_orden FROM aa_rutas_galeria WHERE ruta_id = ?", [$ruta_id])->first();
                        $nuevo_orden = $orden_result ? $orden_result->max_orden + 1 : 1;
                        
                        // Guardar en la base de datos
                        $db->insert('aa_rutas_galeria', [
                            'ruta_id' => $ruta_id,
                            'imagen' => $gallery_url . $new_filename,
                            'orden' => $nuevo_orden,
                            'descripcion' => isset($_POST['descripciones'][$i]) ? $_POST['descripciones'][$i] : ''
                        ]);
                        
                        $uploaded_count++;
                    } else {
                        $error_messages[] = "Error al subir la imagen: " . $_FILES['imagenes']['name'][$i];
                    }
                } else {
                    $error_messages[] = "Formato inválido para: " . $_FILES['imagenes']['name'][$i];
                }
            }
        }
        
        // Si es una petición AJAX, responder con JSON
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'uploaded' => $uploaded_count,
                'total' => $total_files,
                'errors' => $error_messages
            ]);
            exit;
        }
        
        // Si no es AJAX, continuar con el flujo normal
        if($uploaded_count > 0) {
            $success_messages[] = "Se subieron $uploaded_count imagen(es) correctamente";
        }
        
        if(!empty($success_messages) || !empty($error_messages)) {
            if(!empty($success_messages)) Session::flash('success', implode('<br>', $success_messages));
            if(!empty($error_messages)) Session::flash('error', implode('<br>', $error_messages));
            Redirect::to("galeria_ruta.php?id=$ruta_id");
        }
    }
    
    // Procesar actualización de orden y descripciones
    if(isset($_POST['actualizar_galeria'])) {
        $imagenes_data = $_POST['imagenes'] ?? [];
        
        foreach($imagenes_data as $imagen_id => $data) {
            $db->update('aa_rutas_galeria', $imagen_id, [
                'orden' => intval($data['orden']),
                'descripcion' => $data['descripcion']
            ]);
        }
        
        Session::flash('success', 'Galería actualizada correctamente');
        Redirect::to("galeria_ruta.php?id=$ruta_id");
    }
    
    // Obtener imágenes existentes
    $imagenes_galeria = $db->query("SELECT * FROM aa_rutas_galeria WHERE ruta_id = ? ORDER BY orden ASC, id ASC", [$ruta_id])->results();
    
} catch(Exception $e) {
    $error_messages[] = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería - <?= $ruta->nombre ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .gallery-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 25px;
            margin-bottom: 30px;
        }
        .image-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            cursor: move;
        }
        .image-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 8px rgba(0,123,255,0.2);
        }
        .image-card.sortable-ghost {
            opacity: 0.5;
        }
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 4px;
        }
        .upload-area {
            border: 3px dashed #007bff;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            background-color: #e3f2fd;
            border-color: #0056b3;
        }
        .upload-area.dragover {
            background-color: #e3f2fd;
            border-color: #0056b3;
        }
        .btn-action {
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 20px;
        }
        .order-badge {
            position: absolute;
            top: -10px;
            left: -10px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        .drag-handle {
            cursor: move;
            color: #6c757d;
            margin-right: 10px;
        }
        .drag-handle:hover {
            color: #007bff;
        }
        /* Estilos para el botón de eliminar en vista previa */
        .preview-delete-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
            transition: all 0.2s ease;
        }
        .preview-delete-btn:hover {
            background-color: #c82333;
            transform: scale(1.1);
        }
        .preview-card {
            position: relative;
            transition: all 0.3s ease;
        }
        .preview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="gallery-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-images"></i> Galería de <?= $ruta->nombre ?></h2>
                    <p class="text-muted mb-0">Gestiona las imágenes de la galería de esta ruta</p>
                </div>
                <a href="nueva_ruta.php" class="btn btn-secondary btn-action">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            
            <?php if(Session::exists('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= Session::flash('success') ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if(Session::exists('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= Session::flash('error') ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Sección de subida de imágenes -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cloud-upload-alt"></i> Subir Nuevas Imágenes</h5>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" id="uploadForm">
                        <input type="hidden" name="ruta_id" value="<?= $ruta_id ?>">
                        
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                            <h4>Arrastra las imágenes aquí o haz clic para seleccionar</h4>
                            <p class="text-muted">Soporta: JPG, JPEG, PNG, WEBP (máximo 10 imágenes)</p>
                            <input type="file" name="imagenes[]" id="imageInput" multiple accept="image/*" class="d-none">
                        </div>
                        
                        <!-- Barra de progreso -->
                        <div id="uploadProgressContainer" class="mt-3" style="display: none;">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subiendo imágenes...</span>
                                <span id="progressPercentage">0%</span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: 0%"
                                     aria-valuenow="0" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small id="uploadStatus" class="text-muted">Preparando archivos...</small>
                            </div>
                        </div>
                        
                        <div id="previewContainer" class="mt-4" style="display: none;">
                            <h6>Vista previa de imágenes seleccionadas:</h6>
                            <div id="previewImages" class="row"></div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" name="subir_imagenes" class="btn btn-success btn-lg btn-action">
                                <i class="fas fa-upload"></i> Subir Imágenes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Galería existente -->
            <?php if(!empty($imagenes_galeria)): ?>
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-th-large"></i> Imágenes de la Galería (<?= count($imagenes_galeria) ?>)</h5>
                    <small><i class="fas fa-arrows-alt"></i> Arrastra para reordenar</small>
                </div>
                <div class="card-body">
                    <form method="post" id="galleryForm">
                        <div id="sortableGallery">
                            <?php foreach($imagenes_galeria as $index => $imagen): ?>
                            <div class="image-card position-relative" data-id="<?= $imagen->id ?>">
                                <div class="order-badge"><?= $imagen->orden ?></div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-grip-vertical drag-handle"></i>
                                            <strong>Imagen <?= $index + 1 ?></strong>
                                        </div>
                                        <img src="../<?= $imagen->imagen ?>" alt="Imagen de galería" class="image-preview img-thumbnail">
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label>Descripción (opcional)</label>
                                            <input type="text" 
                                                   name="imagenes[<?= $imagen->id ?>][descripcion]" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($imagen->descripcion) ?>" 
                                                   placeholder="Describe esta imagen...">
                                        </div>
                                        <div class="form-group">
                                            <label>Orden</label>
                                            <input type="number" 
                                                   name="imagenes[<?= $imagen->id ?>][orden]" 
                                                   class="form-control orden-input" 
                                                   value="<?= $imagen->orden ?>" 
                                                   min="1" readonly>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> Subida: <?= date('d/m/Y H:i', strtotime($imagen->fecha_subida)) ?>
                                        </small>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <a href="?id=<?= $ruta_id ?>&delete=<?= $imagen->id ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Estás seguro de eliminar esta imagen?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" name="actualizar_galeria" class="btn btn-primary btn-lg btn-action">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay imágenes en la galería</h4>
                    <p class="text-muted">Sube algunas imágenes para crear la galería de esta ruta</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Configurar área de subida con drag & drop
        const uploadArea = document.getElementById('uploadArea');
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewImages = document.getElementById('previewImages');
        const uploadForm = document.getElementById('uploadForm');
        
        // Elementos de progreso
        const progressContainer = document.getElementById('uploadProgressContainer');
        const progressBar = document.getElementById('uploadProgressBar');
        const progressPercentage = document.getElementById('progressPercentage');
        const uploadStatus = document.getElementById('uploadStatus');
        
        // Almacenar los archivos seleccionados para poder eliminarlos
        let selectedFiles = [];
        
        // Eventos de drag & drop
        uploadArea.addEventListener('click', () => imageInput.click());
        uploadArea.addEventListener('dragover', handleDragOver);
        uploadArea.addEventListener('drop', handleDrop);
        uploadArea.addEventListener('dragenter', e => e.preventDefault());
        uploadArea.addEventListener('dragleave', handleDragLeave);
        
        imageInput.addEventListener('change', handleFileSelect);
        
        function handleDragOver(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        }
        
        function handleDragLeave(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        }
        
        function handleDrop(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            handleFileSelect({ target: { files } });
        }
        
        function handleFileSelect(e) {
            const files = Array.from(e.target.files);
            
            if (files.length > 0) {
                // Almacenar los archivos seleccionados
                selectedFiles = [...files];
                
                updatePreview();
            }
        }
        
        // Función para actualizar la vista previa basada en los archivos seleccionados
        function updatePreview() {
            if (selectedFiles.length > 0) {
                previewContainer.style.display = 'block';
                previewImages.innerHTML = '';
                
                selectedFiles.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-md-3 mb-3 preview-item';
                            col.dataset.index = index;
                            col.innerHTML = `
                                <div class="card preview-card">
                                    <div class="preview-delete-btn" data-index="${index}">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <small class="text-muted">${file.name}</small>
                                        <input type="text" name="descripciones[${index}]" class="form-control form-control-sm mt-1" placeholder="Descripción...">
                                    </div>
                                </div>
                            `;
                            previewImages.appendChild(col);
                            
                            // Añadir event listener al botón de eliminar
                            col.querySelector('.preview-delete-btn').addEventListener('click', function() {
                                removeFile(parseInt(this.dataset.index));
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewContainer.style.display = 'none';
            }
        }
        
        // Función para eliminar un archivo de la vista previa
        function removeFile(index) {
            // Eliminar el archivo del array
            selectedFiles.splice(index, 1);
            
            if (selectedFiles.length === 0) {
                // Si no quedan archivos, limpiar el input file
                imageInput.value = '';
                previewContainer.style.display = 'none';
            } else {
                // Actualizar la vista previa
                updatePreview();
                
                // Crear un nuevo objeto FileList (esto es complicado porque FileList es inmutable)
                // En su lugar, vamos a crear un DataTransfer y usar su propiedad files
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                
                // Asignar la nueva lista de archivos al input
                imageInput.files = dataTransfer.files;
            }
        }
        
        // Manejar envío del formulario con AJAX
        uploadForm.addEventListener('submit', function(e) {
            if (e.submitter && e.submitter.name === 'subir_imagenes') {
                e.preventDefault();
                
                // Verificar si hay archivos para subir
                if (selectedFiles.length === 0) {
                    alert('Por favor, selecciona al menos una imagen para subir.');
                    return;
                }
                
                const formData = new FormData(uploadForm);
                
                // Eliminar los archivos existentes en el formData (que vienen del input file)
                formData.delete('imagenes[]');
                
                // Añadir los archivos seleccionados manualmente
                selectedFiles.forEach((file, index) => {
                    formData.append('imagenes[]', file);
                });
                
                formData.append('subir_imagenes', '1');
                
                // Mostrar barra de progreso
                progressContainer.style.display = 'block';
                uploadArea.style.display = 'none';
                previewContainer.style.display = 'none';
                
                // Resetear progreso
                updateProgress(0);
                uploadStatus.textContent = 'Iniciando subida...';
                
                // Deshabilitar el botón de subir
                const submitButton = e.submitter;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
                
                const xhr = new XMLHttpRequest();
                
                // Manejar progreso de subida
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        updateProgress(percentComplete);
                        
                        if (percentComplete < 100) {
                            uploadStatus.textContent = `Subiendo archivos... ${formatBytes(e.loaded)} de ${formatBytes(e.total)}`;
                        } else {
                            uploadStatus.textContent = 'Procesando imágenes en el servidor...';
                        }
                    }
                });
                
                // Manejar respuesta
                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                uploadStatus.textContent = '¡Subida completada con éxito!';
                                progressBar.classList.remove('progress-bar-animated');
                                progressBar.classList.add('bg-success');
                                
                                // Esperar un momento y recargar la página
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                handleUploadError('Error al procesar las imágenes.');
                            }
                        } catch (e) {
                            handleUploadError('Error en la respuesta del servidor.');
                        }
                    } else {
                        handleUploadError('Error en el servidor. Por favor, intenta de nuevo.');
                    }
                });
                
                // Manejar errores
                xhr.addEventListener('error', function() {
                    handleUploadError('Error de conexión. Por favor, verifica tu conexión a internet.');
                });
                
                xhr.addEventListener('abort', function() {
                    handleUploadError('Subida cancelada.');
                });
                
                // Enviar petición
                xhr.open('POST', uploadForm.action || window.location.href);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            }
        });
        
        function updateProgress(percent) {
            progressBar.style.width = percent + '%';
            progressBar.setAttribute('aria-valuenow', percent);
            progressPercentage.textContent = percent + '%';
        }
        
        function handleUploadError(message) {
            uploadStatus.textContent = message;
            uploadStatus.classList.remove('text-muted');
            uploadStatus.classList.add('text-danger');
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-danger');
            
            // Reactivar el formulario después de 3 segundos
            setTimeout(function() {
                progressContainer.style.display = 'none';
                uploadArea.style.display = 'block';
                if (selectedFiles.length > 0) {
                    previewContainer.style.display = 'block';
                }
                
                const submitButton = document.querySelector('button[name="subir_imagenes"]');
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-upload"></i> Subir Imágenes';
                
                // Resetear estados
                uploadStatus.classList.remove('text-danger');
                uploadStatus.classList.add('text-muted');
                progressBar.classList.remove('bg-danger');
            }, 3000);
        }
        
        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Configurar Sortable para reordenar imágenes
        <?php if(!empty($imagenes_galeria)): ?>
        const sortableGallery = document.getElementById('sortableGallery');
        Sortable.create(sortableGallery, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                // Actualizar los números de orden
                const items = sortableGallery.children;
                for (let i = 0; i < items.length; i++) {
                    const orderBadge = items[i].querySelector('.order-badge');
                    const orderInput = items[i].querySelector('.orden-input');
                    const newOrder = i + 1;
                    
                    orderBadge.textContent = newOrder;
                    orderInput.value = newOrder;
                }
            }
        });
        <?php endif; ?>
    });
    </script>
</body>
</html>