-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-07-2025 a las 21:44:49
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
-- Base de datos: `base_gestion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad`
--

CREATE TABLE `actividad` (
  `id_actividad` int(9) NOT NULL,
  `id_usuario` int(9) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `descripcion` text NOT NULL,
  `id_estado_actividad` int(9) NOT NULL,
  `id_lista_actividades` int(9) NOT NULL,
  `id_grado_actividad` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividad`
--

INSERT INTO `actividad` (`id_actividad`, `id_usuario`, `fecha_inicio`, `fecha_fin`, `titulo`, `descripcion`, `id_estado_actividad`, `id_lista_actividades`, `id_grado_actividad`) VALUES
(15, 2, '2025-07-02', '2025-07-22', 'comprar harina Pan', '0', 1, 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `balance`
--

CREATE TABLE `balance` (
  `id_balance` int(9) NOT NULL,
  `total_moneda` int(9) NOT NULL,
  `id_usuario` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `balance`
--

INSERT INTO `balance` (`id_balance`, `total_moneda`, `id_usuario`) VALUES
(1, 25, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_actividad`
--

CREATE TABLE `estado_actividad` (
  `id_estado_actividad` int(9) NOT NULL,
  `estado` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_actividad`
--

INSERT INTO `estado_actividad` (`id_estado_actividad`, `estado`) VALUES
(1, 'Completada'),
(2, 'Incompleta'),
(3, 'En Proceso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_logro`
--

CREATE TABLE `estado_logro` (
  `id_estado_logro` int(9) NOT NULL,
  `estado` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_logro`
--

INSERT INTO `estado_logro` (`id_estado_logro`, `estado`) VALUES
(1, 'Incompleto'),
(2, 'Completo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grado_actividad`
--

CREATE TABLE `grado_actividad` (
  `id_grado_actividad` int(9) NOT NULL,
  `grado` varchar(40) NOT NULL,
  `recompensa` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grado_actividad`
--

INSERT INTO `grado_actividad` (`id_grado_actividad`, `grado`, `recompensa`) VALUES
(1, 'Basico', 20),
(2, 'Intermedio', 45),
(3, 'Importante', 80);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(9) NOT NULL,
  `id_usuario` int(9) NOT NULL,
  `id_item` int(9) NOT NULL,
  `cantidad` int(9) NOT NULL DEFAULT 1,
  `fecha_adquisicion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_inventario`, `id_usuario`, `id_item`, `cantidad`, `fecha_adquisicion`) VALUES
(1, 2, 1, 2, '2025-07-12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `item`
--

CREATE TABLE `item` (
  `id_item` int(9) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `descripcion` text NOT NULL,
  `id_tipo_item` int(9) NOT NULL,
  `valor` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `item`
--

INSERT INTO `item` (`id_item`, `nombre`, `descripcion`, `id_tipo_item`, `valor`) VALUES
(1, 'Goku', 'Es el goku en estado base, continua coleccionandolos  todos para revelar el increible secreto de las bolas\r\n', 1, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_actividades`
--

CREATE TABLE `lista_actividades` (
  `id_lista_actividades` int(9) NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lista_actividades`
--

INSERT INTO `lista_actividades` (`id_lista_actividades`, `nombre`) VALUES
(1, 'Lista actividades');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_logros`
--

CREATE TABLE `lista_logros` (
  `id_lista_logros` int(9) NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logro`
--

CREATE TABLE `logro` (
  `id_logro` int(9) NOT NULL,
  `id_usuario` int(9) NOT NULL,
  `id_lista_actividades` int(9) NOT NULL,
  `id_estado_logro` int(9) NOT NULL,
  `titulo` varchar(40) NOT NULL,
  `descripcion` text NOT NULL,
  `id_item` int(9) NOT NULL,
  `recompensa_dinero` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_item`
--

CREATE TABLE `tipo_item` (
  `id_tipo_item` int(9) NOT NULL,
  `tipo_nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_item`
--

INSERT INTO `tipo_item` (`id_tipo_item`, `tipo_nombre`) VALUES
(1, 'Coleccionable'),
(2, 'Foto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `contraseña` varchar(250) NOT NULL,
  `fecha_registro` date NOT NULL,
  `id_inventario` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `alias`, `contraseña`, `fecha_registro`, `id_inventario`) VALUES
(2, 'Armando', 'Armas', 'admin', '$2y$10$DQQIUcS8gn1zkCejrdBIcuT34MLkbmSenC3pJPWvvXupmouO.Odu6', '2025-07-11', 0),
(3, 'Marleny', 'Andrade', 'madre', '$2y$10$562d4Kq3uTGTncJfK.UlkOBEcb6C7gbmRBpRKN1yCLMyOeKDvVbVW', '2025-07-11', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad`
--
ALTER TABLE `actividad`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_estado_actividad` (`id_estado_actividad`),
  ADD KEY `id_lista_actividades` (`id_lista_actividades`),
  ADD KEY `id_grado_actividad` (`id_grado_actividad`);

--
-- Indices de la tabla `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`id_balance`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `estado_actividad`
--
ALTER TABLE `estado_actividad`
  ADD PRIMARY KEY (`id_estado_actividad`);

--
-- Indices de la tabla `estado_logro`
--
ALTER TABLE `estado_logro`
  ADD PRIMARY KEY (`id_estado_logro`);

--
-- Indices de la tabla `grado_actividad`
--
ALTER TABLE `grado_actividad`
  ADD PRIMARY KEY (`id_grado_actividad`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD UNIQUE KEY `uq_inventario_item_usuario` (`id_usuario`,`id_item`),
  ADD KEY `id_item` (`id_item`);

--
-- Indices de la tabla `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_tipo_item` (`id_tipo_item`);

--
-- Indices de la tabla `lista_actividades`
--
ALTER TABLE `lista_actividades`
  ADD PRIMARY KEY (`id_lista_actividades`);

--
-- Indices de la tabla `logro`
--
ALTER TABLE `logro`
  ADD PRIMARY KEY (`id_logro`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_estado_logro` (`id_estado_logro`),
  ADD KEY `id_lista_actividades` (`id_lista_actividades`),
  ADD KEY `id_item` (`id_item`);

--
-- Indices de la tabla `tipo_item`
--
ALTER TABLE `tipo_item`
  ADD PRIMARY KEY (`id_tipo_item`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad`
--
ALTER TABLE `actividad`
  MODIFY `id_actividad` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `balance`
--
ALTER TABLE `balance`
  MODIFY `id_balance` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estado_actividad`
--
ALTER TABLE `estado_actividad`
  MODIFY `id_estado_actividad` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estado_logro`
--
ALTER TABLE `estado_logro`
  MODIFY `id_estado_logro` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `grado_actividad`
--
ALTER TABLE `grado_actividad`
  MODIFY `id_grado_actividad` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `item`
--
ALTER TABLE `item`
  MODIFY `id_item` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `lista_actividades`
--
ALTER TABLE `lista_actividades`
  MODIFY `id_lista_actividades` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `logro`
--
ALTER TABLE `logro`
  MODIFY `id_logro` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_item`
--
ALTER TABLE `tipo_item`
  MODIFY `id_tipo_item` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividad`
--
ALTER TABLE `actividad`
  ADD CONSTRAINT `actividad_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `actividad_ibfk_2` FOREIGN KEY (`id_estado_actividad`) REFERENCES `estado_actividad` (`id_estado_actividad`),
  ADD CONSTRAINT `actividad_ibfk_3` FOREIGN KEY (`id_lista_actividades`) REFERENCES `lista_actividades` (`id_lista_actividades`),
  ADD CONSTRAINT `actividad_ibfk_4` FOREIGN KEY (`id_grado_actividad`) REFERENCES `grado_actividad` (`id_grado_actividad`);

--
-- Filtros para la tabla `balance`
--
ALTER TABLE `balance`
  ADD CONSTRAINT `balance_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`id_item`) REFERENCES `item` (`id_item`);

--
-- Filtros para la tabla `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`id_tipo_item`) REFERENCES `tipo_item` (`id_tipo_item`);

--
-- Filtros para la tabla `logro`
--
ALTER TABLE `logro`
  ADD CONSTRAINT `logro_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `logro_ibfk_2` FOREIGN KEY (`id_estado_logro`) REFERENCES `estado_logro` (`id_estado_logro`),
  ADD CONSTRAINT `logro_ibfk_3` FOREIGN KEY (`id_lista_actividades`) REFERENCES `lista_actividades` (`id_lista_actividades`),
  ADD CONSTRAINT `logro_ibfk_4` FOREIGN KEY (`id_item`) REFERENCES `item` (`id_item`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
