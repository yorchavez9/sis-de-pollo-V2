<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de proveedores</h4>
                <h6>Administrar proveedores</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_proveedor">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar proveedor
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_proveedores">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Tipo Documento</th>
                                <th>N° Documento</th>
                                <th>Nombre/Razón Social</th>
                                <th>Teléfono</th>
                                <th>Celular</th>
                                <th>Email</th>
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

<!-- MODAL NUEVO PROVEEDOR -->
<div class="modal fade" id="modal_nuevo_proveedor" tabindex="-1" aria-labelledby="modal_nuevo_proveedor_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar proveedor</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_proveedor">
                <div class="modal-body">
                    <div class="row">
                        <!-- Tipo Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_documento_proveedor" class="form-label">Tipo Documento (<span class="text-danger">*</span>)</label>
                                <select name="id_tipo_documento" id="tipo_documento_proveedor" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_tipo_documento_proveedor"></small>
                            </div>
                        </div>
                        <!-- Número Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_documento_proveedor" class="form-label">N° Documento (<span class="text-danger">*</span>)</label>
                                <input type="text" name="numero_documento" id="numero_documento_proveedor" placeholder="Ingresa el número de documento">
                                <small id="error_numero_documento_proveedor"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Tipo de Proveedor -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_proveedor" class="form-label">Tipo de Proveedor (<span class="text-danger">*</span>)</label>
                                <select name="tipo_proveedor" id="tipo_proveedor" class="js-example-basic-single select2">
                                    <option value="NATURAL">Persona Natural</option>
                                    <option value="JURIDICO">Persona Jurídica</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos para Persona Natural -->
                    <div id="campos_natural">
                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_proveedor" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="nombre" id="nombre_proveedor" placeholder="Ingresa el nombre">
                                    <small id="error_nombre_proveedor"></small>
                                </div>
                            </div>
                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="apellidos_proveedor" class="form-label">Apellidos</label>
                                    <input type="text" name="apellidos" id="apellidos_proveedor" placeholder="Ingresa los apellidos">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos para Persona Jurídica -->
                    <div id="campos_juridico" style="display: none;">
                        <div class="row">
                            <!-- Razón Social -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="razon_social_proveedor" class="form-label">Razón Social (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="razon_social" id="razon_social_proveedor" placeholder="Ingresa la razón social">
                                    <small id="error_razon_social_proveedor"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono_proveedor" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="telefono_proveedor" placeholder="Ingresa el teléfono">
                            </div>
                        </div>
                        <!-- Celular -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="celular_proveedor" class="form-label">Celular (<span class="text-danger">*</span>)</label>
                                <input type="text" name="celular" id="celular_proveedor" placeholder="Ingresa el celular">
                                <small id="error_celular_proveedor"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_proveedor" class="form-label">Email</label>
                                <input type="email" name="email" id="email_proveedor" class="form-control" placeholder="Ingresa el email">
                                <small id="error_email_proveedor"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Dirección -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion_proveedor" class="form-label">Dirección</label>
                                <input type="text" name="direccion" id="direccion_proveedor" placeholder="Ingresa la dirección">
                            </div>
                        </div>
                        <!-- Ciudad -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ciudad_proveedor" class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" id="ciudad_proveedor" placeholder="Ingresa la ciudad">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado_proveedor" class="form-label">Estado</label>
                                <select name="estado" id="estado_proveedor" class="js-example-basic-single select2">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_proveedor" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR PROVEEDOR -->
<div class="modal fade" id="modal_editar_proveedor" tabindex="-1" aria-labelledby="modal_editar_proveedor_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar proveedor</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_proveedor">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_persona" id="edit_id_proveedor">
                    
                    <div class="row">
                        <!-- Tipo Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipo_documento_proveedor" class="form-label">Tipo Documento (<span class="text-danger">*</span>)</label>
                                <select name="id_tipo_documento" id="edit_tipo_documento_proveedor" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_tipo_documento_proveedor"></small>
                            </div>
                        </div>
                        <!-- Número Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_numero_documento_proveedor" class="form-label">N° Documento (<span class="text-danger">*</span>)</label>
                                <input type="text" name="numero_documento" id="edit_numero_documento_proveedor" placeholder="Ingresa el número de documento">
                                <small id="error_edit_numero_documento_proveedor"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Tipo de Proveedor -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipo_proveedor" class="form-label">Tipo de Proveedor (<span class="text-danger">*</span>)</label>
                                <select name="tipo_proveedor" id="edit_tipo_proveedor" class="js-example-basic-single select2">
                                    <option value="NATURAL">Persona Natural</option>
                                    <option value="JURIDICO">Persona Jurídica</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos para Persona Natural -->
                    <div id="edit_campos_natural">
                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_nombre_proveedor" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="nombre" id="edit_nombre_proveedor" placeholder="Ingresa el nombre">
                                    <small id="error_edit_nombre_proveedor"></small>
                                </div>
                            </div>
                            <!-- Apellidos -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_apellidos_proveedor" class="form-label">Apellidos</label>
                                    <input type="text" name="apellidos" id="edit_apellidos_proveedor" placeholder="Ingresa los apellidos">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos para Persona Jurídica -->
                    <div id="edit_campos_juridico" style="display: none;">
                        <div class="row">
                            <!-- Razón Social -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_razon_social_proveedor" class="form-label">Razón Social (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="razon_social" id="edit_razon_social_proveedor" placeholder="Ingresa la razón social">
                                    <small id="error_edit_razon_social_proveedor"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_telefono_proveedor" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="edit_telefono_proveedor" placeholder="Ingresa el teléfono">
                            </div>
                        </div>
                        <!-- Celular -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_celular_proveedor" class="form-label">Celular (<span class="text-danger">*</span>)</label>
                                <input type="text" name="celular" id="edit_celular_proveedor" placeholder="Ingresa el celular">
                                <small id="error_edit_celular_proveedor"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email_proveedor" class="form-label">Email</label>
                                <input type="email" name="email" id="edit_email_proveedor" class="form-control" placeholder="Ingresa el email">
                                <small id="error_edit_email_proveedor"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Dirección -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_direccion_proveedor" class="form-label">Dirección</label>
                                <input type="text" name="direccion" id="edit_direccion_proveedor" placeholder="Ingresa la dirección">
                            </div>
                        </div>
                        <!-- Ciudad -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ciudad_proveedor" class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" id="edit_ciudad_proveedor" placeholder="Ingresa la ciudad">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_estado_proveedor" class="form-label">Estado</label>
                                <select name="estado" id="edit_estado_proveedor" class="js-example-basic-single select2">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_proveedor" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>