<?php

require_once "Conexion.php";

class ModeloAccion
{
    /*=============================================
    MOSTRAR ACCIONES
    =============================================*/
    static public function mdlMostrarAcciones()
    {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM acciones");
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
