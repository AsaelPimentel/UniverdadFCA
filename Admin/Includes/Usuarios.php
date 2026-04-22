<?php
// // Llamada del filtro de seguridad 
require_once __DIR__ . '/../../Config/Seguridad.php';
Seguridad::verificarAcceso('admin'); // Exigimos que solo el administrador pueda procesar esto

class Usuario {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

 // --- 1. MÉTODO PARA CREAR USUARIO ---
    public function crearUsuario($datos) {
        $nombre = mysqli_real_escape_string($this->db, $datos['nombre']);
        $email  = mysqli_real_escape_string($this->db, $datos['email']);
        $rol    = mysqli_real_escape_string($this->db, $datos['rol']);
        
        // Tomar la contraseña en texto plano (tal cual se escribió) y protegerla contra inyección SQL
        $password_plana = mysqli_real_escape_string($this->db, $datos['password']);

        // Verificar si el correo ya existe
        $verificar_email = "SELECT id FROM usuarios WHERE email = '$email'";
        $resultado_verificacion = mysqli_query($this->db, $verificar_email);

        if (mysqli_num_rows($resultado_verificacion) > 0) {
            return "duplicado"; // Retornamos un estado para manejarlo en el controlador
        }

        // Insertar el nuevo usuario usando $password_plana en lugar de la encriptada
        $sql = "INSERT INTO usuarios (nombre, email, password, rol) 
                VALUES ('$nombre', '$email', '$password_plana', '$rol')";

        if (mysqli_query($this->db, $sql)) {
            return "ok";
        } else {
            return "error";
        }
    }

    // --- 2. MÉTODO PARA EDITAR USUARIO ---
    public function actualizarUsuario($datos) {
        $id = mysqli_real_escape_string($this->db, $datos['usuario_id']);
        $nombre = mysqli_real_escape_string($this->db, $datos['nombre']);
        $email  = mysqli_real_escape_string($this->db, $datos['email']);
        $rol    = mysqli_real_escape_string($this->db, $datos['rol']);

        // Validación extra: Verificar si el nuevo correo choca con otro usuario existente
        $verificar = "SELECT id FROM usuarios WHERE email = '$email' AND id != '$id'";
        if (mysqli_num_rows(mysqli_query($this->db, $verificar)) > 0) {
            return "duplicado"; 
        }

        $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', rol='$rol' WHERE id='$id'";

        if (mysqli_query($this->db, $sql)) {
            return "ok";
        } else {
            return "error";
        }
    }

    // --- 3. MÉTODO PARA ELIMINAR USUARIO (¡NUEVO!) ---
    public function eliminarUsuario($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        $sql = "DELETE FROM usuarios WHERE id = '$id'";
        
        if (mysqli_query($this->db, $sql)) {
            return "ok";
        } else {
            return "error";
        }
    }
}

//        CONTROLADOR DE ACCIONES MVC

// 1. CONTROLADOR PARA PETICIONES POST (Crear y Editar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    // Subimos un nivel porque estamos dentro de Includes/
    require_once '../../Config/Conexion.php'; 
    $conexion = ConexionDB::obtenerConexion();
    
    $usuarioObj = new Usuario($conexion);

    // ACCIÓN: CREAR
    if ($_POST['action'] == 'crear') {
        $resultado = $usuarioObj->crearUsuario($_POST);

        if ($resultado == "ok") {
            $_SESSION['alerta'] = [
                'tipo' => 'success',
                'mensaje' => 'Usuario registrado con éxito.'
            ];
        } elseif ($resultado == "duplicado") {
            $_SESSION['alerta'] = [
                'tipo' => 'warning',
                'mensaje' => 'Ese correo ya está registrado en el sistema.'
            ];
        } else {
            $_SESSION['alerta'] = [
                'tipo' => 'danger',
                'mensaje' => 'Error al registrar en la base de datos.'
            ];
        }
        
        header("Location: ../index.php");
        exit();
    }

    // ACCIÓN: EDITAR
    if ($_POST['action'] == 'editar') {
        $resultado = $usuarioObj->actualizarUsuario($_POST);

        if ($resultado == "ok") {
            $_SESSION['alerta'] = [
                'tipo' => 'success',
                'mensaje' => '¡Usuario actualizado correctamente!'
            ];
        } elseif ($resultado == "duplicado") {
            $_SESSION['alerta'] = [
                'tipo' => 'warning',
                'mensaje' => 'El correo ingresado ya pertenece a otra cuenta.'
            ];
        } else {
            $_SESSION['alerta'] = [
                'tipo' => 'danger',
                'mensaje' => 'Error al actualizar en la base de datos.'
            ];
        }
        
        header("Location: ../index.php");
        exit();
    }
}

// 2. CONTROLADOR PARA PETICIONES GET (Borrar)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'borrar' && isset($_GET['id'])) {
    
    // Seguridad: Verificamos que sea admin antes de borrar
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
        $_SESSION['alerta'] = [
            'tipo' => 'warning',
            'mensaje' => 'No tienes permisos para realizar esta acción.'
        ];
        header("Location: ../index.php");
        exit();
    }

    require_once '../../Config/Conexion.php'; 
    $conexion = ConexionDB::obtenerConexion();
    $usuarioObj = new Usuario($conexion);

    $resultado = $usuarioObj->eliminarUsuario($_GET['id']);

    if ($resultado == "ok") {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => '¡Usuario eliminado permanentemente!'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'danger',
            'mensaje' => 'Error al eliminar el usuario de la base de datos.'
        ];
    }

    header("Location: ../index.php");
    exit();
}
?>