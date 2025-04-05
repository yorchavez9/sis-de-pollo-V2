function formatCurrency(value) {
    if (!value) return "S/ 0.00";
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(value);
}

   // Función para hacer fetch
   const fetchData = async (url, method = "GET", data = null) => {
    try {
        const options = {
            method,
            body: data,
            cache: "no-cache",
            headers: data ? {} : { "Content-Type": "application/json" },
        };
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error("Error en la solicitud:", error);
        return null;
    }
};

// Agregar evento para mostrar el modal de impresión
$("#tabla_productos").on("click", ".btnImprimirCodigo", async function() {
    const idProducto = $(this).attr("idProducto");
    const codigoBarras = $(this).attr("codigoBarras");
    
    // Obtener datos del producto
    const formData = new FormData();
    formData.append('id_producto', idProducto);
    formData.append('action', "editar");
    
    const response = await fetchData("ajax/producto.ajax.php", "POST", formData);
    console.log(response);
    if (response?.status) {
        const producto = response.data;
        
        // Mostrar información en el modal
        $("#nombre_producto_codigo").text(producto.nombre);
        $("#precio_producto_codigo").text(formatCurrency(producto.precio_venta));
        $("#codigo_producto_texto").text(`Código: ${producto.codigo}`);
        $("#codigo_barra_producto").val(producto.codigo_barras);
        
        // Generar código de barras
        generarCodigoBarras(codigoBarras);
    } else {
        Swal.fire("Error", "No se pudieron cargar los datos del producto", "error");
    }
});

// Función para generar el código de barras
function generarCodigoBarras(codigo) {
    // Limpiar contenedor
    $("#contenedor_codigo_barras").empty();
    
    // Usar la librería JsBarcode
    if (typeof JsBarcode !== 'undefined') {
        const canvas = document.createElement("canvas");
        $("#contenedor_codigo_barras").append(canvas);
        
        JsBarcode(canvas, codigo, {
            format: "CODE128",
            lineColor: "#000",
            width: 2,
            height: 50,
            displayValue: true,
            fontSize: 16,
            margin: 10
        });
    } else {
        $("#contenedor_codigo_barras").html(`
            <div class="alert alert-warning">
                No se pudo cargar la librería de códigos de barras. 
                El código es: <strong>${codigo}</strong>
            </div>
        `);
    }
}



$("#btn_imprimir_codigo").click(function() {
    const cantidad = $("#cantidad_codigos").val();
    const tamano = $("#tamano_codigos").val();
    const nombre = $("#nombre_producto_codigo").text();
    const precio = $("#precio_producto_codigo").text();
    const codigo = $("#codigo_producto_texto").text().replace("Código: ", "");
    const codigoBarras = $("#codigo_barra_producto").val();
    
    // Crear URL con parámetros
    const params = new URLSearchParams({
        cantidad: cantidad,
        tamano: tamano,
        nombre: encodeURIComponent(nombre),
        precio: encodeURIComponent(precio),
        codigo: encodeURIComponent(codigo),
        codigoBarras: encodeURIComponent(codigoBarras)
    });
    
    // Abrir ventana con la plantilla
    const ventana = window.open(`extensiones/plantilla_impresion_codigos.php?${params}`, '_blank');
});