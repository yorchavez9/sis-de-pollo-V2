<?php

include_once "../modelos/Usuario.modelo.php";

class ControladorUsuarios{
    /* ============================================
    LOGIN USUARIO
    ============================================ */
    static public function ctrLoginUsuario(){
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if(isset($_POST["ingUsuario"])){
            if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"]) && 
               preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])){
                
                $tabla = "usuarios";
                $item = "usuario";
                $valor = $_POST["ingUsuario"];
                
                $respuesta = ModeloUsuarios::mdlMostrarLoginUsuario($tabla, $item, $valor);
                
                if($respuesta && $respuesta["usuario"] == $_POST["ingUsuario"]){
                    // Verificar contraseña
                    if(password_verify($_POST["ingPassword"], $respuesta["contrasena"])){
                        
                        if($respuesta["estado"] == 1){
                            // Crear sesión
                            $_SESSION["iniciarSesion"] = "ok";
                            $_SESSION["id_usuario"] = $respuesta["id_usuario"];
                            $_SESSION["nombre_usuario"] = $respuesta["nombre_usuario"];
                            $_SESSION["usuario"] = $respuesta["usuario"];
                            $_SESSION["imagen"] = $respuesta["imagen"];
                            $_SESSION["id_sucursal"] = $respuesta["id_sucursal"];
                            
                            // Registrar último login
                            $fecha = date('Y-m-d H:i:s');
                            $item1 = "ultimo_login";
                            $valor1 = $fecha;
                            $item2 = "id_usuario";
                            $valor2 = $respuesta["id_usuario"];
                            
                            ModeloUsuarios::mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2);
                            
                            // Obtener roles y permisos del usuario
                            $roles = ModeloUsuarios::mdlObtenerRolesUsuario($respuesta["id_usuario"]);
                            $permisos = ModeloUsuarios::mdlObtenerPermisosUsuario($respuesta["id_usuario"]);
                            
                            $_SESSION["roles"] = $roles;
                            $_SESSION["permisos"] = $permisos;
                            
                            // Retornar éxito para la redirección con AJAX
                            echo json_encode(array(
                                "status" => true,
                                "message" => "Inicio de sesión exitoso",
                                "redirect" => "inicio"
                            ));
                            return;
                        } else {
                            echo json_encode(array(
                                "status" => false,
                                "message" => "El usuario está desactivado"
                            ));
                            return;
                        }
                    } else {
                        echo json_encode(array(
                            "status" => false,
                            "message" => "Error al ingresar, vuelve a intentarlo"
                        ));
                        return;
                    }
                }
            }
        }
        
        // Si llega hasta aquí es porque hubo un error
        echo json_encode(array(
            "success" => false,
            "message" => "Error al ingresar, vuelve a intentarlo"
        ));
    }
}

$login = new ControladorUsuarios();
$login->ctrLoginUsuario();
