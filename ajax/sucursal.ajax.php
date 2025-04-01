<?php

require_once "../modelos/Sucursal.modelo.php";

class ControladorSucursal
{
    /*=============================================
    REGISTRO DE SUCURSAL
    =============================================*/
    static public function ctrCrearSucursal()
    {
        $tabla = "sucursales";
        $datos = array(
            "codigo" => $_POST["codigo"],
            "nombre" => $_POST["nombre"],
            "direccion" => $_POST["direccion"],
            "ciudad" => $_POST["ciudad"],
            "telefono" => $_POST["telefono"],
            "responsable" => $_POST["responsable"],
            "es_principal" => isset($_POST["es_principal"]) ? $_POST["es_principal"] : 0,
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );
        $respuesta = ModeloSucursal::mdlIngresarSucursal($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR SUCURSALES
    =============================================*/
    static public function ctrMostrarSucursales($item, $valor)
    {
        $tabla = "sucursales";
        $respuesta = ModeloSucursal::mdlMostrarSucursales($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR SUCURSAL
    =============================================*/
    static public function ctrEditarSucursal()
    {
        $tabla = "sucursales";
        $datos = array(
            "id_sucursal" => $_POST["edit_id_sucursal"],
            "codigo" => $_POST["edit_codigo"],
            "nombre" => $_POST["edit_nombre"],
            "direccion" => $_POST["edit_direccion"],
            "ciudad" => $_POST["edit_ciudad"],
            "telefono" => $_POST["edit_telefono"],
            "responsable" => $_POST["edit_responsable"],
            "es_principal" => isset($_POST["edit_es_principal"]) ? $_POST["edit_es_principal"] : 0,
            "estado" => isset($_POST["edit_estado"]) ? $_POST["edit_estado"] : 1
        );
        $respuesta = ModeloSucursal::mdlEditarSucursal($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    EDITAR SUCURSAL
    =============================================*/
    static public function ctrActualizarSucursal()
    {
        $tabla = "sucursales";
        $datos = array(
            "id_sucursal" => $_POST["id_sucursal"],
            "codigo" => $_POST["codigo"],
            "nombre" => $_POST["nombre"],
            "direccion" => $_POST["direccion"],
            "ciudad" => $_POST["ciudad"],
            "telefono" => $_POST["telefono"],
            "responsable" => $_POST["responsable"],
            "es_principal" => isset($_POST["es_principal"]) ? $_POST["es_principal"] : 0
        );
        $respuesta = ModeloSucursal::mdlActualizarSucursal($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE SUCURSAL
    =============================================*/
    static public function ctrCambiarEstadoSucursal()
    {
        $tabla = "sucursales";
        $datos = array(
            "id_sucursal" => $_POST["id_sucursal"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloSucursal::mdlCambiarEstadoSucursal($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    BORRAR SUCURSAL
    =============================================*/
    static public function ctrBorrarSucursal()
    {
        $tabla = "sucursales";
        $datos = $_POST["id_sucursal"];
        $respuesta = ModeloSucursal::mdlBorrarSucursal($tabla, $datos);
        echo $respuesta;
    }
}

/*=============================================
AJAX REQUEST HANDLER
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                ControladorSucursal::ctrCrearSucursal();
                break;
            case 'editar':
                $item = "id_sucursal";
                $valor = $_POST["id_sucursal"];
                ControladorSucursal::ctrMostrarSucursales($item, $valor);
                break;
            case 'update':
                ControladorSucursal::ctrActualizarSucursal();
                break;
            case 'cambiarEstado':
                ControladorSucursal::ctrCambiarEstadoSucursal();
                break;
            case 'verDetalles':
                $item = "id_sucursal";
                $valor = $_POST["id_sucursal"];
                ControladorSucursal::ctrMostrarSucursales($item, $valor);
                break;
            case 'eliminar':
                ControladorSucursal::ctrBorrarSucursal();
                break;
            default:
                echo json_encode("Acción no válida");
        }
    } else {
        echo json_encode("No se especificó acción");
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    $item = null;
    $valor = null;
    ControladorSucursal::ctrMostrarSucursales($item, $valor);
}