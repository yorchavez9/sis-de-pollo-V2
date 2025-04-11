<?php
require_once "Conexion.php";

class ModeloTipoEncomienda
{
    /*=============================================
    MOSTRAR TIPOS DE ENCOMIENDA
    =============================================*/
    static public function mdlMostrarTipoEncomiendas($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id_tipo_encomienda DESC");
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
    REGISTRAR TIPO ENCOMIENDA
    =============================================*/
    static public function mdlIngresarTipoEncomienda($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    nombre, 
                    descripcion, 
                    requiere_confirmacion,
                    prioridad,
                    estado
                ) VALUES (
                    :nombre, 
                    :descripcion, 
                    :requiere_confirmacion,
                    :prioridad,
                    :estado
                )"
            );

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":requiere_confirmacion", $datos["requiere_confirmacion"], PDO::PARAM_INT);
            $stmt->bindParam(":prioridad", $datos["prioridad"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de encomienda registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar el tipo de encomienda"
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
    ACTUALIZAR TIPO ENCOMIENDA
    =============================================*/
    static public function mdlActualizarTipoEncomienda($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    requiere_confirmacion = :requiere_confirmacion,
                    prioridad = :prioridad,
                    estado = :estado
                WHERE id_tipo_encomienda = :id_tipo_encomienda"
            );

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":requiere_confirmacion", $datos["requiere_confirmacion"], PDO::PARAM_INT);
            $stmt->bindParam(":prioridad", $datos["prioridad"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_encomienda", $datos["id_tipo_encomienda"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de encomienda actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el tipo de encomienda"
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
    CAMBIAR ESTADO DE TIPO ENCOMIENDA
    =============================================*/
    static public function mdlCambiarEstadoTipoEncomienda($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_tipo_encomienda = :id_tipo_encomienda"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_encomienda", $datos["id_tipo_encomienda"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado del tipo de encomienda actualizado"
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
    ELIMINAR TIPO ENCOMIENDA
    =============================================*/
    static public function mdlBorrarTipoEncomienda($tabla, $idTipoEncomienda)
    {
        try {
            // Primero verificar si hay encomiendas asociadas a este tipo
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM encomiendas WHERE id_tipo_encomienda = :id_tipo_encomienda"
            );
            $stmt->bindParam(":id_tipo_encomienda", $idTipoEncomienda, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar el tipo de encomienda porque tiene encomiendas asociadas"
                ]);
            }

            // Si no tiene encomiendas, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_tipo_encomienda = :id_tipo_encomienda"
            );
            $stmt->bindParam(":id_tipo_encomienda", $idTipoEncomienda, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de encomienda eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el tipo de encomienda"
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