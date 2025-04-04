<?php

require_once "../modelos/Trabajador.modelo.php";
require_once "../controladores/Trabajador.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorTrabajador::ctrCrearTrabajador();
                break;
            case 'editar':
                ControladorTrabajador::ctrEditarTrabajador();
                break;
            case 'actualizar':
                ControladorTrabajador::ctrActualizarTrabajador();
                break;
            case 'cambiarEstado':
                ControladorTrabajador::ctrCambiarEstadoTrabajador();
                break;
            case 'eliminar':
                ControladorTrabajador::ctrBorrarTrabajador();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los trabajadores (solo tipo TRABAJADOR)
    /* $item = "tipo_persona";
    $valor = "TRABAJADOR"; */
    ControladorTrabajador::ctrMostrarTrabajadores($item=null, $valor=null);
}