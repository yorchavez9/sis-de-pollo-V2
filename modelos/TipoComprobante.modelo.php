<?php
require_once "Conexion.php";

class ModeloTipoComprobante
{
    /*=============================================
    MOSTRAR TIPOS DE COMPROBANTE
    =============================================*/
    static public function mdlMostrarTipoComprobantes($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id_tipo_comprobante DESC");
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
    REGISTRAR TIPO COMPROBANTE
    =============================================*/
    static public function mdlIngresarTipoComprobante($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    codigo, 
                    nombre, 
                    serie_obligatoria, 
                    numero_obligatorio,
                    afecta_inventario,
                    estado
                ) VALUES (
                    :codigo, 
                    :nombre, 
                    :serie_obligatoria, 
                    :numero_obligatorio,
                    :afecta_inventario,
                    :estado
                )"
            );

            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":serie_obligatoria", $datos["serie_obligatoria"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_obligatorio", $datos["numero_obligatorio"], PDO::PARAM_INT);
            $stmt->bindParam(":afecta_inventario", $datos["afecta_inventario"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de comprobante registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar el tipo de comprobante"
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
    ACTUALIZAR TIPO COMPROBANTE
    =============================================*/
    static public function mdlActualizarTipoComprobante($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    codigo = :codigo,
                    nombre = :nombre,
                    serie_obligatoria = :serie_obligatoria,
                    numero_obligatorio = :numero_obligatorio,
                    afecta_inventario = :afecta_inventario,
                    estado = :estado
                WHERE id_tipo_comprobante = :id_tipo_comprobante"
            );

            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":serie_obligatoria", $datos["serie_obligatoria"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_obligatorio", $datos["numero_obligatorio"], PDO::PARAM_INT);
            $stmt->bindParam(":afecta_inventario", $datos["afecta_inventario"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_comprobante", $datos["id_tipo_comprobante"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de comprobante actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el tipo de comprobante"
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
    CAMBIAR ESTADO DE TIPO COMPROBANTE
    =============================================*/
    static public function mdlCambiarEstadoTipoComprobante($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_tipo_comprobante = :id_tipo_comprobante"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_comprobante", $datos["id_tipo_comprobante"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado del tipo de comprobante actualizado"
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
    ELIMINAR TIPO COMPROBANTE
    =============================================*/
    static public function mdlBorrarTipoComprobante($tabla, $idTipoComprobante)
    {
        try {
            // Verificar si hay comprobantes asociados a este tipo
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM tipo_comprobantes WHERE id_tipo_comprobante = :id_tipo_comprobante"
            );
            $stmt->bindParam(":id_tipo_comprobante", $idTipoComprobante, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar el tipo de comprobante porque tiene comprobantes asociados"
                ]);
            }

            // Si no tiene comprobantes asociados, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_tipo_comprobante = :id_tipo_comprobante"
            );
            $stmt->bindParam(":id_tipo_comprobante", $idTipoComprobante, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de comprobante eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el tipo de comprobante"
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