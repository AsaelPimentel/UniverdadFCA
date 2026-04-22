<?php
class Tarea {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Le agregamos un parámetro opcional "$id_curso" que por defecto busca en 'todos'
    public function obtenerEntregasPorInstructor($id_instructor, $id_curso = 'todos') {
        $id_instructor = mysqli_real_escape_string($this->db, $id_instructor);
        
        // Preparamos la variable del filtro vacía por si escoge "Todos los cursos"
        $filtro_curso = "";
        
        // Si seleccionó un curso en específico, agregamos la condición al SQL
        if ($id_curso !== 'todos' && $id_curso !== null && $id_curso !== '') {
            $id_curso_seguro = mysqli_real_escape_string($this->db, $id_curso);
            $filtro_curso = " AND c.id = '$id_curso_seguro' ";
        }
        
        $sql = "SELECT t.archivo_ruta, t.fecha_envio, 
                       u.nombre AS alumno_nombre, 
                       c.titulo AS curso_titulo, 
                       l.titulo AS leccion_titulo
                FROM tareas_entregadas t
                INNER JOIN usuarios u ON t.usuario_id = u.id
                INNER JOIN lecciones l ON t.leccion_id = l.id
                INNER JOIN cursos c ON l.curso_id = c.id
                WHERE c.instructor_id = '$id_instructor' $filtro_curso
                ORDER BY t.fecha_envio DESC";
        
        return mysqli_query($this->db, $sql);
    }
}
?>