<?php
class ControladorTipoDocumento
{
    /*=============================================
    LISTAR TIPOS DE DOCUMENTOS
    =============================================*/
    static public function ctrListarTiposDocumentos()
    {
        try {
            $respuesta = ModeloTipoDocumento::mdlListarTiposDocumentos();
            return $respuesta;
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => "Error al listar: " . $e->getMessage()
            ]);
        }
    }

    /*=============================================
    CREAR TIPO DE DOCUMENTO
    =============================================*/
    static public function ctrCrearTipoDocumento()
    {
        try {
            // Validar datos requeridos
            if (empty($_POST['nombre']) || empty($_POST['abreviatura'])) {
                return json_encode([
                    "status" => false,
                    "message" => "Nombre y abreviatura son requeridos"
                ]);
            }
            
            $datos = array(
                "nombre" => $_POST["nombre"],
                "abreviatura" => $_POST["abreviatura"],
                "longitud" => $_POST["longitud"] ?? null,
                "es_empresa" => $_POST["es_empresa"] ?? 0,
                "estado" => $_POST["estado"] ?? 1
            );
            
            return ModeloTipoDocumento::mdlCrearTipoDocumento($datos);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => "Error al crear: " . $e->getMessage()
            ]);
        }
    }

    /*=============================================
    OBTENER TIPO DE DOCUMENTO
    =============================================*/
    static public function ctrObtenerTipoDocumento()
    {
        try {
            $id = $_POST["id"] ?? $_GET["id"] ?? null;
            
            if (!$id) {
                return json_encode([
                    "status" => false,
                    "message" => "ID no especificado"
                ]);
            }
            
            return ModeloTipoDocumento::mdlObtenerTipoDocumento($id);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => "Error al obtener: " . $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ACTUALIZAR TIPO DE DOCUMENTO
    =============================================*/
    static public function ctrActualizarTipoDocumento()
    {
        try {
            // Validar datos requeridos
            if (empty($_POST['id_tipo_documento']) || empty($_POST['nombre']) || empty($_POST['abreviatura'])) {
                return json_encode([
                    "status" => false,
                    "message" => "Datos incompletos"
                ]);
            }
            
            $datos = array(
                "id_tipo_documento" => $_POST["id_tipo_documento"],
                "nombre" => $_POST["nombre"],
                "abreviatura" => $_POST["abreviatura"],
                "longitud" => $_POST["longitud"] ?? null,
                "es_empresa" => $_POST["es_empresa"] ?? 0,
                "estado" => $_POST["estado"] ?? 1
            );
            
            return ModeloTipoDocumento::mdlActualizarTipoDocumento($datos);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => "Error al actualizar: " . $e->getMessage()
            ]);
        }
    }

    /*=============================================
    CAMBIAR ESTADO DE TIPO DE DOCUMENTO
    =============================================*/
    static public function ctrCambiarEstadoTipoDocumento()
    {
        try {
            $id = $_POST["id"] ?? null;
            $estado = $_POST["estado"] ?? null;
            
            if (!$id || $estado === null) {
                return json_encode([
                    "status" => false,
                    "message" => "Datos incompletos"
                ]);
            }
            
            $datos = array(
                "id_tipo_documento" => $id,
                "estado" => $estado
            );
            
            return ModeloTipoDocumento::mdlCambiarEstadoTipoDocumento($datos);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => "Error al cambiar estado: " . $e->getMessage()
            ]);
        }
    }

    /*=============================================
    ELIMINAR TIPO DE DOCUMENTO
    =============================================*/
    static public function ctrEliminarTipoDocumento()
    {
        try {
            $id = $_POST["id"] ?? null;
            
            if (!$id) {
                return json_encode([
                    "status" => false,
                    "message" => "ID no especificado"
                ]);
            }
            
            return ModeloTipoDocumento::mdlEliminarTipoDocumento($id);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => "Error al eliminar: " . $e->getMessage()
            ]);
        }
    }
}