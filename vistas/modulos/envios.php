<style>
    /* Estilos para el dropdown */
    .dropdown-toggle {
        transition: all 0.3s ease;
        border-radius: 4px;
    }

    .dropdown-toggle:hover {
        background-color: #0d6efd;
        color: white !important;
    }

    .dropdown-menu {
        border: none;
        border-radius: 8px;
        min-width: 180px;
        padding: 5px 0;
    }

    .dropdown-item {
        padding: 8px 15px;
        font-size: 14px;
        border-radius: 4px;
        margin: 2px 8px;
        transition: all 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item span {
        flex-grow: 1;
    }

    /* Colores para íconos */
    .fa-eye {
        color: #0d6efd;
    }

    .fa-exchange-alt {
        color: #ffc107;
    }

    .fa-times {
        color: #dc3545;
    }

    .fa-print {
        color: #6c757d;
    }
</style>
<?php
if (isset($_SESSION["permisos"])) {
    $permisos = $_SESSION["permisos"];
?>
    <div class="page-wrapper">
        <div class="content">
            <!-- Encabezado -->
            <!-- Encabezado -->
            <div class="page-header">
                <div class="page-title">
                    <h4>Gestión de Envíos <i class="fas fa-truck me-1"></i></h4>
                    <h6>Administración de envíos entre sucursales</h6>
                </div>
                <?php if (isset($permisos["envios"]) && in_array("crear", $permisos["envios"]["acciones"])): ?>
                    <div class="page-btn d-flex align-items-center gap-2">
                        <button class="btn btn-added" id="btnNuevoEnvio">
                            <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Nuevo Envío
                        </button>
                        <button class="btn btn-primary" id="btnCambiarEstadoMasivo" disabled>
                            <i class="fas fa-exchange-alt me-2"></i>Cambiar Estado Masivo
                        </button>
                    </div>
                <?php endif; ?>
            </div>


            <!-- Filtros -->
            <div class="card">
                <div class="card-body">
                    <form id="formFiltroEnvios" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sucursal Origen</label>
                                <select class="select" id="filtroOrigen">
                                    <option value="">Todas</option>
                                    <!-- Opciones dinámicas -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sucursal Destino</label>
                                <select class="select" id="filtroDestino">
                                    <option value="">Todas</option>
                                    <!-- Opciones dinámicas -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tipo</label>
                                <select class="select" id="filtroTipo">
                                    <option value="">Todos</option>
                                    <option value="1">Productos Perecederos</option>
                                    <option value="2">Productos Secos</option>
                                    <option value="3">Documentos</option>
                                    <option value="4">Insumos</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Estado</label>
                                <select class="select" id="filtroEstado">
                                    <option value="">Todos</option>
                                    <option value="PENDIENTE">Pendiente</option>
                                    <option value="PREPARACION">Preparación</option>
                                    <option value="EN_TRANSITO">En Tránsito</option>
                                    <option value="EN_REPARTO">En Reparto</option>
                                    <option value="ENTREGADO">Entregado</option>
                                    <option value="CANCELADO">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="btnFiltrarEnvios" class="btn btn-primary btn-block">
                                    <i class="fas fa-filter me-2"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de envíos -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datanew" id="tablaEnvios">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th> <!-- Checkbox para seleccionar todas -->
                                    <th>N°</th>
                                    <th>Código</th>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>Tipo</th>
                                    <th>Fecha creación</th>
                                    <th>Estado</th>
                                    <th>Transportista</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Datos dinámicos -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Nuevo Envío -->
    <div class="modal fade" id="modalNuevoEnvio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Envío</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="formNuevoEnvio" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Sección: Comprobante serie y numero -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Comprobante</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Selecione el comprobante<span class="text-danger">*</span></label>
                                            <select class="form-select select" name="id_serie" id="id_serie">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Serie <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="serie" id="serie" readonly placeholder="Serie">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Información Básica del Envío -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Información del Envío</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Código de Envío</label>
                                            <input type="text" class="form-control" name="codigo_envio" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Fecha Estimada de Entrega <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" name="fecha_estimada_entrega" id="fecha_estimada_entrega">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tipo de Envío <span class="text-danger">*</span></label>
                                            <select class="form-select select" name="id_tipo_encomienda" id="id_tipo_encomienda">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Origen y Destino -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-route me-2"></i> Ruta del Envío</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sucursal Origen <span class="text-danger">*</span></label>
                                            <select class="form-select select" name="id_sucursal_origen" id="id_sucursal_origen">
                                                <option value="" disabled selected>Seleccionar</option>
                                                <!-- Opciones dinámicas -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sucursal Destino <span class="text-danger">*</span></label>
                                            <select class="form-select select" name="id_sucursal_destino" id="id_sucursal_destino">
                                                <option value="" disabled selected>Seleccionar</option>
                                                <!-- Opciones dinámicas -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Información de Personas -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-users me-2"></i> Información de Personas</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Remitente</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>DNI Remitente</label>
                                                    <input type="text" class="form-control" name="dni_remitente" placeholder="Ingrese el DNI">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Nombre Remitente</label>
                                                    <input type="text" class="form-control" name="nombre_remitente" placeholder="Ingrese el nombre">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Destinatario</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>DNI Destinatario</label>
                                                    <input type="text" class="form-control" name="dni_destinatario" placeholder="Ingrese el DNI">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Nombre Destinatario</label>
                                                    <input type="text" class="form-control" name="nombre_destinatario" placeholder="Ingrese el nombre">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Configuración del Envío -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cog me-2"></i> Configuración</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Transportista</label>
                                            <select class="form-select select" name="id_transportista" id="id_transportista">
                                                <option value="" selected>No asignado</option>
                                                <!-- Opciones dinámicas -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Método de Pago</label>
                                            <select class="form-select select" name="metodo_pago">
                                                <option value="EFECTIVO">Efectivo</option>
                                                <option value="CREDITO">Crédito</option>
                                                <option value="POR_COBRAR">Por Cobrar</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Clave de Recepción</label>
                                            <input type="text" class="form-control" name="clave_recepcion" placeholder="Ingrese la clave">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <label>Instrucciones Especiales</label>
                                    <textarea class="form-control" name="instrucciones" rows="2" placeholder="Ingrese instrucciones adicionales"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Paquetes -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-boxes me-2"></i> Paquetes</h6>
                                <button type="button" class="btn btn-sm btn-primary" id="btnAgregarPaquete">
                                    <i class="fas fa-plus me-1"></i> Agregar Paquete
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="contenedorPaquetes">
                                    <!-- Paquetes se agregarán aquí -->
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Resumen -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-calculator me-2"></i> Resumen del Envío</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Total Paquetes</label>
                                            <input type="text" class="form-control" name="cantidad_paquetes" id="totalPaquetes" readonly value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Peso Total (kg)</label>
                                            <input type="text" class="form-control" name="peso_total" id="pesoTotal" readonly value="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Volumen Total (m³)</label>
                                            <input type="text" class="form-control" name="volumen_total" id="volumenTotal" readonly value="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Costo Estimado</label>
                                            <input type="text" class="form-control" name="costo_envio" id="costoEnvio" value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnCalcularCosto">
                            <i class="fas fa-calculator me-2"></i> Calcular Costo
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Guardar Envío
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Plantilla Paquete -->
    <template id="templatePaquete">
        <div class="card mb-3 paquete">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Paquete <span class="numero-paquete"></span></h6>
                <button type="button" class="btn btn-sm btn-danger btnEliminarPaquete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <input type="text" class="form-control descripcion" placeholder="Descripción del paquete">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Peso (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control peso" step="0.01" min="0.01" placeholder="Peso del paquete">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Alto (cm)</label>
                            <input type="number" class="form-control alto" step="0.1" min="0" placeholder="Alto del paquete">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ancho (cm)</label>
                            <input type="number" class="form-control ancho" step="0.1" min="0" placeholder="Ancho del paquete">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profundidad (cm)</label>
                            <input type="number" class="form-control profundidad" step="0.1" min="0" placeholder="Profundidad del paquete">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Instrucciones de Manejo</label>
                    <textarea class="form-control instrucciones" rows="2" placeholder="Instrucciones del paquete"></textarea>
                </div>

                <h6 class="mt-4 mb-3"><i class="fas fa-box-open me-2"></i> Ítems del Paquete</h6>
                <button type="button" class="btn btn-sm btn-primary btnAgregarItem mb-3">
                    <i class="fas fa-plus me-1"></i> Agregar Ítem
                </button>
                <div class="table-responsive">
                    <table class="table table-sm table-items">
                        <thead>
                            <tr>
                                <th width="40%">Producto</th>
                                <th width="15%">Cantidad</th>
                                <th width="15%">Peso (kg)</th>
                                <th width="20%">Valor Unit.</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ítems se agregarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>

    <!-- Plantilla Ítem -->
    <template id="templateItemPaquete">
        <tr class="item">
            <td>
                <select class="form-control form-control-sm selectProducto">
                    <option value="" disabled selected>Seleccionar</option>
                    <!-- Opciones dinámicas -->
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm cantidad" min="1" value="1" placeholder="Cantidad">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm pesoUnitario" step="0.01" min="0.01" placeholder="Peso">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm valorUnitario" step="0.01" min="0" placeholder="Valor">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btnEliminarItem">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Modal Detalle Envío -->
    <div class="modal fade" id="modalDetalleEnvio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del Envío <span id="codigoEnvio"></span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Información Básica</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr>
                                                    <th width="40%">Código:</th>
                                                    <td id="detalleCodigo"></td>
                                                </tr>
                                                <tr>
                                                    <th>Sucursal Origen:</th>
                                                    <td id="detalleOrigen"></td>
                                                </tr>
                                                <tr>
                                                    <th>Sucursal Destino:</th>
                                                    <td id="detalleDestino"></td>
                                                </tr>
                                                <tr>
                                                    <th>Tipo de Envío:</th>
                                                    <td id="detalleTipo"></td>
                                                </tr>
                                                <tr>
                                                    <th>Transportista:</th>
                                                    <td id="detalleTransportista"></td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha Creación:</th>
                                                    <td id="detalleFechaCreacion"></td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha Envío:</th>
                                                    <td id="detalleFechaEnvio"></td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha Recepción:</th>
                                                    <td id="detalleFechaRecepcion"></td>
                                                </tr>
                                                <tr>
                                                    <th>Estado:</th>
                                                    <td id="detalleEstado"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Seguimiento</h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="btnNuevoSeguimiento">
                                        <i class="fas fa-plus me-1"></i> Nuevo
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="timeline" id="timelineSeguimiento">
                                        <!-- Seguimiento se agregará aquí -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Paquetes (<span id="totalPaquetesDetalle">0</span>)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="tablaPaquetes">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Descripción</th>
                                                    <th>Peso</th>
                                                    <th>Volumen</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Paquetes se agregarán aquí -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Documentos</h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="btnSubirDocumento">
                                        <i class="fas fa-upload me-1"></i> Subir
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="listaDocumentos">
                                        <!-- Documentos se agregarán aquí -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnImprimirGuia">
                        <i class="fas fa-print me-2 text-white"></i> Imprimir Guía
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Plantilla Seguimiento -->
    <template id="templateSeguimiento">
        <div class="timeline-item">
            <div class="timeline-time">
                <span class="fecha">{{fecha}}</span>
            </div>
            <div class="timeline-content">
                <h6 class="mb-1">{{estado}}</h6>
                <p class="mb-1">{{observaciones}}</p>
                <small class="text-muted">Registrado por: {{usuario}}</small>
            </div>
        </div>
    </template>

    <!-- Plantilla Documento -->
    <template id="templateDocumento">
        <div class="col-md-4 mb-3 documento">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">{{tipo}}</h6>
                    <p class="card-text text-muted small mb-2">{{fecha}}</p>
                    <div class="d-flex justify-content-between">
                        <a href="{{url}}" target="_blank" class="btn btn-sm btn-info btnVerDocumento">
                            <i class="fas fa-eye me-1"></i> Ver
                        </a>
                        <button type="button" class="btn btn-sm btn-danger btnEliminarDocumento">
                            <i class="fas fa-trash me-1"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal Cambiar Estado -->
    <!-- <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado de Envío</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="formCambiarEstado">
                    <div class="modal-body">
                        <input type="hidden" id="idEnvioEstado">
                        <div class="form-group">
                            <label>Nuevo Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="nuevoEstado">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="PREPARACION">Preparación</option>
                                <option value="EN_TRANSITO">En Tránsito</option>
                                <option value="EN_REPARTO">En Reparto</option>
                                <option value="ENTREGADO">Entregado</option>
                                <option value="CANCELADO">Cancelado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea class="form-control" id="observacionesEstado" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

    <!-- Modal Subir Documento -->
    <div class="modal fade" id="modalSubirDocumento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subir Documento</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="formSubirDocumento" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="idEnvioDocumento">
                        <div class="form-group">
                            <label>Tipo de Documento <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipo_documento">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="FOTO">Foto</option>
                                <option value="FACTURA">Factura</option>
                                <option value="GUIA">Guía de Remisión</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Documento <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="documento" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Subir Documento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado de Envío(s)</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="formCambiarEstado">
                    <div class="modal-body">
                        <input type="hidden" id="idEnviosEstado" name="id_envios">
                        <div class="form-group">
                            <label>Nuevo Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="nuevoEstado" name="nuevo_estado">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="PREPARACION">Preparación</option>
                                <option value="EN_TRANSITO">En Tránsito</option>
                                <option value="EN_REPARTO">En Reparto</option>
                                <option value="ENTREGADO">Entregado</option>
                                <option value="CANCELADO">Cancelado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea class="form-control" id="observacionesEstado" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
?>