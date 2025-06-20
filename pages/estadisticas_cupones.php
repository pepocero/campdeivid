<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

if(!hasPerm([2,4], $user->data()->id)) {
    Session::flash('error', 'Acceso denegado');
    Redirect::to('index.php');
}

try {
    $db = DB::getInstance();
    
    // Estadísticas generales
    $stats = $db->query("
        SELECT 
            COUNT(*) as total_cupones,
            SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as cupones_activos,
            SUM(CASE WHEN fecha_fin < NOW() THEN 1 ELSE 0 END) as cupones_expirados,
            SUM(usos_actuales) as total_usos
        FROM aa_cupones
    ")->first();
    
    // Total ahorrado
    $total_ahorrado = $db->query("
        SELECT COALESCE(SUM(descuento_aplicado), 0) as total 
        FROM aa_cupones_uso
    ")->first();
    
    // Cupones más utilizados
    $cupones_populares = $db->query("
        SELECT 
            c.codigo,
            c.descripcion,
            c.tipo_descuento,
            c.valor_descuento,
            COUNT(cu.id) as usos,
            SUM(cu.descuento_aplicado) as total_descuento
        FROM aa_cupones c
        LEFT JOIN aa_cupones_uso cu ON c.id = cu.cupon_id
        GROUP BY c.id
        HAVING usos > 0
        ORDER BY usos DESC, total_descuento DESC
        LIMIT 10
    ")->results();
    
    // Usos por mes (últimos 12 meses)
    $usos_mensuales = $db->query("
        SELECT 
            DATE_FORMAT(fecha_uso, '%Y-%m') as mes,
            COUNT(*) as usos,
            SUM(descuento_aplicado) as descuentos
        FROM aa_cupones_uso 
        WHERE fecha_uso >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(fecha_uso, '%Y-%m')
        ORDER BY mes DESC
    ")->results();
    
    // Rutas con más descuentos aplicados
    $rutas_descuentos = $db->query("
        SELECT 
            r.nombre,
            r.plan,
            COUNT(cu.id) as usos_cupones,
            SUM(cu.descuento_aplicado) as total_descuentos
        FROM aa_rutas r
        INNER JOIN aa_cupones_uso cu ON r.id = cu.ruta_id
        GROUP BY r.id
        ORDER BY total_descuentos DESC
        LIMIT 10
    ")->results();
    
    // Usuarios que más han usado cupones
    $usuarios_cupones = $db->query("
        SELECT 
            u.username,
            u.email,
            COUNT(cu.id) as cupones_usados,
            SUM(cu.descuento_aplicado) as total_ahorrado
        FROM users u
        INNER JOIN aa_cupones_uso cu ON u.id = cu.user_id
        GROUP BY u.id
        ORDER BY total_ahorrado DESC
        LIMIT 10
    ")->results();
    
} catch(Exception $e) {
    Session::flash('error', 'Error: ' . $e->getMessage());
    $stats = (object)['total_cupones' => 0, 'cupones_activos' => 0, 'cupones_expirados' => 0, 'total_usos' => 0];
    $total_ahorrado = (object)['total' => 0];
    $cupones_populares = [];
    $usos_mensuales = [];
    $rutas_descuentos = [];
    $usuarios_cupones = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Cupones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            margin: 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-chart-line text-primary"></i> Estadísticas de Cupones</h1>
                    <div>
                        <a href="cupones.php" class="btn btn-primary me-2">
                            <i class="fas fa-ticket-alt"></i> Gestionar Cupones
                        </a>
                        <a href="nueva_ruta.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Rutas
                        </a>
                    </div>
                </div>

                <!-- Estadísticas generales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <h3><?= $stats->total_cupones ?></h3>
                            <p class="mb-0">Total Cupones</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%);">
                            <h3><?= $stats->cupones_activos ?></h3>
                            <p class="mb-0">Cupones Activos</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #FF9800 0%, #FFC107 100%);">
                            <h3><?= $stats->total_usos ?></h3>
                            <p class="mb-0">Total Usos</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card" style="background: linear-gradient(135deg, #E91E63 0%, #F06292 100%);">
                            <h3><?= number_format($total_ahorrado->total, 0) ?>€</h3>
                            <p class="mb-0">Total Ahorrado</p>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de usos mensuales -->
                <?php if(!empty($usos_mensuales)): ?>
                <div class="chart-container">
                    <h4><i class="fas fa-chart-line"></i> Usos de Cupones por Mes</h4>
                    <canvas id="chartUsosMensuales"></canvas>
                </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Cupones más populares -->
                    <div class="col-lg-6 mb-4">
                        <div class="table-container">
                            <h4 class="table-header"><i class="fas fa-trophy"></i> Cupones Más Populares</h4>
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Descuento</th>
                                            <th>Usos</th>
                                            <th>Total Ahorrado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($cupones_populares)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    No hay datos de cupones utilizados
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach($cupones_populares as $cupon): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= $cupon->codigo ?></strong>
                                                        <?php if($cupon->descripcion): ?>
                                                            <br><small class="text-muted"><?= htmlspecialchars($cupon->descripcion) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?= $cupon->tipo_descuento == 'porcentaje' ? 
                                                                $cupon->valor_descuento.'%' : 
                                                                number_format($cupon->valor_descuento, 2).'€' ?>
                                                        </span>
                                                    </td>
                                                    <td><?= $cupon->usos ?></td>
                                                    <td><strong><?= number_format($cupon->total_descuento, 2) ?>€</strong></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Rutas con más descuentos -->
                    <div class="col-lg-6 mb-4">
                        <div class="table-container">
                            <h4 class="table-header"><i class="fas fa-route"></i> Rutas con Más Descuentos</h4>
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ruta</th>
                                            <th>Plan</th>
                                            <th>Cupones Usados</th>
                                            <th>Total Descuentos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($rutas_descuentos)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    No hay datos de descuentos en rutas
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach($rutas_descuentos as $ruta): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($ruta->nombre) ?></td>
                                                    <td>
                                                        <span class="badge <?= $ruta->plan == 'Premium' ? 'bg-danger' : 'bg-success' ?>">
                                                            <?= $ruta->plan ?>
                                                        </span>
                                                    </td>
                                                    <td><?= $ruta->usos_cupones ?></td>
                                                    <td><strong><?= number_format($ruta->total_descuentos, 2) ?>€</strong></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usuarios que más han ahorrado -->
                <div class="table-container">
                    <h4 class="table-header"><i class="fas fa-users"></i> Usuarios que Más Han Ahorrado</h4>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Cupones Usados</th>
                                    <th>Total Ahorrado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($usuarios_cupones)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No hay datos de usuarios con cupones
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($usuarios_cupones as $usuario): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($usuario->username) ?></strong></td>
                                            <td><?= htmlspecialchars($usuario->email) ?></td>
                                            <td><?= $usuario->cupones_usados ?></td>
                                            <td><strong><?= number_format($usuario->total_ahorrado, 2) ?>€</strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if(!empty($usos_mensuales)): ?>
    <script>
        // Gráfico de usos mensuales
        const ctx = document.getElementById('chartUsosMensuales').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_reverse(array_column($usos_mensuales, 'mes'))) ?>,
                datasets: [{
                    label: 'Usos de Cupones',
                    data: <?= json_encode(array_reverse(array_column($usos_mensuales, 'usos'))) ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.4
                }, {
                    label: 'Total Descuentos (€)',
                    data: <?= json_encode(array_reverse(array_column($usos_mensuales, 'descuentos'))) ?>,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Uso de Cupones'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Número de Usos'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Descuentos (€)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>