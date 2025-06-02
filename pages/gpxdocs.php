<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center mb-4"><i class="fas fa-map-marked-alt text-primary"></i> Guía Completa sobre Archivos GPX</h1>
            
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-primary">¿Qué es un archivo GPX?</h2>
                    <p class="card-text">
                        GPX (GPS Exchange Format) es un formato estándar para almacenar y compartir datos de rutas GPS. 
                        Contiene información como:
                    </p>
                    <ul>
                        <li>Puntos de ruta (waypoints)</li>
                        <li>Rutas completas (tracks)</li>
                        <li>Puntos de interés (POIs)</li>
                        <li>Elevación y coordenadas precisas</li>
                    </ul>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Todos nuestros archivos GPX incluyen metadatos adicionales como 
                        dificultad, puntos de interés y sugerencias de paradas.
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-primary">¿Qué Apps de navegación recomiendas?</h2>
                    <p class="card-text">
                        Siempre es una decisión complicada la de elegir una aplicación de navegación, ya que cada una tiene sus ventajas y desventajas.
                        Hemos salido a navegar con muchas de ellas y hemos recopilado nuestras experiencias para ayudarte a elegir la mejor opción para ti.</p>
                        <p class="card-text"> 
                        A nuestro criterio las mejores aplicaciones para navegar con archivos GPX son TomTom Go y OsmAnd. 
                        Ambas ofrecen una excelente experiencia de usuario y son compatibles con la mayoría de los dispositivos GPS y smartphones.
                    </p>
                        

                                        
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Personalmente recomendamos utilizar TomTom Go para navegación en carretera. Tiene una suscripción anual de 10€, que la verdad es poco comparado con lo que ofrece.  
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h2 class="card-title text-primary">Dispositivos y Aplicaciones Compatibles</h2>
        
        <!-- Dispositivos GPS -->
        <h3 class="mb-3"><i class="fas fa-tablet-alt text-success"></i> Dispositivos GPS</h3>
        <div class="row mb-4">
            <div class="col-3 mb-3 text-center">
                <img src="../images/dispositivos/garmin.jpg" class="img-fluid mb-2" style="max-height: 80px;" alt="Garmin">
                <div><strong>Garmin</strong></div>
                <small class="text-muted">Zumo, Montana, eTrex</small>
            </div>
            <div class="col-3 mb-3 text-center">
                <img src="../images/dispositivos/tomtomrider.jpg" class="img-fluid mb-2" style="max-height: 80px;" alt="TomTom">
                <div><strong>TomTom Rider</strong></div>
                <small class="text-muted">Series 400, 500, 550</small>
            </div>
            <div class="col-3 mb-3 text-center">
                <img src="../images/dispositivos/motorrad.png" class="img-fluid mb-2" style="max-height: 80px;" alt="BMW">
                <div><strong>BMW Navigator</strong></div>
                <small class="text-muted">VI, VII</small>
            </div>
            <div class="col-3 mb-3 text-center">
                <img src="../images/dispositivos/smartphone.webp" class="img-fluid mb-2" style="max-height: 80px;" alt="Smartphone">
                <div><strong>Smartphones</strong></div>
                <small class="text-muted">Android, iOS</small>
            </div>
        </div>

        <hr class="my-4">

        <!-- Aplicaciones Móviles -->
        <h3 class="mb-3"><i class="fas fa-mobile-alt text-info"></i> Aplicaciones de Navegación</h3>
        <div class="row">
            <div class="col-lg-2 col-md-3 col-4 mb-3 text-center">
                <img src="../images/dispositivos/osmand.png" class="img-fluid mb-2 rounded" style="max-height: 80px;" alt="OsmAnd">
                <div><strong>OsmAnd</strong></div>
                <small class="text-muted">Offline/Online</small>
            </div>
            <div class="col-lg-2 col-md-3 col-4 mb-3 text-center">
                <img src="../images/dispositivos/kurviger.jpg" class="img-fluid mb-2 rounded" style="max-height: 80px;" alt="Kurviger">
                <div><strong>Kurviger</strong></div>
                <small class="text-muted">Para Moteros</small>
            </div>
            <div class="col-lg-2 col-md-3 col-4 mb-3 text-center">
                <img src="../images/dispositivos/GoogleMaps.webp" class="img-fluid mb-2" style="max-height: 80px;" alt="Google">
                <div><strong>Google Maps</strong></div>
                <small class="text-muted">Navegación</small>
            </div>
            
            <div class="col-lg-2 col-md-3 col-4 mb-3 text-center">
                <img src="../images/dispositivos/myroute.png" class="img-fluid mb-2" style="max-height: 80px;" alt="MyRoute">
                <div><strong>MyRoute-App</strong></div>
                <small class="text-muted">Planificación</small>
            </div>
            <div class="col-lg-2 col-md-3 col-4 mb-3 text-center">
                <img src="../images/dispositivos/TomTomGo.png" class="img-fluid mb-2" style="max-height: 80px;" alt="TomTom GO">
                <div><strong>TomTom GO</strong></div>
                <small class="text-muted">Recomendada</small>
            </div>
        </div>

        <div class="alert alert-success mt-3">
            <i class="fas fa-star"></i> <strong>Nuestras Recomendaciones:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>Para Moteros:</strong> Kurviger (rutas curvas) o TomTom GO (navegación premium)</li>
                <li><strong>Para Aventureros:</strong> OsmAnd (mapas offline y rutas todo terreno)</li>
                <li><strong>Para Carretera:</strong> TomTom GO (mejor navegación con solo 10€/año)</li>
            </ul>
        </div>
    </div>
