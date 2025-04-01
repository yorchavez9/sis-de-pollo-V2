<?php

require_once "Conexion.php";

class ModeloAlmacen
{
    /*=============================================
    MOSTRAR ALMACENES
    =============================================*/
    static public function mdlMostrarAlmacenes($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT a.*, s.nombre as nombre_sucursal 
                    FROM $tabla a 
                    LEFT JOIN sucursales s ON a.id_sucursal = s.id_sucursal 
                    WHERE a.$item = :$item"
                );
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT a.*, s.nombre as nombre_sucursal 
                    FROM $tabla a 
                    LEFT JOIN sucursales s ON a.id_sucursal = s.id_sucursal 
                    ORDER BY a.id_almacen DESC"
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
    REGISTRAR ALMACÉN
    =============================================*/
    static public function mdlIngresarAlmacen($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    id_sucursal, 
                    nombre, 
                    descripcion, 
                    tipo,
                    estado
                ) VALUES (
                    :id_sucursal, 
                    :nombre, 
                    :descripcion, 
                    :tipo,
                    :estado
                )"
            );

            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Almacén registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar el almacén"
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
    ACTUALIZAR ALMACÉN
    =============================================*/
    static public function mdlActualizarAlmacen($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    id_sucursal = :id_sucursal,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    tipo = :tipo,
                    estado = :estado
                WHERE id_almacen = :id_almacen"
            );

            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_almacen", $datos["id_almacen"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Almacén actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el almacén"
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
    CAMBIAR ESTADO DE ALMACÉN
    =============================================*/
    static public function mdlCambiarEstadoAlmacen($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_almacen = :id_almacen"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_almacen", $datos["id_almacen"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado del almacén actualizado"
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
    ELIMINAR ALMACÉN
    =============================================*/
    static public function mdlBorrarAlmacen($tabla, $idAlmacen)
    {
        try {
            // Primero verificar si hay productos en el inventario asociados a este almacén
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM inventario WHERE id_almacen = :id_almacen"
            );
            $stmt->bindParam(":id_almacen", $idAlmacen, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar el almacén porque tiene productos asociados en el inventario"
                ]);
            }

            // Si no tiene productos, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_almacen = :id_almacen"
            );
            $stmt->bindParam(":id_almacen", $idAlmacen, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Almacén eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el almacén"
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