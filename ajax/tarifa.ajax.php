<?php

require_once "../modelos/Tarifa.modelo.php";
require_once "../controladores/Tarifa.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorTarifa::ctrCrearTarifa();
                break;
            case 'editar':
                ControladorTarifa::ctrEditarTarifa();
                break;
            case 'actualizar':
                ControladorTarifa::ctrActualizarTarifa();
                break;
            case 'cambiarEstado':
                ControladorTarifa::ctrCambiarEstadoTarifa();
                break;
            case 'eliminar':
                ControladorTarifa::ctrBorrarTarifa();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todas las tarifas o filtrar por parámetros GET
    $item = null;
    $valor = null;
    ControladorTarifa::ctrMostrarTarifas($item, $valor);
}