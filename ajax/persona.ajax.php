<?php

require_once "../modelos/Persona.modelo.php";
require_once "../controladores/Persona.controlador.php";

/*=============================================
MANEJADOR DE SOLICITUDES AJAX
=============================================*/
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $response = ControladorPersona::ctrMostrarPersonas();
    echo json_encode($response);
}