</div>

            <!-- Tutoriales -->
            <h2 class="text-center mb-4"><i class="fas fa-graduation-cap text-primary"></i> Tutoriales Paso a Paso</h2>

            <!-- TomTom GO -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary">
        <h3 class="mb-0 text-white"><i class="fas fa-map-signs"></i> TomTom GO (App y Dispositivo)</h3>
    </div>
    <div class="card-body">
        <!-- Tabs para diferentes métodos -->
        <ul class="nav nav-tabs mb-3" id="tomtomTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tomtom-app-tab" data-bs-toggle="tab" data-bs-target="#tomtom-app" type="button" role="tab">
                    <i class="fas fa-mobile-alt"></i> App TomTom GO
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tomtom-device-tab" data-bs-toggle="tab" data-bs-target="#tomtom-device" type="button" role="tab">
                    <i class="fas fa-desktop"></i> Dispositivo GPS
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tomtom-card-tab" data-bs-toggle="tab" data-bs-target="#tomtom-card" type="button" role="tab">
                    <i class="fas fa-sd-card"></i> Tarjeta SD
                </button>
            </li>
        </ul>

        <div class="tab-content" id="tomtomTabsContent">
            <!-- App TomTom GO -->
            <div class="tab-pane fade show active" id="tomtom-app" role="tabpanel">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Ve a <a href="https://plan.tomtom.com" target="_blank">plan.tomtom.com</a> en tu navegador</li>
                    <li class="list-group-item">Inicia sesión con tu cuenta TomTom (la misma de la app)</li>
                    <li class="list-group-item">Haz clic en <strong>"My Items"</strong> → <strong>"Routes"</strong></li>
                    <li class="list-group-item">Selecciona <strong>"Import GPX file"</strong></li>
                    <li class="list-group-item">Sube tu archivo GPX (máximo 15MB)</li>
                    <li class="list-group-item">Una vez importado, selecciona la ruta de la lista</li>
                    <li class="list-group-item">Activa el <strong>toggle "Sync this route as a track with your devices"</strong></li>
                    <li class="list-group-item">En tu app TomTom GO, ve a <strong>"My Routes"</strong> para encontrar la ruta sincronizada</li>
                    <li class="list-group-item">Selecciona la ruta y pulsa <strong>"Navigate"</strong></li>
                </ol>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> <strong>Nota:</strong> La sincronización puede tardar unos minutos. 
                    Asegúrate de tener conexión a internet en tu dispositivo.
                </div>
            </div>

            <!-- Dispositivo GPS -->
            <div class="tab-pane fade" id="tomtom-device" role="tabpanel">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Descarga e instala <strong>MyDrive Connect</strong> en tu ordenador</li>
                    <li class="list-group-item">Conecta tu TomTom GO al ordenador via USB</li>
                    <li class="list-group-item">Abre MyDrive Connect y espera a que reconozca el dispositivo</li>
                    <li class="list-group-item">Ve a <strong>"My Routes"</strong> en la interfaz de MyDrive</li>
                    <li class="list-group-item">Haz clic en <strong>"Import track (.GPX)"</strong></li>
                    <li class="list-group-item">Selecciona tu archivo GPX desde el ordenador</li>
                    <li class="list-group-item">El track se sincronizará automáticamente con tu dispositivo</li>
                    <li class="list-group-item">En tu TomTom GO, ve a <strong>"My Routes"</strong> para encontrar la ruta</li>
                    <li class="list-group-item">Selecciona la ruta y sigue las instrucciones exactas del track</li>
                </ol>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Requisitos:</strong> Tu dispositivo necesita al menos 
                    400MB de espacio libre para importar archivos GPX.
                </div>
            </div>

            <!-- Tarjeta SD -->
            <div class="tab-pane fade" id="tomtom-card" role="tabpanel">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Copia tu archivo GPX a una tarjeta SD o micro SD</li>
                    <li class="list-group-item">Inserta la tarjeta en tu dispositivo TomTom GO</li>
                    <li class="list-group-item">El dispositivo detectará automáticamente las rutas en la tarjeta</li>
                    <li class="list-group-item">Aparecerá una notificación: <strong>"Routes detected on card"</strong></li>
                    <li class="list-group-item">Selecciona <strong>"Import Routes"</strong></li>
                    <li class="list-group-item">Marca las rutas que quieres importar de la lista</li>
                    <li class="list-group-item">Pulsa <strong>"Import"</strong> y espera el mensaje de confirmación</li>
                    <li class="list-group-item">Retira la tarjeta cuando termine la importación</li>
                    <li class="list-group-item">Ve a <strong>"My Routes"</strong> en el menú principal para acceder a tus rutas</li>
                </ol>

                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle"></i> <strong>Ventaja:</strong> Este método es útil cuando no tienes acceso 
                    a internet o un ordenador para la sincronización.
                </div>
            </div>
        </div>

        <!-- Características específicas de TomTom -->
        <div class="card mb-4 shadow-sm">
            <div class="alert alert-primary mt-4">
                <h5 class="text-white"><i class="fas fa-star"></i> Características de TomTom GO con GPX</h5>
            </div>
            <div class="p-3">
                <ul class="mb-0">
                <li><strong>Navegación exacta:</strong> Sigue el track GPX exactamente sin recalcular rutas</li>
                <li><strong>Sin desvíos:</strong> No sugiere rutas más rápidas, mantiene la ruta original</li>
                <li><strong>Límite de tamaño:</strong> Archivos GPX máximo de 15MB</li>
                <li><strong>Formato track:</strong> Los GPX se muestran como "tracks" (línea punteada) en el dispositivo</li>
                <li><strong>Sincronización:</strong> Las rutas se sincronizan automáticamente entre dispositivos con la misma cuenta</li>
            </ul>
            </div>
            
        
        </div>

        <!-- Solución de problemas -->
        <div class="alert alert-warning mt-3">
            <h5><i class="fas fa-wrench"></i> Solución de Problemas Comunes</h5>
            <ul class="mb-0">
                <li><strong>Archivo no se importa:</strong> Verifica que el GPX sea menor de 15MB y tenga formato correcto</li>
                <li><strong>Dispositivo lento:</strong> Si el GPS se vuelve lento después de importar, considera resetear a configuración de fábrica</li>
                <li><strong>No aparece en My Routes:</strong> Revisa que tengas espacio suficiente (400MB mínimo)</li>
                <li><strong>No sincroniza:</strong> Asegúrate de estar logueado con la misma cuenta TomTom en todos los dispositivos</li>
            </ul>
        </div>
    </div>
