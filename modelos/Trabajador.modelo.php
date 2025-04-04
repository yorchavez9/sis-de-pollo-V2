<?php

require_once "Conexion.php";

class ModeloTrabajador
{
    /*=============================================
    MOSTRAR TRABAJADORES
    =============================================*/
    static public function mdlMostrarTrabajadores($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT p.*, td.abreviatura as abreviatura 
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
                    WHERE p.tipo_persona = 'TRABAJADOR'
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
    REGISTRAR TRABAJADOR
    =============================================*/
    static public function mdlIngresarTrabajador($tabla, $datos)
    {
        try {
            // Primero verificar si ya existe un trabajador con el mismo tipo y número de documento
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
                    "message" => "Ya existe un trabajador con este tipo y número de documento"
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
                    fecha_nacimiento,
                    cargo,
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
                    :fecha_nacimiento,
                    :cargo,
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
            $stmt->bindParam(":fecha_nacimiento", $datos["fecha_nacimiento"]);
            $stmt->bindParam(":cargo", $datos["cargo"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Trabajador registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar el trabajador"
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
    ACTUALIZAR TRABAJADOR
    =============================================*/
    static public function mdlActualizarTrabajador($tabla, $datos)
    {
        try {
            // Verificar si ya existe otro trabajador con el mismo tipo y número de documento
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
                    "message" => "Ya existe otro trabajador con este tipo y número de documento"
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
                    fecha_nacimiento = :fecha_nacimiento,
                    cargo = :cargo,
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
            $stmt->bindParam(":fecha_nacimiento", $datos["fecha_nacimiento"]);
            $stmt->bindParam(":cargo", $datos["cargo"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Trabajador actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el trabajador"
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
    CAMBIAR ESTADO DE TRABAJADOR
    =============================================*/
    static public function mdlCambiarEstadoTrabajador($tabla, $datos)
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
                    "message" => "Estado del trabajador actualizado"
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
    ELIMINAR TRABAJADOR
    =============================================*/
    static public function mdlBorrarTrabajador($tabla, $idPersona)
    {
        try {
            // Primero verificar si el trabajador está asociado a alguna operación
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM ventas WHERE id_vendedor = :id_persona"
            );
            $stmt->bindParam(":id_persona", $idPersona, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar el trabajador porque tiene ventas asociadas"
                ]);
            }

            // Verificar si es administrador (podrías agregar más validaciones)
            $stmt = Conexion::conectar()->prepare(
                "SELECT cargo FROM $tabla WHERE id_persona = :id_persona"
            );
            $stmt->bindParam(":id_persona", $idPersona, PDO::PARAM_INT);
            $stmt->execute();
            $trabajador = $stmt->fetch();

            if ($trabajador['cargo'] == 'ADMINISTRADOR') {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar un administrador"
                ]);
            }

            // Si no tiene ventas asociadas y no es administrador, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_persona = :id_persona"
            );
            $stmt->bindParam(":id_persona", $idPersona, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Trabajador eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el trabajador"
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