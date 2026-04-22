<?php
// Incluimos el archivo de la clase
include 'config/Conexion.php'; 

session_start();

// Si el usuario ya está logueado, lo redirigimos a su área correspondiente
if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol'])) {
    
    $ruta = 'Public/index.php'; // Ruta por defecto (estudiante)

    switch ($_SESSION['rol']) {
        case 'admin':
            $ruta = 'Admin/index.php'; // Ajusté la ruta según tu estructura anterior
            break;
        case 'instructor':
            $ruta = 'Client/index.php';
            break;
    }

    header("Location: " . $ruta);
    exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  Obtenemos la conexión desde la clase estática
    $db = ConexionDB::obtenerConexion(); 

    //  Limpieza de datos
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password_escrita = $_POST['password'];

    //  Consulta (Asegúrate que el nombre de la tabla coincida)
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($db, $sql);

    //  Verificamos si el correo existe
    if ($fila = mysqli_fetch_assoc($resultado)) {
        
        // Verificación de contraseña (soporta texto plano '123' y hashes)
        if (password_verify($password_escrita, $fila['password']) || $password_escrita === $fila['password']) {
            
            // Guardamos los datos en la sesión
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['nombre']     = $fila['nombre'];
            $_SESSION['rol']        = $fila['rol'];

            // Redirección por roles
            switch ($fila['rol']) {
                case 'admin':
                    header("Location: Admin/index.php");
                    break;
                case 'instructor':
                    header("Location: Client/index.php");
                    break;
                default:
                    header("Location: Public/index.php");
                    break;
            }
            exit();
            
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location.href='index.php';</script>";
        }
    } else {
        // Si llega aquí es porque el SELECT no devolvió ninguna fila con ese email
        echo "<script>alert('Correo no registrado: $email'); window.location.href='index.php';</script>";
    }
}
