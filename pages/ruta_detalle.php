<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Obtener datos de la ruta
$ruta_id = Input::get('id');
if(!$ruta_id || !is_numeric($ruta_id)) Redirect::to('rutas.php');



try {
    $db = DB::getInstance();
    $ruta = $db->query("SELECT * FROM aa_rutas WHERE id = ?", [$ruta_id])->first();
    if(!$ruta) throw new Exception('Ruta no encontrada');
    
    // Preparar datos para meta tags
    $titulo = $ruta->nombre;
    $descripcion = $ruta->descripcion ?: 'Descubre esta increíble ruta en moto';
    $imagen_path = $ruta->imagen ?: 'images/hero/NuestraExperiencia.png';
    
    // Limpiar ruta de imagen y crear URL web completa
    $imagen_limpia = str_replace('../', '', $imagen_path);
    
    // Crear URLs web completas (no rutas del sistema)
    $protocolo = 'https://'; // Siempre usar HTTPS para redes sociales
    $servidor = $_SERVER['HTTP_HOST'];
    $base_url = $protocolo . $servidor;
    
    // URL completa de la imagen para meta tags
    $imagen_url = $base_url . "/" . $imagen_limpia;
    
    // URL actual de la página
    $url_actual = $base_url . $us_url_root . "pages/ruta_detalle.php?id=" . $ruta_id;

    // Determinar si es ruta premium
    $esPremium = ($ruta->plan == 'Premium');
    $precio_base = (float)$ruta->precio;
    
    // ✅ NUEVA LÓGICA DE OFERTAS
    $tiene_oferta = false;
    $porcentaje_descuento = 0;
    $precio_final = $precio_base;
    $ahorro = 0;
    
    // Verificar si la ruta tiene oferta activa
    if ($esPremium && property_exists($ruta, 'en_oferta') && $ruta->en_oferta == 1) {
        if (property_exists($ruta, 'porcentaje_oferta') && $ruta->porcentaje_oferta > 0) {
            $tiene_oferta = true;
            $porcentaje_descuento = (float)$ruta->porcentaje_oferta;
            $precio_final = $precio_base - ($precio_base * $porcentaje_descuento / 100);
            $ahorro = $precio_base - $precio_final;
        }
    }
    
    // Obtener ruta del archivo GPX desde la base de datos
    $gpxPath = $ruta->gpx;
    $gpxFile = $us_url_root.$gpxPath;
    $gpxExists = !empty($gpxPath);    
} catch(Exception $e) {
    Session::flash('error', $e->getMessage());
    Redirect::to('pages/rutas.php');
}



?>

<style>
/* Estilos generales basados en la paleta granate */
:root {
    --granate-base: #800000;
    --granate-hover: #990000;
    --granate-alert-text: #B30000;
    --granate-alert-bg: #FFE5E5;
    --granate-alert-border: #FF9999;
    --granate-dark: #660000;
    --granate-light: #FFCCCC;
}

.card-header.bg-primary {
    background-color: var(--granate-base) !important;
    border-bottom: 3px solid var(--granate-dark);
}

.btn-primary {
    background-color: var(--granate-base);
    border-color: var(--granate-dark);
}

.btn-primary:hover {
    background-color: var(--granate-hover);
    border-color: var(--granate-dark);
}

/* Mejoras específicas para la tarjeta lateral */
.card.sticky-top {
    border: 1px solid var(--granate-alert-border);
    box-shadow: 0 6px 12px rgba(128, 0, 0, 0.15) !important;
}

/* Estilos para los badges */
.badge.bg-info {
    background-color: #0d6efd !important;
}
.badge.bg-warning {
    background-color: #ffc107 !important;
}
.badge.bg-danger {
    background-color: var(--granate-base) !important;
}
.badge.bg-success {
    background-color: #198754 !important;
}
.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Mejoras para las alertas */
.alert {
    border-left: 4px solid;
}
.alert-danger {
    background-color: var(--granate-alert-bg);
    border-color: var(--granate-alert-text);
    color: var(--granate-alert-text);
}
.alert-warning {
    background-color: #FFF3CD;
    border-color: #FFC107;
    color: #856404;
}

