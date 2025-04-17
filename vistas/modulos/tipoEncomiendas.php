<?php

if (isset($_SESSION["permisos"])) {
    $permisos = $_SESSION["permisos"];

?>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Lista de tipos de encomienda</h4>
                    <h6>Administrar tipos de encomienda</h6>
                </div>
                <?php if(isset($permisos["tipoEncomiendas"]) && in_array("crear", $permisos["tipoEncomiendas"])): ?>
                <div class="page-btn">
                    <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_tipo_encomienda">
                        <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar tipo
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%" id="tabla_tipo_encomiendas">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Requiere confirmación</th>
                                    <th class="text-center">Prioridad</th>
                                    <th class="text-center">Estado</th>
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

    <!-- MODAL NUEVO TIPO ENCOMIENDA -->
    <div class="modal fade" id="modal_nuevo_tipo_encomienda" tabindex="-1" aria-labelledby="modal_nuevo_tipo_encomienda_Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear tipo de encomienda</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form enctype="multipart/form-data" id="form_nuevo_tipo_encomienda">
                    <div class="modal-body">
                        <!-- Nombre -->
                        <div class="form-group">
                            <label for="nombre_tipo_encomienda" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                            <input type="text" name="nombre" id="nombre_tipo_encomienda" placeholder="Ingresa el nombre">
                            <small id="error_nombre_tipo_encomienda"></small>
                        </div>
                        <!-- Descripción -->
                        <div class="form-group">
                            <label for="descripcion_tipo_encomienda" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion_tipo_encomienda" placeholder="Ingresa la descripción"></textarea>
                            <small id="error_descripcion_tipo_encomienda"></small>
                        </div>
                        <!-- Requiere confirmación -->
                        <div class="form-group">
                            <label for="requiere_confirmacion_tipo_encomienda" class="form-label">Requiere confirmación</label>
                            <select name="requiere_confirmacion" id="requiere_confirmacion_tipo_encomienda" class="js-example-basic-single select2">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <!-- Prioridad -->
                        <div class="form-group">
                            <label for="prioridad_tipo_encomienda" class="form-label">Prioridad (<span class="text-danger">*</span>)</label>
                            <select name="prioridad" id="prioridad_tipo_encomienda" class="js-example-basic-single select2">
                                <option value="BAJA">BAJA</option>
                                <option value="MEDIA" selected>MEDIA</option>
                                <option value="ALTA">ALTA</option>
                                <option value="URGENTE">URGENTE</option>
                            </select>
                            <small id="error_prioridad_tipo_encomienda"></small>
                        </div>
                        <!-- Estado -->
                        <div class="form-group">
                            <label for="estado_tipo_encomienda" class="form-label">Estado</label>
                            <select name="estado" id="estado_tipo_encomienda" class="js-example-basic-single select2">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_guardar_tipo_encomienda" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR TIPO ENCOMIENDA -->
    <div class="modal fade" id="modal_editar_tipo_encomienda" tabindex="-1" aria-labelledby="modal_editar_tipo_encomienda_Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar tipo de encomienda</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form enctype="multipart/form-data" id="form_update_tipo_encomienda">
                    <div class="modal-body">
                        <!-- ID -->
                        <input type="hidden" name="id_tipo_encomienda" id="edit_id_tipo_encomienda">
                        <!-- Nombre -->
                        <div class="form-group">
                            <label for="edit_nombre_tipo_encomienda" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                            <input type="text" name="nombre" id="edit_nombre_tipo_encomienda" placeholder="Ingresa el nombre">
                            <small id="error_edit_nombre_tipo_encomienda"></small>
                        </div>
                        <!-- Descripción -->
                        <div class="form-group">
                            <label for="edit_descripcion_tipo_encomienda" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion_tipo_encomienda" placeholder="Ingresa la descripción"></textarea>
                            <small id="error_edit_descripcion_tipo_encomienda"></small>
                        </div>
                        <!-- Requiere confirmación -->
                        <div class="form-group">
                            <label for="edit_requiere_confirmacion_tipo_encomienda" class="form-label">Requiere confirmación</label>
                            <select name="requiere_confirmacion" id="edit_requiere_confirmacion_tipo_encomienda" class="form-select">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <!-- Prioridad -->
                        <div class="form-group">
                            <label for="edit_prioridad_tipo_encomienda" class="form-label">Prioridad (<span class="text-danger">*</span>)</label>
                            <select name="prioridad" id="edit_prioridad_tipo_encomienda" class="js-example-basic-single select2">
                                <option value="BAJA">BAJA</option>
                                <option value="MEDIA">MEDIA</option>
                                <option value="ALTA">ALTA</option>
                                <option value="URGENTE">URGENTE</option>
                            </select>
                            <small id="error_edit_prioridad_tipo_encomienda"></small>
                        </div>
                        <!-- Estado -->
                        <div class="form-group">
                            <label for="edit_estado_tipo_encomienda" class="form-label">Estado</label>
                            <select name="estado" id="edit_estado_tipo_encomienda" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_update_tipo_encomienda" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
?>