<?php
/**
 * Conversor GPX Universal - Tyre/Garmin a TomTom y más
 * Soporta conversión entre rutas, tracks, waypoints y diferentes formatos
 * Autor: GPX Converter Tool
 * Versión: 2.0
 */

class GPXConverter {
    private $gpx;
    private $dom;
    private $xpath;
    
    public function __construct() {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
        $this->dom->preserveWhiteSpace = false;
    }
    
    public function loadGPX($content) {
        libxml_use_internal_errors(true);
        
        if (!$this->dom->loadXML($content)) {
            $errors = libxml_get_errors();
            throw new Exception('Error parsing GPX: ' . $errors[0]->message);
        }
        
        $this->xpath = new DOMXPath($this->dom);
        $this->xpath->registerNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
        
        return true;
    }
    
    public function convertRouteToTrack($simplify = false) {
        $routes = $this->xpath->query('//gpx:rte');
        
        foreach ($routes as $route) {
            // Crear nuevo track
            $track = $this->dom->createElement('trk');
            
            // Copiar nombre y descripción
            $routeName = $this->xpath->query('gpx:name', $route)->item(0);
            if ($routeName) {
                $trackName = $this->dom->createElement('name', htmlspecialchars($routeName->nodeValue));
                $track->appendChild($trackName);
            }
            
            $routeDesc = $this->xpath->query('gpx:desc', $route)->item(0);
            if ($routeDesc) {
                $trackDesc = $this->dom->createElement('desc', htmlspecialchars($routeDesc->nodeValue));
                $track->appendChild($trackDesc);
            }
            
            // Crear segmento de track
            $trackSeg = $this->dom->createElement('trkseg');
            
            // Convertir route points a track points
            $routePoints = $this->xpath->query('gpx:rtept', $route);
            $points = [];
            
            foreach ($routePoints as $rtept) {
                $lat = $rtept->getAttribute('lat');
                $lon = $rtept->getAttribute('lon');
                
                if ($lat && $lon) {
                    $points[] = [
                        'lat' => $lat,
                        'lon' => $lon,
                        'ele' => $this->getElementValue($rtept, 'gpx:ele'),
                        'name' => $this->getElementValue($rtept, 'gpx:name'),
                        'time' => $this->getElementValue($rtept, 'gpx:time')
                    ];
                }
            }
            
            // Simplificar si se solicita
            if ($simplify && count($points) > 100) {
                $points = $this->simplifyPoints($points, 50);
            }
            
            // Crear track points
            foreach ($points as $point) {
                $trkpt = $this->dom->createElement('trkpt');
                $trkpt->setAttribute('lat', $point['lat']);
                $trkpt->setAttribute('lon', $point['lon']);
                
                if ($point['ele']) {
                    $ele = $this->dom->createElement('ele', $point['ele']);
                    $trkpt->appendChild($ele);
                }
                
                if ($point['time']) {
                    $time = $this->dom->createElement('time', $point['time']);
                    $trkpt->appendChild($time);
                }
                
                $trackSeg->appendChild($trkpt);
            }
            
            $track->appendChild($trackSeg);
            
            // Reemplazar route con track
            $route->parentNode->replaceChild($track, $route);
        }
        
        return count($routes) > 0;
    }
    
    public function convertTrackToRoute() {
        $tracks = $this->xpath->query('//gpx:trk');
        
        foreach ($tracks as $track) {
            $route = $this->dom->createElement('rte');
            
            // Copiar metadatos
            $trackName = $this->xpath->query('gpx:name', $track)->item(0);
            if ($trackName) {
                $routeName = $this->dom->createElement('name', htmlspecialchars($trackName->nodeValue));
                $route->appendChild($routeName);
            }
            
            // Extraer puntos de todos los segmentos
            $trackPoints = $this->xpath->query('.//gpx:trkpt', $track);
            $totalPoints = $trackPoints->length;
            
            // Simplificar para crear waypoints significativos
            $step = max(1, floor($totalPoints / 20)); // Máximo 20 puntos de ruta
            
            for ($i = 0; $i < $totalPoints; $i += $step) {
                $trkpt = $trackPoints->item($i);
                if ($trkpt) {
                    $rtept = $this->dom->createElement('rtept');
                    $rtept->setAttribute('lat', $trkpt->getAttribute('lat'));
                    $rtept->setAttribute('lon', $trkpt->getAttribute('lon'));
                    
                    $ele = $this->xpath->query('gpx:ele', $trkpt)->item(0);
                    if ($ele) {
                        $routeEle = $this->dom->createElement('ele', $ele->nodeValue);
                        $rtept->appendChild($routeEle);
                    }
                    
                    $route->appendChild($rtept);
                }
            }
            
            // Reemplazar track con route
            $track->parentNode->replaceChild($route, $track);
        }
        
        return count($tracks) > 0;
    }
    