/* Estilo para los puntos destacados */
.card.h-100 {
    transition: all 0.3s ease;
    border: 1px solid var(--granate-alert-border);
}
.card.h-100:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(128, 0, 0, 0.2);
}

/* Mejoras en los checkboxes */
.form-check-input:checked {
    background-color: var(--granate-base);
    border-color: var(--granate-dark);
}

.form-check-input:disabled {
    opacity: 0.5;
}

/* Estilo para el precio total */
h3.text-primary {
    color: var(--granate-base) !important;
    font-weight: 700;
    font-size: 1.8rem;
}

/* Efecto hover para botones */
.btn-lg {
    transition: all 0.3s ease;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(128, 0, 0, 0.3);
}

/* Mejoras en la imagen principal */
.card-img-top {
    border-bottom: 3px solid var(--granate-base);
}

/* Estilo para la información de ruta gratuita */
.free-route-info {
    background-color: rgba(25, 135, 84, 0.1);
    border-left: 4px solid #198754;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 20px;
}

.free-route-info h5 {
    color: #198754;
    font-weight: 600;
    margin-bottom: 15px;
}

.download-btn-container {
    text-align: center;
    margin-top: 15px;
}

/* ✅ NUEVOS ESTILOS PARA OFERTAS */
.precio-oferta-container {
    background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
    border: 2px solid #ffeaa7;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(255, 193, 7, 0.1);
}

.precio-original {
    text-decoration: line-through;
    color: #6c757d;
    font-size: 1.2rem;
    font-weight: 500;
    margin-bottom: 5px;
}

.precio-con-descuento {
    color: #dc3545;
    font-weight: bold;
    font-size: 2rem;
    margin-bottom: 10px;
}

