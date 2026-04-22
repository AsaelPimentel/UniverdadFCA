<?php
// 1. ESCUDO DE SEGURIDAD
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('instructor'); // Exigimos rol de maestro

// 2. INCLUIMOS EL HEADER Y LA CONEXIÓN
include 'Includes/header.php';
require_once __DIR__ . '/../Config/Conexion.php';

$conexion = ConexionDB::obtenerConexion();

if (!isset($_GET['curso_id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$curso_id = mysqli_real_escape_string($conexion, $_GET['curso_id']);

// Consultas
$query_lecciones = "SELECT * FROM lecciones WHERE curso_id = '$curso_id' ORDER BY id ASC";
$lecciones = mysqli_query($conexion, $query_lecciones);
?>

<?php if (isset($_GET['msj']) && $_GET['msj'] == 'ok'): ?>
    <div id="toast-uabc" class="alert alert-success shadow-lg border-0"
        style="position: fixed; top: 20px; right: 20px; z-index: 9999; border-radius: 15px;">
        <i class="fa-solid fa-circle-check me-2"></i> Lección guardada correctamente.
    </div>
    <script>
        setTimeout(() => {
            let el = document.getElementById('toast-uabc');
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
            window.history.replaceState({}, document.title, window.location.pathname + "?curso_id=<?php echo $curso_id; ?>");
        }, 3000);
    </script>
<?php endif; ?>

<div class="container mt-4">
    <a href="index.php" class="btn btn-uabc-gold px-3 py-2 mb-4">
        <i class="fa-solid fa-circle-arrow-left me-2"></i>Volver al panel
    </a>

    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm p-4 mb-4" style="border-radius: 15px; border: none;">
                <h5 class="fw-bold mb-4" style="color: #065b3e;">
                    <i class="fa-solid fa-file-circle-plus me-2"></i>Nueva Lección
                </h5>

                <form action="Includes/Leccion.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="curso_id" value="<?php echo $curso_id; ?>">

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary">TÍTULO DE LA CLASE</label>
                        <input type="text" name="titulo_leccion" class="form-control bg-light" placeholder="Ej. 1.1 Introducción" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary">URL DE VIDEO (YOUTUBE)</label>
                        <input type="text" name="url_video" class="form-control bg-light" placeholder="https://youtube.com/..." required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-danger">
                            <i class="fa-solid fa-file-pdf me-1"></i>SUBIR MATERIALES (Puedes seleccionar varios)
                        </label>
                        <input type="file" name="pdf_files[]" class="form-control bg-light" accept=".pdf" multiple>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="tiene_tarea" id="checkTarea">
                        <label class="form-check-label fw-bold" for="checkTarea">¿Habilitar tarea?</label>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Lección
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white fw-bold py-3" style="color: #065b3e;">
                    <i class="fa-solid fa-list-ul me-2"></i>Contenido Actual
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Lección</th>
                                <th>Extras</th>
                                <th class="text-center">Acciones</th>
                                <th>comentarios</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($l = mysqli_fetch_assoc($lecciones)): ?>
                                <tr>
                                    <td class="ps-3"><span class="badge bg-secondary">#<?php echo $l['id']; ?></span></td>
                                    <td class="small fw-bold text-dark"><?php echo $l['titulo']; ?></td>
                                    <td>
                                        <?php if (!empty($l['pdf_ruta'])): ?>
                                            <i class="fa-solid fa-file-pdf text-danger mx-1"></i>
                                        <?php endif; ?>
                                        <?php if ($l['tiene_tarea']): ?>
                                            <i class="fa-solid fa-file-signature text-success mx-1"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="editar_leccion.php?id=<?php echo $l['id']; ?>" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-pen"></i></a>
                                            <a href="Includes/Leccion.php?action=delete&id=<?php echo $l['id']; ?>&curso_id=<?php echo $curso_id; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('¿Seguro que quieres borrar esta lección?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="Ver_Comentarios.php?leccion_id=<?php echo $l['id']; ?>&curso_id=<?php echo $curso_id; ?>" class="btn btn-sm btn-outline-info" title="Foro de Dudas">
                                            <i class="fa-solid fa-comments"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>