    public function removeGarminExtensions() {
        // Eliminar todos los elementos de extensiones
        $extensions = $this->xpath->query('//gpx:extensions');
        foreach ($extensions as $ext) {
            $ext->parentNode->removeChild($ext);
        }
        
        // Limpiar namespaces de Garmin del root completamente
        $root = $this->dom->documentElement;
        $attributes = [];
        
        foreach ($root->attributes as $attr) {
            if (strpos($attr->name, 'xmlns:') === 0 && 
                (strpos($attr->value, 'garmin.com') !== false || 
                 strpos($attr->name, 'gpxx') !== false ||
                 strpos($attr->name, 'trp') !== false ||
                 strpos($attr->name, 'wptx') !== false ||
                 strpos($attr->name, 'gpxtrx') !== false ||
                 strpos($attr->name, 'gpxtpx') !== false ||
                 strpos($attr->name, 'adv') !== false ||
                 strpos($attr->name, 'prs') !== false ||
                 strpos($attr->name, 'tmd') !== false ||
                 strpos($attr->name, 'vptm') !== false ||
                 strpos($attr->name, 'ctx') !== false ||
                 strpos($attr->name, 'gpxacc') !== false ||
                 strpos($attr->name, 'gpxpx') !== false ||
                 strpos($attr->name, 'vidx') !== false)) {
                $attributes[] = $attr->name;
            }
        }
        
        foreach ($attributes as $attrName) {
            $root->removeAttribute($attrName);
        }
        
        // Limpiar schema locations completamente
        $schemaLocation = $root->getAttribute('xsi:schemaLocation');
        if ($schemaLocation) {
            // Solo mantener el schema GPX básico
            $root->setAttribute('xsi:schemaLocation', 
                'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd');
        }
        
        return true;
    }
    
