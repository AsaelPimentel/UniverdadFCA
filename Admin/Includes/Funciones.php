<?php
// 1. ESCUDO DE SEGURIDAD (2 saltos hacia atrás: salimos de Includes/ y salimos de Admin/)
require_once __DIR__ . '/../../Config/Seguridad.php';
Seguridad::verificarAcceso('admin'); // Exigimos que solo el administrador pueda procesar esto

// Archivo: Includes/Funciones.php

function obtenerTodosLosUsuarios($db) {
    $query = "SELECT id, nombre, email, rol, fecha_registro 
              FROM usuarios 
              ORDER BY rol, nombre ASC";
              
    $resultado = mysqli_query($db, $query);

    if (!$resultado) {
        error_log("Error en la consulta: " . mysqli_error($db));
        return false;
    }

    return $resultado;
}