-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-06-2025 a las 00:02:49
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
-- Base de datos: `campdeivid`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aa_compras`
--

CREATE TABLE `aa_compras` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ruta_id` int(11) NOT NULL,
  `precio_pagado` decimal(10,2) NOT NULL,
  `fecha_compra` datetime NOT NULL,
  `paypal_transaction_id` varchar(100) DEFAULT NULL,
  `payer_id` varchar(100) DEFAULT NULL,
  `payer_email` varchar(150) DEFAULT NULL,
  `estado_pago` varchar(50) DEFAULT NULL,
  `payer_name` varchar(200) DEFAULT NULL,
  `opcion_repostaje` tinyint(1) DEFAULT 0,
  `opcion_hoteles` tinyint(1) DEFAULT 0,
  `opcion_puntos` tinyint(1) DEFAULT 0,
  `opcion_extras` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aa_compras`
--

INSERT INTO `aa_compras` (`id`, `user_id`, `ruta_id`, `precio_pagado`, `fecha_compra`, `paypal_transaction_id`, `payer_id`, `payer_email`, `estado_pago`, `payer_name`, `opcion_repostaje`, `opcion_hoteles`, `opcion_puntos`, `opcion_extras`) VALUES
(7, 1, 5, 12.00, '2025-05-20 13:46:48', '0HE12333E77034037', 'KJ2ADE9B9L8RE', 'pepocero-buyer@hotmail.com', 'COMPLETED', 'test buyer', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aa_cupones`
--

CREATE TABLE `aa_cupones` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_descuento` enum('porcentaje','fijo') NOT NULL DEFAULT 'porcentaje',
  `valor_descuento` decimal(8,2) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `usos_maximos` int(11) DEFAULT NULL,
  `usos_actuales` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `aplicable_a` enum('todos','premium','gratis') NOT NULL DEFAULT 'todos',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aa_cupones`
--

