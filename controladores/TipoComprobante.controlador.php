<?php
class ControladorTipoComprobante
{
    /*=============================================
    REGISTRO DE TIPO COMPROBANTE
    =============================================*/
    static public function ctrCrearTipoComprobante()
    {
        $tabla = "tipo_comprobantes";
        $datos = array(
            "codigo" => $_POST["codigo"],
            "nombre" => $_POST["nombre"],
            "serie_obligatoria" => $_POST["serie_obligatoria"],
            "numero_obligatorio" => $_POST["numero_obligatorio"],
            "afecta_inventario" => $_POST["afecta_inventario"],
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloTipoComprobante::mdlIngresarTipoComprobante($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR TIPOS DE COMPROBANTE
    =============================================*/
    static public function ctrMostrarTipoComprobantes($item, $valor)
    {
        $tabla = "tipo_comprobantes";
        $respuesta = ModeloTipoComprobante::mdlMostrarTipoComprobantes($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR TIPO COMPROBANTE (Obtener datos)
    =============================================*/
    static public function ctrEditarTipoComprobante()
    {
        $tabla = "tipo_comprobantes";
        $item = "id_tipo_comprobante";
        $valor = $_POST["id_tipo_comprobante"];
        $respuesta = ModeloTipoComprobante::mdlMostrarTipoComprobantes($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR TIPO COMPROBANTE
    =============================================*/
    static public function ctrActualizarTipoComprobante()
    {
        $tabla = "tipo_comprobantes";
        $datos = array(
            "id_tipo_comprobante" => $_POST["id_tipo_comprobante"],
            "codigo" => $_POST["codigo"],
            "nombre" => $_POST["nombre"],
            "serie_obligatoria" => $_POST["serie_obligatoria"],
            "numero_obligatorio" => $_POST["numero_obligatorio"],
            "afecta_inventario" => $_POST["afecta_inventario"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTipoComprobante::mdlActualizarTipoComprobante($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE TIPO COMPROBANTE
    =============================================*/
    static public function ctrCambiarEstadoTipoComprobante()
    {
        $tabla = "tipo_comprobantes";
        $datos = array(
            "id_tipo_comprobante" => $_POST["id_tipo_comprobante"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTipoComprobante::mdlCambiarEstadoTipoComprobante($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR TIPO COMPROBANTE
    =============================================*/
    static public function ctrBorrarTipoComprobante()
    {
        $tabla = "tipo_comprobantes";
        $datos = $_POST["id_tipo_comprobante"];
        $respuesta = ModeloTipoComprobante::mdlBorrarTipoComprobante($tabla, $datos);
        echo $respuesta;
    }
}