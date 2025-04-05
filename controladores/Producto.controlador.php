<?php

require_once "../modelos/Producto.modelo.php";

class ControladorProducto
{
    /*=============================================
    REGISTRO DE PRODUCTO
    =============================================*/
    static public function ctrCrearProducto()
    {
        $tabla = "productos";
        $datos = array(
            "id_categoria" => $_POST["id_categoria"],
            "codigo" => $_POST["codigo"],
            "codigo_barras" => $_POST["codigo_barras"] ?? null,
            "nombre" => $_POST["nombre"],
            "descripcion" => $_POST["descripcion"] ?? null,
            "unidad_medida" => $_POST["unidad_medida"],
            "peso_promedio" => $_POST["peso_promedio"] ?? null,
            "precio_compra" => $_POST["precio_compra"],
            "precio_venta" => $_POST["precio_venta"],
            "tiene_iva" => $_POST["tiene_iva"],
            "imagen" => null,
            "estado" => isset($_POST["estado"]) ? $_POST["estado"] : 1
        );

        // Manejo de la imagen
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {
            $directorio = "../vistas/img/productos/";
            $nombreArchivo = time() . "_" . $_FILES["imagen"]["name"];
            $rutaTemporal = $_FILES["imagen"]["tmp_name"];
            
            if (move_uploaded_file($rutaTemporal, $directorio . $nombreArchivo)) {
                $datos["imagen"] = $nombreArchivo;
            }
        }

        $respuesta = ModeloProducto::mdlIngresarProducto($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR PRODUCTOS
    =============================================*/
    static public function ctrMostrarProductos($item, $valor)
    {
        $tabla = "productos";
        $respuesta = ModeloProducto::mdlMostrarProductos($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    VER DETALLES DE PRODUCTO
    =============================================*/
    static public function ctrVerDetallesProducto()
    {
        $tabla = "productos";
        $item = "id_producto";
        $valor = $_POST["id_producto"];
        
        // Hacemos un join con categorías para obtener el nombre de la categoría
        $respuesta = ModeloProducto::mdlMostrarProductos($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    EDITAR PRODUCTO (Obtener datos)
    =============================================*/
    static public function ctrEditarProducto()
    {
        $tabla = "productos";
        $item = "id_producto";
        $valor = $_POST["id_producto"];
        $respuesta = ModeloProducto::mdlMostrarProductos($tabla, $item, $valor);
        echo $respuesta;
    }

    /*=============================================
    ACTUALIZAR PRODUCTO
    =============================================*/
    static public function ctrActualizarProducto()
    {
        $tabla = "productos";
        $datos = array(
            "id_producto" => $_POST["id_producto"],
            "id_categoria" => $_POST["id_categoria"],
            "codigo" => $_POST["codigo"],
            "codigo_barras" => $_POST["codigo_barras"] ?? null,
            "nombre" => $_POST["nombre"],
            "descripcion" => $_POST["descripcion"] ?? null,
            "unidad_medida" => $_POST["unidad_medida"],
            "peso_promedio" => $_POST["peso_promedio"] ?? null,
            "precio_compra" => $_POST["precio_compra"],
            "precio_venta" => $_POST["precio_venta"],
            "tiene_iva" => $_POST["tiene_iva"],
            "imagen" => $_POST["imagen_actual"] ?? null,
            "estado" => $_POST["estado"]
        );

        // Manejo de la nueva imagen
        if (isset($_FILES["imagen"])) {
            // Eliminar imagen anterior si existe
            if (!empty($datos["imagen"])) {
                $rutaImagenAnterior = "../vistas/img/productos/" . $datos["imagen"];
                if (file_exists($rutaImagenAnterior)) {
                    unlink($rutaImagenAnterior);
                }
            }

            // Subir nueva imagen
            if ($_FILES["imagen"]["error"] === 0) {
                $directorio = "../vistas/img/productos/";
                $nombreArchivo = time() . "_" . $_FILES["imagen"]["name"];
                $rutaTemporal = $_FILES["imagen"]["tmp_name"];
                
                if (move_uploaded_file($rutaTemporal, $directorio . $nombreArchivo)) {
                    $datos["imagen"] = $nombreArchivo;
                }
            }
        }

        $respuesta = ModeloProducto::mdlActualizarProducto($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE PRODUCTO
    =============================================*/
    static public function ctrCambiarEstadoProducto()
    {
        $tabla = "productos";
        $datos = array(
            "id_producto" => $_POST["id_producto"],
            "estado" => $_POST["estado"]
        );
        $respuesta = ModeloProducto::mdlCambiarEstadoProducto($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR PRODUCTO
    =============================================*/
    static public function ctrBorrarProducto()
    {
        $tabla = "productos";
        $datos = $_POST["id_producto"];
        $respuesta = ModeloProducto::mdlBorrarProducto($tabla, $datos);
        echo $respuesta;
    }
}