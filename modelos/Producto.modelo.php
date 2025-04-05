<?php

require_once "Conexion.php";

class ModeloProducto
{
    /*=============================================
    MOSTRAR PRODUCTOS
    =============================================*/
    static public function mdlMostrarProductos($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT p.*, c.nombre as nombre_categoria 
                    FROM $tabla p 
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                    WHERE p.$item = :$item"
                );
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT p.*, c.nombre as nombre_categoria 
                    FROM $tabla p 
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                    ORDER BY p.id_producto DESC"
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
    REGISTRAR PRODUCTO
    =============================================*/
    static public function mdlIngresarProducto($tabla, $datos)
    {
        try {
            // Verificar si ya existe un producto con el mismo código
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM $tabla 
                WHERE codigo = :codigo"
            );
            
            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existe un producto con este código"
                ]);
            }

            // Verificar código de barras si existe
            if (!empty($datos["codigo_barras"])) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT COUNT(*) as total FROM $tabla 
                    WHERE codigo_barras = :codigo_barras"
                );
                
                $stmt->bindParam(":codigo_barras", $datos["codigo_barras"], PDO::PARAM_STR);
                $stmt->execute();
                $resultado = $stmt->fetch();

                if ($resultado['total'] > 0) {
                    return json_encode([
                        "status" => false,
                        "message" => "Ya existe un producto con este código de barras"
                    ]);
                }
            }

            // Si no existe, proceder a insertar
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    id_categoria, 
                    codigo, 
                    codigo_barras, 
                    nombre,
                    descripcion,
                    unidad_medida,
                    peso_promedio,
                    precio_compra,
                    precio_venta,
                    tiene_iva,
                    imagen,
                    estado
                ) VALUES (
                    :id_categoria, 
                    :codigo, 
                    :codigo_barras, 
                    :nombre,
                    :descripcion,
                    :unidad_medida,
                    :peso_promedio,
                    :precio_compra,
                    :precio_venta,
                    :tiene_iva,
                    :imagen,
                    :estado
                )"
            );

            $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":codigo_barras", $datos["codigo_barras"]);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"]);
            $stmt->bindParam(":unidad_medida", $datos["unidad_medida"], PDO::PARAM_STR);
            $stmt->bindParam(":peso_promedio", $datos["peso_promedio"]);
            $stmt->bindParam(":precio_compra", $datos["precio_compra"]);
            $stmt->bindParam(":precio_venta", $datos["precio_venta"]);
            $stmt->bindParam(":tiene_iva", $datos["tiene_iva"], PDO::PARAM_INT);
            $stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Producto registrado con éxito",
                    "id" => Conexion::conectar()->lastInsertId()
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al registrar el producto"
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
    ACTUALIZAR PRODUCTO
    =============================================*/
    static public function mdlActualizarProducto($tabla, $datos)
    {
        try {
            // Verificar si ya existe otro producto con el mismo código
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM $tabla 
                WHERE codigo = :codigo
                AND id_producto != :id_producto"
            );
            
            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "Ya existe otro producto con este código"
                ]);
            }

            // Verificar código de barras si existe
            if (!empty($datos["codigo_barras"])) {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT COUNT(*) as total FROM $tabla 
                    WHERE codigo_barras = :codigo_barras
                    AND id_producto != :id_producto"
                );
                
                $stmt->bindParam(":codigo_barras", $datos["codigo_barras"], PDO::PARAM_STR);
                $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);
                $stmt->execute();
                $resultado = $stmt->fetch();

                if ($resultado['total'] > 0) {
                    return json_encode([
                        "status" => false,
                        "message" => "Ya existe otro producto con este código de barras"
                    ]);
                }
            }

            // Si no existe, proceder a actualizar
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET 
                    id_categoria = :id_categoria,
                    codigo = :codigo,
                    codigo_barras = :codigo_barras,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    unidad_medida = :unidad_medida,
                    peso_promedio = :peso_promedio,
                    precio_compra = :precio_compra,
                    precio_venta = :precio_venta,
                    tiene_iva = :tiene_iva,
                    imagen = :imagen,
                    estado = :estado
                WHERE id_producto = :id_producto"
            );

            $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":codigo_barras", $datos["codigo_barras"]);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"]);
            $stmt->bindParam(":unidad_medida", $datos["unidad_medida"], PDO::PARAM_STR);
            $stmt->bindParam(":peso_promedio", $datos["peso_promedio"]);
            $stmt->bindParam(":precio_compra", $datos["precio_compra"]);
            $stmt->bindParam(":precio_venta", $datos["precio_venta"]);
            $stmt->bindParam(":tiene_iva", $datos["tiene_iva"], PDO::PARAM_INT);
            $stmt->bindParam(":imagen", $datos["imagen"]);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Producto actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el producto"
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
    CAMBIAR ESTADO DE PRODUCTO
    =============================================*/
    static public function mdlCambiarEstadoProducto($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "UPDATE $tabla SET estado = :estado WHERE id_producto = :id_producto"
            );
            
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado del producto actualizado"
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
    ELIMINAR PRODUCTO
    =============================================*/
    static public function mdlBorrarProducto($tabla, $idProducto)
    {
        try {
            // Primero obtener información del producto para eliminar la imagen
            $stmt = Conexion::conectar()->prepare(
                "SELECT imagen FROM $tabla WHERE id_producto = :id_producto"
            );
            $stmt->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
            $stmt->execute();
            $producto = $stmt->fetch();

            // Verificar si el producto tiene movimientos en inventario o ventas
            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM inventario WHERE id_producto = :id_producto"
            );
            $stmt->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
            $stmt->execute();
            $inventario = $stmt->fetch();

            $stmt = Conexion::conectar()->prepare(
                "SELECT COUNT(*) as total FROM detalle_ventas WHERE id_producto = :id_producto"
            );
            $stmt->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
            $stmt->execute();
            $ventas = $stmt->fetch();

            if ($inventario['total'] > 0 || $ventas['total'] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "No se puede eliminar el producto porque tiene registros en inventario o ventas"
                ]);
            }

            // Si no tiene registros asociados, proceder a eliminar
            $stmt = Conexion::conectar()->prepare(
                "DELETE FROM $tabla WHERE id_producto = :id_producto"
            );
            $stmt->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Eliminar la imagen si existe
                if (!empty($producto['imagen'])) {
                    $rutaImagen = "../vistas/assets/img/productos/" . $producto['imagen'];
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }
                
                return json_encode([
                    "status" => true,
                    "message" => "Producto eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar el producto"
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