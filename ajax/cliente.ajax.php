<?php

require_once "../modelos/Cliente.modelo.php";
require_once "../controladores/Cliente.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorCliente::ctrCrearCliente();
                break;
            case 'editar':
                ControladorCliente::ctrEditarCliente();
                break;
            case 'actualizar':
                ControladorCliente::ctrActualizarCliente();
                break;
            case 'cambiarEstado':
                ControladorCliente::ctrCambiarEstadoCliente();
                break;
            case 'eliminar':
                ControladorCliente::ctrBorrarCliente();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los clientes (solo tipo CLIENTE)
    /* $item = "tipo_persona";
    $valor = "CLIENTE"; */
    ControladorCliente::ctrMostrarClientes($item=null, $valor=null);
}