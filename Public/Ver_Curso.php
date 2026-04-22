<?php
// Llamada del filtro de seguridad 
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('estudiante');

// --- 1. CONTROLADOR ---
include_once '../Config/Conexion.php';
include_once 'Includes/Aula.php';
include_once 'Includes/Comentario.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// PRIMERO CREAMOS LA CONEXIÓN
$conexion = ConexionDB::obtenerConexion();

// AHORA SÍ SE LA PASAMOS A LOS OBJETOS
$aulaObj = new Aula($conexion);
$comentarioObj = new Comentario($conexion);

$id_usuario = $_SESSION['usuario_id'];
$id_curso = $_GET['id'];

// Obtener la data usando el Modelo
$curso = $aulaObj->obtenerCurso($id_curso);
if (!$curso) { header("Location: index.php"); exit(); }

$lecciones = $aulaObj->obtenerLecciones($id_curso);
$progreso_array = $aulaObj->obtenerProgresoUsuario($id_usuario, $id_curso);
// --- NUEVO: CALCULAR PROGRESO Y CERTIFICADO ---
$total_lecciones = mysqli_num_rows($lecciones);
$lecciones_completadas = count($progreso_array);
$curso_terminado = ($total_lecciones > 0 && $total_lecciones == $lecciones_completadas);
$porcentaje_progreso = ($total_lecciones > 0) ? round(($lecciones_completadas / $total_lecciones) * 100) : 0;
// ----------------------------------------------
// Variables por defecto para la Vista
$leccion_actual = null;
$mi_tarea = null;
$completada = false;
$archivos_leccion = null;

// Si el usuario seleccionó una lección...
if (isset($_GET['lec_id'])) {
    $leccion_actual = $aulaObj->obtenerDetalleLeccion($_GET['lec_id']);
    
    if ($leccion_actual) {
        $completada = in_array($leccion_actual['id'], $progreso_array);
        
        // Traemos todos los archivos de esta lección
        $archivos_leccion = $aulaObj->obtenerArchivosLeccion($leccion_actual['id']);
        
        // Traemos los comentarios
        $lista_comentarios = $comentarioObj->obtenerPorLeccion($leccion_actual['id']);

        if ($leccion_actual['tiene_tarea']) {
            $mi_tarea = $aulaObj->obtenerMiTarea($id_usuario, $leccion_actual['id']);
        }
    }
}

// Incluimos el header visual
include_once 'Includes/Header.php';
?>

