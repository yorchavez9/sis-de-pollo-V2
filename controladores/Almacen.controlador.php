<?php
class ControladorAlmacen
{
    /*=============================================
    REGISTRO DE ALMACÉN
    =============================================*/
    static public function ctrCrearAlmacen()
    {
        $tabla = "almacenes";
        $datos = array(
            "id_sucursal" => $_POST["id_sucursal"],
            "nombre" => $_POST["nombre"],
            "descripcion" => $_POST["descripcion"] ?? null,
            "tipo" => $_POST["tipo"],
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloAlmacen::mdlIngresarAlmacen($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR ALMACENES
    =============================================*/
    static public function ctrMostrarAlmacenes($item, $valor)
    {
        $tabla = "almacenes";
        $respuesta = ModeloAlmacen::mdlMostrarAlmacenes($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR ALMACÉN (Obtener datos)
    =============================================*/
    static public function ctrEditarAlmacen()
    {
        $tabla = "almacenes";
        $item = "id_almacen";
        $valor = $_POST["id_almacen"];
        $respuesta = ModeloAlmacen::mdlMostrarAlmacenes($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR ALMACÉN
    =============================================*/
    static public function ctrActualizarAlmacen()
    {
        $tabla = "almacenes";
        $datos = array(
            "id_almacen" => $_POST["id_almacen"],
            "id_sucursal" => $_POST["id_sucursal"],
            "nombre" => $_POST["nombre"],
            "descripcion" => $_POST["descripcion"] ?? null,
            "tipo" => $_POST["tipo"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloAlmacen::mdlActualizarAlmacen($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE ALMACÉN
    =============================================*/
    static public function ctrCambiarEstadoAlmacen()
    {
        $tabla = "almacenes";
        $datos = array(
            "id_almacen" => $_POST["id_almacen"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloAlmacen::mdlCambiarEstadoAlmacen($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR ALMACÉN
    =============================================*/
    static public function ctrBorrarAlmacen()
    {
        $tabla = "almacenes";
        $datos = $_POST["id_almacen"];
        $respuesta = ModeloAlmacen::mdlBorrarAlmacen($tabla, $datos);
        echo $respuesta;
    }
}

?>