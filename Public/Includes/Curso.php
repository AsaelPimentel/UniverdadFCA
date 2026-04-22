<?php
class Curso {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // --- MÉTODO PARA OBTENER EL CATÁLOGO DE CURSOS ---
    public function obtenerCursosCatalogo() {
        $sql = "SELECT c.*, u.nombre AS nombre_instructor 
                FROM cursos c 
                INNER JOIN usuarios u ON c.instructor_id = u.id 
                ORDER BY c.fecha_creacion DESC";
                
        return mysqli_query($this->db, $sql);
    }
}
?>