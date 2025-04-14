<?php
require_once "Conexion.php";

class ModeloPermiso
{
    /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    static public function mdlMostrarPermisos()
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT p.*, r.nombre as nombre_rol, m.nombre as nombre_modulo, a.nombre as nombre_accion
                 FROM permisos p
                 LEFT JOIN roles r ON p.id_rol = r.id_rol
                 LEFT JOIN modulos m ON p.id_modulo = m.id_modulo
                 LEFT JOIN acciones a ON p.id_accion = a.id_accion
                 ORDER BY p.id_rol, p.id_modulo, p.id_accion"
            );
            
            $stmt->execute();
            return json_encode([
                "status" => true,
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    GUARDAR PERMISO
    =============================================*/
    static public function mdlGuardarPermiso($idRol, $permisos)
    {
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();

            // Verificar si ya existen permisos para este rol
            $stmtVerificar = $conexion->prepare(
                "SELECT COUNT(*) as total FROM permisos WHERE id_rol = :id_rol"
            );
            $stmtVerificar->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $existenPermisos = $stmtVerificar->fetch()['total'] > 0;

            if ($existenPermisos) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existen permisos para este rol. Use la opción de editar."
                ]);
            }

            // Insertar los nuevos permisos
            $stmtInsertar = $conexion->prepare(
                "INSERT INTO permisos (id_rol, id_modulo, id_accion) 
                 VALUES (:id_rol, :id_modulo, :id_accion)"
            );

            foreach ($permisos as $permiso) {
                $stmtInsertar->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
                $stmtInsertar->bindParam(":id_modulo", $permiso['id_modulo'], PDO::PARAM_INT);
                $stmtInsertar->bindParam(":id_accion", $permiso['id_accion'], PDO::PARAM_INT);
                $stmtInsertar->execute();
            }

            $conexion->commit();
            
            return json_encode([
                "status" => true,
                "message" => "Permisos guardados correctamente"
            ]);
        } catch (Exception $e) {
            $conexion->rollBack();
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ACTUALIZAR PERMISO
    =============================================*/
    static public function mdlActualizarPermiso($idRol, $permisos)
    {
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();

            // 1. Eliminar permisos existentes para este rol
            $stmtEliminar = $conexion->prepare(
                "DELETE FROM permisos WHERE id_rol = :id_rol"
            );
            $stmtEliminar->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
            $stmtEliminar->execute();

            // 2. Insertar los nuevos permisos
            if (!empty($permisos)) {
                $stmtInsertar = $conexion->prepare(
                    "INSERT INTO permisos (id_rol, id_modulo, id_accion) 
                     VALUES (:id_rol, :id_modulo, :id_accion)"
                );

                foreach ($permisos as $permiso) {
                    $stmtInsertar->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
                    $stmtInsertar->bindParam(":id_modulo", $permiso['id_modulo'], PDO::PARAM_INT);
                    $stmtInsertar->bindParam(":id_accion", $permiso['id_accion'], PDO::PARAM_INT);
                    $stmtInsertar->execute();
                }
            }

            $conexion->commit();
            
            return json_encode([
                "status" => true,
                "message" => "Permisos actualizados correctamente"
            ]);
        } catch (Exception $e) {
            $conexion->rollBack();
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ELIMINAR PERMISO
    =============================================*/
    static public function mdlEliminarPermiso($idRol)
    {
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare(
                "DELETE FROM permisos WHERE id_rol = :id_rol"
            );
            $stmt->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                    return json_encode([
                        "status" => true,
                        "message" => "Permisos eliminados correctamente"
                    ]);
                } else {
                    return json_encode([
                        "status" => false,
                        "message" => "Error al eliminar los permisos"
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
    