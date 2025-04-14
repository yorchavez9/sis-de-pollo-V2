$(document).ready(function () {
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

    // Validación de formulario de rol
    const validateRolForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        
        return [
            validateField(form.find(`#${prefix}nombre_rol`), /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]+$/, form.find(`#error_${prefix}nombre_rol`), "Nombre inválido"),
            validateField(form.find(`#${prefix}nivel_acceso_rol`), null, form.find(`#error_${prefix}nivel_acceso_rol`), "Selección inválida")
        ].every(Boolean);
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

    // Mostrar lista de roles
    const mostrarRoles = async () => {
        const roles = await fetchData("ajax/rol.ajax.php");
        if (!roles) return;

        const tabla = $("#tabla_roles");
        const tbody = tabla.find("tbody");
        tbody.empty();

        roles.data.forEach((rol, index) => {
            const nivelAcceso = ["", "Básico", "Intermedio", "Avanzado", "Administrador"][rol.nivel_acceso] || "Desconocido";
            
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${rol.nombre}</td>
                    <td>${rol.descripcion || 'N/A'}</td>
                    <td>${nivelAcceso}</td>
                    <td class="text-center">
                        ${rol.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarRol" idRol="${rol.id_rol}" estadoRol="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarRol" idRol="${rol.id_rol}" estadoRol="1">Desactivado</button>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarRol" idRol="${rol.id_rol}" data-bs-toggle="modal" data-bs-target="#modal_editar_rol">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarRol" idRol="${rol.id_rol}">
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

    // Evento para guardar nuevo rol
    $("#btn_guardar_rol").click(async function (e) {
        e.preventDefault();
        if (validateRolForm("form_nuevo_rol")) {
            const formData = new FormData($("#form_nuevo_rol")[0]);
            formData.append('action', 'crear');
            
            const response = await fetchData("ajax/rol.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_nuevo_rol");
                $("#modal_nuevo_rol").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Rol creado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_roles")) {
                    $("#tabla_roles").DataTable().destroy();
                }
                await mostrarRoles();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al guardar el rol", "error");
            }
        }
    });

    // Evento para editar rol
    $("#tabla_roles").on("click", ".btnEditarRol", async function () {
        const idRol = $(this).attr("idRol");
        const formData = new FormData();
        formData.append('id_rol', idRol);
        formData.append('action', "editar");

        const response = await fetchData("ajax/rol.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_rol").val(data.id_rol);
            $("#edit_nombre_rol").val(data.nombre);
            $("#edit_descripcion_rol").val(data.descripcion || '');
            $("#edit_nivel_acceso_rol").val(data.nivel_acceso);
            $("#edit_estado_rol").val(data.estado);
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del rol", "error");
        }
    });

    // Evento para actualizar rol
    $("#btn_update_rol").click(async function (e) {
        e.preventDefault();
        if (validateRolForm("form_update_rol", true)) {
            const formData = new FormData($("#form_update_rol")[0]);
            formData.append("action", "actualizar");
            const response = await fetchData("ajax/rol.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_rol");
                $("#modal_editar_rol").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Rol actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_roles")) {
                    $("#tabla_roles").DataTable().destroy();
                }
                await mostrarRoles();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el rol", "error");
            }
        }
    });

    // Evento para activar/desactivar rol
    $("#tabla_roles").on("click", ".btnActivarRol", async function () {
        const idRol = $(this).attr("idRol");
        const estadoRol = $(this).attr("estadoRol");
        const formData = new FormData();
        formData.append('id_rol', idRol);
        formData.append('estado', estadoRol);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/rol.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del rol actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_roles")) {
                $("#tabla_roles").DataTable().destroy();
            }
            await mostrarRoles();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar rol
    $("#tabla_roles").on("click", ".btnEliminarRol", async function (e) {
        e.preventDefault();
        const idRol = $(this).attr("idRol");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este rol?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_rol", idRol);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/rol.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Rol eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_roles")) {
                    $("#tabla_roles").DataTable().destroy();
                }
                await mostrarRoles();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el rol", "error");
            }
        }
    });

    // Cargar datos iniciales
    mostrarRoles();
});