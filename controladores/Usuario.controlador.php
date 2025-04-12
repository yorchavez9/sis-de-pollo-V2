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
}
