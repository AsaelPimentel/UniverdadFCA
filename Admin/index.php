<?php
include 'Includes/header.php';
include 'Includes/Funciones.php';

// Obtenemos la conexión 
$db = ConexionDB::obtenerConexion();

$resultado = obtenerTodosLosUsuarios($db);

// Calculamos el total para que no te marque 0
$total_usuarios = ($resultado) ? mysqli_num_rows($resultado) : 0;
?>
<?php if (isset($_SESSION['alerta'])): ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo $_SESSION['alerta']['tipo']; ?> alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="fa-solid <?php echo ($_SESSION['alerta']['tipo'] == 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> me-2 fs-4"></i>
                <div>
                    <strong>Sistema:</strong> <?php echo $_SESSION['alerta']['mensaje']; ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php
    // Borramos el mensaje de la sesión para que no aparezca siempre
    unset($_SESSION['alerta']);
    ?>
<?php endif; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 card-registro">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-header-box me-3">
                            <i class="fa-solid fa-user-plus fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Registrar Usuario</h4>
                            <small class="text-muted">Crear nueva cuenta en el sistema</small>
                        </div>
                    </div>

                    <hr class="opacity-25 mb-4">

                    <form action="Includes/usuarios.php" method="POST">
                        <input type="hidden" name="action" value="crear">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">
                                <i class="fa-solid fa-address-card me-1 text-gold-uabc"></i> Nombre Completo
                            </label>
                            <input type="text" name="nombre" class="form-control custom-input py-2" placeholder="Ej: Juan Pérez García" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">
                                <i class="fa-solid fa-envelope me-1 text-gold-uabc"></i> Correo Electrónico
                            </label>
                            <input type="email" name="email" class="form-control custom-input py-2" placeholder="usuario@uabc.edu.mx" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">
                                <i class="fa-solid fa-key me-1 text-gold-uabc"></i> Contraseña Temporal
                            </label>
                            <input type="password" name="password" class="form-control custom-input py-2" placeholder="••••••••" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase">
                                <i class="fa-solid fa-user-shield me-1 text-gold-uabc"></i> Rol del Usuario
                            </label>
                            <select name="rol" class="form-select custom-input py-2" required>
                                <option value="estudiante" selected>🎓 Estudiante</option>
                                <option value="instructor">👨‍🏫 Maestro / Instructor</option>
                                <option value="admin">🛡️ Administrador</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-uabc-action w-100 py-2 fw-bold text-uppercase shadow-sm">
                            <i class="fa-solid fa-user-check me-2"></i> Crear Usuario
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow border-0 card-tabla">
                <div class="card-header bg-custom-green text-white d-flex justify-content-between align-items-center p-3">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-users-gear me-2"></i>
                        <span class="fw-bold">Usuarios en el Sistema</span>
                    </div>
                    <a href="Estadisticas.php" class="btn btn-sm btn-light fw-bold me-2" style="font-size: 0.7rem;">
                        <i class="fa-solid fa-eye me-1"></i> VER REPORTES
                    </a>
                    <span class="badge badge-count rounded-pill"><?php echo $total_usuarios; ?> Registrados</span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light-uabc">
                                <tr>
                                    <th class="ps-4">NOMBRE</th>
                                    <th>EMAIL</th>
                                    <th>ROL</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                                    <?php while ($user = mysqli_fetch_assoc($resultado)): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark"><?php echo $user['nombre']; ?></td>
                                            <td class="text-muted"><?php echo $user['email']; ?></td>
                                            <td>
                                                <?php
                                                // Configuración de los Badges según la imagen
                                                switch ($user['rol']) {
                                                    case 'admin':
                                                        $badge_class = 'badge-admin';
                                                        $icon = 'fa-shield-halved';
                                                        $label = 'Administrador';
                                                        break;
                                                    case 'instructor':
                                                        $badge_class = 'badge-instructor';
                                                        $icon = 'fa-chalkboard-user';
                                                        $label = 'Instructor';
                                                        break;
                                                    default:
                                                        $badge_class = 'badge-estudiante';
                                                        $icon = 'fa-user-graduate';
                                                        $label = 'Estudiante';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?> d-inline-flex align-items-center px-3 py-2">
                                                    <i class="fa-solid <?php echo $icon; ?> me-2"></i> <?php echo $label; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group gap-2">
                                                    <a href="Editar_Usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-action-outline btn-edit">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <a href="Includes/usuarios.php?action=borrar&id=<?php echo $user['id']; ?>"
                                                        class="btn btn-action-outline btn-delete"
                                                        onclick="return confirm('¿Estás seguro de eliminar a este usuario?')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-folder-open fs-1 d-block mb-2 opacity-25"></i>
                                            No hay usuarios registrados aún.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <footer class="footer-uabc mt-5">
                <div class="container text-center">
                    <div class="footer-line-principal d-flex align-items-center justify-content-center">
                        <span class="rombo">♦</span>
                        <span class="mx-3 text-uppercase">Universidad Autónoma de Baja California - Sistema de Gestión Educativa</span>
                        <span class="rombo">♦</span>
                    </div>
                    <div class="footer-line-copyright mt-1">
                        <small>© 2023 - Todos los derechos reservados</small>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Selecciona todas las alertas y las cierra automáticamente después de 3000ms
        setTimeout(function() {
            let alert = document.querySelector('.alert');
            if (alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 3000);
    </script>

    <?php include 'Includes/Footer.php'; ?>