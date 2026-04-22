<?php
// Llamada del filtro de seguridad 
require_once __DIR__ . '/../../Config/Seguridad.php';

// Especificacion de quien accede a esta parte
Seguridad::verificarAcceso('instructor');
class Curso
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function guardar($datos, $archivo, $instructor_id)
    {
        $titulo = mysqli_real_escape_string($this->db, $datos['titulo']);
        $desc   = mysqli_real_escape_string($this->db, $datos['descripcion']);

        // Configuración de la imagen
        $ruta_db = "Assets/Img/default.jpg";

        if (isset($archivo['imagen_curso']) && $archivo['imagen_curso']['error'] == 0) {
            $extension = pathinfo($archivo['imagen_curso']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = "curso_" . time() . "." . $extension;

            // Subimos de carpeta Client/ a la raíz para entrar a Assets/
            $ruta_destino = "../Assets/Img/" . $nombre_archivo;

            if (move_uploaded_file($archivo['imagen_curso']['tmp_name'], $ruta_destino)) {
                $ruta_db = "Assets/Img/" . $nombre_archivo;
            }
        }

        $sql = "INSERT INTO cursos (titulo, descripcion, instructor_id, imagen) 
                VALUES ('$titulo', '$desc', '$instructor_id', '$ruta_db')";

        return mysqli_query($this->db, $sql);
    }
    public function eliminarCurso($id_curso, $id_instructor)
    {
        $id_curso = mysqli_real_escape_string($this->db, $id_curso);

        // 1. Obtener la ruta de la imagen para borrar el archivo físico
        $query_img = mysqli_query($this->db, "SELECT imagen FROM cursos WHERE id = '$id_curso' AND instructor_id = '$id_instructor'");

        if ($row = mysqli_fetch_assoc($query_img)) {
            $ruta_imagen = "../" . $row['imagen']; // Ajustamos la ruta para salir de Client/

            // Borrar archivo si no es el default
            if (!empty($row['imagen']) && $row['imagen'] != 'Assets/Img/default.jpg' && file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        // 2. Borrar de la Base de Datos
        $sql = "DELETE FROM cursos WHERE id = '$id_curso' AND instructor_id = '$id_instructor'";
        return mysqli_query($this->db, $sql);
    }
}
