<?php
session_start();

include "modulos/layouts/head.php";

if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {
    echo '<div class="main-wrapper">'; 
    include "modulos/layouts/header.php" ;
    include "modulos/layouts/sidebar.php"; 

    if (isset($_GET["ruta"])) {

        if (
            $_GET["ruta"] == "inicio" ||
            $_GET["ruta"] == "sucursales" ||
            $_GET["ruta"] == "almacenes" ||
            $_GET["ruta"] == "envios" ||
            $_GET["ruta"] == "tipoDocumentos" ||
            $_GET["ruta"] == "clientes" ||
            $_GET["ruta"] == "proveedores" ||
            $_GET["ruta"] == "transportista" ||

            $_GET["ruta"] == "salir"
        ) {

            include "modulos/" . $_GET["ruta"] . ".php";
        } else {

            include "modulos/404.php";
        }
    } else {

        include "modulos/inicio.php";
    }
    echo '</div>';
} else {
    include "modulos/login.php";
}
 include "modulos/layouts/footer.php"; ?>
