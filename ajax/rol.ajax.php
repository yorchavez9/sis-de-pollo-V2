<?php

require_once "../modelos/Rol.modelo.php";
require_once "../controladores/Rol.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorRoles::ctrCrearRol();
                break;
            case 'editar':
                $item = "id_rol";
                $valor = $_POST["id_rol"];
                ControladorRoles::ctrMostrarRoles($item, $valor);
                break;
            case 'actualizar':
                ControladorRoles::ctrActualizarRol();
                break;
            case 'cambiarEstado':
                ControladorRoles::ctrCambiarEstadoRol();
                break;
            case 'eliminar':
                ControladorRoles::ctrBorrarRol();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todos los roles o filtrar por parámetros GET
    $item = null;
    $valor = null;
    ControladorRoles::ctrMostrarRoles($item, $valor);
}