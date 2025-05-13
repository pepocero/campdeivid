<?php
require_once $_SERVER['DOCUMENT_ROOT']."/ini_folder_camp.php";
require_once $_SERVER['DOCUMENT_ROOT'].$folder."/users/init.php";
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Verificar permisos
if(!hasPerm([2], $user->data()->id)) {
    Session::flash('error', 'No tienes permiso para acceder a esta página');
    Redirect::to('index.php');
}

// Procesar el formulario
if(Input::exists()) {
    $errors = [];
    
    // Validar campos
    $required = [
        'nombre' => 'Nombre',
        'descripcion' => 'Descripción',
        'nivel' => 'Nivel',
        'imagen' => 'Imagen URL',
        'gpx' => 'Archivo GPX',
        'plan' => 'Plan',
        'paisaje' => 'Paisaje',
        'precio' => 'Precio',
        'distancia' => 'Distancia',
        'tiempo' => 'Tiempo estimado',
        'destacados' => 'Puntos destacados',
        'descripcion_completa' => 'Descripción completa'
    ];
    
    foreach($required as $field => $name) {
        if(empty(Input::get($field))) {
            $errors[] = "El campo {$name} es requerido";
        }
    }
    
    // Validación específica para la imagen (URL completa)
    if(!filter_var(Input::get('imagen'), FILTER_VALIDATE_URL)) {
        $errors[] = "La URL de la imagen no es válida";
    }
    
    // Validación modificada para el GPX (ruta relativa)
    $gpx = Input::get('gpx');
    if(!preg_match('/^[a-zA-Z0-9_\-\.\/]+\.gpx$/i', $gpx)) {
        $errors[] = "La ruta del archivo GPX debe ser relativa y terminar en .gpx (ej: gpx/miruta.gpx)";
    }
    
    // Validar precio
    if(!is_numeric(Input::get('precio')) || Input::get('precio') < 0) {
        $errors[] = "El precio debe ser un número positivo";
    }
    
    // Si no hay errores, insertar en la base de datos
    if(empty($errors)) {
        try {
            $db = DB::getInstance();
            $fields = [
                'nombre' => Input::get('nombre'),
                'descripcion' => Input::get('descripcion'),
                'nivel' => Input::get('nivel'),
                'imagen' => Input::get('imagen'),                
                'plan' => Input::get('plan'),
                'paisaje' => Input::get('paisaje'),
                'precio' => Input::get('precio'),
                'gpx' => Input::get('gpx'),
                'distancia' => Input::get('distancia'),
                'tiempo' => Input::get('tiempo'),
                'destacados' => Input::get('destacados'),
                'descripcion_completa' => Input::get('descripcion_completa')
            ];
            
            $db->insert('aa_rutas', $fields);
            $id = $db->lastId();
            
            Session::flash('home', 'Ruta agregada exitosamente!');
            Redirect::to('ruta_detalle.php?id='.$id);
        } catch(Exception $e) {
            $errors[] = "Error al guardar en la base de datos: ".$e->getMessage();
        }
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">Agregar Nueva Ruta</h1>
            
            <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach($errors as $error): ?>
                    <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form action="" method="post" class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Información básica -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                Información Básica
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nombre" class="required-field">Nombre de la Ruta</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?= Input::get('nombre') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="descripcion" class="required-field">Descripción Corta</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" 
                                              rows="3" required><?= Input::get('descripcion') ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="descripcion_completa" class="required-field">Descripción Completa</label>
                                    <textarea class="form-control" id="descripcion_completa" name="descripcion_completa" 
                                              rows="5" required><?= Input::get('descripcion_completa') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Detalles técnicos -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                Detalles Técnicos
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nivel" class="required-field">Nivel de Dificultad</label>
                                    <select class="form-control" id="nivel" name="nivel" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Novato" <?= Input::get('nivel') == 'Novato' ? 'selected' : '' ?>>Novato</option>
                                        <option value="Intermedio" <?= Input::get('nivel') == 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                                        <option value="Experto" <?= Input::get('nivel') == 'Experto' ? 'selected' : '' ?>>Experto</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="plan" class="required-field">Plan</label>
                                    <select class="form-control" id="plan" name="plan" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Gratis" <?= Input::get('plan') == 'Gratis' ? 'selected' : '' ?>>Gratis</option>
                                        <option value="Premium" <?= Input::get('plan') == 'Premium' ? 'selected' : '' ?>>Premium</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="paisaje" class="required-field">Tipo de Paisaje</label>
                                    <input type="text" class="form-control" id="paisaje" name="paisaje" 
                                           value="<?= Input::get('paisaje') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="precio" class="required-field">Precio (€)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" 
                                           value="<?= Input::get('precio') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="gpx" class="required-field">Ruta del Archivo GPX</label>
                                    <input type="text" class="form-control" id="gpx" name="gpx" value="<?= Input::get('gpx') ?>" placeholder="gpx/nombredelarchivo.gpx" required>
                                    <small class="form-text text-muted">Ruta relativa al archivo GPX (ej: gpx/miruta.gpx)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="imagen" class="required-field">URL de la Imagen</label>
                                    <input type="url" class="form-control" id="imagen" name="imagen" 
                                           value="<?= Input::get('imagen') ?>" placeholder="https://ejemplo.com/imagen.jpg" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="distancia" class="required-field">Distancia (km)</label>
                                    <input type="number" step="0.1" min="0" class="form-control" id="distancia" name="distancia" 
                                           value="<?= Input::get('distancia') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tiempo" class="required-field">Tiempo Estimado</label>
                                    <input type="text" class="form-control" id="tiempo" name="tiempo" 
                                           value="<?= Input::get('tiempo') ?>" placeholder="Ej: 3 horas" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="destacados" class="required-field">Puntos Destacados</label>
                                    <textarea class="form-control" id="destacados" name="destacados" 
                                              rows="3" placeholder="Separados por comas" required><?= Input::get('destacados') ?></textarea>
                                    <small class="form-text text-muted">Ej: Mirador, Restaurante típico, Museo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mb-4">
                    <input type="hidden" name="token" value="<?= Token::generate() ?>">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save"></i> Guardar Ruta
                    </button>
                    <a href="rutas.php" class="btn btn-secondary btn-lg px-5 ml-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; ?>