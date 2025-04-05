<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Control de Inventario</h4>
                <h6>Gestión de stock por almacén</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_ajustar_inventario">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Ajustar Inventario
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Almacén:</label>
                        <select id="filtro_almacen" class="js-example-basic-single select2">
                            <option value="">Todos los almacenes</option>
                            <!-- Se llenará dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Producto:</label>
                        <select id="filtro_producto" class="js-example-basic-single select2">
                            <option value="">Todos los productos</option>
                            <!-- Se llenará dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Estado Stock:</label>
                        <select id="filtro_estado" class="js-example-basic-single select2">
                            <option value="">Todos</option>
                            <option value="bajo_minimo">Bajo stock mínimo</option>
                            <option value="sobre_maximo">Sobre stock máximo</option>
                            <option value="normal">Stock normal</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_inventario">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Almacén</th>
                                <th>Stock Actual</th>
                                <th>Mínimo</th>
                                <th>Máximo</th>
                                <th>Estado</th>
                                <th>Última Actualización</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se llenarán los datos dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL AJUSTAR INVENTARIO -->
<div class="modal fade" id="modal_ajustar_inventario" tabindex="-1" aria-labelledby="modal_ajustar_inventario_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajuste de Inventario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form id="form_ajustar_inventario">
                <div class="modal-body">
                    <input type="hidden" name="id_inventario" id="id_inventario">
                    <input type="hidden" name="action" value="ajustar">
                    
                    <div class="row">
                        <!-- Producto -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="producto_inventario" class="form-label">Producto (<span class="text-danger">*</span>)</label>
                                <select name="id_producto" id="producto_inventario" class="js-example-basic-single select2">
                                    <!-- Se llenará dinámicamente -->
                                </select>
                                <small id="error_producto_inventario"></small>
                            </div>
                        </div>
                        <!-- Almacén -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="almacen_inventario" class="form-label">Almacén (<span class="text-danger">*</span>)</label>
                                <select name="id_almacen" id="almacen_inventario" class="js-example-basic-single select2">
                                    <!-- Se llenará dinámicamente -->
                                </select>
                                <small id="error_almacen_inventario"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Tipo de Movimiento -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo_movimiento" class="form-label">Tipo de Movimiento (<span class="text-danger">*</span>)</label>
                                <select name="tipo_movimiento" id="tipo_movimiento" class="js-example-basic-single select2">
                                    <option value="entrada">Entrada</option>
                                    <option value="salida">Salida</option>
                                    <option value="ajuste">Ajuste</option>
                                </select>
                                <small id="error_tipo_movimiento"></small>
                            </div>
                        </div>
                        <!-- Cantidad -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cantidad_inventario" class="form-label">Cantidad (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.001" name="cantidad" id="cantidad_inventario" class="form-control" placeholder="0.000">
                                <small id="error_cantidad_inventario"></small>
                            </div>
                        </div>
                        <!-- Stock Actual (solo lectura) -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="stock_actual" class="form-label">Stock Actual</label>
                                <input type="text" id="stock_actual" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Stock Mínimo -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                <input type="number" step="0.001" name="stock_minimo" id="stock_minimo" class="form-control" placeholder="0.000">
                            </div>
                        </div>
                        <!-- Stock Máximo -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="stock_maximo" class="form-label">Stock Máximo</label>
                                <input type="number" step="0.001" name="stock_maximo" id="stock_maximo" class="form-control" placeholder="0.000">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Motivo -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="motivo_inventario" class="form-label">Motivo</label>
                                <textarea name="motivo" id="motivo_inventario" class="form-control" rows="2" placeholder="Descripción del movimiento"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_inventario" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL HISTORIAL DE MOVIMIENTOS -->
<div class="modal fade" id="modal_historial_inventario" tabindex="-1" aria-labelledby="modal_historial_inventario_Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historial de Movimientos</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_historial">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Stock Anterior</th>
                                <th>Stock Nuevo</th>
                                <th>Usuario</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>