</div>

<!-- MyRoute-App -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary">
        <h3 class="mb-0 text-white"><i class="fas fa-route"></i> MyRoute-App (Web y Móvil)</h3>
    </div>
    <div class="card-body">
        <!-- Tabs para diferentes métodos -->
        <ul class="nav nav-tabs mb-3" id="mraTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="mra-web-tab" data-bs-toggle="tab" data-bs-target="#mra-web" type="button" role="tab">
                    <i class="fas fa-desktop"></i> Versión Web
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mra-mobile-tab" data-bs-toggle="tab" data-bs-target="#mra-mobile" type="button" role="tab">
                    <i class="fas fa-mobile-alt"></i> App Móvil
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mra-export-tab" data-bs-toggle="tab" data-bs-target="#mra-export" type="button" role="tab">
                    <i class="fas fa-download"></i> Exportar
                </button>
            </li>
        </ul>

        <div class="tab-content" id="mraTabsContent">
            <!-- Versión Web (Recomendado) -->
            <div class="tab-pane fade show active" id="mra-web" role="tabpanel">
                <div class="alert alert-success mb-3">
                    <i class="fas fa-star"></i> <strong>Método Recomendado:</strong> La versión web ofrece mejor control y verificación de waypoints.
                </div>
                
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Ve a <a href="https://www.myrouteapp.com" target="_blank">myrouteapp.com</a> y crea una cuenta</li>
                    <li class="list-group-item">Inicia sesión y ve a tu <strong>"Biblioteca de Rutas"</strong></li>
                    <li class="list-group-item">Haz clic en el botón <strong>"+"</strong> en la esquina superior derecha</li>
                    <li class="list-group-item">Selecciona <strong>"Upload"</strong> del menú desplegable</li>
                    <li class="list-group-item">
                        Elige el tipo de archivo que vas a importar:
                        <ul class="mt-2">
                            <li><strong>Ruta:</strong> Archivo con waypoints donde se calculará la ruta</li>
                            <li><strong>Route-track:</strong> Archivo con línea de ruta dibujada</li>
                            <li><strong>Track-log:</strong> Registro de un viaje realizado</li>
                        </ul>
                    </li>
                    <li class="list-group-item">Configura la privacidad: para todos, amigos, o solo para ti</li>
                    <li class="list-group-item">Indica el tipo de transporte: conducir, ciclismo, o caminar</li>
                    <li class="list-group-item">Haz clic en <strong>"Elegir archivo"</strong> y selecciona tu GPX</li>
                    <li class="list-group-item">Tras la importación, la ruta aparecerá en tu archivo de rutas</li>
                    <li class="list-group-item">Haz clic en la ruta para abrirla y verificar los waypoints</li>
                </ol>
            </div>

            <!-- App Móvil -->
            <div class="tab-pane fade" id="mra-mobile" role="tabpanel">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Limitación:</strong> La app móvil tiene funcionalidad limitada para importación. Se recomienda usar la web primero.
                </div>

                <h5><i class="fas fa-apple"></i> iOS (iPhone/iPad):</h5>
                <ol class="list-group list-group-numbered mb-4">
                    <li class="list-group-item">Asegúrate de tener el archivo GPX accesible en tu dispositivo</li>
                    <li class="list-group-item">Toca el archivo GPX desde tu gestor de archivos</li>
                    <li class="list-group-item">Aparecerá el contenido del archivo como texto</li>
                    <li class="list-group-item">Toca el icono de <strong>"Compartir"</strong> en la parte inferior izquierda</li>
                    <li class="list-group-item">Selecciona <strong>"MyRoute-App"</strong> de la lista de aplicaciones</li>
                    <li class="list-group-item">La ruta se abrirá directamente para navegación</li>
                </ol>

                <h5><i class="fab fa-android"></i> Android:</h5>
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Descarga o localiza tu archivo GPX en el dispositivo</li>
                    <li class="list-group-item">Usa el gestor de archivos para encontrar el GPX</li>
                    <li class="list-group-item">Toca el archivo y selecciona <strong>"Abrir con"</strong></li>
                    <li class="list-group-item">Elige <strong>"MyRoute-App"</strong> de las opciones</li>
                    <li class="list-group-item">La ruta se cargará automáticamente en la app</li>
                </ol>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Las rutas importadas en móvil se abren directamente para navegación, pero no se guardan automáticamente en tu biblioteca.
                </div>
            </div>

            <!-- Exportar -->
            <div class="tab-pane fade" id="mra-export" role="tabpanel">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Abre la ruta importada en el editor web de MyRoute-App</li>
                    <li class="list-group-item">Verifica y ajusta los waypoints si es necesario</li>
                    <li class="list-group-item">Haz clic en <strong>"Export"</strong> o <strong>"Save as"</strong></li>
                    <li class="list-group-item">
                        Selecciona el formato según tu dispositivo:
                        <ul class="mt-2">
                            <li><strong>GPX 1.1:</strong> Para sistemas de navegación modernos</li>
                            <li><strong>GPX 1.0:</strong> Para dispositivos GPS más antiguos</li>
                            <li><strong>GPX Track only:</strong> Solo track sin waypoints (Garmin Zumo XT)</li>
                            <li><strong>ITN:</strong> Para modelos antiguos TomTom Rider</li>
                            <li><strong>BMW específico:</strong> Para BMW Navigator</li>
                        </ul>
                    </li>
                    <li class="list-group-item">Para transferencia directa, instala <strong>"MRA Connector"</strong></li>
                    <li class="list-group-item">Conecta tu dispositivo GPS al ordenador</li>
                    <li class="list-group-item">Usa la función <strong>"Export"</strong> para envío directo al dispositivo</li>
                </ol>
            </div>
        </div>

        <!-- Características específicas de MyRoute-App -->
        <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary">
            <h5 class="text-white"><i class="fas fa-star"></i> Ventajas de MyRoute-App</h5>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li><strong>Import universal:</strong> Acepta casi todos los formatos de archivo GPX</li>
                <li><strong>Editor integrado:</strong> Modifica rutas después de importar</li>
                <li><strong>Verificación de waypoints:</strong> Revisa automáticamente posicionamiento en carreteras</li>
                <li><strong>Export multiformato:</strong> Adapta al formato específico de tu dispositivo GPS</li>
                <li><strong>MRA Connector:</strong> Transferencia directa sin cables a dispositivos compatibles</li>
                <li><strong>Biblioteca personal:</strong> Organiza y guarda todas tus rutas importadas</li>
            </ul>
        </div>
        </div>

        <!-- Tipos de importación -->
        <div class="alert alert-info mt-3">
            <h5><i class="fas fa-file-import"></i> Tipos de Importación</h5>
            <div class="row">
                <div class="col-md-4">
                    <strong>📍 Ruta (Route)</strong><br>
                    <small>Archivo con waypoints entre los que se calcula la ruta automáticamente</small>
                </div>
                <div class="col-md-4">
                    <strong>🗺️ Route-Track</strong><br>
                    <small>Archivo con línea de ruta ya dibujada entre inicio y destino</small>
                </div>
                <div class="col-md-4">
                    <strong>📊 Track-log</strong><br>
                    <small>Registro GPS de un viaje realizado previamente</small>
                </div>
            </div>
        </div>

        <!-- Solución de problemas -->
        <div class="alert alert-warning mt-3">
            <h5><i class="fas fa-wrench"></i> Solución de Problemas</h5>
            <ul class="mb-0">
                <li><strong>Demasiados waypoints:</strong> Reduce el número de puntos o usa "Track only"</li>
                <li><strong>Líneas rectas en GPS:</strong> Cambia de GPX 1.1 a GPX 1.0, o usa el truco "shortest route → fastest route"</li>
                <li><strong>Waypoints mal posicionados:</strong> Edita manualmente en la versión web antes de exportar</li>
                <li><strong>No se guarda en móvil:</strong> Importa primero en la web para guardar en biblioteca</li>
                <li><strong>Archivo no reconocido:</strong> Verifica que sea un GPX válido o prueba otros formatos</li>
            </ul>
        </div>

        <!-- Recomendación para moteros -->
        <div class="alert alert-success mt-3">
            <h5><i class="fas fa-motorcycle"></i> Perfecto para Moteros</h5>
            <p class="mb-0">MyRoute-App es muy popular entre motociclistas porque permite crear rutas curvas y escénicas, 
            es compatible con todos los GPS de moto (BMW, Garmin, TomTom), y tiene una comunidad activa 
            compartiendo rutas moteras. <strong>Ideal para planificar y compartir aventuras en moto.</strong></p>
        </div>
    </div>
