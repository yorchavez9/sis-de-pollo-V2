<?php
class ControladorTipoEncomienda
{
    /*=============================================
    REGISTRO DE TIPO ENCOMIENDA
    =============================================*/
    static public function ctrCrearTipoEncomienda()
    {
        $tabla = "tipo_encomiendas";
        $datos = array(
            "nombre" => $_POST["nombre"],
            "descripcion" => $_POST["descripcion"] ?? null,
            "requiere_confirmacion" => $_POST["requiere_confirmacion"],
            "prioridad" => $_POST["prioridad"],
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloTipoEncomienda::mdlIngresarTipoEncomienda($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR TIPOS DE ENCOMIENDA
    =============================================*/
    static public function ctrMostrarTipoEncomiendas($item, $valor)
    {
        $tabla = "tipo_encomiendas";
        $respuesta = ModeloTipoEncomienda::mdlMostrarTipoEncomiendas($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR TIPO ENCOMIENDA (Obtener datos)
    =============================================*/
    static public function ctrEditarTipoEncomienda()
    {
        $tabla = "tipo_encomiendas";
        $item = "id_tipo_encomienda";
        $valor = $_POST["id_tipo_encomienda"];
        $respuesta = ModeloTipoEncomienda::mdlMostrarTipoEncomiendas($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR TIPO ENCOMIENDA
    =============================================*/
    static public function ctrActualizarTipoEncomienda()
    {
        $tabla = "tipo_encomiendas";
        $datos = array(
            "id_tipo_encomienda" => $_POST["id_tipo_encomienda"],
            "nombre" => $_POST["nombre"],
            "descripcion" => $_POST["descripcion"] ?? null,
            "requiere_confirmacion" => $_POST["requiere_confirmacion"],
            "prioridad" => $_POST["prioridad"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTipoEncomienda::mdlActualizarTipoEncomienda($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE TIPO ENCOMIENDA
    =============================================*/
    static public function ctrCambiarEstadoTipoEncomienda()
    {
        $tabla = "tipo_encomiendas";
        $datos = array(
            "id_tipo_encomienda" => $_POST["id_tipo_encomienda"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloTipoEncomienda::mdlCambiarEstadoTipoEncomienda($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR TIPO ENCOMIENDA
    =============================================*/
    static public function ctrBorrarTipoEncomienda()
    {
        $tabla = "tipo_encomiendas";
        $datos = $_POST["id_tipo_encomienda"];
        $respuesta = ModeloTipoEncomienda::mdlBorrarTipoEncomienda($tabla, $datos);
        echo $respuesta;
    }
}