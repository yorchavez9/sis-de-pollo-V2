<?php

class ControladorTransportista
{
    /*=============================================
    REGISTRO DE TRANSPORTISTA
    =============================================*/
    static public function ctrCrearTransportista()
    {
        $tabla = "personas";
        $datos = array(
            "tipo_persona" => $_POST["tipo_persona"],
            "id_tipo_documento" => $_POST["id_tipo_documento"],
            "numero_documento" => $_POST["numero_documento"],
            "nombre" => $_POST["nombre"],
            "apellidos" => $_POST["apellidos"] ?? null,
            "telefono" => $_POST["telefono"] ?? null,
            "celular" => $_POST["celular"] ?? null,
            "email" => $_POST["email"] ?? null,
            "direccion" => $_POST["direccion"] ?? null,
            "ciudad" => $_POST["ciudad"] ?? null,
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloTransportista::mdlIngresarTransportista($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR TRANSPORTISTAS
    =============================================*/
    static public function ctrMostrarTransportistas($item, $valor)
    {
        $tabla = "personas";
        $respuesta = ModeloTransportista::mdlMostrarTransportistas($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR TRANSPORTISTA (Obtener datos)
    =============================================*/
    static public function ctrEditarTransportista()
    {
        $tabla = "personas";
        $item = "id_persona";
        $valor = $_POST["id_persona"];
        $respuesta = ModeloTransportista::mdlMostrarTransportistas($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR TRANSPORTISTA
    =============================================*/
    static public function ctrActualizarTransportista()
    {
        $tabla = "personas";
        $datos = array(
            "id_persona" => $_POST["id_persona"],
            "tipo_persona" => $_POST["tipo_persona"],
            "id_tipo_documento" => $_POST["id_tipo_documento"],
            "numero_documento" => $_POST["numero_documento"],
            "nombre" => $_POST["nombre"],
            "apellidos" => $_POST["apellidos"] ?? null,
            "telefono" => $_POST["telefono"] ?? null,
            "celular" => $_POST["celular"] ?? null,
            "email" => $_POST["email"] ?? null,
            "direccion" => $_POST["direccion"] ?? null,
            "ciudad" => $_POST["ciudad"] ?? null,
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
        $tabla = "personas";
        $datos = array(
            "id_persona" => $_POST["id_persona"],
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
        $tabla = "personas";
        $datos = $_POST["id_persona"];
        $respuesta = ModeloTransportista::mdlBorrarTransportista($tabla, $datos);
        echo $respuesta;
    }
}