.badge-descuento {
    background: linear-gradient(45deg, #dc3545, #ff6b6b);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: bold;
    animation: pulse 2s infinite;
    display: inline-block;
    margin-bottom: 10px;
}

.ahorro-info {
    color: #198754;
    font-weight: 600;
    font-size: 1rem;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

<script src="https://www.paypal.com/sdk/js?client-id=AYzvId4ZYPTgUbDOI3rK1pRiR_InW4iJgsVOAPxO5g2j3YmDzjEA6Z9hayiV7o0E23jLC8hP5e7U-t1Z&currency=EUR"></script>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Imagen principal -->
            <div class="card mb-4 border-0">
                <img src="<?= $ruta->imagen ?: 'https://via.placeholder.com/800x400?text=Motocicleta' ?>" 
                     class="card-img-top rounded-top" 
                     alt="<?= $ruta->nombre ?>"
                     style="max-height: 500px; object-fit: cover;">
            </div>
            
            <!-- Descripción detallada -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="card-header bg-primary text-white">
                        <h2 class="card-title mb-2"><?= $ruta->nombre ?></h2>
                        <p class="card-text mb-0"><?= $ruta->descripcion ?></p>
                    </div>
                    <div class="p-4">
                        <h4 class="mt-3 mb-4 border-bottom pb-2">Detalles de la Ruta</h4>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Nivel:</strong> 
                                <span class="badge <?= 
                                    $ruta->nivel == 'Piloto nuevo' ? 'bg-info' : 
                                    ($ruta->nivel == 'Domando Curvas' ? 'bg-warning text-dark' : 'bg-danger') 
                                ?>">
                                    <?= $ruta->nivel ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Paisaje:</strong> 
                                <span class="badge bg-success"><?= $ruta->paisaje ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Tipo:</strong> 
                                <span class="badge <?= $esPremium ? 'bg-danger' : 'bg-success' ?>">
                                    <?= $ruta->plan ?>
                                </span>
                            </li>
                            <li class="list-group-item"><strong>Distancia:</strong> <?= intval($ruta->distancia) ?> km</li>
                            <li class="list-group-item"><strong>Tiempo estimado:</strong> <?= $ruta->tiempo ?></li>
                        </ul>
                        
                        <h4 class="mb-3 border-bottom pb-2">Descripción completa</h4>
                        <div class="text-justify"><?php echo html_entity_decode($ruta->descripcion_completa); ?></div>
                        
                        <?php if(!empty($ruta->destacados)): ?>
                        <h4 class="mt-4 mb-3 border-bottom pb-2">Puntos destacados</h4>
                        <div class="row">
                            <?php 
                            $puntos_destacados = explode(",", $ruta->destacados);
                            foreach($puntos_destacados as $punto): 
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 bg-secondary">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="fas fa-map-marker-alt text-primary"></i> <?= trim($punto) ?></h5>
                                        <p class="card-text text-white">Lugar destacado en esta ruta</p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top border-0" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 text-white text-center"><?= $esPremium ? 'COMPRAR RUTA' : 'DESCARGA LA RUTA' ?></h4>
                </div>
                <div class="card-body">
                    <?php if($esPremium): ?>
                    <!-- Información detallada sobre la ruta premium -->
                    <div class="mb-4 p-3 bg-light rounded border-left border-primary" style="border-left-width: 4px !important;">
                        <h5 class="mb-3"><i class="fas fa-route text-primary"></i> <strong>Ruta Premium</strong></h5>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <p class="mb-1"><strong>Cuidadosamente planificada</strong> por expertos motociclistas con años de experiencia</p>
                        </div>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <p class="mb-1"><strong>Track GPS de alta precisión</strong> optimizado para la mejor experiencia de conducción</p>
                        </div>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <p class="mb-1"><strong>Descripción detallada</strong> con todos los puntos de interés y consejos relevantes</p>
                        </div>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <p class="mb-1"><strong>Ruta verificada</strong> y recorrida varias veces para garantizar la mejor experiencia</p>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <p class="mb-0"><strong>Soporte postventa</strong> para resolver cualquier duda sobre la ruta</p>
                        </div>
                    </div>
                    
                    <!-- ✅ NUEVA SECCIÓN DE PRECIOS CON OFERTAS -->
                    <?php if($tiene_oferta): ?>
                    <div class="precio-oferta-container">
                        <div class="badge-descuento">
                            <i class="fas fa-fire"></i> ¡OFERTA ESPECIAL -<?= $porcentaje_descuento ?>%!
                        </div>
                        
                        <div class="precio-original">
                            Precio normal: <?= number_format($precio_base, 2) ?>€
                        </div>
                        
                        <div class="precio-con-descuento">
                            <?= number_format($precio_final, 2) ?>€
                        </div>
                        
                        <div class="ahorro-info">
                            <i class="fas fa-piggy-bank"></i> ¡Ahorras <?= number_format($ahorro, 2) ?>€!
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-3">
                        <h3 class="text-primary mb-0">
                            Precio: <span id="precio-total"><?= number_format($precio_final, 2) ?></span>€
                        </h3>
                    </div>
                    <?php endif; ?>
                    
                    <input type="hidden" name="precio_final" id="precio-final" value="<?= number_format($precio_final, 2, '.', '') ?>">
                    
                    <?php else: ?>
                    <!-- Información para rutas gratuitas -->
                    <div class="free-route-info">
                        <h5><i class="fas fa-gift me-2"></i>Ruta Gratuita</h5>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <p class="mb-1">Track GPX listo para usar en tu dispositivo GPS o smartphone</p>
                        </div>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <p class="mb-1">Ruta verificada para "<?= ucfirst($ruta->nivel) ?>"</p>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success mt-1 me-2"></i>
                            <p class="mb-0">Incluye puntos de interés destacados</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($user->isLoggedIn()): ?>
                        <?php if($esPremium): ?>
                            <div id="paypal-button-container"></div>
<script>
paypal.Buttons({
  createOrder: function(data, actions) {
    return actions.order.create({
      purchase_units: [{
        amount: {
          value: document.getElementById('precio-final').value,
          currency_code: "EUR"
        }
      }]
    });
  },
  onApprove: function(data, actions) {
    return actions.order.capture().then(function(details) {
      // Obtener el ID de la transacción correcta (ID de captura)
      let transactionId = details.id; // ID de la orden por defecto
      
      // Comprobar si existe el ID de transacción real (ID de captura)
      if (details.purchase_units && 
          details.purchase_units[0] && 
          details.purchase_units[0].payments && 
          details.purchase_units[0].payments.captures && 
          details.purchase_units[0].payments.captures[0]) {
          transactionId = details.purchase_units[0].payments.captures[0].id;
      }
      
      // Datos capturados de la venta
      const rutaId = <?= $ruta_id ?>;
      const precio = document.getElementById('precio-final').value;
      const status = details.status;
      const payerName = details.payer.name.given_name + ' ' + details.payer.name.surname;
      const payerEmail = details.payer.email_address;
      const payerId = details.payer.payer_id;
      
      // Establecemos todos los valores a 0 ya que eliminamos las opciones
      const repostaje = 0;
      const hoteles = 0;
      const puntos = 0;

      // Redirigir a la página de procesamiento de venta
      window.location.href = `procesar_venta.php?ruta_id=${rutaId}&precio=${precio}&transactionId=${encodeURIComponent(transactionId)}&status=${encodeURIComponent(status)}&payerId=${encodeURIComponent(payerId)}&payerEmail=${encodeURIComponent(payerEmail)}&payerName=${encodeURIComponent(payerName)}&repostaje=${repostaje}&hoteles=${hoteles}&puntos=${puntos}`;
    });
  }
}).render('#paypal-button-container');
</script>

                        <?php elseif($gpxExists): ?>
                            <div class="download-btn-container">
                                <a href="<?= $gpxFile ?>" class="btn btn-success btn-lg w-100 py-3" download>
                                    <i class="fas fa-download me-2"></i> Descargar GPX
                                </a>
                                <p class="text-muted mt-2 small">
                                    <i class="fas fa-info-circle"></i> Archivo compatible con la mayoría de navegadores GPS y apps de navegación
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                Archivo GPX no configurado para esta ruta
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Debes <a href="../users/login.php" class="text-decoration-underline">iniciar sesión</a> para <?= $esPremium ? 'comprar' : 'descargar' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Obtener imágenes de la galería
try {
    $imagenes_galeria = $db->query("SELECT * FROM aa_rutas_galeria WHERE ruta_id = ? ORDER BY orden ASC, id ASC", [$ruta_id])->results();
} catch(Exception $e) {
    $imagenes_galeria = [];
}

if(!empty($imagenes_galeria)):
?>
<!-- Galería de Imágenes - Versión Optimizada -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-white"><i class="fas fa-images"></i> Galería de Imágenes (<?= count($imagenes_galeria) ?>)</h4>
        </div>
        <div class="p-4">
            <!-- Slideshow Container -->
            <div class="simple-slideshow position-relative">
                <div class="slideshow-wrapper" style="overflow: hidden; border-radius: 8px; position: relative; height: 60vh; min-height: 300px; max-height: 500px; background: #f8f9fa;">
                    <div class="slides-container" id="slidesContainer" style="display: flex; height: 100%;">
                        <?php foreach($imagenes_galeria as $index => $imagen): ?>
                        <div class="slide" style="min-width: 100%; position: relative; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                            <img src="../<?= $imagen->imagen ?>" 
                                 alt="<?= $imagen->descripcion ?: 'Imagen de la ruta' ?>"
                                 class="slide-image"
                                 style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; display: block;">
                            <?php if(!empty($imagen->descripcion)): ?>
                            <div class="slide-caption" style="position: absolute; bottom: 20px; left: 20px; right: 20px; background: rgba(0,0,0,0.8); color: white; padding: 12px; border-radius: 8px;">
                                <p class="mb-0 text-center"><?= htmlspecialchars($imagen->descripcion) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Controles de navegación (solo desktop) -->
                <button class="slide-btn prev-btn d-none d-md-block" onclick="changeSlide(-1)" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); background: rgba(128, 0, 0, 0.8); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 18px; cursor: pointer; z-index: 10;">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="slide-btn next-btn d-none d-md-block" onclick="changeSlide(1)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: rgba(128, 0, 0, 0.8); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 18px; cursor: pointer; z-index: 10;">
                    <i class="fas fa-chevron-right"></i>
                </button>
                
                <!-- Botón de pantalla completa (solo móvil) -->
                <button class="fullscreen-btn d-md-none" onclick="toggleFullscreen()" style="position: absolute; top: 15px; right: 15px; background: rgba(0, 0, 0, 0.6); border: none; color: white; width: 40px; height: 40px; border-radius: 8px; font-size: 16px; cursor: pointer; z-index: 15; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-expand" id="fullscreenIcon"></i>
                </button>
                
                <!-- Controles en pantalla completa -->
                <div class="fullscreen-controls" style="display: none; position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 25;">
                    <button class="fs-control-btn" onclick="changeSlide(-1)" style="background: rgba(0, 0, 0, 0.7); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 20px; cursor: pointer; margin: 0 10px;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="fs-control-btn" onclick="toggleFullscreen()" style="background: rgba(0, 0, 0, 0.7); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 20px; cursor: pointer; margin: 0 10px;">
                        <i class="fas fa-compress"></i>
                    </button>
                    <button class="fs-control-btn" onclick="changeSlide(1)" style="background: rgba(0, 0, 0, 0.7); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 20px; cursor: pointer; margin: 0 10px;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Indicadores minimalistas -->
                <div class="slide-indicators text-center" style="position: absolute; bottom: 10px; left: 0; right: 0; z-index: 20;">
                    <?php foreach($imagenes_galeria as $index => $imagen): ?>
                    <span class="indicator-dot <?= $index === 0 ? 'active' : '' ?>" 
                          style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: <?= $index === 0 ? '#fff' : 'rgba(255,255,255,0.5)' ?>; margin: 0 4px; box-shadow: 0 0 3px rgba(0,0,0,0.5);">
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Texto de ayuda para móviles -->
            <p class="text-muted text-center mt-2 d-md-none" style="font-size: 12px;">
                <i class="fas fa-hand-point-up"></i> Desliza para cambiar foto • Doble tap o <i class="fas fa-expand"></i> para pantalla completa
            </p>
        </div>
    </div>
</div>

<style>
/* Estilos simplificados para el slideshow */
.simple-slideshow {
    max-width: 100%;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.slideshow-wrapper {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: grab;
    touch-action: pan-y;
}

.slideshow-wrapper:active {
    cursor: grabbing;
}

.slides-container {
    transition: none !important; /* Sin animaciones */
}

.slide {
    height: 100%;
}

.slide-image {
    pointer-events: none;
}

.slide-btn {
    opacity: 0.8;
}

.slide-btn:hover {
    opacity: 1;
    background: #800000 !important;
}

.indicator-dot {
    pointer-events: none;
    border: 1px solid rgba(0,0,0,0.2);
}

/* Estilos para pantalla completa */
.fullscreen-btn {
    transition: opacity 0.3s;
}

.fullscreen-btn:active {
    transform: scale(0.95);
}

.fs-control-btn {
    transition: all 0.3s;
}

.fs-control-btn:active {
    transform: scale(0.9);
}

/* Modo pantalla completa */
.simple-slideshow:fullscreen,
.simple-slideshow:-webkit-full-screen,
.simple-slideshow:-moz-full-screen {
    background: #000;
}

.simple-slideshow:fullscreen .slideshow-wrapper,
.simple-slideshow:-webkit-full-screen .slideshow-wrapper,
.simple-slideshow:-moz-full-screen .slideshow-wrapper {
    height: 100vh !important;
    max-height: none !important;
    border-radius: 0 !important;
}

.simple-slideshow:fullscreen .slide,
.simple-slideshow:-webkit-full-screen .slide,
.simple-slideshow:-moz-full-screen .slide {
    background: #000 !important;
}

.simple-slideshow:fullscreen .fullscreen-btn,
.simple-slideshow:-webkit-full-screen .fullscreen-btn,
.simple-slideshow:-moz-full-screen .fullscreen-btn {
    display: none !important;
}

.simple-slideshow:fullscreen .fullscreen-controls,
.simple-slideshow:-webkit-full-screen .fullscreen-controls,
.simple-slideshow:-moz-full-screen .fullscreen-controls {
    display: flex !important;
}

.simple-slideshow:fullscreen .slide-caption,
.simple-slideshow:-webkit-full-screen .slide-caption,
.simple-slideshow:-moz-full-screen .slide-caption {
    bottom: 100px !important;
}

.simple-slideshow:fullscreen .slide-indicators,
.simple-slideshow:-webkit-full-screen .slide-indicators,
.simple-slideshow:-moz-full-screen .slide-indicators {
    bottom: 20px !important;
}

.simple-slideshow:fullscreen .indicator-dot,
.simple-slideshow:-webkit-full-screen .indicator-dot,
.simple-slideshow:-moz-full-screen .indicator-dot {
    width: 12px !important;
    height: 12px !important;
    margin: 0 6px !important;
    box-shadow: 0 0 8px rgba(0,0,0,0.8) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .slideshow-wrapper {
        height: 50vh !important;
        min-height: 250px !important;
        max-height: 400px !important;
    }
    
    .slide-caption {
        bottom: 30px !important;
        font-size: 13px !important;
    }
    
    .indicator-dot {
        width: 10px !important;
        height: 10px !important;
        margin: 0 5px !important;
    }
    
    .fullscreen-btn {
        width: 36px !important;
        height: 36px !important;
        font-size: 14px !important;
    }
}

@media (max-width: 480px) {
    .slideshow-wrapper {
        height: 40vh !important;
        min-height: 200px !important;
        max-height: 300px !important;
    }
    
    .slide-caption {
        bottom: 35px !important;
        font-size: 12px !important;
        padding: 8px !important;
    }
    
    .slide-indicators {
        bottom: 15px !important;
    }
    
    .indicator-dot {
        width: 10px !important;
        height: 10px !important;
        margin: 0 4px !important;
    }
    
    .fullscreen-btn {
        width: 35px !important;
        height: 35px !important;
        font-size: 13px !important;
        top: 10px !important;
        right: 10px !important;
    }
}
</style>

<script>
let currentSlide = 0;
const totalSlides = <?= count($imagenes_galeria) ?>;
let isFullscreen = false;

// Variables para el touch
let touchStartX = 0;
let touchEndX = 0;
let isDragging = false;
let startPos = 0;
let currentTranslate = 0;
let prevTranslate = 0;

const slidesContainer = document.getElementById('slidesContainer');

// Función para pantalla completa
function toggleFullscreen() {
    const slideshow = document.querySelector('.simple-slideshow');
    
    if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement) {
        // Entrar en pantalla completa
        if (slideshow.requestFullscreen) {
            slideshow.requestFullscreen();
        } else if (slideshow.webkitRequestFullscreen) {
            slideshow.webkitRequestFullscreen();
        } else if (slideshow.mozRequestFullScreen) {
            slideshow.mozRequestFullScreen();
        }
        isFullscreen = true;
    } else {
        // Salir de pantalla completa
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        }
        isFullscreen = false;
    }
}

