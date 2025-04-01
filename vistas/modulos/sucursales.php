<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de sucursales</h4>
                <h6>Administrar sucursal</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_sucursal"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar sucursal</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-path">
                            <a class="btn btn-filter" id="filter_search">
                                <img src="vistas/assets/img/icons/filter.svg" alt="img">
                                <span><img src="vistas/assets/img/icons/closes.svg" alt="img"></span>
                            </a>
                        </div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="vistas/assets/img/icons/search-white.svg" alt="img">
                            </a>
                        </div>
                    </div>
                    <div class="wordset">
                        <ul>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="vistas/assets/img/icons/pdf.svg" alt="img"></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img src="vistas/assets/img/icons/excel.svg" alt="img"></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img src="vistas/assets/img/icons/printer.svg" alt="img"></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_sucursal">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- MODAL NUEVO SUCURSAL -->
<div class="modal fade" id="modal_nuevo_sucursal" tabindex="-1" aria-labelledby="modal_nuevo_sucursal_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear sucursal</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_sucursal">
                <div class="modal-body">
                    <!-- Código -->
                    <div class="form-group">
                        <label for="codigo_sucursal" class="form-label">Código (<span class="text-danger">*</span>)</label>
                        <input type="text" name="codigo" id="codigo_sucursal" placeholder="Ingresa el código">
                        <small id="error_codigo_sucursal"></small>
                    </div>
                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="nombre_sucursal" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="nombre" id="nombre_sucursal" placeholder="Ingresa el nombre">
                        <small id="error_nombre_sucursal"></small>
                    </div>
                    <!-- Dirección -->
                    <div class="form-group">
                        <label for="direccion_sucursal" class="form-label">Dirección (<span class="text-danger">*</span>)</label>
                        <input type="text" name="direccion" id="direccion_sucursal" placeholder="Ingresa la dirección">
                        <small id="error_direccion_sucursal"></small>
                    </div>
                    <!-- Ciudad -->
                    <div class="form-group">
                        <label for="ciudad_sucursal" class="form-label">Ciudad (<span class="text-danger">*</span>)</label>
                        <input type="text" name="ciudad" id="ciudad_sucursal" placeholder="Ingresa la ciudad">
                        <small id="error_ciudad_sucursal"></small>
                    </div>
                    <!-- Teléfono -->
                    <div class="form-group">
                        <label for="telefono_sucursal" class="form-label">Teléfono (<span class="text-danger">*</span>)</label>
                        <input type="text" name="telefono" id="telefono_sucursal" placeholder="Ingresa el teléfono">
                        <small id="error_telefono_sucursal"></small>
                    </div>
                    <!-- Responsable -->
                    <div class="form-group">
                        <label for="responsable_sucursal" class="form-label">Responsable</label>
                        <input type="text" name="responsable" id="responsable_sucursal" placeholder="Ingresa el responsable">
                        <small id="error_responsable_sucursal"></small>
                    </div>
                    <!-- Es Principal -->
                    <div class="form-group">
                        <label for="es_principal_sucursal" class="form-label">¿Es principal?</label>
                        <select name="es_principal" id="es_principal_sucursal" class="form-select">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_sucursal" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR SUCURSAL -->
<div class="modal fade" id="modal_editar_sucursal" tabindex="-1" aria-labelledby="modal_editar_sucursal_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar sucursal</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_sucursal">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_sucursal" id="edit_id_sucursal">
                    <!-- Código -->
                    <div class="form-group">
                        <label for="edit_codigo_sucursal" class="form-label">Código (<span class="text-danger">*</span>)</label>
                        <input type="text" name="codigo" id="edit_codigo_sucursal" placeholder="Ingresa el código">
                        <small id="error_edit_codigo_sucursal"></small>
                    </div>
                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="edit_nombre_sucursal" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="nombre" id="edit_nombre_sucursal" placeholder="Ingresa el nombre">
                        <small id="error_edit_nombre_sucursal"></small>
                    </div>
                    <!-- Dirección -->
                    <div class="form-group">
                        <label for="edit_direccion_sucursal" class="form-label">Dirección (<span class="text-danger">*</span>)</label>
                        <input type="text" name="direccion" id="edit_direccion_sucursal" placeholder="Ingresa la dirección">
                        <small id="error_edit_direccion_sucursal"></small>
                    </div>
                    <!-- Ciudad -->
                    <div class="form-group">
                        <label for="edit_ciudad_sucursal" class="form-label">Ciudad (<span class="text-danger">*</span>)</label>
                        <input type="text" name="ciudad" id="edit_ciudad_sucursal" placeholder="Ingresa la ciudad">
                        <small id="error_edit_ciudad_sucursal"></small>
                    </div>
                    <!-- Teléfono -->
                    <div class="form-group">
                        <label for="edit_telefono_sucursal" class="form-label">Teléfono (<span class="text-danger">*</span>)</label>
                        <input type="text" name="telefono" id="edit_telefono_sucursal" placeholder="Ingresa el teléfono">
                        <small id="error_edit_telefono_sucursal"></small>
                    </div>
                    <!-- Responsable -->
                    <div class="form-group">
                        <label for="edit_responsable_sucursal" class="form-label">Responsable</label>
                        <input type="text" name="responsable" id="edit_responsable_sucursal" placeholder="Ingresa el responsable">
                        <small id="error_edit_responsable_sucursal"></small>
                    </div>
                    <!-- Es Principal -->
                    <div class="form-group">
                        <label for="edit_es_principal_sucursal" class="form-label">¿Es principal?</label>
                        <select name="es_principal" id="edit_es_principal_sucursal" class="form-select">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_sucursal" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER DETALLES -->
<div class="modal fade" id="modal_ver_detalles_sucursal" tabindex="-1" aria-labelledby="modal_ver_detalles_sucursal_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la sucursal</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="detalle_codigo_sucursal" class="form-label">Código:</label>
                    <p id="detalle_codigo_sucursal"></p>
                </div>
                <div class="form-group">
                    <label for="detalle_nombre_sucursal" class="form-label">Nombre:</label>
                    <p id="detalle_nombre_sucursal"></p>
                </div>
                <div class="form-group">
                    <label for="detalle_direccion_sucursal" class="form-label">Dirección:</label>
                    <p id="detalle_direccion_sucursal"></p>
                </div>
                <div class="form-group">
                    <label for="detalle_ciudad_sucursal" class="form-label">Ciudad:</label>
                    <p id="detalle_ciudad_sucursal"></p>
                </div>
                <div class="form-group">
                    <label for="detalle_telefono_sucursal" class="form-label">Teléfono:</label>
                    <p id="detalle_telefono_sucursal"></p>
                </div>
                <div class="form-group">
                    <label for="detalle_responsable_sucursal" class="form-label">Responsable:</label>
                    <p id="detalle_responsable_sucursal"></p>
                </div>
                <div class="form-group">
                    <label for="detalle_es_principal_sucursal" class="form-label">¿Es principal?</label>
                    <p id="detalle_es_principal_sucursal"></p>
                </div>
            </div>
            <div class="text-end mx-4 mb-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>