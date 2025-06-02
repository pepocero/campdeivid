<?php
require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if(isset($user) && $user->isLoggedIn()){
}
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/custom.css">

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1><span class="text-light">Rutas en Moto</span> <span class="text-accent">Planificadas al Detalle</span></h1>
                <p class="hero-subtitle">Descarga rutas en formato GPX y disfruta de viajes sin preocupaciones. 
                <br>   Candeivid ofrece las mejores rutas para moteros, cuidadosamente planificadas por expertos para garantizar la mejor experiencia de conducción.
                </p>               
            </div>
        </div>
    </section>
    

    <!-- Que ofrecemos -->
    <section id="servicios" class="section services">
        <div class="container">
            <h2 class="section-title">¿Qué Ofrecemos?</h2>
            <div class="grid servicios">
                <div class="card">
                    <i class="fas fa-route"></i>
                    <h3>Rutas en GPX</h3>
                    <p>Descarga rutas profesionales para tu GPS o smartphone.</p>
                </div>
                <div class="card">
                    <i class="fas fa-hotel"></i>
                    <h3>Reservas Premium</h3>
                    <p>Hoteles y restaurantes seleccionados para viajes multi-día.</p>
                </div>
                <div class="card">
                    <i class="fas fa-gas-pump"></i>
                    <h3>Paradas de Combustible</h3>
                    <p>Rutas optimizadas con paradas de repostaje.</p>
                </div>
                <div class="card">
                    <i class="fas fa-road"></i>
                    <h3>Experiencias Cuidadas</h3>
                    <p>Todas nuestras rutas están cuidadosamente diseñadas, clasificadas por dificultad y belleza paisajística. Desde paseos tranquilos para novatos hasta desafíos para expertos.</p>
                </div>
            <div class="card">
                <i class="fas fa-percent"></i>
                <h3>Reservas con Descuento</h3>
                <p>Alojamientos y restaurantes seleccionados con precios especiales y gestionados para nuestros usuarios premium y compradores de rutas con asistencia.</p>
            </div>
            <div class="card">
                <i class="fas fa-shield-alt"></i>
                <h3>Seguridad y Asistencia</h3>
                <p>Planificación de repostajes, información de interés, y para usuarios premium, asistencia en viaje y asesoramiento personalizado.</p>
            </div>
            </div>
        </div>
    </section>

    <!-- Experiencia del Equipo -->
<section class="section team-experience">
    <div class="experience-container">
        <!-- Background con overlay -->
        <div class="experience-background">
            <div class="bg-overlay"></div>
        </div>
        
        <!-- Contenido principal -->
        <div class="container">
            <!-- Primera fila: Texto izquierda + Imagen derecha -->
            <div class="top-section">
                <!-- Lado izquierdo - Texto principal -->
                <div class="content-left">
                    <div class="experience-badge">
                        <span>Experiencia Real</span>
                    </div>
                    
                    <h2 class="main-title">
                        Cada ruta ha sido 
                        <span class="highlight">recorrida por nosotros</span>
                    </h2>
                    
                    <p class="main-description">
                        No vendemos rutas de escritorio. Nuestro equipo de moteros experimentados 
                        recorre personalmente cada kilómetro antes de que llegue a ti. 
                        Conocemos cada curva, cada parada y cada paisaje porque los hemos vivido.
                    </p>
                    
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-number">150+</div>
                            <div class="stat-label">Rutas Probadas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">15</div>
                            <div class="stat-label">Años de Experiencia</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">20K+</div>
                            <div class="stat-label">Km Anuales</div>
                        </div>
                    </div>
                </div>
                
                <!-- Lado derecho - Imagen del equipo -->
                <div class="content-right">
                    <div class="team-image">
                        <div class="image-content">
                            <div class="image-text">
                                <h3>Nuestro Equipo</h3>
                                <p>En acción probando rutas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Segunda fila: Compromiso centrado -->
            <div class="middle-section">
                <div class="guarantee-box-center">
                    <div class="guarantee-content">
                        <h4>Compromiso con la Excelencia</h4>
                        <p>Cada ruta está respaldada por nuestra experiencia y pasión por el motociclismo. Nuestro objetivo es que vivas la mejor experiencia en carretera.</p>
                    </div>
                </div>
            </div>
            
            <!-- Tercera fila: 3 tarjetas horizontales -->
            <div class="bottom-section">
                <div class="services-grid-horizontal">
                    <div class="service-card">
                        <i class="fas fa-check-circle"></i>
                        <h4>Verificación Real</h4>
                        <p>Cada ruta probada múltiples veces en diferentes condiciones</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-map-marked-alt"></i>
                        <h4>Conocimiento Local</h4>
                        <p>Descubrimos lugares únicos fuera de las rutas turísticas</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-sync-alt"></i>
                        <h4>Actualización Continua</h4>
                        <p>Revisamos y mejoramos constantemente nuestras rutas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> <!-- Fin de la sección de experiencia del equipo -->

