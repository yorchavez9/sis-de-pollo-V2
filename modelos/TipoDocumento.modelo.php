<?php
require_once "Conexion.php";

class ModeloTipoDocumento
{
    /*=============================================
    LISTAR TIPOS DE DOCUMENTOS
    =============================================*/
    static public function mdlListarTiposDocumentos()
    {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM tipo_documentos ORDER BY nombre ASC");
            $stmt->execute();
            return json_encode([
                "status" => true,
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage(),
                "error" => $e->getTraceAsString()
            ]);
        }
    }

    /*=============================================
    CREAR TIPO DE DOCUMENTO
    =============================================*/
    static public function mdlCrearTipoDocumento($datos)
    {
        try {
            // Verificar si ya existe el nombre o abreviatura
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as existe FROM tipo_documentos 
                WHERE nombre = :nombre OR abreviatura = :abreviatura"
            );
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":abreviatura", $datos["abreviatura"], PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado["existe"] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existe un tipo de documento con ese nombre o abreviatura"
                ]);
            }

            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO tipo_documentos(
                    nombre, 
                    abreviatura, 
                    longitud, 
                    es_empresa,
                    estado
                ) VALUES (
                    :nombre, 
                    :abreviatura, 
                    :longitud, 
                    :es_empresa,
                    :estado
                )"
            );

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":abreviatura", $datos["abreviatura"], PDO::PARAM_STR);
            $stmt->bindParam(":longitud", $datos["longitud"], PDO::PARAM_INT);
            $stmt->bindParam(":es_empresa", $datos["es_empresa"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de documento creado correctamente",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al crear el tipo de documento"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage(),
                "error" => $e->getTraceAsString()
            ]);
        }
    }

    /*=============================================
    OBTENER TIPO DE DOCUMENTO
    =============================================*/
    static public function mdlObtenerTipoDocumento($id)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT * FROM tipo_documentos WHERE id_tipo_documento = :id"
            );
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                return json_encode([
                    "status" => true,
                    "data" => $resultado
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Tipo de documento no encontrado"
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
    ACTUALIZAR TIPO DE DOCUMENTO
    =============================================*/
    static public function mdlActualizarTipoDocumento($datos)
    {
        try {
            // Verificar si ya existe otro tipo con el mismo nombre o abreviatura
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as existe FROM tipo_documentos 
                WHERE (nombre = :nombre OR abreviatura = :abreviatura) 
                AND id_tipo_documento != :id"
            );
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":abreviatura", $datos["abreviatura"], PDO::PARAM_STR);
            $stmt->bindParam(":id", $datos["id_tipo_documento"], PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado["existe"] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existe otro tipo de documento con ese nombre o abreviatura"
                ]);
            }

            $stmt = Conexion::conectar()->prepare(
                "UPDATE tipo_documentos SET 
                    nombre = :nombre,
                    abreviatura = :abreviatura,
                    longitud = :longitud,
                    es_empresa = :es_empresa,
                    estado = :estado
                WHERE id_tipo_documento = :id"
            );

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":abreviatura", $datos["abreviatura"], PDO::PARAM_STR);
            $stmt->bindParam(":longitud", $datos["longitud"], PDO::PARAM_INT);
            $stmt->bindParam(":es_empresa", $datos["es_empresa"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id", $datos["id_tipo_documento"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de documento actualizado correctamente"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el tipo de documento"
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
    CAMBIAR ESTADO DE TIPO DE DOCUMENTO
    =============================================*/
    static public function mdlCambiarEstadoTipoDocumento($datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE tipo_documentos SET estado = :estado 
                WHERE id_tipo_documento = :id"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id", $datos["id_tipo_documento"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado actualizado correctamente"
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
    VERIFICAR DOCUMENTOS ASOCIADOS
    =============================================*/
    static public function mdlVerificarDocumentosAsociados($id)
    {
        try {
            // Verificar en clientes
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM clientes 
                WHERE id_tipo_documento = :id"
            );
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $clientes = $stmt->fetch();

            // Verificar en proveedores (si aplica)
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM proveedores 
                WHERE id_tipo_documento = :id"
            );
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $proveedores = $stmt->fetch();

            $total = $clientes["total"] + $proveedores["total"];

            return json_encode([
                "status" => true,
                "tiene_asociados" => $total > 0,
                "total" => $total
            ]);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ELIMINAR TIPO DE DOCUMENTO
    =============================================*/
    static public function mdlEliminarTipoDocumento($id)
    {
        try {
            // Primero verificar si hay documentos asociados
            /* $respuesta = self::mdlVerificarDocumentosAsociados($id);
            $resultado = json_decode($respuesta, true);
            
            if ($resultado["tiene_asociados"]) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar, existen documentos asociados a este tipo"
                ]);
            } */
            
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM tipo_documentos WHERE id_tipo_documento = :id"
            );
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Tipo de documento eliminado correctamente"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el tipo de documento"
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