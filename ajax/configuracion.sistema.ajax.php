<?php
require_once "../modelos/Configuracion.sistema.modelo.php";
require_once "../controladores/Configuracion.sistema.controlador.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'guardar') {
        ControladorConfiguracion::ctrGuardarConfiguracion();
    } else {
        echo json_encode(["status" => false, "message" => "Acción no válida"]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    ControladorConfiguracion::ctrMostrarConfiguracion();
}
?>