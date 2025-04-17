<?php
if (isset($_SESSION["permisos"])) {
    $permisos = $_SESSION["permisos"];
?>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Lista de roles</h4>
                    <h6>Administrar roles del sistema</h6>
                </div>
                <?php
                if (isset($permisos["roles"]) && in_array("crear", $permisos["roles"]["acciones"])) {
                ?>
                    <div class="page-btn">
                        <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_rol">
                            <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar rol
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%" id="tabla_roles">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Nivel de Acceso</th>
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

    <!-- MODAL NUEVO ROL -->
    <div class="modal fade" id="modal_nuevo_rol" tabindex="-1" aria-labelledby="modal_nuevo_rol_Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear rol</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form id="form_nuevo_rol">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Nombre -->
                                <div class="form-group">
                                    <label for="nombre_rol" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="nombre" id="nombre_rol" placeholder="Ingresa el nombre del rol">
                                    <small id="error_nombre_rol"></small>
                                </div>
                                <!-- Descripción -->
                                <div class="form-group">
                                    <label for="descripcion_rol" class="form-label">Descripción</label>
                                    <textarea name="descripcion" id="descripcion_rol" rows="3" placeholder="Ingresa una descripción del rol"></textarea>
                                </div>
                                <!-- Nivel de acceso -->
                                <div class="form-group">
                                    <label for="nivel_acceso_rol" class="form-label">Nivel de acceso (<span class="text-danger">*</span>)</label>
                                    <select name="nivel_acceso" id="nivel_acceso_rol" class="form-select">
                                        <option value="1">Básico</option>
                                        <option value="2">Intermedio</option>
                                        <option value="3">Avanzado</option>
                                        <option value="4">Administrador</option>
                                    </select>
                                    <small id="error_nivel_acceso_rol"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_guardar_rol" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR ROL -->
    <div class="modal fade" id="modal_editar_rol" tabindex="-1" aria-labelledby="modal_editar_rol_Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar rol</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form id="form_update_rol">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- ID -->
                                <input type="hidden" name="id_rol" id="edit_id_rol">
                                <!-- Nombre -->
                                <div class="form-group">
                                    <label for="edit_nombre_rol" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                    <input type="text" name="nombre" id="edit_nombre_rol" placeholder="Ingresa el nombre del rol">
                                    <small id="error_edit_nombre_rol"></small>
                                </div>
                                <!-- Descripción -->
                                <div class="form-group">
                                    <label for="edit_descripcion_rol" class="form-label">Descripción</label>
                                    <textarea name="descripcion" id="edit_descripcion_rol" rows="3" placeholder="Ingresa una descripción del rol"></textarea>
                                </div>
                                <!-- Nivel de acceso -->
                                <div class="form-group">
                                    <label for="edit_nivel_acceso_rol" class="form-label">Nivel de acceso (<span class="text-danger">*</span>)</label>
                                    <select name="nivel_acceso" id="edit_nivel_acceso_rol" class="form-select">
                                        <option value="1">Básico</option>
                                        <option value="2">Intermedio</option>
                                        <option value="3">Avanzado</option>
                                        <option value="4">Administrador</option>
                                    </select>
                                    <small id="error_edit_nivel_acceso_rol"></small>
                                </div>
                                <!-- Estado -->
                                <div class="form-group">
                                    <label for="edit_estado_rol" class="form-label">Estado</label>
                                    <select name="estado" id="edit_estado_rol" class="form-select">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_update_rol" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
?>