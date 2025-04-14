<?php
require_once "conexion.php";

class Persona{
    /* ===================================
    MOSTRANDO PERSONAS
    =================================== */

    static public function mdlMostrarPersonas(){
        $stmt = Conexion::conectar()->prepare("SELECT * FROM personas");
        $stmt->execute();
        $personas = $stmt->fetchAll();
        $stmt->closeCursor();
        $stmt = null;
        
        return [
            'status' => true,
            'data' => $personas
        ];
    }

}