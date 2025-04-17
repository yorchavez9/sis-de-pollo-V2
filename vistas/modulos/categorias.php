<?php
if (isset($_SESSION["permisos"])) {
    $permisos = $_SESSION["permisos"];
?>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Lista de categorías</h4>
                    <h6>Administrar categorías de productos</h6>
                </div>
                <?php
                if (isset($permisos["categorias"]) && in_array("crear", $permisos["categorias"]["acciones"])) {
                ?>
                    <div class="page-btn">
                        <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nueva_categoria">
                            <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar categoría
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%" id="tabla_categorias">
                            <thead>
                                <tr>
                                    <th>N°</th>
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

    <!-- MODAL NUEVA CATEGORÍA -->
    <div class="modal fade" id="modal_nueva_categoria" tabindex="-1" aria-labelledby="modal_nueva_categoria_Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear categoría</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form enctype="multipart/form-data" id="form_nueva_categoria">
                    <div class="modal-body">
                        <!-- Nombre -->
                        <div class="form-group">
                            <label for="nombre_categoria" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                            <input type="text" name="nombre" id="nombre_categoria" placeholder="Ingresa el nombre de la categoría">
                            <small id="error_nombre_categoria"></small>
                        </div>
                        <!-- Descripción -->
                        <div class="form-group">
                            <label for="descripcion_categoria" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion_categoria" placeholder="Ingresa la descripción"></textarea>
                        </div>
                        <!-- Tipo -->
                        <div class="form-group">
                            <label for="tipo_categoria" class="form-label">Tipo (<span class="text-danger">*</span>)</label>
                            <select name="tipo" id="tipo_categoria" class="form-select">
                                <option value="POLLO">Pollo</option>
                                <option value="PRODUCTO">Producto</option>
                                <option value="INSUMO">Insumo</option>
                            </select>
                            <small id="error_tipo_categoria"></small>
                        </div>
                        <!-- Estado -->
                        <div class="form-group">
                            <label for="estado_categoria" class="form-label">Estado</label>
                            <select name="estado" id="estado_categoria" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_guardar_categoria" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR CATEGORÍA -->
    <div class="modal fade" id="modal_editar_categoria" tabindex="-1" aria-labelledby="modal_editar_categoria_Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar categoría</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form enctype="multipart/form-data" id="form_update_categoria">
                    <div class="modal-body">
                        <!-- ID -->
                        <input type="hidden" name="id_categoria" id="edit_id_categoria">
                        <!-- Nombre -->
                        <div class="form-group">
                            <label for="edit_nombre_categoria" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                            <input type="text" name="nombre" id="edit_nombre_categoria" placeholder="Ingresa el nombre de la categoría">
                            <small id="error_edit_nombre_categoria"></small>
                        </div>
                        <!-- Descripción -->
                        <div class="form-group">
                            <label for="edit_descripcion_categoria" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion_categoria" placeholder="Ingresa la descripción"></textarea>
                        </div>
                        <!-- Tipo -->
                        <div class="form-group">
                            <label for="edit_tipo_categoria" class="form-label">Tipo (<span class="text-danger">*</span>)</label>
                            <select name="tipo" id="edit_tipo_categoria" class="form-select">
                                <option value="POLLO">Pollo</option>
                                <option value="PRODUCTO">Producto</option>
                                <option value="INSUMO">Insumo</option>
                            </select>
                            <small id="error_edit_tipo_categoria"></small>
                        </div>
                        <!-- Estado -->
                        <div class="form-group">
                            <label for="edit_estado_categoria" class="form-label">Estado</label>
                            <select name="estado" id="edit_estado_categoria" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_update_categoria" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
?>