<!-- ¿Por qué Candeivid? -->
<section class="section why-candeivid">
    <div class="why-background"></div>
    <div class="bg-overlay"></div>
    
    <div class="container candeivid-container">
        <div class="content-wrapper">
            <!-- Primera fila: Texto completo centrado -->
            <div class="top-section">
                <!-- Contenido principal -->
                <div class="content-main">
                    <div class="quality-badge">
                        <span>Calidad Garantizada</span>
                    </div>
                    
                    <h2 class="main-title">
                        ¿Por qué elegir 
                        <span class="highlight">Candeivid?</span>
                    </h2>
                    
                    <p class="main-description">
                        Somos moteros creando experiencias para moteros. Cada ruta que diseñamos nace de nuestra pasión por descubrir los mejores trazados, paisajes espectaculares y esos rincones especiales que solo se encuentran sobre dos ruedas.<br><br>
                        Nuestro compromiso es simple: llevarte por carreteras que realmente compensa el esfuerzo que requiere, con la confianza de que cada kilómetro ha sido cuidadosamente seleccionado para maximizar tu disfrute.
                    </p>
                </div>
                
                <!-- Puntos de garantía a la derecha -->
                <div class="content-side">
                    <div class="guarantee-points">
                        <div class="point-item">
                            <i class="fas fa-heart"></i>
                            <div class="point-content">
                                <h4>Pasión Auténtica</h4>
                                <p>Cada ruta refleja nuestro amor por la carretera y años de experiencia motera</p>
                            </div>
                        </div>
                        <div class="point-item">
                            <i class="fas fa-eye"></i>
                            <div class="point-content">
                                <h4>Cuidado en el Detalle</h4>
                                <p>Seleccionamos cada horquilla, cada mirador y cada parada con criterio experto</p>
                            </div>
                        </div>
                        <div class="point-item">
                            <i class="fas fa-star"></i>
                            <div class="point-content">
                                <h4>Experiencias Memorables</h4>
                                <p>Te llevamos por lugares que se quedarán grabados en tu memoria para siempre</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Nueva sección: Sin ediciones necesarias -->
            <div class="ready-section">
                <div class="ready-container">
                    <h3>Rutas Listas para Disfrutar</h3>
                    <p class="ready-description">
                        Olvídate de pasar horas editando rutas, buscando las mejores carreteras o reformulando trayectos. 
                        <strong>Nosotros ya hemos hecho todo ese trabajo por ti.</strong>
                    </p>
                    
                    <div class="services-grid-horizontal">
                        <div class="service-card">
                            <i class="fas fa-route"></i>
                            <h4>Trazados Perfectos</h4>
                            <p>Espectaculares recorridos ya incluidos y optimizados para máximo disfrute</p>
                        </div>
                        <div class="service-card">
                            <i class="fas fa-mountain"></i>
                            <h4>Paisajes Únicos</h4>
                            <p>Los mejores miradores y vistas seleccionados desde el primer día</p>
                        </div>
                        <div class="service-card">
                            <i class="fas fa-download"></i>
                            <h4>Listo para Usar</h4>
                            <p>Descarga inmediata sin planificación previa ni modificaciones</p>
                        </div>
                    </div>
                    
                    <div class="ready-highlight">
                        <i class="fas fa-magic"></i>
                        <p>Cada ruta está diseñada para ser perfecta desde el momento que la descargas. 
                        Sin modificaciones, sin pérdida de tiempo, solo pura diversión sobre tu moto.</p>
                    </div>
                </div>
            </div>
            
            <!-- Segunda fila: Proceso simplificado -->
            <div class="middle-section">
                <div class="process-simple">
                    <h3>Tu Aventura Comienza Aquí</h3>
                    <div class="steps-row">
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <p>Elige tu ruta favorita</p>
                        </div>
                        <div class="step-arrow">→</div>
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <p>Descarga el GPX optimizado</p>
                        </div>
                        <div class="step-arrow">→</div>
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <p>Disfruta de cada kilómetro</p>
                        </div>
                    </div>
                    <p class="process-note">Cada ruta te llevará por carreteras espectaculares seleccionadas especialmente para crear momentos inolvidables sobre tu moto.</p>
                </div>
            </div>
        </div>
    </div>
