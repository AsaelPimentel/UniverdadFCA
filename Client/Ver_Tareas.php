<?php
// 1. ESCUDO DE SEGURIDAD MÁGICO
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('instructor');

// 2. INCLUSIÓN DE ARCHIVOS
include 'Includes/header.php';
require_once __DIR__ . '/../Config/Conexion.php';
include_once 'Includes/Tarea.php'; 

$conexion = ConexionDB::obtenerConexion();
$tareaObj = new Tarea($conexion);

// 3. OBTENER LISTA DE CURSOS DEL MAESTRO PARA EL FILTRO (Ordenados del más reciente al más viejo)
$query_cursos = mysqli_query($conexion, "SELECT id, titulo FROM cursos WHERE instructor_id = '$id_instructor' ORDER BY fecha_creacion DESC");

// 4. CAPTURAR EL FILTRO SELECCIONADO (Si no ha seleccionado nada, por defecto será 'todos')
$curso_seleccionado = isset($_GET['curso_filter']) ? $_GET['curso_filter'] : 'todos';

// 5. OBTENEMOS LOS DATOS A TRAVÉS DEL MODELO (ENVIÁNDOLE EL FILTRO)
$res_tareas = $tareaObj->obtenerEntregasPorInstructor($id_instructor, $curso_seleccionado);
?>

<div class="container mt-5">
    <div class="nav-actions d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold" style="color: #065b3e;">
            <i class="fa-solid fa-inbox me-2 text-warning"></i> Control de Entregas
        </h4>
        <a href="index.php" class="btn btn-uabc-gold px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
        <div class="card-body bg-light" style="border-radius: 15px;">
            <form method="GET" action="Ver_Tareas.php" class="row gx-3 gy-2 align-items-center m-0">
                <div class="col-auto">
                    <label class="fw-bold text-secondary mb-0"><i class="fa-solid fa-filter me-1"></i> Filtrar por curso:</label>
                </div>
                <div class="col-md-5">
                    <select name="curso_filter" class="form-select shadow-sm fw-bold text-dark border-0" onchange="this.form.submit()">
                        <option value="todos" <?php echo ($curso_seleccionado == 'todos') ? 'selected' : ''; ?>>
                            Ver Todas las Tareas (Más recientes primero)
                        </option>
                        
                        <?php while($c = mysqli_fetch_assoc($query_cursos)): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($curso_seleccionado == $c['id']) ? 'selected' : ''; ?>>
                                📌 <?php echo $c['titulo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Alumno</th>
                            <th>Curso</th>
                            <th>Lección</th>
                            <th>Fecha de Envío</th>
                            <th class="text-center">Evidencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res_tareas) > 0): ?>
                            <?php while ($tarea = mysqli_fetch_assoc($res_tareas)): 
                                $fecha_formato = date('d/m/Y h:i A', strtotime($tarea['fecha_envio']));
                            ?>
                                <tr>
                                    <td class="px-4 fw-bold text-dark"><i class="fa-solid fa-user-graduate text-secondary me-2"></i><?php echo $tarea['alumno_nombre']; ?></td>
                                    <td><span class="badge bg-light text-primary border"><?php echo $tarea['curso_titulo']; ?></span></td>
                                    <td class="text-muted small"><?php echo $tarea['leccion_titulo']; ?></td>
                                    <td class="text-muted small"><i class="fa-regular fa-clock me-1"></i><?php echo $fecha_formato; ?></td>
                                    <td class="text-center">
                                        <?php 
                                        // 1. Creamos la ruta física
                                        $ruta_fisica = '../' . $tarea['archivo_ruta'];
                                        
                                        if (file_exists($ruta_fisica)) {
                                            // 2. Si existe, reemplazamos espacios para que no se rompa el enlace
                                            $url_segura = '../' . str_replace(' ', '%20', $tarea['archivo_ruta']);
                                        ?>
                                            <a href="<?php echo $url_segura; ?>" class="btn btn-outline-success btn-sm px-3 shadow-sm" download>
                                                <i class="fa-solid fa-download me-1"></i> Descargar
                                            </a>
                                        <?php } else { ?>
                                            <button class="btn btn-outline-secondary btn-sm px-3 shadow-sm" disabled title="El archivo ya no se encuentra en el servidor">
                                                <i class="fa-solid fa-file-circle-xmark me-1"></i> No disponible
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open display-4 opacity-25 d-block mb-3"></i>
                                    No hay tareas entregadas para este filtro.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>