$(document).ready(function() {
    /* ======================================================
    DATOS DE INICIO DE SESION
    ====================================================== */
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

    /* ======================================================
    ENVIOS
    ====================================================== */
    const mostrarEnvios = async () => {
        let url = "ajax/envios.ajax.php?action=listar";
        try {
            const [sesion, envios] = await Promise.all([
                obtenerSesion(),
                fetchData(url)
            ]);
    
            if (!sesion || !sesion.permisos) {
                console.error("No hay sesión o permisos");
                return;
            }
            if (!envios || !envios.status) {
                console.error("Error al cargar envíos:", envios?.message);
                return;
            }
         
    
            // Actualizar contadores
            const enviosEnTransito = envios.data.filter(envio => envio.estado === 'EN_TRANSITO').length;
            const enviosPendiente = envios.data.filter(envio => envio.estado === 'PENDIENTE').length;
            const enviosEntregados = envios.data.filter(envio => envio.estado === 'ENTREGADO').length;
            
            $('#total_envios_realizados').text(enviosEnTransito);
            $('#envios_pendientes').text(enviosPendiente);
            $('#envios_entregados').text(enviosEntregados);
    
            // Renderizar tabla
            const tabla = $("#tabla_envios_recientes");
            const tbody = tabla.find("tbody");
            tbody.empty();
    
            // Verificar si hay datos de sesión y sucursal
            if (!sesion.usuario || !sesion.usuario.id_sucursal) {
                console.error("No hay información de sucursal en la sesión");
                return;
            }
    
            // Filtrar y mostrar envíos
            const enviosFiltrados = envios.data.filter(envio => {
                return sesion.usuario.id_sucursal === envio.id_sucursal_origen;
            });
    
            if (enviosFiltrados.length === 0) {
                console.warn("No hay envíos para mostrar para esta sucursal");
                tbody.append('<tr><td colspan="4" class="text-center">No hay envíos recientes</td></tr>');
            } else {
                enviosFiltrados.forEach((envio) => {
                    const fechaEnvio = envio.fecha_creacion ? 
                        new Date(envio.fecha_creacion).toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : 'Pendiente';
    
                    let claseEstado = "";
                    switch (envio.estado) {
                        case 'PENDIENTE': claseEstado = "badge bg-secondary"; break;
                        case 'PREPARACION': claseEstado = "badge bg-warning text-dark"; break;
                        case 'EN_TRANSITO': claseEstado = "badge bg-primary"; break;
                        case 'EN_REPARTO': claseEstado = "badge bg-info"; break;
                        case 'ENTREGADO': claseEstado = "badge bg-success"; break;
                        case 'CANCELADO': case 'RECHAZADO': claseEstado = "badge bg-danger"; break;
                        default: claseEstado = "badge bg-secondary";
                    }
    
                    const fila = `
                    <tr>
                        <td>${envio.codigo_envio}</td>
                        <td>${envio.sucursal_destino}</td>
                        <td><span class="${claseEstado}">${envio.estado.replace('_', ' ')}</span></td>
                        <td>${fechaEnvio}</td>
                    </tr>`;
                    tbody.append(fila);
                });
            }
    
            // Inicializar/reiniciar DataTable
            if ($.fn.DataTable.isDataTable(tabla)) {
                tabla.DataTable().destroy();
            }
            tabla.DataTable({
                autoWidth: false,
                responsive: true,
                searching: false
            });
    
        } catch (error) {
            console.error('Error en mostrarEnvios:', error);
        }
    };

    // Mostrar lista de clientes
    const mostrarClientes = async () => {

        const [sesion, clientes] = await Promise.all([
            obtenerSesion(),
            fetchData("ajax/cliente.ajax.php")
        ]);

        if (!sesion || !sesion.permisos) {
            return;
        }

        if (!clientes) return;
        let total_clientes = clientes.data.filter(cliente => cliente.estado === 1).length;
        $("#cliente_activos").text(total_clientes);
    }

    const mostrarTransportistas = async () => {

        const [sesion, transportistas] = await Promise.all([
            obtenerSesion(),
            fetchData("ajax/transportista.ajax.php")
        ]);

        if (!sesion || !sesion.permisos) {
            return;
        }
        if (!transportistas) return;
        let total_transportistas = transportistas.data.filter(transportista => transportista.estado === 1).length;
        $("#transportistas_activos").text(total_transportistas);
    }

    // Llamar la función al cargar el documento
    if ($.fn.DataTable.isDataTable("#tabla_envios_recientes")) {
        $("#tabla_envios_recientes").DataTable().destroy();
    }
    mostrarEnvios();
    mostrarClientes();
    mostrarTransportistas();
});