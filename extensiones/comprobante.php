<?php
require_once "../modelos/Envio.modelo.php";
require_once "../modelos/Configuracion.sistema.modelo.php";

require_once "../controladores/Envio.controlador.php";
require_once "../controladores/Configuracion.sistema.controlador.php";
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class GeneradorComprobante {
    private $dompdf;
    private $idEnvio;
    private $tipoDocumento;
    private $envio;
    private $paquetes;
    private $templatePath;
    private $configuracion;
    
    public function __construct() {
        $this->validarParametros();
        $this->configurarDompdf();
        $this->obtenerDatosEnvio();
        $this->obtenerConfiguracion();
        $this->determinarTipoDocumento();
        $this->generarComprobante();
    }
    
    private function validarParametros() {
        $this->idEnvio = isset($_GET['id']) ? $_GET['id'] : $this->terminarConError("Error: ID de envío no proporcionado");
        $this->tipoDocumento = isset($_GET["comprobante"]) ? $_GET["comprobante"] : "boleta";
    }

    
    private function configurarDompdf() {
        $options = new Options();
        $options->set([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultMediaType' => 'print',
            'defaultFont' => 'Arial',
            'chroot' => __DIR__,
            'tempDir' => __DIR__ . '/tmp',
            'logOutputFile' => ''
        ]);
        
        $this->dompdf = new Dompdf($options);
    }
    
    private function obtenerDatosEnvio() {
        $response = ControladorEnvio::ctrMostrarDetalleEnvio($this->idEnvio);
        if (!$response['status'] || empty($response['data'])) {
            $this->terminarConError("Error: No se pudieron obtener los datos del envío");
        }
        
        $this->envio = $response['data']['envio'];
        $this->paquetes = $response['data']['paquetes'];
    }

    private function obtenerConfiguracion() {
        $response = ControladorConfiguracion::ctrMostrarConfiguracionPDF();
        
        // Si la respuesta es string (por si acaso), decodifícala
        if (is_string($response)) {
            $response = json_decode($response, true);
        }
        
        if (!$response || !$response['status'] || empty($response['data'])) {
            $this->terminarConError("Error: Configuración no disponible");
        }
        
        $this->configuracion = $response['data'];
    }
    
    private function determinarTipoDocumento() {
        // Determinar tipo de documento basado en la serie
        $serie = $this->envio['serie'];
        
        if (strtoupper($serie[0]) === 'B') {
            $this->tipoDocumento = 'boleta';
        } elseif (strtoupper($serie[0]) === 'F') {
            $this->tipoDocumento = 'factura';
        }
        
        // Establecer la plantilla correspondiente
        switch ($this->tipoDocumento) {
            case 'ticket':
                $this->templatePath = 'plantillas/ticket.html';
                break;
            case 'guia_remision':
                $this->templatePath = 'plantillas/guia_remision.html';
                break;
            case 'factura':
                $this->templatePath = 'plantillas/comprobante.html';
                break;
            default: // boleta por defecto
                $this->templatePath = 'plantillas/comprobante.html';
                break;
        }
    }
    
    private function generarComprobante() {
        $data = $this->prepararDatosComprobante();
        $html = $this->cargarPlantilla($data);
        $this->generarPdf($html);
    }
    
    private function prepararDatosComprobante() {
        $totalPeso = 0;
        $totalValor = 0;
        $itemsHtml = '';
        
        // Procesar paquetes
        foreach ($this->paquetes as $paquete) {
            $valorUnitario = !empty($paquete['valor_declarado']) ? $paquete['valor_declarado'] : '0.00';
            $totalItem = floatval($valorUnitario);
            
            $itemsHtml .= $this->generarFilaItem(
                1.00,
                'UND',
                $paquete['descripcion'] . ' - ' . $paquete['codigo_paquete'],
                $valorUnitario,
                $totalItem
            );
            
            $totalPeso += floatval($paquete['peso']);
            $totalValor += $totalItem;
        }
        
        // Agregar servicio de envío si corresponde
        if (floatval($this->envio['costo_envio']) > 0) {
            $itemsHtml .= $this->generarFilaItem(
                1.00,
                'SERV',
                'Servicio de envío - ' . $this->envio['codigo_envio'],
                $this->envio['costo_envio'],
                $this->envio['costo_envio']
            );
            
            $totalValor += floatval($this->envio['costo_envio']);
        }
        
        $ruta_logo = '';
        if (!empty($this->configuracion['logo'])) {
            $logoPath = '../' . $this->configuracion['logo'];
            if (file_exists($logoPath)) {
                $ruta_logo = base64_encode(file_get_contents($logoPath));
            }
        }
        
        // Calcular IGV (18% del total)
        $igv = $totalValor * 0.18;
        $subtotal = $totalValor - $igv;
        
        return [
            'empresa_nombre'    => $this->configuracion['nombre_empresa'],
            'empresa_ruc'       => $this->configuracion['ruc'],
            'empresa_direccion' => $this->configuracion['direccion'],
            'empresa_telefono'  => $this->configuracion['telefono'],
            'empresa_email'    => $this->configuracion['email'],
            'empresa_logo' => $ruta_logo, 
            'serie_numero'      => $this->envio['serie'] . '-' . sprintf('%08d', $this->envio['numero_comprobante']),
            'cliente_nombre'    => $this->envio['nombre_remitente'],
            'cliente_dni'       => $this->envio['dni_remitente'],
            'cliente_direccion' => $this->envio['sucursal_origen'] . ' - ' . $this->envio['sucursal_destino'],
            'fecha_emision'     => date('d/m/Y', strtotime($this->envio['fecha_creacion'])),
            'moneda'            => $this->configuracion['moneda'] == 'PEN' ? 'SOLES' : 'DÓLARES',
            'items'             => $itemsHtml,
            'subtotal'          => number_format($subtotal, 2),
            'igv'               => number_format($igv, 2),
            'total'             => $this->configuracion['moneda'] . ' ' . number_format($totalValor, 2),
            'monto_texto'       => 'SON: ' . $this->convertirNumeroALetras($totalValor) . ' ' . ($this->configuracion['moneda'] == 'PEN' ? 'SOLES' : 'DÓLARES AMERICANOS')
        ];
    }
    
    private function generarFilaItem($cantidad, $unidad, $descripcion, $valorUnitario, $totalItem) {
        return sprintf('
            <tr>
                <td>%s</td>
                <td>%s</td>
                <td>%s</td>
                <td align="right">%s</td>
                <td align="right">%s</td>
            </tr>',
            number_format($cantidad, 2),
            htmlspecialchars($unidad),
            htmlspecialchars($descripcion),
            number_format(floatval($valorUnitario), 2),
            number_format(floatval($totalItem), 2)
        );
    }
    
    private function cargarPlantilla($data) {
        $html = file_get_contents($this->templatePath);
        
        if ($html === false) {
            $this->terminarConError("Error: No se pudo cargar la plantilla HTML");
        }
        
        foreach ($data as $key => $value) {
            $html = str_replace('{{'.$key.'}}', $key === 'items' ? $value : htmlspecialchars($value), $html);
        }
        
        return $html;
    }
    
    private function generarPdf($html) {
        $this->dompdf->loadHtml($html);
        
        // Configurar tamaño según tipo de documento
        if ($this->tipoDocumento == "ticket") {
            $this->dompdf->setPaper([0, 0, 226.77, 600], 'portrait');
        } else {
            $this->dompdf->setPaper('A4', 'portrait');
        }
        
        $this->dompdf->render();
        
        // Generar nombre de archivo
        $filename = $this->envio['codigo_envio'] . "_" . date('Ymd_His') . ".pdf";
        
        // Enviar al navegador
        $this->dompdf->stream($filename, ["Attachment" => 0]);
    }
    
    private function convertirNumeroALetras($numero) {
        $parte_entera = floor($numero);
        $parte_decimal = round(($numero - $parte_entera) * 100);
        
        $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];
        
        $texto = '';
        
        if ($parte_entera == 0) {
            $texto = 'CERO';
        } elseif ($parte_entera == 1) {
            $texto = 'UNO';
        } elseif ($parte_entera < 10) {
            $texto = $unidades[$parte_entera];
        } elseif ($parte_entera < 100) {
            $decena = floor($parte_entera / 10);
            $unidad = $parte_entera % 10;
            
            if ($unidad == 0) {
                $texto = $decenas[$decena];
            } else {
                $texto = $decenas[$decena] . ' Y ' . $unidades[$unidad];
            }
        }
        
        return $texto . ' CON ' . sprintf('%02d', $parte_decimal) . '/100';
    }
    
    private function terminarConError($mensaje) {
        die($mensaje);
    }
}

// Ejecutar el generador de comprobantes
new GeneradorComprobante();