INSERT INTO `aa_cupones` (`id`, `codigo`, `descripcion`, `tipo_descuento`, `valor_descuento`, `fecha_inicio`, `fecha_fin`, `usos_maximos`, `usos_actuales`, `activo`, `aplicable_a`, `created_at`, `updated_at`) VALUES
(1, 'BIENVENIDO50', 'Descuento de bienvenida', 'porcentaje', 50.00, '2025-06-06 00:20:00', '2025-06-10 20:00:00', 3, 0, 1, 'premium', '2025-06-05 22:35:03', '2025-06-05 22:36:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aa_cupones_uso`
--

CREATE TABLE `aa_cupones_uso` (
  `id` int(11) NOT NULL,
  `cupon_id` int(11) NOT NULL,
  `ruta_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `precio_original` decimal(8,2) NOT NULL,
  `descuento_aplicado` decimal(8,2) NOT NULL,
  `precio_final` decimal(8,2) NOT NULL,
  `fecha_uso` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aa_rutas`
--

CREATE TABLE `aa_rutas` (
  `id` int(5) NOT NULL,
  `nombre` tinytext NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `nivel` tinytext NOT NULL,
  `imagen` tinytext NOT NULL,
  `plan` tinytext NOT NULL,
  `paisaje` tinytext NOT NULL,
  `precio` decimal(5,2) NOT NULL,
  `gpx` tinytext NOT NULL,
  `distancia` decimal(10,2) NOT NULL,
  `tiempo` varchar(50) NOT NULL,
  `destacados` tinytext DEFAULT NULL,
  `descripcion_completa` text NOT NULL,
  `tiene_extras` tinyint(1) NOT NULL DEFAULT 0,
  `en_oferta` tinyint(1) DEFAULT 0,
  `porcentaje_oferta` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aa_rutas`
--

INSERT INTO `aa_rutas` (`id`, `nombre`, `descripcion`, `nivel`, `imagen`, `plan`, `paisaje`, `precio`, `gpx`, `distancia`, `tiempo`, `destacados`, `descripcion_completa`, `tiene_extras`, `en_oferta`, `porcentaje_oferta`) VALUES
(4, 'Silent Route modificada', 'Silent Route modificada por los tuneles de Pitarque', 'Domando Curvas', '../images/rutas/ruta_silent_route_modificada.jpg', 'Premium', 'Bonito', 25.00, 'gpx/ruta_silent_route_modificada.gpx', 497.00, '8 horas 21 minutos', 'Mirador, Paisaje, Monumento', 'Esta Silent Route esta modificada de tal forma que después de Ejulve, en el guante motero, se toma un camino que lleva a los tuneles de Pitarque. Los pasiajes son increibles y las curvas geniales. \r\nLa carretera en Aliaga y Pitarque estan un poco malos.\r\nLa vuelta esconde una sorpresa que pasa por un camino llamado \"El camino de los dinosaurios\", que es una pasada de ruta en medio de la montaña.', 0, 1, 20.00),
(5, 'Priorat', 'Recorrido con abundantes curvas en carreteras de montaña.', 'Piloto nuevo', '../images/rutas/ruta_priorat.jpg', 'Premium', 'Bosques mediterráneos', 5.00, 'gpx/ruta_priorat.gpx', 200.00, '3 horas', NULL, '<p>Discurre entre bosque y pueblos peque&ntilde;os de la comarca del Priorat.cxzx</p>', 0, 0, 0.00),
(10, 'Ruta de prueba 4', 'Descripcion corta', 'Domando Curvas', '../images/rutas/ruta_ruta_de_prueba_4.jpg', 'Premium', 'Rías y acantilados', 10.00, 'gpx/base/ruta_ruta_de_prueba_4.gpx', 40.00, '2', NULL, '<p>asdasdasasdasdadasda as as das dasd fds</p>', 0, 0, 0.00),
(11, 'Ruta de los dinosaurios', 'Descripcion corta', 'Domando Curvas', '../images/rutas/ruta_ruta_de_los_dinosaurios.jpg', 'Gratis', 'Montañas y bosques', 0.00, 'gpx/base/ruta_ruta_de_los_dinosaurios.gpx', 400.00, '3 horas', NULL, '<h3>Problemas clave:</h3>\r\n<ol start=\"1\">\r\n<li>\r\n<p class=\"ds-markdown-paragraph\"><strong>Permisos de escritura en directorios GPX</strong>:</p>\r\n<ul>\r\n<li>\r\n<p class=\"ds-markdown-paragraph\">Los directorios GPX (<code>base/</code>&nbsp;y&nbsp;<code>extras/</code>) se crean con&nbsp;<code>mkdir()</code>&nbsp;pero sin verificar permisos de escritura</p>\r\n</li>\r\n<li>\r\n<p class=\"ds-markdown-paragraph\">Los errores de subida no se manejan correctamente</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li>\r\n<p class=\"ds-markdown-paragraph\"><strong>Actualizaci&oacute;n de base de datos</strong>:</p>\r\n<ul>\r\n<li>\r\n<p class=\"ds-markdown-paragraph\">El m&eacute;todo&nbsp;<code>$db-&gt;update()</code>&nbsp;de UserSpice no est&aacute; funcionando como se espera</p>\r\n</li>\r\n<li>\r\n<p class=\"ds-markdown-paragraph\">Falta validar la conexi&oacute;n a la base de datos</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li>\r\n<p class=\"ds-markdown-paragraph\"><strong>Campos faltantes en el formulario</strong>:</p>\r\n<ul>\r\n<li>\r\n<p class=\"ds-markdown-paragraph\">El campo&nbsp;<code>precio</code>&nbsp;se oculta pero no se env&iacute;a cuando no es Premium</p>\r\n</li>\r\n<li>\r\n<p class=\"ds-markdown-paragraph\">Validaci&oacute;n incorrecta de campos</p>\r\n</li>\r\n</ul>\r\n</li>\r\n</ol>', 0, 0, 0.00),
(12, 'Ruta Prueba', 'fsadfsdfsdfsdfsdfsdfs', 'Piloto nuevo', '../images/rutas/ruta_ruta_prueba.jpg', 'Gratis', 'Costa y playas vírgenes', 0.00, 'gpx/base/ruta_ruta_prueba.gpx', 50.00, '2 horas', NULL, '&lt;p&gt;dsfdfsdfsdf df sdfsdfsdfsdf sdfsdfsdf sdfs df sdfdfsdfsdf&lt;/p&gt;', 0, 0, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aa_rutas_galeria`
--

CREATE TABLE `aa_rutas_galeria` (
  `id` int(11) NOT NULL,
  `ruta_id` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aa_rutas_galeria`
--

INSERT INTO `aa_rutas_galeria` (`id`, `ruta_id`, `imagen`, `orden`, `descripcion`, `fecha_subida`) VALUES
(1, 4, 'images/rutas/silent_route_modificada_gallery/img_1747905137_831_0.jpg', 1, '', '2025-05-22 09:12:17'),
(2, 4, 'images/rutas/silent_route_modificada_gallery/img_1747905137_442_1.jpg', 2, '', '2025-05-22 09:12:17'),
(3, 4, 'images/rutas/silent_route_modificada_gallery/img_1747905137_386_2.jpg', 3, '', '2025-05-22 09:12:17'),
(4, 4, 'images/rutas/silent_route_modificada_gallery/img_1747905137_605_3.jpg', 4, '', '2025-05-22 09:12:17'),
(5, 4, 'images/rutas/silent_route_modificada_gallery/img_1747905137_521_4.jpg', 5, '', '2025-05-22 09:12:17'),
(6, 4, 'images/rutas/silent_route_modificada_gallery/img_1747905137_455_5.jpg', 6, '', '2025-05-22 09:12:17'),
(7, 4, 'images/rutas/silent_route_modificada_gallery/img_1747905137_246_6.jpg', 7, '', '2025-05-22 09:12:17'),
(8, 5, 'images/rutas/priorat_gallery/img_1748050737_191_0.jpg', 1, '', '2025-05-24 01:38:57'),
(9, 5, 'images/rutas/priorat_gallery/img_1748050737_897_1.jpg', 2, '', '2025-05-24 01:38:57'),
(10, 5, 'images/rutas/priorat_gallery/img_1748050737_580_2.jpg', 3, '', '2025-05-24 01:38:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit`
--

CREATE TABLE `audit` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip` varchar(255) NOT NULL,
  `viewed` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `audit`
--

INSERT INTO `audit` (`id`, `user`, `page`, `timestamp`, `ip`, `viewed`) VALUES
(1, 0, '4', '2025-05-13 13:04:42', '::1', 0),
(2, 0, '115', '2025-05-13 13:04:43', '::1', 0),
(3, 0, '95', '2025-05-13 21:42:06', '::1', 0),
(4, 0, '115', '2025-05-13 22:22:31', '::1', 0),
(5, 0, '115', '2025-05-13 22:22:45', '::1', 0),
(6, 0, '115', '2025-05-13 22:31:55', '::1', 0),
(7, 0, '4', '2025-05-13 22:38:19', '::1', 0),
(8, 0, '115', '2025-05-13 23:16:21', '::1', 0),
(9, 0, '115', '2025-05-13 23:16:39', '::1', 0),
(10, 0, '95', '2025-05-13 23:20:34', '::1', 0),
(11, 0, '95', '2025-05-13 23:20:38', '::1', 0),
(12, 0, '95', '2025-05-13 23:21:24', '::1', 0),
(13, 0, '115', '2025-05-13 23:22:58', '::1', 0),
(14, 0, '132', '2025-05-26 18:44:09', '::1', 0),
(15, 0, '131', '2025-05-29 14:03:27', '::1', 0),
(16, 0, '116', '2025-06-04 22:33:39', '::1', 0),
(17, 0, '147', '2025-06-19 10:51:17', '::1', 0),
(18, 0, '4', '2025-06-19 10:51:19', '::1', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `crons`
--

CREATE TABLE `crons` (
  `id` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 1,
  `sort` int(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `createdby` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `crons`
--

INSERT INTO `crons` (`id`, `active`, `sort`, `name`, `file`, `createdby`, `created`, `modified`) VALUES
(1, 0, 100, 'Auto-Backup', 'backup.php', 1, '2017-09-16 07:49:22', '2017-11-11 20:15:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `crons_logs`
--

CREATE TABLE `crons_logs` (
  `id` int(11) NOT NULL,
  `cron_id` int(11) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email`
--

CREATE TABLE `email` (
  `id` int(11) NOT NULL,
  `website_name` varchar(100) NOT NULL,
  `smtp_server` varchar(100) NOT NULL,
  `smtp_port` int(10) NOT NULL,
  `email_login` varchar(150) NOT NULL,
  `email_pass` varchar(100) NOT NULL,
  `from_name` varchar(100) NOT NULL,
  `from_email` varchar(150) NOT NULL,
  `transport` varchar(255) NOT NULL,
  `verify_url` varchar(255) NOT NULL,
  `email_act` int(1) NOT NULL,
  `debug_level` int(1) NOT NULL DEFAULT 0,
  `isSMTP` int(1) NOT NULL DEFAULT 0,
  `isHTML` varchar(5) NOT NULL DEFAULT 'true',
  `useSMTPauth` varchar(6) NOT NULL DEFAULT 'true',
  `authtype` varchar(50) DEFAULT 'CRAM-MD5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `email`
--

INSERT INTO `email` (`id`, `website_name`, `smtp_server`, `smtp_port`, `email_login`, `email_pass`, `from_name`, `from_email`, `transport`, `verify_url`, `email_act`, `debug_level`, `isSMTP`, `isHTML`, `useSMTPauth`, `authtype`) VALUES
(1, 'Candeivid', 'smtp.gmail.com', 587, 'rutascandeivid@gmail.com', 'rzig imat zzlb adhj', 'Candeivid', 'rutascandeivid@gmail.com', 'tls', 'https://localhost/campdeivid/', 1, 0, 1, 'true', 'true', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups_menus`
--

CREATE TABLE `groups_menus` (
  `id` int(11) UNSIGNED NOT NULL,
  `group_id` int(11) UNSIGNED NOT NULL,
  `menu_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `groups_menus`
--

INSERT INTO `groups_menus` (`id`, `group_id`, `menu_id`) VALUES
(5, 0, 3),
(7, 0, 2),
(8, 0, 51),
(9, 0, 52),
(10, 0, 37),
(11, 0, 38),
(12, 2, 39),
(13, 2, 40),
(14, 2, 41),
(15, 2, 42),
(16, 2, 43),
(17, 2, 44),
(18, 2, 45),
(19, 0, 46),
(20, 0, 47),
(21, 0, 49),
(25, 0, 18),
(26, 0, 20),
(27, 0, 21),
(28, 0, 7),
(29, 0, 8),
(30, 2, 9),
(31, 2, 10),
(32, 2, 11),
(33, 2, 12),
(34, 2, 13),
(35, 2, 14),
(36, 2, 15),
(38, 1, 15),
(39, 0, 16),
(40, 0, 17),
(41, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `keys`
--

CREATE TABLE `keys` (
  `id` int(11) NOT NULL,
  `stripe_ts` varchar(255) NOT NULL,
  `stripe_tp` varchar(255) NOT NULL,
  `stripe_ls` varchar(255) NOT NULL,
  `stripe_lp` varchar(255) NOT NULL,
  `recap_pub` varchar(100) NOT NULL,
  `recap_pri` varchar(100) NOT NULL,
  `currency` varchar(3) DEFAULT 'usd',
  `paypal_email` varchar(255) DEFAULT NULL,
  `paypal_sandbox` varchar(5) DEFAULT 'TRUE',
  `paypal_callback` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `keys`
--

INSERT INTO `keys` (`id`, `stripe_ts`, `stripe_tp`, `stripe_ls`, `stripe_lp`, `recap_pub`, `recap_pri`, `currency`, `paypal_email`, `paypal_sandbox`, `paypal_callback`) VALUES
(1, '', '', '', '', '', '', 'usd', NULL, 'TRUE', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `logdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `logtype` varchar(25) NOT NULL,
  `lognote` mediumtext NOT NULL,
  `ip` varchar(75) DEFAULT NULL,
  `metadata` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `logdate`, `logtype`, `lognote`, `ip`, `metadata`) VALUES
(1, 1, '2022-12-23 12:05:38', 'System Updates', 'Update 2022-05-04a successfully deployed.', '::1', NULL),
(2, 1, '2022-12-23 12:05:43', 'login', 'User logged in.', '::1', NULL),
(3, 1, '2022-12-23 12:06:38', 'System Updates', 'Update 2022-11-06a successfully deployed.', '::1', NULL),
(4, 1, '2022-12-23 12:06:38', 'System Updates', 'Update 2022-11-20a successfully deployed.', '::1', NULL),
(5, 1, '2022-12-23 12:06:38', 'System Updates', 'Update 2022-12-04a successfully deployed.', '::1', NULL),
(6, 1, '2022-12-23 12:06:38', 'System Updates', 'Update 2022-12-22a successfully deployed.', '::1', NULL),
(7, 1, '2022-12-23 12:06:38', 'System Updates', 'Update 2022-12-23a successfully deployed.', '::1', NULL),
(8, 1, '2022-12-23 12:16:27', 'login', 'User logged in.', '::1', NULL),
(9, 1, '2024-09-25 09:30:55', 'System Updates', 'Update 2023-01-02a successfully deployed.', '::1', NULL),
(10, 1, '2024-09-25 09:30:55', 'System Updates', 'Update 2023-01-03a successfully deployed.', '::1', NULL),
(11, 1, '2024-09-25 09:30:55', 'System Updates', 'Update 2023-01-03b successfully deployed.', '::1', NULL),
(12, 1, '2024-09-25 09:30:55', 'System Updates', 'Update 2023-01-05a successfully deployed.', '::1', NULL),
(13, 1, '2024-09-25 09:30:55', 'System Updates', 'Update 2023-01-07a successfully deployed.', '::1', NULL),
(14, 1, '2024-09-25 09:30:55', 'System Updates', 'Update 2023-02-10a successfully deployed.', '::1', NULL),
(15, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2023-05-19a successfully deployed.', '::1', NULL),
(16, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2023-06-29a successfully deployed.', '::1', NULL),
(17, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2023-06-29b successfully deployed.', '::1', NULL),
(18, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2023-11-15a successfully deployed.', '::1', NULL),
(19, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2023-11-17a successfully deployed.', '::1', NULL),
(20, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-12a successfully deployed.', '::1', NULL),
(21, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-13a successfully deployed.', '::1', NULL),
(22, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-14a successfully deployed.', '::1', NULL),
(23, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-15a successfully deployed.', '::1', NULL),
(24, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-17a successfully deployed.', '::1', NULL),
(25, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-17b successfully deployed.', '::1', NULL),
(26, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-18a successfully deployed.', '::1', NULL),
(27, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-20a successfully deployed.', '::1', NULL),
(28, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-03-22a successfully deployed.', '::1', NULL),
(29, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-04-01a successfully deployed.', '::1', NULL),
(30, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-04-13a successfully deployed.', '::1', NULL),
(31, 1, '2024-09-25 09:30:56', 'System Updates', 'Update 2024-06-24a successfully deployed.', '::1', NULL),
(32, 1, '2024-09-25 09:31:58', 'login', 'User logged in.', '::1', NULL),
(33, 1, '2025-04-12 10:51:28', 'System Updates', 'Update 2024-09-25a successfully deployed.', '::1', NULL),
(34, 1, '2025-04-12 10:51:28', 'System Updates', 'Update 2024-11-22a successfully deployed.', '::1', NULL),
(35, 1, '2025-04-12 10:51:28', 'System Updates', 'Update 2024-12-16a successfully deployed.', '::1', NULL),
(36, 1, '2025-04-12 10:51:28', 'System Updates', 'Update 2024-12-21a successfully deployed.', '::1', NULL),
(37, 1, '2025-04-12 10:51:28', 'System Updates', 'Update 2025-02-23a successfully deployed.', '::1', NULL),
(38, 1, '2025-04-12 10:51:28', 'System Updates', 'Update 2025-03-02a successfully deployed.', '::1', NULL),
(39, 1, '2025-04-12 10:51:28', 'System Updates', 'Update 2025-03-03a successfully deployed.', '::1', NULL),
(40, 1, '2025-04-12 10:52:00', 'login', 'User logged in.', '::1', NULL),
(41, 1, '2025-04-12 10:52:55', 'User', 'Updated password.', '::1', NULL),
(42, 1, '2025-05-11 15:22:52', 'login', 'User logged in.', '::1', NULL),
(43, 1, '2025-05-11 16:21:42', 'Pages Manager', 'Added 2 permission(s) to pages/inicio.php.', '::1', NULL),
(44, 1, '2025-05-11 16:21:42', 'cleanupPermissionPageMatc', 'Removed 2 orphaned permissions', '::1', NULL),
(45, 1, '2025-05-11 16:22:18', 'Pages Manager', 'Added 2 permission(s) to pages/inicio.php.', '::1', NULL),
(46, 1, '2025-05-11 20:17:53', 'Email Settings', 'Updated verify_url from http://localhost/userspice to https://localhost/campdeivid/pages/inicio.php/.', '::1', NULL),
(47, 1, '2025-05-11 20:18:41', 'Email Settings', 'Updated verify_url from https://localhost/campdeivid/pages/inicio.php/ to https://localhost/campdeivid/.', '::1', NULL),
(48, 1, '2025-05-11 20:19:20', 'Menu Manager', 'Updated 16', '::1', NULL),
(49, 1, '2025-05-11 20:19:51', 'Menu Manager', 'Updated 17', '::1', NULL),
(50, 1, '2025-05-11 20:20:22', 'Menu Manager', 'Updated 1', '::1', NULL),
(51, 0, '2025-05-11 21:24:02', 'Login Fail', 'A failed login on login.php', '::1', NULL),
(52, 1, '2025-05-11 21:24:16', 'login', 'User logged in.', '::1', NULL),
(53, 1, '2025-05-11 22:20:32', 'USPlugins', 'store installed', '::1', NULL),
(54, 1, '2025-05-11 22:20:32', 'USPlugins', 'store Activated', '::1', NULL),
(55, 1, '2025-05-11 22:21:52', 'USPlugins', 'payments installed', '::1', NULL),
(56, 1, '2025-05-11 22:21:52', 'USPlugins', 'payments Activated', '::1', NULL),
(57, 1, '2025-05-11 22:28:28', 'USPlugins', 'downloads installed', '::1', NULL),
(58, 1, '2025-05-11 22:28:28', 'USPlugins', 'downloads Activated', '::1', NULL),
(59, 1, '2025-05-11 22:28:28', 'Migrations', '00001 migration triggered for downloads', '::1', NULL),
(60, 1, '2025-05-11 22:28:28', 'Migrations', '00002 migration triggered for downloads', '::1', NULL),
(61, 1, '2025-05-11 22:28:28', 'Migrations', '00003 migration triggered for downloads', '::1', NULL),
(62, 1, '2025-05-11 22:28:28', 'Migrations', '00004 migration triggered for downloads', '::1', NULL),
(63, 1, '2025-05-11 22:28:28', 'Migrations', '00007 migration triggered for downloads', '::1', NULL),
(64, 1, '2025-05-11 22:28:28', 'Migrations', '00008 migration triggered for downloads', '::1', NULL),
(65, 1, '2025-05-11 22:28:28', 'Migrations', '00009 migration triggered for downloads', '::1', NULL),
(66, 1, '2025-05-11 22:28:28', 'Migrations', '7 migration(s) successfully triggered for downloads', '::1', NULL),
(67, 1, '2025-05-12 12:58:52', 'Pages Manager', 'Added 2 permission(s) to pages/rutas.php.', '::1', NULL),
(68, 1, '2025-05-12 12:59:37', 'Pages Manager', 'Changed private from private to public for Page #114.', '::1', NULL),
(69, 1, '2025-05-12 13:11:33', 'Pages Manager', 'Changed private from private to public for Page #115.', '::1', NULL),
(70, 1, '2025-05-12 13:11:41', 'Pages Manager', 'Deleted 2 permission(s) from pages/rutas.php.', '::1', NULL),
(71, 1, '2025-05-12 14:02:12', 'Pages Manager', 'Changed private from public to private for Page #115.', '::1', NULL),
(72, 1, '2025-05-12 14:02:12', 'Pages Manager', 'Added 2 permission(s) to pages/ruta_detalle.php.', '::1', NULL),
(73, 1, '2025-05-12 15:47:10', 'Pages Manager', 'Added 1 permission(s) to pages/nueva_ruta.php.', '::1', NULL),
(74, 1, '2025-05-13 13:05:02', 'login', 'User logged in.', '::1', NULL),
(75, 1, '2025-05-13 21:42:26', 'login', 'User logged in.', '::1', NULL),
(76, 1, '2025-05-13 21:46:00', 'login', 'User logged in.', '::1', NULL),
(77, 1, '2025-05-13 22:06:51', 'login', 'User logged in.', '::1', NULL),
(78, 1, '2025-05-13 22:38:36', 'login', 'User logged in.', '::1', NULL),
(79, 1, '2025-05-14 09:41:37', 'Pages Manager', 'Changed private from private to public for Page #115.', '::1', NULL),
(80, 1, '2025-05-14 09:41:37', 'Pages Manager', 'Deleted 2 permission(s) from pages/ruta_detalle.php.', '::1', NULL),
(81, 1, '2025-05-14 10:43:28', 'Email Settings', 'Updated website_name from User Spice to Camp Deivid.', '::1', NULL),
(82, 1, '2025-05-14 10:43:28', 'Email Settings', 'Updated email_login.', '::1', NULL),
(83, 1, '2025-05-14 10:43:28', 'Email Settings', 'Updated email_pass.', '::1', NULL),
(84, 1, '2025-05-14 10:43:28', 'Email Settings', 'Updated from_name from User Spice to Camp Deivid.', '::1', NULL),
(85, 1, '2025-05-14 10:43:28', 'Email Settings', 'Updated from_email from yourEmail@gmail.com to campdeividrutas@gmail.com.', '::1', NULL),
(86, 1, '2025-05-14 10:43:28', 'Email Settings', 'Updated authtype from CRAM-MD5 to .', '::1', NULL),
(87, 1, '2025-05-14 10:43:28', 'Email Settings', 'Updated email_act from 0 to 1.', '::1', NULL),
(88, 1, '2025-05-14 10:43:28', 'Email Settings', 'Updated isSMTP from 0 to 1.', '::1', NULL),
(89, 1, '2025-05-14 10:52:36', 'Permissions Manager', 'Added Permission Level named Premium.', '::1', NULL),
(90, 3, '2025-05-14 14:32:10', 'User', 'Registration completed and verification email sent.', '::1', NULL),
(91, 3, '2025-05-14 14:33:46', 'User', 'Verification completed via vericode.', '::1', NULL),
(92, 3, '2025-05-14 14:33:58', 'login', 'User logged in.', '::1', NULL),
(93, 1, '2025-05-14 22:54:31', 'Email Settings', 'Updated website_name from Camp Deivid to Candeivid.', '::1', NULL),
(94, 1, '2025-05-14 22:54:31', 'Email Settings', 'Updated email_login.', '::1', NULL),
(95, 1, '2025-05-14 22:54:31', 'Email Settings', 'Updated email_pass.', '::1', NULL),
(96, 1, '2025-05-14 22:54:31', 'Email Settings', 'Updated from_name from Camp Deivid to Candeivid.', '::1', NULL),
(97, 1, '2025-05-14 22:54:31', 'Email Settings', 'Updated from_email from campdeividrutas@gmail.com to rutascandeivid@gmail.com.', '::1', NULL),
(98, 1, '2025-05-14 22:55:46', 'Email Settings', 'Updated email_pass.', '::1', NULL),
(99, 1, '2025-05-14 22:59:44', 'Email Settings', 'Updated email_act from 0 to 1.', '::1', NULL),
(100, 1, '2025-05-14 23:04:56', 'Email Settings', 'Updated email_pass.', '::1', NULL),
(101, 1, '2025-05-14 23:07:56', 'Email Settings', 'Updated email_act from 1 to 0.', '::1', NULL),
(102, 1, '2025-05-14 23:49:18', 'USPlugins', 'gdpr installed', '::1', NULL),
(103, 1, '2025-05-14 23:49:18', 'USPlugins', 'gdpr Activated', '::1', NULL),
(104, 1, '2025-05-14 23:49:18', 'Migrations', '00001 migration triggered for gdpr', '::1', NULL),
(105, 1, '2025-05-14 23:49:18', 'Migrations', '00002 migration triggered for gdpr', '::1', NULL),
(106, 1, '2025-05-14 23:49:18', 'Migrations', '2 migration(s) successfully triggered for gdpr', '::1', NULL),
(107, 3, '2025-05-16 00:36:14', 'login', 'User logged in.', '::1', NULL),
(108, 1, '2025-05-16 00:45:36', 'User Manager', 'Added user cañete.', '::1', NULL),
(109, 1, '2025-05-16 00:45:51', 'User Manager', 'User # 4 Updated. Added 1 permission(s) to Francisco Cañete.', '::1', NULL),
(110, 1, '2025-05-16 00:45:57', 'User Manager', 'User # 4 Updated. Deleted 1 permission(s) from Francisco Cañete.', '::1', NULL),
(111, 1, '2025-05-16 13:05:03', 'Pages Manager', 'Changed private from private to public for Page #119.', '::1', NULL),
(112, 1, '2025-05-16 15:04:58', 'Email Settings', 'Updated email_act from 0 to 2.', '::1', NULL),
(113, 1, '2025-05-16 15:05:15', 'Email Settings', 'Updated email_act from 2 to 1.', '::1', NULL),
(114, 1, '2025-05-16 23:14:21', 'Email Settings', 'Updated email_act from 1 to 0.', '::1', NULL),
(115, 3, '2025-05-17 00:28:08', 'login', 'User logged in.', '::1', NULL),
(116, 1, '2025-05-17 00:43:46', 'Pages Manager', 'Added 3 permission(s) to pages/gpxdocs.php.', '::1', NULL),
(117, 1, '2025-05-17 00:47:27', 'Permissions Manager', 'Added Permission Level named Editor.', '::1', NULL),
(118, 1, '2025-05-17 00:48:30', 'Permissions Manager', 'Added page id 116 to permission level 4', '::1', NULL),
(119, 1, '2025-05-17 00:48:30', 'Permissions Manager', 'Added page id 3 to permission level 4', '::1', NULL),
(120, 1, '2025-05-17 00:48:53', 'User Manager', 'User # 4 Updated. Deleted 1 permission(s) from Francisco Cañete.', '::1', NULL),
(121, 3, '2025-05-17 10:57:40', 'login', 'User logged in.', '::1', NULL),
(122, 3, '2025-05-17 11:56:35', 'login', 'User logged in.', '::1', NULL),
(123, 1, '2025-05-19 23:03:13', 'Pages Manager', 'Added 4 permission(s) to pages/procesar_venta.php.', '::1', NULL),
(124, 1, '2025-05-19 23:03:29', 'Pages Manager', 'Added 4 permission(s) to pages/mis_compras.php.', '::1', NULL),
(125, 3, '2025-05-20 00:55:51', 'login', 'User logged in.', '::1', NULL),
(126, 1, '2025-05-20 00:56:17', 'login', 'User logged in.', '::1', NULL),
(127, 1, '2025-05-20 15:55:50', 'Pages Manager', 'Added 4 permission(s) to pages/eliminar_manual.php.', '::1', NULL),
(128, 1, '2025-05-20 15:56:25', 'Pages Manager', 'Added 4 permission(s) to pages/ruta_detalle.php.', '::1', NULL),
(129, 1, '2025-05-20 16:48:27', 'Pages Manager', 'Added 4 permission(s) to pages/editar_manual.php.', '::1', NULL),
(130, 1, '2025-05-20 21:20:28', 'Pages Manager', 'Changed private from private to public for Page #127.', '::1', NULL),
(131, 1, '2025-05-20 21:20:38', 'Pages Manager', 'Changed private from private to public for Page #125.', '::1', NULL),
(132, 1, '2025-05-22 09:00:00', 'Pages Manager', 'Added 2 permission(s) to pages/galeria_ruta.php.', '::1', NULL),
(133, 1, '2025-05-22 14:32:08', 'Pages Manager', 'Changed private from private to public for Page #129.', '::1', NULL),
(134, 1, '2025-05-25 01:59:38', 'Pages Manager', 'Added 2 permission(s) to pages/gpx_a_maps.php.', '::1', NULL),
(135, 1, '2025-05-25 02:15:33', 'Pages Manager', 'Added 4 permission(s) to pages/gpx_viewer.php.', '::1', NULL),
(136, 1, '2025-05-25 02:15:34', 'cleanupPermissionPageMatc', 'Removed 2 orphaned permissions', '::1', NULL),
(137, 1, '2025-05-25 09:44:11', 'Pages Manager', 'Added 2 permission(s) to pages/siluetas_gpx.php.', '::1', NULL),
(138, 1, '2025-05-26 18:44:13', 'login', 'User logged in.', '::1', NULL),
(139, 1, '2025-05-26 23:10:47', 'Pages Manager', 'Changed private from private to public for Page #133.', '::1', NULL),
(140, 1, '2025-05-26 23:10:47', 'Pages Manager', 'Added 4 permission(s) to gps/info.php.', '::1', NULL),
(141, 1, '2025-05-28 14:07:47', 'Pages Manager', 'Changed private from private to public for Page #134.', '::1', NULL),
(142, 1, '2025-05-28 14:07:47', 'Pages Manager', 'Added 4 permission(s) to pages/eliminar_usuario.php.', '::1', NULL),
(143, 1, '2025-05-29 14:03:36', 'login', 'User logged in.', '::1', NULL),
(144, 1, '2025-06-03 09:14:23', 'login', 'User logged in.', '::1', NULL),
(145, 3, '2025-06-03 09:15:07', 'login', 'User logged in.', '::1', NULL),
(146, 3, '2025-06-03 09:31:00', 'login', 'User logged in.', '::1', NULL),
(147, 3, '2025-06-03 09:45:25', 'login', 'User logged in.', '::1', NULL),
(148, 3, '2025-06-03 09:46:11', 'login', 'User logged in.', '::1', NULL),
(149, 3, '2025-06-03 09:48:05', 'login', 'User logged in.', '::1', NULL),
(150, 3, '2025-06-03 09:48:34', 'login', 'User logged in.', '::1', NULL),
(151, 3, '2025-06-03 09:52:30', 'login', 'User logged in.', '::1', NULL),
(152, 0, '2025-06-03 09:53:09', 'Login Fail', 'A failed login on login.php', '::1', NULL),
(153, 3, '2025-06-03 09:53:13', 'login', 'User logged in.', '::1', NULL),
(154, 1, '2025-06-03 12:29:15', 'login', 'User logged in.', '::1', NULL),
(155, 1, '2025-06-03 16:51:20', 'Pages Manager', 'Added 2 permission(s) to pages/debug_subida_gpx.php.', '::1', NULL),
(156, 1, '2025-06-04 22:33:45', 'login', 'User logged in.', '::1', NULL),
(157, 1, '2025-06-05 01:16:06', 'Pages Manager', 'Changed private from private to public for Page #138.', '::1', NULL),
(158, 1, '2025-06-05 01:16:06', 'Pages Manager', 'Added 4 permission(s) to pages/conversor.php.', '::1', NULL),
(159, 1, '2025-06-05 22:30:03', 'Pages Manager', 'Added 2 permission(s) to pages/cupones.php.', '::1', NULL),
(160, 1, '2025-06-06 09:15:40', 'login', 'User logged in.', '::1', NULL),
(161, 1, '2025-06-09 22:08:14', 'Pages Manager', 'Changed private from private to public for Page #143.', '::1', NULL),
(162, 1, '2025-06-09 22:08:14', 'Pages Manager', 'Added 4 permission(s) to pages/faq.php.', '::1', NULL),
(163, 1, '2025-06-14 01:17:49', 'Pages Manager', 'Added 2 permission(s) to pages/descargar_gpx.php.', '::1', NULL),
(164, 1, '2025-06-14 01:18:04', 'Pages Manager', 'Added 2 permission(s) to pages/descargar_gpx.php.', '::1', NULL),
(165, 1, '2025-06-14 01:26:36', 'Pages Manager', 'Added 2 permission(s) to pages/estadisticas_descargas.php.', '::1', NULL),
(166, 1, '2025-06-18 11:07:12', 'cleanupPermissionPageMatc', 'Removed 6 orphaned permissions', '::1', NULL),
(167, 1, '2025-06-18 11:07:33', 'Pages Manager', 'Added 4 permission(s) to pages/registrar_descarga.php.', '::1', NULL),
(168, 1, '2025-06-18 12:20:32', 'Pages Manager', 'Added 2 permission(s) to pages/estadisticas_descargas.php.', '::1', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--

CREATE TABLE `menus` (
  `id` int(10) NOT NULL,
  `menu_title` varchar(255) NOT NULL,
  `parent` int(10) NOT NULL,
  `dropdown` int(1) NOT NULL,
  `logged_in` int(1) NOT NULL,
  `display_order` int(10) NOT NULL,
  `label` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `icon_class` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menus`
--

INSERT INTO `menus` (`id`, `menu_title`, `parent`, `dropdown`, `logged_in`, `display_order`, `label`, `link`, `icon_class`) VALUES
(1, 'main', 2, 0, 1, 1, '{{home}}', 'pages/inicio.php', 'fa fa-fw fa-home'),
(2, 'main', -1, 1, 1, 14, '', '', 'fa fa-fw fa-cogs'),
(3, 'main', -1, 0, 1, 11, '{{username}}', 'users/account.php', 'fa fa-fw fa-user'),
(4, 'main', -1, 1, 0, 3, '{{help}}', '', 'fa fa-fw fa-life-ring'),
(5, 'main', -1, 0, 0, 2, '{{register}}', 'users/join.php', 'fa fa-fw fa-plus-square'),
(6, 'main', -1, 0, 0, 1, '{{login}}', 'users/login.php', 'fa fa-fw fa-sign-in'),
(7, 'main', 2, 0, 1, 2, '{{account}}', 'users/account.php', 'fa fa-fw fa-user'),
(8, 'main', 2, 0, 1, 3, '{{hr}}', '', ''),
(9, 'main', 2, 0, 1, 4, '{{dashboard}}', 'users/admin.php', 'fa fa-fw fa-cogs'),
(10, 'main', 2, 0, 1, 5, '{{users}}', 'users/admin.php?view=users', 'fa fa-fw fa-user'),
(11, 'main', 2, 0, 1, 6, '{{perms}}', 'users/admin.php?view=permissions', 'fa fa-fw fa-lock'),
(12, 'main', 2, 0, 1, 7, '{{pages}}', 'users/admin.php?view=pages', 'fa fa-fw fa-wrench'),
(13, 'main', 2, 0, 1, 9, '{{logs}}', 'users/admin.php?view=logs', 'fa fa-fw fa-search'),
(14, 'main', 2, 0, 1, 10, '{{hr}}', '', ''),
(15, 'main', 2, 0, 1, 11, '{{logout}}', 'users/logout.php', 'fa fa-fw fa-sign-out'),
(16, 'main', -1, 0, 0, 0, '{{home}}', 'pages/inicio.php', 'fa fa-fw fa-home'),
(17, 'main', -1, 0, 1, 10, '{{home}}', 'pages/inicio.php', 'fa fa-fw fa-home'),
(18, 'main', 4, 0, 0, 1, '{{forgot}}', 'users/forgot_password.php', 'fa fa-fw fa-wrench'),
(20, 'main', 4, 0, 0, 99999, '{{resend}}', 'users/verify_resend.php', 'fa fa-exclamation-triangle');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `msg_from` int(11) NOT NULL,
  `msg_to` int(11) NOT NULL,
  `msg_body` mediumtext NOT NULL,
  `msg_read` int(1) NOT NULL,
  `msg_thread` int(11) NOT NULL,
  `deleted` int(1) NOT NULL,
  `sent_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `message_threads`
--

CREATE TABLE `message_threads` (
  `id` int(11) NOT NULL,
  `msg_to` int(11) NOT NULL,
  `msg_from` int(11) NOT NULL,
  `msg_subject` varchar(255) NOT NULL,
  `last_update` datetime NOT NULL,
  `last_update_by` int(11) NOT NULL,
  `archive_from` int(1) NOT NULL DEFAULT 0,
  `archive_to` int(1) NOT NULL DEFAULT 0,
  `hidden_from` int(1) NOT NULL DEFAULT 0,
  `hidden_to` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` longtext NOT NULL,
  `is_read` tinyint(4) NOT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `date_created` datetime DEFAULT NULL,
  `date_read` datetime DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `class` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `private` int(11) NOT NULL DEFAULT 0,
  `re_auth` int(1) NOT NULL DEFAULT 0,
  `core` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pages`
--

INSERT INTO `pages` (`id`, `page`, `title`, `private`, `re_auth`, `core`) VALUES
(1, 'index.php', 'Home', 0, 0, 1),
(2, 'z_us_root.php', '', 0, 0, 1),
(3, 'users/account.php', 'Account Dashboard', 1, 0, 1),
(4, 'users/admin.php', 'Admin Dashboard', 1, 0, 1),
(14, 'users/forgot_password.php', 'Forgotten Password', 0, 0, 1),
(15, 'users/forgot_password_reset.php', 'Reset Forgotten Password', 0, 0, 1),
(16, 'users/index.php', 'Home', 0, 0, 1),
(17, 'users/init.php', '', 0, 0, 1),
(18, 'users/join.php', 'Join', 0, 0, 1),
(20, 'users/login.php', 'Login', 0, 0, 1),
(21, 'users/logout.php', 'Logout', 0, 0, 1),
(24, 'users/user_settings.php', 'User Settings', 1, 0, 1),
(25, 'users/verify.php', 'Account Verification', 0, 0, 1),
(26, 'users/verify_resend.php', 'Account Verification', 0, 0, 1),
(45, 'users/maintenance.php', 'Maintenance', 0, 0, 1),
(68, 'users/update.php', 'Update Manager', 1, 0, 1),
(81, 'users/admin_pin.php', 'Verification PIN Set', 1, 0, 1),
(90, 'users/complete.php', NULL, 1, 0, 0),
(92, 'users/init.example.php', NULL, 1, 0, 0),
(93, 'users/passwordless.php', NULL, 1, 0, 0),
(94, 'users/release_blacklist.php', NULL, 1, 0, 0),
(95, 'pages/inicio.php', NULL, 1, 0, 0),
(96, 'usersc/plugins/store/admin/abandoned.php', NULL, 1, 0, 0),
(97, 'usersc/plugins/store/admin/categories.php', NULL, 1, 0, 0),
(98, 'usersc/plugins/store/admin/documentation.php', NULL, 1, 0, 0),
(99, 'usersc/plugins/store/admin/edit_order.php', NULL, 1, 0, 0),
(100, 'usersc/plugins/store/admin/inventory.php', NULL, 1, 0, 0),
(101, 'usersc/plugins/store/admin/manage_inventory.php', NULL, 1, 0, 0),
(102, 'usersc/plugins/store/admin/search_orders.php', NULL, 1, 0, 0),
(103, 'usersc/plugins/store/admin/settings.php', NULL, 1, 0, 0),
(104, 'usersc/plugins/store/admin/store_cart.php', NULL, 1, 0, 0),
(105, 'usersc/plugins/store/admin/store_closed_msg.php', NULL, 1, 0, 0),
(106, 'usersc/plugins/store/admin/store_order.php', NULL, 1, 0, 0),
(107, 'usersc/plugins/store/admin/system_messages.php', NULL, 1, 0, 0),
(108, 'usersc/plugins/store/admin/view_orders.php', NULL, 1, 0, 0),
(109, 'usersc/plugins/store/public/store_closed.php', NULL, 1, 0, 0),
(110, 'usersc/plugins/store/public/cart.php', NULL, 1, 0, 0),
(111, 'usersc/plugins/store/public/item.php', NULL, 1, 0, 0),
(112, 'usersc/plugins/store/public/store.php', NULL, 1, 0, 0),
(113, 'usersc/plugins/store/public/view_order.php', NULL, 1, 0, 0),
(114, 'pages/rutas.php', NULL, 0, 0, 0),
(115, 'pages/ruta_detalle.php', NULL, 0, 0, 0),
(116, 'pages/nueva_ruta.php', NULL, 1, 0, 0),
(117, '_init.php', NULL, 1, 0, 0),
(119, 'pages/contacto.php', NULL, 0, 0, 0),
(120, 'pages/gpxdocs.php', NULL, 1, 0, 0),
(121, 'pages/procesar_venta.php', NULL, 1, 0, 0),
(122, 'pages/mis_compras.php', NULL, 1, 0, 0),
(123, 'pages/eliminar_manual.php', NULL, 1, 0, 0),
(124, 'pages/editar_manual.php', NULL, 1, 0, 0),
(125, 'pages/privacidad.php', NULL, 0, 0, 0),
(126, 'pages/ruta_detalle - Backup Opcion con Extras.php', NULL, 1, 0, 0),
(127, 'pages/terminos.php', NULL, 0, 0, 0),
(128, 'pages/galeria_ruta.php', NULL, 1, 0, 0),
(129, 'pages/cookies.php', NULL, 0, 0, 0),
(131, 'pages/gpx_viewer.php', NULL, 1, 0, 0),
(132, 'pages/siluetas_gpx.php', NULL, 1, 0, 0),
(133, 'gps/info.php', NULL, 0, 0, 0),
(134, 'pages/eliminar_usuario.php', NULL, 0, 0, 0),
(135, 'pages/debug_subida_gpx.php', NULL, 1, 0, 0),
(136, 'pages/ruta_detalle - copia.php', NULL, 1, 0, 0),
(137, 'pages/rutas - copia.php', NULL, 1, 0, 0),
(138, 'pages/conversor.php', NULL, 0, 0, 0),
(139, 'pages/cupones.php', NULL, 1, 0, 0),
(140, 'pages/estadisticas_cupones.php', NULL, 1, 0, 0),
(141, 'pages/registrar_uso_cupon.php', NULL, 1, 0, 0),
(142, 'pages/validar_cupon.php', NULL, 1, 0, 0),
(143, 'pages/faq.php', NULL, 0, 0, 0),
(146, 'pages/registrar_descarga.php', NULL, 1, 0, 0),
(147, 'pages/estadisticas_descargas.php', NULL, 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `descrip` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `descrip`) VALUES
(1, 'User', 'Standard User'),
(2, 'Administrator', 'UserSpice Administrator'),
(3, 'Premium', 'Premium User'),
(4, 'Editor', 'Editor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permission_page_matches`
--

CREATE TABLE `permission_page_matches` (
  `id` int(11) NOT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permission_page_matches`
--

INSERT INTO `permission_page_matches` (`id`, `permission_id`, `page_id`) VALUES
(3, 1, 24),
(14, 2, 4),
(15, 1, 3),
(38, 2, 68),
(54, 1, 81),
(60, 1, 95),
(61, 2, 95),
(62, 2, 96),
(63, 2, 97),
(64, 2, 98),
(65, 2, 99),
(66, 2, 100),
(67, 2, 101),
(68, 2, 102),
(69, 2, 103),
(70, 2, 104),
(71, 2, 105),
(72, 2, 106),
(73, 2, 107),
(74, 2, 108),
(75, 1, 109),
(76, 2, 109),
(77, 1, 110),
(78, 2, 110),
(79, 1, 111),
(80, 2, 111),
(81, 1, 112),
(82, 2, 112),
(83, 1, 113),
(84, 2, 113),
(89, 2, 116),
(90, 1, 120),
(91, 2, 120),
(92, 3, 120),
(93, 4, 116),
(94, 4, 3),
(95, 1, 121),
(96, 2, 121),
(97, 3, 121),
(98, 4, 121),
(99, 1, 122),
(100, 2, 122),
(101, 3, 122),
(102, 4, 122),
(103, 1, 123),
(104, 2, 123),
(105, 3, 123),
(106, 4, 123),
(107, 1, 115),
(108, 2, 115),
(109, 3, 115),
(110, 4, 115),
(111, 1, 124),
(112, 2, 124),
(113, 3, 124),
(114, 4, 124),
(115, 2, 128),
(116, 4, 128),
(119, 1, 131),
(120, 2, 131),
(121, 3, 131),
(122, 4, 131),
(123, 2, 132),
(124, 4, 132),
(125, 1, 133),
(126, 2, 133),
(127, 3, 133),
(128, 4, 133),
(129, 1, 134),
(130, 2, 134),
(131, 3, 134),
(132, 4, 134),
(133, 2, 135),
(134, 4, 135),
(135, 1, 138),
(136, 2, 138),
(137, 3, 138),
(138, 4, 138),
(139, 2, 139),
(140, 4, 139),
(141, 1, 143),
(142, 2, 143),
(143, 3, 143),
(144, 4, 143),
(151, 1, 146),
(152, 2, 146),
(153, 3, 146),
(154, 4, 146),
(155, 2, 147),
(156, 4, 147);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_download_files`
--

CREATE TABLE `plg_download_files` (
  `id` int(11) UNSIGNED NOT NULL,
  `disabled` int(1) DEFAULT 0,
  `location` text DEFAULT NULL,
  `meta` text DEFAULT NULL,
  `downloads` int(11) DEFAULT 0,
  `dlcode` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `folder` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_download_links`
--

CREATE TABLE `plg_download_links` (
  `id` int(11) UNSIGNED NOT NULL,
  `file` int(11) UNSIGNED NOT NULL,
  `disabled` int(1) DEFAULT 0,
  `no_restrictions` tinyint(1) DEFAULT 0,
  `user` int(11) DEFAULT NULL,
  `perms` varchar(255) DEFAULT NULL,
  `max` int(11) UNSIGNED DEFAULT NULL,
  `used` int(11) UNSIGNED DEFAULT 0,
  `expires` datetime DEFAULT NULL,
  `dlcode` varchar(255) DEFAULT NULL,
  `folder` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_download_logs`
--

CREATE TABLE `plg_download_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `link` int(11) UNSIGNED NOT NULL,
  `linkmode` int(11) UNSIGNED NOT NULL,
  `dlcode` varchar(255) DEFAULT NULL,
  `success` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `user` int(11) UNSIGNED NOT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_download_settings`
--

CREATE TABLE `plg_download_settings` (
  `id` int(11) UNSIGNED NOT NULL,
  `dlmode` int(11) UNSIGNED NOT NULL,
  `baseurl` varchar(255) DEFAULT NULL,
  `parser` varchar(255) DEFAULT NULL,
  `perms` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plg_download_settings`
--

INSERT INTO `plg_download_settings` (`id`, `dlmode`, `baseurl`, `parser`, `perms`) VALUES
(1, 1, NULL, 'dl/', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_payments`
--

CREATE TABLE `plg_payments` (
  `id` int(11) UNSIGNED NOT NULL,
  `user` int(11) DEFAULT NULL,
  `amt_paid` decimal(11,2) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `charge_id` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `failed` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_payments_options`
--

CREATE TABLE `plg_payments_options` (
  `id` int(11) UNSIGNED NOT NULL,
  `option` varchar(255) DEFAULT NULL,
  `enabled` tinyint(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `plg_payments_options`
--

INSERT INTO `plg_payments_options` (`id`, `option`, `enabled`) VALUES
(1, 'check', 0),
(2, 'stripe', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_social_logins`
--

CREATE TABLE `plg_social_logins` (
  `id` int(11) NOT NULL,
  `plugin` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `enabledsetting` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `built_in` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_tags`
--

CREATE TABLE `plg_tags` (
  `id` int(11) UNSIGNED NOT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `descrip` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plg_tags_matches`
--

CREATE TABLE `plg_tags_matches` (
  `id` int(11) UNSIGNED NOT NULL,
  `tag_id` int(11) UNSIGNED NOT NULL,
  `tag_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `bio`) VALUES
(1, 1, '&lt;h1&gt;This is the Admin&#039;s bio.&lt;/h1&gt;'),
(2, 2, 'This is your bio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas_log_descargas`
--

CREATE TABLE `rutas_log_descargas` (
  `id` int(11) NOT NULL,
  `ruta_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tipo` enum('gratis','venta') NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rutas_log_descargas`
--

INSERT INTO `rutas_log_descargas` (`id`, `ruta_id`, `user_id`, `tipo`, `fecha`, `ip`) VALUES
(9, 11, 1, 'gratis', '2025-06-18 13:27:36', '::1'),
(10, 11, 1, 'gratis', '2025-06-18 13:30:04', '::1'),
(11, 11, 1, 'gratis', '2025-06-18 13:39:53', '::1'),
(12, 12, 1, 'gratis', '2025-06-18 13:41:44', '::1'),
(13, 5, 1, 'venta', '2025-06-18 13:43:07', '::1'),
(14, 5, 1, 'venta', '2025-06-18 13:52:16', '::1'),
(15, 5, 1, 'venta', '2025-06-18 13:53:50', '::1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `id` int(50) NOT NULL,
  `recaptcha` int(1) NOT NULL DEFAULT 0,
  `force_ssl` int(1) NOT NULL,
  `css_sample` int(1) NOT NULL,
  `site_name` varchar(100) NOT NULL,
  `language` varchar(15) DEFAULT NULL,
  `site_offline` int(1) NOT NULL,
  `force_pr` int(1) NOT NULL,
  `glogin` int(1) NOT NULL DEFAULT 0,
  `fblogin` int(1) NOT NULL,
  `gid` text DEFAULT NULL,
  `gsecret` text DEFAULT NULL,
  `gredirect` text DEFAULT NULL,
  `ghome` text DEFAULT NULL,
  `fbid` text DEFAULT NULL,
  `fbsecret` text DEFAULT NULL,
  `fbcallback` text DEFAULT NULL,
  `graph_ver` text DEFAULT NULL,
  `finalredir` text DEFAULT NULL,
  `req_cap` int(1) NOT NULL,
  `req_num` int(1) NOT NULL,
  `min_pw` int(2) NOT NULL,
  `max_pw` int(3) NOT NULL,
  `min_un` int(2) NOT NULL,
  `max_un` int(3) NOT NULL,
  `messaging` int(1) NOT NULL,
  `snooping` int(1) NOT NULL,
  `echouser` int(11) NOT NULL,
  `wys` int(1) NOT NULL,
  `change_un` int(1) NOT NULL,
  `backup_dest` text DEFAULT NULL,
  `backup_source` text DEFAULT NULL,
  `backup_table` text DEFAULT NULL,
  `msg_notification` int(1) NOT NULL,
  `permission_restriction` int(1) NOT NULL,
  `auto_assign_un` int(1) NOT NULL,
  `page_permission_restriction` int(1) NOT NULL,
  `msg_blocked_users` int(1) NOT NULL,
  `msg_default_to` int(1) NOT NULL,
  `notifications` int(1) NOT NULL,
  `notif_daylimit` int(3) NOT NULL,
  `recap_public` text DEFAULT NULL,
  `recap_private` text DEFAULT NULL,
  `page_default_private` int(1) NOT NULL,
  `navigation_type` tinyint(1) NOT NULL,
  `copyright` varchar(255) NOT NULL,
  `custom_settings` int(1) NOT NULL,
  `system_announcement` varchar(255) NOT NULL,
  `twofa` int(1) DEFAULT 0,
  `force_notif` tinyint(1) DEFAULT NULL,
  `cron_ip` varchar(255) DEFAULT NULL,
  `registration` tinyint(1) DEFAULT NULL,
  `join_vericode_expiry` int(9) UNSIGNED NOT NULL,
  `reset_vericode_expiry` int(9) UNSIGNED NOT NULL,
  `admin_verify` tinyint(1) NOT NULL,
  `admin_verify_timeout` int(9) NOT NULL,
  `session_manager` tinyint(1) NOT NULL,
  `template` varchar(255) DEFAULT 'standard',
  `saas` tinyint(1) DEFAULT NULL,
  `redirect_uri_after_login` mediumtext DEFAULT NULL,
  `show_tos` tinyint(1) DEFAULT 1,
  `default_language` varchar(11) DEFAULT NULL,
  `allow_language` tinyint(1) DEFAULT NULL,
  `spice_api` varchar(75) DEFAULT NULL,
  `announce` datetime DEFAULT NULL,
  `bleeding_edge` tinyint(1) DEFAULT 0,
  `err_time` int(11) DEFAULT 15,
  `container_open_class` varchar(255) DEFAULT 'container-fluid',
  `debug` tinyint(1) DEFAULT 0,
  `widgets` text DEFAULT NULL,
  `uman_search` tinyint(1) DEFAULT 0,
  `no_passwords` tinyint(1) DEFAULT 0,
  `email_login` tinyint(1) DEFAULT 0,
  `pwl_length` int(3) DEFAULT 5,
  `order_link` varchar(255) DEFAULT NULL,
  `open` tinyint(1) DEFAULT 0,
  `closed_msg` text DEFAULT NULL,
  `ignore_inventory` tinyint(1) DEFAULT 0,
  `email_msg` text DEFAULT NULL,
  `checkout_msg` text DEFAULT NULL,
  `header_msg` text DEFAULT NULL,
  `auto_close` datetime DEFAULT NULL,
  `gdpract` tinyint(1) DEFAULT 0,
  `gdprver` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`id`, `recaptcha`, `force_ssl`, `css_sample`, `site_name`, `language`, `site_offline`, `force_pr`, `glogin`, `fblogin`, `gid`, `gsecret`, `gredirect`, `ghome`, `fbid`, `fbsecret`, `fbcallback`, `graph_ver`, `finalredir`, `req_cap`, `req_num`, `min_pw`, `max_pw`, `min_un`, `max_un`, `messaging`, `snooping`, `echouser`, `wys`, `change_un`, `backup_dest`, `backup_source`, `backup_table`, `msg_notification`, `permission_restriction`, `auto_assign_un`, `page_permission_restriction`, `msg_blocked_users`, `msg_default_to`, `notifications`, `notif_daylimit`, `recap_public`, `recap_private`, `page_default_private`, `navigation_type`, `copyright`, `custom_settings`, `system_announcement`, `twofa`, `force_notif`, `cron_ip`, `registration`, `join_vericode_expiry`, `reset_vericode_expiry`, `admin_verify`, `admin_verify_timeout`, `session_manager`, `template`, `saas`, `redirect_uri_after_login`, `show_tos`, `default_language`, `allow_language`, `spice_api`, `announce`, `bleeding_edge`, `err_time`, `container_open_class`, `debug`, `widgets`, `uman_search`, `no_passwords`, `email_login`, `pwl_length`, `order_link`, `open`, `closed_msg`, `ignore_inventory`, `email_msg`, `checkout_msg`, `header_msg`, `auto_close`, `gdpract`, `gdprver`) VALUES
(1, 0, 0, 0, 'Candeivid', 'en', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', 0, 0, 6, 150, 4, 30, 0, 1, 0, 1, 0, '/', 'everything', '', 0, 0, 0, 0, 0, 1, 0, 7, '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe', 1, 1, 'Candeivid', 1, '', 0, 0, 'off', 1, 24, 15, 1, 120, 0, 'customizer', NULL, 'index.php', 1, 'es-ES', 0, 'JUL52-4BLYU-EL5FD-56ADC-84D23', '2025-06-18 14:20:19', 0, 15, 'container-fluid', 0, 'settings,misc,tools,plugins,snapshot,active_users,active-users', 0, 0, 0, 5, NULL, 0, NULL, 0, NULL, NULL, NULL, '2030-12-31 00:00:00', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_categories`
--

CREATE TABLE `store_categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `cat` varchar(255) DEFAULT NULL,
  `disabled` int(1) DEFAULT 0,
  `subcats` int(1) DEFAULT 0,
  `photo` varchar(255) DEFAULT NULL,
  `is_subcat` tinyint(1) DEFAULT 0,
  `subcat_of` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_inventory`
--

CREATE TABLE `store_inventory` (
  `id` int(11) UNSIGNED NOT NULL,
  `category` int(11) DEFAULT NULL,
  `topcat` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `price` decimal(11,2) DEFAULT NULL,
  `cost` varchar(255) DEFAULT NULL,
  `inv_cont` int(1) DEFAULT 0,
  `stock` int(11) DEFAULT NULL,
  `disabled` int(1) DEFAULT 0,
  `qoh` int(11) DEFAULT 999999,
  `digital` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_inventory_photos`
--

CREATE TABLE `store_inventory_photos` (
  `id` int(11) UNSIGNED NOT NULL,
  `item` int(11) DEFAULT NULL,
  `var` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `disabled` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_inventory_vars`
--

CREATE TABLE `store_inventory_vars` (
  `id` int(11) UNSIGNED NOT NULL,
  `item` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `price` decimal(11,2) DEFAULT NULL,
  `cost` varchar(255) DEFAULT NULL,
  `inv_cont` int(1) DEFAULT 0,
  `stock` int(11) DEFAULT NULL,
  `disabled` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_orders`
--

CREATE TABLE `store_orders` (
  `id` int(11) UNSIGNED NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `add1` varchar(255) DEFAULT NULL,
  `add2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `amt_paid` varchar(25) DEFAULT NULL,
  `paid` int(1) DEFAULT 0,
  `reference` varchar(255) DEFAULT NULL,
  `pickup_date` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `archived` int(1) DEFAULT 0,
  `order_type` varchar(255) DEFAULT NULL,
  `taken_by` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `charge_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `store_orders`
--

INSERT INTO `store_orders` (`id`, `fullname`, `code`, `submitted`, `last_update`, `phone`, `email`, `add1`, `add2`, `city`, `state`, `postal`, `status`, `amt_paid`, `paid`, `reference`, `pickup_date`, `notes`, `archived`, `order_type`, `taken_by`, `payment_method`, `charge_id`) VALUES
(1, NULL, '682126d012a952.50617', NULL, '2025-05-11 22:38:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_order_items`
--

CREATE TABLE `store_order_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `orderno` int(11) DEFAULT NULL,
  `item` int(11) DEFAULT NULL,
  `price_each` varchar(255) DEFAULT NULL,
  `price_tot` varchar(255) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_order_status`
--

CREATE TABLE `store_order_status` (
  `id` int(11) UNSIGNED NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `store_order_status`
--

INSERT INTO `store_order_status` (`id`, `status`, `disabled`) VALUES
(1, 'Order Placed', 0),
(2, 'Order Shipped', 0),
(3, 'Order Cancelled', 0),
(4, 'Backordered', 0),
(5, 'Disputed', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `store_payment_options`
--

CREATE TABLE `store_payment_options` (
  `id` int(11) UNSIGNED NOT NULL,
  `opt` varchar(255) DEFAULT NULL,
  `def` tinyint(1) DEFAULT 0,
  `disabled` tinyint(1) DEFAULT 0,
  `common` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `store_payment_options`
--

INSERT INTO `store_payment_options` (`id`, `opt`, `def`, `disabled`, `common`) VALUES
(1, 'check', 1, 0, 'Check');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `updates`
--

CREATE TABLE `updates` (
  `id` int(11) NOT NULL,
  `migration` varchar(15) NOT NULL,
  `applied_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `update_skipped` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `updates`
--

INSERT INTO `updates` (`id`, `migration`, `applied_on`, `update_skipped`) VALUES
(15, '1XdrInkjV86F', '2018-02-18 22:33:24', NULL),
(16, '3GJYaKcqUtw7', '2018-04-25 16:51:08', NULL),
(17, '3GJYaKcqUtz8', '2018-04-25 16:51:08', NULL),
(18, '69qa8h6E1bzG', '2018-04-25 16:51:08', NULL),
(19, '2XQjsKYJAfn1', '2018-04-25 16:51:08', NULL),
(20, '549DLFeHMNw7', '2018-04-25 16:51:08', NULL),
(21, '4Dgt2XVjgz2x', '2018-04-25 16:51:08', NULL),
(22, 'VLBp32gTWvEo', '2018-04-25 16:51:08', NULL),
(23, 'Q3KlhjdtxE5X', '2018-04-25 16:51:08', NULL),
(24, 'ug5D3pVrNvfS', '2018-04-25 16:51:08', NULL),
(25, '69FbVbv4Jtrz', '2018-04-25 16:51:09', NULL),
(26, '4A6BdJHyvP4a', '2018-04-25 16:51:09', NULL),
(27, '37wvsb5BzymK', '2018-04-25 16:51:09', NULL),
(28, 'c7tZQf926zKq', '2018-04-25 16:51:09', NULL),
(29, 'ockrg4eU33GP', '2018-04-25 16:51:09', NULL),
(30, 'XX4zArPs4tor', '2018-04-25 16:51:09', NULL),
(31, 'pv7r2EHbVvhD', '2018-04-26 00:00:00', NULL),
(32, 'uNT7NpgcBDFD', '2018-04-26 00:00:00', NULL),
(33, 'mS5VtQCZjyJs', '2018-12-11 14:19:16', NULL),
(34, '23rqAv5elJ3G', '2018-12-11 14:19:51', NULL),
(35, 'qPEARSh49fob', '2019-01-01 12:01:01', NULL),
(36, 'FyMYJ2oeGCTX', '2019-01-01 12:01:01', NULL),
(37, 'iit5tHSLatiS', '2019-01-01 12:01:01', NULL),
(38, 'hcA5B3PLhq6E', '2020-07-16 11:27:53', NULL),
(39, 'VNEno3E4zaNz', '2020-07-16 11:27:53', NULL),
(40, '2ZB9mg1l0JXe', '2020-07-16 11:27:53', NULL),
(41, 'B9t6He7qmFXa', '2020-07-16 11:27:53', NULL),
(42, '86FkFVV4TGRg', '2020-07-16 11:27:53', NULL),
(43, 'y4A1Y0u9n2Rt', '2020-07-16 11:27:53', NULL),
(44, 'Tm5xY22MM8eC', '2020-07-16 11:27:53', NULL),
(45, '0YXdrInkjV86F', '2020-07-16 11:27:53', NULL),
(46, '99plgnkjV86', '2020-07-16 11:27:53', NULL),
(47, '0DaShInkjV86', '2020-07-16 11:27:53', NULL),
(48, '0DaShInkjVz1', '2020-07-16 11:27:53', NULL),
(49, 'y4A1Y0u9n2SS', '2020-07-16 11:27:53', NULL),
(50, '0DaShInkjV87', '2020-07-16 11:27:53', NULL),
(51, '0DaShInkjV88', '2020-07-16 11:27:53', NULL),
(52, '2019-09-04a', '2020-07-16 11:27:53', NULL),
(53, '2019-09-05a', '2020-07-16 11:27:53', NULL),
(54, '2019-09-26a', '2020-07-16 11:27:53', NULL),
(55, '2019-11-19a', '2020-07-16 11:27:53', NULL),
(56, '2019-12-28a', '2020-07-16 11:27:53', NULL),
(57, '2020-01-21a', '2020-07-16 11:27:54', NULL),
(58, '2020-03-26a', '2020-07-16 11:27:54', NULL),
(59, '2020-04-17a', '2020-07-16 11:27:54', NULL),
(60, '2020-06-06a', '2020-07-16 11:27:54', NULL),
(61, '2020-06-30a', '2020-07-16 11:27:54', NULL),
(62, '2020-07-01a', '2020-07-16 11:27:54', NULL),
(63, '2020-07-16a', '2020-10-08 01:26:22', NULL),
(64, '2020-07-30a', '2020-10-08 01:26:22', NULL),
(65, '2020-10-06a', '2022-04-15 17:37:11', NULL),
(66, '2020-11-03a', '2022-04-15 17:37:11', NULL),
(67, '2020-11-08a', '2022-04-15 17:37:11', NULL),
(68, '2020-11-10a', '2022-04-15 17:37:11', NULL),
(69, '2020-11-10b', '2022-04-15 17:37:11', NULL),
(70, '2020-12-17a', '2022-04-15 17:37:11', NULL),
(71, '2020-12-28a', '2022-04-15 17:37:11', NULL),
(72, '2021-01-20a', '2022-04-15 17:37:11', NULL),
(73, '2021-02-16a', '2022-04-15 17:37:11', NULL),
(74, '2021-04-14a', '2022-04-15 17:37:11', NULL),
(75, '2021-04-15a', '2022-04-15 17:37:11', NULL),
(76, '2021-05-20a', '2022-04-15 17:37:11', NULL),
(77, '2021-07-11a', '2022-04-15 17:37:11', NULL),
(78, '2021-08-22a', '2022-04-15 17:37:11', NULL),
(79, '2021-08-24a', '2022-04-15 17:37:11', NULL),
(80, '2021-09-25a', '2022-04-15 17:37:11', NULL),
(81, '2021-12-26a', '2022-04-15 17:37:11', NULL),
(82, '2022-05-04a', '2022-12-23 12:05:38', NULL),
(83, '2022-11-06a', '2022-12-23 12:06:38', NULL),
(84, '2022-11-20a', '2022-12-23 12:06:38', NULL),
(85, '2022-12-04a', '2022-12-23 12:06:38', NULL),
(86, '2022-12-22a', '2022-12-23 12:06:38', NULL),
(87, '2022-12-23a', '2022-12-23 12:06:38', NULL),
(88, '2023-01-02a', '2024-09-25 09:30:55', NULL),
(89, '2023-01-03a', '2024-09-25 09:30:55', NULL),
(90, '2023-01-03b', '2024-09-25 09:30:55', NULL),
(91, '2023-01-05a', '2024-09-25 09:30:55', NULL),
(92, '2023-01-07a', '2024-09-25 09:30:55', NULL),
(93, '2023-02-10a', '2024-09-25 09:30:55', NULL),
(94, '2023-05-19a', '2024-09-25 09:30:56', NULL),
(95, '2023-06-29a', '2024-09-25 09:30:56', NULL),
(96, '2023-06-29b', '2024-09-25 09:30:56', NULL),
(97, '2023-11-15a', '2024-09-25 09:30:56', NULL),
(98, '2023-11-17a', '2024-09-25 09:30:56', NULL),
(99, '2024-03-12a', '2024-09-25 09:30:56', NULL),
(100, '2024-03-13a', '2024-09-25 09:30:56', NULL),
(101, '2024-03-14a', '2024-09-25 09:30:56', NULL),
(102, '2024-03-15a', '2024-09-25 09:30:56', NULL),
(103, '2024-03-17a', '2024-09-25 09:30:56', NULL),
(104, '2024-03-17b', '2024-09-25 09:30:56', NULL),
(105, '2024-03-18a', '2024-09-25 09:30:56', NULL),
(106, '2024-03-20a', '2024-09-25 09:30:56', NULL),
(107, '2024-03-22a', '2024-09-25 09:30:56', NULL),
(108, '2024-04-01a', '2024-09-25 09:30:56', NULL),
(109, '2024-04-13a', '2024-09-25 09:30:56', NULL),
(110, '2024-06-24a', '2024-09-25 09:30:56', NULL),
(111, '2024-09-25a', '2025-04-12 10:51:28', NULL),
(112, '2024-11-22a', '2025-04-12 10:51:28', NULL),
(113, '2024-12-16a', '2025-04-12 10:51:28', NULL),
(114, '2024-12-21a', '2025-04-12 10:51:28', NULL),
(115, '2025-02-23a', '2025-04-12 10:51:28', NULL),
(116, '2025-03-02a', '2025-04-12 10:51:28', NULL),
(117, '2025-03-03a', '2025-04-12 10:51:28', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `permissions` tinyint(1) NOT NULL,
  `email` varchar(155) NOT NULL,
  `email_new` varchar(155) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `language` varchar(15) DEFAULT 'en-US',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `vericode` text DEFAULT NULL,
  `vericode_expiry` datetime DEFAULT NULL,
  `oauth_provider` text DEFAULT NULL,
  `oauth_uid` text DEFAULT NULL,
  `gender` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `gpluslink` text DEFAULT NULL,
  `account_owner` tinyint(4) NOT NULL DEFAULT 1,
  `account_id` int(11) NOT NULL DEFAULT 0,
  `account_mgr` int(11) NOT NULL DEFAULT 0,
  `fb_uid` text DEFAULT NULL,
  `picture` text DEFAULT NULL,
  `created` datetime NOT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT 0,
  `msg_exempt` tinyint(1) NOT NULL DEFAULT 0,
  `dev_user` tinyint(1) NOT NULL DEFAULT 0,
  `msg_notification` tinyint(1) NOT NULL DEFAULT 1,
  `cloak_allowed` tinyint(1) NOT NULL DEFAULT 0,
  `oauth_tos_accepted` tinyint(1) DEFAULT NULL,
  `un_changed` tinyint(1) NOT NULL DEFAULT 0,
  `force_pr` tinyint(1) NOT NULL DEFAULT 0,
  `logins` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `join_date` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `gdpr` int(11) DEFAULT 0,
  `gdpr_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `permissions`, `email`, `email_new`, `username`, `password`, `pin`, `fname`, `lname`, `language`, `email_verified`, `vericode`, `vericode_expiry`, `oauth_provider`, `oauth_uid`, `gender`, `locale`, `gpluslink`, `account_owner`, `account_id`, `account_mgr`, `fb_uid`, `picture`, `created`, `protected`, `msg_exempt`, `dev_user`, `msg_notification`, `cloak_allowed`, `oauth_tos_accepted`, `un_changed`, `force_pr`, `logins`, `last_login`, `join_date`, `modified`, `active`, `gdpr`, `gdpr_date`) VALUES
(1, 1, 'pepocero@gmail.com', NULL, 'admin', '$2y$14$4LU8JZbPzappRR4x5WNyF.Z1ahWFdYPfvYnX7l1ME9boKbhwb2NfK', NULL, 'Camp', 'Deivid', 'en-US', 1, 's2GiSN9THd7V0Lb', '2022-11-25 05:32:17', '', '', '', '', '', 1, 0, 0, '', '', '0000-00-00 00:00:00', 1, 1, 0, 1, 1, NULL, 0, 0, 14, '2025-06-06 11:15:40', '2022-12-25 00:00:00', '2025-04-12 00:00:00', 1, 0, NULL),
(3, 1, 'rogstack@gmail.com', NULL, 'rogstack', '$2y$13$/4NmRQjGm4x.RFbiJSnbleXXvDXRvxvuIrUIr1TPQz7bqRPphdE86', NULL, 'Rogelio', 'Stack', 'en-US', 1, 'gcoiQHmvVM1PnWM', '2025-05-14 16:33:46', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '0000-00-00 00:00:00', 0, 0, 0, 1, 0, 1, 0, 0, 14, '2025-06-03 11:53:13', '2025-05-14 16:32:06', NULL, 1, 0, NULL),
(4, 1, 'fchbass@gmail.com', NULL, 'cañete', '$2y$13$BcAGdqe7gIDEWY5Ru.b0WuyerHnIU3Kz/7bx7JleAPyQ5knWgZ8Gi', NULL, 'Francisco', 'Cañete', 'en-US', 1, '6827dcf582a02inMMnC9XknTEmlB', '2025-05-17 03:03:53', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '0000-00-00 00:00:00', 0, 0, 0, 1, 0, 1, 0, 0, 0, NULL, '2025-05-16 02:45:35', '2025-05-17 00:00:00', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_online`
--

CREATE TABLE `users_online` (
  `id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `timestamp` varchar(15) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_session`
--

CREATE TABLE `users_session` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `uagent` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_permission_matches`
--

CREATE TABLE `user_permission_matches` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_permission_matches`
--

INSERT INTO `user_permission_matches` (`id`, `user_id`, `permission_id`) VALUES
(100, 1, 1),
(101, 1, 2),
(111, 3, 1),
(114, 4, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_announcements`
--

CREATE TABLE `us_announcements` (
  `id` int(11) NOT NULL,
  `dismissed` int(11) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `ignore` varchar(50) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `dismissed_by` int(11) DEFAULT 0,
  `update_announcement` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_email_logins`
--

CREATE TABLE `us_email_logins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vericode` varchar(50) NOT NULL,
  `success` tinyint(1) DEFAULT 0,
  `login_ip` varchar(50) NOT NULL,
  `login_date` datetime NOT NULL,
  `expired` tinyint(1) DEFAULT 0,
  `expires` datetime DEFAULT NULL,
  `verification_code` varchar(10) DEFAULT NULL,
  `invalid_attempts` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_fingerprints`
--

CREATE TABLE `us_fingerprints` (
  `kFingerprintID` int(11) UNSIGNED NOT NULL,
  `fkUserID` int(11) NOT NULL,
  `Fingerprint` varchar(32) NOT NULL,
  `Fingerprint_Expiry` datetime NOT NULL,
  `Fingerprint_Added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_fingerprint_assets`
--

CREATE TABLE `us_fingerprint_assets` (
  `kFingerprintAssetID` int(11) UNSIGNED NOT NULL,
  `fkFingerprintID` int(11) NOT NULL,
  `IP_Address` varchar(255) NOT NULL,
  `User_Browser` varchar(255) NOT NULL,
  `User_OS` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_forms`
--

CREATE TABLE `us_forms` (
  `id` int(11) NOT NULL,
  `form` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_form_validation`
--

CREATE TABLE `us_form_validation` (
  `id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `params` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `us_form_validation`
--

INSERT INTO `us_form_validation` (`id`, `value`, `description`, `params`) VALUES
(1, 'min', 'Minimum # of Characters', 'number'),
(2, 'max', 'Maximum # of Characters', 'number'),
(3, 'is_numeric', 'Must be a number', 'true'),
(4, 'valid_email', 'Must be a valid email address', 'true'),
(5, '<', 'Must be a number less than', 'number'),
(6, '>', 'Must be a number greater than', 'number'),
(7, '<=', 'Must be a number less than or equal to', 'number'),
(8, '>=', 'Must be a number greater than or equal to', 'number'),
(9, '!=', 'Must not be equal to', 'text'),
(10, '==', 'Must be equal to', 'text'),
(11, 'is_integer', 'Must be an integer', 'true'),
(12, 'is_timezone', 'Must be a valid timezone name', 'true'),
(13, 'is_datetime', 'Must be a valid DateTime', 'true');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_form_views`
--

CREATE TABLE `us_form_views` (
  `id` int(11) NOT NULL,
  `form_name` varchar(255) NOT NULL,
  `view_name` varchar(255) NOT NULL,
  `fields` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_gdpr`
--

CREATE TABLE `us_gdpr` (
  `id` int(11) NOT NULL,
  `popup` longtext DEFAULT NULL,
  `detail` longtext DEFAULT NULL,
  `confirm` longtext DEFAULT NULL,
  `btn_accept` varchar(255) DEFAULT NULL,
  `btn_more` varchar(255) DEFAULT NULL,
  `btn_delete` varchar(255) DEFAULT NULL,
  `btn_confirm_no` varchar(255) DEFAULT NULL,
  `btn_confirm_yes` varchar(255) DEFAULT NULL,
  `delete` tinyint(1) DEFAULT 0,
  `created_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `us_gdpr`
--

INSERT INTO `us_gdpr` (`id`, `popup`, `detail`, `confirm`, `btn_accept`, `btn_more`, `btn_delete`, `btn_confirm_no`, `btn_confirm_yes`, `delete`, `created_on`) VALUES
(1, 'We use cookies and collect personal information in accordance with our policy to provide you with the best possible user experience.', '1. Introduction&lt;br&gt;\r\nThis information sheet serves to inform you about our website&rsquo;s (&ldquo;Website&rdquo;) Cookie Policy so that you may better understand the use of cookies during your navigation and provide your consent thereto.\r\n&lt;br&gt;&lt;br&gt;\r\nIt is understood that by continuing to navigate on this Website you are consenting to the use of cookies, as specifically indicated in the notice on the front page (&ldquo;Homepage&rdquo;) reporting the existence of our Cookie Policy.\r\n&lt;br&gt;&lt;br&gt;\r\n2. Who is the controller of your data?&lt;br&gt;\r\nWhen this policy mentions &ldquo;Company&rdquo;, &ldquo;we,&rdquo; &ldquo;us,&rdquo; &ldquo;our&rdquo; or &ldquo;Data Controller&rdquo;, it refers to the website you are visiting right now.\r\n&lt;br&gt;&lt;br&gt;\r\n3. What are cookies?&lt;br&gt;\r\nCookies are small files which are stored on your computer, they hold a modest amount of data specific to you and allows a server to deliver a page tailored to you on your computer, hard drive, smartphone or tablet (hereinafter referred to as, &ldquo;Device&rdquo;). Later on, if you return to our Website, it can read and recognize the cookies. Primarily, they are used to operate or improve the way our Website works as well as to provide business and marketing information to the Website owner.\r\n&lt;br&gt;&lt;br&gt;\r\n4. Authorization for the use of cookies on our Website&lt;br&gt;\r\nIn accordance with the notice of cookie usage appearing on our Website&rsquo;s homepage and our Cookie Policy you agree that, when browsing our Website, you consent to the use of cookies described herein, except to the extent that you have modified your browser settings to disable their use. This includes but is not limited to browsing our Website to complete any of the following actions: closing the cookie notice on the Homepage, scrolling through the Website, clicking on any element of the Website, etc.\r\n&lt;br&gt;&lt;br&gt;\r\n5. What categories of your data do we collect and use?&lt;br&gt;\r\nWhen you visit the Website (you as a &quot;User&quot;) we collect the categories of personal data as follows:\r\n&lt;br&gt;&lt;br&gt;\r\nPersonal data collected  during your signup and automatically from our Website.\r\n&lt;br&gt;&lt;br&gt;\r\nInformation about your visits to and use of the Website, such as information about the device and browser you are using, your IP address or domain names of the computers connected to the Websites, uniform resource identifiers for requests made, the time of request, the method used to submit the request to the server, the size of the archive obtained as a response, the numerical code indicating the status of the response given by the server (correct, error, etc.) and other parameters relative to the operating system and the computer environment used, the date and time that you visited, the duration of your visit, the referral source and website navigation paths of your visit and your interactions on the Website including the Services and offers you are interested in. Please note that we may associate this information with your account. If you delete your account, this information will no longer be linked to you in our database.\r\n&lt;br&gt;&lt;br&gt;\r\nPlease see the following clause of this Policy for further information on the purposes for which we collect and use this information.\r\n&lt;br&gt;&lt;br&gt;\r\n6. Types of cookies used on our Website&lt;br&gt;&lt;br&gt;\r\n6.1. Types of cookies according to the managing entity&lt;br&gt;\r\n\r\nDepending on what entity manages the computer or domain from which the cookies are sent and processed, there exist the following types of cookies:\r\n&lt;br&gt;\r\nFirst party cookies: these are sent to your Device from a computer or domain managed by us and from which the service you requested is provided.&lt;br&gt;&lt;br&gt;\r\nThird party cookies: these are sent to your Device from a computer or domain that is not managed by us, but by a separate entity that processes data obtained through cookies.&lt;br&gt;&lt;br&gt;\r\n6.2. Types of cookies according to the length of time you stay connected: &lt;br&gt;\r\n\r\nDepending on the amount of time you remain active on your Device, these are the following types of cookies:\r\n&lt;br&gt;&lt;br&gt;\r\nSession cookies: these are designed to receive and store data while you access the Website. These cookies do not remain stored on your Device when you exit the session or browser.&lt;br&gt;&lt;br&gt;\r\nPersistent cookies: these types of cookies remain stored on your Device and can be accessed and processed after you exit the Website as well as when you navigate on it for a pre-determined period of time. The cookie remains on the hard drive until it reaches its expiration date. The maximum time we use persistent cookies on our Website is 2 years. At this point the browser would purge the cookie from the hard drive.&lt;br&gt;&lt;br&gt;\r\n6.3. Types of cookies according to their purpose&lt;br&gt;\r\n\r\nCookies can be grouped as follows:\r\n&lt;br&gt;&lt;br&gt;\r\nTechnical cookies: these cookies are strictly necessary for the operation of our Website and are essential for browsing and allow the use of various features. Without them, you cannot use the search function, compare tool or book other available services on our Website.&lt;br&gt;&lt;br&gt;\r\nPersonalization cookies: these are used to make navigating our Website easier, as well as to remember your selections and offer more personalized services. In some cases, we may allow advertisers or other third parties to place cookies on our Website to provide personalized content and services. In any case, your use of our Website serves as your acceptance of the use of this type of cookie. If cookies are blocked, we cannot guarantee the functioning of such services.&lt;br&gt;&lt;br&gt;\r\nAnalytical cookies for statistical purposes and measuring traffic: these cookies gather information about your use of our Website, the pages you visit and any errors that may occur during navigation. We also use these cookies to recognize the place of origin for visits to our Website. These cookies do not gather information that may personally identify you. All information is collected in an anonymous manner and is used to improve the functioning of our Website through statistical information. Therefore, these cookies do not contain personal data. In some cases, some of these cookies are managed on our behalf by third parties, but may not be used by them for purposes other than those mentioned above.&lt;br&gt;&lt;br&gt;\r\nAdvertising and re-marketing cookies: these cookies are used to gather information so that ads are more interesting to you, as well as to display other advertising campaigns along with advertisements on the Website or on those of third parties. Most of these cookies are &ldquo;third party cookies&rdquo; which are not managed by us and, because of the way they work, cannot be accessed by us, nor are we responsible for their management or purpose.\r\n&lt;br&gt;&lt;br&gt;\r\nTo that end, we can also use the services of a third party in order to collect data and/or publish ads when you visit our Website. These companies often use anonymous and aggregated information (not including, for example, your name, address, email address or telephone number) regarding visits to this Website and others in order to publish ads about goods and services of interest to you.&lt;br&gt;&lt;br&gt;\r\nSocial cookies: these cookies allow you to share our Website and click &ldquo;Like&rdquo; on social networks like Facebook, Twitter, Google, and YouTube, etc. They also allow you interact with each distinct platform&rsquo;s contents. The way these cookies are used and the information gathered is governed by the privacy policy of each social platform, which you can find on the list below in Paragraph 5 of this Policy.&lt;br&gt;&lt;br&gt;\r\n\r\n7. List of cookies used on this Website&lt;br&gt;\r\nDefault Browser/Login Cookie&lt;br&gt;\r\n\r\nWe are not responsible for the contents and accuracy of third party cookie policies contained in our Cookie Policy.\r\n&lt;br&gt;&lt;br&gt;\r\n8. Why do we collect your data?&lt;br&gt;\r\nA. To create and maintain the contractual relation established for the provision of the Service requested by you in all its phases and by way of any possible integration and modification.&lt;br&gt;\r\nTo provide a requested service&lt;br&gt;&lt;br&gt;\r\nB. To meet the legal, regulatory and compliance requirements and to respond to requests by government or law enforcement authorities conducting an investigation.&lt;br&gt;&lt;br&gt;\r\nTo comply with the law&lt;br&gt;\r\nC. To carry out anonymous, aggregation and statistical analyses so that we can see how our Website, products and services are being used and how our Website is performing.&lt;br&gt;&lt;br&gt;\r\nTo pursue our legitimate interest(i.e. improving our Website, its features and our products and services)&lt;br&gt;&lt;br&gt;\r\n\r\nD. To tailor and personalize online marketing notifications and advertising for you based on the information on your use of our Website, products and services and other sites collected through cookies.&lt;br&gt;\r\nWhere you give your consent (i.e. through the cookie banner or by your browser\'s settings)&lt;br&gt;&lt;br&gt;\r\n9. How long do we retain your data?&lt;br&gt;\r\nWe retain your personal data for as long as is required to achieve the purposes and fulfill the activities as set out in this Cookies Policy, otherwise communicated to you or for as long as is permitted by applicable law. Further information about the retention period is available here:\r\n&lt;br&gt;&lt;br&gt;\r\nData collected-retention period&lt;br&gt;\r\nTechnical cookies-Max 3 years from the date of browsing on our websites&lt;br&gt;\r\nNon-technical cookies-Max 1 year	from the date of browsing on our websites&lt;br&gt;\r\n&lt;br&gt;&lt;br&gt;\r\n10. Cookie management&lt;br&gt;\r\nYou must keep in mind that if your Device does not have cookies enabled, your experience on the Website may be limited, thereby impeding the navigation and use of our services.&lt;br&gt;\r\n\r\n10.1.- How do I disable/enable cookies?&lt;br&gt;\r\n\r\nThere are a number of ways to manage cookies. By modifying your browser settings, you can opt to disable cookies or receive a notification before accepting them. You can also erase all cookies installed in your browser&rsquo;s cookie folder. Keep in mind that each browser has a different procedure for managing and configuring cookies. Here&rsquo;s how you manage cookies in the various major browsers:&lt;br&gt;&lt;br&gt;\r\n\r\nHere&rsquo;s how you manage cookies in the various major browsers:&lt;/p&gt;&lt;ul&gt;&lt;li&gt;&lt;a href=&quot;https://support.microsoft.com/en-us/kb/278835&quot;&gt;MICROSOFT INTERNET EXPLORER/EDGE&lt;/a&gt;&lt;/li&gt;&lt;li&gt;&lt;a href=&quot;https://support.google.com/chrome/answer/95647?hl=en-GB&quot;&gt;GOOGLE CHROME&lt;/a&gt;&lt;/li&gt;&lt;li&gt;&lt;a href=&quot;https://support.mozilla.org/en-US/kb/enable-and-disable-cookies-website-preferences?redirectlocale=en-US&amp;amp;redirectslug=Enabling+and+disabling+cookies&quot;&gt;MOZILLA FIREFOX&lt;/a&gt;&lt;/li&gt;&lt;li&gt;&lt;a href=&quot;http://www.apple.com/support/?path=Safari/5.0/en/9277.html&quot;&gt;APPLE SAFARI&lt;/a&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;\r\n&lt;br&gt;\r\nIf you use another browser, please read its help menu for more information.\r\n&lt;br&gt;\r\nIf you would like information about managing cookies on your tablet or smartphone, please read the related documentation or help archives online.&lt;br&gt;&lt;br&gt;\r\n\r\n10.2.- How are third party cookies enabled/disabled?&lt;br&gt;\r\n\r\nWe do not install third party cookies. They are installed by our partners or other third parties when you visit our Website. Therefore, we suggest that you consult our partners&rsquo; Websites for more information on managing any third party cookies that are installed. However, we invite you to visit the following website http://www.youronlinechoices.com/ where you can find useful information about the use of cookies as well as the measures you can take to protect your privacy on the internet.\r\n&lt;br&gt;&lt;br&gt;\r\n11. What are your data protection rights and how can you exercise them?&lt;br&gt;\r\nYou can exercise the rights provided by the Regulation EU 2016/679 (Articles 15-22), including the right to:&lt;br&gt;\r\n\r\nRight of access - To receive confirmation of the existence of your personal data, access its content and obtain a copy.&lt;br&gt;&lt;br&gt;\r\nRight of rectification - To update, rectify and/or correct your personal data.&lt;br&gt;&lt;br&gt;\r\nRight to erasure/right to be forgotten and right to restriction - To request the erasure of your data or restriction of your data which has been processed in violation of the law, including whose storage is not necessary in relation to the purposes for which the data was collected or otherwise processed; where we have made your personal data public, you have also the right to request the erasure of your personal data and to take reasonable steps, including technical measures, to inform other data controllers which are processing the personal data that you have requested the erasure by such controllers of any links to, or copy or replication of, those personal data.&lt;br&gt;&lt;br&gt;\r\nRight to data portability - To receive a copy of your personal data you provided to us for a contract or with your consent in a structured, commonly used and machine-readable format (e.g. data relating to your purchases) and to ask us to transfer that personal data to another data controller.&lt;br&gt;&lt;br&gt;\r\nRight to withdraw your consent - Wherever we rely on your consent, you will always be able to withdraw that consent, although we may have other legal grounds for processing your data for other purposes.&lt;br&gt;&lt;br&gt;\r\nRight to object, at any time\r\nYou have the right to object at any time to the processing of your personal data in some circumstances (in particular, where we don&rsquo;t have to process the data to meet a contractual or other legal requirement, or where we are using your data for direct marketing.&lt;br&gt;&lt;br&gt;\r\nYou can exercise the above rights at any time by:&lt;br&gt;&lt;br&gt;', 'Are you absolutely SURE you want to delete your account. This cannot be undone!', 'Accept', 'More Info', 'Delete My Account', 'No! I Changed My Mind.', 'Yes, Please Delete My Account', 0, '2025-05-15 01:49:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_ip_blacklist`
--

CREATE TABLE `us_ip_blacklist` (
  `id` int(11) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `last_user` int(11) NOT NULL DEFAULT 0,
  `reason` int(11) NOT NULL DEFAULT 0,
  `expires` datetime DEFAULT NULL,
  `descrip` varchar(255) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_ip_list`
--

CREATE TABLE `us_ip_list` (
  `id` int(11) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `us_ip_list`
--

INSERT INTO `us_ip_list` (`id`, `ip`, `user_id`, `timestamp`) VALUES
(2, '::1', 1, '2025-06-06 09:15:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_ip_whitelist`
--

CREATE TABLE `us_ip_whitelist` (
  `id` int(11) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `descrip` varchar(255) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_login_fails`
--

CREATE TABLE `us_login_fails` (
  `id` int(11) NOT NULL,
  `login_method` varchar(50) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `ts` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_management`
--

CREATE TABLE `us_management` (
  `id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `view` varchar(255) NOT NULL,
  `feature` varchar(255) NOT NULL,
  `access` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `us_management`
--

INSERT INTO `us_management` (`id`, `page`, `view`, `feature`, `access`) VALUES
(1, '_admin_manage_ip.php', 'ip', 'IP Whitelist/Blacklist', ''),
(2, '_admin_nav.php', 'nav', 'Navigation [List/Add/Delete]', ''),
(3, '_admin_nav_item.php', 'nav_item', 'Navigation [View/Edit]', ''),
(4, '_admin_pages.php', 'pages', 'Page Management [List]', ''),
(5, '_admin_page.php', 'page', 'Page Management [View/Edit]', ''),
(6, '_admin_security_logs.php', 'security_logs', 'Security Logs', ''),
(7, '_admin_templates.php', 'templates', 'Templates', ''),
(8, '_admin_tools_check_updates.php', 'updates', 'Check Updates', ''),
(16, '_admin_menus.php', 'menus', 'Manage UltraMenu', ''),
(17, '_admin_logs.php', 'logs', 'System Logs', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_menus`
--

CREATE TABLE `us_menus` (
  `id` int(11) UNSIGNED NOT NULL,
  `menu_name` varchar(255) DEFAULT NULL,
  `type` varchar(75) DEFAULT NULL,
  `nav_class` varchar(255) DEFAULT NULL,
  `theme` varchar(25) DEFAULT NULL,
  `z_index` int(11) DEFAULT NULL,
  `brand_html` text DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT 0,
  `justify` varchar(10) DEFAULT 'right',
  `show_active` tinyint(1) DEFAULT 0,
  `screen_reader_mode` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `us_menus`
--

INSERT INTO `us_menus` (`id`, `menu_name`, `type`, `nav_class`, `theme`, `z_index`, `brand_html`, `disabled`, `justify`, `show_active`, `screen_reader_mode`) VALUES
(1, 'Main Menu', 'horizontal', '', 'dark', 1050, '&lt;a href=&quot;{{root}}&quot; &gt;\r\n&lt;img src=&quot;{{root}}users/images/logo.png&quot; /&gt;', 0, 'right', 0, 0),
(2, 'Dashboard Menu', 'horizontal', NULL, 'dark', 55, '&lt;a href=&quot;{{root}}&quot; title=&quot;Home Page&quot;&gt;\r\n&lt;img src=&quot;{{root}}users/images/logo.png&quot; alt=&quot;Main logo&quot; /&gt;&lt;/a&gt;', 0, 'right', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_menu_items`
--

CREATE TABLE `us_menu_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `menu` int(11) UNSIGNED NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `icon_class` varchar(255) DEFAULT NULL,
  `li_class` varchar(255) DEFAULT NULL,
  `a_class` varchar(255) DEFAULT NULL,
  `link_target` varchar(50) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT 0,
  `permissions` varchar(1000) DEFAULT NULL,
  `tags` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `us_menu_items`
--

INSERT INTO `us_menu_items` (`id`, `menu`, `type`, `label`, `link`, `icon_class`, `li_class`, `a_class`, `link_target`, `parent`, `display_order`, `disabled`, `permissions`, `tags`) VALUES
(1, 1, 'dropdown', '', '', 'fa fa-cogs', NULL, NULL, '_self', 0, 12, 0, '[1]', NULL),
(2, 1, 'link', '{{LOGGED_IN_USERNAME}}', 'users/account.php', 'fa fa-user', '', '', '_self', 0, 11, 1, '[\"1\"]', '\"\"'),
(3, 1, 'dropdown', 'Ayuda', '', 'fa fa-life-ring', '', '', '_self', 0, 13, 0, '[\"0\"]', '\"\"'),
(4, 1, 'link', 'Registrarse', 'users/join.php', 'fa fa-plus-square', '', '', '_self', 0, 3, 0, '[\"0\"]', '\"\"'),
(5, 1, 'link', 'Ingresar', 'users/login.php', 'fa fa-sign-in', '', '', '_self', 0, 2, 0, '[\"0\"]', '\"\"'),
(6, 1, 'link', 'Inicio', 'index.php', 'fa fa-home', '', '', '_self', 0, 1, 0, '[\"0\",\"1\"]', '\"\"'),
(8, 1, 'link', 'Inicio', 'pages/inicio.php', 'fa fa-home', '', '', '_self', 1, 1, 0, '[\"1\"]', '\"\"'),
(9, 1, 'link', 'Cuenta', 'users/account.php', 'fa fa-user', '', '', '_self', 1, 2, 0, '[\"1\"]', '\"\"'),
(10, 1, 'separator', '', '', '', NULL, NULL, '_self', 1, 4, 0, '[1]', NULL),
(11, 1, 'link', '{{MENU_DASH}}', 'users/admin.php', 'fa fa-cogs', NULL, NULL, '_self', 1, 5, 0, '[2]', NULL),
(12, 1, 'link', '{{MENU_USER_MGR}}', 'users/admin.php?view=users', 'fa fa-user', NULL, NULL, '_self', 1, 6, 0, '[2]', NULL),
(13, 1, 'link', '{{MENU_PERM_MGR}}', 'users/admin.php?view=permissions', 'fa fa-lock', NULL, NULL, '_self', 1, 7, 0, '[2]', NULL),
(14, 1, 'link', '{{MENU_PAGE_MGR}}', 'users/admin.php?view=pages', 'fa fa-wrench', NULL, NULL, '_self', 1, 8, 0, '[2]', NULL),
(15, 1, 'link', '{{MENU_LOGS_MGR}}', 'users/admin.php?view=logs', 'fa fa-search', NULL, NULL, '_self', 1, 9, 0, '[2]', NULL),
(16, 1, 'separator', '', '', '', NULL, NULL, '_self', 1, 10, 0, '[2]', NULL),
(17, 1, 'link', '{{MENU_LOGOUT}}', 'users/logout.php', 'fa fa-sign-out', NULL, NULL, '_self', 1, 11, 0, '[2,1]', NULL),
(18, 1, 'link', 'Olvide mi contraseña', 'users/forgot_password.php', 'fa fa-wrench', '', '', '_self', 3, 1, 0, '[\"0\"]', '\"\"'),
(19, 1, 'link', 'Reenviar email de verificacion', 'users/verify_resend.php', 'fa fa-exclamation-triangle', '', '', '_self', 3, 99999, 0, '[\"0\"]', '\"\"'),
(45, 2, 'dropdown', 'Tools', '', 'fa fa-wrench', '', '', '_self', 0, 3, 0, '[2]', NULL),
(46, 2, 'link', 'User Manager', 'users/admin.php?view=users', 'fa fa-user', NULL, NULL, NULL, 45, 15, 0, '[2]', NULL),
(47, 2, 'link', 'Bug Report', 'users/admin.php?view=bugs', 'fa fa-bug', NULL, NULL, NULL, 45, 1, 0, '[2]', NULL),
(48, 2, 'link', 'IP Manager', 'users/admin.php?view=ip', 'fa fa-warning', NULL, NULL, NULL, 45, 3, 0, '[0]', NULL),
(49, 2, 'link', 'Cron Jobs', 'users/admin.php?view=cron', 'fa fa-terminal', NULL, NULL, NULL, 45, 2, 0, '[2]', NULL),
(50, 2, 'link', 'Security Logs', 'users/admin.php?view=security_logs', 'fa fa-lock', NULL, NULL, NULL, 45, 9, 0, '[2]', NULL),
(51, 2, 'link', 'System Logs', 'users/admin.php?view=logs', 'fa fa-list-ol', NULL, NULL, NULL, 45, 10, 0, '[2]', NULL),
(52, 2, 'link', 'Templates', 'users/admin.php?view=templates', 'fa fa-eye', NULL, NULL, NULL, 45, 11, 0, '[2]', NULL),
(53, 2, 'link', 'Updates', 'users/admin.php?view=updates', 'fa fa-arrow-circle-o-up', NULL, NULL, NULL, 45, 12, 0, '[2]', NULL),
(54, 2, 'link', 'Page Manager', 'users/admin.php?view=pages', 'fa fa-file', NULL, NULL, NULL, 45, 7, 0, '[2]', NULL),
(55, 2, 'link', 'Permissions', 'users/admin.php?view=permissions', 'fa fa-unlock-alt', NULL, NULL, NULL, 45, 8, 0, '[2]', NULL),
(56, 2, 'dropdown', 'Settings', '', 'fa fa-gear', '', '', '_self', 0, 4, 0, '[2]', NULL),
(57, 2, 'link', 'General', 'users/admin.php?view=general', 'fa fa-check', NULL, NULL, NULL, 56, 1, 0, '[2]', NULL),
(58, 2, 'link', 'Registration', 'users/admin.php?view=reg', 'fa fa-users', NULL, NULL, NULL, 56, 2, 0, '[2]', NULL),
(59, 2, 'link', 'Email', 'users/admin.php?view=email', 'fa fa-envelope', NULL, NULL, NULL, 56, 3, 0, '[0]', NULL),
(60, 2, 'link', 'Navigation (Classic)', 'users/admin.php?view=nav', 'fa fa-rocket', NULL, NULL, NULL, 56, 4, 0, '[2]', NULL),
(61, 2, 'link', 'UltraMenu', 'users/admin.php?view=menus', 'fa fa-lock', NULL, NULL, NULL, 56, 5, 0, '[2]', NULL),
(62, 2, 'link', 'Dashboard Access', 'users/admin.php?view=access', 'fa fa-file-code-o', NULL, NULL, NULL, 56, 5, 0, '[2]', NULL),
(63, 2, 'dropdown', 'Plugins', '#', 'fa fa-plug', '', '', '_self', 0, 5, 0, '[2]', NULL),
(64, 2, 'snippet', 'All Plugins', 'users/includes/menu_hooks/plugins.php', '', NULL, NULL, NULL, 63, 2, 0, '[2]', NULL),
(65, 2, 'link', 'Plugin Manager', 'users/admin.php?view=plugins', 'fa fa-puzzle-piece', NULL, NULL, NULL, 63, 1, 0, '[2]', NULL),
(66, 2, 'link', 'Spice Shaker', 'users/admin.php?view=spice', 'fa fa-user-secret', '', '', '_self', 0, 2, 0, '[2]', NULL),
(67, 2, 'link', 'Home', '#', 'fa fa-home', '', '', '_self', 0, 1, 0, '[2]', NULL),
(68, 2, 'link', 'Dashboard', 'users/admin.php', 'fa-solid fa-desktop', '', '', '_self', 0, 1, 0, '[2]', NULL),
(70, 1, 'link', 'Rutas', 'pages/rutas.php', 'fa fa-route', '', '', '_self', 0, 4, 0, '[\"0\",\"1\"]', '\"\"'),
(71, 1, 'link', 'Editar Ruta', 'pages/nueva_ruta.php', 'fa fa-route', '', '', '_self', 77, 1, 0, '[\"2\",\"4\"]', '\"\"'),
(72, 1, 'link', 'Contacto', 'pages/contacto.php', 'fa fa-envelope', '', '', '_self', 0, 9, 0, '[\"0\",\"2\",\"3\",\"1\"]', '\"\"'),
(73, 1, 'link', 'Como usar GPX', 'pages/gpxdocs.php', 'fa fa-book', '', '', '_self', 0, 5, 0, '[\"0\",\"2\",\"4\",\"3\",\"1\"]', '\"\"'),
(74, 1, 'link', 'Mis rutas', 'pages/mis_compras.php', 'fa fa-map-location-dot', '', '', '_self', 1, 3, 0, '[\"2\",\"4\",\"3\",\"1\"]', '\"\"'),
(75, 1, 'link', 'Visor GPX', 'pages/gpx_viewer.php', 'fa fa-road', '', '', '_self', 0, 6, 0, '[\"2\",\"4\",\"3\",\"1\"]', '\"\"'),
(76, 1, 'link', 'Siluetas GPX', 'pages/siluetas_gpx.php', 'fa fa-draw-polygon', '', '', '_self', 77, 3, 0, '[\"2\",\"4\"]', '\"\"'),
(77, 1, 'dropdown', 'Admin', '#', 'fa fa-user', '', '', '_self', 0, 10, 0, '[\"2\",\"4\"]', '\"\"'),
(78, 1, 'link', 'GPS', 'gps/info.php', 'fa fa-location-arrow', '', '', '_self', 0, 8, 1, '[\"0\",\"2\",\"4\",\"3\",\"1\"]', '\"\"'),
(79, 1, 'link', 'Conversor GPX', 'pages/conversor.php', 'fa fa-file-signature', '', '', '_self', 0, 7, 1, '[\"0\",\"2\",\"4\",\"3\",\"1\"]', '\"\"'),
(80, 1, 'link', 'Cupones', 'pages/cupones.php', 'fa fa-ticket', '', '', '_self', 77, 2, 0, '[\"2\",\"4\"]', '\"\"'),
(81, 1, 'link', 'Estadisticas Cupones', 'pages/estadisticas_cupones.php', 'fa fa-chart-line', '', '', '_self', 77, 4, 0, '[\"2\",\"4\"]', '\"\"'),
(83, 1, 'link', 'Estadisticas Descargas', 'pages/estadisticas_descargas.php', '', '', '', '_self', 77, 5, 0, '[\"2\",\"4\"]', '\"\"');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_password_strength`
--

CREATE TABLE `us_password_strength` (
  `id` int(11) NOT NULL,
  `enforce_rules` tinyint(1) DEFAULT 0,
  `meter_active` tinyint(1) DEFAULT 0,
  `min_length` int(11) DEFAULT 8,
  `max_length` int(11) DEFAULT 24,
  `require_lowercase` tinyint(1) DEFAULT 1,
  `require_uppercase` tinyint(1) DEFAULT 1,
  `require_numbers` tinyint(1) DEFAULT 1,
  `require_symbols` tinyint(1) DEFAULT 1,
  `min_score` int(11) DEFAULT 5,
  `uppercase_score` int(11) NOT NULL DEFAULT 6,
  `lowercase_score` int(11) NOT NULL DEFAULT 6,
  `number_score` int(11) NOT NULL DEFAULT 6,
  `symbol_score` int(11) NOT NULL DEFAULT 11,
  `greater_eight` int(11) NOT NULL DEFAULT 15,
  `greater_twelve` int(11) NOT NULL DEFAULT 28,
  `greater_sixteen` int(11) NOT NULL DEFAULT 40
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `us_password_strength`
--

INSERT INTO `us_password_strength` (`id`, `enforce_rules`, `meter_active`, `min_length`, `max_length`, `require_lowercase`, `require_uppercase`, `require_numbers`, `require_symbols`, `min_score`, `uppercase_score`, `lowercase_score`, `number_score`, `symbol_score`, `greater_eight`, `greater_twelve`, `greater_sixteen`) VALUES
(1, 0, 1, 6, 150, 1, 0, 0, 1, 75, 6, 6, 6, 11, 15, 28, 40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_plugins`
--

CREATE TABLE `us_plugins` (
  `id` int(11) NOT NULL,
  `plugin` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `updates` mediumtext DEFAULT NULL,
  `last_check` datetime DEFAULT '2020-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `us_plugins`
--

INSERT INTO `us_plugins` (`id`, `plugin`, `status`, `updates`, `last_check`) VALUES
(1, 'store', 'active', NULL, '2025-05-12 00:20:32'),
(2, 'payments', 'active', NULL, '2025-05-12 00:21:52'),
(3, 'downloads', 'active', '[\"00001\",\"00002\",\"00003\",\"00004\",\"00007\",\"00008\",\"00009\"]', '2025-05-12 00:28:28'),
(4, 'gdpr', 'active', '[\"00001\",\"00002\"]', '2025-05-15 01:49:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_plugin_hooks`
--

CREATE TABLE `us_plugin_hooks` (
  `id` int(11) UNSIGNED NOT NULL,
  `page` varchar(255) NOT NULL,
  `folder` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `hook` varchar(255) NOT NULL,
  `disabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `us_plugin_hooks`
--

INSERT INTO `us_plugin_hooks` (`id`, `page`, `folder`, `position`, `hook`, `disabled`) VALUES
(1, 'admin.php?view=user', 'userspice_core', 'form', 'hooks/tags_admin_user_form.php', 0),
(2, 'admin.php?view=user', 'userspice_core', 'post', 'hooks/tags_admin_user_post.php', 0),
(3, 'user_settings.php', 'gdpr', 'bottom', 'hooks/settingsbottom.php', 0),
(4, 'account.php', 'gdpr', 'body', 'hooks/accountbody.php', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_saas_levels`
--

CREATE TABLE `us_saas_levels` (
  `id` int(11) NOT NULL,
  `level` varchar(255) NOT NULL,
  `users` int(11) NOT NULL,
  `details` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_saas_orgs`
--

CREATE TABLE `us_saas_orgs` (
  `id` int(11) NOT NULL,
  `org` varchar(255) NOT NULL,
  `owner` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `us_user_sessions`
--

CREATE TABLE `us_user_sessions` (
  `kUserSessionID` int(11) UNSIGNED NOT NULL,
  `fkUserID` int(11) UNSIGNED NOT NULL,
  `UserFingerprint` varchar(255) NOT NULL,
  `UserSessionIP` varchar(255) NOT NULL,
  `UserSessionOS` varchar(255) NOT NULL,
  `UserSessionBrowser` varchar(255) NOT NULL,
  `UserSessionStarted` datetime NOT NULL,
  `UserSessionLastUsed` datetime DEFAULT NULL,
  `UserSessionLastPage` varchar(255) NOT NULL,
  `UserSessionEnded` tinyint(1) NOT NULL DEFAULT 0,
  `UserSessionEnded_Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aa_compras`
--
ALTER TABLE `aa_compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ruta_id` (`ruta_id`);

--
-- Indices de la tabla `aa_cupones`
--
ALTER TABLE `aa_cupones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_codigo` (`codigo`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_fechas` (`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `aa_cupones_uso`
--
ALTER TABLE `aa_cupones_uso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cupon_id` (`cupon_id`),
  ADD KEY `ruta_id` (`ruta_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `aa_rutas`
--
ALTER TABLE `aa_rutas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `aa_rutas_galeria`
--
ALTER TABLE `aa_rutas_galeria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ruta_id` (`ruta_id`);

--
-- Indices de la tabla `audit`
--
ALTER TABLE `audit`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `crons`
--
ALTER TABLE `crons`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `crons_logs`
--
ALTER TABLE `crons_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `email`
--
ALTER TABLE `email`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `groups_menus`
--
ALTER TABLE `groups_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indices de la tabla `keys`
--
ALTER TABLE `keys`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `message_threads`
--
ALTER TABLE `message_threads`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permission_page_matches`
--
ALTER TABLE `permission_page_matches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_download_files`
--
ALTER TABLE `plg_download_files`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_download_links`
--
ALTER TABLE `plg_download_links`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_download_logs`
--
ALTER TABLE `plg_download_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_download_settings`
--
ALTER TABLE `plg_download_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_payments`
--
ALTER TABLE `plg_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_payments_options`
--
ALTER TABLE `plg_payments_options`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_social_logins`
--
ALTER TABLE `plg_social_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_tags`
--
ALTER TABLE `plg_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plg_tags_matches`
--
ALTER TABLE `plg_tags_matches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rutas_log_descargas`
--
ALTER TABLE `rutas_log_descargas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `store_categories`
--
ALTER TABLE `store_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `store_inventory`
--
ALTER TABLE `store_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `store_inventory_photos`
--
ALTER TABLE `store_inventory_photos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `store_inventory_vars`
--
ALTER TABLE `store_inventory_vars`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `store_orders`
--
ALTER TABLE `store_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `store_order_items`
--
ALTER TABLE `store_order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `store_order_status`
--
ALTER TABLE `store_order_status`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `store_payment_options`
--
ALTER TABLE `store_payment_options`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `EMAIL` (`email`) USING BTREE;

--
-- Indices de la tabla `users_online`
--
ALTER TABLE `users_online`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users_session`
--
ALTER TABLE `users_session`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user_permission_matches`
--
ALTER TABLE `user_permission_matches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_announcements`
--
ALTER TABLE `us_announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_email_logins`
--
ALTER TABLE `us_email_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_fingerprints`
--
ALTER TABLE `us_fingerprints`
  ADD PRIMARY KEY (`kFingerprintID`);

--
-- Indices de la tabla `us_fingerprint_assets`
--
ALTER TABLE `us_fingerprint_assets`
  ADD PRIMARY KEY (`kFingerprintAssetID`);

--
-- Indices de la tabla `us_forms`
--
ALTER TABLE `us_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_form_validation`
--
ALTER TABLE `us_form_validation`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_form_views`
--
ALTER TABLE `us_form_views`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_gdpr`
--
ALTER TABLE `us_gdpr`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_ip_blacklist`
--
ALTER TABLE `us_ip_blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_ip_list`
--
ALTER TABLE `us_ip_list`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_ip_whitelist`
--
ALTER TABLE `us_ip_whitelist`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_login_fails`
--
ALTER TABLE `us_login_fails`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_management`
--
ALTER TABLE `us_management`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_menus`
--
ALTER TABLE `us_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_menu_items`
--
ALTER TABLE `us_menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_password_strength`
--
ALTER TABLE `us_password_strength`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_plugins`
--
ALTER TABLE `us_plugins`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_plugin_hooks`
--
ALTER TABLE `us_plugin_hooks`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_saas_levels`
--
ALTER TABLE `us_saas_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_saas_orgs`
--
ALTER TABLE `us_saas_orgs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `us_user_sessions`
--
ALTER TABLE `us_user_sessions`
  ADD PRIMARY KEY (`kUserSessionID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aa_compras`
--
ALTER TABLE `aa_compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `aa_cupones`
--
ALTER TABLE `aa_cupones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `aa_cupones_uso`
--
ALTER TABLE `aa_cupones_uso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aa_rutas`
--
ALTER TABLE `aa_rutas`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `aa_rutas_galeria`
--
ALTER TABLE `aa_rutas_galeria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `audit`
--
ALTER TABLE `audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `crons`
--
ALTER TABLE `crons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `crons_logs`
--
ALTER TABLE `crons_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email`
--
ALTER TABLE `email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `groups_menus`
--
ALTER TABLE `groups_menus`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `keys`
--
ALTER TABLE `keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT de la tabla `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `message_threads`
--
ALTER TABLE `message_threads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `permission_page_matches`
--
ALTER TABLE `permission_page_matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de la tabla `plg_download_files`
--
ALTER TABLE `plg_download_files`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plg_download_links`
--
ALTER TABLE `plg_download_links`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plg_download_logs`
--
ALTER TABLE `plg_download_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plg_download_settings`
--
ALTER TABLE `plg_download_settings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `plg_payments`
--
ALTER TABLE `plg_payments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plg_payments_options`
--
ALTER TABLE `plg_payments_options`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `plg_social_logins`
--
ALTER TABLE `plg_social_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plg_tags`
--
ALTER TABLE `plg_tags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plg_tags_matches`
--
ALTER TABLE `plg_tags_matches`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `rutas_log_descargas`
--
ALTER TABLE `rutas_log_descargas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `store_categories`
--
ALTER TABLE `store_categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `store_inventory`
--
ALTER TABLE `store_inventory`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `store_inventory_photos`
--
ALTER TABLE `store_inventory_photos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `store_inventory_vars`
--
ALTER TABLE `store_inventory_vars`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `store_orders`
--
ALTER TABLE `store_orders`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `store_order_items`
--
ALTER TABLE `store_order_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `store_order_status`
--
ALTER TABLE `store_order_status`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `store_payment_options`
--
ALTER TABLE `store_payment_options`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `updates`
--
ALTER TABLE `updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users_session`
--
ALTER TABLE `users_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_permission_matches`
--
ALTER TABLE `user_permission_matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `us_announcements`
--
ALTER TABLE `us_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_email_logins`
--
ALTER TABLE `us_email_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_fingerprints`
--
ALTER TABLE `us_fingerprints`
  MODIFY `kFingerprintID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_fingerprint_assets`
--
ALTER TABLE `us_fingerprint_assets`
  MODIFY `kFingerprintAssetID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_forms`
--
ALTER TABLE `us_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_form_validation`
--
ALTER TABLE `us_form_validation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `us_form_views`
--
ALTER TABLE `us_form_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_gdpr`
--
ALTER TABLE `us_gdpr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `us_ip_blacklist`
--
ALTER TABLE `us_ip_blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `us_ip_list`
--
ALTER TABLE `us_ip_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `us_ip_whitelist`
--
ALTER TABLE `us_ip_whitelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `us_login_fails`
--
ALTER TABLE `us_login_fails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_management`
--
ALTER TABLE `us_management`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `us_menus`
--
ALTER TABLE `us_menus`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `us_menu_items`
--
ALTER TABLE `us_menu_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `us_password_strength`
--
ALTER TABLE `us_password_strength`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `us_plugins`
--
ALTER TABLE `us_plugins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `us_plugin_hooks`
--
ALTER TABLE `us_plugin_hooks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `us_saas_levels`
--
ALTER TABLE `us_saas_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_saas_orgs`
--
ALTER TABLE `us_saas_orgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `us_user_sessions`
--
ALTER TABLE `us_user_sessions`
  MODIFY `kUserSessionID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aa_compras`
--
ALTER TABLE `aa_compras`
  ADD CONSTRAINT `aa_compras_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `aa_compras_ibfk_2` FOREIGN KEY (`ruta_id`) REFERENCES `aa_rutas` (`id`);

--
-- Filtros para la tabla `aa_cupones_uso`
--
ALTER TABLE `aa_cupones_uso`
  ADD CONSTRAINT `aa_cupones_uso_ibfk_1` FOREIGN KEY (`cupon_id`) REFERENCES `aa_cupones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `aa_cupones_uso_ibfk_2` FOREIGN KEY (`ruta_id`) REFERENCES `aa_rutas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `aa_cupones_uso_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `aa_rutas_galeria`
--
ALTER TABLE `aa_rutas_galeria`
  ADD CONSTRAINT `aa_rutas_galeria_ibfk_1` FOREIGN KEY (`ruta_id`) REFERENCES `aa_rutas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
