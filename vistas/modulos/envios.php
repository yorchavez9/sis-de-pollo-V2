<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Gestión de Envíos <i class="fas fa-truck mr-1"></i></h4>
                <h6>Administración de envíos entre sucursales</h6>
            </div>
            <div class="page-btn">
                <a href="javascript:void(0);" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modalNuevoEnvio">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Nuevo Envío
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="formFiltroEnvios">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sucursal Origen</label>
                                <select class="select2" id="filtroOrigen">
                                    <option value="">Todas</option>
                                    <!-- Dinámico desde JS -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sucursal Destino</label>
                                <select class="select2" id="filtroDestino">
                                    <option value="">Todas</option>
                                    <!-- Dinámico desde JS -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tipo</label>
                                <select class="select2" id="filtroTipo">
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
                                <select class="select2" id="filtroEstado">
                                    <option value="">Todos</option>
                                    <option value="PENDIENTE">Pendiente</option>
                                    <option value="PREPARACION">Preparación</option>
                                    <option value="EN_TRANSITO">En Tránsito</option>
                                    <option value="ENTREGADO">Entregado</option>
                                    <option value="CANCELADO">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Filtrar
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tablaEnvios">
                        <thead>
                            <tr>
                                <th width="5%">N°</th>
                                <th width="10%">Código</th>
                                <th width="15%">Origen</th>
                                <th width="15%">Destino</th>
                                <th width="10%">Tipo</th>
                                <th width="10%">Fecha Envío</th>
                                <th width="10%">Estado</th>
                                <th width="10%">Transportista</th>
                                <th width="15%" class="text-center">Acciones</th>
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


<!-- Modal Nuevo Envío - Versión mejorada -->
<div class="modal fade" id="modalNuevoEnvio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Envío <i class="fas fa-truck mr-1"></i></h5>
                <button type="button" class="close " data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="formNuevoEnvio" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna 1: Información básica del envío -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Información Básica</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Código de Envío</label>
                                        <input type="text" class="form-control" name="codigo_envio" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold">Sucursal Origen <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="id_sucursal_origen" required>
                                            <!-- Dinámico desde JS -->
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold">Sucursal Destino <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="id_sucursal_destino" required>
                                            <!-- Dinámico desde JS -->
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tipo de Envío <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="id_tipo_encomienda" required>
                                            <option value="1">Productos Perecederos</option>
                                            <option value="2">Productos Secos</option>
                                            <option value="3">Documentos</option>
                                            <option value="4">Insumos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna 2: Detalles del envío -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Detalles del Envío</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Transportista</label>
                                        <select class="form-control select2" name="id_transportista">
                                            <!-- Dinámico desde JS -->
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha Estimada Entrega</label>
                                        <input type="datetime-local" class="form-control" name="fecha_estimada_entrega">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold">Método de Pago</label>
                                        <select class="form-control select2" name="metodo_pago">
                                            <option value="EFECTIVO">Efectivo</option>
                                            <option value="CREDITO">Crédito</option>
                                            <option value="POR_COBRAR">Por Cobrar</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold">Instrucciones Especiales</label>
                                        <textarea class="form-control" name="instrucciones" rows="3" placeholder="Ej: Mantener refrigerado a 4°C"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna 3: Resumen y acciones -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Resumen</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Complete todos los campos obligatorios y agregue al menos un paquete.
                                    </div>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Total Paquetes:</span>
                                        <span class="summary-value" id="totalPaquetesResumen">0</span>
                                    </div>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Peso Total:</span>
                                        <span class="summary-value" id="pesoTotalResumen">0.00 kg</span>
                                    </div>
                                    
                                    <div class="summary-item">
                                        <span class="summary-label">Costo Estimado:</span>
                                        <span class="summary-value" id="costoEnvioResumen">$0.00</span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <button type="button" class="btn btn-primary btn-block mb-2" id="btnCalcularCosto">
                                        <i class="fas fa-calculator mr-2"></i> Calcular Costo
                                    </button>
                                    
                                    <button type="button" class="btn btn-outline-secondary btn-block" data-bs-dismiss="modal">
                                        <i class="fas fa-times mr-2"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección de Paquetes - Ahora más destacada -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-boxes mr-2"></i> Paquetes
                                        <small class="text-muted ml-2">(Agregue al menos un paquete)</small>
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="btnAgregarPaquete">
                                        <i class="fas fa-plus mr-1"></i> Agregar Paquete
                                    </button>
                                </div>
                                <div class="card-body" id="contenedorPaquetes">
                                    <!-- Paquetes se agregarán aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i> Guardar Envío
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalle Envío -->
<div class="modal fade" id="modalDetalleEnvio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                        <!-- Información Básica -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6>Información del Envío</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Código:</th>
                                        <td id="detalleCodigo"></td>
                                    </tr>
                                    <tr>
                                        <th>Origen:</th>
                                        <td id="detalleOrigen"></td>
                                    </tr>
                                    <tr>
                                        <th>Destino:</th>
                                        <td id="detalleDestino"></td>
                                    </tr>
                                    <tr>
                                        <th>Tipo:</th>
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
                                </table>
                            </div>
                        </div>
                        
                        <!-- Seguimiento -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Seguimiento</h6>
                                <button class="btn btn-sm btn-primary" id="btnNuevoSeguimiento">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            </div>
                            <div class="card-body">
                                <ul class="timeline" id="timelineSeguimiento">
                                    <!-- Historial de estados -->
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Paquetes -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Paquetes</h6>
                                <span class="badge bg-primary" id="totalPaquetes"></span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tablaPaquetes">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Descripción</th>
                                                <th>Peso (kg)</th>
                                                <th>Volumen (m³)</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Lista de paquetes -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Documentos -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Documentos Adjuntos</h6>
                                <button class="btn btn-sm btn-primary" id="btnSubirDocumento">
                                    <i class="fas fa-upload me-1"></i> Subir
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="listaDocumentos" class="d-flex flex-wrap gap-2">
                                    <!-- Documentos se mostrarán aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnImprimirGuia">
                    <i class="fas fa-print me-1"></i> Imprimir Guía
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Estado</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="formCambiarEstado">
                <input type="hidden" id="idEnvioEstado">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nuevo Estado <span class="text-danger">*</span></label>
                        <select class="form-control" id="nuevoEstado" required>
                            <option value="PREPARACION">En Preparación</option>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Subir Documento -->
