<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>

<!-- Meta tags específicos para FAQ -->
<meta name="description" content="Preguntas frecuentes sobre Candeivid - Todo lo que necesitas saber sobre nuestras rutas en moto, descargas GPX, pagos y más.">
<meta name="keywords" content="FAQ, preguntas frecuentes, rutas moto, GPS, GPX, Candeivid, ayuda, soporte">

<!-- Open Graph para redes sociales -->
<meta property="og:title" content="FAQ - Preguntas Frecuentes | Candeivid">
<meta property="og:description" content="Resuelve todas tus dudas sobre rutas en moto, descargas GPX, pagos y más en Candeivid">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= $us_url_root ?>pages/faq.php">

<style>
/* ===== ESTILOS PARA FAQ ===== */
.faq-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.faq-header {
    text-align: center;
    margin-bottom: 3rem;
    padding: 2rem;
    background: linear-gradient(135deg, #820f2f 0%, #ff6b00 100%);
    color: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 123, 255, 0.3);
}

.faq-header h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.faq-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.faq-search {
    margin-bottom: 2rem;
}

.faq-search input {
    border-radius: 25px;
    padding: 1rem 1.5rem;
    font-size: 1rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.faq-search input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.faq-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.faq-category {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.faq-category:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: #820f2f;
}

.faq-category.active {
    border-color: #820f2f;
    background: #f8f9fa;
}

.category-icon {
    font-size: 2.5rem;
    color: #820f2f;
    margin-bottom: 1rem;
    display: block;
}

.category-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
}

.category-count {
    color: #6c757d;
    font-size: 0.9rem;
}

.faq-section {
    margin-bottom: 2rem;
}

.faq-section-title {
    font-size: 1.5rem;
    color: #820f2f;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #820f2f;
    font-weight: 600;
}

