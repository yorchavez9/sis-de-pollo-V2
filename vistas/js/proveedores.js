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
    $('#modal_nuevo_proveedor, #modal_editar_proveedor').on('shown.bs.modal', function() {
        initSelect2($(this).find('.js-example-basic-single'), $(this));
    });

    // Mostrar/ocultar campos según tipo de proveedor
    $('#tipo_proveedor, #edit_tipo_proveedor').change(function() {
        const tipo = $(this).val();
        const prefix = $(this).attr('id').startsWith('edit_') ? 'edit_' : '';
        
        if (tipo === 'NATURAL') {
            $(`#${prefix}campos_natural`).show();
            $(`#${prefix}campos_juridico`).hide();
            $(`#${prefix}razon_social_proveedor`).val('').removeAttr('required');
            $(`#${prefix}nombre_proveedor`).attr('required', 'required');
        } else {
            $(`#${prefix}campos_natural`).hide();
            $(`#${prefix}campos_juridico`).show();
            $(`#${prefix}nombre_proveedor`).val('').removeAttr('required');
            $(`#${prefix}apellidos_proveedor`).val('').removeAttr('required');
            $(`#${prefix}razon_social_proveedor`).attr('required', 'required');
        }
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

    // Validación de formulario de proveedor
    const validateProveedorForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        const tipoProveedor = $(`#${prefix}tipo_proveedor`).val();
        
        const isValid = [
            validateField(form.find(`#${prefix}tipo_documento_proveedor`), null, form.find(`#error_${prefix}tipo_documento_proveedor`), "Selección inválida"),
            validateField(form.find(`#${prefix}numero_documento_proveedor`), /^[0-9]{8,15}$/, form.find(`#error_${prefix}numero_documento_proveedor`), "Número de documento inválido"),
            validateField(form.find(`#${prefix}celular_proveedor`), /^[0-9]{9}$/, form.find(`#error_${prefix}celular_proveedor`), "Celular inválido (9 dígitos)"),
            tipoProveedor === 'NATURAL' 
                ? validateField(form.find(`#${prefix}nombre_proveedor`), /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, form.find(`#error_${prefix}nombre_proveedor`), "Nombre inválido")
                : validateField(form.find(`#${prefix}razon_social_proveedor`), /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,-]+$/, form.find(`#error_${prefix}razon_social_proveedor`), "Razón social inválida"),
            validateField(form.find(`#${prefix}email_proveedor`), /^[^\s@]+@[^\s@]+\.[^\s@]+$/, form.find(`#error_${prefix}email_proveedor`), "Email inválido")
        ].every(Boolean);
        
        return isValid;
    };

    // Resetear formulario
    const resetForm = (formId) => {
        $(`#${formId}`)[0].reset();
        $(`#${formId} small`).html("").removeClass("text-danger");
        $('#campos_natural').show();
        $('#campos_juridico').hide();
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

    // Mostrar lista de proveedores
    const mostrarProveedores = async () => {
        const proveedores = await fetchData("ajax/proveedores.ajax.php");
        if (!proveedores) return;

        const tabla = $("#tabla_proveedores");
        const tbody = tabla.find("tbody");
        tbody.empty();

        proveedores.data.forEach((proveedor, index) => {
            const nombreCompleto = proveedor.razon_social 
                ? proveedor.razon_social 
                : `${proveedor.nombre} ${proveedor.apellidos || ''}`;
            
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${proveedor.nombre_tipo_documento || 'N/A'}</td>
                    <td>${proveedor.numero_documento}</td>
                    <td>${nombreCompleto}</td>
                    <td>${proveedor.telefono || 'N/A'}</td>
                    <td>${proveedor.celular || 'N/A'}</td>
                    <td>${proveedor.email || 'N/A'}</td>
                    <td class="text-center">
                        ${proveedor.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarProveedor" idProveedor="${proveedor.id_persona}" estadoProveedor="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarProveedor" idProveedor="${proveedor.id_persona}" estadoProveedor="1">Desactivado</button>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarProveedor" idProveedor="${proveedor.id_persona}" data-bs-toggle="modal" data-bs-target="#modal_editar_proveedor">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarProveedor" idProveedor="${proveedor.id_persona}">
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

    // Evento para guardar nuevo proveedor
    $("#btn_guardar_proveedor").click(async function (e) {
        e.preventDefault();
        if (validateProveedorForm("form_nuevo_proveedor")) {
            const datos = new FormData($("#form_nuevo_proveedor")[0]);
            datos.append('action', 'crear');
            datos.append('tipo_persona', 'PROVEEDOR');
            
            const response = await fetchData("ajax/proveedores.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nuevo_proveedor");
                $("#modal_nuevo_proveedor").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Proveedor registrado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_proveedores")) {
                    $("#tabla_proveedores").DataTable().destroy();
                }
                await mostrarProveedores();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al registrar el proveedor", "error");
            }
        }
    });

    // Evento para editar proveedor
    $("#tabla_proveedores").on("click", ".btnEditarProveedor", async function () {
        const idProveedor = $(this).attr("idProveedor");
        const formData = new FormData();
        formData.append('id_persona', idProveedor);
        formData.append('action', "editar");

        const response = await fetchData("ajax/proveedores.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_proveedor").val(data.id_persona);
            $("#edit_numero_documento_proveedor").val(data.numero_documento);
            $("#edit_telefono_proveedor").val(data.telefono);
            $("#edit_celular_proveedor").val(data.celular);
            $("#edit_email_proveedor").val(data.email);
            $("#edit_direccion_proveedor").val(data.direccion);
            $("#edit_ciudad_proveedor").val(data.ciudad);
            $("#edit_estado_proveedor").val(data.estado);
            
            // Determinar tipo de proveedor
            if (data.razon_social) {
                $("#edit_tipo_proveedor").val("JURIDICO").trigger('change');
                $("#edit_razon_social_proveedor").val(data.razon_social);
                $("#edit_nombre_proveedor").val('');
                $("#edit_apellidos_proveedor").val('');
            } else {
                $("#edit_tipo_proveedor").val("NATURAL").trigger('change');
                $("#edit_nombre_proveedor").val(data.nombre);
                $("#edit_apellidos_proveedor").val(data.apellidos);
                $("#edit_razon_social_proveedor").val('');
            }
            
            // Cargar tipos de documento y seleccionar el actual
            await cargarTiposDocumento("edit_tipo_documento_proveedor");
            $("#edit_tipo_documento_proveedor").val(data.id_tipo_documento).trigger('change');
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del proveedor", "error");
        }
    });

    // Evento para actualizar proveedor
    $("#btn_update_proveedor").click(async function (e) {
        e.preventDefault();
        if (validateProveedorForm("form_update_proveedor", true)) {
            const formData = new FormData($("#form_update_proveedor")[0]);
            formData.append("action", "actualizar");
            formData.append("tipo_persona", "PROVEEDOR");
            
            const response = await fetchData("ajax/proveedores.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_proveedor");
                $("#modal_editar_proveedor").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Proveedor actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_proveedores")) {
                    $("#tabla_proveedores").DataTable().destroy();
                }
                await mostrarProveedores();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el proveedor", "error");
            }
        }
    });

    // Evento para activar/desactivar proveedor
    $("#tabla_proveedores").on("click", ".btnActivarProveedor", async function () {
        const idProveedor = $(this).attr("idProveedor");
        const estadoProveedor = $(this).attr("estadoProveedor");
        const formData = new FormData();
        formData.append('id_persona', idProveedor);
        formData.append('estado', estadoProveedor);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/proveedores.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del proveedor actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_proveedores")) {
                $("#tabla_proveedores").DataTable().destroy();
            }
            await mostrarProveedores();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar proveedor
    $("#tabla_proveedores").on("click", ".btnEliminarProveedor", async function (e) {
        e.preventDefault();
        const idProveedor = $(this).attr("idProveedor");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este proveedor?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_persona", idProveedor);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/proveedores.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Proveedor eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_proveedores")) {
                    $("#tabla_proveedores").DataTable().destroy();
                }
                await mostrarProveedores();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el proveedor", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarTiposDocumento("tipo_documento_proveedor");
    mostrarProveedores();
});