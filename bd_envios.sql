-- --------------------------------------------------------
-- Estructura de la base de datos completa
-- Incluye: 
-- 1. Gestión multisucursal
-- 2. Control de inventario y productos
-- 3. Ventas y compras
-- 4. Gestión de usuarios y permisos
-- 5. Módulo completo de envíos entre sucursales
-- 6. Procesamiento especializado de pollos
-- 7. Configuraciones del sistema
-- --------------------------------------------------------

-- Configuración SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Tablas principales del sistema
CREATE TABLE `sucursales` (
  `id_sucursal` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sucursal`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `almacenes` (
  `id_almacen` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('PRINCIPAL','SECUNDARIO','REFRI','CONGELACION') DEFAULT 'PRINCIPAL',
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_almacen`),
  KEY `id_sucursal` (`id_sucursal`),
  CONSTRAINT `almacenes_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tipo_documentos` (
  `id_tipo_documento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  `longitud` int(11) DEFAULT NULL,
  `es_empresa` tinyint(1) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_tipo_documento`),
  UNIQUE KEY `nombre` (`nombre`),
  UNIQUE KEY `abreviatura` (`abreviatura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `personas` (
  `id_persona` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_persona` enum('CLIENTE','PROVEEDOR','TRABAJADOR','TRANSPORTISTA') NOT NULL,
  `id_tipo_documento` int(11) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `razon_social` varchar(150) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_persona`),
  UNIQUE KEY `tipo_doc_numero` (`tipo_persona`,`id_tipo_documento`,`numero_documento`),
  KEY `id_tipo_documento` (`id_tipo_documento`),
  CONSTRAINT `personas_ibfk_1` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documentos` (`id_tipo_documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('POLLO','PRODUCTO','INSUMO') DEFAULT 'POLLO',
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `id_categoria` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidad_medida` enum('UNIDAD','KILOGRAMO','GRAMO','LITRO','MILILITRO','CAJA','BOLSA') DEFAULT 'UNIDAD',
  `peso_promedio` decimal(10,3) DEFAULT NULL COMMENT 'Para pollos',
  `precio_compra` decimal(10,2) DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL,
  `tiene_iva` tinyint(1) DEFAULT 1,
  `imagen` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `codigo_barras` (`codigo_barras`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `stock_minimo` decimal(10,3) DEFAULT 0.000,
  `stock_maximo` decimal(10,3) DEFAULT NULL,
  `ultima_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_inventario`),
  UNIQUE KEY `producto_almacen` (`id_producto`,`id_almacen`),
  KEY `id_almacen` (`id_almacen`),
  CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`id_almacen`) REFERENCES `almacenes` (`id_almacen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `movimientos_inventario` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_inventario` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `stock_anterior` decimal(10,3) NOT NULL,
  `stock_nuevo` decimal(10,3) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movimiento`),
  KEY `id_inventario` (`id_inventario`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id_inventario`),
  CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_acceso` int(11) DEFAULT 1,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_persona` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `ultimo_login` datetime DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `id_sucursal` (`id_sucursal`),
  KEY `id_persona` (`id_persona`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `usuario_roles` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`,`id_rol`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuario_roles_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `usuario_roles_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `ruta` varchar(100) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_modulo`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `acciones` (
  `id_accion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_accion`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `permisos` (
  `id_rol` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_accion` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_rol`,`id_modulo`,`id_accion`),
  KEY `id_modulo` (`id_modulo`),
  KEY `id_accion` (`id_accion`),
  CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  CONSTRAINT `permisos_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`),
  CONSTRAINT `permisos_ibfk_3` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablas de operaciones comerciales
CREATE TABLE `tipo_comprobantes` (
  `id_tipo_comprobante` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `serie_obligatoria` tinyint(1) DEFAULT 1,
  `numero_obligatorio` tinyint(1) DEFAULT 1,
  `afecta_inventario` tinyint(1) DEFAULT 1,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_tipo_comprobante`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `series_comprobantes` (
  `id_serie` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(11) NOT NULL,
  `id_tipo_comprobante` int(11) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `numero_inicial` int(11) NOT NULL,
  `numero_actual` int(11) NOT NULL,
  `numero_final` int(11) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_serie`),
  UNIQUE KEY `sucursal_tipo_serie` (`id_sucursal`,`id_tipo_comprobante`,`serie`),
  KEY `id_tipo_comprobante` (`id_tipo_comprobante`),
  CONSTRAINT `series_comprobantes_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `series_comprobantes_ibfk_2` FOREIGN KEY (`id_tipo_comprobante`) REFERENCES `tipo_comprobantes` (`id_tipo_comprobante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(11) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `numero_comprobante` varchar(10) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_venta` datetime NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `impuesto` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `tipo_pago` enum('CONTADO','CREDITO') DEFAULT 'CONTADO',
  `estado_pago` enum('PENDIENTE','PARCIAL','PAGADO') DEFAULT 'PAGADO',
  `estado` enum('BORRADOR','PENDIENTE','COMPLETADO','ANULADO') DEFAULT 'BORRADOR',
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_venta`),
  UNIQUE KEY `serie_numero` (`id_serie`,`numero_comprobante`),
  KEY `id_sucursal` (`id_sucursal`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_serie`) REFERENCES `series_comprobantes` (`id_serie`),
  CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`id_cliente`) REFERENCES `personas` (`id_persona`),
  CONSTRAINT `ventas_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detalle_venta` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `impuesto` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_venta` (`id_venta`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`),
  CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pagos_venta` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `metodo_pago` enum('EFECTIVO','TARJETA','TRANSFERENCIA','YAPE','PLIN','OTRO') NOT NULL,
  `referencia` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id_venta` (`id_venta`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `pagos_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`),
  CONSTRAINT `pagos_venta_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(11) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `numero_comprobante` varchar(10) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_compra` datetime NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `impuesto` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('BORRADOR','PENDIENTE','COMPLETADO','ANULADO') DEFAULT 'BORRADOR',
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_compra`),
  UNIQUE KEY `serie_numero` (`id_serie`,`numero_comprobante`),
  KEY `id_sucursal` (`id_sucursal`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`id_serie`) REFERENCES `series_comprobantes` (`id_serie`),
  CONSTRAINT `compras_ibfk_3` FOREIGN KEY (`id_proveedor`) REFERENCES `personas` (`id_persona`),
  CONSTRAINT `compras_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detalle_compra` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `impuesto` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_compra` (`id_compra`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`),
  CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablas de gestión de caja
CREATE TABLE `cajas` (
  `id_caja` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_caja`),
  KEY `id_sucursal` (`id_sucursal`),
  CONSTRAINT `cajas_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `aperturas_caja` (
  `id_apertura` int(11) NOT NULL AUTO_INCREMENT,
  `id_caja` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_final` decimal(10,2) DEFAULT NULL,
  `monto_esperado` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` enum('ABIERTA','CERRADA','PENDIENTE') DEFAULT 'ABIERTA',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_apertura`),
  KEY `id_caja` (`id_caja`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `aperturas_caja_ibfk_1` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id_caja`),
  CONSTRAINT `aperturas_caja_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `movimientos_caja` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_apertura` int(11) NOT NULL,
  `tipo_movimiento` enum('INGRESO','EGRESO') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_referencia` int(11) DEFAULT NULL,
  `tipo_referencia` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_movimiento` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movimiento`),
  KEY `id_apertura` (`id_apertura`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`id_apertura`) REFERENCES `aperturas_caja` (`id_apertura`),
  CONSTRAINT `movimientos_caja_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablas de gestión de trabajadores
CREATE TABLE `trabajadores` (
  `id_trabajador` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_cese` date DEFAULT NULL,
  `motivo_cese` text DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_trabajador`),
  UNIQUE KEY `id_persona` (`id_persona`),
  KEY `id_sucursal` (`id_sucursal`),
  CONSTRAINT `trabajadores_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`),
  CONSTRAINT `trabajadores_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `contratos_trabajadores` (
  `id_contrato` int(11) NOT NULL AUTO_INCREMENT,
  `id_trabajador` int(11) NOT NULL,
  `tipo_contrato` enum('FIJO','TEMPORAL','PRACTICAS','OTRO') NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `sueldo_base` decimal(10,2) NOT NULL,
  `moneda` enum('PEN','USD') DEFAULT 'PEN',
  `horario` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_contrato`),
  KEY `id_trabajador` (`id_trabajador`),
  CONSTRAINT `contratos_trabajadores_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `asistencias` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_trabajador` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `estado` enum('ASISTIO','TARDANZA','FALTA','VACACIONES','PERMISO') DEFAULT 'ASISTIO',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_asistencia`),
  UNIQUE KEY `trabajador_fecha` (`id_trabajador`,`fecha`),
  CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pagos_trabajadores` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_trabajador` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `periodo` varchar(20) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `sueldo_base` decimal(10,2) NOT NULL,
  `bonificaciones` decimal(10,2) DEFAULT 0.00,
  `descuentos` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` enum('EFECTIVO','TRANSFERENCIA','OTRO') DEFAULT 'EFECTIVO',
  `estado` enum('PENDIENTE','PAGADO','ANULADO') DEFAULT 'PENDIENTE',
  `observaciones` text DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pago`),
  KEY `id_trabajador` (`id_trabajador`),
  KEY `id_contrato` (`id_contrato`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `pagos_trabajadores_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`),
  CONSTRAINT `pagos_trabajadores_ibfk_2` FOREIGN KEY (`id_contrato`) REFERENCES `contratos_trabajadores` (`id_contrato`),
  CONSTRAINT `pagos_trabajadores_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;






















-- Tablas para gestión de envíos entre sucursales (Módulo completo)
CREATE TABLE `tipo_encomiendas` (
  `id_tipo_encomienda` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `requiere_confirmacion` tinyint(1) DEFAULT 1,
  `prioridad` enum('BAJA','MEDIA','ALTA','URGENTE') DEFAULT 'MEDIA',
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_tipo_encomienda`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transportistas` (
  `id_transportista` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL,
  `tipo_vehiculo` varchar(50) DEFAULT NULL,
  `placa_vehiculo` varchar(20) DEFAULT NULL,
  `telefono_contacto` varchar(20) NOT NULL,
  `fecha_registro` date NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_transportista`),
  UNIQUE KEY `id_persona` (`id_persona`),
  CONSTRAINT `transportistas_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `envios` (
  `id_envio` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_envio` varchar(20) NOT NULL,
  `id_sucursal_origen` int(11) NOT NULL,
  `id_sucursal_destino` int(11) NOT NULL,
  `id_tipo_encomienda` int(11) NOT NULL,
  `id_usuario_creador` int(11) NOT NULL,
  `id_usuario_receptor` int(11) DEFAULT NULL,
  `id_transportista` int(11) DEFAULT NULL,
  `dni_remitente` varchar(20) DEFAULT NULL,             /* Campo nuevo */
  `nombre_remitente` varchar(100) DEFAULT NULL,         /* Campo nuevo */
  `dni_destinatario` varchar(20) DEFAULT NULL,          /* Campo nuevo */
  `nombre_destinatario` varchar(100) DEFAULT NULL,      /* Campo nuevo */
  `clave_recepcion` varchar(50) DEFAULT NULL,           /* Campo nuevo */
  `fecha_creacion` datetime NOT NULL,
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_recepcion` datetime DEFAULT NULL,
  `fecha_estimada_entrega` datetime DEFAULT NULL,
  `peso_total` decimal(10,2) DEFAULT 0.00,
  `volumen_total` decimal(10,2) DEFAULT 0.00,
  `cantidad_paquetes` int(11) DEFAULT 1,
  `instrucciones` text DEFAULT NULL,
  `estado` enum('PENDIENTE','PREPARACION','EN_TRANSITO','EN_REPARTO','ENTREGADO','CANCELADO','RECHAZADO') DEFAULT 'PENDIENTE',
  `motivo_rechazo` text DEFAULT NULL,
  `costo_envio` decimal(10,2) DEFAULT 0.00,
  `metodo_pago` enum('EFECTIVO','CREDITO','POR_COBRAR') DEFAULT 'EFECTIVO',
  `fecha_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_envio`),
  UNIQUE KEY `codigo_envio` (`codigo_envio`),
  KEY `id_sucursal_origen` (`id_sucursal_origen`),
  KEY `id_sucursal_destino` (`id_sucursal_destino`),
  KEY `id_tipo_encomienda` (`id_tipo_encomienda`),
  KEY `id_usuario_creador` (`id_usuario_creador`),
  KEY `id_usuario_receptor` (`id_usuario_receptor`),
  KEY `id_transportista` (`id_transportista`),
  CONSTRAINT `envios_ibfk_1` FOREIGN KEY (`id_sucursal_origen`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `envios_ibfk_2` FOREIGN KEY (`id_sucursal_destino`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `envios_ibfk_3` FOREIGN KEY (`id_tipo_encomienda`) REFERENCES `tipo_encomiendas` (`id_tipo_encomienda`),
  CONSTRAINT `envios_ibfk_4` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `envios_ibfk_5` FOREIGN KEY (`id_usuario_receptor`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `envios_ibfk_6` FOREIGN KEY (`id_transportista`) REFERENCES `transportistas` (`id_transportista`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `paquetes` (
  `id_paquete` int(11) NOT NULL AUTO_INCREMENT,
  `id_envio` int(11) NOT NULL,
  `codigo_paquete` varchar(20) NOT NULL,
  `descripcion` text NOT NULL,
  `peso` decimal(10,2) NOT NULL,
  `alto` decimal(10,2) DEFAULT NULL,
  `ancho` decimal(10,2) DEFAULT NULL,
  `profundidad` decimal(10,2) DEFAULT NULL,
  `volumen` decimal(10,2) GENERATED ALWAYS AS (`alto` * `ancho` * `profundidad`) STORED,
  `valor_declarado` decimal(10,2) DEFAULT 0.00,
  `instrucciones_manejo` text DEFAULT NULL,
  `estado` enum('BUENO','DANADO','PERDIDO','ENTREGADO') DEFAULT 'BUENO',
  PRIMARY KEY (`id_paquete`),
  UNIQUE KEY `codigo_paquete` (`codigo_paquete`),
  KEY `id_envio` (`id_envio`),
  CONSTRAINT `paquetes_ibfk_1` FOREIGN KEY (`id_envio`) REFERENCES `envios` (`id_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `items_paquete` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_paquete` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `descripcion` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `peso_unitario` decimal(10,2) DEFAULT NULL,
  `valor_unitario` decimal(10,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_item`),
  KEY `id_paquete` (`id_paquete`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `items_paquete_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes` (`id_paquete`),
  CONSTRAINT `items_paquete_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `seguimiento_envios` (
  `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_envio` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_seguimiento`),
  KEY `id_envio` (`id_envio`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `seguimiento_envios_ibfk_1` FOREIGN KEY (`id_envio`) REFERENCES `envios` (`id_envio`),
  CONSTRAINT `seguimiento_envios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `documentos_envios` (
  `id_documento` int(11) NOT NULL AUTO_INCREMENT,
  `id_envio` int(11) NOT NULL,
  `tipo_documento` enum('FOTO','FACTURA','GUIA','OTRO') NOT NULL,
  `nombre_archivo` varchar(100) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_documento`),
  KEY `id_envio` (`id_envio`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `documentos_envios_ibfk_1` FOREIGN KEY (`id_envio`) REFERENCES `envios` (`id_envio`),
  CONSTRAINT `documentos_envios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `tarifas_envio` (
  `id_tarifa` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal_origen` int(11) NOT NULL,
  `id_sucursal_destino` int(11) NOT NULL,
  `id_tipo_encomienda` int(11) NOT NULL,
  `rango_peso_min` decimal(10,2) NOT NULL,
  `rango_peso_max` decimal(10,2) NOT NULL,
  `costo_base` decimal(10,2) NOT NULL,
  `costo_kg_extra` decimal(10,2) DEFAULT 0.00,
  `tiempo_estimado` int(11) DEFAULT NULL COMMENT 'En horas',
  `vigencia_desde` date NOT NULL,
  `vigencia_hasta` date DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_tarifa`),
  KEY `id_sucursal_origen` (`id_sucursal_origen`),
  KEY `id_sucursal_destino` (`id_sucursal_destino`),
  KEY `id_tipo_encomienda` (`id_tipo_encomienda`),
  CONSTRAINT `tarifas_envio_ibfk_1` FOREIGN KEY (`id_sucursal_origen`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `tarifas_envio_ibfk_2` FOREIGN KEY (`id_sucursal_destino`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `tarifas_envio_ibfk_3` FOREIGN KEY (`id_tipo_encomienda`) REFERENCES `tipo_encomiendas` (`id_tipo_encomienda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;







-- Tablas para procesamiento especializado de pollos
CREATE TABLE `lotes_pollos` (
  `id_lote` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_lote` varchar(20) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `cantidad_pollos` int(11) NOT NULL,
  `peso_total` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('ACTIVO','PROCESADO','CANCELADO') DEFAULT 'ACTIVO',
  `id_usuario` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_lote`),
  UNIQUE KEY `codigo_lote` (`codigo_lote`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `lotes_pollos_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `personas` (`id_persona`),
  CONSTRAINT `lotes_pollos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `procesamiento_pollos` (
  `id_procesamiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_lote` int(11) NOT NULL,
  `fecha_procesamiento` date NOT NULL,
  `cantidad_pollos` int(11) NOT NULL,
  `peso_total` decimal(10,2) NOT NULL,
  `rendimiento` decimal(5,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_procesamiento`),
  KEY `id_lote` (`id_lote`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `procesamiento_pollos_ibfk_1` FOREIGN KEY (`id_lote`) REFERENCES `lotes_pollos` (`id_lote`),
  CONSTRAINT `procesamiento_pollos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detalle_procesamiento` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_procesamiento` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `peso` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_procesamiento` (`id_procesamiento`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_procesamiento_ibfk_1` FOREIGN KEY (`id_procesamiento`) REFERENCES `procesamiento_pollos` (`id_procesamiento`),
  CONSTRAINT `detalle_procesamiento_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablas de configuración del sistema
CREATE TABLE `configuracion_sistema` (
  `id_configuracion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(100) NOT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `moneda` varchar(10) DEFAULT 'PEN',
  `impuesto` decimal(5,2) DEFAULT 18.00,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_configuracion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `configuracion_ticket` (
  `id_configuracion` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(11) NOT NULL,
  `cabecera` text DEFAULT NULL,
  `pie_pagina` text DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `ancho_papel` int(11) DEFAULT 80,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_configuracion`),
  KEY `id_sucursal` (`id_sucursal`),
  CONSTRAINT `configuracion_ticket_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `configuracion_correo` (
  `id_configuracion` int(11) NOT NULL AUTO_INCREMENT,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_usuario` varchar(100) NOT NULL,
  `smtp_password` varchar(100) NOT NULL,
  `smtp_puerto` int(11) NOT NULL,
  `smtp_seguridad` enum('SSL','TLS','NONE') DEFAULT 'TLS',
  `correo_remitente` varchar(100) NOT NULL,
  `nombre_remitente` varchar(100) NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_configuracion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tablas de auditoría
CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `id_registro_afectado` int(11) DEFAULT NULL,
  `datos_anteriores` text DEFAULT NULL,
  `datos_nuevos` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(50) DEFAULT NULL,
  `dispositivo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_bitacora`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserción de datos básicos
INSERT INTO `roles` (`nombre`, `descripcion`, `nivel_acceso`, `estado`) VALUES
('ADMINISTRADOR', 'Acceso completo al sistema', 100, 1),
('GERENTE', 'Gestiona operaciones de múltiples sucursales', 80, 1),
('SUPERVISOR', 'Supervisa operaciones en sucursales asignadas', 60, 1),
('VENDEDOR', 'Realiza ventas y operaciones básicas', 40, 1),
('TRANSPORTISTA', 'Encargado de realizar envíos entre sucursales', 30, 1),
('INVENTARIO', 'Gestiona productos y almacenes', 50, 1);

-- Módulos del sistema
INSERT INTO `modulos` (`nombre`, `icono`, `ruta`, `orden`, `estado`) VALUES
('dashboard', 'fa fa-home', 'inicio', 1, 1),
('sucursales', 'fa fa-store', 'sucursales', 2, 1),
('usuarios', 'fa fa-users', 'usuarios', 3, 1),
('roles', 'fa fa-user-tag', 'roles', 4, 1),
('permisos', 'fa fa-key', 'permisos', 5, 1),
('clientes', 'fa fa-user-friends', 'clientes', 6, 1),
('proveedores', 'fa fa-truck', 'proveedores', 7, 1),
('productos', 'fa fa-box-open', 'productos', 8, 1),
('inventario', 'fa fa-warehouse', 'inventario', 9, 1),
('ventas', 'fa fa-shopping-cart', 'ventas', 10, 1),
('compras', 'fa fa-shopping-basket', 'compras', 11, 1),
('caja', 'fa fa-cash-register', 'caja', 12, 1),
('trabajadores', 'fa fa-user-tie', 'trabajadores', 13, 1),
('envios', 'fa fa-truck-moving', 'envios', 14, 1),
('procesamiento', 'fa fa-utensils', 'procesamiento', 15, 1),
('reportes', 'fa fa-chart-bar', 'reportes', 16, 1),
('configuracion', 'fa fa-cog', 'configuracion', 17, 1);

-- Acciones del sistema
INSERT INTO `acciones` (`nombre`, `descripcion`, `estado`) VALUES
('ver', 'Ver registros', 1),
('crear', 'Crear nuevos registros', 1),
('editar', 'Editar registros existentes', 1),
('eliminar', 'Eliminar registros', 1),
('imprimir', 'Imprimir documentos', 1),
('exportar', 'Exportar datos', 1),
('aprobar', 'Aprobar procesos', 1),
('configurar', 'Configurar parámetros', 1),
('reportes', 'Generar reportes', 1);

-- Permisos para administrador (acceso completo)
INSERT INTO `permisos` (`id_rol`, `id_modulo`, `id_accion`)
SELECT r.id_rol, m.id_modulo, a.id_accion
FROM `roles` r, `modulos` m, `acciones` a
WHERE r.nombre = 'ADMINISTRADOR'
AND m.estado = 1
AND a.estado = 1;

-- Configuración inicial del sistema
INSERT INTO `configuracion_sistema` (`nombre_empresa`, `ruc`, `direccion`, `telefono`, `email`, `logo`, `moneda`, `impuesto`) 
VALUES ('Mi Pollería', '20123456789', 'Av. Principal 123', '014567890', 'contacto@mipolleria.com', NULL, 'PEN', 18.00);

-- Tipos de documentos
INSERT INTO `tipo_documentos` (`nombre`, `abreviatura`, `longitud`, `es_empresa`, `estado`) VALUES
('Documento Nacional de Identidad', 'DNI', 8, 0, 1),
('Registro Único de Contribuyentes', 'RUC', 11, 1, 1),
('Carnet de Extranjería', 'CE', 12, 0, 1),
('Pasaporte', 'PAS', NULL, 0, 1);

-- Tipos de encomiendas
INSERT INTO `tipo_encomiendas` (`nombre`, `descripcion`, `requiere_confirmacion`, `prioridad`, `estado`) VALUES
('Productos Perecederos', 'Productos que requieren refrigeración', 1, 'ALTA', 1),
('Productos Secos', 'Productos no perecederos', 0, 'MEDIA', 1),
('Documentos', 'Envío de documentos importantes', 1, 'URGENTE', 1),
('Insumos', 'Materiales y suministros', 0, 'BAJA', 1);

-- Creación de usuario administrador inicial
INSERT INTO `personas` (`tipo_persona`, `id_tipo_documento`, `numero_documento`, `nombre`, `apellidos`, `email`, `estado`) 
VALUES ('TRABAJADOR', 1, '12345678', 'Admin', 'Sistema', 'admin@mipolleria.com', 1);

INSERT INTO `usuarios` (`id_persona`, `nombre_usuario`, `usuario`, `contrasena`, `estado`) 
VALUES (1, 'Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

INSERT INTO `usuario_roles` (`id_usuario`, `id_rol`) 
VALUES (1, 1);