</div>
            
            <!-- Garmin -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary">
                    <h3 class="mb-0 text-white"><i class="fas fa-map-marker-alt"></i> Garmin</h3>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">Conecta tu dispositivo Garmin al ordenador via USB</li>
                        <li class="list-group-item">Accede a la carpeta <code>GPX</code> en la memoria interna</li>
                        <li class="list-group-item">Copia el archivo GPX descargado a esta carpeta</li>
                        <li class="list-group-item">Desconecta el dispositivo y enciéndelo</li>
                        <li class="list-group-item">Ve a <strong>Where To?</strong> > <strong>Tracks</strong></li>
                        <li class="list-group-item">Selecciona la ruta y pulsa <strong>Go!</strong></li>
                    </ol>
                </div>
            </div>
            
            <!-- Smartphone con OsmAnd -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary">
                    <h3 class="mb-0 text-white"><i class="fas fa-mobile-alt"></i> OsmAnd en Smartphone</h3>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">Descarga la app OsmAnd desde tu tienda de aplicaciones</li>
                        <li class="list-group-item">Abre el archivo GPX desde tu gestor de archivos</li>
                        <li class="list-group-item">Selecciona <strong>Abrir con OsmAnd</strong></li>
                        <li class="list-group-item">En la app, ve a <strong>Mis mapas</strong> > <strong>Tracks</strong></li>
                        <li class="list-group-item">Selecciona la ruta y pulsa <strong>Mostrar en mapa</strong></li>
                        <li class="list-group-item">Activa el modo navegación si deseas indicaciones</li>
                    </ol>
                </div>
            </div>

            <!-- Kurviger -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary">
        <h3 class="mb-0 text-white"><i class="fas fa-route"></i> Kurviger (App y Web)</h3>
    </div>
    <div class="card-body">
        <!-- Tabs para App y Web -->
        <ul class="nav nav-tabs mb-3" id="kurvigerTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="kurviger-app-tab" data-bs-toggle="tab" data-bs-target="#kurviger-app" type="button" role="tab">
                    <i class="fas fa-mobile-alt"></i> App Móvil
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="kurviger-web-tab" data-bs-toggle="tab" data-bs-target="#kurviger-web" type="button" role="tab">
                    <i class="fas fa-desktop"></i> Versión Web
                </button>
            </li>
        </ul>

        <div class="tab-content" id="kurvigerTabsContent">
            <!-- App Móvil -->
            <div class="tab-pane fade show active" id="kurviger-app" role="tabpanel">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Abre la app Kurviger en tu smartphone</li>
                    <li class="list-group-item">Toca el <strong>menú hamburguesa</strong> (≡) en la parte superior izquierda</li>
                    <li class="list-group-item">Selecciona <strong>"Routing"</strong> y luego <strong>"Import"</strong></li>
                    <li class="list-group-item">Navega hasta tu archivo GPX y selecciónalo</li>
                    <li class="list-group-item">
                        Elige el tipo de importación:
                        <ul class="mt-2">
                            <li><strong>Como Ruta:</strong> Kurviger calculará una ruta navegable con instrucciones</li>
                            <li><strong>Como Overlay:</strong> Se mostrará como línea de referencia en el mapa</li>
                        </ul>
                    </li>
                    <li class="list-group-item">Mantén presionado en el mapa donde quieres empezar la navegación</li>
                    <li class="list-group-item">Selecciona <strong>"Set Target"</strong> y pulsa <strong>"Start Routing"</strong></li>
                </ol>
            </div>

            <!-- Versión Web -->
            <div class="tab-pane fade" id="kurviger-web" role="tabpanel">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">Ve a <a href="https://kurviger.de" target="_blank">kurviger.de</a> en tu navegador</li>
                    <li class="list-group-item">Haz clic en el botón de <strong>importación</strong> (📁) en la barra de herramientas</li>
                    <li class="list-group-item">Selecciona tu archivo GPX desde el ordenador</li>
                    <li class="list-group-item">El track aparecerá en <span class="text-danger">rojo</span> y la ruta calculada en <span class="text-primary">azul</span></li>
                    <li class="list-group-item">
                        Si la ruta no coincide con el track original:
                        <ul class="mt-2">
                            <li>Cambia las opciones de ruta (curvy, fastest, etc.)</li>
                            <li>Haz clic derecho en puntos clave → <strong>"Set as intermediate destination"</strong></li>
                        </ul>
                    </li>
                    <li class="list-group-item">Usa <strong>"Snap waypoints to road"</strong> en el menú (⋮) para optimizar</li>
                    <li class="list-group-item">Exporta la ruta final a tu dispositivo GPS o smartphone</li>
                </ol>
            </div>
        </div>

        <!-- Consejos específicos para Kurviger -->
        <div class="alert alert-warning mt-3">
            <h5><i class="fas fa-lightbulb"></i> Consejos para Kurviger</h5>
            <ul class="mb-0">
                <li><strong>Menos waypoints, mejor:</strong> Usa los mínimos puntos necesarios para evitar errores</li>
                <li><strong>Ajusta opciones:</strong> Cambia entre "extra curvy" y "fastest" según el tipo de carretera</li>
                <li><strong>Modo Follow:</strong> Si importas como overlay, úsalo para seguir el track exacto sin recalcular</li>
                <li><strong>Formato .kurviger:</strong> Si tienes este formato, se importa sin recalculación automática</li>
            </ul>
        </div>
    </div>
