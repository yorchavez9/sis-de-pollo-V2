<?php
class ControladorUsuarios
{
    /* ============================================
    LOGIN USUARIO
    ============================================ */
    static public function ctrLoginUsuario()
    {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_POST["ingUsuario"])) {
            if (
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])
            ) {

                $tabla = "usuarios";
                $item = "usuario";
                $valor = $_POST["ingUsuario"];

                $respuesta = ModeloUsuarios::mdlMostrarLoginUsuario($tabla, $item, $valor);

                if ($respuesta && $respuesta["usuario"] == $_POST["ingUsuario"]) {
                    // Verificar contraseña
                    if (password_verify($_POST["ingPassword"], $respuesta["contrasena"])) {

                        // Después de validar credenciales
                        if ($respuesta["estado"] == 1) {
                            // Crear sesión
                            $_SESSION["iniciarSesion"] = "ok";

                            // Obtener todos los datos de sesión
                            $datosSesion = ModeloUsuarios::mdlObtenerDatosSesion($respuesta["id_usuario"]);

                            // Asignar a la sesión
                            $_SESSION["usuario"] = $datosSesion;

                            // Actualizar último login
                            $fecha = date('Y-m-d H:i:s');
                            ModeloUsuarios::mdlActualizarUsuario("usuarios", "ultimo_login", $fecha, "id_usuario", $respuesta["id_usuario"]);

                            // Retornar éxito para la redirección con AJAX
                            echo json_encode(array(
                                "status" => true,
                                "message" => "Inicio de sesión exitoso",
                                "redirect" => "inicio",
                                "datosUsuario" => $datosSesion
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



    /*=============================================
    REGISTRO DE USUARIO
    =============================================*/
    static public function ctrCrearUsuario()
    {
        $tabla = "usuarios";
        
        // Verificar si el nombre de usuario ya existe
        $item = "usuario";
        $valor = $_POST["usuario"];
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        
        if ($respuesta) {
            echo json_encode(["status" => false, "message" => "El nombre de usuario ya está en uso"]);
            return;
        }
        
        // Encriptar contraseña
        $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
        
        // Procesar imagen si se subió
        $imagen = null;
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $directorio = "../vistas/assets/img/usuarios/";
            $nombreArchivo = time() . "_" . $_FILES["imagen"]["name"];
            $rutaTemp = $_FILES["imagen"]["tmp_name"];
            
            if (move_uploaded_file($rutaTemp, $directorio . $nombreArchivo)) {
                $imagen = $nombreArchivo;
            }
        }
        
        $datos = array(
            "id_sucursal" => $_POST["id_sucursal"],
            "id_persona" => $_POST["id_persona"],
            "nombre_usuario" => $_POST["nombre_usuario"],
            "usuario" => $_POST["usuario"],
            "contrasena" => $contrasena,
            "imagen" => $imagen,
            "estado" => 1 // Por defecto activo
        );
        
        $respuesta = ModeloUsuarios::mdlIngresarUsuario($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR USUARIOS
    =============================================*/
    static public function ctrMostrarUsuarios($item, $valor)
    {
        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR USUARIO (Obtener datos)
    =============================================*/
    static public function ctrEditarUsuario()
    {
        $tabla = "usuarios";
        $item = "id_usuario";
        $valor = $_POST["id_usuario"];
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR USUARIO
    =============================================*/
    static public function ctrActualizarUsuario()
    {
        $tabla = "usuarios";
        
        // Verificar si se cambió el nombre de usuario y si ya existe
        if (isset($_POST["usuario"])) {
            $item = "usuario";
            $valor = $_POST["usuario"];
            $excluirId = $_POST["id_usuario"];
            $respuesta = ModeloUsuarios::mdlVerificarUsuarioExistente($tabla, $item, $valor, $excluirId);
            
            if ($respuesta) {
                echo json_encode(["status" => false, "message" => "El nombre de usuario ya está en uso"]);
                return;
            }
        }
        
        // Procesar contraseña si se proporcionó
        $contrasena = null;
        if (!empty($_POST["contrasena"])) {
            $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);
        }
        
        // Procesar imagen si se subió
        $imagen = null;
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $directorio = "../vistas/assets/img/usuarios/";
            $nombreArchivo = time() . "_" . $_FILES["imagen"]["name"];
            $rutaTemp = $_FILES["imagen"]["tmp_name"];
            
            if (move_uploaded_file($rutaTemp, $directorio . $nombreArchivo)) {
                $imagen = $nombreArchivo;
                
                // Eliminar imagen anterior si existe
                $usuarioActual = json_decode(ModeloUsuarios::mdlMostrarUsuarios($tabla, "id_usuario", $_POST["id_usuario"]), true);
                if ($usuarioActual["data"]["imagen"] && file_exists($directorio . $usuarioActual["data"]["imagen"])) {
                    unlink($directorio . $usuarioActual["data"]["imagen"]);
                }
            }
        }
        
        $datos = array(
            "id_usuario" => $_POST["id_usuario"],
            "id_sucursal" => $_POST["id_sucursal"],
            "id_persona" => $_POST["id_persona"],
            "nombre_usuario" => $_POST["nombre_usuario"],
            "usuario" => $_POST["usuario"],
            "contrasena" => $contrasena,
            "imagen" => $imagen,
            "estado" => $_POST["estado"]
        );
        
        $respuesta = ModeloUsuarios::mdlActualizarUsuarioU($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE USUARIO
    =============================================*/
    static public function ctrCambiarEstadoUsuario()
    {
        $tabla = "usuarios";
        $datos = array(
            "id_usuario" => $_POST["id_usuario"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloUsuarios::mdlCambiarEstadoUsuario($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR USUARIO
    =============================================*/
    static public function ctrBorrarUsuario()
    {
        $tabla = "usuarios";
        $datos = $_POST["id_usuario"];
        $respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $datos);
        echo $respuesta;
    }

}
