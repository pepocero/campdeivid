<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

$db = DB::getInstance();

// -------------------------------
// Filtro por rango de fechas
// -------------------------------
$from = Input::get('from');        // formato YYYY-mm-dd
$to   = Input::get('to');

$where  = '';
$params = [];

if ($from && $to) {
    // Asegurar que las fechas son válidas para evitar inyecciones SQL o errores de consulta
    if (strtotime($from) && strtotime($to)) {
        $where   = 'WHERE l.fecha BETWEEN ? AND ?';
        $params  = [ $from . ' 00:00:00', $to . ' 23:59:59' ];
    } else {
        // Manejar el caso de fechas inválidas, por ejemplo, resetear el filtro
        $from = '';
        $to = '';
        Session::flash('error', 'Fechas de filtro inválidas. Se muestran todas las estadísticas.');
    }
}

// -------------------------------
// Consulta de estadísticas
// -------------------------------
// Usamos prepared statements para mayor seguridad
$query = $db->query(
    "SELECT r.id,
            r.nombre,
            SUM(CASE WHEN l.tipo = 'gratis' THEN 1 ELSE 0 END) AS gratis,
            SUM(CASE WHEN l.tipo = 'venta' THEN 1 ELSE 0 END)  AS ventas,
            COUNT(*)              AS total
       FROM rutas_log_descargas l
       JOIN aa_rutas r ON r.id = l.ruta_id
       $where
    GROUP BY r.id, r.nombre -- Agregamos r.nombre al GROUP BY para compatibilidad estricta SQL
    ORDER BY total DESC",
    $params
);
$stats = $query->results();

// Totales globales
$totalGratis = 0;
$totalVentas = 0;
$totalDesc   = 0;
foreach ($stats as $s) {
    $totalGratis += $s->gratis;
    $totalVentas += $s->ventas;
    $totalDesc   += $s->total;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estadísticas de Descargas de Rutas</title>

  <!-- Font Awesome para iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- DataTables CSS para Bootstrap 5 - ¡IMPORTANTE PARA LA VISIBILIDAD DE LA TABLA! -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">


  <style>
    /* Estilos generales */
    body {
      background-color: #f0f2f5;
      font-family: 'Inter', sans-serif;
    }

    .container-fluid {
      max-width: 1200px;
      margin-top: 2rem;
      margin-bottom: 2rem;
    }

    h1 {
      color: #343a40;
      font-weight: 700;
    }

    /* Filtro de fechas */
    .form-label {
      font-weight: 600;
      color: #495057;
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
      transition: all 0.3s ease;
      border-radius: 8px; /* Bordes redondeados */
      box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    .btn-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 123, 255, 0.3);
    }

    /* Tarjetas de resumen */
    .card {
      border-radius: 12px; /* Bordes redondeados */
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
    }

    .card-body {
      padding: 1.5rem;
    }

    .card-title {
      font-size: 1.1rem;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .card-text.display-6 {
      font-size: 2.8rem; /* Tamaño más grande para los números */
      color: #343a40;
    }

    /* Colores personalizados para las tarjetas de resumen */
    .border-success { border-color: #28a745 !important; }
    .text-success { color: #28a745 !important; }

    .border-warning { border-color: #ffc107 !important; }
    .text-warning { color: #ffc107 !important; }

    .border-info { border-color: #17a2b8 !important; }
    .text-info { color: #17a2b8 !important; }

    /* Estilos de la tabla DataTables */
    #tablaStats.table {
      border-collapse: separate; /* Necesario para border-radius en celdas */
      border-spacing: 0;
      width: 100%;
      margin-top: 1rem;
      font-size: 0.95rem;
    }

    #tablaStats.table thead th {
      background-color: #007bff;
      color: white;
      font-weight: 600;
      padding: 0.9rem 1.25rem;
      border-bottom: none; /* Eliminar el borde predeterminado */
      text-align: inherit; /* Mantener la alineación de th */
    }

    #tablaStats.table thead th:first-child {
      border-top-left-radius: 8px; /* Redondear la primera esquina */
    }

    #tablaStats.table thead th:last-child {
      border-top-right-radius: 8px; /* Redondear la última esquina */
    }

    #tablaStats.table tbody tr {
      background-color: #ffffff;
      transition: background-color 0.2s ease;
    }

    #tablaStats.table tbody tr:hover {
      background-color: #e9ecef;
    }

    #tablaStats.table tbody tr td {
      padding: 0.75rem 1.25rem;
      vertical-align: middle;
      border-top: 1px solid #dee2e6; /* Borde entre filas */
    }

    #tablaStats.table .text-end {
      text-align: end;
    }

    /* Paginación de DataTables */
    .dataTables_wrapper .pagination .page-item .page-link {
      border-radius: 8px !important; /* Bordes redondeados para paginación */
      margin: 0 4px;
      transition: all 0.2s ease;
    }

    .dataTables_wrapper .pagination .page-item.active .page-link {
      background-color: #007bff;
      border-color: #007bff;
      box-shadow: 0 2px 5px rgba(0, 123, 255, 0.2);
    }

    .dataTables_wrapper .pagination .page-item .page-link:hover {
      background-color: #e9ecef;
      color: #007bff;
    }

    /* Estilos para el campo de búsqueda */
    .dataTables_wrapper .dataTables_filter input {
      border-radius: 8px;
      border: 1px solid #ced4da;
      padding: 0.375rem 0.75rem;
      box-shadow: inset 0 1px 2px rgba(0,0,0,0.075);
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }

    /* Mensajes de sesión (UserSpice flash messages) */
    .alert-flash {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        max-width: 350px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 8px;
        animation: fadeOut 8s forwards; /* Duración y animación */
    }

    @keyframes fadeOut {
        0% { opacity: 1; transform: translateY(0); }
        80% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-20px); display: none; }
    }
  </style>
