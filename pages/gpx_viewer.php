<?php
/**
 * Visor de rutas GPX temporal - NO guarda archivos
 * 
 * Características:
 * - Procesamiento temporal de archivos GPX
 * - Navegación GPS funcional
 * - No guarda archivos en servidor (privacidad total)
 */
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Variables para manejo de errores y mensajes
$error = false;
$errorMsg = '';
$success = false;
$successMsg = '';
$gpxName = '';
$gpxDesc = '';
$gpxPoints = 0;
$gpxData = null; // Para almacenar los datos del GPX temporalmente

// Procesar archivo GPX cuando se envía el formulario
if(isset($_POST['convert']) && isset($_FILES['gpxFile'])) {
    if($_FILES['gpxFile']['error'] === UPLOAD_ERR_OK) {
        $originalFileName = $_FILES['gpxFile']['name'];
        $tmpName = $_FILES['gpxFile']['tmp_name'];
        
        // Verificar extensión
        $fileExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        if($fileExt !== 'gpx') {
            $error = true;
            $errorMsg = 'Por favor, sube un archivo GPX válido (.gpx)';
        } else {
            // Leer archivo GPX directamente desde temporal
            try {
                $gpxContent = file_get_contents($tmpName);
                $xml = new SimpleXMLElement($gpxContent);
                
                // Extraer nombre y descripción si están disponibles
                $gpxName = isset($xml->metadata->name) ? (string)$xml->metadata->name : pathinfo($originalFileName, PATHINFO_FILENAME);
                $gpxDesc = isset($xml->metadata->desc) ? (string)$xml->metadata->desc : '';
                
                // Contar puntos usando xpath compatible con namespaces
                $trackPoints = $xml->xpath('//*[local-name()="trkpt"]');
                $routePoints = $xml->xpath('//*[local-name()="rtept"]');
                $wayPoints = $xml->xpath('//*[local-name()="wpt"]');
                
                $gpxPoints = count($trackPoints) + count($routePoints) + count($wayPoints);
                
                if($gpxPoints > 0) {
                    $success = true;
                    $successMsg = 'Archivo GPX cargado exitosamente - ' . $gpxPoints . ' puntos encontrados';
                    
                    // Guardar contenido GPX temporalmente para el JavaScript
                    $gpxData = base64_encode($gpxContent);
                } else {
                    $error = true;
                    $errorMsg = 'El archivo GPX no contiene puntos de ruta válidos';
                }
                
            } catch(Exception $e) {
                $error = true;
                $errorMsg = 'Error al procesar el archivo GPX: ' . $e->getMessage();
            }
        }
    } else {
        $error = true;
        switch($_FILES['gpxFile']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMsg = 'El archivo es demasiado grande';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMsg = 'El archivo se subió parcialmente';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMsg = 'No se ha seleccionado ningún archivo';
                break;
            default:
                $errorMsg = 'Error de carga desconocido';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $success ? htmlspecialchars($gpxName) : 'Visor de Rutas GPX' ?></title>
    <!-- Bootstrap y FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        .container-lg {
            max-width: 1200px;
        }
        .viewer-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 25px;
            margin-bottom: 30px;
        }
        .map-container {
            height: 500px;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
        .file-upload-area {
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .file-upload-area:hover {
            background-color: #f8f9fa;
            border-color: #0056b3;
        }
        .file-preview {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: none;
        }
        .action-button {
            font-weight: 500;
            letter-spacing: 0.5px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .stats-panel {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .stat-item {
            border-bottom: 1px solid #e9ecef;
            padding: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        .stat-item:last-child {
            border-bottom: none;
        }
        .route-title {
            margin-bottom: 5px;
            font-weight: 600;
            color: #212529;
        }
        .route-description {
            color: #6c757d;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .map-controls {
            background-color: rgba(255,255,255,0.95);
            border-radius: 6px;
            padding: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .map-control-btn {
            border: none;
            background: #fff;
            padding: 6px 8px;
            margin: 0;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 11px;
            min-width: 45px;
            text-align: center;
        }
        .map-control-btn:hover {
            background: #f0f0f0;
        }
        .map-control-btn.active {
            background: #007bff;
            color: white;
        }

        /* GPS Button - Más compacto */
        .gps-btn {
            background-color: #28a745 !important;
            color: white !important;
            padding: 8px !important;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 8px;
            width: 100%;
        }

        .gps-btn:hover {
            background-color: #218838 !important;
        }

        .gps-btn:disabled {
            background-color: #6c757d !important;
            cursor: not-allowed;
        }

        /* Panel de navegación - Estilo Google Maps */
        .nav-panel {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 80px; /* Espacio para los controles */
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            backdrop-filter: blur(10px);
            display: none;
        }

        .nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #e0e0e0;
            background: #007bff;
            color: white;
            border-radius: 8px 8px 0 0;
            font-size: 13px;
            font-weight: 500;
        }

        .nav-compact {
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }

        .nav-expanded {
            padding: 10px 12px;
            font-size: 12px;
            line-height: 1.3;
        }

        .nav-expanded div {
            margin-bottom: 4px;
        }

        .nav-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 12px;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .nav-toggle:hover {
            background: rgba(255,255,255,0.2);
        }

        .stop-nav-btn {
            background: #dc3545 !important;
            color: white !important;
            padding: 3px 8px !important;
            font-size: 11px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 8px;
        }

        .stop-nav-btn:hover {
            background: #c82333 !important;
        }

        /* Responsive - Mobile optimizations */
        @media (max-width: 768px) {
            .map-controls {
                top: 5px;
                right: 5px;
                padding: 6px;
            }
            
            .map-control-btn {
                padding: 4px 6px;
                font-size: 10px;
                min-width: 40px;
            }
            
            .gps-btn {
                padding: 6px !important;
                font-size: 11px;
            }
            
            .nav-panel {
                top: 5px;
                left: 5px;
                right: 60px;
            }
            
            .nav-header {
                padding: 6px 10px;
                font-size: 12px;
            }
            
            .nav-compact {
                padding: 6px 10px;
                font-size: 11px;
            }
            
            .nav-expanded {
                padding: 8px 10px;
                font-size: 11px;
            }
        }

        /* Estados de navegación */
        .off-route {
            color: #dc3545;
            font-weight: bold;
        }

        .calculating-route {
            color: #fd7e14;
            font-weight: bold;
        }

        /* Estilos para el triángulo de navegación */
        .user-triangle-marker {
            z-index: 1000 !important;
        }

        /* Animación para la línea de navegación */
        .navigation-line {
            animation: dashMove 2s linear infinite;
        }

        /* Animación para la ruta de vuelta */
        .return-route-line {
            animation: returnRouteDash 3s linear infinite;
        }

        @keyframes dashMove {
            0% {
                stroke-dashoffset: 0;
            }
            100% {
                stroke-dashoffset: 30px;
            }
        }

        @keyframes returnRouteDash {
            0% {
                stroke-dashoffset: 0;
            }
            100% {
                stroke-dashoffset: 50px;
            }
        }

        /* Estilos para estados fuera de ruta */
        .off-route {
            color: #dc3545;
            font-weight: bold;
            animation: blink 1s infinite;
        }

        .calculating-route {
            color: #fd7e14;
            font-weight: bold;
            animation: pulse-orange 1.5s infinite;
        }

        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        @keyframes pulse-orange {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        /* Animación para la posición del usuario */
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .user-position-pulse {
            animation: pulse 2s infinite;
        }

        .privacy-notice {
            background-color: #e8f5e8;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container-lg py-4">
        <div class="viewer-container">
            <?php if($success && $gpxData): ?>
                <!-- Modo visualización temporal de GPX -->
                <h2 class="mb-3">
                    <i class="fas fa-route"></i> <?= htmlspecialchars($gpxName) ?>
                </h2>
                
                <div class="privacy-notice">
                    <i class="fas fa-shield-alt"></i> <strong>Privacidad total:</strong> Tu archivo GPX se procesa temporalmente y no se guarda en el servidor.
                </div>
                
                <?php if(!empty($gpxDesc)): ?>
                    <p class="route-description"><?= htmlspecialchars($gpxDesc) ?></p>
                <?php endif; ?>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="stats-panel">
                            <h5><i class="fas fa-chart-line"></i> Estadísticas de la ruta</h5>
                            <div class="stat-item">
                                <span>Puntos en el archivo GPX:</span>
                                <strong><?= $gpxPoints ?></strong>
                            </div>
                            <div class="stat-item">
                                <span>Distancia calculada:</span>
                                <strong id="route-distance">Calculando...</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Visualización Temporal</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">Este visor procesa tu archivo GPX temporalmente:</p>
                                <ul class="mb-0">
                                    <li><strong>No se guarda</strong> en el servidor</li>
                                    <li><strong>Privacidad total</strong> garantizada</li>
                                    <li><strong>Navegación GPS</strong> funcional</li>
                                    <li><strong>Múltiples capas</strong> de mapa</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mapa con la ruta -->
                <div class="position-relative">
                    <div id="map" class="map-container"></div>
                    
                    <!-- Controles compactos del mapa -->
                    <div class="map-controls">
                        <button class="map-control-btn active" id="map-osm">OSM</button>
                        <button class="map-control-btn" id="map-terrain">Terreno</button>
                        <button class="map-control-btn" id="map-satellite">Satélite</button>
                        <button id="start-navigation" class="gps-btn">
                            <i class="fas fa-location-arrow"></i> GPS
                        </button>
                    </div>
                    
                    <!-- Panel de navegación compacto estilo Google Maps -->
                    <div id="nav-panel" class="nav-panel">
                        <div class="nav-header">
                            <span><i class="fas fa-navigation"></i> Navegación GPS</span>
                            <div>
                                <button id="nav-toggle" class="nav-toggle">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <button id="stop-navigation" class="stop-nav-btn">
                                    <i class="fas fa-stop"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Vista compacta (por defecto) -->
                        <div id="nav-compact" class="nav-compact">
                            <div>
                                <span id="main-instruction">Iniciando GPS...</span>
                            </div>
                            <div>
                                <strong id="distance-display">--</strong>
                            </div>
                        </div>
                        
                        <!-- Vista expandida (oculta por defecto) -->
                        <div id="nav-expanded" class="nav-expanded" style="display: none;">
                            <div>
                                <i class="fas fa-road"></i> <span id="distance-to-next">Calculando...</span>
                            </div>
                            <div>
                                <i class="fas fa-location-arrow"></i> <span id="current-direction">Esperando GPS...</span>
                            </div>
                            <div>
                                <i class="fas fa-tachometer-alt"></i> <span id="current-speed">0 km/h</span>
                            </div>
                            <div>
                                <i class="fas fa-flag-checkered"></i> <span id="distance-to-finish">Calculando...</span>
                            </div>
                            <div style="margin-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                                <button id="center-position" class="map-control-btn" style="padding: 4px 8px; background: #007bff; color: white; border: none; border-radius: 3px; font-size: 11px;">
                                    <i class="fas fa-crosshairs"></i> Centrar
                                </button>
                                <label style="display: flex; align-items: center; font-size: 11px;">
                                    <input type="checkbox" id="follow-mode" checked style="margin-right: 4px;"> Auto-seguir
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botón volver -->
                <div class="text-center mt-4">
                    <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cargar otro archivo GPX
                    </a>
                    <!-- <a href="siluetas_gpx.php" class="btn btn-info ml-2">
                        <i class="fas fa-draw-polygon"></i> Generar Silueta
                    </a> -->
                </div>
                
                <script>
                // Variables globales
                var map;
                var routePolylines = [];
                var routePoints = [];
                var totalDistance = 0;
                
                // Variables de navegación
                var navigationActive = false;
                var userPositionMarker = null;
                var userAccuracyCircle = null;
                var watchPositionId = null;
                var currentPosition = null;
                var closestPointIndex = -1;
                var userHeading = 0;
                var followMode = true;
                var navigationLine = null;
                var nextWaypointMarker = null;
                var returnToRoutePolyline = null;
                var isOffRoute = false;
                var offRouteStartTime = null;
                var returnRouteCalculated = false;

                // Datos GPX desde PHP
                var gpxData = '<?= $gpxData ?>';

                // Función para calcular distancia entre dos puntos (Haversine)
                function calculateDistance(lat1, lng1, lat2, lng2) {
                    var R = 6371000; // Radio de la Tierra en metros
                    var dLat = (lat2 - lat1) * Math.PI / 180;
                    var dLng = (lng2 - lng1) * Math.PI / 180;
                    var lat1Rad = lat1 * Math.PI / 180;
                    var lat2Rad = lat2 * Math.PI / 180;

                    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                            Math.cos(lat1Rad) * Math.cos(lat2Rad) *
                            Math.sin(dLng/2) * Math.sin(dLng/2);
                    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

                    return R * c; // Distancia en metros
                }

                // Función para formatear distancia
                function formatDistance(meters) {
                    if (meters < 1000) {
                        return Math.round(meters) + ' m';
                    } else {
                        return (meters / 1000).toFixed(1) + ' km';
                    }
                }

                // Inicializar el mapa
                document.addEventListener('DOMContentLoaded', function() {
                    // Crear mapa
                    map = L.map('map');
                    
                    // Capas de mapa base
                    var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    });
                    
                    var terrainLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                        attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a>'
                    });
                    
                    var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
                    });
                    
                    // Agregar capa OSM por defecto
                    osmLayer.addTo(map);
                    
                    // Controles de capa
                    document.getElementById('map-osm').addEventListener('click', function() {
                        map.removeLayer(terrainLayer);
                        map.removeLayer(satelliteLayer);
                        map.addLayer(osmLayer);
                        setActiveButton('map-osm');
                    });
                    
                    document.getElementById('map-terrain').addEventListener('click', function() {
                        map.removeLayer(osmLayer);
                        map.removeLayer(satelliteLayer);
                        map.addLayer(terrainLayer);
                        setActiveButton('map-terrain');
                    });
                    
                    document.getElementById('map-satellite').addEventListener('click', function() {
                        map.removeLayer(osmLayer);
                        map.removeLayer(terrainLayer);
                        map.addLayer(satelliteLayer);
                        setActiveButton('map-satellite');
                    });
                    
                    function setActiveButton(id) {
                        document.querySelectorAll('.map-control-btn').forEach(function(btn) {
                            btn.classList.remove('active');
                        });
                        document.getElementById(id).classList.add('active');
                    }
                    
                    // Cargar archivo GPX
                    loadGPX();
                    
                    // Configurar eventos de navegación
                    document.getElementById('start-navigation').addEventListener('click', startNavigation);
                    document.getElementById('stop-navigation').addEventListener('click', stopNavigation);
                    document.getElementById('center-position').addEventListener('click', centerOnUser);
                    document.getElementById('follow-mode').addEventListener('change', function() {
                        followMode = this.checked;
                    });
                    
                    // Configurar toggle del panel de navegación
                    document.getElementById('nav-toggle').addEventListener('click', toggleNavigationPanel);
                });
                
                // Función para alternar entre vista compacta y expandida
                function toggleNavigationPanel() {
                    var compactView = document.getElementById('nav-compact');
                    var expandedView = document.getElementById('nav-expanded');
                    var toggleBtn = document.getElementById('nav-toggle');
                    var toggleIcon = toggleBtn.querySelector('i');
                    
                    if (expandedView.style.display === 'none') {
                        // Mostrar vista expandida
                        compactView.style.display = 'none';
                        expandedView.style.display = 'block';
                        toggleIcon.className = 'fas fa-chevron-up';
                    } else {
                        // Mostrar vista compacta
                        expandedView.style.display = 'none';
                        compactView.style.display = 'block';
                        toggleIcon.className = 'fas fa-chevron-down';
                    }
                }

                function loadGPX() {
                    // Decodificar datos GPX desde base64
                    var gpxText = atob(gpxData);
                    
                    var parser = new DOMParser();
                    var gpxDoc = parser.parseFromString(gpxText, "application/xml");
                    
                    var tracks = gpxDoc.getElementsByTagName("trk");
                    var allCoords = [];
                    totalDistance = 0;
                    
                    for(var i = 0; i < tracks.length; i++) {
                        var segments = tracks[i].getElementsByTagName("trkseg");
                        for(var j = 0; j < segments.length; j++) {
                            var points = segments[j].getElementsByTagName("trkpt");
                            var segmentCoords = [];
                            
                            for(var k = 0; k < points.length; k++) {
                                var lat = parseFloat(points[k].getAttribute("lat"));
                                var lng = parseFloat(points[k].getAttribute("lon"));
                                var coord = [lat, lng];
                                segmentCoords.push(coord);
                                allCoords.push(coord);
                                
                                // Calcular distancia acumulativa
                                if(allCoords.length > 1) {
                                    var prevCoord = allCoords[allCoords.length - 2];
                                    totalDistance += calculateDistance(prevCoord[0], prevCoord[1], lat, lng);
                                }
                            }
                            
                            if(segmentCoords.length > 0) {
                                var polyline = L.polyline(segmentCoords, {
                                    color: "#3388ff",
                                    weight: 5,
                                    opacity: 0.8
                                }).addTo(map);
                                
                                routePolylines.push(polyline);
                            }
                        }
                    }
                    
                    // Guardar puntos para navegación
                    routePoints = allCoords.map(coord => L.latLng(coord[0], coord[1]));
                    
                    // Marcadores de inicio y fin con iconos mejorados
                    if(allCoords.length > 0) {
                        // Marcador de inicio (verde)
                        L.circleMarker(allCoords[0], {
                            radius: 10,
                            fillColor: "#28a745",
                            color: "#fff",
                            weight: 3,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(map).bindPopup("<b>Inicio</b>");
                        
                        // Marcador de fin (rojo)
                        L.circleMarker(allCoords[allCoords.length-1], {
                            radius: 10,
                            fillColor: "#dc3545",
                            color: "#fff",
                            weight: 3,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(map).bindPopup("<b>Final</b>");
                        
                        // Ajustar vista
                        var bounds = L.latLngBounds(allCoords);
                        map.fitBounds(bounds);
                    }
                    
                    // Actualizar estadísticas
                    document.getElementById('route-distance').textContent = formatDistance(totalDistance);
                }

                // [RESTO DEL CÓDIGO DE NAVEGACIÓN GPS IGUAL QUE EL ORIGINAL]
                
                // Funciones de navegación GPS
                function startNavigation() {
                    if (navigationActive) return;
                    
                    if (routePoints.length === 0) {
                        alert('Por favor, espera a que se cargue la ruta GPX.');
                        return;
                    }
                    
                    if (!navigator.geolocation) {
                        alert('Tu navegador no soporta geolocalización.');
                        return;
                    }
                    
                    // Mostrar panel de navegación
                    document.getElementById('nav-panel').style.display = 'block';
                    
                    // Cambiar estado del botón GPS
                    var gpsBtn = document.getElementById('start-navigation');
                    gpsBtn.disabled = true;
                    gpsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GPS';
                    
                    // Actualizar vista compacta
                    document.getElementById('main-instruction').textContent = 'Conectando GPS...';
                    document.getElementById('distance-display').textContent = '--';
                    
                    // Iniciar seguimiento de posición
                    watchPositionId = navigator.geolocation.watchPosition(
                        updatePosition, 
                        handleLocationError, 
                        { 
                            enableHighAccuracy: true, 
                            maximumAge: 1000, 
                            timeout: 10000 
                        }
                    );
                    
                    navigationActive = true;
                }

                function updatePosition(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    var accuracy = position.coords.accuracy;
                    var speed = position.coords.speed || 0;
                    var heading = position.coords.heading;
                    
                    currentPosition = L.latLng(lat, lng);
                    
                    // Actualizar o crear marcador de posición como triángulo
                    if (!userPositionMarker) {
                        // Crear icono triangular para el usuario
                        var triangleIcon = L.divIcon({
                            className: 'user-triangle-marker',
                            html: `<div style="
                                width: 0; 
                                height: 0; 
                                border-left: 10px solid transparent; 
                                border-right: 10px solid transparent; 
                                border-bottom: 20px solid #007bff;
                                transform: rotate(${heading || 0}deg);
                                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
                                transition: transform 0.3s ease;
                            "></div>`,
                            iconSize: [20, 20],
                            iconAnchor: [10, 15]
                        });
                        
                        userPositionMarker = L.marker(currentPosition, {
                            icon: triangleIcon,
                            zIndexOffset: 1000
                        }).addTo(map);
                        
                        // Círculo de precisión más sutil
                        if (accuracy < 100) {
                            userAccuracyCircle = L.circle(currentPosition, {
                                radius: accuracy,
                                fillColor: '#007bff',
                                fillOpacity: 0.05,
                                color: '#007bff',
                                weight: 1,
                                opacity: 0.3
                            }).addTo(map);
                        }
                    } else {
                        // Actualizar posición y rotación
                        userPositionMarker.setLatLng(currentPosition);
                        
                        // Actualizar rotación del triángulo si tenemos heading
                        if (heading !== null && heading !== undefined) {
                            var triangleIcon = L.divIcon({
                                className: 'user-triangle-marker',
                                html: `<div style="
                                    width: 0; 
                                    height: 0; 
                                    border-left: 10px solid transparent; 
                                    border-right: 10px solid transparent; 
                                    border-bottom: 20px solid #007bff;
                                    transform: rotate(${heading}deg);
                                    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
                                    transition: transform 0.3s ease;
                                "></div>`,
                                iconSize: [20, 20],
                                iconAnchor: [10, 15]
                            });
                            userPositionMarker.setIcon(triangleIcon);
                        }
                        
                        if (userAccuracyCircle) {
                            userAccuracyCircle.setLatLng(currentPosition);
                            userAccuracyCircle.setRadius(accuracy);
                        }
                    }
                    
                    if (heading !== null && heading !== undefined) {
                        userHeading = heading;
                    }
                    
                    updateNavigationInfo(speed);
                    updateNavigationLine();
                    
                    if (followMode) {
                        centerOnUser();
                    }
                }

                function updateNavigationInfo(speedMps) {
                    if (routePoints.length === 0) return;
                    
                    // Encontrar el punto más cercano en la ruta
                    var minDist = Infinity;
                    var minIndex = -1;
                    
                    for (var i = 0; i < routePoints.length; i++) {
                        var dist = currentPosition.distanceTo(routePoints[i]);
                        if (dist < minDist) {
                            minDist = dist;
                            minIndex = i;
                        }
                    }
                    
                    closestPointIndex = minIndex;
                    var onRoute = minDist <= 50; // 50 metros de tolerancia
                    
                    // Detectar si está fuera de ruta
                    if (!onRoute && !isOffRoute) {
                        // Acaba de salirse de la ruta
                        isOffRoute = true;
                        offRouteStartTime = Date.now();
                        returnRouteCalculated = false;
                        
                        // Calcular ruta de vuelta después de 5 segundos fuera de ruta
                        setTimeout(function() {
                            if (isOffRoute && !returnRouteCalculated) {
                                calculateReturnToRoute();
                            }
                        }, 5000);
                        
                    } else if (onRoute && isOffRoute) {
                        // Ha vuelto a la ruta
                        isOffRoute = false;
                        returnRouteCalculated = false;
                        clearReturnToRoute();
                    }
                    
                    // Actualizar información en ambas vistas
                    if (isOffRoute) {
                        var timeOffRoute = Math.floor((Date.now() - offRouteStartTime) / 1000);
                        var offRouteMsg = '⚠️ FUERA DE RUTA (' + formatDistance(minDist) + ')';
                        
                        // Vista compacta
                        document.getElementById('main-instruction').innerHTML = '<span class="off-route">Fuera de ruta</span>';
                        document.getElementById('distance-display').innerHTML = '<span class="off-route">' + formatDistance(minDist) + '</span>';
                        
                        // Vista expandida
                        document.getElementById('distance-to-next').innerHTML = '<span class="off-route">' + offRouteMsg + ' - ' + timeOffRoute + 's</span>';
                        
                        if (!returnRouteCalculated && timeOffRoute >= 5) {
                            document.getElementById('current-direction').innerHTML = '<span class="calculating-route">🔄 Calculando ruta de vuelta...</span>';
                        }
                    } else {
                        var distMsg = formatDistance(minDist) + ' al punto más cercano';
                        
                        // Vista compacta
                        document.getElementById('main-instruction').textContent = 'En ruta';
                        document.getElementById('distance-display').textContent = formatDistance(minDist);
                        
                        // Vista expandida  
                        document.getElementById('distance-to-next').textContent = distMsg;
                    }
                    
                    if (minIndex >= 0) {
                        var distanceToFinish = 0;
                        for (var i = minIndex; i < routePoints.length - 1; i++) {
                            distanceToFinish += routePoints[i].distanceTo(routePoints[i+1]);
                        }
                        
                        document.getElementById('distance-to-finish').textContent = formatDistance(distanceToFinish) + ' hasta el final';
                        
                        if (!isOffRoute) {
                            calculateDirectionGuidance(minIndex);
                        }
                    }
                    
                    var speedKmh = speedMps ? (speedMps * 3.6).toFixed(1) : '0.0';
                    document.getElementById('current-speed').textContent = speedKmh + ' km/h';
                }

                // Función para calcular ruta de vuelta usando OSRM
                function calculateReturnToRoute() {
                    if (!currentPosition || closestPointIndex < 0 || !isOffRoute) return;
                    
                    // Encontrar el mejor punto para volver (no necesariamente el más cercano)
                    var bestReturnPoint = findBestReturnPoint();
                    
                    if (!bestReturnPoint) return;
                    
                    // Construir URL para OSRM API
                    var osrmUrl = 'https://router.project-osrm.org/route/v1/driving/' +
                        currentPosition.lng + ',' + currentPosition.lat + ';' +
                        bestReturnPoint.lng + ',' + bestReturnPoint.lat +
                        '?overview=full&geometries=geojson&steps=true';
                    
                    fetch(osrmUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data.routes && data.routes.length > 0) {
                                displayReturnRoute(data.routes[0], bestReturnPoint);
                                returnRouteCalculated = true;
                            } else {
                                console.error('No se pudo calcular la ruta de vuelta');
                                showFallbackDirection(bestReturnPoint);
                            }
                        })
                        .catch(error => {
                            console.error('Error calculando ruta de vuelta:', error);
                            showFallbackDirection(bestReturnPoint);
                        });
                }

                // Encontrar el mejor punto para volver a la ruta
                function findBestReturnPoint() {
                    if (routePoints.length === 0) return null;
                    
                    // Considerar puntos en un rango de ±20 posiciones desde el punto más cercano
                    var startIndex = Math.max(0, closestPointIndex - 20);
                    var endIndex = Math.min(routePoints.length - 1, closestPointIndex + 20);
                    
                    var bestPoint = null;
                    var bestScore = Infinity;
                    
                    for (var i = startIndex; i <= endIndex; i++) {
                        var point = routePoints[i];
                        var distance = currentPosition.distanceTo(point);
                        
                        // Calcular "score" considerando distancia y progreso en la ruta
                        // Preferir puntos que estén más adelante en la ruta pero no demasiado lejos
                        var progressPenalty = Math.abs(i - closestPointIndex) * 2; // Penalizar alejarse del punto más cercano
                        var score = distance + progressPenalty;
                        
                        if (score < bestScore) {
                            bestScore = score;
                            bestPoint = point;
                        }
                    }
                    
                    return bestPoint;
                }

                // Mostrar la ruta de vuelta en el mapa
                function displayReturnRoute(route, targetPoint) {
                    // Limpiar ruta anterior si existe
                    clearReturnToRoute();
                    
                    // Crear polyline para la ruta de vuelta
                    var coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                    
                    returnToRoutePolyline = L.polyline(coordinates, {
                        color: '#e74c3c',
                        weight: 5,
                        opacity: 0.8,
                        dashArray: '15, 10',
                        className: 'return-route-line'
                    }).addTo(map);
                    
                    // Añadir marcador en el punto objetivo
                    if (nextWaypointMarker) {
                        map.removeLayer(nextWaypointMarker);
                    }
                    
                    nextWaypointMarker = L.circleMarker(targetPoint, {
                        radius: 8,
                        fillColor: "#e74c3c",
                        color: "#fff",
                        weight: 3,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map).bindPopup("🎯 Punto de retorno a la ruta");
                    
                    // Actualizar instrucciones
                    if (route.legs && route.legs[0] && route.legs[0].steps && route.legs[0].steps.length > 0) {
                        var firstStep = route.legs[0].steps[0];
                        var instruction = firstStep.maneuver ? firstStep.maneuver.instruction : '';
                        var distance = route.distance;
                        var duration = route.duration;
                        
                        document.getElementById('current-direction').innerHTML = 
                            '🔙 <strong>Vuelta a ruta:</strong> ' + 
                            (instruction || 'Dirígete al punto marcado') + 
                            '<br><small>' + formatDistance(distance) + ' • ' + 
                            Math.round(duration / 60) + ' min</small>';
                    } else {
                        showFallbackDirection(targetPoint);
                    }
                }

                // Mostrar dirección simple si falla el routing
                function showFallbackDirection(targetPoint) {
                    returnRouteCalculated = true;
                    
                    // Calcular dirección simple
                    var dx = targetPoint.lng - currentPosition.lng;
                    var dy = targetPoint.lat - currentPosition.lat;
                    var bearing = Math.atan2(dx, dy) * 180 / Math.PI;
                    if (bearing < 0) bearing += 360;
                    
                    var cardinals = ['Norte ⬆️', 'Noreste ↗️', 'Este ➡️', 'Sureste ↘️', 
                                   'Sur ⬇️', 'Suroeste ↙️', 'Oeste ⬅️', 'Noroeste ↖️'];
                    var index = Math.round(bearing / 45) % 8;
                    var distance = currentPosition.distanceTo(targetPoint);
                    
                    document.getElementById('current-direction').innerHTML = 
                        '🔙 <strong>Vuelta a ruta:</strong> Dirígete al ' + cardinals[index] + 
                        '<br><small>' + formatDistance(distance) + '</small>';
                    
                    // Crear línea simple
                    if (returnToRoutePolyline) {
                        map.removeLayer(returnToRoutePolyline);
                    }
                    
                    returnToRoutePolyline = L.polyline([currentPosition, targetPoint], {
                        color: '#e74c3c',
                        weight: 4,
                        opacity: 0.7,
                        dashArray: '10, 5'
                    }).addTo(map);
                }

                // Limpiar ruta de vuelta
                function clearReturnToRoute() {
                    if (returnToRoutePolyline) {
                        map.removeLayer(returnToRoutePolyline);
                        returnToRoutePolyline = null;
                    }
                }

                function updateNavigationLine() {
                    if (!currentPosition || closestPointIndex < 0 || routePoints.length === 0) return;
                    
                    // No mostrar línea de navegación normal si está fuera de ruta y se está calculando/mostrando ruta de vuelta
                    if (isOffRoute && returnRouteCalculated) {
                        // Si hay línea de navegación normal, removerla
                        if (navigationLine) {
                            map.removeLayer(navigationLine);
                            navigationLine = null;
                        }
                        return;
                    }
                    
                    // Remover línea anterior si existe
                    if (navigationLine) {
                        map.removeLayer(navigationLine);
                    }
                    
                    if (nextWaypointMarker && !isOffRoute) {
                        map.removeLayer(nextWaypointMarker);
                    }
                    
                    // Encontrar los próximos 5-10 puntos para crear la línea de navegación
                    var nextPoints = [];
                    var startIndex = Math.max(0, closestPointIndex - 2);
                    var endIndex = Math.min(routePoints.length - 1, closestPointIndex + 15);
                    
                    // Añadir posición actual como primer punto
                    nextPoints.push(currentPosition);
                    
                    // Añadir los próximos puntos de la ruta
                    for (var i = startIndex; i <= endIndex; i++) {
                        nextPoints.push(routePoints[i]);
                    }
                    
                    // Crear línea de navegación más prominente
                    if (nextPoints.length > 1 && !isOffRoute) {
                        navigationLine = L.polyline(nextPoints, {
                            color: '#ff6b35',
                            weight: 6,
                            opacity: 0.9,
                            dashArray: '10, 5',
                            className: 'navigation-line'
                        }).addTo(map);
                        
                        // Añadir marcador del próximo waypoint importante
                        if (endIndex < routePoints.length - 1) {
                            nextWaypointMarker = L.circleMarker(routePoints[endIndex], {
                                radius: 6,
                                fillColor: "#ff6b35",
                                color: "#fff",
                                weight: 2,
                                opacity: 1,
                                fillOpacity: 0.8
                            }).addTo(map).bindPopup("Próximo waypoint");
                        }
                    }
                }

                function calculateDirectionGuidance(currentIndex) {
                    if (currentIndex < 0 || currentIndex >= routePoints.length - 1) {
                        document.getElementById('current-direction').textContent = 'Final de la ruta';
                        return;
                    }
                    
                    // Buscar el próximo punto significativo (al menos a 20m)
                    var nextIndex = currentIndex;
                    var cumulativeDistance = 0;
                    
                    while (nextIndex < routePoints.length - 1 && cumulativeDistance < 20) {
                        nextIndex++;
                        cumulativeDistance += routePoints[nextIndex-1].distanceTo(routePoints[nextIndex]);
                    }
                    
                    if (nextIndex > currentIndex) {
                        var nextPoint = routePoints[nextIndex];
                        
                        // Calcular ángulo hacia el siguiente punto
                        var dx = nextPoint.lng - currentPosition.lng;
                        var dy = nextPoint.lat - currentPosition.lat;
                        var targetBearing = Math.atan2(dx, dy) * 180 / Math.PI;
                        if (targetBearing < 0) targetBearing += 360;
                        
                        var instruction = '';
                        var distanceToNext = currentPosition.distanceTo(nextPoint);
                        
                        if (userHeading !== null && userHeading !== undefined && userHeading >= 0) {
                            // Calcular diferencia entre dirección actual y objetivo
                            var diff = targetBearing - userHeading;
                            if (diff < -180) diff += 360;
                            if (diff > 180) diff -= 360;
                            
                            // Instrucciones más específicas
                            if (Math.abs(diff) < 15) {
                                instruction = '⬆️ Continúa recto';
                            } else if (diff >= 15 && diff < 45) {
                                instruction = '↗️ Gira ligeramente a la derecha';
                            } else if (diff >= 45 && diff < 90) {
                                instruction = '➡️ Gira a la derecha';
                            } else if (diff >= 90 && diff < 135) {
                                instruction = '↘️ Gira fuertemente a la derecha';
                            } else if (diff >= 135) {
                                instruction = '🔄 Gira completamente a la derecha';
                            } else if (diff <= -15 && diff > -45) {
                                instruction = '↖️ Gira ligeramente a la izquierda';
                            } else if (diff <= -45 && diff > -90) {
                                instruction = '⬅️ Gira a la izquierda';
                            } else if (diff <= -90 && diff > -135) {
                                instruction = '↙️ Gira fuertemente a la izquierda';
                            } else if (diff <= -135) {
                                instruction = '🔄 Gira completamente a la izquierda';
                            }
                            
                            // Añadir distancia al próximo punto
                            if (distanceToNext > 100) {
                                instruction += ' (' + formatDistance(distanceToNext) + ')';
                            }
                        } else {
                            // Si no tenemos rumbo del usuario, mostrar cardinal
                            var cardinals = ['Norte ⬆️', 'Noreste ↗️', 'Este ➡️', 'Sureste ↘️', 'Sur ⬇️', 'Suroeste ↙️', 'Oeste ⬅️', 'Noroeste ↖️'];
                            var index = Math.round(targetBearing / 45) % 8;
                            instruction = 'Dirígete al ' + cardinals[index];
                            
                            if (distanceToNext > 100) {
                                instruction += ' (' + formatDistance(distanceToNext) + ')';
                            }
                        }
                        
                        document.getElementById('current-direction').textContent = instruction;
                    } else {
                        document.getElementById('current-direction').textContent = '🏁 Llegando al destino';
                    }
                }

                function centerOnUser() {
                    if (currentPosition) {
                        map.setView(currentPosition, 16);
                    }
                }

                function stopNavigation() {
                    if (!navigationActive) return;
                    
                    if (watchPositionId !== null) {
                        navigator.geolocation.clearWatch(watchPositionId);
                        watchPositionId = null;
                    }
                    
                    if (userPositionMarker) {
                        map.removeLayer(userPositionMarker);
                        userPositionMarker = null;
                    }
                    
                    if (userAccuracyCircle) {
                        map.removeLayer(userAccuracyCircle);
                        userAccuracyCircle = null;
                    }
                    
                    if (navigationLine) {
                        map.removeLayer(navigationLine);
                        navigationLine = null;
                    }
                    
                    if (nextWaypointMarker) {
                        map.removeLayer(nextWaypointMarker);
                        nextWaypointMarker = null;
                    }
                    
                    // Limpiar ruta de vuelta
                    clearReturnToRoute();
                    
                    navigationActive = false;
                    currentPosition = null;
                    closestPointIndex = -1;
                    isOffRoute = false;
                    returnRouteCalculated = false;
                    offRouteStartTime = null;
                    
                    document.getElementById('nav-panel').style.display = 'none';
                    document.getElementById('start-navigation').disabled = false;
                    document.getElementById('start-navigation').innerHTML = '<i class="fas fa-location-arrow"></i> Iniciar GPS';
                }

                function handleLocationError(error) {
                    var errorMsg = '';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = 'Usuario denegó la solicitud de geolocalización.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = 'La información de ubicación no está disponible.';
                            break;
                        case error.TIMEOUT:
                            errorMsg = 'Se agotó el tiempo de espera para la solicitud de ubicación.';
                            break;
                        default:
                            errorMsg = 'Ocurrió un error desconocido.';
                            break;
                    }
                    
                    alert('Error de GPS: ' + errorMsg);
                    stopNavigation();
                }

                // Limpiar al salir de la página
                window.addEventListener('beforeunload', function() {
                    if (navigationActive) {
                        stopNavigation();
                    }
                });
                </script>
            <?php else: ?>
                <!-- Modo carga de GPX -->
                <h2 class="mb-4 text-center">
                    <i class="fas fa-route"></i> Visor de Rutas GPX
                </h2>
                
                <div class="privacy-notice">
                    <i class="fas fa-shield-alt"></i> <strong>Privacidad total:</strong> Los archivos GPX se procesan temporalmente y no se guardan en el servidor.
                </div>
                
                <?php if($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= $errorMsg ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-upload"></i> Cargar archivo GPX</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data" id="gpxForm">
                            <div class="file-upload-area" id="fileUploadArea">
                                <i class="fas fa-file-upload fa-3x text-primary mb-3"></i>
                                <h4>Arrastra tu archivo GPX aquí o haz clic para seleccionar</h4>
                                <p class="text-muted">Procesamiento temporal - No se guarda en el servidor</p>
                                <input type="file" name="gpxFile" id="gpxFile" accept=".gpx" class="d-none">
                            </div>
                            
                            <div class="file-preview" id="filePreview">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-alt fa-2x text-primary mr-3"></i>
                                    <div>
                                        <h6 id="fileName" class="mb-1">archivo.gpx</h6>
                                        <p class="text-muted mb-0" id="fileSize">0 KB</p>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger ml-auto" onclick="resetFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" name="convert" class="btn btn-primary btn-lg action-button">
                                    <i class="fas fa-map-marked-alt"></i> Visualizar en el mapa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> ¿Qué puedes hacer con este visor?</h5>
                        <ul class="mb-0">
                            <li><strong>Cargar cualquier archivo GPX</strong> y visualizarlo en un mapa interactivo</li>
                            <li><strong>Ver estadísticas de la ruta</strong> (distancia, puntos)</li>
                            <li><strong>Usar navegación GPS en tiempo real</strong> con indicaciones</li>
                            <li><strong>Alternar entre diferentes vistas</strong> de mapa (normal, terreno, satélite)</li>
                            <li><strong>Privacidad total</strong> - los archivos no se guardan en el servidor</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Enlaces de navegación -->
              <!--   <div class="text-center mt-4">
                    <a href="siluetas_gpx.php" class="btn btn-info mr-2">
                        <i class="fas fa-draw-polygon"></i> Generar Silueta
                    </a> 
                    <a href="nueva_ruta.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Gestión de Rutas
                    </a>
                </div>
                -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const fileUploadArea = document.getElementById('fileUploadArea');
                        const gpxFile = document.getElementById('gpxFile');
                        const filePreview = document.getElementById('filePreview');
                        const fileName = document.getElementById('fileName');
                        const fileSize = document.getElementById('fileSize');
                        
                        fileUploadArea.addEventListener('click', function() {
                            gpxFile.click();
                        });
                        
                        fileUploadArea.addEventListener('dragover', function(e) {
                            e.preventDefault();
                            fileUploadArea.style.backgroundColor = '#f8f9fa';
                            fileUploadArea.style.borderColor = '#0056b3';
                        });
                        
                        fileUploadArea.addEventListener('dragleave', function(e) {
                            e.preventDefault();
                            fileUploadArea.style.backgroundColor = '';
                            fileUploadArea.style.borderColor = '#007bff';
                        });
                        
                        fileUploadArea.addEventListener('drop', function(e) {
                            e.preventDefault();
                            fileUploadArea.style.backgroundColor = '';
                            fileUploadArea.style.borderColor = '#007bff';
                            
                            if (e.dataTransfer.files.length > 0) {
                                gpxFile.files = e.dataTransfer.files;
                                updateFilePreview();
                            }
                        });
                        
                        gpxFile.addEventListener('change', updateFilePreview);
                        
                        function updateFilePreview() {
                            if (gpxFile.files.length > 0) {
                                const file = gpxFile.files[0];
                                fileName.textContent = file.name;
                                fileSize.textContent = formatBytes(file.size);
                                filePreview.style.display = 'block';
                                fileUploadArea.style.display = 'none';
                            }
                        }
                        
                        function formatBytes(bytes, decimals = 2) {
                            if (bytes === 0) return '0 Bytes';
                            
                            const k = 1024;
                            const dm = decimals < 0 ? 0 : decimals;
                            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                            
                            const i = Math.floor(Math.log(bytes) / Math.log(k));
                            
                            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
                        }
                    });
                    
                    function resetFile() {
                        document.getElementById('gpxFile').value = '';
                        document.getElementById('filePreview').style.display = 'none';
                        document.getElementById('fileUploadArea').style.display = 'block';
                    }
                </script>
            <?php endif; ?>
        </div>
    </div>
    
    <?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>