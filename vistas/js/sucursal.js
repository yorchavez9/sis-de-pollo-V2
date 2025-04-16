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

    const validateField = (field, regex, errorField, errorMessage) => {
        const value = field.val();
        if (!value) {
            errorField.html("Este campo es obligatorio").addClass("text-danger");
            return false;
        } else if (!regex.test(value)) {
            errorField.html(errorMessage).addClass("text-danger");
            return false;
        } else {
            errorField.html("").removeClass("text-danger");
            return true;
        }
    };

    const validateForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        const isValid = [
            validateField(form.find(`#${prefix}codigo_sucursal`), /^[a-zA-Z0-9-]+$/, form.find(`#error_${prefix}codigo_sucursal`), "Código inválido"),
            validateField(form.find(`#${prefix}nombre_sucursal`), /^[a-zA-Z0-9\s,.\-#áéíóúÁÉÍÓÚñÑ]+$/, form.find(`#error_${prefix}nombre_sucursal`), "Nombre inválido"),
            validateField(form.find(`#${prefix}direccion_sucursal`), /^[a-zA-Z0-9\s,.\-#áéíóúÁÉÍÓÚñÑ]+$/, form.find(`#error_${prefix}direccion_sucursal`), "Dirección inválida"),
            validateField(form.find(`#${prefix}ciudad_sucursal`), /^[a-zA-Z0-9\s,.\-#áéíóúÁÉÍÓÚñÑ]+$/, form.find(`#error_${prefix}ciudad_sucursal`), "Ciudad inválida"),
            validateField(form.find(`#${prefix}telefono_sucursal`), /^\d{9,12}$/, form.find(`#error_${prefix}telefono_sucursal`), "Teléfono inválido"),
        ].every(Boolean);
        return isValid;
    };

    const resetForm = (formId) => {
        $(`#${formId}`)[0].reset();
        $(`#${formId} small`).html("").removeClass("text-danger");
    };

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
    const mostrarSucursal = async () => {
        const [sesion, sucursales] = await Promise.all([
            obtenerSesion(),
            fetchData("ajax/sucursal.ajax.php")
        ]);
        if (!sucursales) return;

        const tabla = $("#tabla_sucursal");
        const tbody = tabla.find("tbody");
        tbody.empty();

        sucursales.data.forEach((sucursal, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${sucursal.nombre}</td>
                    <td>${sucursal.direccion}</td>
                    <td>${sucursal.telefono}</td>
                    <td class="text-center">
                    ${sesion.permisos.sucursales && sesion.permisos.sucursales.acciones.includes("estado")?` ${sucursal.estado != 0
                    ? `<button class="btn btn-sm text-white btn-sm btnActivar" style="background-color: #28C76F" idSucursal="${sucursal.id_sucursal}" estadoSucursal="0">Activado</button>`
                    : `<button class="btn btn-sm text-white btn-sm btnActivar" style="background-color: #E53250" idSucursal="${sucursal.id_sucursal}" estadoSucursal="1">Desactivado</button>`
                    }`:`` }
                       
                    </td>
                    <td class="text-center">
                        ${sesion.permisos.sucursales && sesion.permisos.sucursales.acciones.includes("editar")? 
                            `<a href="#" class="me-3 btnEditarSucursal" idSucursal="${sucursal.id_sucursal}" data-bs-toggle="modal" data-bs-target="#modal_editar_sucursal">
                                <i class="text-warning fas fa-edit fa-lg"></i>
                            </a>`:``}
                        
                        ${sesion.permisos.sucursales && sesion.permisos.sucursales.acciones.includes("ver")?
                            `<a href="#" class="me-3 btnVerDetallesSucursal" idSucursal="${sucursal.id_sucursal}">
                                <i class="text-primary fas fa-eye fa-lg"></i>
                            </a>`:``} 
                        ${sesion.permisos.sucursales && sesion.permisos.sucursales.acciones.includes("eliminar")?
                            `<a href="#" class="me-3 btnEliminarSucursal" idSucursal="${sucursal.id_sucursal}">
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

    $("#btn_guardar_sucursal").click(async function (e) {
        e.preventDefault();
        if (validateForm("form_nuevo_sucursal")) {
            const datos = new FormData($("#form_nuevo_sucursal")[0]);
            datos.append('action', 'crear');
            const response = await fetchData("ajax/sucursal.ajax.php", "POST", datos);
            if (response.status) {
                resetForm("form_nuevo_sucursal");
                $("#modal_nuevo_sucursal").modal("hide");
                Swal.fire("¡Correcto!", response.message, "success");
                if ($.fn.DataTable.isDataTable("#tabla_sucursal")) {
                    $("#tabla_sucursal").DataTable().destroy();
                }
                await mostrarSucursal();
            } else {
                console.error("Error al guardar la sucursal.");
                Swal.fire("¡Error!", response.message, "error");
            }
        }
    });

    $("#tabla_sucursal").on("click", ".btnEditarSucursal", async function () {
        const idSucursal = $(this).attr("idSucursal");
        const formData = new FormData();
        formData.append('id_sucursal', idSucursal);
        formData.append('action', "editar");

        const response = await fetchData("ajax/sucursal.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_sucursal").val(data.id_sucursal);
            $("#edit_codigo_sucursal").val(data.codigo);
            $("#edit_nombre_sucursal").val(data.nombre);
            $("#edit_direccion_sucursal").val(data.direccion);
            $("#edit_ciudad_sucursal").val(data.ciudad);
            $("#edit_telefono_sucursal").val(data.telefono);
            $("#edit_responsable_sucursal").val(data.responsable);
            $("#edit_es_principal_sucursal").val(data.es_principal);
        } else {
            console.error("Error al obtener los datos de la sucursal:", response?.errors);
        }
    });

    $("#btn_update_sucursal").click(async function (e) {
        e.preventDefault();
        if (validateForm("form_update_sucursal", true)) {
            const formData = new FormData($("#form_update_sucursal")[0]);
            formData.append("action", "update");
            const response = await fetchData("ajax/sucursal.ajax.php", "POST", formData);
            if (response.status) {
                resetForm("form_update_sucursal");
                $("#modal_editar_sucursal").modal("hide");
                Swal.fire("¡Correcto!", "Sucursal actualizada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_sucursal")) {
                    $("#tabla_sucursal").DataTable().destroy();
                }
                await mostrarSucursal();
            } else {
                console.error("Error al actualizar la sucursal.");
            }
        }
    });

    $("#tabla_sucursal").on("click", ".btnActivar", async function () {
        const idSucursal = $(this).attr("idSucursal");
        const estadoSucursal = $(this).attr("estadoSucursal");

        const formData = new FormData();
        formData.append('id_sucursal', idSucursal);
        formData.append('estado', estadoSucursal);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/sucursal.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado de la sucursal actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_sucursal")) {
                $("#tabla_sucursal").DataTable().destroy();
            }
            await mostrarSucursal();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    $("#tabla_sucursal").on("click", ".btnVerDetallesSucursal", async function () {
        const idSucursal = $(this).attr("idSucursal");
        const formData = new FormData();
        formData.append('id_sucursal', idSucursal);
        formData.append('action', "verDetalles");
    
        const response = await fetchData("ajax/sucursal.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#detalle_codigo_sucursal").text(data.codigo);
            $("#detalle_nombre_sucursal").text(data.nombre);
            $("#detalle_direccion_sucursal").text(data.direccion);
            $("#detalle_ciudad_sucursal").text(data.ciudad);
            $("#detalle_telefono_sucursal").text(data.telefono);
            $("#detalle_responsable_sucursal").text(data.responsable);
            $("#detalle_es_principal_sucursal").text(data.es_principal ? "Sí" : "No");
    
            $("#modal_ver_detalles_sucursal").modal("show");
        } else {
            console.error("Error al obtener los detalles de la sucursal:", response?.errors);
            Swal.fire("Error", "No se pudieron cargar los detalles de la sucursal", "error");
        }
    });    

    $("#tabla_sucursal").on("click", ".btnEliminarSucursal", async function (e) {
        e.preventDefault();
        const idSucursal = $(this).attr("idSucursal");
        const formData = new FormData();
        formData.append("id_sucursal", idSucursal);
        formData.append("action", "eliminar");

        const result = await Swal.fire({
            title: "¿Está seguro de eliminar esta sucursal?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const response = await fetchData("ajax/sucursal.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message, "success");
                if ($.fn.DataTable.isDataTable("#tabla_sucursal")) {
                    $("#tabla_sucursal").DataTable().destroy();
                }
                await mostrarSucursal();
            } else {
                console.error("Error al eliminar la sucursal.");
            }
        }
    });

    mostrarSucursal();
});
