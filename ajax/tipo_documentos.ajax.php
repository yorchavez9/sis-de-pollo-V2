<?php
require_once "../modelos/TipoDocumento.modelo.php";
require_once "../controladores/TipoDocumento.controlador.php";

// Establecer cabeceras para respuestas JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Función para obtener el contenido de la solicitud
function getRequestData() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return $_GET;
    }
    
    if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    return $_POST;
}

try {
    $requestData = getRequestData();
    $action = $requestData['action'] ?? null;

    // Manejar solicitudes GET
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if ($action === 'listar') {
            echo ControladorTipoDocumento::ctrListarTiposDocumentos();
            exit;
        }
        
        echo json_encode(["status" => false, "message" => "Acción GET no válida"]);
        exit;
    }
    
    // Manejar solicitudes POST
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!$action) {
            echo json_encode(["status" => false, "message" => "No se especificó acción"]);
            exit;
        }
        
        switch ($action) {
            case 'crear':
                echo ControladorTipoDocumento::ctrCrearTipoDocumento();
                break;
            case 'obtener':
                echo ControladorTipoDocumento::ctrObtenerTipoDocumento();
                break;
            case 'actualizar':
                echo ControladorTipoDocumento::ctrActualizarTipoDocumento();
                break;
            case 'cambiarEstado':
                echo ControladorTipoDocumento::ctrCambiarEstadoTipoDocumento();
                break;
            case 'eliminar':
                echo ControladorTipoDocumento::ctrEliminarTipoDocumento();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
        exit;
    }
    
    echo json_encode(["status" => false, "message" => "Método no permitido"]);
    exit;
    
} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "message" => "Error en el servidor: " . $e->getMessage(),
        "error" => $e->getTraceAsString()
    ]);
    exit;
}