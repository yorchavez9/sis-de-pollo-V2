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

    // Validación de formulario de categoría
    const validateCategoriaForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";
        
        const isValid = [
            validateField(form.find(`#${prefix}nombre_categoria`), /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s-]+$/, form.find(`#error_${prefix}nombre_categoria`), "Nombre inválido"),
            validateField(form.find(`#${prefix}tipo_categoria`), null, form.find(`#error_${prefix}tipo_categoria`), "Selección inválida")
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

    // Mostrar lista de categorías
    const mostrarCategorias = async () => {
        const categorias = await fetchData("ajax/categoria.ajax.php");
        if (!categorias) return;

        const tabla = $("#tabla_categorias");
        const tbody = tabla.find("tbody");
        tbody.empty();

        categorias.data.forEach((categoria, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${categoria.nombre}</td>
                    <td>${categoria.descripcion || 'Sin descripción'}</td>
                    <td>${categoria.tipo}</td>
                    <td class="text-center">
                        ${categoria.estado != 0
                            ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarCategoria" idCategoria="${categoria.id_categoria}" estadoCategoria="0">Activado</button>`
                            : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarCategoria" idCategoria="${categoria.id_categoria}" estadoCategoria="1">Desactivado</button>`
                        }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarCategoria" idCategoria="${categoria.id_categoria}" data-bs-toggle="modal" data-bs-target="#modal_editar_categoria">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarCategoria" idCategoria="${categoria.id_categoria}">
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

    // Evento para guardar nueva categoría
    $("#btn_guardar_categoria").click(async function (e) {
        e.preventDefault();
        if (validateCategoriaForm("form_nueva_categoria")) {
            const datos = new FormData($("#form_nueva_categoria")[0]);
            datos.append('action', 'crear');
            
            const response = await fetchData("ajax/categoria.ajax.php", "POST", datos);
            
            if (response?.status) {
                resetForm("form_nueva_categoria");
                $("#modal_nueva_categoria").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Categoría creada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_categorias")) {
                    $("#tabla_categorias").DataTable().destroy();
                }
                await mostrarCategorias();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al guardar la categoría", "error");
            }
        }
    });

    // Evento para editar categoría
    $("#tabla_categorias").on("click", ".btnEditarCategoria", async function () {
        const idCategoria = $(this).attr("idCategoria");
        const formData = new FormData();
        formData.append('id_categoria', idCategoria);
        formData.append('action', "editar");

        const response = await fetchData("ajax/categoria.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_categoria").val(data.id_categoria);
            $("#edit_nombre_categoria").val(data.nombre);
            $("#edit_descripcion_categoria").val(data.descripcion);
            $("#edit_tipo_categoria").val(data.tipo).trigger('change');
            $("#edit_estado_categoria").val(data.estado);
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos de la categoría", "error");
        }
    });

    // Evento para actualizar categoría
    $("#btn_update_categoria").click(async function (e) {
        e.preventDefault();
        if (validateCategoriaForm("form_update_categoria", true)) {
            const formData = new FormData($("#form_update_categoria")[0]);
            formData.append("action", "actualizar");
            
            const response = await fetchData("ajax/categoria.ajax.php", "POST", formData);
            
            if (response?.status) {
                resetForm("form_update_categoria");
                $("#modal_editar_categoria").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Categoría actualizada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_categorias")) {
                    $("#tabla_categorias").DataTable().destroy();
                }
                await mostrarCategorias();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar la categoría", "error");
            }
        }
    });

    // Evento para activar/desactivar categoría
    $("#tabla_categorias").on("click", ".btnActivarCategoria", async function () {
        const idCategoria = $(this).attr("idCategoria");
        const estadoCategoria = $(this).attr("estadoCategoria");
        const formData = new FormData();
        formData.append('id_categoria', idCategoria);
        formData.append('estado', estadoCategoria);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/categoria.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado de la categoría actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_categorias")) {
                $("#tabla_categorias").DataTable().destroy();
            }
            await mostrarCategorias();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar categoría
    $("#tabla_categorias").on("click", ".btnEliminarCategoria", async function (e) {
        e.preventDefault();
        const idCategoria = $(this).attr("idCategoria");
        
        const result = await Swal.fire({
            title: "¿Está seguro de eliminar esta categoría?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_categoria", idCategoria);
            formData.append("action", "eliminar");
            
            const response = await fetchData("ajax/categoria.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Categoría eliminada con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_categorias")) {
                    $("#tabla_categorias").DataTable().destroy();
                }
                await mostrarCategorias();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar la categoría", "error");
            }
        }
    });

    // Cargar datos iniciales
    mostrarCategorias();
});