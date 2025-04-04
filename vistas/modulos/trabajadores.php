<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de trabajadores</h4>
                <h6>Administrar trabajadores</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_trabajador">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar trabajador
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_trabajadores">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Tipo Documento</th>
                                <th>N° Documento</th>
                                <th>Nombre Completo</th>
                                <th>Cargo</th>
                                <th>Teléfono</th>
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

<!-- MODAL NUEVO TRABAJADOR -->
<div class="modal fade" id="modal_nuevo_trabajador" tabindex="-1" aria-labelledby="modal_nuevo_trabajador_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar trabajador</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_trabajador">
                <div class="modal-body">
                    <div class="row">
                        <!-- Tipo Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_documento_trabajador" class="form-label">Tipo Documento (<span class="text-danger">*</span>)</label>
                                <select name="id_tipo_documento" id="tipo_documento_trabajador" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_tipo_documento_trabajador"></small>
                            </div>
                        </div>
                        <!-- Número Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_documento_trabajador" class="form-label">N° Documento (<span class="text-danger">*</span>)</label>
                                <input type="text" name="numero_documento" id="numero_documento_trabajador" placeholder="Ingresa el número de documento">
                                <small id="error_numero_documento_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_trabajador" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                <input type="text" name="nombre" id="nombre_trabajador" placeholder="Ingresa el nombre">
                                <small id="error_nombre_trabajador"></small>
                            </div>
                        </div>
                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellidos_trabajador" class="form-label">Apellidos (<span class="text-danger">*</span>)</label>
                                <input type="text" name="apellidos" id="apellidos_trabajador" placeholder="Ingresa los apellidos">
                                <small id="error_apellidos_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Fecha Nacimiento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_nacimiento_trabajador" class="form-label">Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento_trabajador" class="form-control">
                            </div>
                        </div>
                        <!-- Cargo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo_trabajador" class="form-label">Cargo (<span class="text-danger">*</span>)</label>
                                <select name="cargo" id="cargo_trabajador" class="js-example-basic-single select2">
                                    <option value="" disabled selected>Seleccionar cargo</option>
                                    <option value="ADMINISTRADOR">Administrador</option>
                                    <option value="VENDEDOR">Vendedor</option>
                                    <option value="ALMACENERO">Almacenero</option>
                                    <option value="CONTADOR">Contador</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                                <small id="error_cargo_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono_trabajador" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="telefono_trabajador" placeholder="Ingresa el teléfono">
                            </div>
                        </div>
                        <!-- Celular -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="celular_trabajador" class="form-label">Celular (<span class="text-danger">*</span>)</label>
                                <input type="text" name="celular" id="celular_trabajador" placeholder="Ingresa el celular">
                                <small id="error_celular_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_trabajador" class="form-label">Email (<span class="text-danger">*</span>)</label>
                                <input type="email" name="email" id="email_trabajador" class="form-control" placeholder="Ingresa el email">
                                <small id="error_email_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Dirección -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion_trabajador" class="form-label">Dirección</label>
                                <input type="text" name="direccion" id="direccion_trabajador" placeholder="Ingresa la dirección">
                            </div>
                        </div>
                        <!-- Ciudad -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ciudad_trabajador" class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" id="ciudad_trabajador" placeholder="Ingresa la ciudad">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado_trabajador" class="form-label">Estado</label>
                                <select name="estado" id="estado_trabajador" class="js-example-basic-single select2">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_trabajador" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR TRABAJADOR -->
<div class="modal fade" id="modal_editar_trabajador" tabindex="-1" aria-labelledby="modal_editar_trabajador_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar trabajador</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_trabajador">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_persona" id="edit_id_trabajador">
                    <!-- Campo oculto para cargo -->
                    <input type="hidden" name="cargo" id="edit_cargo_trabajador">
                    
                    <div class="row">
                        <!-- Tipo Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipo_documento_trabajador" class="form-label">Tipo Documento (<span class="text-danger">*</span>)</label>
                                <select name="id_tipo_documento" id="edit_tipo_documento_trabajador" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_tipo_documento_trabajador"></small>
                            </div>
                        </div>
                        <!-- Número Documento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_numero_documento_trabajador" class="form-label">N° Documento (<span class="text-danger">*</span>)</label>
                                <input type="text" name="numero_documento" id="edit_numero_documento_trabajador" placeholder="Ingresa el número de documento">
                                <small id="error_edit_numero_documento_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nombre_trabajador" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                <input type="text" name="nombre" id="edit_nombre_trabajador" placeholder="Ingresa el nombre">
                                <small id="error_edit_nombre_trabajador"></small>
                            </div>
                        </div>
                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_apellidos_trabajador" class="form-label">Apellidos (<span class="text-danger">*</span>)</label>
                                <input type="text" name="apellidos" id="edit_apellidos_trabajador" placeholder="Ingresa los apellidos">
                                <small id="error_edit_apellidos_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Fecha Nacimiento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_fecha_nacimiento_trabajador" class="form-label">Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="edit_fecha_nacimiento_trabajador" class="form-control">
                            </div>
                        </div>
                        <!-- Cargo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_cargo_select_trabajador" class="form-label">Cargo (<span class="text-danger">*</span>)</label>
                                <select id="edit_cargo_select_trabajador" class="js-example-basic-single select2">
                                    <option value="" disabled selected>Seleccionar cargo</option>
                                    <option value="ADMINISTRADOR">Administrador</option>
                                    <option value="VENDEDOR">Vendedor</option>
                                    <option value="ALMACENERO">Almacenero</option>
                                    <option value="CONTADOR">Contador</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                                <small id="error_edit_cargo_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_telefono_trabajador" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="edit_telefono_trabajador" placeholder="Ingresa el teléfono">
                            </div>
                        </div>
                        <!-- Celular -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_celular_trabajador" class="form-label">Celular (<span class="text-danger">*</span>)</label>
                                <input type="text" name="celular" id="edit_celular_trabajador" placeholder="Ingresa el celular">
                                <small id="error_edit_celular_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email_trabajador" class="form-label">Email (<span class="text-danger">*</span>)</label>
                                <input type="email" name="email" id="edit_email_trabajador" class="form-control" placeholder="Ingresa el email">
                                <small id="error_edit_email_trabajador"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Dirección -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_direccion_trabajador" class="form-label">Dirección</label>
                                <input type="text" name="direccion" id="edit_direccion_trabajador" placeholder="Ingresa la dirección">
                            </div>
                        </div>
                        <!-- Ciudad -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ciudad_trabajador" class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" id="edit_ciudad_trabajador" placeholder="Ingresa la ciudad">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_estado_trabajador" class="form-label">Estado</label>
                                <select name="estado" id="edit_estado_trabajador" class="js-example-basic-single select2">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_trabajador" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>