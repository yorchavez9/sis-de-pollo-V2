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

    // Inicializar todos los Select2 al cargar
    initSelect2('.js-example-basic-single');
    
    // Reinicializar Select2 en modales
    $('#modal_nuevo_transportista, #modal_editar_transportista').on('shown.bs.modal', function() {
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

    // Validación de formulario de transportista
    const validateTransportistaForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        const isValid = [
            validateField(form.find(`#${prefix}id_persona`), null, form.find(`#error_${prefix}id_persona`), "Selección inválida"),
            validateField(form.find(`#${prefix}telefono_transportista`), /^[0-9]{7,15}$/, form.find(`#error_${prefix}telefono_transportista`), "Teléfono inválido")
        ].every(Boolean);
        return isValid;
    };

    // Resetear formulario
    const resetForm = (formId) => {
        $(`#${formId}`)[0].reset();
        $(`#${formId} small`).html("").removeClass("text-danger");
        $(`#${formId} .select2`).val(null).trigger('change');
    };

    // Función para hacer fetch
    const fetchData = async (url, method = "GET", data = null) => {
        try {
            const options = {
                method,
                cache: "no-cache",
                headers: {}
            };

            if (data) {
                if (data instanceof FormData) {
                    options.body = data;
                } else {
                    options.headers["Content-Type"] = "application/json";
                    options.body = JSON.stringify(data);
                }
            }

            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            console.error("Error en la solicitud:", error);
            return { status: false, message: "Error en la conexión" };
        }
    };

    // Mostrar lista de transportistas
    const mostrarTransportistas = async () => {
        const response = await fetchData("ajax/transporte.ajax.php");
        if (!response || !response.status) {
            console.error("Error al cargar transportistas:", response?.message);
            return;
        }

        const tabla = $("#tabla_transporte");
        const tbody = tabla.find("tbody");
        tbody.empty();

        response.data.forEach((transportista, index) => {
            const nombreCompleto = transportista.nombre_completo || 
                                 (transportista.nombre ? `${transportista.nombre} ${transportista.apellidos || ''}` : transportista.razon_social);
            
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${nombreCompleto}</td>
                    <td>${transportista.tipo_vehiculo || 'N/A'}</td>
                    <td>${transportista.placa_vehiculo || 'N/A'}</td>
                    <td>${transportista.telefono_contacto}</td>
                    <td>${transportista.fecha_registro ? transportista.fecha_registro.split('-').reverse().join('/') : 'N/A'}</td>
                    <td>
                        ${transportista.estado != 0
                            ? `<span class="badge bg-success">Activo</span>`
                            : `<span class="badge bg-danger">Inactivo</span>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarTransportista" data-id="${transportista.id_transportista}" data-bs-toggle="modal" data-bs-target="#modal_editar_transportista">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarTransportista" data-id="${transportista.id_transportista}">
                            <i class="text-danger fa fa-trash fa-lg"></i>
                        </a>
                    </td>
                </tr>`;
            tbody.append(fila);
        });

        // Inicializar o recargar DataTable
        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().destroy();
        }
        tabla.DataTable({
            autoWidth: false,
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    };

    // Cargar personas en select
    const cargarPersonas = async (selectId) => {
        const response = await fetchData("ajax/transportista.ajax.php");
        if (!response || !response.status) {
            console.error("Error al cargar personas:", response?.message);
            return;
        }

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar persona</option>');
        
        response.data.forEach(persona => {
            select.append(`<option value="${persona.id_persona}">${persona.nombre}</option>`);
        });

        initSelect2(`#${selectId}`);
    };

    // Cargar datos al abrir modal de nuevo
    $('#modal_nuevo_transportista').on('show.bs.modal', async function() {
        resetForm("form_nuevo_transportista");
        await cargarPersonas("id_persona");
    });

    // Evento para guardar nuevo transportista
    $("#btn_guardar_transportista").click(async function (e) {
        e.preventDefault();
        
        if (!validateTransportistaForm("form_nuevo_transportista")) {
            return;
        }

        const formData = new FormData($("#form_nuevo_transportista")[0]);
        formData.append('action', 'crear');

        const response = await fetchData("ajax/transporte.ajax.php", "POST", formData);
        
        if (response?.status) {
            resetForm("form_nuevo_transportista");
            $("#modal_nuevo_transportista").modal("hide");
            await Swal.fire({
                icon: 'success',
                title: '¡Correcto!',
                text: response.message || "Transportista creado con éxito",
                timer: 1500
            });
            if ($.fn.DataTable.isDataTable("#tabla_transporte")) {
                $("#tabla_transporte").DataTable().destroy();
            }
            await mostrarTransportistas();
        } else {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: response?.message || "Error al guardar el transportista"
            });
        }
    });

    // Evento para editar transportista
    $(document).on("click", ".btnEditarTransportista", async function () {
        const idTransportista = $(this).data('id');
        const formData = new FormData();
        formData.append('id_transportista', idTransportista);
        formData.append('action', "editar");

        const response = await fetchData("ajax/transporte.ajax.php", "POST", formData);
        if (!response?.status) {
            Swal.fire("Error", "No se pudieron cargar los datos del transportista", "error");
            return;
        }

        const data = response.data;
        
        // Llenar formulario de edición
        $("#edit_id_transportista").val(data.id_transportista);
        $("#edit_tipo_vehiculo").val(data.tipo_vehiculo).trigger('change');
        $("#edit_placa_vehiculo").val(data.placa_vehiculo);
        $("#edit_telefono_transportista").val(data.telefono_contacto);
        $("#edit_estado_transportista").val(data.estado).trigger('change');
        
        // Cargar persona y seleccionar la actual (solo lectura)
        await cargarPersonas("edit_id_persona");
        $(`#edit_id_persona`).val(data.id_persona).trigger('change');
        $(`#edit_id_persona`).prop('disabled', true);
    });

    // Evento para actualizar transportista
    $("#btn_actualizar_transportista").click(async function (e) {
        e.preventDefault();
        
        if (!validateTransportistaForm("form_editar_transportista", true)) {
            return;
        }

        const formData = new FormData($("#form_editar_transportista")[0]);
        formData.append("action", "actualizar");

        const response = await fetchData("ajax/transporte.ajax.php", "POST", formData);
        
        if (response?.status) {
            resetForm("form_editar_transportista");
            $("#modal_editar_transportista").modal("hide");
            await Swal.fire({
                icon: 'success',
                title: '¡Correcto!',
                text: response.message || "Transportista actualizado con éxito",
                timer: 1500
            });
            if ($.fn.DataTable.isDataTable("#tabla_transporte")) {
                $("#tabla_transporte").DataTable().destroy();
            }
            await mostrarTransportistas();
        } else {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: response?.message || "Error al actualizar el transportista"
            });
        }
    });

    // Evento para eliminar transportista
    $(document).on("click", ".btnEliminarTransportista", async function (e) {
        e.preventDefault();
        const idTransportista = $(this).data('id');
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este transportista?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        });

        if (!result.isConfirmed) return;

        const formData = new FormData();
        formData.append("id_transportista", idTransportista);
        formData.append("action", "eliminar");

        const response = await fetchData("ajax/transporte.ajax.php", "POST", formData);
        if (response?.status) {
            await Swal.fire({
                icon: 'success',
                title: '¡Eliminado!',
                text: response.message || "Transportista eliminado con éxito",
                timer: 1500
            });
            if ($.fn.DataTable.isDataTable("#tabla_transporte")) {
                $("#tabla_transporte").DataTable().destroy();
            }
            await mostrarTransportistas();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response?.message || "Error al eliminar el transportista"
            });
        }
    });

    // Cargar datos iniciales
    mostrarTransportistas();
});