<?php
require_once $_SERVER['DOCUMENT_ROOT']."/ini_folder_camp.php";
require_once $_SERVER['DOCUMENT_ROOT'].$folder."/users/init.php";
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

// Conexión a la base de datos y consulta
try {
    $db = DB::getInstance();
    $query = $db->query("SELECT * FROM aa_rutas");
    $rutas = $query->results();
    
    // Si no hay rutas, cargar datos de ejemplo
    if(empty($rutas)) {
        $rutas = [
            [
                'id' => 1,
                'nombre' => 'Costa Andaluza Express',
                'descripcion' => 'Recorre la vibrante costa sur de España.',
                'nivel' => 'Intermedio',
                'imagen' => 'https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                'plan' => 'Gratis',
                'paisaje' => 'Bonito',
                'precio' => 60.00
            ],
            [
                'id' => 2,
                'nombre' => 'Pirineos para Principiantes',
                'descripcion' => 'Iníciate en las montañas con vistas espectaculares.',
                'nivel' => 'Novato',
                'imagen' => 'https://images.unsplash.com/photo-1605540436563-5bca919ae766?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                'plan' => 'Premium',
                'paisaje' => 'Bonito',
                'precio' => 45.00
            ],
            [
                'id' => 3,
                'nombre' => 'Desafío Picos de Europa',
                'descripcion' => 'Solo para los más experimentados: curvas y altitud.',
                'nivel' => 'Experto',
                'imagen' => 'https://images.unsplash.com/photo-1588666309990-d68f08e3d4a6?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                'plan' => 'Premium',
                'paisaje' => 'Bonito',
                'precio' => 75.00
            ]
        ];
    }
    
    // Convertir a JSON
    $rutas_json = json_encode($rutas);
    
} catch(Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
<!-- cargar css rutas -->
<link rel="stylesheet" href="../css/rutas.css">

<div class="row">
    <div class="col-12">
        <h1 class="text-center mb-4">Explorar Rutas Disponibles</h1>
        
        <div class="row" id="rutas-container">
            <!-- Las rutas se cargarán aquí dinámicamente -->
        </div>
    </div>
</div>

<!-- Template para cada card de ruta (usado por JavaScript) -->
<script type="text/template" id="ruta-template">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="position-relative">
                <img src="{{imagen}}" class="card-img-top" alt="{{nombre}}">
                <span class="badge-plan {{planClass}}">{{plan}}</span>
            </div>
            <div class="card-body">
                <h5 class="card-title">{{nombre}}</h5>
                <p class="card-text">{{descripcion}}</p>
                
                <div class="mb-3">
                    <span class="badge {{nivelClass}} mr-2">{{nivel}}</span>
                    <span class="badge bg-success">{{paisaje}}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5">{{precioFormateado}}€</span>
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
    
    rutas.forEach(ruta => {
        // Preparar datos
        const nivelClass = {
            'Novato': 'bg-info',
            'Intermedio': 'bg-warning text-dark',
            'Experto': 'bg-danger'
        }[ruta.nivel] || 'bg-secondary';
        
        const planClass = ruta.plan === 'Premium' ? 'bg-danger' : 'bg-secondary';
        const precioFormateado = parseFloat(ruta.precio).toFixed(2);
        
        // Reemplazar placeholders
        let html = template
            .replace(/{{id}}/g, ruta.id)
            .replace(/{{nombre}}/g, ruta.nombre)
            .replace(/{{descripcion}}/g, ruta.descripcion)
            .replace(/{{imagen}}/g, ruta.imagen || 'https://via.placeholder.com/300x200?text=Motocicleta')
            .replace(/{{nivel}}/g, ruta.nivel)
            .replace(/{{nivelClass}}/g, nivelClass)
            .replace(/{{paisaje}}/g, ruta.paisaje)
            .replace(/{{plan}}/g, ruta.plan)
            .replace(/{{planClass}}/g, planClass)
            .replace(/{{precioFormateado}}/g, precioFormateado);
        
        // Añadir al contenedor
        container.insertAdjacentHTML('beforeend', html);
    });
});
</script>

<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>