-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-06-2023 a las 19:19:58
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `api_victimas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `denuncia`
--

CREATE TABLE `denuncia` (
  `idDenuncia` int(11) NOT NULL,
  `hechos` varchar(255) DEFAULT NULL,
  `lugar` varchar(255) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `responsable` varchar(255) DEFAULT NULL,
  `idVictima` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `denuncia`
--

INSERT INTO `denuncia` (`idDenuncia`, `hechos`, `lugar`, `fecha`, `responsable`, `idVictima`) VALUES
(1, 'Asalto con violencia', 'Cancun, Q. Roo', '2023-05-25', 'Motociclista', 1),
(2, 'Pleito familiar', 'Chetumal, Q. Roo', '2023-06-08', 'Padre de familia', 2),
(3, 'Robo en casa', 'Bacalar, Q. Roo', '2023-06-10', 'Taxista', 3),
(4, 'Robo de auto', 'Chetumal, Q. Roo', '2023-06-08', 'Luis D', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `contrasena` varchar(128) NOT NULL,
  `correo` varchar(254) NOT NULL,
  `claveApi` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `nombre`, `contrasena`, `correo`, `claveApi`) VALUES
(1, 'daniel', '$2y$10$dYXjvxOrz4vNH3JN56OqLOKbVoHvlMOqa.0hMM8h16f', 'daniel@gmail.com', 'cd82d308393e0f42751f4c0f05c1d8a4'),
(2, 'luis', '$2y$10$BrxsTSWVZK40IPQVHpgDIOrPM78nfLHwW6.LqwjZxqA', 'luis@gmail.com', '531a48c2a84e72ff7b1476c5174ae90b'),
(3, 'Maria', '$2y$10$jNlwH0kPW.lNnQY3lbtQ1On7sNOlcJE.t70s7z95Xv7', 'maria@gmail.com', '37089e23be8da41a5b0d6f415b2c76c3'),
(4, 'maria', '$2y$10$JdExlxX.TtSW2DCSnb2vj.ZXt/Cht6jp2Y.a9nSyNzh', 'maria@gmail.com', '5bbdb2e36e0f9d50baa357ce5103f4d9'),
(5, 'jon', '$2y$10$9bNeQfKYB6JaQz.pEEsL3O1BTU1tYHlVCFVlXcS4wWx', 'jon@gmail.com', 'd8866930151c0602f361271d1983fbf5'),
(6, 'doe', '$2y$10$ZWUxWOk0CQtHKvcXL4PhYO7OOVVGUR1YpfmycC5w9UO', 'doe@gmail.com', 'a91fe799c82ec53389dd79f808e584b4'),
(7, 'ale', '$2y$10$bVIPuBEx/i/GFMKYilTye.oQE2T6QYBbH9DCjhwTfGB', 'ale@gmail.com', 'c46ff3bfdb5ea5e075098a3facbaa0d1'),
(8, 'dan', '$2y$10$fDEsHpbheZZBgTGF0b2eA.DakD78BgXkxHvRWik0eI9', 'dan@gmail.com', '36d2de91d84ca6d5027e5f59e37c97f9'),
(9, 'dani', '$2y$10$azDVrm.0zgy96JghT5yZYO0oBMnIaBVTHvGFeeO/LSf', 'dani@gmail.com', 'b8fcb1780d5983353e2daefd176b2b1c'),
(10, 'yos', '$2y$10$A9C3pydUi.2tjn7mlV1KKeWnE7Fjq4gZHOc3WrMqDZh', 'yos@gmail.com', '536073b80e1aa61f88c98a8c76398206'),
(11, 'prueba', '$2y$10$.KJHLDZSSHmhrmWsDOP6XeIapN4UigjvCHNFq3BgIcJ', 'prueba@gmail.com', '6fb443f6884d90b069b319ab241325b7'),
(12, 'usuario2', '$2y$10$l4xW6l5kObAXLV/T5ZNMXur8dV8nOCS36DFXzGoajdoHSgkDqlpvm', 'usuario2@gmail.com', 'a9d8c1857d9a53ac3c65bd0c452c3eca');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `victima`
--

CREATE TABLE `victima` (
  `idVictima` int(11) NOT NULL,
  `primerNombre` varchar(50) NOT NULL,
  `primerApellido` varchar(50) NOT NULL,
  `edad` int(11) NOT NULL,
  `genero` varchar(50) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `victima`
--

INSERT INTO `victima` (`idVictima`, `primerNombre`, `primerApellido`, `edad`, `genero`, `telefono`, `idUsuario`) VALUES
(1, 'Daniel', 'Garcia', 23, 'masculino', '9831814345', 1),
(2, 'Luis', 'Lopez', 30, 'masculino', '9831814355', 2),
(3, 'Maria', 'Gonzales', 33, 'femenino', '9831004040', 3),
(4, 'Fer', 'Martinez', 30, 'femenino', '9831114040', 4),
(5, 'Jon', 'Dalton', 45, 'masculino', '9831004020', 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `denuncia`
--
ALTER TABLE `denuncia`
  ADD PRIMARY KEY (`idDenuncia`),
  ADD KEY `idVictima` (`idVictima`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`),
  ADD KEY `correo` (`correo`);

--
-- Indices de la tabla `victima`
--
ALTER TABLE `victima`
  ADD PRIMARY KEY (`idVictima`),
  ADD KEY `id_usuario` (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `denuncia`
--
ALTER TABLE `denuncia`
  MODIFY `idDenuncia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `victima`
--
ALTER TABLE `victima`
  MODIFY `idVictima` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `denuncia`
--
ALTER TABLE `denuncia`
  ADD CONSTRAINT `denuncia_ibfk_1` FOREIGN KEY (`idVictima`) REFERENCES `victima` (`idVictima`);

--
-- Filtros para la tabla `victima`
--
ALTER TABLE `victima`
  ADD CONSTRAINT `victima_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
