<?php
class Aula {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // 1. Obtener detalles del curso
    public function obtenerCurso($id_curso) {
        $id = mysqli_real_escape_string($this->db, $id_curso);
        $res = mysqli_query($this->db, "SELECT * FROM cursos WHERE id = '$id'");
        return mysqli_fetch_assoc($res);
    }

    // 2. Obtener todas las lecciones del curso
    public function obtenerLecciones($id_curso) {
        $id = mysqli_real_escape_string($this->db, $id_curso);
        return mysqli_query($this->db, "SELECT * FROM lecciones WHERE curso_id = '$id' ORDER BY id ASC");
    }

    // 3. Obtener detalle de una lección específica
    public function obtenerDetalleLeccion($id_leccion) {
        $id = mysqli_real_escape_string($this->db, $id_leccion);
        $res = mysqli_query($this->db, "SELECT * FROM lecciones WHERE id = '$id'");
        return mysqli_fetch_assoc($res);
    }

    // 4. Obtener un arreglo con los IDs de las lecciones ya completadas por el alumno en este curso
    public function obtenerProgresoUsuario($id_usuario, $id_curso) {
        $usuario = mysqli_real_escape_string($this->db, $id_usuario);
        $curso = mysqli_real_escape_string($this->db, $id_curso);
        
        $sql = "SELECT p.leccion_id 
                FROM progreso_lecciones p
                INNER JOIN lecciones l ON p.leccion_id = l.id
                WHERE p.usuario_id = '$usuario' AND l.curso_id = '$curso'";
                
        $res = mysqli_query($this->db, $sql);
        $completadas = [];
        while($row = mysqli_fetch_assoc($res)) {
            $completadas[] = $row['leccion_id'];
        }
        return $completadas; // Retorna un array ej: [1, 2, 5]
    }

    // 5. Verificar si una tarea ya fue entregada
    public function obtenerMiTarea($id_usuario, $id_leccion) {
        $usuario = mysqli_real_escape_string($this->db, $id_usuario);
        $leccion = mysqli_real_escape_string($this->db, $id_leccion);
        
        $res = mysqli_query($this->db, "SELECT * FROM tareas_entregadas WHERE leccion_id = '$leccion' AND usuario_id = '$usuario'");
        return mysqli_fetch_assoc($res);
    }
    // --- MÉTODO PARA OBTENER LOS ARCHIVOS DE UNA LECCIÓN ---
    public function obtenerArchivosLeccion($id_leccion) {
        $id = mysqli_real_escape_string($this->db, $id_leccion);
        $sql = "SELECT * FROM archivos_leccion WHERE leccion_id = '$id'";
        return mysqli_query($this->db, $sql);
    }
}
?>