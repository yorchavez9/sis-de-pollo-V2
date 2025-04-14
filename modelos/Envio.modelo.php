<?php
class ModeloEnvio {
    /*=============================================
    CREAR ENVÍO
    =============================================*/
    static public function mdlCrearEnvio($tabla, $datos) {
        $pdo = null;
        
        try {
            $pdo = Conexion::conectar();
            $pdo->beginTransaction();

            // 1. Obtener serie y número de comprobante
            $serie = self::getSerieComprobante($pdo, $datos['id_serie']);
            $numero_comprobante = $serie['numero_actual'] + 1;

            // 2. Insertar envío principal
            $idEnvio = self::insertEnvioPrincipal($pdo, $tabla, $datos, $numero_comprobante);

            // 3. Insertar paquetes e items
            self::insertPaquetes($pdo, $idEnvio, $datos['codigo_envio'], $datos['paquetes']);

            // 4. Registrar seguimiento inicial
            self::insertSeguimiento($pdo, $idEnvio, $datos['id_usuario_creador']);

            // 5. Actualizar número de comprobante
            self::updateNumeroComprobante($pdo, $datos['id_serie'], $numero_comprobante);

            $pdo->commit();

            return [
                'status' => true,
                'message' => 'Envío registrado con éxito',
                'data' => [
                    'id_envio' => $idEnvio,
                    'codigo_envio' => $datos['codigo_envio']
                ]
            ];
        } catch (Exception $e) {
            if ($pdo && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return [
                'status' => false,
                'message' => 'Error al crear envío: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    MOSTRAR ENVÍOS
    =============================================*/
    static public function mdlMostrarEnvios($tabla, $item = null, $valor = null, $filtros = []) {
        $pdo = null;
        
        try {
            $pdo = Conexion::conectar();
            
            // Construir consulta base
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
            LEFT JOIN personas p ON t.id_persona = p.id_persona";
            
            // Construir WHERE
            $where = [];
            $params = [];
            
            if ($item && $valor) {
                $where[] = "e.$item = :valor";
                $params[':valor'] = $valor;
            }
            
            // Aplicar filtros
            foreach (['origen', 'destino', 'tipo', 'estado'] as $filtro) {
                if (!empty($filtros[$filtro])) {
                    $where[] = "e." . ($filtro === 'tipo' ? 'id_tipo_encomienda' : "id_sucursal_$filtro") . " = :$filtro";
                    $params[":$filtro"] = $filtros[$filtro];
                }
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }
            
            $sql .= " ORDER BY e.fecha_creacion DESC";
            
            $stmt = $pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            
            $resultado = $item && empty($filtros) ? $stmt->fetch() : $stmt->fetchAll();
            
            return [
                'status' => true,
                'data' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Error al obtener envíos: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    MOSTRAR DETALLE DE ENVÍO
    =============================================*/
    static public function mdlMostrarDetalleEnvio($idEnvio) {
        $pdo = null;
        
        try {
            $pdo = Conexion::conectar();
            
            // 1. Información básica del envío
            $sqlEnvio = "SELECT 
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
            LEFT JOIN series_comprobantes sc ON e.id_serie = sc.id_serie
            WHERE e.id_envio = :id_envio";
            
            $stmtEnvio = $pdo->prepare($sqlEnvio);
            $stmtEnvio->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtEnvio->execute();
            $envio = $stmtEnvio->fetch();
            
            if (!$envio) {
                throw new Exception("Envío no encontrado");
            }
            
            // 2. Paquetes del envío
            $sqlPaquetes = "SELECT * FROM paquetes WHERE id_envio = :id_envio";
            $stmtPaquetes = $pdo->prepare($sqlPaquetes);
            $stmtPaquetes->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtPaquetes->execute();
            $paquetes = $stmtPaquetes->fetchAll();
            
            // 3. Items de cada paquete
            foreach ($paquetes as &$paquete) {
                $sqlItems = "SELECT 
                    ip.*,
                    p.codigo as codigo_producto,
                    p.nombre as nombre_producto
                FROM items_paquete ip
                LEFT JOIN productos p ON ip.id_producto = p.id_producto
                WHERE ip.id_paquete = :id_paquete";
                
                $stmtItems = $pdo->prepare($sqlItems);
                $stmtItems->bindParam(":id_paquete", $paquete['id_paquete'], PDO::PARAM_INT);
                $stmtItems->execute();
                $paquete['items'] = $stmtItems->fetchAll();
            }
            
            // 4. Seguimiento del envío
            $sqlSeguimiento = "SELECT 
                s.*,
                CONCAT(u.nombre_usuario, ' (', u.usuario, ')') as usuario
            FROM seguimiento_envios s
            LEFT JOIN usuarios u ON s.id_usuario = u.id_usuario
            WHERE s.id_envio = :id_envio
            ORDER BY s.fecha_registro DESC";
            
            $stmtSeguimiento = $pdo->prepare($sqlSeguimiento);
            $stmtSeguimiento->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtSeguimiento->execute();
            $seguimiento = $stmtSeguimiento->fetchAll();
            
            // 5. Documentos del envío
            $sqlDocumentos = "SELECT 
                d.*,
                CONCAT(u.nombre_usuario, ' (', u.usuario, ')') as usuario
            FROM documentos_envios d
            LEFT JOIN usuarios u ON d.id_usuario = u.id_usuario
            WHERE d.id_envio = :id_envio
            ORDER BY d.fecha_subida DESC";
            
            $stmtDocumentos = $pdo->prepare($sqlDocumentos);
            $stmtDocumentos->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
            $stmtDocumentos->execute();
            $documentos = $stmtDocumentos->fetchAll();
            
            return [
                'status' => true,
                'data' => [
                    'envio' => $envio,
                    'paquetes' => $paquetes,
                    'seguimiento' => $seguimiento,
                    'documentos' => $documentos
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    CAMBIAR ESTADO DE ENVÍO
    =============================================*/
    static public function mdlCambiarEstadoEnvio($datos) {
        $pdo = null;
        
        try {
            $pdo = Conexion::conectar();
            $pdo->beginTransaction();

            // 1. Obtener estado actual
            $sqlEstadoActual = "SELECT estado FROM envios WHERE id_envio = :id_envio";
            $stmtEstadoActual = $pdo->prepare($sqlEstadoActual);
            $stmtEstadoActual->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
            $stmtEstadoActual->execute();
            $estadoActual = $stmtEstadoActual->fetchColumn();

            // 2. Actualizar estado del envío
            $sqlUpdate = "UPDATE envios SET estado = :estado WHERE id_envio = :id_envio";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(":estado", $datos['estado'], PDO::PARAM_STR);
            $stmtUpdate->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
            
            if (!$stmtUpdate->execute()) {
                throw new Exception("Error al actualizar estado del envío");
            }

            // 3. Registrar seguimiento
            $sqlSeguimiento = "INSERT INTO seguimiento_envios (
                id_envio, id_usuario, estado_anterior, estado_nuevo, observaciones
            ) VALUES (
                :id_envio, :id_usuario, :estado_anterior, :estado_nuevo, :observaciones
            )";
            
            $stmtSeguimiento = $pdo->prepare($sqlSeguimiento);
            $stmtSeguimiento->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(":id_usuario", $datos['id_usuario'], PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(":estado_anterior", $estadoActual, PDO::PARAM_STR);
            $stmtSeguimiento->bindParam(":estado_nuevo", $datos['estado'], PDO::PARAM_STR);
            $stmtSeguimiento->bindParam(":observaciones", $datos['observaciones'], PDO::PARAM_STR);
            
            if (!$stmtSeguimiento->execute()) {
                throw new Exception("Error al registrar seguimiento");
            }

            // 4. Si el estado es ENTREGADO, registrar fecha de recepción
            if ($datos['estado'] == 'ENTREGADO') {
                $sqlRecepcion = "UPDATE envios SET 
                    fecha_recepcion = NOW(),
                    id_usuario_receptor = :id_usuario
                WHERE id_envio = :id_envio";
                
                $stmtRecepcion = $pdo->prepare($sqlRecepcion);
                $stmtRecepcion->bindParam(":id_usuario", $datos['id_usuario'], PDO::PARAM_INT);
                $stmtRecepcion->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
                
                if (!$stmtRecepcion->execute()) {
                    throw new Exception("Error al registrar recepción");
                }
            }

            // 5. Si el estado es EN_TRANSITO, registrar fecha de envío
            if ($datos['estado'] == 'EN_TRANSITO') {
                $sqlEnvio = "UPDATE envios SET fecha_envio = NOW() WHERE id_envio = :id_envio";
                $stmtEnvio = $pdo->prepare($sqlEnvio);
                $stmtEnvio->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
                
                if (!$stmtEnvio->execute()) {
                    throw new Exception("Error al registrar envío");
                }
            }

            $pdo->commit();
            
            return [
                'status' => true,
                'message' => 'Estado del envío actualizado correctamente'
            ];
        } catch (Exception $e) {
            if ($pdo && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return [
                'status' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    SUBIR DOCUMENTO DE ENVÍO
    =============================================*/
    static public function mdlSubirDocumentoEnvio($datos) {
        $pdo = null;
        
        try {
            $pdo = Conexion::conectar();
            
            $sql = "INSERT INTO documentos_envios (
                id_envio, tipo_documento, nombre_archivo, ruta_archivo, 
                descripcion, id_usuario, fecha_subida
            ) VALUES (
                :id_envio, :tipo_documento, :nombre_archivo, :ruta_archivo,
                :descripcion, :id_usuario, NOW()
            )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id_envio", $datos['id_envio'], PDO::PARAM_INT);
            $stmt->bindParam(":tipo_documento", $datos['tipo_documento'], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_archivo", $datos['nombre_archivo'], PDO::PARAM_STR);
            $stmt->bindParam(":ruta_archivo", $datos['ruta_archivo'], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(":id_usuario", $datos['id_usuario'], PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al registrar documento en la base de datos");
            }
            
            return [
                'status' => true,
                'message' => 'Documento subido correctamente',
                'data' => [
                    'id_documento' => $pdo->lastInsertId()
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Error al subir documento: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    ELIMINAR DOCUMENTO DE ENVÍO
    =============================================*/
    static public function mdlEliminarDocumentoEnvio($idDocumento) {
        $pdo = null;
        
        try {
            $pdo = Conexion::conectar();
            $pdo->beginTransaction();

            // 1. Obtener ruta del archivo
            $sqlSelect = "SELECT ruta_archivo FROM documentos_envios WHERE id_documento = :id_documento";
            $stmtSelect = $pdo->prepare($sqlSelect);
            $stmtSelect->bindParam(":id_documento", $idDocumento, PDO::PARAM_INT);
            $stmtSelect->execute();
            $rutaArchivo = $stmtSelect->fetchColumn();

            if (!$rutaArchivo) {
                throw new Exception("Documento no encontrado");
            }

            // 2. Eliminar registro de la base de datos
            $sqlDelete = "DELETE FROM documentos_envios WHERE id_documento = :id_documento";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->bindParam(":id_documento", $idDocumento, PDO::PARAM_INT);
            
            if (!$stmtDelete->execute()) {
                throw new Exception("Error al eliminar registro de documento");
            }

            // 3. Eliminar archivo físico
            if (file_exists($rutaArchivo)) {
                if (!unlink($rutaArchivo)) {
                    throw new Exception("Error al eliminar archivo físico");
                }
            }

            $pdo->commit();
            
            return [
                'status' => true,
                'message' => 'Documento eliminado correctamente'
            ];
        } catch (Exception $e) {
            if ($pdo && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return [
                'status' => false,
                'message' => 'Error al eliminar documento: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    CALCULAR COSTO DE ENVÍO
    =============================================*/
    static public function mdlCalcularCostoEnvio($origen, $destino, $tipo, $peso) {
        $pdo = null;
        
        try {
            $pdo = Conexion::conectar();
            
            // Validar que no sea la misma sucursal
            if ($origen == $destino) {
                return [
                    'status' => true,
                    'data' => [
                        'costo' => 0,
                        'tiempo_estimado' => '0 horas (misma sucursal)'
                    ]
                ];
            }
            
            // Buscar tarifa aplicable
            $sql = "SELECT * FROM tarifas_envio 
                   WHERE id_sucursal_origen = :origen 
                   AND id_sucursal_destino = :destino
                   AND id_tipo_encomienda = :tipo
                   AND rango_peso_min <= :peso 
                   AND (rango_peso_max >= :peso OR costo_kg_extra > 0)
                   AND (vigencia_hasta IS NULL OR vigencia_hasta >= CURDATE())
                   AND vigencia_desde <= CURDATE())
                   AND estado = 1
                   ORDER BY rango_peso_min DESC, vigencia_desde DESC
                   LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":origen", $origen, PDO::PARAM_INT);
            $stmt->bindParam(":destino", $destino, PDO::PARAM_INT);
            $stmt->bindParam(":tipo", $tipo, PDO::PARAM_INT);
            $stmt->bindParam(":peso", $peso, PDO::PARAM_STR);
            $stmt->execute();
            $tarifa = $stmt->fetch();

            if (!$tarifa) {
                throw new Exception("No se encontró tarifa aplicable para la combinación seleccionada");
            }

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
                'status' => true,
                'data' => [
                    'costo' => $costo,
                    'tiempo_estimado' => $tarifa['tiempo_estimado'] ?? 'No especificado'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Error al calcular costo: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    MÉTODOS PRIVADOS AUXILIARES
    =============================================*/
    private static function getSerieComprobante($pdo, $idSerie) {
        $stmt = $pdo->prepare("SELECT * FROM series_comprobantes WHERE id_serie = ?");
        $stmt->execute([$idSerie]);
        $serie = $stmt->fetch();
        
        if (!$serie) {
            throw new Exception("Serie de comprobante no encontrada");
        }
        
        return $serie;
    }

    private static function insertEnvioPrincipal($pdo, $tabla, $datos, $numeroComprobante) {
        $sql = "INSERT INTO $tabla (
            codigo_envio, id_serie, numero_comprobante, id_sucursal_origen, id_sucursal_destino, 
            id_tipo_encomienda, id_usuario_creador, id_transportista, dni_remitente, nombre_remitente, 
            dni_destinatario, nombre_destinatario, clave_recepcion, fecha_estimada_entrega, 
            peso_total, volumen_total, cantidad_paquetes, instrucciones, costo_envio, metodo_pago, estado
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDIENTE')";
        
        $params = [
            $datos['codigo_envio'],
            $datos['id_serie'],
            $numeroComprobante,
            $datos['id_sucursal_origen'],
            $datos['id_sucursal_destino'],
            $datos['id_tipo_encomienda'],
            $datos['id_usuario_creador'],
            $datos['id_transportista'],
            $datos['dni_remitente'],
            $datos['nombre_remitente'],
            $datos['dni_destinatario'],
            $datos['nombre_destinatario'],
            $datos['clave_recepcion'],
            $datos['fecha_estimada_entrega'],
            $datos['peso_total'],
            $datos['volumen_total'],
            $datos['cantidad_paquetes'],
            $datos['instrucciones'],
            $datos['costo_envio'],
            $datos['metodo_pago']
        ];
        
        $stmt = $pdo->prepare($sql);
        
        if (!$stmt->execute($params)) {
            throw new Exception("Error al crear envío principal");
        }
        
        return $pdo->lastInsertId();
    }

    private static function insertPaquetes($pdo, $idEnvio, $codigoEnvio, $paquetes) {
        foreach ($paquetes as $paquete) {
            if (empty($paquete['descripcion']) || empty($paquete['peso'])) {
                throw new Exception("Datos del paquete incompletos");
            }

            $codigoPaquete = "PKG" . substr($codigoEnvio, 3) . "-" .
                str_pad($paquete['numero'], 3, '0', STR_PAD_LEFT);

            $volumen = $paquete['volumen'] ?? (
                ($paquete['alto'] ?? 0) * ($paquete['ancho'] ?? 0) * ($paquete['profundidad'] ?? 0)
            );

            $sqlPaquete = "INSERT INTO paquetes (
                id_envio, codigo_paquete, descripcion, peso, alto, ancho, profundidad,
                volumen, valor_declarado, instrucciones_manejo, estado
            ) VALUES (
                :id_envio, :codigo_paquete, :descripcion, :peso, :alto, :ancho, :profundidad,
                :volumen, :valor_declarado, :instrucciones_manejo, 'BUENO'
            )";
            
            $stmtPaquete = $pdo->prepare($sqlPaquete);
            
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
                throw new Exception("Error al registrar paquete");
            }

            $idPaquete = $pdo->lastInsertId();

            // Items dentro del paquete
            if (!empty($paquete['items'])) {
                foreach ($paquete['items'] as $item) {
                    if (empty($item['descripcion']) || empty($item['cantidad'])) {
                        continue;
                    }

                    $sqlItem = "INSERT INTO items_paquete (
                        id_paquete, id_producto, descripcion, cantidad, 
                        peso_unitario, valor_unitario, observaciones
                    ) VALUES (
                        :id_paquete, :id_producto, :descripcion, :cantidad, 
                        :peso_unitario, :valor_unitario, :observaciones
                    )";
                    
                    $stmtItem = $pdo->prepare($sqlItem);
                    
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
                        throw new Exception("Error al registrar item de paquete");
                    }
                }
            }
        }
    }

    private static function insertSeguimiento($pdo, $idEnvio, $idUsuario) {
        $sql = "INSERT INTO seguimiento_envios (
            id_envio, id_usuario, estado_anterior, estado_nuevo, observaciones
        ) VALUES (
            :id_envio, :id_usuario, NULL, 'PENDIENTE', 'Envío creado'
        )";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_envio", $idEnvio, PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al registrar seguimiento inicial");
        }
    }

    private static function updateNumeroComprobante($pdo, $idSerie, $numeroComprobante) {
        $sql = "UPDATE series_comprobantes SET numero_actual = :numero WHERE id_serie = :id_serie";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":numero", $numeroComprobante, PDO::PARAM_INT);
        $stmt->bindParam(":id_serie", $idSerie, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar número de comprobante");
        }
    }
}