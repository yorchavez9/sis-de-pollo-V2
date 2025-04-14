<?php
require_once "../modelos/Persona.modelo.php";
class ControladorPersona{
    static public function ctrMostrarPersonas(){
        $respuesta = Persona::mdlMostrarPersonas();
        return $respuesta;
    }
}