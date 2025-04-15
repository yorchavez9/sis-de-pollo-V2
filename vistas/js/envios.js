$(document).ready(function () {

    function fechaHoraActual() {
        const now = new Date();
        const timezoneOffset = now.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(now - timezoneOffset)).toISOString().slice(0, 16);
        $("#fecha_estimada_entrega").val(localISOTime);
    }
    fechaHoraActual();

    // Configuración común para Select2
    const select2Config = {
        placeholder: "Seleccionar",
        width: '100%'
    };

    // Inicializar Select2
    function initSelect2(selector, dropdownParent = null) {
        const config = { ...select2Config };
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
    initSelect2('.select');

    // Reinicializar Select2 en modales
    $('#modalNuevoEnvio, #modalDetalleEnvio, #modalCambiarEstado, #modalSubirDocumento').on('shown.bs.modal', function () {
        initSelect2($(this).find('.select'), $(this));
        fechaHoraActual();
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
                cache: "no-cache",
                credentials: 'same-origin' // Para manejar cookies si es necesario
            };

            if (data instanceof FormData) {
                options.body = data;
            } else if (data) {
                options.headers["Content-Type"] = "application/json";
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);

            if (!response.ok) {
                // Intentar obtener el mensaje de error del cuerpo de la respuesta
                let errorDetails = '';
                try {
                    const errorResponse = await response.json();
                    errorDetails = errorResponse.message || JSON.stringify(errorResponse);
                } catch (e) {
                    errorDetails = await response.text();
                }

                throw new Error(`HTTP error! status: ${response.status}, message: ${errorDetails}`);
            }

            return await response.json();
        } catch (error) {
            console.error("Error en la solicitud:", {
                url,
                method,
                errorName: error.name,
                errorMessage: error.message,
                stack: error.stack // Solo en desarrollo
            });

            return {
                status: false,
                message: "Error en la conexión",
                errorDetails: {
                    name: error.name,
                    message: error.message,
                }
            };
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
            const fechaEnvio = envio.fecha_creacion ? new Date(envio.fecha_creacion).toLocaleString() : 'Pendiente';

            // Determinar clase para el estado
            let claseEstado = "";
            switch (envio.estado) {
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
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle py-1 px-2" type="button" 
                                    id="dropdownMenuButton${envio.id_envio}" data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            
                            <ul class="dropdown-menu shadow-sm" aria-labelledby="dropdownMenuButton${envio.id_envio}">
                                <!-- Opción Ver Detalle -->
                                <li>
                                    <a class="dropdown-item d-flex align-items-center btnDetalleEnvio" href="#" data-id="${envio.id_envio}">
                                        <i class="fas fa-eye text-primary me-2"></i>
                                        <span>Ver Detalle</span>
                                    </a>
                                </li>
                                
                                <!-- Opción Cambiar Estado -->
                                <li>
                                    <a class="dropdown-item d-flex align-items-center btnCambiarEstado" href="#" data-id="${envio.id_envio}">
                                        <i class="fas fa-exchange-alt text-warning me-2"></i>
                                        <span>Cambiar Estado</span>
                                    </a>
                                </li>
                                
                                <!-- Opción Cancelar (condicional) -->
                                ${envio.estado === 'PENDIENTE' || envio.estado === 'PREPARACION' ?
                    `<li>
                                        <a class="dropdown-item d-flex align-items-center btnCancelarEnvio" href="#" data-id="${envio.id_envio}">
                                            <i class="fas fa-times text-danger me-2"></i>
                                            <span>Cancelar Envío</span>
                                        </a>
                                    </li>` : ''
                }
                                
                                <!-- Separador visual -->
                                <li><hr class="dropdown-divider"></li>
                                
                                <!-- Opción adicional (ejemplo) -->
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <i class="fas fa-print text-secondary me-2"></i>
                                        <span>Imprimir</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>`;
            tbody.append(fila);
        });

        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().destroy();
        }
        tabla.DataTable({
            autoWidth: false,
            responsive: true
        });
    };

    // Cargar sucursales en select
    const cargarSerieComprobante = async (selectId, todas = false) => {
        const series = await fetchData("ajax/serie_comprobante.ajax.php");

        if (!series || !series.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        if (todas) {
            select.append('<option value="">Todas</option>');
        } else {
            select.append('<option value="" disabled selected>Seleccionar comprobante</option>');
        }

        // Recorremos las series y las agregamos al select con data-serie
        series.data.forEach(serie => {
            if (serie.estado === 1) {
                select.append(`
                    <option 
                        value="${serie.id_serie}" 
                        data-serie="${serie.serie}"
                    >
                        ${serie.nombre_tipo_comprobante}
                    </option>
                `);
            }
        });

        // Evento change: Cuando seleccionan una opción, actualizar el input
        select.on("change", function () {
            const selectedOption = $(this).find("option:selected");
            const serieValue = selectedOption.data("serie") || ""; // Obtiene "B002", "N002", etc.
            $("#serie").val(serieValue); // Asigna al input
        });
    };

    // Cargar sucursales en select
    const cargarTipoEncomienda = async (selectId, todas = false) => {
        const tipo_encomiendas = await fetchData("ajax/tipo_encomienda.ajax.php");
        if (!tipo_encomiendas || !tipo_encomiendas.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        if (todas) {
            select.append('<option value="">Todas</option>');
        } else {
            select.append('<option value="" disabled selected>Seleccionar sucursal</option>');
        }

        tipo_encomiendas.data.forEach(tipo => {
            if (tipo.estado === 1) {
                select.append(`<option value="${tipo.id_tipo_encomienda}">${tipo.nombre}</option>`);
            }
        });
    };

    // Cargar sucursales en select
    const cargarSucursales = async (selectId, todas = false) => {
        const sucursales = await fetchData("ajax/sucursal.ajax.php");
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
        const transportistas = await fetchData("ajax/transporte.ajax.php");
        if (!transportistas || !transportistas.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="">Seleccionar transportista</option>');

        transportistas.data.forEach(transportista => {
            select.append(`<option value="${transportista.id_transportista}">${transportista.nombre_completo}</option>`);
        });
    };

    // Cargar productos para items de paquetes
    const cargarProductos = async (select) => {
        const productos = await fetchData("ajax/producto.ajax.php");
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
        $paquete.find(".btnEliminarPaquete").click(function () {
            $(this).closest(".paquete").remove();
            contadorPaquetes--;
            actualizarResumen();
        });

        $paquete.find(".btnAgregarItem").click(function () {
            agregarItem($(this).closest(".paquete").find(".table-items tbody"));
        });

        // Cargar productos para este paquete
        cargarProductos($paquete.find(".selectProducto"));

        // Evento para actualizar peso cuando se selecciona producto
        $paquete.find(".selectProducto").change(function () {
            const peso = $(this).find("option:selected").data("peso");
            $(this).closest(".item").find(".pesoUnitario").val(peso);
        });

        // Eventos para actualizar resumen cuando cambian valores
        $paquete.find(".peso, .alto, .ancho, .profundidad").on('input', function () {
            actualizarResumen();
        });

        $("#contenedorPaquetes").append($paquete);
    };

    // Agregar ítem a un paquete
    const agregarItem = (tbody) => {
        contadorItems++;
        const template = $("#templateItemPaquete").html();
        const $item = $(template);

        // Asignar eventos al ítem
        $item.find(".btnEliminarItem").click(function () {
            $(this).closest(".item").remove();
            contadorItems--;
        });

        // Cargar productos para este ítem
        cargarProductos($item.find(".selectProducto"));

        tbody.append($item);
    };

    // Actualizar resumen de paquetes
    const actualizarResumen = () => {
        let totalPaquetes = 0;
        let pesoTotal = 0;
        let volumenTotal = 0;

        $(".paquete").each(function () {
            totalPaquetes++;
            const peso = parseFloat($(this).find(".peso").val()) || 0;
            const alto = parseFloat($(this).find(".alto").val()) || 0;
            const ancho = parseFloat($(this).find(".ancho").val()) || 0;
            const profundidad = parseFloat($(this).find(".profundidad").val()) || 0;

            pesoTotal += peso;
            volumenTotal += (alto * ancho * profundidad) / 1000000; // Convertir a m³
        });

        $("#totalPaquetes").val(totalPaquetes);
        $("#pesoTotal").val(pesoTotal.toFixed(2));
        $("#volumenTotal").val(volumenTotal.toFixed(2));
    };

    // Mostrar detalles de un envío
    const mostrarDetalleEnvio = async (idEnvio) => {
        const response = await fetchData(`ajax/envios.ajax.php?action=detalle&id=${idEnvio}`);
        if (!response || !response.status) {
            Swal.fire("Error", "No se pudo cargar el detalle del envío", "error");
            return;
        }

        const envio = response.data.envio;
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
        switch (envio.estado) {
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
        $("#totalPaquetesDetalle").text(response.data.paquetes.length);

        response.data.paquetes.forEach(paquete => {
            let claseEstadoPaquete = "";
            switch (paquete.estado) {
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

        response.data.seguimiento.forEach(seguimiento => {
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

        response.data.documentos.forEach(documento => {
            const template = $("#templateDocumento").html()
                .replace("{{tipo}}", documento.tipo_documento)
                .replace("{{fecha}}", new Date(documento.fecha_subida).toLocaleDateString())
                .replace("{{url}}", documento.ruta_archivo)
                .replace("{{id}}", documento.id_documento);

            const $doc = $(template);
            $doc.find(".btnVerDocumento").attr("href", documento.ruta_archivo);
            $doc.find(".btnEliminarDocumento").click(function () {
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
            if ($.fn.DataTable.isDataTable("#tablaEnvios")) {
                $("#tablaEnvios").DataTable().destroy();
            }
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
        if (response.status) {
            return response
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
    $("#btnNuevoEnvio").click(function () {
        resetForm();
        $("#modalNuevoEnvio").modal("show");
        // Generar código de envío
        $("#formNuevoEnvio [name='codigo_envio']").val(generarCodigoEnvio());
    });

    // Evento para agregar paquete
    $("#btnAgregarPaquete").click(agregarPaquete);

    // Evento para calcular costo de envío
    $("#btnCalcularCosto").click(async function () {
        const origen = $("#formNuevoEnvio [name='id_sucursal_origen']").val();
        const destino = $("#formNuevoEnvio [name='id_sucursal_destino']").val();
        const tipo = $("#formNuevoEnvio [name='id_tipo_encomienda']").val();
        const pesoTotal = parseFloat($("#pesoTotal").val()) || 0;

        if (!origen || !destino || !tipo) {
            Swal.fire("Advertencia", "Debe completar los datos de origen, destino y tipo de envío", "warning");
            return;
        }

        if (pesoTotal <= 0) {
            Swal.fire("Advertencia", "El peso total debe ser mayor a cero", "warning");
            return;
        }

        // Mostrar carga mientras se calcula
        Swal.fire({
            title: 'Calculando costo...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const costo = await calcularCostoEnvio(origen, destino, tipo, pesoTotal);
            Swal.close();
            if (costo.status) {
                $("#costoEnvio").val(costo.data.costo.toFixed(2));
                Swal.fire("¡Calculado!", `Costo estimado: S/ ${costo.data.costo.toFixed(2)}. Tiempo estimado: ${costo.data.tiempo_estimado} horas`, "success");
            } else {
                Swal.fire("Error", costo?.message || "No se pudo calcular el costo. Verifique los datos.", "error");
            }
        } catch (error) {
            Swal.close();
            Swal.fire("Error", "Ocurrió un error al calcular el costo", "error");
            console.error("Error al calcular costo:", error);
        }
    });

    // Evento para guardar nuevo envío
    $("#formNuevoEnvio").submit(async function (e) {
        e.preventDefault();

        if (!validateEnvioForm()) return;

        // Recolectar datos del formulario
        const formData = new FormData(this);
        formData.append("action", "crear");

        // Recolectar datos de paquetes
        const paquetes = [];
        $(".paquete").each(function (index) {
            const paquete = {
                numero: index + 1,
                descripcion: $(this).find(".descripcion").val(),
                peso: $(this).find(".peso").val(),
                alto: $(this).find(".alto").val(),
                ancho: $(this).find(".ancho").val(),
                profundidad: $(this).find(".profundidad").val(),
                instrucciones: $(this).find(".instrucciones").val(),
                items: []
            };

            // Recolectar items del paquete
            $(this).find(".item").each(function () {
                paquete.items.push({
                    id_producto: $(this).find(".selectProducto").val(),
                    descripcion: $(this).find(".selectProducto option:selected").text(),
                    cantidad: $(this).find(".cantidad").val(),
                    peso_unitario: $(this).find(".pesoUnitario").val(),
                    valor_unitario: $(this).find(".valorUnitario").val() || 0
                });
            });

            paquetes.push(paquete);
        });

        // Agregar paquetes al FormData
        formData.append("paquetes", JSON.stringify(paquetes));

        // Calcular peso total
        const pesoTotal = paquetes.reduce((total, p) => total + parseFloat(p.peso), 0);
        formData.append("peso_total", pesoTotal);

        // Calcular volumen total
        const volumenTotal = paquetes.reduce((total, p) => {
            const volumen = (parseFloat(p.alto) * parseFloat(p.ancho) * parseFloat(p.profundidad)) / 1000000;
            return total + (isNaN(volumen) ? 0 : volumen);
        }, 0);
        formData.append("volumen_total", volumenTotal);

        // Agregar cantidad de paquetes
        formData.append("cantidad_paquetes", paquetes.length);

        // Enviar datos al servidor
        const response = await fetchData("ajax/envios.ajax.php", "POST", formData);
        console.log(response);
        if (response?.status) {
            Swal.fire({
                title: "¡Correcto!",
                text: "Envío creado con éxito",
                icon: "success",
                showCancelButton: true,
                confirmButtonText: "Imprimir Guía",
                cancelButtonText: "Cerrar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Abrir ventana para imprimir la guía
                    window.open(`extensiones/comprobante.php?action=imprimirComprobante&id=${response.id_envio}`, "_blank");
                }
                resetForm();
                $("#modalNuevoEnvio").modal("hide");
                if ($.fn.DataTable.isDataTable("#tablaEnvios")) {
                    $("#tablaEnvios").DataTable().destroy();
                }
                mostrarEnvios();
            });
        } else {
            Swal.fire("Error", response?.message || "Error al crear el envío", "error");
        }
    });

    // Evento para filtrar envíos
    $("#btnFiltrarEnvios").click(function (e) {
        e.preventDefault();
        const filtros = {
            origen: $("#filtroOrigen").val(),
            destino: $("#filtroDestino").val(),
            tipo: $("#filtroTipo").val(),
            estado: $("#filtroEstado").val()
        };
        if ($.fn.DataTable.isDataTable("#tablaEnvios")) {
            $("#tablaEnvios").DataTable().destroy();
        }
        mostrarEnvios(filtros);
    });

    // Evento para ver detalle de envío
    $("#tablaEnvios").on("click", ".btnDetalleEnvio", function () {
        const idEnvio = $(this).data("id");
        mostrarDetalleEnvio(idEnvio);
        $("#modalDetalleEnvio").modal("show");
    });

    // Evento para cambiar estado de envío
    $("#tablaEnvios").on("click", ".btnCambiarEstado", function () {
        const idEnvio = $(this).data("id");
        $("#idEnvioEstado").val(idEnvio);
        $("#modalCambiarEstado").modal("show");
    });

    // Evento para cancelar envío
    $("#tablaEnvios").on("click", ".btnCancelarEnvio", async function () {
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
    $("#formCambiarEstado").submit(function (e) {
        e.preventDefault();
        const idEnvio = $("#idEnvioEstado").val();
        const estado = $("#nuevoEstado").val();
        const observaciones = $("#observacionesEstado").val();
        actualizarEstadoEnvio(idEnvio, estado, observaciones);
    });

    // Evento para abrir modal de subir documento
    $("#btnSubirDocumento").click(function () {
        if (!envioActual) return;
        $("#idEnvioDocumento").val(envioActual.id_envio);
        $("#modalSubirDocumento").modal("show");
    });

    // Evento para subir documento
    $("#formSubirDocumento").submit(function (e) {
        e.preventDefault();
        if (!envioActual) return;
        const formData = new FormData(this);
        subirDocumento(envioActual.id_envio, formData);
    });

    // Evento para imprimir guía
    $("#btnImprimirGuia").click(function () {
        if (!envioActual) return;
        window.open(`extensiones/guia_remision.php?action=imprimirGuia&comprobante=guia_remision&id=${envioActual.id_envio}`, "_blank");
    });

    // Evento para agregar seguimiento
    $("#btnNuevoSeguimiento").click(function () {
        if (!envioActual) return;
        $("#idEnvioEstado").val(envioActual.id_envio);
        $("#modalCambiarEstado").modal("show");
    });

    // Cargar datos iniciales
    cargarSucursales("filtroOrigen", true);
    cargarSucursales("filtroDestino", true);
    cargarSerieComprobante("id_serie");
    cargarTipoEncomienda("id_tipo_encomienda");
    cargarSucursales("id_sucursal_origen");
    cargarSucursales("id_sucursal_destino");
    cargarTransportistas("id_transportista");
    if ($.fn.DataTable.isDataTable("#tablaEnvios")) {
        $("#tablaEnvios").DataTable().destroy();
    }
    mostrarEnvios();
});