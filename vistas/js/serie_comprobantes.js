$(document).ready(function () {

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
    $('#modal_nueva_serie, #modal_editar_serie').on('shown.bs.modal', function() {
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



    // Validación de formulario de serie
    const validateSerieForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";

        const isValid = [
            validateField(
                form.find(`#${prefix}sucursal_serie`),
                null,
                form.find(`#error_${prefix}sucursal_serie`),
                "Selección inválida"
            ),
            validateField(
                form.find(`#${prefix}tipo_comprobante_serie`),
                null,
                form.find(`#error_${prefix}tipo_comprobante_serie`),
                "Selección inválida"
            ),
            validateField(
                form.find(`#${prefix}serie_comprobante`),
                /^[A-Za-z0-9]+$/,
                form.find(`#error_${prefix}serie_comprobante`),
                "La serie debe contener solo letras y números"
            ),
            validateField(
                form.find(`#${prefix}numero_inicial_serie`),
                /^\d+$/,
                form.find(`#error_${prefix}numero_inicial_serie`),
                "Debe ser un número válido"
            ),
            validateField(
                form.find(`#${prefix}numero_actual_serie`),
                /^\d+$/,
                form.find(`#error_${prefix}numero_actual_serie`),
                "Debe ser un número válido"
            ),
        ].every(Boolean);

        // Validar que número actual sea mayor o igual que número inicial
        const numInicial = parseInt(form.find(`#${prefix}numero_inicial_serie`).val());
        const numActual = parseInt(form.find(`#${prefix}numero_actual_serie`).val());
        const numFinal = parseInt(form.find(`#${prefix}numero_final_serie`).val()) || null;

        if (numActual < numInicial) {
            form.find(`#error_${prefix}numero_actual_serie`)
                .html("El número actual no puede ser menor al inicial")
                .addClass("text-danger");
            return false;
        }

        if (numFinal && numFinal <= numActual) {
            form.find(`#error_${prefix}numero_final_serie`)
                .html("El número final debe ser mayor al actual")
                .addClass("text-danger");
            return false;
        }

        return isValid;
    };


    // Resetear formulario
    const resetForm = (formId) => {
        $(`#${formId}`)[0].reset();
        $(`#${formId} small`).html("").removeClass("text-danger");
    };
    // Mostrar lista de series
    const mostrarSeriesComprobantes = async () => {
        const series = await fetchData("ajax/serie_comprobante.ajax.php");
        if (!series) return;

        const tabla = $("#tabla_series_comprobantes");
        const tbody = tabla.find("tbody");
        tbody.empty();

        series.data.forEach((serie, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${serie.nombre_sucursal || 'N/A'}</td>
                    <td>${serie.nombre_tipo_comprobante || 'N/A'}</td>
                    <td>${serie.serie}</td>
                    <td>${serie.numero_inicial}</td>
                    <td>${serie.numero_actual}</td>
                    <td>${serie.numero_final || '-'}</td>
                    <td class="text-center">
                        ${serie.estado != 0
                    ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarSerie" idSerie="${serie.id_serie}" estadoSerie="0">Activado</button>`
                    : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarSerie" idSerie="${serie.id_serie}" estadoSerie="1">Desactivado</button>`
                }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarSerie" idSerie="${serie.id_serie}" data-bs-toggle="modal" data-bs-target="#modal_editar_serie">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarSerie" idSerie="${serie.id_serie}">
                            <i class="text-danger fa fa-trash fa-lg"></i>
                        </a>
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

    // Cargar tipos de comprobante activos en select
    const cargarTiposComprobante = async (selectId) => {
        const tipos = await fetchData("ajax/tipo_comprobante.ajax.php?estado=1");
        if (!tipos || !tipos.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar tipo</option>');

        tipos.data.forEach(tipo => {
            if (tipo.estado == 1) {
                select.append(`<option value="${tipo.id_tipo_comprobante}">${tipo.nombre} (${tipo.codigo})</option>`);
            }
        });
    };

    // Evento para guardar nueva serie
    $("#btn_guardar_serie").click(async function (e) {
        e.preventDefault();
        if (validateSerieForm("form_nueva_serie")) {
            const datos = new FormData($("#form_nueva_serie")[0]);
            datos.append('action', 'crear');
            const response = await fetchData("ajax/serie_comprobante.ajax.php", "POST", datos);

            if (response?.status) {
                resetForm("form_nueva_serie");
                $("#modal_nueva_serie").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Serie creada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_series_comprobantes")) {
                    $("#tabla_series_comprobantes").DataTable().destroy();
                }
                await mostrarSeriesComprobantes();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al guardar la serie", "error");
            }
        }
    });

    // Evento para editar serie
    $("#tabla_series_comprobantes").on("click", ".btnEditarSerie", async function () {
        const idSerie = $(this).attr("idSerie");
        const formData = new FormData();
        formData.append('id_serie', idSerie);
        formData.append('action', "editar");

        const response = await fetchData("ajax/serie_comprobante.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_serie").val(data.id_serie);
            $("#edit_serie_comprobante").val(data.serie);
            $("#edit_numero_inicial_serie").val(data.numero_inicial);
            $("#edit_numero_actual_serie").val(data.numero_actual);
            $("#edit_numero_final_serie").val(data.numero_final || '');
            $("#edit_estado_serie").val(data.estado);

            // Cargar sucursales y seleccionar la actual
            await cargarSucursales("edit_sucursal_serie");
            $("#edit_sucursal_serie").val(data.id_sucursal).trigger('change');

            // Cargar tipos de comprobante y seleccionar el actual
            await cargarTiposComprobante("edit_tipo_comprobante_serie");
            $("#edit_tipo_comprobante_serie").val(data.id_tipo_comprobante).trigger('change');
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos de la serie", "error");
        }
    });

    // Evento para actualizar serie
    $("#btn_update_serie").click(async function (e) {
        e.preventDefault();
        if (validateSerieForm("form_update_serie", true)) {
            const formData = new FormData($("#form_update_serie")[0]);
            formData.append("action", "actualizar");
            const response = await fetchData("ajax/serie_comprobante.ajax.php", "POST", formData);

            if (response?.status) {
                resetForm("form_update_serie");
                $("#modal_editar_serie").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Serie actualizada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_series_comprobantes")) {
                    $("#tabla_series_comprobantes").DataTable().destroy();
                }
                await mostrarSeriesComprobantes();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar la serie", "error");
            }
        }
    });

    // Evento para activar/desactivar serie
    $("#tabla_series_comprobantes").on("click", ".btnActivarSerie", async function () {
        const idSerie = $(this).attr("idSerie");
        const estadoSerie = $(this).attr("estadoSerie");
        const formData = new FormData();
        formData.append('id_serie', idSerie);
        formData.append('estado', estadoSerie);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/serie_comprobante.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado de la serie actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_series_comprobantes")) {
                $("#tabla_series_comprobantes").DataTable().destroy();
            }
            await mostrarSeriesComprobantes();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar serie
    $("#tabla_series_comprobantes").on("click", ".btnEliminarSerie", async function (e) {
        e.preventDefault();
        const idSerie = $(this).attr("idSerie");

        const result = await Swal.fire({
            title: "¿Está seguro de eliminar esta serie?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_serie", idSerie);
            formData.append("action", "eliminar");

            const response = await fetchData("ajax/serie_comprobante.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Serie eliminada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_series_comprobantes")) {
                    $("#tabla_series_comprobantes").DataTable().destroy();
                }
                await mostrarSeriesComprobantes();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar la serie", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarSucursales("sucursal_serie");
    cargarTiposComprobante("tipo_comprobante_serie");
    mostrarSeriesComprobantes();
});