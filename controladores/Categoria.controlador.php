<?php

require_once "../modelos/Categoria.modelo.php";


class ControladorCategoria
{
    /*=============================================
    REGISTRO DE CATEGORÍA
    =============================================*/
    static public function ctrCrearCategoria()
    {
        $tabla = "categorias";
        $datos = array(
            "nombre" => $_POST["nombre"],
            "descripcion" => $_POST["descripcion"] ?? null,
            "tipo" => $_POST["tipo"],
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloCategoria::mdlIngresarCategoria($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR CATEGORÍAS
    =============================================*/
    static public function ctrMostrarCategorias($item, $valor)
    {
        $tabla = "categorias";
        $respuesta = ModeloCategoria::mdlMostrarCategorias($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR CATEGORÍA (Obtener datos)
    =============================================*/
    static public function ctrEditarCategoria()
    {
        $tabla = "categorias";
        $item = "id_categoria";
        $valor = $_POST["id_categoria"];
        $respuesta = ModeloCategoria::mdlMostrarCategorias($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR CATEGORÍA
    =============================================*/
    static public function ctrActualizarCategoria()
    {
        $tabla = "categorias";
        $datos = array(
            "id_categoria" => $_POST["id_categoria"],
            "nombre" => $_POST["nombre"],
            "descripcion" => $_POST["descripcion"] ?? null,
            "tipo" => $_POST["tipo"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloCategoria::mdlActualizarCategoria($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE CATEGORÍA
    =============================================*/
    static public function ctrCambiarEstadoCategoria()
    {
        $tabla = "categorias";
        $datos = array(
            "id_categoria" => $_POST["id_categoria"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloCategoria::mdlCambiarEstadoCategoria($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR CATEGORÍA
    =============================================*/
    static public function ctrBorrarCategoria()
    {
        $tabla = "categorias";
        $datos = $_POST["id_categoria"];
        $respuesta = ModeloCategoria::mdlBorrarCategoria($tabla, $datos);
        echo $respuesta;
    }
}