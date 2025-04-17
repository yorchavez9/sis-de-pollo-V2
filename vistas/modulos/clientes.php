<?php
if (isset($_SESSION["permisos"])) {
    $permisos = $_SESSION["permisos"];
?>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Lista de clientes</h4>
                    <h6>Administrar clientes</h6>
                </div>
                <?php
                if (isset($permisos["clientes"]) && in_array("crear", $permisos["clientes"]["acciones"])) {
                ?>
                    <div class="page-btn">
                        <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_cliente">
                            <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar cliente
                        </a>
                    </div>
                <?php } ?>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%" id="tabla_clientes">
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

    <!-- MODAL NUEVO CLIENTE -->
    <div class="modal fade" id="modal_nuevo_cliente" tabindex="-1" aria-labelledby="modal_nuevo_cliente_Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar cliente</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form enctype="multipart/form-data" id="form_nuevo_cliente">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Tipo Documento -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_documento_cliente" class="form-label">Tipo Documento (<span class="text-danger">*</span>)</label>
                                    <select name="id_tipo_documento" id="tipo_documento_cliente" class="js-example-basic-single select2">
                                        <!-- Opciones dinámicas -->
                                    </select>
                                    <small id="error_tipo_documento_cliente"></small>
                                </div>
                            </div>
                            <!-- Número Documento -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_documento_cliente" class="form-label">N° Documento (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="numero_documento" id="numero_documento_cliente" placeholder="Ingresa el número de documento">
                                    <small id="error_numero_documento_cliente"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tipo de Cliente -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_cliente" class="form-label">Tipo de Cliente (<span class="text-danger">*</span>)</label>
                                    <select name="tipo_cliente" id="tipo_cliente" class="js-example-basic-single select2">
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
                                        <label for="nombre_cliente" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                        <input type="text" name="nombre" id="nombre_cliente" placeholder="Ingresa el nombre">
                                        <small id="error_nombre_cliente"></small>
                                    </div>
                                </div>
                                <!-- Apellidos -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apellidos_cliente" class="form-label">Apellidos</label>
                                        <input type="text" name="apellidos" id="apellidos_cliente" placeholder="Ingresa los apellidos">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Fecha Nacimiento -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_nacimiento_cliente" class="form-label">Fecha Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento_cliente" class="form-control">
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
                                        <label for="razon_social_cliente" class="form-label">Razón Social (<span class="text-danger">*</span>)</label>
                                        <input type="text" name="razon_social" id="razon_social_cliente" placeholder="Ingresa la razón social">
                                        <small id="error_razon_social_cliente"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono_cliente" class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" id="telefono_cliente" placeholder="Ingresa el teléfono">
                                </div>
                            </div>
                            <!-- Celular -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="celular_cliente" class="form-label">Celular (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="celular" id="celular_cliente" placeholder="Ingresa el celular">
                                    <small id="error_celular_cliente"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email_cliente" class="form-label">Email</label>
                                    <input type="email" name="email" id="email_cliente" class="form-control" placeholder="Ingresa el email">
                                    <small id="error_email_cliente"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Dirección -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direccion_cliente" class="form-label">Dirección</label>
                                    <input type="text" name="direccion" id="direccion_cliente" placeholder="Ingresa la dirección">
                                </div>
                            </div>
                            <!-- Ciudad -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ciudad_cliente" class="form-label">Ciudad</label>
                                    <input type="text" name="ciudad" id="ciudad_cliente" placeholder="Ingresa la ciudad">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Estado -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado_cliente" class="form-label">Estado</label>
                                    <select name="estado" id="estado_cliente" class="form-select">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_guardar_cliente" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR CLIENTE -->
    <div class="modal fade" id="modal_editar_cliente" tabindex="-1" aria-labelledby="modal_editar_cliente_Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar cliente</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form enctype="multipart/form-data" id="form_update_cliente">
                    <div class="modal-body">
                        <!-- ID -->
                        <input type="hidden" name="id_persona" id="edit_id_cliente">

                        <div class="row">
                            <!-- Tipo Documento -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_tipo_documento_cliente" class="form-label">Tipo Documento (<span class="text-danger">*</span>)</label>
                                    <select name="id_tipo_documento" id="edit_tipo_documento_cliente" class="js-example-basic-single select2">
                                        <!-- Opciones dinámicas -->
                                    </select>
                                    <small id="error_edit_tipo_documento_cliente"></small>
                                </div>
                            </div>
                            <!-- Número Documento -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_numero_documento_cliente" class="form-label">N° Documento (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="numero_documento" id="edit_numero_documento_cliente" placeholder="Ingresa el número de documento">
                                    <small id="error_edit_numero_documento_cliente"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tipo de Cliente -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_tipo_cliente" class="form-label">Tipo de Cliente (<span class="text-danger">*</span>)</label>
                                    <select name="tipo_cliente" id="edit_tipo_cliente" class="js-example-basic-single select2">
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
                                        <label for="edit_nombre_cliente" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                        <input type="text" name="nombre" id="edit_nombre_cliente" placeholder="Ingresa el nombre">
                                        <small id="error_edit_nombre_cliente"></small>
                                    </div>
                                </div>
                                <!-- Apellidos -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_apellidos_cliente" class="form-label">Apellidos</label>
                                        <input type="text" name="apellidos" id="edit_apellidos_cliente" placeholder="Ingresa los apellidos">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Fecha Nacimiento -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_fecha_nacimiento_cliente" class="form-label">Fecha Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" id="edit_fecha_nacimiento_cliente" class="form-control">
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
                                        <label for="edit_razon_social_cliente" class="form-label">Razón Social (<span class="text-danger">*</span>)</label>
                                        <input type="text" name="razon_social" id="edit_razon_social_cliente" placeholder="Ingresa la razón social">
                                        <small id="error_edit_razon_social_cliente"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_telefono_cliente" class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" id="edit_telefono_cliente" placeholder="Ingresa el teléfono">
                                </div>
                            </div>
                            <!-- Celular -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_celular_cliente" class="form-label">Celular (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="celular" id="edit_celular_cliente" placeholder="Ingresa el celular">
                                    <small id="error_edit_celular_cliente"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_email_cliente" class="form-label">Email</label>
                                    <input type="email" name="email" id="edit_email_cliente" class="form-control" placeholder="Ingresa el email">
                                    <small id="error_edit_email_cliente"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Dirección -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_direccion_cliente" class="form-label">Dirección</label>
                                    <input type="text" name="direccion" id="edit_direccion_cliente" placeholder="Ingresa la dirección">
                                </div>
                            </div>
                            <!-- Ciudad -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_ciudad_cliente" class="form-label">Ciudad</label>
                                    <input type="text" name="ciudad" id="edit_ciudad_cliente" placeholder="Ingresa la ciudad">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Estado -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_estado_cliente" class="form-label">Estado</label>
                                    <select name="estado" id="edit_estado_cliente" class="js-example-basic-single select2">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_update_cliente" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
?>