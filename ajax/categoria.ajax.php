<?php

require_once "../modelos/Categoria.modelo.php";
require_once "../controladores/Categoria.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorCategoria::ctrCrearCategoria();
                break;
            case 'editar':
                ControladorCategoria::ctrEditarCategoria();
                break;
            case 'actualizar':
                ControladorCategoria::ctrActualizarCategoria();
                break;
            case 'cambiarEstado':
                ControladorCategoria::ctrCambiarEstadoCategoria();
                break;
            case 'eliminar':
                ControladorCategoria::ctrBorrarCategoria();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Mostrar todas las categorías
    $item = null;
    $valor = null;
    ControladorCategoria::ctrMostrarCategorias($item, $valor);
}