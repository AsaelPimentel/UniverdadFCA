<?php
include 'Includes/header.php';
$conexion = ConexionDB::obtenerConexion();

// 1. Verificamos si viene el parámetro de eliminado
if (isset($_GET['res']) && $_GET['res'] == 'eliminado'): ?>
    <div id="alerta-borrado" class="alert alert-success shadow-lg border-0"
        style="position: fixed; top: 20px; right: 20px; z-index: 9999; border-radius: 15px; min-width: 300px; display: flex; align-items: center;">
        <i class="fa-solid fa-check-circle me-3 fs-4"></i>
        <div>
            <strong class="d-block">¡Eliminado!</strong>
            <small>El curso se quitó correctamente.</small>
        </div>
    </div>

    <script>
        // Función para desvanecer y quitar el mensaje después de 3 segundos
        setTimeout(function() {
            var alerta = document.getElementById('alerta-borrado');
            if (alerta) {
                alerta.style.transition = "opacity 0.5s ease";
                alerta.style.opacity = "0";
                setTimeout(function() {
                    alerta.remove();
                    // Limpiamos la URL para que no vuelva a salir la alerta al recargar
                    window.history.replaceState({}, document.title, window.location.pathname);
                }, 500);
            }
        }, 3000); // 3000 milisegundos = 3 segundos
    </script>
<?php endif; ?>

<main class="container p-5">
    <div class="nav-actions d-flex justify-content-between align-items-center mb-5">
        <h4 class="mb-0 fw-bold" style="color: #065b3e;">
            <i class="fa-solid fa-book-bookmark me-2 text-warning"></i> Mis Cursos Creados
        </h4>

        <a href="ver_tareas.php" class="btn btn-uabc-green px-2 py-2">
            <i class="fa-solid fa-inbox"></i> Revisar Tareas
        </a>
        <a href="Rendimiento_Curso.php" class="btn btn-uabc-green px-2 py-2">
            <i class="fa-solid fa-chart-line"></i> Metricas
        </a>
        <a href="Crear_Curso.php" class="btn btn-uabc-gold px-2 py-2">
            <i class="fa-solid fa-file-circle-plus"></i> Crear Nuevo Curso
        </a>
    </div>
</main>

<div class="container">
    <div class="row">
        <?php
        // La variable $id_instructor ya viene definida en tu header.php
        $sql = "SELECT * FROM cursos WHERE instructor_id = '$id_instructor' ORDER BY fecha_creacion DESC";
        $res = mysqli_query($conexion, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            while ($curso = mysqli_fetch_assoc($res)) {
                // Validar la imagen
                // Agregamos ../ para salir de la carpeta Client y entrar a Assets
                $img = (!empty($curso['imagen'])) ? '../' . $curso['imagen'] : 'https://via.placeholder.com/400x200?text=Sin+Imagen';
        ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-effect">
                        <img src="<?php echo $img; ?>" class="card-img-top curso-img" alt="Portada">

                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark"><?php echo $curso['titulo']; ?></h5>
                            <p class="card-text text-muted small">
                                <?php echo substr($curso['descripcion'], 0, 90) . '...'; ?>
                            </p>
                        </div>

                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between p-3">
                            <a href="nueva_leccion.php?curso_id=<?php echo $curso['id']; ?>" class="btn btn-manage btn-sm fw-bold">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Gestionar
                            </a>
                            <a href="borrar_curso.php?id=<?php echo $curso['id']; ?>" class="btn btn-delete-outline btn-sm" onclick="return confirm('¿Estás seguro de eliminar todo el curso y sus lecciones?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            // Mensaje elegante si no hay datos
            echo '<div class="col-12 text-center py-5 text-muted">
                    <i class="fa-solid fa-book-open display-1 opacity-25 mb-3 d-block"></i>
                    <h5>Aún no tienes cursos creados.</h5>
                    <p>¡Comienza publicando tu primer curso en el botón de arriba!</p>
                  </div>';
        }
        ?>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>