<?php

require_once "../modelos/Proveedor.modelo.php";

class ControladorProveedor
{
    /*=============================================
    REGISTRO DE PROVEEDOR
    =============================================*/
    static public function ctrCrearProveedor()
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
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloProveedor::mdlIngresarProveedor($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR PROVEEDORES
    =============================================*/
    static public function ctrMostrarProveedores($item, $valor)
    {
        $tabla = "personas";
        $respuesta = ModeloProveedor::mdlMostrarProveedores($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR PROVEEDOR (Obtener datos)
    =============================================*/
    static public function ctrEditarProveedor()
    {
        $tabla = "personas";
        $item = "id_persona";
        $valor = $_POST["id_persona"];
        $respuesta = ModeloProveedor::mdlMostrarProveedores($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR PROVEEDOR
    =============================================*/
    static public function ctrActualizarProveedor()
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
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloProveedor::mdlActualizarProveedor($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE PROVEEDOR
    =============================================*/
    static public function ctrCambiarEstadoProveedor()
    {
        $tabla = "personas";
        $datos = array(
            "id_persona" => $_POST["id_persona"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloProveedor::mdlCambiarEstadoProveedor($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR PROVEEDOR
    =============================================*/
    static public function ctrBorrarProveedor()
    {
        $tabla = "personas";
        $datos = $_POST["id_persona"];
        $respuesta = ModeloProveedor::mdlBorrarProveedor($tabla, $datos);
        echo $respuesta;
    }
}