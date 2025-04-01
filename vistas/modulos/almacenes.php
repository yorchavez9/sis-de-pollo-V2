<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de almacenes</h4>
                <h6>Administrar almacenes</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_almacen"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar almacén</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_almacenes">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Sucursal</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
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

<!-- MODAL NUEVO ALMACÉN -->
<div class="modal fade" id="modal_nuevo_almacen" tabindex="-1" aria-labelledby="modal_nuevo_almacen_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear almacén</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_almacen">
                <div class="modal-body">
                    <!-- Sucursal -->
                    <div class="form-group">
                        <label for="sucursal_almacen" class="form-label">Sucursal (<span class="text-danger">*</span>)</label>
                        <select name="id_sucursal" id="sucursal_almacen" class="js-example-basic-single select2">
                            <!-- Opciones dinámicas -->
                        </select>
                        <small id="error_sucursal_almacen"></small>
                    </div>
                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="nombre_almacen" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="nombre" id="nombre_almacen" placeholder="Ingresa el nombre">
                        <small id="error_nombre_almacen"></small>
                    </div>
                    <!-- Descripción -->
                    <div class="form-group">
                        <label for="descripcion_almacen" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion_almacen" placeholder="Ingresa la descripción"></textarea>
                        <small id="error_descripcion_almacen"></small>
                    </div>
                    <!-- Tipo -->
                    <div class="form-group">
                        <label for="tipo_almacen" class="form-label">Tipo (<span class="text-danger">*</span>)</label>
                        <select name="tipo" id="tipo_almacen" class="js-example-basic-single select2">
                            <option disabled selected>Selecionar</option>
                            <option value="PRINCIPAL">PRINCIPAL</option>
                            <option value="SECUNDARIO">SECUNDARIO</option>
                            <option value="REFRI">REFRI</option>
                            <option value="CONGELACION">CONGELACIÓN</option>
                        </select>
                        <small id="error_tipo_almacen"></small>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="estado_almacen" class="form-label">Estado</label>
                        <select name="estado" id="estado_almacen" class="js-example-basic-single select2">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_almacen" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR ALMACÉN -->
<div class="modal fade" id="modal_editar_almacen" tabindex="-1" aria-labelledby="modal_editar_almacen_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar almacén</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_almacen">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_almacen" id="edit_id_almacen">
                    <!-- Sucursal -->
                    <div class="form-group">
                        <label for="edit_sucursal_almacen" class="form-label">Sucursal (<span class="text-danger">*</span>)</label>
                        <select name="id_sucursal" id="edit_sucursal_almacen" class="form-select">
                            <!-- Opciones dinámicas -->
                        </select>
                        <small id="error_edit_sucursal_almacen"></small>
                    </div>
                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="edit_nombre_almacen" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="nombre" id="edit_nombre_almacen" placeholder="Ingresa el nombre">
                        <small id="error_edit_nombre_almacen"></small>
                    </div>
                    <!-- Descripción -->
                    <div class="form-group">
                        <label for="edit_descripcion_almacen" class="form-label">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion_almacen" placeholder="Ingresa la descripción"></textarea>
                        <small id="error_edit_descripcion_almacen"></small>
                    </div>
                    <!-- Tipo -->
                    <div class="form-group">
                        <label for="edit_tipo_almacen" class="form-label">Tipo (<span class="text-danger">*</span>)</label>
                        <select name="tipo" id="edit_tipo_almacen" class="js-example-basic-single select2">
                            <option value="PRINCIPAL">PRINCIPAL</option>
                            <option value="SECUNDARIO">SECUNDARIO</option>
                            <option value="REFRI">REFRI</option>
                            <option value="CONGELACION">CONGELACIÓN</option>
                        </select>
                        <small id="error_edit_tipo_almacen"></small>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="edit_estado_almacen" class="form-label">Estado</label>
                        <select name="estado" id="edit_estado_almacen" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_almacen" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>


