-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 14-09-2023 a las 04:46:30
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `APIMarket`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_clientes`
--

CREATE TABLE `tbl_clientes` (
  `cedula_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(255) NOT NULL,
  `celular_cliente` int(11) DEFAULT NULL,
  `correo_cliente` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_clientes`
--

INSERT INTO `tbl_clientes` (`cedula_cliente`, `nombre_cliente`, `celular_cliente`, `correo_cliente`) VALUES
(99, 'Mak', 3099, 'mak@yahoo.es'),
(111, 'Alberto', 3024444, 'alberto@yahoo.es'),
(123, 'Jose Alberto', 302222, 'jose@yahoo.es'),
(8778, 'saka', 9090, 'zuki@yahoo.es'),
(119357, 'Juan Bossa', 302249, 'juanseb100@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_compra`
--

CREATE TABLE `tbl_compra` (
  `codigo_producto` int(11) NOT NULL,
  `cedula_cliente` int(11) NOT NULL,
  `fecha_compra` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_compra`
--

INSERT INTO `tbl_compra` (`codigo_producto`, `cedula_cliente`, `fecha_compra`) VALUES
(1, 123, '2023-09-30'),
(7, 123, '2023-09-30'),
(999, 99, '2023-09-30'),
(999, 123, '2023-09-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_productos`
--

CREATE TABLE `tbl_productos` (
  `codigo_producto` int(11) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `valor_producto` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_productos`
--

INSERT INTO `tbl_productos` (`codigo_producto`, `nombre_producto`, `valor_producto`) VALUES
(1, 'Carne', 15),
(4, 'apio', 6.1),
(7, 'pan', 50),
(24, 'verdura', 5.23),
(264, 'verdura', 9.23),
(999, 'pollo', 13),
(1001, 'pescado', 20);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_clientes`
--
ALTER TABLE `tbl_clientes`
  ADD PRIMARY KEY (`cedula_cliente`);

--
-- Indices de la tabla `tbl_compra`
--
ALTER TABLE `tbl_compra`
  ADD PRIMARY KEY (`codigo_producto`,`cedula_cliente`),
  ADD KEY `FK_CLIENTE_COMPRA` (`cedula_cliente`);

--
-- Indices de la tabla `tbl_productos`
--
ALTER TABLE `tbl_productos`
  ADD PRIMARY KEY (`codigo_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_productos`
--
ALTER TABLE `tbl_productos`
  MODIFY `codigo_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1002;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_compra`
--
ALTER TABLE `tbl_compra`
  ADD CONSTRAINT `FK_CLIENTE_COMPRA` FOREIGN KEY (`cedula_cliente`) REFERENCES `tbl_clientes` (`cedula_cliente`),
  ADD CONSTRAINT `FK_PRODUCTO_COMPRA` FOREIGN KEY (`codigo_producto`) REFERENCES `tbl_productos` (`codigo_producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