    public function convertToTomTomFormat() {
        // Registrar namespaces necesarios para leer Garmin
        $this->xpath->registerNamespace('trp', 'http://www.garmin.com/xmlschemas/TripExtensions/v1');
        
        // Crear un nuevo documento GPX completamente limpio
        $newDom = new DOMDocument('1.0', 'UTF-8');
        $newDom->formatOutput = true;
        $newDom->preserveWhiteSpace = false;
        
        // Crear elemento GPX con formato TomTom
        $gpx = $newDom->createElement('gpx');
        $gpx->setAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
        $gpx->setAttribute('xmlns:tt', 'TT');
        $gpx->setAttribute('version', '1.1');
        $gpx->setAttribute('creator', 'TomTom MyDrive');
        
        $newDom->appendChild($gpx);
        
        // Debug: contar puntos originales
        $originalRoutes = $this->xpath->query('//gpx:rte');
        $totalOriginalPoints = 0;
        foreach ($originalRoutes as $route) {
            $points = $this->xpath->query('gpx:rtept', $route);
            $totalOriginalPoints += $points->length;
        }
        
        // Procesar todas las rutas
        foreach ($originalRoutes as $route) {
            // Obtener nombre de la ruta
            $nameElement = $this->xpath->query('gpx:name', $route)->item(0);
            $routeName = $nameElement ? $nameElement->nodeValue : 'Imported Route';
            
            // Obtener TODOS los route points
            $routePoints = $this->xpath->query('gpx:rtept', $route);
            $allPoints = [];
            
            // Recopilar todos los puntos
            foreach ($routePoints as $rtept) {
                $lat = $rtept->getAttribute('lat');
                $lon = $rtept->getAttribute('lon');
                
                if ($lat && $lon) {
                    $allPoints[] = [
                        'lat' => floatval($lat),
                        'lon' => floatval($lon),
                        'ele' => $this->getElementValue($rtept, 'gpx:ele'),
                        'time' => $this->getElementValue($rtept, 'gpx:time')
                    ];
                }
            }
            
            $pointCount = count($allPoints);
            
            if ($pointCount > 0) {
                // Solo waypoints para inicio y fin
                $firstPoint = $allPoints[0];
                $lastPoint = $allPoints[$pointCount - 1];
                
                // Waypoint de inicio
                $startWaypoint = $newDom->createElement('wpt');
                $startWaypoint->setAttribute('lat', number_format($firstPoint['lat'], 6, '.', ''));
                $startWaypoint->setAttribute('lon', number_format($firstPoint['lon'], 6, '.', ''));
                $startType = $newDom->createElement('type', 'TT_HARD');
                $startWaypoint->appendChild($startType);
                $gpx->appendChild($startWaypoint);
                
                // Waypoint de fin
                $endWaypoint = $newDom->createElement('wpt');
                $endWaypoint->setAttribute('lat', number_format($lastPoint['lat'], 6, '.', ''));
                $endWaypoint->setAttribute('lon', number_format($lastPoint['lon'], 6, '.', ''));
                $endType = $newDom->createElement('type', 'TT_HARD');
                $endWaypoint->appendChild($endType);
                $gpx->appendChild($endWaypoint);
                
                // Crear ruta simple (solo inicio y fin)
                $newRoute = $newDom->createElement('rte');
                $newRouteName = $newDom->createElement('name', htmlspecialchars($routeName));
                $newRoute->appendChild($newRouteName);
                
                // Route point de inicio
                $startRoutePoint = $newDom->createElement('rtept');
                $startRoutePoint->setAttribute('lat', number_format($firstPoint['lat'], 6, '.', ''));
                $startRoutePoint->setAttribute('lon', number_format($firstPoint['lon'], 6, '.', ''));
                $startRouteType = $newDom->createElement('type', 'TT_HARD');
                $startRoutePoint->appendChild($startRouteType);
                $newRoute->appendChild($startRoutePoint);
                
                // Route point de fin
                $endRoutePoint = $newDom->createElement('rtept');
                $endRoutePoint->setAttribute('lat', number_format($lastPoint['lat'], 6, '.', ''));
                $endRoutePoint->setAttribute('lon', number_format($lastPoint['lon'], 6, '.', ''));
                $endRouteType = $newDom->createElement('type', 'TT_HARD');
                $endRoutePoint->appendChild($endRouteType);
                $newRoute->appendChild($endRoutePoint);
                
                $gpx->appendChild($newRoute);
                
                // Crear track con TODOS los puntos
                $track = $newDom->createElement('trk');
                $trackName = $newDom->createElement('name', htmlspecialchars($routeName));
                $track->appendChild($trackName);
                $trackSeg = $newDom->createElement('trkseg');
                
                // Añadir todos los puntos uno por uno
                foreach ($allPoints as $point) {
                    $trackPoint = $newDom->createElement('trkpt');
                    $trackPoint->setAttribute('lat', number_format($point['lat'], 6, '.', ''));
                    $trackPoint->setAttribute('lon', number_format($point['lon'], 6, '.', ''));
                    $trackSeg->appendChild($trackPoint);
                }
                
                $track->appendChild($trackSeg);
                $gpx->appendChild($track);
                
                // Debug: añadir comentario con estadísticas
                $comment = $newDom->createComment("Convertido: $pointCount puntos de $totalOriginalPoints originales");
                $gpx->appendChild($comment);
            }
        }
        
        // Si no hay rutas, procesar tracks
        if ($originalRoutes->length == 0) {
            $tracks = $this->xpath->query('//gpx:trk');
            foreach ($tracks as $originalTrack) {
                $trackName = $this->getElementValue($originalTrack, 'gpx:name') ?: 'Imported Track';
                
                $trackPoints = $this->xpath->query('.//gpx:trkpt', $originalTrack);
                $allTrackPoints = [];
                
                foreach ($trackPoints as $trkpt) {
                    $lat = $trkpt->getAttribute('lat');
                    $lon = $trkpt->getAttribute('lon');
                    
                    if ($lat && $lon) {
                        $allTrackPoints[] = [
                            'lat' => floatval($lat),
                            'lon' => floatval($lon)
                        ];
                    }
                }
                
                if (count($allTrackPoints) > 0) {
                    $firstPoint = $allTrackPoints[0];
                    $lastPoint = $allTrackPoints[count($allTrackPoints) - 1];
                    
                    // Waypoints
                    $startWaypoint = $newDom->createElement('wpt');
                    $startWaypoint->setAttribute('lat', number_format($firstPoint['lat'], 6, '.', ''));
                    $startWaypoint->setAttribute('lon', number_format($firstPoint['lon'], 6, '.', ''));
                    $startType = $newDom->createElement('type', 'TT_HARD');
                    $startWaypoint->appendChild($startType);
                    $gpx->appendChild($startWaypoint);
                    
                    $endWaypoint = $newDom->createElement('wpt');
                    $endWaypoint->setAttribute('lat', number_format($lastPoint['lat'], 6, '.', ''));
                    $endWaypoint->setAttribute('lon', number_format($lastPoint['lon'], 6, '.', ''));
                    $endType = $newDom->createElement('type', 'TT_HARD');
                    $endWaypoint->appendChild($endType);
                    $gpx->appendChild($endWaypoint);
                    
                    // Ruta simple
                    $newRoute = $newDom->createElement('rte');
                    $newRouteName = $newDom->createElement('name', htmlspecialchars($trackName));
                    $newRoute->appendChild($newRouteName);
                    
                    $startRoutePoint = $newDom->createElement('rtept');
                    $startRoutePoint->setAttribute('lat', number_format($firstPoint['lat'], 6, '.', ''));
                    $startRoutePoint->setAttribute('lon', number_format($firstPoint['lon'], 6, '.', ''));
                    $startRouteType = $newDom->createElement('type', 'TT_HARD');
                    $startRoutePoint->appendChild($startRouteType);
                    $newRoute->appendChild($startRoutePoint);
                    
                    $endRoutePoint = $newDom->createElement('rtept');
                    $endRoutePoint->setAttribute('lat', number_format($lastPoint['lat'], 6, '.', ''));
                    $endRoutePoint->setAttribute('lon', number_format($lastPoint['lon'], 6, '.', ''));
                    $endRouteType = $newDom->createElement('type', 'TT_HARD');
                    $endRoutePoint->appendChild($endRouteType);
                    $newRoute->appendChild($endRoutePoint);
                    
                    $gpx->appendChild($newRoute);
                    
                    // Track con todos los puntos
                    $newTrack = $newDom->createElement('trk');
                    $newTrackName = $newDom->createElement('name', htmlspecialchars($trackName));
                    $newTrack->appendChild($newTrackName);
                    $newTrackSeg = $newDom->createElement('trkseg');
                    
                    foreach ($allTrackPoints as $point) {
                        $newTrkpt = $newDom->createElement('trkpt');
                        $newTrkpt->setAttribute('lat', number_format($point['lat'], 6, '.', ''));
                        $newTrkpt->setAttribute('lon', number_format($point['lon'], 6, '.', ''));
                        $newTrackSeg->appendChild($newTrkpt);
                    }
                    
                    $newTrack->appendChild($newTrackSeg);
                    $gpx->appendChild($newTrack);
                }
            }
        }
        
        // Reemplazar el DOM actual
        $this->dom = $newDom;
        $this->xpath = new DOMXPath($this->dom);
        $this->xpath->registerNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
        
        return true;
    }
    
