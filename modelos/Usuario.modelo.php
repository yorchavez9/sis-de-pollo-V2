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
}