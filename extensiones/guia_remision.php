<?php
require_once "../modelos/Envio.modelo.php";
require_once "../controladores/Envio.controlador.php";

require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar opciones de DOMPDF
$options = new Options();
$options->set([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => false,
    'defaultMediaType' => 'print',
    'defaultFont' => 'Arial',
    'chroot' => __DIR__,
    'tempDir' => __DIR__ . '/tmp',
    'logOutputFile' => ''
]);

// Obtener datos del envío
$tipoDocumento = $_GET["comprobante"]; 
$idEnvio = $_GET['id'];
$response = ControladorEnvio::ctrMostrarDetalleEnvio($idEnvio);

if (!$response || !$response['status']) {
    die("Error: No se pudo obtener los datos del envío");
}

$envio = $response['data']['envio'];
$paquetes = $response['data']['paquetes'];
$seguimiento = $response['data']['seguimiento'];

// Preparar datos para la plantilla
$data = [
    'empresa_nombre'    => 'TRANSPORTES AKIL',
    'empresa_ruc'       => $envio['dni_remitente'],
    'serie'      => $envio['serie'],
    'serie_numero'      => $envio['numero_comprobante'],
    'remitente_nombre'  => $envio['nombre_remitente'],
    'remitente_dni'     => $envio['dni_remitente'],
    'destinatario_nombre' => $envio['nombre_destinatario'],
    'destinatario_dni'  => $envio['dni_destinatario'],
    'clave_recepcion'   => $envio['clave_recepcion'],
    'fecha_emision'     => date('d/m/Y H:i', strtotime($envio['fecha_creacion'])),
    'fecha_traslado'    => date('d/m/Y', strtotime($envio['fecha_envio'])),
    'fecha_estimada_entrega' => date('d/m/Y H:i', strtotime($envio['fecha_estimada_entrega'])),
    'fecha_actualizacion' => date('d/m/Y H:i', strtotime($envio['fecha_actualizacion'])),
    'motivo_traslado'   => 'VENTA',
    'punto_partida'     => $envio['sucursal_origen'],
    'punto_llegada'     => $envio['sucursal_destino'],
    'transportista'     => $envio['transportista'],
    'tipo_encomienda'   => $envio['tipo_encomienda'],
    'peso_total'        => $envio['peso_total'],
    'volumen_total'     => $envio['volumen_total'],
    'cantidad_paquetes' => $envio['cantidad_paquetes'],
    'instrucciones'     => $envio['instrucciones'] ?: 'Ninguna',
    'estado' => str_replace('_', ' ', $envio['estado']),
    'metodo_pago'       => $envio['metodo_pago'],
    'costo_envio'       => number_format($envio['costo_envio'], 2),
    'usuario_creador'   => $envio['usuario_creador'],
    'usuario_receptor'  => $envio['usuario_receptor'] ?: 'No asignado',
    'paquetes'          => '',
    'seguimiento'       => ''
];


// Generar sección de paquetes
// Generar sección de paquetes
// Generar sección de paquetes
foreach ($paquetes as $paquete) {
    $data['paquetes'] .= '
    <table class="data-table package-table">
        <thead>
            <tr>
            <th>Codigo</th>
            <th>Estado</th>
            <th>Paquete</th>
            <th>Descripción</th>
            <th>Dimensiones</th>
            <th>Peso</th>
            <th>Valor</th>
        </thead>
        <tbody>
            <tr>
                <td>'.$paquete['codigo_paquete'].'</td>
                <td>'.$paquete['estado'].'</td>
                <td>'.$paquete['descripcion'].'</td>
                <td>S/ '.number_format($paquete['valor_declarado'], 2).'</td>
                <td>'.$paquete['alto'].'×'.$paquete['ancho'].'×'.$paquete['profundidad'].' cm</td>
                <td>'.$paquete['peso'].' kg</td>
                <td>'.number_format($paquete['volumen']/1000000, 3).' m³</td>
            </tr>
        </tbody>
    </table>
    <tr>
    <td>Items del paquete</td>
    <tr>

    <table class="data-table items">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>P.Unit</th>
                <th>V.Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($paquete['items'] as $item) {
        $data['paquetes'] .= '
            <tr>
                <td>'.$item['codigo_producto'].'</td>
                <td>'.$item['nombre_producto'].'</td>
                <td>'.$item['cantidad'].'</td>
                <td>'.$item['peso_unitario'].' kg</td>
                <td>S/ '.number_format($item['valor_unitario'], 2).'</td>
                <td>S/ '.number_format($item['valor_unitario'] * $item['cantidad'], 2).'</td>
            </tr>';
    }

    $data['paquetes'] .= '
        </tbody>
    </table>';
}



// Generar sección de seguimiento
foreach ($seguimiento as $track) {
    $data['seguimiento'] .= '
    <tr>
        <td>'.date('d/m/Y H:i', strtotime($track['fecha_registro'])).'</td>
        <td>'.str_replace('_',' ',$track['estado_nuevo']).'</td>
        <td>'.($track['ubicacion'] ?: 'No especificado').'</td>
        <td>'.$track['observaciones'].'</td>
        <td>'.$track['usuario'].'</td>
    </tr>';
}


// Cargar template HTML
$html = file_get_contents('plantillas/guia_remision.html');
if ($html === false) {
    die("Error: No se pudo cargar la plantilla HTML");
}

// Reemplazar marcadores
foreach ($data as $key => $value) {
    if (in_array($key, ['paquetes', 'seguimiento'])) {
        $html = str_replace('{{'.$key.'}}', $value, $html); // inserta HTML sin escapar
    } else {
        $html = str_replace('{{'.$key.'}}', htmlspecialchars($value), $html);
    }
}


$html = str_replace('{{paquetes}}', $data['paquetes'], $html);
$html = str_replace('{{seguimiento}}', $data['seguimiento'], $html);



// Generar PDF
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


// Generar nombre de archivo
$filename = "guia_remision_".$envio['codigo_envio'].".pdf";

// Enviar al navegador
$dompdf->stream($filename, ["Attachment" => 0]);