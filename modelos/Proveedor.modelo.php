<?php

require_once "Conexion.php";

class ModeloProveedor
{
    /*=============================================
    MOSTRAR PROVEEDORES
    =============================================*/
    static public function mdlMostrarProveedores($tabla, $item, $valor)
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
                    "SELECT p.*, td.nombre as nombre_tipo_documento 
                    FROM $tabla p 
                    LEFT JOIN tipo_documentos td ON p.id_tipo_documento = td.id_tipo_documento 
                    WHERE p.tipo_persona = 'PROVEEDOR'
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
    REGISTRAR PROVEEDOR
    =============================================*/
    static public function mdlIngresarProveedor($tabla, $datos)
    {
        try {
            // Primero verificar si ya existe un proveedor con el mismo tipo y número de documento
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
                    "message" => "Ya existe un proveedor con este tipo y número de documento"
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
                    razon_social,
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
                    :razon_social,
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
            $stmt->bindParam(":razon_social", $datos["razon_social"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":celular", $datos["celular"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Proveedor registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar el proveedor"
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
    ACTUALIZAR PROVEEDOR
    =============================================*/
    static public function mdlActualizarProveedor($tabla, $datos)
    {
        try {
            // Verificar si ya existe otro proveedor con el mismo tipo y número de documento
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
                    "message" => "Ya existe otro proveedor con este tipo y número de documento"
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
                    razon_social = :razon_social,
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
            $stmt->bindParam(":razon_social", $datos["razon_social"], PDO::PARAM_STR);
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
                    "message" => "Proveedor actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el proveedor"
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
    CAMBIAR ESTADO DE PROVEEDOR
    =============================================*/
    static public function mdlCambiarEstadoProveedor($tabla, $datos)
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
                    "message" => "Estado del proveedor actualizado"
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
    ELIMINAR PROVEEDOR
    =============================================*/
    static public function mdlBorrarProveedor($tabla, $idPersona)
    {
        try {
            // Primero verificar si el proveedor está asociado a alguna compra
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM compras WHERE id_proveedor = :id_persona"
            );
            $stmt->bindParam(":id_persona", $idPersona, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar el proveedor porque tiene compras asociadas"
                ]);
            }

            // Si no tiene compras, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_persona = :id_persona"
            );
            $stmt->bindParam(":id_persona", $idPersona, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Proveedor eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el proveedor"
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