<?php

require_once "Conexion.php";

class ModeloTarifa
{
    /*=============================================
    MOSTRAR TARIFAS
    =============================================*/
    static public function mdlMostrarTarifas($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT t.*, 
                    so.nombre as nombre_sucursal_origen, 
                    sd.nombre as nombre_sucursal_destino,
                    te.nombre as nombre_tipo_encomienda
                    FROM $tabla t
                    LEFT JOIN sucursales so ON t.id_sucursal_origen = so.id_sucursal
                    LEFT JOIN sucursales sd ON t.id_sucursal_destino = sd.id_sucursal
                    LEFT JOIN tipo_encomiendas te ON t.id_tipo_encomienda = te.id_tipo_encomienda
                    WHERE t.$item = :$item"
                );
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT t.*, 
                    so.nombre as nombre_sucursal_origen, 
                    sd.nombre as nombre_sucursal_destino,
                    te.nombre as nombre_tipo_encomienda
                    FROM $tabla t
                    LEFT JOIN sucursales so ON t.id_sucursal_origen = so.id_sucursal
                    LEFT JOIN sucursales sd ON t.id_sucursal_destino = sd.id_sucursal
                    LEFT JOIN tipo_encomiendas te ON t.id_tipo_encomienda = te.id_tipo_encomienda
                    ORDER BY t.id_tarifa DESC"
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
    REGISTRAR TARIFA
    =============================================*/
    static public function mdlIngresarTarifa($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    id_sucursal_origen, 
                    id_sucursal_destino, 
                    id_tipo_encomienda,
                    rango_peso_min,
                    rango_peso_max,
                    costo_base,
                    costo_kg_extra,
                    tiempo_estimado,
                    vigencia_desde,
                    vigencia_hasta,
                    estado
                ) VALUES (
                    :id_sucursal_origen, 
                    :id_sucursal_destino, 
                    :id_tipo_encomienda,
                    :rango_peso_min,
                    :rango_peso_max,
                    :costo_base,
                    :costo_kg_extra,
                    :tiempo_estimado,
                    :vigencia_desde,
                    :vigencia_hasta,
                    :estado
                )"
            );

            $stmt->bindParam(":id_sucursal_origen", $datos["id_sucursal_origen"], PDO::PARAM_INT);
            $stmt->bindParam(":id_sucursal_destino", $datos["id_sucursal_destino"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_encomienda", $datos["id_tipo_encomienda"], PDO::PARAM_INT);
            $stmt->bindParam(":rango_peso_min", $datos["rango_peso_min"], PDO::PARAM_STR);
            $stmt->bindParam(":rango_peso_max", $datos["rango_peso_max"], PDO::PARAM_STR);
            $stmt->bindParam(":costo_base", $datos["costo_base"], PDO::PARAM_STR);
            $stmt->bindParam(":costo_kg_extra", $datos["costo_kg_extra"], PDO::PARAM_STR);
            $stmt->bindParam(":tiempo_estimado", $datos["tiempo_estimado"], PDO::PARAM_INT);
            $stmt->bindParam(":vigencia_desde", $datos["vigencia_desde"], PDO::PARAM_STR);
            $stmt->bindParam(":vigencia_hasta", $datos["vigencia_hasta"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tarifa registrada con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar la tarifa"
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
    ACTUALIZAR TARIFA
    =============================================*/
    static public function mdlActualizarTarifa($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    id_sucursal_origen = :id_sucursal_origen,
                    id_sucursal_destino = :id_sucursal_destino,
                    id_tipo_encomienda = :id_tipo_encomienda,
                    rango_peso_min = :rango_peso_min,
                    rango_peso_max = :rango_peso_max,
                    costo_base = :costo_base,
                    costo_kg_extra = :costo_kg_extra,
                    tiempo_estimado = :tiempo_estimado,
                    vigencia_desde = :vigencia_desde,
                    vigencia_hasta = :vigencia_hasta,
                    estado = :estado
                WHERE id_tarifa = :id_tarifa"
            );

            $stmt->bindParam(":id_sucursal_origen", $datos["id_sucursal_origen"], PDO::PARAM_INT);
            $stmt->bindParam(":id_sucursal_destino", $datos["id_sucursal_destino"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_encomienda", $datos["id_tipo_encomienda"], PDO::PARAM_INT);
            $stmt->bindParam(":rango_peso_min", $datos["rango_peso_min"], PDO::PARAM_STR);
            $stmt->bindParam(":rango_peso_max", $datos["rango_peso_max"], PDO::PARAM_STR);
            $stmt->bindParam(":costo_base", $datos["costo_base"], PDO::PARAM_STR);
            $stmt->bindParam(":costo_kg_extra", $datos["costo_kg_extra"], PDO::PARAM_STR);
            $stmt->bindParam(":tiempo_estimado", $datos["tiempo_estimado"], PDO::PARAM_INT);
            $stmt->bindParam(":vigencia_desde", $datos["vigencia_desde"], PDO::PARAM_STR);
            $stmt->bindParam(":vigencia_hasta", $datos["vigencia_hasta"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tarifa", $datos["id_tarifa"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tarifa actualizada con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar la tarifa"
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
    CAMBIAR ESTADO DE TARIFA
    =============================================*/
    static public function mdlCambiarEstadoTarifa($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_tarifa = :id_tarifa"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_tarifa", $datos["id_tarifa"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado de la tarifa actualizado"
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
    ELIMINAR TARIFA
    =============================================*/
    static public function mdlBorrarTarifa($tabla, $idTarifa)
    {
        try {
            // Verificar si hay envíos asociados a esta tarifa
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM envios WHERE id_tarifa = :id_tarifa"
            );
            $stmt->bindParam(":id_tarifa", $idTarifa, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar la tarifa porque tiene envíos asociados"
                ]);
            }

            // Si no tiene envíos, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_tarifa = :id_tarifa"
            );
            $stmt->bindParam(":id_tarifa", $idTarifa, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tarifa eliminada con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar la tarifa"
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