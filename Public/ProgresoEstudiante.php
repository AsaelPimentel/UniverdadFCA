<?php 
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('estudiante'); 

include 'Includes/Header.php'; 
require_once __DIR__ . '/../Config/Conexion.php';
$db = ConexionDB::obtenerConexion();

$id_estudiante = $_SESSION['usuario_id'];
$nombre_estudiante = $_SESSION['nombre'];

// 1. CONSULTA: CURSOS CON PROGRESO CALCULADO
$sql_cursos = "SELECT c.id, c.titulo, c.imagen,
                (SELECT COUNT(*) FROM lecciones l WHERE l.curso_id = c.id) as total_lecciones,
                (SELECT COUNT(*) FROM progreso_lecciones pl 
                 JOIN lecciones l2 ON pl.leccion_id = l2.id 
                 WHERE l2.curso_id = c.id AND pl.usuario_id = $id_estudiante) as completadas
               FROM cursos c
               WHERE EXISTS (
                   SELECT 1 FROM progreso_lecciones pl2 
                   JOIN lecciones l3 ON pl2.leccion_id = l3.id 
                   WHERE l3.curso_id = c.id AND pl2.usuario_id = $id_estudiante
               )";
$res_cursos = mysqli_query($db, $sql_cursos);

// 2. CONSULTA: ACTIVIDAD RECIENTE (TIMELINE)
$sql_timeline = "(SELECT 'leccion' as tipo, l.titulo as detalle, pl.fecha_completado as fecha 
                  FROM progreso_lecciones pl 
                  JOIN lecciones l ON pl.leccion_id = l.id 
                  WHERE pl.usuario_id = $id_estudiante)
                  UNION
                  (SELECT 'tarea' as tipo, l.titulo as detalle, te.fecha_envio as fecha 
                  FROM tareas_entregadas te 
                  JOIN lecciones l ON te.leccion_id = l.id 
                  WHERE te.usuario_id = $id_estudiante)
                  ORDER BY fecha DESC LIMIT 8";
$res_timeline = mysqli_query($db, $sql_timeline);

$frases = [
    "¡Sigue así! Cada paso te acerca más a tu meta.",
    "El aprendizaje es un tesoro que te seguirá a todas partes.",
    "Tu esfuerzo de hoy es el éxito de mañana.",
    "La constancia es la clave del dominio."
];
$frase_hoy = $frases[array_rand($frases)];
?>

<style>
    :root { --uabc-green: #00723F; --uabc-gold: #F0B323; }
    body { 
        background: linear-gradient(135deg, rgba(240, 244, 243, 0.8) 0%, rgba(216, 226, 223, 0.8) 100%), 
                    url("../Assets/Img/FondoCimarron.jpg"); 
        background-attachment: fixed; 
        background-size: cover; 
    }
    
    .welcome-banner { 
        background: white; 
        border-radius: 20px; 
        padding: 30px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        margin-top: 20px; 
    }
    
    .course-card { 
        background: white; 
        border-radius: 15px; 
        border: none; 
        transition: transform 0.3s; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
    }
    
    .course-card:hover { transform: translateY(-5px); }
    .progress { height: 10px; border-radius: 5px; background-color: #e9ecef; }
    .progress-bar { background-color: var(--uabc-green); }
    
    /* Estilo del Timeline */
    .timeline { position: relative; padding-left: 30px; border-left: 3px solid var(--uabc-gold); margin-left: 15px; }
    .timeline-item { position: relative; margin-bottom: 20px; }
    .timeline-item::before { 
        content: ''; position: absolute; left: -39px; top: 5px; width: 15px; height: 15px; 
        background: var(--uabc-green); border-radius: 50%; border: 3px solid white; 
    }

    /* BOTÓN DE REGRESO UI/UX */
    .btn-return {
        background-color: white;
        color: var(--uabc-green);
        border: 2px solid var(--uabc-green);
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-return:hover {
        background-color: var(--uabc-green);
        color: white;
        transform: translateX(-5px);
        box-shadow: 0 4px 12px rgba(0, 114, 63, 0.2);
    }
</style>

<div class="container mb-5">
    <div class="welcome-banner mb-4 border-start border-5 border-success d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold" style="color: var(--uabc-green);">¡Hola, <?php echo explode(' ', $nombre_estudiante)[0]; ?>! 👋</h2>
            <p class="fst-italic text-muted mb-0">"<?php echo $frase_hoy; ?>"</p>
        </div>
        
        <a href="Index.php" class="btn btn-return px-4 py-2 rounded-pill shadow-sm">
            <i class="fa-solid fa-house-user me-2"></i> Menú Principal
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4"><i class="fa-solid fa-graduation-cap me-2 text-success"></i>Mis Cursos en Progreso</h4>
            <div class="row g-4">
                <?php if (mysqli_num_rows($res_cursos) > 0): ?>
                    <?php while ($curso = mysqli_fetch_assoc($res_cursos)): 
                        $porcentaje = ($curso['total_lecciones'] > 0) ? round(($curso['completadas'] / $curso['total_lecciones']) * 100) : 0;
                    ?>
                    <div class="col-md-6">
                        <div class="card course-card h-100 p-3">
                            <h6 class="fw-bold text-dark mb-3"><?php echo $curso['titulo']; ?></h6>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted"><?php echo $porcentaje; ?>% completado</small>
                                <small class="fw-bold" style="color: var(--uabc-green);"><?php echo $curso['completadas']; ?>/<?php echo $curso['total_lecciones']; ?> Lecciones</small>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentaje; ?>%"></div>
                            </div>
                            <a href="Ver_Curso.php?id=<?php echo $curso['id']; ?>" class="btn btn-sm btn-outline-success rounded-pill">Continuar aprendiendo</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12"><div class="alert alert-light border-0 shadow-sm">Aún no has iniciado ningún curso. ¡Ve al catálogo y comienza hoy!</div></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Actividad Reciente</h5>
                <div class="timeline">
                    <?php while ($act = mysqli_fetch_assoc($res_timeline)): ?>
                    <div class="timeline-item">
                        <small class="text-muted d-block" style="font-size: 0.75rem;"><?php echo date('d M, H:i', strtotime($act['fecha'])); ?></small>
                        <p class="mb-0 small">
                            <?php if ($act['tipo'] == 'leccion'): ?>
                                <span class="badge bg-success-subtle text-success me-1">Lección</span> Completaste <strong><?php echo $act['detalle']; ?></strong>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning me-1">Tarea</span> Enviaste la actividad de <strong><?php echo $act['detalle']; ?></strong>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>