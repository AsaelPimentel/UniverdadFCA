<?php
// // Llamada del filtro de seguridad 
require_once __DIR__ . '/../../Config/Seguridad.php';

// Especificacion de quien accede a esta parte
Seguridad::verificarAcceso('instructor');

class Comentario {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Guardar un nuevo comentario (sirve tanto para maestro como alumno)
    public function guardarComentario($leccion_id, $usuario_id, $texto) {
        $leccion = mysqli_real_escape_string($this->db, $leccion_id);
        $usuario = mysqli_real_escape_string($this->db, $usuario_id);
        $texto = mysqli_real_escape_string($this->db, $texto);

        $sql = "INSERT INTO comentarios (leccion_id, usuario_id, comentario) 
                VALUES ('$leccion', '$usuario', '$texto')";
        return mysqli_query($this->db, $sql);
    }

    // Obtener los comentarios de una lección
    public function obtenerPorLeccion($leccion_id) {
        $leccion = mysqli_real_escape_string($this->db, $leccion_id);
        $sql = "SELECT c.*, u.nombre, u.rol 
                FROM comentarios c 
                INNER JOIN usuarios u ON c.usuario_id = u.id 
                WHERE c.leccion_id = '$leccion' 
                ORDER BY c.fecha DESC";
        return mysqli_query($this->db, $sql);
    }
}

// --- CONTROLADOR DE ACCIONES MVC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'comentar') {
    
    // Subimos dos niveles para llegar a Config
    require_once '../../Config/Conexion.php'; 
    $conexion = ConexionDB::obtenerConexion();
    $comentarioObj = new Comentario($conexion);

    $leccion_id = $_POST['leccion_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $texto = $_POST['comentario'];
    $origen = $_POST['origen']; 

    if ($comentarioObj->guardarComentario($leccion_id, $usuario_id, $texto)) {
        if ($origen == 'alumno') {
            $curso_id = $_POST['curso_id'];
            header("Location: ../Ver_Curso.php?id=$curso_id&lec_id=$leccion_id&msj=comentario_ok");
        } else {
            // Origen maestro
            $curso_id = $_POST['curso_id'];
            header("Location: ../Ver_Comentarios.php?leccion_id=$leccion_id&curso_id=$curso_id&msj=comentario_ok");
        }
    }
    exit();
}
?>