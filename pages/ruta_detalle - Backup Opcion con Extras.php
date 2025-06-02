<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Configuración de porcentajes
$configOpciones = [
    'repostaje' => 10,
    'hoteles_restaurantes' => 10,
    'fotos' => 10
];

// Obtener datos de la ruta
$ruta_id = Input::get('id');
if(!$ruta_id || !is_numeric($ruta_id)) Redirect::to('rutas.php');

try {
    $db = DB::getInstance();
    $ruta = $db->query("SELECT * FROM aa_rutas WHERE id = ?", [$ruta_id])->first();
    if(!$ruta) throw new Exception('Ruta no encontrada');
    
    // Determinar si es ruta premium
    $esPremium = ($ruta->plan == 'Premium');
    $precio_base = (float)$ruta->precio;
    
    // Obtener ruta del archivo GPX desde la base de datos
    $gpxPath = $ruta->gpx;
    $gpxFile = $us_url_root.$gpxPath;
    $gpxExists = !empty($gpxPath);
    echo "GPX Path: $gpxPath";
    echo "<br>";
    echo $us_url_root;
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
                                    $ruta->nivel == 'Novato' ? 'bg-info' : 
                                    ($ruta->nivel == 'Intermedio' ? 'bg-warning text-dark' : 'bg-danger') 
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
                                <span class="badge <?= $esPremium ? 'bg-danger' : 'bg-secondary' ?>">
                                    <?= $ruta->plan ?>
                                </span>
                            </li>
                            <li class="list-group-item"><strong>Distancia:</strong> <?= $ruta->distancia ?> km</li>
                            <li class="list-group-item"><strong>Tiempo estimado:</strong> <?= $ruta->tiempo ?></li>
                        </ul>
                        
                        <h4 class="mb-3 border-bottom pb-2">Descripción completa</h4>
                        <p class="text-justify"><?= nl2br($ruta->descripcion_completa) ?></p>
                        
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
                    <h4 class="mb-0 text-white text-center"><?= $esPremium ? 'PERSONALIZAR PAQUETE' : 'DESCARGA LA RUTA' ?></h4>
                </div>
                <div class="card-body">
                    <?php if($esPremium): ?>
                    <div id="opciones-container" class="px-2">
                        <!-- Opción base -->
                        <div class="form-check mb-3 p-3 bg-light rounded">
                            <input class="form-check-input" type="checkbox" id="base" checked disabled>
                            <label class="form-check-label">
                                <strong>Ruta Básica</strong><br>
                                <small class="text-muted">Incluye track GPS y descripción detallada</small>
                            </label>
                        </div>
                        
                        <!-- Opciones adicionales -->
                        <div class="form-check mb-3 p-3 bg-light rounded">
                            <input class="form-check-input opcion-check" type="checkbox" 
                                   name="opciones[]" value="repostaje" id="repostaje"
                                   data-porcentaje="<?= $configOpciones['repostaje'] ?>">
                            <label class="form-check-label">
                                <strong>+ Plan de Repostaje</strong><br>
                                <small class="text-muted">Estaciones recomendadas y cálculo de consumo</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3 p-3 bg-light rounded">
                            <input class="form-check-input opcion-check" type="checkbox" 
                                   name="opciones[]" value="hoteles_restaurantes" id="hoteles"
                                   data-porcentaje="<?= $configOpciones['hoteles_restaurantes'] ?>">
                            <label class="form-check-label">
                                <strong>+ Alojamiento y Gastronomía</strong><br>
                                <small class="text-muted">Hoteles con parking y restaurantes locales (requiere Plan de Repostaje)</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3 p-3 bg-light rounded">
                            <input class="form-check-input opcion-check" type="checkbox" 
                                   name="opciones[]" value="fotos" id="fotos"
                                   data-porcentaje="<?= $configOpciones['fotos'] ?>">
                            <label class="form-check-label">
                                <strong>+ Puntos Fotográficos</strong><br>
                                <small class="text-muted">Mejores ubicaciones para fotos y descansos (requiere opciones anteriores)</small>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="text-center py-4">
                        <h3 class="<?= $esPremium ? 'text-primary' : 'text-success' ?> mb-0">
                            <?= $esPremium ? 'Total: <span id="precio-total">'.number_format($precio_base, 2).'</span>€' : 'RUTA GRATUITA' ?>
                        </h3>
                        <?php if($esPremium): ?>
                        <input type="hidden" name="precio_final" id="precio-final" value="<?= $precio_base ?>">
                        <?php endif; ?>
                    </div>

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
      // Datos capturados de la venta
      const rutaId = <?= $ruta_id ?>;
      const precio = document.getElementById('precio-final').value;
      const transactionId = details.id;
      const status = details.status;
      const payerName = details.payer.name.given_name + ' ' + details.payer.name.surname;
      const payerEmail = details.payer.email_address;
      const payerId = details.payer.payer_id;
      
      // Capturar opciones seleccionadas (respetando la jerarquía)
      const repostaje = document.getElementById('repostaje').checked ? 1 : 0;
      const hoteles = (repostaje && document.getElementById('hoteles').checked) ? 1 : 0;
      const puntos = (repostaje && hoteles && document.getElementById('fotos').checked) ? 1 : 0;

      // Redirigir a la página de procesamiento de venta
      window.location.href = `procesar_venta.php?ruta_id=${rutaId}&precio=${precio}&transactionId=${encodeURIComponent(transactionId)}&status=${encodeURIComponent(status)}&payerId=${encodeURIComponent(payerId)}&payerEmail=${encodeURIComponent(payerEmail)}&payerName=${encodeURIComponent(payerName)}&repostaje=${repostaje}&hoteles=${hoteles}&puntos=${puntos}`;
    });
  }
}).render('#paypal-button-container');
</script>

                        <?php elseif($gpxExists): ?>
                            <a href="<?= $gpxFile ?>" class="btn btn-success btn-lg w-100 py-3" download>
                                <i class="fas fa-download"></i> Descargar GPX
                            </a>
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

