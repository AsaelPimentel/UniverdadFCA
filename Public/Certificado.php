<?php
require_once __DIR__ . '/../Config/Seguridad.php';
Seguridad::verificarAcceso('estudiante');
require_once __DIR__ . '/../Config/Conexion.php';

$conexion = ConexionDB::obtenerConexion();
$id_curso = isset($_GET['curso_id']) ? mysqli_real_escape_string($conexion, $_GET['curso_id']) : 0;
$nombre_alumno = $_SESSION['nombre'];

$res_curso = mysqli_query($conexion, "SELECT titulo FROM cursos WHERE id = '$id_curso'");
$curso_data = mysqli_fetch_assoc($res_curso);
$nombre_curso = ($curso_data) ? $curso_data['titulo'] : "Curso Finalizado";
$fecha_finalizacion = date('d/m/Y');

include_once 'Includes/Header.php';
?>

<style>
    /* Contenedor de visualización */
    .view-container {
        background-color: #525659;
        padding: 20px 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* AJUSTE PARA EVITAR SALTO DE PÁGINA */
    .certificado-page {
        background: white;
        width: 11in;
        height: 8.4in; /* Reducido ligeramente de 8.5 para dar margen de seguridad */
        margin: 0 auto;
        padding: 0.25in; 
        box-sizing: border-box;
        position: relative;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .border-main {
        border: 12px solid #065b3e;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        padding: 8px;
    }

    .border-inner {
        border: 2px solid #bfa071;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        padding: 30px;
        text-align: center;
        background-color: #fdfdfd;
        background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png');
    }

    .cert-logo { width: 90px; margin-bottom: 10px; }
    .cert-fca { font-size: 0.9rem; color: #666; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 5px; }
    .cert-title { font-family: 'Playfair Display', serif; font-size: 2.8rem; color: #065b3e; font-weight: bold; margin-bottom: 15px; }
    .cert-text { font-size: 1rem; color: #333; margin-bottom: 0; }
    .cert-name { font-family: 'Playfair Display', serif; font-size: 2.5rem; color: #a67c00; font-weight: bold; margin: 10px 0; border-bottom: 2px solid #a67c00; display: inline-block; padding: 0 40px; }
    .cert-course { font-size: 1.6rem; color: #065b3e; font-weight: bold; display: block; margin-top: 5px; }
    
    .cert-footer { margin-top: 30px; }
    .firma-box { border-top: 1px solid #333; width: 220px; margin: 0 auto; padding-top: 8px; }
    .firma-text { font-weight: bold; color: #065b3e; font-size: 0.8rem; }

    .no-print-zone { text-align: center; margin-bottom: 15px; }

    /* REGLAS CRÍTICAS DE IMPRESIÓN */
    @media print {
        @page {
            size: letter landscape;
            margin: 0 !important; /* Elimina márgenes del sistema */
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100%;
            background: none !important;
        }
        header, footer, nav, .no-print-zone { 
            display: none !important; 
        }
        .view-container { 
            padding: 0 !important; 
            margin: 0 !important;
            background: none !important;
            display: block !important;
        }
        .certificado-page {
            box-shadow: none !important;
            margin: 0 !important;
            width: 100vw !important; /* Ocupa todo el ancho disponible */
            height: 100vh !important; /* Ocupa todo el alto disponible */
            padding: 0.25in !important;
            border: none !important;
        }
    }
</style>

<div class="view-container">
    <div class="no-print-zone">
        <a href="Ver_Curso.php?id=<?php echo $id_curso; ?>" class="btn btn-outline-light me-2 shadow-sm">
            <i class="fa-solid fa-arrow-left me-2"></i>Volver al Curso
        </a>
        <button onclick="window.print()" class="btn btn-uabc-gold shadow px-5">
            <i class="fa-solid fa-print me-2"></i> Imprimir / Guardar PDF
        </button>
    </div>

    <div class="certificado-page">
        <div class="border-main">
            <div class="border-inner">
                <img src="Assets/Img/logo.png" alt="UABC" class="cert-logo">
                <div class="cert-fca">Facultad de Ciencias Administrativas</div>
                <h1 class="cert-title">Reconocimiento</h1>
                <p class="cert-text">Se otorga el presente documento a:</p>
                <div class="cert-name"><?php echo $nombre_alumno; ?></div>
                <p class="cert-text">Por su dedicación y cumplimiento en el curso:</p>
                <span class="cert-course">"<?php echo $nombre_curso; ?>"</span>

                <div class="row cert-footer">
                    <div class="col-6">
                        <p class="cert-text" style="margin-top: 15px;">Expedido el: <b><?php echo $fecha_finalizacion; ?></b></p>
                    </div>
                    <div class="col-6 text-center">
                        <div class="firma-box">
                            <p class="firma-text mb-0">Dirección de la Facultad</p>
                            <small class="text-muted">UABC Mexicali</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Includes/Footer.php'; ?>