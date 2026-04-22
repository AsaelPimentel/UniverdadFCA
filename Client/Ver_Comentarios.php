<?php
// 1. ESCUDO DE SEGURIDAD
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('instructor'); // Exigimos rol de maestro

// 2. INCLUIMOS ARCHIVOS NECESARIOS CON RUTAS ABSOLUTAS
include 'Includes/header.php';
require_once __DIR__ . '/../Config/Conexion.php';
require_once __DIR__ . '/Includes/Comentario.php';

$conexion = ConexionDB::obtenerConexion();
$comentarioObj = new Comentario($conexion);

// === LÓGICA PARA MARCAR NOTIFICACIÓN COMO LEÍDA ===
if (isset($_GET['notif_id'])) {
    $notif_id = mysqli_real_escape_string($conexion, $_GET['notif_id']);
    mysqli_query($conexion, "UPDATE notificaciones SET leida = 1 WHERE id = '$notif_id'");
}

if (!isset($_GET['leccion_id'])) {
    echo "<script>window.history.back();</script>";
    exit();
}

$leccion_id = mysqli_real_escape_string($conexion, $_GET['leccion_id']);
$curso_id = mysqli_real_escape_string($conexion, $_GET['curso_id']);

// Traer nombre de la lección
$res_lec = mysqli_query($conexion, "SELECT titulo FROM lecciones WHERE id = '$leccion_id'");
$leccion = mysqli_fetch_assoc($res_lec);

$comentarios = $comentarioObj->obtenerPorLeccion($leccion_id);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold" style="color: #065b3e;">
            <i class="fa-solid fa-comments text-warning me-2"></i> Dudas: <?php echo $leccion['titulo']; ?>
        </h4>
        <a href="Nueva_Leccion.php?curso_id=<?php echo $curso_id; ?>" class="btn btn-uabc-gold">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Lecciones
        </a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body p-4">
            
            <form action="Includes/Comentario.php" method="POST" class="mb-5">
                <input type="hidden" name="action" value="comentar">
                <input type="hidden" name="origen" value="maestro">
                <input type="hidden" name="leccion_id" value="<?php echo $leccion_id; ?>">
                <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">
                
                <label class="fw-bold mb-2">Escribir una respuesta general:</label>
                <div class="input-group shadow-sm rounded">
                    <textarea name="comentario" class="form-control bg-light" rows="3" placeholder="Responde a las dudas de tus alumnos..." required></textarea>
                    <button class="btn btn-uabc-green fw-bold px-4" type="submit"><i class="fa-solid fa-reply"></i> Publicar</button>
                </div>
            </form>

            <hr>

            <div class="mt-4">
                <?php if(mysqli_num_rows($comentarios) > 0): ?>
                    <?php while($com = mysqli_fetch_assoc($comentarios)): 
                        $es_maestro = ($com['rol'] == 'instructor');
                    ?>
                        <div class="d-flex mb-4">
                            <div class="me-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                     style="width: 45px; height: 45px; background-color: <?php echo $es_maestro ? '#065b3e' : '#a67c00'; ?>;">
                                    <?php echo substr($com['nombre'], 0, 1); ?>
                                </div>
                            </div>
                            <div class="w-100 p-3 rounded bg-light <?php echo $es_maestro ? 'border-start border-success border-4' : 'border border-light'; ?>">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong class="<?php echo $es_maestro ? 'text-success' : 'text-dark'; ?>">
                                        <?php echo $com['nombre']; ?>
                                        <?php if($es_maestro) echo '<span class="badge bg-success ms-2">Instructor</span>'; ?>
                                    </strong>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($com['fecha'])); ?></small>
                                </div>
                                <p class="mb-0"><?php echo nl2br($com['comentario']); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa-regular fa-comments display-4 opacity-25 mb-3 d-block"></i>
                        <p>No hay preguntas en esta lección todavía.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>