<?php if($esPremium): ?>
    
<script>
document.addEventListener('DOMContentLoaded', function() {
    const precioBase = <?= $precio_base ?>;
    const opciones = document.querySelectorAll('.opcion-check');
    const precioTotal = document.getElementById('precio-total');
    const precioFinal = document.getElementById('precio-final');
    
    // Referencias a cada checkbox
    const checkRepostaje = document.getElementById('repostaje');
    const checkHoteles = document.getElementById('hoteles');
    const checkFotos = document.getElementById('fotos');
    
    // Inicialmente, solo repostaje está habilitado, los demás deshabilitados
    checkHoteles.disabled = true;
    checkFotos.disabled = true;
    
    // Función para actualizar estados de los checkboxes
    function actualizarEstadoCheckboxes() {
        // Si repostaje está marcado, habilitar hoteles; sino, deshabilitar y desmarcar hoteles y fotos
        if (checkRepostaje.checked) {
            checkHoteles.disabled = false;
        } else {
            checkHoteles.disabled = true;
            checkHoteles.checked = false;
            checkFotos.disabled = true;
            checkFotos.checked = false;
        }
        
        // Si hoteles está marcado, habilitar fotos; sino, deshabilitar y desmarcar fotos
        if (checkHoteles.checked) {
            checkFotos.disabled = false;
        } else {
            checkFotos.disabled = true;
            checkFotos.checked = false;
        }
        
        // Actualizar el precio después de cambiar los estados
        actualizarPrecio();
    }

    function actualizarPrecio() {
        let total = precioBase;
        
        opciones.forEach(opcion => {
            if(opcion.checked) {
                const porcentaje = parseFloat(opcion.dataset.porcentaje);
                total += precioBase * (porcentaje / 100);
            }
        });
        
        precioTotal.textContent = total.toFixed(2);
        precioFinal.value = total.toFixed(2);
    }

    // Asignar eventos a los checkboxes
    checkRepostaje.addEventListener('change', actualizarEstadoCheckboxes);
    checkHoteles.addEventListener('change', actualizarEstadoCheckboxes);
    checkFotos.addEventListener('change', actualizarEstadoCheckboxes);
    
    // Inicializar estados
    actualizarEstadoCheckboxes();
});
</script>
<?php endif; ?>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>