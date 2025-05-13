<?php
require_once $_SERVER['DOCUMENT_ROOT']."/ini_folder_camp.php";
require_once $_SERVER['DOCUMENT_ROOT'].$folder."/users/init.php";
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Obtener ID de la ruta desde la URL
$ruta_id = Input::get('id');
if(!$ruta_id || !is_numeric($ruta_id)) {
    Redirect::to('rutas.php');
}

// Consultar la ruta específica con todos los campos
try {
    $db = DB::getInstance();
    $query = $db->query("SELECT * FROM aa_rutas WHERE id = ?", [$ruta_id]);
    $ruta = $query->first();
    
    if(!$ruta) {
        Session::flash('error', 'Ruta no encontrada');
        Redirect::to('rutas.php');
    }
} catch(Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Procesar compra si se envió el formulario
if(Input::exists() && Token::check(Input::get('token'))) {
    $user_id = $user->data()->id;
    $ruta_id = $ruta->id;
    $precio = $ruta->precio;
    
    $db->insert('aa_compras', [
        'user_id' => $user_id,
        'ruta_id' => $ruta_id,
        'precio' => $precio,
        'fecha' => date('Y-m-d H:i:s'),
        'estado' => 'completo'
    ]);
    
    Session::flash('home', '¡Compra realizada con éxito! La ruta ha sido añadida a tu cuenta.');
    Redirect::to('cuenta.php');
}
?>

<div class="row">
    <div class="col-md-8">
        <!-- Imagen principal -->
        <div class="card mb-4">
            <img src="<?= $ruta->imagen ?: 'https://via.placeholder.com/800x400?text=Motocicleta' ?>" 
                 class="card-img-top" 
                 alt="<?= $ruta->nombre ?>"
                 style="max-height: 500px; object-fit: cover;">
        </div>
        
        <!-- Descripción detallada -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title"><?= $ruta->nombre ?></h2>
                <p class="card-text"><?= $ruta->descripcion ?></p>
                
                <h4 class="mt-4">Detalles de la Ruta</h4>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Nivel:</strong> 
                        <span class="badge <?= 
                            $ruta->nivel == 'Novato' ? 'bg-info' : 
                            ($ruta->nivel == 'Intermedio' ? 'bg-warning text-dark' : 'bg-danger') 
                        ?>">
                            <?= $ruta->nivel ?>
                        </span>
                    </li>
                    <li class="list-group-item"><strong>Paisaje:</strong> 
                        <span class="badge bg-success"><?= $ruta->paisaje ?></span>
                    </li>
                    <li class="list-group-item"><strong>Tipo:</strong> 
                        <span class="badge <?= $ruta->plan == 'Premium' ? 'bg-danger' : 'bg-secondary' ?>">
                            <?= $ruta->plan ?>
                        </span>
                    </li>
                    <li class="list-group-item"><strong>Distancia:</strong> <?= $ruta->distancia ?> km</li>
                    <li class="list-group-item"><strong>Tiempo estimado:</strong> <?= $ruta->tiempo ?></li>
                    <!-- <li class="list-group-item"><strong>Puntos de interés:</strong> <?= $ruta->destacados ?></li> -->
                </ul>
                
                <h4>Descripción completa</h4>
                <p><?= nl2br($ruta->descripcion_completa) ?></p>
                
                <?php if(!empty($ruta->destacados)): ?>
                <h4 class="mt-4">Puntos destacados</h4>
                <div class="row">
                    <?php 
                    $puntos_destacados = explode(",", $ruta->destacados);
                    foreach($puntos_destacados as $punto): 
                    ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-map-marker-alt text-primary"></i> <?= trim($punto) ?></h5>
                                <p class="card-text text-muted">Lugar destacado en esta ruta</p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Panel de compra -->
        <div class="card d-md-block sticky-top" style="top: 80px; z-index: 1030;">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-white">Comprar Ruta</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <span class="display-4 text-primary fw-bolder"><?= number_format($ruta->precio, 2) ?>€</span>
                    <p class="text-muted">Precio final</p>
                </div>
                
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Archivo GPX
                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Guía de viaje PDF
                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= $ruta->distancia ?> km de ruta
                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Puntos destacados: <?= $ruta->destacados ?> 
                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                    </li>
                </ul>
                
                <?php if($user->isLoggedIn()): ?>
                    <form action="" method="post">
                        <input type="hidden" name="token" value="<?= Token::generate() ?>">
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                            <i class="fas fa-shopping-cart"></i> Comprar Ahora
                        </button>
                    </form>
                    
                    <?php if($ruta->plan == 'Premium'): ?>
                        <div class="mt-3 text-center">
                            <a href="suscripcion.php" class="btn btn-outline-primary">
                                <i class="fas fa-crown"></i> Suscríbete y ahorra
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php?redirect=ruta_detalle.php?id=<?= $ruta->id ?>" 
                       class="btn btn-warning btn-lg w-100 py-3">
                        <i class="fas fa-sign-in-alt"></i> Inicia sesión para comprar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Mapa de la ruta 
<div class="card mb-4">
    <div class="card-header bg-light">
        <h4 class="mb-0">Mapa de la Ruta</h4>
    </div>
    <div class="card-body p-0" style="height: 400px;">
        <div id="mapa-ruta" style="height: 100%; width: 100%;">
            <img src="https://maps.googleapis.com/maps/api/staticmap?center=<?= urlencode('37.7749,-122.4194') ?>&zoom=12&size=800x400&maptype=roadmap&key=TU_API_KEY" 
                 alt="Mapa de la ruta" 
                 style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>
</div>
-->
<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>