    public function extractWaypoints() {
        $waypoints = [];
        $wpts = $this->xpath->query('//gpx:wpt');
        
        foreach ($wpts as $wpt) {
            $waypoints[] = [
                'lat' => $wpt->getAttribute('lat'),
                'lon' => $wpt->getAttribute('lon'),
                'name' => $this->getElementValue($wpt, 'gpx:name'),
                'desc' => $this->getElementValue($wpt, 'gpx:desc'),
                'ele' => $this->getElementValue($wpt, 'gpx:ele')
            ];
        }
        
        return $waypoints;
    }
    
    public function generateITN($waypoints, $maxPoints = 100) {
        // Formato ITN básico para TomTom
        $itn = "TomTom POI\n";
        $count = 0;
        
        foreach ($waypoints as $wp) {
            if ($count >= $maxPoints) break;
            
            $name = $wp['name'] ?: "Waypoint " . ($count + 1);
            $itn .= sprintf("%.6f\t%.6f\t%s\n", 
                floatval($wp['lat']), 
                floatval($wp['lon']), 
                $name
            );
            $count++;
        }
        
        return $itn;
    }
    
    public function generateKML() {
        $kml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $kml .= '<kml xmlns="http://www.opengis.net/kml/2.2">' . "\n";
        $kml .= '<Document>' . "\n";
        
        // Añadir waypoints
        $waypoints = $this->xpath->query('//gpx:wpt');
        foreach ($waypoints as $wpt) {
            $name = $this->getElementValue($wpt, 'gpx:name') ?: 'Waypoint';
            $desc = $this->getElementValue($wpt, 'gpx:desc');
            
            $kml .= '<Placemark>' . "\n";
            $kml .= '<name>' . htmlspecialchars($name) . '</name>' . "\n";
            if ($desc) {
                $kml .= '<description>' . htmlspecialchars($desc) . '</description>' . "\n";
            }
            $kml .= '<Point>' . "\n";
            $kml .= '<coordinates>' . $wpt->getAttribute('lon') . ',' . $wpt->getAttribute('lat') . '</coordinates>' . "\n";
            $kml .= '</Point>' . "\n";
            $kml .= '</Placemark>' . "\n";
        }
        
        // Añadir tracks
        $tracks = $this->xpath->query('//gpx:trk');
        foreach ($tracks as $track) {
            $name = $this->getElementValue($track, 'gpx:name') ?: 'Track';
            
            $kml .= '<Placemark>' . "\n";
            $kml .= '<name>' . htmlspecialchars($name) . '</name>' . "\n";
            $kml .= '<LineString>' . "\n";
            $kml .= '<coordinates>' . "\n";
            
            $trackPoints = $this->xpath->query('.//gpx:trkpt', $track);
            foreach ($trackPoints as $trkpt) {
                $kml .= $trkpt->getAttribute('lon') . ',' . $trkpt->getAttribute('lat') . "\n";
            }
            
            $kml .= '</coordinates>' . "\n";
            $kml .= '</LineString>' . "\n";
            $kml .= '</Placemark>' . "\n";
        }
        
        $kml .= '</Document>' . "\n";
        $kml .= '</kml>' . "\n";
        
        return $kml;
    }
    
