<?php

require_once "Conexion.php";

class ModeloTransportista
{
    /*=============================================
    MOSTRAR TRANSPORTISTAS
    =============================================*/
    static public function mdlMostrarTransportistas($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT p.*, td.nombre as nombre_tipo_documento 
                    FROM $tabla p 
                    LEFT JOIN tipo_documentos td ON p.id_tipo_documento = td.id_tipo_documento 
                    WHERE p.$item = :$item"
                );
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT p.*, td.abreviatura as abreviatura 
                    FROM $tabla p 
                    LEFT JOIN tipo_documentos td ON p.id_tipo_documento = td.id_tipo_documento 
                    WHERE p.tipo_persona = 'TRANSPORTISTA'
                    ORDER BY p.id_persona DESC"
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
            // Primero verificar si ya existe un transportista con el mismo tipo y número de documento
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM $tabla 
                WHERE tipo_persona = :tipo_persona 
                AND id_tipo_documento = :id_tipo_documento 
                AND numero_documento = :numero_documento"
            );
            
            $stmt->bindParam(":tipo_persona", $datos["tipo_persona"], PDO::PARAM_STR);
            $stmt->bindParam(":id_tipo_documento", $datos["id_tipo_documento"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existe un transportista con este tipo y número de documento"
                ]);
            }

            // Si no existe, proceder a insertar
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    tipo_persona, 
                    id_tipo_documento, 
                    numero_documento, 
                    nombre,
                    apellidos,
                    telefono,
                    celular,
                    email,
                    direccion,
                    ciudad,
                    estado
                ) VALUES (
                    :tipo_persona, 
                    :id_tipo_documento, 
                    :numero_documento, 
                    :nombre,
                    :apellidos,
                    :telefono,
                    :celular,
                    :email,
                    :direccion,
                    :ciudad,
                    :estado
                )"
            );

            $stmt->bindParam(":tipo_persona", $datos["tipo_persona"], PDO::PARAM_STR);
            $stmt->bindParam(":id_tipo_documento", $datos["id_tipo_documento"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos", $datos["apellidos"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":celular", $datos["celular"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
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
            // Verificar si ya existe otro transportista con el mismo tipo y número de documento
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM $tabla 
                WHERE tipo_persona = :tipo_persona 
                AND id_tipo_documento = :id_tipo_documento 
                AND numero_documento = :numero_documento
                AND id_persona != :id_persona"
            );
            
            $stmt->bindParam(":tipo_persona", $datos["tipo_persona"], PDO::PARAM_STR);
            $stmt->bindParam(":id_tipo_documento", $datos["id_tipo_documento"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existe otro transportista con este tipo y número de documento"
                ]);
            }

            // Si no existe, proceder a actualizar
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    tipo_persona = :tipo_persona,
                    id_tipo_documento = :id_tipo_documento,
                    numero_documento = :numero_documento,
                    nombre = :nombre,
                    apellidos = :apellidos,
                    telefono = :telefono,
                    celular = :celular,
                    email = :email,
                    direccion = :direccion,
                    ciudad = :ciudad,
                    estado = :estado
                WHERE id_persona = :id_persona"
            );

            $stmt->bindParam(":tipo_persona", $datos["tipo_persona"], PDO::PARAM_STR);
            $stmt->bindParam(":id_tipo_documento", $datos["id_tipo_documento"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos", $datos["apellidos"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":celular", $datos["celular"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);

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
                "UPDATE $tabla SET estado = :estado WHERE id_persona = :id_persona"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);

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
    static public function mdlBorrarTransportista($tabla, $idPersona)
    {
        try {
            // Primero verificar si el transportista está asociado a algún envío
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM envios WHERE id_transportista = :id_persona"
            );
            $stmt->bindParam(":id_persona", $idPersona, PDO::PARAM_INT);
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
                "DELETE FROM $tabla WHERE id_persona = :id_persona"
            );
            $stmt->bindParam(":id_persona", $idPersona, PDO::PARAM_INT);

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