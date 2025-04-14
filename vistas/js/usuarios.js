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
    $('#modal_nuevo_usuario, #modal_editar_usuario').on('shown.bs.modal', function() {
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

    // Validación de formulario de usuario
    const validateUsuarioForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        
        // Validaciones comunes
        const isValid = [
            validateField(form.find(`#${prefix}sucursal_usuario`), null, form.find(`#error_${prefix}sucursal_usuario`), "Selección inválida"),
            validateField(form.find(`#${prefix}persona_usuario`), null, form.find(`#error_${prefix}persona_usuario`), "Selección inválida"),
            validateField(form.find(`#${prefix}nombre_usuario`), /^[a-zA-Z0-9\s,.\-#áéíóúÁÉÍÓÚñÑ]+$/, form.find(`#error_${prefix}nombre_usuario`), "Nombre inválido"),
            validateField(form.find(`#${prefix}usuario`), /^[a-zA-Z0-9_]+$/, form.find(`#error_${prefix}usuario`), "Solo letras, números y guiones bajos")
        ].every(Boolean);
        
        // Validar contraseña solo si no es edición o si se está editando y se ingresó una nueva contraseña
        if (!isEdit || (isEdit && form.find(`#${prefix}contrasena`).val())) {
            if (!validateField(form.find(`#${prefix}contrasena`), /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/, form.find(`#error_${prefix}contrasena`), "Mínimo 6 caracteres con al menos una letra y un número")) {
                return false;
            }
        }
        
        return isValid;
    };

    // Resetear formulario
    const resetForm = (formId) => {
        $(`#${formId}`)[0].reset();
        $(`#${formId} small`).html("").removeClass("text-danger");
        $('#preview_imagen_usuario').hide();
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

    // Mostrar lista de usuarios
    const mostrarUsuarios = async () => {
        const usuarios = await fetchData("ajax/usuario.ajax.php");
        if (!usuarios) return;

        const tabla = $("#tabla_usuarios");
        const tbody = tabla.find("tbody");
        tbody.empty();

        usuarios.data.forEach((usuario, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${usuario.nombre_sucursal || 'N/A'}</td>
                    <td>${usuario.nombre_persona || 'N/A'}</td>
                    <td>${usuario.nombre_usuario}</td>
                    <td>${usuario.usuario}</td>
                    <td class="text-center">
                        ${usuario.imagen 
                            ? `<img src="vistas/assets/img/usuarios/${usuario.imagen}" alt="Imagen usuario" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">` 
                            : '<i class="fas fa-user-circle fa-2x"></i>'}
                    </td>
                    <td>${usuario.ultimo_login ? new Date(usuario.ultimo_login).toLocaleString() : 'Nunca'}</td>
                    <td class="text-center">
                        ${usuario.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarUsuario" idUsuario="${usuario.id_usuario}" estadoUsuario="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarUsuario" idUsuario="${usuario.id_usuario}" estadoUsuario="1">Desactivado</button>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarUsuario" idUsuario="${usuario.id_usuario}" data-bs-toggle="modal" data-bs-target="#modal_editar_usuario">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarUsuario" idUsuario="${usuario.id_usuario}">
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

    // Cargar personas activas en select
    const cargarPersonas = async (selectId) => {
        const personas = await fetchData("ajax/persona.ajax.php");
        if (!personas || !personas.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar persona</option>');
        
        personas.data.forEach(persona => {
            if (persona.estado == 1) {
                select.append(`<option value="${persona.id_persona}">${persona.nombre} ${persona.apellidos} - ${persona.tipo_persona}</option>`);
            }
        });
    };

    // Toggle para mostrar/ocultar contraseña
    $(document).on('click', '.toggle-password', function() {
        const input = $(this).siblings('input');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Evento para guardar nuevo usuario
    $("#btn_guardar_usuario").click(async function (e) {
        e.preventDefault();
        if (validateUsuarioForm("form_nuevo_usuario")) {
            const datos = new FormData($("#form_nuevo_usuario")[0]);
            datos.append('action', 'crear');
            const response = await fetchData("ajax/usuario.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nuevo_usuario");
                $("#modal_nuevo_usuario").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Usuario creado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_usuarios")) {
                    $("#tabla_usuarios").DataTable().destroy();
                }
                await mostrarUsuarios();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al guardar el usuario", "error");
            }
        }
    });

    // Evento para editar usuario
    $("#tabla_usuarios").on("click", ".btnEditarUsuario", async function () {
        const idUsuario = $(this).attr("idUsuario");
        const formData = new FormData();
        formData.append('id_usuario', idUsuario);
        formData.append('action', "editar");

        const response = await fetchData("ajax/usuario.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_usuario").val(data.id_usuario);
            $("#edit_nombre_usuario").val(data.nombre_usuario);
            $("#edit_usuario").val(data.usuario);
            $("#edit_estado_usuario").val(data.estado);
            
            // Cargar sucursales y seleccionar la actual
            await cargarSucursales("edit_sucursal_usuario");
            $("#edit_sucursal_usuario").val(data.id_sucursal).trigger('change');
            
            // Cargar personas y seleccionar la actual
            await cargarPersonas("edit_persona_usuario");
            $("#edit_persona_usuario").val(data.id_persona).trigger('change');
            
            // Mostrar imagen actual si existe
            if (data.imagen) {
                $("#preview_imagen_usuario").attr("src", `vistas/assets/img/usuarios/${data.imagen}`).show();
            } else {
                $("#preview_imagen_usuario").hide();
            }
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del usuario", "error");
        }
    });

    // Evento para actualizar usuario
    $("#btn_update_usuario").click(async function (e) {
        e.preventDefault();
        if (validateUsuarioForm("form_update_usuario", true)) {
            const formData = new FormData($("#form_update_usuario")[0]);
            formData.append("action", "actualizar");
            const response = await fetchData("ajax/usuario.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_usuario");
                $("#modal_editar_usuario").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Usuario actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_usuarios")) {
                    $("#tabla_usuarios").DataTable().destroy();
                }
                await mostrarUsuarios();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el usuario", "error");
            }
        }
    });

    // Evento para activar/desactivar usuario
    $("#tabla_usuarios").on("click", ".btnActivarUsuario", async function () {
        const idUsuario = $(this).attr("idUsuario");
        const estadoUsuario = $(this).attr("estadoUsuario");
        const formData = new FormData();
        formData.append('id_usuario', idUsuario);
        formData.append('estado', estadoUsuario);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/usuario.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del usuario actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_usuarios")) {
                $("#tabla_usuarios").DataTable().destroy();
            }
            await mostrarUsuarios();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar usuario
    $("#tabla_usuarios").on("click", ".btnEliminarUsuario", async function (e) {
        e.preventDefault();
        const idUsuario = $(this).attr("idUsuario");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este usuario?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_usuario", idUsuario);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/usuario.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Usuario eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_usuarios")) {
                    $("#tabla_usuarios").DataTable().destroy();
                }
                await mostrarUsuarios();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el usuario", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarSucursales("sucursal_usuario");
    cargarPersonas("persona_usuario");
    mostrarUsuarios();
});