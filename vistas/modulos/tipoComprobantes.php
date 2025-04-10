<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de tipos de comprobante</h4>
                <h6>Administrar tipos de comprobante</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_tipo_comprobante">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar tipo de comprobante
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_tipo_comprobantes">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Serie obligatoria</th>
                                <th>Número obligatorio</th>
                                <th>Afecta inventario</th>
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

<!-- MODAL NUEVO TIPO COMPROBANTE -->
<div class="modal fade" id="modal_nuevo_tipo_comprobante" tabindex="-1" aria-labelledby="modal_nuevo_tipo_comprobante_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear tipo de comprobante</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_tipo_comprobante">
                <div class="modal-body">
                    <!-- Código -->
                    <div class="form-group">
                        <label for="codigo_tipo_comprobante" class="form-label">Código (<span class="text-danger">*</span>)</label>
                        <input type="text" name="codigo" id="codigo_tipo_comprobante" placeholder="Ej: 01, 03, 07, etc.">
                        <small id="error_codigo_tipo_comprobante"></small>
                    </div>
                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="nombre_tipo_comprobante" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="nombre" id="nombre_tipo_comprobante" placeholder="Ej: Factura, Boleta, Nota de Crédito">
                        <small id="error_nombre_tipo_comprobante"></small>
                    </div>
                    <!-- Serie Obligatoria -->
                    <div class="form-group">
                        <label for="serie_obligatoria_tipo_comprobante" class="form-label">Serie obligatoria</label>
                        <select name="serie_obligatoria" id="serie_obligatoria_tipo_comprobante" class="form-select">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <!-- Número Obligatorio -->
                    <div class="form-group">
                        <label for="numero_obligatorio_tipo_comprobante" class="form-label">Número obligatorio</label>
                        <select name="numero_obligatorio" id="numero_obligatorio_tipo_comprobante" class="form-select">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <!-- Afecta Inventario -->
                    <div class="form-group">
                        <label for="afecta_inventario_tipo_comprobante" class="form-label">Afecta inventario</label>
                        <select name="afecta_inventario" id="afecta_inventario_tipo_comprobante" class="form-select">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="estado_tipo_comprobante" class="form-label">Estado</label>
                        <select name="estado" id="estado_tipo_comprobante" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_tipo_comprobante" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR TIPO COMPROBANTE -->
<div class="modal fade" id="modal_editar_tipo_comprobante" tabindex="-1" aria-labelledby="modal_editar_tipo_comprobante_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar tipo de comprobante</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_tipo_comprobante">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_tipo_comprobante" id="edit_id_tipo_comprobante">
                    <!-- Código -->
                    <div class="form-group">
                        <label for="edit_codigo_tipo_comprobante" class="form-label">Código (<span class="text-danger">*</span>)</label>
                        <input type="text" name="codigo" id="edit_codigo_tipo_comprobante" placeholder="Ej: 01, 03, 07, etc.">
                        <small id="error_edit_codigo_tipo_comprobante"></small>
                    </div>
                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="edit_nombre_tipo_comprobante" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="nombre" id="edit_nombre_tipo_comprobante" placeholder="Ej: Factura, Boleta, Nota de Crédito">
                        <small id="error_edit_nombre_tipo_comprobante"></small>
                    </div>
                    <!-- Serie Obligatoria -->
                    <div class="form-group">
                        <label for="edit_serie_obligatoria_tipo_comprobante" class="form-label">Serie obligatoria</label>
                        <select name="serie_obligatoria" id="edit_serie_obligatoria_tipo_comprobante" class="form-select">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <!-- Número Obligatorio -->
                    <div class="form-group">
                        <label for="edit_numero_obligatorio_tipo_comprobante" class="form-label">Número obligatorio</label>
                        <select name="numero_obligatorio" id="edit_numero_obligatorio_tipo_comprobante" class="form-select">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <!-- Afecta Inventario -->
                    <div class="form-group">
                        <label for="edit_afecta_inventario_tipo_comprobante" class="form-label">Afecta inventario</label>
                        <select name="afecta_inventario" id="edit_afecta_inventario_tipo_comprobante" class="form-select">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="edit_estado_tipo_comprobante" class="form-label">Estado</label>
                        <select name="estado" id="edit_estado_tipo_comprobante" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_tipo_comprobante" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>