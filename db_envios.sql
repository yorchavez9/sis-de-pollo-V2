-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-04-2025 a las 15:39:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sis_pollo_profesional`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones`
--

CREATE TABLE `acciones` (
  `id_accion` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acciones`
--

INSERT INTO `acciones` (`id_accion`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'ver', 'Ver registros', 1),
(2, 'crear', 'Crear nuevos registros', 1),
(3, 'editar', 'Editar registros existentes', 1),
(4, 'eliminar', 'Eliminar registros', 1),
(5, 'imprimir', 'Imprimir documentos', 1),
(6, 'exportar', 'Exportar datos', 1),
(7, 'aprobar', 'Aprobar procesos', 1),
(8, 'configurar', 'Configurar parámetros', 1),
(9, 'reportes', 'Generar reportes', 1),
(10, 'estado', 'Actualizar los estados', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacenes`
--

CREATE TABLE `almacenes` (
  `id_almacen` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('PRINCIPAL','SECUNDARIO','REFRI','CONGELACION') DEFAULT 'PRINCIPAL',
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `almacenes`
--

INSERT INTO `almacenes` (`id_almacen`, `id_sucursal`, `nombre`, `descripcion`, `tipo`, `estado`) VALUES
(1, 4, 'Almacen Oeste', 'Este es el almacen principal de todo el negocio\r\n', 'PRINCIPAL', 1),
(2, 3, 'Almacen centro', '', 'SECUNDARIO', 1),
(3, 2, 'Almacen Norte', '', 'SECUNDARIO', 1),
(4, 2, 'Almacen norte', '', 'SECUNDARIO', 1),
(5, 1, 'Almacen sur', '', 'SECUNDARIO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aperturas_caja`
--

CREATE TABLE `aperturas_caja` (
  `id_apertura` int(11) NOT NULL,
  `id_caja` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_final` decimal(10,2) DEFAULT NULL,
  `monto_esperado` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` enum('ABIERTA','CERRADA','PENDIENTE') DEFAULT 'ABIERTA',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id_asistencia` int(11) NOT NULL,
  `id_trabajador` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `estado` enum('ASISTIO','TARDANZA','FALTA','VACACIONES','PERMISO') DEFAULT 'ASISTIO',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `id_registro_afectado` int(11) DEFAULT NULL,
  `datos_anteriores` text DEFAULT NULL,
  `datos_nuevos` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `ip` varchar(50) DEFAULT NULL,
  `dispositivo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id_caja` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('POLLO','PRODUCTO','INSUMO') DEFAULT 'POLLO',
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`, `tipo`, `estado`, `fecha_creacion`) VALUES
(1, 'Cementos', 'Cementos Andinos', 'PRODUCTO', 1, '2025-04-14 09:58:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL,
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
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_correo`
--

CREATE TABLE `configuracion_correo` (
  `id_configuracion` int(11) NOT NULL,
  `smtp_host` varchar(100) NOT NULL,
  `smtp_usuario` varchar(100) NOT NULL,
  `smtp_password` varchar(100) NOT NULL,
  `smtp_puerto` int(11) NOT NULL,
  `smtp_seguridad` enum('SSL','TLS','NONE') DEFAULT 'TLS',
  `correo_remitente` varchar(100) NOT NULL,
  `nombre_remitente` varchar(100) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id_configuracion` int(11) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `moneda` varchar(10) DEFAULT 'PEN',
  `impuesto` decimal(5,2) DEFAULT 18.00,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id_configuracion`, `nombre_empresa`, `ruc`, `direccion`, `telefono`, `email`, `logo`, `moneda`, `impuesto`, `fecha_registro`) VALUES
(1, 'Mi negocio', '20123456789', 'Av. Principal 123', '014567890', 'contacto@mipolleria.com', 'vistas/img/sistema/logo_680168d35da20.png', 'PEN', 18.00, '2025-04-12 12:08:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_ticket`
--

CREATE TABLE `configuracion_ticket` (
  `id_configuracion` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `cabecera` text DEFAULT NULL,
  `pie_pagina` text DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `ancho_papel` int(11) DEFAULT 80,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratos_trabajadores`
--

CREATE TABLE `contratos_trabajadores` (
  `id_contrato` int(11) NOT NULL,
  `id_trabajador` int(11) NOT NULL,
  `tipo_contrato` enum('FIJO','TEMPORAL','PRACTICAS','OTRO') NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `sueldo_base` decimal(10,2) NOT NULL,
  `moneda` enum('PEN','USD') DEFAULT 'PEN',
  `horario` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id_detalle` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `impuesto` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_procesamiento`
--

CREATE TABLE `detalle_procesamiento` (
  `id_detalle` int(11) NOT NULL,
  `id_procesamiento` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `peso` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `impuesto` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_envios`
--

CREATE TABLE `documentos_envios` (
  `id_documento` int(11) NOT NULL,
  `id_envio` int(11) NOT NULL,
  `tipo_documento` enum('FOTO','FACTURA','GUIA','OTRO') NOT NULL,
  `nombre_archivo` varchar(100) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios`
--

CREATE TABLE `envios` (
  `id_envio` int(11) NOT NULL,
  `codigo_envio` varchar(20) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `numero_comprobante` int(11) NOT NULL,
  `id_sucursal_origen` int(11) NOT NULL,
  `id_sucursal_destino` int(11) NOT NULL,
  `id_tipo_encomienda` int(11) NOT NULL,
  `id_usuario_creador` int(11) NOT NULL,
  `id_usuario_receptor` int(11) DEFAULT NULL,
  `id_transportista` int(11) DEFAULT NULL,
  `dni_remitente` varchar(20) DEFAULT NULL,
  `nombre_remitente` varchar(100) DEFAULT NULL,
  `dni_destinatario` varchar(20) DEFAULT NULL,
  `nombre_destinatario` varchar(100) DEFAULT NULL,
  `clave_recepcion` varchar(50) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_envio` date NOT NULL DEFAULT current_timestamp(),
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
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `envios`
--

INSERT INTO `envios` (`id_envio`, `codigo_envio`, `id_serie`, `numero_comprobante`, `id_sucursal_origen`, `id_sucursal_destino`, `id_tipo_encomienda`, `id_usuario_creador`, `id_usuario_receptor`, `id_transportista`, `dni_remitente`, `nombre_remitente`, `dni_destinatario`, `nombre_destinatario`, `clave_recepcion`, `fecha_creacion`, `fecha_envio`, `fecha_recepcion`, `fecha_estimada_entrega`, `peso_total`, `volumen_total`, `cantidad_paquetes`, `instrucciones`, `estado`, `motivo_rechazo`, `costo_envio`, `metodo_pago`, `fecha_actualizacion`) VALUES
(1, 'ENV2504123247', 1, 1, 1, 2, 4, 1, NULL, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '123', '2025-04-12 12:30:49', '2025-04-15', '2025-04-15 09:03:59', '2025-04-13 12:29:00', 45.00, 0.34, 1, 'Llevar con mucho cuidado', 'EN_REPARTO', NULL, 0.00, 'EFECTIVO', '2025-04-17 17:18:50'),
(3, 'ENV2504152024', 1, 3, 1, 2, 4, 1, 1, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '722435', '2025-04-15 09:26:33', '2025-04-15', '2025-04-15 09:30:24', '2025-04-16 09:25:00', 40.00, 0.20, 1, 'dfasfdaf', 'ENTREGADO', NULL, 0.00, 'EFECTIVO', '2025-04-15 09:30:24'),
(4, 'ENV2504153098', 2, 1, 1, 2, 4, 1, NULL, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '123123', '2025-04-15 09:42:56', '2025-04-15', NULL, '2025-04-16 09:41:00', 20.00, 0.00, 1, 'Ninguno', 'PENDIENTE', NULL, 0.00, 'EFECTIVO', '2025-04-15 09:42:56'),
(5, 'ENV2504159261', 1, 4, 1, 2, 4, 1, NULL, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '34554', '2025-04-15 09:45:50', '2025-04-15', NULL, '2025-04-16 09:44:00', 30.00, 0.50, 1, 'Ninguno', 'PREPARACION', NULL, 0.00, 'EFECTIVO', '2025-04-15 09:58:19'),
(6, 'ENV2504156159', 1, 5, 1, 2, 4, 1, NULL, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '722435', '2025-04-15 10:04:19', '2025-04-15', NULL, '2025-04-16 10:03:00', 50.00, 0.50, 1, 'Ninguno', 'PENDIENTE', NULL, 200.00, 'POR_COBRAR', '2025-04-15 10:19:18'),
(7, 'ENV2504156463', 1, 6, 1, 2, 4, 1, 1, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '722435', '2025-04-15 10:25:18', '2025-04-15', '2025-04-17 12:42:06', '2025-04-16 10:24:00', 20.00, 0.10, 1, 'Ninguno', 'ENTREGADO', NULL, 0.00, 'EFECTIVO', '2025-04-17 12:42:06'),
(8, 'ENV2504155427', 1, 7, 1, 2, 4, 1, NULL, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '722435', '2025-04-15 10:30:50', '2025-04-15', NULL, '2025-04-16 10:29:00', 50.00, 1.00, 1, 'Ninguno', 'EN_REPARTO', NULL, 15.00, 'CREDITO', '2025-04-15 11:33:40'),
(9, 'ENV2504156068', 3, 1, 4, 1, 4, 2, NULL, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '12345', '2025-04-15 12:34:58', '2025-04-15', NULL, '2025-04-15 12:33:00', 45.00, 0.12, 1, '', 'EN_TRANSITO', NULL, 240.00, 'EFECTIVO', '2025-04-15 12:36:40'),
(11, 'ENV2504155418', 3, 2, 4, 1, 4, 2, NULL, 2, '72243561', 'Jorge Chavez Huincho', '34471483', 'Daniel Chavez Martinez', '722435', '2025-04-15 12:40:57', '2025-04-15', NULL, '2025-04-15 12:39:00', 80.00, 0.50, 1, '', 'PENDIENTE', NULL, 20.00, 'EFECTIVO', '2025-04-15 12:40:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `stock_minimo` decimal(10,3) DEFAULT 0.000,
  `stock_maximo` decimal(10,3) DEFAULT NULL,
  `ultima_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_inventario`, `id_producto`, `id_almacen`, `stock`, `stock_minimo`, `stock_maximo`, `ultima_actualizacion`) VALUES
(6, 1, 5, 20.000, 20.000, 500.000, '2025-04-17 11:27:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items_paquete`
--

CREATE TABLE `items_paquete` (
  `id_item` int(11) NOT NULL,
  `id_paquete` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `descripcion` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `peso_unitario` decimal(10,2) DEFAULT NULL,
  `valor_unitario` decimal(10,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_pollos`
--

CREATE TABLE `lotes_pollos` (
  `id_lote` int(11) NOT NULL,
  `codigo_lote` varchar(20) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `cantidad_pollos` int(11) NOT NULL,
  `peso_total` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('ACTIVO','PROCESADO','CANCELADO') DEFAULT 'ACTIVO',
  `id_usuario` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `ruta` varchar(100) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nombre`, `icono`, `ruta`, `orden`, `estado`) VALUES
(1, 'dashboard', 'fa fa-home', 'inicio', 1, 1),
(2, 'sucursales', 'fa fa-store', 'sucursales', 2, 1),
(3, 'usuarios', 'fa fa-users', 'usuarios', 3, 1),
(4, 'roles', 'fa fa-user-tag', 'roles', 4, 1),
(5, 'permisos', 'fa fa-key', 'permisos', 5, 1),
(6, 'clientes', 'fa fa-user-friends', 'clientes', 6, 1),
(7, 'proveedores', 'fa fa-truck', 'proveedores', 7, 1),
(8, 'productos', 'fa fa-box-open', 'productos', 8, 1),
(9, 'inventario', 'fa fa-warehouse', 'inventario', 9, 1),
(10, 'ventas', 'fa fa-shopping-cart', 'ventas', 10, 1),
(11, 'compras', 'fa fa-shopping-basket', 'compras', 11, 1),
(12, 'caja', 'fa fa-cash-register', 'caja', 12, 1),
(13, 'trabajadores', 'fa fa-user-tie', 'trabajadores', 13, 1),
(14, 'envios', 'fa fa-truck-moving', 'envios', 14, 1),
(15, 'procesamiento', 'fa fa-utensils', 'procesamiento', 15, 1),
(16, 'reportes', 'fa fa-chart-bar', 'reportes', 16, 1),
(17, 'configuracion', 'fa fa-cog', 'configuracion', 17, 1),
(18, 'almacenes', 'fas fa-warehouse', 'almacenes', 18, 1),
(19, 'tipoEncomiendas', 'fas fa-truck-moving', 'tipoEncomiendas', 19, 1),
(20, 'tarifas', 'fas fa-tags', 'tarifas', 20, 1),
(21, 'tipoDocumentos', 'fas fa-id-card', 'tipoDocumentos', 21, 1),
(22, 'transportista', 'fas fa-truck-pickup', 'transportista', 22, 1),
(23, 'transporte', 'fas fa-truck-pickup', 'transporte', 23, 1),
(24, 'bitacora', 'fas fa-clipboard-list', 'bitacora', 24, 1),
(25, 'categorias', 'fas fa-tags', 'categorias', 25, 1),
(26, 'codigoBarra', 'fas fa-barcode', 'codigoBarra', 26, 1),
(27, 'lotes-pollos', 'fas fa-kiwi-bird', 'lotes-pollos', 27, 1),
(28, 'detalle-procesamiento', 'fas fa-clipboard-list', 'detalle-procesamiento', 28, 1),
(29, 'cotizacion', 'fas fa-file-invoice-dollar', 'cotizacion', 29, 1),
(30, 'comprobantes', 'fas fa-receipt', 'comprobantes', 30, 1),
(31, 'series-comprobantes', 'fas fa-list-ol', 'series-comprobantes', 31, 1),
(32, 'listaCompras', 'fas fa-list', 'listaCompras', 32, 1),
(33, 'cajas', 'fas fa-cash-register', 'cajas', 33, 1),
(34, 'apertura-cierre', 'fas fa-door-open', 'apertura-cierre', 34, 1),
(35, 'arqueosCaja', 'fas fa-calculator', 'arqueosCaja', 35, 1),
(36, 'movimientos-caja', 'fas fa-exchange-alt', 'movimientos-caja', 36, 1),
(37, 'contratos', 'fas fa-file-signature', 'contratos', 37, 1),
(38, 'asistencias', 'fas fa-calendar-check', 'asistencias', 38, 1),
(39, 'pagos-trabajadores', 'fas fa-money-bill-wave', 'pagos-trabajadores', 39, 1),
(40, 'vacaciones', 'fas fa-umbrella-beach', 'vacaciones', 40, 1),
(41, 'reporte-ventas', 'fas fa-chart-line', 'reporte-ventas', 41, 1),
(42, 'reporte-compras', 'fas fa-chart-pie', 'reporte-compras', 42, 1),
(43, 'reporte-inventario', 'fas fa-boxes', 'reporte-inventario', 43, 1),
(44, 'reporte-caja', 'fas fa-cash-register', 'reporte-caja', 44, 1),
(45, 'reporte-trabajadores', 'fas fa-user-tie', 'reporte-trabajadores', 45, 1),
(46, 'reporte-envios', 'fas fa-truck-moving', 'reporte-envios', 46, 1),
(47, 'configuracion-sistema', 'fas fa-sliders-h', 'configuracion-sistema', 47, 1),
(48, 'tipoComprobantes', 'fas fa-ticket-alt', 'tipoComprobantes', 48, 1),
(49, 'serieComprobante', 'fas fa-list-ol', 'serieComprobante', 49, 1),
(50, 'configuracion-correo', 'fas fa-envelope', 'configuracion-correo', 50, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `id_movimiento` int(11) NOT NULL,
  `id_apertura` int(11) NOT NULL,
  `tipo_movimiento` enum('INGRESO','EGRESO') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_referencia` int(11) DEFAULT NULL,
  `tipo_referencia` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_movimiento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id_movimiento` int(11) NOT NULL,
  `id_inventario` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `stock_anterior` decimal(10,3) NOT NULL,
  `stock_nuevo` decimal(10,3) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_inventario`
--

INSERT INTO `movimientos_inventario` (`id_movimiento`, `id_inventario`, `tipo_movimiento`, `cantidad`, `stock_anterior`, `stock_nuevo`, `motivo`, `id_usuario`, `fecha`) VALUES
(10, 6, 'entrada', 2000.000, 0.000, 2000.000, 'Compra del cemento', 1, '2025-04-17 11:26:52'),
(11, 6, 'ajuste', 20.000, 2000.000, 20.000, 'Agregando el cemento de loq ue se presto', 1, '2025-04-17 11:27:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_trabajadores`
--

CREATE TABLE `pagos_trabajadores` (
  `id_pago` int(11) NOT NULL,
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
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_venta`
--

CREATE TABLE `pagos_venta` (
  `id_pago` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `metodo_pago` enum('EFECTIVO','TARJETA','TRANSFERENCIA','YAPE','PLIN','OTRO') NOT NULL,
  `referencia` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes`
--

CREATE TABLE `paquetes` (
  `id_paquete` int(11) NOT NULL,
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
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetes`
--

INSERT INTO `paquetes` (`id_paquete`, `id_envio`, `codigo_paquete`, `descripcion`, `peso`, `alto`, `ancho`, `profundidad`, `valor_declarado`, `instrucciones_manejo`, `estado`, `fecha_creacion`) VALUES
(1, 1, 'PKG2504123247-001', 'Saco de azucar', 45.00, 120.00, 70.00, 40.00, 0.00, 'Llevar con mucho cuidado', 'BUENO', '2025-04-12 12:30:49'),
(3, 3, 'PKG2504152024-001', 'Saco de arrooz', 40.00, 100.00, 50.00, 40.00, 0.00, 'Llevar con cuidado', 'BUENO', '2025-04-15 09:26:33'),
(4, 4, 'PKG2504153098-001', 'Saco de maiz', 20.00, 10.00, 10.00, 10.00, 0.00, 'Ninguno', 'BUENO', '2025-04-15 09:42:56'),
(5, 5, 'PKG2504159261-001', 'Ninguno', 30.00, 100.00, 100.00, 50.00, 0.00, 'Ninguno', 'BUENO', '2025-04-15 09:45:50'),
(6, 6, 'PKG2504156159-001', 'Saco de cebolla', 50.00, 100.00, 100.00, 50.00, 0.00, 'Ninguno', 'BUENO', '2025-04-15 10:04:19'),
(7, 7, 'PKG2504156463-001', 'Saco de papel', 20.00, 200.00, 100.00, 5.00, 0.00, 'Ninguno', 'BUENO', '2025-04-15 10:25:18'),
(8, 8, 'PKG2504155427-001', 'Saco de azucar', 50.00, 100.00, 200.00, 50.00, 0.00, 'Ninguno', 'BUENO', '2025-04-15 10:30:50'),
(9, 9, 'PKG2504156068-001', 'Saco de arina', 45.00, 100.00, 40.00, 30.00, 0.00, '', 'BUENO', '2025-04-15 12:34:58'),
(10, 11, 'PKG2504155418-001', 'Costal de papa', 80.00, 100.00, 100.00, 50.00, 0.00, '', 'BUENO', '2025-04-15 12:40:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_rol` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_accion` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_rol`, `id_modulo`, `id_accion`, `fecha_asignacion`) VALUES
(1, 1, 1, '2025-04-17 11:03:33'),
(1, 1, 2, '2025-04-17 11:03:33'),
(1, 1, 3, '2025-04-17 11:03:33'),
(1, 1, 4, '2025-04-17 11:03:33'),
(1, 1, 5, '2025-04-17 11:03:33'),
(1, 1, 6, '2025-04-17 11:03:33'),
(1, 1, 7, '2025-04-17 11:03:33'),
(1, 1, 8, '2025-04-17 11:03:33'),
(1, 1, 9, '2025-04-17 11:03:33'),
(1, 1, 10, '2025-04-17 11:03:33'),
(1, 2, 1, '2025-04-17 11:03:33'),
(1, 2, 2, '2025-04-17 11:03:33'),
(1, 2, 3, '2025-04-17 11:03:33'),
(1, 2, 4, '2025-04-17 11:03:33'),
(1, 2, 5, '2025-04-17 11:03:33'),
(1, 2, 6, '2025-04-17 11:03:33'),
(1, 2, 7, '2025-04-17 11:03:33'),
(1, 2, 8, '2025-04-17 11:03:33'),
(1, 2, 9, '2025-04-17 11:03:33'),
(1, 2, 10, '2025-04-17 11:03:33'),
(1, 3, 1, '2025-04-17 11:03:33'),
(1, 3, 2, '2025-04-17 11:03:33'),
(1, 3, 3, '2025-04-17 11:03:33'),
(1, 3, 4, '2025-04-17 11:03:33'),
(1, 3, 5, '2025-04-17 11:03:33'),
(1, 3, 6, '2025-04-17 11:03:33'),
(1, 3, 7, '2025-04-17 11:03:33'),
(1, 3, 8, '2025-04-17 11:03:33'),
(1, 3, 9, '2025-04-17 11:03:33'),
(1, 3, 10, '2025-04-17 11:03:33'),
(1, 4, 1, '2025-04-17 11:03:33'),
(1, 4, 2, '2025-04-17 11:03:33'),
(1, 4, 3, '2025-04-17 11:03:33'),
(1, 4, 4, '2025-04-17 11:03:33'),
(1, 4, 5, '2025-04-17 11:03:33'),
(1, 4, 6, '2025-04-17 11:03:33'),
(1, 4, 7, '2025-04-17 11:03:33'),
(1, 4, 8, '2025-04-17 11:03:33'),
(1, 4, 9, '2025-04-17 11:03:33'),
(1, 4, 10, '2025-04-17 11:03:33'),
(1, 5, 1, '2025-04-17 11:03:33'),
(1, 5, 2, '2025-04-17 11:03:33'),
(1, 5, 3, '2025-04-17 11:03:33'),
(1, 5, 4, '2025-04-17 11:03:33'),
(1, 5, 5, '2025-04-17 11:03:33'),
(1, 5, 6, '2025-04-17 11:03:33'),
(1, 5, 7, '2025-04-17 11:03:33'),
(1, 5, 8, '2025-04-17 11:03:33'),
(1, 5, 9, '2025-04-17 11:03:33'),
(1, 5, 10, '2025-04-17 11:03:33'),
(1, 6, 1, '2025-04-17 11:03:33'),
(1, 6, 2, '2025-04-17 11:03:33'),
(1, 6, 3, '2025-04-17 11:03:33'),
(1, 6, 4, '2025-04-17 11:03:33'),
(1, 6, 5, '2025-04-17 11:03:33'),
(1, 6, 6, '2025-04-17 11:03:33'),
(1, 6, 7, '2025-04-17 11:03:33'),
(1, 6, 8, '2025-04-17 11:03:33'),
(1, 6, 9, '2025-04-17 11:03:33'),
(1, 6, 10, '2025-04-17 11:03:33'),
(1, 7, 1, '2025-04-17 11:03:33'),
(1, 7, 2, '2025-04-17 11:03:33'),
(1, 7, 3, '2025-04-17 11:03:33'),
(1, 7, 4, '2025-04-17 11:03:33'),
(1, 7, 5, '2025-04-17 11:03:33'),
(1, 7, 6, '2025-04-17 11:03:33'),
(1, 7, 7, '2025-04-17 11:03:33'),
(1, 7, 8, '2025-04-17 11:03:33'),
(1, 7, 9, '2025-04-17 11:03:33'),
(1, 7, 10, '2025-04-17 11:03:33'),
(1, 8, 1, '2025-04-17 11:03:33'),
(1, 8, 2, '2025-04-17 11:03:33'),
(1, 8, 3, '2025-04-17 11:03:33'),
(1, 8, 4, '2025-04-17 11:03:33'),
(1, 8, 5, '2025-04-17 11:03:33'),
(1, 8, 6, '2025-04-17 11:03:33'),
(1, 8, 7, '2025-04-17 11:03:33'),
(1, 8, 8, '2025-04-17 11:03:33'),
(1, 8, 9, '2025-04-17 11:03:33'),
(1, 8, 10, '2025-04-17 11:03:33'),
(1, 9, 1, '2025-04-17 11:03:33'),
(1, 9, 2, '2025-04-17 11:03:33'),
(1, 9, 3, '2025-04-17 11:03:33'),
(1, 9, 4, '2025-04-17 11:03:33'),
(1, 9, 5, '2025-04-17 11:03:33'),
(1, 9, 6, '2025-04-17 11:03:33'),
(1, 9, 7, '2025-04-17 11:03:33'),
(1, 9, 8, '2025-04-17 11:03:33'),
(1, 9, 9, '2025-04-17 11:03:33'),
(1, 9, 10, '2025-04-17 11:03:33'),
(1, 10, 1, '2025-04-17 11:03:33'),
(1, 10, 2, '2025-04-17 11:03:33'),
(1, 10, 3, '2025-04-17 11:03:33'),
(1, 10, 4, '2025-04-17 11:03:33'),
(1, 10, 5, '2025-04-17 11:03:33'),
(1, 10, 6, '2025-04-17 11:03:33'),
(1, 10, 7, '2025-04-17 11:03:33'),
(1, 10, 8, '2025-04-17 11:03:33'),
(1, 10, 9, '2025-04-17 11:03:33'),
(1, 10, 10, '2025-04-17 11:03:33'),
(1, 11, 1, '2025-04-17 11:03:33'),
(1, 11, 2, '2025-04-17 11:03:33'),
(1, 11, 3, '2025-04-17 11:03:33'),
(1, 11, 4, '2025-04-17 11:03:33'),
(1, 11, 5, '2025-04-17 11:03:33'),
(1, 11, 6, '2025-04-17 11:03:33'),
(1, 11, 7, '2025-04-17 11:03:33'),
(1, 11, 8, '2025-04-17 11:03:33'),
(1, 11, 9, '2025-04-17 11:03:33'),
(1, 11, 10, '2025-04-17 11:03:33'),
(1, 12, 1, '2025-04-17 11:03:33'),
(1, 12, 2, '2025-04-17 11:03:33'),
(1, 12, 3, '2025-04-17 11:03:33'),
(1, 12, 4, '2025-04-17 11:03:33'),
(1, 12, 5, '2025-04-17 11:03:33'),
(1, 12, 6, '2025-04-17 11:03:33'),
(1, 12, 7, '2025-04-17 11:03:33'),
(1, 12, 8, '2025-04-17 11:03:33'),
(1, 12, 9, '2025-04-17 11:03:33'),
(1, 12, 10, '2025-04-17 11:03:33'),
(1, 13, 1, '2025-04-17 11:03:33'),
(1, 13, 2, '2025-04-17 11:03:33'),
(1, 13, 3, '2025-04-17 11:03:33'),
(1, 13, 4, '2025-04-17 11:03:33'),
(1, 13, 5, '2025-04-17 11:03:33'),
(1, 13, 6, '2025-04-17 11:03:33'),
(1, 13, 7, '2025-04-17 11:03:33'),
(1, 13, 8, '2025-04-17 11:03:33'),
(1, 13, 9, '2025-04-17 11:03:33'),
(1, 13, 10, '2025-04-17 11:03:33'),
(1, 14, 1, '2025-04-17 11:03:33'),
(1, 14, 2, '2025-04-17 11:03:33'),
(1, 14, 3, '2025-04-17 11:03:33'),
(1, 14, 4, '2025-04-17 11:03:33'),
(1, 14, 5, '2025-04-17 11:03:33'),
(1, 14, 6, '2025-04-17 11:03:33'),
(1, 14, 7, '2025-04-17 11:03:33'),
(1, 14, 8, '2025-04-17 11:03:33'),
(1, 14, 9, '2025-04-17 11:03:33'),
(1, 14, 10, '2025-04-17 11:03:33'),
(1, 15, 1, '2025-04-17 11:03:33'),
(1, 15, 2, '2025-04-17 11:03:33'),
(1, 15, 3, '2025-04-17 11:03:33'),
(1, 15, 4, '2025-04-17 11:03:33'),
(1, 15, 5, '2025-04-17 11:03:33'),
(1, 15, 6, '2025-04-17 11:03:33'),
(1, 15, 7, '2025-04-17 11:03:33'),
(1, 15, 8, '2025-04-17 11:03:33'),
(1, 15, 9, '2025-04-17 11:03:33'),
(1, 15, 10, '2025-04-17 11:03:33'),
(1, 16, 1, '2025-04-17 11:03:33'),
(1, 16, 2, '2025-04-17 11:03:33'),
(1, 16, 3, '2025-04-17 11:03:33'),
(1, 16, 4, '2025-04-17 11:03:33'),
(1, 16, 5, '2025-04-17 11:03:33'),
(1, 16, 6, '2025-04-17 11:03:33'),
(1, 16, 7, '2025-04-17 11:03:33'),
(1, 16, 8, '2025-04-17 11:03:33'),
(1, 16, 9, '2025-04-17 11:03:33'),
(1, 16, 10, '2025-04-17 11:03:33'),
(1, 17, 1, '2025-04-17 11:03:33'),
(1, 17, 2, '2025-04-17 11:03:33'),
(1, 17, 3, '2025-04-17 11:03:33'),
(1, 17, 4, '2025-04-17 11:03:33'),
(1, 17, 5, '2025-04-17 11:03:33'),
(1, 17, 6, '2025-04-17 11:03:33'),
(1, 17, 7, '2025-04-17 11:03:33'),
(1, 17, 8, '2025-04-17 11:03:33'),
(1, 17, 9, '2025-04-17 11:03:33'),
(1, 17, 10, '2025-04-17 11:03:33'),
(1, 18, 1, '2025-04-17 11:03:33'),
(1, 18, 2, '2025-04-17 11:03:33'),
(1, 18, 3, '2025-04-17 11:03:33'),
(1, 18, 4, '2025-04-17 11:03:33'),
(1, 18, 5, '2025-04-17 11:03:33'),
(1, 18, 6, '2025-04-17 11:03:33'),
(1, 18, 7, '2025-04-17 11:03:33'),
(1, 18, 8, '2025-04-17 11:03:33'),
(1, 18, 9, '2025-04-17 11:03:33'),
(1, 18, 10, '2025-04-17 11:03:33'),
(1, 19, 1, '2025-04-17 11:03:33'),
(1, 19, 2, '2025-04-17 11:03:33'),
(1, 19, 3, '2025-04-17 11:03:33'),
(1, 19, 4, '2025-04-17 11:03:33'),
(1, 19, 5, '2025-04-17 11:03:33'),
(1, 19, 6, '2025-04-17 11:03:33'),
(1, 19, 7, '2025-04-17 11:03:33'),
(1, 19, 8, '2025-04-17 11:03:33'),
(1, 19, 9, '2025-04-17 11:03:33'),
(1, 19, 10, '2025-04-17 11:03:33'),
(1, 20, 1, '2025-04-17 11:03:33'),
(1, 20, 2, '2025-04-17 11:03:33'),
(1, 20, 3, '2025-04-17 11:03:33'),
(1, 20, 4, '2025-04-17 11:03:33'),
(1, 20, 5, '2025-04-17 11:03:33'),
(1, 20, 6, '2025-04-17 11:03:33'),
(1, 20, 7, '2025-04-17 11:03:33'),
(1, 20, 8, '2025-04-17 11:03:33'),
(1, 20, 9, '2025-04-17 11:03:33'),
(1, 20, 10, '2025-04-17 11:03:33'),
(1, 21, 1, '2025-04-17 11:03:33'),
(1, 21, 2, '2025-04-17 11:03:33'),
(1, 21, 3, '2025-04-17 11:03:33'),
(1, 21, 4, '2025-04-17 11:03:33'),
(1, 21, 5, '2025-04-17 11:03:33'),
(1, 21, 6, '2025-04-17 11:03:33'),
(1, 21, 7, '2025-04-17 11:03:33'),
(1, 21, 8, '2025-04-17 11:03:33'),
(1, 21, 9, '2025-04-17 11:03:33'),
(1, 21, 10, '2025-04-17 11:03:33'),
(1, 22, 1, '2025-04-17 11:03:33'),
(1, 22, 2, '2025-04-17 11:03:33'),
(1, 22, 3, '2025-04-17 11:03:33'),
(1, 22, 4, '2025-04-17 11:03:33'),
(1, 22, 5, '2025-04-17 11:03:33'),
(1, 22, 6, '2025-04-17 11:03:33'),
(1, 22, 7, '2025-04-17 11:03:33'),
(1, 22, 8, '2025-04-17 11:03:33'),
(1, 22, 9, '2025-04-17 11:03:33'),
(1, 22, 10, '2025-04-17 11:03:33'),
(1, 23, 1, '2025-04-17 11:03:33'),
(1, 23, 2, '2025-04-17 11:03:33'),
(1, 23, 3, '2025-04-17 11:03:33'),
(1, 23, 4, '2025-04-17 11:03:33'),
(1, 23, 5, '2025-04-17 11:03:33'),
(1, 23, 6, '2025-04-17 11:03:33'),
(1, 23, 7, '2025-04-17 11:03:33'),
(1, 23, 8, '2025-04-17 11:03:33'),
(1, 23, 9, '2025-04-17 11:03:33'),
(1, 23, 10, '2025-04-17 11:03:33'),
(1, 24, 1, '2025-04-17 11:03:33'),
(1, 24, 2, '2025-04-17 11:03:33'),
(1, 24, 3, '2025-04-17 11:03:33'),
(1, 24, 4, '2025-04-17 11:03:33'),
(1, 24, 5, '2025-04-17 11:03:33'),
(1, 24, 6, '2025-04-17 11:03:33'),
(1, 24, 7, '2025-04-17 11:03:33'),
(1, 24, 8, '2025-04-17 11:03:33'),
(1, 24, 9, '2025-04-17 11:03:33'),
(1, 24, 10, '2025-04-17 11:03:33'),
(1, 25, 1, '2025-04-17 11:03:33'),
(1, 25, 2, '2025-04-17 11:03:33'),
(1, 25, 3, '2025-04-17 11:03:33'),
(1, 25, 4, '2025-04-17 11:03:33'),
(1, 25, 5, '2025-04-17 11:03:33'),
(1, 25, 6, '2025-04-17 11:03:33'),
(1, 25, 7, '2025-04-17 11:03:33'),
(1, 25, 8, '2025-04-17 11:03:33'),
(1, 25, 9, '2025-04-17 11:03:33'),
(1, 25, 10, '2025-04-17 11:03:33'),
(1, 26, 1, '2025-04-17 11:03:33'),
(1, 26, 2, '2025-04-17 11:03:33'),
(1, 26, 3, '2025-04-17 11:03:33'),
(1, 26, 4, '2025-04-17 11:03:33'),
(1, 26, 5, '2025-04-17 11:03:33'),
(1, 26, 6, '2025-04-17 11:03:33'),
(1, 26, 7, '2025-04-17 11:03:33'),
(1, 26, 8, '2025-04-17 11:03:33'),
(1, 26, 9, '2025-04-17 11:03:33'),
(1, 26, 10, '2025-04-17 11:03:33'),
(1, 27, 1, '2025-04-17 11:03:33'),
(1, 27, 2, '2025-04-17 11:03:33'),
(1, 27, 3, '2025-04-17 11:03:33'),
(1, 27, 4, '2025-04-17 11:03:33'),
(1, 27, 5, '2025-04-17 11:03:33'),
(1, 27, 6, '2025-04-17 11:03:33'),
(1, 27, 7, '2025-04-17 11:03:33'),
(1, 27, 8, '2025-04-17 11:03:33'),
(1, 27, 9, '2025-04-17 11:03:33'),
(1, 27, 10, '2025-04-17 11:03:33'),
(1, 28, 1, '2025-04-17 11:03:33'),
(1, 28, 2, '2025-04-17 11:03:33'),
(1, 28, 3, '2025-04-17 11:03:33'),
(1, 28, 4, '2025-04-17 11:03:33'),
(1, 28, 5, '2025-04-17 11:03:33'),
(1, 28, 6, '2025-04-17 11:03:33'),
(1, 28, 7, '2025-04-17 11:03:33'),
(1, 28, 8, '2025-04-17 11:03:33'),
(1, 28, 9, '2025-04-17 11:03:33'),
(1, 28, 10, '2025-04-17 11:03:33'),
(1, 29, 1, '2025-04-17 11:03:33'),
(1, 29, 2, '2025-04-17 11:03:33'),
(1, 29, 3, '2025-04-17 11:03:33'),
(1, 29, 4, '2025-04-17 11:03:33'),
(1, 29, 5, '2025-04-17 11:03:33'),
(1, 29, 6, '2025-04-17 11:03:33'),
(1, 29, 7, '2025-04-17 11:03:33'),
(1, 29, 8, '2025-04-17 11:03:33'),
(1, 29, 9, '2025-04-17 11:03:33'),
(1, 29, 10, '2025-04-17 11:03:33'),
(1, 30, 1, '2025-04-17 11:03:33'),
(1, 30, 2, '2025-04-17 11:03:33'),
(1, 30, 3, '2025-04-17 11:03:33'),
(1, 30, 4, '2025-04-17 11:03:33'),
(1, 30, 5, '2025-04-17 11:03:33'),
(1, 30, 6, '2025-04-17 11:03:33'),
(1, 30, 7, '2025-04-17 11:03:33'),
(1, 30, 8, '2025-04-17 11:03:33'),
(1, 30, 9, '2025-04-17 11:03:33'),
(1, 30, 10, '2025-04-17 11:03:33'),
(1, 31, 1, '2025-04-17 11:03:33'),
(1, 31, 2, '2025-04-17 11:03:33'),
(1, 31, 3, '2025-04-17 11:03:33'),
(1, 31, 4, '2025-04-17 11:03:33'),
(1, 31, 5, '2025-04-17 11:03:33'),
(1, 31, 6, '2025-04-17 11:03:33'),
(1, 31, 7, '2025-04-17 11:03:33'),
(1, 31, 8, '2025-04-17 11:03:33'),
(1, 31, 9, '2025-04-17 11:03:33'),
(1, 31, 10, '2025-04-17 11:03:33'),
(1, 32, 1, '2025-04-17 11:03:33'),
(1, 32, 2, '2025-04-17 11:03:33'),
(1, 32, 3, '2025-04-17 11:03:33'),
(1, 32, 4, '2025-04-17 11:03:33'),
(1, 32, 5, '2025-04-17 11:03:33'),
(1, 32, 6, '2025-04-17 11:03:33'),
(1, 32, 7, '2025-04-17 11:03:33'),
(1, 32, 8, '2025-04-17 11:03:33'),
(1, 32, 9, '2025-04-17 11:03:33'),
(1, 32, 10, '2025-04-17 11:03:33'),
(1, 33, 1, '2025-04-17 11:03:33'),
(1, 33, 2, '2025-04-17 11:03:33'),
(1, 33, 3, '2025-04-17 11:03:33'),
(1, 33, 4, '2025-04-17 11:03:33'),
(1, 33, 5, '2025-04-17 11:03:33'),
(1, 33, 6, '2025-04-17 11:03:33'),
(1, 33, 7, '2025-04-17 11:03:33'),
(1, 33, 8, '2025-04-17 11:03:33'),
(1, 33, 9, '2025-04-17 11:03:33'),
(1, 33, 10, '2025-04-17 11:03:33'),
(1, 34, 1, '2025-04-17 11:03:33'),
(1, 34, 2, '2025-04-17 11:03:33'),
(1, 34, 3, '2025-04-17 11:03:33'),
(1, 34, 4, '2025-04-17 11:03:33'),
(1, 34, 5, '2025-04-17 11:03:33'),
(1, 34, 6, '2025-04-17 11:03:33'),
(1, 34, 7, '2025-04-17 11:03:33'),
(1, 34, 8, '2025-04-17 11:03:33'),
(1, 34, 9, '2025-04-17 11:03:33'),
(1, 34, 10, '2025-04-17 11:03:33'),
(1, 35, 1, '2025-04-17 11:03:33'),
(1, 35, 2, '2025-04-17 11:03:33'),
(1, 35, 3, '2025-04-17 11:03:33'),
(1, 35, 4, '2025-04-17 11:03:33'),
(1, 35, 5, '2025-04-17 11:03:33'),
(1, 35, 6, '2025-04-17 11:03:33'),
(1, 35, 7, '2025-04-17 11:03:33'),
(1, 35, 8, '2025-04-17 11:03:33'),
(1, 35, 9, '2025-04-17 11:03:33'),
(1, 35, 10, '2025-04-17 11:03:33'),
(1, 36, 1, '2025-04-17 11:03:33'),
(1, 36, 2, '2025-04-17 11:03:33'),
(1, 36, 3, '2025-04-17 11:03:33'),
(1, 36, 4, '2025-04-17 11:03:33'),
(1, 36, 5, '2025-04-17 11:03:33'),
(1, 36, 6, '2025-04-17 11:03:33'),
(1, 36, 7, '2025-04-17 11:03:33'),
(1, 36, 8, '2025-04-17 11:03:33'),
(1, 36, 9, '2025-04-17 11:03:33'),
(1, 36, 10, '2025-04-17 11:03:33'),
(1, 37, 1, '2025-04-17 11:03:33'),
(1, 37, 2, '2025-04-17 11:03:33'),
(1, 37, 3, '2025-04-17 11:03:33'),
(1, 37, 4, '2025-04-17 11:03:33'),
(1, 37, 5, '2025-04-17 11:03:33'),
(1, 37, 6, '2025-04-17 11:03:33'),
(1, 37, 7, '2025-04-17 11:03:33'),
(1, 37, 8, '2025-04-17 11:03:33'),
(1, 37, 9, '2025-04-17 11:03:33'),
(1, 37, 10, '2025-04-17 11:03:33'),
(1, 38, 1, '2025-04-17 11:03:33'),
(1, 38, 2, '2025-04-17 11:03:33'),
(1, 38, 3, '2025-04-17 11:03:33'),
(1, 38, 4, '2025-04-17 11:03:33'),
(1, 38, 5, '2025-04-17 11:03:33'),
(1, 38, 6, '2025-04-17 11:03:33'),
(1, 38, 7, '2025-04-17 11:03:33'),
(1, 38, 8, '2025-04-17 11:03:33'),
(1, 38, 9, '2025-04-17 11:03:33'),
(1, 38, 10, '2025-04-17 11:03:33'),
(1, 39, 1, '2025-04-17 11:03:33'),
(1, 39, 2, '2025-04-17 11:03:33'),
(1, 39, 3, '2025-04-17 11:03:33'),
(1, 39, 4, '2025-04-17 11:03:33'),
(1, 39, 5, '2025-04-17 11:03:33'),
(1, 39, 6, '2025-04-17 11:03:33'),
(1, 39, 7, '2025-04-17 11:03:33'),
(1, 39, 8, '2025-04-17 11:03:33'),
(1, 39, 9, '2025-04-17 11:03:33'),
(1, 39, 10, '2025-04-17 11:03:33'),
(1, 40, 1, '2025-04-17 11:03:33'),
(1, 40, 2, '2025-04-17 11:03:33'),
(1, 40, 3, '2025-04-17 11:03:33'),
(1, 40, 4, '2025-04-17 11:03:33'),
(1, 40, 5, '2025-04-17 11:03:33'),
(1, 40, 6, '2025-04-17 11:03:33'),
(1, 40, 7, '2025-04-17 11:03:33'),
(1, 40, 8, '2025-04-17 11:03:33'),
(1, 40, 9, '2025-04-17 11:03:33'),
(1, 40, 10, '2025-04-17 11:03:33'),
(1, 41, 1, '2025-04-17 11:03:33'),
(1, 41, 2, '2025-04-17 11:03:33'),
(1, 41, 3, '2025-04-17 11:03:33'),
(1, 41, 4, '2025-04-17 11:03:33'),
(1, 41, 5, '2025-04-17 11:03:33'),
(1, 41, 6, '2025-04-17 11:03:33'),
(1, 41, 7, '2025-04-17 11:03:33'),
(1, 41, 8, '2025-04-17 11:03:33'),
(1, 41, 9, '2025-04-17 11:03:33'),
(1, 41, 10, '2025-04-17 11:03:33'),
(1, 42, 1, '2025-04-17 11:03:33'),
(1, 42, 2, '2025-04-17 11:03:33'),
(1, 42, 3, '2025-04-17 11:03:33'),
(1, 42, 4, '2025-04-17 11:03:33'),
(1, 42, 5, '2025-04-17 11:03:33'),
(1, 42, 6, '2025-04-17 11:03:33'),
(1, 42, 7, '2025-04-17 11:03:33'),
(1, 42, 8, '2025-04-17 11:03:33'),
(1, 42, 9, '2025-04-17 11:03:33'),
(1, 42, 10, '2025-04-17 11:03:33'),
(1, 43, 1, '2025-04-17 11:03:33'),
(1, 43, 2, '2025-04-17 11:03:33'),
(1, 43, 3, '2025-04-17 11:03:33'),
(1, 43, 4, '2025-04-17 11:03:33'),
(1, 43, 5, '2025-04-17 11:03:33'),
(1, 43, 6, '2025-04-17 11:03:33'),
(1, 43, 7, '2025-04-17 11:03:33'),
(1, 43, 8, '2025-04-17 11:03:33'),
(1, 43, 9, '2025-04-17 11:03:33'),
(1, 43, 10, '2025-04-17 11:03:33'),
(1, 44, 1, '2025-04-17 11:03:33'),
(1, 44, 2, '2025-04-17 11:03:33'),
(1, 44, 3, '2025-04-17 11:03:33'),
(1, 44, 4, '2025-04-17 11:03:33'),
(1, 44, 5, '2025-04-17 11:03:33'),
(1, 44, 6, '2025-04-17 11:03:33'),
(1, 44, 7, '2025-04-17 11:03:33'),
(1, 44, 8, '2025-04-17 11:03:33'),
(1, 44, 9, '2025-04-17 11:03:33'),
(1, 44, 10, '2025-04-17 11:03:33'),
(1, 45, 1, '2025-04-17 11:03:33'),
(1, 45, 2, '2025-04-17 11:03:33'),
(1, 45, 3, '2025-04-17 11:03:33'),
(1, 45, 4, '2025-04-17 11:03:33'),
(1, 45, 5, '2025-04-17 11:03:33'),
(1, 45, 6, '2025-04-17 11:03:33'),
(1, 45, 7, '2025-04-17 11:03:33'),
(1, 45, 8, '2025-04-17 11:03:33'),
(1, 45, 9, '2025-04-17 11:03:33'),
(1, 45, 10, '2025-04-17 11:03:33'),
(1, 46, 1, '2025-04-17 11:03:33'),
(1, 46, 2, '2025-04-17 11:03:33'),
(1, 46, 3, '2025-04-17 11:03:33'),
(1, 46, 4, '2025-04-17 11:03:33'),
(1, 46, 5, '2025-04-17 11:03:33'),
(1, 46, 6, '2025-04-17 11:03:33'),
(1, 46, 7, '2025-04-17 11:03:33'),
(1, 46, 8, '2025-04-17 11:03:33'),
(1, 46, 9, '2025-04-17 11:03:33'),
(1, 46, 10, '2025-04-17 11:03:33'),
(1, 47, 1, '2025-04-17 11:03:33'),
(1, 47, 2, '2025-04-17 11:03:33'),
(1, 47, 3, '2025-04-17 11:03:33'),
(1, 47, 4, '2025-04-17 11:03:33'),
(1, 47, 5, '2025-04-17 11:03:33'),
(1, 47, 6, '2025-04-17 11:03:33'),
(1, 47, 7, '2025-04-17 11:03:33'),
(1, 47, 8, '2025-04-17 11:03:33'),
(1, 47, 9, '2025-04-17 11:03:33'),
(1, 47, 10, '2025-04-17 11:03:33'),
(1, 48, 1, '2025-04-17 11:03:33'),
(1, 48, 2, '2025-04-17 11:03:33'),
(1, 48, 3, '2025-04-17 11:03:33'),
(1, 48, 4, '2025-04-17 11:03:33'),
(1, 48, 5, '2025-04-17 11:03:33'),
(1, 48, 6, '2025-04-17 11:03:33'),
(1, 48, 7, '2025-04-17 11:03:33'),
(1, 48, 8, '2025-04-17 11:03:33'),
(1, 48, 9, '2025-04-17 11:03:33'),
(1, 48, 10, '2025-04-17 11:03:33'),
(1, 49, 1, '2025-04-17 11:03:33'),
(1, 49, 2, '2025-04-17 11:03:33'),
(1, 49, 3, '2025-04-17 11:03:33'),
(1, 49, 4, '2025-04-17 11:03:33'),
(1, 49, 5, '2025-04-17 11:03:33'),
(1, 49, 6, '2025-04-17 11:03:33'),
(1, 49, 7, '2025-04-17 11:03:33'),
(1, 49, 8, '2025-04-17 11:03:33'),
(1, 49, 9, '2025-04-17 11:03:33'),
(1, 49, 10, '2025-04-17 11:03:33'),
(1, 50, 1, '2025-04-17 11:03:33'),
(1, 50, 2, '2025-04-17 11:03:33'),
(1, 50, 3, '2025-04-17 11:03:33'),
(1, 50, 4, '2025-04-17 11:03:33'),
(1, 50, 5, '2025-04-17 11:03:33'),
(1, 50, 6, '2025-04-17 11:03:33'),
(1, 50, 7, '2025-04-17 11:03:33'),
(1, 50, 8, '2025-04-17 11:03:33'),
(1, 50, 9, '2025-04-17 11:03:33'),
(1, 50, 10, '2025-04-17 11:03:33'),
(5, 1, 1, '2025-04-14 15:18:17'),
(5, 1, 2, '2025-04-14 15:18:17'),
(5, 1, 3, '2025-04-14 15:18:17'),
(5, 1, 4, '2025-04-14 15:18:17'),
(5, 1, 5, '2025-04-14 15:18:17'),
(5, 1, 6, '2025-04-14 15:18:17'),
(5, 1, 7, '2025-04-14 15:18:17'),
(5, 1, 8, '2025-04-14 15:18:17'),
(5, 1, 9, '2025-04-14 15:18:17'),
(5, 2, 1, '2025-04-14 15:18:17'),
(5, 2, 2, '2025-04-14 15:18:17'),
(5, 2, 3, '2025-04-14 15:18:17'),
(5, 2, 4, '2025-04-14 15:18:17'),
(5, 2, 5, '2025-04-14 15:18:17'),
(5, 2, 6, '2025-04-14 15:18:17'),
(5, 2, 7, '2025-04-14 15:18:17'),
(5, 2, 8, '2025-04-14 15:18:17'),
(5, 2, 9, '2025-04-14 15:18:17'),
(5, 3, 1, '2025-04-14 15:18:17'),
(5, 3, 2, '2025-04-14 15:18:17'),
(5, 3, 3, '2025-04-14 15:18:17'),
(5, 3, 4, '2025-04-14 15:18:17'),
(5, 3, 5, '2025-04-14 15:18:17'),
(5, 3, 6, '2025-04-14 15:18:17'),
(5, 3, 7, '2025-04-14 15:18:17'),
(5, 3, 8, '2025-04-14 15:18:17'),
(5, 3, 9, '2025-04-14 15:18:17'),
(5, 4, 1, '2025-04-14 15:18:17'),
(5, 4, 2, '2025-04-14 15:18:17'),
(5, 4, 3, '2025-04-14 15:18:17'),
(5, 4, 4, '2025-04-14 15:18:17'),
(5, 4, 5, '2025-04-14 15:18:17'),
(5, 4, 6, '2025-04-14 15:18:17'),
(5, 4, 7, '2025-04-14 15:18:17'),
(5, 4, 8, '2025-04-14 15:18:17'),
(5, 4, 9, '2025-04-14 15:18:17'),
(5, 5, 1, '2025-04-14 15:18:17'),
(5, 5, 2, '2025-04-14 15:18:17'),
(5, 5, 3, '2025-04-14 15:18:17'),
(5, 5, 4, '2025-04-14 15:18:17'),
(5, 5, 5, '2025-04-14 15:18:17'),
(5, 5, 6, '2025-04-14 15:18:17'),
(5, 5, 7, '2025-04-14 15:18:17'),
(5, 5, 8, '2025-04-14 15:18:17'),
(5, 5, 9, '2025-04-14 15:18:17'),
(5, 6, 1, '2025-04-14 15:18:17'),
(5, 6, 2, '2025-04-14 15:18:17'),
(5, 6, 3, '2025-04-14 15:18:17'),
(5, 6, 4, '2025-04-14 15:18:17'),
(5, 6, 5, '2025-04-14 15:18:17'),
(5, 6, 6, '2025-04-14 15:18:17'),
(5, 6, 7, '2025-04-14 15:18:17'),
(5, 6, 8, '2025-04-14 15:18:17'),
(5, 6, 9, '2025-04-14 15:18:17'),
(5, 7, 1, '2025-04-14 15:18:17'),
(5, 7, 2, '2025-04-14 15:18:17'),
(5, 7, 3, '2025-04-14 15:18:17'),
(5, 7, 4, '2025-04-14 15:18:17'),
(5, 7, 5, '2025-04-14 15:18:17'),
(5, 7, 6, '2025-04-14 15:18:17'),
(5, 7, 7, '2025-04-14 15:18:17'),
(5, 7, 8, '2025-04-14 15:18:17'),
(5, 7, 9, '2025-04-14 15:18:17'),
(5, 8, 1, '2025-04-14 15:18:17'),
(5, 8, 2, '2025-04-14 15:18:17'),
(5, 8, 3, '2025-04-14 15:18:17'),
(5, 8, 4, '2025-04-14 15:18:17'),
(5, 8, 5, '2025-04-14 15:18:17'),
(5, 8, 6, '2025-04-14 15:18:17'),
(5, 8, 7, '2025-04-14 15:18:17'),
(5, 8, 8, '2025-04-14 15:18:17'),
(5, 8, 9, '2025-04-14 15:18:17'),
(5, 9, 1, '2025-04-14 15:18:17'),
(5, 9, 2, '2025-04-14 15:18:17'),
(5, 9, 3, '2025-04-14 15:18:17'),
(5, 9, 4, '2025-04-14 15:18:17'),
(5, 9, 5, '2025-04-14 15:18:17'),
(5, 9, 6, '2025-04-14 15:18:17'),
(5, 9, 7, '2025-04-14 15:18:17'),
(5, 9, 8, '2025-04-14 15:18:17'),
(5, 9, 9, '2025-04-14 15:18:17'),
(5, 10, 1, '2025-04-14 15:18:17'),
(5, 10, 2, '2025-04-14 15:18:17'),
(5, 10, 3, '2025-04-14 15:18:17'),
(5, 10, 4, '2025-04-14 15:18:17'),
(5, 10, 5, '2025-04-14 15:18:17'),
(5, 10, 6, '2025-04-14 15:18:17'),
(5, 10, 7, '2025-04-14 15:18:17'),
(5, 10, 8, '2025-04-14 15:18:17'),
(5, 10, 9, '2025-04-14 15:18:17'),
(5, 11, 1, '2025-04-14 15:18:17'),
(5, 11, 2, '2025-04-14 15:18:17'),
(5, 11, 3, '2025-04-14 15:18:17'),
(5, 11, 4, '2025-04-14 15:18:17'),
(5, 11, 5, '2025-04-14 15:18:17'),
(5, 11, 6, '2025-04-14 15:18:17'),
(5, 11, 7, '2025-04-14 15:18:17'),
(5, 11, 8, '2025-04-14 15:18:17'),
(5, 11, 9, '2025-04-14 15:18:17'),
(5, 12, 1, '2025-04-14 15:18:17'),
(5, 12, 2, '2025-04-14 15:18:17'),
(5, 12, 3, '2025-04-14 15:18:17'),
(5, 12, 4, '2025-04-14 15:18:17'),
(5, 12, 5, '2025-04-14 15:18:17'),
(5, 12, 6, '2025-04-14 15:18:17'),
(5, 12, 7, '2025-04-14 15:18:17'),
(5, 12, 8, '2025-04-14 15:18:17'),
(5, 12, 9, '2025-04-14 15:18:17'),
(5, 13, 1, '2025-04-14 15:18:17'),
(5, 13, 2, '2025-04-14 15:18:17'),
(5, 13, 3, '2025-04-14 15:18:17'),
(5, 13, 4, '2025-04-14 15:18:17'),
(5, 13, 5, '2025-04-14 15:18:17'),
(5, 13, 6, '2025-04-14 15:18:17'),
(5, 13, 7, '2025-04-14 15:18:17'),
(5, 13, 8, '2025-04-14 15:18:17'),
(5, 13, 9, '2025-04-14 15:18:17'),
(5, 14, 1, '2025-04-14 15:18:17'),
(5, 14, 2, '2025-04-14 15:18:17'),
(5, 14, 3, '2025-04-14 15:18:17'),
(5, 14, 4, '2025-04-14 15:18:17'),
(5, 14, 5, '2025-04-14 15:18:17'),
(5, 14, 6, '2025-04-14 15:18:17'),
(5, 14, 7, '2025-04-14 15:18:17'),
(5, 14, 8, '2025-04-14 15:18:17'),
(5, 14, 9, '2025-04-14 15:18:17'),
(5, 15, 1, '2025-04-14 15:18:17'),
(5, 15, 2, '2025-04-14 15:18:17'),
(5, 15, 3, '2025-04-14 15:18:17'),
(5, 15, 4, '2025-04-14 15:18:17'),
(5, 15, 5, '2025-04-14 15:18:17'),
(5, 15, 6, '2025-04-14 15:18:17'),
(5, 15, 7, '2025-04-14 15:18:17'),
(5, 15, 8, '2025-04-14 15:18:17'),
(5, 15, 9, '2025-04-14 15:18:17'),
(5, 16, 1, '2025-04-14 15:18:17'),
(5, 16, 2, '2025-04-14 15:18:17'),
(5, 16, 3, '2025-04-14 15:18:17'),
(5, 16, 4, '2025-04-14 15:18:17'),
(5, 16, 5, '2025-04-14 15:18:17'),
(5, 16, 6, '2025-04-14 15:18:17'),
(5, 16, 7, '2025-04-14 15:18:17'),
(5, 16, 8, '2025-04-14 15:18:17'),
(5, 16, 9, '2025-04-14 15:18:17'),
(5, 17, 1, '2025-04-14 15:18:17'),
(5, 17, 2, '2025-04-14 15:18:17'),
(5, 17, 3, '2025-04-14 15:18:17'),
(5, 17, 4, '2025-04-14 15:18:17'),
(5, 17, 5, '2025-04-14 15:18:17'),
(5, 17, 6, '2025-04-14 15:18:17'),
(5, 17, 7, '2025-04-14 15:18:17'),
(5, 17, 8, '2025-04-14 15:18:17'),
(5, 17, 9, '2025-04-14 15:18:17'),
(6, 1, 1, '2025-04-14 15:09:18'),
(6, 1, 2, '2025-04-14 15:09:18'),
(6, 1, 3, '2025-04-14 15:09:18'),
(6, 1, 4, '2025-04-14 15:09:18'),
(6, 1, 5, '2025-04-14 15:09:18'),
(6, 1, 6, '2025-04-14 15:09:18'),
(6, 1, 7, '2025-04-14 15:09:18'),
(6, 1, 8, '2025-04-14 15:09:18'),
(6, 1, 9, '2025-04-14 15:09:18'),
(6, 2, 1, '2025-04-14 15:09:18'),
(6, 2, 2, '2025-04-14 15:09:18'),
(6, 2, 3, '2025-04-14 15:09:18'),
(6, 2, 4, '2025-04-14 15:09:18'),
(6, 2, 5, '2025-04-14 15:09:18'),
(6, 2, 6, '2025-04-14 15:09:18'),
(6, 2, 7, '2025-04-14 15:09:18'),
(6, 2, 8, '2025-04-14 15:09:18'),
(6, 2, 9, '2025-04-14 15:09:18'),
(6, 3, 1, '2025-04-14 15:09:18'),
(6, 3, 2, '2025-04-14 15:09:18'),
(6, 3, 3, '2025-04-14 15:09:18'),
(6, 3, 4, '2025-04-14 15:09:18'),
(6, 3, 5, '2025-04-14 15:09:18'),
(6, 3, 6, '2025-04-14 15:09:18'),
(6, 3, 7, '2025-04-14 15:09:18'),
(6, 3, 8, '2025-04-14 15:09:18'),
(6, 3, 9, '2025-04-14 15:09:18'),
(6, 4, 1, '2025-04-14 15:09:18'),
(6, 4, 2, '2025-04-14 15:09:18'),
(6, 4, 3, '2025-04-14 15:09:18'),
(6, 4, 4, '2025-04-14 15:09:18'),
(6, 4, 5, '2025-04-14 15:09:18'),
(6, 4, 6, '2025-04-14 15:09:18'),
(6, 4, 7, '2025-04-14 15:09:18'),
(6, 4, 8, '2025-04-14 15:09:18'),
(6, 4, 9, '2025-04-14 15:09:18'),
(6, 5, 1, '2025-04-14 15:09:18'),
(6, 5, 2, '2025-04-14 15:09:18'),
(6, 5, 3, '2025-04-14 15:09:18'),
(6, 5, 4, '2025-04-14 15:09:18'),
(6, 5, 5, '2025-04-14 15:09:18'),
(6, 5, 6, '2025-04-14 15:09:18'),
(6, 5, 7, '2025-04-14 15:09:18'),
(6, 5, 8, '2025-04-14 15:09:18'),
(6, 5, 9, '2025-04-14 15:09:18'),
(6, 6, 1, '2025-04-14 15:09:18'),
(6, 6, 2, '2025-04-14 15:09:18'),
(6, 6, 3, '2025-04-14 15:09:18'),
(6, 6, 4, '2025-04-14 15:09:18'),
(6, 6, 5, '2025-04-14 15:09:18'),
(6, 6, 6, '2025-04-14 15:09:18'),
(6, 6, 7, '2025-04-14 15:09:18'),
(6, 6, 8, '2025-04-14 15:09:18'),
(6, 6, 9, '2025-04-14 15:09:18'),
(6, 7, 1, '2025-04-14 15:09:18'),
(6, 7, 2, '2025-04-14 15:09:18'),
(6, 7, 3, '2025-04-14 15:09:18'),
(6, 7, 4, '2025-04-14 15:09:18'),
(6, 7, 5, '2025-04-14 15:09:18'),
(6, 7, 6, '2025-04-14 15:09:18'),
(6, 7, 7, '2025-04-14 15:09:18'),
(6, 7, 8, '2025-04-14 15:09:18'),
(6, 7, 9, '2025-04-14 15:09:18'),
(6, 8, 1, '2025-04-14 15:09:18'),
(6, 8, 2, '2025-04-14 15:09:18'),
(6, 8, 3, '2025-04-14 15:09:18'),
(6, 8, 4, '2025-04-14 15:09:18'),
(6, 8, 5, '2025-04-14 15:09:18'),
(6, 8, 6, '2025-04-14 15:09:18'),
(6, 8, 7, '2025-04-14 15:09:18'),
(6, 8, 8, '2025-04-14 15:09:18'),
(6, 8, 9, '2025-04-14 15:09:18'),
(6, 9, 1, '2025-04-14 15:09:18'),
(6, 9, 2, '2025-04-14 15:09:18'),
(6, 9, 3, '2025-04-14 15:09:18'),
(6, 9, 4, '2025-04-14 15:09:18'),
(6, 9, 5, '2025-04-14 15:09:18'),
(6, 9, 6, '2025-04-14 15:09:18'),
(6, 9, 7, '2025-04-14 15:09:18'),
(6, 9, 8, '2025-04-14 15:09:18'),
(6, 9, 9, '2025-04-14 15:09:18'),
(6, 10, 1, '2025-04-14 15:09:18'),
(6, 10, 2, '2025-04-14 15:09:18'),
(6, 10, 3, '2025-04-14 15:09:18'),
(6, 10, 4, '2025-04-14 15:09:18'),
(6, 10, 5, '2025-04-14 15:09:18'),
(6, 10, 6, '2025-04-14 15:09:18'),
(6, 10, 7, '2025-04-14 15:09:18'),
(6, 10, 8, '2025-04-14 15:09:18'),
(6, 10, 9, '2025-04-14 15:09:18'),
(6, 11, 1, '2025-04-14 15:09:18'),
(6, 11, 2, '2025-04-14 15:09:18'),
(6, 11, 3, '2025-04-14 15:09:18'),
(6, 11, 4, '2025-04-14 15:09:18'),
(6, 11, 5, '2025-04-14 15:09:18'),
(6, 11, 6, '2025-04-14 15:09:18'),
(6, 11, 7, '2025-04-14 15:09:18'),
(6, 11, 8, '2025-04-14 15:09:18'),
(6, 11, 9, '2025-04-14 15:09:18'),
(6, 12, 1, '2025-04-14 15:09:18'),
(6, 12, 2, '2025-04-14 15:09:18'),
(6, 12, 3, '2025-04-14 15:09:18'),
(6, 12, 4, '2025-04-14 15:09:18'),
(6, 12, 5, '2025-04-14 15:09:18'),
(6, 12, 6, '2025-04-14 15:09:18'),
(6, 12, 7, '2025-04-14 15:09:18'),
(6, 12, 8, '2025-04-14 15:09:18'),
(6, 12, 9, '2025-04-14 15:09:18'),
(6, 13, 1, '2025-04-14 15:09:18'),
(6, 13, 2, '2025-04-14 15:09:18'),
(6, 13, 3, '2025-04-14 15:09:18'),
(6, 13, 4, '2025-04-14 15:09:18'),
(6, 13, 5, '2025-04-14 15:09:18'),
(6, 13, 6, '2025-04-14 15:09:18'),
(6, 13, 7, '2025-04-14 15:09:18'),
(6, 13, 8, '2025-04-14 15:09:18'),
(6, 13, 9, '2025-04-14 15:09:18'),
(6, 14, 1, '2025-04-14 15:09:18'),
(6, 14, 2, '2025-04-14 15:09:18'),
(6, 14, 3, '2025-04-14 15:09:18'),
(6, 14, 4, '2025-04-14 15:09:18'),
(6, 14, 5, '2025-04-14 15:09:18'),
(6, 14, 6, '2025-04-14 15:09:18'),
(6, 14, 7, '2025-04-14 15:09:18'),
(6, 14, 8, '2025-04-14 15:09:18'),
(6, 14, 9, '2025-04-14 15:09:18'),
(6, 15, 1, '2025-04-14 15:09:18'),
(6, 15, 2, '2025-04-14 15:09:18'),
(6, 15, 3, '2025-04-14 15:09:18'),
(6, 15, 4, '2025-04-14 15:09:18'),
(6, 15, 5, '2025-04-14 15:09:18'),
(6, 15, 6, '2025-04-14 15:09:18'),
(6, 15, 7, '2025-04-14 15:09:18'),
(6, 15, 8, '2025-04-14 15:09:18'),
(6, 15, 9, '2025-04-14 15:09:18'),
(6, 16, 1, '2025-04-14 15:09:18'),
(6, 16, 2, '2025-04-14 15:09:18'),
(6, 16, 3, '2025-04-14 15:09:18'),
(6, 16, 4, '2025-04-14 15:09:18'),
(6, 16, 5, '2025-04-14 15:09:18'),
(6, 16, 6, '2025-04-14 15:09:18'),
(6, 16, 7, '2025-04-14 15:09:18'),
(6, 16, 8, '2025-04-14 15:09:18'),
(6, 16, 9, '2025-04-14 15:09:18'),
(6, 17, 1, '2025-04-14 15:09:18'),
(6, 17, 2, '2025-04-14 15:09:18'),
(6, 17, 3, '2025-04-14 15:09:18'),
(6, 17, 4, '2025-04-14 15:09:18'),
(6, 17, 5, '2025-04-14 15:09:18'),
(6, 17, 6, '2025-04-14 15:09:18'),
(6, 17, 7, '2025-04-14 15:09:18'),
(6, 17, 8, '2025-04-14 15:09:18'),
(6, 17, 9, '2025-04-14 15:09:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id_persona` int(11) NOT NULL,
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
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id_persona`, `tipo_persona`, `id_tipo_documento`, `numero_documento`, `nombre`, `apellidos`, `razon_social`, `direccion`, `ciudad`, `telefono`, `celular`, `email`, `fecha_nacimiento`, `cargo`, `estado`, `fecha_registro`) VALUES
(1, 'TRABAJADOR', 1, '12345678', 'Admin', 'Sistema', NULL, NULL, NULL, NULL, NULL, 'admin@mipolleria.com', NULL, NULL, 1, '2025-04-12 12:08:41'),
(2, 'CLIENTE', 1, '72243561', 'Luis ', 'Martinez Algo', '', '', '', '', '988818539', 'luis@gmail.com', '0000-00-00', NULL, 1, '2025-04-12 12:23:17'),
(3, 'TRANSPORTISTA', 1, '72243567', 'Lucas', 'Gomes Altamar ', NULL, '', '', '', '918818539', 'lucas@gmail.com', NULL, NULL, 1, '2025-04-12 12:24:18'),
(4, 'TRANSPORTISTA', 1, '72243991', 'Demetrio', 'Lucas Mordis', NULL, '', '', '987765432', '987765432', 'demetrio@gmail.com', NULL, NULL, 1, '2025-04-12 12:25:02'),
(5, 'CLIENTE', 1, '72243599', 'Jorge ', 'chavez', '', 'Vía Los libertadores', 'Carhuapata, Huancavelica, Peru', '', '988818539', 'loslibertadores2024@gmail.com', '0000-00-00', NULL, 1, '2025-04-17 09:57:25'),
(6, 'PROVEEDOR', 10, '20343434347', '', '', 'Inversiones todo', 'Ninguno', 'Lima', '987564342', '986767787', 'todo@gmail.com', NULL, NULL, 1, '2025-04-17 10:07:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procesamiento_pollos`
--

CREATE TABLE `procesamiento_pollos` (
  `id_procesamiento` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `fecha_procesamiento` date NOT NULL,
  `cantidad_pollos` int(11) NOT NULL,
  `peso_total` decimal(10,2) NOT NULL,
  `rendimiento` decimal(5,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
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
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_categoria`, `codigo`, `codigo_barras`, `nombre`, `descripcion`, `unidad_medida`, `peso_promedio`, `precio_compra`, `precio_venta`, `tiene_iva`, `imagen`, `estado`, `fecha_creacion`) VALUES
(1, 1, 'A001', 'C001', 'Cemento Andino', 'Ninguno', 'UNIDAD', 40.000, 28.00, 30.00, 1, '1744642756_cemento andino.jpg', 1, '2025-04-14 09:59:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_acceso` int(11) DEFAULT 1,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`, `descripcion`, `nivel_acceso`, `estado`) VALUES
(1, 'ADMINISTRADOR', 'Acceso completo al sistema', 100, 1),
(2, 'GERENTE', 'Gestiona operaciones de múltiples sucursales', 80, 1),
(3, 'SUPERVISOR', 'Supervisa operaciones en sucursales asignadas', 60, 1),
(4, 'VENDEDOR', 'Realiza ventas y operaciones básicas', 40, 1),
(5, 'TRANSPORTISTA', 'Encargado de realizar envíos entre sucursales', 2, 1),
(6, 'INVENTARIO', 'Gestiona productos y almacenes', 50, 1),
(7, 'CLIENTE', 'Cliente acceso limitado', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_envios`
--

CREATE TABLE `seguimiento_envios` (
  `id_seguimiento` int(11) NOT NULL,
  `id_envio` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `seguimiento_envios`
--

INSERT INTO `seguimiento_envios` (`id_seguimiento`, `id_envio`, `id_usuario`, `estado_anterior`, `estado_nuevo`, `ubicacion`, `observaciones`, `fecha_registro`) VALUES
(1, 1, 1, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-12 12:30:49'),
(3, 1, NULL, 'PENDIENTE', 'PREPARACION', NULL, '', '2025-04-14 16:02:05'),
(4, 1, NULL, 'PREPARACION', 'EN_TRANSITO', NULL, '', '2025-04-14 16:02:08'),
(5, 1, NULL, 'EN_TRANSITO', 'EN_TRANSITO', NULL, '', '2025-04-14 16:02:26'),
(6, 1, NULL, 'EN_TRANSITO', 'EN_REPARTO', NULL, '', '2025-04-14 16:03:21'),
(7, 1, NULL, 'EN_REPARTO', 'ENTREGADO', NULL, '', '2025-04-14 16:07:49'),
(8, 1, NULL, 'ENTREGADO', 'ENTREGADO', NULL, '', '2025-04-14 16:07:58'),
(9, 1, NULL, 'ENTREGADO', 'PREPARACION', NULL, '', '2025-04-14 17:21:39'),
(10, 1, NULL, 'PREPARACION', 'EN_TRANSITO', NULL, '', '2025-04-14 17:26:49'),
(11, 1, NULL, 'EN_TRANSITO', 'EN_TRANSITO', NULL, '', '2025-04-15 08:50:44'),
(12, 1, NULL, 'EN_TRANSITO', 'EN_TRANSITO', NULL, '', '2025-04-15 08:52:02'),
(13, 1, NULL, 'EN_TRANSITO', 'EN_REPARTO', NULL, '', '2025-04-15 08:54:42'),
(14, 1, NULL, 'EN_REPARTO', 'ENTREGADO', NULL, '', '2025-04-15 08:55:55'),
(15, 1, NULL, 'ENTREGADO', 'PREPARACION', NULL, '', '2025-04-15 08:56:24'),
(16, 1, NULL, 'PREPARACION', 'PREPARACION', NULL, '', '2025-04-15 08:57:37'),
(17, 1, NULL, 'PREPARACION', 'EN_TRANSITO', NULL, '', '2025-04-15 08:59:01'),
(18, 1, NULL, 'EN_TRANSITO', 'ENTREGADO', NULL, '', '2025-04-15 08:59:40'),
(19, 1, NULL, 'ENTREGADO', 'EN_REPARTO', NULL, '', '2025-04-15 09:02:13'),
(20, 1, NULL, 'EN_REPARTO', 'ENTREGADO', NULL, '', '2025-04-15 09:03:59'),
(21, 1, NULL, 'ENTREGADO', 'EN_REPARTO', NULL, 'fsdafsa', '2025-04-15 09:13:22'),
(22, 1, 1, 'EN_REPARTO', 'EN_TRANSITO', NULL, '', '2025-04-15 09:24:42'),
(23, 3, 1, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-15 09:26:33'),
(24, 3, 1, 'PENDIENTE', 'EN_TRANSITO', NULL, '', '2025-04-15 09:28:59'),
(25, 3, 1, 'EN_TRANSITO', 'ENTREGADO', NULL, '', '2025-04-15 09:30:24'),
(26, 4, 1, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-15 09:42:56'),
(27, 5, 1, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-15 09:45:50'),
(28, 5, 1, 'PENDIENTE', 'PREPARACION', NULL, '', '2025-04-15 09:58:19'),
(29, 6, 1, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-15 10:04:19'),
(30, 7, 1, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-15 10:25:18'),
(31, 8, 1, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-15 10:30:50'),
(32, 8, 1, 'PENDIENTE', 'EN_REPARTO', NULL, '', '2025-04-15 11:33:40'),
(33, 9, 2, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-15 12:34:58'),
(34, 9, 2, 'PENDIENTE', 'CANCELADO', NULL, 'Envío cancelado por el usuario', '2025-04-15 12:36:35'),
(35, 9, 2, 'CANCELADO', 'EN_TRANSITO', NULL, '', '2025-04-15 12:36:40'),
(36, 11, 2, NULL, 'PENDIENTE', NULL, 'Envío creado', '2025-04-15 12:40:57'),
(37, 7, 1, 'PENDIENTE', 'ENTREGADO', NULL, '', '2025-04-17 12:41:51'),
(38, 7, 1, 'ENTREGADO', 'EN_REPARTO', NULL, '', '2025-04-17 12:41:59'),
(39, 7, 1, 'EN_REPARTO', 'ENTREGADO', NULL, '', '2025-04-17 12:42:06'),
(40, 1, 1, 'EN_TRANSITO', 'EN_REPARTO', NULL, '', '2025-04-17 17:18:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `series_comprobantes`
--

CREATE TABLE `series_comprobantes` (
  `id_serie` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `id_tipo_comprobante` int(11) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `numero_inicial` int(11) NOT NULL,
  `numero_actual` int(11) NOT NULL,
  `numero_final` int(11) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `series_comprobantes`
--

INSERT INTO `series_comprobantes` (`id_serie`, `id_sucursal`, `id_tipo_comprobante`, `serie`, `numero_inicial`, `numero_actual`, `numero_final`, `estado`) VALUES
(1, 1, 2, 'B001', 1, 7, 100, 1),
(2, 2, 2, 'B002', 1, 1, 1000, 1),
(3, 4, 2, 'B003', 1, 2, 1000, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id_sucursal` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id_sucursal`, `codigo`, `nombre`, `direccion`, `ciudad`, `telefono`, `responsable`, `es_principal`, `estado`, `fecha_creacion`) VALUES
(1, 'T001', 'Tienda Sur', 'Tienda sur #345', 'Tacna', '987876765', 'Luis Torres Martinez', 0, 1, '2025-04-12 12:11:34'),
(2, 'T002', 'Tienda Norte', 'Tinda Norte #4563', 'Piura', '987789876', 'Samuel Gomez Quevedo', 0, 1, '2025-04-12 12:12:54'),
(3, 'T003', 'Tienda centro', 'Tienda centroi #45646', 'Huancayo', '912456234', 'Marcos Sacramento', 0, 1, '2025-04-12 12:14:02'),
(4, 'T004', 'Tienda Oeste', 'Tienda Oeste #456', 'Lima', '989989111', 'Ángelica Salvatierra Pedri', 1, 1, '2025-04-12 12:15:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas_envio`
--

CREATE TABLE `tarifas_envio` (
  `id_tarifa` int(11) NOT NULL,
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
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifas_envio`
--

INSERT INTO `tarifas_envio` (`id_tarifa`, `id_sucursal_origen`, `id_sucursal_destino`, `id_tipo_encomienda`, `rango_peso_min`, `rango_peso_max`, `costo_base`, `costo_kg_extra`, `tiempo_estimado`, `vigencia_desde`, `vigencia_hasta`, `estado`) VALUES
(1, 1, 2, 5, 1.00, 100.00, 1.00, 0.00, 15, '2025-04-01', '2025-06-12', 1),
(2, 2, 1, 5, 1.00, 100.00, 1.00, 0.00, 15, '2025-03-30', '2025-05-23', 1),
(3, 1, 2, 4, 1.00, 100.00, 15.00, 0.00, 15, '2025-03-30', '2025-05-09', 1),
(4, 4, 1, 4, 1.00, 100.00, 20.00, 0.00, 7, '2025-03-30', '2025-04-30', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_comprobantes`
--

CREATE TABLE `tipo_comprobantes` (
  `id_tipo_comprobante` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `serie_obligatoria` tinyint(1) DEFAULT 1,
  `numero_obligatorio` tinyint(1) DEFAULT 1,
  `afecta_inventario` tinyint(1) DEFAULT 1,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_comprobantes`
--

INSERT INTO `tipo_comprobantes` (`id_tipo_comprobante`, `codigo`, `nombre`, `serie_obligatoria`, `numero_obligatorio`, `afecta_inventario`, `estado`) VALUES
(1, '543', 'FACTURA', 1, 1, 1, 1),
(2, '238', 'BOLETA', 1, 1, 1, 1),
(3, 'N001', 'NOTA DE VENTA', 0, 1, 1, 1),
(4, 'G001', 'GUIA DE REMISION', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documentos`
--

CREATE TABLE `tipo_documentos` (
  `id_tipo_documento` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  `longitud` int(11) DEFAULT NULL,
  `es_empresa` tinyint(1) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_documentos`
--

INSERT INTO `tipo_documentos` (`id_tipo_documento`, `nombre`, `abreviatura`, `longitud`, `es_empresa`, `estado`) VALUES
(1, 'Documento Nacional de Identidad', 'DNI', 8, 0, 1),
(10, 'Registro Único de Contribuyentes', 'RUC', 20, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_encomiendas`
--

CREATE TABLE `tipo_encomiendas` (
  `id_tipo_encomienda` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `requiere_confirmacion` tinyint(1) DEFAULT 1,
  `prioridad` enum('BAJA','MEDIA','ALTA','URGENTE') DEFAULT 'MEDIA',
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_encomiendas`
--

INSERT INTO `tipo_encomiendas` (`id_tipo_encomienda`, `nombre`, `descripcion`, `requiere_confirmacion`, `prioridad`, `estado`) VALUES
(1, 'Productos Perecederos', 'Productos que requieren refrigeración', 0, 'MEDIA', 1),
(2, 'Productos Secos', 'Productos no perecederos', 0, 'MEDIA', 1),
(3, 'Documentos', 'Envío de documentos importantes', 0, 'MEDIA', 1),
(4, 'Insumos', 'Materiales y suministros', 0, 'MEDIA', 1),
(5, 'Sacos de costal', 'Descripciòn del saco de costal', 0, 'MEDIA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id_trabajador` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_cese` date DEFAULT NULL,
  `motivo_cese` text DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transportistas`
--

CREATE TABLE `transportistas` (
  `id_transportista` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `tipo_vehiculo` varchar(50) DEFAULT NULL,
  `placa_vehiculo` varchar(20) DEFAULT NULL,
  `telefono_contacto` varchar(20) NOT NULL,
  `fecha_registro` date NOT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transportistas`
--

INSERT INTO `transportistas` (`id_transportista`, `id_persona`, `tipo_vehiculo`, `placa_vehiculo`, `telefono_contacto`, `fecha_registro`, `estado`) VALUES
(1, 4, 'CAMIONETA', 'A-123', '98761231', '2025-04-12', 1),
(2, 3, 'CAMION', 'SD-123', '987789999', '2025-04-12', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `id_persona` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `ultimo_login` datetime DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_sucursal`, `id_persona`, `nombre_usuario`, `usuario`, `contrasena`, `imagen`, `estado`, `ultimo_login`, `fecha_creacion`) VALUES
(1, 1, 1, 'Administrador', 'Apuuray12345', '$2a$07$asxx54ahjppf45sd87a5au.KWXKi/QEnipU29qpDnSWgzsF5pKqrK', NULL, 1, '2025-04-18 00:36:00', '2025-04-12 12:08:41'),
(2, 4, 2, 'Jorge Chavez Huincho', 'Jorge12345', '$2y$10$CVlycCUnN.Y9iuVG2/tWq.HM7DN1.wTIwlPe3g3dCujEuGFfXNgwO', '1744640017_OrangeBlueModernIllustrativeLogisticsCompanyLogo.png', 1, '2025-04-15 19:32:34', '2025-04-14 08:42:00'),
(4, 2, 4, 'Pedro 123', 'Pedro123', '$2y$10$AW545gj243Pd7aMb2.35Iu6zuW/dYlYDI3yQTsx3zkfaMaVGx0.7u', '1744661249_usaer.jpg', 1, NULL, '2025-04-14 15:07:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_roles`
--

CREATE TABLE `usuario_roles` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_roles`
--

INSERT INTO `usuario_roles` (`id_usuario`, `id_rol`, `fecha_asignacion`) VALUES
(1, 1, '2025-04-17 11:03:33'),
(2, 5, '2025-04-14 15:18:17'),
(4, 6, '2025-04-14 15:09:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
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
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acciones`
--
ALTER TABLE `acciones`
  ADD PRIMARY KEY (`id_accion`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  ADD PRIMARY KEY (`id_almacen`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `aperturas_caja`
--
ALTER TABLE `aperturas_caja`
  ADD PRIMARY KEY (`id_apertura`),
  ADD KEY `id_caja` (`id_caja`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD UNIQUE KEY `trabajador_fecha` (`id_trabajador`,`fecha`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id_caja`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD UNIQUE KEY `serie_numero` (`id_serie`,`numero_comprobante`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `configuracion_correo`
--
ALTER TABLE `configuracion_correo`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `configuracion_ticket`
--
ALTER TABLE `configuracion_ticket`
  ADD PRIMARY KEY (`id_configuracion`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `contratos_trabajadores`
--
ALTER TABLE `contratos_trabajadores`
  ADD PRIMARY KEY (`id_contrato`),
  ADD KEY `id_trabajador` (`id_trabajador`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_procesamiento`
--
ALTER TABLE `detalle_procesamiento`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_procesamiento` (`id_procesamiento`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `documentos_envios`
--
ALTER TABLE `documentos_envios`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `id_envio` (`id_envio`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `envios`
--
ALTER TABLE `envios`
  ADD PRIMARY KEY (`id_envio`),
  ADD UNIQUE KEY `codigo_envio` (`codigo_envio`),
  ADD KEY `id_sucursal_origen` (`id_sucursal_origen`),
  ADD KEY `id_sucursal_destino` (`id_sucursal_destino`),
  ADD KEY `id_tipo_encomienda` (`id_tipo_encomienda`),
  ADD KEY `id_usuario_creador` (`id_usuario_creador`),
  ADD KEY `id_usuario_receptor` (`id_usuario_receptor`),
  ADD KEY `id_transportista` (`id_transportista`),
  ADD KEY `envios_ibfk_7` (`id_serie`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD UNIQUE KEY `producto_almacen` (`id_producto`,`id_almacen`),
  ADD KEY `id_almacen` (`id_almacen`);

--
-- Indices de la tabla `items_paquete`
--
ALTER TABLE `items_paquete`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `lotes_pollos`
--
ALTER TABLE `lotes_pollos`
  ADD PRIMARY KEY (`id_lote`),
  ADD UNIQUE KEY `codigo_lote` (`codigo_lote`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_apertura` (`id_apertura`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_inventario` (`id_inventario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pagos_trabajadores`
--
ALTER TABLE `pagos_trabajadores`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_trabajador` (`id_trabajador`),
  ADD KEY `id_contrato` (`id_contrato`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pagos_venta`
--
ALTER TABLE `pagos_venta`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  ADD PRIMARY KEY (`id_paquete`),
  ADD UNIQUE KEY `codigo_paquete` (`codigo_paquete`),
  ADD KEY `id_envio` (`id_envio`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_rol`,`id_modulo`,`id_accion`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_accion` (`id_accion`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `tipo_doc_numero` (`tipo_persona`,`id_tipo_documento`,`numero_documento`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`);

--
-- Indices de la tabla `procesamiento_pollos`
--
ALTER TABLE `procesamiento_pollos`
  ADD PRIMARY KEY (`id_procesamiento`),
  ADD KEY `id_lote` (`id_lote`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD UNIQUE KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `seguimiento_envios`
--
ALTER TABLE `seguimiento_envios`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `id_envio` (`id_envio`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `series_comprobantes`
--
ALTER TABLE `series_comprobantes`
  ADD PRIMARY KEY (`id_serie`),
  ADD UNIQUE KEY `sucursal_tipo_serie` (`id_sucursal`,`id_tipo_comprobante`,`serie`),
  ADD KEY `id_tipo_comprobante` (`id_tipo_comprobante`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id_sucursal`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `tarifas_envio`
--
ALTER TABLE `tarifas_envio`
  ADD PRIMARY KEY (`id_tarifa`),
  ADD KEY `id_sucursal_origen` (`id_sucursal_origen`),
  ADD KEY `id_sucursal_destino` (`id_sucursal_destino`),
  ADD KEY `id_tipo_encomienda` (`id_tipo_encomienda`);

--
-- Indices de la tabla `tipo_comprobantes`
--
ALTER TABLE `tipo_comprobantes`
  ADD PRIMARY KEY (`id_tipo_comprobante`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `tipo_documentos`
--
ALTER TABLE `tipo_documentos`
  ADD PRIMARY KEY (`id_tipo_documento`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `abreviatura` (`abreviatura`);

--
-- Indices de la tabla `tipo_encomiendas`
--
ALTER TABLE `tipo_encomiendas`
  ADD PRIMARY KEY (`id_tipo_encomienda`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id_trabajador`),
  ADD UNIQUE KEY `id_persona` (`id_persona`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `transportistas`
--
ALTER TABLE `transportistas`
  ADD PRIMARY KEY (`id_transportista`),
  ADD UNIQUE KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD PRIMARY KEY (`id_usuario`,`id_rol`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD UNIQUE KEY `serie_numero` (`id_serie`,`numero_comprobante`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acciones`
--
ALTER TABLE `acciones`
  MODIFY `id_accion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  MODIFY `id_almacen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `aperturas_caja`
--
ALTER TABLE `aperturas_caja`
  MODIFY `id_apertura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id_caja` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_correo`
--
ALTER TABLE `configuracion_correo`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `configuracion_ticket`
--
ALTER TABLE `configuracion_ticket`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratos_trabajadores`
--
ALTER TABLE `contratos_trabajadores`
  MODIFY `id_contrato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_procesamiento`
--
ALTER TABLE `detalle_procesamiento`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documentos_envios`
--
ALTER TABLE `documentos_envios`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `envios`
--
ALTER TABLE `envios`
  MODIFY `id_envio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `items_paquete`
--
ALTER TABLE `items_paquete`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotes_pollos`
--
ALTER TABLE `lotes_pollos`
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `pagos_trabajadores`
--
ALTER TABLE `pagos_trabajadores`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_venta`
--
ALTER TABLE `pagos_venta`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  MODIFY `id_paquete` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `procesamiento_pollos`
--
ALTER TABLE `procesamiento_pollos`
  MODIFY `id_procesamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `seguimiento_envios`
--
ALTER TABLE `seguimiento_envios`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `series_comprobantes`
--
ALTER TABLE `series_comprobantes`
  MODIFY `id_serie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id_sucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tarifas_envio`
--
ALTER TABLE `tarifas_envio`
  MODIFY `id_tarifa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_comprobantes`
--
ALTER TABLE `tipo_comprobantes`
  MODIFY `id_tipo_comprobante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_documentos`
--
ALTER TABLE `tipo_documentos`
  MODIFY `id_tipo_documento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipo_encomiendas`
--
ALTER TABLE `tipo_encomiendas`
  MODIFY `id_tipo_encomienda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id_trabajador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transportistas`
--
ALTER TABLE `transportistas`
  MODIFY `id_transportista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `almacenes`
--
ALTER TABLE `almacenes`
  ADD CONSTRAINT `almacenes_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aperturas_caja`
--
ALTER TABLE `aperturas_caja`
  ADD CONSTRAINT `aperturas_caja_ibfk_1` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id_caja`) ON DELETE CASCADE,
  ADD CONSTRAINT `aperturas_caja_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`) ON DELETE CASCADE;

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD CONSTRAINT `cajas_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`id_serie`) REFERENCES `series_comprobantes` (`id_serie`) ON DELETE CASCADE,
  ADD CONSTRAINT `compras_ibfk_3` FOREIGN KEY (`id_proveedor`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE,
  ADD CONSTRAINT `compras_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `configuracion_ticket`
--
ALTER TABLE `configuracion_ticket`
  ADD CONSTRAINT `configuracion_ticket_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contratos_trabajadores`
--
ALTER TABLE `contratos_trabajadores`
  ADD CONSTRAINT `contratos_trabajadores_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_procesamiento`
--
ALTER TABLE `detalle_procesamiento`
  ADD CONSTRAINT `detalle_procesamiento_ibfk_1` FOREIGN KEY (`id_procesamiento`) REFERENCES `procesamiento_pollos` (`id_procesamiento`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_procesamiento_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `documentos_envios`
--
ALTER TABLE `documentos_envios`
  ADD CONSTRAINT `documentos_envios_ibfk_1` FOREIGN KEY (`id_envio`) REFERENCES `envios` (`id_envio`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_envios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `envios`
--
ALTER TABLE `envios`
  ADD CONSTRAINT `envios_ibfk_1` FOREIGN KEY (`id_sucursal_origen`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `envios_ibfk_2` FOREIGN KEY (`id_sucursal_destino`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `envios_ibfk_3` FOREIGN KEY (`id_tipo_encomienda`) REFERENCES `tipo_encomiendas` (`id_tipo_encomienda`) ON DELETE CASCADE,
  ADD CONSTRAINT `envios_ibfk_4` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `envios_ibfk_5` FOREIGN KEY (`id_usuario_receptor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `envios_ibfk_6` FOREIGN KEY (`id_transportista`) REFERENCES `transportistas` (`id_transportista`) ON DELETE CASCADE,
  ADD CONSTRAINT `envios_ibfk_7` FOREIGN KEY (`id_serie`) REFERENCES `series_comprobantes` (`id_serie`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`id_almacen`) REFERENCES `almacenes` (`id_almacen`) ON DELETE CASCADE;

--
-- Filtros para la tabla `items_paquete`
--
ALTER TABLE `items_paquete`
  ADD CONSTRAINT `items_paquete_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes` (`id_paquete`) ON DELETE CASCADE,
  ADD CONSTRAINT `items_paquete_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lotes_pollos`
--
ALTER TABLE `lotes_pollos`
  ADD CONSTRAINT `lotes_pollos_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE,
  ADD CONSTRAINT `lotes_pollos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`id_apertura`) REFERENCES `aperturas_caja` (`id_apertura`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_caja_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id_inventario`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos_trabajadores`
--
ALTER TABLE `pagos_trabajadores`
  ADD CONSTRAINT `pagos_trabajadores_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_trabajadores_ibfk_2` FOREIGN KEY (`id_contrato`) REFERENCES `contratos_trabajadores` (`id_contrato`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_trabajadores_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos_venta`
--
ALTER TABLE `pagos_venta`
  ADD CONSTRAINT `pagos_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_venta_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `paquetes`
--
ALTER TABLE `paquetes`
  ADD CONSTRAINT `paquetes_ibfk_1` FOREIGN KEY (`id_envio`) REFERENCES `envios` (`id_envio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `permisos_ibfk_3` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `personas`
--
ALTER TABLE `personas`
  ADD CONSTRAINT `personas_ibfk_1` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipo_documentos` (`id_tipo_documento`) ON DELETE CASCADE;

--
-- Filtros para la tabla `procesamiento_pollos`
--
ALTER TABLE `procesamiento_pollos`
  ADD CONSTRAINT `procesamiento_pollos_ibfk_1` FOREIGN KEY (`id_lote`) REFERENCES `lotes_pollos` (`id_lote`) ON DELETE CASCADE,
  ADD CONSTRAINT `procesamiento_pollos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seguimiento_envios`
--
ALTER TABLE `seguimiento_envios`
  ADD CONSTRAINT `seguimiento_envios_ibfk_1` FOREIGN KEY (`id_envio`) REFERENCES `envios` (`id_envio`) ON DELETE CASCADE,
  ADD CONSTRAINT `seguimiento_envios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `series_comprobantes`
--
ALTER TABLE `series_comprobantes`
  ADD CONSTRAINT `series_comprobantes_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `series_comprobantes_ibfk_2` FOREIGN KEY (`id_tipo_comprobante`) REFERENCES `tipo_comprobantes` (`id_tipo_comprobante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tarifas_envio`
--
ALTER TABLE `tarifas_envio`
  ADD CONSTRAINT `tarifas_envio_ibfk_1` FOREIGN KEY (`id_sucursal_origen`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarifas_envio_ibfk_2` FOREIGN KEY (`id_sucursal_destino`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarifas_envio_ibfk_3` FOREIGN KEY (`id_tipo_encomienda`) REFERENCES `tipo_encomiendas` (`id_tipo_encomienda`) ON DELETE CASCADE;

--
-- Filtros para la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD CONSTRAINT `trabajadores_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE,
  ADD CONSTRAINT `trabajadores_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE;

--
-- Filtros para la tabla `transportistas`
--
ALTER TABLE `transportistas`
  ADD CONSTRAINT `transportistas_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD CONSTRAINT `usuario_roles_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_roles_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_serie`) REFERENCES `series_comprobantes` (`id_serie`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`id_cliente`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
