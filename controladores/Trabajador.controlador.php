<?php

require_once "../modelos/Trabajador.modelo.php";


class ControladorTrabajador
{
    /*=============================================
    REGISTRO DE TRABAJADOR
    =============================================*/
    static public function ctrCrearTrabajador()
    {
        $tabla = "personas";
        $datos = array(
            "tipo_persona" => $_POST["tipo_persona"],
            "id_tipo_documento" => $_POST["id_tipo_documento"],
            "numero_documento" => $_POST["numero_documento"],
            "nombre" => $_POST["nombre"],
            "apellidos" => $_POST["apellidos"],
            "telefono" => $_POST["telefono"] ?? null,
            "celular" => $_POST["celular"] ?? null,
            "email" => $_POST["email"] ?? null,
            "direccion" => $_POST["direccion"] ?? null,
            "ciudad" => $_POST["ciudad"] ?? null,
            "fecha_nacimiento" => $_POST["fecha_nacimiento"] ?? null,
            "cargo" => $_POST["cargo"] ?? null,
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloTrabajador::mdlIngresarTrabajador($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR TRABAJADORES
    =============================================*/
    static public function ctrMostrarTrabajadores($item, $valor)
    {
        $tabla = "personas";
        $respuesta = ModeloTrabajador::mdlMostrarTrabajadores($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR TRABAJADOR (Obtener datos)
    =============================================*/
    static public function ctrEditarTrabajador()
    {
        $tabla = "personas";
        $item = "id_persona";
        $valor = $_POST["id_persona"];
        $respuesta = ModeloTrabajador::mdlMostrarTrabajadores($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR TRABAJADOR
    =============================================*/
    static public function ctrActualizarTrabajador()
    {
        $tabla = "personas";
        $datos = array(
            "id_persona" => $_POST["id_persona"],
            "tipo_persona" => $_POST["tipo_persona"],
            "id_tipo_documento" => $_POST["id_tipo_documento"],
            "numero_documento" => $_POST["numero_documento"],
            "nombre" => $_POST["nombre"],
            "apellidos" => $_POST["apellidos"],
            "telefono" => $_POST["telefono"] ?? null,
            "celular" => $_POST["celular"] ?? null,
            "email" => $_POST["email"] ?? null,
            "direccion" => $_POST["direccion"] ?? null,
            "ciudad" => $_POST["ciudad"] ?? null,
            "fecha_nacimiento" => $_POST["fecha_nacimiento"] ?? null,
            "cargo" => $_POST["cargo"] ?? null,
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTrabajador::mdlActualizarTrabajador($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE TRABAJADOR
    =============================================*/
    static public function ctrCambiarEstadoTrabajador()
    {
        $tabla = "personas";
        $datos = array(
            "id_persona" => $_POST["id_persona"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTrabajador::mdlCambiarEstadoTrabajador($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR TRABAJADOR
    =============================================*/
    static public function ctrBorrarTrabajador()
    {
        $tabla = "personas";
        $datos = $_POST["id_persona"];
        $respuesta = ModeloTrabajador::mdlBorrarTrabajador($tabla, $datos);
        echo $respuesta;
    }
}