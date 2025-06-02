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

<!-- CSS adicional para ofertas -->
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
    font-size: 1.1em;
}

.badge-oferta {
    background: linear-gradient(45deg, #dc3545, #ff6b6b);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75em;
    font-weight: bold;
    margin-left: 8px;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
    animation: pulse-oferta 2s infinite;
}

@keyframes pulse-oferta {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.card-oferta {
    position: relative;
    overflow: hidden;
}

.card-oferta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #dc3545, #ff6b6b, #dc3545);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
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
    background-color: #d4edda;
    color: #155724;
    padding: 2px 6px;
    border-radius: 8px;
    font-size: 0.7em;
    margin-top: 2px;
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
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                    </div>
                </div>
            </div>
            
            <!-- Contenedor para alerta sin rutas -->
            <div id="sin-rutas-alerta" class="alert alert-warning text-center" style="display: none;">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                No hay rutas disponibles en este momento.
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
                {{badgeOferta}}
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
    
    // Función para generar HTML del precio
    function generarPrecioHTML(ruta) {
        const precio = parseFloat(ruta.precio) || 0;
        const enOferta = ruta.en_oferta == 1;
        const porcentajeOferta = parseFloat(ruta.porcentaje_oferta) || 0;
        
        // Si es gratis
        if (precio === 0) {
            return '<span class="h5 text-success">Gratis</span>';
        }
        
        // Si está en oferta
        if (enOferta && porcentajeOferta > 0) {
            const precioConDescuento = calcularPrecioOferta(precio, porcentajeOferta);
            return `
                <div class="precio-container">
                    <span class="precio-original">${precio.toFixed(2)}€</span>
                    <span class="precio-oferta h5">${precioConDescuento.toFixed(2)}€</span>
                </div>
            `;
        }
        
        // Precio normal
        return `<span class="h5">${precio.toFixed(2)}€</span>`;
    }
    
    // Función para generar info de ahorro
    function generarAhorroInfo(ruta) {
        const precio = parseFloat(ruta.precio) || 0;
        const enOferta = ruta.en_oferta == 1;
        const porcentajeOferta = parseFloat(ruta.porcentaje_oferta) || 0;
        
        if (enOferta && porcentajeOferta > 0 && precio > 0) {
            const ahorro = precio - calcularPrecioOferta(precio, porcentajeOferta);
            return `<div class="ahorro-info">Ahorras ${ahorro.toFixed(2)}€</div>`;
        }
        return '';
    }
    
    // Función para generar badge de oferta
    function generarBadgeOferta(ruta) {
        const enOferta = ruta.en_oferta == 1;
        const porcentajeOferta = parseFloat(ruta.porcentaje_oferta) || 0;
        
        if (enOferta && porcentajeOferta > 0) {
            return `<span class="badge-oferta position-absolute" style="top: 10px; right: 10px;">-${porcentajeOferta}%</span>`;
        }
        return '';
    }
    
    // Función para mostrar las rutas filtradas
    function mostrarRutas(nivelFiltro = '', planFiltro = '') {
        // Limpiar contenedor
        container.innerHTML = '';
        
        // Contador para rutas visibles
        let rutasVisibles = 0;
        
        rutas.forEach(ruta => {
            // Aplicar filtros
            if ((nivelFiltro === '' || ruta.nivel === nivelFiltro) && 
                (planFiltro === '' || ruta.plan === planFiltro)) {
                
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
                
                // Generar HTML de precio, ahorro y badge de oferta
                const precioHTML = generarPrecioHTML(ruta);
                const ahorroInfo = generarAhorroInfo(ruta);
                const badgeOferta = generarBadgeOferta(ruta);
                
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
                    .replace(/{{badgeOferta}}/g, badgeOferta);
                
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
    }
    
    // Mostrar todas las rutas inicialmente
    mostrarRutas();
    
    // Configurar eventos de filtrado
    document.getElementById('filtroNivel').addEventListener('change', function() {
        const nivelSeleccionado = this.value;
        const planSeleccionado = document.getElementById('filtroPlan').value;
        mostrarRutas(nivelSeleccionado, planSeleccionado);
    });
    
    document.getElementById('filtroPlan').addEventListener('change', function() {
        const planSeleccionado = this.value;
        const nivelSeleccionado = document.getElementById('filtroNivel').value;
        mostrarRutas(nivelSeleccionado, planSeleccionado);
    });
});
</script>
<div style="height: 30rem;"></div>
<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>