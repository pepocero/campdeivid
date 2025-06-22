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
    
    // ✅ NUEVO: Variables para sistema de cupones
    $cupon_aplicado = false;
    $cupon_data = null;
    $precio_con_cupon = $precio_final;
    $descuento_cupon = 0;
    
    // Obtener ruta del archivo GPX desde la base de datos
    $gpxPath = $ruta->gpx;
    $gpxFile = $us_url_root.$gpxPath;
    $gpxExists = !empty($gpxPath);    
} catch(Exception $e) {
    Session::flash('error', $e->getMessage());
    Redirect::to('pages/rutas.php');
}

echo $gpxFile;
?>

<!-- PAYPAL SDK LIVE -->
<script src="https://www.paypal.com/sdk/js?client-id=AYzvId4ZYPTgUbDOI3rK1pRiR_InW4iJgsVOAPxO5g2j3YmDzjEA6Z9hayiV7o0E23jLC8hP5e7U-t1Z&currency=EUR"></script>

<!-- ESTILOS MEJORADOS PARA DISEÑO COMPACTO Y PROFESIONAL -->
<style>
/* ===== CARRO DE COMPRA COMPACTO ===== */
.purchase-card {
    max-width: 380px !important;
    margin: 0 auto;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    border-radius: 12px !important;
    overflow: hidden;
}

.purchase-card .card-header {
    padding: 1rem 1.25rem !important;
    background: #ff0500 !important;
    border: none !important;
}

.purchase-card .card-body {
    padding: 1.5rem !important;
}

/* ===== SECCIÓN DE OFERTAS MEJORADA ===== */
.precio-oferta-container {
    background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
    border: 2px solid #dc3545;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.15);
    animation: subtle-glow 3s ease-in-out infinite alternate;
}

@keyframes subtle-glow {
    from { box-shadow: 0 4px 15px rgba(220, 53, 69, 0.15); }
    to { box-shadow: 0 6px 20px rgba(220, 53, 69, 0.25); }
}

