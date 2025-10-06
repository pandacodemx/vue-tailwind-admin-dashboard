-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-10-2025 a las 23:53:46
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
-- Base de datos: `sistema_barberia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activo`) VALUES
(1, 'Aceites Naturales', 'Variedad de aceites ', 1),
(2, 'Tinte de Cabello', 'Multiples ', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `notas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(11,2) NOT NULL DEFAULT 0.00,
  `pagado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_pago` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `cliente_id`, `fecha`, `fecha_fin`, `estado`, `notas`, `created_at`, `total`, `pagado`, `fecha_pago`) VALUES
(22, 1, '2025-08-16 13:00:00', '2025-08-16 14:00:00', 'cancelada', 'Corte de cabello para adulto estudiante.', '2025-08-15 18:50:12', 0.00, 0, NULL),
(23, 2, '2025-08-15 15:00:00', '2025-08-15 16:45:00', 'atendida', '', '2025-08-15 19:39:38', 0.00, 0, NULL),
(24, 3, '2025-08-16 12:00:00', '2025-08-16 13:00:00', 'atendida', '', '2025-08-15 19:41:55', 0.00, 1, '2025-08-18 12:04:33'),
(25, 1, '2025-08-16 13:00:00', '2025-08-16 14:45:00', 'atendida', '', '2025-08-15 19:57:58', 0.00, 1, '2025-08-18 12:00:15'),
(26, 3, '2025-08-18 13:00:00', '2025-08-18 14:45:00', 'atendida', 'Cita Mario Dominguez texto texto texto ', '2025-08-18 16:58:10', 0.00, 1, '2025-08-18 16:01:16'),
(29, 1, '2025-08-18 17:00:00', '2025-08-18 18:45:00', 'pendiente', '', '2025-08-18 17:38:38', 215.00, 0, NULL),
(30, 2, '2025-08-19 10:00:00', '2025-08-19 11:30:00', 'pendiente', 'Prueba Servicio ', '2025-08-19 15:58:38', 450.00, 0, NULL),
(34, 2, '2025-08-20 10:22:00', '2025-08-20 12:52:00', 'pendiente', 'werfsd', '2025-08-19 16:22:34', 570.00, 0, NULL),
(35, 1, '2025-08-19 16:00:00', '2025-08-19 16:45:00', 'pendiente', 'as', '2025-08-19 18:02:30', 95.00, 0, NULL),
(36, 2, '2025-08-23 12:04:00', '2025-08-23 13:04:00', 'pendiente', '', '2025-08-19 18:04:57', 120.00, 0, NULL),
(37, 3, '2025-08-21 12:05:00', '2025-08-21 12:50:00', 'pendiente', '', '2025-08-19 18:05:28', 95.00, 0, NULL),
(38, 3, '2025-10-04 10:30:00', '2025-10-04 12:45:00', 'atendida', 'Prueba de ctia', '2025-10-02 20:30:36', 545.00, 1, '2025-10-06 13:53:21'),
(39, 2, '2025-10-02 15:59:00', '2025-10-02 19:14:00', 'atendida', '', '2025-10-02 21:59:40', 665.00, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cita_servicios`
--

CREATE TABLE `cita_servicios` (
  `id` int(11) NOT NULL,
  `cita_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `precio` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cita_servicios`
--

INSERT INTO `cita_servicios` (`id`, `cita_id`, `servicio_id`, `precio`) VALUES
(25, 22, 2, 0.00),
(26, 23, 2, 0.00),
(27, 23, 1, 0.00),
(28, 24, 2, 0.00),
(29, 25, 2, 0.00),
(30, 25, 1, 0.00),
(31, 26, 2, 0.00),
(32, 26, 1, 0.00),
(33, 29, 2, 120.00),
(34, 29, 1, 95.00),
(35, 30, 3, 450.00),
(36, 34, 3, 450.00),
(37, 34, 2, 120.00),
(38, 35, 1, 95.00),
(39, 36, 2, 120.00),
(40, 37, 1, 95.00),
(41, 38, 3, 450.00),
(42, 38, 1, 95.00),
(43, 39, 3, 450.00),
(44, 39, 2, 120.00),
(45, 39, 1, 95.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `correo`, `status`) VALUES
(1, 'Said Alfredo Peña Nava', '35310157801', 'nava.saidalfredo@gmail.com', 1),
(2, 'Valeria Ruiz Barocio', '353144859511', 'valeria.cristina@gmail.com', 1),
(3, 'Mario Dominguez', '12345687888', 'mario@correo.com', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes`
--

CREATE TABLE `paquetes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `detalles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetes`
--

INSERT INTO `paquetes` (`id`, `nombre`, `precio`, `duracion`, `status`, `detalles`) VALUES
(1, 'Paquete Papa+Hijo5', 541.50, '150', 1, ''),
(2, 'Alaciado+Niño', 517.75, '135', 0, ''),
(3, 'Niño+PAPA', 598.50, '195', 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_servicio`
--

CREATE TABLE `paquete_servicio` (
  `id` int(11) NOT NULL,
  `id_paquete` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_servicio`
--

INSERT INTO `paquete_servicio` (`id`, `id_paquete`, `id_servicio`) VALUES
(3, 2, 3),
(4, 2, 1),
(7, 1, 2),
(8, 1, 3),
(13, 3, 2),
(14, 3, 1),
(15, 3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `categoria`, `precio`, `stock`, `stock_minimo`, `imagen`, `descripcion`, `activo`, `fecha_registro`, `id_categoria`) VALUES
(1, 'Aceite Para Barba Aroma Cítricos, Don Porfirio, 30ML', 'Aceites Naturales', 310.00, 42, 5, 'uploads/productos/68deebf778c74_1759439863.webp', 'Aceite Para Barba Aroma Cítricos, Don Porfirio, 30ML Prueba12', 1, '2025-08-04 09:43:34', 1),
(2, 'Tinte de cabello Rubio', 'Aceites Naturales', 130.00, 0, 5, 'uploads/productos/68e4319108ba4_1759785361.webp', 'Tinte de cabello Rubio', 1, '2025-08-04 11:13:32', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `precio` decimal(11,2) NOT NULL,
  `duracion` int(11) NOT NULL,
  `detalles` text NOT NULL,
  `status` int(11) NOT NULL,
  `categoria` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `precio`, `duracion`, `detalles`, `status`, `categoria`) VALUES
(1, 'Corte de cabello Niño', 95.00, 45, 'CORTE DE CABELLO LOREMM LOREM', 1, 'barbería'),
(2, 'Corte de cabello Adulto', 120.00, 60, 'Corte para adulto', 1, 'barbería'),
(3, 'Alaciado Permantte', 450.00, 90, 'Texto detalles ', 1, 'estética');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','recepcionista') DEFAULT 'admin',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `first_name`, `last_name`, `telefono`, `email`, `password`, `rol`, `creado_en`) VALUES
(1, 'Administrador', 'Said Alfredo', 'Peña Nava', '3531015780', 'admin@correo.com', '$2y$10$tpivUhf2VVTAyc5JTrxzAOuCaQsQoApNyjdZpzs20wa8PpUDrAaVS', 'admin', '2025-08-02 18:33:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'completada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `total`, `usuario_id`, `cliente_id`, `metodo_pago`, `estado`) VALUES
(1, '2025-08-04 11:49:27', 1699.00, 1, NULL, 'efectivo', 'completada'),
(2, '2025-08-04 12:39:20', 1699.00, 1, NULL, 'efectivo', 'completada'),
(3, '2025-08-04 12:40:10', 1699.00, 1, NULL, 'efectivo', 'completada'),
(4, '2025-08-04 12:43:03', 310.00, 1, NULL, 'efectivo', 'completada'),
(5, '2025-08-04 12:58:45', 310.00, 1, NULL, 'efectivo', 'completada'),
(6, '2025-08-04 13:03:23', 310.00, 1, NULL, 'efectivo', 'completada'),
(7, '2025-08-04 13:05:07', 310.00, 1, NULL, 'efectivo', 'completada'),
(8, '2025-08-04 13:24:15', 310.00, 1, NULL, 'efectivo', 'completada'),
(9, '2025-08-04 13:24:50', 310.00, 1, NULL, 'efectivo', 'completada'),
(10, '2025-08-04 13:28:36', 1699.00, 1, NULL, 'efectivo', 'completada'),
(11, '2025-08-04 13:29:13', 1699.00, 1, NULL, 'efectivo', 'completada'),
(12, '2025-08-04 13:31:03', 1699.00, 1, NULL, 'efectivo', 'completada'),
(13, '2025-08-04 13:42:31', 310.00, 1, NULL, 'efectivo', 'completada'),
(14, '2025-08-05 14:23:33', 620.00, 1, NULL, 'efectivo', 'completada'),
(15, '2025-08-08 11:18:28', 310.00, 1, NULL, 'efectivo', 'completada'),
(16, '2025-08-15 14:00:10', 1240.00, 1, NULL, 'efectivo', 'completada'),
(17, '2025-08-18 10:57:07', 1060.00, 1, NULL, 'efectivo', 'completada'),
(18, '2025-10-02 15:55:54', 390.00, 1, NULL, 'efectivo', 'completada'),
(19, '2025-10-02 15:55:56', 390.00, 1, NULL, 'efectivo', 'completada'),
(20, '2025-10-02 15:55:57', 390.00, 1, NULL, 'efectivo', 'completada'),
(21, '2025-10-02 15:55:57', 390.00, 1, NULL, 'efectivo', 'completada'),
(22, '2025-10-06 15:17:16', 130.00, 1, NULL, 'efectivo', 'completada'),
(23, '2025-10-06 15:33:27', 1680.00, 1, NULL, 'transferencia', 'completada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta_detalles`
--

INSERT INTO `venta_detalles` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 2, 1, 1699.00, 1699.00),
(2, 2, 2, 1, 1699.00, 1699.00),
(3, 3, 2, 1, 1699.00, 1699.00),
(4, 4, 1, 1, 310.00, 310.00),
(5, 5, 1, 1, 310.00, 310.00),
(6, 6, 1, 1, 310.00, 310.00),
(7, 7, 1, 1, 310.00, 310.00),
(8, 8, 1, 1, 310.00, 310.00),
(9, 9, 1, 1, 310.00, 310.00),
(10, 10, 2, 1, 1699.00, 1699.00),
(11, 11, 2, 1, 1699.00, 1699.00),
(12, 12, 2, 1, 1699.00, 1699.00),
(13, 13, 1, 1, 310.00, 310.00),
(14, 14, 1, 2, 310.00, 620.00),
(15, 15, 1, 1, 310.00, 310.00),
(16, 16, 1, 1, 310.00, 310.00),
(17, 16, 1, 3, 310.00, 930.00),
(18, 17, 1, 3, 310.00, 930.00),
(19, 17, 2, 1, 130.00, 130.00),
(20, 18, 2, 3, 130.00, 390.00),
(21, 19, 2, 3, 130.00, 390.00),
(22, 20, 2, 3, 130.00, 390.00),
(23, 21, 2, 3, 130.00, 390.00),
(24, 22, 2, 1, 130.00, 130.00),
(25, 23, 1, 5, 310.00, 1550.00),
(26, 23, 2, 1, 130.00, 130.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cita_servicios`
--
ALTER TABLE `cita_servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `paquete_servicio`
--
ALTER TABLE `paquete_servicio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria` (`id_categoria`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `cita_servicios`
--
ALTER TABLE `cita_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paquete_servicio`
--
ALTER TABLE `paquete_servicio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `paquete_servicio`
--
ALTER TABLE `paquete_servicio`
  ADD CONSTRAINT `paquete_servicio_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paquete_servicio_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `venta_detalles_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `venta_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
