<?php
require_once "Conexion.php";

class ModeloSerieComprobante
{
    /*=============================================
    MOSTRAR SERIES COMPROBANTES
    =============================================*/
    static public function mdlMostrarSeriesComprobantes($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT s.*, su.nombre as nombre_sucursal, tc.nombre as nombre_tipo_comprobante 
                    FROM $tabla s 
                    LEFT JOIN sucursales su ON s.id_sucursal = su.id_sucursal 
                    LEFT JOIN tipo_comprobantes tc ON s.id_tipo_comprobante = tc.id_tipo_comprobante 
                    WHERE s.$item = :$item"
                );
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT s.*, su.nombre as nombre_sucursal, tc.nombre as nombre_tipo_comprobante 
                    FROM $tabla s 
                    LEFT JOIN sucursales su ON s.id_sucursal = su.id_sucursal 
                    LEFT JOIN tipo_comprobantes tc ON s.id_tipo_comprobante = tc.id_tipo_comprobante 
                    ORDER BY s.id_serie DESC"
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
    REGISTRAR SERIE COMPROBANTE
    =============================================*/
    static public function mdlIngresarSerieComprobante($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    id_sucursal, 
                    id_tipo_comprobante, 
                    serie, 
                    numero_inicial,
                    numero_actual,
                    numero_final,
                    estado
                ) VALUES (
                    :id_sucursal, 
                    :id_tipo_comprobante, 
                    :serie, 
                    :numero_inicial,
                    :numero_actual,
                    :numero_final,
                    :estado
                )"
            );

            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_comprobante", $datos["id_tipo_comprobante"], PDO::PARAM_INT);
            $stmt->bindParam(":serie", $datos["serie"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_inicial", $datos["numero_inicial"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_actual", $datos["numero_actual"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_final", $datos["numero_final"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Serie de comprobante registrada con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar la serie de comprobante"
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
    ACTUALIZAR SERIE COMPROBANTE
    =============================================*/
    static public function mdlActualizarSerieComprobante($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    id_sucursal = :id_sucursal,
                    id_tipo_comprobante = :id_tipo_comprobante,
                    serie = :serie,
                    numero_inicial = :numero_inicial,
                    numero_actual = :numero_actual,
                    numero_final = :numero_final,
                    estado = :estado
                WHERE id_serie = :id_serie"
            );

            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_comprobante", $datos["id_tipo_comprobante"], PDO::PARAM_INT);
            $stmt->bindParam(":serie", $datos["serie"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_inicial", $datos["numero_inicial"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_actual", $datos["numero_actual"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_final", $datos["numero_final"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_serie", $datos["id_serie"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Serie de comprobante actualizada con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar la serie de comprobante"
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
    CAMBIAR ESTADO DE SERIE COMPROBANTE
    =============================================*/
    static public function mdlCambiarEstadoSerieComprobante($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_serie = :id_serie"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_serie", $datos["id_serie"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado de la serie actualizado"
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
    ELIMINAR SERIE COMPROBANTE
    =============================================*/
    static public function mdlBorrarSerieComprobante($tabla, $idSerie)
    {
        try {
            // Verificar si hay comprobantes asociados a esta serie
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM comprobantes WHERE id_serie = :id_serie"
            );
            $stmt->bindParam(":id_serie", $idSerie, PDO::PARAM_INT);    
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado["total"] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar la serie, hay comprobantes asociados"
                ]);
            }
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_serie = :id_serie"
            );
            $stmt->bindParam(":id_serie", $idSerie, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Serie de comprobante eliminada con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar la serie de comprobante"
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