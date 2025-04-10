<?php

require_once "../modelos/SerieComprobante.modelo.php";
require_once "../controladores/SerieComprobante.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorSerieComprobante::ctrCrearSerieComprobante();
                break;
            case 'editar':
                ControladorSerieComprobante::ctrEditarSerieComprobante();
                break;
            case 'actualizar':
                ControladorSerieComprobante::ctrActualizarSerieComprobante();
                break;
            case 'cambiarEstado':
                ControladorSerieComprobante::ctrCambiarEstadoSerieComprobante();
                break;
            case 'eliminar':
                ControladorSerieComprobante::ctrBorrarSerieComprobante();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todas las series o filtrar por parámetros GET
    $item = null;
    $valor = null;
    ControladorSerieComprobante::ctrMostrarSeriesComprobantes($item, $valor);
}