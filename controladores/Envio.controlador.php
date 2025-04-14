<?php
class ControladorEnvio {
    /*=============================================
    REGISTRO DE ENVÍO
    =============================================*/
    static public function ctrCrearEnvio() {
        try {
            self::validateRequestMethod('POST');
            self::validateAction('crear');
            
            $datos = self::getEnvioDataFromPost();
            $paquetes = self::validateAndDecodePaquetes($_POST['paquetes']);
            
            $datosCompletos = array_merge($datos, [
                'peso_total' => $_POST['peso_total'] ?? 0,
                'volumen_total' => $_POST['volumen_total'] ?? 0,
                'cantidad_paquetes' => $_POST['cantidad_paquetes'] ?? 1,
                'paquetes' => $paquetes
            ]);
            
            $respuesta = ModeloEnvio::mdlCrearEnvio("envios", $datosCompletos);
            self::sendResponse($respuesta['status'], $respuesta['message'], $respuesta['data'] ?? []);
        } catch (Exception $e) {
            self::sendResponse(false, $e->getMessage());
        }
    }

    /*=============================================
    MOSTRAR ENVÍOS
    =============================================*/
    static public function ctrMostrarEnvios($item = null, $valor = null, $filtros = []) {
        try {
            $respuesta = ModeloEnvio::mdlMostrarEnvios("envios", $item, $valor, $filtros);
            
            if (!$respuesta['status']) {
                throw new Exception($respuesta['message'] ?? "Error al obtener envíos");
            }
            
            return $respuesta;
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /*=============================================
    MOSTRAR DETALLE DE ENVÍO
    =============================================*/
    static public function ctrMostrarDetalleEnvio($idEnvio) {
        try {
            if (!$idEnvio) {
                throw new Exception("ID de envío no proporcionado");
            }
            
            $respuesta = ModeloEnvio::mdlMostrarDetalleEnvio($idEnvio);
            
            if (!$respuesta['status']) {
                throw new Exception($respuesta['message'] ?? "Error al obtener detalle");
            }
            
            return $respuesta;
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /*=============================================
    CAMBIAR ESTADO DE ENVÍO
    =============================================*/
    static public function ctrCambiarEstadoEnvio() {
        try {
            self::validateRequestMethod('POST');
            
            $datos = [
                "id_envio" => $_POST['id_envio'] ?? null,
                "estado" => $_POST['estado'] ?? null,
                "observaciones" => $_POST['observaciones'] ?? null,
                "id_usuario" => $_SESSION['id_usuario'] ?? null
            ];
            
            self::validateRequiredFields($datos, ['id_envio', 'estado', 'id_usuario']);
            
            $respuesta = ModeloEnvio::mdlCambiarEstadoEnvio($datos);
            
            if (!$respuesta['status']) {
                throw new Exception($respuesta['message'] ?? "Error al cambiar estado");
            }
            
            return $respuesta;
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /*=============================================
    SUBIR DOCUMENTO DE ENVÍO
    =============================================*/
    static public function ctrSubirDocumentoEnvio() {
        try {
            self::validateRequestMethod('POST');
            self::validateAction('subirDocumento');
            
            if (!isset($_FILES['documento'])) {
                throw new Exception("No se ha seleccionado ningún archivo");
            }
            
            $directorio = "../documentos/envios/";
            if (!file_exists($directorio)) {
                if (!mkdir($directorio, 0777, true)) {
                    throw new Exception("No se pudo crear el directorio para documentos");
                }
            }

            $nombreArchivo = time() . "_" . basename($_FILES['documento']['name']);
            $rutaArchivo = $directorio . $nombreArchivo;

            if (!move_uploaded_file($_FILES['documento']['tmp_name'], $rutaArchivo)) {
                throw new Exception("Error al subir el archivo");
            }

            $datos = [
                "id_envio" => $_POST['id_envio'] ?? null,
                "tipo_documento" => $_POST['tipo_documento'] ?? null,
                "nombre_archivo" => $nombreArchivo,
                "ruta_archivo" => $rutaArchivo,
                "descripcion" => $_POST['descripcion'] ?? null,
                "id_usuario" => $_SESSION['id_usuario'] ?? null
            ];
            
            self::validateRequiredFields($datos, ['id_envio', 'tipo_documento', 'id_usuario']);

            $respuesta = ModeloEnvio::mdlSubirDocumentoEnvio($datos);
            
            if (!$respuesta['status']) {
                // Intentar eliminar el archivo subido si falló la BD
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
                throw new Exception($respuesta['message'] ?? "Error al registrar documento");
            }
            
            return $respuesta;
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /*=============================================
    ELIMINAR DOCUMENTO DE ENVÍO
    =============================================*/
    static public function ctrEliminarDocumentoEnvio() {
        try {
            self::validateRequestMethod('POST');
            self::validateAction('eliminarDocumento');
            
            $idDocumento = $_POST['id_documento'] ?? null;
            if (!$idDocumento) {
                throw new Exception("ID de documento no proporcionado");
            }
            
            $respuesta = ModeloEnvio::mdlEliminarDocumentoEnvio($idDocumento);
            
            if (!$respuesta['status']) {
                throw new Exception($respuesta['message'] ?? "Error al eliminar documento");
            }
            
            return $respuesta;
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /*=============================================
    CALCULAR COSTO DE ENVÍO
    =============================================*/
    static public function ctrCalcularCostoEnvio() {
        try {
            self::validateRequestMethod('GET');
            
            $origen = $_GET['origen'] ?? null;
            $destino = $_GET['destino'] ?? null;
            $tipo = $_GET['tipo'] ?? null;
            $peso = $_GET['peso'] ?? null;
            
            if (!$origen || !$destino || !$tipo || !$peso) {
                throw new Exception("Faltan parámetros requeridos");
            }
            
            if (!is_numeric($origen) || !is_numeric($destino) || !is_numeric($tipo)) {
                throw new Exception("Parámetros inválidos");
            }
            
            $peso = (float)str_replace(',', '.', $peso);
            if ($peso <= 0) {
                throw new Exception("El peso debe ser mayor a cero");
            }
            
            $respuesta = ModeloEnvio::mdlCalcularCostoEnvio($origen, $destino, $tipo, $peso);
            
            if (!$respuesta['status']) {
                throw new Exception($respuesta['message'] ?? "Error al calcular costo");
            }
            
            return $respuesta;
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /*=============================================
    MÉTODOS DE VALIDACIÓN Y UTILIDAD
    =============================================*/
    private static function validateRequestMethod($expected) {
        if ($_SERVER['REQUEST_METHOD'] !== $expected) {
            throw new Exception("Método no permitido");
        }
    }
    
    private static function validateAction($expected) {
        if (($_POST['action'] ?? null) !== $expected) {
            throw new Exception("Acción no válida");
        }
    }
    
    private static function validateRequiredFields($data, $fields) {
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("El campo $field es requerido");
            }
        }
    }
    
    private static function getEnvioDataFromPost() {
        return [
            "codigo_envio" => $_POST['codigo_envio'] ?? '',
            "id_serie" => $_POST['id_serie'] ?? null,
            "id_sucursal_origen" => $_POST['id_sucursal_origen'] ?? null,
            "id_sucursal_destino" => $_POST['id_sucursal_destino'] ?? null,
            "id_tipo_encomienda" => $_POST['id_tipo_encomienda'] ?? null,
            "id_usuario_creador" => $_SESSION['id_usuario'] ?? null,
            "id_transportista" => $_POST['id_transportista'] ?? null,
            "dni_remitente" => $_POST['dni_remitente'] ?? null,
            "nombre_remitente" => $_POST['nombre_remitente'] ?? null,
            "dni_destinatario" => $_POST['dni_destinatario'] ?? null,
            "nombre_destinatario" => $_POST['nombre_destinatario'] ?? null,
            "clave_recepcion" => $_POST['clave_recepcion'] ?? null,
            "fecha_estimada_entrega" => $_POST['fecha_estimada_entrega'] ?? null,
            "instrucciones" => $_POST['instrucciones'] ?? null,
            "costo_envio" => $_POST['costo_envio'] ?? 0,
            "metodo_pago" => $_POST['metodo_pago'] ?? 'EFECTIVO'
        ];
    }
    
    private static function validateAndDecodePaquetes($paquetesJson) {
        if (empty($paquetesJson)) {
            throw new Exception("No se proporcionaron datos de paquetes");
        }
        
        $paquetes = json_decode($paquetesJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Formato de paquetes inválido: " . json_last_error_msg());
        }
        
        if (empty($paquetes)) {
            throw new Exception("Debe agregar al menos un paquete");
        }
        
        return $paquetes;
    }
    
    private static function sendResponse($success, $message = '', $data = []) {
        echo json_encode([
            'status' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}