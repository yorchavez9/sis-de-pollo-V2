<?php
class ControladorConfiguracion
{
    /*=============================================
    MOSTRAR CONFIGURACIÓN
    =============================================*/
    static public function ctrMostrarConfiguracion()
    {
        $respuesta = ModeloConfiguracion::mdlMostrarConfiguracion();
        echo $respuesta;
    }

    /*=============================================
    GUARDAR CONFIGURACIÓN
    =============================================*/
    static public function ctrGuardarConfiguracion()
{
    $datos = array(
        "nombre_empresa" => $_POST["nombre_empresa"],
        "ruc" => $_POST["ruc"],
        "direccion" => $_POST["direccion"],
        "telefono" => $_POST["telefono"],
        "email" => $_POST["email"],
        "moneda" => $_POST["moneda"],
        "impuesto" => $_POST["impuesto"]
    );

    // Procesar imagen si se subió
    if (!empty($_FILES["logo"]["tmp_name"])) {
        $directorio = "../vistas/img/sistema/";
        
        // Crear directorio si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }
        
        $nombreArchivo = "logo_" . uniqid() . "." . pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
        $ruta = $directorio . $nombreArchivo;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $ruta)) {
            // Aquí corregimos la ruta para que coincida con el directorio
            $datos["logo"] = "vistas/img/sistema/" . $nombreArchivo;
            
            // Opcional: Eliminar el logo anterior si existe
            $configActual = json_decode(ModeloConfiguracion::mdlMostrarConfiguracion(), true);
            if ($configActual["status"] && !empty($configActual["data"]["logo"])) {
                $logoAnterior = "../" . $configActual["data"]["logo"];
                if (file_exists($logoAnterior)) {
                    unlink($logoAnterior);
                }
            }
        }
    }

    $respuesta = ModeloConfiguracion::mdlGuardarConfiguracion($datos);
    echo $respuesta;
}
}
?>