.badge-descuento {
    background: linear-gradient(45deg, #dc3545, #ff6b6b);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
    text-align: center;
    margin-bottom: 0.75rem;
    box-shadow: 0 3px 10px rgba(220, 53, 69, 0.3);
    animation: pulse-offer 2s infinite;
}

@keyframes pulse-offer {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.precio-original {
    text-decoration: line-through;
    color: #6c757d;
    font-size: 0.9rem;
    text-align: center;
    margin-bottom: 0.25rem;
}

.precio-con-descuento {
    font-size: 2rem;
    font-weight: bold;
    color: #dc3545;
    text-align: center;
    margin-bottom: 0.5rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.ahorro-info {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    text-align: center;
    margin-top: 0.5rem;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

/* ===== PRECIO NORMAL MEJORADO ===== */
.precio-normal {
    font-size: 1.75rem !important;
    font-weight: bold;
    color: #007bff;
    text-align: center;
    padding: 1rem 0;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 1rem;
}

/* ===== INFORMACIÓN PREMIUM COMPACTA ===== */
.premium-info {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 0 8px 8px 0;
}

.premium-info h5 {
    font-size: 1rem;
    margin-bottom: 0.75rem;
    color: #007bff;
}

.premium-feature {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    line-height: 1.4;
}

.premium-feature:last-child {
    margin-bottom: 0;
}

.premium-feature i {
    color: #28a745;
    margin-right: 0.5rem;
    margin-top: 0.15rem;
    font-size: 0.75rem;
}

/* ===== INFORMACIÓN RUTA GRATUITA ===== */
.free-route-info {
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 0 8px 8px 0;
}

.free-route-info h5 {
    color: #28a745;
    font-size: 1rem;
    margin-bottom: 0.75rem;
}

.free-feature {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    line-height: 1.4;
}

.free-feature:last-child {
    margin-bottom: 0;
}

.free-feature i {
    color: #28a745;
    margin-right: 0.5rem;
    margin-top: 0.15rem;
    font-size: 0.75rem;
}

/* ===== BOTÓN DE DESCARGA MEJORADO ===== */
.download-btn-container {
    margin-top: 1rem;
}

.download-btn-container .btn {
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    padding: 0.75rem 1.5rem;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
}

.download-btn-container .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

/* ===== PAYPAL BUTTON CONTAINER ===== */
#paypal-button-container {
    margin-top: 1rem;
    border-radius: 8px;
    overflow: hidden;
}

/* ===== ESTILOS PARA SISTEMA DE CUPONES ===== */
.cupon-section {
    background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%);
    border: 2px solid #2196f3;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
    transition: all 0.3s ease;
}

.cupon-section.cupon-aplicado {
    background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
    border-color: #4caf50;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
}

.cupon-input-group {
    position: relative;
}

.cupon-input {
    border-radius: 8px 0 0 8px;
    border: 2px solid #2196f3;
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: small;
    text-transform: uppercase;
    background: white;
}

.cupon-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
    border-color: #1976d2;
}

.cupon-btn {
    border-radius: 0 8px 8px 0;
    border: 2px solid #2196f3;
    border-left: none;
    padding: 0.75rem 1.5rem;
    background: #2196f3;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.cupon-btn:hover {
    background: #1976d2;
    border-color: #1976d2;
    transform: translateY(-1px);
    color: white;
}

.cupon-btn:disabled {
    background: #ccc;
    border-color: #ccc;
    cursor: not-allowed;
    transform: none;
}

.cupon-mensaje {
    margin-top: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.cupon-mensaje.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.cupon-mensaje.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.cupon-loading {
    display: none;
    margin-top: 0.5rem;
    text-align: center;
    color: #666;
}

.precio-breakdown {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.precio-linea {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    padding: 0.25rem 0;
}

.precio-linea.total {
    border-top: 2px solid #ddd;
    margin-top: 0.5rem;
    padding-top: 0.75rem;
    font-weight: bold;
    font-size: 1.1em;
}

.precio-original-cupon {
    text-decoration: line-through;
    color: #6c757d;
}

.precio-descuento-cupon {
    color: #28a745;
    font-weight: bold;
}

.cupon-aplicado-info {
    background: linear-gradient(45deg, #4caf50, #8bc34a);
    color: white;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cupon-aplicado-info::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 2px,
        rgba(255,255,255,0.1) 2px,
        rgba(255,255,255,0.1) 4px
    );
    animation: shine 3s linear infinite;
}

@keyframes shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.remover-cupon {
    background: none;
    border: none;
    color: white;
    font-size: 1.2em;
    cursor: pointer;
    padding: 0;
    margin-left: 1rem;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.remover-cupon:hover {
    opacity: 1;
    transform: scale(1.1);
}

/* ===== RESPONSIVE MÓVIL ===== */
@media (max-width: 768px) {
    .purchase-card {
        max-width: 100% !important;
        margin: 0 0 2rem 0;
    }
    
    .precio-con-descuento {
        font-size: 1.75rem;
    }
    
    .precio-normal {
        font-size: 1.5rem !important;
    }
    
    .premium-info,
    .free-route-info {
        margin-left: -15px;
        margin-right: -15px;
        border-radius: 0;
        border-left: none;
        border-top: 4px solid #007bff;
    }
    
    .free-route-info {
        border-top-color: #28a745;
    }
    
    /* Asegurar que la oferta se vea bien en móvil */
    .precio-oferta-container {
        margin-left: -15px;
        margin-right: -15px;
        border-radius: 0;
        border-left: none;
        border-right: none;
        border-top: 3px solid #dc3545;
        border-bottom: 3px solid #dc3545;
    }
    
    /* Cupones responsive */
    .cupon-input-group {
        flex-direction: column;
    }
    
    .cupon-input, .cupon-btn {
        border-radius: 8px;
        border: 2px solid #2196f3;
        margin-bottom: 0.5rem;
    }
    
    .cupon-btn {
        margin-bottom: 0;
    }
}

/* ===== MEJORAS GENERALES DE LAYOUT ===== */
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
}

.sticky-top {
    top: 20px !important;
}

@media (min-width: 992px) {
    .col-md-4 {
        padding-left: 2rem;
    }
}

/* ===== ESTILOS PARA BADGES ===== */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
}

/* ===== ALERTA MEJORADA ===== */
.alert-warning {
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
    color: #856404;
    padding: 1rem;
    font-size: 0.9rem;
}

.alert-warning a {
    color: #007bff;
    font-weight: 600;
}

/* ===== MEJORAS TIPOGRÁFICAS ===== */
.card-title {
    font-weight: 600;
    line-height: 1.3;
}

.card-text {
    color: #FFFFFF;
    line-height: 1.5;
}

/* ===== OCULTAR SCROLL EN CONTENEDORES PEQUEÑOS ===== */
.purchase-card {
    overflow-x: hidden;
}

/* ===== GALERÍA OPTIMIZADA PARA MÓVILES ===== */

/* Desktop: altura normal */
.slideshow-wrapper {
    overflow: hidden;
    border-radius: 8px;
    position: relative;
    height: 60vh;
    min-height: 300px;
    max-height: 500px;
    background: #000;
    border: 2px solid #ddd;
    width: 100%;
}

/* Móvil: altura optimizada */
@media (max-width: 768px) {
    .slideshow-wrapper {
        height: 70vh;
        min-height: 350px;
        max-height: none;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
}

/* Contenedor de slides con zoom */
.slides-container {
    display: flex;
    height: 100%;
    touch-action: none; /* Prevenir scroll mientras se hace zoom */
    width: 100%;
}

.slide {
    min-width: 100%;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #000;
    overflow: hidden;
}

/* Imagen optimizada */
.slide-image {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    display: block;
    transition: transform 0.3s ease;
    cursor: grab;
}

.slide-image:active {
    cursor: grabbing;
}

/* En móvil: usar object-fit cover para llenar mejor */
@media (max-width: 768px) {
    .slide-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
}

/* ===== PANTALLA COMPLETA CORREGIDA ===== */
.simple-slideshow.fullscreen-mode {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    height: 100dvh !important; /* Dynamic viewport height para móviles */
    z-index: 999999 !important;
    background: #000 !important;
    border-radius: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
}

.simple-slideshow.fullscreen-mode .slideshow-wrapper {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    height: 100dvh !important; /* Dynamic viewport height */
    max-height: 100vh !important;
    max-height: 100dvh !important;
    min-height: 100vh !important;
    min-height: 100dvh !important;
    border: none !important;
    border-radius: 0 !important;
    background: #000 !important;
}

.simple-slideshow.fullscreen-mode .slide-image {
    width: 100vw !important;
    height: 100vh !important;
    height: 100dvh !important;
    object-fit: contain !important;
    object-position: center !important;
    max-width: 100vw !important;
    max-height: 100vh !important;
    max-height: 100dvh !important;
}

/* Controles de zoom */
.zoom-controls {
    position: absolute;
    top: 60px;
    left: 20px;
    z-index: 30;
    display: flex;
    gap: 10px;
}

/* En pantalla completa, mover más abajo */
.simple-slideshow.fullscreen-mode .zoom-controls {
    top: 80px;
    left: 25px;
}

.zoom-btn {
    background: rgba(0, 0, 0, 0.8);
    border: none;
    color: white;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.zoom-btn:hover, .zoom-btn:active {
    background: rgba(0, 0, 0, 1);
    transform: scale(1.05);
}

/* Solo mostrar controles de zoom en móvil */
@media (min-width: 769px) {
    .zoom-controls {
        display: none;
    }
}

/* En móviles en landscape, usar posiciones normales */
@media (max-width: 768px) and (orientation: landscape) {
    .zoom-controls {
        top: 40px;
        left: 15px;
    }
    
    .fullscreen-btn {
        top: 40px;
        right: 15px;
    }
    
    .zoom-indicator {
        top: 40px;
        right: 65px;
    }
    
    .simple-slideshow.fullscreen-mode .zoom-controls {
        top: 50px;
        left: 20px;
    }
    
    .simple-slideshow.fullscreen-mode .fullscreen-btn {
        top: 50px;
        right: 20px;
    }
    
    .simple-slideshow.fullscreen-mode .zoom-indicator {
        top: 50px;
        right: 70px;
    }
}

/* En móviles en portrait, usar posiciones normales */
@media (max-width: 768px) and (orientation: portrait) {
    .zoom-controls {
        top: 70px;
        left: 25px;
    }
    
    .fullscreen-btn {
        top: 70px;
        right: 25px;
    }
    
    .zoom-indicator {
        top: 70px;
        right: 80px;
    }
}

/* Soporte para safe areas (dispositivos con notch) */
@supports (padding: max(0px)) {
    @media (max-width: 768px) {
        .zoom-controls {
            top: max(70px, env(safe-area-inset-top, 0px) + 20px);
        }
        
        .fullscreen-btn {
            top: max(70px, env(safe-area-inset-top, 0px) + 20px);
        }
        
        .zoom-indicator {
            top: max(70px, env(safe-area-inset-top, 0px) + 20px);
        }
        
        .simple-slideshow.fullscreen-mode .zoom-controls {
            top: max(80px, env(safe-area-inset-top, 0px) + 30px);
            left: max(25px, env(safe-area-inset-left, 0px) + 25px);
        }
        
        .simple-slideshow.fullscreen-mode .fullscreen-btn {
            top: max(80px, env(safe-area-inset-top, 0px) + 30px);
            right: max(25px, env(safe-area-inset-right, 0px) + 25px);
        }
        
        .simple-slideshow.fullscreen-mode .zoom-indicator {
            top: max(80px, env(safe-area-inset-top, 0px) + 30px);
            right: max(85px, env(safe-area-inset-right, 0px) + 85px);
        }
    }
}

/* Indicador de zoom */
.zoom-indicator {
    position: absolute;
    top: 60px;
    right: 80px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    z-index: 30;
    display: none;
}

/* En pantalla completa, ajustar posición */
.simple-slideshow.fullscreen-mode .zoom-indicator {
    top: 80px;
    right: 85px;
}

/* Botón de pantalla completa mejorado */
.fullscreen-btn {
    position: absolute;
    top: 60px;
    right: 20px;
    background: rgba(0, 0, 0, 0.8);
    border: none;
    color: white;
    width: 45px;
    height: 45px;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    z-index: 25;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

/* En pantalla completa, ajustar posición */
.simple-slideshow.fullscreen-mode .fullscreen-btn {
    top: 80px;
    right: 25px;
}

.fullscreen-btn:hover, .fullscreen-btn:active {
    background: rgba(0, 0, 0, 1);
    transform: scale(1.05);
}

/* Controles en pantalla completa */
.fullscreen-controls {
    display: none;
    position: absolute;
    bottom: 120px; /* Mucho más arriba para evitar barra de navegación */
    left: 50%;
    transform: translateX(-50%);
    z-index: 35;
    gap: 15px;
}

.simple-slideshow.fullscreen-mode .fullscreen-controls {
    display: flex;
}

/* En landscape, subir aún más */
@media (max-width: 768px) and (orientation: landscape) {
    .fullscreen-controls {
        bottom: 80px;
    }
}

/* En dispositivos con notch o safe areas */
@supports (padding: max(0px)) {
    .fullscreen-controls {
        bottom: max(120px, env(safe-area-inset-bottom, 0px) + 90px);
    }
    
    @media (max-width: 768px) and (orientation: landscape) {
        .fullscreen-controls {
            bottom: max(80px, env(safe-area-inset-bottom, 0px) + 50px);
        }
    }
}

.fs-control-btn {
    background: rgba(0, 0, 0, 0.8);
    border: none;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
}

.fs-control-btn:hover {
    background: rgba(0, 0, 0, 1);
}

/* Indicadores mejorados */
.slide-indicators {
    position: absolute;
    bottom: 25px;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 30;
}

/* En pantalla completa en móvil, subir los indicadores */
@media (max-width: 768px) {
    .simple-slideshow.fullscreen-mode .slide-indicators {
        bottom: 60px; /* Más arriba para evitar barra de navegación */
    }
}

/* En landscape en pantalla completa */
@media (max-width: 768px) and (orientation: landscape) {
    .simple-slideshow.fullscreen-mode .slide-indicators {
        bottom: 40px;
    }
}

/* Con safe areas */
@supports (padding: max(0px)) {
    @media (max-width: 768px) {
        .simple-slideshow.fullscreen-mode .slide-indicators {
            bottom: max(60px, env(safe-area-inset-bottom, 0px) + 30px);
        }
    }
    
    @media (max-width: 768px) and (orientation: landscape) {
        .simple-slideshow.fullscreen-mode .slide-indicators {
            bottom: max(40px, env(safe-area-inset-bottom, 0px) + 20px);
        }
    }
}

.indicator-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin: 0 5px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.indicator-dot.active {
    background: #fff !important;
    box-shadow: 0 0 8px rgba(255,255,255,0.8) !important;
    transform: scale(1.2);
}

.indicator-dot:not(.active) {
    background: rgba(255,255,255,0.5) !important;
    box-shadow: 0 0 3px rgba(0,0,0,0.5) !important;
}

/* Caption mejorado */
.slide-caption {
    position: absolute;
    bottom: 180px; /* Más arriba para no chocar con controles */
    left: 20px;
    right: 20px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 12px 16px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    z-index: 25;
}

/* En modo normal (no pantalla completa) */
.slideshow-wrapper:not(.simple-slideshow.fullscreen-mode *) .slide-caption {
    bottom: 50px;
}

/* En pantalla completa en móvil */
@media (max-width: 768px) {
    .simple-slideshow.fullscreen-mode .slide-caption {
        bottom: 200px; /* Aún más arriba para dar espacio a controles */
    }
}

/* En landscape */
@media (max-width: 768px) and (orientation: landscape) {
    .simple-slideshow.fullscreen-mode .slide-caption {
        bottom: 150px;
    }
}

/* Texto de ayuda mejorado */
.help-text {
    font-size: 11px;
    color: #666;
    text-align: center;
    margin-top: 8px;
    padding: 0 10px;
}

/* Animaciones suaves */
.slide-image.zooming {
    transition: transform 0.1s ease-out;
}

/* Prevenir selección de texto durante gestos */
.slideshow-wrapper {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Estilos para cuando el body está en pantalla completa */
body.fullscreen-active {
    overflow: hidden !important;
    position: fixed !important;
    width: 100% !important;
    height: 100% !important;
    top: 0 !important;
    left: 0 !important;
}

/* Ocultar scrollbars */
body.fullscreen-active::-webkit-scrollbar {
    display: none;
}

body.fullscreen-active {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

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
                                               
                       
                    </div>
                </div>
            </div>
            <!-- ===== BOTÓN DE COMPARTIR ===== -->
<div class="text-center my-4">
    <button class="btn btn-success" onclick="compartirRuta()" id="btnCompartir">
        <i class="fas fa-share-alt"></i> Compartir esta ruta
    </button>
</div>
        </div>

        <div class="col-md-4">
            <div class="card purchase-card sticky-top border-0">
                <div class="card-header bg-primary">
                    <h4 class="mb-0 text-white text-center"><?= $esPremium ? 'COMPRAR RUTA' : 'DESCARGA LA RUTA' ?></h4>
                </div>
                <div class="card-body">
                    <?php if($esPremium): ?>
                    <!-- Información detallada sobre la ruta premium -->
                    <div class="premium-info">
                        <h5><i class="fas fa-route"></i> Ruta Premium</h5>
                        
                        <div class="premium-feature">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Cuidadosamente planificada</strong> por expertos motociclistas</span>
                        </div>
                        
                        <div class="premium-feature">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Track GPS de alta precisión</strong> optimizado para conducción</span>
                        </div>
                        
                        <div class="premium-feature">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Descripción detallada</strong> con puntos de interés</span>
                        </div>
                        
                        <div class="premium-feature">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Ruta verificada</strong> y recorrida múltiples veces</span>
                        </div>
                        
                        <div class="premium-feature">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Soporte postventa</strong> para resolver dudas</span>
                        </div>
                    </div>
                    
                    <!-- ✅ SECCIÓN DE PRECIOS CON OFERTAS MEJORADA -->
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
                    <div class="precio-normal">
                        <i class="fas fa-euro-sign"></i> <?= number_format($precio_final, 2) ?>€
                    </div>
                    <?php endif; ?>
                    
                    <!-- ✅ NUEVO: Sistema de Cupones -->
                    <?php if($esPremium && $precio_final > 0): ?>
                    <div class="cupon-section" id="cuponSection">
                        <h6><i class="fas fa-ticket-alt"></i> ¿Tienes un cupón de descuento?</h6>
                        
                        <div class="input-group">
                            <input type="text" class="form-control" id="codigoCupon" placeholder="Ingresa tu código" maxlength="50">
                            <button class="btn btn-success" type="button" id="aplicarCupon">
                                <i class="fas fa-check me-1"></i>
                                <!-- En pantallas grandes pone este texto: -->
                                <span class="d-none d-sm-inline">Aplicar</span>
                                <!-- En pantallas pequeñas pone este texto:  -->
                                <span class="d-sm-none">OK</span>
                            </button>
                        </div>
                        
                        <div class="cupon-loading" id="cuponLoading">
                            <i class="fas fa-spinner fa-spin"></i> Validando cupón...
                        </div>
                        
                        <div class="cupon-mensaje" id="cuponMensaje" style="display: none;"></div>
                    </div>

                    <!-- Información de cupón aplicado (se muestra cuando se aplica un cupón) -->
                    <div class="cupon-aplicado-info" id="cuponAplicadoInfo" style="display: none;">
                        <i class="fas fa-ticket-alt"></i> 
                        Cupón <strong id="cuponAplicadoCodigo"></strong> aplicado
                        <button type="button" class="remover-cupon" id="removerCupon" title="Remover cupón">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Desglose de precios con cupón -->
                    <div class="precio-breakdown" id="precioBreakdown" style="display: none;">
                        <div class="precio-linea">
                            <span>Precio original:</span>
                            <span id="precioOriginalDisplay"><?= number_format($precio_final, 2) ?>€</span>
                        </div>
                        <div class="precio-linea" id="lineaDescuentoCupon" style="display: none;">
                            <span>Descuento cupón (<span id="cuponDescripcion"></span>):</span>
                            <span class="precio-descuento-cupon">-<span id="descuentoCuponDisplay">0.00</span>€</span>
                        </div>
                        <div class="precio-linea total">
                            <span>Total a pagar:</span>
                            <span id="precioFinalDisplay"><?= number_format($precio_final, 2) ?>€</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <input type="hidden" name="precio_final" id="precio-final" value="<?= number_format($precio_final, 2, '.', '') ?>">
                    
                    <?php else: ?>
                    <!-- Información para rutas gratuitas -->
                    <div class="free-route-info">
                        <h5><i class="fas fa-gift"></i> Ruta Gratuita</h5>
                        
                        <div class="free-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Track GPX listo para usar en tu dispositivo GPS</span>
                        </div>
                        
                        <div class="free-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Ruta verificada para "<?= ucfirst($ruta->nivel) ?>"</span>
                        </div>
                        
                        <div class="free-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Incluye puntos de interés destacados</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($user->isLoggedIn()): ?>
                        <?php if($esPremium): ?>
                            <div id="paypal-button-container"></div>
                        <?php elseif($gpxExists): ?>
                            <div class="download-btn-container">
                                <!-- <a href="<?= $gpxFile ?>" class="btn btn-success btn-lg w-100" download>
                                    <i class="fas fa-download me-2"></i> Descargar GPX
                                </a> -->
                                <!-- Boton descarga registra bajada: -->
                                <?php $csrf = Token::generate(); ?>
<a id="btnDescargar"
   href="<?= 
            

            // Si $ruta->gpx ya empieza por «/», NO añadimos $us_url_root.
            $gpxUrl = (strpos($gpxFile, '/') === 0)
             ? $gpxFile
             : $us_url_root . $gpxFile;
        ?>"
   data-ruta="<?= $ruta->id ?>" 
   data-csrf="<?= $csrf ?>"
   download
   class="btn btn-success btn-lg w-100">
  Descargar gratis
</a>
                                <p class="text-muted mt-2 small text-center">
                                    <i class="fas fa-info-circle"></i> Compatible con la mayoría de navegadores GPS
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                Archivo GPX no configurado para esta ruta
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
<div class="alert alert-warning">
    Debes <a href="#" onclick="loginAndReturn(); return false;" class="alert-link">iniciar sesión</a> para <?= $esPremium ? 'comprar' : 'descargar' ?>
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

<!-- Galería de Imágenes - Versión Optimizada para Móviles -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-white"><i class="fas fa-images"></i> Galería de Imágenes (<?= count($imagenes_galeria) ?>)</h4>
        </div>
        <div class="p-4">
            <!-- Slideshow Container -->
            <div class="simple-slideshow position-relative" id="simpleSlideshow">
                <div class="slideshow-wrapper" id="slideshowWrapper">
                    <div class="slides-container" id="slidesContainer">
                        <?php foreach($imagenes_galeria as $index => $imagen): ?>
                        <div class="slide">
                            <img src="../<?= $imagen->imagen ?>" 
                                 alt="<?= $imagen->descripcion ?: 'Imagen de la ruta' ?>"
                                 class="slide-image"
                                 data-index="<?= $index ?>"
                                 draggable="false">
                            <?php if(!empty($imagen->descripcion)): ?>
                            <div class="slide-caption">
                                <p class="mb-0 text-center"><?= htmlspecialchars($imagen->descripcion) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Controles de zoom (solo móvil) -->
                    <div class="zoom-controls d-md-none">
                        <button class="zoom-btn" onclick="zoomOut()" title="Alejar">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button class="zoom-btn" onclick="zoomIn()" title="Acercar">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <!-- Indicador de zoom -->
                    <div class="zoom-indicator" id="zoomIndicator">100%</div>

                    <!-- Controles de navegación (desktop) -->
                    <button class="slide-btn prev-btn d-none d-md-block" onclick="changeSlide(-1)" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); background: rgba(128, 0, 0, 0.8); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 18px; cursor: pointer; z-index: 20;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="slide-btn next-btn d-none d-md-block" onclick="changeSlide(1)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: rgba(128, 0, 0, 0.8); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 18px; cursor: pointer; z-index: 20;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    
                    <!-- Botón de pantalla completa -->
                    <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Pantalla completa">
                        <i class="fas fa-expand" id="fullscreenIcon"></i>
                    </button>
                    
                    <!-- Controles en pantalla completa -->
                    <div class="fullscreen-controls">
                        <button class="fs-control-btn" onclick="changeSlide(-1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="fs-control-btn" onclick="resetZoom()" title="Resetear zoom">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button class="fs-control-btn" onclick="toggleFullscreen()">
                            <i class="fas fa-compress"></i>
                        </button>
                        <button class="fs-control-btn" onclick="changeSlide(1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Indicadores -->
                    <div class="slide-indicators">
                        <?php foreach($imagenes_galeria as $index => $imagen): ?>
                        <span class="indicator-dot <?= $index === 0 ? 'active' : '' ?>" 
                              onclick="goToSlide(<?= $index ?>)"
                              data-index="<?= $index ?>">
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Texto de ayuda para móviles -->
            <p class="help-text d-md-none">
                <i class="fas fa-hand-point-up"></i> Desliza para cambiar • Pellizca para zoom • <i class="fas fa-expand"></i> para pantalla completa
            </p>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentSlide = 0;
const totalSlides = <?= count($imagenes_galeria) ?>;
let isFullscreen = false;

// Variables para zoom
let currentZoom = 1;
let maxZoom = 3;
let minZoom = 1;

// Variables para touch y gestos
let touchStartX = 0;
let touchEndX = 0;
let isDragging = false;
let startPos = 0;
let currentTranslate = 0;
let prevTranslate = 0;

// Variables para pinch zoom
let initialDistance = 0;
let initialZoom = 1;
let isPinching = false;

// Variables para pan (desplazar imagen con zoom)
let isPanning = false;
let startPanX = 0;
let startPanY = 0;
let currentPanX = 0;
let currentPanY = 0;

const slidesContainer = document.getElementById('slidesContainer');
const slideshowWrapper = document.getElementById('slideshowWrapper');
const simpleSlideshow = document.getElementById('simpleSlideshow');

// ===== FUNCIONES DE NAVEGACIÓN =====

function changeSlide(direction) {
    resetZoom();
    currentSlide += direction;
    
    if (currentSlide >= totalSlides) {
        currentSlide = 0;
    } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    }
    
    updateSlideshow();
}

function goToSlide(index) {
    resetZoom();
    currentSlide = index;
    updateSlideshow();
}

function updateSlideshow() {
    const translateX = -currentSlide * 100;
    slidesContainer.style.transform = `translateX(${translateX}%)`;
    
    // Actualizar indicadores
    document.querySelectorAll('.indicator-dot').forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
    });
}

// ===== FUNCIONES DE ZOOM =====

function getCurrentImage() {
    return document.querySelector(`.slide-image[data-index="${currentSlide}"]`);
}

function updateZoom(zoom, panX = 0, panY = 0) {
    const img = getCurrentImage();
    if (!img) return;
    
    currentZoom = Math.max(minZoom, Math.min(maxZoom, zoom));
    currentPanX = panX;
    currentPanY = panY;
    
    img.style.transform = `scale(${currentZoom}) translate(${currentPanX}px, ${currentPanY}px)`;
    
    // Mostrar indicador de zoom
    const indicator = document.getElementById('zoomIndicator');
    if (currentZoom > 1.1) {
        indicator.style.display = 'block';
        indicator.textContent = Math.round(currentZoom * 100) + '%';
    } else {
        indicator.style.display = 'none';
    }
    
    // Cambiar cursor
    if (currentZoom > 1.1) {
        img.style.cursor = 'grab';
    } else {
        img.style.cursor = 'default';
    }
}

function zoomIn() {
    updateZoom(currentZoom + 0.3);
}

function zoomOut() {
    if (currentZoom > 1.1) {
        updateZoom(currentZoom - 0.3);
    } else {
        resetZoom();
    }
}

function resetZoom() {
    currentZoom = 1;
    currentPanX = 0;
    currentPanY = 0;
    updateZoom(1, 0, 0);
}

// ===== PANTALLA COMPLETA CORREGIDA =====

function toggleFullscreen() {
    if (!isFullscreen) {
        enterFullscreen();
    } else {
        exitFullscreen();
    }
}

function enterFullscreen() {
    // Guardar posición actual del scroll
    const scrollY = window.scrollY;
    document.documentElement.style.setProperty('--scroll-y', scrollY + 'px');
    
    // Activar modo pantalla completa
    simpleSlideshow.classList.add('fullscreen-mode');
    document.body.classList.add('fullscreen-active');
    
    // Fijar posición del body para evitar saltos
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
    document.body.style.overflow = 'hidden';
    
    isFullscreen = true;
    document.getElementById('fullscreenIcon').className = 'fas fa-compress';
    
    // Resetear zoom al entrar en pantalla completa
    resetZoom();
}

function exitFullscreen() {
    // Desactivar modo pantalla completa
    simpleSlideshow.classList.remove('fullscreen-mode');
    document.body.classList.remove('fullscreen-active');
    
    // Restaurar scroll y posición
    const scrollY = document.documentElement.style.getPropertyValue('--scroll-y');
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    document.body.style.overflow = '';
    
    // Restaurar posición del scroll
    window.scrollTo(0, parseInt(scrollY || '0') * -1);
    
    isFullscreen = false;
    document.getElementById('fullscreenIcon').className = 'fas fa-expand';
    
    // Resetear zoom al salir de pantalla completa
    resetZoom();
}

// ===== EVENTOS TÁCTILES =====

// Calcular distancia entre dos puntos (para pinch)
function getDistance(touches) {
    const dx = touches[0].clientX - touches[1].clientX;
    const dy = touches[0].clientY - touches[1].clientY;
    return Math.sqrt(dx * dx + dy * dy);
}

// Touch start
function handleTouchStart(e) {
    if (e.touches.length === 2) {
        // Pinch zoom
        isPinching = true;
        initialDistance = getDistance(e.touches);
        initialZoom = currentZoom;
        return;
    }
    
    if (e.touches.length === 1) {
        const touch = e.touches[0];
        
        if (currentZoom > 1.1) {
            // Pan mode (desplazar imagen con zoom)
            isPanning = true;
            startPanX = touch.clientX - currentPanX;
            startPanY = touch.clientY - currentPanY;
            getCurrentImage().style.cursor = 'grabbing';
        } else {
            // Slide mode (cambiar imagen)
            isDragging = true;
            touchStartX = touch.clientX;
            startPos = touch.clientX;
            prevTranslate = -currentSlide * 100;
        }
    }
}

// Touch move
function handleTouchMove(e) {
    e.preventDefault();
    
    if (isPinching && e.touches.length === 2) {
        // Pinch zoom
        const distance = getDistance(e.touches);
        const scale = distance / initialDistance;
        updateZoom(initialZoom * scale);
        return;
    }
    
    if (isPanning && e.touches.length === 1 && currentZoom > 1.1) {
        // Pan image
        const touch = e.touches[0];
        currentPanX = touch.clientX - startPanX;
        currentPanY = touch.clientY - startPanY;
        
        // Limitar el pan para no sacar demasiado la imagen
        const maxPan = 100;
        currentPanX = Math.max(-maxPan, Math.min(maxPan, currentPanX));
        currentPanY = Math.max(-maxPan, Math.min(maxPan, currentPanY));
        
        updateZoom(currentZoom, currentPanX, currentPanY);
        return;
    }
    
    if (isDragging && e.touches.length === 1 && currentZoom <= 1.1) {
        // Slide change
        const currentPosition = e.touches[0].clientX;
        const diff = currentPosition - startPos;
        const percentageMoved = (diff / slidesContainer.offsetWidth) * 100;
        
        currentTranslate = prevTranslate + percentageMoved;
        slidesContainer.style.transform = `translateX(${currentTranslate}%)`;
    }
}

// Touch end
function handleTouchEnd(e) {
    if (isPinching) {
        isPinching = false;
        return;
    }
    
    if (isPanning) {
        isPanning = false;
        getCurrentImage().style.cursor = 'grab';
        return;
    }
    
    if (isDragging) {
        isDragging = false;
        touchEndX = e.changedTouches[0].clientX;
        
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                changeSlide(1);
            } else {
                changeSlide(-1);
            }
        } else {
            updateSlideshow();
        }
    }
}

// Doble tap para zoom
let lastTap = 0;
function handleDoubleTap(e) {
    const currentTime = new Date().getTime();
    const tapLength = currentTime - lastTap;
    
    if (tapLength < 300 && tapLength > 0) {
        e.preventDefault();
        
        if (currentZoom > 1.1) {
            resetZoom();
        } else {
            updateZoom(2);
        }
    }
    lastTap = currentTime;
}

// ===== EVENT LISTENERS =====

// Touch events
slidesContainer.addEventListener('touchstart', handleTouchStart, { passive: false });
slidesContainer.addEventListener('touchmove', handleTouchMove, { passive: false });
slidesContainer.addEventListener('touchend', handleTouchEnd, { passive: true });
slidesContainer.addEventListener('touchend', handleDoubleTap, { passive: false });

// Mouse events para desktop
slidesContainer.addEventListener('mousedown', (e) => {
    e.preventDefault();
    if (currentZoom > 1.1) {
        isPanning = true;
        startPanX = e.clientX - currentPanX;
        startPanY = e.clientY - currentPanY;
        getCurrentImage().style.cursor = 'grabbing';
    }
});

document.addEventListener('mousemove', (e) => {
    if (isPanning && currentZoom > 1.1) {
        currentPanX = e.clientX - startPanX;
        currentPanY = e.clientY - startPanY;
        updateZoom(currentZoom, currentPanX, currentPanY);
    }
});

document.addEventListener('mouseup', () => {
    if (isPanning) {
        isPanning = false;
        const img = getCurrentImage();
        if (img) img.style.cursor = 'grab';
    }
});

// Wheel zoom para desktop
slidesContainer.addEventListener('wheel', (e) => {
    if (e.ctrlKey || e.metaKey) {
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.2 : 0.2;
        updateZoom(currentZoom + delta);
    }
});

// Navegación con teclado
document.addEventListener('keydown', (e) => {
    if (e.keyCode === 37) { // Flecha izquierda
        changeSlide(-1);
    } else if (e.keyCode === 39) { // Flecha derecha
        changeSlide(1);
    } else if (e.keyCode === 27) { // ESC
        if (isFullscreen) {
            exitFullscreen();
        } else if (currentZoom > 1.1) {
            resetZoom();
        }
    } else if (e.keyCode === 187 || e.keyCode === 107) { // + 
        zoomIn();
    } else if (e.keyCode === 189 || e.keyCode === 109) { // -
        zoomOut();
    }
});

// Detectar cambio de orientación y resize
window.addEventListener('orientationchange', () => {
    setTimeout(() => {
        resetZoom();
        updateSlideshow();
    }, 300);
});

window.addEventListener('resize', () => {
    if (isFullscreen) {
        resetZoom();
    }
});

// Prevenir zoom del navegador y otros gestos
document.addEventListener('gesturestart', (e) => e.preventDefault());
document.addEventListener('gesturechange', (e) => e.preventDefault());

// Prevenir pull-to-refresh en pantalla completa
document.addEventListener('touchmove', (e) => {
    if (isFullscreen) {
        e.preventDefault();
    }
}, { passive: false });

// Detectar cuando la página se vuelve visible (para reajustar)
document.addEventListener('visibilitychange', () => {
    if (!document.hidden && isFullscreen) {
        setTimeout(() => {
            resetZoom();
            updateSlideshow();
        }, 100);
    }
});

// Inicializar
updateSlideshow();
</script>

<?php endif; //FIN DE GALERIA ?>

<!-- Variables JavaScript necesarias para cupones -->
<script>
// Variables globales para el sistema de cupones
window.cuponData = {
    rutaId: <?= $ruta_id ?>,
    precioOriginal: <?= $precio_final ?>,
    cuponAplicado: null,
    precioFinal: <?= $precio_final ?>
};

// ✅ SISTEMA DE CUPONES - JavaScript principal
document.addEventListener('DOMContentLoaded', function() {
    // Solo ejecutar si existe la sección de cupones
    if (!document.getElementById('cuponSection')) {
        // Si no hay sección de cupones, inicializar PayPal directamente
        if (document.getElementById('paypal-button-container')) {
            renderPayPalButtons();
        }
        return;
    }
    
    const codigoCuponInput = document.getElementById('codigoCupon');
    const aplicarCuponBtn = document.getElementById('aplicarCupon');
    const cuponLoading = document.getElementById('cuponLoading');
    const cuponMensaje = document.getElementById('cuponMensaje');
    const cuponSection = document.getElementById('cuponSection');
    const cuponAplicadoInfo = document.getElementById('cuponAplicadoInfo');
    const precioBreakdown = document.getElementById('precioBreakdown');
    const removerCuponBtn = document.getElementById('removerCupon');
    
    // Aplicar cupón
    aplicarCuponBtn.addEventListener('click', function() {
        const codigo = codigoCuponInput.value.trim().toUpperCase();
        
        if (!codigo) {
            mostrarMensaje('Por favor ingresa un código de cupón', 'error');
            return;
        }
        
        aplicarCupon(codigo);
    });
    
    // Aplicar cupón al presionar Enter
    codigoCuponInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            aplicarCuponBtn.click();
        }
    });
    
    // Remover cupón
    removerCuponBtn.addEventListener('click', function() {
        removerCupon();
    });
    
    // Convertir a mayúsculas automáticamente
    codigoCuponInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    function aplicarCupon(codigo) {
        // Mostrar loading
        cuponLoading.style.display = 'block';
        aplicarCuponBtn.disabled = true;
        cuponMensaje.style.display = 'none';
        
        // Hacer petición AJAX
        fetch('validar_cupon.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                codigo: codigo,
                ruta_id: window.cuponData.rutaId,
                precio_original: window.cuponData.precioOriginal
            })
        })
        .then(response => response.json())
        .then(data => {
            cuponLoading.style.display = 'none';
            aplicarCuponBtn.disabled = false;
            
            if (data.success) {
                // Cupón válido
                window.cuponData.cuponAplicado = data.cupon;
                window.cuponData.precioFinal = data.precios.final_raw;
                
                mostrarCuponAplicado(data);
                actualizarPayPal();
                mostrarMensaje('¡Cupón aplicado correctamente!', 'success');
            } else {
                // Error en el cupón
                mostrarMensaje(data.message, 'error');
            }
        })
        .catch(error => {
            cuponLoading.style.display = 'none';
            aplicarCuponBtn.disabled = false;
            mostrarMensaje('Error al validar el cupón. Inténtalo de nuevo.', 'error');
            console.error('Error:', error);
        });
    }
    
    function mostrarCuponAplicado(data) {
        // Ocultar sección de entrada de cupón
        cuponSection.style.display = 'none';
        
        // Mostrar información del cupón aplicado
        document.getElementById('cuponAplicadoCodigo').textContent = data.cupon.codigo;
        cuponAplicadoInfo.style.display = 'block';
        
        // Mostrar desglose de precios
        document.getElementById('precioOriginalDisplay').textContent = data.precios.original + '€';
        document.getElementById('cuponDescripcion').textContent = 
            data.cupon.tipo_descuento === 'porcentaje' ? 
            data.cupon.valor_descuento + '%' : 
            data.cupon.valor_descuento + '€';
        document.getElementById('descuentoCuponDisplay').textContent = data.precios.descuento;
        document.getElementById('precioFinalDisplay').textContent = data.precios.final + '€';
        document.getElementById('lineaDescuentoCupon').style.display = 'flex';
        precioBreakdown.style.display = 'block';
        
        // Actualizar el precio en el input hidden
        document.getElementById('precio-final').value = data.precios.final_raw;
    }
    
    function removerCupon() {
        // Resetear estado
        window.cuponData.cuponAplicado = null;
        window.cuponData.precioFinal = window.cuponData.precioOriginal;
        
        // Mostrar sección de entrada de cupón
        cuponSection.style.display = 'block';
        cuponAplicadoInfo.style.display = 'none';
        precioBreakdown.style.display = 'none';
        
        // Limpiar input
        codigoCuponInput.value = '';
        cuponMensaje.style.display = 'none';
        
        // Restaurar precio original
        document.getElementById('precio-final').value = window.cuponData.precioOriginal;
        
        // Actualizar PayPal
        actualizarPayPal();
    }
    
    function mostrarMensaje(mensaje, tipo) {
        cuponMensaje.textContent = mensaje;
        cuponMensaje.className = 'cupon-mensaje ' + tipo;
        cuponMensaje.style.display = 'block';
        
        // Ocultar mensaje después de 5 segundos si es de éxito
        if (tipo === 'success') {
            setTimeout(() => {
                cuponMensaje.style.display = 'none';
            }, 5000);
        }
    }
    
    function actualizarPayPal() {
        // Destruir PayPal existente
        if (window.paypalButtonsRendered) {
            document.getElementById('paypal-button-container').innerHTML = '';
        }
        
        // Crear nuevo PayPal con precio actualizado
        renderPayPalButtons();
    }
    
    // Inicializar PayPal al cargar
    renderPayPalButtons();
});

