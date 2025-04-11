<?php
class ControladorTarifa
{
    /*=============================================
    REGISTRO DE TARIFA
    =============================================*/
    static public function ctrCrearTarifa()
    {
        $tabla = "tarifas_envio";
        $datos = array(
            "id_sucursal_origen" => $_POST["id_sucursal_origen"],
            "id_sucursal_destino" => $_POST["id_sucursal_destino"],
            "id_tipo_encomienda" => $_POST["id_tipo_encomienda"],
            "rango_peso_min" => $_POST["rango_peso_min"],
            "rango_peso_max" => $_POST["rango_peso_max"],
            "costo_base" => $_POST["costo_base"],
            "costo_kg_extra" => $_POST["costo_kg_extra"] ?? 0.00,
            "tiempo_estimado" => $_POST["tiempo_estimado"] ?? null,
            "vigencia_desde" => $_POST["vigencia_desde"],
            "vigencia_hasta" => $_POST["vigencia_hasta"] ?? null,
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloTarifa::mdlIngresarTarifa($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR TARIFAS
    =============================================*/
    static public function ctrMostrarTarifas($item, $valor)
    {
        $tabla = "tarifas_envio";
        $respuesta = ModeloTarifa::mdlMostrarTarifas($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR TARIFA (Obtener datos)
    =============================================*/
    static public function ctrEditarTarifa()
    {
        $tabla = "tarifas_envio";
        $item = "id_tarifa";
        $valor = $_POST["id_tarifa"];
        $respuesta = ModeloTarifa::mdlMostrarTarifas($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR TARIFA
    =============================================*/
    static public function ctrActualizarTarifa()
    {
        $tabla = "tarifas_envio";
        $datos = array(
            "id_tarifa" => $_POST["id_tarifa"],
            "id_sucursal_origen" => $_POST["id_sucursal_origen"],
            "id_sucursal_destino" => $_POST["id_sucursal_destino"],
            "id_tipo_encomienda" => $_POST["id_tipo_encomienda"],
            "rango_peso_min" => $_POST["rango_peso_min"],
            "rango_peso_max" => $_POST["rango_peso_max"],
            "costo_base" => $_POST["costo_base"],
            "costo_kg_extra" => $_POST["costo_kg_extra"] ?? 0.00,
            "tiempo_estimado" => $_POST["tiempo_estimado"] ?? null,
            "vigencia_desde" => $_POST["vigencia_desde"],
            "vigencia_hasta" => $_POST["vigencia_hasta"] ?? null,
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTarifa::mdlActualizarTarifa($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE TARIFA
    =============================================*/
    static public function ctrCambiarEstadoTarifa()
    {
        $tabla = "tarifas_envio";
        $datos = array(
            "id_tarifa" => $_POST["id_tarifa"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTarifa::mdlCambiarEstadoTarifa($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR TARIFA
    =============================================*/
    static public function ctrBorrarTarifa()
    {
        $tabla = "tarifas_envio";
        $datos = $_POST["id_tarifa"];
        $respuesta = ModeloTarifa::mdlBorrarTarifa($tabla, $datos);
        echo $respuesta;
    }
}