<?php
require_once '../users/init.php';  //make sure this path is correct!
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Conexión a la base de datos y consulta
try {
    $db = DB::getInstance();
    $query = $db->query("SELECT * FROM aa_rutas");
    $rutas = $query->results();
    
    // Obtener niveles y planes únicos para los filtros
    $niveles = [];
    $planes = [];
    if(!empty($rutas)) {
        foreach($rutas as $ruta) {
            if(!in_array($ruta->nivel, $niveles)) {
                $niveles[] = $ruta->nivel;
            }
            if(!in_array($ruta->plan, $planes)) {
                $planes[] = $ruta->plan;
            }
        }
    }
    
    // Convertir a JSON
    $rutas_json = json_encode($rutas);
    
} catch(Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
<!-- cargar css rutas -->
<link rel="stylesheet" href="../css/rutas.css">

<!-- CSS adicional para ofertas MUY LLAMATIVAS -->
<style>
.precio-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.precio-original {
    text-decoration: line-through;
    color: #6c757d;
    font-size: 0.9em;
    margin-bottom: 2px;
}

.precio-oferta {
    color: #dc3545;
    font-weight: bold;
    font-size: 1.3em;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

/* BADGE DE OFERTA MÁS LLAMATIVO */
.badge-oferta {
    background: linear-gradient(45deg, #dc3545, #ff6b6b, #fd7e14);
    background-size: 200% 200%;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: bold;
    margin-left: 8px;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
    animation: pulse-oferta 2s infinite, gradient-shift 3s ease infinite;
    position: relative;
    overflow: hidden;
}

.badge-oferta::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shine 2s infinite;
}

@keyframes pulse-oferta {
    0% { transform: scale(1); box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4); }
    50% { transform: scale(1.08); box-shadow: 0 6px 25px rgba(220, 53, 69, 0.6); }
    100% { transform: scale(1); box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4); }
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes shine {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* CARD CON OFERTA MÁS DESTACADA */
.card-oferta {
    position: relative;
    overflow: hidden;
    transform: scale(1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box,
                linear-gradient(45deg, #dc3545, #ff6b6b, #fd7e14) border-box;
}

.card-oferta:hover {
    transform: scale(1.02);
    box-shadow: 0 15px 35px rgba(220, 53, 69, 0.2);
}

.card-oferta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #dc3545, #ff6b6b, #fd7e14, #dc3545);
    background-size: 200% 100%;
    animation: border-flow 3s linear infinite;
}

@keyframes border-flow {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* BADGE FLOTANTE "EN OFERTA" */
.badge-oferta-flotante {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(45deg, #dc3545, #ff6b6b);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.9em;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.5);
    animation: float-badge 2s ease-in-out infinite;
    z-index: 10;
}

@keyframes float-badge {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
}

.precio-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}

.precio-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.ahorro-info {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.75em;
    font-weight: bold;
    margin-top: 4px;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    animation: bounce-ahorro 1s ease-in-out infinite alternate;
}

@keyframes bounce-ahorro {
    from { transform: scale(1); }
    to { transform: scale(1.05); }
}

/* EFECTO SPARKLE PARA OFERTAS */
.sparkle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #ffd700;
    border-radius: 50%;
    animation: sparkle 1.5s infinite;
}

.sparkle:nth-child(1) { top: 20%; left: 20%; animation-delay: 0s; }
.sparkle:nth-child(2) { top: 80%; left: 80%; animation-delay: 0.5s; }
.sparkle:nth-child(3) { top: 60%; left: 10%; animation-delay: 1s; }

@keyframes sparkle {
    0%, 100% { opacity: 0; transform: scale(0.5); }
    50% { opacity: 1; transform: scale(1.2); }
}

/* GLOW EFFECT */
.precio-oferta-glow {
    color: #dc3545;
    font-weight: bold;
    font-size: 1.3em;
    text-shadow: 0 0 10px rgba(220, 53, 69, 0.5),
                 0 0 20px rgba(220, 53, 69, 0.3),
                 0 0 30px rgba(220, 53, 69, 0.1);
    animation: glow-pulse 2s ease-in-out infinite alternate;
}

@keyframes glow-pulse {
    from { 
        text-shadow: 0 0 10px rgba(220, 53, 69, 0.5),
                     0 0 20px rgba(220, 53, 69, 0.3),
                     0 0 30px rgba(220, 53, 69, 0.1);
    }
    to { 
        text-shadow: 0 0 15px rgba(220, 53, 69, 0.8),
                     0 0 25px rgba(220, 53, 69, 0.5),
                     0 0 35px rgba(220, 53, 69, 0.3);
    }
}

/* INDICADOR VISUAL DE DESCUENTO */
.descuento-tag {
    background: linear-gradient(135deg, #ff6b6b 0%, #dc3545 100%);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7em;
    font-weight: bold;
    margin-left: 5px;
    position: relative;
    animation: wiggle 1s ease-in-out infinite;
}

@keyframes wiggle {
    0%, 7% { transform: rotateZ(0); }
    15% { transform: rotateZ(-15deg); }
    20% { transform: rotateZ(10deg); }
    25% { transform: rotateZ(-10deg); }
    30% { transform: rotateZ(6deg); }
    35% { transform: rotateZ(-4deg); }
    40%, 100% { transform: rotateZ(0); }
}

/* ESTILOS PARA EL FILTRO DE OFERTAS - VERSIÓN DISCRETA */
.filtro-ofertas {
    background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 5px 12px;
    margin: 0;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.1);
}

.filtro-ofertas:hover {
    border-color: #dc3545;
    box-shadow: 0 3px 8px rgba(220, 53, 69, 0.2);
}

.filtro-ofertas .form-check-input {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    margin-top: 2px;
    accent-color: #dc3545;
}

.filtro-ofertas-label {
    color: #dc3545;
    font-weight: 600;
    margin-bottom: 0;
    cursor: pointer;
    font-size: 0.9em;
}

.filtro-ofertas-label i {
    margin-right: 5px;
    color: #ff6b6b;
}

.filtro-ofertas.checked {
    background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
    border-color: #dc3545;
}

.filtro-ofertas.checked .filtro-ofertas-label {
    color: white;
}

.filtro-ofertas.checked .filtro-ofertas-label i {
    color: white;
}

/* Contador de ofertas */
.contador-ofertas {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: bold;
    margin-left: 10px;
    animation: bounce-contador 2s ease-in-out infinite;
}

@keyframes bounce-contador {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-5px); }
    60% { transform: translateY(-3px); }
}

/* RESPONSIVE IMPROVEMENTS */
@media (max-width: 768px) {
    .badge-oferta-flotante {
        top: 10px;
        right: 10px;
        padding: 6px 12px;
        font-size: 0.8em;
    }
    
    .precio-oferta-glow {
        font-size: 1.1em;
    }
}
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">Explorar Rutas Disponibles</h1>
            
            <!-- Filtros -->
            <div class="card mb-4 bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Filtros</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filtroNivel">Nivel:</label>
                                <select class="form-control" id="filtroNivel">
                                    <option value="">Todos los niveles</option>
                                    <?php foreach($niveles as $nivel): ?>
                                        <option value="<?= $nivel ?>"><?= $nivel ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filtroPlan">Plan:</label>
                                <select class="form-control" id="filtroPlan">
                                    <option value="">Todos los planes</option>
                                    <?php foreach($planes as $plan): ?>
                                        <option value="<?= $plan ?>"><?= $plan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filtro-ofertas">Filtros Especiales:</label>                                
                                <!-- NUEVO: Filtro de ofertas discreto -->
                                <div class="filtro-ofertas">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filtroOfertas">
                                        <label class="form-check-label filtro-ofertas-label" for="filtroOfertas">
                                            <i class="fas fa-fire"></i> Solo Ofertas
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Información de resultados -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <button class="btn btn-light btn-sm" id="limpiarFiltros">
                                <i class="fas fa-times"></i> Limpiar Filtros
                            </button>
                            <span class="ml-3 text-muted" id="resultadosInfo">
                                <i class="fas fa-info-circle"></i> Mostrando todas las rutas
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contenedor para alerta sin rutas -->
            <div id="sin-rutas-alerta" class="alert alert-warning text-center" style="display: none;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                No hay rutas disponibles con los filtros seleccionados.
            </div>
            
            <!-- Contenedor de rutas -->
            <div class="row" id="rutas-container">
                <!-- Las rutas se cargarán aquí dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Template para cada card de ruta (usado por JavaScript) -->
<script type="text/template" id="ruta-template">
    <div class="col-md-4 mb-4 ruta-card" data-nivel="{{nivel}}" data-plan="{{plan}}">
        <div class="card h-100 {{cardOfertaClass}}">
            <div class="position-relative">
                <img src="{{imagen}}" class="card-img-top" alt="{{nombre}}">
                <span class="badge-plan {{planClass}}">{{plan}}</span>
                {{badgeOfertaFlotante}}
                {{sparkles}}
            </div>
            <div class="card-body">
                <h5 class="card-title">{{nombre}}</h5>
                <p class="card-text">{{descripcion}}</p>
                
                <div class="mb-3">
                    <span class="badge {{nivelClass}} mr-2">{{nivel}}</span>
                    <span class="badge mr-2" style="background-color:#b2361b;"><i class="fas fa-route"></i> {{distancia}} km</span>
                    <span class="badge bg-success"><i class="fas fa-clock"></i> {{tiempo}}</span>
                </div>
                
                <div class="precio-section">
                    <div class="precio-info">
                        {{precioHTML}}
                        {{ahorroInfo}}
                    </div>
                    <a href="ruta_detalle.php?id={{id}}" class="btn btn-primary">Ver Detalles</a>
                </div>
            </div>
        </div>
    </div>
</script>

<script>
// Procesar el JSON y mostrar las rutas
document.addEventListener('DOMContentLoaded', function() {
    const rutas = <?php echo $rutas_json; ?>;
    const container = document.getElementById('rutas-container');
    const template = document.getElementById('ruta-template').innerHTML;
    const sinRutasAlerta = document.getElementById('sin-rutas-alerta');
    
    // Verificar si hay rutas
    if (rutas.length === 0) {
        sinRutasAlerta.style.display = 'block';
        return;
    }
    
    // Función para calcular precio con descuento
    function calcularPrecioOferta(precio, porcentajeOferta) {
        return precio - (precio * porcentajeOferta / 100);
    }
    
    // Función para generar HTML del precio MÁS LLAMATIVO
    function generarPrecioHTML(ruta) {
        const precio = parseFloat(ruta.precio) || 0;
        const enOferta = ruta.en_oferta == 1;
        const porcentajeOferta = parseFloat(ruta.porcentaje_oferta) || 0;
        
        // Si es gratis
        if (precio === 0) {
            return '<span class="h5 text-success"><i class="fas fa-gift"></i> Gratis</span>';
        }
        
        // Si está en oferta - SÚPER LLAMATIVO
        if (enOferta && porcentajeOferta > 0) {
            const precioConDescuento = calcularPrecioOferta(precio, porcentajeOferta);
            return `
                <div class="precio-container">
                    <span class="precio-original"><i class="fas fa-times"></i> ${precio.toFixed(2)}€</span>
                    <div style="display: flex; align-items: center;">
                        <span class="precio-oferta-glow">${precioConDescuento.toFixed(2)}€</span>
                        <span class="badge-oferta">-${porcentajeOferta}%</span>
                        <span class="descuento-tag">¡OFERTA!</span>
                    </div>
                </div>
            `;
        }
        
        // Precio normal
        return `<span class="h5"><i class="fas fa-euro-sign"></i> ${precio.toFixed(2)}€</span>`;
    }
    
    // Función para generar info de ahorro MÁS LLAMATIVA
    function generarAhorroInfo(ruta) {
        const precio = parseFloat(ruta.precio) || 0;
        const enOferta = ruta.en_oferta == 1;
        const porcentajeOferta = parseFloat(ruta.porcentaje_oferta) || 0;
        
        if (enOferta && porcentajeOferta > 0 && precio > 0) {
            const ahorro = precio - calcularPrecioOferta(precio, porcentajeOferta);
            return `<div class="ahorro-info">
                        <i class="fas fa-piggy-bank"></i> ¡Ahorras ${ahorro.toFixed(2)}€!
                    </div>`;
        }
        return '';
    }
    
    // Función para generar badge flotante de oferta
    function generarBadgeOfertaFlotante(ruta) {
        const enOferta = ruta.en_oferta == 1;
        const porcentajeOferta = parseFloat(ruta.porcentaje_oferta) || 0;
        
        if (enOferta && porcentajeOferta > 0) {
            return `<div class="badge-oferta-flotante">
                        <i class="fas fa-fire"></i> -${porcentajeOferta}% OFF
                    </div>`;
        }
        return '';
    }
    
    // Función para generar efectos sparkle
    function generarSparkles(ruta) {
        const enOferta = ruta.en_oferta == 1;
        const porcentajeOferta = parseFloat(ruta.porcentaje_oferta) || 0;
        
        if (enOferta && porcentajeOferta > 0) {
            return `
                <div class="sparkle" style="top: 15%; left: 15%;"></div>
                <div class="sparkle" style="top: 75%; left: 85%;"></div>
                <div class="sparkle" style="top: 45%; left: 5%;"></div>
            `;
        }
        return '';
    }
    
    // Función para contar ofertas
    function contarOfertas(rutasFiltradas = null) {
        const rutasParaContar = rutasFiltradas || rutas;
        return rutasParaContar.filter(ruta => {
            const enOferta = ruta.en_oferta == 1;
            const tieneDescuento = parseFloat(ruta.porcentaje_oferta) > 0;
            return enOferta && tieneDescuento;
        }).length;
    }
    
    // Función para actualizar información de resultados
    function actualizarInfoResultados(rutasVisibles, soloOfertas = false, rutasFiltradas = null) {
        const infoElement = document.getElementById('resultadosInfo');
        const totalOfertas = contarOfertas();
        const ofertasEnFiltro = rutasFiltradas ? contarOfertas(rutasFiltradas) : totalOfertas;
        
        if (rutasVisibles === 0) {
            infoElement.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> No se encontraron rutas con los filtros seleccionados';
        } else if (soloOfertas) {
            infoElement.innerHTML = `<i class="fas fa-fire text-danger"></i> <span class="badge badge-danger">${rutasVisibles} ruta${rutasVisibles !== 1 ? 's' : ''} en oferta</span> <span class="contador-ofertas">🔥 ${rutasVisibles} OFERTAS</span>`;
        } else if (rutasVisibles === rutas.length) {
            infoElement.innerHTML = `</i> <span class="badge badge-dark">${rutasVisibles} rutas totales</span> <span class="contador-ofertas">${totalOfertas} en oferta</span>`;
        } else {
            infoElement.innerHTML = `<i class="fas fa-filter text-primary"></i> <span class="badge badge-secondary">${rutasVisibles} de ${rutas.length} rutas</span> <span class="contador-ofertas">${ofertasEnFiltro} ofertas</span>`;
        }
    }
    
    // Función para manejar el estado visual del filtro de ofertas
    function actualizarEstadoFiltroOfertas() {
        const checkbox = document.getElementById('filtroOfertas');
        const contenedor = checkbox.closest('.filtro-ofertas');
        
        if (checkbox.checked) {
            contenedor.classList.add('checked');
        } else {
            contenedor.classList.remove('checked');
        }
    }
    
    // Función para mostrar las rutas filtradas
    function mostrarRutas(nivelFiltro = '', planFiltro = '', soloOfertas = false) {
        // Limpiar contenedor
        container.innerHTML = '';
        
        // Contador para rutas visibles
        let rutasVisibles = 0;
        let rutasFiltradas = [];
        
        rutas.forEach(ruta => {
            // Aplicar filtros
            let cumpleFiltros = true;
            
            // Filtro por nivel
            if (nivelFiltro !== '' && ruta.nivel !== nivelFiltro) {
                cumpleFiltros = false;
            }
            
            // Filtro por plan
            if (planFiltro !== '' && ruta.plan !== planFiltro) {
                cumpleFiltros = false;
            }
            
            // NUEVO: Filtro por ofertas
            if (soloOfertas) {
                const enOferta = ruta.en_oferta == 1;
                const tieneDescuento = parseFloat(ruta.porcentaje_oferta) > 0;
                if (!enOferta || !tieneDescuento) {
                    cumpleFiltros = false;
                }
            }
            
            if (cumpleFiltros) {
                rutasFiltradas.push(ruta);
                
                // Preparar datos
                const nivelClass = {
                    'Novato': 'bg-info',
                    'Intermedio': 'bg-warning text-dark',
                    'Experto': 'bg-danger',
                    'Piloto nuevo': 'bg-info',
                    'Domando Curvas': 'bg-warning text-dark',
                    'Maestro del Asfalto': 'bg-danger'
                }[ruta.nivel] || 'bg-secondary';
                
                const planClass = ruta.plan === 'Premium' ? 'bg-danger' : 'bg-secondary';
                
                // Determinar si la card tiene oferta para añadir clase especial
                const enOferta = ruta.en_oferta == 1;
                const cardOfertaClass = enOferta ? 'card-oferta' : '';
                
                // Generar HTML de precio, ahorro y efectos de oferta SÚPER LLAMATIVOS
                const precioHTML = generarPrecioHTML(ruta);
                const ahorroInfo = generarAhorroInfo(ruta);
                const badgeOfertaFlotante = generarBadgeOfertaFlotante(ruta);
                const sparkles = generarSparkles(ruta);
                
                // Reemplazar placeholders
                let html = template
                    .replace(/{{id}}/g, ruta.id)
                    .replace(/{{nombre}}/g, ruta.nombre)
                    .replace(/{{descripcion}}/g, ruta.descripcion)
                    .replace(/{{imagen}}/g, ruta.imagen || 'https://via.placeholder.com/300x200?text=Motocicleta')
                    .replace(/{{nivel}}/g, ruta.nivel)
                    .replace(/{{nivelClass}}/g, nivelClass)
                    .replace(/{{distancia}}/g, Math.round(parseFloat(ruta.distancia)) || '0')
                    .replace(/{{tiempo}}/g, ruta.tiempo || 'No especificado')
                    .replace(/{{plan}}/g, ruta.plan)
                    .replace(/{{planClass}}/g, planClass)
                    .replace(/{{cardOfertaClass}}/g, cardOfertaClass)
                    .replace(/{{precioHTML}}/g, precioHTML)
                    .replace(/{{ahorroInfo}}/g, ahorroInfo)
                    .replace(/{{badgeOfertaFlotante}}/g, badgeOfertaFlotante)
                    .replace(/{{sparkles}}/g, sparkles);
                
                // Añadir al contenedor
                container.insertAdjacentHTML('beforeend', html);
                rutasVisibles++;
            }
        });
        
        // Mostrar alerta si no hay rutas después de filtrar
        if (rutasVisibles === 0) {
            sinRutasAlerta.style.display = 'block';
        } else {
            sinRutasAlerta.style.display = 'none';
        }
        
        // Actualizar información de resultados
        actualizarInfoResultados(rutasVisibles, soloOfertas, rutasFiltradas);
    }
    
    // Mostrar todas las rutas inicialmente
    mostrarRutas();
    actualizarEstadoFiltroOfertas();
    
    // Configurar eventos de filtrado
    document.getElementById('filtroNivel').addEventListener('change', function() {
        const nivelSeleccionado = this.value;
        const planSeleccionado = document.getElementById('filtroPlan').value;
        const soloOfertas = document.getElementById('filtroOfertas').checked;
        mostrarRutas(nivelSeleccionado, planSeleccionado, soloOfertas);
    });
    
    document.getElementById('filtroPlan').addEventListener('change', function() {
        const planSeleccionado = this.value;
        const nivelSeleccionado = document.getElementById('filtroNivel').value;
        const soloOfertas = document.getElementById('filtroOfertas').checked;
        mostrarRutas(nivelSeleccionado, planSeleccionado, soloOfertas);
    });
    
    // NUEVO: Event listener para filtro de ofertas
    document.getElementById('filtroOfertas').addEventListener('change', function() {
        actualizarEstadoFiltroOfertas();
        const nivelSeleccionado = document.getElementById('filtroNivel').value;
        const planSeleccionado = document.getElementById('filtroPlan').value;
        mostrarRutas(nivelSeleccionado, planSeleccionado, this.checked);
        
        // Efecto especial cuando se activa el filtro de ofertas
        if (this.checked) {
            // Mostrar mensaje temporal
            const mensaje = document.createElement('div');
            mensaje.className = 'alert alert-success position-fixed';
            mensaje.style.cssText = 'top: 20px; right: 20px; z-index: 9999; opacity: 0; transition: opacity 0.3s;';
            mensaje.innerHTML = '<i class="fas fa-fire"></i> <strong>¡Mostrando solo ofertas especiales!</strong>';
            document.body.appendChild(mensaje);
            
            // Animación de aparición
            setTimeout(() => mensaje.style.opacity = '1', 100);
            setTimeout(() => {
                mensaje.style.opacity = '0';
                setTimeout(() => document.body.removeChild(mensaje), 300);
            }, 2500);
        }
    });
    
    // Event listener para limpiar filtros
    document.getElementById('limpiarFiltros').addEventListener('click', function() {
        document.getElementById('filtroNivel').value = '';
        document.getElementById('filtroPlan').value = '';
        document.getElementById('filtroOfertas').checked = false;
        actualizarEstadoFiltroOfertas();
        mostrarRutas();
    });
});
</script>
<div style="height: 30rem;"></div>
<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>