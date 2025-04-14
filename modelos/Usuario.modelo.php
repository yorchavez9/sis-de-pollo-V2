<?php
require_once "conexion.php";

class ModeloUsuarios
{
    /* ==============================================
    MOSTRAR USUARIO PARA INICIAR SESION
    ============================================== */
    static public function mdlMostrarLoginUsuario($tabla, $item, $valor)
    {
        $stmt = Conexion::conectar()->prepare("
            SELECT u.*, s.nombre as nombre_sucursal 
            FROM $tabla u
            LEFT JOIN sucursales s ON u.id_sucursal = s.id_sucursal
            WHERE u.$item = :$item
        ");
        $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt->closeCursor();
        $stmt = null;
        
        return $usuario;
    }
    
    /* ==============================================
    ACTUALIZAR USUARIO (ÚLTIMO LOGIN)
    ============================================== */
    static public function mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
        $stmt->bindParam(":".$item1, $valor1, PDO::PARAM_STR);
        $stmt->bindParam(":".$item2, $valor2, PDO::PARAM_STR);
        
        $resultado = $stmt->execute();
        
        $stmt->closeCursor();
        $stmt = null;
        
        return $resultado ? "ok" : "error";
    }
    
    /* ==============================================
    OBTENER ROLES DEL USUARIO
    ============================================== */
    static public function mdlObtenerRolesUsuario($idUsuario)
    {
        $stmt = Conexion::conectar()->prepare("
            SELECT r.* FROM roles r
            JOIN usuario_roles ur ON r.id_rol = ur.id_rol
            WHERE ur.id_usuario = :id_usuario
            AND r.estado = 1
        ");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt->closeCursor();
        $stmt = null;
        
        return $roles;
    }
    
    /* ==============================================
    OBTENER PERMISOS DEL USUARIO (ESTRUCTURA MEJORADA)
    ============================================== */
    static public function mdlObtenerPermisosUsuario($idUsuario)
    {
        $stmt = Conexion::conectar()->prepare("
            SELECT 
                m.id_modulo,
                m.nombre as modulo, 
                m.ruta, 
                m.icono,
                m.orden,
                a.id_accion,
                a.nombre as accion,
                a.descripcion as descripcion_accion
            FROM permisos p
            JOIN usuario_roles ur ON p.id_rol = ur.id_rol
            JOIN modulos m ON p.id_modulo = m.id_modulo
            JOIN acciones a ON p.id_accion = a.id_accion
            WHERE ur.id_usuario = :id_usuario
            AND m.estado = 1
            AND a.estado = 1
            ORDER BY m.orden ASC, a.id_accion ASC
        ");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $permisos = array();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($resultados as $resultado){
            $moduloId = $resultado["id_modulo"];
            
            if(!isset($permisos[$moduloId])){
                $permisos[$moduloId] = array(
                    "nombre" => $resultado["modulo"],
                    "ruta" => $resultado["ruta"],
                    "icono" => $resultado["icono"],
                    "orden" => $resultado["orden"],
                    "acciones" => array()
                );
            }
            
            $permisos[$moduloId]["acciones"][] = array(
                "id" => $resultado["id_accion"],
                "nombre" => $resultado["accion"],
                "descripcion" => $resultado["descripcion_accion"]
            );
        }
        
        $stmt->closeCursor();
        $stmt = null;
        
        return $permisos;
    }
    
    /* ==============================================
    OBTENER DATOS COMPLETOS DEL USUARIO PARA SESIÓN
    ============================================== */
    static public function mdlObtenerDatosSesion($idUsuario)
    {
        // Obtener datos básicos del usuario
        $usuario = self::mdlMostrarLoginUsuario("usuarios", "id_usuario", $idUsuario);
        
        if(!$usuario) {
            return false;
        }
        
        // Obtener roles y permisos
        $roles = self::mdlObtenerRolesUsuario($idUsuario);
        $permisos = self::mdlObtenerPermisosUsuario($idUsuario);
        
        // Estructurar datos para la sesión
        $datosSesion = array(
            "id_usuario" => $usuario["id_usuario"],
            "nombre_usuario" => $usuario["nombre_usuario"],
            "usuario" => $usuario["usuario"],
            "imagen" => $usuario["imagen"],
            "id_sucursal" => $usuario["id_sucursal"],
            "nombre_sucursal" => $usuario["nombre_sucursal"] ?? null,
            "roles" => $roles,
            "permisos" => $permisos,
            "ultimo_login" => $usuario["ultimo_login"]
        );
        
        return $datosSesion;
    }


    /*=============================================
    MOSTRAR USUARIOS
    =============================================*/
    static public function mdlMostrarUsuarios($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT u.*, s.nombre as nombre_sucursal, p.nombre, p.apellidos, 
                    CONCAT(p.nombre, ' ', p.apellidos) as nombre_persona 
                    FROM $tabla u 
                    LEFT JOIN sucursales s ON u.id_sucursal = s.id_sucursal 
                    LEFT JOIN personas p ON u.id_persona = p.id_persona 
                    WHERE u.$item = :$item"
                );
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT u.*, s.nombre as nombre_sucursal, p.nombre, p.apellidos, 
                    CONCAT(p.nombre, ' ', p.apellidos) as nombre_persona 
                    FROM $tabla u 
                    LEFT JOIN sucursales s ON u.id_sucursal = s.id_sucursal 
                    LEFT JOIN personas p ON u.id_persona = p.id_persona 
                    ORDER BY u.id_usuario DESC"
                );
            }
            
            $stmt->execute();
            return json_encode([
                "status" => true,
                "data" => $item != null ? $stmt->fetch() : $stmt->fetchAll()
            ]);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    VERIFICAR USUARIO EXISTENTE (para actualización)
    =============================================*/
    static public function mdlVerificarUsuarioExistente($tabla, $item, $valor, $excluirId)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM $tabla 
                WHERE $item = :$item AND id_usuario != :excluirId"
            );
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->bindParam(":excluirId", $excluirId, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();
            return $resultado['total'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /*=============================================
    REGISTRAR USUARIO
    =============================================*/
    static public function mdlIngresarUsuario($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    id_sucursal, 
                    id_persona, 
                    nombre_usuario, 
                    usuario, 
                    contrasena, 
                    imagen,
                    estado
                ) VALUES (
                    :id_sucursal, 
                    :id_persona, 
                    :nombre_usuario, 
                    :usuario, 
                    :contrasena, 
                    :imagen,
                    :estado
                )"
            );

            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
            $stmt->bindParam(":nombre_usuario", $datos["nombre_usuario"], PDO::PARAM_STR);
            $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
            $stmt->bindParam(":contrasena", $datos["contrasena"], PDO::PARAM_STR);
            $stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Usuario registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar el usuario"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ACTUALIZAR USUARIO
    =============================================*/
    static public function mdlActualizarUsuarioU($tabla, $datos)
    {
        try {
            // Construir la consulta dinámicamente según los campos proporcionados
            $sql = "UPDATE $tabla SET 
                id_sucursal = :id_sucursal,
                id_persona = :id_persona,
                nombre_usuario = :nombre_usuario,
                usuario = :usuario";
            
            // Agregar contraseña solo si se proporcionó
            if (!empty($datos["contrasena"])) {
                $sql .= ", contrasena = :contrasena";
            }
            
            // Agregar imagen solo si se proporcionó
            if (!empty($datos["imagen"])) {
                $sql .= ", imagen = :imagen";
            }
            
            $sql .= ", estado = :estado
                WHERE id_usuario = :id_usuario";
            
            $stmt = Conexion::conectar()->prepare($sql);

            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
            $stmt->bindParam(":nombre_usuario", $datos["nombre_usuario"], PDO::PARAM_STR);
            $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
            
            // Bind de contraseña solo si se proporcionó
            if (!empty($datos["contrasena"])) {
                $stmt->bindParam(":contrasena", $datos["contrasena"], PDO::PARAM_STR);
            }
            
            // Bind de imagen solo si se proporcionó
            if (!empty($datos["imagen"])) {
                $stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
            }
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Usuario actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el usuario"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    CAMBIAR ESTADO DE USUARIO
    =============================================*/
    static public function mdlCambiarEstadoUsuario($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_usuario = :id_usuario"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado del usuario actualizado"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al cambiar el estado"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ELIMINAR USUARIO
    =============================================*/
    static public function mdlBorrarUsuario($tabla, $idUsuario)
    {
        try {
            // Obtener información del usuario para eliminar su imagen
            $usuario = json_decode(self::mdlMostrarUsuarios($tabla, "id_usuario", $idUsuario), true);
            
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_usuario = :id_usuario"
            );
            $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Eliminar imagen si existe
                if ($usuario["data"]["imagen"]) {
                    $rutaImagen = "../vistas/assets/img/usuarios/" . $usuario["data"]["imagen"];
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }
                
                return json_encode([
                    "status" => true,
                    "message" => "Usuario eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el usuario"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }




}