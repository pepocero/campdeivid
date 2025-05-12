<?php

require_once $_SERVER['DOCUMENT_ROOT']."/ini_folder_camp.php";
require_once $_SERVER['DOCUMENT_ROOT'].$folder."/users/init.php";
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

//imprimir el url root
//echo $abs_us_root;


?>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/custom.css">

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1><span class="text-light">Rutas en Moto</span> <span class="text-accent">Planificadas al Detalle</span></h1>
                <p class="hero-subtitle">Descarga rutas en formato GPX, reserva hoteles y disfruta de viajes sin preocupaciones.</p>
                <a href="#rutas" class="btn btn-primary">Explorar Rutas</a>
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

     <!-- Cómo Funciona -->
<section id="como-funciona" class="section process">
    <div class="container">
        <h2 class="section-title">¿Cómo Funciona Nuestro Sistema?</h2>
        <p class="section-subtitle">Te ofrecemos un modelo híbrido para disfrutar de nuestras rutas y servicios exclusivos</p>
        
        <div class="process-grid">
            <!-- Compra Individual -->
            <div class="process-card">
                <div class="process-header">
                    <i class="fas fa-route"></i>
                    <h3>Compra Individual de Rutas</h3>
                </div>
                <p>Adquiere acceso a rutas específicas sin necesidad de suscripción</p>
                
                <div class="process-option">
                    <h4>Ruta con Asistencia Completa</h4>
                    <p>Ideal para una experiencia planificada sin complicaciones. Incluye:</p>
                    <ul>
                        <li>Descarga del archivo GPX de la ruta</li>
                        <li>Asistencia para reserva de hasta 2 hoteles</li>
                        <li>Asistencia para reserva de hasta 2 restaurantes</li>
                        <li>Sugerencias detalladas de puntos de interés</li>
                        <li>Soporte básico para dudas sobre la ruta</li>
                    </ul>
                    <div class="price-tag">Precio: Variable por ruta (€60 - €200)</div>
                </div>
                
                <div class="process-option">
                    <h4>Ruta con Sugerencias</h4>
                    <p>Para quienes prefieren gestionar sus propias reservas pero quieren orientación. Incluye:</p>
                    <ul>
                        <li>Descarga del archivo GPX de la ruta</li>
                        <li>Sugerencias de hoteles y restaurantes</li>
                        <li>Soporte básico para dudas sobre la ruta</li>
                    </ul>
                    <div class="price-tag">Precio: Variable por ruta (€40 - €100)</div>
                </div>
                
                <div class="process-note">
                    <p>Nota: Las rutas marcadas como "Gratis" permiten descarga del GPX sin coste e incluyen sugerencias básicas</p>
                </div>
            </div>
            
            <!-- Suscripción Premium -->
            <div class="process-card featured">
                <div class="process-header">
                    <i class="fas fa-crown"></i>
                    <h3>Suscripción Premium Anual</h3>
                </div>
                <p>La experiencia completa para el motero apasionado</p>
                
                <h4>Desbloquea todos los beneficios:</h4>
                <ul>
                    <li>Acceso y descarga GPX de todas nuestras rutas</li>
                    <li>Información detallada de Puntos de Interés</li>
                    <li>Modificaciones y paradas personalizadas</li>
                    <li>Planificación de paradas de repostaje</li>
                    <li>Planificación flexible de comidas</li>
                    <li>Descuentos exclusivos en colaboradores</li>
                    <li>Asistencia en viaje básica</li>
                    <li>Atención personalizada prioritaria</li>
                    <li>Acceso anticipado a nuevas rutas</li>
                    <li>Contenido exclusivo y comunidad premium</li>
                    <li>Consultoría de viaje anual</li>
                    <li>Prioridad en eventos grupales</li>
                </ul>
                
                <div class="price-tag">Precio: 299€/año</div>
                <a href="#suscribirse" class="btn btn-primary">Suscribirse Ahora</a>
            </div>
        </div>
    </div>
