<?php

require_once "Conexion.php";

class ModeloInventario
{
    /*=============================================
    MOSTRAR INVENTARIO
    =============================================*/
    static public function mdlMostrarInventario($tabla, $filtroAlmacen = null, $filtroProducto = null, $filtroEstado = null, $item = null, $valor = null)
{
    try {
        $where = [];
        $params = [];

        // Convertir 'null' string a null real
        $filtroAlmacen = ($filtroAlmacen === 'null') ? null : $filtroAlmacen;
        $filtroProducto = ($filtroProducto === 'null') ? null : $filtroProducto;
        $filtroEstado = ($filtroEstado === 'null' || $filtroEstado === '') ? null : $filtroEstado;

        // Filtro por item específico (prioridad)
        if ($item !== null && $valor !== null) {
            $where[] = "i.$item = :item_valor";
            $params[':item_valor'] = $valor;
        } else {
            // Filtro por almacén
            if ($filtroAlmacen !== null && $filtroAlmacen !== '') {
                $where[] = "i.id_almacen = :id_almacen";
                $params[':id_almacen'] = (int)$filtroAlmacen;
            }

            // Filtro por producto
            if ($filtroProducto !== null && $filtroProducto !== '') {
                $where[] = "i.id_producto = :id_producto";
                $params[':id_producto'] = (int)$filtroProducto;
            }

            // Filtro por estado de stock
            if ($filtroEstado !== null && $filtroEstado !== '') {
                switch (strtolower($filtroEstado)) {
                    case "bajo_minimo":
                        $where[] = "i.stock <= i.stock_minimo AND i.stock_minimo > 0";
                        break;
                    case "sobre_maximo":
                        $where[] = "i.stock >= i.stock_maximo AND i.stock_maximo > 0";
                        break;
                    case "normal":
                        $where[] = "(i.stock > i.stock_minimo OR i.stock_minimo = 0) AND (i.stock < i.stock_maximo OR i.stock_maximo = 0)";
                        break;
                }
            }
        }

        $sql = "SELECT i.*, p.nombre as nombre_producto, p.codigo as codigo_producto, 
               a.nombre as nombre_almacen 
               FROM $tabla i
               INNER JOIN productos p ON i.id_producto = p.id_producto
               INNER JOIN almacenes a ON i.id_almacen = a.id_almacen";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY a.nombre, p.nombre";

        $stmt = Conexion::conectar()->prepare($sql);

        // Bind de parámetros
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }

        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode([
            "status" => true,
            "data" => $resultados,
            "sql" => $sql, // Para depuración
            "params" => $params // Para depuración
        ]);

    } catch (Exception $e) {
        return json_encode([
            "status" => false,
            "message" => $e->getMessage(),
            "sql" => $sql ?? '',
            "params" => $params ?? []
        ]);
    }
}

    /*=============================================
    CONSULTAR INVENTARIO (Producto + Almacén)
    =============================================*/
    static public function mdlConsultarInventario($tabla, $item1, $valor1, $item2, $valor2)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT i.* FROM $tabla i
                WHERE i.$item1 = :$item1 AND i.$item2 = :$item2"
            );

            $stmt->bindParam(":$item1", $valor1, PDO::PARAM_INT);
            $stmt->bindParam(":$item2", $valor2, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                return json_encode([
                    "status" => true,
                    "data" => $resultado,
                    "id_inventario" => $resultado['id_inventario']
                ]);
            } else {
                // Si no existe registro, creamos uno con stock 0
                $stmt = Conexion::conectar()->prepare(
                    "INSERT INTO $tabla (id_producto, id_almacen, stock, stock_minimo, stock_maximo) 
                    VALUES (:id_producto, :id_almacen, 0, 0, 0)"
                );

                $stmt->bindParam(":id_producto", $valor1, PDO::PARAM_INT);
                $stmt->bindParam(":id_almacen", $valor2, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $idInventario = Conexion::conectar()->lastInsertId();

                    // Obtenemos el registro recién creado usando los mismos parámetros originales
                    $stmt = Conexion::conectar()->prepare(
                        "SELECT * FROM $tabla 
                        WHERE id_producto = :id_producto AND id_almacen = :id_almacen"
                    );

                    $stmt->bindParam(":id_producto", $valor1, PDO::PARAM_INT);
                    $stmt->bindParam(":id_almacen", $valor2, PDO::PARAM_INT);
                    $stmt->execute();

                    $nuevoRegistro = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($nuevoRegistro) {
                        return json_encode([
                            "status" => true,
                            "data" => $nuevoRegistro,
                            "id_inventario" => $nuevoRegistro['id_inventario'],
                            "nuevo_registro" => true
                        ]);
                    } else {
                        return json_encode([
                            "status" => false,
                            "message" => "Registro creado pero no se pudo recuperar",
                            "id_inventario" => $idInventario
                        ]);
                    }
                } else {
                    return json_encode([
                        "status" => false,
                        "message" => "Error al crear registro de inventario"
                    ]);
                }
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    AJUSTAR INVENTARIO
    =============================================*/
    static public function mdlAjustarInventario($tabla, $datos)
    {
        $conexion = null;
        try {
            // Iniciar transacción
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();

            // 1. Obtener el inventario actual
            $stmt = $conexion->prepare("SELECT * FROM $tabla WHERE id_producto = :id_producto AND id_almacen = :id_almacen");
            $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);
            $stmt->bindParam(":id_almacen", $datos["id_almacen"], PDO::PARAM_INT);
            $stmt->execute();

            $inventario = $stmt->fetch(PDO::FETCH_ASSOC);
            $stockAnterior = $inventario ? (float)$inventario['stock'] : 0;
            $stockNuevo = $stockAnterior;

            // Validar tipo de movimiento
            if (!in_array($datos["tipo_movimiento"], ['entrada', 'salida', 'ajuste'])) {
                throw new Exception("Tipo de movimiento no válido");
            }

            // Calcular nuevo stock según tipo de movimiento
            switch ($datos["tipo_movimiento"]) {
                case 'entrada':
                    $stockNuevo += $datos["cantidad"];
                    break;
                case 'salida':
                    $stockNuevo -= $datos["cantidad"];
                    if ($stockNuevo < 0) {
                        throw new Exception("No hay suficiente stock para esta salida");
                    }
                    break;
                case 'ajuste':
                    $stockNuevo = $datos["cantidad"];
                    break;
            }

            // 2. Actualizar o insertar registro de inventario
            if ($inventario) {
                $sql = "UPDATE $tabla SET 
                    stock = :stock,
                    stock_minimo = COALESCE(:stock_minimo, stock_minimo),
                    stock_maximo = COALESCE(:stock_maximo, stock_maximo),
                    ultima_actualizacion = NOW()
                WHERE id_inventario = :id_inventario";

                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(":stock", $stockNuevo, PDO::PARAM_STR);
                $stmt->bindParam(":stock_minimo", $datos["stock_minimo"]);
                $stmt->bindParam(":stock_maximo", $datos["stock_maximo"]);
                $stmt->bindParam(":id_inventario", $inventario['id_inventario'], PDO::PARAM_INT);
            } else {
                $sql = "INSERT INTO $tabla (
                    id_producto, id_almacen, stock, 
                    stock_minimo, stock_maximo
                ) VALUES (
                    :id_producto, :id_almacen, :stock,
                    :stock_minimo, :stock_maximo
                )";

                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);
                $stmt->bindParam(":id_almacen", $datos["id_almacen"], PDO::PARAM_INT);
                $stmt->bindParam(":stock", $stockNuevo, PDO::PARAM_STR);
                $stmt->bindValue(":stock_minimo", $datos["stock_minimo"], $datos["stock_minimo"] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->bindValue(":stock_maximo", $datos["stock_maximo"], $datos["stock_maximo"] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el inventario: " . implode(", ", $stmt->errorInfo()));
            }

            // 3. Registrar el movimiento en el historial
            $idInventario = $inventario ? $inventario['id_inventario'] : $conexion->lastInsertId();

            $stmt = $conexion->prepare(
                "INSERT INTO movimientos_inventario (
                id_inventario, tipo_movimiento, cantidad,
                stock_anterior, stock_nuevo, motivo, id_usuario
            ) VALUES (
                :id_inventario, :tipo_movimiento, :cantidad,
                :stock_anterior, :stock_nuevo, :motivo, :id_usuario
            )"
            );

            $stmt->bindParam(":id_inventario", $idInventario, PDO::PARAM_INT);
            $stmt->bindParam(":tipo_movimiento", $datos["tipo_movimiento"], PDO::PARAM_STR);
            $stmt->bindParam(":cantidad", $datos["cantidad"], PDO::PARAM_STR);
            $stmt->bindParam(":stock_anterior", $stockAnterior, PDO::PARAM_STR);
            $stmt->bindParam(":stock_nuevo", $stockNuevo, PDO::PARAM_STR);
            $stmt->bindValue(":motivo", $datos["motivo"], $datos["motivo"] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Error al registrar el movimiento: " . implode(", ", $stmt->errorInfo()));
            }

            // Confirmar transacción
            $conexion->commit();

            return json_encode([
                "status" => true,
                "message" => "Inventario actualizado correctamente",
                "stock_actual" => $stockNuevo,
                "id_inventario" => $idInventario
            ]);
        } catch (Exception $e) {
            if ($conexion) {
                $conexion->rollBack();
            }
            return json_encode([
                "status" => false,
                "message" => $e->getMessage(),
                "error_details" => isset($stmt) ? $stmt->errorInfo() : null
            ]);
        } finally {
            if ($conexion) {
                $conexion = null; // Cerrar conexión
            }
        }
    }

    /*=============================================
    HISTORIAL DE INVENTARIO
    =============================================*/
    static public function mdlHistorialInventario($tabla, $item, $valor)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT m.*, u.nombre_usuario as nombre_usuario 
                FROM $tabla m
                LEFT JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE m.$item = :$item
                ORDER BY m.fecha DESC"
            );

            $stmt->bindParam(":$item", $valor, PDO::PARAM_INT);
            $stmt->execute();

            return json_encode([
                "status" => true,
                "data" => $stmt->fetchAll()
            ]);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
