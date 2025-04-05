<style>
    /* Estilos para el modal de código de barras */
#contenedor_codigo_barras {
    background: white;
    padding: 20px;
    border-radius: 5px;
    margin: 10px auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

#info_producto_codigo {
    margin-top: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}

#nombre_producto_codigo {
    font-weight: bold;
    margin-bottom: 5px;
}

#precio_producto_codigo {
    font-size: 1.2em;
    color: #28a745;
    font-weight: bold;
}

</style>

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de Productos</h4>
                <h6>Administrar productos del sistema</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_producto">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar Producto
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_productos">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Código</th>
                                <th>Código Barras</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Unidad Medida</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>IVA</th>
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

<!-- MODAL NUEVO PRODUCTO -->
<div class="modal fade" id="modal_nuevo_producto" tabindex="-1" aria-labelledby="modal_nuevo_producto_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Producto</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_producto">
                <div class="modal-body">
                    <div class="row">
                        <!-- Código -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo_producto" class="form-label">Código (<span class="text-danger">*</span>)</label>
                                <input type="text" name="codigo" id="codigo_producto" placeholder="Ingresa el código del producto">
                                <small id="error_codigo_producto"></small>
                            </div>
                        </div>
                        <!-- Código de Barras -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo_barras_producto" class="form-label">Código de Barras</label>
                                <input type="text" name="codigo_barras" id="codigo_barras_producto" placeholder="Ingresa el código de barras">
                                <small id="error_codigo_barras_producto"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_producto" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                <input type="text" name="nombre" id="nombre_producto" placeholder="Ingresa el nombre del producto">
                                <small id="error_nombre_producto"></small>
                            </div>
                        </div>
                        <!-- Categoría -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categoria_producto" class="form-label">Categoría (<span class="text-danger">*</span>)</label>
                                <select name="id_categoria" id="categoria_producto" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_categoria_producto"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Descripción -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion_producto" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion_producto" class="form-control" rows="2" placeholder="Ingresa una descripción del producto"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Unidad de Medida -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unidad_medida_producto" class="form-label">Unidad de Medida (<span class="text-danger">*</span>)</label>
                                <select name="unidad_medida" id="unidad_medida_producto" class="js-example-basic-single select2">
                                    <option value="UNIDAD">Unidad</option>
                                    <option value="KILOGRAMO">Kilogramo</option>
                                    <option value="GRAMO">Gramo</option>
                                    <option value="LITRO">Litro</option>
                                    <option value="MILILITRO">Mililitro</option>
                                    <option value="CAJA">Caja</option>
                                    <option value="BOLSA">Bolsa</option>
                                </select>
                                <small id="error_unidad_medida_producto"></small>
                            </div>
                        </div>
                        <!-- Peso Promedio (para pollos) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="peso_promedio_producto" class="form-label">Peso Promedio (kg)</label>
                                <input type="number" step="0.001" name="peso_promedio" id="peso_promedio_producto" placeholder="Ej. 1.250" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Precio Compra -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio_compra_producto" class="form-label">Precio Compra (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="precio_compra" id="precio_compra_producto" placeholder="0.00" class="form-control">
                                <small id="error_precio_compra_producto"></small>
                            </div>
                        </div>
                        <!-- Precio Venta -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio_venta_producto" class="form-label">Precio Venta (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="precio_venta" id="precio_venta_producto" placeholder="0.00" class="form-control">
                                <small id="error_precio_venta_producto"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Tiene IVA -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tiene_iva_producto" class="form-label">¿Aplica IVA? (<span class="text-danger">*</span>)</label>
                                <select name="tiene_iva" id="tiene_iva_producto" class="js-example-basic-single select2">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                                <small id="error_tiene_iva_producto"></small>
                            </div>
                        </div>
                        <!-- Imagen -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="imagen_producto" class="form-label">Imagen</label>
                                <input type="file" name="imagen" id="imagen_producto" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado_producto" class="form-label">Estado</label>
                                <select name="estado" id="estado_producto" class="js-example-basic-single select2">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_producto" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR PRODUCTO -->
