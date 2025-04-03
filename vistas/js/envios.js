$(document).ready(function () {
    // Configuración común para Select2
    const select2Config = {
        placeholder: "Seleccionar",
        width: '100%'
    };

    // Inicializar Select2
    function initSelect2(selector, dropdownParent = null) {
        const config = {...select2Config};
        if (dropdownParent) {
            config.dropdownParent = dropdownParent;
        }
        $(selector).select2(config);
    }

    // Variables globales
    let contadorPaquetes = 0;
    let contadorItems = 0;
    let envioActual = null;

    // Inicializar todos los Select2 al cargar
    initSelect2('.select2');
    
    // Reinicializar Select2 en modales
    $('#modalNuevoEnvio, #modalDetalleEnvio, #modalCambiarEstado, #modalSubirDocumento').on('shown.bs.modal', function() {
        initSelect2($(this).find('.select2'), $(this));
    });

    // Función para validar campos
    const validateField = (field, regex, errorField, errorMessage) => {
        const value = field.val();
        if (!value) {
            errorField.html("Este campo es obligatorio").addClass("text-danger");
            return false;
        } else if (regex && !regex.test(value)) {
            errorField.html(errorMessage).addClass("text-danger");
            return false;
        } else {
            errorField.html("").removeClass("text-danger");
            return true;
        }
    };

    // Validación de formulario de envío
    const validateEnvioForm = () => {
        const form = $("#formNuevoEnvio");
        const isValid = [
            validateField(form.find("[name='id_sucursal_origen']"), null, form.find("#error_origen"), "Selección inválida"),
            validateField(form.find("[name='id_sucursal_destino']"), null, form.find("#error_destino"), "Selección inválida"),
            validateField(form.find("[name='id_tipo_encomienda']"), null, form.find("#error_tipo"), "Selección inválida"),
            // Validar que hay al menos un paquete
            contadorPaquetes > 0
        ].every(Boolean);
        
        if (contadorPaquetes === 0) {
            Swal.fire("Advertencia", "Debe agregar al menos un paquete al envío", "warning");
            return false;
        }
        
        return isValid;
    };

    // Resetear formulario
    const resetForm = () => {
        $("#formNuevoEnvio")[0].reset();
        $("#contenedorPaquetes").empty();
        contadorPaquetes = 0;
        contadorItems = 0;
        $(".error-message").html("").removeClass("text-danger");
    };

    // Función para hacer fetch
    const fetchData = async (url, method = "GET", data = null) => {
        try {
            const options = {
                method,
                headers: {},
                cache: "no-cache"
            };
            
            if (data instanceof FormData) {
                options.body = data;
            } else if (data) {
                options.headers["Content-Type"] = "application/json";
                options.body = JSON.stringify(data);
            }
            
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            console.error("Error en la solicitud:", error);
            return { status: false, message: "Error en la conexión" };
        }
    };

    // Mostrar lista de envíos
    const mostrarEnvios = async (filtros = {}) => {
        // Construir URL con filtros
        let url = "ajax/envios.ajax.php?action=listar";
        if (Object.keys(filtros).length > 0) {
            const params = new URLSearchParams(filtros);
            url += `&${params.toString()}`;
        }

        const envios = await fetchData(url);
        if (!envios || !envios.status) {
            console.error("Error al cargar envíos:", envios?.message);
            return;
        }

        const tabla = $("#tablaEnvios");
        const tbody = tabla.find("tbody");
        tbody.empty();

        envios.data.forEach((envio, index) => {
            // Formatear fechas
            const fechaEnvio = envio.fecha_envio ? new Date(envio.fecha_envio).toLocaleString() : 'Pendiente';
            
            // Determinar clase para el estado
            let claseEstado = "";
            switch(envio.estado) {
                case 'PENDIENTE': claseEstado = "badge bg-secondary"; break;
                case 'PREPARACION': claseEstado = "badge bg-warning text-dark"; break;
                case 'EN_TRANSITO': claseEstado = "badge bg-primary"; break;
                case 'EN_REPARTO': claseEstado = "badge bg-info"; break;
                case 'ENTREGADO': claseEstado = "badge bg-success"; break;
                case 'CANCELADO': case 'RECHAZADO': claseEstado = "badge bg-danger"; break;
                default: claseEstado = "badge bg-secondary";
            }

            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${envio.codigo_envio}</td>
                    <td>${envio.sucursal_origen}</td>
                    <td>${envio.sucursal_destino}</td>
                    <td>${envio.tipo_encomienda}</td>
                    <td>${fechaEnvio}</td>
                    <td><span class="${claseEstado}">${envio.estado.replace('_', ' ')}</span></td>
                    <td>${envio.transportista || 'No asignado'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info btnDetalleEnvio me-1" data-id="${envio.id_envio}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning btnCambiarEstado me-1" data-id="${envio.id_envio}">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                        ${envio.estado === 'PENDIENTE' || envio.estado === 'PREPARACION' ? 
                            `<button class="btn btn-sm btn-danger btnCancelarEnvio" data-id="${envio.id_envio}">
                                <i class="fas fa-times"></i>
                            </button>` : ''
                        }
                    </td>
                </tr>`;
            tbody.append(fila);
        });

        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().destroy();
        }
        tabla.DataTable({
            autoWidth: false,
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
    };

    // Cargar sucursales en select
    const cargarSucursales = async (selectId, todas = false) => {
        const sucursales = await fetchData("ajax/sucursal.ajax.php?estado=1");
        if (!sucursales || !sucursales.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        if (todas) {
            select.append('<option value="">Todas</option>');
        } else {
            select.append('<option value="" disabled selected>Seleccionar sucursal</option>');
        }
        
        sucursales.data.forEach(sucursal => {
            if (sucursal.estado === 1) {
                select.append(`<option value="${sucursal.id_sucursal}">${sucursal.nombre}</option>`);
            }
        });
    };

    // Cargar transportistas
    const cargarTransportistas = async (selectId) => {
        const transportistas = await fetchData("ajax/transportistas.ajax.php?estado=1");
        if (!transportistas || !transportistas.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="">Seleccionar transportista</option>');
        
        transportistas.data.forEach(transportista => {
            select.append(`<option value="${transportista.id_transportista}">${transportista.nombre} - ${transportista.tipo_vehiculo || 'Sin vehículo'}</option>`);
        });
    };

    // Cargar productos para items de paquetes
    const cargarProductos = async (select) => {
        const productos = await fetchData("ajax/productos.ajax.php?estado=1");
        if (!productos || !productos.status) return;

        select.empty();
        select.append('<option value="" disabled selected>Seleccionar producto</option>');
        
        productos.data.forEach(producto => {
            select.append(`<option value="${producto.id_producto}" data-peso="${producto.peso || 0}">${producto.codigo} - ${producto.nombre}</option>`);
        });
    };

    // Agregar paquete al formulario
    const agregarPaquete = () => {
        contadorPaquetes++;
        const template = $("#templatePaquete").html();
        const $paquete = $(template.replace(/{{numero}}/g, contadorPaquetes));
        
        // Asignar eventos al paquete
        $paquete.find(".btnEliminarPaquete").click(function() {
            $(this).closest(".paquete").remove();
            contadorPaquetes--;
        });
        
        $paquete.find(".btnAgregarItem").click(function() {
            agregarItem($(this).closest(".paquete").find(".table-items tbody"));
        });
        
        // Cargar productos para este paquete
        cargarProductos($paquete.find(".selectProducto"));
        
        // Evento para actualizar peso cuando se selecciona producto
        $paquete.find(".selectProducto").change(function() {
            const peso = $(this).find("option:selected").data("peso");
            $(this).closest(".item").find(".pesoUnitario").val(peso);
        });
        
        $("#contenedorPaquetes").append($paquete);
    };

    // Agregar ítem a un paquete
    const agregarItem = (tbody) => {
        contadorItems++;
        const template = $("#templateItemPaquete").html();
        const $item = $(template);
        
        // Asignar eventos al ítem
        $item.find(".btnEliminarItem").click(function() {
            $(this).closest(".item").remove();
            contadorItems--;
        });
        
        // Cargar productos para este ítem
        cargarProductos($item.find(".selectProducto"));
        
        tbody.append($item);
    };

    // Mostrar detalles de un envío
    const mostrarDetalleEnvio = async (idEnvio) => {
        const response = await fetchData(`ajax/envios.ajax.php?action=detalle&id=${idEnvio}`);
        if (!response || !response.status) {
            Swal.fire("Error", "No se pudo cargar el detalle del envío", "error");
            return;
        }

        const envio = response.data;
        envioActual = envio;
        
        // Actualizar información básica
        $("#codigoEnvio").text(envio.codigo_envio);
        $("#detalleCodigo").text(envio.codigo_envio);
        $("#detalleOrigen").text(envio.sucursal_origen);
        $("#detalleDestino").text(envio.sucursal_destino);
        $("#detalleTipo").text(envio.tipo_encomienda);
        $("#detalleTransportista").text(envio.transportista || 'No asignado');
        $("#detalleFechaCreacion").text(new Date(envio.fecha_creacion).toLocaleString());
        $("#detalleFechaEnvio").text(envio.fecha_envio ? new Date(envio.fecha_envio).toLocaleString() : 'Pendiente');
        $("#detalleFechaRecepcion").text(envio.fecha_recepcion ? new Date(envio.fecha_recepcion).toLocaleString() : 'Pendiente');
        
        // Estado
        let claseEstado = "";
        switch(envio.estado) {
            case 'PENDIENTE': claseEstado = "badge bg-secondary"; break;
            case 'PREPARACION': claseEstado = "badge bg-warning text-dark"; break;
            case 'EN_TRANSITO': claseEstado = "badge bg-primary"; break;
            case 'EN_REPARTO': claseEstado = "badge bg-info"; break;
            case 'ENTREGADO': claseEstado = "badge bg-success"; break;
            case 'CANCELADO': case 'RECHAZADO': claseEstado = "badge bg-danger"; break;
            default: claseEstado = "badge bg-secondary";
        }
        $("#detalleEstado").html(`<span class="${claseEstado}">${envio.estado.replace('_', ' ')}</span>`);
        
        // Mostrar paquetes
        const tbodyPaquetes = $("#tablaPaquetes tbody");
        tbodyPaquetes.empty();
        $("#totalPaquetes").text(envio.paquetes.length);
        
        envio.paquetes.forEach(paquete => {
            let claseEstadoPaquete = "";
            switch(paquete.estado) {
                case 'BUENO': claseEstadoPaquete = "badge bg-success"; break;
                case 'DANADO': claseEstadoPaquete = "badge bg-danger"; break;
                case 'PERDIDO': claseEstadoPaquete = "badge bg-dark"; break;
                case 'ENTREGADO': claseEstadoPaquete = "badge bg-primary"; break;
                default: claseEstadoPaquete = "badge bg-secondary";
            }
            
            const fila = `
                <tr>
                    <td>${paquete.codigo_paquete}</td>
                    <td>${paquete.descripcion}</td>
                    <td>${paquete.peso} kg</td>
                    <td>${paquete.volumen ? paquete.volumen + ' m³' : 'N/A'}</td>
                    <td><span class="${claseEstadoPaquete}">${paquete.estado}</span></td>
                </tr>`;
            tbodyPaquetes.append(fila);
        });
        
        // Mostrar seguimiento
        const timeline = $("#timelineSeguimiento");
        timeline.empty();
        
        envio.seguimiento.forEach(seguimiento => {
            const template = $("#templateSeguimiento").html()
                .replace("{{estado}}", seguimiento.estado_nuevo.replace('_', ' '))
                .replace("{{fecha}}", new Date(seguimiento.fecha_registro).toLocaleString())
                .replace("{{observaciones}}", seguimiento.observaciones || 'Sin observaciones')
                .replace("{{usuario}}", seguimiento.usuario || 'Sistema');
            timeline.append(template);
        });
        
        // Mostrar documentos
        const listaDocumentos = $("#listaDocumentos");
        listaDocumentos.empty();
        
        envio.documentos.forEach(documento => {
            const template = $("#templateDocumento").html()
                .replace("{{tipo}}", documento.tipo_documento)
                .replace("{{fecha}}", new Date(documento.fecha_subida).toLocaleDateString())
                .replace("{{url}}", documento.ruta_archivo)
                .replace("{{id}}", documento.id_documento);
            
            const $doc = $(template);
            $doc.find(".btnVerDocumento").attr("href", documento.ruta_archivo);
            $doc.find(".btnEliminarDocumento").click(function() {
                eliminarDocumento(documento.id_documento);
            });
            
            listaDocumentos.append($doc);
        });
        
        // Mostrar modal
        $("#modalDetalleEnvio").modal("show");
    };

    // Actualizar estado de un envío
    const actualizarEstadoEnvio = async (idEnvio, estado, observaciones = null) => {
        const formData = new FormData();
        formData.append("id_envio", idEnvio);
        formData.append("estado", estado);
        formData.append("observaciones", observaciones);
        formData.append("action", "cambiarEstado");
        
        const response = await fetchData("ajax/envios.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del envío actualizado", "success");
            mostrarEnvios();
            if (envioActual && envioActual.id_envio == idEnvio) {
                mostrarDetalleEnvio(idEnvio);
            }
            $("#modalCambiarEstado").modal("hide");
        } else {
            Swal.fire("Error", response?.message || "Error al actualizar el estado", "error");
        }
    };

    // Subir documento para un envío
    const subirDocumento = async (idEnvio, formData) => {
        formData.append("id_envio", idEnvio);
        formData.append("action", "subirDocumento");
        
        const response = await fetchData("ajax/envios.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Documento subido con éxito", "success");
            mostrarDetalleEnvio(idEnvio);
            $("#modalSubirDocumento").modal("hide");
            $("#formSubirDocumento")[0].reset();
        } else {
            Swal.fire("Error", response?.message || "Error al subir el documento", "error");
        }
    };

    // Eliminar documento
    const eliminarDocumento = async (idDocumento) => {
        const result = await Swal.fire({
            title: "¿Eliminar documento?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar"
        });
        
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_documento", idDocumento);
            formData.append("action", "eliminarDocumento");
            
            const response = await fetchData("ajax/envios.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", "Documento eliminado", "success");
                mostrarDetalleEnvio(envioActual.id_envio);
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el documento", "error");
            }
        }
    };

    // Calcular costo de envío
    const calcularCostoEnvio = async (idSucursalOrigen, idSucursalDestino, idTipoEncomienda, pesoTotal) => {
        const response = await fetchData(`ajax/envios.ajax.php?action=calcularCosto&origen=${idSucursalOrigen}&destino=${idSucursalDestino}&tipo=${idTipoEncomienda}&peso=${pesoTotal}`);
        if (response?.status) {
            return response.data;
        }
        return 0;
    };

    // Generar código único para envío
    const generarCodigoEnvio = () => {
        const fecha = new Date();
        const year = fecha.getFullYear().toString().slice(-2);
        const month = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const day = fecha.getDate().toString().padStart(2, '0');
        const random = Math.floor(1000 + Math.random() * 9000);
        return `ENV${year}${month}${day}${random}`;
    };

    // Evento para abrir modal de nuevo envío
    $(".btn-added").click(function() {
        resetForm();
        // Generar código de envío
        $("#formNuevoEnvio [name='codigo_envio']").val(generarCodigoEnvio());
    });

    // Evento para agregar paquete
    $("#btnAgregarPaquete").click(agregarPaquete);

    // Evento para guardar nuevo envío
    $("#formNuevoEnvio").submit(async function(e) {
        e.preventDefault();
        
        if (!validateEnvioForm()) return;
        
        // Recolectar datos del formulario
        const formData = new FormData(this);
        formData.append("action", "crear");
        formData.append("codigo_envio", generarCodigoEnvio());
        
        // Recolectar datos de paquetes
        const paquetes = [];
        $(".paquete").each(function(index) {
            const paquete = {
                descripcion: $(this).find(".descripcion").val(),
                peso: $(this).find(".peso").val(),
                alto: $(this).find(".alto").val(),
                ancho: $(this).find(".ancho").val(),
                profundidad: $(this).find(".profundidad").val(),
                instrucciones: $(this).find(".instrucciones").val(),
                items: []
            };
            
            // Recolectar items del paquete
            $(this).find(".item").each(function() {
                paquete.items.push({
                    id_producto: $(this).find(".selectProducto").val(),
                    descripcion: $(this).find(".selectProducto option:selected").text(),
                    cantidad: $(this).find(".cantidad").val(),
                    peso_unitario: $(this).find(".pesoUnitario").val(),
                    valor_unitario: 0 // Podría calcularse si es necesario
                });
            });
            
            paquetes.push(paquete);
        });
        
        // Agregar paquetes al FormData
        formData.append("paquetes", JSON.stringify(paquetes));
        
        // Calcular peso total
        const pesoTotal = paquetes.reduce((total, p) => total + parseFloat(p.peso), 0);
        formData.append("peso_total", pesoTotal);
        
        // Calcular costo de envío
        const costoEnvio = await calcularCostoEnvio(
            formData.get("id_sucursal_origen"),
            formData.get("id_sucursal_destino"),
            formData.get("id_tipo_encomienda"),
            pesoTotal
        );
        formData.append("costo_envio", costoEnvio);
        
        // Enviar datos al servidor
        const response = await fetchData("ajax/envios.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Envío creado con éxito", "success");
            resetForm();
            $("#modalNuevoEnvio").modal("hide");
            mostrarEnvios();
        } else {
            Swal.fire("Error", response?.message || "Error al crear el envío", "error");
        }
    });

    // Evento para filtrar envíos
    $("#formFiltroEnvios").submit(function(e) {
        e.preventDefault();
        const filtros = {
            origen: $("#filtroOrigen").val(),
            destino: $("#filtroDestino").val(),
            tipo: $("#filtroTipo").val(),
            estado: $("#filtroEstado").val()
        };
        mostrarEnvios(filtros);
    });

    // Evento para ver detalle de envío
    $("#tablaEnvios").on("click", ".btnDetalleEnvio", function() {
        const idEnvio = $(this).data("id");
        mostrarDetalleEnvio(idEnvio);
    });

    // Evento para cambiar estado de envío
    $("#tablaEnvios").on("click", ".btnCambiarEstado", function() {
        const idEnvio = $(this).data("id");
        $("#idEnvioEstado").val(idEnvio);
        $("#modalCambiarEstado").modal("show");
    });

    // Evento para cancelar envío
    $("#tablaEnvios").on("click", ".btnCancelarEnvio", async function() {
        const idEnvio = $(this).data("id");
        
        const result = await Swal.fire({
            title: "¿Cancelar este envío?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, cancelar"
        });
        
        if (result.isConfirmed) {
            await actualizarEstadoEnvio(idEnvio, "CANCELADO", "Envío cancelado por el usuario");
        }
    });

    // Evento para enviar formulario de cambio de estado
    $("#formCambiarEstado").submit(function(e) {
        e.preventDefault();
        const idEnvio = $("#idEnvioEstado").val();
        const estado = $("#nuevoEstado").val();
        const observaciones = $("#observacionesEstado").val();
        actualizarEstadoEnvio(idEnvio, estado, observaciones);
    });

    // Evento para abrir modal de subir documento
    $("#btnSubirDocumento").click(function() {
        if (!envioActual) return;
        $("#idEnvioDocumento").val(envioActual.id_envio);
        $("#modalSubirDocumento").modal("show");
    });

    // Evento para subir documento
    $("#formSubirDocumento").submit(function(e) {
        e.preventDefault();
        if (!envioActual) return;
        const formData = new FormData(this);
        subirDocumento(envioActual.id_envio, formData);
    });

    // Evento para imprimir guía
    $("#btnImprimirGuia").click(function() {
        if (!envioActual) return;
        window.open(`ajax/envios.ajax.php?action=imprimirGuia&id=${envioActual.id_envio}`, "_blank");
    });

    // Evento para agregar seguimiento
    $("#btnNuevoSeguimiento").click(function() {
        if (!envioActual) return;
        $("#idEnvioEstado").val(envioActual.id_envio);
        $("#modalCambiarEstado").modal("show");
    });

    // Cargar datos iniciales
    cargarSucursales("filtroOrigen", true);
    cargarSucursales("filtroDestino", true);
    cargarSucursales("id_sucursal_origen");
    cargarSucursales("id_sucursal_destino");
    cargarTransportistas("id_transportista");
    mostrarEnvios();
});