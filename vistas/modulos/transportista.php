<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de transportistas</h4>
                <h6>Administrar transportistas</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_transportista">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar transportista
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_transportistas">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Tipo Documento</th>
                                <th>N° Documento</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Teléfono</th>
                                <th>Celular</th>
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

<!-- MODAL NUEVO TRANSPORTISTA -->
<div class="modal fade" id="modal_nuevo_transportista" tabindex="-1" aria-labelledby="modal_nuevo_transportista_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar transportista</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_transportista">
                <div class="modal-body">
                    <div class="row">
                        <!-- Tipo Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_documento_transportista" class="form-label">Tipo Documento (<span class="text-danger">*</span>)</label>
                                <select name="id_tipo_documento" id="tipo_documento_transportista" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_tipo_documento_transportista"></small>
                            </div>
                        </div>
                        <!-- Número Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_documento_transportista" class="form-label">N° Documento (<span class="text-danger">*</span>)</label>
                                <input type="text" name="numero_documento" id="numero_documento_transportista" placeholder="Ingresa el número de documento">
                                <small id="error_numero_documento_transportista"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_transportista" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                <input type="text" name="nombre" id="nombre_transportista" placeholder="Ingresa el nombre">
                                <small id="error_nombre_transportista"></small>
                            </div>
                        </div>
                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellidos_transportista" class="form-label">Apellidos</label>
                                <input type="text" name="apellidos" id="apellidos_transportista" placeholder="Ingresa los apellidos">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono_transportista" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="telefono_transportista" placeholder="Ingresa el teléfono">
                            </div>
                        </div>
                        <!-- Celular -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="celular_transportista" class="form-label">Celular (<span class="text-danger">*</span>)</label>
                                <input type="text" name="celular" id="celular_transportista" placeholder="Ingresa el celular">
                                <small id="error_celular_transportista"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_transportista" class="form-label">Email</label>
                                <input type="email" name="email" id="email_transportista" class="form-control" placeholder="Ingresa el email">
                                <small id="error_email_transportista"></small>
                            </div>
                        </div>
                        <!-- Dirección -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion_transportista" class="form-label">Dirección</label>
                                <input type="text" name="direccion" id="direccion_transportista" placeholder="Ingresa la dirección">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Ciudad -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ciudad_transportista" class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" id="ciudad_transportista" placeholder="Ingresa la ciudad">
                            </div>
                        </div>
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado_transportista" class="form-label">Estado</label>
                                <select name="estado" id="estado_transportista" class="form-select">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_transportista" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR TRANSPORTISTA -->
<div class="modal fade" id="modal_editar_transportista" tabindex="-1" aria-labelledby="modal_editar_transportista_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar transportista</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_transportista">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_persona" id="edit_id_transportista">
                    
                    <div class="row">
                        <!-- Tipo Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipo_documento_transportista" class="form-label">Tipo Documento (<span class="text-danger">*</span>)</label>
                                <select name="id_tipo_documento" id="edit_tipo_documento_transportista" class="form-select">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_tipo_documento_transportista"></small>
                            </div>
                        </div>
                        <!-- Número Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_numero_documento_transportista" class="form-label">N° Documento (<span class="text-danger">*</span>)</label>
                                <input type="text" name="numero_documento" id="edit_numero_documento_transportista" placeholder="Ingresa el número de documento">
                                <small id="error_edit_numero_documento_transportista"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nombre_transportista" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                <input type="text" name="nombre" id="edit_nombre_transportista" placeholder="Ingresa el nombre">
                                <small id="error_edit_nombre_transportista"></small>
                            </div>
                        </div>
                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_apellidos_transportista" class="form-label">Apellidos</label>
                                <input type="text" name="apellidos" id="edit_apellidos_transportista" placeholder="Ingresa los apellidos">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_telefono_transportista" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="edit_telefono_transportista" placeholder="Ingresa el teléfono">
                            </div>
                        </div>
                        <!-- Celular -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_celular_transportista" class="form-label">Celular (<span class="text-danger">*</span>)</label>
                                <input type="text" name="celular" id="edit_celular_transportista" placeholder="Ingresa el celular">
                                <small id="error_edit_celular_transportista"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email_transportista" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email_transportista" placeholder="Ingresa el email">
                                <small id="error_edit_email_transportista"></small>
                            </div>
                        </div>
                        <!-- Dirección -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_direccion_transportista" class="form-label">Dirección</label>
                                <input type="text" name="direccion" id="edit_direccion_transportista" placeholder="Ingresa la dirección">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Ciudad -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ciudad_transportista" class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" id="edit_ciudad_transportista" placeholder="Ingresa la ciudad">
                            </div>
                        </div>
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_estado_transportista" class="form-label">Estado</label>
                                <select name="estado" id="edit_estado_transportista" class="form-select">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_transportista" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>