// ✅ PAYPAL BUTTONS MODIFICADO PARA CUPONES
function renderPayPalButtons() {
    if (!document.getElementById('paypal-button-container')) {
        return; // No hay contenedor de PayPal
    }
    
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
                // Obtener ID de transacción
                let transactionId = details.id;
                
                if (details.purchase_units && 
                    details.purchase_units[0] && 
                    details.purchase_units[0].payments && 
                    details.purchase_units[0].payments.captures && 
                    details.purchase_units[0].payments.captures[0]) {
                    transactionId = details.purchase_units[0].payments.captures[0].id;
                }
                
                // Datos de la venta
                const rutaId = <?= $ruta_id ?>;
                const precio = document.getElementById('precio-final').value;
                const status = details.status;
                const payerName = details.payer.name.given_name + ' ' + details.payer.name.surname;
                const payerEmail = details.payer.email_address;
                const payerId = details.payer.payer_id;
                
                // Datos del cupón si está aplicado
                let cuponParams = '';
                if (window.cuponData.cuponAplicado) {
                    cuponParams = `&cupon_id=${window.cuponData.cuponAplicado.id}&precio_original=${window.cuponData.precioOriginal}&descuento_cupon=${window.cuponData.precioOriginal - window.cuponData.precioFinal}`;
                }
                
                // Redirigir con todos los parámetros
                window.location.href = `procesar_venta.php?ruta_id=${rutaId}&precio=${precio}&transactionId=${encodeURIComponent(transactionId)}&status=${encodeURIComponent(status)}&payerId=${encodeURIComponent(payerId)}&payerEmail=${encodeURIComponent(payerEmail)}&payerName=${encodeURIComponent(payerName)}&repostaje=0&hoteles=0&puntos=0${cuponParams}`;
            });
        }
    }).render('#paypal-button-container');
    
    window.paypalButtonsRendered = true;
}

