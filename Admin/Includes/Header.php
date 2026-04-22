<?php
// Llamada del filtro de seguridad 
require_once __DIR__ . '/../../Config/Seguridad.php';
// Especificacion de quien accede a esta parte
Seguridad::verificarAcceso('admin');

require_once __DIR__ . '/../../Config/Conexion.php';

// SEGURIDAD: Solo el 'admin' puede entrar aquí
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../");
    exit();
} ?>
<!doctype html>
<html lang="en">

<head>
    <title>Administracion | proyecto</title>
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

<body>
    <style>
                body {
/* Usamos exactamente el mismo gradiente lineal semi-transparente y la imagen */
            background: linear-gradient(135deg, rgba(240, 244, 243, 0.8) 0%, rgba(216, 226, 223, 0.8) 100%), 
                        url("../Assets/Img/FondoCimarron.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: repeat; /* Lo dejaste en repeat en tu ejemplo, lo mantengo */
            background-attachment: fixed;
            min-height: 100vh;
        }
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

        /* Estilos del Formulario de Registro */
        .card-registro {
            border-radius: 20px !important;
            /* Bordes muy redondeados como en la imagen */
            overflow: hidden;
        }

        /* Caja del icono (Verde UABC) */
        .icon-header-box {
            background-color: #065b3e;
            color: white;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
        }

        /* Color dorado para los iconos de los labels */
        .text-gold-uabc {
            color: #bfa071;
        }

        /* Inputs con fondo gris suave */
        .custom-input {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 10px !important;
            font-size: 0.9rem;
        }

        .custom-input:focus {
            border-color: #065b3e !important;
            box-shadow: 0 0 0 0.25rem rgba(6, 91, 62, 0.1) !important;
            background-color: #fff !important;
        }

        /* Botón Ocre/Dorado de la imagen */
        .btn-uabc-action {
            background-color: #a67c00;
            border: none;
            color: white;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-uabc-action:hover {
            background-color: #8b6900;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(166, 124, 0, 0.2);
        }

        /* Estilos para la Tabla de Usuarios */
        .card-tabla {
            border-radius: 20px !important;
            overflow: hidden;
        }

        /* Encabezado de la tabla (gris muy claro) */
        .table-light-uabc {
            background-color: #f8f9fa;
            color: #6c757d;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            border-bottom: 2px solid #edf2f7;
        }

        /* Badges Redondeados Estilo "Pill" */
        .badge-admin {
            background-color: #065b3e;
            color: white;
            border-radius: 50px;
        }

        .badge-instructor {
            background-color: #004a7c;
            color: white;
            border-radius: 50px;
        }

        .badge-estudiante {
            background-color: #a67c00;
            color: white;
            border-radius: 50px;
        }

        /* Contador del Header */
        .badge-count {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            font-weight: 400;
        }

        /* Botones de Acción (Editar/Borrar) */
        .btn-action-outline {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #94a3b8;
            transition: all 0.2s ease;
        }

        .btn-edit:hover {
            border-color: #065b3e;
            color: #065b3e;
            background: #f0fdf4;
        }

        .btn-delete:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        /* Espaciado de las celdas */
        .align-middle td {
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
    <header>
        <nav class="navbar navbar-dark bg-danger shadow bg-custom-green">
            <div class="container">
                <a class="navbar-brand fw-bold" href="./index.php"><img src="../Assets/Img/Logo CNI.png" alt="" srcset="" style="width:50px;">ADMINISTRACIÓN CNI</a>

                <div class="navbar-text text-white">
                    <img src="../Assets/Img/ALUMNOS.png" alt="Admin" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                    <span class="me-3"> <?php echo $_SESSION['nombre']; ?></span>
                    <a href="Logout.php" class="btn btn-danger btn-sm cerrar-sesion">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </div>
        </nav>
    </header>
    <main></main>