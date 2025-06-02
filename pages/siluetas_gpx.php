<?php
/**
 * Generador de Siluetas de Rutas GPX - Basado en gpx_viewer.php
 * Usa el mismo procesamiento que funciona + genera siluetas para descargar
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
$gpxDistance = 0;
$routeData = null;

// Función para sanitizar nombres de archivo para URL
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9\-\_]/', '', str_replace(' ', '-', $filename));
    return strtolower($filename);
}

// Función para normalizar puntos y crear silueta (NUEVA)
function normalizePointsForSilhouette($allCoords) {
    if (empty($allCoords)) return [];
    
    // Encontrar límites
    $minLat = $maxLat = $allCoords[0][0];
    $minLng = $maxLng = $allCoords[0][1];
    
    foreach ($allCoords as $coord) {
        $minLat = min($minLat, $coord[0]);
        $maxLat = max($maxLat, $coord[0]);
        $minLng = min($minLng, $coord[1]);
        $maxLng = max($maxLng, $coord[1]);
    }
    
    // Calcular centro y rango
    $centerLat = ($minLat + $maxLat) / 2;
    $centerLng = ($minLng + $maxLng) / 2;
    $rangeLat = $maxLat - $minLat;
    $rangeLng = $maxLng - $minLng;
    
    // Normalizar a rango -1 a 1 manteniendo proporciones
    $maxRange = max($rangeLat, $rangeLng);
    if ($maxRange == 0) $maxRange = 1; // Evitar división por cero
    
    $normalizedPoints = [];
    foreach ($allCoords as $coord) {
        $normalizedPoints[] = [
            'x' => ($coord[1] - $centerLng) / $maxRange, // lng -> x
            'y' => ($coord[0] - $centerLat) / $maxRange  // lat -> y
        ];
    }
    
    return $normalizedPoints;
}

// Procesar archivo GPX cuando se envía el formulario (IGUAL QUE ORIGINAL)
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
            // Leer archivo GPX para extraer metadatos (IGUAL QUE ORIGINAL)
            try {
                $gpxContent = file_get_contents($tmpName);
                $xml = new SimpleXMLElement($gpxContent);
                
                // Extraer nombre y descripción si están disponibles
                $gpxName = isset($xml->metadata->name) ? (string)$xml->metadata->name : pathinfo($originalFileName, PATHINFO_FILENAME);
                $gpxDesc = isset($xml->metadata->desc) ? (string)$xml->metadata->desc : '';
                
                // Contar puntos (aproximado) - CORREGIDO PARA NAMESPACES
                $trackPoints = $xml->xpath('//*[local-name()="trkpt"]');
                $routePoints = $xml->xpath('//*[local-name()="rtept"]');
                $wayPoints = $xml->xpath('//*[local-name()="wpt"]');
                
                $gpxPoints = count($trackPoints) + count($routePoints) + count($wayPoints);
                
                // NUEVA PARTE: Extraer coordenadas para silueta - CORREGIDO PARA NAMESPACES
                $allCoords = [];
                
                // Usar xpath que funciona con namespaces
                $trackPointsXml = $xml->xpath('//*[local-name()="trkpt"]');
                foreach ($trackPointsXml as $point) {
                    $lat = (float)$point['lat'];
                    $lng = (float)$point['lon'];
                    if ($lat != 0 || $lng != 0) { // Validar que no sean coordenadas vacías
                        $allCoords[] = [$lat, $lng];
                    }
                }
                
                // Si no hay track points, buscar route points
                if (empty($allCoords)) {
                    $routePointsXml = $xml->xpath('//*[local-name()="rtept"]');
                    foreach ($routePointsXml as $point) {
                        $lat = (float)$point['lat'];
                        $lng = (float)$point['lon'];
                        if ($lat != 0 || $lng != 0) {
                            $allCoords[] = [$lat, $lng];
                        }
                    }
                }
                
                // Si tampoco hay route points, usar waypoints
                if (empty($allCoords)) {
                    $wayPointsXml = $xml->xpath('//*[local-name()="wpt"]');
                    foreach ($wayPointsXml as $point) {
                        $lat = (float)$point['lat'];
                        $lng = (float)$point['lon'];
                        if ($lat != 0 || $lng != 0) {
                            $allCoords[] = [$lat, $lng];
                        }
                    }
                }
                
                // Calcular distancia (igual que original pero simplificado)
                $totalDistance = 0;
                for ($i = 1; $i < count($allCoords); $i++) {
                    $lat1 = $allCoords[$i-1][0];
                    $lng1 = $allCoords[$i-1][1];
                    $lat2 = $allCoords[$i][0];
                    $lng2 = $allCoords[$i][1];
                    
                    // Fórmula de Haversine simple
                    $R = 6371000;
                    $dLat = deg2rad($lat2 - $lat1);
                    $dLng = deg2rad($lng2 - $lng1);
                    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
                    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                    $totalDistance += $R * $c;
                }
                $gpxDistance = $totalDistance;
                
                // Normalizar puntos para silueta
                $routeData = normalizePointsForSilhouette($allCoords);
                
                if (!empty($routeData)) {
                    $success = true;
                    $successMsg = 'Archivo GPX procesado exitosamente - ' . count($allCoords) . ' puntos encontrados';
                } else {
                    $error = true;
                    $errorMsg = 'No se pudieron extraer puntos de la ruta del archivo GPX';
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

// Función para formatear distancia
function formatDistance($meters) {
    if ($meters < 1000) {
        return round($meters) . ' m';
    } else {
        return number_format($meters / 1000, 1) . ' km';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Siluetas de Rutas GPX</title>
    <!-- Bootstrap y FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .container-lg {
            max-width: 1200px;
        }
        .silhouette-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 25px;
            margin-bottom: 30px;
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
        .silhouette-canvas {
            width: 100%;
            height: 500px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            margin: 20px 0;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .map-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            filter: blur(2px) brightness(0.6) saturate(0.8) contrast(1.1);
            opacity: 0.9;
            z-index: 1;
            transition: all 0.3s ease;
            display: none;
            overflow: hidden;
        }
        .silhouette-canvas::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255,255,255,0.08) 0%, transparent 50%);
            filter: blur(1px);
            z-index: 1;
        }
        #routeSvg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 3;
            transition: transform 0.3s ease;
        }
        .zoom-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 4;
            background: rgba(255,255,255,0.9);
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .zoom-btn {
            display: block;
            width: 35px;
            height: 35px;
            margin: 5px 0;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 4px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        .zoom-btn:hover {
            background: #0056b3;
            transform: scale(1.1);
        }
        .zoom-info {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin: 5px 0;
        }
        .route-path {
            fill: none;
            stroke: #ffffff;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        .route-start {
            fill: #28a745;
            stroke: #ffffff;
            stroke-width: 2;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        .route-end {
            fill: #dc3545;
            stroke: #ffffff;
            stroke-width: 2;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        .download-section {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .style-options {
            margin: 20px 0;
        }
        .style-btn {
            margin: 5px;
            padding: 8px 15px;
            border: 2px solid #007bff;
            background: transparent;
            color: #007bff;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .style-btn.active,
        .style-btn:hover {
            background: #007bff;
            color: white;
        }
        .privacy-info {
            background-color: #e8f5e8;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container-lg py-4">
        <div class="silhouette-container">
            <h2 class="mb-4 text-center">
                <i class="fas fa-draw-polygon"></i> Generador de Siluetas de Rutas
            </h2>
            
            <div class="privacy-info">
                <h5><i class="fas fa-user-shield"></i> Privacidad Total</h5>
                <p class="mb-0">Esta herramienta genera solo la <strong>forma</strong> de tu ruta sin mostrar la ubicación geográfica real. Perfecta para compartir el perfil de tus aventuras manteniendo la privacidad de los lugares visitados.</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $errorMsg ?>
                </div>
            <?php endif; ?>
            
            <?php if($success && $routeData): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $successMsg ?>
                </div>
                
                <!-- Estadísticas de la ruta -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="stats-panel">
                            <h5><i class="fas fa-chart-line"></i> Estadísticas de la ruta</h5>
                            <div class="stat-item">
                                <span>Nombre:</span>
                                <strong><?= htmlspecialchars($gpxName) ?></strong>
                            </div>
                            <div class="stat-item">
                                <span>Distancia total:</span>
                                <strong><?= formatDistance($gpxDistance) ?></strong>
                            </div>
                            <div class="stat-item">
                                <span>Puntos registrados:</span>
                                <strong><?= number_format($gpxPoints) ?></strong>
                            </div>
                            <?php if(!empty($gpxDesc)): ?>
                                <div class="stat-item">
                                    <span>Descripción:</span>
                                    <strong><?= htmlspecialchars($gpxDesc) ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-palette"></i> Personalizar Estilo</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">Elige el estilo de tu silueta:</p>
                                <div class="style-options">
                                    <button class="style-btn active" onclick="changeStyle('gradient')">
                                        <i class="fas fa-mountain"></i> Gradiente
                                    </button>
                                    <button class="style-btn" onclick="changeStyle('dark')">
                                        <i class="fas fa-moon"></i> Oscuro
                                    </button>
                                    <button class="style-btn" onclick="changeStyle('minimal')">
                                        <i class="fas fa-minus"></i> Minimal
                                    </button>
                                    <button class="style-btn" onclick="changeStyle('neon')">
                                        <i class="fas fa-bolt"></i> Neón
                                    </button>
                                    <button class="style-btn" onclick="changeStyle('map')">
                                        <i class="fas fa-map"></i> Mapa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Canvas de la silueta -->
                <div class="silhouette-canvas" id="silhouetteCanvas">
                    <div class="map-background" id="mapBackground"></div>
                    <svg id="routeSvg" viewBox="0 0 800 500" preserveAspectRatio="xMidYMid meet">
                        <!-- El path se generará con JavaScript -->
                    </svg>
                    <div class="zoom-controls">
                        <button class="zoom-btn" id="zoomIn" title="Acercar">+</button>
                        <div class="zoom-info" id="zoomLevel">100%</div>
                        <button class="zoom-btn" id="zoomOut" title="Alejar">−</button>
                        <button class="zoom-btn" id="zoomReset" title="Reset" style="font-size: 14px; height: 30px; margin-top: 10px;">🔄</button>
                    </div>
                </div>
                
                <!-- Sección de descarga -->
                <div class="download-section">
                    <h5><i class="fas fa-download"></i> Descargar tu silueta</h5>
                    <p class="text-muted">Guarda tu silueta de ruta para compartir en redes sociales o usar como wallpaper</p>
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary" onclick="downloadSVG()">
                            <i class="fas fa-file-code"></i> Descargar SVG
                        </button>
                        <button class="btn btn-success" onclick="downloadPNG()">
                            <i class="fas fa-file-image"></i> Descargar PNG
                        </button>
                    </div>
                </div>
                
                <script>
                    // Datos de la ruta desde PHP (normalizados)
                    const routePoints = <?= json_encode($routeData) ?>;
                    
                    // Variables de zoom y mapa
                    let currentZoom = 1;
                    let minZoom = 0.5;
                    let maxZoom = 5;
                    
                    // Coordenadas reales para el mapa de fondo (primeros valores para centrar)
                    const realCoords = <?= json_encode($allCoords) ?>;
                    
                    // Configuración de estilos (actualizada con mapas)
                    const styles = {
                        gradient: {
                            background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                            pathColor: '#ffffff',
                            startColor: '#28a745',
                            endColor: '#dc3545',
                            useMap: false
                        },
                        dark: {
                            background: 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)',
                            pathColor: '#ecf0f1',
                            startColor: '#27ae60',
                            endColor: '#e74c3c',
                            useMap: false
                        },
                        minimal: {
                            background: 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)',
                            pathColor: '#495057',
                            startColor: '#28a745',
                            endColor: '#dc3545',
                            useMap: false
                        },
                        neon: {
                            background: 'linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%)',
                            pathColor: '#00ffff',
                            startColor: '#ff00ff',
                            endColor: '#ffff00',
                            useMap: false
                        },
                        map: {
                            background: 'transparent',
                            pathColor: '#1e3a8a',
                            startColor: '#00ff00',
                            endColor: '#ff0000',
                            useMap: true
                        }
                    };
                    
                    let currentStyle = 'gradient';
                    const currentStrokeWidth = 1; // Grosor fijo de 1px
                    
                    // Generar la silueta al cargar la página
                    document.addEventListener('DOMContentLoaded', function() {
                        setupZoomControls();
                        generateSilhouette();
                    });
                    
                    function setupZoomControls() {
                        const zoomInBtn = document.getElementById('zoomIn');
                        const zoomOutBtn = document.getElementById('zoomOut');
                        const zoomResetBtn = document.getElementById('zoomReset');
                        
                        if (zoomInBtn) {
                            zoomInBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                if (currentZoom < maxZoom) {
                                    currentZoom += 0.25;
                                    updateZoom();
                                }
                            });
                        }
                        
                        if (zoomOutBtn) {
                            zoomOutBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                if (currentZoom > minZoom) {
                                    currentZoom -= 0.25;
                                    updateZoom();
                                }
                            });
                        }
                        
                        if (zoomResetBtn) {
                            zoomResetBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                currentZoom = 1;
                                updateZoom();
                            });
                        }
                        
                        // Inicializar el display del zoom
                        updateZoom();
                    }
                    
                    function updateZoom() {
                        const svg = document.getElementById('routeSvg');
                        const mapBackground = document.getElementById('mapBackground');
                        const zoomLevel = document.getElementById('zoomLevel');
                        
                        if (svg) {
                            svg.style.transform = `scale(${currentZoom})`;
                            svg.style.transformOrigin = 'center center';
                        }
                        
                        if (mapBackground) {
                            mapBackground.style.transform = `scale(${currentZoom})`;
                            mapBackground.style.transformOrigin = 'center center';
                        }
                        
                        if (zoomLevel) {
                            zoomLevel.textContent = Math.round(currentZoom * 100) + '%';
                        }
                        
                        // Actualizar estado de los botones
                        const zoomInBtn = document.getElementById('zoomIn');
                        const zoomOutBtn = document.getElementById('zoomOut');
                        
                        if (zoomInBtn) {
                            zoomInBtn.style.opacity = currentZoom >= maxZoom ? '0.5' : '1';
                            zoomInBtn.disabled = currentZoom >= maxZoom;
                        }
                        
                        if (zoomOutBtn) {
                            zoomOutBtn.style.opacity = currentZoom <= minZoom ? '0.5' : '1';
                            zoomOutBtn.disabled = currentZoom <= minZoom;
                        }
                    }
                    
                    function loadMapBackground() {
                        if (!realCoords || realCoords.length === 0) return;
                        
                        // Calcular centro para el mapa
                        const lats = realCoords.map(coord => coord[0]);
                        const lngs = realCoords.map(coord => coord[1]);
                        const centerLat = (Math.min(...lats) + Math.max(...lats)) / 2;
                        const centerLng = (Math.min(...lngs) + Math.max(...lngs)) / 2;
                        
                        // Calcular zoom apropiado basado en el área
                        const latRange = Math.max(...lats) - Math.min(...lats);
                        const lngRange = Math.max(...lngs) - Math.min(...lngs);
                        const maxRange = Math.max(latRange, lngRange);
                        
                        let mapZoom = 12;
                        if (maxRange > 0.5) mapZoom = 9;
                        else if (maxRange > 0.1) mapZoom = 11;
                        else if (maxRange > 0.01) mapZoom = 13;
                        else mapZoom = 15;
                        
                        // Calcular tile central
                        const tileX = Math.floor((centerLng + 180) / 360 * Math.pow(2, mapZoom));
                        const tileY = Math.floor((1 - Math.log(Math.tan(centerLat * Math.PI / 180) + 1 / Math.cos(centerLat * Math.PI / 180)) / Math.PI) / 2 * Math.pow(2, mapZoom));
                        
                        const mapBackground = document.getElementById('mapBackground');
                        mapBackground.innerHTML = ''; // Limpiar contenido anterior
                        
                        // Usar más tiles para cubrir completamente (5x3 = 1280x768px)
                        const tilesHorizontal = 5;
                        const tilesVertical = 3;
                        const tileSize = 256;
                        
                        // Centrar el mosaico en el contenedor de 800x500
                        const startX = -((tilesHorizontal * tileSize - 800) / 2);
                        const startY = -((tilesVertical * tileSize - 500) / 2);
                        
                        console.log('Creando mosaico:', tilesHorizontal, 'x', tilesVertical, 'start:', startX, startY);
                        
                        for (let dx = 0; dx < tilesHorizontal; dx++) {
                            for (let dy = 0; dy < tilesVertical; dy++) {
                                const img = document.createElement('img');
                                const currentTileX = tileX + dx - Math.floor(tilesHorizontal/2);
                                const currentTileY = tileY + dy - Math.floor(tilesVertical/2);
                                
                                img.src = `https://tile.openstreetmap.org/${mapZoom}/${currentTileX}/${currentTileY}.png`;
                                img.style.position = 'absolute';
                                img.style.left = (startX + dx * tileSize) + 'px';
                                img.style.top = (startY + dy * tileSize) + 'px';
                                img.style.width = tileSize + 'px';
                                img.style.height = tileSize + 'px';
                                img.style.pointerEvents = 'none';
                                
                                img.onload = function() {
                                    console.log('Tile cargado:', dx, dy);
                                };
                                
                                img.onerror = function() {
                                    console.log('Error cargando tile:', dx, dy, this.src);
                                    // Si falla cargar el tile, usar color sólido
                                    this.style.backgroundColor = '#e8e8e8';
                                    this.style.opacity = '0.6';
                                    this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgZmlsbD0iI2Y0ZjRmNCIvPjwvc3ZnPg==';
                                };
                                
                                mapBackground.appendChild(img);
                            }
                        }
                        
                        mapBackground.style.display = 'block';
                        console.log('Mapa cargado con zoom:', mapZoom, 'centro:', centerLat, centerLng);
                    }
                    
                    function generateSilhouette() {
                        if (!routePoints || routePoints.length === 0) return;
                        
                        const svg = document.getElementById('routeSvg');
                        const width = 800;
                        const height = 500;
                        const padding = 50;
                        
                        // Limpiar SVG
                        svg.innerHTML = '';
                        
                        // Convertir puntos normalizados a coordenadas SVG
                        const svgPoints = routePoints.map(point => ({
                            x: (point.x + 1) / 2 * (width - 2 * padding) + padding,
                            y: (1 - point.y) / 2 * (height - 2 * padding) + padding // Invertir Y
                        }));
                        
                        // Crear path de la ruta
                        let pathData = `M ${svgPoints[0].x} ${svgPoints[0].y}`;
                        for (let i = 1; i < svgPoints.length; i++) {
                            pathData += ` L ${svgPoints[i].x} ${svgPoints[i].y}`;
                        }
                        
                        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        path.setAttribute('d', pathData);
                        path.classList.add('route-path');
                        path.style.stroke = styles[currentStyle].pathColor;
                        path.style.strokeWidth = '1px'; // Grosor fijo de 1px
                        
                        // Añadir sombra para mejor contraste en el mapa
                        if (currentStyle === 'map') {
                            path.style.filter = 'drop-shadow(0 0 3px rgba(0,0,0,0.9)) drop-shadow(0 0 6px rgba(255,255,255,0.8))';
                        } else {
                            path.style.filter = 'drop-shadow(0 2px 4px rgba(0,0,0,0.3))';
                        }
                        
                        svg.appendChild(path);
                        
                        // Marcador de inicio
                        const startCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                        startCircle.setAttribute('cx', svgPoints[0].x);
                        startCircle.setAttribute('cy', svgPoints[0].y);
                        startCircle.setAttribute('r', currentStyle === 'map' ? 6 : 5);
                        startCircle.classList.add('route-start');
                        startCircle.style.fill = styles[currentStyle].startColor;
                        startCircle.style.strokeWidth = '1px';
                        if (currentStyle === 'map') {
                            startCircle.style.filter = 'drop-shadow(0 0 3px rgba(0,0,0,0.9)) drop-shadow(0 0 6px rgba(255,255,255,0.6))';
                        }
                        svg.appendChild(startCircle);
                        
                        // Marcador de fin
                        const endCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                        endCircle.setAttribute('cx', svgPoints[svgPoints.length - 1].x);
                        endCircle.setAttribute('cy', svgPoints[svgPoints.length - 1].y);
                        endCircle.setAttribute('r', currentStyle === 'map' ? 6 : 5);
                        endCircle.classList.add('route-end');
                        endCircle.style.fill = styles[currentStyle].endColor;
                        endCircle.style.strokeWidth = '1px';
                        if (currentStyle === 'map') {
                            endCircle.style.filter = 'drop-shadow(0 0 3px rgba(0,0,0,0.9)) drop-shadow(0 0 6px rgba(255,255,255,0.6))';
                        }
                        svg.appendChild(endCircle);
                    }
                    
                    function changeStyle(styleName) {
                        currentStyle = styleName;
                        
                        // Actualizar botones activos
                        document.querySelectorAll('.style-btn').forEach(btn => {
                            btn.classList.remove('active');
                        });
                        event.target.classList.add('active');
                        
                        // Actualizar fondo
                        const canvas = document.getElementById('silhouetteCanvas');
                        const mapBackground = document.getElementById('mapBackground');
                        
                        if (styles[styleName].useMap && realCoords && realCoords.length > 0) {
                            // Estilo mapa: mostrar fondo de mapa difuminado
                            canvas.style.background = 'transparent';
                            mapBackground.style.display = 'block';
                            
                            // Cargar el mapa si no está ya cargado
                            if (!mapBackground.hasChildNodes()) {
                                loadMapBackground();
                            }
                        } else {
                            // Otros estilos: usar gradientes
                            canvas.style.background = styles[styleName].background;
                            mapBackground.style.display = 'none';
                        }
                        
                        // Regenerar silueta
                        generateSilhouette();
                    }
                    
                    function updateStrokeWidth(width) {
                        // Convertir a número para asegurar que es numérico
                        currentStrokeWidth = parseInt(width);
                        
                        // Actualizar el indicador visual
                        const strokeValue = document.getElementById('strokeValue');
                        if (strokeValue) {
                            strokeValue.textContent = currentStrokeWidth + 'px';
                        }
                        
                        // Asegurar que el slider mantenga el valor correcto
                        const slider = document.getElementById('strokeWidth');
                        if (slider && slider.value != currentStrokeWidth) {
                            slider.value = currentStrokeWidth;
                        }
                        
                        // Regenerar silueta con el nuevo grosor
                        generateSilhouette();
                        
                        console.log('Grosor actualizado a:', currentStrokeWidth);
                    }
                    
                    function downloadSVG() {
                        const svg = document.getElementById('routeSvg');
                        
                        // Clonar SVG y añadir fondo
                        const svgClone = svg.cloneNode(true);
                        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect.setAttribute('width', '100%');
                        rect.setAttribute('height', '100%');
                        rect.setAttribute('fill', 'url(#bg)');
                        
                        // Crear gradiente de fondo
                        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                        const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
                        gradient.setAttribute('id', 'bg');
                        gradient.setAttribute('x1', '0%');
                        gradient.setAttribute('y1', '0%');
                        gradient.setAttribute('x2', '100%');
                        gradient.setAttribute('y2', '100%');
                        
                        const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
                        stop1.setAttribute('offset', '0%');
                        stop1.setAttribute('stop-color', '#667eea');
                        const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
                        stop2.setAttribute('offset', '100%');
                        stop2.setAttribute('stop-color', '#764ba2');
                        
                        gradient.appendChild(stop1);
                        gradient.appendChild(stop2);
                        defs.appendChild(gradient);
                        svgClone.insertBefore(defs, svgClone.firstChild);
                        svgClone.insertBefore(rect, svgClone.firstChild.nextSibling);
                        
                        // Descargar
                        const serializer = new XMLSerializer();
                        const svgString = serializer.serializeToString(svgClone);
                        const blob = new Blob([svgString], {type: 'image/svg+xml'});
                        const url = URL.createObjectURL(blob);
                        
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'silueta-ruta.svg';
                        a.click();
                        URL.revokeObjectURL(url);
                    }
                    
                    function downloadPNG() {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        const baseWidth = 1600;
                        const baseHeight = 1000;
                        
                        // Aplicar zoom a las dimensiones del canvas
                        canvas.width = baseWidth * currentZoom;
                        canvas.height = baseHeight * currentZoom;
                        
                        // Si el estilo actual usa mapa, crear un fondo simulado
                        if (styles[currentStyle].useMap) {
                            // Crear un patrón que simule un mapa difuminado
                            const gradient = ctx.createRadialGradient(canvas.width/2, canvas.height/2, 0, canvas.width/2, canvas.height/2, Math.max(canvas.width, canvas.height)/2);
                            gradient.addColorStop(0, '#f0f0f0');
                            gradient.addColorStop(0.3, '#e0e0e0');
                            gradient.addColorStop(0.6, '#d0d0d0');
                            gradient.addColorStop(1, '#c0c0c0');
                            ctx.fillStyle = gradient;
                            ctx.fillRect(0, 0, canvas.width, canvas.height);
                            
                            // Añadir algunos elementos que simulen calles/características geográficas
                            ctx.globalAlpha = 0.1;
                            ctx.strokeStyle = '#888888';
                            ctx.lineWidth = 2 * currentZoom;
                            for (let i = 0; i < 10; i++) {
                                ctx.beginPath();
                                ctx.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
                                ctx.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
                                ctx.stroke();
                            }
                            ctx.globalAlpha = 1;
                            
                            // Aplicar filtro difuminado
                            ctx.filter = 'blur(6px) brightness(0.4)';
                            ctx.drawImage(canvas, 0, 0);
                            ctx.filter = 'none';
                        } else {
                            // Estilo sin mapa, usar gradiente correspondiente
                            drawGradientBackground(ctx, canvas.width, canvas.height);
                        }
                        
                        // Dibujar la ruta encima
                        drawRouteOnCanvas(ctx, canvas.width, canvas.height, canvas);
                    }
                    
                    function drawGradientBackground(ctx, width, height) {
                        const gradient = ctx.createLinearGradient(0, 0, width, height);
                        gradient.addColorStop(0, '#667eea');
                        gradient.addColorStop(1, '#764ba2');
                        ctx.fillStyle = gradient;
                        ctx.fillRect(0, 0, width, height);
                    }
                    
                    function drawRouteOnCanvas(ctx, width, height, canvas) {
                        if (routePoints && routePoints.length > 0) {
                            const padding = 100 * currentZoom;
                            const svgPoints = routePoints.map(point => ({
                                x: (point.x + 1) / 2 * (width - 2 * padding) + padding,
                                y: (1 - point.y) / 2 * (height - 2 * padding) + padding
                            }));
                            
                            // Dibujar path
                            ctx.strokeStyle = styles[currentStyle].pathColor;
                            ctx.lineWidth = 2 * currentZoom; // 1px base * 2 para alta resolución * zoom
                            ctx.lineCap = 'round';
                            ctx.lineJoin = 'round';
                            ctx.shadowBlur = 8 * currentZoom;
                            ctx.shadowColor = 'rgba(0,0,0,0.3)';
                            
                            ctx.beginPath();
                            ctx.moveTo(svgPoints[0].x, svgPoints[0].y);
                            for (let i = 1; i < svgPoints.length; i++) {
                                ctx.lineTo(svgPoints[i].x, svgPoints[i].y);
                            }
                            ctx.stroke();
                            
                            // Dibujar marcadores
                            ctx.shadowBlur = 4 * currentZoom;
                            const markerRadius = 10 * currentZoom; // Más pequeño para línea fina
                            
                            // Inicio
                            ctx.fillStyle = styles[currentStyle].startColor;
                            ctx.strokeStyle = '#ffffff';
                            ctx.lineWidth = 2 * currentZoom;
                            ctx.beginPath();
                            ctx.arc(svgPoints[0].x, svgPoints[0].y, markerRadius, 0, 2 * Math.PI);
                            ctx.fill();
                            ctx.stroke();
                            
                            // Fin
                            ctx.fillStyle = styles[currentStyle].endColor;
                            ctx.beginPath();
                            ctx.arc(svgPoints[svgPoints.length - 1].x, svgPoints[svgPoints.length - 1].y, markerRadius, 0, 2 * Math.PI);
                            ctx.fill();
                            ctx.stroke();
                        }
                        
                        // Descargar - ahora usando el parámetro canvas
                        canvas.toBlob(blob => {
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `silueta-ruta-${Math.round(currentZoom * 100)}%.png`;
                            a.click();
                            URL.revokeObjectURL(url);
                        });
                    }
                </script>
            <?php else: ?>
                <!-- Formulario de carga (IGUAL QUE ORIGINAL) -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-upload"></i> Cargar archivo GPX</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data" id="gpxForm">
                            <div class="file-upload-area" id="fileUploadArea">
                                <i class="fas fa-file-upload fa-3x text-primary mb-3"></i>
                                <h4>Arrastra tu archivo GPX aquí o haz clic para seleccionar</h4>
                                <p class="text-muted">Tu ubicación permanecerá completamente privada</p>
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
                                    <i class="fas fa-draw-polygon"></i> Generar Silueta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> ¿Qué hace esta herramienta?</h5>
                        <ul class="mb-0">
                            <li><strong>Extrae solo la forma</strong> de tu ruta sin mostrar ubicaciones reales</li>
                            <li><strong>Mantiene privacidad total</strong> - nadie sabrá dónde has estado</li>
                            <li><strong>Múltiples estilos</strong> - elige el que más te guste</li>
                            <li><strong>Descarga en alta calidad</strong> - SVG y PNG disponibles</li>
                            <li><strong>Perfecto para redes sociales</strong> - comparte tus aventuras de forma segura</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Navegación -->
            <div class="text-center mt-4">
                <a href="gpx_viewer.php" class="btn btn-secondary mr-2">
                    <i class="fas fa-map"></i> Visor GPX Completo
                </a>
                <a href="nueva_ruta.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Gestión de Rutas
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Funciones para el manejo de archivos (IGUAL QUE ORIGINAL)
        <?php if (!$routeData): ?>
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
        <?php endif; ?>
    </script>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>