// Función para guardar la ubicación actual y hacer login
function loginAndReturn() {
    // Guardar la URL actual completa incluyendo parámetros
    sessionStorage.setItem('returnAfterLogin', window.location.href);
    // Ir a la página de login
    window.location.href = '../users/login.php';
}

// Función para registro con retorno
function registerAndReturn() {
    // Guardar la URL actual completa incluyendo parámetros  
    sessionStorage.setItem('returnAfterLogin', window.location.href);
    // Ir a la página de registro
    window.location.href = '../users/join.php';
}

// Al cargar la página, verificar si hay sesión con UserSpice PARA USAR CUPONES
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el usuario está logueado usando UserSpice
    const isLoggedIn = <?php echo $user->isLoggedIn() ? 'true' : 'false'; ?>;
    
    if (!isLoggedIn) {
        // Deshabilitar el input y botón de cupón
        document.getElementById('codigoCupon').disabled = true;
        document.getElementById('aplicarCupon').disabled = true;
        
        // Cambiar placeholder para informar
        document.getElementById('codigoCupon').placeholder = "Inicia sesión para usar cupones";
        
        // Opcional: Agregar tooltip explicativo
        document.getElementById('codigoCupon').title = "Debes iniciar sesión para aplicar cupones";
    }
});

// JAVASCRIPT ARREGLADO COMPARTIR

