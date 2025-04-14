<?php
class ControladorPermiso
{
    /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    static public function ctrMostrarPermisos()
    {
        $respuesta = ModeloPermiso::mdlMostrarPermisos();
        echo $respuesta;
    }

    /*=============================================
    GUARDAR PERMISO
    =============================================*/
    static public function ctrGuardarPermiso()
    {
        if (isset($_POST['id_rol']) && isset($_POST['permisos'])) {
            $idRol = $_POST['id_rol'];
            $permisos = json_decode($_POST['permisos'], true);
            
            $respuesta = ModeloPermiso::mdlGuardarPermiso($idRol, $permisos);
            echo $respuesta;
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Datos incompletos"
            ]);
        }
    }

    /*=============================================
    ACTUALIZAR PERMISO
    =============================================*/
    static public function ctrActualizarPermiso()
    {
        if (isset($_POST['id_rol']) && isset($_POST['permisos'])) {
            $idRol = $_POST['id_rol'];
            $permisos = json_decode($_POST['permisos'], true);
            
            $respuesta = ModeloPermiso::mdlActualizarPermiso($idRol, $permisos);
            echo $respuesta;
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Datos incompletos"
            ]);
        }
    }

    /*=============================================
    ELIMINAR PERMISO
    =============================================*/
    static public function ctrEliminarPermiso()
    {
        if (isset($_POST['id_rol'])) {
            $idRol = $_POST['id_rol'];
            
            $respuesta = ModeloPermiso::mdlEliminarPermiso($idRol);
            echo $respuesta;
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Datos incompletos"
            ]);
        }
    }
}