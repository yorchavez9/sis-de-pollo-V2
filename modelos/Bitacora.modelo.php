<?php
require_once "Conexion.php";

class ModeloBitacora
{
    /*=============================================
    MOSTRAR REGISTROS DE BITÁCORA
    =============================================*/
    static public function mdlMostrarBitacora($fechaInicio = null, $fechaFin = null, $idUsuario = null, $accion = null)
    {
        try {
            $sql = "SELECT b.*, u.nombre_usuario as nombre_usuario 
                    FROM bitacora b 
                    LEFT JOIN usuarios u ON b.id_usuario = u.id_usuario 
                    WHERE 1=1";
            
            $params = array();
            
            // Filtro por fechas
            if ($fechaInicio && $fechaFin) {
                $sql .= " AND DATE(b.fecha_registro) BETWEEN :fecha_inicio AND :fecha_fin";
                $params[':fecha_inicio'] = $fechaInicio;
                $params[':fecha_fin'] = $fechaFin;
            } elseif ($fechaInicio) {
                $sql .= " AND DATE(b.fecha_registro) >= :fecha_inicio";
                $params[':fecha_inicio'] = $fechaInicio;
            } elseif ($fechaFin) {
                $sql .= " AND DATE(b.fecha_registro) <= :fecha_fin";
                $params[':fecha_fin'] = $fechaFin;
            }
            
            // Filtro por usuario
            if ($idUsuario) {
                $sql .= " AND b.id_usuario = :id_usuario";
                $params[':id_usuario'] = $idUsuario;
            }
            
            // Filtro por acción
            if ($accion) {
                $sql .= " AND b.accion = :accion";
                $params[':accion'] = $accion;
            }
            
            $sql .= " ORDER BY b.fecha_registro DESC";
            
            $stmt = Conexion::conectar()->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
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

    /*=============================================
    LIMPIAR BITÁCORA
    =============================================*/
    static public function mdlLimpiarBitacora()
    {
        try {
            $stmt = Conexion::conectar()->prepare("TRUNCATE TABLE bitacora");
            
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Bitácora limpiada correctamente"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al limpiar la bitácora"
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
    REGISTRAR EN BITÁCORA
    =============================================*/
    static public function mdlRegistrarEnBitacora($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare(
                "INSERT INTO $tabla(
                    id_usuario, 
                    accion, 
                    tabla_afectada, 
                    id_registro_afectado,
                    datos_anteriores,
                    datos_nuevos,
                    ip,
                    dispositivo
                ) VALUES (
                    :id_usuario, 
                    :accion, 
                    :tabla_afectada, 
                    :id_registro_afectado,
                    :datos_anteriores,
                    :datos_nuevos,
                    :ip,
                    :dispositivo
                )"
            );

            $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
            $stmt->bindParam(":accion", $datos["accion"], PDO::PARAM_STR);
            $stmt->bindParam(":tabla_afectada", $datos["tabla_afectada"], PDO::PARAM_STR);
            $stmt->bindParam(":id_registro_afectado", $datos["id_registro_afectado"], PDO::PARAM_INT);
            $stmt->bindParam(":datos_anteriores", $datos["datos_anteriores"], PDO::PARAM_STR);
            $stmt->bindParam(":datos_nuevos", $datos["datos_nuevos"], PDO::PARAM_STR);
            $stmt->bindParam(":ip", $datos["ip"], PDO::PARAM_STR);
            $stmt->bindParam(":dispositivo", $datos["dispositivo"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error al registrar en bitácora: " . $e->getMessage());
            return false;
        }
    }
}