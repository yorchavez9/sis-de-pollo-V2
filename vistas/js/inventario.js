$(document).ready(function () {

    function formatearFecha(fecha) {
        const date = new Date(fecha);
        const options = {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        return date.toLocaleString('es-ES', options);
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
    $('#modal_ajustar_inventario').on('shown.bs.modal', function() {
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

    // Validación de formulario de inventario
    const validateInventarioForm = () => {
        const isValid = [
            validateField($("#producto_inventario"), null, $("#error_producto_inventario"), "Selección inválida"),
            validateField($("#almacen_inventario"), null, $("#error_almacen_inventario"), "Selección inválida"),
            validateField($("#tipo_movimiento"), null, $("#error_tipo_movimiento"), "Selección inválida"),
            validateField($("#cantidad_inventario"), /^[0-9]+(\.[0-9]{1,3})?$/, $("#error_cantidad_inventario"), "Cantidad inválida (ej: 1.500)")
        ].every(Boolean);
        
        return isValid;
    };

    // Resetear formulario
    const resetForm = () => {
        $("#form_ajustar_inventario")[0].reset();
        $("#form_ajustar_inventario small").html("").removeClass("text-danger");
        $("#id_inventario").val("");
        $("#stock_actual").val("");
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

    // Mostrar lista de inventario
    const mostrarInventario = async () => {
        const filtroAlmacen = $("#filtro_almacen").val();
        const filtroProducto = $("#filtro_producto").val();
        const filtroEstado = $("#filtro_estado").val();
        
        let url = "ajax/inventario.ajax.php";
        if (filtroAlmacen || filtroProducto || filtroEstado) {
            url += `?filtro_almacen=${filtroAlmacen}&filtro_producto=${filtroProducto}&filtro_estado=${filtroEstado}`;
        }
        const inventario = await fetchData(url);
        if (!inventario) return;

        const tabla = $("#tabla_inventario");
        const tbody = tabla.find("tbody");
        tbody.empty();

        inventario.data.forEach((item, index) => {
            // Determinar clase de estado
            let estadoClass = "";
            let estadoText = "";
            
            if (item.stock_minimo > 0 && item.stock <= item.stock_minimo) {
                estadoClass = "text-danger";
                estadoText = "Bajo Mínimo";
            } else if (item.stock_maximo > 0 && item.stock >= item.stock_maximo) {
                estadoClass = "text-warning";
                estadoText = "Sobre Máximo";
            } else {
                estadoClass = "text-success";
                estadoText = "Normal";
            }
            
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nombre_producto}</td>
                    <td>${item.codigo_producto}</td>
                    <td>${item.nombre_almacen}</td>
                    <td class="font-weight-bold">${parseFloat(item.stock).toFixed(3)}</td>
                    <td>${item.stock_minimo ? parseFloat(item.stock_minimo).toFixed(3) : 'N/A'}</td>
                    <td>${item.stock_maximo ? parseFloat(item.stock_maximo).toFixed(3) : 'N/A'}</td>
                    <td class="${estadoClass}">${estadoText}</td>
                    <td>${formatearFecha(item.ultima_actualizacion)}</td>
                    <td class="text-center">
                        <a href="#" class="me-3 btnEditarInventario" idInventario="${item.id_inventario}" data-bs-toggle="modal" data-bs-target="#modal_ajustar_inventario">
                            <i class="text-warning fas fa-edit fa-lg"></i>
                        </a>
                        <a href="#" class="me-3 btnHistorialInventario" idInventario="${item.id_inventario}" data-bs-toggle="modal" data-bs-target="#modal_historial_inventario">
                            <i class="text-info fas fa-history fa-lg"></i>
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
            order: [[3, 'asc'], [1, 'asc']], // Ordenar por almacén y producto
        });
    };

    // Cargar productos en select
    const cargarProductos = async (selectId) => {
        const productos = await fetchData("ajax/producto.ajax.php");
        if (!productos || !productos.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar producto</option>');
        
        productos.data.forEach(producto => {
            select.append(`<option value="${producto.id_producto}">${producto.codigo} - ${producto.nombre}</option>`);
        });
    };

    // Cargar almacenes en select
    const cargarAlmacenes = async (selectId) => {
        const almacenes = await fetchData("ajax/almacen.ajax.php");
        if (!almacenes || !almacenes.status) return;

        const select = $(`#${selectId}`);
        select.empty();
        select.append('<option value="" disabled selected>Seleccionar almacén</option>');
        
        almacenes.data.forEach(almacen => {
            select.append(`<option value="${almacen.id_almacen}">${almacen.nombre}</option>`);
        });
    };

    // Evento para cargar stock actual cuando se selecciona producto y almacén
    $("#producto_inventario, #almacen_inventario").change(async function() {
        const idProducto = $("#producto_inventario").val();
        const idAlmacen = $("#almacen_inventario").val();
        
        if (idProducto && idAlmacen) {
            const stock = await fetchData(`ajax/inventario.ajax.php?action=consultar&id_producto=${idProducto}&id_almacen=${idAlmacen}`);
            if (stock?.status) {
                $("#stock_actual").val(parseFloat(stock.data.stock).toFixed(3));
                $("#stock_minimo").val(stock.data.stock_minimo ? parseFloat(stock.data.stock_minimo).toFixed(3) : '');
                $("#stock_maximo").val(stock.data.stock_maximo ? parseFloat(stock.data.stock_maximo).toFixed(3) : '');
                $("#id_inventario").val(stock.data.id_inventario || '');
            } else {
                $("#stock_actual").val("0.000");
                $("#stock_minimo").val("");
                $("#stock_maximo").val("");
                $("#id_inventario").val("");
            }
        }
    });

    // Evento para guardar ajuste de inventario
    $("#btn_guardar_inventario").click(async function (e) {
        e.preventDefault();
        if (validateInventarioForm()) {
            const formData = new FormData($("#form_ajustar_inventario")[0]);
            const response = await fetchData("ajax/inventario.ajax.php", "POST", formData);
   
            if (response?.status) {
                resetForm();
                $("#modal_ajustar_inventario").modal("hide");
                Swal.fire("¡Correcto!", response.message || "Inventario actualizado con éxito", "success");
                if ($.fn.DataTable.isDataTable("#tabla_inventario")) {
                    $("#tabla_inventario").DataTable().destroy();
                }
                await mostrarInventario();
            } else {
                Swal.fire("¡Error!", response?.message || "Error al actualizar el inventario", "error");
            }
        }
    });

    // Evento para editar inventario
    $("#tabla_inventario").on("click", ".btnEditarInventario", async function () {
        const idInventario = $(this).attr("idInventario");
        const response = await fetchData(`ajax/inventario.ajax.php?action=editar&id_inventario=${idInventario}`);
        
        if (response?.status) {
            const data = response.data;
            
            // Cargar productos y seleccionar el actual
            await cargarProductos("producto_inventario");
            $("#producto_inventario").val(data.id_producto).trigger('change');
            
            // Cargar almacenes y seleccionar el actual
            await cargarAlmacenes("almacen_inventario");
            $("#almacen_inventario").val(data.id_almacen).trigger('change');
            
            // Llenar los demás campos
            $("#stock_actual").val(parseFloat(data.stock).toFixed(3));
            $("#stock_minimo").val(data.stock_minimo ? parseFloat(data.stock_minimo).toFixed(3) : '');
            $("#stock_maximo").val(data.stock_maximo ? parseFloat(data.stock_maximo).toFixed(3) : '');
            $("#id_inventario").val(data.id_inventario);
            
            // Configurar el modal para edición
            $("#tipo_movimiento").val("ajuste").trigger('change');
            $("#cantidad_inventario").val("");
        } else {
            Swal.fire("Error", "No se pudieron cargar los datos del inventario", "error");
        }
    });

    // Asignar evento al botón de cerrar
    $('button[data-bs-dismiss="modal"]').on('click', function() {
        if ($.fn.DataTable.isDataTable("#tabla_historial")) {
            $("#tabla_historial").DataTable().destroy();
            $("#tabla_inventario").DataTable().destroy();
        }
    });

    // Evento para ver historial de inventario
    $("#tabla_inventario").on("click", ".btnHistorialInventario", async function () {
        const idInventario = $(this).attr("idInventario");
        const response = await fetchData(`ajax/inventario.ajax.php?action=historial&id_inventario=${idInventario}`);
        if (response?.status) {
            const tabla = $("#tabla_historial");
            const tbody = tabla.find("tbody");
            tbody.empty();

            response.data.forEach(movimiento => {
                const fila = `
                    <tr>
                        <td>${formatearFecha(movimiento.fecha)}</td>
                        <td>
                            ${movimiento.tipo_movimiento === 'entrada' ? 
                              '<span class="badge bg-success">Entrada</span>' : 
                              movimiento.tipo_movimiento === 'salida' ? 
                              '<span class="badge bg-danger">Salida</span>' : 
                              '<span class="badge bg-info">Ajuste</span>'}
                        </td>
                        <td>${parseFloat(movimiento.cantidad).toFixed(3)}</td>
                        <td>${parseFloat(movimiento.stock_anterior).toFixed(3)}</td>
                        <td>${parseFloat(movimiento.stock_nuevo).toFixed(3)}</td>
                        <td>${movimiento.nombre_usuario || 'Sistema'}</td>
                        <td>${movimiento.motivo || 'N/A'}</td>
                    </tr>`;
                tbody.append(fila);
            });

            if ($.fn.DataTable.isDataTable(tabla)) {
                tabla.DataTable().destroy();
            }
            tabla.DataTable({
                autoWidth: false,
                responsive: true,
                order: [[0, 'desc']], // Ordenar por fecha descendente
            });
        } else {
            Swal.fire("Error", "No se pudo cargar el historial", "error");
        }
    });

    // Eventos para filtros
    $("#filtro_almacen, #filtro_producto, #filtro_estado").change(async function() {
        if ($.fn.DataTable.isDataTable("#tabla_inventario")) {
            $("#tabla_inventario").DataTable().destroy();
        }
        await mostrarInventario();
    });

    // Cargar datos iniciales
    cargarProductos("producto_inventario");
    cargarAlmacenes("almacen_inventario");
    cargarAlmacenes("filtro_almacen");
    cargarProductos("filtro_producto");
    mostrarInventario();
});