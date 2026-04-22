<?php
// 1. Damos 1 salto: salimos de Client/
require_once __DIR__ . '/../Config/Seguridad.php';

// 2. Exigimos que SOLO los maestros puedan ejecutar este código
Seguridad::verificarAcceso('instructor');

// 3. Incluimos Header (para estilos) y Conexión
include 'Includes/Header.php';
require_once __DIR__ . '/../Config/Conexion.php';

// 4. IMPORTANTE: Incluimos el archivo que contiene la clase Curso
require_once __DIR__ . '/Includes/Curso.php'; 

// 5. Instanciamos los objetos
$conexion = ConexionDB::obtenerConexion();
$cursoObj = new Curso($conexion);

// Si se recibe el formulario, llamamos al método de la clase
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resultado = $cursoObj->guardar($_POST, $_FILES, $_SESSION['usuario_id']);

    if ($resultado) {
        echo "<script>alert('¡Curso creado con éxito!'); window.location.href='Index.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error al guardar: " . mysqli_error($conexion) . "</div>";
    }
}

?>

<div class="container mt-5">
    <div class="nav-actions d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold" style="color: #065b3e;">
            <i class="fa-solid fa-plus-circle me-2 text-warning"></i> Nuevo Curso
        </h4>
        <a href="index.php" class="btn btn-uabc-gold px-4 py-2">
            <i class="fa-solid fa-arrow-left me-2"></i> Volver
        </a>
    </div>

    <div class="card shadow-lg border-0 mx-auto" style="max-width: 650px; border-radius: 20px;">
        <div class="card-body p-5">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">TÍTULO DEL CURSO</label>
                    <input type="text" name="titulo" class="form-control border-0 bg-light py-2" required placeholder="Ej: Business Intelligence Avanzado">
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary small">DESCRIPCIÓN</label>
                    <textarea name="descripcion" class="form-control border-0 bg-light" rows="4" required placeholder="Describe brevemente el objetivo del curso..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary small">IMAGEN DE PORTADA</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-image text-muted"></i></span>
                        <input type="file" name="imagen_curso" class="form-control border-0 bg-light" accept="image/*">
                    </div>
                    <div class="form-text mt-2 small">Formatos permitidos: JPG, PNG. Proporción recomendada 16:9.</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-manage py-3 fw-bold text-uppercase shadow-sm">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Publicar Curso
                    </button>
                    <a href="index.php" class="btn btn-delete-outline btn-md ">Cancelar y salir</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>