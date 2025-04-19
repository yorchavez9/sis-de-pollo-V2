<?php
require_once "../modelos/Envio.modelo.php";
require_once "../controladores/Envio.controlador.php";

// Configurar cabeceras para respuesta JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Obtener todos los envíos sin filtros
    $envios = ControladorEnvio::ctrMostrarEnvios();
    
    // Verificar si se obtuvieron resultados
    if ($envios === false || $envios === null) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener los envíos',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // Si no hay envíos, devolver array vacío
    if (empty($envios)) {
        $envios = [];
    }
    
    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $envios,
        'count' => count($envios),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}