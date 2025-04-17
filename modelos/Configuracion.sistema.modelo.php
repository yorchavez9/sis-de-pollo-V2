<?php
require_once "Conexion.php";

class ModeloConfiguracion
{
    /*=============================================
    MOSTRAR CONFIGURACIÓN
    =============================================*/
    static public function mdlMostrarConfiguracion()
    {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM configuracion_sistema WHERE id_configuracion = 1");
            $stmt->execute();
            
            $resultado = $stmt->fetch();
            
            return json_encode([
                "status" => true,
                "data" => $resultado
            ]);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    GUARDAR CONFIGURACIÓN
    =============================================*/
    static public function mdlGuardarConfiguracion($datos)
    {
        try {
            $setPart = "";
            $params = [];
            
            foreach ($datos as $key => $value) {
                if (!empty($setPart)) {
                    $setPart .= ", ";
                }
                $setPart .= "$key = :$key";
                $params[":$key"] = $value;
            }
            
            $stmt = Conexion::conectar()->prepare(
                "UPDATE configuracion_sistema SET $setPart WHERE id_configuracion = 1"
            );
            
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            
            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Configuración actualizada con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar la configuración"
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
?>