<div class="modal fade" id="modal_editar_producto" tabindex="-1" aria-labelledby="modal_editar_producto_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_producto">
                <div class="modal-body">
                    <!-- ID -->
                    <input type="hidden" name="id_producto" id="edit_id_producto">
                    <!-- Imagen actual -->
                    <input type="hidden" name="imagen_actual" id="edit_imagen_actual">
                    
                    <div class="row">
                        <!-- Código -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_codigo_producto" class="form-label">Código (<span class="text-danger">*</span>)</label>
                                <input type="text" name="codigo" id="edit_codigo_producto" placeholder="Ingresa el código del producto">
                                <small id="error_edit_codigo_producto"></small>
                            </div>
                        </div>
                        <!-- Código de Barras -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_codigo_barras_producto" class="form-label">Código de Barras</label>
                                <input type="text" name="codigo_barras" id="edit_codigo_barras_producto" placeholder="Ingresa el código de barras">
                                <small id="error_edit_codigo_barras_producto"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nombre_producto" class="form-label">Nombre (<span class="text-danger">*</span>)</label>
                                <input type="text" name="nombre" id="edit_nombre_producto" placeholder="Ingresa el nombre del producto">
                                <small id="error_edit_nombre_producto"></small>
                            </div>
                        </div>
                        <!-- Categoría -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_categoria_producto" class="form-label">Categoría (<span class="text-danger">*</span>)</label>
                                <select name="id_categoria" id="edit_categoria_producto" class="js-example-basic-single select2">
                                    <!-- Opciones dinámicas -->
                                </select>
                                <small id="error_edit_categoria_producto"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Descripción -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_descripcion_producto" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="edit_descripcion_producto" class="form-control" rows="2" placeholder="Ingresa una descripción del producto"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Unidad de Medida -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_unidad_medida_producto" class="form-label">Unidad de Medida (<span class="text-danger">*</span>)</label>
                                <select name="unidad_medida" id="edit_unidad_medida_producto" class="js-example-basic-single select2">
                                    <option value="UNIDAD">Unidad</option>
                                    <option value="KILOGRAMO">Kilogramo</option>
                                    <option value="GRAMO">Gramo</option>
                                    <option value="LITRO">Litro</option>
                                    <option value="MILILITRO">Mililitro</option>
                                    <option value="CAJA">Caja</option>
                                    <option value="BOLSA">Bolsa</option>
                                </select>
                                <small id="error_edit_unidad_medida_producto"></small>
                            </div>
                        </div>
                        <!-- Peso Promedio (para pollos) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_peso_promedio_producto" class="form-label">Peso Promedio (kg)</label>
                                <input type="number" step="0.001" name="peso_promedio" id="edit_peso_promedio_producto" placeholder="Ej. 1.250" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Precio Compra -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_precio_compra_producto" class="form-label">Precio Compra (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="precio_compra" id="edit_precio_compra_producto" placeholder="0.00" class="form-control">
                                <small id="error_edit_precio_compra_producto"></small>
                            </div>
                        </div>
                        <!-- Precio Venta -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_precio_venta_producto" class="form-label">Precio Venta (<span class="text-danger">*</span>)</label>
                                <input type="number" step="0.01" name="precio_venta" id="edit_precio_venta_producto" placeholder="0.00" class="form-control">
                                <small id="error_edit_precio_venta_producto"></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Tiene IVA -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tiene_iva_producto" class="form-label">¿Aplica IVA? (<span class="text-danger">*</span>)</label>
                                <select name="tiene_iva" id="edit_tiene_iva_producto" class="js-example-basic-single select2">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                                <small id="error_edit_tiene_iva_producto"></small>
                            </div>
                        </div>
                        <!-- Imagen -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_imagen_producto" class="form-label">Imagen</label>
                                <input type="file" name="imagen" id="edit_imagen_producto" class="form-control">
                                <div class="mt-2">
                                    <img id="img_producto_actual" src="" alt="Imagen actual" style="max-width: 100px; max-height: 100px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Estado -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_estado_producto" class="form-label">Estado</label>
                                <select name="estado" id="edit_estado_producto" class="js-example-basic-single select2">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_producto" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER DETALLES DEL PRODUCTO -->
<div class="modal fade" id="modal_ver_detalles" tabindex="-1" aria-labelledby="modal_ver_detalles_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Producto</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Código:</label>
                            <p id="detalle_codigo"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Nombre:</label>
                            <p id="detalle_nombre"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Categoría:</label>
                            <p id="detalle_categoria"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Descripción:</label>
                            <p id="detalle_descripcion"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Unidad de Medida:</label>
                            <p id="detalle_unidad_medida"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Peso Promedio:</label>
                            <p id="detalle_peso_promedio"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Precio Compra:</label>
                            <p id="detalle_precio_compra"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Precio Venta:</label>
                            <p id="detalle_precio_venta"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">IVA:</label>
                            <p id="detalle_iva"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="form-group">
                            <label class="font-weight-bold">Imagen:</label>
                            <img id="detalle_imagen" src="" alt="Imagen del producto" class="img-fluid" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mx-4 mb-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL IMPRIMIR CÓDIGO DE BARRAS -->
<div class="modal fade" id="modal_imprimir_codigo" tabindex="-1" aria-labelledby="modal_imprimir_codigo_Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imprimir Código de Barras</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cantidad_codigos" class="form-label">Cantidad de etiquetas a imprimir</label>
                        <input type="number" id="cantidad_codigos" class="form-control" min="1" max="100" value="1">
                    </div>
                    <div class="col-md-6">
                        <label for="tamano_codigos" class="form-label">Tamaño de etiqueta</label>
                        <select id="tamano_codigos" class="form-select">
                            <option value="small">Pequeña (25x15mm)</option>
                            <option value="medium" selected>Mediana (50x30mm)</option>
                            <option value="large">Grande (75x50mm)</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="contenedor_codigo_barras" class="mb-3">
                            <!-- Aquí se mostrará el código de barras -->
                        </div>
                        <div id="info_producto_codigo">
                            <h5 id="nombre_producto_codigo"></h5>
                            <p id="precio_producto_codigo"></p>
                            <p id="codigo_producto_texto"></p>
                            <input type="hidden" hidden id="codigo_barra_producto">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btn_imprimir_codigo" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>