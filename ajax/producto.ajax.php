<?php

require_once "../modelos/Producto.modelo.php";
require_once "../controladores/Producto.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorProducto::ctrCrearProducto();
                break;
            case 'editar':
                ControladorProducto::ctrEditarProducto();
                break;
            case 'actualizar':
                ControladorProducto::ctrActualizarProducto();
                break;
            case 'cambiarEstado':
                ControladorProducto::ctrCambiarEstadoProducto();
                break;
            case 'eliminar':
                ControladorProducto::ctrBorrarProducto();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los productos
    ControladorProducto::ctrMostrarProductos($item=null, $valor=null);
}