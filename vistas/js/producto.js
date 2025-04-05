$(document).ready(function () {

    function formatCurrency(value) {
        if (!value) return "S/ 0.00";
        return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(value);
    }

    // Configuración común para Select2
    const select2Config = {
        placeholder: "Seleccionar",
    };

    // Inicializar Select2
    function initSelect2(selector, dropdownParent = null) {
        const config = { ...select2Config };
        if (dropdownParent) {
            config.dropdownParent = dropdownParent;
        }
        $(selector).select2(config);
    }

    // Inicializar todos los Select2 al cargar
    initSelect2('.js-example-basic-single');

    // Reinicializar Select2 en modales
    $('#modal_nuevo_producto, #modal_editar_producto').on('shown.bs.modal', function () {
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

    // Validación de formulario de producto
    const validateProductoForm = (formId, isEdit = false) => {
        const form = $(`#${formId}`);
        const prefix = isEdit ? "edit_" : "";

        // Asegúrate de que todos los elementos de error existan en tu HTML
        const isValid = [
            validateField(
                form.find(`#${prefix}codigo_producto`),
                null,
                $(`#error_${prefix}codigo_producto`),
                "Código inválido"
            ),
            validateField(
                form.find(`#${prefix}nombre_producto`),
                null,
                $(`#error_${prefix}nombre_producto`),
                "Nombre inválido"
            ),
            validateField(
                form.find(`#${prefix}categoria_producto`),
                null,
                $(`#error_${prefix}categoria_producto`),
                "Selección inválida"
            ),
            validateField(
                form.find(`#${prefix}unidad_medida_producto`),
                null,
                $(`#error_${prefix}unidad_medida_producto`),
                "Selección inválida"
            ),
            validateField(
                form.find(`#${prefix}precio_compra_producto`),
                /^\d+(\.\d{1,2})?$/,
                $(`#error_${prefix}precio_compra_producto`),
                "Precio inválido (ej. 10.50)"
            ),
            validateField(
                form.find(`#${prefix}precio_venta_producto`),
                /^\d+(\.\d{1,2})?$/,
                $(`#error_${prefix}precio_venta_producto`),
                "Precio inválido (ej. 15.00)"
            ),
            validateField(
                form.find(`#${prefix}tiene_iva_producto`),
                null,
                $(`#error_${prefix}tiene_iva_producto`),
                "Selección inválida"
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

    // Mostrar lista de productos
    const mostrarProductos = async () => {
        const productos = await fetchData("ajax/producto.ajax.php");
        if (!productos) return;
        const tabla = $("#tabla_productos");
        const tbody = tabla.find("tbody");
        tbody.empty();

        productos.data.forEach((producto, index) => {
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${producto.codigo}</td>
                    <td>${producto.codigo_barras || 'N/A'}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.nombre_categoria || 'N/A'}</td>
                    <td>${producto.unidad_medida}</td>
                    <td>${formatCurrency(producto.precio_compra)}</td>
                    <td>${formatCurrency(producto.precio_venta)}</td>
                    <td class="text-center">
                        ${producto.tiene_iva != 0
                    ? `<span class="badge btn-estado-success">Sí</span>`
                    : `<span class="badge btn-estado-danger">No</span>`
                }
                    </td>
                    <td class="text-center">
                        ${producto.estado != 0
                    ? `<button class="btn btn-sm text-white btn-estado-success btn-sm btnActivarProducto" idProducto="${producto.id_producto}" estadoProducto="0">Activado</button>`
                    : `<button class="btn btn-sm text-white btn-estado-danger btn-sm btnActivarProducto" idProducto="${producto.id_producto}" estadoProducto="1">Desactivado</button>`
                }
                    </td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarProducto" idProducto="${producto.id_producto}" data-bs-toggle="modal" data-bs-target="#modal_editar_producto">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                         <a href="#" class="me-3 btnVerDetalles" idProducto="${producto.id_producto}" data-bs-toggle="modal" data-bs-target="#modal_ver_detalles">
                            <i class="text-primary fas fa-eye fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnEliminarProducto" idProducto="${producto.id_producto}">
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

    // Cargar categorías activas en select
    const cargarCategorias = async (selectId) => {
        const categorias = await fetchData("ajax/categoria.ajax.php");
        if (!categorias || !categorias.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar categoría</option>');

        categorias.data.forEach(categoria => {
            if (categoria.estado == 1) {
                select.append(`<option value="${categoria.id_categoria}">${categoria.nombre}</option>`);
            }
        });
    };

    // Evento para guardar nuevo producto
    $("#btn_guardar_producto").click(async function (e) {
        e.preventDefault();
        if (validateProductoForm("form_nuevo_producto")) {
            const datos = new FormData($("#form_nuevo_producto")[0]);
            datos.append('action', 'crear');
            datos.forEach(element => {
                console.log(element);
            });
            const response = await fetchData("ajax/producto.ajax.php", "POST", datos);

            if (response?.status) {
                resetForm("form_nuevo_producto");
                $("#modal_nuevo_producto").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Producto registrado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_productos")) {
                    $("#tabla_productos").DataTable().destroy();
                }
                await mostrarProductos();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al registrar el producto", "error");
            }
        }
    });

    // Evento para ver detalles del producto
    $("#tabla_productos").on("click", ".btnVerDetalles", async function () {
        const idProducto = $(this).attr("idProducto");
        const formData = new FormData();
        formData.append('id_producto', idProducto);
        formData.append('action', "editar"); // Reutilizamos la misma acción del modal editar

        const response = await fetchData("ajax/producto.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            
            // Llenamos los campos del modal de detalles
            $("#detalle_codigo").text(data.codigo);
            $("#detalle_nombre").text(data.nombre);
            $("#detalle_categoria").text(data.nombre_categoria || 'N/A');
            $("#detalle_descripcion").text(data.descripcion || 'Sin descripción');
            $("#detalle_unidad_medida").text(data.unidad_medida);
            $("#detalle_peso_promedio").text(data.peso_promedio ? data.peso_promedio + ' kg' : 'N/A');
            $("#detalle_precio_compra").text(formatCurrency(data.precio_compra));
            $("#detalle_precio_venta").text(formatCurrency(data.precio_venta));
            $("#detalle_iva").html(data.tiene_iva != 0 ? '<span class="badge bg-success">Sí aplica</span>' : '<span class="badge bg-danger">No aplica</span>');
            
            // Mostramos la imagen si existe
            if (data.imagen) {
                $("#detalle_imagen").attr("src", `vistas/img/productos/${data.imagen}`).show();
            } else {
                $("#detalle_imagen").hide();
            }
        } else {
            Swal.fire("Error", "No se pudieron cargar los detalles del producto", "error");
        }
    });


    // Evento para editar producto
    $("#tabla_productos").on("click", ".btnEditarProducto", async function () {
        const idProducto = $(this).attr("idProducto");
        const formData = new FormData();
        formData.append('id_producto', idProducto);
        formData.append('action', "editar");

        const response = await fetchData("ajax/producto.ajax.php", "POST", formData);
        if (response?.status) {
            const data = response.data;
            $("#edit_id_producto").val(data.id_producto);
            $("#edit_codigo_producto").val(data.codigo);
            $("#edit_codigo_barras_producto").val(data.codigo_barras || '');
            $("#edit_nombre_producto").val(data.nombre);
            $("#edit_descripcion_producto").val(data.descripcion || '');
            $("#edit_peso_promedio_producto").val(data.peso_promedio || '');
            $("#edit_precio_compra_producto").val(data.precio_compra);
            $("#edit_precio_venta_producto").val(data.precio_venta);
            $("#edit_estado_producto").val(data.estado);

            // Seleccionar unidades y categoría
            $("#edit_unidad_medida_producto").val(data.unidad_medida || 'UNIDAD').trigger('change');
            $("#edit_tiene_iva_producto").val(data.tiene_iva).trigger('change');

            // Cargar categorías y seleccionar la actual
            await cargarCategorias("edit_categoria_producto");
            $("#edit_categoria_producto").val(data.id_categoria).trigger('change');

            // Mostrar imagen actual si existe
            if (data.imagen) {
                $("#edit_imagen_actual").val(data.imagen);
                $("#img_producto_actual").attr("src", `vistas/img/productos/${data.imagen}`);
                $("#img_producto_actual").show();
            } else {
                $("#img_producto_actual").hide();
            }
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del producto", "error");
        }
    });

    // Evento para actualizar producto
    $("#btn_update_producto").click(async function (e) {
        e.preventDefault();
        if (validateProductoForm("form_update_producto", true)) {
            const formData = new FormData($("#form_update_producto")[0]);
            formData.append("action", "actualizar");

            const response = await fetchData("ajax/producto.ajax.php", "POST", formData);

            if (response?.status) {
                resetForm("form_update_producto");
                $("#modal_editar_producto").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Producto actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_productos")) {
                    $("#tabla_productos").DataTable().destroy();
                }
                await mostrarProductos();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el producto", "error");
            }
        }
    });

    // Evento para activar/desactivar producto
    $("#tabla_productos").on("click", ".btnActivarProducto", async function () {
        const idProducto = $(this).attr("idProducto");
        const estadoProducto = $(this).attr("estadoProducto");
        const formData = new FormData();
        formData.append('id_producto', idProducto);
        formData.append('estado', estadoProducto);
        formData.append('action', 'cambiarEstado');

        const response = await fetchData("ajax/producto.ajax.php", "POST", formData);
        if (response?.status) {
            Swal.fire("¡Correcto!", "Estado del producto actualizado", "success");
            if ($.fn.DataTable.isDataTable("#tabla_productos")) {
                $("#tabla_productos").DataTable().destroy();
            }
            await mostrarProductos();
        } else {
            Swal.fire("Error", "No se pudo cambiar el estado", "error");
        }
    });

    // Evento para eliminar producto
    $("#tabla_productos").on("click", ".btnEliminarProducto", async function (e) {
        e.preventDefault();
        const idProducto = $(this).attr("idProducto");

        const result = await Swal.fire({
            title: "¿Está seguro de eliminar este producto?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#655CC9",
            cancelButtonColor: "#E53250",
            confirmButtonText: "Sí, eliminar"
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append("id_producto", idProducto);
            formData.append("action", "eliminar");

            const response = await fetchData("ajax/producto.ajax.php", "POST", formData);
            if (response?.status) {
                Swal.fire("¡Eliminado!", response.message || "Producto eliminado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_productos")) {
                    $("#tabla_productos").DataTable().destroy();
                }
                await mostrarProductos();
            } else {
                Swal.fire("Error", response?.message || "Error al eliminar el producto", "error");
            }
        }
    });

    // Cargar datos iniciales
    cargarCategorias("categoria_producto");
    mostrarProductos();
});