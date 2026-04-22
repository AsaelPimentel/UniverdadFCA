<?php
// 1. Damos 2 saltos: salimos de Includes/ y salimos de Client/
require_once __DIR__ . '/../../Config/Seguridad.php';

// 2. Exigimos que SOLO los maestros puedan ejecutar este código
Seguridad::verificarAcceso('instructor');
class Leccion {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // --- LÓGICA PRIVADA PARA SUBIR MÚLTIPLES ARCHIVOS ---
    private function procesarMultiplesArchivos($id_leccion, $archivos) {
        // Verificamos si realmente se subió al menos un archivo
        // Nota que ahora usamos pdf_files (plural) en lugar de pdf_file
        if (!empty($archivos['pdf_files']['name'][0])) {
            $total_archivos = count($archivos['pdf_files']['name']);
            
            // Creamos la carpeta si no existe
            if (!is_dir('../Assets/Pdfs/')) { 
                mkdir('../Assets/Pdfs/', 0777, true); 
            }

            // Recorremos cada archivo subido
            for ($i = 0; $i < $total_archivos; $i++) {
                $nombre_original = mysqli_real_escape_string($this->db, $archivos['pdf_files']['name'][$i]);
                $nombre_limpio = time() . "_" . $i . "_" . basename($nombre_original);
                $ruta_fisica = "../Assets/Pdfs/" . $nombre_limpio;
                $ruta_db = "Assets/Pdfs/" . $nombre_limpio;

                // Si se mueve al servidor, lo guardamos en la nueva tabla
                if (move_uploaded_file($archivos['pdf_files']['tmp_name'][$i], $ruta_fisica)) {
                    $sql_archivo = "INSERT INTO archivos_leccion (leccion_id, nombre_original, ruta_archivo) 
                                    VALUES ('$id_leccion', '$nombre_original', '$ruta_db')";
                    mysqli_query($this->db, $sql_archivo);
                }
            }
        }
    }

    // --- MÉTODO PARA GUARDAR (Insert) ---
    public function guardarLeccion($datos, $archivos) {
        $curso_id = mysqli_real_escape_string($this->db, $datos['curso_id']);
        $titulo = mysqli_real_escape_string($this->db, $datos['titulo_leccion']);
        $url_sucia = $datos['url_video'];
        $tiene_tarea = isset($datos['tiene_tarea']) ? 1 : 0;

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url_sucia, $match)) {
            $video_id = $match[1];
        } else { $video_id = $url_sucia; }

        // 1. Guardamos la lección primero (sin el PDF, porque eso va en la otra tabla)
        $sql = "INSERT INTO lecciones (curso_id, titulo, contenido_url, tiene_tarea) 
                VALUES ('$curso_id', '$titulo', '$video_id', '$tiene_tarea')";
        
        if (mysqli_query($this->db, $sql)) {
            // 2. Obtenemos el ID de la lección que acabamos de crear
            $id_leccion = mysqli_insert_id($this->db);
            
            // 3. Procesamos los múltiples PDFs
            $this->procesarMultiplesArchivos($id_leccion, $archivos);
            return true;
        }
        return false;
    }

    // --- MÉTODO PARA ACTUALIZAR (Update) ---
    public function actualizarLeccion($datos, $archivos) {
        $id = mysqli_real_escape_string($this->db, $datos['id']);
        $titulo = mysqli_real_escape_string($this->db, $datos['titulo']);
        $url_video = mysqli_real_escape_string($this->db, $datos['url']);
        $tiene_tarea = isset($datos['tiene_tarea']) ? 1 : 0;

        // Actualizamos los datos básicos de la lección
        $sql = "UPDATE lecciones SET 
                titulo = '$titulo', 
                contenido_url = '$url_video', 
                tiene_tarea = '$tiene_tarea' 
                WHERE id = '$id'";
        
        if (mysqli_query($this->db, $sql)) {
            // Al actualizar, simplemente añadimos los nuevos archivos a la lección
            // (Si quisieras que al actualizar se borraran los anteriores, habría que añadir un paso extra aquí)
            $this->procesarMultiplesArchivos($id, $archivos);
            return true;
        }
        return false;
    }

    // --- MÉTODO PARA ELIMINAR ---
    public function eliminarLeccion($id_leccion) {
        $id_leccion = mysqli_real_escape_string($this->db, $id_leccion);
        
        // 1. Borramos físicamente todos los PDFs asociados a esta lección
        $query_archivos = mysqli_query($this->db, "SELECT ruta_archivo FROM archivos_leccion WHERE leccion_id = '$id_leccion'");
        while ($row = mysqli_fetch_assoc($query_archivos)) {
            $ruta_fisica = "../" . $row['ruta_archivo'];
            if (file_exists($ruta_fisica)) { 
                unlink($ruta_fisica); 
            }
        }
        
        // 2. Al borrar la lección, la base de datos debería borrar automáticamente los registros 
        // de archivos_leccion gracias al "FOREIGN KEY ... ON DELETE CASCADE" que configuramos en SQL.
        $sql = "DELETE FROM lecciones WHERE id = '$id_leccion'";
        return mysqli_query($this->db, $sql);
    }
}

// --- CONTROLADOR DE ACCIONES ---
require_once '../../Config/Conexion.php';
$conexion = ConexionDB::obtenerConexion();
$leccionObj = new Leccion($conexion);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $curso_id = $_POST['curso_id'];

    // ¿Es una actualización o un nuevo registro?
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // ACCIÓN: ACTUALIZAR
        if ($leccionObj->actualizarLeccion($_POST, $_FILES)) {
            header("Location: ../nueva_leccion.php?curso_id=$curso_id&msj=edit_ok");
        } else {
            header("Location: ../nueva_leccion.php?curso_id=$curso_id&msj=error");
        }
    } else {
        // ACCIÓN: GUARDAR NUEVO
        if ($leccionObj->guardarLeccion($_POST, $_FILES)) {
            header("Location: ../nueva_leccion.php?curso_id=$curso_id&msj=ok");
        } else {
            header("Location: ../nueva_leccion.php?curso_id=$curso_id&msj=error");
        }
    }
    exit();
}

// ACCIÓN: BORRAR (GET)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_leccion = $_GET['id'];
    $curso_id = $_GET['curso_id'];
    if ($leccionObj->eliminarLeccion($id_leccion)) {
        header("Location: ../nueva_leccion.php?curso_id=$curso_id&msj=borrado");
    } else {
        header("Location: ../nueva_leccion.php?curso_id=$curso_id&msj=error");
    }
    exit();
}
?>