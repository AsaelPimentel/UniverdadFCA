<?php
// Llamada del filtro de seguridad 
require_once __DIR__ . '/../../Config/Seguridad.php';

// Especificacion de quien accede a esta parte
Seguridad::verificarAcceso('estudiante');

// llamada de la cadena de conexion
require_once __DIR__ . '/../../Config/Conexion.php';
?>
<!doctype html>
<html lang="en">

<head>
    <title>Alumno | Proyecto</title>
    <link rel="icon" type="image/png" href="../Assets/Img/logo.png">
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
    <!-- Font Awesome 7.0.1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-light pb-5" background="">

    <style>
        .bg-custom-green {
            background-color: #065b3e !important;
        }

        .cerrar-sesion {
            transition: color 0.3s, transform 0.3s ease-in-out;
            background: none;
            /* Asegura que el botón no tenga fondo */
            border: none;
            /* Elimina el borde del botón */
            color: white;
            /* Hereda el color del texto */
            font-size: inherit;
            /* Hereda el tamaño de la fuente */
            cursor: pointer;
            /* Cambia el cursor para que parezca un clic */
        }

        .cerrar-sesion:hover {
            color: #007BFF;
            /* Cambia el color del texto al pasar el mouse */
            transform: scale(1.1);
            /* Aumenta ligeramente el tamaño del texto e ícono */
        }

        .cerrar-sesion i {
            transition: transform 0.3s ease-in-out;
            /* Transición suave para el ícono */
        }

        .cerrar-sesion:hover i {
            transform: rotate(20deg);
            /* Gira el ícono al pasar el mouse */
        }

        .hover-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease;
        }

        .curso-img {
            height: 180px;
            object-fit: cover;
            width: 100%;
        }

        body {
/* Usamos exactamente el mismo gradiente lineal semi-transparente y la imagen */
            background: linear-gradient(135deg, rgba(240, 244, 243, 0.8) 0%, rgba(216, 226, 223, 0.8) 100%), 
                        url("../Assets/Img/FondoCimarron.jpg");
            background-position: center;
            background-repeat: repeat; /* Lo dejaste en repeat en tu ejemplo, lo mantengo */
            background-attachment: fixed;
            min-height: 100vh;
        }

        .bg-custom-green {
            background-color: #065b3e !important;
        }

        /* Barra de navegación blanca superior */
        .nav-actions {
            background: white;
            border-radius: 15px;
            padding: 15px 25px;
            margin-top: -25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-uabc-gold {
            background-color: #a67c00;
            color: white;
            border-radius: 10px;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
        }

        .btn-uabc-gold:hover {
            background-color: #8b6900;
            color: white;
            transform: translateY(-2px);
        }

        .btn-uabc-green {
            background-color: #065b3e;
            color: white;
            border-radius: 10px;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
        }

        .btn-uabc-green:hover {
            background-color: #1a7230;
            color: white;
            transform: translateY(-2px);
        }

        .card-curso {
            border-radius: 20px !important;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .card-curso:hover {
            transform: translateY(-10px);
        }

        .curso-img {
            height: 200px;
            object-fit: cover;
        }

        .btn-manage {
            background-color: #065b3e;
            color: white;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn-delete-outline {
            color: #dc3545;
            border: 1px solid #dc3545;
            border-radius: 8px;
        }

        .btn-delete-outline:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
    <header>
        <nav class="navbar navbar-dark bg-danger shadow bg-custom-green">
            <div class="container">
                <a class="navbar-brand fw-bold" href="./Index.php"><i class="fa-solid fa-graduation-cap me-2"></i> Catalogo de aprendizaje</a>

                <div class="navbar-text text-white d-flex align-items-center">

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
        </nav>
    </header>