<?php 
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('instructor'); // Solo instructores pueden ver su rendimiento

include 'Includes/Header.php'; 
require_once __DIR__ . '/../Config/Conexion.php';
$db = ConexionDB::obtenerConexion();

$id_instructor = $_SESSION['usuario_id']; // ID del instructor actual

// ==========================================
// 1. OBTENER LISTA DE CURSOS DEL INSTRUCTOR
// ==========================================
$sql_mis_cursos = "SELECT id, titulo FROM cursos WHERE instructor_id = $id_instructor";
$res_mis_cursos = mysqli_query($db, $sql_mis_cursos);
$cursos = [];
while($c = mysqli_fetch_assoc($res_mis_cursos)) { $cursos[] = $c; }

// Seleccionar curso actual (por defecto el primero)
$id_curso_sel = isset($_GET['curso_id']) ? (int)$_GET['curso_id'] : ($cursos[0]['id'] ?? 0);

// ==========================================
// 2. CONSULTAS DE BI (KPIs)
// ==========================================
if ($id_curso_sel > 0) {
    // A. Tasa de Finalización: (Lecciones completadas por el total de lecciones del curso)
    $q_total_lec = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM lecciones WHERE curso_id = $id_curso_sel"));
    $total_lecciones = $q_total_lec['total'] > 0 ? $q_total_lec['total'] : 1;

    // Promedio de progreso de alumnos (Asumiendo que alumnos inscritos son los que tienen al menos un progreso)
    $q_progreso = mysqli_fetch_assoc(mysqli_query($db, "
        SELECT AVG(progreso) as promedio_finalizacion FROM (
            SELECT (COUNT(pl.id) / $total_lecciones) * 100 as progreso 
            FROM progreso_lecciones pl 
            JOIN lecciones l ON pl.leccion_id = l.id 
            WHERE l.curso_id = $id_curso_sel 
            GROUP BY pl.usuario_id
        ) as subquery"));
    $tasa_finalizacion = round($q_progreso['promedio_finalizacion'] ?? 0, 1);

    // B. Tasa de Entrega de Tareas
    $q_alumnos = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(DISTINCT usuario_id) as total FROM progreso_lecciones pl JOIN lecciones l ON pl.leccion_id = l.id WHERE l.curso_id = $id_curso_sel"));
    $total_alumnos = $q_alumnos['total'] > 0 ? $q_alumnos['total'] : 1;
    
    $q_tareas = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM tareas_entregadas te JOIN lecciones l ON te.leccion_id = l.id WHERE l.curso_id = $id_curso_sel"));
    $tasa_tareas = round(($q_tareas['total'] / $total_alumnos) * 100, 1);

    // C. Datos para el Gráfico de Embudo (Funnel)
    $sql_funnel = "SELECT l.titulo, COUNT(pl.id) as completados 
                   FROM lecciones l 
                   LEFT JOIN progreso_lecciones pl ON l.id = pl.leccion_id 
                   WHERE l.curso_id = $id_curso_sel 
                   GROUP BY l.id ORDER BY l.id ASC";
    $res_funnel = mysqli_query($db, $sql_funnel);
    $labels_funnel = [];
    $data_funnel = [];
    while($f = mysqli_fetch_assoc($res_funnel)) {
        $labels_funnel[] = $f['titulo'];
        $data_funnel[] = $f['completados'];
    }

    // D. Tabla de Feedback (Zonas de Fricción)
    $sql_friccion = "SELECT l.id, l.titulo, COUNT(c.id) as dudas 
                     FROM lecciones l 
                     LEFT JOIN comentarios c ON l.id = c.leccion_id 
                     WHERE l.curso_id = $id_curso_sel 
                     GROUP BY l.id HAVING dudas > 0 ORDER BY dudas DESC";
    $res_friccion = mysqli_query($db, $sql_friccion);
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root { --uabc-green: #00723F; --uabc-gold: #F0B323; }
    .dashboard-instructor { background: #f4f7f6; min-height: 100vh; padding: 30px 0; }
    .kpi-gauge { background: white; border-radius: 20px; padding: 25px; text-align: center; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
    .gauge-value { font-size: 2.5rem; font-weight: 800; color: var(--uabc-green); }
    .chart-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
    .table-friccion thead { background: var(--uabc-green); color: white; }
    .selector-curso { background: white; border-radius: 12px; border: 2px solid #eee; padding: 10px 20px; font-weight: 600; }
</style>

<div class="dashboard-instructor">
    <div class="container">
        
        <div class="row align-items-center mb-5">
            <div class="col-md-7">
                <h2 class="fw-bold" style="color: var(--uabc-green);">Rendimiento del Curso</h2>
                <p class="text-muted">Análisis detallado de retención y participación.</p>
            </div>
            <div class="col-md-5">
                <form method="GET" action="" id="formCurso">
                    <label class="small fw-bold text-muted text-uppercase mb-2">Selecciona un Curso:</label>
                    <select name="curso_id" class="form-select selector-curso" onchange="this.form.submit()">
                        <?php foreach($cursos as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($id_curso_sel == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo $c['titulo']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <?php if($id_curso_sel > 0): ?>
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="kpi-gauge border-start border-5 border-success">
                    <h6 class="text-muted fw-bold small">TASA DE FINALIZACIÓN</h6>
                    <div class="gauge-value"><?php echo $tasa_finalizacion; ?>%</div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar" style="width: <?php echo $tasa_finalizacion; ?>%; background: var(--uabc-green);"></div>
                    </div>
                    <p class="small text-muted mt-2">Promedio de lecciones vistas por alumno</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="kpi-gauge border-start border-5 border-warning">
                    <h6 class="text-muted fw-bold small">ENTREGA DE TAREAS</h6>
                    <div class="gauge-value" style="color: var(--uabc-gold);"><?php echo $tasa_tareas; ?>%</div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar" style="width: <?php echo $tasa_tareas; ?>%; background: var(--uabc-gold);"></div>
                    </div>
                    <p class="small text-muted mt-2">Relación tareas enviadas / alumnos inscritos</p>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="chart-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-filter me-2 text-success"></i> Embudo de Progreso de Lecciones</h5>
                    <div style="height: 400px;">
                        <canvas id="funnelChart"></canvas>
                    </div>
                    <p class="small text-center text-muted mt-3">Muestra cuántos alumnos completan cada etapa del curso.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="chart-card">
                    <h5 class="fw-bold mb-4 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Zonas de Fricción (Dudas)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th># ID</th>
                                    <th>Título de la Lección</th>
                                    <th class="text-center">Comentarios / Dudas</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($fric = mysqli_fetch_assoc($res_friccion)): ?>
                                <tr>
                                    <td><?php echo $fric['id']; ?></td>
                                    <td class="fw-bold"><?php echo $fric['titulo']; ?></td>
                                    <td class="text-center"><span class="badge bg-danger rounded-pill"><?php echo $fric['dudas']; ?></span></td>
                                    <td class="text-center">
                                        <a href="Ver_Comentarios.php?leccion_id=<?php echo $fric['id']; ?>" class="btn btn-sm btn-outline-success">Atender</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-5">Aún no tienes cursos creados para analizar.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Gráfico de Embudo (Usando barras horizontales para simular retención)
    const ctx = document.getElementById('funnelChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels_funnel); ?>,
            datasets: [{
                label: 'Alumnos que completaron',
                data: <?php echo json_encode($data_funnel); ?>,
                backgroundColor: 'rgba(0, 114, 63, 0.7)',
                borderColor: '#00723F',
                borderWidth: 2,
                borderRadius: 5,
                barThickness: 30
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true, grid: { display: false } },
                y: { grid: { display: false } }
            }
        }
    });
</script>

<?php include 'Includes/Footer.php'; ?>