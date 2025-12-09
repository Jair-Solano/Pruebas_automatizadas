-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-07-2025 a las 03:29:19
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
-- Base de datos: `proyectofinal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura`
--

CREATE TABLE `detalle_factura` (
  `id` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal_linea` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_factura`
--

INSERT INTO `detalle_factura` (`id`, `id_factura`, `id_producto`, `nombre_producto`, `cantidad`, `precio_unitario`, `subtotal_linea`) VALUES
(4, 4, 6, 'Combo 6 - Callewrap de pollo', 3, 6.25, 18.75),
(5, 5, 5, 'Combo 5 - Callepollo Crispy', 1, 6.75, 6.75),
(6, 5, 6, 'Combo 6 - Callewrap de pollo', 2, 6.25, 12.50),
(7, 6, 7, 'Combo 7 - Callepollo BBQ', 2, 6.80, 13.60),
(8, 7, 7, 'Combo 7 - Callepollo BBQ', 2, 6.80, 13.60);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_factura` datetime NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `envio` decimal(10,2) NOT NULL,
  `itbms` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `direccion_envio` varchar(255) NOT NULL,
  `correo_cliente` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `id_usuario`, `fecha_factura`, `subtotal`, `envio`, `itbms`, `total`, `metodo_pago`, `direccion_envio`, `correo_cliente`, `created_at`) VALUES
(4, 1, '2025-07-29 02:17:54', 18.75, 0.00, 1.31, 20.06, 'efectivo', 'Calle Avenida Norte, Natá', 'solanojair12@gmail.com', '2025-07-29 00:17:54'),
(5, 1, '2025-07-29 02:52:43', 19.25, 0.00, 1.35, 20.60, 'efectivo', 'Calle Avenida Norte, Natá', 'solanojair12@gmail.com', '2025-07-29 00:52:43'),
(6, 1, '2025-07-29 03:10:57', 13.60, 0.00, 0.95, 14.55, 'efectivo', 'Calle Avenida Norte, Natá', 'solanojair12@gmail.com', '2025-07-29 01:10:57'),
(7, 1, '2025-07-29 03:12:15', 13.60, 0.00, 0.95, 14.55, 'efectivo', 'Calle Avenida Norte, Natá', 'solanojair12@gmail.com', '2025-07-29 01:12:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(5,2) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria` varchar(100) NOT NULL DEFAULT 'Indefinido',
  `imagen` varchar(100) NOT NULL,
  `en_carrusel` tinyint(1) DEFAULT 0,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`ID`, `nombre`, `precio`, `descripcion`, `categoria`, `imagen`, `en_carrusel`, `stock`) VALUES
(1, 'Combo 1 - Calleburger de carne', 6.50, 'Hamburguesa de carne con papas fritas y soda', 'combo', 'combo1.png', 0, 4),
(2, 'Combo 2 - Calleburger de pollo', 6.50, 'Hamburguesa de pollo con papas fritas y soda', 'combo', 'combo2.png', 1, 6),
(3, 'Combo 3 - Callepollo Asado', 6.50, 'Pieza de pollo asado con papas fritas y soda', 'combo', 'combo3.png', 0, 19),
(4, 'Combo 4 - Alitas-jón', 4.50, 'Porción de alitas de pollo con papas fritas y soda', 'combo', 'combo4.png', 0, 3),
(5, 'Combo 5 - Callepollo Crispy', 6.75, 'Pieza de pollo crujiente con papas fritas y soda', 'combo', 'pollocrispy.jpg', 1, 2),
(6, 'Combo 6 - Callewrap de pollo', 6.25, 'Wrap de pollo con papas fritas y soda', 'combo', 'pollowrap.jpg', 1, 1),
(7, 'Combo 7 - Callepollo BBQ', 6.80, 'Pieza de pollo a la BBQ con papas fritas y soda', 'combo', 'pollobbq.jpg', 1, 5),
(8, 'Combo 8 - Nuggets Callejeros', 5.50, 'Nuggets de pollo con papas fritas y soda', 'combo', 'combo8.png', 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `cedula` varchar(13) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `contraseña` varchar(300) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `rol` int(1) NOT NULL DEFAULT 0 COMMENT '0-empleado\r\n1-administrador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `cedula`, `usuario`, `contraseña`, `correo`, `rol`) VALUES
(1, '8-1013-271', '@Jair-1221', '$2y$10$lqJGFJJfxgk3NmUfE9TOVO9krFF0HT77k.axJrvA/gg88QA.emdZO', 'solanojair12@gmail.com', 1),
(2, '8-971-2411', 'Jorge-2411', '$2y$10$NnazzKL4jZHmU8tiUUEeE./8UNN8E39AHzlG58/5PimPfUy0gv.UK', 'jorge.vanegas@utp.ac.pa', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_factura` (`id_factura`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD CONSTRAINT `detalle_factura_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
