<?php
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

// Datos del documento
$data = [
    'empresa_nombre'    => 'CHAVEZ MARTINEZ DANIEL',
    'empresa_ruc'       => '10456789012',
    'serie_numero'      => 'EB01-110234714835',
    'cliente_nombre'    => 'BARRIO COCCHAPATA C.P. PAMPA DEL CARMEN',
    'cliente_dni'       => '10456789012',
    'cliente_direccion' => 'LIRCAY - ANGARAES - HUANCAVELICA',
    'fecha_emision'     => date('d/m/Y'),
    'moneda'            => 'SOLES',
    'items'             => '',
    'total'             => 'S/ 6.00'
];

// Tipo de documento (boleta o ticket)
$tipoDocumento = 'boleta'; 

// Productos
$productos = [
    ['cantidad' => '1.00', 'unidad' => 'UND', 'descripcion' => 'POR VENTA DE DISCO DE CORTE PARA METAL', 'precio' => '6.00', 'total' => '6.00']
];

$data['items'] = '';

// Generar filas de productos
foreach ($productos as $producto) {
    foreach ($productos as $producto) {
        $data['items'] .= '
        <tr>
            <td>'.$producto['cantidad'].'</td>
            <td>'.$producto['unidad'].'</td>
            <td>'.$producto['descripcion'].'</td>
            <td align="right">'.$producto['precio'].'</td>
            <td align="right">'.$producto['total'].'</td>
        </tr>';
    }
}


// Cargar template HTML
if($tipoDocumento == 'ticket'){
    $html = file_get_contents('plantillas/ticket.html');
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
$filename = "documento_".date('Ymd_His').".pdf";

// Enviar al navegador
$dompdf->stream($filename, ["Attachment" => 0]);