<div class="modal fade" id="modalSubirDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Documento</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="formSubirDocumento" enctype="multipart/form-data">
                <input type="hidden" id="idEnvioDocumento">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipo de Documento <span class="text-danger">*</span></label>
                        <select class="form-control" name="tipo_documento" required>
                            <option value="FOTO">Foto</option>
                            <option value="FACTURA">Factura</option>
                            <option value="GUIA">Guía de Remisión</option>
                            <option value="OTRO">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Archivo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="archivo" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Subir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Plantillas para JS -->
<template id="templatePaquete">
    <div class="card paquete mb-3">
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
                        <input type="text" class="form-control descripcion" required>
                    </div>
                    <div class="form-group">
                        <label>Peso (kg) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control peso" step="0.01" min="0.01" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Alto (cm)</label>
                        <input type="number" class="form-control alto" step="0.1" min="0">
                    </div>
                    <div class="form-group">
                        <label>Ancho (cm)</label>
                        <input type="number" class="form-control ancho" step="0.1" min="0">
                    </div>
                    <div class="form-group">
                        <label>Profundidad (cm)</label>
                        <input type="number" class="form-control profundidad" step="0.1" min="0">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Instrucciones de Manejo</label>
                <textarea class="form-control instrucciones" rows="2"></textarea>
            </div>
            
            <div class="card mt-2">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Ítems del Paquete</h6>
                    <button type="button" class="btn btn-sm btn-primary btnAgregarItem">
                        <i class="fas fa-plus me-1"></i> Agregar
                    </button>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-items">
                        <thead>
                            <tr>
                                <th width="50%">Producto</th>
                                <th width="20%">Cantidad</th>
                                <th width="20%">Peso (kg)</th>
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
    </div>
</template>

<template id="templateItemPaquete">
    <tr class="item">
        <td>
            <select class="form-control form-control-sm selectProducto" required>
                <!-- Productos dinámicos -->
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm cantidad" min="1" value="1" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm pesoUnitario" step="0.01" min="0.01">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btnEliminarItem">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<template id="templateSeguimiento">
    <li class="event">
        <div class="d-flex justify-content-between">
            <h6 class="mb-1 estado"></h6>
            <small class="fecha"></small>
        </div>
        <p class="observaciones mb-1"></p>
        <small class="usuario"></small>
    </li>
</template>

<template id="templateDocumento">
    <div class="documento card p-2" style="width: 150px;">
        <div class="text-center">
            <i class="fas fa-file-alt fa-3x mb-2"></i>
            <h6 class="tipo-documento mb-1"></h6>
            <small class="fecha d-block mb-2"></small>
            <div class="btn-group btn-group-sm w-100">
                <a href="#" class="btn btn-info btnVerDocumento" target="_blank">
                    <i class="fas fa-eye"></i>
                </a>
                <button type="button" class="btn btn-danger btnEliminarDocumento">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>