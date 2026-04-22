<?php 
//// Llamada del filtro de seguridad 
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('admin'); // Exigimos rol de administrador

// Llamada de la cadena de conexion
require_once __DIR__ . '/../Config/Conexion.php';

$db = ConexionDB::obtenerConexion();

// obtenemos ls datos del usuario por su id
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($db, $_GET['id']);
    $query = "SELECT * FROM usuarios WHERE id = '$id'";
    $resultado = mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($resultado);

    if (!$user) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

include 'Includes/header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow border-0 card-registro">
                <div class="card-body p-5">
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-header-box me-3" style="background-color: #bfa071;">
                            <i class="fa-solid fa-user-pen fs-3 text-white"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Editar Usuario</h4>
                            <small class="text-muted">Modifica los privilegios o datos del perfil</small>
                        </div>
                    </div>

                    <hr class="opacity-25 mb-4">

                    <form action="Includes/Usuarios.php" method="POST">
                        
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" name="usuario_id" value="<?php echo $user['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control custom-input py-2" 
                                   value="<?php echo $user['nombre']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control custom-input py-2" 
                                   value="<?php echo $user['email']; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Rol del Usuario</label>
                            <select name="rol" class="form-select custom-input py-2" required>
                                <option value="estudiante" <?php echo ($user['rol'] == 'estudiante') ? 'selected' : ''; ?>>🎓 Estudiante</option>
                                <option value="instructor" <?php echo ($user['rol'] == 'instructor') ? 'selected' : ''; ?>>👨‍🏫 Maestro / Instructor</option>
                                <option value="admin" <?php echo ($user['rol'] == 'admin') ? 'selected' : ''; ?>>🛡️ Administrador</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-uabc-action py-2 fw-bold text-uppercase shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Cambios
                            </button>
                            <a href="index.php" class="btn btn-light py-2 fw-bold text-uppercase small text-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>