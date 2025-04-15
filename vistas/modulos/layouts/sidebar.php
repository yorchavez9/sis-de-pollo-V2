<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <?php 
            if(isset($_SESSION["permisos"])){
                $permisos = $_SESSION["permisos"];
            ?>
            <ul>
                <!-- Dashboard -->
                <?php if(isset($permisos["dashboard"])): ?>
                <li class="active">
                    <a href="inicio"><i class="fas fa-home"></i><span>Panel Principal</span></a>
                </li>
                <?php endif; ?>
                
                <!-- Gestión Multisucursal - Solo mostrar si tiene al menos un permiso -->
                <?php if(isset($permisos["sucursales"]) || isset($permisos["almacenes"]) || 
                      isset($permisos["tipoEncomiendas"]) || isset($permisos["envios"]) || 
                      isset($permisos["tarifas"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-network-wired"></i><span>Red de Sucursales</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["sucursales"])): ?>
                        <li><a href="sucursales"><i class="fas fa-store"></i> Sucursales</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["almacenes"])): ?>
                        <li><a href="almacenes"><i class="fas fa-warehouse"></i> Almacenes</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["tipoEncomiendas"])): ?>
                        <li><a href="tipoEncomiendas"><i class="fas fa-truck-moving"></i> Tipo de encomiendas</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["envios"])): ?>
                        <li><a href="envios"><i class="fas fa-truck-moving"></i> Envíos</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["tarifas"])): ?>
                        <li><a href="tarifas"><i class="fas fa-tags"></i> Tarifas de Envío</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Personas -->
                <?php if(isset($permisos["tipoDocumentos"]) || isset($permisos["clientes"]) || 
                      isset($permisos["proveedores"]) || isset($permisos["transportista"]) || 
                      isset($permisos["transporte"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-users"></i><span>Personas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["tipoDocumentos"])): ?>
                        <li><a href="tipoDocumentos"><i class="fas fa-id-card"></i> Tipos de Documento</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["clientes"])): ?>
                        <li><a href="clientes"><i class="fas fa-user-friends"></i> Clientes</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["proveedores"])): ?>
                        <li><a href="proveedores"><i class="fas fa-truck"></i> Proveedores</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["transportista"])): ?>
                        <li><a href="transportista"><i class="fas fa-truck-pickup"></i> Transportistas</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["transporte"])): ?>
                        <li><a href="transporte"><i class="fas fa-truck-pickup"></i> Transporte</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Usuarios y Seguridad -->
                <?php if(isset($permisos["usuarios"]) || isset($permisos["roles"]) || 
                      isset($permisos["permisos"]) || isset($permisos["bitacora"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-user-shield"></i><span>Seguridad</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["usuarios"])): ?>
                        <li><a href="usuarios"><i class="fas fa-users-cog"></i> Usuarios</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["roles"])): ?>
                        <li><a href="roles"><i class="fas fa-user-tag"></i> Roles</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["permisos"])): ?>
                        <li><a href="permisos"><i class="fas fa-key"></i> Permisos</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["bitacora"])): ?>
                        <li><a href="bitacora"><i class="fas fa-clipboard-list"></i> Bitácora</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Inventario y Productos -->
                <?php if(isset($permisos["categorias"]) || isset($permisos["productos"]) || 
                      isset($permisos["inventario"]) || isset($permisos["codigoBarra"]) || 
                      isset($permisos["lotes-pollos"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-boxes"></i><span>Inventario</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["categorias"])): ?>
                        <li><a href="categorias"><i class="fas fa-tags"></i> Categorías</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["productos"])): ?>
                        <li><a href="productos"><i class="fas fa-box-open"></i> Productos</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["inventario"])): ?>
                        <li><a href="inventario"><i class="fas fa-clipboard-check"></i> Control Inventario</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["codigoBarra"])): ?>
                        <li><a href="codigoBarra"><i class="fas fa-barcode"></i> Códigos de Barras</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["lotes-pollos"])): ?>
                        <li><a href="lotes-pollos"><i class="fas fa-kiwi-bird"></i> Lotes de Pollos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Procesamiento de Pollos -->
                <?php if(isset($permisos["procesamiento"]) || isset($permisos["detalle-procesamiento"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-utensils"></i><span>Procesamiento</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["procesamiento"])): ?>
                        <li><a href="procesamiento"><i class="fas fa-mortar-pestle"></i> Procesar Pollos</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["detalle-procesamiento"])): ?>
                        <li><a href="detalle-procesamiento"><i class="fas fa-clipboard-list"></i> Detalle Procesamiento</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Continúa con el mismo patrón para las demás secciones -->
                <!-- Ventas -->
                <?php if(isset($permisos["ventas"]) || isset($permisos["cotizacion"]) || 
                      isset($permisos["comprobantes"]) || isset($permisos["series-comprobantes"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-cash-register"></i><span>Ventas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["ventas"])): ?>
                        <li><a href="ventas"><i class="fas fa-shopping-cart"></i> Punto de Venta</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["cotizacion"])): ?>
                        <li><a href="cotizacion"><i class="fas fa-file-invoice-dollar"></i> Cotizaciones</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["comprobantes"])): ?>
                        <li><a href="comprobantes"><i class="fas fa-receipt"></i> Comprobantes</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["series-comprobantes"])): ?>
                        <li><a href="series-comprobantes"><i class="fas fa-list-ol"></i> Series</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Compras -->
                <?php if(isset($permisos["compras"]) || isset($permisos["listaCompras"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-shopping-basket"></i><span>Compras</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["compras"])): ?>
                        <li><a href="compras"><i class="fas fa-cart-plus"></i> Registrar Compra</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["listaCompras"])): ?>
                        <li><a href="listaCompras"><i class="fas fa-list"></i> Historial Compras</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Gestión de Caja -->
                <?php if(isset($permisos["cajas"]) || isset($permisos["apertura-cierre"]) || 
                      isset($permisos["arqueosCaja"]) || isset($permisos["movimientos-caja"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-cash-register"></i><span>Caja</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["cajas"])): ?>
                        <li><a href="cajas"><i class="fas fa-cash-register"></i> Cajas</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["apertura-cierre"])): ?>
                        <li><a href="apertura-cierre"><i class="fas fa-door-open"></i> Apertura/Cierre</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["arqueosCaja"])): ?>
                        <li><a href="arqueosCaja"><i class="fas fa-calculator"></i> Arqueos</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["movimientos-caja"])): ?>
                        <li><a href="movimientos-caja"><i class="fas fa-exchange-alt"></i> Movimientos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Trabajadores -->
                <?php if(isset($permisos["trabajadores"]) || isset($permisos["contratos"]) || 
                      isset($permisos["asistencias"]) || isset($permisos["pagos-trabajadores"]) || 
                      isset($permisos["vacaciones"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-user-tie"></i><span>Trabajadores</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["trabajadores"])): ?>
                        <li><a href="trabajadores"><i class="fas fa-users"></i> Trabajadores</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["contratos"])): ?>
                        <li><a href="contratos"><i class="fas fa-file-signature"></i> Contratos</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["asistencias"])): ?>
                        <li><a href="asistencias"><i class="fas fa-calendar-check"></i> Asistencias</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["pagos-trabajadores"])): ?>
                        <li><a href="pagos-trabajadores"><i class="fas fa-money-bill-wave"></i> Pagos</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["vacaciones"])): ?>
                        <li><a href="vacaciones"><i class="fas fa-umbrella-beach"></i> Vacaciones</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Reportes -->
                <?php if(isset($permisos["reporte-ventas"]) || isset($permisos["reporte-compras"]) || 
                      isset($permisos["reporte-inventario"]) || isset($permisos["reporte-caja"]) || 
                      isset($permisos["reporte-trabajadores"]) || isset($permisos["reporte-envios"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-chart-bar"></i><span>Reportes</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["reporte-ventas"])): ?>
                        <li><a href="reporte-ventas"><i class="fas fa-chart-line"></i> Ventas</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["reporte-compras"])): ?>
                        <li><a href="reporte-compras"><i class="fas fa-chart-pie"></i> Compras</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["reporte-inventario"])): ?>
                        <li><a href="reporte-inventario"><i class="fas fa-boxes"></i> Inventario</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["reporte-caja"])): ?>
                        <li><a href="reporte-caja"><i class="fas fa-cash-register"></i> Caja</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["reporte-trabajadores"])): ?>
                        <li><a href="reporte-trabajadores"><i class="fas fa-user-tie"></i> Trabajadores</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["reporte-envios"])): ?>
                        <li><a href="reporte-envios"><i class="fas fa-truck-moving"></i> Envíos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Configuración -->
                <?php if(isset($permisos["configuracion-sistema"]) || isset($permisos["tipoComprobantes"]) || 
                      isset($permisos["serieComprobante"]) || isset($permisos["configuracion-correo"])): ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-cog"></i><span>Configuración</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if(isset($permisos["configuracion-sistema"])): ?>
                        <li><a href="configuracion-sistema"><i class="fas fa-sliders-h"></i> Sistema</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["tipoComprobantes"])): ?>
                        <li><a href="tipoComprobantes"><i class="fas fa-ticket-alt"></i> Comprobantes</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["serieComprobante"])): ?>
                        <li><a href="serieComprobante"><i class="fas fa-list-ol"></i> Folios</a></li>
                        <?php endif; ?>
                        
                        <?php if(isset($permisos["configuracion-correo"])): ?>
                        <li><a href="configuracion-correo"><i class="fas fa-envelope"></i> Correo</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <?php
            }
            ?>
        </div>
    </div>
</div>