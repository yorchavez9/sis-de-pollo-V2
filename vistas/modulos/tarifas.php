<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de tarifas de envío</h4>
                <h6>Administrar tarifas</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nueva_tarifa">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar tarifa
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_tarifas">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Sucursal Origen</th>
                                <th>Sucursal Destino</th>
                                <th>Tipo Encomienda</th>
                                <th>Rango Peso (kg)</th>
                                <th>Costo Base</th>
                                <th>Costo Kg Extra</th>
                                <th>Tiempo Estimado</th>
                                <th>Vigencia</th>
                                <th>Estado</th>
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

<!-- MODAL NUEVA TARIFA -->
<div class="modal fade" id="modal_nueva_tarifa" tabindex="-1" aria-labelledby="modal_nueva_tarifa_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear tarifa de envío</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nueva_tarifa">
                <div class="modal-body">
                    <div class="row">
                        <!-- Sucursal Origen -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sucursal_origen" class="form-label">Sucursal Origen (<span class="text-danger">*</span>)</label>
                                <select name="id_sucursal_origen" id="sucursal_origen" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_sucursal_origen"></small>
                            </div>
                        </div>
                        <!-- Sucursal Destino -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sucursal_destino" class="form-label">Sucursal Destino (<span class="text-danger">*</span>)</label>
                                <select name="id_sucursal_destino" id="sucursal_destino" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_sucursal_destino"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Tipo Encomienda -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_encomienda" class="form-label">Tipo Encomienda (<span class="text-danger">*</span>)</label>
                                <select name="id_tipo_encomienda" id="tipo_encomienda" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_tipo_encomienda"></small>
                            </div>
                        </div>
                        <!-- Tiempo Estimado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tiempo_estimado" class="form-label">Tiempo Estimado (horas)</label>
                                <input type="number" name="tiempo_estimado" id="tiempo_estimado" class="form-control" placeholder="Ingresa el tiempo estimado">
                                <small id="error_tiempo_estimado"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Rango Peso Mínimo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rango_peso_min" class="form-label">Peso Mínimo (kg) (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="rango_peso_min" id="rango_peso_min" class="form-control" placeholder="Ingresa el peso mínimo">
                                <small id="error_rango_peso_min"></small>
                            </div>
                        </div>
                        <!-- Rango Peso Máximo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rango_peso_max" class="form-label">Peso Máximo (kg) (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="rango_peso_max" id="rango_peso_max" class="form-control" placeholder="Ingresa el peso máximo">
                                <small id="error_rango_peso_max"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Costo Base -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="costo_base" class="form-label">Costo Base (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="costo_base" id="costo_base" class="form-control" placeholder="Ingresa el costo base">
                                <small id="error_costo_base"></small>
                            </div>
                        </div>
                        <!-- Costo Kg Extra -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="costo_kg_extra" class="form-label">Costo por Kg Extra</label>
                                <input type="number" step="0.01" name="costo_kg_extra" id="costo_kg_extra" class="form-control" placeholder="Ingresa el costo por kg extra" value="0.00">
                                <small id="error_costo_kg_extra"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Vigencia Desde -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vigencia_desde" class="form-label">Vigencia Desde (<span class="text-danger">*</span>)</label>
                                <input type="date" name="vigencia_desde" class="form-control" id="vigencia_desde">
                                <small id="error_vigencia_desde"></small>
                            </div>
                        </div>
                        <!-- Vigencia Hasta -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vigencia_hasta" class="form-label">Vigencia Hasta</label>
                                <input type="date" name="vigencia_hasta" class="form-control" id="vigencia_hasta">
                                <small id="error_vigencia_hasta"></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="estado_tarifa" class="form-label">Estado</label>
                        <select name="estado" id="estado_tarifa" class="js-example-basic-single select2">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_tarifa" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR TARIFA -->
<div class="modal fade" id="modal_editar_tarifa" tabindex="-1" aria-labelledby="modal_editar_tarifa_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar tarifa de envío</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_tarifa">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_tarifa" id="edit_id_tarifa">
                    
                    <div class="row">
                        <!-- Sucursal Origen -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_sucursal_origen" class="form-label">Sucursal Origen (<span class="text-danger">*</span>)</label>
                                <select name="id_sucursal_origen" id="edit_sucursal_origen" class="form-select">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_sucursal_origen"></small>
                            </div>
                        </div>
                        <!-- Sucursal Destino -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_sucursal_destino" class="form-label">Sucursal Destino (<span class="text-danger">*</span>)</label>
                                <select name="id_sucursal_destino" id="edit_sucursal_destino" class="form-select">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_sucursal_destino"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Tipo Encomienda -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipo_encomienda" class="form-label">Tipo Encomienda (<span class="text-danger">*</span>)</label>
                                <select name="id_tipo_encomienda" id="edit_tipo_encomienda" class="form-select">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_tipo_encomienda"></small>
                            </div>
                        </div>
                        <!-- Tiempo Estimado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tiempo_estimado" class="form-label">Tiempo Estimado (horas)</label>
                                <input type="number" name="tiempo_estimado" id="edit_tiempo_estimado" class="form-control" placeholder="Ingresa el tiempo estimado">
                                <small id="error_edit_tiempo_estimado"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Rango Peso Mínimo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_rango_peso_min" class="form-label">Peso Mínimo (kg) (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="rango_peso_min" id="edit_rango_peso_min" class="form-control" placeholder="Ingresa el peso mínimo">
                                <small id="error_edit_rango_peso_min"></small>
                            </div>
                        </div>
                        <!-- Rango Peso Máximo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_rango_peso_max" class="form-label">Peso Máximo (kg) (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="rango_peso_max" id="edit_rango_peso_max" class="form-control" placeholder="Ingresa el peso máximo">
                                <small id="error_edit_rango_peso_max"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Costo Base -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_costo_base" class="form-label">Costo Base (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="costo_base" id="edit_costo_base" class="form-control" placeholder="Ingresa el costo base">
                                <small id="error_edit_costo_base"></small>
                            </div>
                        </div>
                        <!-- Costo Kg Extra -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_costo_kg_extra" class="form-label">Costo por Kg Extra</label>
                                <input type="number" step="0.01" name="costo_kg_extra" id="edit_costo_kg_extra" class="form-control" placeholder="Ingresa el costo por kg extra">
                                <small id="error_edit_costo_kg_extra"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Vigencia Desde -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_vigencia_desde" class="form-label">Vigencia Desde (<span class="text-danger">*</span>)</label>
                                <input type="date" name="vigencia_desde" id="edit_vigencia_desde" class="form-control">
                                <small id="error_edit_vigencia_desde"></small>
                            </div>
                        </div>
                        <!-- Vigencia Hasta -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_vigencia_hasta" class="form-label">Vigencia Hasta</label>
                                <input type="date" name="vigencia_hasta" id="edit_vigencia_hasta" class="form-control">
                                <small id="error_edit_vigencia_hasta"></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="edit_estado_tarifa" class="form-label">Estado</label>
                        <select name="estado" id="edit_estado_tarifa" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_tarifa" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>