// Detectar cambios en pantalla completa
document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('mozfullscreenchange', handleFullscreenChange);

function handleFullscreenChange() {
    const icon = document.getElementById('fullscreenIcon');
    if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement) {
        if (icon) icon.className = 'fas fa-compress';
    } else {
        if (icon) icon.className = 'fas fa-expand';
    }
}

function changeSlide(direction) {
    currentSlide += direction;
    
    if (currentSlide >= totalSlides) {
        currentSlide = 0;
    } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    }
    
    updateSlideshow();
}

function updateSlideshow() {
    // Mover las slides sin transición
    const translateX = -currentSlide * 100;
    slidesContainer.style.transform = `translateX(${translateX}%)`;
    
    // Actualizar indicadores
    document.querySelectorAll('.indicator-dot').forEach((dot, index) => {
        if (index === currentSlide) {
            dot.style.background = '#fff';
            dot.style.boxShadow = '0 0 5px rgba(0,0,0,0.8)';
        } else {
            dot.style.background = 'rgba(255,255,255,0.5)';
            dot.style.boxShadow = '0 0 3px rgba(0,0,0,0.5)';
        }
    });
}

// Touch events para móviles
function handleTouchStart(e) {
    touchStartX = e.touches[0].clientX;
    isDragging = true;
    startPos = e.touches[0].clientX;
    prevTranslate = -currentSlide * 100;
}

