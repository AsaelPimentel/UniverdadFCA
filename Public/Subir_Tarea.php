<?php
// Llamada del filtro de seguridad 
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('estudiante');

// llamada a la cadena de conexion
require_once __DIR__ . '/../Config/Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si llega a este punto, la conexión ya se cargó exitosamente
    $conexion = ConexionDB::obtenerConexion();

    // 3. Recibir y limpiar datos
    $usuario_id = $_SESSION['usuario_id'];
    $leccion_id = mysqli_real_escape_string($conexion, $_POST['leccion_id']);
    $curso_id   = mysqli_real_escape_string($conexion, $_POST['curso_id']);

    // 4. Procesar el archivo subido
    if (isset($_FILES['archivo_tarea']) && $_FILES['archivo_tarea']['error'] == 0) {
        
        // Vamos a guardar las tareas en la carpeta global de Assets
        $directorio_destino = __DIR__ . '/../Assets/Tareas/';
        
        // Si la carpeta "Tareas" no existe, el sistema la crea automáticamente
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        // Limpiar el nombre original (quitamos espacios y caracteres raros para evitar errores)
        $nombre_original = basename($_FILES['archivo_tarea']['name']);
        $nombre_limpio = preg_replace("/[^a-zA-Z0-9.]/", "_", $nombre_original);
        
        // Generar un nombre único para que no se sobreescriban archivos con el mismo nombre
        $nombre_archivo_final = "tarea_u" . $usuario_id . "_l" . $leccion_id . "_" . time() . "_" . $nombre_limpio;
        
        // Ruta donde se guarda físicamente en tu PC/Servidor
        $ruta_fisica = $directorio_destino . $nombre_archivo_final;
        
        // Ruta que se guarda en la BD (Es clave para que el maestro la encuentre después)
        $ruta_db = "Assets/Tareas/" . $nombre_archivo_final;

        // Movemos el archivo de la memoria temporal a nuestra carpeta
        if (move_uploaded_file($_FILES['archivo_tarea']['tmp_name'], $ruta_fisica)) {
            
            // 5. Guardar el registro en la base de datos
            $sql = "INSERT INTO tareas_entregadas (leccion_id, usuario_id, archivo_ruta) 
                    VALUES ('$leccion_id', '$usuario_id', '$ruta_db')";
            
            if (mysqli_query($conexion, $sql)) {
                // Redireccionar de vuelta al curso mostrando la alerta verde de éxito
                header("Location: Ver_Curso.php?id=$curso_id&lec_id=$leccion_id&msj=tarea_ok");
                exit();
            } else {
                echo "Error al guardar en base de datos: " . mysqli_error($conexion);
                exit();
            }
        } else {
            echo "Error al subir el archivo al servidor.";
            exit();
        }
    }
}

// Si alguien intenta entrar a esta URL sin enviar datos, lo regresamos al catálogo
header("Location: index.php");
exit();
?>