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
    $('#modal_nuevo_tipo_documento, #modal_editar_tipo_documento').on('shown.bs.modal', function() {
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

    // Validación de formulario de tipo documento
    const validateTipoDocumentoForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        const isValid = [
            validateField(form.find(`#${prefix}nombre_tipo_documento`), /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, $(`#error_${prefix}nombre_tipo_documento`), "Nombre inválido"),
            validateField(form.find(`#${prefix}abreviatura_tipo_documento`), /^[A-Z]{1,10}$/, $(`#error_${prefix}abreviatura_tipo_documento`), "Abreviatura inválida (máx 10 mayúsculas)")
        ].every(Boolean);
        return isValid;
    };

    // Resetear formulario
    const resetForm = (formId) => {
        $(`#${formId}`)[0].reset();
        $(`#${formId} .text-danger`).html("");
        $(`#${formId} .js-example-basic-single`).trigger('change');
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

    // Mostrar lista de tipos de documentos
    const mostrarTiposDocumentos = async () => {

        const [sesion, response] = await Promise.all([
            obtenerSesion(),
            fetchData("ajax/tipo_documentos.ajax.php?action=listar")
        ]);

        if (!sesion || !sesion.permisos) {
            return;
        }
        
        if (!response || !response.status) {
            console.error("Error al cargar tipos de documentos:", response?.message);
            return;
        }

        const tabla = $("#tabla_tipos_documentos");
        const tbody = tabla.find("tbody");
        tbody.empty();

        response.data.forEach((tipo, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${tipo.nombre}</td>
                    <td>${tipo.abreviatura}</td>
                    <td>${tipo.longitud || 'Sin límite'}</td>
                    <td class="text-center">
                        ${tipo.es_empresa == 1 
                            ? '<span class="badge btn-estado-success">Sí</span>' 
                            : '<span class="badge bg-secondary">No</span>'}
                    </td>
                    <td class="text-center">
                        ${sesion.permisos.tipoDocumentos && sesion.permisos.tipoDocumentos.acciones.includes("estado")?
                            `${tipo.estado == 1 
                            ? `<button class="btn btn-sm btn-estado-success text-white btn-activar" data-id="${tipo.id_tipo_documento}" data-estado="0">Activo</button>`
                            : `<button class="btn btn-sm btn-estado-danger text-white btn-activar" data-id="${tipo.id_tipo_documento}" data-estado="1">Inactivo</button>`}`:``}
                        
                    </td>
                    <td class="text-center">
                        ${sesion.permisos.tipoDocumentos && sesion.permisos.tipoDocumentos.acciones.includes("editar")?
                            `<a href="#" class="btn-editar me-3" data-id="${tipo.id_tipo_documento}" data-bs-toggle="modal" data-bs-target="#modal_editar_tipo_documento">
                                <i class="text-warning fas fa-edit fa-lg"></i>
                            </a>`:``}
                        
                        ${sesion.permisos.tipoDocumentos && sesion.permisos.tipoDocumentos.acciones.includes("eliminar")?
                            `<a href="#" class="btn-eliminar" data-id="${tipo.id_tipo_documento}">
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

    // Evento para guardar nuevo tipo de documento
    $("#form_nuevo_tipo_documento").submit(async function(e) {
        e.preventDefault();
        if (!validateTipoDocumentoForm("form_nuevo_tipo_documento")) return;

        const formData = new FormData(this);
        formData.append("action", "crear");

        const response = await fetchData("ajax/tipo_documentos.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", response.message || "Tipo de documento creado", "success");
            resetForm("form_nuevo_tipo_documento");
            $("#modal_nuevo_tipo_documento").modal("hide");
            if ($.fn.DataTable.isDataTable("#tabla_tipos_documentos")) {
                $("#tabla_tipos_documentos").DataTable().destroy();
            }
            mostrarTiposDocumentos();
        } else {
            Swal.fire("¡Error!", response?.message || "Error al crear tipo de documento", "error");
        }
    });

    // Evento para editar tipo de documento
    $(document).on("click", ".btn-editar", async function() {
        const idTipo = $(this).data("id");
        const formData = new FormData();
        formData.append('action', 'obtener');
        formData.append('id', idTipo);

        const response = await fetchData("ajax/tipo_documentos.ajax.php", "POST", formData);
        if (response?.status) {
            const tipo = response.data;
            $("#edit_id_tipo_documento").val(tipo.id_tipo_documento);
            $("#edit_nombre_tipo_documento").val(tipo.nombre);
            $("#edit_abreviatura_tipo_documento").val(tipo.abreviatura);
            $("#edit_longitud_tipo_documento").val(tipo.longitud);
            $("#edit_es_empresa_tipo_documento").val(tipo.es_empresa).trigger('change');
            $("#edit_estado_tipo_documento").val(tipo.estado).trigger('change');
            
            $("#modal_editar_tipo_documento").modal("show");
        } else {
            Swal.fire("Error", response?.message || "No se pudieron cargar los datos", "error");
        }
    });

    // Evento para actualizar tipo de documento
    $("#form_editar_tipo_documento").submit(async function(e) {
        e.preventDefault();
        if (!validateTipoDocumentoForm("form_editar_tipo_documento", true)) return;

        const formData = new FormData(this);
        formData.append("action", "actualizar");

        const response = await fetchData("ajax/tipo_documentos.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", response.message || "Tipo de documento actualizado", "success");
            $("#modal_editar_tipo_documento").modal("hide");
            if ($.fn.DataTable.isDataTable("#tabla_tipos_documentos")) {
                $("#tabla_tipos_documentos").DataTable().destroy();
            }
            mostrarTiposDocumentos();
        } else {
            Swal.fire("¡Error!", response?.message || "Error al actualizar", "error");
        }
    });

    // Evento para activar/desactivar tipo de documento
    $(document).on("click", ".btn-activar", async function() {
        const idTipo = $(this).data("id");
        const estado = $(this).data("estado");
        
        const formData = new FormData();
        formData.append('action', 'cambiarEstado');
        formData.append('id', idTipo);
        formData.append('estado', estado);

        const response = await fetchData("ajax/tipo_documentos.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_tipos_documentos")) {
                $("#tabla_tipos_documentos").DataTable().destroy();
            }
            mostrarTiposDocumentos();
        } else {
            Swal.fire("Error", response?.message || "Error al cambiar estado", "error");
        }
    });

    // Evento para eliminar tipo de documento
    $(document).on("click", ".btn-eliminar", async function(e) {
        e.preventDefault();
        const idTipo = $(this).data("id");
        
        const result = await Swal.fire({
            title: "¿Eliminar este tipo de documento?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("action", "eliminar");
            formData.append("id", idTipo);
 
            const response = await fetchData("ajax/tipo_documentos.ajax.php", "POST", formData);
            console.log(response);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Tipo de documento eliminado", "success");
                if ($.fn.DataTable.isDataTable("#tabla_tipos_documentos")) {
                    $("#tabla_tipos_documentos").DataTable().destroy();
                }
                mostrarTiposDocumentos();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar", "error");
            }
        }
    });

    // Cargar datos iniciales
    mostrarTiposDocumentos();
});