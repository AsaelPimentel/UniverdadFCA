<?php
// Especificacion de quien accede a esta parte
require_once __DIR__ . '../../../Config/Seguridad.php';
Seguridad::verificarAcceso('estudiante');

class Comentario {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Guardar un nuevo comentario
    public function guardarComentario($leccion_id, $usuario_id, $texto) {
        $leccion = mysqli_real_escape_string($this->db, $leccion_id);
        $usuario = mysqli_real_escape_string($this->db, $usuario_id);
        $texto = mysqli_real_escape_string($this->db, $texto);

        $sql = "INSERT INTO comentarios (leccion_id, usuario_id, comentario) 
                VALUES ('$leccion', '$usuario', '$texto')";
        return mysqli_query($this->db, $sql);
    }

    // Obtener los comentarios de una lección con los datos del usuario
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

// --- CONTROLADOR DE ACCIONES ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'comentar') {
    require_once '../../Config/Conexion.php'; // Ajusta la ruta según desde dónde se llame
    $conexion = ConexionDB::obtenerConexion();
    $comentarioObj = new Comentario($conexion);

    $leccion_id = $_POST['leccion_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $texto = $_POST['comentario'];
    $origen = $_POST['origen']; 
    $curso_id = $_POST['curso_id']; // Recuperamos el ID del curso

    if ($comentarioObj->guardarComentario($leccion_id, $usuario_id, $texto)) {
        
        if ($origen == 'alumno') {
            // === LÓGICA DE NOTIFICACIONES ===
            // 1. Buscamos quién es el maestro de este curso
            $query_inst = mysqli_query($conexion, "SELECT instructor_id FROM cursos WHERE id = '$curso_id'");
            
            if($row_inst = mysqli_fetch_assoc($query_inst)) {
                $instructor_id = $row_inst['instructor_id'];
                $nombre_alumno = $_SESSION['nombre'];
                
                // 2. Creamos el mensaje
                $mensaje = "El alumno(a) <b>$nombre_alumno</b> hizo una nueva pregunta en el foro.";
                
                // 3. Insertamos la notificación
                $sql_notif = "INSERT INTO notificaciones (usuario_id, mensaje, leccion_id, curso_id) 
                              VALUES ('$instructor_id', '$mensaje', '$leccion_id', '$curso_id')";
                mysqli_query($conexion, $sql_notif);
            }
            // ================================

            header("Location: ../Ver_Curso.php?id=$curso_id&lec_id=$leccion_id&msj=comentario_ok");
        } else {
            // Origen maestro
            header("Location: ../Ver_Comentarios.php?leccion_id=$leccion_id&curso_id=$curso_id&msj=comentario_ok");
        }
    }
    exit();
}
?>