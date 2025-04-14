<?php
require_once "../modelos/Envio.modelo.php";
require_once "../controladores/Envio.controlador.php";

// Establecer cabecera JSON para todas las respuestas
header('Content-Type: application/json');

// Función para obtener los datos de entrada según el método de solicitud
function getRequestData() {
    return $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
}

// Obtener datos de la solicitud
$requestData = getRequestData();

// Manejar solicitudes AJAX
if (isset($requestData['action'])) {
    switch ($requestData['action']) {
        case 'listar':
            // Obtener filtros según el método de solicitud
            $filtros = [
                'origen' => $requestData['origen'] ?? null,
                'destino' => $requestData['destino'] ?? null,
                'tipo' => $requestData['tipo'] ?? null,
                'estado' => $requestData['estado'] ?? null
            ];
            
            $response = ControladorEnvio::ctrMostrarEnvios(null, null, $filtros);
            echo json_encode($response);
            break;
            
        case 'detalle':
            $idEnvio = $requestData['id'] ?? null;
            if (!$idEnvio) {
                echo json_encode(['status' => false, 'message' => 'ID de envío no proporcionado']);
                break;
            }
            $response = ControladorEnvio::ctrMostrarDetalleEnvio($idEnvio);
            echo json_encode($response);
            break;
            
        case 'calcularCosto':
            $response = ControladorEnvio::ctrCalcularCostoEnvio();
            echo json_encode($response);
            break;
            
        case 'crear':
            // Manejar la creación con FormData (incluyendo archivos)
            $response = ControladorEnvio::ctrCrearEnvio();
            echo json_encode($response);
            break;
            
        case 'cambiarEstado':
            $response = ControladorEnvio::ctrCambiarEstadoEnvio();
            echo json_encode($response);
            break;
            
        case 'subirDocumento':
            $response = ControladorEnvio::ctrSubirDocumentoEnvio();
            echo json_encode($response);
            break;
            
        case 'eliminarDocumento':
            $response = ControladorEnvio::ctrEliminarDocumentoEnvio();
            echo json_encode($response);
            break;
            
        default:
            echo json_encode(['status' => false, 'message' => 'Acción no válida']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Acción no especificada']);
}