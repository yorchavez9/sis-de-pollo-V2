<?php

require_once "../modelos/Almacen.modelo.php";
require_once "../controladores/Almacen.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorAlmacen::ctrCrearAlmacen();
                break;
            case 'editar':
                ControladorAlmacen::ctrEditarAlmacen();
                break;
            case 'actualizar':
                ControladorAlmacen::ctrActualizarAlmacen();
                break;
            case 'cambiarEstado':
                ControladorAlmacen::ctrCambiarEstadoAlmacen();
                break;
            case 'eliminar':
                ControladorAlmacen::ctrBorrarAlmacen();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los almacenes o filtrar por parámetros GET
    $item = null;
    $valor = null;
    ControladorAlmacen::ctrMostrarAlmacenes($item, $valor);
}