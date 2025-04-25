<?php

include "Conexion.php";

class ModeloEnvio
{
    /*=============================================
    CREAR ENVÍO
    =============================================*/

    static public function mdlCrearEnvio($tabla, $datos)
    {
        $pdo = null;

        try {
            $pdo = Conexion::conectar();
            $pdo->beginTransaction();

            /*===== 1. OBTENER COMPROBANTE Y NÚMERO ACTUAL =====*/
            $stmtSerie = $pdo->prepare("SELECT * FROM series_comprobantes WHERE id_serie = :id_serie");
            $stmtSerie->bindParam(":id_serie", $datos['id_serie'], PDO::PARAM_INT);
            $stmtSerie->execute();
            $serie = $stmtSerie->fetch(PDO::FETCH_ASSOC);

            if (!$serie) {
                throw new Exception("Serie de comprobante no encontrada");
            }

            $numero_comprobante = $serie['numero_actual'] + 1;

            /*===== 2. INSERTAR ENVÍO PRINCIPAL =====*/
            $stmtEnvio = $pdo->prepare("INSERT INTO $tabla (
                codigo_envio, id_serie, numero_comprobante, id_sucursal_origen, id_sucursal_destino, 
                id_tipo_encomienda, id_usuario_creador, id_transportista, dni_remitente, nombre_remitente, 
                dni_destinatario, nombre_destinatario, clave_recepcion, fecha_estimada_entrega, 
                peso_total, volumen_total, cantidad_paquetes, instrucciones, costo_envio, metodo_pago, estado
            ) VALUES (
                :codigo_envio, :id_serie, :numero_comprobante, :id_sucursal_origen, :id_sucursal_destino, 
                :id_tipo_encomienda, :id_usuario_creador, :id_transportista, :dni_remitente, :nombre_remitente, 
                :dni_destinatario, :nombre_destinatario, :clave_recepcion, :fecha_estimada_entrega, 
                :peso_total, :volumen_total, :cantidad_paquetes, :instrucciones, :costo_envio, :metodo_pago, 'PENDIENTE'
            )");

            $paramsEnvio = [
                ':codigo_envio' => $datos['codigo_envio'],
                ':id_serie' => $datos['id_serie'],
                ':numero_comprobante' => $numero_comprobante,
                ':id_sucursal_origen' => $datos['id_sucursal_origen'],
                ':id_sucursal_destino' => $datos['id_sucursal_destino'],
                ':id_tipo_encomienda' => $datos['id_tipo_encomienda'],
                ':id_usuario_creador' => $datos['id_usuario_creador'],
                ':id_transportista' => $datos['id_transportista'] ?? null,
                ':dni_remitente' => $datos['dni_remitente'] ?? null,
                ':nombre_remitente' => $datos['nombre_remitente'] ?? null,
                ':dni_destinatario' => $datos['dni_destinatario'] ?? null,
                ':nombre_destinatario' => $datos['nombre_destinatario'] ?? null,
                ':clave_recepcion' => $datos['clave_recepcion'] ?? null,
                ':fecha_estimada_entrega' => $datos['fecha_estimada_entrega'] ?? null,
                ':peso_total' => $datos['peso_total'] ?? 0,
                ':volumen_total' => $datos['volumen_total'] ?? 0,
                ':cantidad_paquetes' => $datos['cantidad_paquetes'] ?? 1,
                ':instrucciones' => $datos['instrucciones'] ?? null,
                ':costo_envio' => $datos['costo_envio'] ?? 0,
                ':metodo_pago' => $datos['metodo_pago'] ?? 'EFECTIVO'
            ];

            if (!$stmtEnvio->execute($paramsEnvio)) {
                throw new Exception("Error al crear envío: " . implode(' | ', $stmtEnvio->errorInfo()));
            }

            $idEnvio = $pdo->lastInsertId();

            /*===== 3. INSERTAR PAQUETES Y SUS ÍTEMS =====*/
            if (!empty($datos['paquetes']) && is_array($datos['paquetes'])) {
                foreach ($datos['paquetes'] as $paquete) {

                    if (empty($paquete['descripcion']) || empty($paquete['peso'])) {
                        throw new Exception("Datos del paquete incompletos");
                    }

                    $codigoPaquete = "PKG" . substr($datos['codigo_envio'], 3) . "-" .
                        str_pad($paquete['numero'], 3, '0', STR_PAD_LEFT);

                    $volumen = $paquete['volumen'] ?? (
                        ($paquete['alto'] ?? 0) * ($paquete['ancho'] ?? 0) * ($paquete['profundidad'] ?? 0)
                    );

                    $stmtPaquete = $pdo->prepare("INSERT INTO paquetes (
                        id_envio, codigo_paquete, descripcion, peso, alto, ancho, profundidad,
                        volumen, valor_declarado, instrucciones_manejo, estado
                    ) VALUES (
                        :id_envio, :codigo_paquete, :descripcion, :peso, :alto, :ancho, :profundidad,
                        :volumen, :valor_declarado, :instrucciones_manejo, 'BUENO'
                    )");

                    $paramsPaquete = [
                        ':id_envio' => $idEnvio,
                        ':codigo_paquete' => $codigoPaquete,
                        ':descripcion' => $paquete['descripcion'],
                        ':peso' => $paquete['peso'],
                        ':alto' => $paquete['alto'] ?? 0,
                        ':ancho' => $paquete['ancho'] ?? 0,
                        ':profundidad' => $paquete['profundidad'] ?? 0,
                        ':volumen' => $volumen,
                        ':valor_declarado' => $paquete['valor_declarado'] ?? 0,
                        ':instrucciones_manejo' => $paquete['instrucciones'] ?? null
                    ];

                    if (!$stmtPaquete->execute($paramsPaquete)) {
                        throw new Exception("Error al registrar paquete: " . implode(' | ', $stmtPaquete->errorInfo()));
                    }

                    $idPaquete = $pdo->lastInsertId();

                    // Items dentro del paquete
                    if (!empty($paquete['items']) && is_array($paquete['items'])) {
                        foreach ($paquete['items'] as $item) {
                            if (empty($item['descripcion']) || empty($item['cantidad'])) {
                                continue;
                            }

                            $stmtItem = $pdo->prepare("INSERT INTO items_paquete (
                                id_paquete, id_producto, descripcion, cantidad, 
                                peso_unitario, valor_unitario, observaciones
                            ) VALUES (
                                :id_paquete, :id_producto, :descripcion, :cantidad, 
                                :peso_unitario, :valor_unitario, :observaciones
                            )");

                            $paramsItem = [
                                ':id_paquete' => $idPaquete,
                                ':id_producto' => $item['id_producto'] ?? null,
                                ':descripcion' => $item['descripcion'],
                                ':cantidad' => $item['cantidad'],
                                ':peso_unitario' => $item['peso_unitario'] ?? 0,
                                ':valor_unitario' => $item['valor_unitario'] ?? 0,
                                ':observaciones' => $item['observaciones'] ?? null
                            ];

                            if (!$stmtItem->execute($paramsItem)) {
                                throw new Exception("Error al registrar item: " . implode(' | ', $stmtItem->errorInfo()));
                            }
                        }
                    }
                }
            }

            /*===== 4. REGISTRAR SEGUIMIENTO =====*/
            $stmtSeguimiento = $pdo->prepare("INSERT INTO seguimiento_envios (
                id_envio, id_usuario, estado_anterior, estado_nuevo, ubicacion, observaciones
            ) VALUES (
                :id_envio, :id_usuario, NULL, 'PENDIENTE', NULL, 'Envío creado'
            )");

            if (!$stmtSeguimiento->execute([
                ':id_envio' => $idEnvio,
                ':id_usuario' => $datos['id_usuario_creador']
            ])) {
                throw new Exception("Error al registrar seguimiento: " . implode(' | ', $stmtSeguimiento->errorInfo()));
            }

            /*===== 5. ACTUALIZAR NÚMERO DE COMPROBANTE =====*/
            $stmtUpdateSerie = $pdo->prepare("UPDATE series_comprobantes SET numero_actual = :numero WHERE id_serie = :id_serie");
            $stmtUpdateSerie->bindParam(":numero", $numero_comprobante, PDO::PARAM_INT);
            $stmtUpdateSerie->bindParam(":id_serie", $datos['id_serie'], PDO::PARAM_INT);
            $stmtUpdateSerie->execute();

            $pdo->commit();

            return [
                'status' => true,
                'message' => 'Envío registrado con éxito',
                'id_envio' => $idEnvio,
                'codigo_envio' => $datos['codigo_envio']
            ];
        } catch (Exception $e) {
            if ($pdo && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return [
                'status' => false,
                'message' => 'Error al crear envío: ' . $e->getMessage(),
                'error_info' => $e->getTraceAsString()
            ];
        } finally {
            if ($pdo) {
                $pdo = null; // Cierra la conexión
            }
        }
    }


    /*=============================================
    MOSTRAR ENVÍOS
    =============================================*/
    static public function mdlMostrarEnvios($tabla, $item = null, $valor = null, $filtros = [])
    {
        try {
            // Construir la parte WHERE de la consulta
            $where = '';
            $conditions = [];
            $params = [];

            // Si se proporciona un item y valor individual
            if ($item && $valor) {
                $conditions[] = "e.$item = :$item";
                $params[":$item"] = $valor;
            }

            // Aplicar filtros adicionales
            if (!empty($filtros)) {
                if (isset($filtros['origen']) && $filtros['origen']) {
                    $conditions[] = "e.id_sucursal_origen = :origen";
                    $params[':origen'] = $filtros['origen'];
                }
                if (isset($filtros['codigo']) && $filtros['codigo']) {
                    $conditions[] = "e.codigo_envio = :codigo";
                    $params[':codigo'] = $filtros['codigo'];
                }                
                if (isset($filtros['destino']) && $filtros['destino']) {
                    $conditions[] = "e.id_sucursal_destino = :destino";
                    $params[':destino'] = $filtros['destino'];
                }
                if (isset($filtros['tipo']) && $filtros['tipo']) {
                    $conditions[] = "e.id_tipo_encomienda = :tipo";
                    $params[':tipo'] = $filtros['tipo'];
                }
                if (isset($filtros['estado']) && $filtros['estado']) {
                    $conditions[] = "e.estado = :estado";
                    $params[':estado'] = $filtros['estado'];
                }
            }

            // Combinar todas las condiciones
            if (!empty($conditions)) {
                $where = "WHERE " . implode(" AND ", $conditions);
            }

            $sql = "SELECT 
                e.id_envio, e.codigo_envio, 
                so.nombre as sucursal_origen, 
                e.id_sucursal_origen, 
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

            // Vincular todos los parámetros
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            if ($item && !empty($filtros)) {
                return $stmt->fetchAll();
            } elseif ($item) {
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
                    CONCAT(ur.nombre_usuario, ' (', ur.usuario, ')') as usuario_receptor,
                    sc.serie
                FROM envios e
                LEFT JOIN sucursales so ON e.id_sucursal_origen = so.id_sucursal
                LEFT JOIN sucursales sd ON e.id_sucursal_destino = sd.id_sucursal
                LEFT JOIN tipo_encomiendas te ON e.id_tipo_encomienda = te.id_tipo_encomienda
                LEFT JOIN transportistas t ON e.id_transportista = t.id_transportista
                LEFT JOIN personas p ON t.id_persona = p.id_persona
                LEFT JOIN usuarios u ON e.id_usuario_creador = u.id_usuario
                LEFT JOIN usuarios ur ON e.id_usuario_receptor = ur.id_usuario
                LEFT JOIN series_comprobantes sc ON sc.id_serie = e.id_serie
                WHERE e.id_envio = :id_envio OR e.codigo_envio = :codigo_envio"
            );
            $stmtEnvio->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtEnvio->bindParam(":codigo_envio", $idEnvio, PDO::PARAM_INT);
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
    MOSTRAR DETALLE DE ENVÍO API
    =============================================*/
    static public function mdlMostrarDetalleEnvioRastreo($codigo)
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
                    CONCAT(ur.nombre_usuario, ' (', ur.usuario, ')') as usuario_receptor,
                    sc.serie
                FROM envios e
                LEFT JOIN sucursales so ON e.id_sucursal_origen = so.id_sucursal
                LEFT JOIN sucursales sd ON e.id_sucursal_destino = sd.id_sucursal
                LEFT JOIN tipo_encomiendas te ON e.id_tipo_encomienda = te.id_tipo_encomienda
                LEFT JOIN transportistas t ON e.id_transportista = t.id_transportista
                LEFT JOIN personas p ON t.id_persona = p.id_persona
                LEFT JOIN usuarios u ON e.id_usuario_creador = u.id_usuario
                LEFT JOIN usuarios ur ON e.id_usuario_receptor = ur.id_usuario
                LEFT JOIN series_comprobantes sc ON sc.id_serie = e.id_serie
                WHERE e.clave_recepcion = :clave_recepcion OR e.codigo_envio = :codigo_envio"
            );
            $stmtEnvio->bindParam(":clave_recepcion", $codigo, PDO::PARAM_STR);
            $stmtEnvio->bindParam(":codigo_envio", $codigo, PDO::PARAM_STR);
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
            // Validar y convertir peso a decimal
            $peso = (float)str_replace(',', '.', $peso);
            if ($peso <= 0) {
                return ["status" => false, "message" => "El peso debe ser mayor a cero"];
            }

            // Buscar tarifa aplicable
            $stmt = Conexion::conectar()->prepare(
                "SELECT * FROM tarifas_envio 
                WHERE id_sucursal_origen = :origen 
                AND id_sucursal_destino = :destino
                AND id_tipo_encomienda = :tipo
                AND rango_peso_min <= :peso 
                AND (rango_peso_max >= :peso OR costo_kg_extra > 0)
                AND (vigencia_hasta IS NULL OR vigencia_hasta >= CURDATE())
                AND vigencia_desde <= CURDATE()
                AND estado = 1
                ORDER BY rango_peso_min DESC, vigencia_desde DESC
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
                $costo = (float)$tarifa['costo_base'];

                // Si el peso excede el rango base, calcular costo adicional
                if ($peso > $tarifa['rango_peso_min']) {
                    $pesoExcedente = $peso - $tarifa['rango_peso_min'];
                    $costo += $pesoExcedente * $tarifa['costo_kg_extra'];
                }

                // Redondear a 2 decimales
                $costo = round($costo, 2);

                return [
                    "status" => true,
                    "data" => [
                        "costo" => $costo,
                        "tiempo_estimado" => $tarifa['tiempo_estimado'] ?? 'No especificado'
                    ]
                ];
            } else {
                return [
                    "status" => false,
                    "message" => "No se encontró tarifa aplicable para la combinación seleccionada"
                ];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => "Error en el sistema: " . $e->getMessage()];
        }
    }
}
