<?php

require_once "../modelos/Cliente.modelo.php";

class ControladorCliente
{
    /*=============================================
    REGISTRO DE CLIENTE
    =============================================*/
    static public function ctrCrearCliente()
    {
        $tabla = "personas";
        $datos = array(
            "tipo_persona" => $_POST["tipo_persona"],
            "id_tipo_documento" => $_POST["id_tipo_documento"],
            "numero_documento" => $_POST["numero_documento"],
            "nombre" => $_POST["nombre"] ?? null,
            "apellidos" => $_POST["apellidos"] ?? null,
            "razon_social" => $_POST["razon_social"] ?? null,
            "telefono" => $_POST["telefono"] ?? null,
            "celular" => $_POST["celular"] ?? null,
            "email" => $_POST["email"] ?? null,
            "direccion" => $_POST["direccion"] ?? null,
            "ciudad" => $_POST["ciudad"] ?? null,
            "fecha_nacimiento" => $_POST["fecha_nacimiento"] ?? null,
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloCliente::mdlIngresarCliente($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR CLIENTES
    =============================================*/
    static public function ctrMostrarClientes($item, $valor)
    {
        $tabla = "personas";
        $respuesta = ModeloCliente::mdlMostrarClientes($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR CLIENTE (Obtener datos)
    =============================================*/
    static public function ctrEditarCliente()
    {
        $tabla = "personas";
        $item = "id_persona";
        $valor = $_POST["id_persona"];
        $respuesta = ModeloCliente::mdlMostrarClientes($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR CLIENTE
    =============================================*/
    static public function ctrActualizarCliente()
    {
        $tabla = "personas";
        $datos = array(
            "id_persona" => $_POST["id_persona"],
            "tipo_persona" => $_POST["tipo_persona"],
            "id_tipo_documento" => $_POST["id_tipo_documento"],
            "numero_documento" => $_POST["numero_documento"],
            "nombre" => $_POST["nombre"] ?? null,
            "apellidos" => $_POST["apellidos"] ?? null,
            "razon_social" => $_POST["razon_social"] ?? null,
            "telefono" => $_POST["telefono"] ?? null,
            "celular" => $_POST["celular"] ?? null,
            "email" => $_POST["email"] ?? null,
            "direccion" => $_POST["direccion"] ?? null,
            "ciudad" => $_POST["ciudad"] ?? null,
            "fecha_nacimiento" => $_POST["fecha_nacimiento"] ?? null,
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloCliente::mdlActualizarCliente($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE CLIENTE
    =============================================*/
    static public function ctrCambiarEstadoCliente()
    {
        $tabla = "personas";
        $datos = array(
            "id_persona" => $_POST["id_persona"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloCliente::mdlCambiarEstadoCliente($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR CLIENTE
    =============================================*/
    static public function ctrBorrarCliente()
    {
        $tabla = "personas";
        $datos = $_POST["id_persona"];
        $respuesta = ModeloCliente::mdlBorrarCliente($tabla, $datos);
        echo $respuesta;
    }
}