function handleTouchMove(e) {
    if (!isDragging) return;
    
    const currentPosition = e.touches[0].clientX;
    const diff = currentPosition - startPos;
    const percentageMoved = (diff / slidesContainer.offsetWidth) * 100;
    
    currentTranslate = prevTranslate + percentageMoved;
    
    slidesContainer.style.transform = `translateX(${currentTranslate}%)`;
}

function handleTouchEnd(e) {
    isDragging = false;
    touchEndX = e.changedTouches[0].clientX;
    
    const swipeThreshold = 50; // píxeles mínimos para considerar un swipe
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
            // Swipe izquierda - siguiente imagen
            currentSlide++;
            if (currentSlide >= totalSlides) {
                currentSlide = 0; // Volver al principio
            }
        } else {
            // Swipe derecha - imagen anterior
            currentSlide--;
            if (currentSlide < 0) {
                currentSlide = totalSlides - 1; // Ir a la última
            }
        }
    }
    
    updateSlideshow();
}

// Event listeners
slidesContainer.addEventListener('touchstart', handleTouchStart, { passive: true });
slidesContainer.addEventListener('touchmove', handleTouchMove, { passive: true });
slidesContainer.addEventListener('touchend', handleTouchEnd, { passive: true });

// Prevenir el comportamiento por defecto del drag en desktop
slidesContainer.addEventListener('mousedown', (e) => e.preventDefault());

