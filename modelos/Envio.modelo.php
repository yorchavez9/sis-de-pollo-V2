<?php

include "Conexion.php";

class ModeloEnvio
{
    /*=============================================
    CREAR ENVÍO
    =============================================*/
    static public function mdlCrearEnvio($tabla, $datos)
    {
        try {
            $pdo = Conexion::conectar();
            $pdo->beginTransaction();

            // Insertar envío principal
            $stmt = $pdo->prepare("INSERT INTO $tabla (
                codigo_envio, id_sucursal_origen, id_sucursal_destino, id_tipo_encomienda,
                id_usuario_creador, id_transportista, dni_remitente, nombre_remitente,
                dni_destinatario, nombre_destinatario, clave_recepcion, fecha_creacion,
                fecha_estimada_entrega, peso_total, volumen_total, cantidad_paquetes,
                instrucciones, costo_envio, metodo_pago, estado
            ) VALUES (
                :codigo_envio, :id_sucursal_origen, :id_sucursal_destino, :id_tipo_encomienda,
                :id_usuario_creador, :id_transportista, :dni_remitente, :nombre_remitente,
                :dni_destinatario, :nombre_destinatario, :clave_recepcion, NOW(),
                :fecha_estimada_entrega, :peso_total, :volumen_total, :cantidad_paquetes,
                :instrucciones, :costo_envio, :metodo_pago, 'PENDIENTE'
            )");

            $stmt->bindParam(":codigo_envio", $datos['codigo_envio'], PDO::PARAM_STR);
            $stmt->bindParam(":id_sucursal_origen", $datos['id_sucursal_origen'], PDO::PARAM_INT);
            $stmt->bindParam(":id_sucursal_destino", $datos['id_sucursal_destino'], PDO::PARAM_INT);
            $stmt->bindParam(":id_tipo_encomienda", $datos['id_tipo_encomienda'], PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario_creador", $datos['id_usuario_creador'], PDO::PARAM_INT);
            $stmt->bindParam(":id_transportista", $datos['id_transportista'], PDO::PARAM_INT);
            $stmt->bindParam(":dni_remitente", $datos['dni_remitente'], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_remitente", $datos['nombre_remitente'], PDO::PARAM_STR);
            $stmt->bindParam(":dni_destinatario", $datos['dni_destinatario'], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_destinatario", $datos['nombre_destinatario'], PDO::PARAM_STR);
            $stmt->bindParam(":clave_recepcion", $datos['clave_recepcion'], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_estimada_entrega", $datos['fecha_estimada_entrega'], PDO::PARAM_STR);
            $stmt->bindParam(":peso_total", $datos['peso_total'], PDO::PARAM_STR);
            $stmt->bindParam(":volumen_total", $datos['volumen_total'], PDO::PARAM_STR);
            $stmt->bindParam(":cantidad_paquetes", $datos['cantidad_paquetes'], PDO::PARAM_INT);
            $stmt->bindParam(":instrucciones", $datos['instrucciones'], PDO::PARAM_STR);
            $stmt->bindParam(":costo_envio", $datos['costo_envio'], PDO::PARAM_STR);
            $stmt->bindParam(":metodo_pago", $datos['metodo_pago'], PDO::PARAM_STR);

            if (!$stmt->execute()) {
                $pdo->rollBack();
                return ["status" => false, "message" => "Error al crear el envío"];
            }

            $idEnvio = $pdo->lastInsertId();

            // Insertar paquetes
            foreach ($datos['paquetes'] as $paquete) {
                $stmtPaquete = $pdo->prepare("INSERT INTO paquetes (
                    id_envio, codigo_paquete, descripcion, peso, alto, ancho, profundidad,
                    valor_declarado, instrucciones_manejo, estado
                ) VALUES (
                    :id_envio, :codigo_paquete, :descripcion, :peso, :alto, :ancho, :profundidad,
                    :valor_declarado, :instrucciones_manejo, 'BUENO'
                )");

                $codigoPaquete = "PKG" . substr($datos['codigo_envio'], 3) . "-" . str_pad($paquete['numero'], 3, '0', STR_PAD_LEFT);

                $stmtPaquete->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
                $stmtPaquete->bindParam(":codigo_paquete", $codigoPaquete, PDO::PARAM_STR);
                $stmtPaquete->bindParam(":descripcion", $paquete['descripcion'], PDO::PARAM_STR);
                $stmtPaquete->bindParam(":peso", $paquete['peso'], PDO::PARAM_STR);
                $stmtPaquete->bindParam(":alto", $paquete['alto'], PDO::PARAM_STR);
                $stmtPaquete->bindParam(":ancho", $paquete['ancho'], PDO::PARAM_STR);
                $stmtPaquete->bindParam(":profundidad", $paquete['profundidad'], PDO::PARAM_STR);
                $stmtPaquete->bindParam(":valor_declarado", $paquete['valor_declarado'] ?? 0, PDO::PARAM_STR);
                $stmtPaquete->bindParam(":instrucciones_manejo", $paquete['instrucciones'], PDO::PARAM_STR);

                if (!$stmtPaquete->execute()) {
                    $pdo->rollBack();
                    return ["status" => false, "message" => "Error al registrar paquetes"];
                }

                $idPaquete = $pdo->lastInsertId();

                // Insertar items del paquete
                foreach ($paquete['items'] as $item) {
                    $stmtItem = $pdo->prepare("INSERT INTO items_paquete (
                        id_paquete, id_producto, descripcion, cantidad, peso_unitario, valor_unitario, observaciones
                    ) VALUES (
                        :id_paquete, :id_producto, :descripcion, :cantidad, :peso_unitario, :valor_unitario, :observaciones
                    )");

                    $stmtItem->bindParam(":id_paquete", $idPaquete, PDO::PARAM_INT);
                    $stmtItem->bindParam(":id_producto", $item['id_producto'], PDO::PARAM_INT);
                    $stmtItem->bindParam(":descripcion", $item['descripcion'], PDO::PARAM_STR);
                    $stmtItem->bindParam(":cantidad", $item['cantidad'], PDO::PARAM_INT);
                    $stmtItem->bindParam(":peso_unitario", $item['peso_unitario'], PDO::PARAM_STR);
                    $stmtItem->bindParam(":valor_unitario", $item['valor_unitario'], PDO::PARAM_STR);
                    $stmtItem->bindParam(":observaciones", $item['observaciones'] ?? null, PDO::PARAM_STR);

                    if (!$stmtItem->execute()) {
                        $pdo->rollBack();
                        return ["status" => false, "message" => "Error al registrar items del paquete"];
                    }
                }
            }

            // Registrar primer seguimiento
            $stmtSeguimiento = $pdo->prepare("INSERT INTO seguimiento_envios (
                id_envio, id_usuario, estado_anterior, estado_nuevo, ubicacion, observaciones
            ) VALUES (
                :id_envio, :id_usuario, NULL, 'PENDIENTE', NULL, 'Envío creado'
            )");

            $stmtSeguimiento->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(":id_usuario", $datos['id_usuario_creador'], PDO::PARAM_INT);

            if (!$stmtSeguimiento->execute()) {
                $pdo->rollBack();
                return ["status" => false, "message" => "Error al registrar seguimiento"];
            }

            $pdo->commit();
            return ["status" => true, "message" => "Envío registrado con éxito", "id_envio" => $idEnvio];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    /*=============================================
    MOSTRAR ENVÍOS
    =============================================*/
    static public function mdlMostrarEnvios($tabla, $item, $valor)
    {
        try {
            $where = $item ? "WHERE e.$item = :$item" : "";
            $sql = "SELECT 
                    e.id_envio, e.codigo_envio, 
                    so.nombre as sucursal_origen, 
                    sd.nombre as sucursal_destino,
                    te.nombre as tipo_encomienda,
                    CONCAT(p.nombre, ' ', p.apellidos) as transportista,
                    e.fecha_creacion, e.fecha_envio, e.fecha_recepcion,
                    e.peso_total, e.volumen_total, e.cantidad_paquetes,
                    e.estado, e.costo_envio
                FROM $tabla e
                LEFT JOIN sucursales so ON e.id_sucursal_origen = so.id_sucursal
                LEFT JOIN sucursales sd ON e.id_sucursal_destino = sd.id_sucursal
                LEFT JOIN tipo_encomiendas te ON e.id_tipo_encomienda = te.id_tipo_encomienda
                LEFT JOIN transportistas t ON e.id_transportista = t.id_transportista
                LEFT JOIN personas p ON t.id_persona = p.id_persona
                $where
                ORDER BY e.fecha_creacion DESC";

            $stmt = Conexion::conectar()->prepare($sql);
            
            if ($item) {
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            
            if ($item) {
                return $stmt->fetch();
            } else {
                return [
                    "status" => true,
                    "data" => $stmt->fetchAll()
                ];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    /*=============================================
    MOSTRAR DETALLE DE ENVÍO
    =============================================*/
    static public function mdlMostrarDetalleEnvio($idEnvio)
    {
        try {
            // Información básica del envío
            $stmtEnvio = Conexion::conectar()->prepare(
                "SELECT 
                    e.*,
                    so.nombre as sucursal_origen, 
                    sd.nombre as sucursal_destino,
                    te.nombre as tipo_encomienda,
                    CONCAT(p.nombre, ' ', p.apellidos) as transportista,
                    CONCAT(u.nombre_usuario, ' (', u.usuario, ')') as usuario_creador,
                    CONCAT(ur.nombre_usuario, ' (', ur.usuario, ')') as usuario_receptor
                FROM envios e
                LEFT JOIN sucursales so ON e.id_sucursal_origen = so.id_sucursal
                LEFT JOIN sucursales sd ON e.id_sucursal_destino = sd.id_sucursal
                LEFT JOIN tipo_encomiendas te ON e.id_tipo_encomienda = te.id_tipo_encomienda
                LEFT JOIN transportistas t ON e.id_transportista = t.id_transportista
                LEFT JOIN personas p ON t.id_persona = p.id_persona
                LEFT JOIN usuarios u ON e.id_usuario_creador = u.id_usuario
                LEFT JOIN usuarios ur ON e.id_usuario_receptor = ur.id_usuario
                WHERE e.id_envio = :id_envio"
            );
            $stmtEnvio->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtEnvio->execute();
            $envio = $stmtEnvio->fetch();

            if (!$envio) {
                return ["status" => false, "message" => "Envío no encontrado"];
            }

            // Paquetes del envío
            $stmtPaquetes = Conexion::conectar()->prepare(
                "SELECT * FROM paquetes WHERE id_envio = :id_envio"
            );
            $stmtPaquetes->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtPaquetes->execute();
            $paquetes = $stmtPaquetes->fetchAll();

            // Items de cada paquete
            foreach ($paquetes as &$paquete) {
                $stmtItems = Conexion::conectar()->prepare(
                    "SELECT 
                        ip.*,
                        p.codigo as codigo_producto,
                        p.nombre as nombre_producto
                    FROM items_paquete ip
                    LEFT JOIN productos p ON ip.id_producto = p.id_producto
                    WHERE ip.id_paquete = :id_paquete"
                );
                $stmtItems->bindParam(":id_paquete", $paquete['id_paquete'], PDO::PARAM_INT);
                $stmtItems->execute();
                $paquete['items'] = $stmtItems->fetchAll();
            }

            // Seguimiento del envío
            $stmtSeguimiento = Conexion::conectar()->prepare(
                "SELECT 
                    s.*,
                    CONCAT(u.nombre_usuario, ' (', u.usuario, ')') as usuario
                FROM seguimiento_envios s
                LEFT JOIN usuarios u ON s.id_usuario = u.id_usuario
                WHERE s.id_envio = :id_envio
                ORDER BY s.fecha_registro DESC"
            );
            $stmtSeguimiento->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtSeguimiento->execute();
            $seguimiento = $stmtSeguimiento->fetchAll();

            // Documentos del envío
            $stmtDocumentos = Conexion::conectar()->prepare(
                "SELECT 
                    d.*,
                    CONCAT(u.nombre_usuario, ' (', u.usuario, ')') as usuario
                FROM documentos_envios d
                LEFT JOIN usuarios u ON d.id_usuario = u.id_usuario
                WHERE d.id_envio = :id_envio
                ORDER BY d.fecha_subida DESC"
            );
            $stmtDocumentos->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtDocumentos->execute();
            $documentos = $stmtDocumentos->fetchAll();

            return [
                "status" => true,
                "data" => [
                    "envio" => $envio,
                    "paquetes" => $paquetes,
                    "seguimiento" => $seguimiento,
                    "documentos" => $documentos
                ]
            ];
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    /*=============================================
    CAMBIAR ESTADO DE ENVÍO
    =============================================*/
    static public function mdlCambiarEstadoEnvio($datos)
    {
        try {
            $pdo = Conexion::conectar();
            $pdo->beginTransaction();

            // Obtener estado actual
            $stmtEstadoActual = $pdo->prepare("SELECT estado FROM envios WHERE id_envio = :id_envio");
            $stmtEstadoActual->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
            $stmtEstadoActual->execute();
            $estadoActual = $stmtEstadoActual->fetchColumn();

            // Actualizar estado del envío
            $stmt = $pdo->prepare("UPDATE envios SET estado = :estado WHERE id_envio = :id_envio");
            $stmt->bindParam(":estado", $datos['estado'], PDO::PARAM_STR);
            $stmt->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);

            if (!$stmt->execute()) {
                $pdo->rollBack();
                return ["status" => false, "message" => "Error al actualizar el estado del envío"];
            }

            // Registrar seguimiento
            $stmtSeguimiento = $pdo->prepare("INSERT INTO seguimiento_envios (
                id_envio, id_usuario, estado_anterior, estado_nuevo, ubicacion, observaciones
            ) VALUES (
                :id_envio, :id_usuario, :estado_anterior, :estado_nuevo, NULL, :observaciones
            )");

            $stmtSeguimiento->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(":id_usuario", $datos['id_usuario'], PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(":estado_anterior", $estadoActual, PDO::PARAM_STR);
            $stmtSeguimiento->bindParam(":estado_nuevo", $datos['estado'], PDO::PARAM_STR);
            $stmtSeguimiento->bindParam(":observaciones", $datos['observaciones'], PDO::PARAM_STR);

            if (!$stmtSeguimiento->execute()) {
                $pdo->rollBack();
                return ["status" => false, "message" => "Error al registrar el seguimiento"];
            }

            // Si el estado es ENTREGADO, registrar fecha de recepción y usuario receptor
            if ($datos['estado'] == 'ENTREGADO') {
                $stmtRecepcion = $pdo->prepare("UPDATE envios SET 
                    fecha_recepcion = NOW(),
                    id_usuario_receptor = :id_usuario
                    WHERE id_envio = :id_envio");

                $stmtRecepcion->bindParam(":id_usuario", $datos['id_usuario'], PDO::PARAM_INT);
                $stmtRecepcion->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);

                if (!$stmtRecepcion->execute()) {
                    $pdo->rollBack();
                    return ["status" => false, "message" => "Error al registrar la recepción"];
                }
            }

            // Si el estado es EN_TRANSITO, registrar fecha de envío
            if ($datos['estado'] == 'EN_TRANSITO') {
                $stmtEnvio = $pdo->prepare("UPDATE envios SET 
                    fecha_envio = NOW()
                    WHERE id_envio = :id_envio");

                $stmtEnvio->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);

                if (!$stmtEnvio->execute()) {
                    $pdo->rollBack();
                    return ["status" => false, "message" => "Error al registrar el envío"];
                }
            }

            $pdo->commit();
            return ["status" => true, "message" => "Estado del envío actualizado"];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    /*=============================================
    SUBIR DOCUMENTO DE ENVÍO
    =============================================*/
    static public function mdlSubirDocumentoEnvio($datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO documentos_envios (
                id_envio, tipo_documento, nombre_archivo, ruta_archivo, 
                descripcion, id_usuario, fecha_subida
            ) VALUES (
                :id_envio, :tipo_documento, :nombre_archivo, :ruta_archivo,
                :descripcion, :id_usuario, NOW()
            )");

            $stmt->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
            $stmt->bindParam(":tipo_documento", $datos['tipo_documento'], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_archivo", $datos['nombre_archivo'], PDO::PARAM_STR);
            $stmt->bindParam(":ruta_archivo", $datos['ruta_archivo'], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(":id_usuario", $datos['id_usuario'], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ["status" => true, "message" => "Documento subido correctamente"];
            } else {
                return ["status" => false, "message" => "Error al registrar el documento"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    /*=============================================
    ELIMINAR DOCUMENTO DE ENVÍO
    =============================================*/
    static public function mdlEliminarDocumentoEnvio($idDocumento)
    {
        try {
            // Obtener ruta del archivo
            $stmt = Conexion::conectar()->prepare("SELECT ruta_archivo FROM documentos_envios WHERE id_documento = :id_documento");
            $stmt->bindParam(":id_documento", $idDocumento, PDO::PARAM_INT);
            $stmt->execute();
            $rutaArchivo = $stmt->fetchColumn();

            // Eliminar registro de la base de datos
            $stmt = Conexion::conectar()->prepare("DELETE FROM documentos_envios WHERE id_documento = :id_documento");
            $stmt->bindParam(":id_documento", $idDocumento, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Eliminar archivo físico
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
                return ["status" => true, "message" => "Documento eliminado correctamente"];
            } else {
                return ["status" => false, "message" => "Error al eliminar el documento"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    /*=============================================
    CALCULAR COSTO DE ENVÍO
    =============================================*/
    static public function mdlCalcularCostoEnvio($origen, $destino, $tipo, $peso)
    {
        try {
            // Buscar tarifa aplicable
            $stmt = Conexion::conectar()->prepare(
                "SELECT * FROM tarifas_envio 
                WHERE id_sucursal_origen = :origen 
                AND id_sucursal_destino = :destino
                AND id_tipo_encomienda = :tipo
                AND :peso BETWEEN rango_peso_min AND rango_peso_max
                AND (vigencia_hasta IS NULL OR vigencia_hasta >= CURDATE())
                AND estado = 1
                ORDER BY vigencia_desde DESC
                LIMIT 1"
            );

            $stmt->bindParam(":origen", $origen, PDO::PARAM_INT);
            $stmt->bindParam(":destino", $destino, PDO::PARAM_INT);
            $stmt->bindParam(":tipo", $tipo, PDO::PARAM_INT);
            $stmt->bindParam(":peso", $peso, PDO::PARAM_STR);
            $stmt->execute();
            $tarifa = $stmt->fetch();

            if ($tarifa) {
                // Calcular costo basado en la tarifa
                $costo = $tarifa['costo_base'];
                
                // Si el peso excede el rango base, calcular costo adicional
                if ($peso > $tarifa['rango_peso_max']) {
                    $pesoExcedente = $peso - $tarifa['rango_peso_max'];
                    $costo += ceil($pesoExcedente) * $tarifa['costo_kg_extra'];
                }

                return [
                    "status" => true,
                    "data" => [
                        "costo" => $costo,
                        "tiempo_estimado" => $tarifa['tiempo_estimado']
                    ]
                ];
            } else {
                return [
                    "status" => false,
                    "message" => "No se encontró tarifa aplicable para este envío"
                ];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }
}
?>