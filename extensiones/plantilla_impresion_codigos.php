<?php
// Obtener parámetros de la URL
$cantidad = isset($_GET['cantidad']) ? intval($_GET['cantidad']) : 1;
$tamano = isset($_GET['tamano']) ? $_GET['tamano'] : 'medium';
$nombre = isset($_GET['nombre']) ? htmlspecialchars(urldecode($_GET['nombre'])) : '';
$precio = isset($_GET['precio']) ? htmlspecialchars(urldecode($_GET['precio'])) : '';
$codigo = isset($_GET['codigo']) ? htmlspecialchars(urldecode($_GET['codigo'])) : '';
$codigoBarras = isset($_GET['codigoBarras']) ? htmlspecialchars(urldecode($_GET['codigoBarras'])) : '';

// Determinar el texto del tamaño
function obtenerTextoSize($tamano)
{
    switch ($tamano) {
        case 'small':
            return 'Pequeña (25x15mm)';
        case 'large':
            return 'Grande (75x50mm)';
        default:
            return 'Mediana (50x30mm)';
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Códigos de Barras</title>
    <style>
        body {
            margin: 0;
            padding: 10px;
            font-family: Arial, sans-serif;
        }

        .etiqueta {
            border: 1px dotted #ccc;
            margin: 5px;
            padding: 5px;
            display: inline-block;
            text-align: center;
            page-break-inside: avoid;
        }

        .codigo-barras {
            margin: 5px auto;
        }

        .nombre {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .precio {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .codigo {
            font-size: 0.8em;
            color: #666;
        }

        .controles-impresion {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .btn-imprimir {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        .btn-cerrar {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            .etiqueta {
                border: none;
            }
        }

        /* Agregar al estilo existente */
        .codigo-barras-container {
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            width: 100%;
        }

        .etiqueta {
            box-sizing: border-box;
            overflow: hidden;
            position: relative;
        }

        .nombre {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
            display: inline-block;
        }

        @media print {
            .etiqueta {
                break-inside: avoid;
            }

            .codigo-barras-container svg {
                max-width: 100% !important;
                height: auto !important;
            }
        }
    </style>
</head>

<body>
    <div class="controles-impresion no-print">
        <h1>Previsualización de etiquetas</h1>
        <p id="info-configuracion">Cantidad: <?php echo $cantidad; ?> | Tamaño: <?php echo obtenerTextoSize($tamano); ?></p>
        <button class="btn-imprimir" onclick="window.print()">Imprimir</button>
        <button class="btn-cerrar" onclick="window.close()">Cerrar</button>
        <hr>
    </div>

    <div id="contenedor-etiquetas">
        <?php
        // Configurar estilos según el tamaño
        $estilo = '';
        switch ($tamano) {
            case 'small':
                $estilo = 'width: 25mm; height: 15mm; font-size: 8px;';
                break;
            case 'large':
                $estilo = 'width: 75mm; height: 50mm; font-size: 20px;';
                break;
            default: // medium
                $estilo = 'width: 50mm; height: 30mm; font-size: 14px;';
        }

        // Generar las etiquetas
        for ($i = 0; $i < $cantidad; $i++) {
            echo '
            <div class="etiqueta" style="' . $estilo . '">
                <div class="nombre" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . $nombre . '</div>
                <div class="precio">' . $precio . '</div>
                <div class="codigo-barras-container" style="overflow: hidden; width: 100%;">
                    <svg id="barcode-' . $i . '" style="max-width: 100%; height: auto;"></svg>
                </div>
                <div class="codigo">' . $codigo . '</div>
            </div>';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        // En la parte del script JavaScript, modificar la configuración:
        window.onload = function() {
            const tamano = '<?php echo $tamano; ?>';
            const codigoBarras = '<?php echo $codigoBarras; ?>';
            const cantidad = <?php echo $cantidad; ?>;

            // Calcular ancho dinámico basado en la longitud del código
            const codeLength = codigoBarras.length;
            let baseWidth = 2; // Ancho base para tamaño medium

            if (codeLength > 15) {
                baseWidth = Math.max(1, 2 - (codeLength - 15) * 0.1); // Reducir ancho para códigos largos
            }

            // Configuración del código de barras según tamaño
            const config = {
                format: "CODE128",
                lineColor: "#000",
                displayValue: true,
                margin: 5,
                marginTop: 5,
                marginBottom: 5,
                marginLeft: 5,
                marginRight: 5
            };

            // Ajustar configuración según tamaño
            switch (tamano) {
                case 'small':
                    config.width = Math.max(0.5, 1 - (codeLength / 50)); // Más ajustado
                    config.height = 15; // Menor altura
                    config.fontSize = Math.max(6, 8 - Math.floor(codeLength / 5));
                    config.displayValue = codeLength < 10; // Solo mostrar texto si el código es corto
                    break;
                case 'large':
                    config.width = Math.max(1.5, 3 - (codeLength / 20));
                    config.height = 50;
                    config.fontSize = Math.max(10, 16 - Math.floor(codeLength / 3));
                    config.displayValue = true;
                    break;
                default: // medium
                    config.width = Math.max(1, 2 - (codeLength / 30));
                    config.height = 30;
                    config.fontSize = Math.max(8, 12 - Math.floor(codeLength / 4));
                    config.displayValue = true;
            }

            // Generar todos los códigos de barras
            for (let i = 0; i < cantidad; i++) {
                try {
                    JsBarcode(`#barcode-${i}`, codigoBarras, config);
                } catch (e) {
                    console.error("Error generando código de barras:", e);
                    document.getElementById(`barcode-${i}`).innerHTML =
                        `<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" 
                 font-size="${config.fontSize}px">${codigoBarras}</text>`;
                }
            }

            // Ajustar visualmente si es necesario
            setTimeout(() => {
                document.querySelectorAll('.codigo-barras-container svg').forEach(svg => {
                    const bbox = svg.getBBox();
                    const viewBox = [bbox.x, bbox.y, bbox.width, bbox.height].join(' ');
                    svg.setAttribute('viewBox', viewBox);
                    svg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
                });
            }, 100);
        };
    </script>
</body>

</html>