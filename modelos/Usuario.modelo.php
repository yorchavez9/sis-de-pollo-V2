<?php
require_once "conexion.php";

class ModeloUsuarios
{
    /* ==============================================
    MOSTRAR USUARIO PARA INICIAR SESION
    ============================================== */
    static public function mdlMostrarLoginUsuario($tabla, $item, $valor){
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
        $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
        $stmt->close();
        $stmt = null;
    }
    
    /* ==============================================
    ACTUALIZAR USUARIO (ÚLTIMO LOGIN)
    ============================================== */
    static public function mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2){
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
        $stmt->bindParam(":".$item1, $valor1, PDO::PARAM_STR);
        $stmt->bindParam(":".$item2, $valor2, PDO::PARAM_STR);
        
        if($stmt->execute()){
            return "ok";
        } else {
            return "error";
        }
        
        $stmt->close();
        $stmt = null;
    }
    
    /* ==============================================
    OBTENER ROLES DEL USUARIO
    ============================================== */
    static public function mdlObtenerRolesUsuario($idUsuario){
        $stmt = Conexion::conectar()->prepare("
            SELECT r.* FROM roles r
            JOIN usuario_roles ur ON r.id_rol = ur.id_rol
            WHERE ur.id_usuario = :id_usuario
            AND r.estado = 1
        ");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
        $stmt->close();
        $stmt = null;
    }
    
    /* ==============================================
    OBTENER PERMISOS DEL USUARIO
    ============================================== */
    static public function mdlObtenerPermisosUsuario($idUsuario){
        $stmt = Conexion::conectar()->prepare("
            SELECT 
                m.nombre as modulo, 
                m.ruta, 
                m.icono,
                a.nombre as accion
            FROM permisos p
            JOIN usuario_roles ur ON p.id_rol = ur.id_rol
            JOIN modulos m ON p.id_modulo = m.id_modulo
            JOIN acciones a ON p.id_accion = a.id_accion
            WHERE ur.id_usuario = :id_usuario
            AND m.estado = 1
            AND a.estado = 1
            ORDER BY m.orden ASC
        ");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $permisos = array();
        $resultados = $stmt->fetchAll();
        
        foreach($resultados as $resultado){
            $modulo = $resultado["modulo"];
            $accion = $resultado["accion"];
            
            if(!isset($permisos[$modulo])){
                $permisos[$modulo] = array(
                    "ruta" => $resultado["ruta"],
                    "icono" => $resultado["icono"],
                    "acciones" => array()
                );
            }
            
            $permisos[$modulo]["acciones"][] = $accion;
        }
        
        return $permisos;
        $stmt->close();
        $stmt = null;
    }
}