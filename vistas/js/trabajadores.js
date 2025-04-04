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
    $('#modal_nuevo_trabajador, #modal_editar_trabajador').on('shown.bs.modal', function() {
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

    // Validación de formulario de trabajador
    const validateTrabajadorForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        
        const isValid = [
            validateField(form.find(`#${prefix}tipo_documento_trabajador`), null, form.find(`#error_${prefix}tipo_documento_trabajador`), "Selección inválida"),
            validateField(form.find(`#${prefix}numero_documento_trabajador`), /^[0-9]{8,15}$/, form.find(`#error_${prefix}numero_documento_trabajador`), "Número de documento inválido"),
            validateField(form.find(`#${prefix}nombre_trabajador`), /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, form.find(`#error_${prefix}nombre_trabajador`), "Nombre inválido"),
            validateField(form.find(`#${prefix}apellidos_trabajador`), /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, form.find(`#error_${prefix}apellidos_trabajador`), "Apellidos inválidos"),
            validateField(isEdit ? $('#edit_cargo_select_trabajador') : $(`#${prefix}cargo_trabajador`), null, form.find(`#error_${prefix}cargo_trabajador`), "Selección inválida"),
            validateField(form.find(`#${prefix}celular_trabajador`), /^[0-9]{9}$/, form.find(`#error_${prefix}celular_trabajador`), "Celular inválido (9 dígitos)"),
            validateField(form.find(`#${prefix}email_trabajador`), /^[^\s@]+@[^\s@]+\.[^\s@]+$/, form.find(`#error_${prefix}email_trabajador`), "Email inválido")
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

    // Mostrar lista de trabajadores
    const mostrarTrabajadores = async () => {
        const trabajadores = await fetchData("ajax/trabajador.ajax.php");
        if (!trabajadores) return;

        const tabla = $("#tabla_trabajadores");
        const tbody = tabla.find("tbody");
        tbody.empty();

        trabajadores.data.forEach((trabajador, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${trabajador.abreviatura || 'N/A'}</td>
                    <td>${trabajador.numero_documento}</td>
                    <td>${trabajador.nombre} ${trabajador.apellidos || ''}</td>
                    <td>${trabajador.cargo || 'N/A'}</td>
                    <td>${trabajador.telefono || 'N/A'}</td>
                    <td>${trabajador.email || 'N/A'}</td>
                    <td class="text-center">
                        ${trabajador.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarTrabajador" idTrabajador="${trabajador.id_persona}" estadoTrabajador="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarTrabajador" idTrabajador="${trabajador.id_persona}" estadoTrabajador="1">Desactivado</button>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarTrabajador" idTrabajador="${trabajador.id_persona}" data-bs-toggle="modal" data-bs-target="#modal_editar_trabajador">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarTrabajador" idTrabajador="${trabajador.id_persona}">
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

    // Evento para guardar nuevo trabajador
    $("#btn_guardar_trabajador").click(async function (e) {
        e.preventDefault();
        if (validateTrabajadorForm("form_nuevo_trabajador")) {
            const datos = new FormData($("#form_nuevo_trabajador")[0]);
            datos.append('action', 'crear');
            datos.append('tipo_persona', 'TRABAJADOR');
            
            const response = await fetchData("ajax/trabajador.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nuevo_trabajador");
                $("#modal_nuevo_trabajador").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Trabajador registrado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_trabajadores")) {
                    $("#tabla_trabajadores").DataTable().destroy();
                }
                await mostrarTrabajadores();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al registrar el trabajador", "error");
            }
        }
    });

    // Evento para editar trabajador
    $("#tabla_trabajadores").on("click", ".btnEditarTrabajador", async function () {
        const idTrabajador = $(this).attr("idTrabajador");
        const formData = new FormData();
        formData.append('id_persona', idTrabajador);
        formData.append('action', "editar");

        const response = await fetchData("ajax/trabajador.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_trabajador").val(data.id_persona);
            $("#edit_nombre_trabajador").val(data.nombre);
            $("#edit_apellidos_trabajador").val(data.apellidos);
            $("#edit_numero_documento_trabajador").val(data.numero_documento);
            $("#edit_telefono_trabajador").val(data.telefono);
            $("#edit_celular_trabajador").val(data.celular);
            $("#edit_email_trabajador").val(data.email);
            $("#edit_direccion_trabajador").val(data.direccion);
            $("#edit_ciudad_trabajador").val(data.ciudad);
            $("#edit_estado_trabajador").val(data.estado);
            $("#edit_fecha_nacimiento_trabajador").val(data.fecha_nacimiento || '');
            
            // Seleccionar cargo
            $("#edit_cargo_select_trabajador").val(data.cargo || '').trigger('change');
            $("#edit_cargo_trabajador").val(data.cargo || '');
            
            // Cargar tipos de documento y seleccionar el actual
            await cargarTiposDocumento("edit_tipo_documento_trabajador");
            $("#edit_tipo_documento_trabajador").val(data.id_tipo_documento).trigger('change');
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del trabajador", "error");
        }
    });

    // Actualizar campo oculto de cargo cuando cambia el select
    $("#edit_cargo_select_trabajador").change(function() {
        $("#edit_cargo_trabajador").val($(this).val());
    });

    // Evento para actualizar trabajador
    $("#btn_update_trabajador").click(async function (e) {
        e.preventDefault();
        if (validateTrabajadorForm("form_update_trabajador", true)) {
            const formData = new FormData($("#form_update_trabajador")[0]);
            formData.append("action", "actualizar");
            formData.append("tipo_persona", "TRABAJADOR");
            
            const response = await fetchData("ajax/trabajador.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_trabajador");
                $("#modal_editar_trabajador").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Trabajador actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_trabajadores")) {
                    $("#tabla_trabajadores").DataTable().destroy();
                }
                await mostrarTrabajadores();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el trabajador", "error");
            }
        }
    });

    // Evento para activar/desactivar trabajador
    $("#tabla_trabajadores").on("click", ".btnActivarTrabajador", async function () {
        const idTrabajador = $(this).attr("idTrabajador");
        const estadoTrabajador = $(this).attr("estadoTrabajador");
        const formData = new FormData();
        formData.append('id_persona', idTrabajador);
        formData.append('estado', estadoTrabajador);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/trabajador.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del trabajador actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_trabajadores")) {
                $("#tabla_trabajadores").DataTable().destroy();
            }
            await mostrarTrabajadores();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar trabajador
    $("#tabla_trabajadores").on("click", ".btnEliminarTrabajador", async function (e) {
        e.preventDefault();
        const idTrabajador = $(this).attr("idTrabajador");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este trabajador?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_persona", idTrabajador);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/trabajador.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Trabajador eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_trabajadores")) {
                    $("#tabla_trabajadores").DataTable().destroy();
                }
                await mostrarTrabajadores();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el trabajador", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarTiposDocumento("tipo_documento_trabajador");
    mostrarTrabajadores();
});