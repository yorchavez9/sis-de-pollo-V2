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
    $('#modal_nuevo_almacen, #modal_editar_almacen, #modal_nuevo_tipo_documento, #modal_editar_tipo_documento').on('shown.bs.modal', function() {
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

    // Validación de formulario de almacén
    const validateAlmacenForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        const isValid = [
            validateField(form.find(`#${prefix}sucursal_almacen`), null, form.find(`#error_${prefix}sucursal_almacen`), "Selección inválida"),
            validateField(form.find(`#${prefix}nombre_almacen`), /^[a-zA-Z0-9\s,.\-#áéíóúÁÉÍÓÚñÑ]+$/, form.find(`#error_${prefix}nombre_almacen`), "Nombre inválido"),
            validateField(form.find(`#${prefix}tipo_almacen`), null, form.find(`#error_${prefix}tipo_almacen`), "Tipo inválido")
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

    // Mostrar lista de almacenes
    const mostrarAlmacenes = async () => {
        const [sesion, almacenes] = await Promise.all([
            obtenerSesion(),
            fetchData("ajax/almacen.ajax.php")
        ]);
        if (!almacenes) return;

        const tabla = $("#tabla_almacenes");
        const tbody = tabla.find("tbody");
        tbody.empty();

        almacenes.data.forEach((almacen, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${almacen.nombre_sucursal || 'N/A'}</td>
                    <td>${almacen.nombre}</td>
                    <td>${almacen.descripcion || 'Sin descripción'}</td>
                    <td>${almacen.tipo}</td>
                    <td class="text-center">
                        ${sesion.permisos.almacenes && sesion.permisos.almacenes.acciones.includes("estado")?
                            `${almacen.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarAlmacen" idAlmacen="${almacen.id_almacen}" estadoAlmacen="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarAlmacen" idAlmacen="${almacen.id_almacen}" estadoAlmacen="1">Desactivado</button>`
                            }`:``}
                        
                    </td>
                    <td class="text-center">
                        ${sesion.permisos.almacenes && sesion.permisos.almacenes.acciones.includes("editar")?
                            `<a href="#" class="me-3 btnEditarAlmacen" idAlmacen="${almacen.id_almacen}" data-bs-toggle="modal" data-bs-target="#modal_editar_almacen">
                                <i class="text-warning fas fa-edit fa-lg"></i>
                            </a>`:``}
                        ${sesion.permisos.almacenes && sesion.permisos.almacenes.acciones.includes("eliminar")?
                            `<a href="#" class="me-3 btnEliminarAlmacen" idAlmacen="${almacen.id_almacen}">
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

    // Cargar sucursales activas en select
    const cargarSucursales = async (selectId) => {
        // Agregar parámetro para filtrar por estado=1 (activas)
        const sucursales = await fetchData("ajax/sucursal.ajax.php?estado=1");
        if (!sucursales || !sucursales.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar sucursal</option>');
        
        // Filtrar sucursales activas (aunque ya viene filtrado del servidor)
        sucursales.data.forEach(sucursal => {
            // Verificación adicional por si acaso
            if (sucursal.estado == 1) {
                select.append(`<option value="${sucursal.id_sucursal}">${sucursal.nombre}</option>`);
            }
        });
    };

    // Evento para guardar nuevo almacén
    $("#btn_guardar_almacen").click(async function (e) {
        e.preventDefault();
        if (validateAlmacenForm("form_nuevo_almacen")) {
            const datos = new FormData($("#form_nuevo_almacen")[0]);
            datos.append('action', 'crear');
            const response = await fetchData("ajax/almacen.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nuevo_almacen");
                $("#modal_nuevo_almacen").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Almacén creado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_almacenes")) {
                    $("#tabla_almacenes").DataTable().destroy();
                }
                await mostrarAlmacenes();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al guardar el almacén", "error");
            }
        }
    });

    // Evento para editar almacén
    $("#tabla_almacenes").on("click", ".btnEditarAlmacen", async function () {
        const idAlmacen = $(this).attr("idAlmacen");
        const formData = new FormData();
        formData.append('id_almacen', idAlmacen);
        formData.append('action', "editar");

        const response = await fetchData("ajax/almacen.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_almacen").val(data.id_almacen);
            $("#edit_nombre_almacen").val(data.nombre);
            $("#edit_descripcion_almacen").val(data.descripcion);
            $("#edit_tipo_almacen").val(data.tipo).trigger('change');
            $("#edit_estado_almacen").val(data.estado);
            
            // Cargar sucursales y seleccionar la actual
            await cargarSucursales("edit_sucursal_almacen");
            $("#edit_sucursal_almacen").val(data.id_sucursal).trigger('change');
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del almacén", "error");
        }
    });

    // Evento para actualizar almacén
    $("#btn_update_almacen").click(async function (e) {
        e.preventDefault();
        if (validateAlmacenForm("form_update_almacen", true)) {
            const formData = new FormData($("#form_update_almacen")[0]);
            formData.append("action", "actualizar");
            const response = await fetchData("ajax/almacen.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_almacen");
                $("#modal_editar_almacen").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Almacén actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_almacenes")) {
                    $("#tabla_almacenes").DataTable().destroy();
                }
                await mostrarAlmacenes();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el almacén", "error");
            }
        }
    });

    // Evento para activar/desactivar almacén
    $("#tabla_almacenes").on("click", ".btnActivarAlmacen", async function () {
        const idAlmacen = $(this).attr("idAlmacen");
        const estadoAlmacen = $(this).attr("estadoAlmacen");
        const formData = new FormData();
        formData.append('id_almacen', idAlmacen);
        formData.append('estado', estadoAlmacen);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/almacen.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del almacén actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_almacenes")) {
                $("#tabla_almacenes").DataTable().destroy();
            }
            await mostrarAlmacenes();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar almacén
    $("#tabla_almacenes").on("click", ".btnEliminarAlmacen", async function (e) {
        e.preventDefault();
        const idAlmacen = $(this).attr("idAlmacen");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este almacén?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_almacen", idAlmacen);
            formData.append("action", "eliminar");
            formData.forEach(element => {
                console.log(element);
            });
            const response = await fetchData("ajax/almacen.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Almacén eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_almacenes")) {
                    $("#tabla_almacenes").DataTable().destroy();
                }
                await mostrarAlmacenes();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el almacén", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarSucursales("sucursal_almacen");
    mostrarAlmacenes();
});