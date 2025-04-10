<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de series y folios de comprobantes</h4>
                <h6>Administrar series para comprobantes</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nueva_serie">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar serie
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_series_comprobantes">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Sucursal</th>
                                <th>Tipo Comprobante</th>
                                <th>Serie</th>
                                <th>N° Inicial</th>
                                <th>N° Actual</th>
                                <th>N° Final</th>
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

<!-- MODAL NUEVA SERIE -->
<div class="modal fade" id="modal_nueva_serie" tabindex="-1" aria-labelledby="modal_nueva_serie_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear serie de comprobante</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nueva_serie">
                <div class="modal-body">
                    <!-- Sucursal -->
                    <div class="form-group">
                        <label for="sucursal_serie" class="form-label">Sucursal (<span class="text-danger">*</span>)</label>
                        <select name="id_sucursal" id="sucursal_serie" class="js-example-basic-single select2">
                            <!-- Opciones dinámicas -->
                        </select>
                        <small id="error_sucursal_serie"></small>
                    </div>
                    <!-- Tipo Comprobante -->
                    <div class="form-group">
                        <label for="tipo_comprobante_serie" class="form-label">Tipo Comprobante (<span class="text-danger">*</span>)</label>
                        <select name="id_tipo_comprobante" id="tipo_comprobante_serie" class="js-example-basic-single select2">
                            <!-- Opciones dinámicas -->
                        </select>
                        <small id="error_tipo_comprobante_serie"></small>
                    </div>
                    <!-- Serie -->
                    <div class="form-group">
                        <label for="serie_comprobante" class="form-label">Serie (<span class="text-danger">*</span>)</label>
                        <input type="text" name="serie" id="serie_comprobante" placeholder="Ej: F001, B001, etc." maxlength="10">
                        <small id="error_serie_comprobante"></small>
                    </div>
                    <!-- Número Inicial -->
                    <div class="form-group">
                        <label for="numero_inicial_serie" class="form-label">Número Inicial (<span class="text-danger">*</span>)</label>
                        <input type="number" name="numero_inicial" id="numero_inicial_serie" class="form-control" placeholder="Ej: 1, 100, etc." min="1">
                        <small id="error_numero_inicial_serie"></small>
                    </div>
                    <!-- Número Actual -->
                    <div class="form-group">
                        <label for="numero_actual_serie" class="form-label">Número Actual (<span class="text-danger">*</span>)</label>
                        <input type="number" name="numero_actual" id="numero_actual_serie" class="form-control" placeholder="Ej: 1, 100, etc." min="1">
                        <small id="error_numero_actual_serie"></small>
                    </div>
                    <!-- Número Final -->
                    <div class="form-group">
                        <label for="numero_final_serie" class="form-label">Número Final</label>
                        <input type="number" name="numero_final" id="numero_final_serie" class="form-control" placeholder="Opcional" min="1">
                        <small id="error_numero_final_serie"></small>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="estado_serie" class="form-label">Estado</label>
                        <select name="estado" id="estado_serie" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_serie" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR SERIE -->
<div class="modal fade" id="modal_editar_serie" tabindex="-1" aria-labelledby="modal_editar_serie_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar serie de comprobante</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_serie">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_serie" id="edit_id_serie">
                    <!-- Sucursal -->
                    <div class="form-group">
                        <label for="edit_sucursal_serie" class="form-label">Sucursal (<span class="text-danger">*</span>)</label>
                        <select name="id_sucursal" id="edit_sucursal_serie" class="form-select">
                            <!-- Opciones dinámicas -->
                        </select>
                        <small id="error_edit_sucursal_serie"></small>
                    </div>
                    <!-- Tipo Comprobante -->
                    <div class="form-group">
                        <label for="edit_tipo_comprobante_serie" class="form-label">Tipo Comprobante (<span class="text-danger">*</span>)</label>
                        <select name="id_tipo_comprobante" id="edit_tipo_comprobante_serie" class="form-select">
                            <!-- Opciones dinámicas -->
                        </select>
                        <small id="error_edit_tipo_comprobante_serie"></small>
                    </div>
                    <!-- Serie -->
                    <div class="form-group">
                        <label for="edit_serie_comprobante" class="form-label">Serie (<span class="text-danger">*</span>)</label>
                        <input type="text" name="serie" id="edit_serie_comprobante" placeholder="Ej: F001, B001, etc." maxlength="10">
                        <small id="error_edit_serie_comprobante"></small>
                    </div>
                    <!-- Número Inicial -->
                    <div class="form-group">
                        <label for="edit_numero_inicial_serie" class="form-label">Número Inicial (<span class="text-danger">*</span>)</label>
                        <input type="number" name="numero_inicial" class="form-control" id="edit_numero_inicial_serie" placeholder="Ej: 1, 100, etc." min="1">
                        <small id="error_edit_numero_inicial_serie"></small>
                    </div>
                    <!-- Número Actual -->
                    <div class="form-group">
                        <label for="edit_numero_actual_serie" class="form-label">Número Actual (<span class="text-danger">*</span>)</label>
                        <input type="number" name="numero_actual" class="form-control" id="edit_numero_actual_serie" placeholder="Ej: 1, 100, etc." min="1">
                        <small id="error_edit_numero_actual_serie"></small>
                    </div>
                    <!-- Número Final -->
                    <div class="form-group">
                        <label for="edit_numero_final_serie" class="form-label">Número Final</label>
                        <input type="number" name="numero_final" id="edit_numero_final_serie" class="form-control" placeholder="Opcional" min="1">
                        <small id="error_edit_numero_final_serie"></small>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="edit_estado_serie" class="form-label">Estado</label>
                        <select name="estado" id="edit_estado_serie" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_serie" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>