<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Verificar si hay datos en la sesión
    if (!empty($_SESSION)) {
        // Devolver los datos de la sesión en formato JSON
        echo json_encode(array(
            "status" => true,
            "session_data" => $_SESSION
        ));
    } else {
        // Si no hay datos en la sesión
        echo json_encode(array(
            "status" => false,
            "message" => "No hay datos de sesión disponibles"
        ));
    }
}
?>