    public function validateGPX() {
        $errors = [];
        
        // Verificar estructura básica
        if (!$this->xpath->query('//gpx:gpx')->length) {
            $errors[] = 'No se encontró elemento GPX raíz';
        }
        
        // Verificar versión
        $gpxElement = $this->xpath->query('//gpx:gpx')->item(0);
        if ($gpxElement && $gpxElement->getAttribute('version') !== '1.1') {
            $errors[] = 'Versión GPX no es 1.1';
        }
        
        // Verificar coordenadas válidas
        $allPoints = $this->xpath->query('//gpx:wpt | //gpx:trkpt | //gpx:rtept');
        foreach ($allPoints as $point) {
            $lat = floatval($point->getAttribute('lat'));
            $lon = floatval($point->getAttribute('lon'));
            
            if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
                $errors[] = 'Coordenadas inválidas encontradas';
                break;
            }
        }
        
        return $errors;
    }
    
    public function getStats() {
        return [
            'waypoints' => $this->xpath->query('//gpx:wpt')->length,
            'routes' => $this->xpath->query('//gpx:rte')->length,
            'tracks' => $this->xpath->query('//gpx:trk')->length,
            'total_points' => $this->xpath->query('//gpx:wpt | //gpx:trkpt | //gpx:rtept')->length
        ];
    }
    
    public function output($tomtomFormat = false) {
        if ($tomtomFormat) {
            // Formato específico para TomTom con standalone="yes"
            $xml = $this->dom->saveXML();
            // Reemplazar la declaración XML para incluir standalone="yes"
            $xml = preg_replace('/^<\?xml version="1\.0" encoding="UTF-8"\?>/', 
                              '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', $xml);
            return $xml;
        }
        return $this->dom->saveXML();
    }
    
    private function getElementValue($parent, $tagName) {
        $element = $this->xpath->query($tagName, $parent)->item(0);
        return $element ? $element->nodeValue : null;
    }
    
    private function simplifyPoints($points, $maxPoints) {
        if (count($points) <= $maxPoints) {
            return $points;
        }
        
        $step = count($points) / $maxPoints;
        $simplified = [];
        
        for ($i = 0; $i < count($points); $i += $step) {
            $simplified[] = $points[floor($i)];
        }
        
        // Asegurar que el último punto esté incluido
        if (end($simplified) !== end($points)) {
            $simplified[] = end($points);
        }
        
        return $simplified;
    }
}

