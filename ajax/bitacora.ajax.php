<?php

require_once "../modelos/Bitacora.modelo.php";
require_once "../controladores/Bitacora.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'limpiar':
                ControladorBitacora::ctrLimpiarBitacora();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Obtener parámetros de filtrado
    $fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
    $fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
    $idUsuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;
    $accion = isset($_GET['accion']) ? $_GET['accion'] : null;
    
    ControladorBitacora::ctrMostrarBitacora($fechaInicio, $fechaFin, $idUsuario, $accion);
}