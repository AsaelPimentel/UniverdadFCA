<?php
// Llamada del filtro de seguridad 
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('instructor'); // Exigimos que sea maestro

// ARCHIVOS NECESARIOS
require_once __DIR__ . '/../Config/Conexion.php';
require_once __DIR__ . '/Includes/Curso.php';

if (isset($_GET['id'])) {
    $conexion = ConexionDB::obtenerConexion();
    $cursoObj = new Curso($conexion);

    $id_curso = $_GET['id'];
    $id_instructor = $_SESSION['usuario_id'];

    if ($cursoObj->eliminarCurso($id_curso, $id_instructor)) {
        header("Location: index.php?res=eliminado");
    } else {
        echo "Error al eliminar el curso.";
    }
} else {
    header("Location: index.php");
}
exit();