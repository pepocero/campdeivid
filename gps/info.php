<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if(isset($user) && $user->isLoggedIn()){
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#007bff">
    <title>GPS Navigator - Navegación GPS para Motos | Candeivid</title>
    <meta name="description" content="Navegador GPS profesional para motocicletas. Carga archivos GPX, navegación en tiempo real, funciona offline. Compatible con Android e iOS.">
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .header {
            text-align: center;
            padding: 60px 0;
            color: white;
        }
        
        .header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .header .subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .header .logo {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        /* Main content */
        .main-content {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 40px;
        }
        
        .section {
            padding: 50px 40px;
        }
        
        .section:nth-child(even) {
            background: #f8f9fa;
        }
        
        .section h2 {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }
        
        .section h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #007bff, #0056b3);
            border-radius: 2px;
        }
        
        .section h3 {
            font-size: 1.8rem;
            color: #333;
            margin: 30px 0 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .section h3 i {
            color: #007bff;
            font-size: 1.5rem;
        }
        
        .section p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            color: #555;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .feature-card i {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 20px;
        }
        
        .feature-card h4 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature-card p {
            color: #666;
            font-size: 1rem;
        }
        
        .steps-list {
            list-style: none;
            counter-reset: step-counter;
        }
        
        .steps-list li {
            counter-increment: step-counter;
            margin-bottom: 25px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            position: relative;
            padding-left: 80px;
        }
        
        .steps-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: #007bff;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .steps-list li h4 {
            font-size: 1.3rem;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .steps-list li p {
            margin: 0;
            color: #666;
        }
        
        .platform-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }
        
        .platform-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .platform-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .platform-card.android i {
            color: #3ddc84;
        }
        
        .platform-card.ios i {
            color: #007aff;
        }
        
        .platform-card.desktop i {
            color: #6c757d;
        }
        
        .platform-card h4 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: #333;
        }
        
        .cta-section {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            text-align: center;
            padding: 60px 40px;
        }
        
        .cta-section h2 {
            color: white;
            font-size: 2.8rem;
            margin-bottom: 20px;
        }
        
        .cta-section h2::after {
            background: white;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .cta-button {
            display: inline-block;
            background: white;
            color: #007bff;
            padding: 20px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.3rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.3);
            color: #0056b3;
        }
        
        .cta-button i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .tech-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .tech-spec {
            background: #007bff;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .tech-spec i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .tech-spec h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .tech-spec p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .warning-box h4 {
            color: #856404;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .warning-box p {
            color: #856404;
            margin: 0;
        }
        
        .footer {
            text-align: center;
            padding: 40px 20px;
            color: white;
            opacity: 0.8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .header .subtitle {
                font-size: 1.1rem;
            }
            
            .section {
                padding: 30px 20px;
            }
            
            .section h2 {
                font-size: 2rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .steps-list li {
                padding-left: 60px;
            }
            
            .steps-list li::before {
                width: 30px;
                height: 30px;
                font-size: 1rem;
            }
            
            .cta-section {
                padding: 40px 20px;
            }
            
            .cta-section h2 {
                font-size: 2.2rem;
            }
            
            .cta-button {
                padding: 15px 30px;
                font-size: 1.1rem;
            }
        }
        
        .demo-video {
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            margin: 30px 0;
            position: relative;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .demo-video i {
            font-size: 4rem;
            opacity: 0.5;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🏍️</div>
            <h1>GPS Navigator</h1>
            <p class="subtitle">Navegación GPS Profesional para Motocicletas</p>
            <a href="../gps/index.html" class="cta-button">
                <i class="fas fa-motorcycle"></i>
                Probar el GPS de Candeivid
            </a>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- ¿Qué es? -->
            <section class="section">
                <h2>¿Qué es GPS Navigator?</h2>
                <p>GPS Navigator es una <strong>aplicación web progresiva (PWA)</strong> diseñada específicamente para motociclistas que buscan una navegación GPS profesional y confiable. Carga tus archivos GPX, obtén indicaciones en tiempo real y navega de forma segura por cualquier ruta.</p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-route"></i>
                        <h4>Rutas GPX</h4>
                        <p>Carga cualquier archivo GPX y visualiza tu ruta completa en mapas interactivos de alta calidad.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-location-arrow"></i>
                        <h4>Navegación en Tiempo Real</h4>
                        <p>Indicaciones paso a paso con detección automática de desvíos y recálculo de ruta inteligente.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-wifi-slash"></i>
                        <h4>Funciona Offline</h4>
                        <p>Una vez cargada, la aplicación funciona sin conexión a internet para navegación en zonas remotas.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-motorcycle"></i>
                        <h4>Optimizado para Moto</h4>
                        <p>Controles grandes, pantalla siempre encendida y diseño adaptado para uso con guantes.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-mobile-alt"></i>
                        <h4>PWA Instalable</h4>
                        <p>Se instala como una app nativa en tu teléfono, sin necesidad de tiendas de aplicaciones.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-shield-alt"></i>
                        <h4>100% Privado</h4>
                        <p>Tus rutas se procesan localmente. No enviamos ni almacenamos tu información personal.</p>
                    </div>
                </div>
                
                <div class="tech-specs">
                    <div class="tech-spec">
                        <i class="fas fa-map"></i>
                        <h4 class="text-white">3 Tipos de Mapa</h4>
                        <p class="text-white">OpenStreetMap, Vista Satélite, Terreno</p>
                    </div>
                    <div class="tech-spec">
                        <i class="fas fa-tachometer-alt"></i>
                        <h4 class="text-white">Velocímetro GPS</h4>
                        <p class="text-white">Velocidad y rumbo en tiempo real</p>
                    </div>
                    <div class="tech-spec">
                        <i class="fas fa-crosshairs"></i>
                        <h4 class="text-white">Alta Precisión</h4>
                        <p class="text-white">GPS de alta precisión con tolerancia de 50m</p>
                    </div>
                    <div class="tech-spec">
                        <i class="fas fa-clock"></i>
                        <h4 class="text-white">Tiempo Estimado</h4>
                        <p class="text-white">Cálculo automático de llegada</p>
                    </div>
                </div>
            </section>
            
            <!-- Cómo funciona -->
            <section class="section">
                <h2>¿Cómo Funciona?</h2>
                <p>GPS Navigator utiliza tecnología web moderna para ofrecerte una experiencia de navegación comparable a aplicaciones nativas, pero con la flexibilidad de funcionar en cualquier dispositivo.</p>
                
                <h3><i class="fas fa-cogs"></i>Tecnología Avanzada</h3>
                <ul class="steps-list">
                    <li>
                        <h4>Procesamiento Local</h4>
                        <p>Tu archivo GPX se procesa completamente en tu dispositivo. Nada se envía a servidores externos, garantizando tu privacidad total.</p>
                    </li>
                    <li>
                        <h4>Service Worker Inteligente</h4>
                        <p>Cache automático de mapas y funcionamiento offline. Los mapas visitados se guardan para uso sin conexión.</p>
                    </li>
                    <li>
                        <h4>Geolocalización de Alta Precisión</h4>
                        <p>Utiliza el GPS de tu dispositivo con configuración de alta precisión para seguimiento exacto de tu posición.</p>
                    </li>
                    <li>
                        <h4>Routing Inteligente</h4>
                        <p>Cuando te sales de la ruta, calcula automáticamente el camino más corto para volver usando OpenStreetMap Routing Machine.</p>
                    </li>
                </ul>
                
                <div class="warning-box">
                    <h4><i class="fas fa-exclamation-triangle"></i>Importante para la Seguridad</h4>
                    <p>Siempre monta tu teléfono de forma segura en tu motocicleta y usa comandos de voz o detente de forma segura para interactuar con la aplicación. La seguridad es lo primero.</p>
                </div>
            </section>
            
            <!-- Instalación -->
            <section class="section">
                <h2>Instalación Fácil</h2>
                <p>GPS Navigator es una <strong>PWA (Progressive Web App)</strong>, lo que significa que se instala directamente desde tu navegador web sin necesidad de tiendas de aplicaciones.</p>
                
                <div class="platform-grid">
                    <div class="platform-card android">
                        <i class="fab fa-android"></i>
                        <h4>Android</h4>
                        <ol class="steps-list">
                            <li>
                                <h4>Abre en Chrome</h4>
                                <p>Visita la aplicación en Google Chrome</p>
                            </li>
                            <li>
                                <h4>Instalar App</h4>
                                <p>Toca "Instalar App" o el botón "+" en la barra de direcciones</p>
                            </li>
                            <li>
                                <h4>Confirmar</h4>
                                <p>Selecciona "Instalar" en el diálogo que aparece</p>
                            </li>
                            <li>
                                <h4>¡Listo!</h4>
                                <p>La app aparecerá en tu pantalla de inicio como cualquier app nativa</p>
                            </li>
                        </ol>
                    </div>
                    <div class="platform-card ios">
                        <i class="fab fa-apple"></i>
                        <h4>iPhone/iPad</h4>
                        <ol class="steps-list">
                            <li>
                                <h4>Abre en Safari</h4>
                                <p>Visita la aplicación en Safari (importante: debe ser Safari)</p>
                            </li>
                            <li>
                                <h4>Compartir</h4>
                                <p>Toca el botón "Compartir" en la barra inferior</p>
                            </li>
                            <li>
                                <h4>Añadir a Inicio</h4>
                                <p>Selecciona "Añadir a pantalla de inicio"</p>
                            </li>
                            <li>
                                <h4>Confirmar</h4>
                                <p>Toca "Añadir" y la app se instalará</p>
                            </li>
                        </ol>
                    </div>
                    <div class="platform-card desktop">
                        <i class="fas fa-desktop"></i>
                        <h4>PC/Mac</h4>
                        <ol class="steps-list">
                            <li>
                                <h4>Navegador Compatible</h4>
                                <p>Abre en Chrome, Edge, Firefox o Safari</p>
                            </li>
                            <li>
                                <h4>Icono de Instalación</h4>
                                <p>Busca el icono "+" en la barra de direcciones</p>
                            </li>
                            <li>
                                <h4>Instalar</h4>
                                <p>Haz clic en "Instalar GPS Navigator"</p>
                            </li>
                            <li>
                                <h4>Acceso Directo</h4>
                                <p>Se creará un acceso directo en tu escritorio</p>
                            </li>
                        </ol>
                    </div>
                </div>
                
                <div class="warning-box">
                    <h4><i class="fas fa-info-circle"></i>Nota sobre Permisos</h4>
                    <p>La aplicación solicitará permisos de ubicación GPS y mantener pantalla encendida. Estos permisos son necesarios para la navegación y son seguros.</p>
                </div>
            </section>
            
            <!-- Cómo usar -->
            <section class="section">
                <h2>Cómo Usar GPS Navigator</h2>
                <p>Una vez instalada, usar GPS Navigator es intuitivo y directo. Aquí te explicamos paso a paso:</p>
                
                <h3><i class="fas fa-play-circle"></i>Primeros Pasos</h3>
                <ol class="steps-list">
                    <li>
                        <h4>Abrir la Aplicación</h4>
                        <p>Toca el icono de GPS Navigator en tu pantalla de inicio. La app se abrirá en pantalla completa como una aplicación nativa.</p>
                    </li>
                    <li>
                        <h4>Cargar tu Archivo GPX</h4>
                        <p>Arrastra y suelta tu archivo .gpx en la zona de carga, o usa el botón "Seleccionar archivo GPX". La ruta se visualizará inmediatamente en el mapa.</p>
                    </li>
                    <li>
                        <h4>Revisar la Ruta</h4>
                        <p>Explora tu ruta usando los controles de zoom y pan. Puedes cambiar entre vista de mapa, satélite o terreno según tus preferencias.</p>
                    </li>
                    <li>
                        <h4>Iniciar Navegación</h4>
                        <p>Toca el botón de navegación (triángulo azul) y permite acceso a tu ubicación cuando se solicite.</p>
                    </li>
                    <li>
                        <h4>¡A Navegar!</h4>
                        <p>Sigue las indicaciones en pantalla. La app te guiará paso a paso y recalculará la ruta si te desvías.</p>
                    </li>
                </ol>
                
                <h3><i class="fas fa-motorcycle"></i>Durante la Navegación</h3>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-compass"></i>
                        <h4>Panel de Navegación</h4>
                        <p>Instrucciones claras, distancia al próximo punto y información de velocidad.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Alertas de Desvío</h4>
                        <p>Detección automática si te sales de la ruta con recálculo inteligente.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-mobile-screen"></i>
                        <h4>Pantalla Activa</h4>
                        <p>La pantalla permanece encendida durante la navegación para visibilidad constante.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-hand-pointer"></i>
                        <h4>Controles Táctiles</h4>
                        <p>Botones grandes y fáciles de usar, incluso con guantes de moto.</p>
                    </div>
                </div>
                
                <h3><i class="fas fa-tools"></i>Funciones Avanzadas</h3>
                <ul class="steps-list">
                    <li>
                        <h4>Modo Seguimiento Automático</h4>
                        <p>El mapa se centra automáticamente en tu posición. Puedes desactivarlo para explorar manualmente.</p>
                    </li>
                    <li>
                        <h4>Cambio de Capas de Mapa</h4>
                        <p>Alterna entre OpenStreetMap (calles), vista satélite (imagen real) y terreno (topográfico) según la situación.</p>
                    </li>
                    <li>
                        <h4>Estadísticas en Tiempo Real</h4>
                        <p>Velocidad actual, rumbo, distancia restante y tiempo estimado de llegada siempre visibles.</p>
                    </li>
                    <li>
                        <h4>Modo Pantalla Completa</h4>
                        <p>Elimina distracciones ocultando elementos innecesarios durante la navegación activa.</p>
                    </li>
                </ul>
            </section>
        </div>
        
        <!-- CTA Section -->
        <div class="cta-section">
            <h2>¿Listo para tu Próxima Aventura?</h2>
            <p>Prueba GPS Navigator ahora y descubre la diferencia de una navegación GPS diseñada específicamente para motociclistas.</p>
            <a href="../gps/index.html" class="cta-button">
                <i class="fas fa-motorcycle"></i>
                Probar el GPS de Candeivid
            </a>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 Candeivid - GPS Navigator. Navegación GPS profesional para motociclistas.</p>
            <p>Desarrollado con tecnología web moderna • PWA • Privacidad garantizada</p>
        </div>
    </div>
</body>
</html>