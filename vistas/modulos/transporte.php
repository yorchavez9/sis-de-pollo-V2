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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_transporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Encargado</th>
                                <th>Tipo Vehículo</th>
                                <th>Placa Vehículo</th>
                                <th>Teléfono Contacto</th>
                                <th>Fecha Registro</th>
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
<div class="modal fade" id="modal_nuevo_transportista" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar transportista</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="form_nuevo_transportista" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <!-- Datos de Persona -->
                        <div class="form-group col-md-6">
                            <label for="id_persona">Seleccione el transportista (<span class="text-danger">*</span>)</label>
                            <select name="id_persona" id="id_persona" class="js-example-basic-single select2">
                                <!-- mostrar datos desde javascript del módulo de personas -->
                            </select>
                            <small id="error_id_persona" class="text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tipo_vehiculo">Tipo de Vehículo</label>
                            <select name="tipo_vehiculo" id="tipo_vehiculo" class="js-example-basic-single select2">
                                <option value="">Seleccionar</option>
                                <option value="MOTO">Moto</option>
                                <option value="AUTO">Auto</option>
                                <option value="CAMIONETA">Camioneta</option>
                                <option value="CAMION">Camión</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="placa_vehiculo">Placa del Vehículo</label>
                            <input type="text" name="placa_vehiculo" id="placa_vehiculo" placeholder="Ej: ABC-123">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="telefono_transportista">Teléfono (<span class="text-danger">*</span>)</label>
                            <input type="text" name="telefono_contacto" id="telefono_transportista" placeholder="Ingrese teléfono">
                            <small id="error_telefono_transportista" class="text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="estado_transportista">Estado</label>
                            <select name="estado" id="estado_transportista" class="js-example-basic-single select2">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
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
<div class="modal fade" id="modal_editar_transportista" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar transportista</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="form_editar_transportista" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_transportista" id="edit_id_transportista">
                    <div class="row">
                        <!-- Datos de Persona -->
                        <div class="form-group col-md-6">
                            <label for="edit_id_persona">Transportista (<span class="text-danger">*</span>)</label>
                            <select name="id_persona" id="edit_id_persona" class="js-example-basic-single select2" disabled>
                                <!-- mostrar datos desde javascript del módulo de personas -->
                            </select>
                            <small id="error_edit_id_persona" class="text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="edit_tipo_vehiculo">Tipo de Vehículo</label>
                            <select name="tipo_vehiculo" id="edit_tipo_vehiculo" class="js-example-basic-single select2">
                                <option value="">Seleccionar</option>
                                <option value="MOTO">Moto</option>
                                <option value="AUTO">Auto</option>
                                <option value="CAMIONETA">Camioneta</option>
                                <option value="CAMION">Camión</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="edit_placa_vehiculo">Placa del Vehículo</label>
                            <input type="text" name="placa_vehiculo" id="edit_placa_vehiculo" placeholder="Ej: ABC-123">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="edit_telefono_transportista">Teléfono (<span class="text-danger">*</span>)</label>
                            <input type="text" name="telefono_contacto" id="edit_telefono_transportista" placeholder="Ingrese teléfono">
                            <small id="error_edit_telefono_transportista" class="text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="edit_estado_transportista">Estado</label>
                            <select name="estado" id="edit_estado_transportista" class="js-example-basic-single select2">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_actualizar_transportista" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>