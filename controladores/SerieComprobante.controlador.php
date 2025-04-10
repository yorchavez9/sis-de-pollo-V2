<?php
class ControladorSerieComprobante
{
    /*=============================================
    REGISTRO DE SERIE COMPROBANTE
    =============================================*/
    static public function ctrCrearSerieComprobante()
    {
        $tabla = "series_comprobantes";
        $datos = array(
            "id_sucursal" => $_POST["id_sucursal"],
            "id_tipo_comprobante" => $_POST["id_tipo_comprobante"],
            "serie" => $_POST["serie"],
            "numero_inicial" => $_POST["numero_inicial"],
            "numero_actual" => $_POST["numero_actual"],
            "numero_final" => $_POST["numero_final"] ?? null,
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloSerieComprobante::mdlIngresarSerieComprobante($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR SERIES COMPROBANTES
    =============================================*/
    static public function ctrMostrarSeriesComprobantes($item, $valor)
    {
        $tabla = "series_comprobantes";
        $respuesta = ModeloSerieComprobante::mdlMostrarSeriesComprobantes($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR SERIE COMPROBANTE (Obtener datos)
    =============================================*/
    static public function ctrEditarSerieComprobante()
    {
        $tabla = "series_comprobantes";
        $item = "id_serie";
        $valor = $_POST["id_serie"];
        $respuesta = ModeloSerieComprobante::mdlMostrarSeriesComprobantes($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR SERIE COMPROBANTE
    =============================================*/
    static public function ctrActualizarSerieComprobante()
    {
        $tabla = "series_comprobantes";
        $datos = array(
            "id_serie" => $_POST["id_serie"],
            "id_sucursal" => $_POST["id_sucursal"],
            "id_tipo_comprobante" => $_POST["id_tipo_comprobante"],
            "serie" => $_POST["serie"],
            "numero_inicial" => $_POST["numero_inicial"],
            "numero_actual" => $_POST["numero_actual"],
            "numero_final" => $_POST["numero_final"] ?? null,
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloSerieComprobante::mdlActualizarSerieComprobante($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE SERIE COMPROBANTE
    =============================================*/
    static public function ctrCambiarEstadoSerieComprobante()
    {
        $tabla = "series_comprobantes";
        $datos = array(
            "id_serie" => $_POST["id_serie"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloSerieComprobante::mdlCambiarEstadoSerieComprobante($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR SERIE COMPROBANTE
    =============================================*/
    static public function ctrBorrarSerieComprobante()
    {
        $tabla = "series_comprobantes";
        $datos = $_POST["id_serie"];
        $respuesta = ModeloSerieComprobante::mdlBorrarSerieComprobante($tabla, $datos);
        echo $respuesta;
    }
}