</head>
<body>

<div class="container-fluid">
    <?php
    // Mostrar mensajes flash de UserSpice
    if (Session::exists('success')) {
        echo '<div class="alert alert-success alert-dismissible fade show alert-flash" role="alert">' . Session::flash('success') . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    if (Session::exists('error')) {
        echo '<div class="alert alert-danger alert-dismissible fade show alert-flash" role="alert">' . Session::flash('error') . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    if (Session::exists('info')) {
        echo '<div class="alert alert-info alert-dismissible fade show alert-flash" role="alert">' . Session::flash('info') . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
    ?>

  <h1 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Estadísticas de Descargas de Rutas</h1>

  <!-- Filtro por fechas -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Filtrar por Rango de Fechas</h5>
      <form class="row g-3" method="get">
        <div class="col-md-4 col-lg-3">
          <label class="form-label" for="from">Desde:</label>
          <input type="date" id="from" name="from" value="<?= htmlspecialchars($from ?? '') ?>" class="form-control form-control-sm">
        </div>
        <div class="col-md-4 col-lg-3">
          <label class="form-label" for="to">Hasta:</label>
          <input type="date" id="to" name="to" value="<?= htmlspecialchars($to ?? '') ?>" class="form-control form-control-sm">
        </div>
        <div class="col-md-4 col-lg-2 align-self-end">
          <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-filter me-1"></i> Filtrar</button>
        </div>
        <?php if ($from || $to): // Mostrar botón de limpiar solo si hay filtros aplicados ?>
        <div class="col-md-4 col-lg-2 align-self-end">
          <a href="estadisticas_descargas.php" class="btn btn-outline-secondary btn-sm w-100"><i class="fas fa-times me-1"></i> Limpiar Filtro</a>
        </div>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <!-- Resumen de Totales -->
  <div class="row text-center mb-4">
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm border-success h-100">
        <div class="card-body">
          <h5 class="card-title text-success">Descargas Gratuitas</h5>
          <p class="card-text display-6 mb-0 fw-bold"><?= $totalGratis ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm border-warning h-100">
        <div class="card-body">
          <h5 class="card-title text-warning">Ventas</h5>
          <p class="card-text display-6 mb-0 fw-bold"><?= $totalVentas ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm border-info h-100">
        <div class="card-body">
          <h5 class="card-title text-info">Total Descargas</h5>
          <p class="card-text display-6 mb-0 fw-bold"><?= $totalDesc ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla detallada -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title mb-3">Detalle de Rutas</h5>
      <div class="table-responsive">
        <table id="tablaStats" class="table table-striped table-hover responsive nowrap" style="width:100%">
          <thead>
            <tr>
              <th>Ruta</th>
              <th class="text-end">Gratis</th>
              <th class="text-end">Ventas</th>
              <th class="text-end">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($stats)): ?>
                <?php foreach ($stats as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s->nombre) ?></td>
                    <td class="text-end"><?= $s->gratis ?></td>
                    <td class="text-end"><?= $s->ventas ?></td>
                    <td class="text-end fw-bold"><?= $s->total ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No hay datos disponibles para el período seleccionado.</td>
                </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Scripts de Bootstrap y DataTables -->
<!-- jQuery es un requisito de DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- Bootstrap Bundle con Popper (necesario para Bootstrap JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS principal -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<!-- DataTables JS para Bootstrap 5 -->
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<!-- DataTables Responsive JS -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>


<script>
// Inicialización de DataTables
$(document).ready(function () {
  // Solo inicializar DataTables si hay filas en la tabla (para evitar errores si no hay datos)
  if ($('#tablaStats tbody tr').length > 0 && $('#tablaStats tbody tr td').attr('colspan') !== '4') {
    $('#tablaStats').DataTable({
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' // Idioma español
      },
      pageLength: 25, // Mostrar 25 entradas por página por defecto
      order: [[3, 'desc']], // Ordenar por la columna 'Total' (índice 3) en orden descendente
      responsive: true // Habilitar la funcionalidad responsiva
    });
  }
});
</script>
</body>
</html>