</div>
            
            <!-- Google Earth -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary">
                    <h3 class="mb-0 text-white"><i class="fab fa-google"></i> Google Earth</h3>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">Abre Google Earth en tu ordenador</li>
                        <li class="list-group-item">Haz clic en <strong>Archivo</strong> > <strong>Abrir</strong></li>
                        <li class="list-group-item">Selecciona tu archivo GPX descargado</li>
                        <li class="list-group-item">La ruta aparecerá en el panel izquierdo bajo <strong>Lugares temporales</strong></li>
                        <li class="list-group-item">Haz clic derecho y selecciona <strong>Mostrar perfil de elevación</strong></li>
                        <li class="list-group-item">Para guardar, arrastra a <strong>Mis sitios</strong> y haz clic en guardar</li>
                    </ol>
                </div>
            </div>
            
            <!-- Consejos adicionales -->
            <div class="card border-warning shadow-sm">
                <div class="card-header bg-warning">
                    <h3 class="mb-0"><i class="fas fa-lightbulb"></i> Consejos Prácticos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4><i class="fas fa-battery-three-quarters"></i> Optimización de Batería</h4>
                            <p>Usa el modo avión en zonas sin cobertura para ahorrar batería mientras registras rutas.</p>
                        </div>
                        <div class="col-md-6">
                            <h4><i class="fas fa-sync-alt"></i> Actualizaciones</h4>
                            <p>Mantén tu dispositivo y apps actualizados para mejor compatibilidad con archivos GPX.</p>
                        </div>
                        <div class="col-md-6">
                            <h4><i class="fas fa-file-export"></i> Conversión de Formatos</h4>
                            <p>Puedes convertir GPX a KML usando herramientas como <a href="https://gpx2kml.com" target="_blank">GPX2KML</a>.</p>
                        </div>
                        <div class="col-md-6">
                            <h4><i class="fas fa-cloud-download-alt"></i> Almacenamiento</h4>
                            <p>Guarda copias de tus rutas en la nube (Google Drive, Dropbox) como respaldo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="height: 30rem;"></div>
<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>