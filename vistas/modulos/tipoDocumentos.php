<?php
if (isset($_SESSION["permisos"])) {
    $permisos = $_SESSION["permisos"];
?>
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Tipos de Documentos <i class="fas fa-file-alt"></i></h4>
                <h6>Administrar tipos de documentos identificatorios</h6>
            </div>
            <?php if(isset($permisos["tipoDocumentos"]) && in_array("crear", $permisos["tipoDocumentos"]["acciones"])): ?>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_tipo_documento">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar Tipo
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_tipos_documentos">
                        <thead>
                            <tr>
                                <th width="5%">N°</th>
                                <th>Nombre</th>
                                <th>Abreviatura</th>
                                <th>Longitud</th>
                                <th>Para Empresa</th>
                                <th>Estado</th>
                                <th width="15%" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos dinámicos -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL NUEVO TIPO DOCUMENTO -->
<div class="modal fade" id="modal_nuevo_tipo_documento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Tipo de Documento</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="form_nuevo_tipo_documento">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre (<span class="text-danger">*</span>)</label>
                                <input type="text" class="form-control" name="nombre" id="nombre_tipo_documento" placeholder="Ingrese el nombre">
                                <small class="text-danger" id="error_nombre_tipo_documento"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Abreviatura (<span class="text-danger">*</span>)</label>
                                <input type="text" class="form-control" name="abreviatura" id="abreviatura_tipo_documento" maxlength="10" placeholder="Ingrese la abreviatura">
                                <small class="text-muted">Máximo 10 caracteres</small>
                                <small class="text-danger" id="error_abreviatura_tipo_documento"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Longitud</label>
                                <input type="number" class="form-control" name="longitud" id="longitud_tipo_documento" min="0" placeholder="Ingrese la longitud">
                                <small class="text-muted">Longitud en caracteres (0 para sin límite)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Es para empresa?</label>
                                <select class="js-example-basic-single select2" name="es_empresa" id="es_empresa_tipo_documento">
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado</label>
                                <select class="js-example-basic-single select2" name="estado" id="estado_tipo_documento">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="submit" id="btn_guardar_tipo_documento" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR TIPO DOCUMENTO -->
<div class="modal fade" id="modal_editar_tipo_documento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Tipo de Documento</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="form_editar_tipo_documento">
                <input type="hidden" name="id_tipo_documento" id="edit_id_tipo_documento">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre" id="edit_nombre_tipo_documento" required>
                                <small class="text-danger" id="error_edit_nombre_tipo_documento"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Abreviatura <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="abreviatura" id="edit_abreviatura_tipo_documento" maxlength="10" required>
                                <small class="text-danger" id="error_edit_abreviatura_tipo_documento"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Longitud</label>
                                <input type="number" class="form-control" name="longitud" id="edit_longitud_tipo_documento" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Es para empresa?</label>
                                <select class="js-example-basic-single select2" name="es_empresa" id="edit_es_empresa_tipo_documento">
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estado</label>
                                <select class="js-example-basic-single select2" name="estado" id="edit_estado_tipo_documento">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn_actualizar_tipo_documento">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
}
?>