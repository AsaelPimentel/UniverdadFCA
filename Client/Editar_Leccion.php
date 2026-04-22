<?php
// 1. ESCUDO DE SEGURIDAD
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('instructor'); // Exigimos rol de maestro

// 2. INCLUIMOS HEADER Y CONEXIÓN
include 'Includes/header.php';
require_once __DIR__ . '/../Config/Conexion.php';

$conexion = ConexionDB::obtenerConexion();

// 3. OBTENER DATOS DE LA LECCIÓN
$id_leccion = mysqli_real_escape_string($conexion, $_GET['id']);
$res = mysqli_query($conexion, "SELECT * FROM lecciones WHERE id = $id_leccion");
$lec = mysqli_fetch_assoc($res);
?>

<div class="container mt-4">
    <a href="nueva_leccion.php?curso_id=<?php echo $lec['curso_id']; ?>" class="btn btn-uabc-gold px-3 py-2 mb-4 shadow-sm">
        <i class="fa-solid fa-circle-arrow-left me-2"></i>Volver al panel
    </a>
</div>

<div class="container mt-5">
    <div class="card shadow border-0 mx-auto" style="max-width: 600px; border-radius: 20px;">
        <div class="card-body p-5">
            <h3 class="fw-bold mb-4" style="color: #065b3e;">
                <i class="fa-solid fa-file-pen me-2"></i>Editar Lección
            </h3>

            <form action="Includes/Leccion.php" method="POST" enctype="multipart/form-data"> <input type="hidden" name="id" value="<?php echo $lec['id']; ?>">
                <input type="hidden" name="curso_id" value="<?php echo $lec['curso_id']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">TÍTULO</label>
                    <input type="text" name="titulo" class="form-control bg-light border-0" value="<?php echo $lec['titulo']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">ID DE VIDEO YOUTUBE</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-brands fa-youtube text-danger"></i></span>
                        <input type="text" name="url" class="form-control bg-light border-0" value="<?php echo $lec['contenido_url']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-danger">
                        <i class="fa-solid fa-file-pdf me-1"></i>CAMBIAR PDF OPCIONAL (Puedes seleccionar varios)
                    </label>
                    <input type="file" name="pdf_files[]" class="form-control bg-light" accept=".pdf" multiple>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="tiene_tarea" id="checkTarea" <?php echo ($lec['tiene_tarea'] == 1) ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-bold" for="checkTarea">¿Habilitar entrega de tarea?</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-outline-success py-2 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios
                    </button>
                    <a href="nueva_leccion.php?curso_id=<?php echo $lec['curso_id']; ?>" class="btn btn-outline-danger py-2 fw-bold border-0">
                        <i class="fa-solid fa-xmark me-2"></i>Cancelar y Volver
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>