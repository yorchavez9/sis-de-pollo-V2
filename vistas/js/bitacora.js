$(document).ready(function () {
    // Variables globales
    let tablaBitacora;
    
    // Función para formatear JSON
    const formatJSON = (jsonString) => {
        try {
            if (!jsonString) return "No hay datos";
            const obj = JSON.parse(jsonString);
            return JSON.stringify(obj, null, 2);
        } catch (e) {
            return jsonString || "No hay datos";
        }
    };

    // Función para cargar usuarios en el filtro
    const cargarUsuariosFiltro = async () => {
        try {
            const response = await fetch('ajax/usuario.ajax.php');
            const data = await response.json();
            
            if (data.status) {
                const select = $('#filtro_usuario');
                select.empty();
                select.append('<option value="">Todos</option>');
                
                data.data.forEach(usuario => {
                    select.append(`<option value="${usuario.id_usuario}">${usuario.nombre} ${usuario.apellidos}</option>`);
                });
            }
        } catch (error) {
            console.error('Error al cargar usuarios:', error);
        }
    };

    // Función para mostrar registros de bitácora
    const mostrarBitacora = async (filtros = {}) => {
        try {
            // Construir URL con filtros
            let url = 'ajax/bitacora.ajax.php?';
            const params = new URLSearchParams();
            
            if (filtros.fecha_inicio) params.append('fecha_inicio', filtros.fecha_inicio);
            if (filtros.fecha_fin) params.append('fecha_fin', filtros.fecha_fin);
            if (filtros.id_usuario) params.append('id_usuario', filtros.id_usuario);
            if (filtros.accion) params.append('accion', filtros.accion);
            
            url += params.toString();
            
            const response = await fetch(url);
            const data = await response.json();
            
            const tabla = $('#tabla_bitacora');
            const tbody = tabla.find('tbody');
            tbody.empty();
            
            if (data.status) {
                data.data.forEach((registro, index) => {
                    const fila = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${registro.fecha_registro}</td>
                            <td>${registro.nombre_usuario || 'Sistema'}</td>
                            <td>${registro.accion}</td>
                            <td>${registro.tabla_afectada || 'N/A'}</td>
                            <td>${registro.id_registro_afectado || 'N/A'}</td>
                            <td>${registro.ip || 'N/A'}</td>
                            <td>${registro.dispositivo || 'N/A'}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info btnVerDetalles" 
                                    data-anteriores='${registro.datos_anteriores || ''}'
                                    data-nuevos='${registro.datos_nuevos || ''}'
                                    data-bs-toggle="modal" data-bs-target="#modal_detalles_bitacora">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>`;
                    tbody.append(fila);
                });
                
                // Inicializar DataTable si no existe
                if ($.fn.DataTable.isDataTable(tabla)) {
                    tablaBitacora.destroy();
                }
                
                tablaBitacora = tabla.DataTable({
                    autoWidth: false,
                    responsive: true,
                    dom: '<"top"Bf>rt<"bottom"lip><"clear">',
                    buttons: [
                        'excel', 'pdf', 'print'
                    ],
                    order: [[1, 'desc']] // Ordenar por fecha descendente
                });
            } else {
                console.error('Error al cargar bitácora:', data.message);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Aplicar filtros
    const aplicarFiltros = () => {
        const filtros = {
            fecha_inicio: $('#filtro_fecha_inicio').val(),
            fecha_fin: $('#filtro_fecha_fin').val(),
            id_usuario: $('#filtro_usuario').val(),
            accion: $('#filtro_accion').val()
        };
        
        mostrarBitacora(filtros);
    };

    // Evento para ver detalles
    $('#tabla_bitacora').on('click', '.btnVerDetalles', function () {
        const anteriores = $(this).data('anteriores');
        const nuevos = $(this).data('nuevos');
        
        $('#detalles_datos_anteriores').text(formatJSON(anteriores));
        $('#detalles_datos_nuevos').text(formatJSON(nuevos));
    });

    // Evento para limpiar bitácora
    $('#btn_limpiar_bitacora').click(async function () {
        const result = await Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción eliminará todos los registros de la bitácora y no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar'
        });
        
        if (result.isConfirmed) {
            try {
                // Crear FormData
                const formData = new FormData();
                formData.append('action', 'limpiar');
                
                // Enviar con FormData
                const response = await fetch('ajax/bitacora.ajax.php', {
                    method: 'POST',
                    body: formData
                    // No necesitas headers con FormData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    Swal.fire('¡Éxito!', data.message, 'success');
                    mostrarBitacora();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Ocurrió un error al limpiar la bitácora', 'error');
            }
        }
    });

    // Eventos de cambio en filtros
    $('#filtro_fecha_inicio, #filtro_fecha_fin, #filtro_usuario, #filtro_accion').change(aplicarFiltros);

    // Inicializar
    cargarUsuariosFiltro();
    mostrarBitacora();
    
    // Configurar fecha máxima como hoy
    const today = new Date().toISOString().split('T')[0];
    $('#filtro_fecha_inicio, #filtro_fecha_fin').attr('max', today);
});