</section>

    <!-- Dificultad -->
    <section class="section difficulty">
        <div class="container">
            <h2 class="section-title">Niveles de Dificultad</h2>
            <div class="grid">
                <div class="card difficulty-card novice">
                    <h3>Novato</h3>
                    <p>Rutas sencillas, ideales para principiantes.</p>
                </div>
                <div class="card difficulty-card intermediate">
                    <h3>Intermedio</h3>
                    <p>Curvas técnicas y tramos variados.</p>
                </div>
                <div class="card difficulty-card advanced">
                    <h3>Avanzado</h3>
                    <p>Para expertos: carreteras técnicas y desafiantes.</p>
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
                    <h3>Compra Individual</h3>
                    <p class="price">Desde <span>15€</span>/ruta</p>
                    <ul>
                        <li>Ruta en formato GPX</li>
                        <li>Nivel de dificultad a elegir</li>
                        <li>Descarga inmediata</li>
                    </ul>
                    <a href="#" class="btn btn-outline">Comprar</a>
                </div>
                <div class="card pricing-card featured">
                    <h3>Suscripción Anual</h3>
                    <p class="price">299€<span>/año</span></p>
                    <ul>
                        <li>Acceso a todas las rutas</li>
                        <li>Reservas con descuento</li>
                        <li>Soporte prioritario</li>
                        <li>Planificación personalizada</li>
                    </ul>
                    <a href="#" class="btn btn-primary">Suscribirse</a>
                </div>
                <div class="card pricing-card">
                    <h3>Servicio Premium</h3>
                    <p class="price">Desde <span>100€</span>/día</p>
                    <ul>
                        <li>Ruta + hotel + restaurante</li>
                        <li>Planificación personalizada</li>
                        <li>Paradas de combustible</li>
                        <li>Asistencia en viaje básica</li>
                        <li>Sugerencias detalladas de puntos de interés</li>
                        <li>Soporte básico para dudas sobre la ruta</li>

                    </ul>
                    <a href="#" class="btn btn-outline">Saber Más</a>
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
                    <img src="../images/testimonios/motero1.png" alt="RoadWarrior87" class="author-avatar">
                    <div class="author-info">
                        <h4>RoadWarrior87</h4>
                        <p class="author-detail">Usuario Premium | BMW R 1250 GS</p>
                        <p class="author-stats">24 rutas completadas</p>
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
                    <p class="testimonial-text">"Las rutas de Camp Deivid son mi Biblia para los fines de semana. La planificación de paradas es tan precisa que nunca me quedé sin combustible, ¡incluso en las carreteras más remotas!"</p>
                </div>
            </div>

            <!-- Testimonio 2 -->
            <div class="testimonial-card">
                <div class="testimonial-author">
                    <img src="../images/testimonios/motero2.png" alt="CurvyRider" class="author-avatar">
                    <div class="author-info">
                        <h4>CurvyRider</h4>
                        <p class="author-detail">Usuario Básico | Ducati Monster</p>
                        <p class="author-stats">8 rutas completadas</p>
                    </div>
                </div>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <div class="testimonial-content">
                    <p class="testimonial-text">"Como motera que odia planificar, esto ha sido un salvavidas. Encontré restaurantes escondidos que ni los locales conocían. ¡Mis compañeros de grupo ahora me consideran su guía!"</p>
                </div>
            </div>

            <!-- Testimonio 3 -->
            <div class="testimonial-card">
                <div class="testimonial-author">
                    <img src="../images/testimonios/motero3.png" alt="IronButt" class="author-avatar">
                    <div class="author-info">
                        <h4>IronButt</h4>
                        <p class="author-detail">Suscriptor VIP | Honda Gold Wing</p>
                        <p class="author-stats">56 rutas completadas</p>
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
                    <p class="testimonial-text">"3 años con la suscripción premium y cada euro valió la pena. Las rutas personalizadas para mi Gold Wing hacen que cada viaje sea una experiencia de lujo sobre dos ruedas."</p>
                </div>
            </div>
        </div>
    </div>
</section>


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">CAMP DEIVID</div>
                <div class="footer-links">
                    <a href="#">Términos y Condiciones</a>
                    <a href="#">Política de Privacidad</a>
                    <a href="#">Contacto</a>
                </div>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <p class="copyright">© 2023 Camp Deivid. Todos los derechos reservados.</p>
        </div>
    </footer>
