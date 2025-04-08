<?php

require_once "Conexion.php";

class ModeloTransportista
{
    /*=============================================
    MOSTRAR TRANSPORTISTAS
    =============================================*/
    static public function mdlMostrarTransportistas($tabla, $item = null, $valor = null)
    {
        try {
            if ($item != null) {
                // Mostrar un transportista específico con datos de la persona
                $stmt = Conexion::conectar()->prepare(
                    "SELECT t.*, p.nombre, p.apellidos, p.razon_social, p.numero_documento, td.nombre as tipo_documento 
                    FROM $tabla t
                    INNER JOIN personas p ON t.id_persona = p.id_persona
                    LEFT JOIN tipo_documentos td ON p.id_tipo_documento = td.id_tipo_documento
                    WHERE t.$item = :$item"
                );
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_INT);
            } else {
                // Mostrar todos los transportistas con datos de las personas
                $stmt = Conexion::conectar()->prepare(
                    "SELECT t.*, 
                    CASE 
                        WHEN p.tipo_persona = 'TRANSPORTISTA' THEN CONCAT(p.nombre, ' ', p.apellidos)
                        ELSE p.razon_social
                    END as nombre_completo,
                    p.numero_documento,
                    td.nombre as tipo_documento
                    FROM $tabla t
                    INNER JOIN personas p ON t.id_persona = p.id_persona
                    LEFT JOIN tipo_documentos td ON p.id_tipo_documento = td.id_tipo_documento
                    ORDER BY t.id_transportista DESC"
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
    REGISTRAR TRANSPORTISTA
    =============================================*/
    static public function mdlIngresarTransportista($tabla, $datos)
    {
        try {
            // Verificar si la persona ya está registrada como transportista
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM $tabla WHERE id_persona = :id_persona"
            );
            $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Esta persona ya está registrada como transportista"
                ]);
            }

            // Insertar nuevo transportista
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    id_persona, 
                    tipo_vehiculo, 
                    placa_vehiculo, 
                    telefono_contacto,
                    fecha_registro,
                    estado
                ) VALUES (
                    :id_persona, 
                    :tipo_vehiculo, 
                    :placa_vehiculo, 
                    :telefono_contacto,
                    CURDATE(),
                    :estado
                )"
            );

            $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
            $stmt->bindParam(":tipo_vehiculo", $datos["tipo_vehiculo"], PDO::PARAM_STR);
            $stmt->bindParam(":placa_vehiculo", $datos["placa_vehiculo"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono_contacto", $datos["telefono_contacto"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Transportista registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar el transportista"
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
    ACTUALIZAR TRANSPORTISTA
    =============================================*/
    static public function mdlActualizarTransportista($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    tipo_vehiculo = :tipo_vehiculo,
                    placa_vehiculo = :placa_vehiculo,
                    telefono_contacto = :telefono_contacto,
                    estado = :estado
                WHERE id_transportista = :id_transportista"
            );

            $stmt->bindParam(":tipo_vehiculo", $datos["tipo_vehiculo"], PDO::PARAM_STR);
            $stmt->bindParam(":placa_vehiculo", $datos["placa_vehiculo"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono_contacto", $datos["telefono_contacto"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_transportista", $datos["id_transportista"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Transportista actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el transportista"
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
    CAMBIAR ESTADO DE TRANSPORTISTA
    =============================================*/
    static public function mdlCambiarEstadoTransportista($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_transportista = :id_transportista"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_transportista", $datos["id_transportista"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado del transportista actualizado"
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
    ELIMINAR TRANSPORTISTA
    =============================================*/
    static public function mdlBorrarTransportista($tabla, $idTransportista)
    {
        try {
            // Verificar si el transportista está asociado a algún envío
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM envios WHERE id_transportista = :id_transportista"
            );
            $stmt->bindParam(":id_transportista", $idTransportista, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar el transportista porque tiene envíos asociados"
                ]);
            }

            // Si no tiene envíos, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_transportista = :id_transportista"
            );
            $stmt->bindParam(":id_transportista", $idTransportista, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Transportista eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el transportista"
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