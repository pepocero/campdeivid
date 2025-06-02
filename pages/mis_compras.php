<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

$userId = $user->data()->id;
$db = DB::getInstance();

// Consulta para obtener las compras del usuario
$compras = $db->query("
    SELECT c.*, r.nombre, r.gpx, r.imagen, r.tiene_extras, r.nivel,
           c.opcion_repostaje, c.opcion_hoteles, c.opcion_puntos
    FROM aa_compras c
    JOIN aa_rutas r ON c.ruta_id = r.id
    WHERE c.user_id = ?
    ORDER BY c.fecha_compra DESC
", [$userId])->results();
?>


<div class="container mt-5">
    <h2 class="mb-4">Mis Compras</h2>

    <?php if(Session::exists('success')): ?>
        <div class="alert alert-success"><?= Session::flash('success') ?></div>
    <?php elseif(Session::exists('info')): ?>
        <div class="alert alert-info"><?= Session::flash('info') ?></div>
    <?php endif; ?>

    <?php if(empty($compras)): ?>
        <div class="alert alert-warning">No has comprado ninguna ruta todavía.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
            <?php foreach($compras as $compra): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="<?= $compra->imagen ?: 'https://via.placeholder.com/600x300' ?>" 
                             class="card-img-top" alt="<?= $compra->nombre ?>"
                             style="height: 120px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge <?= ($compra->estado_pago == 'COMPLETED') ? 'bg-success' : 'bg-warning' ?>">
                                <?= $compra->estado_pago ?: 'Pendiente' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="card-title text-truncate" title="<?= $compra->nombre ?>"><?= $compra->nombre ?></h6>
                        
                        <!-- Badge de nivel -->
                        <div class="mb-2">
                            <span class="badge <?= 
                                $compra->nivel == 'Piloto nuevo' ? 'bg-info' : 
                                ($compra->nivel == 'Domando Curvas' ? 'bg-warning text-dark' : 'bg-danger') 
                            ?>">
                                <i class="fas fa-road me-1"></i> <?= $compra->nivel ?>
                            </span>
                        </div>
                        
                        <div class="small text-muted mb-2">
                            <?= number_format($compra->precio_pagado, 2) ?>€ • <?= date('d/m/Y', strtotime($compra->fecha_compra)) ?>
                        </div>
                        
                        <?php
                        // Determinar qué versión del GPX debe descargarse
                        $nombreBase = pathinfo(basename($compra->gpx), PATHINFO_FILENAME);
                        $extension = pathinfo($compra->gpx, PATHINFO_EXTENSION);
                        
                        // Por defecto, utilizar la ruta base
                        $rutaArchivo = $us_url_root . 'gpx/base/' . $nombreBase . '.' . $extension;
                        $descripcion = "Ruta básica";
                        $tieneExtras = false;
                        
                        // Si tiene compra premium con opciones y la ruta tiene extras
                        if ($compra->tiene_extras && ($compra->opcion_repostaje || $compra->opcion_hoteles || $compra->opcion_puntos)) {
                            // Verificar si el archivo de extras existe físicamente
                            $extras_file = $nombreBase . '_extras.' . $extension;
                            $extras_path = $abs_us_root . $us_url_root . 'gpx/extras/' . $extras_file;
                            
                            if (file_exists($extras_path)) {
                                $rutaArchivo = $us_url_root . 'gpx/extras/' . $extras_file;
                                $descripcion = "Ruta con extras";
                                $tieneExtras = true;
                            }
                        }
                        ?>
                        
                        <div class="d-grid gap-2">
                            <a href="<?= $rutaArchivo ?>" class="btn btn-sm btn-success" download>
                                <i class="fas fa-download"></i> Descargar GPX
                                <?php if($tieneExtras): ?>
                                <span class="badge bg-info">+extras</span>
                                <?php endif; ?>
                            </a>
                            <small class="text-muted text-center"><?= $descripcion ?></small>
                        </div>
                    </div>
                    <div class="card-footer p-2 bg-light">
                        <small class="text-muted d-block text-truncate" title="ID: <?= $compra->paypal_transaction_id ?>">
                            <i class="fas fa-receipt"></i> <?= $compra->paypal_transaction_id ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; ?>