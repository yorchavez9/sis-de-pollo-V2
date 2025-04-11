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
    $('#modal_nuevo_tipo_encomienda, #modal_editar_tipo_encomienda').on('shown.bs.modal', function() {
        initSelect2($(this).find('.js-example-basic-single'), $(this));
    });

    // Función para validar campos (que faltaba)
    const validateField = (field, regex = null, errorField = null, errorMessage = null) => {
        const value = field.val();
        if (!value) {
            if (errorField) {
                errorField.html("Este campo es obligatorio").addClass("text-danger");
            }
            return false;
        } else if (regex && !regex.test(value)) {
            if (errorField) {
                errorField.html(errorMessage || "Formato inválido").addClass("text-danger");
            }
            return false;
        } else {
            if (errorField) {
                errorField.html("").removeClass("text-danger");
            }
            return true;
        }
    };

    // Validación de formulario de tipo encomienda
    const validateTipoEncomiendaForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        const isValid = [
            validateField(
                form.find(`#${prefix}nombre_tipo_encomienda`), 
                /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]+$/, 
                form.find(`#error_${prefix}nombre_tipo_encomienda`), 
                "Nombre inválido (solo letras, números y espacios)"
            ),
            validateField(
                form.find(`#${prefix}prioridad_tipo_encomienda`), 
                null, 
                form.find(`#error_${prefix}prioridad_tipo_encomienda`), 
                "Seleccione una prioridad"
            )
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
                headers: {},
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
            return null;
        }
    };

    // Mostrar lista de tipos de encomienda
    const mostrarTiposEncomienda = async () => {
        const tipos = await fetchData("ajax/tipo_encomienda.ajax.php");
        if (!tipos || !tipos.status) return;

        const tabla = $("#tabla_tipo_encomiendas");
        const tbody = tabla.find("tbody");
        tbody.empty();

        tipos.data.forEach((tipo, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${tipo.nombre}</td>
                    <td>${tipo.descripcion || 'Sin descripción'}</td>
                    <td class="text-center">
                        ${tipo.requiere_confirmacion == 1 
                            ? '<span class="badge bg-success">Sí</span>' 
                            : '<span class="badge bg-secondary">No</span>'}
                    </td>
                    <td class="text-center">
                        ${getPrioridadBadge(tipo.prioridad)}
                    </td>
                    <td class="text-center">
                        ${tipo.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarTipoEncomienda" idTipoEncomienda="${tipo.id_tipo_encomienda}" estadoTipoEncomienda="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarTipoEncomienda" idTipoEncomienda="${tipo.id_tipo_encomienda}" estadoTipoEncomienda="1">Desactivado</button>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarTipoEncomienda" idTipoEncomienda="${tipo.id_tipo_encomienda}" data-bs-toggle="modal" data-bs-target="#modal_editar_tipo_encomienda">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarTipoEncomienda" idTipoEncomienda="${tipo.id_tipo_encomienda}">
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

    // Función para obtener el badge de prioridad
    const getPrioridadBadge = (prioridad) => {
        const badges = {
            'BAJA': 'badge bg-info',
            'MEDIA': 'badge bg-primary',
            'ALTA': 'badge bg-warning',
            'URGENTE': 'badge bg-danger'
        };
        return `<span class="${badges[prioridad]}">${prioridad}</span>`;
    };

    // Evento para guardar nuevo tipo de encomienda
    $("#btn_guardar_tipo_encomienda").click(async function (e) {
        e.preventDefault();
        if (validateTipoEncomiendaForm("form_nuevo_tipo_encomienda")) {
            const datos = {
                nombre: $("#nombre_tipo_encomienda").val(),
                descripcion: $("#descripcion_tipo_encomienda").val(),
                requiere_confirmacion: $("#requiere_confirmacion_tipo_encomienda").val(),
                prioridad: $("#prioridad_tipo_encomienda").val(),
                estado: $("#estado_tipo_encomienda").val(),
                action: 'crear'
            };
            // Convertir el objeto a FormData
            const formData = new FormData();
            for (const key in datos) {
                formData.append(key, datos[key]);
            }

            const response = await fetchData("ajax/tipo_encomienda.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_nuevo_tipo_encomienda");
                $("#modal_nuevo_tipo_encomienda").modal("hide");
                Swal.fire({
                    icon: "success",
                    title: "¡Correcto!",
                    text: response.message || "Tipo de encomienda creado con éxito"
                });
                if ($.fn.DataTable.isDataTable("#tabla_tipo_encomiendas")) {
                    $("#tabla_tipo_encomiendas").DataTable().destroy();
                }
                await mostrarTiposEncomienda();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "¡Error!",
                    text: response?.message || "Error al guardar el tipo de encomienda",
                    confirmButtonColor: "#d33"
                });
            }
        }
    });

    // Evento para editar tipo de encomienda
  
    $("#tabla_tipo_encomiendas").on("click", ".btnEditarTipoEncomienda", async function () {
        const idTipoEncomienda = $(this).attr("idTipoEncomienda");
        
        // Crear FormData como en tu implementación original
        const formData = new FormData();
        formData.append('id_tipo_encomienda', idTipoEncomienda);
        formData.append('action', "editar");

        const response = await fetchData("ajax/tipo_encomienda.ajax.php", "POST", formData);
        
        if (response?.status) {
            const data = response.data;
            $("#edit_id_tipo_encomienda").val(data.id_tipo_encomienda);
            $("#edit_nombre_tipo_encomienda").val(data.nombre);
            $("#edit_descripcion_tipo_encomienda").val(data.descripcion);
            $("#edit_requiere_confirmacion_tipo_encomienda").val(data.requiere_confirmacion).trigger('change');
            $("#edit_prioridad_tipo_encomienda").val(data.prioridad).trigger('change');
            $("#edit_estado_tipo_encomienda").val(data.estado);
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: response?.message || "No se pudieron cargar los datos del tipo de encomienda",
                confirmButtonColor: "#d33"
            });
        }
    });

    // Evento para actualizar tipo de encomienda
    $("#btn_update_tipo_encomienda").click(async function (e) {
        e.preventDefault();
        if (validateTipoEncomiendaForm("form_update_tipo_encomienda", true)) {
            // Crear objeto con los datos
            const datos = {
                id_tipo_encomienda: $("#edit_id_tipo_encomienda").val(),
                nombre: $("#edit_nombre_tipo_encomienda").val(),
                descripcion: $("#edit_descripcion_tipo_encomienda").val(),
                requiere_confirmacion: $("#edit_requiere_confirmacion_tipo_encomienda").val(),
                prioridad: $("#edit_prioridad_tipo_encomienda").val(),
                estado: $("#edit_estado_tipo_encomienda").val(),
                action: "actualizar"
            };

            // Convertir el objeto a FormData
            const formData = new FormData();
            for (const key in datos) {
                formData.append(key, datos[key]);
            }

            // Enviar la solicitud
            const response = await fetchData("ajax/tipo_encomienda.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_tipo_encomienda");
                $("#modal_editar_tipo_encomienda").modal("hide");
                Swal.fire({
                    icon: "success",
                    title: "¡Correcto!",
                    text: response.message || "Tipo de encomienda actualizado con éxito",
                    confirmButtonColor: "#3085d6"
                });
                if ($.fn.DataTable.isDataTable("#tabla_tipo_encomiendas")) {
                    $("#tabla_tipo_encomiendas").DataTable().destroy();
                }
                await mostrarTiposEncomienda();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "¡Error!",
                    text: response?.message || "Error al actualizar el tipo de encomienda",
                    confirmButtonColor: "#d33"
                });
            }
        }
    });

    // Evento para activar/desactivar tipo de encomienda
    $("#tabla_tipo_encomiendas").on("click", ".btnActivarTipoEncomienda", async function () {
        const idTipoEncomienda = $(this).attr("idTipoEncomienda");
        const estadoTipoEncomienda = $(this).attr("estadoTipoEncomienda");
        
        // Crear FormData y agregar los parámetros
        const formData = new FormData();
        formData.append('id_tipo_encomienda', idTipoEncomienda);
        formData.append('estado', estadoTipoEncomienda);
        formData.append('action', 'cambiarEstado');
    
        const response = await fetchData("ajax/tipo_encomienda.ajax.php", "POST", formData);
    
        if (response?.status) {
            Swal.fire({
                icon: "success",
                title: "¡Correcto!",
                text: "Estado del tipo de encomienda actualizado",
                confirmButtonColor: "#655CC9"
            });
            if ($.fn.DataTable.isDataTable("#tabla_tipo_encomiendas")) {
                $("#tabla_tipo_encomiendas").DataTable().destroy();
            }
            await mostrarTiposEncomienda();
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: response?.message || "No se pudo cambiar el estado",
                confirmButtonColor: "#d33"
            });
        }
    });

    // Evento para eliminar tipo de encomienda
    $("#tabla_tipo_encomiendas").on("click", ".btnEliminarTipoEncomienda", async function (e) {
        e.preventDefault();
        const idTipoEncomienda = $(this).attr("idTipoEncomienda");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este tipo de encomienda?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        });

        if (result.isConfirmed) {
            const response = await fetchData("ajax/tipo_encomienda.ajax.php", "POST", {
                id_tipo_encomienda: idTipoEncomienda,
                action: "eliminar"
            });

            if (response?.status) {
                Swal.fire({
                    icon: "success",
                    title: "¡Eliminado!",
                    text: response.message || "Tipo de encomienda eliminado con éxito",
                    confirmButtonColor: "#655CC9"
                });
                if ($.fn.DataTable.isDataTable("#tabla_tipo_encomiendas")) {
                    $("#tabla_tipo_encomiendas").DataTable().destroy();
                }
                await mostrarTiposEncomienda();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response?.message || "Error al eliminar el tipo de encomienda",
                    confirmButtonColor: "#d33"
                });
            }
        }
    });

    // Cargar datos iniciales
    mostrarTiposEncomienda();
});