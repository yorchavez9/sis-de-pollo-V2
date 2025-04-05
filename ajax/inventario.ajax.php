<?php

require_once "../modelos/Inventario.modelo.php";
require_once "../controladores/Inventario.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajustar':
                ControladorInventario::ctrAjustarInventario();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "No se especificó acción"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'editar':
                ControladorInventario::ctrEditarInventario();
                break;
            case 'consultar':
                ControladorInventario::ctrConsultarInventario();
                break;
            case 'historial':
                ControladorInventario::ctrHistorialInventario();
                break;
            case 'listar':
                ControladorInventario::ctrMostrarInventario();
                break;
            default:
                echo json_encode(["status" => false, "message" => "Acción no válida"]);
        }
    } else {
        // Mostrar inventario con filtros
        $filtroAlmacen = isset($_GET['filtro_almacen']) ? $_GET['filtro_almacen'] : null;
        $filtroProducto = isset($_GET['filtro_producto']) ? $_GET['filtro_producto'] : null;
        $filtroEstado = isset($_GET['filtro_estado']) ? $_GET['filtro_estado'] : null;
        
        ControladorInventario::ctrMostrarInventario($filtroAlmacen, $filtroProducto, $filtroEstado);
    }
}