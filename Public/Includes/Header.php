<?php
// Aseguramos que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Llamada del filtro de seguridad 
require_once __DIR__ . '/../../Config/Seguridad.php';

// Especificacion de quien accede a esta parte
Seguridad::verificarAcceso('estudiante');

// llamada de la cadena de conexion y notificaciones
require_once __DIR__ . '/../../Config/Conexion.php';
$db = ConexionDB::obtenerConexion();
$id_usuario_actual = $_SESSION['usuario_id'];

// Consultamos las notificaciones no leídas del estudiante (Limitado a 5)
$sql_notif = "SELECT * FROM notificaciones WHERE usuario_id = $id_usuario_actual AND leida = 0 ORDER BY fecha DESC LIMIT 5";
$res_notif = mysqli_query($db, $sql_notif);
$total_notif = mysqli_num_rows($res_notif);
?>
<!doctype html>
<html lang="es">

<head>
    <title>Alumno | Proyecto</title>
    <link rel="icon" type="image/png" href="../Assets/Img/logo.png">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-light pb-5">

    <style>
        .bg-custom-green {
            background-color: #065b3e !important;
        }

        .cerrar-sesion {
            transition: color 0.3s, transform 0.3s ease-in-out;
            background: none;
            border: none;
            color: white;
            font-size: inherit;
            cursor: pointer;
        }

        .cerrar-sesion:hover {
            color: #007BFF;
            transform: scale(1.1);
        }

        .cerrar-sesion i {
            transition: transform 0.3s ease-in-out;
        }

        .cerrar-sesion:hover i {
            transform: rotate(20deg);
        }

        .hover-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease;
        }

        body {
            /* Usamos exactamente el mismo gradiente lineal semi-transparente y la imagen */
            background: linear-gradient(135deg, rgba(240, 244, 243, 0.8) 0%, rgba(216, 226, 223, 0.8) 100%), 
                        url("../Assets/Img/FondoCimarron.jpg");
            background-position: center;
            background-repeat: repeat; 
            background-attachment: fixed;
            min-height: 100vh;
        }

        .nav-actions {
            background: white;
            border-radius: 15px;
            padding: 15px 25px;
            margin-top: -25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-uabc-gold { background-color: #a67c00; color: white; border-radius: 10px; font-weight: bold; transition: all 0.3s; border: none; }
        .btn-uabc-gold:hover { background-color: #8b6900; color: white; transform: translateY(-2px); }

        .btn-uabc-green { background-color: #065b3e; color: white; border-radius: 10px; font-weight: bold; transition: all 0.3s; border: none; }
        .btn-uabc-green:hover { background-color: #1a7230; color: white; transform: translateY(-2px); }

        .card-curso { border-radius: 20px !important; overflow: hidden; transition: transform 0.3s; }
        .card-curso:hover { transform: translateY(-10px); }

        .curso-img { height: 200px; object-fit: cover; width: 100%; }

        .btn-manage { background-color: #065b3e; color: white; border-radius: 8px; font-weight: bold; }
        .btn-delete-outline { color: #dc3545; border: 1px solid #dc3545; border-radius: 8px; }
        .btn-delete-outline:hover { background-color: #dc3545; color: white; }
    </style>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark shadow bg-custom-green">
            <div class="container">
                <a class="navbar-brand fw-bold" href="./Index.php"><i class="fa-solid fa-graduation-cap me-2"></i> Catálogo de aprendizaje</a>

                <div class="d-flex align-items-center ms-auto">

                    <div class="nav-item dropdown me-4" style="list-style: none;">
                        <a class="nav-link position-relative text-white d-flex align-items-center" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bell fs-5"></i>
                            <?php if ($total_notif > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size: 0.65rem; border: 2px solid #065b3e;">
                                    <?php echo $total_notif; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 15px;">
                            <li>
                                <h6 class="dropdown-header fw-bold" style="color: #065b3e;"><i class="fa-solid fa-bell me-2"></i> Mis Notificaciones</h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>

                            <?php if ($total_notif > 0): ?>
                                <?php while ($notif = mysqli_fetch_assoc($res_notif)): ?>
                                    <li>
                                        <a class="dropdown-item text-wrap py-2 text-dark" style="border-left: 4px solid #bfa071;" href="Ver_Curso.php?id=<?php echo $notif['curso_id']; ?>">
                                            <span class="small d-block mb-1"><?php echo $notif['mensaje']; ?></span>
                                            <div class="text-end"><small class="text-muted" style="font-size: 0.65rem;"><i class="fa-regular fa-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($notif['fecha'])); ?></small></div>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider m-0"></li>
                                <?php endwhile; ?>
                                
                                <li>
                                    <a class="dropdown-item text-center fw-bold mt-2" href="../Config/Marcar_Leidas.php" style="color: #065b3e; font-size: 0.85rem;">
                                        <i class="fa-solid fa-check-double me-1"></i> Marcar todas como leídas
                                    </a>
                                </li>
                            <?php else: ?>
                                <li><span class="dropdown-item text-muted small text-center py-3">No tienes notificaciones nuevas.</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="navbar-text text-white border-start border-light border-opacity-25 ps-3 d-flex align-items-center">
                        <div class="dropdown me-3">
                            <a href="#" class="text-white text-decoration-none dropdown-toggle d-flex align-items-center" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="../Assets/Img/ALUMNOS.png" alt="Perfil" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                <span><?php echo $_SESSION['nombre']; ?></span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userMenu">
                                <li>
                                    <a class="dropdown-item fw-bold text-dark" href="./ProgresoEstudiante.php">
                                        <i class="fa-solid fa-chart-line text-success me-2"></i> Ver mi progreso
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <a href="Logout.php" class="btn btn-danger btn-sm cerrar-sesion">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    </div>

                </div>
            </div>
        </nav>
    </header>