<?php

require_once "../modelos/Proveedor.modelo.php";
require_once "../controladores/Proveedor.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorProveedor::ctrCrearProveedor();
                break;
            case 'editar':
                ControladorProveedor::ctrEditarProveedor();
                break;
            case 'actualizar':
                ControladorProveedor::ctrActualizarProveedor();
                break;
            case 'cambiarEstado':
                ControladorProveedor::ctrCambiarEstadoProveedor();
                break;
            case 'eliminar':
                ControladorProveedor::ctrBorrarProveedor();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los proveedores (solo tipo PROVEEDOR)
    /* $item = "tipo_persona";
    $valor = "PROVEEDOR"; */
    ControladorProveedor::ctrMostrarProveedores($item=null, $valor=null);
}