// Procesar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        // Verificar que se subió un archivo
        if (!isset($_FILES['gpx_file'])) {
            throw new Exception('No se recibió ningún archivo');
        }
        
        if ($_FILES['gpx_file']['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta directorio temporal',
                UPLOAD_ERR_CANT_WRITE => 'Error escribiendo archivo al disco',
                UPLOAD_ERR_EXTENSION => 'Subida detenida por extensión PHP'
            ];
            
            $errorCode = $_FILES['gpx_file']['error'];
            $errorMsg = isset($uploadErrors[$errorCode]) ? $uploadErrors[$errorCode] : 'Error desconocido subiendo archivo';
            throw new Exception($errorMsg);
        }
        
        $content = file_get_contents($_FILES['gpx_file']['tmp_name']);
        if ($content === false) {
            throw new Exception('Error leyendo contenido del archivo');
        }
        
        if (empty($content)) {
            throw new Exception('El archivo está vacío');
        }
        
        $converter = new GPXConverter();
        
        // Intentar cargar el GPX
        try {
            $converter->loadGPX($content);
        } catch (Exception $e) {
            throw new Exception('Error parseando GPX: ' . $e->getMessage());
        }
        
        $action = $_POST['action'] ?? 'tomtom';
        $result = ['success' => true];
        
        switch ($action) {        
            case 'tomtom':
                $converter->convertToTomTomFormat();
                $result['filename'] = 'converted_for_tomtom.gpx';
                $result['content'] = $converter->output(true); // true para formato TomTom
                $result['type'] = 'application/gpx+xml';
                $result['message'] = '🎯 Archivo convertido al formato nativo de TomTom: waypoints + rutas + tracks con tipos TT_HARD';
                break;
                
            case 'garmin':
                $converted = $converter->convertTrackToRoute();
                $result['filename'] = 'converted_for_garmin.gpx';
                $result['content'] = $converter->output();
                $result['type'] = 'application/gpx+xml';
                $result['message'] = '🚗 Archivo optimizado para Garmin: ' . 
                    ($converted ? 'tracks convertidos a rutas con waypoints' : 'archivo procesado');
                break;
                
            case 'clean':
                $converter->removeGarminExtensions();
                $result['filename'] = 'cleaned.gpx';
                $result['content'] = $converter->output();
                $result['type'] = 'application/gpx+xml';
                $result['message'] = '🧹 Archivo limpiado: todas las extensiones propietarias eliminadas, formato GPX estándar';
                break;
                
            case 'kml':
                $result['filename'] = 'converted.kml';
                $result['content'] = $converter->generateKML();
                $result['type'] = 'application/vnd.google-earth.kml+xml';
                $result['message'] = '🌍 Archivo convertido a formato KML para Google Earth/Maps';
                break;
                
            case 'itn':
                $waypoints = $converter->extractWaypoints();
                if (empty($waypoints)) {
                    throw new Exception('No se encontraron waypoints en el archivo GPX para generar ITN');
                }
                $result['filename'] = 'waypoints.itn';
                $result['content'] = $converter->generateITN($waypoints);
                $result['type'] = 'text/plain';
                $result['message'] = '📍 Archivo ITN generado: ' . count($waypoints) . ' waypoints extraídos para TomTom';
                break;
                
            case 'validate':
                $errors = $converter->validateGPX();
                $stats = $converter->getStats();
                $result['errors'] = $errors;
                $result['stats'] = $stats;
                $result['message'] = empty($errors) ? 
                    '✅ Archivo GPX completamente válido' : 
                    '⚠️ Archivo GPX con ' . count($errors) . ' advertencia(s)';
                echo json_encode($result);
                exit;
                
            default:
                throw new Exception('Acción no reconocida: ' . $action);
        }
        
        // Para descargas, codificar contenido en base64
        if (isset($result['content'])) {
            $result['content'] = base64_encode($result['content']);
        }
        
        echo json_encode($result);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage(),
            'debug_info' => [
                'php_version' => PHP_VERSION,
                'max_file_size' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit')
            ]
        ]);
    }
    
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversor GPX Universal - Tyre/Garmin a TomTom</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .main-content {
            padding: 40px;
        }
        
        .upload-section {
            background: #f8f9fa;
            border: 3px dashed #dee2e6;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        
        .upload-section:hover {
            border-color: #4CAF50;
            background: #f0f8f0;
        }
        
        .upload-section.dragover {
            border-color: #4CAF50;
            background: #e8f5e8;
            transform: scale(1.02);
        }
        
        .file-input {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        
        .file-input input[type=file] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-input-button {
            background: #4CAF50;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-input-button:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        
        .file-info {
            margin-top: 20px;
            padding: 15px;
            background: #e8f5e8;
            border-radius: 10px;
            display: none;
        }
        
        .conversion-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .option-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-color: #4CAF50;
        }
        
        .option-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .option-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .convert-btn {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .convert-btn:hover {
            background: #45a049;
        }
        
        .convert-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .stats-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            display: none;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #4CAF50;
        }
        
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4CAF50;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error {
            background: #ffe6e6;
            color: #d8000c;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 5px solid #d8000c;
        }
        
        .success {
            background: #e8f5e8;
            color: #4CAF50;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 5px solid #4CAF50;
        }
        
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 5px solid #ffc107;
        }
        
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 5px solid #17a2b8;
        }
        
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        
        .feature-item {
            display: flex;
            align-items: start;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .conversion-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗺️ Conversor GPX Universal</h1>
            <p>Convierte archivos de Tyre/Garmin para TomTom y otros formatos</p>
        </div>
        
        <div class="main-content">
            <div class="upload-section" id="uploadSection">
                <div class="file-input">
                    <input type="file" id="gpxFile" accept=".gpx,.xml" />
                    <button class="file-input-button">
                        📁 Seleccionar archivo GPX
                    </button>
                </div>
                <p style="margin-top: 15px; color: #666;">
                    O arrastra y suelta tu archivo GPX aquí
                </p>
                <div class="file-info" id="fileInfo"></div>
            </div>
            
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Procesando archivo...</p>
            </div>
            
            <div id="messageArea"></div>
            
            <div class="stats-section" id="statsSection">
                <h3>📊 Estadísticas del archivo</h3>
                <div class="stats-grid" id="statsGrid"></div>
            </div>
            
            <div class="conversion-options" id="conversionOptions" style="display: none;">
                <div class="option-card">
                    <h3>🎯 Para TomTom</h3>
                    <p>Convierte rutas a tracks, elimina extensiones de Garmin y optimiza para máxima compatibilidad con TomTom.</p>
                    <button class="convert-btn" onclick="convertFile('tomtom')">🔧 Convertir para TomTom</button>
                </div>
                
                <div class="option-card">
                    <h3>🚗 Para Garmin</h3>
                    <p>Convierte tracks a rutas con waypoints para navegación óptima en dispositivos Garmin.</p>
                    <button class="convert-btn" onclick="convertFile('garmin')">🔧 Convertir para Garmin</button>
                </div>
                
                <div class="option-card">
                    <h3>🧹 Limpiar archivo</h3>
                    <p>Elimina todas las extensiones propietarias manteniendo solo datos GPX estándar.</p>
                    <button class="convert-btn" onclick="convertFile('clean')">🧹 Limpiar GPX</button>
                </div>
                
                <div class="option-card">
                    <h3>🌍 Exportar a KML</h3>
                    <p>Convierte a formato KML para usar en Google Earth, Google Maps y otras aplicaciones.</p>
                    <button class="convert-btn" onclick="convertFile('kml')">🌍 Exportar KML</button>
                </div>
                
                <div class="option-card">
                    <h3>📍 Formato ITN</h3>
                    <p>Extrae waypoints y genera archivo ITN nativo de TomTom para máxima compatibilidad.</p>
                    <button class="convert-btn" onclick="convertFile('itn')">📍 Generar ITN</button>
                </div>
                
                <div class="option-card">
                    <h3>✅ Validar archivo</h3>
                    <p>Analiza el archivo GPX y reporta errores o problemas de compatibilidad.</p>
                    <button class="convert-btn" onclick="convertFile('validate')">✅ Validar GPX</button>
                </div>
            </div>
            
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon">🔄</div>
                    <div>
                        <h4>Conversión Universal</h4>
                        <p>Soporta conversión entre rutas, tracks, waypoints y múltiples formatos de navegación.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <div>
                        <h4>Optimización Automática</h4>
                        <p>Simplifica automáticamente tracks complejos y optimiza para cada dispositivo.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">🛡️</div>
                    <div>
                        <h4>Validación Completa</h4>
                        <p>Detecta y reporta errores de formato, coordenadas inválidas y problemas de compatibilidad.</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">🎨</div>
                    <div>
                        <h4>Limpieza Inteligente</h4>
                        <p>Elimina extensiones propietarias problemáticas manteniendo datos esenciales.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const uploadSection = document.getElementById('uploadSection');
        const fileInput = document.getElementById('gpxFile');
        const fileInfo = document.getElementById('fileInfo');
        const conversionOptions = document.getElementById('conversionOptions');
        const statsSection = document.getElementById('statsSection');
        const statsGrid = document.getElementById('statsGrid');
        const loading = document.getElementById('loading');
        const messageArea = document.getElementById('messageArea');
        
        // Drag and drop functionality
        uploadSection.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadSection.classList.add('dragover');
        });
        
        uploadSection.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadSection.classList.remove('dragover');
        });
        
        uploadSection.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadSection.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelection();
            }
        });
        
        fileInput.addEventListener('change', handleFileSelection);
        
        function handleFileSelection() {
            const file = fileInput.files[0];
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                fileInfo.innerHTML = `
                    <strong>📄 ${file.name}</strong><br>
                    Tamaño: ${fileSize} MB<br>
                    Tipo: ${file.type || 'GPX/XML'}<br>
                    <button onclick="showConversionOptions()" style="margin-top: 10px; padding: 8px 16px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        🔧 Mostrar opciones de conversión
                    </button>
                `;
                fileInfo.style.display = 'block';
                
                // Validar automáticamente
                validateFile();
            }
        }
        
        function showConversionOptions() {
            conversionOptions.style.display = 'grid';
            showMessage('🔧 Opciones de conversión mostradas. Puedes probar cualquier conversión.', 'info');
        }
        
        function validateFile() {
            const formData = new FormData();
            formData.append('gpx_file', fileInput.files[0]);
            formData.append('action', 'validate');
            
            showLoading(true);
            showMessage('🔍 Analizando archivo GPX...', 'info');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    showLoading(false);
                    
                    if (data.success) {
                        showStats(data.stats);
                        if (data.errors && data.errors.length > 0) {
                            showMessage('⚠️ Archivo válido pero con advertencias:<br>' + data.errors.join('<br>'), 'warning');
                        } else {
                            showMessage('✅ Archivo GPX válido y listo para conversión', 'success');
                        }
                        conversionOptions.style.display = 'grid';
                    } else {
                        showMessage('❌ Error: ' + (data.error || 'Error desconocido'), 'error');
                        // Mostrar opciones anyway para intentar conversión
                        conversionOptions.style.display = 'grid';
                    }
                } catch (parseError) {
                    showLoading(false);
                    console.error('JSON parse error:', parseError);
                    showMessage('⚠️ Error analizando respuesta, pero puedes intentar la conversión', 'warning');
                    conversionOptions.style.display = 'grid';
                }
            })
            .catch(error => {
                showLoading(false);
                console.error('Fetch error:', error);
                showMessage('⚠️ Error de conexión, pero puedes intentar la conversión directamente', 'warning');
                // Mostrar opciones para permitir intentar conversión
                conversionOptions.style.display = 'grid';
            });
        }
        
        function convertFile(action) {
            if (!fileInput.files[0]) {
                showMessage('❌ Por favor selecciona un archivo primero', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('gpx_file', fileInput.files[0]);
            formData.append('action', action);
            
            showLoading(true);
            showMessage('🔄 Procesando conversión...', 'info');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Conversion response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Conversion response:', text);
                try {
                    const data = JSON.parse(text);
                    showLoading(false);
                    
                    if (data.success) {
                        if (action === 'validate') {
                            if (data.errors && data.errors.length > 0) {
                                showMessage('⚠️ Problemas encontrados:<br>' + data.errors.join('<br>'), 'warning');
                            } else {
                                showMessage('✅ Archivo completamente válido', 'success');
                            }
                            if (data.stats) {
                                showStats(data.stats);
                            }
                        } else {
                            // Intentar descarga automática
                            const downloadSuccess = downloadFile(data.content, data.filename, data.type);
                            
                            let message = `✅ ${data.message || 'Conversión completada'}`;
                            
                            if (downloadSuccess) {
                                message += '<br>📥 Archivo descargado automáticamente';
                            } else {
                                message += '<br>⚠️ Descarga automática falló, usa el enlace manual:';
                            }
                            
                            // Siempre crear enlace manual de respaldo
                            const downloadLink = createDownloadLink(data.content, data.filename, data.type);
                            
                            showMessage(message + downloadLink, 'success');
                        }
                    } else {
                        showMessage('❌ Error: ' + (data.error || 'Error desconocido'), 'error');
                        
                        // Mostrar información de debug si está disponible
                        if (data.debug_info) {
                            console.log('Debug info:', data.debug_info);
                        }
                    }
                } catch (parseError) {
                    showLoading(false);
                    console.error('JSON parse error in conversion:', parseError);
                    console.log('Raw response:', text);
                    showMessage('❌ Error procesando respuesta del servidor. Revisa la consola para más detalles.', 'error');
                }
            })
            .catch(error => {
                showLoading(false);
                console.error('Conversion fetch error:', error);
                showMessage('❌ Error de conexión: ' + error.message, 'error');
            });
        }
        
        function downloadFile(base64Content, filename, mimeType) {
            try {
                const byteCharacters = atob(base64Content);
                const byteNumbers = new Array(byteCharacters.length);
                
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], { type: mimeType });
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                
                // Cleanup
                setTimeout(() => {
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                }, 100);
                
                return true;
            } catch (error) {
                console.error('Error downloading file:', error);
                return false;
            }
        }
        
        function createDownloadLink(base64Content, filename, mimeType) {
            try {
                const byteCharacters = atob(base64Content);
                const byteNumbers = new Array(byteCharacters.length);
                
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], { type: mimeType });
                
                const url = window.URL.createObjectURL(blob);
                
                return `
                    <div style="margin-top: 15px; padding: 15px; background: #e8f5e8; border-radius: 10px; text-align: center;">
                        <p style="margin-bottom: 10px;">📁 <strong>Archivo convertido listo:</strong></p>
                        <a href="${url}" download="${filename}" 
                           style="display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; 
                                  text-decoration: none; border-radius: 5px; font-weight: bold;">
                            ⬇️ Descargar ${filename}
                        </a>
                        <p style="margin-top: 10px; font-size: 0.9em; color: #666;">
                            Tamaño: ${(blob.size / 1024).toFixed(1)} KB
                        </p>
                    </div>
                `;
            } catch (error) {
                console.error('Error creating download link:', error);
                return '<div class="error">❌ Error creando enlace de descarga</div>';
            }
        }
        
        function showStats(stats) {
            statsGrid.innerHTML = `
                <div class="stat-item">
                    <div class="stat-number">${stats.waypoints}</div>
                    <div class="stat-label">Waypoints</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${stats.routes}</div>
                    <div class="stat-label">Rutas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${stats.tracks}</div>
                    <div class="stat-label">Tracks</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${stats.total_points}</div>
                    <div class="stat-label">Puntos totales</div>
                </div>
            `;
            statsSection.style.display = 'block';
        }
        
        function showMessage(message, type) {
            const clearButton = type !== 'info' ? 
                '<button onclick="clearMessages()" style="float: right; background: none; border: none; font-size: 1.2em; cursor: pointer; opacity: 0.7;">×</button>' 
                : '';
            
            messageArea.innerHTML = `<div class="${type}">${clearButton}${message}</div>`;
            
            // Auto-hide solo para mensajes de info
            if (type === 'info') {
                setTimeout(() => {
                    messageArea.innerHTML = '';
                }, 3000);
            }
        }
        
        function clearMessages() {
            messageArea.innerHTML = '';
        }
        
        function showLoading(show) {
            loading.style.display = show ? 'block' : 'none';
            const buttons = document.querySelectorAll('.convert-btn');
            buttons.forEach(btn => btn.disabled = show);
        }
    </script>
</body>
</html>