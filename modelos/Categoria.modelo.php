<?php

require_once "Conexion.php";

class ModeloCategoria
{
    /*=============================================
    MOSTRAR CATEGORÍAS
    =============================================*/
    static public function mdlMostrarCategorias($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT * FROM $tabla WHERE $item = :$item"
                );
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT * FROM $tabla ORDER BY id_categoria DESC"
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
    REGISTRAR CATEGORÍA
    =============================================*/
    static public function mdlIngresarCategoria($tabla, $datos)
    {
        try {
            // Primero verificar si ya existe una categoría con el mismo nombre
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM $tabla WHERE nombre = :nombre"
            );
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existe una categoría con este nombre"
                ]);
            }

            // Si no existe, proceder a insertar
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    nombre, 
                    descripcion, 
                    tipo,
                    estado
                ) VALUES (
                    :nombre, 
                    :descripcion, 
                    :tipo,
                    :estado
                )"
            );

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Categoría registrada con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar la categoría"
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
    ACTUALIZAR CATEGORÍA
    =============================================*/
    static public function mdlActualizarCategoria($tabla, $datos)
    {
        try {
            // Verificar si ya existe otra categoría con el mismo nombre
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM $tabla 
                WHERE nombre = :nombre
                AND id_categoria != :id_categoria"
            );
            
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existe otra categoría con este nombre"
                ]);
            }

            // Si no existe, proceder a actualizar
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    tipo = :tipo,
                    estado = :estado
                WHERE id_categoria = :id_categoria"
            );

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Categoría actualizada con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar la categoría"
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
    CAMBIAR ESTADO DE CATEGORÍA
    =============================================*/
    static public function mdlCambiarEstadoCategoria($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_categoria = :id_categoria"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado de la categoría actualizado"
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
    ELIMINAR CATEGORÍA
    =============================================*/
    static public function mdlBorrarCategoria($tabla, $idCategoria)
    {
        try {
            // Primero verificar si la categoría está asociada a algún producto
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM productos WHERE id_categoria = :id_categoria"
            );
            $stmt->bindParam(":id_categoria", $idCategoria, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar la categoría porque tiene productos asociados"
                ]);
            }

            // Si no tiene productos, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_categoria = :id_categoria"
            );
            $stmt->bindParam(":id_categoria", $idCategoria, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Categoría eliminada con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar la categoría"
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