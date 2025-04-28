<?php
require_once "../modelos/Envio.modelo.php";
require_once "../controladores/Envio.controlador.php";

// Establecer cabecera JSON para todas las respuestas
header('Content-Type: application/json');

// Manejar solicitudes AJAX
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'listar':
            $filtros = [
                'origen' => $_GET['origen'] ?? null,
                'destino' => $_GET['destino'] ?? null,
                'tipo' => $_GET['tipo'] ?? null,
                'estado' => $_GET['estado'] ?? null
            ];
            $response = ControladorEnvio::ctrMostrarEnvios(null, null, $filtros);
            echo json_encode($response);
            break;

        case 'detalle':
            $idEnvio = $_GET['id'];
            $response = ControladorEnvio::ctrMostrarDetalleEnvio($idEnvio);
            echo json_encode($response);
            break;

        case 'calcularCosto':
            $response = ControladorEnvio::ctrCalcularCostoEnvio();
            echo json_encode($response);
            break;

        default:
            echo json_encode(['status' => false, 'message' => 'Acción no válida']);
    }
} elseif (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'crear':
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
}