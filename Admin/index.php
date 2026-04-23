<?php
include 'Includes/header.php';
include 'Includes/Funciones.php';

// Obtenemos la conexión 
$db = ConexionDB::obtenerConexion();

// ==========================================
// LÓGICA DE BÚSQUEDA Y PAGINACIÓN
// ==========================================
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($db, $_GET['buscar']) : '';
$registros_por_pagina = 5; // Cambia este número si quieres ver más o menos usuarios por página
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Construir la condición de búsqueda
$where = "";
if (!empty($buscar)) {
    $where = " WHERE nombre LIKE '%$buscar%' OR email LIKE '%$buscar%' ";
}

// 1. Obtener el total de usuarios para el Badge
$res_total_abs = mysqli_query($db, "SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = mysqli_fetch_assoc($res_total_abs)['total'];

// 2. Obtener el total de resultados de la búsqueda para calcular las páginas
$sql_total_filtro = "SELECT COUNT(*) as total FROM usuarios $where";
$res_total_filtro = mysqli_query($db, $sql_total_filtro);
$total_filtro = mysqli_fetch_assoc($res_total_filtro)['total'];
$total_paginas = ceil($total_filtro / $registros_por_pagina);

// 3. Obtener los usuarios de la página actual
$sql_usuarios = "SELECT * FROM usuarios $where ORDER BY id DESC LIMIT $offset, $registros_por_pagina";
$resultado = mysqli_query($db, $sql_usuarios);
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
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

<style>
    /* Estilos adicionales para la paginación UABC */
    .pagination .page-link {
        color: #065b3e;
        border: 1px solid #dee2e6;
    }
    .pagination .page-item.active .page-link {
        background-color: #065b3e;
        border-color: #065b3e;
        color: white;
    }
    .pagination .page-link:hover {
        background-color: #e9ecef;
        color: #065b3e;
    }
</style>

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
                                <i class="fa-solid fa-address-card me-1 text-warning"></i> Nombre Completo
                            </label>
                            <input type="text" name="nombre" class="form-control custom-input py-2" placeholder="Ej: Juan Pérez García" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">
                                <i class="fa-solid fa-envelope me-1 text-warning"></i> Correo Electrónico
                            </label>
                            <input type="email" name="email" class="form-control custom-input py-2" placeholder="usuario@uabc.edu.mx" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase">
                                <i class="fa-solid fa-key me-1 text-warning"></i> Contraseña Temporal
                            </label>
                            <input type="password" name="password" class="form-control custom-input py-2" placeholder="••••••••" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase">
                                <i class="fa-solid fa-user-shield me-1 text-warning"></i> Rol del Usuario
                            </label>
                            <select name="rol" class="form-select custom-input py-2" required>
                                <option value="estudiante" selected>🎓 Estudiante</option>
                                <option value="instructor">👨‍🏫 Maestro / Instructor</option>
                                <option value="admin">🛡️ Administrador</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-uabc-green w-100 py-2 fw-bold text-uppercase shadow-sm" style="background-color: #065b3e; color: white;">
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
                    <div>
                        <a href="Estadisticas.php" class="btn btn-sm btn-light fw-bold me-2" style="font-size: 0.7rem;">
                            <i class="fa-solid fa-eye me-1"></i> VER REPORTES
                        </a>
                        <span class="badge bg-light text-dark rounded-pill"><?php echo $total_usuarios; ?> Registrados</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="GET" action="" class="mb-4">
                        <div class="input-group shadow-sm border rounded">
                            <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="buscar" class="form-control border-0" placeholder="Buscar por nombre o correo electrónico..." value="<?php echo htmlspecialchars($buscar); ?>">
                            <button class="btn btn-warning fw-bold px-4" type="submit" style="color: #065b3e;">Buscar</button>
                            <?php if (!empty($buscar)): ?>
                                <a href="index.php" class="btn btn-light border-start"><i class="fa-solid fa-xmark"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 border">
                            <thead class="table-light">
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
                                                switch ($user['rol']) {
                                                    case 'admin':
                                                        $b_class = 'bg-danger text-white'; $icon = 'fa-shield-halved'; $lbl = 'Administrador'; break;
                                                    case 'instructor':
                                                        $b_class = 'bg-warning text-dark'; $icon = 'fa-chalkboard-user'; $lbl = 'Instructor'; break;
                                                    default:
                                                        $b_class = 'bg-success text-white'; $icon = 'fa-user-graduate'; $lbl = 'Estudiante';
                                                }
                                                ?>
                                                <span class="badge <?php echo $b_class; ?> d-inline-flex align-items-center px-3 py-2">
                                                    <i class="fa-solid <?php echo $icon; ?> me-2"></i> <?php echo $lbl; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group gap-2">
                                                    <a href="Editar_Usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <a href="Includes/usuarios.php?action=borrar&id=<?php echo $user['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar a este usuario?')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-magnifying-glass fs-1 d-block mb-3 opacity-25"></i>
                                            <?php echo !empty($buscar) ? "No se encontraron usuarios que coincidan con '<strong>$buscar</strong>'." : "No hay usuarios registrados aún."; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_paginas > 1): ?>
                        <nav aria-label="Paginación de usuarios" class="mt-4">
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Anterior</a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                    <li class="page-item <?php echo ($pagina_actual == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $i; ?>&buscar=<?php echo urlencode($buscar); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>&buscar=<?php echo urlencode($buscar); ?>">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                </div>
            </div>
            
            <footer class="footer-uabc mt-5 text-center text-muted small">
                <div class="d-flex align-items-center justify-content-center">
                    <span class="text-warning">♦</span>
                    <span class="mx-3 text-uppercase fw-bold">Universidad Autónoma de Baja California</span>
                    <span class="text-warning">♦</span>
                </div>
                <div class="mt-1">© 2026 - Todos los derechos reservados</div>
            </footer>
        </div>
    </div>

    <script>
        setTimeout(function() {
            let alert = document.querySelector('.alert');
            if (alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 3000);
    </script>

<?php include 'Includes/Footer.php'; ?>