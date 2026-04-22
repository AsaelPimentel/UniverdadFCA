<?php
// 1. Escudo de Seguridad: Cargamos el filtro y exigimos que sea estudiante
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('estudiante');

// 1. Conectamos con tu nueva arquitectura
require_once __DIR__ . '/../Config/Conexion.php';

// 2. Verificamos que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Login.php");
    exit();
}

// 3. Recibimos los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexion = ConexionDB::obtenerConexion();
    
    $usuario_id = $_SESSION['usuario_id'];
    $leccion_id = mysqli_real_escape_string($conexion, $_POST['leccion_id']);
    $curso_id   = mysqli_real_escape_string($conexion, $_POST['curso_id']);

    // 4. Verificamos si el alumno YA tiene esta lección completada para no duplicar registros
    $check_sql = "SELECT id FROM progreso_lecciones WHERE usuario_id = '$usuario_id' AND leccion_id = '$leccion_id'";
    $check_res = mysqli_query($conexion, $check_sql);

    if (mysqli_num_rows($check_res) == 0) {
        // 5. Si no existe el registro, lo insertamos
        $sql = "INSERT INTO progreso_lecciones (usuario_id, leccion_id) 
                VALUES ('$usuario_id', '$leccion_id')";

        if (mysqli_query($conexion, $sql)) {
            // Redirigimos de vuelta a la lección exacta
            header("Location: Ver_Curso.php?id=$curso_id&lec_id=$leccion_id");
            exit();
        } else {
            echo "Error al registrar progreso: " . mysqli_error($conexion);
        }
    } else {
        // Si ya estaba completada desde antes, simplemente lo regresamos al curso sin hacer nada
        header("Location: Ver_Curso.php?id=$curso_id&lec_id=$leccion_id");
        exit();
    }
} else {
    // Si intentan entrar directo a la URL sin pasar por el botón
    header("Location: index.php");
    exit();
}
?>