// Navegación con teclado (solo desktop)
document.addEventListener('keydown', function(e) {
    if (e.keyCode === 37) { // Flecha izquierda
        changeSlide(-1);
    } else if (e.keyCode === 39) { // Flecha derecha
        changeSlide(1);
    } else if (e.keyCode === 27 && isFullscreen) { // ESC en pantalla completa
        toggleFullscreen();
    }
});

// Doble tap para pantalla completa en móviles
let lastTap = 0;
slidesContainer.addEventListener('touchend', function(e) {
    const currentTime = new Date().getTime();
    const tapLength = currentTime - lastTap;
    if (tapLength < 300 && tapLength > 0) {
        e.preventDefault();
        toggleFullscreen();
    }
    lastTap = currentTime;
});
</script>
<?php endif; ?>


 <!-- DEBUG: Información visible solo para testing -->

    <!-- <div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border: 1px solid #ccc;">
        <h3>Debug Info:</h3>
        <p><strong>Título:</strong> <?php //echo $titulo; ?></p>
        <p><strong>Descripción:</strong> <?php //echo $descripcion; ?></p>
        <p><strong>Imagen URL:</strong> <a href="<?php //echo $imagen_url; ?>" target="_blank"><?php //echo $imagen_url; ?></a></p>
        <p><strong>Página URL:</strong> <?php //echo $url_actual; ?></p>
        <p><strong>Image exists:</strong> <?php //echo file_exists($imagen_limpia) ? 'YES' : 'NO'; ?></p>
    </div> -->



<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>