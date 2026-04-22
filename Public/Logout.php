<?php
// 1. Iniciamos la sesión para poder acceder a ella
session_start();

// 2. Destruimos todas las variables de sesión que creamos al entrar
session_unset();

// 3. Destruimos la sesión por completo del servidor
session_destroy();

// 4. Redirigimos al usuario al login con un mensaje de despedida (opcional)
header("Location: ../");
exit();
?>