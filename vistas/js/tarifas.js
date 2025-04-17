$(document).ready(function () {

    async function obtenerSesion() {
        try {
            const response = await fetch('ajax/sesion.ajax.php?action=sesion', {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            });

            if (!response.ok) throw new Error('Error en la respuesta del servidor');

            const data = await response.json();
            return data.status === false ? null : data;

        } catch (error) {
            console.error('Error al obtener sesión:', error);
            return null;
        }
    }


    function formatCurrency(value) {
        if (!value) return "S/ 0.00";
        return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(value);
    }


    // Configuración común para Select2
    const select2Config = {
        placeholder: "Seleccionar",
    };

    // Inicializar Select2
    function initSelect2(selector, dropdownParent = null) {
        const config = {...select2Config};
        if (dropdownParent) {
            config.dropdownParent = dropdownParent;
        }
        $(selector).select2(config);
    }

    // Inicializar todos los Select2 al cargar
    initSelect2('.js-example-basic-single');
    
    // Reinicializar Select2 en modales
    $('#modal_nueva_tarifa, #modal_editar_tarifa').on('shown.bs.modal', function() {
        initSelect2($(this).find('.js-example-basic-single'), $(this));
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

    // Validación de formulario de tarifa
    const validateTarifaForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        
        // Validar campos obligatorios
        const isValid = [
            validateField(form.find(`#${prefix}sucursal_origen`), null, form.find(`#error_${prefix}sucursal_origen`), "Selección inválida"),
            validateField(form.find(`#${prefix}sucursal_destino`), null, form.find(`#error_${prefix}sucursal_destino`), "Selección inválida"),
            validateField(form.find(`#${prefix}tipo_encomienda`), null, form.find(`#error_${prefix}tipo_encomienda`), "Selección inválida"),
            validateField(form.find(`#${prefix}rango_peso_min`), /^\d+(\.\d{1,2})?$/, form.find(`#error_${prefix}rango_peso_min`), "Peso inválido"),
            validateField(form.find(`#${prefix}rango_peso_max`), /^\d+(\.\d{1,2})?$/, form.find(`#error_${prefix}rango_peso_max`), "Peso inválido"),
            validateField(form.find(`#${prefix}costo_base`), /^\d+(\.\d{1,2})?$/, form.find(`#error_${prefix}costo_base`), "Costo inválido"),
            validateField(form.find(`#${prefix}vigencia_desde`), null, form.find(`#error_${prefix}vigencia_desde`), "Fecha inválida")
        ].every(Boolean);
        
        // Validar que peso máximo sea mayor que peso mínimo
        if (isValid) {
            const pesoMin = parseFloat(form.find(`#${prefix}rango_peso_min`).val());
            const pesoMax = parseFloat(form.find(`#${prefix}rango_peso_max`).val());
            
            if (pesoMax <= pesoMin) {
                form.find(`#error_${prefix}rango_peso_max`).html("El peso máximo debe ser mayor al mínimo").addClass("text-danger");
                return false;
            }
        }
        
        return isValid;
    };

    // Resetear formulario
    const resetForm = (formId) => {
        $(`#${formId}`)[0].reset();
        $(`#${formId} small`).html("").removeClass("text-danger");
    };

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

    // Mostrar lista de tarifas
    const mostrarTarifas = async () => {
        const [sesion, tarifas] = await Promise.all([
            obtenerSesion(),
            fetchData("ajax/tarifa.ajax.php")
        ]);
        if (!tarifas) return;

        const tabla = $("#tabla_tarifas");
        const tbody = tabla.find("tbody");
        tbody.empty();

        tarifas.data.forEach((tarifa, index) => {
            // Formatear fechas
            const vigenciaDesde = new Date(tarifa.vigencia_desde).toLocaleDateString();
            const vigenciaHasta = tarifa.vigencia_hasta ? new Date(tarifa.vigencia_hasta).toLocaleDateString() : 'Indefinido';
            
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${tarifa.nombre_sucursal_origen || 'N/A'}</td>
                    <td>${tarifa.nombre_sucursal_destino || 'N/A'}</td>
                    <td>${tarifa.nombre_tipo_encomienda || 'N/A'}</td>
                    <td>${tarifa.rango_peso_min} - ${tarifa.rango_peso_max} kg</td>
                    <td>${formatCurrency(tarifa.costo_base)}</td>
                    <td>${tarifa.costo_kg_extra ? formatCurrency(tarifa.costo_kg_extra ): '0.00'}</td>
                    <td>${tarifa.tiempo_estimado ? tarifa.tiempo_estimado + ' horas' : 'No especificado'}</td>
                    <td>${vigenciaDesde} - ${vigenciaHasta}</td>
                    <td class="text-center">
                        ${sesion.permisos.tarifas && sesion.permisos.tarifas.acciones.includes("estado")?
                            `${tarifa.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarTarifa" idTarifa="${tarifa.id_tarifa}" estadoTarifa="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarTarifa" idTarifa="${tarifa.id_tarifa}" estadoTarifa="1">Desactivado</button>`
                        }`:``}
                        
                    </td>
                    <td class="text-center">
                        ${sesion.permisos.tarifas && sesion.permisos.tarifas.acciones.includes("editar")?
                            `<a href="#" class="me-3 btnEditarTarifa" idTarifa="${tarifa.id_tarifa}" data-bs-toggle="modal" data-bs-target="#modal_editar_tarifa">
                                <i class="text-warning fas fa-edit fa-lg"></i>
                            </a>`:``}
                        ${sesion.permisos.tarifas && sesion.permisos.tarifas.acciones.includes("eliminar")?
                            `<a href="#" class="me-3 btnEliminarTarifa" idTarifa="${tarifa.id_tarifa}">
                                <i class="text-danger fa fa-trash fa-lg"></i>
                            </a>`:``}
                        
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
        });
    };

    // Cargar sucursales activas en select
    const cargarSucursales = async (selectId) => {
        const sucursales = await fetchData("ajax/sucursal.ajax.php?estado=1");
        if (!sucursales || !sucursales.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar sucursal</option>');
        
        sucursales.data.forEach(sucursal => {
            if (sucursal.estado == 1) {
                select.append(`<option value="${sucursal.id_sucursal}">${sucursal.nombre}</option>`);
            }
        });
    };

    // Cargar tipos de encomienda activos
    const cargarTiposEncomienda = async (selectId) => {
        const tipos = await fetchData("ajax/tipo_encomienda.ajax.php?estado=1");
        if (!tipos || !tipos.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar tipo</option>');
        
        tipos.data.forEach(tipo => {
            if (tipo.estado == 1) {
                select.append(`<option value="${tipo.id_tipo_encomienda}">${tipo.nombre}</option>`);
            }
        });
    };

    // Evento para guardar nueva tarifa
    $("#btn_guardar_tarifa").click(async function (e) {
        e.preventDefault();
        if (validateTarifaForm("form_nueva_tarifa")) {
            const datos = new FormData($("#form_nueva_tarifa")[0]);
            datos.append('action', 'crear');
            const response = await fetchData("ajax/tarifa.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nueva_tarifa");
                $("#modal_nueva_tarifa").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Tarifa creada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_tarifas")) {
                    $("#tabla_tarifas").DataTable().destroy();
                }
                await mostrarTarifas();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al guardar la tarifa", "error");
            }
        }
    });

    // Evento para editar tarifa
    $("#tabla_tarifas").on("click", ".btnEditarTarifa", async function () {
        const idTarifa = $(this).attr("idTarifa");
        const formData = new FormData();
        formData.append('id_tarifa', idTarifa);
        formData.append('action', "editar");

        const response = await fetchData("ajax/tarifa.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_tarifa").val(data.id_tarifa);
            $("#edit_rango_peso_min").val(data.rango_peso_min);
            $("#edit_rango_peso_max").val(data.rango_peso_max);
            $("#edit_costo_base").val(data.costo_base);
            $("#edit_costo_kg_extra").val(data.costo_kg_extra);
            $("#edit_tiempo_estimado").val(data.tiempo_estimado);
            $("#edit_vigencia_desde").val(data.vigencia_desde.split(' ')[0]);
            $("#edit_vigencia_hasta").val(data.vigencia_hasta ? data.vigencia_hasta.split(' ')[0] : '');
            $("#edit_estado_tarifa").val(data.estado);
            
            // Cargar sucursales y seleccionar las actuales
            await cargarSucursales("edit_sucursal_origen");
            await cargarSucursales("edit_sucursal_destino");
            $("#edit_sucursal_origen").val(data.id_sucursal_origen).trigger('change');
            $("#edit_sucursal_destino").val(data.id_sucursal_destino).trigger('change');
            
            // Cargar tipos de encomienda y seleccionar el actual
            await cargarTiposEncomienda("edit_tipo_encomienda");
            $("#edit_tipo_encomienda").val(data.id_tipo_encomienda).trigger('change');
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos de la tarifa", "error");
        }
    });

    // Evento para actualizar tarifa
    $("#btn_update_tarifa").click(async function (e) {
        e.preventDefault();
        if (validateTarifaForm("form_update_tarifa", true)) {
            const formData = new FormData($("#form_update_tarifa")[0]);
            formData.append("action", "actualizar");
            const response = await fetchData("ajax/tarifa.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_tarifa");
                $("#modal_editar_tarifa").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Tarifa actualizada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_tarifas")) {
                    $("#tabla_tarifas").DataTable().destroy();
                }
                await mostrarTarifas();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar la tarifa", "error");
            }
        }
    });

    // Evento para activar/desactivar tarifa
    $("#tabla_tarifas").on("click", ".btnActivarTarifa", async function () {
        const idTarifa = $(this).attr("idTarifa");
        const estadoTarifa = $(this).attr("estadoTarifa");
        const formData = new FormData();
        formData.append('id_tarifa', idTarifa);
        formData.append('estado', estadoTarifa);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/tarifa.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado de la tarifa actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_tarifas")) {
                $("#tabla_tarifas").DataTable().destroy();
            }
            await mostrarTarifas();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar tarifa
    $("#tabla_tarifas").on("click", ".btnEliminarTarifa", async function (e) {
        e.preventDefault();
        const idTarifa = $(this).attr("idTarifa");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar esta tarifa?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_tarifa", idTarifa);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/tarifa.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Tarifa eliminada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_tarifas")) {
                    $("#tabla_tarifas").DataTable().destroy();
                }
                await mostrarTarifas();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar la tarifa", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarSucursales("sucursal_origen");
    cargarSucursales("sucursal_destino");
    cargarTiposEncomienda("tipo_encomienda");
    mostrarTarifas();
});