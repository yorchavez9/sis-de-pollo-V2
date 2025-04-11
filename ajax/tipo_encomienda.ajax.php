<?php

require_once "../modelos/TipoEncomienda.modelo.php";
require_once "../controladores/TipoEncomienda.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorTipoEncomienda::ctrCrearTipoEncomienda();
                break;
            case 'editar':
                ControladorTipoEncomienda::ctrEditarTipoEncomienda();
                break;
            case 'actualizar':
                ControladorTipoEncomienda::ctrActualizarTipoEncomienda();
                break;
            case 'cambiarEstado':
                ControladorTipoEncomienda::ctrCambiarEstadoTipoEncomienda();
                break;
            case 'eliminar':
                ControladorTipoEncomienda::ctrBorrarTipoEncomienda();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los tipos de encomienda o filtrar por parámetros GET
    $item = null;
    $valor = null;
    ControladorTipoEncomienda::ctrMostrarTipoEncomiendas($item, $valor);
}