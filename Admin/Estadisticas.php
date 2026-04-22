<?php
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('admin');

include 'Includes/Header.php';
require_once __DIR__ . '/../Config/Conexion.php';
$db = ConexionDB::obtenerConexion();

// ==========================================
// 0. CAPTURAR EL FILTRO DE FECHAS
// ==========================================
$rango = isset($_GET['rango']) ? $_GET['rango'] : '6m';

// Ajustamos los formatos y límites de SQL dependiendo de la selección
if ($rango === '7d') {
    $formato_fecha = "'%Y-%m-%d'"; // Agrupar por DÍA
    $limite_linea = 7;
    $limite_barras = 7;
} elseif ($rango === '1m') {
    $formato_fecha = "'%Y-%m-%d'"; // Agrupar por DÍA
    $limite_linea = 30;
    $limite_barras = 15; // Limitamos a 15 para no saturar las barras
} elseif ($rango === '1y') {
    $formato_fecha = "'%Y-%m'";    // Agrupar por MES
    $limite_linea = 12;
    $limite_barras = 30;
} else {
    // Por defecto: 6 Meses ('6m')
    $formato_fecha = "'%Y-%m'";    // Agrupar por MES
    $limite_linea = 6;
    $limite_barras = 14;
}

// ==========================================
// 1. CONSULTAS PARA EL KPI BANNER (Visión Global Total)
// ==========================================
$q_est = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'estudiante'"));
$q_cur = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM cursos"));
$q_lec = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM lecciones"));

// ==========================================
// 2. CONSULTA: Nuevos Usuarios (Dinámica según el filtro)
// ==========================================
$sql_usuarios_mes = "
    SELECT * FROM (
        SELECT DATE_FORMAT(fecha_registro, $formato_fecha) as periodo, COUNT(*) as total 
        FROM usuarios 
        GROUP BY periodo 
        ORDER BY periodo DESC 
        LIMIT $limite_linea
    ) as sub 
    ORDER BY periodo ASC";

$res_usu_mes = mysqli_query($db, $sql_usuarios_mes);
$labels_meses = [];
$data_usuarios = [];
while ($row = mysqli_fetch_assoc($res_usu_mes)) {
    $labels_meses[] = $row['periodo'];
    $data_usuarios[] = $row['total'];
}

// ==========================================
// 3. CONSULTAS: Actividad Diaria (Barras Apiladas dinámicas)
// ==========================================
$actividad_fechas = [];

// Lecciones completadas
$sql_progreso = "SELECT DATE(fecha_completado) as fecha, COUNT(*) as total FROM progreso_lecciones GROUP BY fecha ORDER BY fecha DESC LIMIT $limite_barras";
$res_progreso = mysqli_query($db, $sql_progreso);
while ($row = mysqli_fetch_assoc($res_progreso)) {
    $actividad_fechas[$row['fecha']] = ['lecciones' => $row['total'], 'tareas' => 0, 'comentarios' => 0];
}

// Tareas entregadas
$sql_tareas = "SELECT DATE(fecha_envio) as fecha, COUNT(*) as total FROM tareas_entregadas GROUP BY fecha ORDER BY fecha DESC LIMIT $limite_barras";
$res_tareas = mysqli_query($db, $sql_tareas);
while ($row = mysqli_fetch_assoc($res_tareas)) {
    if (!isset($actividad_fechas[$row['fecha']])) $actividad_fechas[$row['fecha']] = ['lecciones' => 0, 'tareas' => 0, 'comentarios' => 0];
    $actividad_fechas[$row['fecha']]['tareas'] = $row['total'];
}

// Comentarios
$sql_comentarios = "SELECT DATE(fecha) as fecha, COUNT(*) as total FROM comentarios GROUP BY fecha ORDER BY fecha DESC LIMIT $limite_barras";
$res_comentarios = mysqli_query($db, $sql_comentarios);
while ($row = mysqli_fetch_assoc($res_comentarios)) {
    if (!isset($actividad_fechas[$row['fecha']])) $actividad_fechas[$row['fecha']] = ['lecciones' => 0, 'tareas' => 0, 'comentarios' => 0];
    $actividad_fechas[$row['fecha']]['comentarios'] = $row['total'];
}

