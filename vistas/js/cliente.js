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
    $('#modal_nuevo_cliente, #modal_editar_cliente').on('shown.bs.modal', function() {
        initSelect2($(this).find('.js-example-basic-single'), $(this));
    });

    // Mostrar/ocultar campos según tipo de cliente
    $('#tipo_cliente, #edit_tipo_cliente').change(function() {
        const tipo = $(this).val();
        const prefix = $(this).attr('id').startsWith('edit_') ? 'edit_' : '';
        
        if (tipo === 'NATURAL') {
            $(`#${prefix}campos_natural`).show();
            $(`#${prefix}campos_juridico`).hide();
            $(`#${prefix}razon_social_cliente`).val('').removeAttr('required');
            $(`#${prefix}nombre_cliente`).attr('required', 'required');
        } else {
            $(`#${prefix}campos_natural`).hide();
            $(`#${prefix}campos_juridico`).show();
            $(`#${prefix}nombre_cliente`).val('').removeAttr('required');
            $(`#${prefix}apellidos_cliente`).val('').removeAttr('required');
            $(`#${prefix}razon_social_cliente`).attr('required', 'required');
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

    // Validación de formulario de cliente
    const validateClienteForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        const tipoCliente = $(`#${prefix}tipo_cliente`).val();
        
        const isValid = [
            validateField(form.find(`#${prefix}tipo_documento_cliente`), null, form.find(`#error_${prefix}tipo_documento_cliente`), "Selección inválida"),
            validateField(form.find(`#${prefix}numero_documento_cliente`), /^[0-9]{8,15}$/, form.find(`#error_${prefix}numero_documento_cliente`), "Número de documento inválido"),
            validateField(form.find(`#${prefix}celular_cliente`), /^[0-9]{9}$/, form.find(`#error_${prefix}celular_cliente`), "Celular inválido (9 dígitos)"),
            tipoCliente === 'NATURAL' 
                ? validateField(form.find(`#${prefix}nombre_cliente`), /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, form.find(`#error_${prefix}nombre_cliente`), "Nombre inválido")
                : validateField(form.find(`#${prefix}razon_social_cliente`), /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,-]+$/, form.find(`#error_${prefix}razon_social_cliente`), "Razón social inválida"),
            validateField(form.find(`#${prefix}email_cliente`), /^[^\s@]+@[^\s@]+\.[^\s@]+$/, form.find(`#error_${prefix}email_cliente`), "Email inválido")
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

    // Mostrar lista de clientes
    const mostrarClientes = async () => {
        const clientes = await fetchData("ajax/cliente.ajax.php");
        if (!clientes) return;

        const tabla = $("#tabla_clientes");
        const tbody = tabla.find("tbody");
        tbody.empty();

        clientes.data.forEach((cliente, index) => {
            const nombreCompleto = cliente.razon_social 
                ? cliente.razon_social 
                : `${cliente.nombre} ${cliente.apellidos || ''}`;
            
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${cliente.abreviatura || 'N/A'}</td>
                    <td>${cliente.numero_documento}</td>
                    <td>${nombreCompleto}</td>
                    <td>${cliente.telefono || 'N/A'}</td>
                    <td>${cliente.celular || 'N/A'}</td>
                    <td>${cliente.email || 'N/A'}</td>
                    <td class="text-center">
                        ${cliente.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarCliente" idCliente="${cliente.id_persona}" estadoCliente="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarCliente" idCliente="${cliente.id_persona}" estadoCliente="1">Desactivado</button>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarCliente" idCliente="${cliente.id_persona}" data-bs-toggle="modal" data-bs-target="#modal_editar_cliente">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarCliente" idCliente="${cliente.id_persona}">
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

    // Evento para guardar nuevo cliente
    $("#btn_guardar_cliente").click(async function (e) {
        e.preventDefault();
        if (validateClienteForm("form_nuevo_cliente")) {
            const datos = new FormData($("#form_nuevo_cliente")[0]);
            datos.append('action', 'crear');
            datos.append('tipo_persona', 'CLIENTE');
            
            const response = await fetchData("ajax/cliente.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nuevo_cliente");
                $("#modal_nuevo_cliente").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Cliente registrado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_clientes")) {
                    $("#tabla_clientes").DataTable().destroy();
                }
                await mostrarClientes();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al registrar el cliente", "error");
            }
        }
    });

    // Evento para editar cliente
    $("#tabla_clientes").on("click", ".btnEditarCliente", async function () {
        const idCliente = $(this).attr("idCliente");
        const formData = new FormData();
        formData.append('id_persona', idCliente);
        formData.append('action', "editar");

        const response = await fetchData("ajax/cliente.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_cliente").val(data.id_persona);
            $("#edit_numero_documento_cliente").val(data.numero_documento);
            $("#edit_telefono_cliente").val(data.telefono);
            $("#edit_celular_cliente").val(data.celular);
            $("#edit_email_cliente").val(data.email);
            $("#edit_direccion_cliente").val(data.direccion);
            $("#edit_ciudad_cliente").val(data.ciudad);
            $("#edit_estado_cliente").val(data.estado);
            
            // Determinar tipo de cliente
            if (data.razon_social) {
                $("#edit_tipo_cliente").val("JURIDICO").trigger('change');
                $("#edit_razon_social_cliente").val(data.razon_social);
                $("#edit_nombre_cliente").val('');
                $("#edit_apellidos_cliente").val('');
            } else {
                $("#edit_tipo_cliente").val("NATURAL").trigger('change');
                $("#edit_nombre_cliente").val(data.nombre);
                $("#edit_apellidos_cliente").val(data.apellidos);
                $("#edit_razon_social_cliente").val('');
                $("#edit_fecha_nacimiento_cliente").val(data.fecha_nacimiento || '');
            }
            
            // Cargar tipos de documento y seleccionar el actual
            await cargarTiposDocumento("edit_tipo_documento_cliente");
            $("#edit_tipo_documento_cliente").val(data.id_tipo_documento).trigger('change');
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del cliente", "error");
        }
    });

    // Evento para actualizar cliente
    $("#btn_update_cliente").click(async function (e) {
        e.preventDefault();
        if (validateClienteForm("form_update_cliente", true)) {
            const formData = new FormData($("#form_update_cliente")[0]);
            formData.append("action", "actualizar");
            formData.append("tipo_persona", "CLIENTE");
            
            const response = await fetchData("ajax/cliente.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_cliente");
                $("#modal_editar_cliente").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Cliente actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_clientes")) {
                    $("#tabla_clientes").DataTable().destroy();
                }
                await mostrarClientes();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el cliente", "error");
            }
        }
    });

    // Evento para activar/desactivar cliente
    $("#tabla_clientes").on("click", ".btnActivarCliente", async function () {
        const idCliente = $(this).attr("idCliente");
        const estadoCliente = $(this).attr("estadoCliente");
        const formData = new FormData();
        formData.append('id_persona', idCliente);
        formData.append('estado', estadoCliente);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/cliente.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del cliente actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_clientes")) {
                $("#tabla_clientes").DataTable().destroy();
            }
            await mostrarClientes();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar cliente
    $("#tabla_clientes").on("click", ".btnEliminarCliente", async function (e) {
        e.preventDefault();
        const idCliente = $(this).attr("idCliente");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este cliente?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_persona", idCliente);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/cliente.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Cliente eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_clientes")) {
                    $("#tabla_clientes").DataTable().destroy();
                }
                await mostrarClientes();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el cliente", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarTiposDocumento("tipo_documento_cliente");
    mostrarClientes();
});