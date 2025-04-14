<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de usuarios</h4>
                <h6>Administrar usuarios del sistema</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_usuario">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar usuario
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_usuarios">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Sucursal</th>
                                <th>Persona</th>
                                <th>Nombre Usuario</th>
                                <th>Usuario</th>
                                <th>Imagen</th>
                                <th>Último Login</th>
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

<!-- MODAL NUEVO USUARIO -->
<div class="modal fade" id="modal_nuevo_usuario" tabindex="-1" aria-labelledby="modal_nuevo_usuario_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_usuario">
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <!-- Sucursal -->
                            <div class="form-group">
                                <label for="sucursal_usuario" class="form-label">Sucursal (<span class="text-danger">*</span>)</label>
                                <select name="id_sucursal" id="sucursal_usuario" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_sucursal_usuario"></small>
                            </div>
                            <!-- Persona -->
                            <div class="form-group">
                                <label for="persona_usuario" class="form-label">Persona (<span class="text-danger">*</span>)</label>
                                <select name="id_persona" id="persona_usuario" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_persona_usuario"></small>
                            </div>
                            <!-- Nombre de usuario -->
                            <div class="form-group">
                                <label for="nombre_usuario" class="form-label">Nombre de usuario (<span class="text-danger">*</span>)</label>
                                <input type="text" name="nombre_usuario" id="nombre_usuario" placeholder="Ingresa el nombre para mostrar">
                                <small id="error_nombre_usuario"></small>
                            </div>
                        </div>
                        
                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <!-- Usuario (login) -->
                            <div class="form-group">
                                <label for="usuario" class="form-label">Usuario (login) (<span class="text-danger">*</span>)</label>
                                <input type="text" name="usuario" id="usuario" placeholder="Ingresa el nombre de usuario">
                                <small id="error_usuario"></small>
                            </div>
                            <!-- Contraseña -->
                            <div class="form-group">
                                <label for="contrasena" class="form-label">Contraseña (<span class="text-danger">*</span>)</label>
                                <div class="input-group">
                                    <input type="password" name="contrasena" id="contrasena" placeholder="Ingresa la contraseña">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                                <small id="error_contrasena"></small>
                            </div>
                            <!-- Imagen -->
                            <div class="form-group">
                                <label for="imagen_usuario" class="form-label">Imagen de perfil</label>
                                <input type="file" name="imagen" id="imagen_usuario" class="form-control">
                                <small id="error_imagen_usuario"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_usuario" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR USUARIO -->
<div class="modal fade" id="modal_editar_usuario" tabindex="-1" aria-labelledby="modal_editar_usuario_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_usuario">
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <!-- ID -->
                            <input type="hidden" name="id_usuario" id="edit_id_usuario">
                            <!-- Sucursal -->
                            <div class="form-group">
                                <label for="edit_sucursal_usuario" class="form-label">Sucursal (<span class="text-danger">*</span>)</label>
                                <select name="id_sucursal" id="edit_sucursal_usuario" class="form-select">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_sucursal_usuario"></small>
                            </div>
                            <!-- Persona -->
                            <div class="form-group">
                                <label for="edit_persona_usuario" class="form-label">Persona (<span class="text-danger">*</span>)</label>
                                <select name="id_persona" id="edit_persona_usuario" class="form-select">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_persona_usuario"></small>
                            </div>
                            <!-- Nombre de usuario -->
                            <div class="form-group">
                                <label for="edit_nombre_usuario" class="form-label">Nombre de usuario (<span class="text-danger">*</span>)</label>
                                <input type="text" name="nombre_usuario" id="edit_nombre_usuario" placeholder="Ingresa el nombre para mostrar">
                                <small id="error_edit_nombre_usuario"></small>
                            </div>
                        </div>
                        
                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <!-- Usuario (login) -->
                            <div class="form-group">
                                <label for="edit_usuario" class="form-label">Usuario (login) (<span class="text-danger">*</span>)</label>
                                <input type="text" name="usuario" id="edit_usuario" placeholder="Ingresa el nombre de usuario">
                                <small id="error_edit_usuario"></small>
                            </div>
                            <!-- Contraseña -->
                            <div class="form-group">
                                <label for="edit_contrasena" class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
                                <div class="input-group">
                                    <input type="password" name="contrasena" id="edit_contrasena" placeholder="Ingresa la nueva contraseña">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                                <small id="error_edit_contrasena"></small>
                            </div>
                            <!-- Imagen -->
                            <div class="form-group">
                                <label for="edit_imagen_usuario" class="form-label">Imagen de perfil</label>
                                <input type="file" name="imagen" id="edit_imagen_usuario" class="form-control">
                                <small id="error_edit_imagen_usuario"></small>
                                <div class="mt-2">
                                    <img id="preview_imagen_usuario" src="" alt="Imagen actual" style="max-width: 100px; display: none;">
                                </div>
                            </div>
                            <!-- Estado -->
                            <div class="form-group">
                                <label for="edit_estado_usuario" class="form-label">Estado</label>
                                <select name="estado" id="edit_estado_usuario" class="form-select">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_usuario" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>