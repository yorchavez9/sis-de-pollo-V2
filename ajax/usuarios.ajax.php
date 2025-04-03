<?php

include_once "../modelos/Usuario.modelo.php";
include_once "../controladores/Usuario.controlador.php";

$login = new ControladorUsuarios();
$login->ctrLoginUsuario();
