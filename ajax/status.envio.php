<?php
require_once "../modelos/Envio.modelo.php";
require_once "../controladores/Envio.controlador.php";

// Configurar cabeceras para respuesta JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Max-Age: 3600');

try {
    // Verificar que la acción esté definida
    if (!isset($_GET['action'])) {
        throw new Exception('Acción no especificada');
    }

    $action = $_GET['action'];
    $response = [];

    switch ($action) {
        case 'codigo':
            // Validar que el código esté presente
            if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
                throw new Exception('Código de envío no proporcionado');
            }

            $codigo = trim($_GET['codigo']);
            $detalles = ModeloEnvio::mdlMostrarDetalleEnvioRastreo($codigo);

            $response = [
                'success' => true,
                'data' => $detalles ?: [],
                'count' => $detalles ? count($detalles) : 0,
                'codigo' => $codigo,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            break;

        case 'detalle':
            // Validar que el código esté presente
            if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
                throw new Exception('Código de envío no proporcionado');
            }

            $codigo = trim($_GET['codigo']);
            $detalles = ModeloEnvio::mdlMostrarDetalleEnvioRastreo($codigo);

            $response = [
                'success' => true,
                'data' => $detalles ?: [],
                'count' => $detalles ? count($detalles) : 0,
                'codigo' => $codigo,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            break;

        case 'listar':
            $envios = ControladorEnvio::ctrMostrarEnvios();
            $response = [
                'success' => true,
                'data' => $envios ?: [],
                'count' => $envios ? count($envios) : 0,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            break;

        default:
            throw new Exception('Acción no reconocida');
    }

    // Enviar respuesta
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}