.faq-item {
    background: white;
    border-radius: 8px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.faq-item:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.faq-question {
    padding: 1.5rem;
    cursor: pointer;
    font-weight: 600;
    color: #333;
    background: white;
    border: none;
    width: 100%;
    text-align: left;
    font-size: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.faq-question:hover {
    background: #f8f9fa;
    color: #820f2f;
}

.faq-question.active {
    background: #820f2f;
    color: white;
}

.faq-toggle {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.faq-question.active .faq-toggle {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 1.5rem;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #f8f9fa;
    color: #555;
}

.faq-answer.active {
    padding: 1.5rem;
    max-height: 500px;
}

.faq-answer p {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.faq-answer ul {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.faq-answer li {
    margin-bottom: 0.5rem;
    line-height: 1.5;
}

.highlight {
    background: #fff3cd;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-weight: 600;
    color: #856404;
}

.contact-cta {
    background: linear-gradient(135deg, #ff6b00 0%, #ff6b0f 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    margin-top: 3rem;
    box-shadow: 0 4px 20px rgba(40, 167, 69, 0.3);
}

.contact-cta h3 {
    margin-bottom: 1rem;
    font-weight: 600;
}

.btn-contact {
    background: white;
    color: #28a745;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-contact:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .faq-header h1 {
        font-size: 2rem;
    }
    
    .faq-categories {
        grid-template-columns: 1fr;
    }
    
    .faq-question {
        padding: 1rem;
        font-size: 0.95rem;
    }
    
    .faq-answer.active {
        padding: 1rem;
    }
}

/* ===== ANIMACIONES ===== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.faq-item {
    animation: fadeIn 0.5s ease-out;
}

/* ===== BÚSQUEDA DESTACADA ===== */
.search-highlight {
    background: #ffeb3b;
    padding: 0.2rem;
    border-radius: 3px;
    font-weight: bold;
}
</style>

<div class="faq-container">
    <!-- Header -->
    <div class="faq-header">
        <h1><i class="fas fa-question-circle"></i> Preguntas Frecuentes</h1>
        <p>Encuentra respuestas rápidas a las dudas más comunes sobre Candeivid</p>
    </div>

    <!-- Búsqueda -->
    <div class="faq-search">
        <input type="text" class="form-control" id="faqSearch" placeholder="🔍 Busca tu pregunta aquí...">
    </div>

    <!-- Categorías -->
    <div class="faq-categories">
        <div class="faq-category" data-category="general">
            <i class="fas fa-info-circle category-icon"></i>
            <div class="category-title">General</div>
            <div class="category-count">8 preguntas</div>
        </div>
        <div class="faq-category" data-category="rutas">
            <i class="fas fa-route category-icon"></i>
            <div class="category-title">Rutas y GPX</div>
            <div class="category-count">12 preguntas</div>
        </div>
        <div class="faq-category" data-category="pagos">
            <i class="fas fa-credit-card category-icon"></i>
            <div class="category-title">Pagos y Premium</div>
            <div class="category-count">8 preguntas</div>
        </div>
        <div class="faq-category" data-category="tecnico">
            <i class="fas fa-cog category-icon"></i>
            <div class="category-title">Técnico</div>
            <div class="category-count">6 preguntas</div>
        </div>
    </div>

    <!-- FAQ Content -->
    <div id="faqContent">
        
        <!-- SECCIÓN GENERAL -->
        <div class="faq-section" data-category="general">
            <h2 class="faq-section-title"><i class="fas fa-info-circle"></i> General</h2>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué es Candeivid?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Candeivid es una plataforma creada <span class="highlight">por moteros para moteros</span>, especializada en ofrecer rutas en motocicleta cuidadosamente diseñadas y verificadas.</p>
                    <p>Nuestro compromiso es llevarte por carreteras que realmente compensa el esfuerzo que requiere, con la confianza de que cada kilómetro ha sido seleccionado para maximizar tu disfrute sobre dos ruedas.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué diferencia a Candeivid de otras apps de rutas?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>A diferencia de las aplicaciones automáticas, nuestras rutas son:</p>
                    <ul>
                        <li><strong>Diseñadas por expertos motociclistas</strong> con años de experiencia</li>
                        <li><strong>Recorridas y verificadas</strong> múltiples veces antes de ser publicadas</li>
                        <li><strong>Categorizadas por nivel</strong> según tu experiencia de conducción</li>
                        <li><strong>Optimizadas para el disfrute</strong>, no solo para llegar rápido</li>
                        <li><strong>Incluyen información detallada</strong> sobre puntos de interés y características de la ruta</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Necesito registrarme para usar Candeivid?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Puedes explorar nuestras rutas sin registrarte, pero <span class="highlight">necesitas crear una cuenta gratuita</span> para:</p>
                    <ul>
                        <li>Descargar archivos GPX de rutas gratuitas</li>
                        <li>Comprar rutas premium</li>
                        <li>Aplicar cupones de descuento</li>
                        <li>Acceder a tu historial de descargas</li>
                    </ul>
                    <p>El registro es <strong>completamente gratuito</strong> y solo toma unos segundos.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Candeivid es gratis?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Candeivid ofrece <span class="highlight">ambas opciones</span>:</p>
                    <ul>
                        <li><strong>Rutas Gratuitas:</strong> Puedes descargar y disfrutar sin coste alguno</li>
                        <li><strong>Rutas Premium:</strong> Rutas más elaboradas y exclusivas con precio accesible</li>
                    </ul>
                    <p>Todas las rutas, tanto gratuitas como premium, mantienen el mismo estándar de calidad y verificación.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué tipos de motocicleta son adecuadas para las rutas?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Nuestras rutas están diseñadas principalmente para <span class="highlight">motocicletas de carretera</span>:</p>
                    <ul>
                        <li>Deportivas y supersportivas</li>
                        <li>Naked y streetfighter</li>
                        <li>Touring y sport-touring</li>
                        <li>Custom y cruiser (en rutas apropiadas)</li>
                        <li>Adventure (en rutas de asfalto)</li>
                    </ul>
                    <p>Cada ruta especifica su nivel de dificultad y tipo de carreteras para que elijas la más adecuada para tu motocicleta.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Las rutas están disponibles en toda España?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Actualmente ofrecemos rutas en <span class="highlight">diversas regiones de España</span>, con especial enfoque en:</p>
                    <ul>
                        <li>Cataluña y Pirineos</li>
                        <li>Sistema Central (Madrid, Ávila, Segovia)</li>
                        <li>Andalucía (sierras y costas)</li>
                        <li>Valencia y Castellón</li>
                        <li>Asturias y Cantabria</li>
                    </ul>
                    <p>Constantemente añadimos nuevas rutas en diferentes regiones. ¡Suscríbete para recibir novedades!</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Cómo puedo sugerir una ruta o colaborar?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>¡Nos encanta recibir sugerencias de la comunidad motera! Puedes colaborar:</p>
                    <ul>
                        <li>Enviando sugerencias de rutas a través de nuestro formulario de contacto</li>
                        <li>Compartiendo fotos de rutas que hayas realizado</li>
                        <li>Reportando cambios en carreteras o nuevos puntos de interés</li>
                        <li>Dejando comentarios y valoraciones en nuestras redes sociales</li>
                    </ul>
                    <p>Todas las contribuciones son revisadas por nuestro equipo y, si son aprobadas, ¡podrías recibir cupones de descuento!</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Hay alguna comunidad o grupo de Candeivid?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Sí! Mantente conectado con otros moteros a través de:</p>
                    <ul>
                        <li><strong>Redes sociales:</strong> Síguenos en Instagram y Facebook para ver fotos de rutas y novedades</li>
                        <li><strong>Newsletter:</strong> Recibe rutas nuevas y ofertas especiales</li>
                        <li><strong>Compartir rutas:</strong> Usa nuestro botón de compartir para organizar salidas con amigos</li>
                    </ul>
                    <p>Próximamente lanzaremos un foro y sistema de quedadas integrado en la plataforma.</p>
                </div>
            </div>
        </div>

        <!-- SECCIÓN RUTAS Y GPX -->
        <div class="faq-section" data-category="rutas">
            <h2 class="faq-section-title"><i class="fas fa-route"></i> Rutas y Archivos GPX</h2>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué es un archivo GPX y para qué sirve?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Un archivo <span class="highlight">GPX (GPS eXchange format)</span> es un formato estándar que contiene coordenadas GPS de una ruta.</p>
                    <p><strong>Te permite:</strong></p>
                    <ul>
                        <li>Cargar la ruta en tu navegador GPS (Garmin, TomTom, etc.)</li>
                        <li>Usarla en apps móviles como Google Maps, Waze, Calimoto</li>
                        <li>Seguir la ruta exacta sin perderte</li>
                        <li>Ver puntos de interés marcados en el recorrido</li>
                    </ul>
                    <p>Es como tener un guía experto que te lleva por el camino perfecto.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Cómo descargo y uso un archivo GPX?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p><strong>Para descargar:</strong></p>
                    <ol>
                        <li>Inicia sesión en tu cuenta</li>
                        <li>Ve a la ruta que quieres descargar</li>
                        <li>Haz clic en "Descargar GPX" (rutas gratuitas) o compra la ruta premium</li>
                        <li>El archivo se guardará en tu dispositivo</li>
                    </ol>
                    <p><strong>Para usar:</strong></p>
                    <ul>
                        <li><strong>En tu GPS:</strong> Copia el archivo a la carpeta de rutas del dispositivo</li>
                        <li><strong>En el móvil:</strong> Ábrelo con apps como Calimoto, Google Maps, Wikiloc</li>
                        <li><strong>En ordenador:</strong> Úsalo para planificar con software como BaseCamp</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué significan los niveles de las rutas?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Clasificamos las rutas según el nivel de experiencia requerido:</p>
                    <ul>
                        <li><strong>Piloto Nuevo:</strong> Rutas sencillas, carreteras amplias, pocas curvas técnicas. Ideal para principiantes</li>
                        <li><strong>Domando Curvas:</strong> Nivel intermedio, más curvas y carreteras secundarias. Para moteros con experiencia básica</li>
                        <li><strong>Experto:</strong> Rutas técnicas, curvas cerradas, puertos de montaña. Solo para moteros experimentados</li>
                    </ul>
                    <p>Esta clasificación te ayuda a elegir rutas acordes a tu nivel y disfrutar con seguridad.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué información incluye cada ruta?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Cada ruta de Candeivid incluye:</p>
                    <ul>
                        <li><strong>Descripción detallada:</strong> Características del recorrido y qué esperar</li>
                        <li><strong>Distancia y tiempo estimado:</strong> Para planificar tu salida</li>
                        <li><strong>Nivel de dificultad:</strong> Según tu experiencia de conducción</li>
                        <li><strong>Tipo de paisaje:</strong> Montaña, costa, rural, urbano</li>
                        <li><strong>Galería de imágenes:</strong> Fotos reales de la ruta</li>
                        <li><strong>Puntos de interés:</strong> Miradores, restaurantes, gasolineras</li>
                        <li><strong>Consejos específicos:</strong> Mejores horarios, precauciones, etc.</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Las rutas se actualizan si cambian las carreteras?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p><span class="highlight">Sí, mantenemos nuestras rutas actualizadas</span>. Nuestro proceso incluye:</p>
                    <ul>
                        <li>Revisiones periódicas de todas las rutas</li>
                        <li>Actualización inmediata cuando hay obras o cambios importantes</li>
                        <li>Verificación de nuevos puntos de interés</li>
                        <li>Mejoras basadas en feedback de usuarios</li>
                    </ul>
                    <p>Si encuentras algún cambio en una ruta, por favor repórtalo a través de nuestro formulario de contacto.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Puedo usar las rutas sin conexión a internet?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p><strong>¡Absolutamente!</strong> Los archivos GPX funcionan sin conexión:</p>
                    <ul>
                        <li><strong>En GPS dedicados:</strong> Funcionan completamente offline</li>
                        <li><strong>En móviles:</strong> Descarga mapas offline en apps como Google Maps o Calimoto</li>
                        <li><strong>Recomendación:</strong> Siempre descarga los mapas de la zona antes de salir</li>
                    </ul>
                    <p>Esto es especialmente útil en zonas de montaña donde la cobertura puede ser limitada.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué apps móviles recomendáis para usar con vuestros GPX?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Recomendamos estas apps por su compatibilidad y funciones moteras:</p>
                    <ul>
                        <li><strong>Kurviger:</strong> Especializada en motos, muy buena para seguir rutas</li>
                        <li><strong>Google Maps:</strong> Excelente navegación y disponible offline</li>
                        <li><strong>TomTom:</strong> Ideal para navegar y modificar rutas</li>
                        <li><strong>OsmAnd:</strong> Completamente offline y muy detallada</li>
                        <li><strong>Waze:</strong> Buena para evitar tráfico y radares</li>
                    </ul>
                    <p>La mayoría son gratuitas y todas aceptan archivos GPX.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Incluís información sobre gasolineras y restaurantes?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Sí, nuestras rutas premium incluyen <span class="highlight">puntos de interés cuidadosamente seleccionados</span>:</p>
                    <ul>
                        <li><strong>Gasolineras:</strong> Ubicadas estratégicamente según la autonomía media de motos</li>
                        <li><strong>Restaurantes:</strong> Locales recomendados, especialmente aquellos "moto-friendly"</li>
                        <li><strong>Miradores:</strong> Los mejores puntos para hacer fotos y descansar</li>
                        <li><strong>Puntos de interés cultural:</strong> Castillos, monumentos, pueblos con encanto</li>
                    </ul>
                    <p>Esta información está integrada en el archivo GPX y aparecerá en tu navegador.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué hago si me pierdo siguiendo una ruta?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p><strong>No te preocupes, es normal:</strong></p>
                    <ol>
                        <li><strong>Mantén la calma</strong> y busca un lugar seguro para parar</li>
                        <li><strong>Consulta tu app de navegación</strong> - la mayoría recalculan automáticamente</li>
                        <li><strong>Usa Google Maps</strong> como respaldo para volver a la ruta</li>
                        <li><strong>Busca referencias</strong> mencionadas en la descripción de la ruta</li>
                    </ol>
                    <p><strong>Prevención:</strong> Familiarízate con la ruta antes de salir y lleva siempre un respaldo de navegación.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Puedo modificar o personalizar las rutas descargadas?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p><span class="highlight">¡Por supuesto!</span> Una vez descargado el GPX, puedes:</p>
                    <ul>
                        <li><strong>Añadir paradas:</strong> Agregar puntos de interés personales</li>
                        <li><strong>Modificar el recorrido:</strong> Cambiar algún tramo según tus preferencias</li>
                        <li><strong>Combinar rutas:</strong> Unir varias rutas nuestras en una sola salida</li>
                        <li><strong>Crear variantes:</strong> Adaptarla a tu grupo o tipo de moto</li>
                    </ul>
                    <p>Recomendamos usar software como BaseCamp (Garmin) o Tyre, o aplicaciones online como Kurviger, TomTom, para las modificaciones.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Tenéis rutas específicas para grupos o eventos?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Ofrecemos rutas adecuadas para diferentes tipos de salidas:</p>
                    <ul>
                        <li><strong>Rutas familiares:</strong> Distancias cortas, nivel principiante</li>
                        <li><strong>Rutas de club:</strong> Distancias medias, paradas estratégicas</li>
                        <li><strong>Rutas deportivas:</strong> Para grupos experimentados, más técnicas</li>
                        <li><strong>Rutas turísticas:</strong> Con múltiples puntos de interés</li>
                    </ul>
                    <p>Para eventos especiales o grupos grandes, contacta con nosotros para asesoramiento personalizado.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Cómo sé si una ruta es adecuada para la época del año?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>En la descripción de cada ruta incluimos información estacional:</p>
                    <ul>
                        <li><strong>Rutas de montaña:</strong> Indicamos si hay riesgo de nieve o hielo</li>
                        <li><strong>Rutas costeras:</strong> Mejor época para evitar aglomeraciones</li>
                        <li><strong>Puertos de montaña:</strong> Fechas de apertura/cierre</li>
                        <li><strong>Consejos meteorológicos:</strong> Épocas de mayor riesgo de lluvia</li>
                    </ul>
                    <p><strong>Siempre</strong> consulta el tiempo antes de salir y adapta la ruta a las condiciones actuales.</p>
                </div>
            </div>
        </div>

        <!-- SECCIÓN PAGOS Y PREMIUM -->
        <div class="faq-section" data-category="pagos">
            <h2 class="faq-section-title"><i class="fas fa-credit-card"></i> Pagos y Rutas Premium</h2>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Cuál es la diferencia entre rutas gratuitas y premium?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p><strong>Ambas mantienen nuestro estándar de calidad</strong>, pero las rutas premium ofrecen:</p>
                    <p><strong>Rutas Premium incluyen:</strong></p>
                    <ul>
                        <li>Planificación más detallada y exclusiva</li>
                        <li>Track GPS de alta precisión optimizado para conducción</li>
                        <li>Descripción completa con puntos de interés específicos</li>
                        <li>Verificación y recorrido múltiple por expertos</li>
                        <li>Soporte postventa para resolver dudas</li>
                        <li>Galería fotográfica más extensa</li>
                    </ul>
                    <p><strong>Rutas Gratuitas incluyen:</strong></p>
                    <ul>
                        <li>Track GPX listo para usar en tu dispositivo GPS</li>
                        <li>Información básica de la ruta</li>
                        <li>Ruta verificada y categorizada por nivel</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué métodos de pago aceptáis?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Aceptamos pagos a través de <span class="highlight">PayPal</span>, que incluye:</p>
                    <ul>
                        <li><strong>Tarjetas de crédito:</strong> Visa, MasterCard, American Express</li>
                        <li><strong>Tarjetas de débito</strong> de bancos españoles</li>
                        <li><strong>Cuenta PayPal:</strong> Si ya tienes una configurada</li>
                        <li><strong>Financiación PayPal:</strong> Para compras elegibles</li>
                    </ul>
                    <p>PayPal garantiza la <strong>seguridad de tus datos</strong> y ofrece protección al comprador.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Los precios incluyen IVA?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p><span class="highlight">Sí, todos nuestros precios incluyen IVA</span> (21% en España).</p>
                    <p>Lo que ves en pantalla es exactamente lo que pagarás, sin costes adicionales ocultos.</p>
                    <p>Recibirás una factura digital automáticamente en tu email tras completar la compra.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Cómo funcionan los cupones de descuento?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Los cupones se pueden aplicar durante el proceso de compra:</p>
                    <ol>
                        <li>Selecciona la ruta premium que quieres comprar</li>
                        <li>En la página de compra, busca la sección "¿Tienes un cupón?"</li>
                        <li>Introduce tu código de cupón</li>
                        <li>Haz clic en "Aplicar" para ver el descuento</li>
                        <li>Procede con el pago del precio final</li>
                    </ol>
                    <p><strong>Los cupones pueden ser:</strong></p>
                    <ul>
                        <li>Porcentaje de descuento (ej: 20% OFF)</li>
                        <li>Cantidad fija de descuento (ej: 5€ menos)</li>
                        <li>Válidos por tiempo limitado</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Dónde puedo conseguir cupones de descuento?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Ofrecemos cupones a través de varios canales:</p>
                    <ul>
                        <li><strong>Newsletter:</strong> Suscríbete para recibir ofertas exclusivas</li>
                        <li><strong>Redes sociales:</strong> Síguenos en Instagram y Facebook</li>
                        <li><strong>Colaboraciones:</strong> Con clubs de motos y influencers</li>
                        <li><strong>Eventos especiales:</strong> Ferias de moto, fechas señaladas</li>
                        <li><strong>Programa de fidelidad:</strong> Para usuarios habituales</li>
                        <li><strong>Referidos:</strong> Comparte Candeivid y obtén descuentos</li>
                    </ul>
                    <p>¡Mantente atento a nuestras comunicaciones para no perderte ninguna oferta!</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Qué pasa si no funciona mi cupón de descuento?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Si tu cupón no funciona, verifica:</p>
                    <ul>
                        <li><strong>Fecha de validez:</strong> Algunos cupones expiran</li>
                        <li><strong>Condiciones:</strong> Monto mínimo de compra o rutas específicas</li>
                        <li><strong>Mayúsculas/minúsculas:</strong> Introduce el código exactamente como aparece</li>
                        <li><strong>Un solo uso:</strong> Algunos cupones son válidos una sola vez</li>
                    </ul>
                    <p>Si sigue sin funcionar, contacta con nosotros enviando una captura del cupón y te ayudaremos inmediatamente.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Puedo devolver una ruta si no me gusta?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Ofrecemos <span class="highlight">garantía de satisfacción</span>:</p>
                    <ul>
                        <li><strong>7 días</strong> desde la compra para solicitar devolución</li>
                        <li><strong>Motivos válidos:</strong> Error en la descripción, ruta impracticable, problemas técnicos</li>
                        <li><strong>Proceso simple:</strong> Contacta con nosotros explicando el motivo</li>
                        <li><strong>Reembolso completo</strong> en 24-48 horas hábiles</li>
                    </ul>
                    <p>Nuestro objetivo es tu satisfacción total. Si hay algún problema, lo solucionamos.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Recibo factura por mi compra?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p><span class="highlight">Sí, automáticamente</span> tras completar tu compra:</p>
                    <ul>
                        <li>Recibes una <strong>factura digital</strong> en tu email</li>
                        <li>Incluye todos los <strong>datos fiscales necesarios</strong></li>
                        <li>Válida para <strong>gastos de empresa</strong> o autónomos</li>
                        <li>Guardamos una copia en nuestro sistema para futuras consultas</li>
                    </ul>
                    <p>Si necesitas modificar algún dato fiscal o no recibes la factura, contacta con nosotros.</p>
                </div>
            </div>
        </div>

        <!-- SECCIÓN TÉCNICO -->
        <div class="faq-section" data-category="tecnico">
            <h2 class="faq-section-title"><i class="fas fa-cog"></i> Soporte Técnico</h2>

            <div class="faq-item">
                <button class="faq-question">
                    No puedo descargar el archivo GPX, ¿qué hago?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Prueba estos pasos para solucionar el problema:</p>
                    <ol>
                        <li><strong>Verifica tu sesión:</strong> Asegúrate de estar logueado</li>
                        <li><strong>Actualiza la página</strong> y vuelve a intentar</li>
                        <li><strong>Prueba otro navegador</strong> (Chrome, Firefox, Safari)</li>
                        <li><strong>Desactiva bloqueadores</strong> de anuncios temporalmente</li>
                        <li><strong>Verifica tu conexión</strong> a internet</li>
                    </ol>
                    <p>Si el problema persiste, contacta con nosotros indicando:</p>
                    <ul>
                        <li>Navegador y versión que usas</li>
                        <li>Dispositivo (móvil, tablet, ordenador)</li>
                        <li>Mensaje de error que aparece</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    El archivo GPX no se abre en mi dispositivo
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Los archivos GPX son compatibles con la mayoría de dispositivos, pero si tienes problemas:</p>
                    <p><strong>En móviles:</strong></p>
                    <ul>
                        <li>Instala una app compatible (Calimoto, Google Maps, TomTom GO, Kurviger...)</li>
                        <li>Abre el archivo directamente desde la app, no desde el explorador</li>
                        <li>En Android, toca el archivo y selecciona "Abrir con..."</li>
                    </ul>
                    <p><strong>En GPS dedicados:</strong></p>
                    <ul>
                        <li>Copia el archivo a la carpeta "GPX" o "Rutas" del dispositivo</li>
                        <li>Consulta el manual de tu GPS para la ubicación exacta</li>
                        <li>Algunos GPS requieren conversión a formatos específicos</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    Las imágenes de la galería no cargan
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Si las imágenes no se muestran correctamente:</p>
                    <ul>
                        <li><strong>Espera unos segundos:</strong> Las imágenes de alta calidad pueden tardar en cargar</li>
                        <li><strong>Actualiza la página</strong> (F5 o Ctrl+R)</li>
                        <li><strong>Verifica tu conexión:</strong> Imágenes grandes necesitan buena conexión</li>
                        <li><strong>Desactiva VPN</strong> si estás usando una</li>
                        <li><strong>Prueba en modo incógnito</strong> del navegador</li>
                    </ul>
                    <p>Las imágenes están optimizadas para móviles, pero conexiones muy lentas pueden afectar la carga.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Candeivid funciona en todos los navegadores?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Candeivid está optimizado para los navegadores más populares:</p>
                    <p><strong>Totalmente compatible:</strong></p>
                    <ul>
                        <li>Google Chrome (recomendado)</li>
                        <li>Firefox</li>
                        <li>Safari (macOS e iOS)</li>
                        <li>Microsoft Edge</li>
                    </ul>
                    <p><strong>Funcionalidad limitada:</strong></p>
                    <ul>
                        <li>Internet Explorer (no recomendado)</li>
                        <li>Navegadores muy antiguos</li>
                    </ul>
                    <p>Para la mejor experiencia, recomendamos mantener tu navegador actualizado.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Cómo puedo reportar un error o problema?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Puedes reportar problemas a través de varios canales:</p>
                    <p><strong>Información útil a incluir:</strong></p>
                    <ul>
                        <li>Descripción detallada del problema</li>
                        <li>Pasos que seguiste cuando ocurrió</li>
                        <li>Navegador y versión</li>
                        <li>Dispositivo (móvil, tablet, PC)</li>
                        <li>Capturas de pantalla si es posible</li>
                    </ul>
                    <p><strong>Canales de contacto:</strong></p>
                    <ul>
                        <li>Formulario de contacto en la web</li>
                        <li>Email directo (se proporciona en contacto)</li>
                        <li>Redes sociales para problemas urgentes</li>
                    </ul>
                    <p>Respondemos en menos de 24 horas hábiles.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    ¿Tenéis aplicación móvil nativa?
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </button>
                <div class="faq-answer">
                    <p>Actualmente <span class="highlight">no tenemos app nativa</span>, pero nuestra web está completamente optimizada para móviles:</p>
                    <p><strong>Ventajas de la web móvil:</strong></p>
                    <ul>
                        <li>Se adapta perfectamente a tu pantalla</li>
                        <li>Siempre actualizada con las últimas rutas</li>
                        <li>No ocupa espacio en tu dispositivo</li>
                        <li>Compatible con todos los sistemas operativos</li>
                    </ul>
                    <p><strong>Puedes crear un acceso directo:</strong></p>
                    <ul>
                        <li>En iOS: Abre Safari → Compartir → "Añadir a pantalla de inicio"</li>
                        <li>En Android: Abre Chrome → Menú → "Añadir a pantalla de inicio"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA de contacto -->
    <div class="contact-cta">
        <h3><i class="fas fa-question-circle"></i> ¿No encuentras la respuesta que buscas?</h3>
        <p>Nuestro equipo está aquí para ayudarte. Contacta con nosotros y te responderemos lo antes posible.</p>
        <a href="contacto.php" class="btn btn-contact">
            <i class="fas fa-envelope"></i> Contactar con Soporte
        </a>
    </div>
</div>

<!-- JavaScript para funcionalidad interactiva -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ toggle functionality
    const faqQuestions = document.querySelectorAll('.faq-question');
    const faqSearch = document.getElementById('faqSearch');
    const faqCategories = document.querySelectorAll('.faq-category');
    
    // Toggle FAQ items
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const isActive = this.classList.contains('active');
            
            // Close all other FAQs
            faqQuestions.forEach(q => {
                q.classList.remove('active');
                q.nextElementSibling.classList.remove('active');
            });
            
            // Toggle current FAQ
            if (!isActive) {
                this.classList.add('active');
                answer.classList.add('active');
            }
        });
    });
    
    // Search functionality
    faqSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const faqItems = document.querySelectorAll('.faq-item');
        const faqSections = document.querySelectorAll('.faq-section');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
                highlightSearchTerm(item, searchTerm);
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide sections based on visible items
        faqSections.forEach(section => {
            const visibleItems = section.querySelectorAll('.faq-item[style="display: block"], .faq-item:not([style])');
            section.style.display = visibleItems.length > 0 ? 'block' : 'none';
        });
    });
    
    // Category filtering
    faqCategories.forEach(category => {
        category.addEventListener('click', function() {
            const targetCategory = this.dataset.category;
            const faqSections = document.querySelectorAll('.faq-section');
            
            // Remove active class from all categories
            faqCategories.forEach(cat => cat.classList.remove('active'));
            
            if (targetCategory === 'all' || !targetCategory) {
                // Show all sections
                faqSections.forEach(section => section.style.display = 'block');
                this.classList.add('active');
            } else {
                // Show only selected category
                faqSections.forEach(section => {
                    if (section.dataset.category === targetCategory) {
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                    }
                });
                this.classList.add('active');
            }
            
            // Clear search when filtering by category
            faqSearch.value = '';
            removeHighlights();
        });
    });
    
    // Add "Show All" category functionality
    const showAllBtn = document.createElement('div');
    showAllBtn.className = 'faq-category active';
    showAllBtn.innerHTML = `
        <i class="fas fa-th-large category-icon"></i>
        <div class="category-title">Todas</div>
        <div class="category-count">34 preguntas</div>
    `;
    showAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.faq-section').forEach(section => {
            section.style.display = 'block';
        });
        faqCategories.forEach(cat => cat.classList.remove('active'));
        this.classList.add('active');
        faqSearch.value = '';
        removeHighlights();
    });
    
    // Insert "Show All" at the beginning
    document.querySelector('.faq-categories').insertBefore(showAllBtn, document.querySelector('.faq-categories').firstChild);
    
    // Helper functions
    function highlightSearchTerm(item, term) {
        if (!term) return;
        
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        
        highlightInElement(question, term);
        highlightInElement(answer, term);
    }
    
    function highlightInElement(element, term) {
        const innerHTML = element.innerHTML;
        const regex = new RegExp(`(${term})`, 'gi');
        element.innerHTML = innerHTML.replace(regex, '<span class="search-highlight">$1</span>');
    }
    
    function removeHighlights() {
        document.querySelectorAll('.search-highlight').forEach(highlight => {
            highlight.outerHTML = highlight.innerHTML;
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>