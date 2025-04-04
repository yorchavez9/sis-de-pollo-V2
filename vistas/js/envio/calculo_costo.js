// Función mejorada para calcular costo
const calcularCostoEnvio = async (idSucursalOrigen, idSucursalDestino, idTipoEncomienda, pesoTotal) => {
    const response = await fetchData(`ajax/envios.ajax.php?action=calcularCosto&origen=${idSucursalOrigen}&destino=${idSucursalDestino}&tipo=${idTipoEncomienda}&peso=${pesoTotal}`);
    
    if (response?.status) {
        // Actualizar el resumen en el modal
        $("#costoEnvioResumen").text(`$${response.data.toFixed(2)}`);
        return response.data;
    }
    
    return 0;
};

// Evento para el botón calcular costo
$("#btnCalcularCosto").click(async function() {
    const origen = $("[name='id_sucursal_origen']").val();
    const destino = $("[name='id_sucursal_destino']").val();
    const tipo = $("[name='id_tipo_encomienda']").val();
    
    if (!origen || !destino || !tipo) {
        Swal.fire("Advertencia", "Seleccione origen, destino y tipo de envío primero", "warning");
        return;
    }
    
    // Calcular peso total
    let pesoTotal = 0;
    $(".paquete").each(function() {
        pesoTotal += parseFloat($(this).find(".peso").val()) || 0;
    });
    
    if (pesoTotal <= 0) {
        Swal.fire("Advertencia", "Agregue al menos un paquete con peso válido", "warning");
        return;
    }
    
    await calcularCostoEnvio(origen, destino, tipo, pesoTotal);
});