<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Registro de Bitácora</h4>
                <h6>Historial de actividades del sistema</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-added" id="btn_limpiar_bitacora">
                    <img src="vistas/assets/img/icons/delete.svg" alt="img" class="me-2">Limpiar registros
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filtro_fecha_inicio" class="form-label">Fecha inicio</label>
                        <input type="date" class="form-control" id="filtro_fecha_inicio">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_fecha_fin" class="form-label">Fecha fin</label>
                        <input type="date" class="form-control" id="filtro_fecha_fin">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_usuario" class="form-label">Usuario</label>
                        <select class="js-example-basic-single select2" id="filtro_usuario">
                            <option value="">Todos</option>
                            <!-- Se llenará dinámicamente -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_accion" class="form-label">Acción</label>
                        <select class="js-example-basic-single select2" id="filtro_accion">
                            <option value="">Todas</option>
                            <option value="CREAR">Creación</option>
                            <option value="ACTUALIZAR">Actualización</option>
                            <option value="ELIMINAR">Eliminación</option>
                            <option value="LOGIN">Inicio de sesión</option>
                            <option value="LOGOUT">Cierre de sesión</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_bitacora">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Tabla afectada</th>
                                <th>ID Registro</th>
                                <th>IP</th>
                                <th>Dispositivo</th>
                                <th class="text-center">Detalles</th>
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

<!-- MODAL DETALLES BITÁCORA -->
<div class="modal fade" id="modal_detalles_bitacora" tabindex="-1" aria-labelledby="modal_detalles_bitacora_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del registro</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-center text-danger">Datos anteriores</h6>
                        <pre id="detalles_datos_anteriores" class="p-3 bg-light rounded"></pre>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-center text-success">Datos nuevos</h6>
                        <pre id="detalles_datos_nuevos" class="p-3 bg-light rounded"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>