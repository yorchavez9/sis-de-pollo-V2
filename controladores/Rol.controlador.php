<?php
class ControladorRoles
{
    /* =============================================
    MOSTRAR ROLES
    ============================================= */
    static public function ctrMostrarRoles($item, $valor)
    {
        $tabla = "roles";
        $respuesta = ModeloRoles::mdlMostrarRoles($tabla, $item, $valor);
        echo json_encode($respuesta);
    }

    /* =============================================
    REGISTRAR ROL
    ============================================= */
    static public function ctrCrearRol()
    {
        // Validar campos obligatorios
        $camposRequeridos = ["nombre", "nivel_acceso"];
        foreach ($camposRequeridos as $campo) {
            if (!isset($_POST[$campo])) {
                echo json_encode(["status" => false, "message" => "El campo $campo es requerido"]);
                return;
            }
        }

        $tabla = "roles";
        
        // Verificar si el nombre de rol ya existe
        $respuesta = ModeloRoles::mdlMostrarRoles($tabla, "nombre", $_POST["nombre"]);
        
        if ($respuesta["status"] === false) {
            echo json_encode($respuesta);
            return;
        }
        
        if ($respuesta["exists"]) {
            echo json_encode(["status" => false, "message" => "El nombre de rol ya está en uso"]);
            return;
        }

        // Crear rol
        $datos = [
            "nombre" => htmlspecialchars(trim($_POST["nombre"])),
            "descripcion" => isset($_POST["descripcion"]) ? htmlspecialchars(trim($_POST["descripcion"])) : null,
            "nivel_acceso" => (int)$_POST["nivel_acceso"],
            "estado" => isset($_POST["estado"]) ? (int)$_POST["estado"] : 1
        ];

        $respuesta = ModeloRoles::mdlIngresarRol($tabla, $datos);
        echo $respuesta;
    }

    /* =============================================
    ACTUALIZAR ROL
    ============================================= */
    static public function ctrActualizarRol()
    {
        if (!isset($_POST["id_rol"])) {
            echo json_encode(["status" => false, "message" => "ID de rol requerido"]);
            return;
        }

        $tabla = "roles";
        $idRol = (int)$_POST["id_rol"];

        // Verificar rol existente si se cambia el nombre
        if (isset($_POST["nombre"])) {
            $nombre = trim($_POST["nombre"]);
            $respuesta = ModeloRoles::mdlMostrarRoles($tabla, "nombre", $nombre);
            
            if ($respuesta["status"] === false) {
                echo json_encode($respuesta);
                return;
            }
            
            // Verificar si el nombre ya existe en otro rol
            if ($respuesta["exists"] && $respuesta["data"]["id_rol"] != $idRol) {
                echo json_encode(["status" => false, "message" => "El nombre de rol ya está en uso"]);
                return;
            }
        }

        // Preparar datos
        $datos = [
            "id_rol" => $idRol,
            "nombre" => isset($_POST["nombre"]) ? htmlspecialchars(trim($_POST["nombre"])) : null,
            "descripcion" => isset($_POST["descripcion"]) ? htmlspecialchars(trim($_POST["descripcion"])) : null,
            "nivel_acceso" => isset($_POST["nivel_acceso"]) ? (int)$_POST["nivel_acceso"] : null,
            "estado" => isset($_POST["estado"]) ? (int)$_POST["estado"] : null
        ];

        // Eliminar campos nulos para no actualizarlos
        $datos = array_filter($datos, function($value) {
            return $value !== null;
        });

        $respuesta = ModeloRoles::mdlActualizarRol($tabla, $datos);
        echo $respuesta;
    }

    /* =============================================
    CAMBIAR ESTADO DE ROL
    ============================================= */
    static public function ctrCambiarEstadoRol()
    {
        if (!isset($_POST["id_rol"], $_POST["estado"])) {
            echo json_encode(["status" => false, "message" => "Datos incompletos"]);
            return;
        }

        $tabla = "roles";
        $datos = [
            "id_rol" => (int)$_POST["id_rol"],
            "estado" => (int)$_POST["estado"]
        ];

        $respuesta = ModeloRoles::mdlCambiarEstadoRol($tabla, $datos);
        echo $respuesta;
    }

    /* =============================================
    ELIMINAR ROL
    ============================================= */
    static public function ctrBorrarRol()
    {
        if (!isset($_POST["id_rol"])) {
            echo json_encode(["status" => false, "message" => "ID de rol requerido"]);
            return;
        }

        $tabla = "roles";
        $idRol = (int)$_POST["id_rol"];

        $respuesta = ModeloRoles::mdlBorrarRol($tabla, $idRol);
        echo $respuesta;
    }
}