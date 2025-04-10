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

     // Resetear formulario
     const resetForm = (formId) => {
        $(`#${formId}`)[0].reset();
        $(`#${formId} small`).html("").removeClass("text-danger");
    };


    // Validación de formulario de tipo comprobante
    const validateTipoComprobanteForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
    
        const isValid = [
            validateField(
                form.find(`#${prefix}codigo_tipo_comprobante`),
                /^[A-Za-z0-9]+$/, // regex de ejemplo, modifícalo según lo que necesites
                form.find(`#error_${prefix}codigo_tipo_comprobante`),
                "Código inválido"
            ),
            validateField(
                form.find(`#${prefix}nombre_tipo_comprobante`),
                /^[A-Za-z\s]+$/, // regex de ejemplo para letras y espacios
                form.find(`#error_${prefix}nombre_tipo_comprobante`),
                "Nombre inválido"
            ),
        ].every(Boolean);
    
        return isValid;
    };
    

    // Mostrar lista de tipos de comprobante
    const mostrarTiposComprobante = async () => {
        const tiposComprobante = await fetchData("ajax/tipo_comprobante.ajax.php");
        if (!tiposComprobante) return;

        const tabla = $("#tabla_tipo_comprobantes");
        const tbody = tabla.find("tbody");
        tbody.empty();

        tiposComprobante.data.forEach((tipo, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${tipo.codigo}</td>
                    <td>${tipo.nombre}</td>
                    <td class="text-center">
                        ${tipo.serie_obligatoria != 0 
                            ? '<span class="badge bg-activado">Sí</span>' 
                            : '<span class="badge bg-desactivado">No</span>'
                        }
                    </td>
                    <td class="text-center">
                        ${tipo.numero_obligatorio != 0 
                            ? '<span class="badge bg-activado">Sí</span>' 
                            : '<span class="badge bg-desactivado">No</span>'
                        }
                    </td>
                    <td class="text-center">
                        ${tipo.afecta_inventario != 0 
                            ? '<span class="badge bg-activado">Sí</span>' 
                            : '<span class="badge bg-desactivado">No</span>'
                        }
                    </td>
                    <td class="text-center">
                        ${tipo.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarTipoComprobante" idTipoComprobante="${tipo.id_tipo_comprobante}" estadoTipoComprobante="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarTipoComprobante" idTipoComprobante="${tipo.id_tipo_comprobante}" estadoTipoComprobante="1">Desactivado</button>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarTipoComprobante" idTipoComprobante="${tipo.id_tipo_comprobante}" data-bs-toggle="modal" data-bs-target="#modal_editar_tipo_comprobante">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarTipoComprobante" idTipoComprobante="${tipo.id_tipo_comprobante}">
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

    // Evento para guardar nuevo tipo de comprobante
    $("#btn_guardar_tipo_comprobante").click(async function (e) {
        e.preventDefault();
        if (validateTipoComprobanteForm("form_nuevo_tipo_comprobante")) {
            const datos = new FormData($("#form_nuevo_tipo_comprobante")[0]);
            datos.append('action', 'crear');
            const response = await fetchData("ajax/tipo_comprobante.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nuevo_tipo_comprobante");
                $("#modal_nuevo_tipo_comprobante").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Tipo de comprobante creado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_tipo_comprobantes")) {
                    $("#tabla_tipo_comprobantes").DataTable().destroy();
                }
                await mostrarTiposComprobante();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al guardar el tipo de comprobante", "error");
            }
        }
    });

    // Evento para editar tipo de comprobante
    $("#tabla_tipo_comprobantes").on("click", ".btnEditarTipoComprobante", async function () {
        const idTipoComprobante = $(this).attr("idTipoComprobante");
        const formData = new FormData();
        formData.append('id_tipo_comprobante', idTipoComprobante);
        formData.append('action', "editar");

        const response = await fetchData("ajax/tipo_comprobante.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_tipo_comprobante").val(data.id_tipo_comprobante);
            $("#edit_codigo_tipo_comprobante").val(data.codigo);
            $("#edit_nombre_tipo_comprobante").val(data.nombre);
            $("#edit_serie_obligatoria_tipo_comprobante").val(data.serie_obligatoria);
            $("#edit_numero_obligatorio_tipo_comprobante").val(data.numero_obligatorio);
            $("#edit_afecta_inventario_tipo_comprobante").val(data.afecta_inventario);
            $("#edit_estado_tipo_comprobante").val(data.estado);
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del tipo de comprobante", "error");
        }
    });

    // Evento para actualizar tipo de comprobante
    $("#btn_update_tipo_comprobante").click(async function (e) {
        e.preventDefault();
        if (validateTipoComprobanteForm("form_update_tipo_comprobante", true)) {
            const formData = new FormData($("#form_update_tipo_comprobante")[0]);
            formData.append("action", "actualizar");
            const response = await fetchData("ajax/tipo_comprobante.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_tipo_comprobante");
                $("#modal_editar_tipo_comprobante").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Tipo de comprobante actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_tipo_comprobantes")) {
                    $("#tabla_tipo_comprobantes").DataTable().destroy();
                }
                await mostrarTiposComprobante();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el tipo de comprobante", "error");
            }
        }
    });

    // Evento para activar/desactivar tipo de comprobante
    $("#tabla_tipo_comprobantes").on("click", ".btnActivarTipoComprobante", async function () {
        const idTipoComprobante = $(this).attr("idTipoComprobante");
        const estadoTipoComprobante = $(this).attr("estadoTipoComprobante");
        const formData = new FormData();
        formData.append('id_tipo_comprobante', idTipoComprobante);
        formData.append('estado', estadoTipoComprobante);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/tipo_comprobante.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del tipo de comprobante actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_tipo_comprobantes")) {
                $("#tabla_tipo_comprobantes").DataTable().destroy();
            }
            await mostrarTiposComprobante();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar tipo de comprobante
    $("#tabla_tipo_comprobantes").on("click", ".btnEliminarTipoComprobante", async function (e) {
        e.preventDefault();
        const idTipoComprobante = $(this).attr("idTipoComprobante");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este tipo de comprobante?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_tipo_comprobante", idTipoComprobante);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/tipo_comprobante.ajax.php", "POST", formData);
            console.log(response);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Tipo de comprobante eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_tipo_comprobantes")) {
                    $("#tabla_tipo_comprobantes").DataTable().destroy();
                }
                await mostrarTiposComprobante();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el tipo de comprobante", "error");
            }
        }
    });

    // Cargar datos iniciales
    mostrarTiposComprobante();
});