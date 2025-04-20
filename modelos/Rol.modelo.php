<?php
require_once "Conexion.php";

class ModeloRoles
{
    /* =============================================
    MOSTRAR ROLES
    ============================================= */
    static public function mdlMostrarRoles($tabla, $item, $valor)
    {
        try {
            $where = ($item !== null) ? "WHERE $item = :$item" : "";
            
            $stmt = Conexion::conectar()->prepare("
                SELECT * FROM $tabla 
                $where
                ORDER BY nivel_acceso ASC
            ");
            
            if ($item !== null) {
                $stmt->bindParam(":".$item, $valor);
            }
            
            $stmt->execute();
            
            $result = ($item !== null) ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                "status" => true,
                "data" => $result,
                "exists" => ($item !== null) ? ($result !== false) : null
            ];
            
        } catch (PDOException $e) {
            error_log("Error en mdlMostrarRoles: " . $e->getMessage());
            return [
                "status" => false,
                "message" => "Error al obtener los roles"
            ];
        } finally {
            $stmt = null;
        }
    }

    /* =============================================
    REGISTRAR ROL
    ============================================= */
    static public function mdlIngresarRol($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("
                INSERT INTO $tabla(
                    nombre, descripcion, nivel_acceso, estado
                ) VALUES (
                    :nombre, :descripcion, :nivel_acceso, :estado
                )
            ");
            
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":nivel_acceso", $datos["nivel_acceso"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Rol registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            }
            
            return json_encode([
                "status" => false,
                "message" => "Error al registrar el rol"
            ]);
            
        } catch (PDOException $e) {
            error_log("Error en mdlIngresarRol: " . $e->getMessage());
            return json_encode([
                "status" => false,
                "message" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } finally {
            $stmt = null;
        }
    }

    /* =============================================
    ACTUALIZAR ROL
    ============================================= */
    static public function mdlActualizarRol($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("
                UPDATE $tabla SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    nivel_acceso = :nivel_acceso,
                    estado = :estado
                WHERE id_rol = :id_rol
            ");
            
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":nivel_acceso", $datos["nivel_acceso"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_rol", $datos["id_rol"], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Rol actualizado con éxito"
                ]);
            }
            
            return json_encode([
                "status" => false,
                "message" => "Error al actualizar el rol"
            ]);
            
        } catch (PDOException $e) {
            error_log("Error en mdlActualizarRol: " . $e->getMessage());
            return json_encode([
                "status" => false,
                "message" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } finally {
            $stmt = null;
        }
    }

    /* =============================================
    CAMBIAR ESTADO DE ROL
    ============================================= */
    static public function mdlCambiarEstadoRol($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("
                UPDATE $tabla 
                SET estado = :estado 
                WHERE id_rol = :id_rol
            ");
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_rol", $datos["id_rol"], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado del rol actualizado"
                ]);
            }
            
            return json_encode([
                "status" => false,
                "message" => "Error al cambiar el estado"
            ]);
            
        } catch (PDOException $e) {
            error_log("Error en mdlCambiarEstadoRol: " . $e->getMessage());
            return json_encode([
                "status" => false,
                "message" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } finally {
            $stmt = null;
        }
    }

    /* =============================================
    ELIMINAR ROL
    ============================================= */
    static public function mdlBorrarRol($tabla, $idRol)
    {
        try {
            // Verificar si el rol está asignado a algún usuario
            $stmtCheck = Conexion::conectar()->prepare("
                SELECT COUNT(*) as total 
                FROM usuario_roles 
                WHERE id_rol = :id_rol
            ");
            
            $stmtCheck->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
            $stmtCheck->execute();
            $resultado = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar el rol porque está asignado a usuarios"
                ]);
            }
            
            // Eliminar el rol si no está asignado
            $stmt = Conexion::conectar()->prepare("
                DELETE FROM $tabla 
                WHERE id_rol = :id_rol
            ");
            
            $stmt->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Rol eliminado con éxito"
                ]);
            }
            
            return json_encode([
                "status" => false,
                "message" => "Error al eliminar el rol"
            ]);
            
        } catch (PDOException $e) {
            error_log("Error en mdlBorrarRol: " . $e->getMessage());
            return json_encode([
                "status" => false,
                "message" => "Error en la base de datos: " . $e->getMessage()
            ]);
        } finally {
            $stmt = null;
            $stmtCheck = null;
        }
    }
}