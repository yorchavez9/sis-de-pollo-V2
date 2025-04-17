<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <!-- Widgets principales -->
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="dash-widget">
                    <div class="dash-widgetimg">
                        <span><img src="vistas/assets/img/icons/dash1.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5>S/ <span class="counters">0.00</span></h5>
                        <h6>Ventas del Mes</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="dash-widget dash1">
                    <div class="dash-widgetimg">
                        <span><img src="vistas/assets/img/icons/dash2.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5><span class="counters" id="total_envios_realizados"></span></h5>
                        <h6>Envíos Realizados</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="dash-widget dash2">
                    <div class="dash-widgetimg">
                        <span><img src="vistas/assets/img/icons/dash3.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5><span class="counters" id="envios_pendientes">0</span></h5>
                        <h6>Envíos Pendientes</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="dash-widget dash3">
                    <div class="dash-widgetimg">
                        <span><img src="vistas/assets/img/icons/dash4.svg" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5><span class="counters" >0</span></h5>
                        <h6>Incidencias Reportadas</h6>
                    </div>
                </div>
            </div>

            <!-- Contadores secundarios -->
            <div class="col-lg-3 col-sm-6 col-12 d-flex">
                <div class="dash-count">
                    <div class="dash-counts">
                        <h4 id="cliente_activos">0</h4>
                        <h5>Clientes Activos</h5>
                    </div>
                    <div class="dash-imgs">
                        <i data-feather="user"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12 d-flex">
                <div class="dash-count das1">
                    <div class="dash-counts">
                        <h4 id="transportistas_activos">0</h4>
                        <h5>Transportistas</h5>
                    </div>
                    <div class="dash-imgs">
                        <i data-feather="truck"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12 d-flex">
                <div class="dash-count das2">
                    <div class="dash-counts">
                        <h4 id="total_ventas">0</h4>
                        <h5>Total de ventas</h5>
                    </div>
                    <div class="dash-imgs">
                        <i data-feather="shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12 d-flex">
                <div class="dash-count das3">
                    <div class="dash-counts">
                        <h4 id="envios_entregados">0</h4>
                        <h5>Envíos entregados</h5>
                    </div>
                    <div class="dash-imgs">
                        <i data-feather="clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos y tablas -->
        <div class="row">
            <div class="col-lg-7 col-sm-12 col-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Ventas y Envíos Mensuales</h5>
                        <div class="graph-sets">
                            <ul>
                                <li>
                                    <span>Ventas</span>
                                </li>
                                <li>
                                    <span>Envíos</span>
                                </li>
                            </ul>
                            <div class="dropdown">
                                <button class="btn btn-white btn-sm dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    2023 <img src="vistas/assets/img/icons/dropdown.svg" alt="img" class="ms-2">
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">2023</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">2022</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">2021</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sales_shipping_charts"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-sm-12 col-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Envíos Recientes</h4>
                        <div class="dropdown">
                            <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false"
                                class="dropset">
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <a href="envios" class="dropdown-item">Ver Todos</a>
                                </li>
                                <li>
                                    <a href="envios" class="dropdown-item">Nuevo Envío</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive dataview">
                            <table class="table datatable" id="tabla_envios_recientes">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Destinatario</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
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

        <!-- Tabla de pedidos pendientes -->
       <!--  <div class="card mb-0">
            <div class="card-body">
                <h4 class="card-title">Pedidos por Preparar</h4>
                <div class="table-responsive dataview">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>Pedido #</th>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="order_detail.html">PE-10025</a></td>
                                <td>Luis Martínez</td>
                                <td>3 productos</td>
                                <td>$245.50</td>
                                <td>15/05/2023</td>
                                <td><button class="btn btn-sm btn-primary">Preparar</button></td>
                            </tr>
                            <tr>
                                <td><a href="order_detail.html">PE-10024</a></td>
                                <td>Tienda ABC</td>
                                <td>5 productos</td>
                                <td>$189.75</td>
                                <td>15/05/2023</td>
                                <td><button class="btn btn-sm btn-primary">Preparar</button></td>
                            </tr>
                            <tr>
                                <td><a href="order_detail.html">PE-10023</a></td>
                                <td>Supermercado XYZ</td>
                                <td>12 productos</td>
                                <td>$1,245.00</td>
                                <td>14/05/2023</td>
                                <td><button class="btn btn-sm btn-primary">Preparar</button></td>
                            </tr>
                            <tr>
                                <td><a href="order_detail.html">PE-10022</a></td>
                                <td>Restaurante La Casa</td>
                                <td>8 productos</td>
                                <td>$876.30</td>
                                <td>14/05/2023</td>
                                <td><button class="btn btn-sm btn-primary">Preparar</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> -->
    </div>
</div>