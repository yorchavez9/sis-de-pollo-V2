<?php

require_once "../modelos/Transportista.modelo.php";
require_once "../controladores/Transportista.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorTransportista::ctrCrearTransportista();
                break;
            case 'editar':
                ControladorTransportista::ctrEditarTransportista();
                break;
            case 'actualizar':
                ControladorTransportista::ctrActualizarTransportista();
                break;
            case 'cambiarEstado':
                ControladorTransportista::ctrCambiarEstadoTransportista();
                break;
            case 'eliminar':
                ControladorTransportista::ctrBorrarTransportista();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los transportistas (solo tipo TRANSPORTISTA)
    /* $item = "tipo_persona";
    $valor = "TRANSPORTISTA"; */
    ControladorTransportista::ctrMostrarTransportistas($item=null, $valor=null);
}