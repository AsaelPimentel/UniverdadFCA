<?php
// 1. Salimos de Public con ../ para encontrar la carpeta Config
include_once '../Config/Conexion.php'; 

// Estos se quedan igual porque la carpeta Includes sí está dentro de Public
include_once 'Includes/Header.php';
include_once 'Includes/Curso.php';

// Ahora sí, PHP ya sabe qué es ConexionDB
$conexion = ConexionDB::obtenerConexion();
$cursoObj = new Curso($conexion);

// 2. Extraemos los datos a través del modelo
$res_cursos = $cursoObj->obtenerCursosCatalogo();
?>

<div class="container mt-4 mb-4">
    <div class="py-4 px-4 text-center bg-custom-green text-white shadow-sm position-relative overflow-hidden" style="border-radius: 15px;">
        <i class="fa-solid fa-book-open-reader position-absolute" style="font-size: 8rem; opacity: 0.05; right: -10px; bottom: -20px; transform: rotate(-10deg);"></i>
        <h3 class="fw-bold mb-2 position-relative z-1">
            <i class="fa-solid fa-compass text-warning me-2"></i> Explora el Catálogo
        </h3>
        <p class="col-lg-8 mx-auto mb-0 opacity-75 position-relative z-1" style="font-size: 0.95rem;">
            Desarrolla nuevas habilidades en tecnología y análisis de datos con los mejores instructores de la institución. Tu futuro comienza aquí.
        </p>
    </div>
</div>

<div class="container" id="catalogo">
    <div class="row">
        <?php if ($res_cursos && mysqli_num_rows($res_cursos) > 0): ?>
            <?php while ($curso = mysqli_fetch_assoc($res_cursos)): 
                // Añadimos la ruta de ../ para salir de Client si las imágenes están en la raíz
                $img = (!empty($curso['imagen'])) ? '../' . $curso['imagen'] : 'https://via.placeholder.com/400x200?text=Aprende+Ahora';
            ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-effect" style="border-radius: 20px; overflow: hidden;">
                        
                        <img src="<?php echo $img; ?>" class="card-img-top curso-img" alt="Portada">
                        
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold text-dark mb-2"><?php echo $curso['titulo']; ?></h5>
                            
                            <p class="small fw-bold mb-3" style="color: #065b3e;">
                                <i class="fa-solid fa-chalkboard-user me-2 text-warning"></i> Por: <?php echo $curso['nombre_instructor']; ?>
                            </p>
                            
                            <p class="card-text text-muted small">
                                <?php echo substr($curso['descripcion'], 0, 100) . '...'; ?>
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0 p-4 pt-0 text-center">
                            <a href="Ver_Curso.php?id=<?php echo $curso['id']; ?>" class="btn btn-uabc-green w-100 fw-bold py-2 shadow-sm rounded-pill">
                                Entrar al Curso <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa-solid fa-box-open display-1 opacity-25 mb-3 d-block"></i>
                <h5 class="fw-bold">Aún no hay cursos disponibles.</h5>
                <p>Los instructores están preparando material excelente para ti.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "Includes/Footer.php"; ?>