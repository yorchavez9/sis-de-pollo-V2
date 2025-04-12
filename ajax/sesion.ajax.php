<?php
session_start();

// Verificar si la acción solicitada es 'sesion'
if(isset($_GET['action']) && $_GET['action'] == 'sesion') {
    // Verificar si existe la sesión de usuario
    if(isset($_SESSION["usuario"])) {
        // Devolver los datos de la sesión en formato JSON
        header('Content-Type: application/json');
        echo json_encode($_SESSION["usuario"]);
    } else {
        // Si no hay sesión activa
        header('Content-Type: application/json');
        echo json_encode([
            'status' => false,
            'message' => 'No hay sesión activa'
        ]);
    }
    exit;
}

// Si no es una acción válida
header('Content-Type: application/json');
echo json_encode([
    'status' => false,
    'message' => 'Acción no válida'
]);
?>