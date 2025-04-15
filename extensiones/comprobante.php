<?php
require_once "../modelos/Envio.modelo.php";
require_once "../controladores/Envio.controlador.php";

require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar opciones de DOMPDF (versión limpia)
$options = new Options();
$options->set([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => false, // Mejor para seguridad
    'defaultMediaType' => 'print',
    'defaultFont' => 'Arial',
    'chroot' => __DIR__,
    'tempDir' => __DIR__ . '/tmp',
    'logOutputFile' => '' // Desactivar logging
]);

// Verificar parámetros de entrada
$idEnvio = isset($_GET['id']) ? $_GET['id'] : die("Error: ID de envío no proporcionado");
$tipoDocumento = isset($_GET["comprobante"]) ? $_GET["comprobante"] : "boleta";

// Obtener datos del envío
$response = ControladorEnvio::ctrMostrarDetalleEnvio($idEnvio);

// Verificar si se obtuvieron datos correctamente
if (!$response['status'] || empty($response['data'])) {
    die("Error: No se pudieron obtener los datos del envío");
}

// Extraer datos del envío
$envio = $response['data']['envio'];
$paquetes = $response['data']['paquetes'];

// Datos del documento
$data = [
    'empresa_nombre'    => 'CHAVEZ MARTINEZ DANIEL',
    'empresa_ruc'       => '10456789012',
    'serie_numero'      => $envio['serie'] . '-' . sprintf('%08d', $envio['numero_comprobante']),
    'cliente_nombre'    => $envio['nombre_remitente'],
    'cliente_dni'       => $envio['dni_remitente'],
    'cliente_direccion' => $envio['sucursal_origen'] . ' - ' . $envio['sucursal_destino'],
    'fecha_emision'     => date('d/m/Y', strtotime($envio['fecha_creacion'])),
    'moneda'            => 'SOLES',
    'items'             => '',
    'total'             => 'S/ ' . number_format($envio['costo_envio'], 2)
];

// Generar filas de productos (paquetes)
$totalPeso = 0;
$totalValor = 0;

foreach ($paquetes as $paquete) {
    $valorUnitario = !empty($paquete['valor_declarado']) ? $paquete['valor_declarado'] : '0.00';
    $totalItem = floatval($valorUnitario);
    
    $data['items'] .= '
    <tr>
        <td>1.00</td>
        <td>UND</td>
        <td>' . htmlspecialchars($paquete['descripcion']) . ' - ' . htmlspecialchars($paquete['codigo_paquete']) . '</td>
        <td align="right">' . number_format(floatval($valorUnitario), 2) . '</td>
        <td align="right">' . number_format($totalItem, 2) . '</td>
    </tr>';
    
    $totalPeso += floatval($paquete['peso']);
    $totalValor += $totalItem;
}

// Agregar fila para el servicio de envío
if (floatval($envio['costo_envio']) > 0) {
    $data['items'] .= '
    <tr>
        <td>1.00</td>
        <td>SERV</td>
        <td>Servicio de envío - ' . htmlspecialchars($envio['codigo_envio']) . '</td>
        <td align="right">' . number_format(floatval($envio['costo_envio']), 2) . '</td>
        <td align="right">' . number_format(floatval($envio['costo_envio']), 2) . '</td>
    </tr>';
    
    $totalValor += floatval($envio['costo_envio']);
}

// Actualizar el total
$data['total'] = 'S/ ' . number_format($totalValor, 2);

// Cargar template HTML
if($tipoDocumento == 'ticket'){
    $html = file_get_contents('plantillas/ticket.html');
}else if($tipoDocumento == 'guia_remision'){
    $html = file_get_contents('plantillas/guia_remision.html');
}else{
    $html = file_get_contents('plantillas/comprobante.html');
}

if ($html === false) {
    die("Error: No se pudo cargar la plantilla HTML");
}

// Reemplazar marcadores
foreach ($data as $key => $value) {
    if ($key === 'items') {
        $html = str_replace('{{'.$key.'}}', $value, $html);
    } else {
        $html = str_replace('{{'.$key.'}}', htmlspecialchars($value), $html);
    }
}

// Generar monto en texto
$montoEnTexto = convertirNumeroALetras($totalValor);
$html = str_replace('{{monto_texto}}', 'SON: ' . $montoEnTexto . ' SOLES', $html);

// Generar PDF
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Configurar tamaño según tipo de documento
if ($tipoDocumento == "ticket") {
    // Tamaño ticket: 80mm de ancho (226.77pt) x alto automático
    $dompdf->setPaper([0, 0, 226.77, 600], 'portrait');
} else {
    $dompdf->setPaper('A4', 'portrait');
}

$dompdf->render();

// Generar nombre de archivo
$filename = $envio['codigo_envio'] . "_" . date('Ymd_His') . ".pdf";

// Enviar al navegador
$dompdf->stream($filename, ["Attachment" => 0]);

/**
 * Función para convertir un número a letras
 * @param float $numero El número a convertir
 * @return string El número en letras
 */
function convertirNumeroALetras($numero) {
    $parte_entera = floor($numero);
    $parte_decimal = round(($numero - $parte_entera) * 100);
    
    $unidades = array('', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE');
    $decenas = array('', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA');
    $centenas = array('', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS');
    
    $texto = '';
    
    if ($parte_entera == 0) {
        $texto = 'CERO';
    } else if ($parte_entera == 1) {
        $texto = 'UNO';
    } else if ($parte_entera < 10) {
        $texto = $unidades[$parte_entera];
    } else if ($parte_entera < 100) {
        $decena = floor($parte_entera / 10);
        $unidad = $parte_entera % 10;
        
        if ($unidad == 0) {
            $texto = $decenas[$decena];
        } else {
            $texto = $decenas[$decena] . ' Y ' . $unidades[$unidad];
        }
    }
    
    $texto .= ' CON ' . sprintf('%02d', $parte_decimal) . '/100';
    
    return $texto;
}
?>