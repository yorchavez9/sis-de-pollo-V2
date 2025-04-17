<?php
if (isset($_SESSION["permisos"])) {
    $permisos = $_SESSION["permisos"];
?>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Configuración del Sistema</h4>
                    <h6>Administrar la configuración general</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form id="form_configuracion" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Nombre Empresa -->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="nombre_empresa">Nombre de la Empresa</label>
                                    <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" required>
                                    <small id="error_nombre_empresa" class="text-danger"></small>
                                </div>
                            </div>
                            
                            <!-- RUC -->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="ruc">RUC</label>
                                    <input type="text" class="form-control" id="ruc" name="ruc" required>
                                    <small id="error_ruc" class="text-danger"></small>
                                </div>
                            </div>
                            
                            <!-- Dirección -->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" required>
                                    <small id="error_direccion" class="text-danger"></small>
                                </div>
                            </div>
                            
                            <!-- Teléfono -->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" required>
                                    <small id="error_telefono" class="text-danger"></small>
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <small id="error_email" class="text-danger"></small>
                                </div>
                            </div>
                            
                            <!-- Logo -->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                    <small id="error_logo" class="text-danger"></small>
                                    <div id="preview_logo" class="mt-2"></div>
                                </div>
                            </div>
                            
                            <!-- Moneda -->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="moneda">Moneda</label>
                                    <select class="form-control" id="moneda" name="moneda" required>
                                        <option value="PEN">Soles (PEN)</option>
                                        <option value="USD">Dólares (USD)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Impuesto -->
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="impuesto">Impuesto (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="impuesto" name="impuesto" required>
                                    <small id="error_impuesto" class="text-danger"></small>
                                </div>
                            </div>
                            
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>