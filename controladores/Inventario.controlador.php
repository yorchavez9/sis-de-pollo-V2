<?php
session_start();
require_once "../modelos/Inventario.modelo.php";

class ControladorInventario
{
    /*=============================================
    MOSTRAR INVENTARIO
    =============================================*/
    static public function ctrMostrarInventario($filtroAlmacen = null, $filtroProducto = null, $filtroEstado = null)
    {
        $tabla = "inventario";
        $respuesta = ModeloInventario::mdlMostrarInventario($tabla, $filtroAlmacen, $filtroProducto, $filtroEstado);
        echo $respuesta;
    }

    /*=============================================
    EDITAR INVENTARIO (Obtener datos)
    =============================================*/
    static public function ctrEditarInventario()
    {
        $tabla = "inventario";
        $item = "id_inventario";
        $valor = $_GET["id_inventario"];
        $respuesta = ModeloInventario::mdlMostrarInventario($tabla, null, null, null, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    CONSULTAR INVENTARIO (Producto + Almacén)
    =============================================*/
    static public function ctrConsultarInventario()
    {
        $tabla = "inventario";
        $item1 = "id_producto";
        $valor1 = $_GET["id_producto"];
        $item2 = "id_almacen";
        $valor2 = $_GET["id_almacen"];
        $respuesta = ModeloInventario::mdlConsultarInventario($tabla, $item1, $valor1, $item2, $valor2);
        echo $respuesta;
    }

    /*=============================================
    AJUSTAR INVENTARIO
    =============================================*/
    static public function ctrAjustarInventario()
    {
        // Validar datos requeridos
        if(empty($_POST["id_producto"]) || empty($_POST["id_almacen"]) || empty($_POST["tipo_movimiento"]) || empty($_POST["cantidad"])) {
            echo json_encode([
                "status" => false,
                "message" => "Datos incompletos"
            ]);
            return;
        }

        $tabla = "inventario";
        $datos = array(
            "id_inventario" => $_POST["id_inventario"] ?? null,
            "id_producto" => $_POST["id_producto"],
            "id_almacen" => $_POST["id_almacen"],
            "tipo_movimiento" => $_POST["tipo_movimiento"],
            "cantidad" => (float)$_POST["cantidad"],
            "stock_minimo" => isset($_POST["stock_minimo"]) ? (float)$_POST["stock_minimo"] : null,
            "stock_maximo" => isset($_POST["stock_maximo"]) ? (float)$_POST["stock_maximo"] : null,
            "motivo" => $_POST["motivo"] ?? null,
            "id_usuario" => $_SESSION["usuario"]["id_usuario"] ?? 0
        );
        
        $respuesta = ModeloInventario::mdlAjustarInventario($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    HISTORIAL DE INVENTARIO
    =============================================*/
    static public function ctrHistorialInventario()
    {
        $tabla = "movimientos_inventario";
        $item = "id_inventario";
        $valor = $_GET["id_inventario"];
        $respuesta = ModeloInventario::mdlHistorialInventario($tabla, $item, $valor);
        echo $respuesta;
    }
}