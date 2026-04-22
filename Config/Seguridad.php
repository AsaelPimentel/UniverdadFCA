<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Seguridad {
    public static function verificarAcceso($rol_permitido = null) {
        
        // Creamos la ruta absoluta hacia tu login sin importar en qué carpeta estemos
        $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $base_url = $protocolo . "://" . $_SERVER['HTTP_HOST'] . "/UniversidadCNI";

        // 1. FILTRO DE AUTENTICACIÓN: ¿Inició sesión?
        if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
            header("Location: " . $base_url . "/Index.php");
            exit();
        }

        // 2. FILTRO DE AUTORIZACIÓN: ¿Tiene permiso de ver esta página?
        if ($rol_permitido != null && $_SESSION['rol'] != $rol_permitido) {
            // Si un alumno intenta entrar al panel admin, lo regresamos a su zona
            switch ($_SESSION['rol']) {
                case 'admin':
                    header("Location: " . $base_url . "/Admin/index.php");
                    break;
                case 'instructor':
                    header("Location: " . $base_url . "/Client/Index.php");
                    break;
                case 'estudiante':
                    header("Location: " . $base_url . "/Public/Index.php");
                    break;
                default:
                    header("Location: " . $base_url . "/Logout.php");
                    break;
            }
            exit();
        }
    }
}
?>