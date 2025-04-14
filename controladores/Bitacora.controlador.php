<?php
class ControladorBitacora
{
    /*=============================================
    MOSTRAR REGISTROS DE BITÁCORA
    =============================================*/
    static public function ctrMostrarBitacora($fechaInicio = null, $fechaFin = null, $idUsuario = null, $accion = null)
    {
        $respuesta = ModeloBitacora::mdlMostrarBitacora($fechaInicio, $fechaFin, $idUsuario, $accion);
        echo $respuesta;
    }

    /*=============================================
    LIMPIAR BITÁCORA
    =============================================*/
    static public function ctrLimpiarBitacora()
    {
        $respuesta = ModeloBitacora::mdlLimpiarBitacora();
        echo $respuesta;
    }

    /*=============================================
    REGISTRAR EN BITÁCORA (para usar desde otros controladores)
    =============================================*/
    static public function ctrRegistrarEnBitacora($accion, $tablaAfectada = null, $idRegistroAfectado = null, $datosAnteriores = null, $datosNuevos = null)
    {
        // Obtener información del usuario (si está logueado)
        $idUsuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;
        
        // Obtener información del cliente
        $ip = self::obtenerIpCliente();
        $dispositivo = self::obtenerDispositivo();
        
        $datos = array(
            "id_usuario" => $idUsuario,
            "accion" => $accion,
            "tabla_afectada" => $tablaAfectada,
            "id_registro_afectado" => $idRegistroAfectado,
            "datos_anteriores" => $datosAnteriores ? json_encode($datosAnteriores) : null,
            "datos_nuevos" => $datosNuevos ? json_encode($datosNuevos) : null,
            "ip" => $ip,
            "dispositivo" => $dispositivo
        );
        
        $respuesta = ModeloBitacora::mdlRegistrarEnBitacora("bitacora", $datos);
        return $respuesta;
    }
    
    /*=============================================
    FUNCIONES AUXILIARES
    =============================================*/
    private static function obtenerIpCliente()
    {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
    
    private static function obtenerDispositivo()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $dispositivo = 'Desconocido';
        
        if (strpos($userAgent, 'Mobile') !== false) {
            $dispositivo = 'Móvil';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            $dispositivo = 'Tablet';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $dispositivo = 'Windows';
        } elseif (strpos($userAgent, 'Macintosh') !== false) {
            $dispositivo = 'Mac';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $dispositivo = 'Linux';
        }
        
        return $dispositivo;
    }
}

/* =========================================
INSERTAR BITACORA DESDE LOS MOUDLOS
========================================= */

/* // Después de crear un almacén
ControladorBitacora::ctrRegistrarEnBitacora(
    "CREAR", 
    "almacenes", 
    $idNuevoAlmacen, 
    null, 
    $datosAlmacen
);

// Después de actualizar un almacén
ControladorBitacora::ctrRegistrarEnBitacora(
    "ACTUALIZAR", 
    "almacenes", 
    $_POST["id_almacen"], 
    $datosAnteriores, 
    $datosNuevos
);

// Después de eliminar un almacén
ControladorBitacora::ctrRegistrarEnBitacora(
    "ELIMINAR", 
    "almacenes", 
    $_POST["id_almacen"], 
    $datosAlmacen, 
    null
); */