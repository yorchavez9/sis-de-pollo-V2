<?php

require_once "../modelos/Transporte.modelo.php";

class ControladorTransportista
{
    /*=============================================
    REGISTRO DE TRANSPORTISTA
    =============================================*/
    static public function ctrCrearTransportista()
    {
        $tabla = "transportistas";
        $datos = array(
            "id_persona" => $_POST["id_persona"],
            "tipo_vehiculo" => $_POST["tipo_vehiculo"],
            "placa_vehiculo" => $_POST["placa_vehiculo"],
            "telefono_contacto" => $_POST["telefono_contacto"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTransportista::mdlIngresarTransportista($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR TRANSPORTISTAS
    =============================================*/
    static public function ctrMostrarTransportistas()
    {
        $tabla = "transportistas";
        $respuesta = ModeloTransportista::mdlMostrarTransportistas($tabla);
        echo $respuesta;
    }

    /*=============================================
    EDITAR TRANSPORTISTA (Obtener datos)
    =============================================*/
    static public function ctrEditarTransportista()
    {
        $tabla = "transportistas";
        $item = "id_transportista";
        $valor = $_POST["id_transportista"];
        $respuesta = ModeloTransportista::mdlMostrarTransportistas($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR TRANSPORTISTA
    =============================================*/
    static public function ctrActualizarTransportista()
    {
        $tabla = "transportistas";
        $datos = array(
            "id_transportista" => $_POST["id_transportista"],
            "tipo_vehiculo" => $_POST["tipo_vehiculo"],
            "placa_vehiculo" => $_POST["placa_vehiculo"],
            "telefono_contacto" => $_POST["telefono_contacto"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTransportista::mdlActualizarTransportista($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE TRANSPORTISTA
    =============================================*/
    static public function ctrCambiarEstadoTransportista()
    {
        $tabla = "transportistas";
        $datos = array(
            "id_transportista" => $_POST["id_transportista"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTransportista::mdlCambiarEstadoTransportista($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR TRANSPORTISTA
    =============================================*/
    static public function ctrBorrarTransportista()
    {
        $tabla = "transportistas";
        $datos = $_POST["id_transportista"];
        $respuesta = ModeloTransportista::mdlBorrarTransportista($tabla, $datos);
        echo $respuesta;
    }
}