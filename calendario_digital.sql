-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-05-2025 a las 18:06:34
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
-- Base de datos: `calendario_digital`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `usuario_id`, `titulo`, `fecha`, `hora`, `categoria`, `descripcion`, `fecha_creacion`) VALUES
(1779, 25, 'Año Nuevo', '2025-01-01', NULL, 'party', 'Celebración del primer día del año', '2025-05-26 10:44:43'),
(1780, 25, 'Día de los Reyes Magos', '2025-01-06', NULL, 'party', 'Celebración tradicional de la Epifanía', '2025-05-26 10:44:43'),
(1781, 25, 'Día Mundial de la Paz', '2025-01-01', NULL, 'party', 'Día internacional de la paz', '2025-05-26 10:44:43'),
(1782, 25, 'Día de Martin Luther King Jr.', '2025-01-15', NULL, 'party', 'Conmemoración del líder de los derechos civiles', '2025-05-26 10:44:43'),
(1783, 25, 'Día Mundial de la Religión', '2025-01-21', NULL, 'party', 'Celebración de la diversidad religiosa', '2025-05-26 10:44:43'),
(1784, 25, 'Día de la Candelaria', '2025-02-02', NULL, 'party', 'Festividad cristiana tradicional', '2025-05-26 10:44:43'),
(1785, 25, 'Día de San Valentín', '2025-02-14', NULL, 'party', 'Día de los enamorados', '2025-05-26 10:44:43'),
(1786, 25, 'Día Mundial de la Justicia Social', '2025-02-20', NULL, 'party', 'Promoción de la justicia social', '2025-05-26 10:44:43'),
(1787, 25, 'Día Internacional de la Lengua Materna', '2025-02-21', NULL, 'party', 'Promoción de la diversidad lingüística', '2025-05-26 10:44:43'),
(1788, 25, 'Carnaval', '2025-02-28', NULL, 'party', 'Celebración tradicional antes de la Cuaresma', '2025-05-26 10:44:43'),
(1789, 25, 'Día Internacional de la Mujer', '2025-03-08', NULL, 'party', 'Celebración de los derechos de la mujer', '2025-05-26 10:44:43'),
(1790, 25, 'Día Mundial del Agua', '2025-03-22', NULL, 'party', 'Concienciación sobre la importancia del agua', '2025-05-26 10:44:43'),
(1791, 25, 'Día Internacional de la Felicidad', '2025-03-20', NULL, 'party', 'Promoción del bienestar y la felicidad', '2025-05-26 10:44:43'),
(1792, 25, 'Día Mundial de la Poesía', '2025-03-21', NULL, 'party', 'Celebración de la expresión poética', '2025-05-26 10:44:43'),
(1793, 25, 'Día Mundial del Teatro', '2025-03-27', NULL, 'party', 'Celebración del arte teatral', '2025-05-26 10:44:43'),
(1794, 25, 'Día Internacional del Libro', '2025-04-23', NULL, 'party', 'Promoción de la lectura y la literatura', '2025-05-26 10:44:43'),
(1795, 25, 'Día Mundial de la Salud', '2025-04-07', NULL, 'party', 'Concienciación sobre temas de salud global', '2025-05-26 10:44:43'),
(1796, 25, 'Día de la Tierra', '2025-04-22', NULL, 'party', 'Concienciación ambiental', '2025-05-26 10:44:43'),
(1797, 25, 'Día Mundial de la Danza', '2025-04-29', NULL, 'party', 'Celebración del arte de la danza', '2025-05-26 10:44:43'),
(1798, 25, 'Día Internacional del Deporte', '2025-04-06', NULL, 'party', 'Promoción del deporte y la actividad física', '2025-05-26 10:44:43'),
(1799, 25, 'Día del Trabajador', '2025-05-01', NULL, 'party', 'Celebración internacional del trabajo', '2025-05-26 10:44:43'),
(1800, 25, 'Día de la Madre', '2025-05-10', NULL, 'party', 'Homenaje a las madres', '2025-05-26 10:44:43'),
(1801, 25, 'Día Mundial de la Libertad de Prensa', '2025-05-03', NULL, 'party', 'Defensa de la libertad de expresión', '2025-05-26 10:44:43'),
(1802, 25, 'Día Mundial del Medio Ambiente', '2025-05-05', NULL, 'party', 'Concienciación ambiental', '2025-05-26 10:44:43'),
(1803, 25, 'Día Internacional de los Museos', '2025-05-18', NULL, 'party', 'Promoción del patrimonio cultural', '2025-05-26 10:44:43'),
(1804, 25, 'Día Mundial del Medio Ambiente', '2025-06-05', NULL, 'party', 'Concienciación sobre el medio ambiente', '2025-05-26 10:44:43'),
(1805, 25, 'Día Mundial de los Océanos', '2025-06-08', NULL, 'party', 'Protección de los océanos', '2025-05-26 10:44:43'),
(1806, 25, 'Día del Padre', '2025-06-21', NULL, 'party', 'Homenaje a los padres', '2025-05-26 10:44:43'),
(1807, 25, 'Día Internacional del Yoga', '2025-06-21', NULL, 'party', 'Promoción del bienestar físico y mental', '2025-05-26 10:44:43'),
(1808, 25, 'Día Mundial de la Música', '2025-06-21', NULL, 'party', 'Celebración de la música', '2025-05-26 10:44:43'),
(1809, 25, 'Día Mundial de la Población', '2025-07-11', NULL, 'party', 'Concienciación sobre temas demográficos', '2025-05-26 10:44:43'),
(1810, 25, 'Día Internacional de la Justicia Penal', '2025-07-17', NULL, 'party', 'Promoción de la justicia internacional', '2025-05-26 10:44:43'),
(1811, 25, 'Día Mundial contra la Hepatitis', '2025-07-28', NULL, 'party', 'Concienciación sobre la hepatitis', '2025-05-26 10:44:43'),
(1812, 25, 'Día Internacional de la Amistad', '2025-07-30', NULL, 'party', 'Celebración de la amistad', '2025-05-26 10:44:43'),
(1813, 25, 'Día Mundial de los Abuelos', '2025-07-26', NULL, 'party', 'Homenaje a los abuelos', '2025-05-26 10:44:43'),
(1814, 25, 'Día Internacional de la Juventud', '2025-08-12', NULL, 'party', 'Celebración de los jóvenes', '2025-05-26 10:44:43'),
(1815, 25, 'Día Mundial de la Fotografía', '2025-08-19', NULL, 'party', 'Celebración del arte fotográfico', '2025-05-26 10:44:43'),
(1816, 25, 'Día Internacional de los Pueblos Indígenas', '2025-08-09', NULL, 'party', 'Reconocimiento de los pueblos originarios', '2025-05-26 10:44:43'),
(1817, 25, 'Día Mundial del Gato', '2025-08-08', NULL, 'party', 'Celebración de los felinos domésticos', '2025-05-26 10:44:43'),
(1818, 25, 'Día Internacional de la Solidaridad', '2025-08-31', NULL, 'party', 'Promoción de la solidaridad humana', '2025-05-26 10:44:43'),
(1819, 25, 'Día Internacional de la Paz', '2025-09-21', NULL, 'party', 'Promoción de la paz mundial', '2025-05-26 10:44:43'),
(1820, 25, 'Día Mundial del Turismo', '2025-09-27', NULL, 'party', 'Promoción del turismo sostenible', '2025-05-26 10:44:43'),
(1821, 25, 'Día Internacional de la Caridad', '2025-09-05', NULL, 'party', 'Promoción de obras caritativas', '2025-05-26 10:44:43'),
(1822, 25, 'Día Mundial del Corazón', '2025-09-29', NULL, 'party', 'Concienciación sobre enfermedades cardiovasculares', '2025-05-26 10:44:43'),
(1823, 25, 'Día Internacional de la Alfabetización', '2025-09-08', NULL, 'party', 'Promoción de la educación', '2025-05-26 10:44:43'),
(1824, 25, 'Día Mundial de los Animales', '2025-10-04', NULL, 'party', 'Protección y bienestar animal', '2025-05-26 10:44:43'),
(1825, 25, 'Día Mundial de los Docentes', '2025-10-05', NULL, 'party', 'Reconocimiento a los educadores', '2025-05-26 10:44:43'),
(1826, 25, 'Día Mundial de la Salud Mental', '2025-10-10', NULL, 'party', 'Concienciación sobre salud mental', '2025-05-26 10:44:43'),
(1827, 25, 'Día de las Naciones Unidas', '2025-10-24', NULL, 'party', 'Celebración de la ONU', '2025-05-26 10:44:43'),
(1828, 25, 'Halloween', '2025-10-31', NULL, 'party', 'Celebración tradicional de Halloween', '2025-05-26 10:44:43'),
(1829, 25, 'Día de Todos los Santos', '2025-11-01', NULL, 'party', 'Festividad cristiana tradicional', '2025-05-26 10:44:43'),
(1830, 25, 'Día Mundial de la Ciencia', '2025-11-10', NULL, 'party', 'Promoción de la ciencia', '2025-05-26 10:44:43'),
(1831, 25, 'Día Internacional del Estudiante', '2025-11-17', NULL, 'party', 'Celebración de los estudiantes', '2025-05-26 10:44:43'),
(1832, 25, 'Día Mundial de la Filosofía', '2025-11-21', NULL, 'party', 'Promoción del pensamiento filosófico', '2025-05-26 10:44:43'),
(1833, 25, 'Día Internacional contra la Violencia de Género', '2025-11-25', NULL, 'party', 'Lucha contra la violencia hacia las mujeres', '2025-05-26 10:44:43'),
(1834, 25, 'Día Mundial del SIDA', '2025-12-01', NULL, 'party', 'Concienciación sobre el VIH/SIDA', '2025-05-26 10:44:43'),
(1835, 25, 'Día Internacional de los Derechos Humanos', '2025-12-10', NULL, 'party', 'Promoción de los derechos humanos', '2025-05-26 10:44:43'),
(1836, 25, 'Nochebuena', '2025-12-24', NULL, 'party', 'Víspera de Navidad', '2025-05-26 10:44:43'),
(1837, 25, 'Navidad', '2025-12-25', NULL, 'party', 'Celebración del nacimiento de Jesucristo', '2025-05-26 10:44:43'),
(1838, 25, 'Nochevieja', '2025-12-31', NULL, 'party', 'Último día del año', '2025-05-26 10:44:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `festivos_globales`
--

CREATE TABLE `festivos_globales` (
  `id` int(11) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mes` int(2) NOT NULL,
  `dia` int(2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `festivos_globales`
--

INSERT INTO `festivos_globales` (`id`, `titulo`, `mes`, `dia`, `descripcion`, `fecha_creacion`) VALUES
(1, 'Año Nuevo', 1, 1, 'Celebración del primer día del año', '2025-05-23 19:28:26'),
(2, 'Día de los Reyes Magos', 1, 6, 'Celebración tradicional de la Epifanía', '2025-05-23 19:28:26'),
(3, 'Día Mundial de la Paz', 1, 1, 'Día internacional de la paz', '2025-05-23 19:28:26'),
(4, 'Día de Martin Luther King Jr.', 1, 15, 'Conmemoración del líder de los derechos civiles', '2025-05-23 19:28:26'),
(5, 'Día Mundial de la Religión', 1, 21, 'Celebración de la diversidad religiosa', '2025-05-23 19:28:26'),
(6, 'Día de la Candelaria', 2, 2, 'Festividad cristiana tradicional', '2025-05-23 19:28:26'),
(7, 'Día de San Valentín', 2, 14, 'Día de los enamorados', '2025-05-23 19:28:26'),
(8, 'Día Mundial de la Justicia Social', 2, 20, 'Promoción de la justicia social', '2025-05-23 19:28:26'),
(9, 'Día Internacional de la Lengua Materna', 2, 21, 'Promoción de la diversidad lingüística', '2025-05-23 19:28:26'),
(10, 'Carnaval', 2, 28, 'Celebración tradicional antes de la Cuaresma', '2025-05-23 19:28:26'),
(11, 'Día Internacional de la Mujer', 3, 8, 'Celebración de los derechos de la mujer', '2025-05-23 19:28:26'),
(12, 'Día Mundial del Agua', 3, 22, 'Concienciación sobre la importancia del agua', '2025-05-23 19:28:26'),
(13, 'Día Internacional de la Felicidad', 3, 20, 'Promoción del bienestar y la felicidad', '2025-05-23 19:28:26'),
(14, 'Día Mundial de la Poesía', 3, 21, 'Celebración de la expresión poética', '2025-05-23 19:28:26'),
(15, 'Día Mundial del Teatro', 3, 27, 'Celebración del arte teatral', '2025-05-23 19:28:26'),
(16, 'Día Internacional del Libro', 4, 23, 'Promoción de la lectura y la literatura', '2025-05-23 19:28:26'),
(17, 'Día Mundial de la Salud', 4, 7, 'Concienciación sobre temas de salud global', '2025-05-23 19:28:26'),
(18, 'Día de la Tierra', 4, 22, 'Concienciación ambiental', '2025-05-23 19:28:26'),
(19, 'Día Mundial de la Danza', 4, 29, 'Celebración del arte de la danza', '2025-05-23 19:28:26'),
(20, 'Día Internacional del Deporte', 4, 6, 'Promoción del deporte y la actividad física', '2025-05-23 19:28:26'),
(21, 'Día del Trabajador', 5, 1, 'Celebración internacional del trabajo', '2025-05-23 19:28:26'),
(22, 'Día de la Madre', 5, 10, 'Homenaje a las madres', '2025-05-23 19:28:26'),
(23, 'Día Mundial de la Libertad de Prensa', 5, 3, 'Defensa de la libertad de expresión', '2025-05-23 19:28:26'),
(24, 'Día Mundial del Medio Ambiente', 5, 5, 'Concienciación ambiental', '2025-05-23 19:28:26'),
(25, 'Día Internacional de los Museos', 5, 18, 'Promoción del patrimonio cultural', '2025-05-23 19:28:26'),
(26, 'Día Mundial del Medio Ambiente', 6, 5, 'Concienciación sobre el medio ambiente', '2025-05-23 19:28:26'),
(27, 'Día Mundial de los Océanos', 6, 8, 'Protección de los océanos', '2025-05-23 19:28:26'),
(28, 'Día del Padre', 6, 21, 'Homenaje a los padres', '2025-05-23 19:28:26'),
(29, 'Día Internacional del Yoga', 6, 21, 'Promoción del bienestar físico y mental', '2025-05-23 19:28:26'),
(30, 'Día Mundial de la Música', 6, 21, 'Celebración de la música', '2025-05-23 19:28:26'),
(31, 'Día Mundial de la Población', 7, 11, 'Concienciación sobre temas demográficos', '2025-05-23 19:28:26'),
(32, 'Día Internacional de la Justicia Penal', 7, 17, 'Promoción de la justicia internacional', '2025-05-23 19:28:26'),
(33, 'Día Mundial contra la Hepatitis', 7, 28, 'Concienciación sobre la hepatitis', '2025-05-23 19:28:26'),
(34, 'Día Internacional de la Amistad', 7, 30, 'Celebración de la amistad', '2025-05-23 19:28:26'),
(35, 'Día Mundial de los Abuelos', 7, 26, 'Homenaje a los abuelos', '2025-05-23 19:28:26'),
(36, 'Día Internacional de la Juventud', 8, 12, 'Celebración de los jóvenes', '2025-05-23 19:28:26'),
(37, 'Día Mundial de la Fotografía', 8, 19, 'Celebración del arte fotográfico', '2025-05-23 19:28:26'),
(38, 'Día Internacional de los Pueblos Indígenas', 8, 9, 'Reconocimiento de los pueblos originarios', '2025-05-23 19:28:26'),
(39, 'Día Mundial del Gato', 8, 8, 'Celebración de los felinos domésticos', '2025-05-23 19:28:26'),
(40, 'Día Internacional de la Solidaridad', 8, 31, 'Promoción de la solidaridad humana', '2025-05-23 19:28:26'),
(41, 'Día Internacional de la Paz', 9, 21, 'Promoción de la paz mundial', '2025-05-23 19:28:26'),
(42, 'Día Mundial del Turismo', 9, 27, 'Promoción del turismo sostenible', '2025-05-23 19:28:26'),
(43, 'Día Internacional de la Caridad', 9, 5, 'Promoción de obras caritativas', '2025-05-23 19:28:26'),
(44, 'Día Mundial del Corazón', 9, 29, 'Concienciación sobre enfermedades cardiovasculares', '2025-05-23 19:28:26'),
(45, 'Día Internacional de la Alfabetización', 9, 8, 'Promoción de la educación', '2025-05-23 19:28:26'),
(46, 'Día Mundial de los Animales', 10, 4, 'Protección y bienestar animal', '2025-05-23 19:28:26'),
(47, 'Día Mundial de los Docentes', 10, 5, 'Reconocimiento a los educadores', '2025-05-23 19:28:26'),
(48, 'Día Mundial de la Salud Mental', 10, 10, 'Concienciación sobre salud mental', '2025-05-23 19:28:26'),
(49, 'Día de las Naciones Unidas', 10, 24, 'Celebración de la ONU', '2025-05-23 19:28:26'),
(50, 'Halloween', 10, 31, 'Celebración tradicional de Halloween', '2025-05-23 19:28:26'),
(51, 'Día de Todos los Santos', 11, 1, 'Festividad cristiana tradicional', '2025-05-23 19:28:26'),
(52, 'Día Mundial de la Ciencia', 11, 10, 'Promoción de la ciencia', '2025-05-23 19:28:26'),
(53, 'Día Internacional del Estudiante', 11, 17, 'Celebración de los estudiantes', '2025-05-23 19:28:26'),
(54, 'Día Mundial de la Filosofía', 11, 21, 'Promoción del pensamiento filosófico', '2025-05-23 19:28:26'),
(55, 'Día Internacional contra la Violencia de Género', 11, 25, 'Lucha contra la violencia hacia las mujeres', '2025-05-23 19:28:26'),
(56, 'Día Mundial del SIDA', 12, 1, 'Concienciación sobre el VIH/SIDA', '2025-05-23 19:28:26'),
(57, 'Día Internacional de los Derechos Humanos', 12, 10, 'Promoción de los derechos humanos', '2025-05-23 19:28:26'),
(58, 'Nochebuena', 12, 24, 'Víspera de Navidad', '2025-05-23 19:28:26'),
(59, 'Navidad', 12, 25, 'Celebración del nacimiento de Jesucristo', '2025-05-23 19:28:26'),
(60, 'Nochevieja', 12, 31, 'Último día del año', '2025-05-23 19:28:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `fecha_registro`, `remember_token`, `email_verified`, `verification_token`, `reset_token`, `token_expires_at`) VALUES
(25, 'test', 'test@localhost', '$2y$10$ZymBLRhRrP64lvjSvnDqbuGJ7tuOMA5V23pzI3PUGZGj69sYAayzi', '2025-05-26 10:44:43', NULL, 1, NULL, NULL, '2025-05-27 12:44:43');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `festivos_globales`
--
ALTER TABLE `festivos_globales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_festivo` (`mes`,`dia`,`titulo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2037;

--
-- AUTO_INCREMENT de la tabla `festivos_globales`
--
ALTER TABLE `festivos_globales`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