<style>
    /* Estilos Premium para las tarjetas */
    .premium-card {
        border-radius: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .icon-box-lg {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        font-size: 1.5rem;
    }
</style>

<div class="bg-dark text-white py-3 shadow-sm mb-4" style="background-color: #065b3e !important;">
    <div class="container d-flex align-items-center">
        <a class="btn btn-uabc-gold btn-sm me-3" href="index.php">
            <i class="fa-solid fa-arrow-left me-1"></i> Catálogo
        </a>
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-book-open me-2 text-warning"></i> <?php echo $curso['titulo']; ?></h5>
    </div>
</div>

<div class="container-fluid px-lg-5">
    <div class="row">
        
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header text-white fw-bold py-3" style="background-color: #065b3e;">
                    <i class="fa-solid fa-list-ul me-2"></i>Contenido del Curso
                </div>
                <div class="list-group list-group-flush sidebar-scroll">
                    <?php while($lec = mysqli_fetch_assoc($lecciones)): 
                        $es_activa = ($leccion_actual && $leccion_actual['id'] == $lec['id']) ? 'active' : '';
                        $esta_terminada = in_array($lec['id'], $progreso_array);
                    ?>
                        <a href="Ver_Curso.php?id=<?php echo $id_curso; ?>&lec_id=<?php echo $lec['id']; ?>" 
                           class="list-group-item list-group-item-action py-3 <?php echo $es_activa; ?>" 
                           style="<?php echo $es_activa ? 'background-color: #f8f9fa; color: #065b3e; border-left: 4px solid #bfa071;' : ''; ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-0 small fw-bold <?php echo $esta_terminada ? 'text-success' : ''; ?>">
                                    <?php if($esta_terminada) echo '<i class="fa-solid fa-circle-check me-1"></i>'; ?>
                                    <?php echo $lec['titulo']; ?>
                                </h6>
                                <i class="fa-solid fa-circle-play <?php echo $es_activa ? 'text-warning' : 'text-secondary opacity-50'; ?>"></i>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
                <?php if($curso_terminado): ?>
                    <div class="card-footer bg-white border-top border-light p-3 text-center">
                        <div class="alert alert-success py-2 mb-3" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-star text-warning me-1"></i> ¡Curso finalizado!
                        </div>
                        <a href="Certificado.php?curso_id=<?php echo $id_curso; ?>"  class="btn btn-uabc-gold w-100 fw-bold shadow-sm py-2">
                            <i class="fa-solid fa-graduation-cap me-2"></i> Ver Certificado
                        </a>
                    </div>
                <?php else: ?>
                    <div class="card-footer bg-light border-top border-light p-4 text-center">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold text-secondary">Tu progreso:</span>
                            <span class="small fw-bold text-success"><?php echo $porcentaje_progreso; ?>%</span>
                        </div>
                        <div class="progress shadow-sm" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" 
                                 style="width: <?php echo $porcentaje_progreso; ?>%;" 
                                 aria-valuenow="<?php echo $porcentaje_progreso; ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-3" style="font-size: 0.75rem;">
                            Completa todas las lecciones para obtener tu certificado oficial.
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8 col-lg-9">
            
            <?php if(isset($_GET['msj']) && $_GET['msj'] == 'tarea_ok'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" style="border-radius: 15px;">
                    <i class="fa-solid fa-check-circle me-2"></i> Tu archivo ha sido enviado correctamente al profesor.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if($leccion_actual): ?>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-bold mb-0" style="color: #065b3e;"><?php echo $leccion_actual['titulo']; ?></h3>
                    <button class="btn btn-warning fw-bold shadow-sm rounded-pill px-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#foroOffcanvas" aria-controls="foroOffcanvas">
                        <i class="fa-solid fa-comments me-2"></i> Foro de Dudas
                    </button>
                </div>
                
                <div class="video-wrapper shadow-sm mb-3" style="border-radius: 15px; overflow: hidden;">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/<?php echo $leccion_actual['contenido_url']; ?>?rel=0" title="Video lección" allowfullscreen></iframe>
                    </div>
                </div>

                <div class="mb-5">
                    <?php if(!$completada): ?>
                        <form action="Marcar_Completada.php" method="POST" class="m-0">
                            <input type="hidden" name="leccion_id" value="<?php echo $leccion_actual['id']; ?>">
                            <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow rounded-pill py-3" style="background-color: #198754; font-size: 1.1rem;">
                                <i class="fa-solid fa-circle-check me-2 fs-4 align-middle"></i> ¡He terminado de estudiar esta lección!
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-success shadow-sm border-0 text-center py-3 rounded-pill">
                            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-award me-2 text-warning fs-3 align-middle"></i> ¡Lección Completada con Éxito!</h5>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4 d-flex">
                        <?php if($archivos_leccion && mysqli_num_rows($archivos_leccion) > 0): ?>
                            <div class="card w-100 premium-card bg-white">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="icon-box-lg bg-danger bg-opacity-10 me-3 text-danger">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1">Material de Apoyo</h5>
                                            <p class="text-muted small mb-0">Recursos descargables de la clase.</p>
                                        </div>
                                    </div>
                                    <ul class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                                        <?php while($archivo = mysqli_fetch_assoc($archivos_leccion)): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom border-light">
                                                <div class="d-flex align-items-center overflow-hidden">
                                                    <i class="fa-solid fa-file-pdf text-danger fs-4 me-3"></i>
                                                    <div class="text-truncate">
                                                        <span class="d-block fw-bold text-dark text-truncate" style="max-width: 180px;" title="<?php echo $archivo['nombre_original']; ?>">
                                                            <?php echo $archivo['nombre_original']; ?>
                                                        </span>
                                                        <small class="text-muted">Documento PDF</small>
                                                    </div>
                                                </div>
                                                <a href="../<?php echo $archivo['ruta_archivo']; ?>" class="btn btn-outline-danger rounded-circle p-2" download title="Descargar">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-4 d-flex">
                        <?php if($leccion_actual['tiene_tarea']): ?>
                            <div class="card w-100 premium-card bg-white">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="icon-box-lg bg-primary bg-opacity-10 me-3 text-primary">
                                            <i class="fa-solid fa-cloud-arrow-up"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1">Actividad Práctica</h5>
                                            <p class="text-muted small mb-0">Sube tu archivo para evaluación.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto p-4 bg-light rounded-4 border border-light text-center">
                                        <?php if($mi_tarea): ?>
                                            <i class="fa-solid fa-check text-success display-4 mb-2"></i>
                                            <h6 class="fw-bold text-success mb-0">¡Tarea Entregada!</h6>
                                            <p class="small text-muted mt-1 mb-0">Fecha: <?php echo date('d/m/Y', strtotime($mi_tarea['fecha_envio'])); ?></p>
                                        <?php else: ?>
                                            <form action="subir_tarea.php" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="leccion_id" value="<?php echo $leccion_actual['id']; ?>">
                                                <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
                                                <div class="mb-3">
                                                    <input type="file" name="archivo_tarea" class="form-control bg-white shadow-sm" required>
                                                </div>
                                                <button class="btn btn-primary w-100 fw-bold shadow-sm rounded-pill" type="submit">
                                                    <i class="fa-solid fa-paper-plane me-2"></i> Enviar Archivo
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <div class="text-center py-5 mt-5 bg-white shadow-sm border-0" style="border-radius: 20px;">
                    <i class="fa-solid fa-photo-film display-1 text-secondary opacity-25"></i>
                    <h3 class="fw-bold mt-3 text-dark">Bienvenido a: <?php echo $curso['titulo']; ?></h3>
                    <p class="text-muted">Selecciona una lección del menú lateral para comenzar a aprender.</p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php if($leccion_actual): ?>
<div class="offcanvas offcanvas-end" tabindex="-1" id="foroOffcanvas" aria-labelledby="foroOffcanvasLabel" style="width: 450px;">
    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title fw-bold" id="foroOffcanvasLabel">
            <i class="fa-solid fa-comments text-warning me-2"></i> Foro de Dudas
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <div class="offcanvas-body" style="background-color: #f8f9fa;">
        <div class="comentarios-lista mb-4">
            <?php if($lista_comentarios && mysqli_num_rows($lista_comentarios) > 0): ?>
                <?php while($com = mysqli_fetch_assoc($lista_comentarios)): 
                    $es_maestro = ($com['rol'] == 'instructor');
                ?>
                    <div class="d-flex mb-4 <?php echo $es_maestro ? 'ms-4' : ''; ?>">
                        <div class="me-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                 style="width: 40px; height: 40px; background-color: <?php echo $es_maestro ? '#065b3e' : '#6c757d'; ?>;">
                                <?php echo substr($com['nombre'], 0, 1); ?>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded-4 shadow-sm w-100 <?php echo $es_maestro ? 'border border-success border-opacity-50' : ''; ?>">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="<?php echo $es_maestro ? 'text-success' : 'text-dark'; ?> small">
                                    <?php echo $com['nombre']; ?> <?php echo $es_maestro ? '<i class="fa-solid fa-chalkboard-user ms-1"></i>' : ''; ?>
                                </strong>
                                <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('d/m/Y H:i', strtotime($com['fecha'])); ?></small>
                            </div>
                            <p class="mb-0 text-secondary" style="font-size: 0.9rem;"><?php echo nl2br($com['comentario']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-regular fa-comments fs-1 opacity-25 mb-3 d-block"></i>
                    <p class="small">Aún no hay preguntas. ¡Sé el primero en participar!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="offcanvas-footer p-3 bg-white border-top">
        <form action="Includes/Comentario.php" method="POST">
            <input type="hidden" name="action" value="comentar">
            <input type="hidden" name="origen" value="alumno">
            <input type="hidden" name="leccion_id" value="<?php echo $leccion_actual['id']; ?>">
            <input type="hidden" name="curso_id" value="<?php echo $id_curso; ?>">
            <div class="form-floating mb-2">
                <textarea name="comentario" class="form-control" id="floatingTextarea" style="height: 80px" placeholder="Escribe tu duda aquí" required></textarea>
                <label for="floatingTextarea">Escribe tu duda aquí...</label>
            </div>
            <button class="btn btn-uabc-green w-100 fw-bold rounded-pill" type="submit">
                <i class="fa-solid fa-paper-plane me-2"></i> Publicar Pregunta
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'Includes/Footer.php'; ?>