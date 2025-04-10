<?php

require_once "../modelos/TipoComprobante.modelo.php";
require_once "../controladores/TipoComprobante.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorTipoComprobante::ctrCrearTipoComprobante();
                break;
            case 'editar':
                ControladorTipoComprobante::ctrEditarTipoComprobante();
                break;
            case 'actualizar':
                ControladorTipoComprobante::ctrActualizarTipoComprobante();
                break;
            case 'cambiarEstado':
                ControladorTipoComprobante::ctrCambiarEstadoTipoComprobante();
                break;
            case 'eliminar':
                ControladorTipoComprobante::ctrBorrarTipoComprobante();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los tipos de comprobante o filtrar por parámetros GET
    $item = null;
    $valor = null;
    ControladorTipoComprobante::ctrMostrarTipoComprobantes($item, $valor);
}