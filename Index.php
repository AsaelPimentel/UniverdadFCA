<!doctype html>
<html lang="es">

<head>
    <title>Iniciar Sesión | Universidad Udemy</title>
    <link rel="icon" type="image/png" href="Assets/Img/logo.png">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Assets/Css/Login.css">
</head>

<body class="login-page">
    <div class="login-wrapper">
        <div class="login-card shadow-lg">
            <div class="row g-0">
                
                <div class="col-md-5 brand-side d-flex flex-column justify-content-center align-items-center text-center p-5">
                    
                    <div class="logo-container mb-4 shadow-sm">
                        <img src="Assets/Img/Logo CNI.png" alt="Logo CNI" class="img-fluid cni-logo-main">
                    </div>
                    
                    <div class="uabc-inline-container d-flex align-items-center justify-content-center mb-1">
                        <img src="Assets/Img/EscudoUABC.png" alt="Escudo UABC" class="uabc-logo-small me-3">
                        <h5 class="institution-name text-white mb-0">UNIVERSIDAD AUTÓNOMA DE BAJA CALIFORNIA</h5>
                    </div>

                    <h6 class="faculty-name text-white text-uppercase opacity-75 mb-3">Facultad de Ciencias Administrativas</h6>
                    <hr class="w-25 mx-auto my-3 brand-divider">
                    <p class="motto fst-italic">"Por la realización plena del ser"</p>
                </div>

                <div class="col-md-7 form-side p-5 d-flex flex-column justify-content-center">
                    <div class="text-center mb-5">
                        <h3 class="fw-bold text-dark mb-2">Acceso al Sistema</h3>
                        <p class="text-muted small">Plataforma Educativa Exclusiva</p>
                    </div>

                    <form action="Login.php" method="POST">
                        <div class="form-floating mb-4 position-relative custom-form-floating">
                            <input type="email" name="email" class="form-control" id="emailInput" placeholder=" " required>
                            <label for="emailInput">Correo Institucional</label>
                            <i class="fa-solid fa-envelope input-icon"></i>
                        </div>

                        <div class="form-floating mb-4 position-relative custom-form-floating">
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder=" " required>
                            <label for="passwordInput">Contraseña</label>
                            <i class="fa-solid fa-lock input-icon"></i>
                        </div>

                        <button type="submit" class="btn btn-uabc-gold w-100 fw-bold py-3 mt-3 shadow-sm login-btn text-uppercase">
                            Iniciar Sesión <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <div class="text-center mt-5 pt-4 border-top border-light">
                        <small class="text-muted">
                            ¿Problemas para acceder a tu cuenta? <br>
                            <a href="#" class="text-decoration-none fw-bold link-uabc-green">Contacta al administrador del sistema</a>
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>