function compartirRuta() {
    const url = window.location.href;
    const titulo = <?= json_encode($titulo) ?>;
    
    // Detectar si es móvil real
    const esMobil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    // Solo usar navigator.share en móviles reales
    if (esMobil && navigator.share) {
        navigator.share({
            title: titulo + ' - Candeivid',
            text: <?= json_encode($descripcion) ?>,
            url: url
        }).catch(() => {
            // Si falla en móvil, copiar
            copiarEnlace(url);
        });
    } else {
        // En escritorio, siempre copiar
        copiarEnlace(url);
    }
}

function copiarEnlace(url) {
    // Crear elemento temporal
    const textarea = document.createElement('textarea');
    textarea.value = url;
    
    // Estilos para hacer invisible pero seleccionable
    textarea.style.position = 'fixed';
    textarea.style.top = '0';
    textarea.style.left = '0';
    textarea.style.width = '2em';
    textarea.style.height = '2em';
    textarea.style.padding = '0';
    textarea.style.border = 'none';
    textarea.style.outline = 'none';
    textarea.style.boxShadow = 'none';
    textarea.style.background = 'transparent';
    
    document.body.appendChild(textarea);
    
    // Enfocar y seleccionar
    textarea.focus();
    textarea.select();
    textarea.setSelectionRange(0, 99999);
    
    let copiado = false;
    
    try {
        // Intentar copiar
        copiado = document.execCommand('copy');
    } catch (err) {
        console.log('Error al copiar:', err);
    }
    
    // Limpiar
    document.body.removeChild(textarea);
    
    if (copiado) {
        mostrarExito();
    } else {
        // Último recurso: mostrar el enlace
        const mensaje = 'Copia este enlace:';
        if (window.prompt) {
            prompt(mensaje, url);
        } else {
            alert(mensaje + '\n\n' + url);
        }
    }
}

function mostrarExito() {
    const btn = document.getElementById('btnCompartir');
    const textoOriginal = btn.innerHTML;
    
    // Cambiar aspecto del botón temporalmente
    btn.innerHTML = '<i class="fas fa-check"></i> ¡Enlace copiado!';
    btn.style.backgroundColor = '#28a745';
    btn.style.borderColor = '#28a745';
    
    // Restaurar después de 2 segundos
    setTimeout(() => {
        btn.innerHTML = textoOriginal;
        btn.style.backgroundColor = '';
        btn.style.borderColor = '';
    }, 2000);
}
</script>

<!-- SCRIPT PARA REGISTRAR LA DESCARGA -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btnDescargar');
  btn.addEventListener('click', function () {
    // 1️⃣ Registramos la descarga (no bloquea la navegación)
    fetch('registrar_descarga.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({
        csrf: '<?= $csrf ?>',
        ruta_id: this.dataset.ruta,
        tipo: 'gratis'
      })
    })
    .catch(err => console.error('Log descarga:', err));
    // 2️⃣ La descarga se hace sola porque el anchor ya tiene el href
  });
});
</script>


<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>