</section> <!-- Fin de la sección ¿Por qué Candeivid? -->

<!-- Cómo Funciona -->
<section id="como-funciona" class="section process">
    <div class="container">
        <h2 class="section-title">¿Cómo Funciona Nuestro Sistema?</h2>
        <p class="section-subtitle">Ofrecemos dos opciones flexibles para adaptarnos a tus necesidades</p>
        
        <div class="process-grid">
            <!-- Compra Individual Modificada -->
            <div class="process-card">
                <div class="process-header">
                    <i class="fas fa-route"></i>
                    <h3>Rutas Personalizables</h3>
                </div>
                <p>Adquiere rutas específicas y personalízalas según tus preferencias</p>
                
                <div class="process-option">
                    <h4>Características Principales:</h4>
                    <ul>
                        <li>Descarga del archivo GPX de alta precisión</li>
                        <li>Personaliza puntos de repostaje estratégicos</li>
                        <li>Sugerencias de hoteles con mejores valoraciones</li>
                        <li>Recomendaciones de restaurantes locales auténticos</li>
                        <li>Puntos de interés y fotográficos destacados</li>
                        <li>Asistencia básica para modificaciones de ruta</li>
                    </ul>
                    <div class="price-tag">Precio por ruta: Desde 5€</div>
                </div>
                
                <div class="process-note">
                    <p>¡Tú decides el nivel de detalle! Añade servicios extra a cualquier ruta</p>
                </div>
            </div>
            
            <!-- Nueva Planificación Personalizada -->
            <div class="process-card featured">
                <div class="process-header">
                    <i class="fas fa-concierge-bell"></i>
                    <h3>Planificación Premium</h3>
                </div>
                <p>Servicio completo para experiencias únicas</p>
                
                <div class="process-option">
                    <h4>Incluye todo de la versión básica más:</h4>
                    <ul>
                        <li>Diseño de ruta 100% personalizado</li>
                        <li>Reserva gestionada de hoteles y restaurantes</li>
                        <li>Coordinación de fechas específicas</li>
                        <li>Asesoramiento fotográfico profesional</li>
                        <li>Optimización de paradas técnicas</li>
                        <li>Asistencia de emergencia en ruta</li>
                        <li>Documentación de viaje personalizada</li>
                    </ul>
                    <div class="price-tag">Desde 90€/día (mínimo 2 días)</div>
                </div>
                
                <div class="process-note">
                    <p><i class="fas fa-envelope"></i> Servicio gestionado via email con nuestro equipo de expertos</p>
                    <a href="pages/contacto.php" class="btn btn-primary">Solicitar Presupuesto</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dificultad -->
<!-- Dificultad -->
<section class="section difficulty">
    <div class="container">
        <h2 class="section-title">Niveles de Dificultad</h2>
        <div class="grid">
            <div class="card difficulty-card novice">
                <span class="difficulty-badge">I</span>
                
                <div class="difficulty-top">
                    <i class="fas fa-motorcycle difficulty-icon"></i>
                </div>
                
                <div class="difficulty-center">
                    <!-- Espacio para apreciar la imagen de fondo -->
                </div>
                
                <div class="difficulty-bottom">
                    <h3>Piloto Nuevo</h3>
                </div>
            </div>
            
            <div class="card difficulty-card intermediate">
                <span class="difficulty-badge">II</span>
                
                <div class="difficulty-top">
                    <i class="fas fa-biking difficulty-icon"></i>                    
                </div>
                
                <div class="difficulty-center">
                    <!-- Espacio para apreciar la imagen de fondo -->
                </div>
                
                <div class="difficulty-bottom">
                    <h3>Domando Curvas</h3>
                </div>
            </div>
            
            <div class="card difficulty-card advanced">
                <span class="difficulty-badge">III</span>
                
                <div class="difficulty-top">
                    <i class="fas fa-tachometer-alt difficulty-icon"></i>
                </div>
                
                <div class="difficulty-center">
                    <!-- Espacio para apreciar la imagen de fondo -->
                </div>
                
                <div class="difficulty-bottom">
                    <h3>Maestro del Asfalto</h3>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Precios -->
