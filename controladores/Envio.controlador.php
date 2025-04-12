<?php
session_start();
require_once "../modelos/Envio.modelo.php";
class ControladorEnvio
{
    /*=============================================
    REGISTRO DE ENVÍO
    =============================================*/
    static public function ctrCrearEnvio() {
        try {
            if (isset($_POST['action']) && $_POST['action'] == 'crear') {
                // Verificar si paquetes es un JSON válido
                $paquetes = json_decode($_POST['paquetes'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("JSON de paquetes inválido: " . json_last_error_msg());
                }
    
                $datos = array(
                    "codigo_envio" => $_POST['codigo_envio'],
                    "id_serie" => $_POST['id_serie'],
                    "id_sucursal_origen" => $_POST['id_sucursal_origen'],
                    "id_sucursal_destino" => $_POST['id_sucursal_destino'],
                    "id_tipo_encomienda" => $_POST['id_tipo_encomienda'],
                    "id_usuario_creador" => $_SESSION['id_usuario'],
                    "id_transportista" => $_POST['id_transportista'] ?? null,
                    "dni_remitente" => $_POST['dni_remitente'] ?? null,
                    "nombre_remitente" => $_POST['nombre_remitente'] ?? null,
                    "dni_destinatario" => $_POST['dni_destinatario'] ?? null,
                    "nombre_destinatario" => $_POST['nombre_destinatario'] ?? null,
                    "clave_recepcion" => $_POST['clave_recepcion'] ?? null,
                    "fecha_estimada_entrega" => $_POST['fecha_estimada_entrega'] ?? null,
                    "peso_total" => $_POST['peso_total'] ?? 0,
                    "volumen_total" => $_POST['volumen_total'] ?? 0,
                    "cantidad_paquetes" => $_POST['cantidad_paquetes'] ?? 1,
                    "instrucciones" => $_POST['instrucciones'] ?? null,
                    "costo_envio" => $_POST['costo_envio'] ?? 0,
                    "metodo_pago" => $_POST['metodo_pago'] ?? 'EFECTIVO',
                    "paquetes" => $paquetes
                );
    
                error_log("Datos recibidos: " . print_r($datos, true)); // Log para depuración
    
                $respuesta = ModeloEnvio::mdlCrearEnvio("envios", $datos);
                echo json_encode($respuesta);
            } else {
                throw new Exception("Acción no válida o no especificada");
            }
        } catch (Exception $e) {
            error_log("Error en ctrCrearEnvio: " . $e->getMessage());
            echo json_encode([
                "status" => false,
                "message" => "Error: " . $e->getMessage(),
                "trace" => $e->getTraceAsString()
            ]);
        }
    }

    /*=============================================
    MOSTRAR ENVÍOS
    =============================================*/
    static public function ctrMostrarEnvios($item = null, $valor = null, $filtros = [])
    {
        $respuesta = ModeloEnvio::mdlMostrarEnvios("envios", $item, $valor, $filtros);
        return $respuesta;
    }

    /*=============================================
    MOSTRAR DETALLE DE ENVÍO
    =============================================*/
    static public function ctrMostrarDetalleEnvio($idEnvio)
    {
        $respuesta = ModeloEnvio::mdlMostrarDetalleEnvio($idEnvio);
        return $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE ENVÍO
    =============================================*/
    static public function ctrCambiarEstadoEnvio()
    {
        if (isset($_POST['action'])) {
            $datos = array(
                "id_envio" => $_POST['id_envio'],
                "estado" => $_POST['estado'],
                "observaciones" => $_POST['observaciones'] ?? null,
                "id_usuario" => $_SESSION['id_usuario']
            );

            $respuesta = ModeloEnvio::mdlCambiarEstadoEnvio($datos);
            echo json_encode($respuesta);
        }
    }

    /*=============================================
    SUBIR DOCUMENTO DE ENVÍO
    =============================================*/
    static public function ctrSubirDocumentoEnvio()
    {
        if (isset($_FILES['documento'])) {
            $directorio = "../documentos/envios/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $nombreArchivo = time() . "_" . basename($_FILES['documento']['name']);
            $rutaArchivo = $directorio . $nombreArchivo;

            if (move_uploaded_file($_FILES['documento']['tmp_name'], $rutaArchivo)) {
                $datos = array(
                    "id_envio" => $_POST['id_envio'],
                    "tipo_documento" => $_POST['tipo_documento'],
                    "nombre_archivo" => $nombreArchivo,
                    "ruta_archivo" => $rutaArchivo,
                    "descripcion" => $_POST['descripcion'] ?? null,
                    "id_usuario" => $_SESSION['id_usuario']
                );

                $respuesta = ModeloEnvio::mdlSubirDocumentoEnvio($datos);
                echo json_encode($respuesta);
            } else {
                echo json_encode(["status" => false, "message" => "Error al subir el archivo"]);
            }
        }
    }

    /*=============================================
    ELIMINAR DOCUMENTO DE ENVÍO
    =============================================*/
    static public function ctrEliminarDocumentoEnvio()
    {
        if (isset($_POST['id_documento'])) {
            $respuesta = ModeloEnvio::mdlEliminarDocumentoEnvio($_POST['id_documento']);
            echo json_encode($respuesta);
        }
    }

    /*=============================================
    CALCULAR COSTO DE ENVÍO
    =============================================*/
    static public function ctrCalcularCostoEnvio()
{
    if (isset($_GET['origen'], $_GET['destino'], $_GET['tipo'], $_GET['peso'])) {
        // Validar que sean números
        if (!is_numeric($_GET['origen']) || !is_numeric($_GET['destino']) || 
            !is_numeric($_GET['tipo']) || !is_numeric(str_replace(',', '.', $_GET['peso']))) {
            echo json_encode(["status" => false, "message" => "Datos inválidos"]);
            return;
        }

        $respuesta = ModeloEnvio::mdlCalcularCostoEnvio(
            (int)$_GET['origen'],
            (int)$_GET['destino'],
            (int)$_GET['tipo'],
            (float)str_replace(',', '.', $_GET['peso'])
        );
        
        return $respuesta;
    } else {
        echo json_encode(["status" => false, "message" => "Faltan parámetros"]);
    }
}
}
?>