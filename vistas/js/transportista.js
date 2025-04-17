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
            validateField(form.find(`#${prefix}tipo_documento_transportista`), null, form.find(`#error_${prefix}tipo_documento_transportista`), "Selección inválida"),
            validateField(form.find(`#${prefix}numero_documento_transportista`), /^[0-9]{8,15}$/, form.find(`#error_${prefix}numero_documento_transportista`), "Número de documento inválido"),
            validateField(form.find(`#${prefix}nombre_transportista`), /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, form.find(`#error_${prefix}nombre_transportista`), "Nombre inválido"),
            validateField(form.find(`#${prefix}celular_transportista`), /^[0-9]{9}$/, form.find(`#error_${prefix}celular_transportista`), "Celular inválido (9 dígitos)"),
            validateField(form.find(`#${prefix}email_transportista`), /^[^\s@]+@[^\s@]+\.[^\s@]+$/, form.find(`#error_${prefix}email_transportista`), "Email inválido")
        ].every(Boolean);
        
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

    // Mostrar lista de transportistas
    const mostrarTransportistas = async () => {

        const [sesion, transportistas] = await Promise.all([
            obtenerSesion(),
            fetchData("ajax/transportista.ajax.php")
        ]);

        if (!sesion || !sesion.permisos) {
            return;
        }
        if (!transportistas) return;

        const tabla = $("#tabla_transportistas");
        const tbody = tabla.find("tbody");
        tbody.empty();

        transportistas.data.forEach((transportista, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${transportista.abreviatura || 'N/A'}</td>
                    <td>${transportista.numero_documento}</td>
                    <td>${transportista.nombre}</td>
                    <td>${transportista.apellidos || 'N/A'}</td>
                    <td>${transportista.telefono || 'N/A'}</td>
                    <td>${transportista.celular || 'N/A'}</td>
                    <td class="text-center">
                    ${sesion.permisos.transportista && sesion.permisos.transportista.acciones.includes("estado")?
                        `${transportista.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarTransportista" idTransportista="${transportista.id_persona}" estadoTransportista="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarTransportista" idTransportista="${transportista.id_persona}" estadoTransportista="1">Desactivado</button>`
                        }`:``}
                        
                    </td>
                    <td class="text-center">
                        ${sesion.permisos.transportista && sesion.permisos.transportista.acciones.includes("estado")?
                            `<a href="#" class="me-3 btnEditarTransportista" idTransportista="${transportista.id_persona}" data-bs-toggle="modal" data-bs-target="#modal_editar_transportista">
                                <i class="text-warning fas fa-edit fa-lg"></i>
                            </a>`:``}
                        
                        ${sesion.permisos.transportista && sesion.permisos.transportista.acciones.includes("estado")?
                            `<a href="#" class="me-3 btnEliminarTransportista" idTransportista="${transportista.id_persona}">
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

    // Cargar tipos de documento activos en select
    const cargarTiposDocumento = async (selectId) => {
        const tiposDocumento = await fetchData("ajax/tipo_documentos.ajax.php?action=listar");
        if (!tiposDocumento || !tiposDocumento.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar tipo documento</option>');
        
        tiposDocumento.data.forEach(tipo => {
            if (tipo.estado == 1) {
                select.append(`<option value="${tipo.id_tipo_documento}">${tipo.abreviatura}</option>`);
            }
        });
    };

    // Evento para guardar nuevo transportista
    $("#btn_guardar_transportista").click(async function (e) {
        e.preventDefault();
        if (validateTransportistaForm("form_nuevo_transportista")) {
            const datos = new FormData($("#form_nuevo_transportista")[0]);
            datos.append('action', 'crear');
            datos.append('tipo_persona', 'TRANSPORTISTA');
            
            const response = await fetchData("ajax/transportista.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nuevo_transportista");
                $("#modal_nuevo_transportista").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Transportista registrado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_transportistas")) {
                    $("#tabla_transportistas").DataTable().destroy();
                }
                await mostrarTransportistas();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al registrar el transportista", "error");
            }
        }
    });

    // Evento para editar transportista
    $("#tabla_transportistas").on("click", ".btnEditarTransportista", async function () {
        const idTransportista = $(this).attr("idTransportista");
        const formData = new FormData();
        formData.append('id_persona', idTransportista);
        formData.append('action', "editar");

        const response = await fetchData("ajax/transportista.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_transportista").val(data.id_persona);
            $("#edit_nombre_transportista").val(data.nombre);
            $("#edit_apellidos_transportista").val(data.apellidos);
            $("#edit_numero_documento_transportista").val(data.numero_documento);
            $("#edit_telefono_transportista").val(data.telefono);
            $("#edit_celular_transportista").val(data.celular);
            $("#edit_email_transportista").val(data.email);
            $("#edit_direccion_transportista").val(data.direccion);
            $("#edit_ciudad_transportista").val(data.ciudad);
            $("#edit_estado_transportista").val(data.estado);
            
            // Cargar tipos de documento y seleccionar el actual
            await cargarTiposDocumento("edit_tipo_documento_transportista");
            $("#edit_tipo_documento_transportista").val(data.id_tipo_documento).trigger('change');
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del transportista", "error");
        }
    });

    // Evento para actualizar transportista
    $("#btn_update_transportista").click(async function (e) {
        e.preventDefault();
        if (validateTransportistaForm("form_update_transportista", true)) {
            const formData = new FormData($("#form_update_transportista")[0]);
            formData.append("action", "actualizar");
            formData.append("tipo_persona", "TRANSPORTISTA");
            
            const response = await fetchData("ajax/transportista.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_transportista");
                $("#modal_editar_transportista").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Transportista actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_transportistas")) {
                    $("#tabla_transportistas").DataTable().destroy();
                }
                await mostrarTransportistas();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el transportista", "error");
            }
        }
    });

    // Evento para activar/desactivar transportista
    $("#tabla_transportistas").on("click", ".btnActivarTransportista", async function () {
        const idTransportista = $(this).attr("idTransportista");
        const estadoTransportista = $(this).attr("estadoTransportista");
        const formData = new FormData();
        formData.append('id_persona', idTransportista);
        formData.append('estado', estadoTransportista);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/transportista.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del transportista actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_transportistas")) {
                $("#tabla_transportistas").DataTable().destroy();
            }
            await mostrarTransportistas();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar transportista
    $("#tabla_transportistas").on("click", ".btnEliminarTransportista", async function (e) {
        e.preventDefault();
        const idTransportista = $(this).attr("idTransportista");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este transportista?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_persona", idTransportista);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/transportista.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Transportista eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_transportistas")) {
                    $("#tabla_transportistas").DataTable().destroy();
                }
                await mostrarTransportistas();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el transportista", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarTiposDocumento("tipo_documento_transportista");
    mostrarTransportistas();
});