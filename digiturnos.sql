-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-07-2025 a las 01:42:21
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
-- Base de datos: `digiturnos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`id`, `nombre`) VALUES
(1, 'Medicina General'),
(2, 'Odontología'),
(3, 'Pediatría'),
(4, 'Cardiología');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `especialidad_id` int(11) NOT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','llamado','atendido','no atendido') DEFAULT 'pendiente',
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id`, `cedula`, `especialidad_id`, `fecha_hora`, `estado`, `observacion`) VALUES
(1, '1120556', 2, '2025-04-01 01:29:47', 'atendido', 'No trajo plata'),
(2, '1120556', 2, '2025-04-01 01:36:19', 'no atendido', ''),
(3, '1120556', 1, '2025-04-01 01:36:49', 'atendido', ''),
(4, '255', 1, '2025-04-01 01:37:26', 'atendido', '3223'),
(5, '69996', 1, '2025-04-01 01:37:33', 'atendido', ''),
(6, '32654', 1, '2025-04-01 01:38:31', 'atendido', ''),
(7, '255', 1, '2025-04-01 01:57:57', 'llamado', NULL),
(8, '32654', 3, '2025-04-01 01:58:02', 'no atendido', ''),
(9, '255', 2, '2025-04-01 01:58:07', 'no atendido', ''),
(10, '69996', 3, '2025-04-01 01:58:11', 'no atendido', ''),
(11, '69996', 1, '2025-04-01 01:58:17', 'atendido', ''),
(12, '5', 4, '2025-04-01 02:03:52', 'llamado', NULL),
(13, '32', 1, '2025-04-01 02:05:52', 'llamado', NULL),
(14, '32', 2, '2025-04-01 02:06:16', 'llamado', NULL),
(15, '32', 2, '2025-04-01 02:08:44', 'no atendido', ''),
(16, '255', 1, '2025-04-01 02:09:38', 'atendido', ''),
(17, '255', 1, '2025-04-01 02:13:45', 'llamado', NULL),
(18, '255', 1, '2025-04-01 02:13:49', 'llamado', NULL),
(19, '32654', 1, '2025-04-01 23:26:03', 'llamado', NULL),
(20, '1120556', 2, '2025-04-02 00:40:05', 'llamado', NULL),
(21, '5', 2, '2025-04-02 01:06:27', 'atendido', 'kklhñkhñknñ'),
(22, '6546+454', 1, '2025-07-01 23:08:14', 'atendido', ''),
(23, '1654614646', 1, '2025-07-02 00:25:54', 'atendido', 'jefhfduigujy'),
(24, '156616', 2, '2025-07-03 00:57:42', 'atendido', ''),
(25, '168465468', 1, '2025-07-08 00:48:59', 'atendido', 'hgujkgu'),
(26, '1120556', 1, '2025-07-15 23:31:34', 'atendido', ''),
(27, '255', 1, '2025-07-15 23:37:57', 'atendido', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('recepcionista','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `especialidad_id` (`especialidad_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD CONSTRAINT `turnos_ibfk_1` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
