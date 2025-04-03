<?php

require_once "../modelos/Sucursal.modelo.php";
require_once "../controladores/Sucursal.controlador.php";

/*=============================================
AJAX REQUEST HANDLER
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorSucursal::ctrCrearSucursal();
                break;
            case 'editar':
                $item = "id_sucursal";
                $valor = $_POST["id_sucursal"];
                ControladorSucursal::ctrMostrarSucursales($item, $valor);
                break;
            case 'update':
                ControladorSucursal::ctrActualizarSucursal();
                break;
            case 'cambiarEstado':
                ControladorSucursal::ctrCambiarEstadoSucursal();
                break;
            case 'verDetalles':
                $item = "id_sucursal";
                $valor = $_POST["id_sucursal"];
                ControladorSucursal::ctrMostrarSucursales($item, $valor);
                break;
            case 'eliminar':
                ControladorSucursal::ctrBorrarSucursal();
                break;
            default:
                echo json_encode("Acción no válida");
        }
    } else {
        echo json_encode("No se especificó acción");
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    $item = null;
    $valor = null;
    ControladorSucursal::ctrMostrarSucursales($item, $valor);
}