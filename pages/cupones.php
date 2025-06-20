<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Verificar permisos de administrador
if(!hasPerm([2,4], $user->data()->id)) {
    Session::flash('error', 'Acceso denegado');
    Redirect::to('index.php');
}

try {
    $db = DB::getInstance();
    
    // Crear tablas si no existen
    $db->query("CREATE TABLE IF NOT EXISTS aa_cupones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(50) NOT NULL UNIQUE,
        descripcion TEXT,
        tipo_descuento ENUM('porcentaje', 'fijo') NOT NULL DEFAULT 'porcentaje',
        valor_descuento DECIMAL(8,2) NOT NULL,
        fecha_inicio DATETIME NOT NULL,
        fecha_fin DATETIME NOT NULL,
        usos_maximos INT NULL DEFAULT NULL,
        usos_actuales INT NOT NULL DEFAULT 0,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        aplicable_a ENUM('todos', 'premium', 'gratis') NOT NULL DEFAULT 'todos',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_codigo (codigo),
        INDEX idx_activo (activo),
        INDEX idx_fechas (fecha_inicio, fecha_fin)
    )");
    
    $db->query("CREATE TABLE IF NOT EXISTS aa_cupones_uso (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cupon_id INT NOT NULL,
        ruta_id INT NOT NULL,
        user_id INT NOT NULL,
        precio_original DECIMAL(8,2) NOT NULL,
        descuento_aplicado DECIMAL(8,2) NOT NULL,
        precio_final DECIMAL(8,2) NOT NULL,
        fecha_uso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY fk_cupon_uso (cupon_id),
        KEY fk_ruta_uso (ruta_id),
        KEY fk_user_uso (user_id)
    )");
    
    // Procesar acciones
    if(Input::exists()) {
        if(Input::get('action') == 'create') {
            // Crear nuevo cupón
            $errors = [];
            
            // Validaciones
            if(empty(Input::get('codigo'))) $errors[] = "El código es requerido";
            if(empty(Input::get('valor_descuento')) || Input::get('valor_descuento') <= 0) $errors[] = "El valor del descuento debe ser mayor a 0";
            if(empty(Input::get('fecha_inicio'))) $errors[] = "La fecha de inicio es requerida";
            if(empty(Input::get('fecha_fin'))) $errors[] = "La fecha de fin es requerida";
            
            // Validar que el código no exista
            $exists = $db->query("SELECT id FROM aa_cupones WHERE codigo = ?", [Input::get('codigo')])->first();
            if($exists) $errors[] = "Ya existe un cupón con ese código";
            
            // Validar rangos de descuento
            if(Input::get('tipo_descuento') == 'porcentaje' && Input::get('valor_descuento') > 100) {
                $errors[] = "El porcentaje no puede ser mayor a 100%";
            }
            
            if(empty($errors)) {
                $data = [
                    'codigo' => strtoupper(trim(Input::get('codigo'))),
                    'descripcion' => Input::get('descripcion'),
                    'tipo_descuento' => Input::get('tipo_descuento'),
                    'valor_descuento' => Input::get('valor_descuento'),
                    'fecha_inicio' => Input::get('fecha_inicio'),
                    'fecha_fin' => Input::get('fecha_fin'),
                    'usos_maximos' => Input::get('usos_maximos') ?: null,
                    'activo' => Input::get('activo') ? 1 : 0,
                    'aplicable_a' => Input::get('aplicable_a')
                ];
                
                if($db->insert('aa_cupones', $data)) {
                    Session::flash('success', 'Cupón creado exitosamente');
                    Redirect::to('cupones.php');
                } else {
                    $errors[] = "Error al crear el cupón";
                }
            }
        }
        
        if(Input::get('action') == 'edit' && Input::get('id')) {
            // Editar cupón existente
            $cupon_id = Input::get('id');
            $errors = [];
            
            // Validaciones similares a create
            if(empty(Input::get('codigo'))) $errors[] = "El código es requerido";
            if(empty(Input::get('valor_descuento')) || Input::get('valor_descuento') <= 0) $errors[] = "El valor del descuento debe ser mayor a 0";
            
            // Validar que el código no exista en otros cupones
            $exists = $db->query("SELECT id FROM aa_cupones WHERE codigo = ? AND id != ?", [Input::get('codigo'), $cupon_id])->first();
            if($exists) $errors[] = "Ya existe otro cupón con ese código";
            
            if(Input::get('tipo_descuento') == 'porcentaje' && Input::get('valor_descuento') > 100) {
                $errors[] = "El porcentaje no puede ser mayor a 100%";
            }
            
            if(empty($errors)) {
                $data = [
                    'codigo' => strtoupper(trim(Input::get('codigo'))),
                    'descripcion' => Input::get('descripcion'),
                    'tipo_descuento' => Input::get('tipo_descuento'),
                    'valor_descuento' => Input::get('valor_descuento'),
                    'fecha_inicio' => Input::get('fecha_inicio'),
                    'fecha_fin' => Input::get('fecha_fin'),
                    'usos_maximos' => Input::get('usos_maximos') ?: null,
                    'activo' => Input::get('activo') ? 1 : 0,
                    'aplicable_a' => Input::get('aplicable_a')
                ];
                
                if($db->update('aa_cupones', $cupon_id, $data)) {
                    Session::flash('success', 'Cupón actualizado exitosamente');
                    Redirect::to('cupones.php');
                } else {
                    $errors[] = "Error al actualizar el cupón";
                }
            }
        }
        
        if(Input::get('action') == 'delete' && Input::get('id')) {
            // Eliminar cupón
            $cupon_id = Input::get('id');
            if($db->delete('aa_cupones', ['id' => $cupon_id])) {
                Session::flash('success', 'Cupón eliminado exitosamente');
            } else {
                Session::flash('error', 'Error al eliminar el cupón');
            }
            Redirect::to('cupones.php');
        }
        
        if(Input::get('action') == 'toggle' && Input::get('id')) {
            // Activar/desactivar cupón
            $cupon_id = Input::get('id');
            $cupon = $db->query("SELECT activo FROM aa_cupones WHERE id = ?", [$cupon_id])->first();
            if($cupon) {
                $nuevo_estado = $cupon->activo ? 0 : 1;
                $db->update('aa_cupones', $cupon_id, ['activo' => $nuevo_estado]);
                Session::flash('success', 'Estado del cupón actualizado');
            }
            Redirect::to('cupones.php');
        }
    }
    
    // Obtener lista de cupones
    $cupones = $db->query("
        SELECT c.*, 
               COUNT(cu.id) as total_usos,
               SUM(cu.descuento_aplicado) as total_descuentos
        FROM aa_cupones c 
        LEFT JOIN aa_cupones_uso cu ON c.id = cu.cupon_id 
        GROUP BY c.id 
        ORDER BY c.created_at DESC
    ")->results();
    
    // Obtener cupón para editar si se especifica
    $cupon_editar = null;
    if(isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $cupon_editar = $db->query("SELECT * FROM aa_cupones WHERE id = ?", [$_GET['edit']])->first();
    }
    
} catch(Exception $e) {
    Session::flash('error', 'Error: ' . $e->getMessage());
    $cupones = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cupones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cupon-activo { background-color: #d4edda; }
        .cupon-inactivo { background-color: #f8d7da; }
        .cupon-expirado { background-color: #fff3cd; }
        .badge-custom { font-size: 0.8em; }
        .form-container { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .stats-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-ticket-alt text-primary"></i> Gestión de Cupones</h1>
                    <a href="nueva_ruta.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Rutas
                    </a>
                </div>

                <!-- Mensajes de estado -->
                <?php if(Session::exists('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?= Session::flash('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(Session::exists('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?= Session::flash('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h6>Se encontraron errores:</h6>
                        <ul class="mb-0">
                            <?php foreach($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Estadísticas rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <h3><?= count($cupones) ?></h3>
                                <p class="mb-0">Total Cupones</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3><?= count(array_filter($cupones, function($c) { return $c->activo; })) ?></h3>
                                <p class="mb-0">Activos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3><?= array_sum(array_column($cupones, 'total_usos')) ?></h3>
                                <p class="mb-0">Usos Totales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3><?= number_format(array_sum(array_column($cupones, 'total_descuentos')), 2) ?>€</h3>
                                <p class="mb-0">Descuentos Dados</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de creación/edición -->
                <div class="form-container">
                    <h3>
                        <i class="fas fa-plus-circle"></i> 
                        <?= $cupon_editar ? 'Editar Cupón' : 'Crear Nuevo Cupón' ?>
                    </h3>
                    
                    <form method="post" class="row g-3">
                        <input type="hidden" name="action" value="<?= $cupon_editar ? 'edit' : 'create' ?>">
                        <?php if($cupon_editar): ?>
                            <input type="hidden" name="id" value="<?= $cupon_editar->id ?>">
                        <?php endif; ?>

                        <div class="col-md-6">
                            <label class="form-label">Código del Cupón *</label>
                            <input type="text" name="codigo" class="form-control" 
                                   value="<?= $cupon_editar ? $cupon_editar->codigo : (Input::get('codigo') ?? '') ?>" 
                                   required maxlength="50" style="text-transform: uppercase;">
                            <small class="text-muted">Ejemplo: DESCUENTO20, WELCOME10</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Aplicable a</label>
                            <select name="aplicable_a" class="form-select" required>
                                <option value="todos" <?= ($cupon_editar && $cupon_editar->aplicable_a == 'todos') || Input::get('aplicable_a') == 'todos' ? 'selected' : '' ?>>Todas las rutas</option>
                                <option value="premium" <?= ($cupon_editar && $cupon_editar->aplicable_a == 'premium') || Input::get('aplicable_a') == 'premium' ? 'selected' : '' ?>>Solo rutas Premium</option>
                                <option value="gratis" <?= ($cupon_editar && $cupon_editar->aplicable_a == 'gratis') || Input::get('aplicable_a') == 'gratis' ? 'selected' : '' ?>>Solo rutas Gratis</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo de Descuento *</label>
                            <select name="tipo_descuento" class="form-select" id="tipoDescuento" required>
                                <option value="porcentaje" <?= ($cupon_editar && $cupon_editar->tipo_descuento == 'porcentaje') || Input::get('tipo_descuento') == 'porcentaje' ? 'selected' : '' ?>>Porcentaje (%)</option>
                                <option value="fijo" <?= ($cupon_editar && $cupon_editar->tipo_descuento == 'fijo') || Input::get('tipo_descuento') == 'fijo' ? 'selected' : '' ?>>Valor Fijo (€)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Valor del Descuento *</label>
                            <div class="input-group">
                                <input type="number" name="valor_descuento" class="form-control" 
                                       value="<?= $cupon_editar ? $cupon_editar->valor_descuento : (Input::get('valor_descuento') ?? '') ?>" 
                                       step="0.01" min="0.01" required>
                                <span class="input-group-text" id="unidadDescuento">%</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Usos Máximos</label>
                            <input type="number" name="usos_maximos" class="form-control" 
                                   value="<?= $cupon_editar ? $cupon_editar->usos_maximos : (Input::get('usos_maximos') ?? '') ?>" 
                                   min="1">
                            <small class="text-muted">Dejar vacío para ilimitado</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha de Inicio *</label>
                            <input type="datetime-local" name="fecha_inicio" class="form-control" 
                                   value="<?= $cupon_editar ? date('Y-m-d\TH:i', strtotime($cupon_editar->fecha_inicio)) : (Input::get('fecha_inicio') ?? '') ?>" 
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Fecha de Fin *</label>
                            <input type="datetime-local" name="fecha_fin" class="form-control" 
                                   value="<?= $cupon_editar ? date('Y-m-d\TH:i', strtotime($cupon_editar->fecha_fin)) : (Input::get('fecha_fin') ?? '') ?>" 
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2" 
                                      placeholder="Descripción opcional del cupón..."><?= $cupon_editar ? $cupon_editar->descripcion : (Input::get('descripcion') ?? '') ?></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="activo" class="form-check-input" id="activo" 
                                       <?= ($cupon_editar && $cupon_editar->activo) || (!$cupon_editar && !Input::get('activo') === false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">
                                    Cupón activo
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $cupon_editar ? 'Actualizar Cupón' : 'Crear Cupón' ?>
                            </button>
                            <?php if($cupon_editar): ?>
                                <a href="cupones.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar Edición
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Lista de cupones -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-list"></i> Cupones Existentes</h4>
                    </div>
                    <div class="card-body p-0">
                        <?php if(empty($cupones)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay cupones creados</h5>
                                <p class="text-muted">Crea tu primer cupón usando el formulario de arriba</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Descuento</th>
                                            <th>Aplicable a</th>
                                            <th>Vigencia</th>
                                            <th>Usos</th>
                                            <th>Estado</th>
                                            <th>Total Ahorrado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($cupones as $cupon): ?>
                                            <?php
                                            $ahora = new DateTime();
                                            $inicio = new DateTime($cupon->fecha_inicio);
                                            $fin = new DateTime($cupon->fecha_fin);
                                            $expirado = $ahora > $fin;
                                            $no_iniciado = $ahora < $inicio;
                                            $agotado = $cupon->usos_maximos && $cupon->total_usos >= $cupon->usos_maximos;
                                            
                                            $clase_fila = '';
                                            if(!$cupon->activo) $clase_fila = 'cupon-inactivo';
                                            elseif($expirado || $agotado) $clase_fila = 'cupon-expirado';
                                            elseif($cupon->activo && !$no_iniciado) $clase_fila = 'cupon-activo';
                                            ?>
                                            <tr class="<?= $clase_fila ?>">
                                                <td>
                                                    <strong><?= $cupon->codigo ?></strong>
                                                    <?php if($cupon->descripcion): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($cupon->descripcion) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info badge-custom">
                                                        <?= $cupon->tipo_descuento == 'porcentaje' ? 
                                                            $cupon->valor_descuento.'%' : 
                                                            number_format($cupon->valor_descuento, 2).'€' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= 
                                                        $cupon->aplicable_a == 'todos' ? 'bg-primary' : 
                                                        ($cupon->aplicable_a == 'premium' ? 'bg-danger' : 'bg-success') 
                                                    ?> badge-custom">
                                                        <?= ucfirst($cupon->aplicable_a) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>
                                                        <strong>Desde:</strong> <?= date('d/m/Y H:i', strtotime($cupon->fecha_inicio)) ?><br>
                                                        <strong>Hasta:</strong> <?= date('d/m/Y H:i', strtotime($cupon->fecha_fin)) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?= $cupon->total_usos ?> / <?= $cupon->usos_maximos ?: '∞' ?>
                                                    <?php if($agotado): ?>
                                                        <br><span class="badge bg-warning badge-custom">Agotado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if(!$cupon->activo): ?>
                                                        <span class="badge bg-secondary badge-custom">Inactivo</span>
                                                    <?php elseif($expirado): ?>
                                                        <span class="badge bg-warning badge-custom">Expirado</span>
                                                    <?php elseif($no_iniciado): ?>
                                                        <span class="badge bg-info badge-custom">Próximamente</span>
                                                    <?php elseif($agotado): ?>
                                                        <span class="badge bg-warning badge-custom">Agotado</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success badge-custom">Activo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($cupon->total_descuentos ?: 0, 2) ?>€</strong>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?edit=<?= $cupon->id ?>" class="btn btn-outline-primary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="?action=toggle&id=<?= $cupon->id ?>" 
                                                           class="btn btn-outline-<?= $cupon->activo ? 'warning' : 'success' ?>" 
                                                           title="<?= $cupon->activo ? 'Desactivar' : 'Activar' ?>">
                                                            <i class="fas fa-<?= $cupon->activo ? 'pause' : 'play' ?>"></i>
                                                        </a>
                                                        <a href="?action=delete&id=<?= $cupon->id ?>" 
                                                           class="btn btn-outline-danger" 
                                                           title="Eliminar"
                                                           onclick="return confirm('¿Estás seguro de eliminar este cupón?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 20rem;"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cambiar unidad del descuento según el tipo
        document.getElementById('tipoDescuento').addEventListener('change', function() {
            const unidad = document.getElementById('unidadDescuento');
            unidad.textContent = this.value === 'porcentaje' ? '%' : '€';
        });
        
        // Convertir código a mayúsculas automáticamente
        document.querySelector('input[name="codigo"]').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>