<section id="precios" class="section pricing">
    <div class="container">
        <h2 class="section-title">Planes y Precios</h2>
        <div class="grid">
            <div class="card pricing-card">
                <h3>Rutas Básicas</h3>
                <p class="price">Desde <span>5€</span>/ruta</p>
                <ul>
                    <li>GPX + Sugerencias básicas</li>
                    <li>Personalización opcional</li>
                    <li>Descarga inmediata</li>
                </ul>
                <a href="pages/rutas.php" class="btn btn-outline">Comprar</a>
            </div>
            <div class="card pricing-card featured">
                <h3>Planificación Premium</h3>
                <p class="price">Desde <span>90€</span>/día</p>
                <ul>
                    <li>Servicio completo personalizado</li>
                    <li>Gestión de reservas incluida</li>
                    <li>Asistencia 24/7 durante el viaje</li>
                    <li>Documentación exclusiva</li>
                </ul>
                <a href="pages/contacto.php" class="btn btn-primary">Contactar</a>
            </div>
        </div>
    </div>
</section>


<!-- Testimonios -->
<section class="section testimonials">
    <div class="container">
        <h2 class="section-title">Experiencias de nuestros moteros</h2>
        <div class="testimonials-grid">
            <!-- Testimonio 1 -->
            <div class="testimonial-card">
                <div class="testimonial-author">
                    <img src="images/testimonios/motero1.png" alt="RoadWarrior87" class="author-avatar">
                    <div class="author-info">
                        <h4>RoadWarrior87</h4>
                        <p class="author-detail">BMW R 1250 GS</p>
                        <p class="author-stats">4 rutas completadas</p>
                    </div>
                </div>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <div class="testimonial-content">
                    <p class="testimonial-text">"Siempre me da mucha pereza ponerme a planificar una ruta. Me paso horas en el mapa y al final cuando salgo descubro que no era tan buen recorrido. Con Candeivid me ahorro todo eso. Bajo una ruta, la cargo en el Garmin y ya está, no tengo que hacer nada mas.</p>
                </div>
            </div>

            <!-- Testimonio 2 -->
            <div class="testimonial-card">
                <div class="testimonial-author">
                    <img src="images/testimonios/motero2.png" alt="CurvyRider" class="author-avatar">
                    <div class="author-info">
                        <h4>CurvyRider</h4>
                        <p class="author-detail">Ducati Monster</p>
                        <p class="author-stats">2 rutas completadas</p>
                    </div>
                </div>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <!-- <i class="fas fa-star-half-alt"></i> -->
                </div>
                <div class="testimonial-content">
                    <p class="testimonial-text">"Como motera que odia planificar, esto ha sido un salvavidas. Encontré lugares escondidos increíbles. ¡Mis compañeros de grupo ahora me consideran su guía!"</p>
                </div>
            </div>

            <!-- Testimonio 3 -->
            <div class="testimonial-card">
                <div class="testimonial-author">
                    <img src="images/testimonios/motero3.png" alt="IronButt" class="author-avatar">
                    <div class="author-info">
                        <h4>IronButt</h4>
                        <p class="author-detail">Honda CB650R</p>
                        <p class="author-stats">6 rutas completadas</p>
                    </div>
                </div>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <div class="testimonial-content">
                    <p class="testimonial-text">"Cada euro ha valido la pena. No tengo que pasarme horas planeando rutas, descartando caminos y haciendo zoom en el maps para ver si hay curvas o buenos paisajes. Ahora simplemente descargo la ruta, la pongo en mi TomTom y a correr."</p>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Place any per-page javascript here -->
<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