// Ordenar fechas cronológicamente
ksort($actividad_fechas);

$labels_actividad = array_keys($actividad_fechas);
$data_lec = [];
$data_tar = [];
$data_com = [];
foreach ($actividad_fechas as $fecha => $datos) {
    $data_lec[] = $datos['lecciones'];
    $data_tar[] = $datos['tareas'];
    $data_com[] = $datos['comentarios'];
}
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* VARIABLES BI UABC */
    :root {
        --bi-green: #00723F;
        --bi-gold: #F0B323;
        --bi-bg: #f5f6f8;
        --bi-card: #ffffff;
        --bi-text: #333333;
        --bi-muted: #8898aa;
    }

    body {
        background-color: var(--bi-bg);
        font-family: 'Inter', sans-serif;
    }

    .bi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    /* ==========================================
       ESTILOS DEL FILTRO DE FECHAS (PREMIUM UI)
       ========================================== */
    .date-filter-form {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 0.4rem 0.8rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .date-filter-form:hover {
        border-color: var(--bi-green);
        box-shadow: 0 4px 12px rgba(0, 114, 63, 0.1);
    }

    .date-filter-form .fa-calendar {
        color: var(--bi-green) !important;
        font-size: 1.05rem;
        transition: transform 0.3s ease;
    }

    .date-filter-form:hover .fa-calendar {
        transform: scale(1.1);
    }

    .date-filter-form select {
        border: none;
        background: transparent;
        color: #475569;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        outline: none;
        box-shadow: none;
        padding-top: 0;
        padding-bottom: 0;
    }

    .date-filter-form select:focus {
        box-shadow: none;
        border: none;
        color: var(--bi-green);
    }

    .date-filter-form select option {
        font-weight: 500;
        color: #334155;
        background-color: #ffffff;
        padding: 10px;
    }

    /* Tarjetas KPI */
    .kpi-card {
        background: var(--bi-card);
        border-radius: 16px;
        border: none;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
    }

    .kpi-info h5 {
        color: var(--bi-muted);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .kpi-info h2 {
        color: var(--bi-text);
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .kpi-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .bg-green-light {
        background: rgba(0, 114, 63, 0.1);
        color: var(--bi-green);
    }

    .bg-gold-light {
        background: rgba(240, 179, 35, 0.1);
        color: var(--bi-gold);
    }

    .bg-blue-light {
        background: rgba(0, 123, 255, 0.1);
        color: #007bff;
    }

    /* Tarjetas de Gráficos */
    .chart-container-card {
        background: var(--bi-card);
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--bi-text);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Estilos para las opciones desplegables */
    .custom-option {
        font-weight: 500;
        color: #334155;
        background-color: #ffffff;
        padding: 10px;
        font-size: 0.95rem;
    }

    /* Estilo para la opción que el usuario tiene seleccionada actualmente */
    .custom-option:checked {
        background-color: var(--bi-green) !important;
        color: #ffffff !important;
        font-weight: 700;
    }

    /* Ligero cambio de color al pasar el mouse (soportado en navegadores de Windows) */
    .custom-option:hover {
        background-color: #f8fafc;
        color: var(--bi-green);
    }
</style>

<div class="container mt-4 mb-5">

    <div class="bi-header">
        <div>
            <h3 class="fw-bold mb-1">Visión Global (Administrador)</h3>
            <p class="text-muted small mb-0">Monitoreo de salud e interacción de la plataforma.</p>
        </div>
        <div class="d-flex align-items-center gap-3">

            <form id="filtroDashboard" method="GET" action="" class="m-0">
                <div class="date-filter-form">
                    <i class="fa-regular fa-calendar text-muted ms-2"></i>
                    <select name="rango" class="form-select form-select-sm" onchange="document.getElementById('filtroDashboard').submit();">
                        <option value="7d" class="custom-option" <?php echo ($rango == '7d') ? 'selected' : ''; ?>>Últimos 7 Días</option>
                        <option value="1m" class="custom-option" <?php echo ($rango == '1m') ? 'selected' : ''; ?>>Último Mes</option>
                        <option value="6m" class="custom-option" <?php echo ($rango == '6m') ? 'selected' : ''; ?>>Últimos 6 Meses</option>
                        <option value="1y" class="custom-option" <?php echo ($rango == '1y') ? 'selected' : ''; ?>>Último Año</option>
                    </select>
                </div>
            </form>

            <a href="index.php" class="btn btn-dark" style="border-radius: 8px;">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row mb-4 g-4">
        <div class="col-md-4">
            <div class="kpi-card border-bottom border-success border-4">
                <div class="kpi-info">
                    <h5>Estudiantes Activos</h5>
                    <h2><?php echo number_format($q_est['total']); ?></h2>
                </div>
                <div class="kpi-icon bg-green-light">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card border-bottom border-warning border-4">
                <div class="kpi-info">
                    <h5>Cursos Totales</h5>
                    <h2><?php echo number_format($q_cur['total']); ?></h2>
                </div>
                <div class="kpi-icon bg-gold-light">
                    <i class="fa-solid fa-laptop-file"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card border-bottom border-primary border-4">
                <div class="kpi-info">
                    <h5>Lecciones Publicadas</h5>
                    <h2><?php echo number_format($q_lec['total']); ?></h2>
                </div>
                <div class="kpi-icon bg-blue-light">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="chart-container-card">
                <div class="chart-title d-flex justify-content-between w-100">
                    <div><i class="fa-solid fa-chart-line text-success"></i> Nuevos Usuarios (Registros)</div>
                    <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.8rem;">
                        <?php
                        if ($rango == '7d') echo "Viendo: Por Día (7 Días)";
                        elseif ($rango == '1m') echo "Viendo: Por Día (Mes actual)";
                        else echo "Viendo: Por Meses";
                        ?>
                    </span>
                </div>
                <div style="height: 300px; width: 100%;">
                    <canvas id="lineChartUsuarios"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="chart-container-card">
                <div class="chart-title">
                    <i class="fa-solid fa-cubes-stacked text-warning"></i> Actividad Diaria (Interacción)
                </div>
                <div style="height: 350px; width: 100%;">
                    <canvas id="barChartActividad"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Configuración global de la tipografía para los gráficos
    Chart.defaults.font.family = "'Inter', sans-serif";

    // 1. GRÁFICO DE ÁREA: NUEVOS USUARIOS
    const ctxLine = document.getElementById('lineChartUsuarios');
    if (ctxLine) {
        let contextLine = ctxLine.getContext('2d');
        let gradientGreen = contextLine.createLinearGradient(0, 0, 0, 400);
        gradientGreen.addColorStop(0, 'rgba(0, 114, 63, 0.5)');
        gradientGreen.addColorStop(1, 'rgba(0, 114, 63, 0.0)');

        new Chart(contextLine, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels_meses); ?>,
                datasets: [{
                    label: 'Usuarios Registrados',
                    data: <?php echo json_encode($data_usuarios); ?>,
                    borderColor: '#00723F',
                    backgroundColor: gradientGreen,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#00723F',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            borderDash: [5, 5],
                            color: '#e2e8f0'
                        },
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // 2. GRÁFICO DE BARRAS APILADAS: ACTIVIDAD DIARIA
    const ctxBar = document.getElementById('barChartActividad');
    if (ctxBar) {
        new Chart(ctxBar.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_actividad); ?>,
                datasets: [{
                        label: 'Lecciones Completadas',
                        data: <?php echo json_encode($data_lec); ?>,
                        backgroundColor: '#00723F',
                        borderRadius: 4
                    },
                    {
                        label: 'Tareas Enviadas',
                        data: <?php echo json_encode($data_tar); ?>,
                        backgroundColor: '#F0B323',
                        borderRadius: 4
                    },
                    {
                        label: 'Comentarios',
                        data: <?php echo json_encode($data_com); ?>,
                        backgroundColor: '#17a2b8',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: true,
                        grid: {
                            borderDash: [5, 5],
                            color: '#e2e8f0'
                        },
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
</script>

<?php include 'Includes/Footer.php'; ?>