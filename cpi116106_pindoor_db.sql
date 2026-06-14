-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 14-06-2026 a las 11:04:18
-- Versión del servidor: 10.11.15-MariaDB-cll-lve
-- Versión de PHP: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cpi116106_pindoor_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artistas`
--

CREATE TABLE `artistas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `disciplina` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_perfil` varchar(255) DEFAULT NULL,
  `ciudad` varchar(255) DEFAULT NULL,
  `email_contacto` varchar(255) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `enlace_web` varchar(255) DEFAULT NULL,
  `enlace_instagram` varchar(255) DEFAULT NULL,
  `enlace_facebook` varchar(255) DEFAULT NULL,
  `enlace_spotify` varchar(255) DEFAULT NULL,
  `enlace_youtube` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artista_imagenes`
--

CREATE TABLE `artista_imagenes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `artista_id` bigint(20) UNSIGNED NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `icono` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `slug`, `tipo`, `icono`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Miradores', 'miradores', NULL, 'camera', 'Explora los mejores puntos de Miradores en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(2, 'Cafeterías', 'cafeterias', 'alimentacion', 'coffee', 'Explora los mejores puntos de Cafeterías en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(3, 'Street Art', 'street-art', NULL, 'paint-brush', 'Explora los mejores puntos de Street Art en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(4, 'Monumentos', 'monumentos', NULL, 'monument', 'Explora los mejores puntos de Monumentos en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(5, 'Centro Cultural', 'cultura', NULL, 'theater-masks', 'Explora los mejores puntos de Cultura en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(6, 'Naturaleza', 'naturaleza', NULL, 'leaf', 'Explora los mejores puntos de Naturaleza en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(7, 'Museos', 'museos', NULL, 'landmark', 'Explora los mejores puntos de Museos en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(8, 'Picadas', 'picadas', 'alimentacion', 'utensils', 'Explora los mejores puntos de Picadas en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(9, 'Arquitectura', 'arquitectura', NULL, 'archway', 'Explora los mejores puntos de Arquitectura en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(10, 'Restaurante', 'comer', 'alimentacion', 'hamburger', 'Explora los mejores puntos de Comer en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(11, 'Hostal/Hotel', 'alojar', 'alojamiento', 'bed', 'Explora los mejores puntos de Alojar en Valparaíso.', '2026-03-24 08:47:54', '2026-03-24 08:47:54'),
(12, 'Estatuas', 'estatuas', 'Explora los mejores puntos de estatuas en Valparaíso.', 'chess-knight', 'Explora los mejores puntos de estatuas en Valparaíso.', NULL, NULL),
(13, 'Patrimonio inmaterial', 'patrimonio_inmaterial', NULL, 'wind', 'Patrimonio inmaterial de Valparaíso', NULL, NULL),
(14, 'Plazas', 'plazas', 'Plaza', 'kiwi-bird', 'Plaza', '2026-05-08 16:54:21', '2026-05-08 16:54:21'),
(15, 'Iglesias', 'iglesias', 'Iglesia', 'church', 'Iglesias históricas', '2026-05-08 17:00:17', '2026-05-08 17:00:17'),
(16, 'Ascensores', 'ascensores', 'Asensores', 'helicopter', 'Ascensores o funiculares', '2026-05-08 17:03:16', '2026-05-08 17:03:16'),
(17, 'Tiendas', 'tiendas', 'cliente', NULL, 'Tiendas y locales comerciales: vinillos, ropa de diseño, cerveza artesanal y más.', '2026-05-24 17:41:05', '2026-05-24 17:41:05'),
(18, 'Artesanía', 'artesania', 'cliente', 'basketball', 'Productos artesanales elaborados a mano.', '2026-05-24 17:42:12', '2026-05-24 17:42:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE `configuraciones` (
  `clave` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuraciones`
--

INSERT INTO `configuraciones` (`clave`, `valor`, `created_at`, `updated_at`) VALUES
('panoramas_limite_dias', '90', '2026-05-20 00:42:38', '2026-06-09 03:29:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `experiencias`
--

CREATE TABLE `experiencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `proveedor` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `es_gratuito` tinyint(1) NOT NULL DEFAULT 0,
  `precio` int(10) UNSIGNED DEFAULT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `capacidad` smallint(5) UNSIGNED DEFAULT NULL,
  `nivel` varchar(20) DEFAULT NULL,
  `dias_semana` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dias_semana`)),
  `hora` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `enlace` varchar(500) DEFAULT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `email_contacto` varchar(200) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `experiencias`
--

INSERT INTO `experiencias` (`id`, `titulo`, `proveedor`, `descripcion`, `ubicacion`, `categoria`, `es_gratuito`, `precio`, `duracion`, `capacidad`, `nivel`, `dias_semana`, `hora`, `fecha_inicio`, `fecha_fin`, `enlace`, `whatsapp`, `email_contacto`, `imagen`, `activo`, `estado`, `orden`, `created_at`, `updated_at`) VALUES
(1, 'Taller de Cueca Urbana', 'Rosi Riquelme', 'Taller de Cueca Urbana, compuesta de los estilos deCueca Chora, Porteña y Brava. Usted elige cual desea aprender.', 'Bar Sin Remedio', 'danza', 0, 3000, 'Una hora y media', NULL, 'todos', '[2]', '19:45', NULL, NULL, 'https://www.instagram.com/p/DYx_-GdNEi6/', '56976766961', NULL, 'experiencias/mKlvh6MX7Elm7eyTQ02sRpeSxOGKerKSPtVGpBrH.jpg', 1, 'aprobada', 0, '2026-05-28 13:37:57', '2026-05-28 14:15:44'),
(2, 'Taller de Cueca Urbana (Avanzado)', 'Rosi Riquelme', 'Taller de Cueca Urbana nivel avanzado. Posterior se viviran cuecas en vivo y karaoke bailable.', 'Gato en la Ventana, Cumming 113', 'danza', 0, 3000, 'Una hora y media', NULL, 'avanzado', '[3]', '19:45', NULL, NULL, 'https://www.instagram.com/p/DY2UNNUEeQ6/', '56976766961', NULL, 'experiencias/Fyxi3JDWehYz4vGsWZ1y1qee5oZIVcaQrhrAp2nl.webp', 1, 'aprobada', 0, '2026-05-28 13:57:57', '2026-05-28 14:16:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `experiencia_imagenes`
--

CREATE TABLE `experiencia_imagenes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `experiencia_id` bigint(20) UNSIGNED NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_punto`
--

CREATE TABLE `imagenes_punto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `punto_interes_id` bigint(20) UNSIGNED NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenes_punto`
--

INSERT INTO `imagenes_punto` (`id`, `punto_interes_id`, `ruta`, `es_principal`, `orden`, `created_at`, `updated_at`) VALUES
(1, 1, 'puntos/yZ4XIT5JeoB3ziYLOfyusuWpk4nmhxa0dibbOrtE.jpg', 0, 1, '2026-04-10 04:33:57', '2026-05-14 23:35:59'),
(2, 2, 'puntos/GcCaa30ComoGQ79i1pGATkFDdXc5XYWAyqMPyrMc.jpg', 1, 0, '2026-04-10 04:38:52', '2026-05-09 04:50:09'),
(4, 4, 'puntos/kYDDwTJ8UmN66XOftykFGsUh11F9S7C3Wphzs64b.jpg', 1, 0, '2026-04-10 04:42:04', '2026-04-10 04:42:04'),
(5, 5, 'puntos/AEwJvu0DeBkEZKfabRUGd9un3qNB9ttrCeqhvUCe.jpg', 1, 0, '2026-04-10 04:43:51', '2026-05-29 22:38:15'),
(6, 6, 'puntos/xS71Rj7YKh0HtDlc2OKHNGOX4JdugoXmfFKgmqjK.jpg', 1, 0, '2026-04-10 04:46:36', '2026-05-09 04:50:47'),
(7, 7, 'puntos/2sJeg2yADL3V7Kkr43wpBw1V52wCFLNXPwwqewTp.jpg', 1, 0, '2026-04-10 04:47:20', '2026-04-12 18:15:48'),
(8, 8, 'puntos/UpEqiRuX91YaxvZnAyGs4hxULMDXZJqIJIcdYMRW.jpg', 1, 0, '2026-04-10 04:48:14', '2026-04-12 08:31:19'),
(9, 9, 'puntos/pLZYe0CTldI4EX2yj3WQjHlDExFDf6WHFaXB7TVc.jpg', 0, 1, '2026-04-10 04:49:50', '2026-05-22 03:12:24'),
(10, 10, 'puntos/IDqJ89E9rsf9n7nDpdt7OxJBxCyYX4eElV3XRe3C.jpg', 1, 0, '2026-04-10 04:50:34', '2026-04-10 04:50:34'),
(11, 11, 'puntos/i52nXxLlVsyXqPYT1TrvBe7BxQgvMvUHtoEApel8.jpg', 1, 0, '2026-04-10 04:51:19', '2026-04-10 04:51:19'),
(12, 12, 'puntos/eaxL3oryTgpHnd6l5A7XKxBec9pSUkhu54opf48E.jpg', 1, 0, '2026-04-10 04:52:40', '2026-04-10 04:52:40'),
(13, 13, 'puntos/ixs4AqoJFxjqqvYsJC5Movw1FQC4GktyimsK3Tya.jpg', 1, 0, '2026-04-10 04:54:31', '2026-04-10 04:54:31'),
(14, 14, 'puntos/6S9SpASwv6N4OTtpP5KcSOBPU6L1N81QuSNY5tRV.jpg', 1, 0, '2026-04-10 04:59:55', '2026-04-12 17:25:29'),
(15, 15, 'puntos/VYV6Rl8otCniSmsHLXe11n0vgq634N3HEH9iap8Y.jpg', 1, 0, '2026-04-10 05:01:01', '2026-04-10 05:01:01'),
(16, 16, 'puntos/b1CDmFcawnviY7maEHz3ZCxURRiMIH63eJ13MGXA.jpg', 1, 0, '2026-04-10 05:01:50', '2026-04-10 05:01:50'),
(19, 19, 'puntos/NMuev4AhfnkYl6sqhITXHcsRIrATKx4N9N2B01sa.jpg', 1, 0, '2026-04-10 05:06:31', '2026-04-10 05:06:31'),
(20, 20, 'puntos/Eje5cePWQRnUMqAUA0coEXTJ1NUTkwnX4sJGioaz.png', 0, 0, '2026-04-10 05:07:40', '2026-04-12 20:55:59'),
(21, 21, 'puntos/cwCScqmxMtdQfO0qHemjh9THlr5PMQwKNqjmO6rX.jpg', 1, 0, '2026-04-10 05:11:13', '2026-04-20 05:13:51'),
(22, 22, 'puntos/lh0DpVfMKYYocZdUtB2Y8oWMokzN5T0qccvFjOci.jpg', 1, 0, '2026-04-10 06:49:43', '2026-04-10 06:49:43'),
(23, 23, 'puntos/ntFTHVql101hQvUbPyVUQ9YN6tInj4rOyuV9GMnG.jpg', 1, 0, '2026-04-10 06:50:37', '2026-04-20 05:14:55'),
(24, 24, 'puntos/et8tpMqEi52815tIVO1LHOfxr6gu9ozq55mGvt2r.jpg', 0, 0, '2026-04-10 06:51:45', '2026-04-12 20:26:16'),
(25, 25, 'puntos/wf4yaxGw7oxPBVP0k9JUjQH8MxQAMbDLRIojkRVS.jpg', 1, 0, '2026-04-10 06:52:54', '2026-04-12 08:51:24'),
(26, 26, 'puntos/lnpPjkJe4gxSLkrqi43gSwIhEPibZwClOLUnlASw.jpg', 1, 0, '2026-04-10 06:53:49', '2026-04-10 06:53:49'),
(27, 27, 'puntos/UHhqcI4RR11DOLvmdnmPNFOIaIHAX7XYZ3fBm8Vb.jpg', 1, 0, '2026-04-10 06:55:05', '2026-04-10 06:55:05'),
(28, 28, 'puntos/n9jIrNykH9Hm1ns3p6oCnVLC0mWFpc7ae2adtNiM.jpg', 1, 0, '2026-04-10 06:56:20', '2026-04-10 06:56:20'),
(29, 29, 'puntos/U3HYLzyM7bqp3CA1tr98FK9nV3gLIYwqNYdOZkQ3.jpg', 0, 0, '2026-04-10 06:58:54', '2026-05-21 01:38:09'),
(30, 30, 'puntos/7HqvbBJ9yDsRzDleP7PVtY8rbotIrdoQhSrmKFY2.jpg', 0, 1, '2026-04-10 06:59:45', '2026-05-21 03:44:32'),
(31, 31, 'puntos/4u33WSDrttynkdHw8i7vuDWE1OpmmISdbWzS2eqs.jpg', 1, 0, '2026-04-10 07:00:40', '2026-04-12 08:18:35'),
(32, 32, 'puntos/gUAgioo2uvCAdf1vAidLGquyyEq6a7XwYF2vyZHg.jpg', 0, 0, '2026-04-10 07:01:57', '2026-06-02 22:17:06'),
(33, 33, 'puntos/83XfmZgY7htZMzIsCrZjtlLC6908hyUWkxrrhTdY.jpg', 1, 0, '2026-04-10 07:03:18', '2026-05-29 22:38:07'),
(34, 34, 'puntos/XEPPBhrXvjXFIBAuykrO4pKUaIrvYBH31izuqSkt.jpg', 1, 0, '2026-04-10 07:12:51', '2026-05-29 22:37:45'),
(35, 35, 'puntos/C8dyNnrckECbP13EcJyiNKqLp1RAGY5JU9MVRaHl.jpg', 0, 0, '2026-04-10 07:15:27', '2026-05-09 04:55:46'),
(36, 36, 'puntos/fgdbn5U0CJ1p0CNaSukopF2CAmjKyooyqUlnJCPt.jpg', 0, 0, '2026-04-10 07:20:42', '2026-04-12 07:35:26'),
(37, 37, 'puntos/oeaAWG8SDnEfS3VDyq08U8Jhk9hrKVhCdoeQmmZU.jpg', 1, 0, '2026-04-10 07:23:13', '2026-05-09 04:53:39'),
(38, 38, 'puntos/KArUV2BwrwlMLxWWaiZ1apxBtQwbiEhJroxJT7ul.jpg', 1, 0, '2026-04-10 07:24:32', '2026-04-12 08:24:01'),
(39, 39, 'puntos/aY3hk7qJmEAZSJ3v6pHUy2gJUK9ngfvTs5cRnFz6.jpg', 1, 0, '2026-04-10 07:25:21', '2026-04-10 07:25:21'),
(40, 40, 'puntos/fpYjbgoCqqNrUbe3lMNTXENIfpjqxtfYiRArlYok.jpg', 1, 0, '2026-04-10 07:26:22', '2026-05-09 05:05:28'),
(41, 24, 'puntos/YTHKqZOr43MsBLopN6wkHLSObXzPQAVg1LfePQz9.jpg', 0, 1, '2026-04-10 07:27:52', '2026-04-12 20:26:16'),
(42, 41, 'puntos/6tNS47bzAag5ABqV7ZwkQ6qzJ5yr2xP4wNYdZOHL.jpg', 1, 0, '2026-04-10 16:30:57', '2026-04-10 16:31:44'),
(43, 42, 'puntos/lCb4NnywOTJ2ao0knfHbh62t4ZSh2URGuRImKxTN.jpg', 1, 0, '2026-04-10 16:33:17', '2026-04-10 16:33:17'),
(44, 43, 'puntos/Y9BjecvvoGk8hSA62nzCjvNVHZH447Cal3mJI8hr.jpg', 0, 0, '2026-04-10 16:35:22', '2026-04-12 08:02:21'),
(45, 44, 'puntos/myKme4axumr19vBYcVowutZXfQMgzxTNDnfwxVoF.jpg', 1, 0, '2026-04-10 16:37:18', '2026-04-14 02:52:23'),
(46, 45, 'puntos/jpfoObJcwXveJTt28HKJ5cFteTqwIeHOHPvesJjW.jpg', 1, 0, '2026-04-10 16:38:41', '2026-05-08 17:15:52'),
(47, 46, 'puntos/J53OlULTcxxeQbYFlGOHAyCpMpfazZscBedTffHK.jpg', 1, 0, '2026-04-10 16:40:03', '2026-04-10 16:40:03'),
(48, 47, 'puntos/S7HcQg9MprbOkMemD8CfOtnKs9sXbrk3clgmJBBJ.jpg', 1, 0, '2026-04-10 16:41:14', '2026-05-08 17:15:27'),
(49, 48, 'puntos/X8u1wxYALG6MSwoZhGAwikPWw1jMyJK8fgJwQkMW.jpg', 0, 0, '2026-04-10 16:42:23', '2026-05-08 17:15:12'),
(51, 50, 'puntos/KwODAibaKDuQK5pJ21cGy6QM3F8QcGrnYPTDBLCG.jpg', 1, 0, '2026-04-10 16:57:42', '2026-04-12 08:29:26'),
(52, 51, 'puntos/Offa3gWyWyCnvnYbSEFOXI3JfgSDmUx2PUUCjqa8.jpg', 1, 0, '2026-04-10 16:58:52', '2026-05-08 17:14:39'),
(53, 52, 'puntos/S9XvnkDumAZWpDq5RLPEfVfnCugCykF1J3cUtHo5.jpg', 1, 0, '2026-04-10 17:01:01', '2026-04-12 08:19:17'),
(54, 53, 'puntos/bmqEzwGgrqcACeidHQE68SY2zCjpXmqlbNLATaGu.jpg', 1, 0, '2026-04-10 17:02:00', '2026-05-08 17:12:43'),
(55, 54, 'puntos/QnnzaxNc4y9jFAVHwZ1RAj9n6UjCQX9HBOj9H7tP.jpg', 0, 0, '2026-04-10 17:03:11', '2026-04-13 19:40:38'),
(56, 55, 'puntos/Y9YOzONF14wgfThv7YGHMFT95ulyIRdN1eXtxPsd.jpg', 1, 0, '2026-04-10 17:03:57', '2026-05-14 23:40:13'),
(57, 56, 'puntos/XmKlXjlJ9F3YCInxBb1HwgsyS39OJ6mbDQwtkmWi.jpg', 1, 0, '2026-04-10 17:04:56', '2026-04-10 17:04:56'),
(58, 57, 'puntos/8vlewU9CpJoG6BM4G7NnaloyF813E9nlbbSTNtix.jpg', 1, 0, '2026-04-10 17:07:09', '2026-05-29 22:37:38'),
(59, 58, 'puntos/rpBQHHYR1bVHu0mGXDRZlhh5DGPQsEoBCktQs6OK.jpg', 1, 0, '2026-04-10 17:08:43', '2026-05-29 22:37:30'),
(60, 59, 'puntos/BcMk7xy8LJDdNaa7iFCsTNeqizJMz3tyOiJNlzSB.jpg', 1, 0, '2026-04-10 17:09:58', '2026-05-29 22:37:22'),
(61, 60, 'puntos/a4idQ6Q6kVViUbs9XDPkUIJDOw5eFKA1aS5zYIbZ.jpg', 1, 0, '2026-04-10 17:11:20', '2026-05-23 17:40:33'),
(62, 61, 'puntos/YW5YI9dWpx6coUucfp7rSRIxuu6btRI5ekDkcchR.jpg', 1, 0, '2026-04-10 17:14:53', '2026-05-08 17:10:33'),
(63, 62, 'puntos/VdrM8pexPMzeEr1zq6Jep73DIUMbuJ1gVTU2dMqZ.jpg', 0, 1, '2026-04-10 17:16:01', '2026-05-21 04:04:01'),
(64, 63, 'puntos/KAqX2SBJBKaNzHunORbRQw3RiCAzvyuZ7QnXazCp.jpg', 0, 1, '2026-04-10 17:17:09', '2026-05-29 22:37:14'),
(67, 64, 'puntos/MfZFU953DvvmIoQooT0muyjcQkTyjbRwPzhCwXKn.png', 0, 0, '2026-04-12 06:02:33', '2026-05-21 21:18:34'),
(68, 64, 'puntos/N4YNDnz7M1xIl1mq5bWKz44lvFICVlisuicvZNkC.png', 1, 1, '2026-04-12 06:02:33', '2026-05-21 21:18:34'),
(69, 64, 'puntos/N8kQKFDlunmIcshlV8ItQsSMah46lW1OR6o8fTpR.png', 0, 2, '2026-04-12 06:02:53', '2026-05-21 21:18:34'),
(70, 64, 'puntos/aBzqKOBGTswER7JshG5WP7fyNLY9peUW0ODtVeRz.png', 0, 3, '2026-04-12 06:03:23', '2026-05-21 21:18:34'),
(72, 63, 'puntos/VXBl6lOvxmpumoTNRwyc1JnmQaNJA0xcG7zzyb5c.jpg', 0, 2, '2026-04-12 07:19:27', '2026-05-29 22:37:14'),
(73, 63, 'puntos/bMSKDrCr1CZ3Grzvqm1uXPKdGWEsp6bIzolvzW2J.jpg', 0, 3, '2026-04-12 07:19:27', '2026-05-29 22:37:14'),
(74, 60, 'puntos/AnaVa4cXchs80ci7ULi98RltmRBvu8XlIuwsVAEA.jpg', 0, 1, '2026-04-12 07:24:19', '2026-05-23 17:40:33'),
(75, 60, 'puntos/HL4kCymSOU1e2ujhbFlItNB9v1Jwl4PqhFngnU6Z.jpg', 0, 2, '2026-04-12 07:24:19', '2026-05-23 17:40:33'),
(76, 60, 'puntos/IbGKkQ3EcPC7bSytw60EqOic7NjppgWLGEW2BAkH.jpg', 0, 3, '2026-04-12 07:24:19', '2026-05-23 17:40:33'),
(77, 60, 'puntos/vSiimLPHbjnS5xsTCri3LUEn2pncTPZs3b4zAeqo.jpg', 0, 4, '2026-04-12 07:24:19', '2026-05-23 17:40:33'),
(78, 61, 'puntos/p2eA25Ni8tD4aCidY93pktK6axOfpMfseaRyeqI3.jpg', 0, 1, '2026-04-12 07:26:56', '2026-05-08 17:10:33'),
(79, 61, 'puntos/BfANUZBuhjBMfZD1vU40Z5hH5bdtDVd0RUWPxOh3.jpg', 0, 2, '2026-04-12 07:26:56', '2026-05-08 17:10:33'),
(80, 61, 'puntos/5am6IMClZYqiAFXpHZpq27JcLlSm7CmzNNLNFeNp.jpg', 0, 3, '2026-04-12 07:26:56', '2026-05-08 17:10:33'),
(81, 61, 'puntos/DNDMkqZqD98a3QBtwbjdcofhTHSqrqLneWMhr5Kn.jpg', 0, 4, '2026-04-12 07:26:56', '2026-05-08 17:10:33'),
(82, 37, 'puntos/Glf1DZNgdXl9v1YpZkmqR5cSzA3QmCgAUsVlBcSP.jpg', 0, 1, '2026-04-12 07:33:15', '2026-05-09 04:53:39'),
(83, 37, 'puntos/MS3WqOmFlq8UlHxHlNxHWFdcDXZMdPGCOfBbMPzq.jpg', 0, 2, '2026-04-12 07:33:15', '2026-05-09 04:53:39'),
(84, 37, 'puntos/fKn6jpcNkjBVUasaoWVj3TwHZIldmz2VrmdhxM4u.jpg', 0, 3, '2026-04-12 07:33:15', '2026-05-09 04:53:39'),
(85, 54, 'puntos/09zOEsWr8KBXnosuVVGb9b9peUsLf4TI5l7fXt9E.jpg', 0, 1, '2026-04-12 07:33:45', '2026-04-13 19:40:38'),
(86, 54, 'puntos/UAgZWEgJm5aMidlAVSdiaNLCCAOqy8NukcoxEtD6.jpg', 1, 2, '2026-04-12 07:33:45', '2026-04-13 19:40:38'),
(87, 36, 'puntos/ZEZpCJlnYSaYrlaKiMxnIqhCqNw65aHLPHUWSKal.jpg', 0, 1, '2026-04-12 07:35:26', '2026-04-12 07:35:26'),
(88, 36, 'puntos/jhThRpIo9KLvzf7QHxI4KmvMAGgPZYPXYL0XA4e1.jpg', 1, 2, '2026-04-12 07:35:26', '2026-04-12 07:35:26'),
(89, 36, 'puntos/rcOmj4isek9iIgRApJ5AU13QsUX1GGUq4CawjPE9.jpg', 0, 3, '2026-04-12 07:35:26', '2026-04-12 07:35:26'),
(90, 36, 'puntos/E7KOh0PN6xI9kpfm2UEv9Awky1GYXopYrxshijEy.jpg', 0, 4, '2026-04-12 07:35:26', '2026-04-12 07:35:26'),
(91, 36, 'puntos/FCnroR9OnOGVGbi4XSB6KOPZ1fV1Oju6NgX4lsBJ.jpg', 0, 5, '2026-04-12 07:35:26', '2026-04-12 07:35:26'),
(92, 3, 'puntos/qJGOS7HFDJcSTHfYRQTDu4eazE6LO0dZzqriAulG.jpg', 1, 0, '2026-04-12 07:53:08', '2026-05-22 04:32:21'),
(93, 3, 'puntos/DmstS11v2be8vmcCq7V0ovJ2ynWS8PfCGovacKs8.jpg', 0, 7, '2026-04-12 07:53:08', '2026-05-22 04:32:21'),
(94, 48, 'puntos/jiNEyizIfICYH8YwsoS5fHq5OSdzhkPlwG7P10af.jpg', 0, 1, '2026-04-12 07:56:30', '2026-05-08 17:15:12'),
(95, 48, 'puntos/9Ds0nprbpCzoMeuQxez01AGE6i0ptnbaRH9t31U8.jpg', 0, 2, '2026-04-12 07:56:30', '2026-05-08 17:15:12'),
(96, 48, 'puntos/oyVEi1e9P0OVE5bWZCBiq75gmQfukLPdcDhHi9mV.jpg', 0, 3, '2026-04-12 07:56:30', '2026-05-08 17:15:12'),
(97, 48, 'puntos/DZX8FiCYlOIcIjuoD14RYj8dywGdTlXXtYx6Uzzt.jpg', 0, 4, '2026-04-12 07:56:30', '2026-05-08 17:15:12'),
(98, 48, 'puntos/hbgAHRe1VIRQjQt9P67PYMMhLiaKo88BIeeCHJVo.jpg', 1, 5, '2026-04-12 07:56:30', '2026-05-08 17:15:12'),
(99, 43, 'puntos/zbfXm35LhapamaK5NVdo1jSUThjOHTPq3jH9CVSF.jpg', 0, 1, '2026-04-12 08:01:07', '2026-04-12 08:02:21'),
(100, 43, 'puntos/61Wmg7Rouu7Pmu3FK5khEcEz9qRL3tI7iFqDTAYa.jpg', 1, 2, '2026-04-12 08:01:07', '2026-04-12 08:02:21'),
(101, 18, 'puntos/rITKlmskUWdYuncKNpW1OLYPI20HVyJuGzLC4oyo.jpg', 1, 0, '2026-04-12 08:02:31', '2026-06-09 16:33:35'),
(102, 53, 'puntos/pezeR9F4s3CLrxOqUkhYb5oEw0Llid10knxyV5iu.jpg', 0, 1, '2026-04-12 08:03:06', '2026-05-08 17:12:43'),
(103, 53, 'puntos/byhO1UYMW6z6owCpXMraNmPbvVYfLVErvhQVRE1R.jpg', 0, 2, '2026-04-12 08:03:06', '2026-05-08 17:12:43'),
(104, 53, 'puntos/9xIzZDga0u1lqtQbd7eqHgZWi0G8nIBNxGw7BR6n.jpg', 0, 3, '2026-04-12 08:03:06', '2026-05-08 17:12:43'),
(105, 55, 'puntos/0R38JnHxIunxykPxSDyNx3ZrHW11lL9u2Atb0HoX.jpg', 0, 5, '2026-04-12 08:05:40', '2026-05-14 23:40:13'),
(106, 55, 'puntos/OOIf40KAzuSe68qXAy19KbUD05CtyM9gj0i9qqi3.jpg', 0, 6, '2026-04-12 08:05:55', '2026-05-14 23:40:13'),
(107, 55, 'puntos/s7xH8c5aleWkvEMDhhMcMBi0WJhX5S1XV6Asm9Ya.jpg', 0, 4, '2026-04-12 08:05:55', '2026-05-14 23:40:13'),
(108, 62, 'puntos/K1MBHFUfzgDthG3c26rNa8Tyz60gcJy0TCXRlSpx.jpg', 0, 2, '2026-04-12 08:07:33', '2026-05-21 04:04:01'),
(109, 62, 'puntos/nl1iJjMpVWEHMlR9Ud5u7Z9Un232qhYkL6CPccIZ.jpg', 0, 3, '2026-04-12 08:07:33', '2026-05-21 04:04:01'),
(110, 62, 'puntos/dBs3p5j8e5S36GJ5qpFCrSRxBOkQbbzFv0oB5pvD.jpg', 0, 4, '2026-04-12 08:07:33', '2026-05-21 04:04:01'),
(111, 65, 'puntos/uN3ojsekJYDNgWSALJXNX5sYu4rFOcoa2quzmKIl.jpg', 0, 0, '2026-04-12 08:14:21', '2026-05-08 17:05:22'),
(112, 65, 'puntos/JStC5lzKyDHhPcUyTuCTKJ7gGwUL6qAmS1invq4U.jpg', 1, 1, '2026-04-12 08:14:21', '2026-05-08 17:05:22'),
(113, 65, 'puntos/1RSNhyfttfeqw84OIjdD4ek2Od29utr2mW65w7el.jpg', 0, 2, '2026-04-12 08:14:21', '2026-05-08 17:05:22'),
(114, 65, 'puntos/f5zFEVV27ffknVBALCzIi7NRr5EEMwn9wtPj84QV.jpg', 0, 3, '2026-04-12 08:14:21', '2026-05-08 17:05:22'),
(115, 31, 'puntos/Ar7jwvnQFbklWu2OLgOwBbtbZlYnoUuRgdqxYHA9.jpg', 0, 1, '2026-04-12 08:18:35', '2026-04-12 08:18:35'),
(116, 52, 'puntos/xexiIlzek0CFBaaotccskyWNpEWnYwPd924K6NQL.jpg', 0, 1, '2026-04-12 08:19:17', '2026-04-12 08:19:17'),
(117, 66, 'puntos/NlnTMQzTzIi5IyM6LLLrJDSySQGwVqUi6gdZm09K.jpg', 1, 0, '2026-04-12 08:22:47', '2026-05-12 03:04:30'),
(118, 66, 'puntos/UgE3AqrfICRKPT0CCWfGj5UGAFx08aUuNyZYizeS.jpg', 0, 1, '2026-04-12 08:22:47', '2026-05-12 03:04:30'),
(119, 38, 'puntos/lSKY27vOFvprVFpON8c9cUwSAle7fzxPn1DCjaHu.jpg', 0, 1, '2026-04-12 08:24:01', '2026-04-12 08:24:01'),
(120, 49, 'puntos/JXY4tyChlf7w52Aa8c1L35zMOaeGRY71tEWSpfS2.jpg', 0, 0, '2026-04-12 08:26:46', '2026-04-12 08:26:46'),
(121, 49, 'puntos/Igp04nLB2bLqsz3EZ1wPShq9QtnoLP3OHcfgVUAk.jpg', 0, 1, '2026-04-12 08:26:46', '2026-04-12 08:26:46'),
(122, 49, 'puntos/rmCE2DSQSKTtNOSpLSVdeYVZOzPiCQRn0HtfXIIB.jpg', 1, 2, '2026-04-12 08:26:46', '2026-04-12 08:26:46'),
(123, 49, 'puntos/iM13q7szazboLTtt5vhnkNJ9rASq4YXYeSlnTvDY.jpg', 0, 3, '2026-04-12 08:26:46', '2026-04-12 08:26:46'),
(124, 49, 'puntos/TJeyWtOX69XQTnLoOw8zzfuuOJ4d186iRkfu2vS8.jpg', 0, 4, '2026-04-12 08:26:46', '2026-04-12 08:26:46'),
(125, 49, 'puntos/kcDAsr0Z9tC449hwZCcnvXqrT97BZLq4aU7T9uF4.jpg', 0, 5, '2026-04-12 08:26:46', '2026-04-12 08:26:46'),
(126, 50, 'puntos/1BUyBhNGz4QKczYGlHzh68p8nP3qq8qJIkzSK8KF.jpg', 0, 1, '2026-04-12 08:29:26', '2026-04-12 08:29:26'),
(127, 50, 'puntos/lkvycz9NxXuM7LpYMwj3p4KF4zDetFkrzFhoEMjk.jpg', 0, 2, '2026-04-12 08:29:26', '2026-04-12 08:29:26'),
(128, 50, 'puntos/Ra0RvR1VZcag2dbIhT7i7cAxlyY67eaU56sYPNuj.jpg', 0, 3, '2026-04-12 08:29:26', '2026-04-12 08:29:26'),
(129, 50, 'puntos/Q9b5RHP2RF6TTkI7jmEHBrwMbkANgGTWFQZBebQY.jpg', 0, 4, '2026-04-12 08:29:26', '2026-04-12 08:29:26'),
(130, 8, 'puntos/JowEyWpvJ0ebBLTC04suug0r9i3GtpEJI3inbBJl.jpg', 0, 1, '2026-04-12 08:31:19', '2026-04-12 08:31:19'),
(131, 67, 'puntos/101cBXOtVmxWJ2PXOSkL2HapA2ufkrUfDJXJEumk.jpg', 1, 0, '2026-04-12 08:33:12', '2026-04-18 03:05:45'),
(132, 67, 'puntos/yxqNoZnVgo4GUfL5AYhQ2DHRqHl9ehfFsRxx0y4B.jpg', 0, 1, '2026-04-12 08:33:12', '2026-04-18 03:05:45'),
(133, 68, 'puntos/nVsFSxQCDTuKrK3un5njoFqWpzqUerxSQe3ttIHx.jpg', 0, 0, '2026-04-12 08:35:42', '2026-04-30 01:41:04'),
(134, 68, 'puntos/IUskokpztylWhMhAV5CLEfa3U3IEVXeIUan58U3P.jpg', 1, 1, '2026-04-12 08:35:42', '2026-04-30 01:41:04'),
(135, 68, 'puntos/JAicm4gzTFprsUpY3czK32y1Xb310NB50LeD0fwv.jpg', 0, 2, '2026-04-12 08:35:42', '2026-04-30 01:41:04'),
(136, 54, 'puntos/QH9mV9Z6SLob4xusG9NUqptpwdkbz9OXBWvW0BJz.jpg', 0, 3, '2026-04-12 08:36:45', '2026-04-13 19:40:38'),
(137, 54, 'puntos/avOJ3ZQRTOh14b4LxVFeVabyQzHGdKwt3K9VZ371.jpg', 0, 4, '2026-04-12 08:36:45', '2026-04-13 19:40:38'),
(138, 54, 'puntos/mIHjI0Nt5ZJhBbKwTh3zsAstaDPBZHcjXCxiMwmG.jpg', 0, 5, '2026-04-12 08:36:45', '2026-04-13 19:40:38'),
(139, 69, 'puntos/y8ykV0ZCnGpndr9zjSeHDyqyAoXv4QdoCBAhlMdT.jpg', 1, 0, '2026-04-12 08:41:13', '2026-05-08 17:01:18'),
(140, 69, 'puntos/pWyndwatgK0EvHK42mlTsXHHSipQ8TA2Stbuv9Yc.jpg', 0, 1, '2026-04-12 08:41:13', '2026-05-08 17:01:18'),
(141, 35, 'puntos/kAM3L4IyHGCVXwdOyrgPXZsPSFjfTlAHiL7Udvtz.jpg', 0, 1, '2026-04-12 08:45:11', '2026-05-09 04:55:46'),
(142, 35, 'puntos/MY08K38WxzZd0Y5oqQlV9Y5XSc9y6n3MBiJ1sCXD.jpg', 0, 2, '2026-04-12 08:45:11', '2026-05-09 04:55:46'),
(143, 35, 'puntos/6SnV66StMDmZK0B5wVJA8K42DELSVcrWa56XbJjA.jpg', 1, 3, '2026-04-12 08:45:11', '2026-05-09 04:55:46'),
(144, 70, 'puntos/Q3HevdrHWOixYDfDQ1BampAzpZYw52gkzD5a8q3t.jpg', 0, 0, '2026-04-12 08:49:37', '2026-05-08 17:02:10'),
(145, 70, 'puntos/88VnGGolC8vOxuc2ALkLJIaY7SP8ABIxyD82vNSS.jpg', 0, 1, '2026-04-12 08:49:37', '2026-05-08 17:02:10'),
(146, 70, 'puntos/97YtR8UGWSPDIorf4et2SDAqZX8bkFx72Vwerxt2.jpg', 1, 2, '2026-04-12 08:49:37', '2026-05-08 17:02:10'),
(147, 25, 'puntos/yheF9iPbsiMonylk58xOtdP5EiJMpM953HUKHamP.jpg', 0, 1, '2026-04-12 08:51:24', '2026-04-12 08:51:24'),
(148, 25, 'puntos/inpZyNts1p4pdAEIJrrR64OtTrzrbZNSF5Vk1PXB.jpg', 0, 2, '2026-04-12 08:51:24', '2026-04-12 08:51:24'),
(149, 25, 'puntos/8gKn7wc4a30DWyCYSYWpV8qEY9qis4vJcDYcWcFi.jpg', 0, 3, '2026-04-12 08:51:24', '2026-04-12 08:51:24'),
(151, 14, 'puntos/KP6Jd9uHGcEa3EIDPepfiPdYx2eaJOrZfb4aJ5FW.jpg', 0, 1, '2026-04-12 17:25:29', '2026-04-12 17:25:29'),
(152, 71, 'puntos/8HRCT03wdLFFBZ25moDsX1LQkga1nIFDifNzFxdn.jpg', 1, 0, '2026-04-12 17:33:21', '2026-05-08 17:00:50'),
(153, 71, 'puntos/rlOVoUe3CrRPzDBnsxXD7g1JkrisJJ8Rm0wXCBk3.jpg', 0, 1, '2026-04-12 17:33:21', '2026-05-08 17:00:50'),
(154, 71, 'puntos/uEmyMuVj5lV3Muk7AVP42V5T4MmZndrqUhDRYIg0.jpg', 0, 2, '2026-04-12 17:33:21', '2026-05-08 17:00:50'),
(155, 68, 'puntos/nKMbmhZ7lGHQRVWVogLV5VQCzLvT2eiopLbifuED.jpg', 0, 3, '2026-04-12 17:34:27', '2026-04-30 01:41:04'),
(156, 72, 'puntos/auqNh81RntGsW9TqABWBqEb8JsLPznjp19LsaCDy.jpg', 1, 0, '2026-04-12 17:39:25', '2026-06-09 14:34:48'),
(157, 29, 'puntos/tGVAmWaqCUlVvOgqWmdzkJa7UBfVMR3MZ8PkR3rs.jpg', 0, 1, '2026-04-12 17:40:39', '2026-05-21 01:38:09'),
(158, 29, 'puntos/Zw1Eo9mwO4F9G2M7QX31TRHTaHDN2yDnCKXItKex.jpg', 1, 2, '2026-04-12 17:40:39', '2026-05-21 01:38:09'),
(159, 29, 'puntos/XkRLqtmpOpA8L3fkHKHwxoauErpM5Z5ELZjo3hds.jpg', 0, 3, '2026-04-12 17:40:39', '2026-05-21 01:38:09'),
(160, 24, 'puntos/DNaRpphIucPoywMuGgHEKsJrIXYOBJljtLs83vxU.jpg', 0, 2, '2026-04-12 17:42:35', '2026-04-12 20:26:16'),
(161, 5, 'puntos/W72ZeZTsOe7PIncK5dqfYPLiu9RFgYbysNuvGuZT.jpg', 0, 1, '2026-04-12 17:43:49', '2026-05-29 22:38:15'),
(162, 5, 'puntos/2zIrjJIonU3RurBPDlaQxLHAeFLNh5zehAJa3gCO.jpg', 0, 2, '2026-04-12 17:43:49', '2026-05-29 22:38:15'),
(163, 5, 'puntos/T5T9LUgVW0slgI05kF3RVHHNrqcUn2PhPCpTVsKi.jpg', 0, 3, '2026-04-12 17:43:49', '2026-05-29 22:38:15'),
(164, 66, 'puntos/TBLwfaYgXSGWtryM9baKBD7GneX8makCbjZuUGQ4.jpg', 0, 2, '2026-04-12 17:46:33', '2026-05-12 03:04:30'),
(165, 66, 'puntos/v9E9NjhjjMetDv9KmX1YRYyw8SMvmNuy4XxHPMWA.jpg', 0, 3, '2026-04-12 17:46:33', '2026-05-12 03:04:30'),
(166, 63, 'puntos/AAeDWUzlLFKnUGlaSpoP1h9nCUz63YAWKFO45jPj.jpg', 0, 4, '2026-04-12 17:47:46', '2026-05-29 22:37:14'),
(167, 63, 'puntos/UhvApsdsZkN4pUbz6MMNzxyoEOdKBmnjuTPoT9RT.jpg', 0, 5, '2026-04-12 17:48:09', '2026-05-29 22:37:14'),
(168, 54, 'puntos/U2yYW4na81hkPTYXnCWJ9HmvMxT6c3OE5OuGcppO.jpg', 0, 6, '2026-04-12 17:49:00', '2026-04-13 19:40:38'),
(169, 73, 'puntos/4kJQGwxuVFImUmD8U7WRijEb7CyNebCrlT95xf47.jpg', 1, 0, '2026-04-12 17:54:39', '2026-04-20 05:21:10'),
(170, 73, 'puntos/pzGr5WR9k1fJ4yzhC5ZXqPCMYP6Db0uNaJdnViac.jpg', 0, 1, '2026-04-12 17:54:39', '2026-04-20 05:21:10'),
(171, 73, 'puntos/8Itcfbcjp34A8ZOshm6LIwkPHcR3YEledri7JIdC.jpg', 0, 2, '2026-04-12 17:54:39', '2026-04-20 05:21:10'),
(172, 23, 'puntos/1sL71hLEQGrKUw7cRwLZShIiSxnd0W3XjXSl7Mzb.jpg', 0, 1, '2026-04-12 17:55:06', '2026-04-20 05:14:55'),
(173, 34, 'puntos/fa0u2gQSggcbwEQVQMHEWTi2K4bEYQa1aLaKNTcA.jpg', 0, 1, '2026-04-12 17:56:05', '2026-05-29 22:37:45'),
(174, 34, 'puntos/LfTn1Lh3yrEnadviSjcMz1BkGh4nulxppxDIgnyS.jpg', 0, 2, '2026-04-12 17:56:26', '2026-05-29 22:37:45'),
(175, 74, 'puntos/2wqbGvXvdUz9ZMJshwZ0aMYd3gd5kgcqBffry0LW.jpg', 0, 0, '2026-04-12 18:00:40', '2026-05-02 20:43:10'),
(176, 74, 'puntos/nahJKNcZSKMFGVEXwC2gPRKbDnuLQRcm5lfNg0Ug.jpg', 0, 1, '2026-04-12 18:00:40', '2026-05-02 20:43:10'),
(177, 74, 'puntos/bYshM5DJTdHb9ZNHeqTORQN3foUU9H1b0ZD6j9o4.jpg', 0, 2, '2026-04-12 18:00:40', '2026-05-02 20:43:10'),
(178, 66, 'puntos/MHB9fCOjlYjBLuf21mMO2i26lYdIJWmrygo6csGY.jpg', 0, 4, '2026-04-12 18:10:04', '2026-05-12 03:04:30'),
(179, 66, 'puntos/oRO7VVsB0qTbPKgXSxjlbzd9JhkTIVzfsb8X7rew.jpg', 0, 5, '2026-04-12 18:10:04', '2026-05-12 03:04:30'),
(180, 66, 'puntos/t4xiGDejwQGnblNimhuMxM1AXqHWCL3McarVUd6D.jpg', 0, 6, '2026-04-12 18:10:28', '2026-05-12 03:04:30'),
(182, 32, 'puntos/mJOORyLM850eY1jFsUzAffXZodGjtP23eCer5xtK.jpg', 1, 1, '2026-04-12 18:12:52', '2026-06-02 22:17:06'),
(183, 5, 'puntos/HQB3FBDMeQ762d6rnmmPQu3s2PeuBeGURaxYbw6d.jpg', 0, 4, '2026-04-12 18:13:26', '2026-05-29 22:38:15'),
(184, 32, 'puntos/qyztc3ScpDqn1z7HWGgm1HGirfTG9lJWVXRUSupr.jpg', 0, 2, '2026-04-12 18:13:40', '2026-06-02 22:17:06'),
(185, 33, 'puntos/9DCCGknMQANb5OBlputyrj9gsHuUJMU5OLegXW8q.jpg', 0, 1, '2026-04-12 18:15:24', '2026-05-29 22:38:08'),
(186, 7, 'puntos/mccBgDNwFbx6z1a4PJwL3Whkk1rBkdkfGILO2BYd.jpg', 0, 1, '2026-04-12 18:15:48', '2026-04-12 18:15:48'),
(187, 65, 'puntos/7l5JSEDbN4rSsBtNIJLMLqkgfkVmdMD6sSbHRujS.jpg', 0, 4, '2026-04-12 18:17:19', '2026-05-08 17:05:22'),
(188, 65, 'puntos/Ogj5qzFmKhVozevW7sSrtmfKzHKOHniVY8R0Pw2A.jpg', 0, 5, '2026-04-12 18:20:22', '2026-05-08 17:05:22'),
(189, 24, 'puntos/7vSpT2hZRDY8ZcRE8WDKBYoYaH0RJEGUiSmme39R.jpg', 0, 3, '2026-04-12 20:26:16', '2026-04-12 20:26:16'),
(190, 24, 'puntos/IYPLLIGyAoo0agtSVmRyR4Sydz7VQwksm1xEk65w.jpg', 1, 4, '2026-04-12 20:26:16', '2026-04-12 20:26:16'),
(191, 33, 'puntos/WkYpnrtNBZcekmVpP54tk4j1DlASenzW1BPWIO4T.jpg', 0, 2, '2026-04-12 20:27:14', '2026-05-29 22:38:08'),
(192, 33, 'puntos/PkwUvzNsR0s3wndlYn3E2Rh2qnh2gbP1tfdCDBpn.jpg', 0, 3, '2026-04-12 20:27:15', '2026-05-29 22:38:08'),
(193, 33, 'puntos/e78zUMjApVWBYL51o10lAb0bcUcJlFLbHZmpIdMq.jpg', 0, 4, '2026-04-12 20:27:32', '2026-05-29 22:38:08'),
(194, 63, 'puntos/C4ROKZeJRZufilRjgzwwOdnItQ1hiwP4IkPp8SmJ.jpg', 1, 0, '2026-04-12 20:33:05', '2026-05-29 22:37:14'),
(195, 59, 'puntos/7m25Egn2no82rwLO8xyZ2t13x6g4kWEDdkSArcK1.jpg', 0, 1, '2026-04-12 20:39:03', '2026-05-29 22:37:22'),
(196, 59, 'puntos/cmV39XGMKwK6HIzcPqCAMR3ktp617yPLO1jkueMe.jpg', 0, 2, '2026-04-12 20:39:03', '2026-05-29 22:37:22'),
(197, 59, 'puntos/jv6iNeDRBu97NJkwlSW6Ge1cDH0R36J1hnFwnxiY.jpg', 0, 3, '2026-04-12 20:39:03', '2026-05-29 22:37:22'),
(198, 58, 'puntos/zDpTCxbXLOWc9Y29qLalYGhtpjyEFoZtcNmDsv6I.jpg', 0, 1, '2026-04-12 20:39:11', '2026-05-29 22:37:30'),
(199, 58, 'puntos/G9qxrRVH8JfdlwmhUYup0AcWmpdPaTKIiyvpCiLb.jpg', 0, 2, '2026-04-12 20:39:11', '2026-05-29 22:37:30'),
(200, 33, 'puntos/jxweOlPLY1YAGd6Rjcjq7N7NkMmtU6CT1xwr3gQw.jpg', 0, 5, '2026-04-12 20:45:46', '2026-05-29 22:38:08'),
(201, 33, 'puntos/R8NEn6LGw02Wsy0gpMDhnl53FQ6irJl0YSTallwB.jpg', 0, 6, '2026-04-12 20:45:46', '2026-05-29 22:38:08'),
(202, 63, 'puntos/qufDTb9AQ4QS3Y4E0XOab1Hn31i90dbk9kfaf89T.jpg', 0, 6, '2026-04-12 20:47:17', '2026-05-29 22:37:14'),
(203, 75, 'puntos/KprLk9z0o7x26dCJbRCFsKCNne3SHifq6BN1NjTj.jpg', 0, 2, '2026-04-12 20:53:57', '2026-05-22 03:17:37'),
(204, 75, 'puntos/9eaMEZSaItfqEEnrNhlEDt5sf5Kl8EFEU3HtAzkT.jpg', 0, 3, '2026-04-12 20:53:57', '2026-05-22 03:17:37'),
(205, 75, 'puntos/avknt1n8DgSfgIvfvopLI3WtrE4boR3KLh6IVdVi.jpg', 0, 4, '2026-04-12 20:53:57', '2026-05-22 03:17:37'),
(206, 75, 'puntos/8CT7rgeCYJdqGiFhn4jEO8JW0eWPhmVLmGZxGgzY.jpg', 0, 6, '2026-04-12 20:53:57', '2026-05-22 03:17:37'),
(207, 75, 'puntos/1FcwHBrIVRDQaiCBDRyiiPR1rtgYQm0YALne1Ls9.jpg', 0, 7, '2026-04-12 20:53:57', '2026-05-22 03:17:37'),
(208, 75, 'puntos/d85Btx4B9fim4ExyWTrnaDBcvISSjkB3ZaRGp4Kp.jpg', 1, 0, '2026-04-12 20:53:57', '2026-05-22 03:17:37'),
(209, 75, 'puntos/WZ2lJzSiZMZbdGvILsb95L7X32FNWVqpxoSBsQt3.jpg', 0, 5, '2026-04-12 20:53:57', '2026-05-22 03:17:37'),
(210, 75, 'puntos/dXF8PubJminlrIwdnVf7Vgn31tGgyBQHpMznNQKQ.jpg', 0, 1, '2026-04-12 20:53:57', '2026-05-22 03:17:37'),
(211, 20, 'puntos/fVhUj6ZUkbQbDJNVuN3Eh25ALTPs9jWlQtGFqBxt.jpg', 1, 1, '2026-04-12 20:55:59', '2026-04-12 20:55:59'),
(212, 20, 'puntos/CiTJftifGdY6SSFbpghtUoNYFg1yf2fewbxF3WV1.jpg', 0, 2, '2026-04-12 20:55:59', '2026-04-12 20:55:59'),
(213, 73, 'puntos/oJjkGki3FPNvGAFlMpuSDnqn97dXOH9bvtQEuFmO.jpg', 0, 3, '2026-04-13 17:09:59', '2026-04-20 05:21:10'),
(214, 76, 'puntos/M7RSULkvVmCHXJWIehVr22GhQM8fMa6AXUAuGETE.jpg', 1, 0, '2026-04-14 00:06:41', '2026-05-08 16:58:42'),
(215, 76, 'puntos/iKVU6iRxTtxmEdpyui9NcnTc3IG5Y2JKEXwUROL7.jpg', 0, 1, '2026-04-14 00:06:41', '2026-05-08 16:58:42'),
(216, 76, 'puntos/W7VZEDAla2bSiC9ZQvSNzEp60fk5bNIK8XGwpmZx.jpg', 0, 2, '2026-04-14 00:06:41', '2026-05-08 16:58:42'),
(217, 76, 'puntos/Esj2srli0qszGCTIEfseh6NfGd3ogoI3wJc8UpeC.jpg', 0, 3, '2026-04-14 00:06:41', '2026-05-08 16:58:42'),
(218, 76, 'puntos/1o0izNtMBtkrALMuy0MGo3xa9O2fXhD04ItiyzGD.jpg', 0, 4, '2026-04-14 00:06:41', '2026-05-08 16:58:42'),
(219, 76, 'puntos/VNuNb7WNIJFnGxOtW6n1k8lCXbtrtKYvh2JSSigD.jpg', 0, 5, '2026-04-14 00:06:41', '2026-05-08 16:58:43'),
(220, 76, 'puntos/CP2ZduOZcWF36ZfcBiyi31YAPlRhnLrsNgpGqJYj.jpg', 0, 6, '2026-04-14 00:06:41', '2026-05-08 16:58:43'),
(221, 77, 'puntos/pclntSYgRYPjyofyUvhxQvThYJtPZGDfRSl7JZTm.jpg', 1, 0, '2026-04-14 02:34:09', '2026-04-14 02:45:58'),
(222, 77, 'puntos/IdbRYSuQzzcbPxdmsYap9QkzgNHxzkgUXDzI8AgW.jpg', 0, 1, '2026-04-14 02:34:09', '2026-04-14 02:45:58'),
(223, 77, 'puntos/4QtgaPUKVdIwORGxVn8zfe3Ovl5EB4ZMywH6fgJf.jpg', 0, 2, '2026-04-14 02:34:09', '2026-04-14 02:45:58'),
(224, 77, 'puntos/CzZHTh9hLQO4W55Ke4TF4uLIJMJiVDjytJT288wg.jpg', 0, 3, '2026-04-14 02:34:09', '2026-04-14 02:45:58'),
(225, 77, 'puntos/EQEPOKQHz6z45uYIPPnGcfxMWV8uWmK8q9eSprAc.jpg', 0, 4, '2026-04-14 02:34:09', '2026-04-14 02:45:58'),
(226, 78, 'puntos/n73doH5wg5C4VwaXk7mcZhQgz7LRJ1SSUr5AV3Me.jpg', 1, 0, '2026-04-14 02:41:20', '2026-05-08 16:51:30'),
(227, 78, 'puntos/6ASNBGgeNXwrHgD1NlYyvlQYnotZMZTxtzkLD8AF.jpg', 0, 1, '2026-04-14 02:41:20', '2026-05-08 16:51:30'),
(228, 78, 'puntos/N8yuSImAqraLMS8q5hg3zO5wr4vfSZPLIzIaVVgi.jpg', 0, 2, '2026-04-14 02:41:20', '2026-05-08 16:51:30'),
(229, 79, 'puntos/qzhwP1TmsRSRoK5pN8WuNi9sDRLK7GYIro8OQGKs.jpg', 0, 0, '2026-04-14 02:50:49', '2026-05-08 16:50:59'),
(230, 79, 'puntos/jhLuGKZN7wqtHUSeL7EymMJMip5YW05NHFKVdIMN.jpg', 0, 1, '2026-04-14 02:50:49', '2026-05-08 16:50:59'),
(231, 79, 'puntos/qtmIizoJwqdBvesfvVDSI3nWJN2DQdZwyS3G08bV.jpg', 1, 2, '2026-04-14 02:50:49', '2026-05-08 16:50:59'),
(232, 44, 'puntos/9gWfXK66vNHUdbVpp4CFAS9ag6aOSCiBWuEOZ8MH.jpg', 0, 1, '2026-04-14 02:52:23', '2026-04-14 02:52:23'),
(233, 80, 'puntos/6w0p5POEpyv8sN2yPQztfdT1fT9kYUUwT1JxDD5m.jpg', 1, 0, '2026-04-14 03:16:44', '2026-04-14 03:36:14'),
(234, 80, 'puntos/IHq17oeD3C0Xld8hSTPddOBzKs1QMu7V9B3oBya2.jpg', 0, 1, '2026-04-14 03:16:44', '2026-04-14 03:36:14'),
(235, 80, 'puntos/AYDsPAFMgwp8pN9xevCdjAyxmrWDYj2o6CIReqFi.png', 0, 2, '2026-04-14 03:23:57', '2026-04-14 03:36:14'),
(236, 80, 'puntos/lhoWxaB1jURastYEVxNc2klVCAJhoajLwkzWZLzP.png', 0, 3, '2026-04-14 03:28:04', '2026-04-14 03:36:14'),
(237, 80, 'puntos/f21MoaKNpchq9jaO9LqsIn45PEllpmtD3lWrT68q.png', 0, 4, '2026-04-14 03:36:14', '2026-04-14 03:36:14'),
(238, 81, 'puntos/VKrsy57Y8iZVK4wC68uycmc0CvTsYQcYnSjGSjZ0.jpg', 0, 0, '2026-04-14 06:33:30', '2026-04-14 19:56:04'),
(239, 81, 'puntos/4khlKNpmPMQOyL4aQsOFAJSZpujvnLuO8rPB7xeG.jpg', 0, 1, '2026-04-14 06:33:30', '2026-04-14 19:56:04'),
(240, 81, 'puntos/5MDWHhCZY6eCRSgRcJuR1tMYnMWIuvYRNi70O4DY.jpg', 0, 2, '2026-04-14 06:33:30', '2026-04-14 19:56:04'),
(241, 81, 'puntos/DAIdtEw39xJudk6A2Rg8JTsmRutAB7BnPK7Xorjb.jpg', 0, 3, '2026-04-14 06:33:30', '2026-04-14 19:56:04'),
(242, 81, 'puntos/KH6MEw9w3TeKNJYVB40uQLWA4OokRH7GTM48mXzt.jpg', 1, 4, '2026-04-14 06:33:30', '2026-04-14 19:56:04'),
(243, 82, 'puntos/JEodmLv5VP2Yth4LW1znPG0aaBAa88POjUFSSG3b.jpg', 1, 0, '2026-04-15 02:18:36', '2026-05-08 17:00:35'),
(244, 82, 'puntos/dLooFypcDcdIJ1d0t7QFPp9nWeXFdQi8E0keaU2i.jpg', 0, 1, '2026-04-15 02:18:36', '2026-05-08 17:00:35'),
(245, 82, 'puntos/3ob3lGAGlKAKYiVHnIxOQbOo0SsEH7uZ3RyW5gML.jpg', 0, 2, '2026-04-15 02:18:36', '2026-05-08 17:00:35'),
(246, 82, 'puntos/zzjmj0Tf9lSSJqDcE5vUQ3D86shaRGKwOVImG9zk.jpg', 0, 3, '2026-04-15 02:18:36', '2026-05-08 17:00:35'),
(247, 82, 'puntos/F5K8lTgdbguEelfVxYoP9mTykVPPJYmhxf0gmtnC.jpg', 0, 4, '2026-04-15 02:18:36', '2026-05-08 17:00:35'),
(248, 82, 'puntos/7VbXIstCZEWWlfCUrVCRNkFDF0ff6C0Q8MRrKf4t.jpg', 0, 5, '2026-04-15 02:18:36', '2026-05-08 17:00:35'),
(249, 82, 'puntos/5U3EZxjPR6X5b5z6fALUM19O0PA8Ij7zCH4xDFiy.jpg', 0, 6, '2026-04-15 02:18:36', '2026-05-08 17:00:35'),
(250, 83, 'puntos/V7Zqin5jA1QLn9vMvFrObfnmAkNMNAoeA3H03ySZ.jpg', 0, 0, '2026-04-16 05:43:33', '2026-06-09 14:33:11'),
(251, 83, 'puntos/HYzil9TYVvnJHpiGb1KvQoSFhOC5xWv77L3to1Wq.jpg', 1, 1, '2026-04-16 05:43:33', '2026-06-09 14:33:11'),
(252, 83, 'puntos/UeIzG8krFUO9c97L2kSmp09eZfRpofUVQeBEsmqE.jpg', 0, 2, '2026-04-16 05:43:33', '2026-06-09 14:33:11'),
(253, 67, 'puntos/EJRwukZvANNByr0X8m2ZEhAOmPYMOrLOopi4UZul.jpg', 0, 2, '2026-04-18 03:05:45', '2026-04-18 03:05:45'),
(254, 57, 'puntos/x35QQ1oBhYZylw5OgSg8Ng8HNMdztMwYh5h83eCO.jpg', 0, 1, '2026-04-18 03:08:15', '2026-05-29 22:37:38'),
(255, 57, 'puntos/bb2MT7CIXaoN3lKVNXcQGiOydlT4G7c6MUeosrXI.jpg', 0, 2, '2026-04-18 03:09:07', '2026-05-29 22:37:38'),
(256, 84, 'puntos/l6NZVQoRMvVS98hJBJFNBea2N0aTn6BVXMWFukpS.jpg', 1, 0, '2026-04-18 04:06:05', '2026-05-08 16:50:02'),
(257, 84, 'puntos/36WQHUTHb5Yvq6u8fQ8Rp66vE5f0xX7cbVsIR7Fm.jpg', 0, 1, '2026-04-18 04:06:05', '2026-05-08 16:50:02'),
(258, 84, 'puntos/8Ck3LgtBeX6yUbdEKwTsTnH0wwYeffHW4vTxvttq.jpg', 0, 2, '2026-04-18 04:06:05', '2026-05-08 16:50:02'),
(259, 84, 'puntos/eJkZMAxT1GLcDVQaQGWawbRpTAaELRo3tAMKNqT3.jpg', 0, 3, '2026-04-18 04:06:05', '2026-05-08 16:50:02'),
(260, 84, 'puntos/qVVVX5Ts2akFdbwBZOjmP7ZMtut0bxgrfJlGIy8o.jpg', 0, 4, '2026-04-18 04:06:05', '2026-05-08 16:50:02'),
(261, 84, 'puntos/KU21eU9EMoXkP8L5naTObF2g9pMmOxemoiT5iE5S.jpg', 0, 5, '2026-04-18 04:06:05', '2026-05-08 16:50:02'),
(262, 85, 'puntos/nGRg6g0ojWut6QEYQJSWhovtABt0VH4V1NrFyfbx.jpg', 1, 0, '2026-04-20 03:37:30', '2026-05-08 16:49:32'),
(263, 85, 'puntos/84Seel7CIvYINgwjiAYm8FkAYU7VumaUISq7S8MX.jpg', 0, 1, '2026-04-20 03:37:30', '2026-05-08 16:49:32'),
(264, 85, 'puntos/6Nh4koeBGWu9xBPEow3HMdZEcWAKCT3gBkoU6zsQ.jpg', 0, 2, '2026-04-20 03:37:30', '2026-05-08 16:49:32'),
(265, 85, 'puntos/ykwJtZ1aiwbzyRiB1isNBKYLBSjYCmgbt6XcW7HS.jpg', 0, 3, '2026-04-20 03:37:30', '2026-05-08 16:49:32'),
(266, 85, 'puntos/BDh8oUuCXPhGflGgjPzac9NRDXP7cpLeC0IEMwTl.jpg', 0, 4, '2026-04-20 03:37:30', '2026-05-08 16:49:32'),
(267, 85, 'puntos/tZLhge4aqP36GVZd7ih6rKR8RQuujsUNYQCFk8V3.jpg', 0, 5, '2026-04-20 03:37:31', '2026-05-08 16:49:32'),
(268, 85, 'puntos/SsxoQHwvgaxQ5kjZFLzvCIWeksHzsUnzla7CNPOQ.jpg', 0, 6, '2026-04-20 03:37:31', '2026-05-08 16:49:32'),
(269, 85, 'puntos/KqajurPvuiYfdKS31lY2tnxFZjc0jMmx0DL9WKRG.jpg', 0, 7, '2026-04-20 03:37:31', '2026-05-08 16:49:32'),
(270, 85, 'puntos/faecMDUrSGdLHzFTPIg7lzAcoGfH39DycIff18Cy.jpg', 0, 8, '2026-04-20 03:37:31', '2026-05-08 16:49:32'),
(271, 85, 'puntos/aSxbKYsFJP4WXlddo01nlJZnY8cxzcjVCPkszJCX.jpg', 0, 9, '2026-04-20 03:37:31', '2026-05-08 16:49:32'),
(272, 85, 'puntos/KE9rxOfneYq0lLKRFL3RMPdElJyRpqCn6zV7w9Mu.jpg', 0, 10, '2026-04-20 03:37:31', '2026-05-08 16:49:32'),
(273, 47, 'puntos/arYj7BI8wRbp6AhISCYNJaS1VuTrc0iTuzy82Yu1.jpg', 0, 1, '2026-04-20 04:57:53', '2026-05-08 17:15:27'),
(274, 74, 'puntos/REgb0t7s1jBf372VuMPCyB9WFELukH25HXmc93xQ.jpg', 0, 3, '2026-04-20 05:02:31', '2026-05-02 20:43:10'),
(275, 74, 'puntos/KwyIjtSyalDs4AwYj7a0p0hJ3oj6A0jIIMnrFZiF.jpg', 0, 4, '2026-04-20 05:02:31', '2026-05-02 20:43:10'),
(276, 74, 'puntos/LPq2cQLxjdUwXJnC8GRGEZDlM1FTMqDeTiC3ayu0.jpg', 0, 5, '2026-04-20 05:02:31', '2026-05-02 20:43:10'),
(277, 74, 'puntos/xdpqv3dZv0fiZCBjJsj96uHtA5xIeoVV7XbgHngp.jpg', 0, 6, '2026-04-20 05:02:31', '2026-05-02 20:43:10'),
(278, 74, 'puntos/oByUbkhaKefWkm2BOK6bUkK9i4zsN8wxxHrhVQxM.jpg', 0, 7, '2026-04-20 05:02:31', '2026-05-02 20:43:10'),
(279, 74, 'puntos/a9pwPcCAKRio0w9p0eCVdyMi547H08BXjtWgzkLK.jpg', 0, 8, '2026-04-20 05:02:32', '2026-05-02 20:43:10'),
(280, 9, 'puntos/w7XWNajpQBK0K3jymPnpOo8BUuUnK19b9HCjC5j1.jpg', 0, 2, '2026-04-20 05:10:41', '2026-05-22 03:12:24'),
(281, 21, 'puntos/wFqnvhlTd38pHUcOORlW9N9r3XKXhPoKX395jqy0.jpg', 0, 1, '2026-04-20 05:13:51', '2026-04-20 05:13:51'),
(282, 21, 'puntos/67kENQMp2dnbmslBhSkjJ6APf8Ib31zjtrcSFAY3.jpg', 0, 2, '2026-04-20 05:13:51', '2026-04-20 05:13:51'),
(283, 68, 'puntos/Ml1eZZDatwuvNlRfkWX4PONq5HZvgMizmt4RPwlp.jpg', 0, 4, '2026-04-20 05:16:16', '2026-04-30 01:41:04'),
(284, 73, 'puntos/RYdve4vKzmLwZ4J6fM7zRKZCh8YI2maBInAp20FK.jpg', 0, 4, '2026-04-20 05:17:50', '2026-04-20 05:21:10'),
(285, 71, 'puntos/Bza209RHu2E7kLWYVy3uwo436lhoKDlwadK8vbqB.jpg', 0, 3, '2026-04-20 05:22:14', '2026-05-08 17:00:50'),
(286, 71, 'puntos/jrRJKCVM86q6h9y15VsLvg2gtOxU0iLcatO6iKF8.jpg', 0, 4, '2026-04-20 05:22:14', '2026-05-08 17:00:50'),
(287, 72, 'puntos/xJ0SoeKiIlMsnNi38opMRRi9b9drVBkAwTJOzOlm.jpg', 0, 1, '2026-04-20 05:24:48', '2026-06-09 14:34:48'),
(288, 35, 'puntos/p82pk6uApUNUIXWMGAhc2icsF09bGcotWehPywr1.jpg', 0, 4, '2026-04-20 05:26:23', '2026-05-09 04:55:46'),
(289, 35, 'puntos/dPcxsrlvAFFfSTSLcoIX923AsyZnjcVNCWcWBgh3.jpg', 0, 5, '2026-04-20 05:26:23', '2026-05-09 04:55:46'),
(290, 35, 'puntos/egOJzbWqbpbeV3oLX3aQeAdbaQBPr1dZzG1DoCNR.jpg', 0, 6, '2026-04-20 05:26:23', '2026-05-09 04:55:46'),
(291, 9, 'puntos/1oWzq0964WRefuu7eEwvvAAxl4ruKoHqz9ETSm4d.jpg', 1, 0, '2026-04-20 05:36:19', '2026-05-22 03:12:24'),
(292, 86, 'puntos/9lxvprgsfbmT4eXpKwelwjj1yn7l4qTCAuCD9Jd6.jpg', 0, 2, '2026-04-20 05:46:27', '2026-05-22 03:13:19'),
(293, 86, 'puntos/u8SjMDbSaWpyg5ZxEp2uX81wXX3iREoTHShKaTR7.jpg', 0, 3, '2026-04-20 05:46:27', '2026-05-22 03:13:19'),
(294, 86, 'puntos/QwJjAiEyrln7zd8VOslI5wu7wGhjUakZGJ1eGMeR.jpg', 0, 4, '2026-04-20 05:46:27', '2026-05-22 03:13:19'),
(295, 86, 'puntos/Azcm52euZlCmzNB3nLE7on2pEgWjnSmwmpJNZmtW.jpg', 0, 1, '2026-04-20 05:46:27', '2026-05-22 03:13:19'),
(296, 87, 'puntos/xoULseictDCcWwWctzK1hXoZrSJHLQiqgqAp3Za8.jpg', 1, 0, '2026-04-21 18:47:04', '2026-04-21 18:47:04'),
(297, 87, 'puntos/7B8UKxVWk4n3AhXnxwlsbFTMyoKbXCd1DuNungIu.jpg', 0, 1, '2026-04-21 18:47:05', '2026-04-21 18:47:05'),
(298, 87, 'puntos/YbPIDbBUnYHTYW7cpC6ObKAm3bcJVYNYMZLQ8Kbl.jpg', 0, 2, '2026-04-21 18:47:05', '2026-04-21 18:47:05'),
(299, 87, 'puntos/vnWFhM0WXhjLisLG4Mc7pwG9s2g4kYTEKbNuD6Hg.jpg', 0, 3, '2026-04-21 18:47:06', '2026-04-21 18:47:06'),
(300, 87, 'puntos/ejxjvhMB5ao4sEf8z19Lcnlk4DwaSgRuHIi24AK5.jpg', 0, 4, '2026-04-21 18:47:07', '2026-04-21 18:47:07'),
(301, 88, 'puntos/6qk0HfLliooIbsvlrNUPpS8ZqfGkNnxDGDBSKta0.jpg', 1, 0, '2026-04-24 05:50:20', '2026-05-08 16:48:58'),
(302, 88, 'puntos/XTNrTwbpuESAdLnRtZb9rMoZZqXytuiyYLw5fm2d.jpg', 0, 1, '2026-04-24 05:50:20', '2026-05-08 16:48:58'),
(303, 89, 'puntos/4oWN5Wco0bLJfr5IEaGV9NTFuaYBsXLXl4xbUOwy.jpg', 1, 0, '2026-04-24 05:56:39', '2026-04-24 05:56:39'),
(304, 89, 'puntos/o2g4xNJ6V6gBT3xmZxulcvaMsqgXTEIPOUjLE9pE.jpg', 0, 1, '2026-04-24 05:56:39', '2026-04-24 05:56:39'),
(305, 90, 'puntos/rzjUdu9CHTlf3LUS7APe3NgmpmheqHRVaTc1NIpJ.jpg', 1, 0, '2026-04-24 06:04:54', '2026-04-24 06:04:54'),
(306, 90, 'puntos/TySZ9wprsMadn1ZRIdcpx5nOfBVt5fGmsTNj041k.jpg', 0, 1, '2026-04-24 06:04:54', '2026-04-24 06:04:54'),
(307, 91, 'puntos/7TjFDfpRkuecwFcNCfa1K8CIGFVZ6qff6wdEbPor.jpg', 1, 0, '2026-04-25 00:37:53', '2026-06-09 14:33:03'),
(308, 91, 'puntos/cGVHwd9KqQh4dUzjE9IXcwIqiBHxzjzOWmDEFK6J.jpg', 0, 1, '2026-04-25 00:37:54', '2026-06-09 14:33:03'),
(309, 92, 'puntos/VaFTjd5upgxzOEz7jgH3yMVmmMvYoW01Be7Ezm0M.jpg', 1, 0, '2026-04-25 00:45:52', '2026-04-25 00:45:52'),
(310, 92, 'puntos/BPi3qpdOP3q5I6uk4bwAIYnH7CcltAxNZDRTWWCA.jpg', 0, 1, '2026-04-25 00:45:52', '2026-04-25 00:45:52'),
(311, 93, 'puntos/IqAyOuURqJnxTFleNtUnH5VsQ65n2yIcOSp0KSr0.jpg', 1, 0, '2026-04-25 00:55:33', '2026-05-08 16:45:04'),
(312, 93, 'puntos/cdufUZmps6rbFIDsIj7kxDlFdiubreNg3LOfCoNH.jpg', 0, 1, '2026-04-25 00:55:33', '2026-05-08 16:45:04'),
(313, 94, 'puntos/p5VLY1J6akgUHCxoYrBWqtxZMPmbYSJOX2YbIq3B.jpg', 1, 0, '2026-04-25 01:02:19', '2026-04-25 01:02:19'),
(314, 94, 'puntos/pxFWhHGCLje3oGiVvP6qzL5kRybTRr6n4njuIbdy.jpg', 0, 1, '2026-04-25 01:02:19', '2026-04-25 01:02:19'),
(315, 68, 'puntos/h31ELSKntGKRi7vvFhw3VLmjKYHRhzmY7swjqs2Z.jpg', 0, 5, '2026-04-30 01:41:04', '2026-04-30 01:41:04'),
(323, 74, 'puntos/hD3U16whFUhfDyQPMSMGYCtYhhhdzz6qTx9Rq6Iu.jpg', 0, 9, '2026-04-30 01:45:33', '2026-05-02 20:43:10'),
(324, 74, 'puntos/bBEN8lHobiVBVAImvazGPk2mkb9MnVGeKdQz0afq.jpg', 0, 10, '2026-04-30 01:45:34', '2026-05-02 20:43:10'),
(325, 74, 'puntos/OVmXN5RkpeJU4ASPyTutxluua9YsqJV6PnIG8dkf.jpg', 0, 11, '2026-04-30 01:45:34', '2026-05-02 20:43:10'),
(326, 74, 'puntos/IEwQgWeGpyahGvV9B7oRCQYbe6mYORn8KJ4bHt6F.jpg', 0, 12, '2026-04-30 01:45:34', '2026-05-02 20:43:10'),
(327, 74, 'puntos/j0GgYPUomehTs9inh6LgtXOGhkdYnvgroTScmfkr.jpg', 1, 13, '2026-04-30 01:46:10', '2026-05-02 20:43:10'),
(328, 74, 'puntos/Bh83VM18U9vzNbwKBBJ8IGGb7pjQwLWiDPQV4nWU.jpg', 0, 14, '2026-04-30 01:46:10', '2026-05-02 20:43:10'),
(329, 74, 'puntos/89phcaGQWCD5fhfwQVyDGwRRnG69dRJOqxfcA90c.jpg', 0, 15, '2026-04-30 01:46:11', '2026-05-02 20:43:10'),
(330, 74, 'puntos/A3Hs0ZRUsnQFa02fXianede6t5Je6FCYnT3ELXhe.jpg', 0, 16, '2026-04-30 01:46:11', '2026-05-02 20:43:10'),
(339, 62, 'puntos/JAnLMVJB5tVw1RWEHd8q0H1KARZGYaDSIqF7DqN3.jpg', 0, 5, '2026-05-08 17:10:11', '2026-05-21 04:04:01'),
(340, 51, 'puntos/Zk6SqSMg4KOBb25XaNE799x3FethOqqSkGiAzYTw.jpg', 0, 1, '2026-05-08 17:14:39', '2026-05-08 17:14:39'),
(341, 103, 'puntos/I3CnV27VdLugAk7MlyZRcO6OiFfWLfoPDhPbQNZM.jpg', 1, 0, '2026-05-08 17:25:47', '2026-06-12 20:06:26'),
(342, 103, 'puntos/YckchyAunrSJh8PTuWupWlrFio8jnPn5rrCqQxjR.jpg', 0, 1, '2026-05-08 17:25:47', '2026-06-12 20:06:26'),
(343, 104, 'puntos/R2hgmnl1198cu4Ag8U2KcIUDaE5us8eY9gGLiJlA.jpg', 0, 0, '2026-05-08 17:33:20', '2026-06-09 14:32:58'),
(344, 104, 'puntos/nOAk0eXzU38W8bRUOlrect8Knq3IcGmMRCWJzAbZ.jpg', 0, 1, '2026-05-08 17:33:20', '2026-06-09 14:32:58'),
(345, 104, 'puntos/HuFSMLEILPG1O0wBhs2oAI2ndbd8uwNyUYbXWlII.jpg', 0, 2, '2026-05-08 17:33:20', '2026-06-09 14:32:58'),
(346, 104, 'puntos/BQFP9GikpWpPjjdglLdkCs3G94jLtvAxdoVorJgm.jpg', 1, 3, '2026-05-08 17:33:20', '2026-06-09 14:32:58'),
(347, 104, 'puntos/OzNuzomcDfrRxx6wA9cVk9OOmi7W38RKCz7yOIN0.jpg', 0, 4, '2026-05-08 17:33:20', '2026-06-09 14:32:58'),
(348, 105, 'puntos/f0jfDoYXMPCzAPaIWxcDjZCO7912A4Zma0jmxBQX.jpg', 1, 0, '2026-05-09 21:04:27', '2026-05-10 01:12:22'),
(349, 105, 'puntos/mE6eRvSzNnlR10KVfLTBi5D8wvxKrVyj2MNL8SU5.jpg', 0, 1, '2026-05-09 21:04:27', '2026-05-10 01:12:22'),
(350, 105, 'puntos/DF39Z4ymSiWnxOqnUO4n4KemVEEG6ZFzGQlQR1cY.jpg', 0, 2, '2026-05-09 21:04:28', '2026-05-10 01:12:22'),
(351, 106, 'puntos/0MFlOREmgGMRvpPhhcJ0GTEJuyyVzCRlfqZakyeI.jpg', 1, 0, '2026-05-11 01:15:01', '2026-05-11 01:15:01'),
(352, 106, 'puntos/vkVfmlMDPk12dHGQsBifUZEUE1ZoKJam98IvwwgN.jpg', 0, 1, '2026-05-11 01:15:01', '2026-05-11 01:15:01'),
(353, 66, 'puntos/MIhY9M4NFB1IdXhxgE0DflBJPVcPF0cBCOmT3u9u.jpg', 0, 7, '2026-05-12 03:04:34', '2026-05-12 03:04:34'),
(354, 66, 'puntos/4390wzIkchXrLeVEageECP7egI8v7AWS3j5f5uvv.jpg', 0, 8, '2026-05-12 03:04:36', '2026-05-12 03:04:36'),
(355, 66, 'puntos/zzy2rWjzkrju8w23Uacc7WN8fPt7qpKOQM2bBXM0.jpg', 0, 9, '2026-05-12 03:04:39', '2026-05-12 03:04:39'),
(356, 107, 'puntos/e7TUm3moaQy1XnUuYNnOhorO1JlOG1PNbUfhz1XU.jpg', 0, 1, '2026-05-14 23:19:58', '2026-05-24 16:59:51'),
(357, 107, 'puntos/8KJevZ5ib4dPn1p99hB5ElhLn8dpvTmzfMTlRaaR.jpg', 0, 2, '2026-05-14 23:19:58', '2026-05-24 16:59:51'),
(358, 108, 'puntos/qF2aBgjuU3OyrcXalYLIoJfRDhE3Fbqc4rBZsvEd.jpg', 0, 0, '2026-05-14 23:28:37', '2026-06-09 14:35:33'),
(359, 108, 'puntos/fSclVmQzDi8pSqNjkZMrntZGiN4GLEFHCt3jo6gz.jpg', 0, 1, '2026-05-14 23:28:37', '2026-06-09 14:35:33'),
(360, 108, 'puntos/P1hb0cKURDNeglhVoIl3mAQzPyRnQCOwcBxG2dxG.jpg', 0, 2, '2026-05-14 23:28:37', '2026-06-09 14:35:33'),
(361, 108, 'puntos/8lV14TjNjJzWfTk7GG5yx8OlRdgmTsngcLJwNmk6.jpg', 0, 3, '2026-05-14 23:28:37', '2026-06-09 14:35:33'),
(362, 108, 'puntos/LjSJCYu8Qin8d18B3RUnsnkiauXQJin8pi9ZhIIQ.jpg', 0, 4, '2026-05-14 23:28:37', '2026-06-09 14:35:33'),
(363, 108, 'puntos/Mbz8LkR7rIZVgJj0TCJfAotl2XbJ0yvVMhihOCfl.jpg', 0, 5, '2026-05-14 23:31:06', '2026-06-09 14:35:33'),
(364, 108, 'puntos/cI0GsJ7xGZUQcGXeYihCVZShuLmdrVMDfirCRrdP.jpg', 1, 6, '2026-05-14 23:31:06', '2026-06-09 14:35:33'),
(365, 1, 'puntos/Ig94Zn3emMUkk2AkewJL2sEIKxK3HxZVBl7bCRok.jpg', 1, 0, '2026-05-14 23:35:38', '2026-05-14 23:35:59'),
(366, 55, 'puntos/O5fMIi8rCgmOm7fy64aIsFil8v1lSlmoOshnMc7u.jpg', 0, 1, '2026-05-14 23:40:13', '2026-05-14 23:40:13'),
(367, 55, 'puntos/zFnQJy88rpLnbvMTKxmrm4oCWSLV7pCwQXfWeH8x.jpg', 0, 2, '2026-05-14 23:40:13', '2026-05-14 23:40:13'),
(368, 55, 'puntos/hHcSQHiDymagciNniUzcXtriUBe1uCIwGAcrdqWq.jpg', 0, 3, '2026-05-14 23:40:13', '2026-05-14 23:40:13'),
(369, 55, 'puntos/2xXM22svNZWc2TgwIT3vx4uRddnNLUa2KYu83tkl.jpg', 0, 7, '2026-05-14 23:40:13', '2026-05-14 23:40:13'),
(370, 55, 'puntos/YBB0Jjvca8F2ZSBAxbBneV7s4HVLc8nQEl5NCADJ.jpg', 0, 8, '2026-05-14 23:40:13', '2026-05-14 23:40:13'),
(371, 55, 'puntos/hPkn8jliZlmjPumYKxzu16NCXhF9BNfaZBERs3ku.jpg', 0, 9, '2026-05-14 23:40:13', '2026-05-14 23:40:13'),
(373, 86, 'puntos/D8gvlLUmOtl2EiG1ysNBqVpiNdeF1wiEXrytW3fM.jpg', 1, 0, '2026-05-21 03:35:04', '2026-05-22 03:13:19'),
(374, 30, 'puntos/1OYdIKrJ10lbdLwX8aHpHGyNuhou9JtuUhkvw3Cx.jpg', 1, 2, '2026-05-21 03:42:21', '2026-05-21 03:44:32'),
(375, 30, 'puntos/cAlfbqB5bXzdZDNkRE6tajGN8hcMlAWUMaAA8RNh.jpg', 0, 0, '2026-05-21 03:44:32', '2026-05-21 03:44:32'),
(376, 34, 'puntos/gLrmeLvB6rBYWWIKedLepzkj8ERTqO5PVXuoPldD.jpg', 0, 3, '2026-05-21 03:47:23', '2026-05-29 22:37:45'),
(377, 62, 'puntos/P8OHHdwol4ELpES66KPjfed6xDL7bXDZi4XAHJHa.jpg', 1, 0, '2026-05-21 03:50:00', '2026-05-21 04:04:01'),
(378, 3, 'puntos/GnDPy4SOYkfWWCLRClILuLhS5083JI6xozg43qU2.jpg', 0, 2, '2026-05-22 04:27:55', '2026-05-22 04:32:21'),
(379, 3, 'puntos/LW8RIue86NMFWl0zF0nuRw0TBtYjd6279Qa5faAf.jpg', 0, 3, '2026-05-22 04:27:55', '2026-05-22 04:32:21'),
(380, 3, 'puntos/BtfmWIex5e8Zf12lIJ0OSEwAryxowEMOpzhu0vRh.jpg', 0, 4, '2026-05-22 04:27:55', '2026-05-22 04:32:21'),
(381, 3, 'puntos/Nc9Yb4gnrc5Fcouw4nxt77bxK7YIjeyuBfwZs0XP.jpg', 0, 5, '2026-05-22 04:27:55', '2026-05-22 04:32:21'),
(382, 3, 'puntos/VI5eNH1IWFqqfquBNyKzLSLr51DqekojKRiJKcil.jpg', 0, 6, '2026-05-22 04:27:55', '2026-05-22 04:32:21'),
(383, 3, 'puntos/xUiQscSknGZPpR1653JIu6iIvqzbOR3CCJ2r4KMO.jpg', 0, 1, '2026-05-22 04:32:21', '2026-05-22 04:32:21'),
(386, 110, 'puntos/blVoeb3HGug4PpOjj08u68inVqXWFCwUROZuS3Ab.jpg', 1, 0, '2026-05-23 00:22:59', '2026-05-23 00:24:42'),
(387, 110, 'puntos/4mWSuh0fV8aAH7RvJgwX37nrdEOyx6T4GdbR2Tbr.jpg', 0, 1, '2026-05-23 00:22:59', '2026-05-23 00:24:42'),
(388, 110, 'puntos/CsdZHp4t4Y0LLONqRyiMGmTadgJJyteON74OJioj.jpg', 0, 2, '2026-05-23 00:22:59', '2026-05-23 00:24:42'),
(389, 110, 'puntos/0PBfSBkwosvgO2h8KWndCY8UosRkKpRHBYP1ySp5.jpg', 0, 3, '2026-05-23 00:22:59', '2026-05-23 00:24:42'),
(390, 111, 'puntos/lqH7pUxkhXKzwzZRJI5P1YR1JodKd399NH1YsjPt.jpg', 0, 0, '2026-05-23 17:35:40', '2026-06-09 14:32:27'),
(391, 111, 'puntos/JnPo77xANUJEqCccyetPozvS9ndcENJhTmPrUV1w.jpg', 0, 1, '2026-05-23 17:35:40', '2026-06-09 14:32:27'),
(392, 111, 'puntos/8RgVM0TuzANjZdhhTUnVhCml6wNgyUb2IapRLmlj.jpg', 0, 2, '2026-05-23 17:35:40', '2026-06-09 14:32:27'),
(393, 111, 'puntos/P8SCD47C600uVeeUFt5FPZbIJviZodowLhQ3vKia.jpg', 0, 3, '2026-05-23 17:35:40', '2026-06-09 14:32:27'),
(394, 111, 'puntos/zi7Y9UAIbNm4Pndp5mSuLRNsbMzjY3oCDESFgUc6.jpg', 0, 4, '2026-05-23 17:35:40', '2026-06-09 14:32:27'),
(395, 111, 'puntos/Z6K3YzH2KJzfo4At6VgHqD5gi1RLhPcnsTWfQXr9.jpg', 0, 5, '2026-05-23 17:35:40', '2026-06-09 14:32:27'),
(396, 60, 'puntos/pzDXWqPAgZVkKUozJj8UmJCHfnSEWA3XUhav6kAH.jpg', 0, 5, '2026-05-23 17:39:54', '2026-05-23 17:40:33'),
(397, 112, 'puntos/pJGs8SMMDUNTjuh41IG4wPp8miCZicDHN4BGR1SC.jpg', 1, 0, '2026-05-24 15:56:24', '2026-05-24 15:56:24'),
(398, 112, 'puntos/4ksRaNM14XpQDgy5UYTOWvZa4WZRmfIA3EohhUIe.jpg', 0, 1, '2026-05-24 15:56:24', '2026-05-24 15:56:24'),
(399, 112, 'puntos/GFCfG1aprOf89yjoqOHgvKi2KHRxmvpPA6sXYhCH.jpg', 0, 2, '2026-05-24 15:56:24', '2026-05-24 15:56:24'),
(400, 112, 'puntos/E6uAdvPOOo80ukRUzl3wU53eRaYtAkUpYvL5j7bf.jpg', 0, 3, '2026-05-24 15:56:24', '2026-05-24 15:56:24'),
(401, 113, 'puntos/S4qjuklaU9nisX2CCF4y50ToFFmKJRiwf2n3XnkR.jpg', 1, 0, '2026-05-24 16:17:26', '2026-05-24 16:17:26'),
(402, 113, 'puntos/0RdFcDgVSAZ20uFGv6i2yHvPeEFFADfLCtM8rfW6.jpg', 0, 1, '2026-05-24 16:17:26', '2026-05-24 16:17:26'),
(403, 113, 'puntos/YBAYmdjT1foPiDe86iX9X1zwBvuNzuBCACm5wnEO.jpg', 0, 2, '2026-05-24 16:17:26', '2026-05-24 16:17:26'),
(404, 113, 'puntos/fjW2vkMo91fDVD3moMQ9P1kFSOMhD5jbZAgSxxkh.jpg', 0, 3, '2026-05-24 16:17:26', '2026-05-24 16:17:26'),
(405, 113, 'puntos/A1mgB9NeBswoCNVvKMsauWTGixe3TRN7mrzZ3EAL.jpg', 0, 4, '2026-05-24 16:17:26', '2026-05-24 16:17:26'),
(406, 111, 'puntos/p3mMUdB8KlKoXSJ2LXB73L5uKR3I6Qb8iPaCXKNE.jpg', 1, 6, '2026-05-24 16:28:31', '2026-06-09 14:32:27'),
(407, 107, 'puntos/ZQTCPogionc6LCZZAOCXiigK5A0vLqvaT0Xr31Kj.jpg', 1, 0, '2026-05-24 16:33:43', '2026-05-24 16:59:51'),
(408, 107, 'puntos/7sb3WDIQMPfRKgCeAMwNQSdrLWBHSVqDNCPKn7IM.jpg', 0, 3, '2026-05-24 16:33:43', '2026-05-24 16:59:51'),
(409, 114, 'puntos/VWh0d0aJNtQtxA902agNjYt8Gp4oQ0I7nth9ua8P.jpg', 1, 0, '2026-05-24 17:44:47', '2026-05-24 17:44:47'),
(410, 115, 'puntos/uOSSWL8XdzlONPaSBwkO7YmD6pxrMTcdfwoc9l3d.jpg', 1, 0, '2026-05-26 14:32:54', '2026-05-26 14:32:54'),
(411, 115, 'puntos/WXytVGulKjC5IZl24O7Sb04fwxMDEQhwjwAPKF3U.jpg', 0, 1, '2026-05-26 14:34:09', '2026-05-26 14:34:09'),
(412, 115, 'puntos/vtHiMjKmkdles2VzYApmUymiYNOnAnAtAF9wJWQ1.jpg', 0, 2, '2026-05-26 14:34:09', '2026-05-26 14:34:09'),
(413, 115, 'puntos/fGBEaFzVFiWynmCPtJckSipTOEz8lRQpOmUBQqkk.jpg', 0, 3, '2026-05-26 14:34:09', '2026-05-26 14:34:09'),
(414, 115, 'puntos/npTVgZ1911mf9UpYi21BvIzTH5rQsC6peaYHrTSS.jpg', 0, 4, '2026-05-26 14:34:38', '2026-05-26 14:34:38'),
(415, 116, 'puntos/jvw5VJ3rbBlLiOX66uMuDIqSbUE24dx2o42K9Xfy.jpg', 1, 0, '2026-05-27 16:43:54', '2026-05-30 12:53:21'),
(416, 117, 'puntos/AiqWYS14HK1dotbrzooguwfrHmPnqU9xRD5Kbkb0.jpg', 1, 0, '2026-05-29 21:43:57', '2026-05-29 22:18:59'),
(417, 117, 'puntos/GcLSVFo6QQdKYdv5LpfumOEfRj2wIOknxHFpjxRf.jpg', 0, 1, '2026-05-29 21:43:57', '2026-05-29 22:18:59'),
(418, 117, 'puntos/wQbR4BT6y1Sdohh269Ty3LUzaTxbwYlcgxZF09dq.jpg', 0, 2, '2026-05-29 21:43:57', '2026-05-29 22:18:59'),
(419, 117, 'puntos/5jQmweulnmLNBlGqWiyCHCWWBCPnEnKVzeMMR5I4.jpg', 0, 3, '2026-05-29 21:43:57', '2026-05-29 22:18:59'),
(420, 117, 'puntos/crQuhFtvKMWLX7vpJMy6QofEKHICSqr4Y3s7UICc.jpg', 0, 4, '2026-05-29 21:43:57', '2026-05-29 22:18:59'),
(421, 117, 'puntos/6aGnI7D0rx68sMwYpV9yxlgqG7qUocGFADygIRNX.jpg', 0, 5, '2026-05-29 21:43:58', '2026-05-29 22:18:59'),
(422, 117, 'puntos/ECp8z37d42SuFlAaPNxCVw56H6Bbw9qFHTKvaxEe.jpg', 0, 6, '2026-05-29 21:43:58', '2026-05-29 22:18:59'),
(423, 117, 'puntos/U3ZxBhApMCDSsekb6LuyDytJOrONtCnwpa6bNqUq.jpg', 0, 7, '2026-05-29 21:43:58', '2026-05-29 22:18:59'),
(424, 32, 'puntos/swc5Js2xRO0Zz95HfMPINH52QyMKrZIyvwwT5WzG.jpg', 0, 4, '2026-05-29 21:47:02', '2026-06-02 22:17:06'),
(425, 32, 'puntos/cAUjKSDHfGoVlXnl9keX4dJFxhRxLpjbynCLXief.jpg', 0, 5, '2026-05-29 21:47:02', '2026-06-02 22:17:06'),
(426, 32, 'puntos/ICUpJkfKEtFuO6vtcAK6wJwLt0hI8H5RO2Qj41JK.jpg', 0, 6, '2026-05-29 21:47:02', '2026-06-02 22:17:06'),
(427, 32, 'puntos/hKeEPUyKPLFP6impjiWycRruZjZMRQKaZV5WKEhx.jpg', 0, 7, '2026-05-29 21:47:02', '2026-06-02 22:17:06'),
(428, 32, 'puntos/54hjzirEvI1BxTqv9FRHqDwOmz9t9Z6l5cWVjwIA.jpg', 0, 8, '2026-05-29 21:47:02', '2026-06-02 22:17:06'),
(429, 118, 'puntos/NWnW5JL6W8XwEIW9tirLwE8gqBZcFTQbxLcBgyL8.jpg', 1, 0, '2026-05-29 22:24:04', '2026-05-29 22:24:04'),
(430, 118, 'puntos/ZJZfCCLO7YxtjVSsTm1sZ7j6XgzjKSaVOVa0lnIn.jpg', 0, 1, '2026-05-29 22:24:04', '2026-05-29 22:24:04'),
(431, 118, 'puntos/GYpipcoxHyAtVVAHncEQQWVg2wc8aBQe1OmlwpBM.jpg', 0, 2, '2026-05-29 22:24:04', '2026-05-29 22:24:04'),
(432, 118, 'puntos/Nfvgcz1jsyughwfmrvDfWnULQR0AzxKhQYSWU3Mq.jpg', 0, 3, '2026-05-29 22:24:04', '2026-05-29 22:24:04'),
(433, 118, 'puntos/Fso6zkKP9YU34R8Aj51OmLlzDPniKYP0izlzSMf3.jpg', 0, 4, '2026-05-29 22:24:04', '2026-05-29 22:24:04'),
(434, 118, 'puntos/PPFzikiUTQDWRkYJmPogzWaOJw3FzTkModE8jAHt.jpg', 0, 5, '2026-05-29 22:24:05', '2026-05-29 22:24:05'),
(435, 118, 'puntos/rUdikAg80sCb9v9YA4bA7YOs7OIP5O3pWFyUhBFt.jpg', 0, 6, '2026-05-29 22:24:05', '2026-05-29 22:24:05'),
(436, 118, 'puntos/mcf1jUq7PpAoFPtRQw2AgVqnX9Mn262czXouZ9ca.jpg', 0, 7, '2026-05-29 22:24:05', '2026-05-29 22:24:05'),
(437, 118, 'puntos/wz7LicSP9cojsvWYCKAYVPMecpO0l2mWct3n9noG.jpg', 0, 8, '2026-05-29 22:24:05', '2026-05-29 22:24:05'),
(438, 119, 'puntos/eheaeKcZZwcnVUDsJTuIQbbbjIzwMjkooeQdNx2r.jpg', 1, 0, '2026-05-29 22:48:58', '2026-05-29 22:51:03'),
(439, 119, 'puntos/dbh0Wv50ABRP6UV0J77MTIVBVq2oeP4Th7l99pGE.jpg', 0, 1, '2026-05-29 22:48:59', '2026-05-29 22:51:03'),
(440, 119, 'puntos/2TCtzPyDCcNWYALHX557j5sUxkWj3Ojjya3PD3wn.jpg', 0, 2, '2026-05-29 22:48:59', '2026-05-29 22:51:03'),
(441, 119, 'puntos/qcAFCwSWxOkhbNEdLAbDNzEoWoC74iW9Xp1as2zw.jpg', 0, 3, '2026-05-29 22:48:59', '2026-05-29 22:51:03'),
(442, 119, 'puntos/6L8BeIjOJ5YfGX5Ix0KjCDd0jplWjKt1knsQSYZI.png', 0, 4, '2026-05-29 22:48:59', '2026-05-29 22:51:03'),
(443, 119, 'puntos/E68voWOQYWIPpJETVYsdHn6eXZKKRqoNzSRqLzos.jpg', 0, 5, '2026-05-29 22:49:00', '2026-05-29 22:51:03'),
(444, 119, 'puntos/CX5SO9CsyznbPvOoN2q6UKVLT69YVhGmyAgd8Wri.jpg', 0, 6, '2026-05-29 22:49:00', '2026-05-29 22:51:03'),
(445, 119, 'puntos/2kQ9MaMHum44U8gfwFUUfFevdaSmEuqTBs4VjSfa.jpg', 0, 7, '2026-05-29 22:49:00', '2026-05-29 22:51:03'),
(446, 119, 'puntos/k0A1KepGXmxvRPoV2OqMeyJ2HRlP05k5G6QVo39t.jpg', 0, 8, '2026-05-29 22:51:03', '2026-05-29 22:51:03'),
(447, 120, 'puntos/UXkbSCsEZWN2sfvEhyzwWMnGbA2AAZDHpOraEBqE.jpg', 0, 2, '2026-05-29 22:57:43', '2026-06-02 22:13:55'),
(448, 120, 'puntos/LxCrbOU4vQykAP10WaOabWcIvQIl6LstApbjs5V2.jpg', 1, 0, '2026-05-29 22:57:43', '2026-06-02 22:13:55'),
(449, 120, 'puntos/ECGvLQXvYCuwi4QzbCp25Nq5FzEKdFxfOWRr7qSk.jpg', 0, 3, '2026-05-29 22:57:43', '2026-06-02 22:13:55'),
(450, 120, 'puntos/UtrcmXrwfQzAJ30RLFTb6fWV0yb5dqu80Pq3hH5k.jpg', 0, 4, '2026-05-29 22:57:43', '2026-06-02 22:13:55'),
(451, 120, 'puntos/Yuj44MuLpYzsW87RjjElvsaK5yaUoHfgW6smCFlm.jpg', 0, 5, '2026-05-29 22:57:43', '2026-06-02 22:13:55'),
(453, 121, 'puntos/whjIYDd5xLL3dM2dGaeQBJxDYNpeQ4il0tIKt6XB.jpg', 1, 0, '2026-05-30 04:01:58', '2026-05-30 12:57:06'),
(454, 122, 'puntos/xyrTozcZEcTF2mkrP3XF0PaQF4s9wZ8swnHQJYXq.jpg', 1, 0, '2026-05-30 13:11:20', '2026-06-03 00:52:29'),
(455, 122, 'puntos/yPb2N5Z8jcNa4ySVBNnNcKdCOJ4WZGdb02Xbao6E.jpg', 0, 1, '2026-05-30 13:11:20', '2026-06-03 00:52:29'),
(456, 122, 'puntos/FZdLSYmJUwnrSI5UiSSVaHK9zlKDYZ1X2Vi57rVG.jpg', 0, 3, '2026-05-30 13:11:20', '2026-06-03 00:52:29'),
(457, 122, 'puntos/GhJKCu2dMhQCNzszkZr265cxaGlNDOsdKGByqtLC.jpg', 0, 4, '2026-05-30 13:11:20', '2026-06-03 00:52:29'),
(458, 122, 'puntos/pHw4sD6ihxPhmksckd5SMqcVSirXjhBshWCwc5nS.jpg', 0, 5, '2026-05-30 13:11:21', '2026-06-03 00:52:29');
INSERT INTO `imagenes_punto` (`id`, `punto_interes_id`, `ruta`, `es_principal`, `orden`, `created_at`, `updated_at`) VALUES
(459, 122, 'puntos/i2tg6lGO6PtMAjczTqL5oEUlYnXTz0TiivW8L37W.jpg', 0, 6, '2026-05-30 13:11:21', '2026-06-03 00:52:29'),
(460, 18, 'puntos/4W3QhQU0VeCL4Bh8p2YYd5mj3pKPRnxCWxI7svJR.jpg', 0, 1, '2026-06-02 21:51:21', '2026-06-09 16:33:35'),
(461, 17, 'puntos/kXlPfPY58F70J3MP7aOSZR39ew7GDcZba6P1nSHO.jpg', 0, 1, '2026-06-02 21:59:04', '2026-06-02 22:38:50'),
(463, 120, 'puntos/I8xBc7p5jGsoVjq3aFvyvOoyL9plUkwsNdIZXzns.jpg', 0, 1, '2026-06-02 22:13:55', '2026-06-02 22:13:55'),
(464, 32, 'puntos/hCi6XIpqr8LUoKk4raIQAqmyKieYLukLAnuSw5nI.jpg', 0, 3, '2026-06-02 22:17:06', '2026-06-02 22:17:06'),
(465, 18, 'puntos/iEtq98i2VnD9xE3O4ZvM08PpBLIJ5McEMnTmYzit.jpg', 0, 2, '2026-06-02 22:24:03', '2026-06-09 16:33:35'),
(466, 17, 'puntos/Q1zYnsKIYmTe9kaD3pBf8hOEZbuJQztdN8dzNzwZ.jpg', 1, 0, '2026-06-02 22:37:29', '2026-06-02 22:38:50'),
(467, 17, 'puntos/8pz7AzdlBLDX2soGXdrZSCrOl1nfZy91U4WmykOC.jpg', 0, 2, '2026-06-02 22:38:50', '2026-06-02 22:38:50'),
(468, 122, 'puntos/lPh1HuauoZAQmWy7KrIHt3NcPfYuyKzwQBcOmhRm.jpg', 0, 2, '2026-06-03 00:52:29', '2026-06-03 00:52:29'),
(469, 123, 'puntos/misQXwMAWjSkCmFjGK70fBdG8bqeH6y2d31UrPYJ.jpg', 1, 0, '2026-06-03 01:01:44', '2026-06-03 01:01:44'),
(470, 123, 'puntos/5tCd4mZ4tTk1tzIi2lO2EAwWJ8jEnwORCwItqio6.jpg', 0, 1, '2026-06-03 01:01:44', '2026-06-03 01:01:44'),
(471, 123, 'puntos/mMMBTblFH6oIhK8tcagmQuTbPVlFm435fsTWzaLg.jpg', 0, 2, '2026-06-03 01:01:44', '2026-06-03 01:01:44'),
(472, 123, 'puntos/43eSg6HiaWfyW8Pp0YCXMYhNIGJKSvOn8atgixhR.jpg', 0, 3, '2026-06-03 01:01:45', '2026-06-03 01:01:45'),
(473, 123, 'puntos/7kumVhZmj54byY7MoCQBinHREwEtMDW7a6mV448j.jpg', 0, 4, '2026-06-03 01:01:45', '2026-06-03 01:01:45'),
(474, 124, 'puntos/irLX9QNiXn2voDIfgUW8pbTDRzX7MAaGLxYFxQuI.jpg', 1, 0, '2026-06-03 01:12:25', '2026-06-03 01:15:18'),
(475, 124, 'puntos/6GfcyVslm5iDfxcY964TFGGhDJ134n0y2xroOWiS.jpg', 0, 1, '2026-06-03 01:12:25', '2026-06-03 01:15:18'),
(476, 124, 'puntos/Mq9pCfX2zBsN9Jb0lQgRaABopVTj5heun56Dn3Bq.jpg', 0, 2, '2026-06-03 01:12:25', '2026-06-03 01:15:18'),
(477, 124, 'puntos/7R3TlmYND6SLgGbHuAmSVKvQwrcdKMFZlgGZpHfV.jpg', 0, 3, '2026-06-03 01:12:26', '2026-06-03 01:15:18'),
(478, 124, 'puntos/ANVtZcrZDV6B73JnJJHxKjc7l9ahj1lEsEZRvoR9.jpg', 0, 4, '2026-06-03 01:12:26', '2026-06-03 01:15:18'),
(479, 124, 'puntos/1AXSm2NcJUKvDbiuUyGE8wuwsFHTEU9Dd8rGqvWD.jpg', 0, 5, '2026-06-03 01:12:26', '2026-06-03 01:15:18'),
(480, 124, 'puntos/gy82MGGoHKw2d5fHoa6bsGLlVmoXchZS83DQsZsU.jpg', 0, 6, '2026-06-03 01:12:26', '2026-06-03 01:15:18'),
(481, 124, 'puntos/vht7SiB1faISEpTDUvnC149KIhJosSMRFHHWImd4.jpg', 0, 7, '2026-06-03 01:12:26', '2026-06-03 01:15:18'),
(482, 124, 'puntos/7A0U0od2NZNF8qohcBW8b64Z8KuRzZBAaJ3lX3s9.jpg', 0, 8, '2026-06-03 01:12:26', '2026-06-03 01:15:18'),
(483, 125, 'puntos/yHUFCV7rLq0slyvkjQLrUZPNeiLuB2J7tbxcuuJP.jpg', 1, 0, '2026-06-03 01:28:41', '2026-06-03 01:28:41'),
(484, 125, 'puntos/18SxzB9ggwoGxBCYNADG05lIlLiWnY4lvu4v93M1.jpg', 0, 1, '2026-06-03 01:28:41', '2026-06-03 01:28:41'),
(485, 125, 'puntos/5Ax3HGfDI6ALdwtbYnodFnfTtwjDhG9QfbQBA5bj.jpg', 0, 2, '2026-06-03 01:28:41', '2026-06-03 01:28:41'),
(486, 125, 'puntos/yMxB7ZevpgPDWDKRegoUaxOPhrf9KA0gSG2MOP7y.jpg', 0, 3, '2026-06-03 01:28:41', '2026-06-03 01:28:41'),
(487, 126, 'puntos/UBJrtLyEJCYYLVnEkW4MwzVIoQhk2XPRru87u5Ah.png', 1, 0, '2026-06-04 17:19:31', '2026-06-04 17:19:31'),
(488, 127, 'puntos/tUAVNnMN4weyIzmV24bM3pDUSC1yHPY2KV2hZ6e4.jpg', 1, 0, '2026-06-05 13:56:52', '2026-06-06 13:23:12'),
(490, 127, 'puntos/tIJyKxpNJsUQjjML8Dp93kqbHVj8S5EqgKucebGa.jpg', 0, 2, '2026-06-05 13:56:52', '2026-06-06 13:23:12'),
(491, 127, 'puntos/hwfeANGQ8aezcxTNvSpvMK7i8xZhDsdLfnsv6AcH.jpg', 0, 1, '2026-06-06 13:23:12', '2026-06-06 13:23:12'),
(492, 127, 'puntos/NAJkaR1aETatUwox8t6j56DbOvWVXE1i0tvMMDuF.jpg', 0, 3, '2026-06-06 13:23:12', '2026-06-06 13:23:12'),
(493, 127, 'puntos/3NA0oXMm9K4ZGu1ManPLxG24LiLu1Eo0hTHuwYZ7.jpg', 0, 4, '2026-06-06 13:23:12', '2026-06-06 13:23:12'),
(494, 127, 'puntos/bkYXHWm5vSF71yugslWK3Hbm0Cfpx5s2gbiyfzVy.jpg', 0, 5, '2026-06-06 13:23:12', '2026-06-06 13:23:12'),
(495, 127, 'puntos/fMkTNVDOO5USGIqapo1KVqAZ6OdCQKSEprd78IUG.jpg', 0, 6, '2026-06-06 13:23:12', '2026-06-06 13:23:12'),
(496, 127, 'puntos/6R4NZDxFKghyC395ikcSwJTj3ZAx9XhFpe4hTqFj.jpg', 0, 7, '2026-06-06 13:23:12', '2026-06-06 13:23:12'),
(497, 127, 'puntos/ZyQrZiwsVnRPl2LJtA8EqEqBAK1eQL8ZPplW31bt.jpg', 0, 8, '2026-06-06 13:23:12', '2026-06-06 13:23:12'),
(498, 121, 'puntos/rzeNGrsKuW4OhuIZtNwZsJBs2eS3IF9dQGyM9VHy.jpg', 0, 1, '2026-06-07 23:31:13', '2026-06-07 23:31:13'),
(499, 121, 'puntos/8mhPSKd4rPY5r7WIfw7vgxbc3E7plnTODIwhAjgn.jpg', 0, 2, '2026-06-07 23:31:14', '2026-06-07 23:31:14'),
(500, 121, 'puntos/bSPaY1HhWerThO8SO51dB5ojmrhbM5ujDs0OC7n9.jpg', 0, 3, '2026-06-07 23:31:15', '2026-06-07 23:31:15'),
(502, 121, 'puntos/DkhpRaYNRTk4bcnYtaGp6kdCOSCMN5fIMU8lYSQL.jpg', 0, 5, '2026-06-07 23:31:17', '2026-06-07 23:31:17'),
(503, 121, 'puntos/B7wEAcQ5yz7PCz2aTPrbHSyUaFPKA3qrodpdiS1t.jpg', 0, 6, '2026-06-07 23:31:19', '2026-06-07 23:31:19'),
(505, 121, 'puntos/CrqZWfJqNcL0H22VnqlHmVYMY4hl4ZN181DzmaMt.jpg', 0, 8, '2026-06-07 23:31:21', '2026-06-07 23:31:21'),
(506, 121, 'puntos/jqbQOaeJmzxI07rKx63giZkdfPz9QzE5D8nHIqKx.jpg', 0, 9, '2026-06-07 23:31:22', '2026-06-07 23:31:22'),
(509, 18, 'puntos/HhfEJsXgiYfUbowcZVSuY9Cg4sFqtK2FSnL1y6xy.jpg', 0, 3, '2026-06-09 16:33:38', '2026-06-09 16:33:38'),
(510, 18, 'puntos/3emYnJePr9WdFFxapd05GbZxGVqK3iTEIjpbJjv1.jpg', 0, 4, '2026-06-09 16:33:40', '2026-06-09 16:33:40'),
(511, 18, 'puntos/kRCeV3Rk6wpOLtUCojOZbJJ2ePlm4W5AsisenM8z.jpg', 0, 5, '2026-06-09 16:33:42', '2026-06-09 16:33:42'),
(512, 18, 'puntos/ZjHyCq6qcjORoQQuGKkWsqEzM5069fgdVFUjNwBw.jpg', 0, 6, '2026-06-09 16:33:46', '2026-06-09 16:33:46'),
(513, 128, 'puntos/89148e53-a88d-42e5-b086-2c1e14b23bbc.webp', 1, 0, '2026-06-12 01:56:08', '2026-06-12 01:56:08'),
(514, 129, 'puntos/d43924a8-d5de-413e-a1af-36ca24a5ea08.webp', 1, 0, '2026-06-12 17:43:38', '2026-06-12 17:43:38'),
(515, 129, 'puntos/e9c60f2a-9b22-40d6-8b02-c335a0390a92.webp', 0, 1, '2026-06-12 19:00:47', '2026-06-12 19:00:47'),
(516, 129, 'puntos/a1f85c5d-466c-4436-962e-d1b294217772.webp', 0, 2, '2026-06-12 19:01:00', '2026-06-12 19:01:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `leads_contacto`
--

CREATE TABLE `leads_contacto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('cliente','artista') NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `tipo_negocio` varchar(60) DEFAULT NULL,
  `nombre_negocio` varchar(150) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `contactado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `leads_contacto`
--

INSERT INTO `leads_contacto` (`id`, `tipo`, `nombre`, `email`, `telefono`, `tipo_negocio`, `nombre_negocio`, `especialidad`, `ciudad`, `mensaje`, `contactado`, `created_at`, `updated_at`) VALUES
(1, 'cliente', 'sadsads', 'casas@asd.com', '894654', 'galeria', 'sacsac', NULL, 'sacsac', 'sacsacsac', 0, '2026-05-22 04:47:20', '2026-05-22 04:47:20'),
(2, 'cliente', 'Daniela', 'danielapazcabrera89@gmail.com', '56987547161', 'galeria', 'Daniela la artista', NULL, 'Valparaiso', 'Quiero ser una artista', 0, '2026-05-22 04:59:31', '2026-05-22 04:59:31'),
(3, 'cliente', 'Luis', 'luis.andrade.chile@gmail.com', '56995774085', 'otro', 'Mr Energy Pin', NULL, 'Viña', NULL, 0, '2026-05-22 05:02:30', '2026-05-22 05:02:30'),
(4, 'cliente', 'Rodrigo Hormazabal', 'emporiomoro@gmail.com', '+56981336153', 'restaurante', 'MORO', NULL, 'VALPARAISO', 'Tenemos terrazas Panorámicas con vista a la Sebastiana, la casa de Neruda en cerro Bellavista. Ofrecemos comida tradicional Chilena, como Pastel deChoclo, pecados, carne mechada, y machas a la parmesano, etc.', 0, '2026-05-22 20:27:03', '2026-05-22 20:27:03'),
(5, 'cliente', 'Rodrigo Hormazabal', 'emporiomoro@gmail.com', '+56981336153', 'restaurante', 'MORO', NULL, 'VALPARAISO', 'Tenemos terrazas Panorámicas con vista a la Sebastiana, la casa de Neruda en cerro Bellavista. Ofrecemos comida tradicional Chilena, como Pastel deChoclo, pecados, carne mechada, y machas a la parmesano, etc.', 0, '2026-05-22 20:27:03', '2026-05-22 20:27:03'),
(6, 'cliente', 'Rodrigo Hormazabal', 'emporiomoro@gmail.com', '+56981336153', 'restaurante', 'MORO', NULL, 'VALPARAISO', 'Tenemos terrazas Panorámicas con vista a la Sebastiana, la casa de Neruda en cerro Bellavista. Ofrecemos comida tradicional Chilena, como Pastel deChoclo, pecados, carne mechada, y machas a la parmesano, etc.', 0, '2026-05-22 20:27:18', '2026-05-22 20:27:18'),
(7, 'cliente', 'asd adas', 'cesar.eav@gmail.com', '465421651', 'museo', 'ewfweW', NULL, 'Valparaiso', 'SAD asd as dsa', 0, '2026-06-12 20:24:26', '2026-06-12 20:24:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `leads_publicita`
--

CREATE TABLE `leads_publicita` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `negocio` varchar(255) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `contactado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `leads_publicita`
--

INSERT INTO `leads_publicita` (`id`, `nombre`, `email`, `telefono`, `negocio`, `mensaje`, `contactado`, `created_at`, `updated_at`) VALUES
(1, 'Kira Andrade', 'cesar.eav@gmail.com', '+872173283', 'aÑJSB A', 'webWIHBRBEAÑJG', 1, '2026-04-16 07:15:08', '2026-04-30 01:37:46'),
(2, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:12', '2026-04-25 21:27:12'),
(3, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:13', '2026-04-25 21:27:13'),
(4, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:14', '2026-04-25 21:27:14'),
(5, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:16', '2026-04-25 21:27:16'),
(6, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:17', '2026-04-25 21:27:17'),
(7, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:18', '2026-04-25 21:27:18'),
(8, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:19', '2026-04-25 21:27:19'),
(9, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:20', '2026-04-25 21:27:20'),
(10, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:22', '2026-04-25 21:27:22'),
(11, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:23', '2026-04-25 21:27:23'),
(12, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:39', '2026-04-25 21:27:39'),
(13, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:41', '2026-04-25 21:27:41'),
(14, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:42', '2026-04-25 21:27:42'),
(15, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:44', '2026-04-25 21:27:44'),
(16, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:50', '2026-04-25 21:27:50'),
(17, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:27:56', '2026-04-25 21:27:56'),
(18, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:28:03', '2026-04-25 21:28:03'),
(19, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:28:05', '2026-04-25 21:28:05'),
(20, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:28:06', '2026-04-25 21:28:06'),
(21, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:28:08', '2026-04-25 21:28:08'),
(22, 'oykddvhmzs', 'izvxyzil@immenseignite.info', '+1-560-025-0040', 'xuttrieelm', 'hgeyzfzhwvwknxtrimxliwttuopzgj', 0, '2026-04-25 21:28:10', '2026-04-25 21:28:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_03_23_231238_create_punto_interes_table', 1),
(6, '2026_03_23_231524_create_imagen_puntos_table', 1),
(7, '2026_03_24_023315_create_categorias_table', 1),
(8, '2026_03_24_023519_add_categoria_id_to_punto_interes_table', 1),
(9, '2026_04_08_214834_add_orden_to_imagenes_punto_table', 1),
(10, '2026_04_08_233942_drop_category_from_puntosinteres_table', 1),
(11, '2026_04_09_013522_add_campos_cliente_to_puntosinteres_table', 1),
(12, '2026_04_09_022252_add_carta_to_puntosinteres_and_tipo_to_categorias', 1),
(13, '2026_04_09_023857_add_menu_del_dia_to_puntosinteres_table', 1),
(14, '2026_04_09_024459_add_timestamps_carta_menu_to_puntosinteres_table', 1),
(15, '2026_04_09_025632_add_oferta_control_to_puntosinteres_table', 1),
(16, '2026_04_09_032124_add_alojamiento_fields_to_puntosinteres_table', 1),
(17, '2026_04_09_034429_add_modulos_habilitados_to_puntosinteres_table', 1),
(18, '2026_04_09_100000_create_entradas_museo_table', 1),
(19, '2026_04_09_100001_create_exposiciones_museo_table', 1),
(20, '2026_04_09_100002_create_eventos_agenda_table', 1),
(21, '2026_04_09_110000_consolidar_tablas_modulos', 1),
(22, '2026_04_12_172102_create_leads_publicita_table', 2),
(23, '2026_05_01_193723_create_panoramas_table', 3),
(24, '2026_05_02_145254_add_google_id_to_users_table', 4),
(25, '2026_05_15_000001_add_categoria_to_panoramas_table', 5),
(26, '2026_05_15_000002_add_fecha_fin_to_panoramas_table', 5),
(27, '2026_05_19_000001_create_panorama_imagenes_table', 6),
(28, '2026_05_19_194920_add_enlace_to_panoramas_table', 7),
(29, '2026_05_19_202843_change_hora_length_in_panoramas_table', 8),
(30, '2026_05_19_203750_create_configuraciones_table', 9),
(31, '2026_05_19_232420_create_artistas_table', 10),
(32, '2026_05_19_232421_create_artista_imagenes_table', 10),
(33, '2026_05_21_232359_create_leads_contacto_table', 11),
(34, '2026_05_24_103712_add_dias_semana_to_panoramas_table', 12),
(35, '2026_05_24_125330_create_punto_productos_table', 13),
(36, '2026_05_24_131920_add_imagen_to_leads_publicita_table', 13),
(37, '2026_05_26_000001_create_posts_table', 14),
(38, '2026_05_26_000002_add_imagenes_to_posts_table', 14),
(39, '2026_05_27_000001_create_experiencias_table', 15),
(40, '2026_05_27_000002_create_experiencia_imagenes_table', 15),
(41, '2026_05_27_000003_add_whatsapp_to_experiencias_table', 16),
(42, '2026_05_28_000001_add_estado_to_experiencias_table', 17),
(43, '2026_05_28_000002_add_periodo_to_experiencias_table', 17),
(44, '2026_06_07_182934_add_slug_to_panoramas_table', 18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `panoramas`
--

CREATE TABLE `panoramas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `dias_semana` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dias_semana`)),
  `hora` varchar(100) DEFAULT NULL,
  `enlace` varchar(255) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `es_gratuito` tinyint(1) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `panoramas`
--

INSERT INTO `panoramas` (`id`, `titulo`, `slug`, `ubicacion`, `fecha`, `fecha_fin`, `dias_semana`, `hora`, `enlace`, `categoria`, `es_gratuito`, `imagen`, `activo`, `orden`, `created_at`, `updated_at`) VALUES
(1, 'Los Crack del Puerto', 'los-crack-del-puerto-1', 'Quinta Los Nuñez', '2026-05-01', NULL, NULL, '20:00 hrs', NULL, NULL, 0, 'panoramas/HOYIEIVgi7epI9yGTgX4QHd9pa3BeVDcFGIankJA.webp', 1, 3, '2026-05-02 03:39:48', '2026-05-06 01:18:28'),
(2, 'Fabiola Campos', 'fabiola-campos-2', 'Bar El Canario', '2026-05-02', NULL, NULL, '22:00 hrs', NULL, NULL, 0, 'panoramas/94Mg0w4JwMzsqLw38tEvDuBQtu9RDSqvRZC6PDIw.webp', 1, 2, '2026-05-02 03:43:34', '2026-05-06 01:19:31'),
(4, 'Circo “Bogardus, en busca del paraíso”', 'circo-bogardus-en-busca-del-paraiso-4', 'Parque Cultural de Valparaíso - Ex Cárcel', '2026-05-08', NULL, NULL, '19:00', NULL, NULL, 0, 'panoramas/PbfrJd7wEvsNv6nAn4ztcL5VrWoeFqZGLuJxLe8N.png', 1, 1, '2026-05-06 01:16:37', '2026-05-06 01:16:37'),
(5, 'Cata de vinos- charcutería', 'cata-de-vinos-charcuteria-5', 'Corazón continto', '2026-05-06', NULL, NULL, NULL, NULL, NULL, 0, 'panoramas/vxLOp3toSA53aWzu3VMBJtvvlvvQcbes5iecUeAQ.png', 1, 1, '2026-05-06 01:23:58', '2026-05-06 01:23:58'),
(6, 'Noche Roja', 'noche-roja-6', 'Blanco 1273', '2026-05-15', NULL, NULL, '22:30', NULL, 'danza', 0, 'panoramas/dlzu2PfEprICKauszYO3UdjctDFd6e4O3MoITnlO.jpg', 1, 0, '2026-05-11 00:46:04', '2026-05-15 20:52:23'),
(7, 'Festival Tributo', 'festival-tributo-7', 'Blanco 1386', '2026-05-15', NULL, NULL, '22:00 hrs', NULL, 'musica', 0, 'panoramas/BPW9uD1kN9iMjXkpZHpuFYlT4tyat8w3L77LtY4D.jpg', 1, 0, '2026-05-11 00:49:07', '2026-05-15 20:50:43'),
(8, 'Varieté a Mar Azul', 'variete-a-mar-azul-8', 'Santa Inés 38, cerro Barón', '2026-05-16', NULL, NULL, '19:00 hrs', NULL, 'musica', 0, 'panoramas/h1ZaBBgvvI9itDiAGvdounHjfKc4vUZEAUn6Mfnt.webp', 1, 0, '2026-05-11 01:04:40', '2026-05-15 20:56:18'),
(9, 'Oiga usted', 'oiga-usted-9', 'Santa Inés 38, cerro Barón', '2026-05-17', NULL, NULL, '16:00', NULL, 'teatro', 1, 'panoramas/JdWtDtUV7aNNrqyhKPmschBdFrozCQIvJTUQ7T0q.webp', 1, 0, '2026-05-11 01:05:57', '2026-05-15 20:51:39'),
(10, 'Concierto de Bolsillo Orquesta Alumnos', 'concierto-de-bolsillo-orquesta-alumnos-10', 'Centro Extension DUOC Valparaíso', '2026-05-31', NULL, NULL, '11:30 a 12:15 hrs,', NULL, 'musica', 1, 'panoramas/oIl6g0OkkRp2Z4s4lhyru6iu6Pn2NCRC744Ur3ZF.jpg', 1, 0, '2026-05-19 22:16:00', '2026-05-19 22:16:00'),
(11, 'Taller de Reciclaje Textil', 'taller-de-reciclaje-textil-11', 'Centro Extension DUOC Valparaíso', '2026-05-31', NULL, NULL, '11:00 a 14:00 hrs', NULL, 'arte', 1, 'panoramas/KHUh2CYgJKqo5ZjaJ4RjsKZTsa72XkISg69cxHTI.jpg', 1, 0, '2026-05-19 22:17:29', '2026-05-19 22:17:29'),
(12, 'Restauración Patrimonial', 'restauracion-patrimonial-12', 'Centro Extension DUOC Valparaíso', '2026-05-31', NULL, NULL, '11:00 a 14:00 hrs', NULL, 'arte', 1, 'panoramas/0oXr5lbrjM43zid0aR9TZOjGIc0WAoCxXVlnaDRO.jpg', 1, 0, '2026-05-19 22:18:44', '2026-05-19 22:18:44'),
(13, 'Recorrido Patrimonial', 'recorrido-patrimonial-13', 'Centro Extension DUOC Valparaíso', '2026-05-31', NULL, NULL, '11:00/12:00/13:00', NULL, 'tour', 1, 'panoramas/Gi0qw1WXuOJZxL1LH9Z47EKwxbZgU34tMCxXoqKX.jpg', 1, 0, '2026-05-19 22:21:20', '2026-05-19 22:30:32'),
(14, 'Tertulia Gastronómica \"El sabor de nuestro Patrimonio Culinario\"', 'tertulia-gastronomica-el-sabor-de-nuestro-patrimonio-culinario-14', 'Centro Extension DUOC Valparaíso', '2026-05-31', NULL, NULL, '11:00 y 12:30 hrs', NULL, 'gastronomia', 1, 'panoramas/EidFpa7LWNQDNP95DYfduoAVtKouef8QcG4HnYA8.jpg', 1, 0, '2026-05-19 22:22:32', '2026-05-19 22:22:32'),
(15, 'Taller de Serigrafía', 'taller-de-serigrafia-15', 'Centro Extension DUOC Valparaíso', '2026-05-31', NULL, NULL, '11:00 a 14:00 hrs', NULL, 'taller', 1, 'panoramas/YPciBUpevxDxqsUpc4DSeIMRt3o0LzjWfvB9Nzea.jpg', 1, 0, '2026-05-19 22:27:40', '2026-05-19 22:27:40'),
(16, 'Restauración en Acción', 'restauracion-en-accion-16', 'Centro Extension DUOC Valparaíso', '2026-05-30', NULL, NULL, '11:00 a 14:00 hrs', NULL, 'taller', 1, 'panoramas/kuiBPb5xJTHOinDt4l4eKIxlDSEYU93aH87h6oaV.jpg', 1, 0, '2026-05-19 22:28:58', '2026-05-19 22:28:58'),
(17, 'Teatro Lambe Lambe- Trilogía Libre de Dani Teatro', 'teatro-lambe-lambe-trilogia-libre-de-dani-teatro-17', 'Centro Extension DUOC Valparaíso', '2026-05-31', NULL, NULL, '11:00 a 14:00 hrs', NULL, 'teatro', 1, 'panoramas/FqfCa7cc1lwl1r9mP8E0lDU1Z7MR5abyYTT5KTMC.jpg', 1, 0, '2026-05-19 22:30:05', '2026-05-24 16:30:35'),
(18, 'The Doors and Jimi Hendrix', 'the-doors-and-jimi-hendrix-18', 'Sala Rivoli', '2026-05-23', NULL, NULL, '22: 30', NULL, 'musica', 0, 'panoramas/dX3ohh4G1LAXA08CLpKOSrqHa7qczIivCqsj6lKM.jpg', 1, 0, '2026-05-19 22:37:03', '2026-05-19 22:37:03'),
(19, 'Hermanas', 'hermanas-19', 'Teatro Municipal de Valparaíso', '2026-05-20', NULL, NULL, '10:00 hrs', NULL, 'cine', 1, 'panoramas/wXznPULCIDONoIUbdYtWiNYxl9Lxhxke1c2AjcSk.jpg', 1, 0, '2026-05-19 22:39:51', '2026-05-19 22:39:51'),
(20, 'V Congreso de Carnaval y Fiesta 2026', 'v-congreso-de-carnaval-y-fiesta-2026-20', 'Sala Rubén Dario. Universidad de Valparaíso.', '2026-05-25', '2026-05-26', NULL, '10:00 a 18:00 hrs', NULL, 'conferencia', 1, 'panoramas/MfBpy6A9Nxf7WukTFOKTeRIi5rA14pcHWFiala8s.webp', 0, 0, '2026-05-19 23:06:41', '2026-05-24 15:38:31'),
(21, 'Anti Tour Patrimonial', 'anti-tour-patrimonial-21', 'Barrio Puerto', '2026-05-30', NULL, NULL, '10:00 | 12:00 hrs', 'https://www.instagram.com/trece_restauro/', 'tour', 1, 'panoramas/c3lk95aIGv7TsrpX4OTULHsSfCLTXOKa0SPKrfgZ.jpg', 1, 0, '2026-05-19 23:58:17', '2026-05-20 00:31:52'),
(22, 'Lecturas Perfomáticas', 'lecturas-perfomaticas-22', 'Patio Sócrates, Socrates 15, cerro Cordillera.', '2026-05-23', NULL, NULL, '19:30 hrs.', 'https://www.instagram.com/p/DYVQW8LgbRy/', 'musica', 0, 'panoramas/gtbq7C24NuH4K1lBeDbJOuX2cacKcZVTm78mtmSS.webp', 1, 0, '2026-05-20 00:06:34', '2026-05-20 00:06:34'),
(23, 'Taller de Cueca', 'taller-de-cueca-23', 'Rincón de las Guitarras, Freire 431', '2026-05-21', NULL, NULL, '20:00 hrs', 'https://www.instagram.com/p/DYh-bfLJqly/', 'danza', 0, 'panoramas/eBevPyPib7lIUSFjPs0nHGQBukEBsmtSjlz8XSn2.webp', 1, 0, '2026-05-20 00:08:16', '2026-05-20 00:08:16'),
(25, 'Degustación + DJ SET EN STADIO RISTORANTE', 'degustacion-dj-set-en-stadio-ristorante-25', 'Manuel Rodríguez 1915, Valparaíso.', '2026-05-23', NULL, NULL, '19:00 a 23:00 hrs.', 'https://www.instagram.com/p/DYA1uy8jiae/', 'gastronomia', 0, 'panoramas/gY7HIn68QHpoxUuYo5AUycrJPKEJSKLWuzId7i5Y.jpg', 1, 0, '2026-05-20 00:17:11', '2026-05-20 00:17:11'),
(26, 'Voces de Cerro', 'voces-de-cerro-26', 'Patio Sócrates, Socrates 15, cerro Cordillera.', '2026-05-23', '2026-06-13', '[6]', 'Sábados de 17:00 a 19:00 hrs.', 'https://www.instagram.com/p/DYFU_W2jsad/?img_index=1', 'taller', 0, 'panoramas/sTPeEWDMWAWiLS2G1Pbo4FZqi7GCpHTfZPxEsnSq.jpg', 1, 0, '2026-05-20 00:30:26', '2026-05-24 14:54:43'),
(27, 'El Reino de las Cosas', 'el-reino-de-las-cosas-27', 'Teatromuseo del Títere y el Payaso, Cumming 795.', '2026-05-23', NULL, NULL, '16:30 hrs', 'https://www.instagram.com/p/DYf3gb6Dmde/?img_index=1', 'teatro', 0, 'panoramas/MMe97zIJnR9uYgr306ivKDygKCBPnEhc6uEIa216.jpg', 1, 0, '2026-05-20 01:21:42', '2026-05-20 01:21:42'),
(28, 'Tour de Medianoche', 'tour-de-medianoche-28', 'Cementerio 3 de Playa Ancha', '2026-05-23', NULL, NULL, '23:59 hrs', 'https://www.instagram.com/tncvalpo/', 'tour', 0, 'panoramas/ir21rhdVjGi8JoSyS8O7m8dRs6yYUx5jrph1teGq.webp', 1, 0, '2026-05-20 02:36:49', '2026-05-20 02:36:49'),
(29, 'Día internacional de los juegos de mesa', 'dia-internacional-de-los-juegos-de-mesa-29', 'Universidad Santa María, Av. Placeres 401', '2026-06-06', NULL, NULL, NULL, 'https://www.instagram.com/clubderol_sansano/', 'taller', 1, 'panoramas/wvsTwH3ISM78eaBWTogAxKlwpsQ5zxuIlrKAB0zE.webp', 1, 0, '2026-05-20 02:40:23', '2026-05-20 02:40:23'),
(30, 'Concierto de otoño', 'concierto-de-otono-30', 'Teatro Municipal de Valparaíso', '2026-05-20', NULL, NULL, NULL, 'https://www.instagram.com/p/DYfwvuzn0vF/', 'musica', 1, 'panoramas/p2bfx6qCQyBuzBRGX7DX3tuYtr284o3uV7F6XjaM.jpg', 1, 0, '2026-05-20 02:43:03', '2026-05-20 02:43:03'),
(31, 'Ensayo general para un fracaso', 'ensayo-general-para-un-fracaso-31', 'Plaza Simón Bolivar', '2026-05-24', NULL, NULL, '18:30 hrs', 'https://www.instagram.com/p/DYft8xYTz93/', 'teatro', 0, 'panoramas/FXLUORXOESdp9icRptmrsbEih07gN4e1IVD2kUO2.jpg', 1, 0, '2026-05-20 02:45:33', '2026-05-24 15:17:53'),
(32, 'Expo de Vinillos', 'expo-de-vinillos-32', 'Café Terrua Bistró', '2026-05-22', NULL, NULL, '12:00 a 18:00 hrs', NULL, 'musica', 1, 'panoramas/lY0p4D2nqKQvv4FjrKy6eHK2Oo3BHcWOLqfcZxkL.jpg', 1, 0, '2026-05-20 06:20:54', '2026-05-20 06:20:54'),
(33, 'Tomadochi Festival de Otoño', 'tomadochi-festival-de-otono-33', 'Parque Cultural de Valparaíso', '2026-06-07', NULL, NULL, '11:00 a 19:00 hrs', 'https://www.instagram.com/p/DYVVmp-ljSa/', 'feria', 1, 'panoramas/AjvwnT0PrEULlpiuG3lLLK9qf1EI66AzD8rtIqVV.jpg', 1, 0, '2026-05-20 06:29:40', '2026-05-20 06:29:40'),
(34, 'Acuyo Mestizo', 'acuyo-mestizo-34', 'Gato en la Ventana', '2026-05-23', NULL, NULL, '22:00 hrs', 'https://www.instagram.com/p/DYizqaCxgvn/', 'musica', 0, 'panoramas/ddvkkLW3bZZVrumtSFlXhsH1jtIN6Gy8hZu4QAAD.jpg', 1, 0, '2026-05-20 06:43:05', '2026-05-20 06:43:05'),
(35, 'Tarde de Música Andina', 'tarde-de-musica-andina-35', 'Gato en la Ventana, Cumming 113', '2026-05-31', NULL, NULL, 'Desde las 17:00 hrs', 'https://www.instagram.com/p/DYfj0SLRozg/', 'musica', 0, 'panoramas/4pV68IbdY9NEYZ9Gv18Ltbj4Z4TODRjY9nyAhBvb.jpg', 1, 0, '2026-05-20 06:45:04', '2026-05-20 06:45:04'),
(36, 'Creedence Show 🔥🎸 Revive', 'creedence-show-revive-36', 'Sala Rivoli', '2026-05-22', NULL, NULL, '22:30 hrs', 'https://www.instagram.com/p/DYimlzchY03/', 'musica', 0, 'panoramas/dSMjU32RgeLI58HUVqQX8d09GjXAxYioRyBGXfLH.jpg', 1, 0, '2026-05-20 06:49:45', '2026-05-20 06:49:45'),
(37, 'Intoxicadas', 'intoxicadas-37', 'CECO, Avenida Rodelillo 2277', '2026-05-23', '2026-05-24', NULL, '19:00 hrs', 'https://www.instagram.com/p/DYBLvZHkaw3/?img_index=1', 'teatro', 0, 'panoramas/vpuitAX0yHCWfexsGqYaJNSy8Y207STrp3U1p2sR.jpg', 1, 0, '2026-05-20 17:35:49', '2026-05-20 17:35:49'),
(38, 'Noche de cumbia psicodélica', 'noche-de-cumbia-psicodelica-38', 'Bar La Morada, Cumming 68', '2026-05-20', NULL, NULL, '22:00 hrs', 'https://www.instagram.com/p/DYe-ROwgq3V/', 'musica', 1, 'panoramas/lM6q3GzOKAvOMr91ohqrzm9gVCthVe33WMJdrPlY.jpg', 1, 0, '2026-05-20 18:55:45', '2026-05-20 18:55:45'),
(40, '3ra Expo Vinillos y Flahtattoo', '3ra-expo-vinillos-y-flahtattoo-40', 'Von Schroeders 170', '2026-05-21', NULL, NULL, '12:00 a 20:00 hrs', 'https://www.instagram.com/p/DYiCee3ifos/?img_index=1', 'exposicion', 0, 'panoramas/cSDnDFPyN2hjBMYK414x2V2l3w3XWEAuYmVDfNWb.webp', 1, 0, '2026-05-20 19:27:02', '2026-05-20 19:27:02'),
(41, 'Pingüino de Humboldt', 'pinguino-de-humboldt-41', 'Centro Deportivo Nautilus, Caleta Abarca, Viña del Mar.', '2026-05-23', NULL, NULL, '16:30 hrs', 'https://www.instagram.com/p/DYi07FiRqcr/', 'taller', 0, 'panoramas/6BlVDjcXNhXPd3keb7a6w1pJmiKAXUyQHTHC1J3u.webp', 1, 0, '2026-05-20 19:30:51', '2026-05-20 19:30:51'),
(42, 'Valparaíso en marcha: proyectos para el desarrollo regional', 'valparaiso-en-marcha-proyectos-para-el-desarrollo-regional-42', 'Salón de Actos del Edificio T de la UTFSM, ubicado en Av. España 1680, Valparaíso.', '2026-05-26', NULL, NULL, '09:00 hrs', 'https://www.instagram.com/p/DYkAPKNjmii/', 'conferencia', 1, 'panoramas/EOceNjLw6Hpwj0pwq3GrZ92I7IOaZsbIYNj2Aj3W.jpg', 1, 0, '2026-05-20 20:20:37', '2026-05-20 20:20:37'),
(43, 'Almuerzo solidario', 'almuerzo-solidario-43', 'Av. Rodelillo 2277', '2026-05-30', NULL, NULL, 'Desde las 13:00 hrs', 'https://www.instagram.com/p/DYkUXqFAH26/', 'gastronomia', 0, 'panoramas/akUaSYaSPUnCN1zuR8rdj7dHzaOG5oQ2yuNtOmoM.jpg', 1, 0, '2026-05-20 21:10:27', '2026-05-20 21:10:27'),
(44, 'Tributo a Ernest Ranglin', 'tributo-a-ernest-ranglin-44', 'La Morada Bar, Cumming 68', '2026-05-21', NULL, NULL, '23:00 hrs', 'https://www.instagram.com/p/DYhkUL7jltu/?img_index=1', 'musica', 0, 'panoramas/5XvsapjsZfn5L6AheKOzmmfGftT4ILm0rhUliuY9.jpg', 1, 0, '2026-05-21 02:08:11', '2026-05-21 02:08:11'),
(45, 'Vinyl Night', 'vinyl-night-45', 'Almirante Montt 658', '2026-05-22', NULL, NULL, '19:00 a 22:00 hrs', 'https://www.instagram.com/p/DYk6gdLPPSYWRfUx5eR0cZL5g4JHeIersgNgJk0/', 'musica', 0, 'panoramas/23fnhaz30kvfvdt8Rj3zkNwB7Kp4a2dcuS0xxZxT.jpg', 1, 0, '2026-05-21 04:08:34', '2026-05-21 04:08:34'),
(46, 'Día Internacional de los Museos USM', 'dia-internacional-de-los-museos-usm-46', 'Universidad Técnica Federico Santa María (Avenida España 1680, Valparaíso', '2026-05-31', NULL, NULL, '10:30 a 17:00', 'https://www.instagram.com/p/DYfgHK5lXUC/', 'exposicion', 1, 'panoramas/UNylRAsMAYhZpbTwwZ2MuRowkk8Wf9hTrpQbXi3a.jpg', 1, 0, '2026-05-21 18:12:36', '2026-05-21 18:12:36'),
(47, 'Virtualparaíso', 'virtualparaiso-47', 'Destino Valparaíso, Concepciòn 499', '2026-05-21', '2026-05-24', NULL, '10:00 a 19:00', 'https://www.instagram.com/p/DYlA3VLgA4J/', 'exposicion', 1, 'panoramas/BvDxjNZjf6tTgNxa4xTZ1A1gDbFZTqzFjCWrblwl.jpg', 1, 0, '2026-05-21 18:14:32', '2026-05-21 18:14:32'),
(48, 'Curso de adobillo y paja encofrada', 'curso-de-adobillo-y-paja-encofrada-48', 'Xiloscopio, Valparaíso', '2026-05-30', '2026-05-31', NULL, 'Ver enlace', 'https://www.instagram.com/p/DYm3hlDxKYv/', 'taller', 0, 'panoramas/c28CPD6Yy6Ql6c5YEbwXQXoSA1LVRFeJ7eIn4r8P.jpg', 1, 0, '2026-05-22 03:26:29', '2026-05-22 03:26:29'),
(49, 'Nico Mattioi', 'nico-mattioi-49', 'La Convencional, Victoria 2345', '2026-05-22', NULL, NULL, '22:00 hrs', 'https://www.instagram.com/p/DYfMBbdxqeX/', 'musica', 0, 'panoramas/iqeco8G3O3NNPLnxyq2y5JCg0NSuLn6w1i2WBmJs.jpg', 1, 0, '2026-05-22 03:36:36', '2026-05-22 03:36:36'),
(50, 'Segunda Feria de Artes y Oficios en La Compañía', 'segunda-feria-de-artes-y-oficios-en-la-compania-50', 'Espacio La Compañia, Eusebio Lillo 387, Valparaíso', '2026-05-30', '2026-05-31', NULL, '11:00 a 19:00', 'https://www.instagram.com/p/DYN4YqQmYcm/', 'feria', 0, 'panoramas/1wc6odQQUtIrUDAs1FnoWgnHmrA8Nts5cTABrL9j.jpg', 1, 0, '2026-05-22 03:39:18', '2026-05-22 03:39:18'),
(51, 'La suma constructiva de las perturbaciones', 'la-suma-constructiva-de-las-perturbaciones-51', 'Parque Cultural de Valparaíso', '2026-05-23', '2026-05-24', NULL, '18:00 hrs', 'https://www.instagram.com/p/DYfRwhPFZIA/?img_index=2', 'teatro', 0, 'panoramas/VvgvvQiLO775m179v7qf7Iv5miCYVQlo2kUF701u.jpg', 1, 0, '2026-05-22 03:43:43', '2026-05-24 15:34:16'),
(52, 'Transport Challenge', 'transport-challenge-52', 'Avenida Brasil con General Curz', '2026-08-19', '2026-08-21', NULL, 'Por confirmar.', 'https://www.instagram.com/ict_pucv/', 'conferencia', 1, 'panoramas/Wr1eTX6B3eLx5sJoIqaRNEC4jT3luAnW6Z4rMgHg.png', 1, 0, '2026-05-22 18:09:14', '2026-05-22 18:09:14'),
(53, 'Escalada Costera', 'escalada-costera-53', 'Laguna Verde', '2026-05-31', NULL, NULL, '10:00 a 16:00', 'https://www.instagram.com/p/DYKPf_mkSyK/', 'otros', 0, 'panoramas/oGBRavHsTFWskh9clbS3qdqLqSAI2BXXLzx2W4K0.webp', 1, 0, '2026-05-22 18:22:18', '2026-05-22 18:22:18'),
(54, 'Homenaje Roberto Parra', 'homenaje-roberto-parra-54', 'Valparaiso Profundo, Fisher 24', '2026-05-22', NULL, NULL, '19:00 hrs', 'https://www.instagram.com/p/DYm-rBXyLuq/', 'musica', 0, 'panoramas/ivbR2CsEbH8HvYdYnkQ2Ej2pCWUHpaAp2GRHtasK.jpg', 1, 0, '2026-05-22 18:25:35', '2026-05-22 18:25:35'),
(55, 'Fiesta de disfraces', 'fiesta-de-disfraces-55', 'Plaza Anibal Pinto 1178', '2026-05-30', NULL, NULL, 'Toda la noche', 'https://www.instagram.com/p/DYOQzlEThZD/', 'otros', 1, 'panoramas/qCGHmt3H6Xjy1YUX8aRg5IblD4xOzaHX9vgXpp7J.webp', 1, 0, '2026-05-22 18:48:27', '2026-05-22 18:48:27'),
(56, 'Tangos y Boleros', 'tangos-y-boleros-56', 'Café del Poeta, Plaza Anibal Pinto 1181', '2026-05-22', NULL, NULL, 'Desde las 18:30', 'https://www.instagram.com/p/DYa4S8jNZfK/', 'danza', 1, 'panoramas/8kHnUVJqWdp14yvHPpo3YhZIHRaErC3q2iRKdESL.jpg', 1, 0, '2026-05-22 18:53:16', '2026-05-22 18:53:16'),
(58, 'Gufi', 'gufi-58', 'Club Segundo Piso, Av. Brasil 1395, Valparaíso', '2026-06-20', NULL, NULL, '21:00', 'https://www.instagram.com/p/DYqkmvHjF8D/?img_index=1', 'musica', 0, 'panoramas/LrOLUmM5A1dgDV4TYre2AyTo8W3jTHeqlQCveoVa.webp', 1, 0, '2026-05-23 19:28:26', '2026-05-23 19:28:26'),
(59, 'Fiesta Retro', 'fiesta-retro-59', 'Club Segundo Piso, Av. Brasil 1395, Valparaíso', '2026-06-06', NULL, NULL, 'Por confirmar.', 'https://www.instagram.com/p/DYddNEFtzbH/', 'musica', 0, 'panoramas/PlCB5PU61f3OViWQmaP31uGwRH4b3y8v8DOCMi88.jpg', 1, 0, '2026-05-23 19:33:09', '2026-05-23 19:33:09'),
(60, 'Sueño Siamés', 'sueno-siames-60', 'Espanta Pajaros, Av Ecuador 257', '2026-06-06', NULL, NULL, '21:00', 'https://www.instagram.com/p/DYqdbANOHCt/', 'musica', 0, 'panoramas/YKfSY0e9EV1ARTFlCie2DfHyAQb9sdHAj3Tu9gjk.jpg', 1, 0, '2026-05-23 19:51:33', '2026-05-23 19:51:33'),
(61, '21 años Ritoque FM', '21-anos-ritoque-fm-61', 'Club Segundo Piso, Av. Brasil 1395, Valparaíso', '2026-06-05', NULL, NULL, '21:00', 'https://www.instagram.com/p/DYXlwIDDEIv/', 'musica', 0, 'panoramas/LIYEp36Bq2gpJol1R3tgfALENeiDAibNMadIMjYY.jpg', 1, 0, '2026-05-23 19:53:37', '2026-05-23 19:53:37'),
(62, 'Las Analfabetas', 'las-analfabetas-62', 'Centro Cultural Ascensor Concepción - Valparaíso (Paseo Gervasoni. Cerro Concepción)', '2026-05-28', NULL, NULL, '18:30', 'https://www.instagram.com/p/DYqscsDstd_/', 'teatro', 0, 'panoramas/cbHnYHCIU5BELE1YoUYJcbxg1KaZvFvrcF9ZpY2Y.webp', 1, 0, '2026-05-23 20:26:16', '2026-05-23 20:26:16'),
(63, 'Ruta Puerto, Patrimonio Cultural', 'ruta-puerto-patrimonio-cultural-63', 'Muelle Prat', '2026-05-30', '2026-05-30', NULL, 'Desde las 10:00 horas, cada una hora, hasta las 13:30 horas el último viaje.', 'https://www.instagram.com/p/DYkwf-dRrr-/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'tour', 1, 'panoramas/6CFGCwdTfauQSwErpsYcn2CvwhoFelosbMwCB3di.jpg', 1, 0, '2026-05-24 15:23:02', '2026-05-24 15:23:58'),
(64, 'Recorrido con historia por la Sucursal Prat de Valparaíso', 'recorrido-con-historia-por-la-sucursal-prat-de-valparaiso-64', 'Arturo Prat 656', '2026-05-30', '2026-05-30', NULL, 'O9:00- 17:00 hrs.', NULL, 'tour', 1, 'panoramas/JSL1hll5NA4yUKpcelJuCfUMCjtwy8xlEdhqhW2t.jpg', 1, 0, '2026-05-24 15:30:37', '2026-05-24 15:30:37'),
(65, 'Teletón, patrimonio social y solidario de Chile', 'teleton-patrimonio-social-y-solidario-de-chile-65', 'Avenida Francia 259, Valparaíso', '2026-05-30', NULL, NULL, '09:00 - 14:00', 'https://www.instagram.com/p/DYqR3WKDrl6/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'tour', 1, 'panoramas/LwpsSOtzlwFsq7HoNCOMx3UNfseaGzxZwghjsLa4.jpg', 1, 0, '2026-05-24 15:41:46', '2026-05-24 15:41:46'),
(66, 'Taller ¿Cómo imaginas el Valparaíso del año 2050?', 'taller-como-imaginas-el-valparaiso-del-ano-2050-66', 'CENTEX, Sotomayor 233, Valparaíso', '2026-05-30', NULL, NULL, 'Desde las 10:30 hasta las 17:30 hrs', 'https://www.instagram.com/p/DYr35e8uKpb/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'taller', 1, 'panoramas/zDMXKTLLqvUmrswO4ZbufRJzUa8jECRlYu7X8GHW.jpg', 1, 0, '2026-05-24 15:44:53', '2026-05-24 16:42:37'),
(67, 'Visitas Patrimoniales Edificio Ex Correos y Telégrafos de Valparaíso', 'visitas-patrimoniales-edificio-ex-correos-y-telegrafos-de-valparaiso-67', 'CENTEX, Sotomayor 233, Valparaíso', '2026-05-30', '2026-05-31', NULL, '11:00- 17: 00', NULL, 'tour', 1, 'panoramas/sm8fumMzbirUju44CXq8ZH3VIfw9iNS2BzeiLKiu.webp', 1, 0, '2026-05-24 15:50:28', '2026-05-24 15:50:28'),
(69, 'Recorrido Patrimonial Iglesia de los Sagrados Corazones de Valparaíso', 'recorrido-patrimonial-iglesia-de-los-sagrados-corazones-de-valparaiso-69', 'Independencia 2086, Valparaíso', '2026-05-30', '2026-05-31', NULL, '10:00 - 12:00 hrs. // 13:00-16.00 hrs.', 'https://www.instagram.com/p/DYNHe-GuTow/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'tour', 1, 'panoramas/yycOZepVUtXxYHHtk5JBpdNuYIvqEbSOlKkonLz5.jpg', 1, 0, '2026-05-24 15:57:20', '2026-05-24 16:41:15'),
(70, 'Recorrido Histórico Cuartel del Bote Salvavidas', 'recorrido-historico-cuartel-del-bote-salvavidas-70', 'Muelle Prat, Bajo el Restaurante \"Bote Salvavidas\"', '2026-05-30', '2026-05-31', NULL, '11:00 - 17:00 horario de corrido', 'https://www.instagram.com/p/DYZ3gXgkQ3-/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'tour', 1, 'panoramas/liC47RzsQIgKrHsAxJqhkp8Um88pdnkrZ4pAMyxj.jpg', 1, 0, '2026-05-24 16:03:33', '2026-05-24 16:03:33'),
(71, 'Cementerio Playa Ancha de Noche', 'cementerio-playa-ancha-de-noche-71', 'Puerta del Cementerio 3 de Playa Ancha', '2026-05-30', NULL, NULL, '19:00 a 22:00 hrs.', NULL, 'tour', 1, 'panoramas/TzmLY4ad1WPVABQGtlMN0CXShL3hJgMJUB90pJxV.png', 1, 0, '2026-05-24 16:05:50', '2026-05-24 16:05:50'),
(72, 'Taller “Inteligencia ambiental: Juguetes textiles sostenibles, valorizando un residuo', 'taller-inteligencia-ambiental-juguetes-textiles-sostenibles-valorizando-un-residuo-72', 'Museo de Historia Natural de Valparaíso, 📍 Sala Educativa Carlos Vivar', '2026-05-30', NULL, NULL, '15:00 a 15:45 hrs.', 'https://www.instagram.com/p/DYk9UxLGjAd/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'infantil', 1, 'panoramas/sPv7ck7DWZTyWG36Bs1pLLLSBmQVXjB2HJ7Tc9pH.jpg', 1, 0, '2026-05-24 16:09:42', '2026-05-24 16:09:42'),
(74, '✨ Taller de ilustración “Inteligencia animal: aves grandes constructoras de nidos', 'taller-de-ilustracion-inteligencia-animal-aves-grandes-constructoras-de-nidos-74', 'Museo de Historia Natural de Valparaíso, 📍 Sala Educativa Carlos Vivar', '2026-05-31', NULL, NULL, '15:00 a 16:00 hrs.', 'https://www.instagram.com/p/DYk_sDZmv2b/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'infantil', 1, 'panoramas/V0okXJ5HdpqXsgCTeLwj22UScUAtHLg2tmzESdCz.jpg', 1, 0, '2026-05-24 16:11:32', '2026-05-24 16:11:32'),
(75, 'Ruta Patrimonial Cerro Bellavista', 'ruta-patrimonial-cerro-bellavista-75', 'Ricardo Ferrari con Condor, Cerro Bellavista, Plaza de los Poetas.', '2026-05-30', NULL, NULL, '11:00- 14:00 hrs.', 'https://www.instagram.com/perrosurvalparaiso/', 'tour', 1, 'panoramas/x4iROdOE8qRmBDztlwWTMaarSTCwnQB4mmvoTW2c.jpg', 1, 0, '2026-05-24 16:17:19', '2026-05-24 16:17:19'),
(76, 'BancoEstado, Patrimonio de Chile en Centro Cultural Valparaíso', 'bancoestado-patrimonio-de-chile-en-centro-cultural-valparaiso-76', 'Paseo Yugoslavo, Valparaíso', '2026-05-30', '2026-05-31', NULL, '09:30-16:00 hrs.', 'https://www.diadelpatrimonio.cl/actividad/bancoestado-patrimonio-de-chile-en-centro-cultural-valparaiso', 'exposicion', 1, 'panoramas/iBEgnPLoJrfGjbhnQuCjvIw68BPShkib6XlEudJR.png', 1, 0, '2026-05-24 16:21:46', '2026-05-24 16:21:46'),
(78, 'Día del Patrimonio Cultural en la UPLA', 'dia-del-patrimonio-cultural-en-la-upla-78', 'Casa Central Universidad de Playa Ancha, Av. Playa Ancha 850, Valparaíso', '2026-05-30', NULL, NULL, '10:00 a 17:00 hrs.', 'https://www.diadelpatrimonio.cl/actividad/dia-del-patrimonio-cultural-en-la-upla', 'tour', 1, 'panoramas/e9FbavuukiLPLzRpBVlcPi6RWYdGDnzaAdBsdK1k.png', 1, 0, '2026-05-24 16:33:23', '2026-05-24 16:41:21'),
(79, 'Taller arqueológico para las infancias: Tesoros del Almendro', 'taller-arqueologico-para-las-infancias-tesoros-del-almendro-79', 'Pasaje Fisher 18 y 24 Cerro Concepción, Valparaíso- Calle Urriola', '2026-05-31', NULL, NULL, '13:00-14:00 hrs.', 'https://www.diadelpatrimonio.cl/actividad/tesoros-del-almendro', 'infantil', 0, 'panoramas/JFFcf2LPy0SJh90BLxonpqFomGuMKSkihPKEDSxM.png', 1, 0, '2026-05-24 16:37:14', '2026-05-24 16:37:14'),
(80, 'Recorrido por la Tesorería Regional de Valparaíso: un edificio de conservación histórica', 'recorrido-por-la-tesoreria-regional-de-valparaiso-un-edificio-de-conservacion-historica-80', 'Tesorería General de la República, Prat 765, Valparaíso', '2026-05-30', NULL, NULL, '09:00 - 14:00', 'https://www.diadelpatrimonio.cl/actividad/recorrido-por-la-tesoreria-regional-de-valparaiso-un-edificio-de-conservacion-historica', 'tour', 1, 'panoramas/dmij6K6e6QvyQbpHFEWX1JfL0H9x94tpVcouwR1k.jpg', 1, 0, '2026-05-24 16:39:07', '2026-05-26 22:03:49'),
(81, 'Radio Teatro \"Las Baldosas de la memoria\"', 'radio-teatro-las-baldosas-de-la-memoria-81', 'Pasaje Fisher 24 Cerro Concepción , Valparaíso - Calle Urriola', '2026-05-30', '2026-05-31', NULL, '17:00 a 18:00 hrs.', 'https://www.diadelpatrimonio.cl/actividad/radio-teatro', 'teatro', 1, 'panoramas/44YIAvrrH86G9xNP4qApANseIxeklZZ2HNSOiNCe.png', 1, 0, '2026-05-24 16:46:56', '2026-05-24 16:46:56'),
(82, 'Teatromuseo abre sus puertas en el día del Patrimonio', 'teatromuseo-abre-sus-puertas-en-el-dia-del-patrimonio-82', 'Cumming #795 Valparaiso , Valparaíso- Frente a Plaza Bismark', '2026-05-30', '2026-05-31', NULL, '12:00 a 15:00 hrs.', 'https://www.instagram.com/p/DYm7101DoaO/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'teatro', 1, 'panoramas/k7r4oK6uXNdXp0Ns2f5FPVlMhDvauhq6LKSfxoSV.jpg', 1, 0, '2026-05-24 16:50:36', '2026-05-24 16:50:36'),
(83, 'Ruta & danza // Cuerpos', 'ruta-danza-cuerpos-83', 'Rudolph 241,Valparaíso- Ascensor Espíritu Santo', '2026-05-31', NULL, NULL, '12:00 a 13:00 hrs.', 'https://www.instagram.com/cia.cuerpos', 'danza', 1, 'panoramas/sCwlxTGUmBymnkchleAsdXdDgLJwlCLSzPAfLjlP.png', 1, 0, '2026-05-24 16:53:31', '2026-05-24 16:53:31'),
(84, '\"Valpaisaje\"', 'valpaisaje-84', 'Bahía Utópica Galería de Arte- Almirante Montt 372. Cerro Alegre, Valparaíso', '2026-05-30', '2026-05-31', NULL, '11:00 a 19:00 hrs.', 'https://www.instagram.com/galeriabahiautopica/', 'exposicion', 1, 'panoramas/Wicz6nQST6oZFHFUN0W8xib6bJg9CV54oHzQ4SfC.png', 1, 0, '2026-05-24 16:55:36', '2026-05-24 16:55:36'),
(85, 'Valparaíso Rock Fest', 'valparaiso-rock-fest-85', 'Teatro Municipal de Valparaíso', '2026-05-30', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/valparaisorockfest/', 'musica', 0, 'panoramas/8nW2LIuIpJqCDTVewiSqoUoKl1g52U5qMtzJmmf1.jpg', 1, 0, '2026-05-24 16:58:11', '2026-05-24 16:58:11'),
(86, 'Cineclub Recobrado presenta El húsar de la muerte', 'cineclub-recobrado-presenta-el-husar-de-la-muerte-86', '📍Teatro Municipal de Valparaíso.', '2026-05-29', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DYhwl_YAlKE/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'cine', 1, 'panoramas/aoLj6kkeXLKfC5pOEJpCDWsOYFR4i4dH68Cs4Td0.jpg', 1, 0, '2026-05-24 17:00:29', '2026-05-24 17:00:29'),
(87, 'Ruta: Cementerio de Disidentes Valparaíso', 'ruta-cementerio-de-disidentes-valparaiso-87', 'Dinamarca 14, Valparaíso- Cerro Panteón cementerio disidentes', '2026-05-30', '2026-05-31', NULL, 'Sábado 12:00- 12:50 hrs.  Domingo 11:00 a 11:30 hrs.', 'https://www.diadelpatrimonio.cl/actividad/pasos-historicos', 'tour', 1, 'panoramas/N113GVzVaPV6lab6EF1YjaaLdUWHRxXqkWsGlV2I.jpg', 1, 0, '2026-05-24 17:05:18', '2026-05-24 17:05:18'),
(88, 'Ruta: La historia del centro hospitalario más antiguo de la ciudad', 'ruta-la-historia-del-centro-hospitalario-mas-antiguo-de-la-ciudad-88', 'Hospital Carlos Van Buren- Subida Los Loros, final calle San Ignacio', '2026-05-31', NULL, NULL, '09:00 - 18:00', 'https://www.diadelpatrimonio.cl/actividad/la-historia-del-centro-hospitalario-mas-antiguo-de-la-ciudad', 'tour', 1, 'panoramas/EI7C6QrvBYDD2oIE2movgV3YnvRya0r6MNqr6e7K.png', 1, 0, '2026-05-24 17:08:41', '2026-05-24 17:08:41'),
(89, 'Cuarteles Abiertos Bomberos Valparaíso', 'cuarteles-abiertos-bomberos-valparaiso-89', 'Plaza Sotomayor 147, Valparaíso', '2026-05-31', NULL, NULL, '10:00 a 15:00 hrs.', 'https://www.diadelpatrimonio.cl/actividad/cuarteles-abiertos-bomberos-valparaiso', 'tour', 1, 'panoramas/mfidFADNi36WAO70b6UmGJJ1VHvZWIY4DMK1rObF.png', 1, 0, '2026-05-24 17:10:41', '2026-05-24 17:10:41'),
(90, 'Tour y degustación por la cervecería Greedy Jill', 'tour-y-degustacion-por-la-cerveceria-greedy-jill-90', 'El Granero rojo, F-846, Valparaíso', '2026-05-30', NULL, NULL, '13:30 a 21:30 hrs.', 'https://www.diadelpatrimonio.cl/actividad/tour-y-degustacion-por-la-cerveceria', 'gastronomia', 0, 'panoramas/e0rZqoqoroBOyU0uFGbQO7U3mmv8XSAIHZ826P5l.png', 1, 0, '2026-05-24 17:14:00', '2026-05-24 17:14:00'),
(91, 'Museo Valparaíso Moto Club 112 años de Historia Fotografías y Motos', 'museo-valparaiso-moto-club-112-anos-de-historia-fotografias-y-motos-91', 'Almirante Simpson 71, Valparaíso - Valparaíso Moto Club', '2026-05-30', NULL, NULL, '11:00 a 18:00 hrs.', 'https://www.diadelpatrimonio.cl/actividad/museo-valparaiso-moto-club-112-anos-de-historia-fotografias-y-motos', 'exposicion', 0, 'panoramas/fAg93IdO0elSHGf7U3yqLoM9qBeZCr22NQrzHou4.png', 1, 0, '2026-05-24 17:16:42', '2026-05-24 17:16:42'),
(92, '🎭 Repertorio para habitar la soledad', 'repertorio-para-habitar-la-soledad-92', '📍 Teatro del Parque Cultural de Valparaíso – Ex Cárcel', '2026-05-31', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DYuVaV4CUOu/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'teatro', 0, 'panoramas/DMKzloEqRq1nMRdHZ02xlGyfEcy5GZcCYsiuRR4K.jpg', 1, 0, '2026-05-24 17:19:59', '2026-05-24 17:19:59'),
(93, '“Estado #3” de Soledad Medina y Marta Núñez', 'estado-3-de-soledad-medina-y-marta-nunez-93', 'Parque Cultural de Valparaíso - Ex Cárcel', '2026-05-28', '2026-05-29', NULL, '18:30 hrs.', 'https://www.instagram.com/p/DYkCPnqiSv_/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'danza', 0, 'panoramas/kXiDrgMTgAO63rKt1QZvSyVs9HQ2hrC1SOBJtAFy.jpg', 1, 0, '2026-05-24 17:23:30', '2026-05-24 17:23:30'),
(94, '“Desatino de la Soledad”', 'desatino-de-la-soledad-94', '📍 Sala Estudio, Parque Cultural de Valparaíso – Ex Cárcel', '2026-05-29', '2026-05-31', NULL, 'Viernes y Sábado 19:00 hrs. Domingo 17:00 hrs.', 'https://www.instagram.com/p/DYpL4JHiR4N/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'teatro', 0, 'panoramas/ygvfIeLZfneIwDjGc1JRM8Ds0iI0OBIC26aw5h5Y.jpg', 1, 0, '2026-05-24 17:26:53', '2026-05-24 17:26:53'),
(95, 'Visita guiada a la iglesia Nuestra Señora del Perpetuo Socorro - Cerro Cordillera', 'visita-guiada-a-la-iglesia-nuestra-senora-del-perpetuo-socorro-cerro-cordillera-95', 'San Alfonso 24, Valparaíso', '2026-05-30', '2026-05-31', NULL, 'Sábado 10:00 - 16:00 hrs. Domingo 10:00 - 14:00 hrs.', 'https://www.diadelpatrimonio.cl/actividad/visita-guiada-la-iglesia-nuestra-senora-del-perpetuo-socorro-cerro-cordillera', 'tour', 1, 'panoramas/Na77qqn25JPHFuA6Oei28vsmRakmyjFXKrObzA2I.png', 1, 0, '2026-05-24 17:29:53', '2026-05-24 17:29:53'),
(96, 'Visita al Monumento a la Marina Nacional', 'visita-al-monumento-a-la-marina-nacional-96', 'Plaza Sotomayor S/N, Valparaíso', '2026-05-31', NULL, NULL, '10:00 - 18:00 Horario continuado', 'https://www.diadelpatrimonio.cl/actividad/visita-al-monumento-la-marina-nacional', 'tour', 1, 'panoramas/v9isDC6LlM3aHuoW7lGRAzsaDhWGc4SALS22yan3.png', 1, 0, '2026-05-24 17:32:56', '2026-05-24 17:32:56'),
(97, 'Visita al Edificio Armada de Chile', 'visita-al-edificio-armada-de-chile-97', 'Sotomayor 592, Valparaíso-  Plaza Sotomayor', '2026-05-31', NULL, NULL, '10:00 - 18:00 Horario continuado', 'https://www.diadelpatrimonio.cl/actividad/visita-al-edificio-armada-de-chile', 'tour', 1, 'panoramas/VsbxdknRKyFSKRmjAjnCDR0vwxo8RXrqPKOtOfSw.png', 1, 0, '2026-05-24 17:36:12', '2026-05-24 17:36:12'),
(98, '📸✨ ACCIÓN SALVAJE 5: EL BARRIO SE RETRATA', 'accion-salvaje-5-el-barrio-se-retrata-98', 'Casa Espacio (Buenos Aires #824)', '2026-05-30', NULL, NULL, 'Desde las 11:00 hrs.', 'https://www.instagram.com/p/DYQUzZGksyL/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'exposicion', 1, 'panoramas/oe0y5BKLrZvK1kicl8WOpsA6mU0eLAloRnZlriCz.jpg', 1, 0, '2026-05-24 17:38:22', '2026-05-24 17:38:22'),
(99, '\"ENCONTRARTE\" 8va feria expositiva de artes visuales y oficios', 'encontrarte-8va-feria-expositiva-de-artes-visuales-y-oficios-99', '📍 Explanada superior del Ascensor el Peral, Cerro Alegre, Valparaíso.', '2026-05-30', '2026-05-31', NULL, '10 am - 20 pm.', 'https://www.instagram.com/p/DYaPRMFB9XC/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'arte', 0, 'panoramas/bzcuvUZTiwv8VPHtvVrHhjkUg5yC61Zvf1qN4lIu.webp', 1, 0, '2026-05-24 17:41:38', '2026-05-24 17:41:38'),
(100, 'CERATI Xperience presenta: Bocanada Tour en Trotamundos Valparaíso', 'cerati-xperience-presenta-bocanada-tour-en-trotamundos-valparaiso-100', 'Trotamundos Valparaíso (Blanco 1253, Estación Bellavista)', '2026-05-28', NULL, NULL, 'Acceso desde las 20:00 hrs. El show desde las 22:00 hrs.', 'https://www.passline.com/eventos/cerati-xperience-presenta-bocanada-tour-en-trotamundos-valparaiso', 'musica', 0, 'panoramas/y3318yZUWFf6MapbkvHbnrbjqCQKtHeVqmJse2fp.png', 1, 0, '2026-05-24 17:47:24', '2026-05-24 17:47:24'),
(101, '🎤✨ Los Viking’s 5 en vivo ✨🎤', 'los-vikings-5-en-vivo-101', 'Sala Rivoli – Valparaíso// Calle Victoria N° 2474', '2026-05-30', NULL, NULL, '🕗 Apertura: 20:00 hrs 🎤 Show: 22:30 hrs', 'https://www.instagram.com/p/DXNxi6Pjapb/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/1TKjn7yeFhUY8oGm3f94kccyZ7lz0DJouJ4oueHP.jpg', 1, 0, '2026-05-24 17:53:50', '2026-05-24 17:53:50'),
(102, '🎶 Luis Jara en vivo', 'luis-jara-en-vivo-102', '📍 Sala Rivoli – Valparaíso // Calle Victoria N° 2474', '2026-05-28', NULL, NULL, '🕗 Apertura: 20:00 hrs 🎤 Show: 22:30 hrs', 'https://www.instagram.com/p/DYi6LvUN3eM/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/yLY8pB6mpt6n4lEvBz9BBmboRo9RuiidIaAlamXi.jpg', 1, 0, '2026-05-24 17:55:19', '2026-05-24 17:55:19'),
(103, 'Electric Light Orchestra Tributo Starlight + Fiesta en Trotamundos Valparaíso', 'electric-light-orchestra-tributo-starlight-fiesta-en-trotamundos-valparaiso-103', 'Trotamundos Valparaíso (Blanco 1253, Estación Bellavista)', '2026-05-29', NULL, NULL, '22:00 hrs.', 'https://www.instagram.com/p/DYakELSRl_A/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/0iRXg3wAJ4NlzujRwt1ojEMnFQIdiFslnrk3aj68.jpg', 1, 0, '2026-05-24 18:01:29', '2026-05-24 18:01:29'),
(104, 'Feria Gráfica Sobreimpresiones 2026 💘', 'feria-grafica-sobreimpresiones-2026-104', '📍CENTEX Plaza Sotomayor, Valparaíso', '2026-06-06', '2026-06-07', NULL, '15:00 a 21:00 hrs.', 'https://www.instagram.com/p/DYVoRfujLXI/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'exposicion', 1, 'panoramas/I2wMVFRum434TJqrJdH1ZyW0kwivjSpd7yjvdVAA.jpg', 1, 0, '2026-05-24 18:15:59', '2026-06-04 05:17:57'),
(105, 'ELLA FEST 🌈✨', 'ella-fest-105', 'Trotamundos Valparaíso (Blanco 1253, Estación Bellavista)', '2026-05-13', NULL, NULL, '21:00 hrs.', 'https://www.instagram.com/p/DYifePuGlTu/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/7rVT1aUJR3n5IV2IU2WwBnAPw0I95UJ7rhvWkurp.jpg', 1, 0, '2026-05-24 18:18:06', '2026-05-24 18:18:06'),
(106, '¿Este es el patrimonio que cuidamos?', 'este-es-el-patrimonio-que-cuidamos-106', 'Barrio Puerto | Ascensor Mariposas', '2026-05-30', '2026-05-31', NULL, '10: 30 a 14:00 | 18:00', 'https://www.instagram.com/p/DYs2MuQM-hr/', 'otros', 1, 'panoramas/LXyfhZBSG8a2ML8VdXt5s9u20WLwCAlzsu56xEcF.jpg', 1, 0, '2026-05-24 18:26:30', '2026-05-26 22:07:49'),
(107, 'Chinoy en concierto 🎵', 'chinoy-en-concierto-107', 'Edificio Federico Santa María, Prat 790 Valparaíso', '2026-05-28', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DYk6V9gxOLV/?igsh=dHczMWRjZ3ZqaWMz', 'musica', 1, 'panoramas/ZCwJyTkW6Wkw4LR2qVkcIFnW60k2v3ayWVIcf3TW.jpg', 1, 0, '2026-05-24 20:42:26', '2026-05-24 20:42:26'),
(108, 'Día del Patrimonio Polanco', 'dia-del-patrimonio-polanco-108', 'Ascensor Polanco- Almirante Simpson 171', '2026-05-30', NULL, NULL, '10:30 hrs', 'https://www.instagram.com/p/DYdUYN5pY4L/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'tour', 0, 'panoramas/BCkttUqwpKFqfQ1SlOnlbuUZhjp2P8iSkdi9X9kE.jpg', 1, 0, '2026-05-24 22:54:52', '2026-05-24 22:54:52'),
(110, 'Feria Caleidoscopio', 'feria-caleidoscopio-110', 'Parque Cultural de Valparaíso- Ex cárcel', '2026-05-30', '2026-05-31', NULL, '11:00 a 19:00 hrs', 'https://www.instagram.com/p/DYvA80cEw4E/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'feria', 1, 'panoramas/tYBhHHPm5YnA49tPkBSOfe39gdOTanUWzjYBV6ly.jpg', 1, 0, '2026-05-25 10:32:54', '2026-05-25 10:32:54'),
(111, 'Película Little Miss Sunshine', 'pelicula-little-miss-sunshine-111', 'Cineteca PUCV- Avenida Brasil 2830, Valparaíso', '2026-05-27', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DYve-JIDKCf/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'cine', 1, 'panoramas/yIwKds1NSeH2STRENFqh2x9ANzb0LGcFScxdbiDk.jpg', 1, 0, '2026-05-25 10:52:35', '2026-05-25 10:52:35'),
(112, 'La Ruta del Misterio Porteño', 'la-ruta-del-misterio-porteno-112', NULL, '2026-06-06', NULL, NULL, '11:00 hrs.', 'https://www.instagram.com/larutadelmisteriovalpo?igsh=MXhvNjAwY3hmczJ3Nw==', 'tour', 0, 'panoramas/9f90KXWLLqZY1HElSq2FrkZDZihbg5xg8bKRLya7.jpg', 1, 0, '2026-05-25 14:34:29', '2026-05-29 11:01:52'),
(113, 'Fiesta SKA - LA COMBOS VIENEN XL vol2!!!!📢 💥💥', 'fiesta-ska-la-combos-vienen-xl-vol2-113', 'Casa Cultural Marx , Valparaíso - Calle Ecuador #12', '2026-06-05', NULL, NULL, '22:00 hrs.', 'https://www.instagram.com/p/DYQTQXDFJd3/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'musica', 0, 'panoramas/y8SaUYO8r2bK0SIn1U2VQARmURFzYxBEsYKuyDJf.jpg', 1, 0, '2026-05-25 14:38:18', '2026-05-25 14:38:18'),
(114, 'Cumbia Chicha en vivo', 'cumbia-chicha-en-vivo-114', 'Terraza Miaw, Almirante Montt 109, Valparaíso', '2026-05-30', NULL, NULL, '22:00 hrs.', 'https://www.instagram.com/p/DYvKpm6CdnB/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'musica', 0, 'panoramas/fWlNy5povbD6eYwLiThhD3NsUXXMMJB3tTHNklct.jpg', 1, 0, '2026-05-25 19:05:05', '2026-05-25 19:05:05'),
(115, 'Tour a la comunidad: Naufragios', 'tour-a-la-comunidad-naufragios-115', 'Plaza Sotomayor', '2026-05-27', NULL, NULL, '11:30 hrs.', 'https://www.instagram.com/p/DYxEvHcjr7t/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'tour', 1, 'panoramas/YFclBVErJAYohNeG7BPEJyctrVEt0m8M8MYewWiY.jpg', 1, 0, '2026-05-25 19:08:48', '2026-05-25 19:08:48'),
(116, 'Apianás las cuecas', 'apianas-las-cuecas-116', 'Bar Liberty- Almirante Riveros 9, Valparaíso', '2026-06-06', NULL, NULL, '20:00 hrs.', 'https://www.instagram.com/p/DYva__HMozR/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'musica', 0, 'panoramas/qawNyrWjFG25RuUXqdDapwNDSKcvnzKYxUQgc8Y9.jpg', 1, 0, '2026-05-25 19:15:46', '2026-05-25 19:15:46'),
(117, 'BERSUIT VERGARABAT  🔥 HIJOS DEL CULO 🔥 25 AÑOS', 'bersuit-vergarabat-hijos-del-culo-25-anos-117', 'Trotamundos Valparaíso, Blanco 253', '2026-07-25', NULL, NULL, '22:00 hrs.', 'https://www.instagram.com/p/DYVftcBRf6y/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'musica', 0, 'panoramas/IcHJPTIWyQxEOClT7KdQqkrEvOGZjwsowRvf5rqQ.jpg', 1, 0, '2026-05-25 19:52:21', '2026-05-25 19:52:21'),
(118, 'LUCHA LIBRE VLL ORÍGENES 🤼', 'lucha-libre-vll-origenes-118', 'Polideportivo Tranque Seco', '2026-06-06', NULL, NULL, '15:00', 'https://www.instagram.com/p/DYNVlQrPj79/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'otros', 0, 'panoramas/pYeYKjWqaDI33gU4ytuTY18PguFlQfPA0JwkCH1F.jpg', 1, 0, '2026-05-25 20:01:59', '2026-06-03 00:38:29'),
(119, '🔵 Bienvenidos a bordo del submarino azul ⚓️ Casa/Museo Gonzalo Ilabaca', 'bienvenidos-a-bordo-del-submarino-azul-casamuseo-gonzalo-ilabaca-119', '📍Cerro Playa Ancha', '2026-05-26', '2026-09-30', '[1,2,3,4,5,6,7]', 'Reservas y tarifas al @el_submarino_azul', 'https://www.instagram.com/el_submarino_azul?igsh=NWFtcWgxOG9wMTYx', 'exposicion', 0, 'panoramas/Vljih3TFM6IvAtuSTsNAUfEKLH3VSWOoFRmNUJsx.jpg', 1, 0, '2026-05-26 00:22:57', '2026-06-09 13:40:26'),
(121, 'Ex Comisaría Barón', 'ex-comisaria-baron-121', 'Ex Comisaría Barón, Setimio 131 Cerro Barón.', '2026-05-31', NULL, NULL, '14:00 a 17:00 hrs', 'https://www.instagram.com/p/DY0a_AExZc4/', 'otros', 1, 'panoramas/xcQWSULL4mOOpnn0V33HmyeHcHaEfq1Dqut5xdbG.webp', 1, 0, '2026-05-28 14:00:48', '2026-05-28 14:00:48'),
(122, 'Ruta Patrimonial Cerro Bellavista', 'ruta-patrimonial-cerro-bellavista-122', 'Plaza de los poetas - Ricardo de Ferrari 725, Valparaiso', '2026-05-30', '2026-05-30', NULL, '11:00 hrs.', 'https://www.instagram.com/p/DYyLiZ1MaHQ/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'tour', 1, 'panoramas/ZfgqbGblff93fFvb4Eg3vFb4IXbdg38WXvrpWw5a.jpg', 1, 0, '2026-05-29 03:07:13', '2026-05-29 03:07:13'),
(123, 'Ruta Patrimonial Barrio Puerto', 'ruta-patrimonial-barrio-puerto-123', 'Plaza Sotomayor', '2026-05-31', NULL, NULL, '10:30 hrs.', 'https://www.instagram.com/p/DYxOHtXNZ_T/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'tour', 1, 'panoramas/kP3vFLzT0dujwIDPXJC9C7X8oCfaaruTQ2VBEw4L.jpg', 1, 0, '2026-05-29 03:14:54', '2026-05-29 03:14:54'),
(124, 'Feria Bohemia Porteña', 'feria-bohemia-portena-124', 'Explanada Mercado Puerto', '2026-06-06', NULL, NULL, '13:00hrs - 21:30 hrs.', 'https://www.instagram.com/p/DY3P-RmFIUa/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'feria', 1, 'panoramas/XHvtHWrf240crlDuc38cpi2HHEUJBGjJH9RtPJ6O.jpg', 1, 0, '2026-05-29 03:22:38', '2026-05-29 03:22:38'),
(125, 'FIESTA INVERNAL NOCTÁMBULA 🌟', 'fiesta-invernal-noctambula-125', 'Casa Plan, Avenida Brasil 1490, Valparaíso', '2026-06-06', NULL, NULL, '19:00 a 01:00 hrs.', 'https://www.instagram.com/p/DY2l6hlCVUr/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'musica', 0, 'panoramas/Btt7NBj6NyBAx4ZSixbvihSCcp1XOEctbZVarg4f.jpg', 1, 0, '2026-05-29 03:35:48', '2026-05-29 03:35:48'),
(126, 'Feria Artesanos de Plaza Sotomayor', 'feria-artesanos-de-plaza-sotomayor-126', 'Plaza Sotomayor', '2026-05-29', '2026-05-31', NULL, '12:00 a 18:00 hrs.', 'https://www.instagram.com/p/DY1fCi4AeuN/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'feria', 1, 'panoramas/BQu3h8m7SxhagLZ3VFCKXetfZlOgEHYvpsXjVOmJ.jpg', 1, 0, '2026-05-29 03:42:36', '2026-05-29 03:42:36'),
(127, 'Lanzamiento Ecos del Futuro Vol.2 🎧🌴', 'lanzamiento-ecos-del-futuro-vol2-127', 'Museo de Historia Natural de Valparaíso- Condell 1546', '2026-06-04', NULL, NULL, '15:30 hrs.', 'https://www.instagram.com/ecosdelfuturo.cl?igsh=MTUweThiZGg4ZHJubQ==', 'musica', 0, 'panoramas/sLDUHLCACqeAmNpVqrribo9HVNOt4YrXWLDfJaeJ.jpg', 1, 0, '2026-05-29 03:51:37', '2026-05-29 03:51:37'),
(128, 'NOCHE DE SALSA 💃🏽 EN EL LIBERTY', 'noche-de-salsa-en-el-liberty-128', 'Bar Liberty - Almirante Riveros #9, Valparaíso', '2026-06-12', NULL, NULL, '21:30 hrs.', 'https://www.instagram.com/p/DY08UwmxMRm/?igsh=dWJ5Zm13NnVyejk5', 'musica', 0, 'panoramas/uOkwgccGzCjL9aEEPgY5OapAuoYxgkkA3Fl7sgLC.jpg', 1, 0, '2026-05-29 03:56:58', '2026-05-29 03:56:58'),
(129, 'Festival Cervezas de invierno 2026 🎵🍻🍺', 'festival-cervezas-de-invierno-2026-129', 'Terminal de Pasajeros de Valparaíso- Acceso Estación Francia', '2026-06-27', NULL, NULL, '15:00 hrs.', 'https://www.passline.com/eventos/festival-cervezas-de-invierno-2026?srsltid=AfmBOopEhFJEzRj9JjMZw8iipiHOUTbc3VjmQPPqXiOUr8Zje0OjEhcq', 'musica', 0, 'panoramas/YPAeuTqY5ckWIQYu4KJDpsM2EnGXXeXpFm5K251r.jpg', 1, 0, '2026-05-29 11:26:32', '2026-05-29 11:26:32'),
(131, 'Los Juanitos - Tributo a Chancho en Piedra', 'los-juanitos-tributo-a-chancho-en-piedra-131', 'Trotamundos Valpo', '2026-06-04', NULL, NULL, 'Apertura de puertas 20:00 hrs. Show 22:00 hrs.', 'https://www.instagram.com/p/DY8NHmSEbvp/?igsh=MXc0dHI2ejJndHpxMQ==', 'musica', 0, 'panoramas/tgkDzToVoPPZiDMnk30HvGABuzr0ry9v5pbED9p3.jpg', 1, 0, '2026-05-30 20:05:08', '2026-05-30 23:03:13'),
(132, 'JOHN WILLIAMS Un Homenaje Sinfónico 🎼', 'john-williams-un-homenaje-sinfonico-132', 'Aula magna Federico Santa María- Avenida España 1680', '2026-06-13', NULL, NULL, '19:00 hrs.', 'https://www.passline.com/eventos-plano/john-williams-un-homenaje-sinfonico-teatro-aula-magna-usm-valparaiso', 'musica', 0, 'panoramas/ITEiq4uGuaZdf0xxv2iQiwDUJo3SIvyboJsiQQgW.jpg', 1, 0, '2026-05-30 23:01:27', '2026-05-30 23:01:27'),
(133, 'SHOW DE COMEDIA, CONCURSOS Y MÚSICA', 'show-de-comedia-concursos-y-musica-133', 'Terraza Miauw- Almirante Montt 109', '2026-06-04', NULL, NULL, '20:00 hrs.', 'https://www.instagram.com/p/DY0SbNJFiPZ/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', NULL, 1, 'panoramas/gsg3WMiGPkJxJwyaYLzlOG6o0v7hPc027bSifJjg.jpg', 1, 0, '2026-05-31 13:20:44', '2026-05-31 13:20:44'),
(134, '🔥✨ Desestresadas – Tributo a Romeo Santos ✨🔥', 'desestresadas-tributo-a-romeo-santos-134', 'Sala Rivoli – Valparaíso ⚓🏛️', '2026-06-04', NULL, NULL, '🕗 Apertura: 20:00 hrs 🎤 Show: 22:30 hrs', 'https://www.instagram.com/p/DY-pFfvPKTR/?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', 'musica', 0, 'panoramas/rNwddo9ky0kyy6kbQoH0m3SFvbkkhYD6vHs4ybVh.jpg', 1, 0, '2026-05-31 13:26:48', '2026-06-04 03:29:08'),
(135, 'El ciudadano Kane', 'el-ciudadano-kane-135', 'Cineteca PUCV , Avda.Brasil 2830 Valparaíso', '2026-06-03', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DZBeW_xMpAI/?igsh=MWJoNHcyaDd0ZWpoZw==', 'cine', 0, 'panoramas/LR6hCYGkZkTMKDXfaPeEsUSJypO2ddBW78LmI6aF.jpg', 1, 0, '2026-06-01 02:51:04', '2026-06-01 02:51:04'),
(137, 'Teatro familiar “La ratita presumida”', 'teatro-familiar-la-ratita-presumida-137', 'Teatro IPA- Condell 1349, Valparaíso', '2026-06-13', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DZA0BawS8xV/?igsh=YjFwM2JlZ3V0emE2', 'teatro', 0, 'panoramas/CHHJGstw2Df9KPOfEbAuNkqFtpVOgRtuOXu3swxB.jpg', 1, 0, '2026-06-01 23:54:32', '2026-06-01 23:54:32'),
(139, 'Arte terapia - Taller de muñecas  ✨', 'arte-terapia-taller-de-munecas-139', 'Héctor Calvo 84, Cerro Bellavista , Valparaíso', '2026-06-13', NULL, NULL, '11:00 hrs.', 'https://www.instagram.com/p/DY7g2piEUil/?igsh=MTZ5OXIyZ3d2b2Jnaw==', 'taller', 0, 'panoramas/RbZciDXyG7kn1NAOHHTsP7lEusbF1CI9LmLgZ8Ly.jpg', 1, 0, '2026-06-02 00:01:29', '2026-06-02 00:01:29'),
(140, 'Ateísmo, Cristianismo y Ciencia', 'ateismo-cristianismo-y-ciencia-140', 'MFE, Almirante Barroso 558', '2026-06-05', NULL, NULL, '19:00 hrs', 'https://www.instagram.com/cuestionando_el_cristianismo/', 'otros', 1, 'panoramas/RFGJmNPHtnNSnj4ATxdDgC5vX3mU9iT6HI6xBkjk.jpg', 1, 0, '2026-06-02 21:42:38', '2026-06-02 21:42:38'),
(141, 'Inti Illimani en tu barrio 🎵', 'inti-illimani-en-tu-barrio-141', 'Trotamundos - Blanco 253, Valparaíso', '2026-06-06', NULL, NULL, '20:00 hrs.', 'https://www.instagram.com/p/DZGze41ht6g/?igsh=eWI0OWhsOTZ1Ym9s', 'musica', 0, 'panoramas/8ZFy1gnqj5MySEUXkUSRZwfDnP0onaT8VLQVj58h.jpg', 1, 0, '2026-06-03 11:37:00', '2026-06-03 11:37:00'),
(142, 'Noche Psicotropika 🎵', 'noche-psicotropika-142', 'Terraza Miaw- Almirante Montt 109, Valparaíso', '2026-06-06', NULL, NULL, '21:00 hrs', 'https://www.instagram.com/p/DYszoYwjfz6/?igsh=cDB3ZHZ0aDhwcWdp', 'musica', 1, 'panoramas/adz8p78VpGSFPmCSPt7WwgO4MDcURAE2tNOIEM56.jpg', 1, 0, '2026-06-03 11:41:12', '2026-06-03 11:41:12'),
(143, 'PUERTO SKA JAZZ QUINTETO', 'puerto-ska-jazz-quinteto-143', 'Cervezocracia- Blanco 398, Valparaíso', '2026-06-13', NULL, NULL, '22:00 hrs.', 'https://www.instagram.com/p/DZGl2nQnLtI/?igsh=YzJpMmF6bW1yaWsy', 'musica', 1, 'panoramas/W95s1uJvfIKN5AU8S2cNMeLo2oF47fO2rhn8sTNV.jpg', 1, 0, '2026-06-03 11:46:59', '2026-06-03 11:46:59'),
(144, 'Fiesta del Sol 5534', 'fiesta-del-sol-5534-144', 'Pasacalle Convite', '2026-06-13', NULL, NULL, '15:00 hrs.', 'https://www.instagram.com/p/DZG5w91tGJZ/?igsh=MTJtd2UwN3gwbDdocw==', 'musica', 1, 'panoramas/azPnyfIvCyfeXdmej1wQJAjzd8islLJd5efkLrJh.jpg', 1, 0, '2026-06-03 11:50:49', '2026-06-03 11:50:49'),
(145, 'Valparaíso Paisano', 'valparaiso-paisano-145', 'Bar Verde Absenta - Avenida Brasil 1830, Valparaíso', '2026-06-05', NULL, NULL, '21:00 hrs.', 'https://www.instagram.com/p/DZESR5Lu7gh/?igsh=Z2E5Z3U5ZnFmMzl4', 'musica', 1, 'panoramas/6A154jLT5E3WfqHnmkR1FZxw2O2vtFJg2LDP06ai.jpg', 1, 0, '2026-06-03 11:56:34', '2026-06-03 11:56:34'),
(146, 'Sergio Aguilera interpretando joyas de la bohemia porteña.', 'sergio-aguilera-interpretando-joyas-de-la-bohemia-portena-146', 'Bar El Canario- Cumming 140 , Valparaíso', '2026-06-04', NULL, NULL, '21:00 hrs.', 'https://www.instagram.com/p/DZGBkWNv1-U/?igsh=YjJheG11ZDA4bmY2', 'musica', 1, 'panoramas/8CyOnkWDF0ujgu5xEmUSgElZxVcYukq77zvQKEQO.jpg', 1, 0, '2026-06-03 12:00:58', '2026-06-03 12:00:58'),
(147, '🚒Exposición Bomba Americana -  1era Compañía de Bomberos', 'exposicion-bomba-americana-1era-compania-de-bomberos-147', 'Plaza Destino Valparaíso - Concepción 499, Valparaíso', '2026-06-06', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DZItwnwmWJz/?igsh=a2NhcWZiZ2Z1bG5l', 'exposicion', 1, 'panoramas/iC1Oq5cNi4e3w9ihgBRGF5zrOnefIlJVdEnVy5Bl.jpg', 1, 0, '2026-06-03 22:57:41', '2026-06-03 23:03:38'),
(148, 'Apari y Likanantay', 'apari-y-likanantay-148', 'La Pará Kultural- Serrano 452, Valparaíso', '2026-06-06', NULL, NULL, '19:00 hrs.', NULL, 'musica', 1, 'panoramas/LpSeurxbnsGWlmDxKWDbYjCjZvIWXlXC1bO8L17h.jpg', 1, 0, '2026-06-03 23:00:50', '2026-06-03 23:00:50'),
(149, 'Ciclo Rock Oscuro', 'ciclo-rock-oscuro-149', 'Cervezocracia - Blanco 398, Valparaíso', '2026-06-12', NULL, NULL, '22:00 hrs.', 'https://www.instagram.com/p/DZIymbTvzAk/?igsh=czFvOHB0NWJicDE=', 'musica', 1, 'panoramas/rf4DlS9u3psboCDjmAq4vYOfG7PXWlCCOEUFEjCF.jpg', 1, 0, '2026-06-03 23:03:07', '2026-06-03 23:03:07'),
(150, 'Especial The Cure', 'especial-the-cure-150', 'Máscara - Plaza Aníbal Pinto 1178', '2026-06-06', NULL, NULL, '23:00 hrs', 'https://www.instagram.com/p/DZGnGc-TiC9/?igsh=MXZ3dGNtcjh5ZnNnMg==', 'musica', 0, 'panoramas/483BmP1UxY3n0tZ34Epu3MaedmdlaTfMPCXZH9FC.jpg', 1, 0, '2026-06-03 23:06:14', '2026-06-03 23:06:14'),
(151, 'Domingos dominicales : Marco Antonio Solís', 'domingos-dominicales-marco-antonio-solis-151', 'Sala Rivoli - Victoria 2474, Valparaíso', '2026-06-07', NULL, NULL, '14:00 hrs.', 'https://www.instagram.com/p/DZGMrsfvAZt/?igsh=aTg1enhqeDFmYzU2', 'musica', 0, 'panoramas/0kooN5FFmMUESsggJc6uLsaXgGIROrYYuea5KsvD.jpg', 1, 0, '2026-06-03 23:29:00', '2026-06-03 23:29:00'),
(152, 'ARTEPUERTO 🖼️EXPOSICIÓN COLECTIVA DE ARTES VISUALES', 'artepuerto-exposicion-colectiva-de-artes-visuales-152', 'MERCADO PUERTO / COCHRANE 117, BARRIO PUERTO, VALPARAISO', '2026-06-01', '2026-07-03', '[1,2,3,4,5,6,7]', '11:00 a 17:00 hrs.', 'https://artepuerto-475rmrxh.manus.space/', 'exposicion', 1, 'panoramas/udiKAqhM1DCm7rCUEsc3TbYVK2EjlAe5fqcIhc7O.jpg', 1, 0, '2026-06-03 23:37:04', '2026-06-09 13:41:07');
INSERT INTO `panoramas` (`id`, `titulo`, `slug`, `ubicacion`, `fecha`, `fecha_fin`, `dias_semana`, `hora`, `enlace`, `categoria`, `es_gratuito`, `imagen`, `activo`, `orden`, `created_at`, `updated_at`) VALUES
(153, 'Recorrido a la mar y ofrendas para las aguas', 'recorrido-a-la-mar-y-ofrendas-para-las-aguas-153', 'Playa San Mateo', '2026-06-06', NULL, NULL, '13:30 hrs.', 'https://www.instagram.com/p/DZBCJxpHN7m/?igsh=cWwyNjJodzNiN3o4', 'tour', 1, 'panoramas/gjUgUohl7ZVtQkxDbXGer3jHpJ9uyO0KCmYCjBci.jpg', 1, 0, '2026-06-04 21:57:26', '2026-06-04 21:57:26'),
(154, 'Juan Pirquinero y el diablo', 'juan-pirquinero-y-el-diablo-154', 'Teatro Museo , Cumming 795, Valparaíso', '2026-06-07', NULL, NULL, '16:30 hrs.', 'https://www.instagram.com/p/DZK8PLbjhtL/?igsh=MThtaGh4YXM4ZTc3Mw==', 'teatro', 0, 'panoramas/4sku5SIfsDDqco4nxqoXvRFfsY4c9YgynrlRYJGI.jpg', 1, 0, '2026-06-04 22:01:14', '2026-06-04 22:01:14'),
(155, 'Tapas & Copas V.01', 'tapas-copas-v01-155', '17 locales de Cerro Alegre y Cerro Concepción', '2026-06-12', NULL, NULL, 'Desde las 18:00 hrs.', 'https://www.instagram.com/p/DZI9dGClWdu/?igsh=cHY3YW1mOWNnc29w', 'gastronomia', 0, 'panoramas/REk4VNyPXnxNka5f7zXcTL2tU2FPqjsYEh1Uws32.jpg', 1, 0, '2026-06-04 22:05:00', '2026-06-04 22:05:00'),
(157, 'Retazo- Palomillando Teatro 🎭', 'retazo-palomillando-teatro-157', 'Centro cultural Casa Polanco - Cicarelli 41, Polanco , Valparaíso', '2026-06-06', NULL, NULL, 'Desde las 16:00 hrs.', 'https://www.instagram.com/p/DZGjgVNFbkc/?igsh=cHdzNHRyMTgzYWFt', 'teatro', 0, 'panoramas/fX8C6rfcWOX2og2kgN3e5fKGRoDTeV1PDeAmRr7V.jpg', 1, 0, '2026-06-04 22:17:49', '2026-06-04 22:17:49'),
(158, 'Homenaje a Frank Sinatra 💎', 'homenaje-a-frank-sinatra-158', 'Estrella Negra Club de Jazz- Carrera 440, Valparaíso', '2026-06-12', NULL, NULL, 'Apertura 19:00 hrs.', 'https://www.instagram.com/p/DZJA4AHmORV/?igsh=MThpd3RyZ3hmNjZ0aw==', 'musica', 0, 'panoramas/TvxhEofEiArE65AQs4Sk76hcDnudbRSCxusjUAth.jpg', 1, 0, '2026-06-04 22:23:53', '2026-06-04 22:23:53'),
(159, 'Ciclo de cine. Enfoque Libre: «The Truman Show»Si', 'ciclo-de-cine-enfoque-libre-the-truman-showsi-159', 'FPP Valparaíso, Prat 887, 5° piso', '2026-06-09', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DZKpfblAFo-/?igsh=ODR5enI5cjR0bjlz', 'cine', 1, 'panoramas/74QmU6LeOfTBBOnWqaSplKB8wLehi47k8BHeTN9c.jpg', 1, 0, '2026-06-04 22:33:30', '2026-06-04 22:35:17'),
(160, 'Festival de Mujeres y Disidencias La Colorina', 'festival-de-mujeres-y-disidencias-la-colorina-160', 'Centro Cultural Casa Polanco , Cicarelli 41 , Polanco, Valparaíso', '2026-06-06', NULL, NULL, 'A partir de las 16:00 hrs.', 'https://www.instagram.com/p/DYdYEyQsRy3/?igsh=eGx0dmJ6dTYxb2ts', 'musica', 0, 'panoramas/vb4OSyYK5TaKDs64nBzM1YgWmqU7Tqoxx4T727Yh.jpg', 1, 0, '2026-06-05 11:37:34', '2026-06-05 11:37:34'),
(161, 'Cuánto dueles Palestina', 'cuanto-dueles-palestina-161', 'Parque Cultural de Valapraíso, Cerro Cárcel 471, Valparaíso', '2026-06-06', '2026-07-05', '[1,2,3,4,5,6,7]', '10:00 a 19:30 hrs.', 'https://www.instagram.com/p/DY90g4eCdgd/?igsh=Y3FydDNjMzdzdDBv', 'exposicion', 1, 'panoramas/cafZgIrFN8g2NFfBw4oagI3Oun5KtfwkXLYpZQMn.jpg', 1, 0, '2026-06-05 11:51:10', '2026-06-09 13:42:31'),
(162, 'Viudas del Mar', 'viudas-del-mar-162', 'Parque Cultural Valparaíso , Cerro Cárcel 471', '2026-06-06', '2026-06-07', NULL, '18:00 hrs.', 'https://www.instagram.com/p/DY90cvXFXgs/?img_index=3&igsh=Y3Y4b3NvNXlicXdk', 'danza', 0, 'panoramas/9FjgbNpWh6K3uVWRvhlsNqtqqCbAHRjtmTrK8XsT.jpg', 1, 0, '2026-06-05 12:07:08', '2026-06-05 12:07:08'),
(163, 'Valparaíso como modernización fallida. Una aproximación antipatrimonialista', 'alparaiso-como-modernizacion-fallida-una-aproximacion-antipatrimonialista-163', 'Online', '2026-06-08', NULL, NULL, '14:30', 'https://www.instagram.com/p/DZIWEI_ODk-/', 'conferencia', 1, 'panoramas/BbW4DmroM9NUsFtfOW4S5YHxovZOIIKF34aWWx93.jpg', 1, 0, '2026-06-08 16:07:26', '2026-06-08 16:07:44'),
(164, 'TALLER CIANOTIPIA 📷', 'taller-cianotipia-164', 'Papudo #612, Cerro Concepción, Valparaíso', '2026-06-14', '2026-06-14', NULL, '🕓 12:00 hrs (duración: 3 horas)', 'https://www.instagram.com/p/DZF9OGxjGMC/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'taller', 0, 'panoramas/6CIVmAdeHARaPP91F9Lo6GUysH0Xkzn9IFV8x6Z4.jpg', 1, 0, '2026-06-09 00:49:47', '2026-06-09 00:49:47'),
(165, '5TA VERSIÓN SUMMIT 🤖  INTELIGENCIA ARTIFICIAL APLICADA', '5ta-version-summit-inteligencia-artificial-aplicada-165', 'Centro de Extensión Duoc UC Sede Valparaíso, Av. Errázuriz 1020, Valparaíso', '2026-06-11', NULL, NULL, '🕤 09:30 horas', 'https://www.instagram.com/p/DZVE4ffDE4D/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'conferencia', 1, 'panoramas/9DocXk8eBWyWsqnAi3zmXtmOwx8SY10c26LM4KS0.jpg', 1, 0, '2026-06-09 01:02:11', '2026-06-09 01:10:20'),
(166, '\"García Lorca en un espejo roto\"', 'garcia-lorca-en-un-espejo-roto-166', 'Centro de Extensión Duoc UC Sede Valparaíso, Av. Errázuriz 1020, Valparaíso', '2026-06-13', NULL, NULL, '19:00 hrs.', 'https://www.instagram.com/p/DZM_1e7ik8C/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'teatro', 1, 'panoramas/5wzIi2t84Iho9Cem0tvIl7yJghqTnOCpffaI6584.jpg', 1, 0, '2026-06-09 01:11:25', '2026-06-09 01:11:25'),
(167, 'Concierto Paz Miranda', 'concierto-paz-miranda-167', 'Centro de Extensión Duoc UC Sede Valparaíso, Av. Errázuriz 1020, Valparaíso', '2026-06-19', NULL, NULL, '19:00 hrs.', 'https://www.instagram.com/p/DZM_1e7ik8C/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/3HUmbAwfrivkMzUeXGzRp4cZaTw20egkqHEEmedF.jpg', 1, 0, '2026-06-09 01:13:57', '2026-06-09 01:13:57'),
(168, 'STAND-UP COMEDY Toto Giglio y Frank', 'stand-up-comedy-toto-giglio-y-frank-168', 'El pimentón - Ecuador 27, Valparaíso', '2026-06-11', NULL, NULL, 'Desde las 19:00 hrs.', 'https://www.instagram.com/p/DZV-BLzRx_g/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'standup', 1, 'panoramas/yeIEHGQwKLNKzSIi88xQJpeURTqr4C8XAXORBDZU.webp', 1, 0, '2026-06-09 01:17:36', '2026-06-11 14:33:38'),
(169, 'SESIÓN EN VIVO EP Eta Karinae', 'sesion-en-vivo-ep-eta-karinae-169', 'Cervezocracia - Blanco 398, Valparaíso', '2026-06-11', NULL, NULL, '21:00 hrs.', 'https://www.instagram.com/p/DZVosPuRg-R/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 1, 'panoramas/9eNfMYhaMlR9dgssjGeDkwLr3FKfhwxS66Oh6KPA.jpg', 1, 0, '2026-06-09 01:19:14', '2026-06-09 01:19:14'),
(170, '✨ Entre faroles y bohemia ✨', 'entre-faroles-y-bohemia-170', 'La Pará Kultural- Serrano 452, Valparaíso', '2026-06-13', NULL, NULL, 'Desde las 20:30 hrs', 'https://www.instagram.com/p/DZVX69gRlrR/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/ICpVjBKLiAcLJo8q17oep75ejSPa1xXVV7b0iohP.webp', 1, 0, '2026-06-09 01:20:46', '2026-06-09 01:20:46'),
(171, 'Música Latinoamericana Nico Orlandi', 'musica-latinoamericana-nico-orlandi-171', 'Gato en la Ventana, Cumming 113', '2026-06-12', NULL, NULL, '22:00 hrs', 'https://www.instagram.com/p/DZVWDY6Rtnx/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 1, 'panoramas/56mooE0ybowXRW5U2n0QT2u61hOTGbsG1WVPUT53.webp', 1, 0, '2026-06-09 01:22:34', '2026-06-09 01:22:34'),
(172, '🌈🏳️‍🌈 ELLA FEST ♥️💙💜💛💚🧡🩷🤍', 'ella-fest-172', 'Trotamundos Valparaíso (Blanco 1253, Estación Bellavista)', '2026-06-13', NULL, NULL, '23:00 hrs.', 'https://www.passline.com/eventos/ella-fest-valparaiso', 'musica', 0, 'panoramas/yMZDehfdsFPts3n9H4c4akm6LXAX5maTocFmANKC.jpg', 1, 0, '2026-06-09 01:25:36', '2026-06-09 01:25:36'),
(173, 'SONORA DE LLEGAR', 'sonora-de-llegar-173', 'Casona Barón — Blanco Viel 327, Valparaíso', '2026-06-14', NULL, NULL, 'SONORA DE LLEGAR', 'https://www.instagram.com/p/DZT1-jlkcNl/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/esNCdV0Gsl5bCfLwXbz0tx9FsrdXwetOfeU2QKuY.jpg', 1, 0, '2026-06-09 01:30:11', '2026-06-09 01:30:11'),
(174, 'TRIBUTO DAVID BOWIE', 'tributo-david-bowie-174', 'Trotamundos Valparaíso (Blanco 1253, Estación Bellavista)', '2026-06-27', NULL, NULL, '22:00 hrs', 'https://www.instagram.com/p/DZQC25Axrko/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/FlEQ7i5NENhzOSBpu7gxOYK9wiy5eNlQwzfgbS0M.jpg', 1, 0, '2026-06-09 01:31:35', '2026-06-09 01:31:35'),
(175, 'Concierto Conmemorativo- Agrupación de Cámara de la Universidad de Valparaíso', 'concierto-conmemorativo-agrupacion-de-camara-de-la-universidad-de-valparaiso-175', 'Aula Magna Victorio Pescio Vargas, Facultad de Derecho UV (Errázuriz 2120, Valparaíso)', '2026-06-12', NULL, NULL, '12:00 HRS.', 'https://www.instagram.com/p/DZSunjEgnu-/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/eQiTWkxvP8mtnb7P5KGtPn973FS9tJcv3c5v65NU.jpg', 1, 0, '2026-06-09 01:33:10', '2026-06-09 01:33:10'),
(176, 'TALLER DE TEATRO AQUÍ/AHORA', 'taller-de-teatro-aquiahora-176', 'LA SEDE (SUBIDA ECUADOR #57)', '2026-06-10', NULL, '[3]', '19:00 hrs.', 'https://www.instagram.com/p/DZAje8ISfg9/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'teatro', 0, 'panoramas/d0BZmXM4zTrjZbwZx8RqSYXkHbG0msxxT6jRk6mu.webp', 1, 0, '2026-06-09 01:36:43', '2026-06-09 01:36:43'),
(177, 'Crónicas policiales de Valparaíso', 'cronicas-policiales-de-valparaiso-177', 'Microteatro del CCDV, Quilpué', '2026-06-11', NULL, NULL, '19:00 hrs.', 'https://www.instagram.com/p/DZU1ZS9A29t/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'otros', 1, 'panoramas/rW8Ab8a9M6xNb04YNLWv9piAjr95FXk0vexso95H.jpg', 1, 0, '2026-06-09 01:39:06', '2026-06-09 01:39:06'),
(178, '3ER ENCUENTRO DE SEGURIDAD NÁUTICA', '3er-encuentro-de-seguridad-nautica-178', 'Museo Marítimo Nacional, 21 de mayo, Valparaíso', '2026-06-13', NULL, NULL, '09:00 - 14:00 hrs.', 'https://www.instagram.com/p/DZVDod3NE8I/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'otros', 1, 'panoramas/lq86PreUBNbie7KyTfM95WNtU9CdudbubCLcaFDy.jpg', 1, 0, '2026-06-09 01:41:34', '2026-06-09 01:41:34'),
(179, '🤸🏽‍♂️Creatividades desde el lenguaje del breaking 🤸🏽‍♂️', 'creatividades-desde-el-lenguaje-del-breaking-179', 'Santa Inés #38, cerro Barón. Valparaíso', '2026-06-20', NULL, NULL, '15:00 a 20:00 hrs.', 'https://www.instagram.com/p/DZTfdGhgItI/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'taller', 0, 'panoramas/kKkzW4fPJzzOrjW8tMJ5EzGG1oaAoCYbanJtwX0w.webp', 1, 0, '2026-06-09 01:47:35', '2026-06-09 01:47:35'),
(180, '📚Tres postales tres. Bolaño, Lemebel, Piglia [Entrevistas]', 'tres-postales-tres-bolano-lemebel-piglia-entrevistas-180', 'Fisher 18, Escalera de Colores, Cerro Concepción, Valparaíso', '2026-06-11', NULL, NULL, '18:30 hrs.', 'https://www.instagram.com/p/DZWHBSYjYp1/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'otros', 1, 'panoramas/NyChn0z0FEAVJ3zISZFS1FjFAZvkJrswjazmtlEx.jpg', 1, 0, '2026-06-09 01:59:54', '2026-06-09 01:59:54'),
(181, 'Documental En Tránsito (2017)', 'documental-en-transito-2017-181', 'Biblioteca Santiago Severin', '2026-06-10', NULL, NULL, '17:30 hrs.', 'https://www.instagram.com/p/DZQ4fR8Af78/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'cine', 1, 'panoramas/gWyQzhCXD83t10B8wTFqHQq2PCetEOSu1rf037Ar.jpg', 1, 0, '2026-06-09 02:03:08', '2026-06-09 02:03:08'),
(182, 'Lucy Briceño y Los del rincón', 'lucy-briceno-y-los-del-rincon-182', 'Rincón de las Guitarras, Freire 431', '2026-06-12', '2026-06-13', NULL, '21:00 hrs.', 'https://www.instagram.com/p/DZNqXPyRX51/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/DN0NCUedJXAJSBl5jeFuQeT2HKKgHIzHhl59aY4y.jpg', 1, 0, '2026-06-09 02:05:14', '2026-06-09 02:05:14'),
(183, 'EL JUEGO / Lanzamiento Videoclip + Concierto', 'el-juego-lanzamiento-videoclip-concierto-183', 'CENTEX, Sotomayor 233, Valparaíso', '2026-06-11', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DZMBX1bgH0r/?utm_source=ig_web_copy_link&igsh=NTc4MTIwNjQ2YQ==', 'musica', 1, 'panoramas/mrGU6Vjx4DZ1OhqbEP1q87cO8zTvOThsQHkypKfJ.webp', 1, 0, '2026-06-09 02:10:39', '2026-06-09 02:10:39'),
(184, 'Concierto Gerardo Saks', 'concierto-gerardo-saks-184', 'Centro de Extensión Duoc UC Sede Valparaíso, Av. Errázuriz 1020, Valparaíso', '2026-06-26', NULL, NULL, '19:00 hrs.', 'https://www.instagram.com/p/DZM_1e7ik8C/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 1, 'panoramas/oK9TjkFZLHZSv38v76sUFPRnPLv8NbbWS2JyaCkG.jpg', 1, 0, '2026-06-09 02:21:35', '2026-06-09 02:21:35'),
(185, 'Cava de Cousiño: Lúpulo de mesa', 'cava-de-cousino-lupulo-de-mesa-185', 'Centro de Extensión Duoc UC Sede Valparaíso, Av. Errázuriz 1020, Valparaíso', '2026-06-24', NULL, NULL, '19:00 hrs.', 'https://www.instagram.com/p/DZM_1e7ik8C/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'gastronomia', 1, 'panoramas/5ljsoI7aTdPGhiiVPcZW2T2HRWBoSb1mpd2uVnpb.jpg', 1, 0, '2026-06-09 02:22:30', '2026-06-09 02:24:30'),
(186, 'Especial día de la Felicidad \"Peluches\"', 'especial-dia-de-la-felicidad-peluches-186', 'Centro de Extensión Duoc UC Sede Valparaíso, Av. Errázuriz 1020, Valparaíso', '2026-06-20', NULL, NULL, '16:00 hrs.', 'https://www.instagram.com/p/DZM_1e7ik8C/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'teatro', 1, 'panoramas/AAjIACzHgLoVbTE7TqTDxHUVBafnn4gyb9Q3yIBg.jpg', 1, 0, '2026-06-09 02:23:40', '2026-06-09 02:23:40'),
(187, 'Cuentacuentos a puertas abiertas V.2.', 'cuentacuentos-a-puertas-abiertas-v2-187', 'Centro de Extensión Duoc UC Sede Valparaíso, Av. Errázuriz 1020, Valparaíso', '2026-06-20', NULL, NULL, '11:00 hrs.', 'https://www.instagram.com/p/DZM_1e7ik8C/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'teatro', 1, 'panoramas/sbYSNN1MoVgsGB90ggzaPZXnRe7BuuCThTcECcTr.jpg', 1, 0, '2026-06-09 02:26:11', '2026-06-09 02:26:11'),
(188, 'De la poesía a la pintura', 'de-la-poesia-a-la-pintura-188', 'Centro de Extensión Duoc UC Sede Valparaíso, Av. Errázuriz 1020, Valparaíso', '2026-06-20', NULL, NULL, '10:30 hrs.', 'https://www.instagram.com/p/DZM_1e7ik8C/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'taller', 1, 'panoramas/G9lk5DlXmYsJcrlEpTI3FVIO9XUayEqJpy0UYCJT.jpg', 1, 0, '2026-06-09 02:27:17', '2026-06-09 02:27:17'),
(189, 'Ruta Cerro Lecheros', 'ruta-cerro-lecheros-189', 'Planta baja Ascensor Barón', '2026-06-10', NULL, NULL, '11:30 hrs.', 'https://www.instagram.com/p/DZVE75rjuiR/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'tour', 1, 'panoramas/4oPARMW6EZhZSfjjMUtwZzvGL9EnZsBXfU2SpmkE.jpg', 1, 0, '2026-06-09 02:29:50', '2026-06-09 02:29:50'),
(190, '🍷✨ CERÁMICA & VINO ✨🍷', 'ceramica-vino-190', 'Centro Cultural Tarpuy San Pedro #321, Playa Ancha (A pasos de Plaza Waddington)', '2026-06-06', NULL, NULL, '16:00 hrs.', 'https://www.instagram.com/p/DZEFhPYu59p/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'taller', 0, 'panoramas/tH9Wwlx2ETIHVF4ZanvIGy4ZhqiR3BCwgDvWZvHG.webp', 1, 0, '2026-06-09 02:31:46', '2026-06-09 02:31:46'),
(191, 'SdC Big Band en Estrella Negra', 'sdc-big-band-en-estrella-negra-191', 'Estrella Negra Club de Jazz- Carrera 440, Valparaíso', '2026-06-11', NULL, NULL, '🕣 Apertura del bar 19:00 hrs || Música en vivo desde las 20:30', 'https://www.instagram.com/p/DZV0u4kDuvb/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/E9VKMTAzF5wu5IJysxOHR3MzDGGMOiFQVmSJkSC9.jpg', 1, 0, '2026-06-09 02:35:07', '2026-06-09 02:35:07'),
(192, 'Ricardo Loebell Palabra escondida', 'ricardo-loebell-palabra-escondida-192', 'Museo Universitario del Grabado, Lautaro Rosas 485 Valparaíso', '2026-06-12', NULL, NULL, '12:00 hrs.', NULL, 'conferencia', 1, 'panoramas/ynWP8ieaxr888YReDgrIfzCrXVW0RGhWWHvlm8YR.jpg', 1, 0, '2026-06-09 02:41:04', '2026-06-09 02:41:04'),
(194, 'Simo en Vivo!', 'simo-en-vivo-194', 'El pimentón - Ecuador 27, Valparaíso', '2026-06-10', NULL, NULL, '19:30 open mic - 20:00 hrs show', 'https://www.instagram.com/p/DZVJnawEbys/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 1, 'panoramas/lCaK1U36jXNHhw2XtzFEkwvewaYc2f1A7QfiQVYF.jpg', 1, 0, '2026-06-09 02:56:51', '2026-06-09 02:56:51'),
(195, 'El Húsar de la Muerte', 'el-husar-de-la-muerte-195', 'CCO Hormiguero (República #290, esquina Av. Quebrada Verde, Playa Ancha)', '2026-06-12', NULL, NULL, '18:00 hrs.', 'https://www.instagram.com/p/DZQUarUSpc2/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'cine', 1, 'panoramas/eIo2Pbwa2KRJXRRydT063Wf6PdbWodTg35RcJaLA.jpg', 1, 0, '2026-06-09 02:59:12', '2026-06-09 02:59:12'),
(196, 'Concierto Sonidos de Casa', 'concierto-sonidos-de-casa-196', 'BAJ VALPO Santa Isabel 739, C.Alegre, Valparaíso.', '2026-06-26', NULL, NULL, '18:30 hrs.', 'https://www.instagram.com/p/DZDOgaHlf6C/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 0, 'panoramas/z7AfZt5ubRZLtBefzYPzFAtDiTXKTF6aadQJrx5v.webp', 1, 0, '2026-06-09 03:03:40', '2026-06-09 03:03:40'),
(197, 'Tormenta Tropical en Caleta Gomez  📰', 'tormenta-tropical-en-caleta-gomez-197', 'Bar La Morada, Cumming 68', '2026-06-10', NULL, NULL, '20:00 hrs', 'https://www.instagram.com/p/DZWLmwnjLX4/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'musica', 1, 'panoramas/xPX5ePkddurMonR0URKRv8AKjSlvd2kjtG91HQQd.webp', 1, 0, '2026-06-09 03:06:41', '2026-06-09 03:06:41'),
(198, 'Varieté de la diversidad 🌈', 'variete-de-la-diversidad-198', 'Santa Inés #38, cerro Barón. Valparaíso.', '2026-06-13', NULL, NULL, '19:00 hrs.', 'https://www.instagram.com/p/DZORCPfgE-s/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', 'teatro', 0, 'panoramas/D4Pb2Wg01Exi5T7pmKMEuMRQUGDUoqgFN2koxTga.webp', 1, 0, '2026-06-09 03:13:10', '2026-06-09 03:13:10'),
(199, 'bluesystem fooggy ACID LIVE SET', 'bluesystem-fooggy-acid-live-set-199', 'Esmeralda 1060, local 7. Valparaíso, Chile', '2026-06-13', NULL, NULL, '22:00 HRS.', 'https://www.instagram.com/p/DZLBGOStbKJ/?utm_source=ig_web_copy_link&igsh=NTc4MTIwNjQ2YQ==', 'musica', 1, 'panoramas/MHA8Y9P9JZvDuCksWmDrwBoDApYkeB0wAS4q0Xc0.jpg', 1, 0, '2026-06-09 03:22:05', '2026-06-09 03:22:05'),
(200, 'Raporteño', 'raporteno-200', 'Bustamante 69', '2026-06-13', NULL, NULL, '17:00 a 01:0', 'https://www.instagram.com/p/DZarkz6gWN5/', 'feria', 0, 'panoramas/LehsnejKHDJ9BwiTB28MproUcprxJ2bTQyO5jy3h.jpg', 1, 0, '2026-06-11 13:55:32', '2026-06-11 13:55:32'),
(201, 'Dua Lipa', 'dua-lipa-201', 'Máscara - Plaza Aníbal Pinto 1178', '2026-06-12', NULL, NULL, 'https://www.instagram.com/p/DZa6KpeTLq-/', 'https://www.instagram.com/p/DZa6KpeTLq-/', 'musica', 0, 'panoramas/TNWpvzGsxjHpSDsxvTz2LNBU670fpPbcyk5tySBY.webp', 1, 0, '2026-06-11 13:58:51', '2026-06-11 13:58:51'),
(202, 'Pulse', 'pulse-202', 'Club Segundo Piso, Av. Brasil 1395, Valparaíso', '2026-06-13', NULL, NULL, '21:00', 'https://www.instagram.com/p/DZVTpi0gIjt/', 'musica', 0, 'panoramas/3FrNRtejcGmGE7a35cxf48WW3AqTOhHs3lhPlQ6e.jpg', 1, 0, '2026-06-11 14:07:28', '2026-06-11 14:07:28'),
(203, 'Celebremos el día de tejer en público', 'celebremos-el-dia-de-tejer-en-publico-203', 'Café Waddington', '2026-06-13', NULL, NULL, '11: 00 hrs', 'https://www.instagram.com/p/DZX9nTXlbhi/', 'otros', 1, 'panoramas/P6zRno5AmcOdhBdVzAQv1lXy1PT7iQhsaRcP3gow.jpg', 1, 0, '2026-06-11 14:09:01', '2026-06-11 14:09:01'),
(204, 'Biblioteca abierta', 'biblioteca-abierta-204', 'Biblioteca Santiago Severin', '2026-06-13', '2026-06-30', '[6]', '09:00 a 13:45 hrs', 'https://www.instagram.com/p/DZbLj6yjEyF/', 'literatura', 0, 'panoramas/IfW8jacmDbNZ3hVhsZlKLMk46wnRxR6rJmxYyKrq.jpg', 1, 0, '2026-06-11 14:13:10', '2026-06-11 14:13:10'),
(205, 'Trayectopia', 'trayectopia-205', '.Casa Masónica de la GLMCH, Valparaíso Independencia 2386', '2026-06-12', NULL, NULL, '17:00', 'https://www.instagram.com/p/DZX2qFvFi4j/?igsh=NXF5N2k2ZWp6bDE%3D', 'taller', 1, 'panoramas/kgOrJueHfPE6uh3xH6Jo05HghT0hrsePw1RLjm4F.jpg', 1, 0, '2026-06-11 14:30:06', '2026-06-11 14:30:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `panorama_imagenes`
--

CREATE TABLE `panorama_imagenes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `panorama_id` bigint(20) UNSIGNED NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `panorama_imagenes`
--

INSERT INTO `panorama_imagenes` (`id`, `panorama_id`, `ruta`, `orden`, `created_at`, `updated_at`) VALUES
(1, 20, 'panoramas/wjR34nfEmF7Hf6Nh5MFhNhsixgdpYvmXb1nnhn4R.webp', 1, '2026-05-19 23:54:15', '2026-05-19 23:54:15'),
(2, 20, 'panoramas/4AN23nSDbcQFTIPjJv3OEpfQfQtjKD1xCAoU0zs5.webp', 2, '2026-05-19 23:54:15', '2026-05-19 23:54:15'),
(4, 26, 'panoramas/ScdpwvGM3N8hJRcPp9KjaZASAn1yGfBn6i68dpDe.jpg', 0, '2026-05-20 00:30:26', '2026-05-20 00:30:26'),
(5, 27, 'panoramas/DQQCeZeIkz4dm7QpEbbmC4Usd9ckupKkxsoMANtj.jpg', 0, '2026-05-20 01:21:42', '2026-05-20 01:21:42'),
(6, 27, 'panoramas/r4irb4cDD4Sn4TXMCIEABDl1x7bvZczmswKExwl4.jpg', 1, '2026-05-20 01:21:42', '2026-05-20 01:21:42'),
(8, 42, 'panoramas/WJ0f8Sp8BIxseLCU3Pt93dcwXaBKiJT3qSSGhWjj.jpg', 0, '2026-05-20 20:20:37', '2026-05-20 20:20:37'),
(9, 46, 'panoramas/BS9om1Y7lJyXmXIQv9K4dkAaeeDTmmCkAB7dh8Ed.jpg', 0, '2026-05-21 18:12:36', '2026-05-21 18:12:36'),
(10, 46, 'panoramas/3sVihD44NNmPB4piRZ6jHUthEUJgv8DXjugWsiXT.jpg', 1, '2026-05-21 18:12:37', '2026-05-21 18:12:37'),
(11, 46, 'panoramas/Awy8w2PpI2iy6PaEdJeHWGaXz4BRL9lz9PRwz3aL.jpg', 2, '2026-05-21 18:12:37', '2026-05-21 18:12:37'),
(12, 46, 'panoramas/RPitoIx6mwQkNBpGOu3di4ID4RCb6wvnssu6Vu44.jpg', 3, '2026-05-21 18:12:37', '2026-05-21 18:12:37'),
(13, 67, 'panoramas/ulgI9dF3PXmsDHPY0Z9dAVWDsbtlHvBbjyK93xFZ.webp', 0, '2026-05-24 15:50:28', '2026-05-24 15:50:28'),
(15, 67, 'panoramas/ehi8lXEckWil1aj377wsfjxQmOf2G98S0O512rsa.webp', 1, '2026-05-24 15:50:29', '2026-05-24 15:50:29'),
(20, 82, 'panoramas/V1LO5iAgzfYsFEe84onPtZSudEEhIOzyOppVBsSH.jpg', 0, '2026-05-24 16:50:36', '2026-05-24 16:50:36'),
(21, 82, 'panoramas/wGMabwb0nnuhzcK3FyNKnijUamzzBeMpP8pF8fq1.jpg', 1, '2026-05-24 16:50:36', '2026-05-24 16:50:36'),
(22, 93, 'panoramas/bgjXvP4eypARAu1tA1FBYN9uppWyqIVtcOo5tsEt.jpg', 0, '2026-05-24 17:23:30', '2026-05-24 17:23:30'),
(23, 93, 'panoramas/gYZ1qcfkvtxUcMGU51pyF6hkRcFog5CPSk5cPqZj.jpg', 1, '2026-05-24 17:23:30', '2026-05-24 17:23:30'),
(24, 93, 'panoramas/aPZco76yb8XccU308bSmvid7b5TZXiymiCHy964r.jpg', 2, '2026-05-24 17:23:31', '2026-05-24 17:23:31'),
(25, 94, 'panoramas/6lLI2IvCaslf11SazENJomvmbXDR6jX3NWnj40mi.jpg', 0, '2026-05-24 17:26:53', '2026-05-24 17:26:53'),
(26, 94, 'panoramas/XP1OPlKTGlKzdu5NOPDI1OLkgZ9XlzqLhcVYNvB1.jpg', 1, '2026-05-24 17:26:54', '2026-05-24 17:26:54'),
(27, 94, 'panoramas/TrsCHZ9BdocIb6NIRh2x7Utfyj149stP8Yu7xP15.jpg', 2, '2026-05-24 17:26:54', '2026-05-24 17:26:54'),
(28, 98, 'panoramas/ThbXK4M0UEmVO5E1wWUvCd97eDLNoppTPiThufED.jpg', 1, '2026-05-24 17:38:47', '2026-05-24 17:38:47'),
(29, 98, 'panoramas/5hPPgbgrEN25mSkIMoUIeYLnkLq6NkGYZi9q9im3.jpg', 2, '2026-05-24 17:38:47', '2026-05-24 17:38:47'),
(30, 98, 'panoramas/vbVsv65KbisklVUEaBs6XVFQWvfmp6RslTtvizZK.jpg', 3, '2026-05-24 17:38:47', '2026-05-24 17:38:47'),
(31, 104, 'panoramas/MFWNR5zqSk8Z29NTlDXkIfbL4C7LHREsSuWDOVMy.jpg', 0, '2026-05-24 18:15:59', '2026-05-24 18:15:59'),
(32, 104, 'panoramas/WN8LkEWSXqAw7NxOq4VBKg5rZlrxAaNEui0TodDd.jpg', 1, '2026-05-24 18:15:59', '2026-05-24 18:15:59'),
(33, 105, 'panoramas/xG2m3ufBqWiDkmMR9e8yl7O3ghLgwXpbnRbPsqaJ.jpg', 0, '2026-05-24 18:18:06', '2026-05-24 18:18:06'),
(34, 114, 'panoramas/mqZHTVHLjgL6HmdC1TtfhMXfY8Oipgsa00kbVoQ5.jpg', 0, '2026-05-25 19:05:05', '2026-05-25 19:05:05'),
(35, 129, 'panoramas/LVnipSVxolPdcGNVdiw1SJcjnNE8W3ZGHviFCfKN.jpg', 0, '2026-05-29 11:26:33', '2026-05-29 11:26:33'),
(37, 133, 'panoramas/FNCwJcNNdrzIIxM7PsOYMRWFiAO0yGFDfE9pRm1N.jpg', 0, '2026-05-31 13:20:44', '2026-05-31 13:20:44'),
(38, 139, 'panoramas/3xJ0WMu7MroDhXGK4AgQclVLSHRCjDONieEcGfKR.jpg', 0, '2026-06-02 00:01:29', '2026-06-02 00:01:29'),
(39, 142, 'panoramas/JOSY2kNS0bd6yNDJwVCLd8ICY2fs7fwt9lP43pVC.jpg', 0, '2026-06-03 11:41:12', '2026-06-03 11:41:12'),
(40, 142, 'panoramas/w6orwGuY6mf2vzuPvcxO5HT4FI87iLWzv1i31Otc.jpg', 1, '2026-06-03 11:41:12', '2026-06-03 11:41:12'),
(41, 143, 'panoramas/a2pZN2AzuNVSlS8V4cIT9KIlUV5wxRMuahwAa0X1.jpg', 0, '2026-06-03 11:46:59', '2026-06-03 11:46:59'),
(42, 152, 'panoramas/cPFejCJ9xPpDAEpuoDxqcCc7JkmwXwjv68g9AXjr.jpg', 0, '2026-06-03 23:37:05', '2026-06-03 23:37:05'),
(43, 152, 'panoramas/cmq082RGWvQvZQ1yHfYJLbwFJ4dDpVsykNzrIeKa.jpg', 1, '2026-06-04 00:10:27', '2026-06-04 00:10:27'),
(44, 153, 'panoramas/ydPtcVPDLIOwx7iRai4bzOe1yxYtu5pnqUjL0BXS.jpg', 0, '2026-06-04 21:57:26', '2026-06-04 21:57:26'),
(45, 153, 'panoramas/vTIf57NlefiE0bqGUh5tvvr4EXYbrg8DGilheeTk.jpg', 1, '2026-06-04 21:57:26', '2026-06-04 21:57:26'),
(46, 155, 'panoramas/BGVAL0fdQnJcfDI6XHg4uYKWBvlvCN7bJlQgpYB8.jpg', 0, '2026-06-04 22:05:00', '2026-06-04 22:05:00'),
(47, 162, 'panoramas/n3GEeGaUjsE4JUjmel3JCGeSklHGtP2KEUbcu8PF.jpg', 0, '2026-06-05 12:07:08', '2026-06-05 12:07:08'),
(48, 165, 'panoramas/GzfHSbTGEWkbchZCJYnXL2Q61t6t584zjIislZBt.jpg', 0, '2026-06-09 01:02:11', '2026-06-09 01:02:11'),
(49, 179, 'panoramas/k3kfkGXVTrOSuNS7BrW1ZaRfajSm1DWpGItLOUID.webp', 0, '2026-06-09 01:47:35', '2026-06-09 01:47:35'),
(50, 180, 'panoramas/gAYm5e2kSUiFQXO7S27PpYjzkflzXkza5lF7sTOK.jpg', 0, '2026-06-09 01:59:54', '2026-06-09 01:59:54'),
(51, 183, 'panoramas/9DCXv4lLh0KKZoY3mOkizinvF2o6rEhZSYkQucdp.webp', 0, '2026-06-09 02:10:39', '2026-06-09 02:10:39'),
(52, 186, 'panoramas/2Z5vb3wl6W5OKgc6xDNpuGv2crgEHeGvO4VcbdZE.jpg', 0, '2026-06-09 02:23:40', '2026-06-09 02:23:40'),
(53, 197, 'panoramas/wnDqkk2MbRoM9eI2ztNAMkv0cAGbZHv8gr1lTMZN.webp', 0, '2026-06-09 03:06:41', '2026-06-09 03:06:41'),
(54, 198, 'panoramas/5RVF0RtQlFXtkc22nlo7WAwXn3cDKYwXFStPyoMr.webp', 0, '2026-06-09 03:13:11', '2026-06-09 03:13:11'),
(55, 202, 'panoramas/cXUEt8W5fu1dTRL3AnbsrLga2tOuRD8I9M9CregT.jpg', 0, '2026-06-11 14:07:29', '2026-06-11 14:07:29'),
(56, 202, 'panoramas/Jp2pf0JwZhoAd85sq8GFZIWsLCMqDu9ILL2D3hK1.jpg', 1, '2026-06-11 14:07:29', '2026-06-11 14:07:29'),
(57, 202, 'panoramas/bXQzZQ9vhVSj6DIkfsGWOsrDeIzeCBY5i2Mjg0km.jpg', 2, '2026-06-11 14:07:29', '2026-06-11 14:07:29'),
(58, 202, 'panoramas/n4HotpU6PTNOdeYWvtu5IFPnikDXETGSEY3gGkhg.jpg', 3, '2026-06-11 14:07:29', '2026-06-11 14:07:29'),
(59, 202, 'panoramas/vPorqgrbgH6e8pozbvhoG0n4vMW96UPJttneZG9e.jpg', 4, '2026-06-11 14:07:29', '2026-06-11 14:07:29'),
(60, 202, 'panoramas/r5ZuOphyHF7r7AKH0YB7EqwduUysBFobO20bBYyt.jpg', 5, '2026-06-11 14:07:29', '2026-06-11 14:07:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `resumen` text DEFAULT NULL,
  `contenido` longtext DEFAULT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `imagenes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`imagenes`)),
  `publicado` tinyint(1) NOT NULL DEFAULT 0,
  `publicado_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `posts`
--

INSERT INTO `posts` (`id`, `titulo`, `slug`, `resumen`, `contenido`, `imagen_portada`, `imagenes`, `publicado`, `publicado_en`, `created_at`, `updated_at`) VALUES
(1, '3 imprescindibles de Cerro Bellavista', '3-imprescindibles-de-cerro-bellavista', 'Cerro Bellavista es poesía con vista al Pacífico. Subes en el Ascensor Espíritu Santo, funicular histórico de 1911 que por $100 te deja en pleno Museo a Cielo Abierto con 20 murales pintados tras el retorno a la democracia. Desde ahí caminas entre arte y miradores hasta La Sebastiana, la casa-laberinto de Pablo Neruda con sus 4 pisos llenos de objetos, color y la mejor panorámica de la bahía. En una mañana tienes transporte patrimonial, arte callejero y la casa del Nobel: la esencia de Valpo en un solo cerro.', '<h3>1. La Sebastiana – La casa de Pablo Neruda</h3><p>La casa-museo que Neruda rescató del viento porteño y convirtió en su refugio. 4 pisos llenos de objetos lúdicos, escaleras estrechas y ventanales con vista a la bahía. La inauguró el 18 de septiembre de 1961. Es un ícono del barrio y te muestra la vida cotidiana del poeta: su imaginación, sus colecciones y ese surrealismo que lo hacía único. <em>Tip</em>: desde sus terrazas tenés la vista que le dio nombre al cerro: “bella vista”.</p><p><br></p><h3>2. Ascensor Espíritu Santo – El funicular al cielo abierto</h3><p>Monumento Histórico inaugurado el 24 de diciembre de 1911. Conecta el plan con Cerro Bellavista por $100 y te deja justo en el Museo a Cielo Abierto. Lleva el nombre de un antiguo templo que hubo cerca y que se demolió tras un terremoto. Fue restaurado con inversión de $1.678 millones y reabrió en 2019. Subís en 1 minuto lo que a pie te toma 10. Arriba hay mirador y murales.</p><p><br></p><h3>3. Parroquia del Carmen + Museo a Cielo Abierto</h3><p>Aunque el templo original que dio nombre al ascensor ya no está, el barrio mantiene su vocación religiosa y cultural. Hoy el imperdible es el Museo a Cielo Abierto: 20 murales de artistas como Roberto Matta, Mario Carreño y Gracia Barrios pintados en 1992 tras el retorno a la democracia. Arranca justo saliendo del ascensor Espíritu Santo. Caminás entre arte, rayados y vistas, con la Plaza de los Poetas cerca, donde están las estatuas de Neruda, Mistral y Huidobro.</p><p><br></p><p>Bonus express:</p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Plaza Victoria y Catedral de Valparaíso: Abajo del cerro, la catedral está consagrada a la Virgen del Carmen y el Espíritu Santo. Ideal si querés cerrar el circuito iglesia-ascensor-poesía.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Dato local: Bellavista limita con las quebradas del Circo y Yerbas Buenas, hoy convertidas en calles. Por eso tiene esa vista central a toda la bahía.</li></ol><p><br></p><p>¿Cómo recorrerlo? Sube en Ascensor Espíritu Santo, recorre el Museo a Cielo Abierto, baja a la Plaza de los Poetas y termina en La Sebastiana. 2 horas, pura esencia porteña.</p>', 'blog/portadas/Z9nJuDoyg8P48RFgMiPRPR8YNhTBrOyZLdLlUT09.jpg', '[{\"ruta\":\"blog\\/galeria\\/1U22dQMo1dFIbZknHmzAgSpFZkV7ah8sCfMxJ1ki.jpg\",\"posicion\":1},{\"ruta\":\"blog\\/galeria\\/nFBWA3zwAtV1PUL0vwugQmwOQuWVuOOZ72DspnwE.jpg\",\"posicion\":3},{\"ruta\":\"blog\\/galeria\\/kBnzQvRLokAoNLKL3VTU8ETw215LC5afjn4ImCCv.jpg\",\"posicion\":5}]', 1, '2026-05-26 17:06:08', '2026-05-26 17:06:08', '2026-05-29 20:10:39'),
(2, 'Cerro Polanco, el secreto vertical y cultural de Valparaíso', 'cerro-polanco-el-secreto-vertical-y-cultural-de-valparaiso', 'Para vivir el puerto desde su esencia más auténtica y comunitaria, el Cerro Polanco es un destino imperdible que se escapa de las rutas turísticas tradicionales. La aventura comienza cruzando un místico túnel de 150 metros bajo la roca para subir en su icónico ascensor, el único completamente vertical de la ciudad, que regala una panorámica espectacular de la bahía. Al salir a sus pasajes, te encuentras con un vibrante museo al aire libre de murales a gran escala, cuyo corazón cultural y comunitario hoy late con fuerza en el Centro Cultural Casa Polanco, un espacio autogestionado que reúne el', '<p>Si buscas experimentar la verdadera esencia de Valparaíso, lejos de los circuitos turísticos tradicionales de los cerros Alegre o Concepción, el <strong>Cerro Polanco</strong> es una parada obligatoria. Conocido por su fuerte identidad de barrio y su impresionante despliegue de arte urbano, este rincón de la ciudad ofrece una perspectiva única, auténtica y profundamente comunitaria.</p><p>El viaje comienza de una manera espectacular con su mayor hito arquitectónico: el <strong>Ascensor Polanco</strong>. A diferencia de los demás funiculares del puerto, este es el único ascensor completamente vertical de la ciudad. Para acceder a él, cruzas un imponente túnel peatonal de 150 metros excavado en la roca que te transporta a otra época. Tras subir en su cabina histórica, emerges en una espectacular torre que regala una de las vistas panorámicas más impresionantes y menos fotografiadas de la bahía.</p><p><br></p><p>Al salir, te encuentras en un laberinto de pasajes que sirvieron de lienzo para el primer festival de grafiti a gran escala en Chile (<em>Polanco Graff</em>), transformando las fachadas de las casas en un gigantesco museo al aire libre. Cada esquina desborda color, memoria y crítica social.</p><p><br></p><p><strong>El nuevo latido del cerro: Centro Cultural Casa Polanco</strong> El circuito por el cerro se ha enriquecido profundamente gracias a la comunidad local y la llegada del <strong>Centro Cultural Casa Polanco</strong>. Este espacio se ha consolidado como un punto de encuentro clave para el arte, la música, los talleres y la memoria del barrio. Visitarlo no solo te permite conocer la calidez de los porteños, sino también empaparte de la autogestión y el tejido social que mantiene vivo al puerto. Es el lugar perfecto para hacer una pausa, conversar con creadores locales y entender que Valparaíso no es solo una postal del pasado, sino un territorio cultural en constante ebullición.</p><p><br></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Ideal para:</strong> Viajeros independientes, amantes del <em>street art</em>, la fotografía urbana y el turismo comunitario.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>El tip viajero:</strong> Dedica un par de horas a caminar sin prisa por sus pasajes, respeta la tranquilidad residencial del barrio y no dejes de pasar por Casa Polanco para ver su cartelera cultural vigente.</li></ol>', 'blog/portadas/7QRF9pkIGijKoP9xRfHgaI2W6qe4Teq9mLw6cOPm.jpg', '[{\"ruta\":\"blog\\/galeria\\/S4kP4mjqmz58n6qsYsXqaK8N9tcbMbWIF35QYKep.jpg\",\"posicion\":1},{\"ruta\":\"blog\\/galeria\\/3rgm3IuAW0GEmgp9ije2hvgpW7dVD9QgTa6cZAKW.jpg\",\"posicion\":3}]', 1, '2026-06-07 21:57:18', '2026-06-07 21:56:15', '2026-06-07 21:57:18'),
(3, 'La Leyenda de Emilio Dubois, el \"Santo Criminal\" de Valparaíso', 'la-leyenda-de-emilio-dubois-el-santo-criminal-de-valparaiso', 'El mito de Emilio Dubois: Para los viajeros fascinados por el misterio y el folclor, conocer la historia de Emilio Dubois en el Cementerio N°3 de Playa Ancha ofrece una inmersión única en el misticismo porteño. Fusilado en 1907 por asesinar a usureros, este francés defendió su inocencia hasta el final, ganándose la simpatía del pueblo que lo transformó de criminal a un \"Robin Hood\" y santo popular. Hoy, su animita es un concurrido centro de peregrinación lleno de placas de fieles que agradecen sus favores y aseguran que su alma sigue cumpliendo milagros.', '<p>Para los viajeros que buscan desentrañar los secretos más oscuros y fascinantes del folclor urbano, Valparaíso esconde historias que desafían la lógica. Una de las más potentes es la de <strong>Emilio Dubois</strong> (Émile Dubois), un enigmático ciudadano francés que llegó al puerto a principios del siglo XX y que terminó transformándose, paradójicamente, en uno de los santos populares más venerados de la ciudad.</p><p><br></p><p>A Dubois se le acusó de ser el primer asesino en serie de Chile, sindicado como el responsable de las muertes de varios usureros y empresarios extranjeros acaudalados en el plan de Valparaíso. Sin embargo, el juicio estuvo lleno de dudas y él defendió su inocencia con una elocuencia y elegancia que cautivaron a la opinión pública de la época. Frente al pelotón de fusilamiento en 1907, en la antigua cárcel pública, él mismo dio la orden de disparar tras rechazar que le vendaran los ojos, consolidando su imagen de mártir.</p><p><br></p><p>Las clases populares del puerto, resentidas por las injusticias sociales de la época, no tardaron en mitificarlo. Para el pueblo, Dubois no era un asesino común, sino un \"Robin Hood\" que eliminaba a los explotadores para ayudar a los desvalidos.</p><p>Hoy en día, la experiencia turística vinculada a su figura te lleva directamente al <strong>Cementerio N°3 de Playa Ancha</strong>. Allí, su tumba se ha transformado en una \"animita\" (un santuario popular) de proporciones monumentales, completamente cubierta por cientos de placas de bronce y mármol donde los fieles le agradecen por \"favores concedidos\". Visitar este rincón no solo es un viaje a la crónica roja del Valparaíso de antaño, sino una oportunidad única para entender cómo el puerto reescribe sus propias leyendas y encuentra devoción en los lugares más inesperados.</p><p><br></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Ideal para:</strong> Amantes del turismo histórico, el misterio, las leyendas urbanas y las tradiciones culturales de los cementerios porteños.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>El tip viajero:</strong> Puedes combinar esta visita con un recorrido por el Paseo 21 de Mayo, ya que el cementerio queda en el mismo cerro Playa Ancha, un barrio con una identidad residencial hermosísima.</li></ol><p><br></p>', 'blog/portadas/gWkPBUMC8zT6ksLY3NXjOkwIBAvtPCDwW717Pfd7.jpg', '[{\"ruta\":\"blog\\/galeria\\/jIq6SjLkXP5PtIJTaP07LB5aA9gU2gmqrqleRDdU.jpg\",\"posicion\":1},{\"ruta\":\"blog\\/galeria\\/IVSZ8JmpLps5Bi92VnduSjq5Y7w4yvPvmtPDcywI.jpg\",\"posicion\":3},{\"ruta\":\"blog\\/galeria\\/tpL3XVzFSkweGWp9Zw8BIMZI8xnypjjOtxF8YunG.jpg\",\"posicion\":6}]', 1, '2026-06-07 22:49:18', '2026-06-07 22:49:18', '2026-06-07 22:49:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntosinteres`
--

CREATE TABLE `puntosinteres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sector` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `ciudad` varchar(255) NOT NULL DEFAULT 'Valparaíso',
  `description` longtext NOT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `video_url` varchar(255) DEFAULT NULL,
  `enlace` varchar(255) DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL,
  `autor` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `es_cliente` tinyint(1) NOT NULL DEFAULT 0,
  `modulos_habilitados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`modulos_habilitados`)),
  `oferta_del_dia` text DEFAULT NULL,
  `oferta_activa` tinyint(1) NOT NULL DEFAULT 0,
  `oferta_expira_at` timestamp NULL DEFAULT NULL,
  `descripcion_busqueda` text DEFAULT NULL,
  `imagen_perfil` varchar(255) DEFAULT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `categoria_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `puntosinteres`
--

INSERT INTO `puntosinteres` (`id`, `user_id`, `title`, `slug`, `sector`, `direccion`, `lat`, `lng`, `ciudad`, `description`, `tags`, `video_url`, `enlace`, `horario`, `autor`, `activo`, `es_cliente`, `modulos_habilitados`, `oferta_del_dia`, `oferta_activa`, `oferta_expira_at`, `descripcion_busqueda`, `imagen_perfil`, `eliminado`, `created_at`, `updated_at`, `categoria_id`) VALUES
(1, 1, 'Museo Institucional UTFSM', 'museo-institucional-utfsm-902', 'Plan', 'Avenida España 1680', -33.03423000, -71.59580700, 'Valparaíso', '<p>El Museo Institucional de la Universidad Técnica Federico Santa María surge el año 2000, se nomina como presidente honorario al destacado Profesor Benemérito Sr. Carlos González de La Fuente, entregando a la comunidad universitaria un espacio destinado a preservar la memoria institucional recopilando elementos que pudieran resultar significativos para la identidad sansana. Fuente: www.usm.cl</p>', '[\"valparaiso\",\"museo\",\"usm\"]', NULL, 'https://biblioteca.usm.cl/patrimonio_institucional/museo', 'Previa agenda', NULL, 0, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:33:57', '2026-06-06 23:17:31', 7),
(2, 1, 'Museo de Historia Natural de Valparaíso', 'museo-de-historia-natural-de-valparaiso-119', 'Plan', 'Condell 1546', -33.04642000, -71.62122300, 'Valparaíso', 'Ubicado en el corazón de la ciudad, el Museo de Historia Natural de Valparaíso es un oasis de conocimiento y maravilla. Con una colección de más de 100.000 especímenes, explora la rica biodiversidad de Chile y el mundo. Desde fósiles antiguos hasta especies marinas únicas, este museo es un destino imperdible para curiosos de todas las edades. ¡Ven y descubre los secretos de la naturaleza!', '[\"Valpara\\u00edso\",\"museo\",\"educaci\\u00f3n\",\"cultura\"]', NULL, 'www.mhnv.gob.cl', 'Martes a viernes 10:00 a 17:30 sábado 11:00 a 16:00. Festivos, domingo y lunes cerrado.', NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:38:52', '2026-05-09 04:50:09', 7),
(3, 1, 'Museo Maritimo Nacional', 'museo-maritimo-nacional-397', 'Cerro Artillería', 'Paseo Veintiuno de Mayo Nº45', -33.03252200, -71.63099800, 'Valparaíso', '<p>Ubicado en el corazón del puerto, el Museo Marítimo Nacional de Valparaíso es un lugar emblemático que te sumergirá en la rica historia naval del país. Descubre la evolución de la Marina de Chile, admira modelos de barcos históricos y explora la vida de los marinos que escribieron la historia del país. ¡Un destino imperdible para amantes del mar y la historia!</p>', '[\"valpara\\u00edso\",\"museo\",\"mar\",\"armada\",\"historia\"]', NULL, 'https://www.museomaritimo.cl', 'Lunes a Domingo de 10:00 a 18:00 hrs.', NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:40:39', '2026-05-22 04:32:21', 7),
(4, 1, 'Museo Histórico de Placilla', 'museo-historico-de-placilla-992', 'Placilla', 'Av. El Tranque 571', -33.11047200, -71.57165600, 'Valparaíso', 'El Museo Histórico de Placilla es un tesoro escondido que te transportará a la época colonial. Ubicado en una casona del siglo XVIII, este museo alberga una rica colección de objetos y documentos que narran la historia de Valparaíso y su entorno. Descubre la vida cotidiana de los antiguos habitantes, admira la arquitectura tradicional y sumérgete en la memoria colectiva de esta región única. ¡Un destino imperdible para amantes de la historia y la cultura!', '[]', NULL, 'https://muhp.cl/nuestro-museo/', 'Abierto de lunes a sábado 10:00 a 14:00 hrs y 15:30 a 17:30 hrs', NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:42:04', '2026-04-10 04:42:04', 7),
(5, 1, 'Ascensor Barón', 'ascensor-baron-454', 'Cerro Barón', NULL, -33.04306700, -71.60490200, 'Valparaíso', '<p><strong>*OPERATIVO*</strong></p><p><br></p><p>El Ascensor Barón es uno de los 15 ascensores históricos de Valparaíso y un símbolo de la ciudad. Construido en 1904, este ascensor de hierro y madera te llevará desde el puerto hasta el barrio de Cerro Barón, ofreciéndote vistas impresionantes de la ciudad y el mar. ¡Disfruta del viaje y descubre la esencia de Valparaíso!</p>', '[\"funicular\",\"ascensor\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:43:51', '2026-05-29 22:38:15', 16),
(6, 1, 'Iglesia La Matriz', 'iglesia-la-matriz-552', 'Plan', NULL, -33.03629500, -71.63164100, 'Valparaíso', 'La Iglesia La Matriz es un icono de la ciudad y uno de los edificios más antiguos y significativos de Valparaíso. Construida en 1837, esta iglesia neoclásica es un ejemplo destacado de la arquitectura religiosa del siglo XIX en Chile. Su fachada imponente, su torre alta y su interior acogedor la convierten en un lugar de gran belleza y espiritualidad. ¡Un destino imperdible para amantes de la arquitectura y la historia!', '[\"iglesia\",\"monumento\",\"hist\\u00f3ria\",\"Valpara\\u00edso\"]', NULL, 'https://www.facebook.com/profile.php?id=100011363277244', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:46:36', '2026-05-09 04:50:47', 15),
(7, 1, 'Monumento a Los Héroes de Iquique', 'monumento-a-los-heroes-de-iquique-872', 'Plan', NULL, -33.03827900, -71.62828800, 'Valparaíso', 'El Monumento a los Héroes de Iquique es un emblemático monumento que conmemora la valiente acción de los marineros chilenos durante la Guerra del Pacífico. Ubicado en el corazón de Valparaíso, este monumento impresionante rinde homenaje a los héroes que defendieron la soberanía de Chile en la Batalla de Iquique. Su imponente estructura y su significado histórico lo convierten en un lugar de gran importancia y orgullo nacional. ¡Un destino imperdible para recordar y honrar a los héroes de la patria!', '[\"combate naval de iquique\",\"arturo prat\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:47:20', '2026-04-12 18:15:48', 4),
(8, 1, 'Arco Británico', 'arco-britanico-986', 'Plan', NULL, -33.04465200, -71.62042500, 'Valparaíso', 'El Arco Británico de Valparaíso es un monumento histórico que conmemora la amistad y la alianza entre Chile y el Reino Unido. Construido en 1911, este arco imponente es un ejemplo de la arquitectura neoclásica y un testimonio de la relación comercial y cultural entre ambos países. Ubicado en el corazón del puerto, el Arco Británico es un lugar emblemático que evoca la rica historia y la conexión internacional de Valparaíso. ¡Un destino imperdible para descubrir la herencia cultural de la ciudad!', '[\"gran breta\\u00f1a\",\"arquitectura\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:48:14', '2026-04-12 08:31:19', 4),
(9, 1, 'Escalera de Piano', 'escalera-de-piano-933', 'Cerro Concepción', NULL, -33.04286100, -71.62623100, 'Valparaíso', '<p>La <strong>Escalera Piano</strong> es una de las intervenciones urbanas más fotografiadas y rítmicas de Valparaíso. Ubicada en el <strong>Cerro Concepción</strong>, específicamente en la calle Beethoven, esta escalera rinde un homenaje visual a la música y al nombre de la calle que la alberga.</p><p><br></p><h3><strong>Música en cada peldaño</strong></h3><p>Inaugurada en 2013 como parte de una iniciativa de recuperación de espacios públicos, sus peldaños fueron pintados meticulosamente para simular las teclas de un piano de cola. El diseño juega con la perspectiva: al observarla desde la base, la escalera parece un teclado gigante que invita a los peatones a \"componer\" su propio camino mientras suben hacia la parte alta del cerro.</p><p><br></p><h3><strong>Un rincón para la cultura</strong></h3><p>La escalera no es solo un punto estético; se ha convertido en un escenario natural para músicos callejeros y eventos culturales. Su ubicación conecta el plano de la ciudad con barrios residenciales llenos de casas coloridas y pequeños talleres de artistas. Es el lugar ideal para quienes buscan una fotografía icónica que resuma la creatividad porteña, lejos de los circuitos más saturados.</p><p><br></p><h3><strong>Experiencia recomendada</strong></h3><p>Al llegar a la cima, aprovecha para explorar los alrededores del Cerro Concepción, un sector que conserva una vida de barrio muy auténtica y que ofrece vistas  impresionantes de la bahía.</p>', '[\"arte urbano\",\"escaleras\",\"escalera\",\"valpara\\u00edso\",\"escalera valpara\\u00edso\"]', NULL, NULL, NULL, 'chinoatonal', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:49:50', '2026-05-22 03:12:24', 3),
(10, 1, 'Puerta China', 'puerta-china-906', 'Cerro Concepción', NULL, -33.04180800, -71.62861600, 'Valparaíso', 'Debe ser una de las puertas más buscadas de Valparaíso. Se encuentra en el cerro Concepción en el pasaje Galvez. Se puede llegar por el pasaje Papudo o por calle Urriola.', '[\"Puerta\",\"Valpara\\u00edso\",\"Street Art\",\"arte urbano\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:50:34', '2026-04-10 04:50:34', 3),
(11, 1, 'La abuela aburrida', 'la-abuela-aburrida-802', 'Cerro Concepción', NULL, -33.04418900, -71.63017500, 'Valparaíso', 'La abuela aburrida, ubicada en calle Almirante Montt de Cerro Concepción, es uno de los pocos murales que ha perdurado por años en Valparaíso. Es un impresindible de la ciudad y del arte urbano local.', '[\"arte urbano\",\"valpara\\u00edso\"]', NULL, 'https://www.ellapitr.com', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:51:19', '2026-04-10 04:51:19', 3),
(12, 1, 'Escalera Serrano', 'escalera-serrano-930', 'Plan', NULL, -33.03779100, -71.62999700, 'Valparaíso', 'La escalera Serrano o Cienfuegos de Valparaíso se ha convertido en un atractivo, no solo por sus 166 peldaños, sino por su trabajo con mosaicos.', '[\"escalera\",\"valpara\\u00edso\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:52:40', '2026-04-10 04:52:40', 3),
(13, 1, 'Reserva Nacional Lago Peñuelas', 'reserva-nacional-lago-penuelas-745', 'Plan', NULL, -33.17474900, -71.51345900, 'Valparaíso', 'La Reserva Nacional Lago Peñuelas es un área protegida ubicada en la región de Valparaíso, Chile. Esta reserva abarca una superficie de aproximadamente 9.200 hectáreas y alberga una gran variedad de ecosistemas, incluyendo bosques, humedales y praderas. El lago Peñuelas es el principal atractivo de la reserva, ofreciendo paisajes naturales impresionantes y oportunidades para la observación de aves, la pesca y el senderismo. La reserva también es hogar de diversas especies de flora y fauna, algunas de las cuales se encuentran en peligro de extinción. La Reserva Nacional Lago Peñuelas es un destino ideal para los amantes de la naturaleza y el aire libre, ofreciendo una experiencia única y enriquecedora en contacto con la belleza natural de Chile.', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:54:31', '2026-04-10 04:54:31', 6),
(14, 1, 'Palacio Justicia', 'palacio-justicia-779', 'Plan', NULL, -33.03941800, -71.62979100, 'Valparaíso', 'Arquitectura historicista, Palacio de los Tribunales, Valparaíso, Patrimonio cultural, Arquitectura chilena.El Palacio de los Tribunales de Justicia de Valparaíso es un edificio icónico que se encuentra en el corazón de la ciudad. Construido en el siglo XIX, es un ejemplo destacado de la arquitectura historicista, inspirado en la arquitectura renacentista italiana. Su diseño refleja la influencia europea en la arquitectura chilena de la época. La fachada presenta una composición simétrica, con un cuerpo central y dos alas laterales. Está revestida de piedra y presenta una rica ornamentación, con columnas, arcos, molduras y balcones. Un ejemplo emblemático de la arquitectura chilena del siglo XIX, que combina elementos históricos con una funcionalidad moderna. Su conservación es un testimonio de la riqueza cultural y patrimonial de Valparaíso.', '[\"Arquitectura historicista\",\"Palacio de los Tribunales\",\"Valpara\\u00edso\",\"Patrimonio cultural\",\"Arquitectura chilena.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 04:59:55', '2026-04-12 17:25:29', 9),
(15, 1, 'Cuartel General del Cuerpo de Bomberos de Valparaíso', 'cuartel-general-del-cuerpo-de-bomberos-de-valparaiso-163', 'Plan', NULL, -33.03880000, -71.62853100, 'Valparaíso', 'El Cuartel General del Cuerpo de Bomberos de Valparaíso es un edificio emblemático que se encuentra en el corazón de la ciudad. Construido en 1851, es uno de los primeros cuarteles de bomberos de Chile y un ejemplo destacado de la arquitectura neoclásica de la época. El edificio presenta una fachada imponente, con una composición simétrica y elementos como columnas, arcos y frontones. La estructura está revestida de piedra y presenta una rica ornamentación, que refleja la importancia del Cuerpo de Bomberos en la historia de la ciudad. A lo largo de su historia, el Cuartel General ha sido testigo de importantes eventos y ha jugado un papel fundamental en la lucha contra incendios y catástrofes en Valparaíso. Hoy en día, es un símbolo de la identidad y la tradición de la ciudad.', '[\"Arquitectura neocl\\u00e1sica\",\"Cuerpo de Bomberos\",\"Valpara\\u00edso\",\"Patrimonio hist\\u00f3rico\",\"Edificio emblem\\u00e1tico.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 05:01:01', '2026-04-10 05:01:01', 9),
(16, 1, 'Edificio del Ministerio de la Culturas', 'edificio-del-ministerio-de-la-culturas-618', 'Plan', NULL, -33.03905100, -71.62889500, 'Valparaíso', 'El Edificio de Correos y Telegrafos de Chile, ubicado en Valparaíso, es un ejemplo destacado de la arquitectura moderna chilena del siglo XX. Construido con hormigón armado, presenta una estética innovadora y funcional, inspirada en el movimiento Bauhaus. Su diseño se caracteriza por líneas curvas, ventanas circulares y una distribución espacial que maximiza la luz natural. La forma del edificio, que recuerda a un barco, es un homenaje a la tradición marítima de la ciudad. Este edificio es considerado uno de los mejores exponentes de la arquitectura moderna en Valparaíso, y su diseño ha influido en la arquitectura chilena posterior.', '[\"Arquitectura moderna\",\"Movimiento Bauhaus\",\"Valpara\\u00edso\",\"Correos y Telegrafos\",\"Arquitectura chilena.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 05:01:50', '2026-04-10 05:01:50', 9),
(17, 1, 'Cooperativa Vitalicia', 'cooperativa-vitalicia-525', 'Plan', NULL, -33.04351600, -71.62472100, 'Valparaíso', '<p>El Edificio de la Cooperativa Vitalicia en Valparaíso es un ejemplo destacado de la arquitectura moderna chilena. Su diseño innovador y funcional refleja los principios de la arquitectura moderna, con líneas limpias, formas geométricas y una atención especial a la iluminación natural. Construido en la segunda mitad del siglo XX, el edificio es un testimonio de la evolución arquitectónica de Valparaíso durante ese período. Su estilo moderno se integra armónicamente con el entorno urbano de la ciudad.</p>', '[\"Arquitectura moderna\",\"Cooperativa Vitalicia\",\"Valpara\\u00edso\",\"Arquitectura chilena\",\"Dise\\u00f1o innovador.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 05:03:36', '2026-06-02 22:38:49', 9),
(18, 1, 'Biblioteca Santiago Severín', 'biblioteca-santiago-severin-672', 'Plan', 'https://www.bibliotecaseverin.gob.cl', -33.04515300, -71.61943700, 'Valparaíso', '<p>La Biblioteca Santiago Severín es una joya cultural en Valparaíso, Chile. Fue fundada en 1873 y es la segunda biblioteca pública más antigua de Chile, después de la Biblioteca Nacional. Su edificio actual, ubicado en la Plaza Simón Bolívar, es un ejemplo impresionante de arquitectura neoclásica y renacentista, con elementos como pilastras, tímpanos y capiteles. ALa biblioteca alberga una colección impresionante de más de 82.000 volúmenes de libros y 260.000 ejemplares de diarios y revistas. La biblioteca ha sido testigo de importantes eventos históricos y culturales, como la firma de la Ley de Institucionalidad Cultural en 2000 y la entrega del Premio Iberoamericano de Poesía Pablo Neruda en 2007. Después de sufrir daños en el terremoto de 2010, la biblioteca fue restaurada y reabierta en 2011 con mejoras significativas en su infraestructura y servicios.</p>', '[\"Biblioteca Santiago Severin\",\"Valpara\\u00edso\",\"Chile\",\"Cultura\",\"Historia\",\"Libros\",\"Conocimiento\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 05:05:37', '2026-06-09 16:33:35', 5),
(19, 1, 'Parque Cultural de Valparaíso', 'parque-cultural-de-valparaiso-811', 'Plan', NULL, -33.04636600, -71.62761700, 'Valparaíso', 'El Parque Cultural de Valparaíso es un espacio emblemático que combina la riqueza cultural y histórica de la ciudad con la naturaleza y la innovación. Ubicado en el cerro Cárcel, ofrece una vista impresionante de la ciudad y el mar. El parque alberga varios espacios culturales, como el Museo de Bellas Artes, el Archivo Histórico y la Biblioteca Municipal. También cuenta con áreas verdes, senderos peatonales y espacios para eventos y actividades culturales.', '[\"Parque Cultural de Valpara\\u00edso\",\"Valpara\\u00edso\",\"Chile\",\"Cultura\",\"Historia\",\"Naturaleza\",\"Innovaci\\u00f3n.\"]', NULL, 'https://www.parquecultural.cl', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 05:06:31', '2026-04-10 05:06:31', 5),
(20, 1, 'Bar Liberty', 'bar-liberty-512', 'Plan', NULL, -33.03660200, -71.63100700, 'Valparaíso', 'El Bar Liberty es un emblemático establecimiento ubicado en el corazón de Valparaíso, Chile. Con más de un siglo de historia, este bar es un testimonio de la rica tradición cultural y nocturna de la ciudad. El Bar Liberty es famoso por su amplia variedad de cócteles y bebidas, así como por su animada atmósfera, que atrae a visitantes y locales por igual. Es un lugar imperdible para experimentar la noche porteña y disfrutar de un trago en un ambiente único y lleno de historia. Su arquitectura y decoración interior son un ejemplo de la época en que fue construido, con elementos como madera, espejos y lámparas que crean un ambiente acogedor y clásico.', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 05:07:40', '2026-04-12 20:55:59', 8),
(21, 1, 'Escalera Calle 13', 'escalera-calle-13-706', 'Cerro Concepción', NULL, -33.04186400, -71.62875700, 'Valparaíso', '<p>Esta famosa escalera, con la letra de una canción de Calle 13, se encuentra en calle Urriola de Cerro Concepción.</p>', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 05:11:13', '2026-04-20 05:13:51', 3),
(22, 1, 'El Lechero', 'el-lechero-815', 'Plan', NULL, -33.04314400, -71.60197800, 'Valparaíso', 'Mural que representa un oficio característico del cerro. Hace décadas, la leche era repartida por personas que se transportaban en burros por los cerros de Valparaíso. El nombre del cerro es por la misma razón: cerro Los Lecheros.', '[\"mural\",\"valpara\\u00edso\",\"street art\"]', NULL, 'https://www.instagram.com/alapinta.cl', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:49:43', '2026-04-10 06:49:43', 3),
(23, 1, 'Escalera Fisher', 'escalera-fisher-843', 'Cerro Concepción', NULL, -33.04100200, -71.62807200, 'Valparaíso', '<p>La escalera Fisher fue pintada por ChinoAtonal en año 2012 y es un atractivo imperdible de la ciudad. Conecta la calle Urriola con el pasaje Galvez y se encuentra en el Cerro Concepción, donde puedes encontrar tiendas, artesania, comida y muchos miradores.</p>', '[\"Valpara\\u00edso\",\"escalera\",\"colores\"]', NULL, 'https://www.instagram.com/chinoatonal', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:50:37', '2026-04-20 05:14:55', 3),
(24, 1, 'La Industria', 'la-industria-777', 'Plan', NULL, -33.02050800, -71.63678400, 'Valparaíso', 'La Estatua \"La República\" de Valparaíso es un monumento histórico que ha generado confusión debido a su ubicación y nombre. Originalmente, la estatua se encontraba en la Plaza La República, frente a la Calle General Holley, y representaba a la República sentada sobre un sillón, todo en bronce. Sin embargo, la estatua que se encuentra en el Paseo Rubén Darío es en realidad \"La Industria\", una obra de Mathurin Moreau, escultor francés. Esta estatua representa a una mujer sentada en un yunque, con un martillo y un engranaje en sus manos. La verdadera estatua \"La República\" se perdió durante un tiempo, pero fue recuperada por la Policía de Investigaciones de Chile en 2019, después de 16 años de búsqueda. Actualmente, se encuentra en proceso de restauración para ser devuelta a su ubicación original.', '[\"Estatua La Rep\\u00fablica\",\"Valpara\\u00edso\",\"Monumento Hist\\u00f3rico\",\"Mathurin Moreau\",\"La Industria\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:51:45', '2026-04-12 20:26:16', 3),
(25, 1, 'Vida Oceanica', 'vida-oceanica-476', 'Playa Ancha', NULL, -33.02197400, -71.64089000, 'Valparaíso', 'El mural \"Vida Oceánica\" de María Martner es una obra maestra de la artista chilena, ubicada en el corazón de Valparaíso. Este mural, creado en 1994, es un ejemplo destacado del arte público y la identidad cultural de la ciudad. La obra, que mide más de 100 metros cuadrados, representa la riqueza y diversidad de la vida marina, con imágenes de peces, algas y otros elementos oceánicos. El mural es un tributo a la conexión de Valparaíso con el mar y su importancia en la historia y la economía de la ciudad. María Martner, artista y muralista chilena, es conocida por sus obras que reflejan la identidad cultural y la historia de Chile. \"Vida Oceánica\" es una de sus obras más emblemáticas y ha sido declarada Monumento Nacional.', '[\"Mural Vida Oce\\u00e1nica\",\"Mar\\u00eda Martner\",\"Valpara\\u00edso\",\"Arte p\\u00fablico\",\"Identidad cultural\",\"Monumento Nacional\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:52:54', '2026-04-12 08:51:24', 3),
(26, 1, 'Insomnia - Teatro Condell', 'insomnia-teatro-condell-641', 'Plan', NULL, -33.04640500, -71.62060900, 'Valparaíso', 'INSOMNIA Teatro Condell es un espacio cultural emblemático en Valparaíso, Chile. Fundado en 2005, comenzó como un ritual de cine de los viernes y se convirtió en un referente cultural en la ciudad. En 2011, se mudó al Teatro Condell, un espacio histórico que había sido restaurado. Desde entonces, ofrece una variedad de actividades, como estrenos de cine chileno, cine foros, talleres y franjas temáticas. En 2018, se realizó una puesta en valor del espacio, con proyectos de restauración y adecuación. Hoy en día, INSOMNIA Teatro Condell es un espacio totalmente acondicionado y con un alto estándar técnico y de comodidad.', '[\"INSOMNIA Teatro Condell\",\"Valpara\\u00edso\",\"Cine\",\"Cultura\",\"Teatro\",\"Actividades\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:53:49', '2026-04-10 06:53:49', 5),
(27, 1, 'Playa Las Torpederas', 'playa-las-torpederas-414', 'Plan', NULL, -33.02205000, -71.64439000, 'Valparaíso', 'La Playa Las Torpederas es una de las playas más emblemáticas y populares de Valparaíso, Chile. Ubicada en una bahía tranquila y protegida, esta playa ofrece un entorno relajante y pintoresco para disfrutar del sol, el mar y la arena. La playa cuenta con una amplia franja de arena dorada y aguas cristalinas, ideales para nadar, tomar el sol o simplemente disfrutar de la vista. Además, hay una variedad de servicios y actividades disponibles, como restaurantes, bares y alquiler de kayaks o paddleboards. Una de las características más destacadas de la Playa Las Torpederas es su ubicación en el corazón de Valparaíso, lo que la hace fácilmente accesible en transporte público o a pie. Además, la playa ofrece una vista impresionante de la ciudad y el puerto, lo que la hace un lugar ideal para tomar fotos y disfrutar del paisaje.', '[\"Playa Las Torpederas\",\"Valpara\\u00edso\",\"Chile\",\"Playa\",\"Mar\",\"Arena\",\"Sol\",\"Relajaci\\u00f3n\"]', NULL, 'https://www.instagram.com/playalastorpederas', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:55:05', '2026-04-10 06:55:05', 6),
(28, 1, 'Faro Punta Angeles', 'faro-punta-angeles-856', 'Plan', NULL, -33.02250000, -71.64804900, 'Valparaíso', 'El Faro Punta Ángeles es un emblemático faro ubicado en la ciudad de Valparaíso, Chile. Construido en 1837, es uno de los faros más antiguos de la costa chilena y ha sido testigo de la rica historia marítima de la región. Con una altura de 18 metros, el faro ofrece vistas impresionantes del puerto de Valparaíso y la ciudad. Su arquitectura es un ejemplo de la ingeniería naval de la época y ha sido restaurado para mantener su originalidad. El faro es un lugar popular para los turistas y los locales, que se reúnen allí para disfrutar del atardecer y la vista del mar. La zona circundante también cuenta con senderos para caminar y áreas de picnic, lo que la hace ideal para una visita familiar. Además de su importancia histórica y arquitectónica, el Faro Punta Ángeles también es un símbolo de la identidad de Valparaíso. Ha sido objeto de numerosas obras de arte y ha sido mencionado en literatura y música.', '[\"Faro Punta \\u00c1ngeles\",\"Valpara\\u00edso\",\"Chile\",\"Faro\",\"Historia\",\"Arquitectura\",\"Turismo\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:56:20', '2026-04-10 06:56:20', 1),
(29, 1, 'Palacio Baburizza', 'palacio-baburizza-790', 'Cerro Alegre', 'Paseo Yugoslavo 176', -33.04025200, -71.62886090, 'Valparaíso', '<p>Sumérgete en la elegancia de principios del siglo XX visitando el Palacio Baburizza, una impresionante mansión de estilo Art Nouveau que evoca la opulencia de la época dorada de Valparaíso. Originalmente residencia de una acaudalada familia croata, hoy alberga el Museo Municipal de Bellas Artes, donde podrás admirar una destacada colección de pintura chilena y europea de los siglos XIX y XX. Recorre sus salones ricamente decorados, déjate sorprender por sus detalles arquitectónicos y disfruta de las vistas panorámicas del puerto y la bahía desde sus balcones. Un paseo por sus jardines bien cuidados complementa esta experiencia que te transportará a un pasado lleno de encanto y sofisticación. Descubre la historia y el arte en un entorno único que captura la esencia vibrante y nostálgica de Valparaíso.</p>', '[\"Palacio Baburizza\",\"Valpara\\u00edso\",\"Chile\",\"Arquitectura\",\"Museo\",\"Cultura\"]', NULL, 'https://www.museobaburizza.cl', 'Martes a domingo de 10.00 a 18.00 horas.', NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:58:54', '2026-05-21 01:38:09', 7),
(30, 1, 'Museo del Títere y el Payaso', 'museo-del-titere-y-el-payaso-557', 'Cerro Cárcel', NULL, -33.04583000, -71.61889000, 'Valparaíso', '<p>Adéntrate en el mágico mundo del teatro de títeres y la tradición circense chilena en este singular museo. Descubre una colorida colección de marionetas de diversas épocas y técnicas, así como vestuario, fotografías y objetos relacionados con el arte del payaso. Revive la nostalgia de espectáculos entrañables y conoce la historia de importantes figuras del teatro de marionetas y el circo en Chile. Un espacio lleno de fantasía y humor que encantará a grandes y pequeños, rescatando un importante patrimonio cultural y artístico. Sumérgete en un universo de creatividad y diversión.</p>', '[\"Museo del t\\u00edtere\",\"Marionetas\",\"Payasos\",\"Circo chileno\",\"Patrimonio cultural\"]', NULL, 'https://www.teatromuseo.cl', 'Vie - Sáb y Dom de 12 a 15 hrs', NULL, 0, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 06:59:45', '2026-06-04 17:43:35', 7),
(31, 1, 'Reloj Turri', 'reloj-turri-512', 'Plan', NULL, -33.04062000, -71.62629600, 'Valparaíso', 'El Reloj Turri, una elegante torre de estilo neoclásico, se erige como un símbolo emblemático en la Plaza Sotomayor, corazón histórico de Valparaíso. Construido a principios del siglo XX, este monumento no solo marca el tiempo, sino que también testimonia la riqueza arquitectónica y el esplendor de la época dorada del puerto. Su cuidada estructura y su ubicación estratégica lo convierten en un punto de encuentro imprescindible y un excelente lugar para orientarse en la ciudad. Admira su diseño detallado, escucha sus campanadas y captura fotografías memorables con el telón de fondo del puerto y los edificios históricos circundantes. Un paseo por la plaza no está completo sin contemplar la majestuosidad del Reloj Turri, un hito que evoca la historia viva de Valparaíso.', '[\"Reloj hist\\u00f3rico\",\"Plaza Sotomayor\",\"Arquitectura neocl\\u00e1sica\",\"S\\u00edmbolo Valpara\\u00edso\",\"Punto de referencia\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:00:40', '2026-04-12 08:18:35', 9),
(32, 1, 'Iglesia San Francisco', 'iglesia-san-francisco-987', 'Cerro Barón', NULL, -33.03940100, -71.60080600, 'Valparaíso', '<p>Desde la cumbre del cerro Barón, la Iglesia San Francisco destaca como el primer vigía de los navegantes. Construida en 1845, su torre de ladrillo sirvió de referencia náutica, ganándose el apodo de \"El Faro del Barón\". Pese a los incendios, este Monumento Nacional simboliza la resiliencia porteña y la fe en el puerto. Es un hito neoclásico imprescindible que domina el horizonte de la bahía.</p>', '[\"Historia\",\"Cerro Bar\\u00f3n\",\"Monumento\",\"Arquitectura\",\"Valpara\\u00edso\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:01:57', '2026-06-02 22:17:06', 15),
(33, 1, 'Ascensor Polanco', 'ascensor-polanco-132', 'Cerro Polanco', NULL, -33.05100800, -71.60046100, 'Valparaíso', '<p><strong>*FUERA DE SERVICIO DESDE EL 2024*</strong></p><p><br></p><p>El Ascensor Polanco es una joya única en Valparaíso por su eje vertical, a diferencia de los funiculares inclinados. Inaugurado en 1915, su acceso es una experiencia cinematográfica: un túnel de 150 metros que se interna en las entrañas del cerro para llevarte a un ascensor que emerge en una torre neogótica. Con su pasarela elevada, ofrece una vista de 360 grados sobre el anfiteatro porteño, siendo un testimonio audaz de la ingeniería de principios del siglo XX.</p>', '[\"Ingenier\\u00eda\",\"Patrimonio\",\"Cerro Polanco\",\"Ascensores\",\"Historia\"]', NULL, NULL, 'CERRADO', NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:03:18', '2026-05-29 22:38:07', 16),
(34, 1, 'Ascensor Reina Victoria', 'ascensor-reina-victoria-934', 'Cerro Alegre', NULL, -33.04403600, -71.62624300, 'Valparaíso', '<p><strong>*OPERATIVO*</strong></p><p><br></p><p>Inaugurado en 1902, el Ascensor Reina Victoria es la puerta de entrada al místico Cerro Alegre. Destaca por su pronunciada pendiente y sus vagones de madera que parecen desafiar la gravedad. Al desembarcar, el visitante es recibido por el colorido arte urbano y la arquitectura de influencia británica y alemana. Es un ícono de la época de oro del puerto, conectando el ajetreado plan con la elegancia bohemia de uno de los barrios más fotogénicos de Valparaíso.</p>', '[\"Patrimonio\",\"Cerro Alegre\",\"Funicular\",\"Historia\",\"Turismo\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:12:51', '2026-05-29 22:37:45', 16),
(35, 1, 'Iglesia Luterana', 'iglesia-luterana-668', 'Cerro Concepción', NULL, -33.04274600, -71.62621500, 'Valparaíso', '<p>Dominando el Cerro Concepción, la Iglesia Luterana es un símbolo de la libertad de culto en Chile. Construida en 1897 por la comunidad alemana, su arquitectura neogótica y su distintiva techumbre roja rompen el horizonte porteño. Fue el primer templo protestante en lucir una torre y campanas, desafiando las restricciones de la época. Hoy, su silueta es parte esencial del paisaje protegido por la UNESCO y testimonio de la diversidad cultural del puerto.</p>', '[\"Patrimonio\",\"Cerro Concepci\\u00f3n\",\"Arquitectura\",\"Historia\",\"UNESCO\"]', NULL, 'https://www.iglesialuterana.cl', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:15:27', '2026-05-09 04:55:46', 15),
(36, 1, 'Mural de Mon Laferte: \"Día Uno\"', 'mural-de-mon-laferte-dia-uno-633', 'Cerro Alegre', NULL, -33.04443100, -71.62989200, 'Valparaíso', 'Ubicado en el corazón del cerro Alegre, el mural \"Día Uno\" es una obra de la reconocida artista Mon Laferte. Inaugurado en 2021, relata visualmente el ciclo de la vida y el proceso creativo. Su estilo naíf y colores vibrantes contrastan con la arquitectura histórica del barrio, consolidando a Valparaíso como una capital mundial del arte urbano. Esta pieza se ha convertido en un nuevo hito de peregrinación cultural, uniendo la música y la pintura en las paredes del puerto.', '[\"Mon Laferte\",\"Arte Urbano\",\"Cerro Alegre\",\"Cultura\",\"Valpara\\u00edso\"]', NULL, 'https://www.instagram.com/monlafertevisual', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:20:42', '2026-04-12 07:35:26', 3),
(37, 1, 'Iglesia San Luis Gonzaga', 'iglesia-san-luis-gonzaga-285', 'Cerro Alegre', NULL, -33.04166200, -71.62534500, 'Valparaíso', 'La Iglesia San Luis Gonzaga, ubicada en el corazón del Cerro Alegre, es un pilar de la identidad barrial desde finales del siglo XIX. Su diseño neoclásico, de líneas sobrias y elegantes, destaca por su imponente torre que se divisa desde diversos puntos del puerto. Construida para servir a la creciente comunidad del sector, ha resistido los embates del tiempo y los sismos, consolidándose como un hito espiritual y arquitectónico que dialoga armoniosamente con las casonas patrimoniales que la rodean.', '[\"Cerro Alegre\",\"Patrimonio\",\"Iglesia\",\"Arquitectura\",\"Historia\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:23:13', '2026-05-09 04:53:39', 15),
(38, 1, 'Iglesia del Corazón de María', 'iglesia-del-corazon-de-maria-933', 'Plan', NULL, -33.05142800, -71.60845400, 'Valparaíso', 'Ubicada en el barrio El Almendral, la Iglesia del Corazón de María es un baluarte de la fe porteña. Inaugurada a fines del siglo XIX, destaca por su imponente cúpula y su arquitectura neorrománica que resguarda a la Virgen del Corazón de María, cuya imagen sobrevivió al devastador terremoto de 1906. Es un centro de devoción popular que ha resistido incendios y sismos, manteniéndose como un faro de esperanza y resiliencia en una de las zonas más vibrantes del plan de la ciudad.', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:24:32', '2026-04-12 08:24:01', 4),
(39, 1, 'Parroquia Sagrado Corazón de Jesús', 'parroquia-sagrado-corazon-de-jesus-330', 'Cerro Barón', NULL, -33.04170500, -71.59969000, 'Valparaíso', 'La Parroquia Sagrado Corazón de Jesús, ubicada en el Cerro Barón, es un emblema de la fe porteña desde su fundación a inicios del siglo XX. Su arquitectura de estilo gótico francés destaca por sus vitrales y una torre que se alza como un faro espiritual sobre la bahía. Este templo no solo es un refugio de paz, sino también un punto de encuentro comunitario que ha sido testigo de la evolución social del cerro, consolidándose como un hito patrimonial que embellece la silueta de Valparaíso.', '[\"Cerro Bar\\u00f3n\",\"Patrimonio\",\"Arquitectura\",\"Historia\",\"Iglesia\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:25:21', '2026-04-24 17:20:31', 4),
(40, 1, 'Iglesia de la Medalla Milagrosa', 'iglesia-de-la-medalla-milagrosa-434', 'Plan', NULL, -33.05157000, -71.61131700, 'Valparaíso', 'La Iglesia de la Medalla Milagrosa, en el dinámico Barrio Almendral, es un tesoro neogótico que destaca por su verticalidad y elegancia. Su estructura, caracterizada por arcos ojivales y una atmósfera de recogimiento, ha sido por décadas un refugio espiritual frente al ajetreo del plan de la ciudad. Aunque menos imponente en tamaño que otras parroquias, su valor patrimonial y devocional la convierten en un hito fundamental que narra la historia religiosa y el desarrollo urbano de Valparaíso.', '[\"Barrio Almendral\",\"Patrimonio\",\"Historia\",\"Arquitectura\",\"Iglesia\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 07:26:22', '2026-05-09 05:05:28', 15),
(41, 1, 'Mirador Purcell', 'mirador-purcell-463', 'Cerro Cordillera', NULL, -33.03792100, -71.63161800, 'Valparaíso', 'Ubicado en el cerro Cordillera, el Mirador Purcell es uno de los balcones más auténticos hacia el origen de la ciudad. Desde este punto, la mirada se clava en el corazón del Barrio Puerto, destacando la cúpula de la Iglesia La Matriz entre el laberinto de techumbres antiguas. Es el sitio ideal para observar la dualidad porteña: la quietud de los edificios históricos frente al movimiento incesante de las grúas y contenedores en el terminal marítimo, el alma viva de Valparaíso.', '[\"Mirador\",\"Barrio Puerto\",\"Patrimonio\",\"Vistas\",\"Historia\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:30:57', '2026-04-10 16:31:44', 1),
(42, 1, 'Mirador Ongolmo', 'mirador-ongolmo-464', 'Cerro Mariposas', NULL, -33.05094100, -71.61673100, 'Valparaíso', 'El Mirador Ongolmo, ubicado en la sinuosa Avenida Baquedano por Cerro Mariposas, ofrece una de las perspectivas más amplias y vertiginosas de la bahía. Desde este balcón natural, la ciudad se despliega como un anfiteatro infinito, donde los cerros parecen descender en cascada hacia el mar. Es un punto privilegiado para contemplar el horizonte marino y la silueta de los buques, capturando la esencia residencial y la magnitud geográfica que define el carácter indómito de Valparaíso.', '[\"Mirador\",\"Cerro Mariposas\",\"Vistas\",\"Bah\\u00eda\",\"Valpara\\u00edso\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:33:17', '2026-04-10 16:33:17', 1),
(43, 1, 'Cinzano', 'cinzano-793', 'Plan', 'Bohemia, Historia, Plaza Aníbal Pinto, Tradición, Restaurante', -33.04313800, -71.62485800, 'Valparaíso', 'El Cinzano, fundado en 1896 frente a la Plaza Aníbal Pinto, es el epicentro de la bohemia porteña tradicional. Por sus mesas de madera han pasado generaciones de poetas, marinos y artistas, atraídos por la nostalgia de sus tangos y boleros en vivo. Este mítico local no solo es un restaurante, sino un museo vivo de la identidad de Valparaíso, donde el aroma a chorrillana y el sonido del piano evocan los años de gloria del puerto. Es una parada obligatoria para sentir el alma de la ciudad.', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:35:22', '2026-04-12 08:02:21', 8),
(44, 1, 'J. Cruz M', 'j-cruz-m-704', 'Plan', NULL, -33.04575700, -71.62225100, 'Valparaíso', 'El J. Cruz M. es una leyenda oculta en el corazón de Valparaíso, famoso por ser el lugar de nacimiento de la chorrillana. Más que un restaurante, es un gabinete de curiosidades donde las paredes, atiborradas de antigüedades y recuerdos, cuentan la historia sentimental del puerto. Ubicado en un estrecho pasaje, su atmósfera bohemia y compartida invita a sumergirse en la cultura popular porteña. Es un rincón donde el tiempo se detiene entre música de antaño y el sabor más icónico de la zona.', '[\"Bohemia\",\"Chorrillana\",\"Gastronom\\u00eda\",\"Tradici\\u00f3n\",\"Historia\"]', NULL, 'https://www.jcruz.cl/', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:37:18', '2026-04-14 02:52:23', 8),
(45, 1, 'Restaurante La Españita', 'restaurante-la-espanita-983', 'Plan', NULL, -33.04787200, -71.61296300, 'Valparaíso', '<p>Con más de 80 años de historia, el Restaurante La Españita es un pilar de la gastronomía tradicional en el barrio El Almendral. Este emblemático local de la Avenida Francia ha alimentado a generaciones de porteños con su cocina de alma casera y su reconocida pastelería. Es un refugio de sabor y nostalgia que resiste el paso del tiempo, manteniendo viva la esencia del Valparaíso de antaño. Su ambiente familiar y recetas heredadas lo convierten en un baluarte vivo del patrimonio culinario del puerto.</p>', '[\"Gastronom\\u00eda\",\"El Almendral\",\"Tradici\\u00f3n\",\"Centenario\",\"Historia\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:38:41', '2026-05-08 17:15:52', 8),
(46, 1, 'Sala Rivoli', 'sala-rivoli-457', 'Plan', NULL, -33.04853000, -71.61032900, 'Valparaíso', 'La Sala Rivoli, ubicada en el tradicional barrio El Almendral, es un espacio que respira la nostalgia del antiguo cine porteño. Tras años de silencio, este edificio ha resurgido como un vibrante centro cultural y sala de espectáculos, devolviendo la vida artística a la calle Victoria. Con su imponente arquitectura restaurada, el Rivoli hoy acoge cine, teatro y música en vivo, consolidándose como un faro de la bohemia culta y un punto de encuentro esencial para la revitalización del plan de Valparaíso.', '[\"Cultura\",\"El Almendral\",\"Espect\\u00e1culos\",\"Patrimonio\"]', NULL, 'https://salarivoli.cl', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:40:03', '2026-04-10 16:40:03', 10),
(47, 1, 'Casa Crucero', 'casa-crucero-680', 'Cerro Alegre', NULL, -33.04120900, -71.62944700, 'Valparaíso', '<p>La Casa Crucero, ubicada en el corazón del Cerro Alegre, es una joya de la arquitectura porteña que destaca por su singular forma inspirada en las líneas de una embarcación. Construida para adaptarse a la geografía rebelde del cerro, esta casona patrimonial evoca la estrecha relación de Valparaíso con el mar. Con sus ventanales que parecen ojos de buey y su estructura escalonada, representa el ingenio de los arquitectos que desafiaron la pendiente para crear hogares con alma náutica.</p>', '[\"Cerro Alegre\",\"Arquitectura\",\"Patrimonio\",\"Historia\",\"Vistas\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:41:14', '2026-05-08 17:15:27', 9),
(48, 1, 'Paseo 21 de Mayo', 'paseo-21-de-mayo-139', 'Cerro Artillería', NULL, -33.03234100, -71.63053500, 'Valparaíso', '<p>El Paseo 21 de Mayo, situado en la cima del cerro Artillería, es el mirador más emblemático de Valparaíso. Inaugurado a fines del siglo XIX, ofrece una panorámica sobrecogedora del puerto y la bahía. Custodiado por el imponente Museo Marítimo Nacional, su terraza es el lugar donde la historia naval se funde con el ajetreo diario de los buques. Sus puestos de artesanía y su entorno histórico lo convierten en un balcón privilegiado para contemplar el espíritu indómito de la \"Joya del Pacífico\".</p>', '[\"Mirador\",\"Cerro Artiller\\u00eda\",\"Patrimonio\",\"Historia\",\"Turismo\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:42:23', '2026-05-08 17:15:12', 1),
(49, 1, 'Parque Merced', 'parque-merced-219', 'Cerro Merced', NULL, -33.06050600, -71.60421700, 'Valparaíso', 'El Parque Merced, ubicado en el cerro del mismo nombre, es un símbolo de resiliencia y renovación urbana. Este espacio surge como un pulmón verde tras el gran incendio de 2014, transformándose en un lugar de encuentro comunitario y recreación. Con sus senderos, áreas de juegos y anfiteatro, ofrece una vista privilegiada hacia los cerros vecinos y la bahía, rescatando la identidad barrial y proporcionando un entorno natural que fortalece el tejido social del Valparaíso profundo.', '[\"Cerro Merced\",\"Parque\",\"Naturaleza\",\"Comunidad\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:54:33', '2026-04-12 08:26:46', 6),
(50, 1, 'Mirador El Vergel', 'mirador-el-vergel-798', 'Cerro La Cruz', NULL, -33.05517800, -71.61377000, 'Valparaíso', 'El Mirador El Vergel, ubicado en el cerro La Cruz, es un sitio cargado de mística porteña. Desde este balcón se obtiene una de las vistas más icónicas del puerto, destacando la famosa insignia de Santiago Wanderers, símbolo de la pasión incondicional por el Decano del fútbol chileno. Es un lugar que fusiona la identidad deportiva con la inmensidad de la bahía, permitiendo apreciar la vida cotidiana de los cerros y el movimiento marítimo en un entorno profundamente barrial y auténtico.', '[\"Cerro La Cruz\",\"Santiago Wanderers\",\"Mirador\",\"Identidad\",\"Bah\\u00eda\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:57:42', '2026-04-12 08:29:26', 1),
(51, 1, 'Catedral de Valparaíso', 'catedral-de-valparaiso-154', 'Almendral', NULL, -33.04621800, -71.61921800, 'Valparaíso', '<p>La Catedral de Valparaíso, situada frente a la Plaza Victoria en el barrio El Almendral, es el principal templo católico del puerto. Su construcción, que se extendió por décadas hasta 1950, presenta una arquitectura neogótica con arcos abovedados y hermosos rosetones que filtran la luz marina. Tras resistir diversos sismos, este Monumento Nacional permanece como un pilar espiritual y un hito arquitectónico inconfundible, resguardando en su cripta la historia y la fe de la diócesis porteña.</p>', '[\"Catedral\",\"El Almendral\",\"Patrimonio\",\"Arquitectura\",\"Historia\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 16:58:51', '2026-05-08 17:14:39', 15),
(52, 1, 'Acantilados Federico Santa María', 'acantilados-federico-santa-maria-300', 'Laguna Verde', NULL, -33.08113700, -71.65696900, 'Valparaíso', 'Los Acantilados Federico Santa María, en el extremo sur de Playa Ancha, son el balcón natural más imponente del litoral central. Estas vertiginosas murallas de roca caen directamente al Pacífico, ofreciendo un paisaje salvaje donde el viento y el romper de las olas dominan los sentidos. Es un sitio de enorme valor geológico y un refugio para la avifauna local, permitiendo al visitante conectar con la fuerza indómita del mar, lejos del bullicio urbano del puerto principal.', '[\"Playa Ancha\",\"Acantilados\",\"Naturaleza\",\"Paisaje\",\"Valpara\\u00edso\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:01:01', '2026-04-12 08:19:17', 6),
(53, 1, 'Muelle Prat', 'muelle-prat-801', 'Barrio Puerto', NULL, -33.03756500, -71.62727800, 'Valparaíso', '<p>El Muelle Prat es la ventana histórica y el pulso vibrante de Valparaíso. Situado frente a la Plaza Sotomayor, este paseo marítimo permite observar de cerca el movimiento de los grandes buques y las faenas portuarias que dieron vida a la ciudad. Es el punto de partida de las tradicionales lanchas de turismo, donde antiguos botes de madera invitan a recorrer la bahía. Entre el aroma a salitre y el grito de las gaviotas, el muelle ofrece la conexión más pura entre el porteño y su mar.</p>', '[\"Patrimonio\",\"Puerto\",\"Turismo\",\"Muelle Prat\",\"Valpara\\u00edso\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:02:00', '2026-05-08 17:12:43', 1),
(54, 1, 'Paseo Dimalow', 'paseo-dimalow-725', 'Cerro Alegre', NULL, -33.04381500, -71.62690200, 'Valparaíso', 'El Paseo Dimalow, en el corazón del Cerro Alegre, es uno de los rincones más mágicos y bohemios de Valparaíso. Conectado por el histórico Ascensor Reina Victoria, este paseo peatonal destaca por sus coloridos murales de arte urbano y su arquitectura que mezcla influencias europeas con el ingenio porteño. Sus terrazas ofrecen una vista privilegiada del Cerro Concepción y la bahía, creando una atmósfera única donde la historia patrimonial se funde con la vibrante vida cultural y gastronómica del puerto.', '[\"cerro Alegre\",\"Patrimonio\",\"Arte Urbano\",\"Vistas\",\"Paseo\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:03:11', '2026-04-13 19:40:38', 1),
(55, 1, 'Paseo Wheelwright', 'paseo-wheelwright-124', 'Almendral', NULL, -33.04093800, -71.60626700, 'Valparaíso', '<p>El Paseo Wheelwright bordea la costa de Valparaíso con una vista directa al mar y un ambiente ideal para caminar, andar en bicicleta o descansar frente al océano; se puede acceder fácilmente por Muelle Barón o por Caleta Portales, y cuenta con bancas, ciclovía, miradores y juegos para niños, siendo un espacio perfecto para disfrutar en familia y contemplar el paisaje porteño.</p>', '[\"paseo\",\"mar\",\"mirador\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:03:57', '2026-06-11 23:40:13', 1),
(56, 1, 'Salto del Agua', 'salto-del-agua-192', 'Placilla', NULL, -33.11468500, -71.60078600, 'Valparaíso', 'El Salto del Agua, ubicado en la localidad de Placilla de Peñuelas, es una de las joyas naturales más impresionantes y desconocidas de la comuna de Valparaíso. Se trata de una espectacular caída de agua de aproximadamente 100 metros de altura, enmarcada por acantilados de roca y una densa vegetación de bosque nativo esclerófilo. Este sitio no solo destaca por su belleza escénica y su potencial para el senderismo, sino también por su valor histórico, al ser parte de la cuenca que abasteció de agua al puerto a principios del siglo XX. Es un refugio de biodiversidad que invita a la desconexión total, ofreciendo una perspectiva rural y salvaje a pocos minutos del centro urbano.', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:04:56', '2026-04-10 17:04:56', 6),
(57, 1, 'Ascensor San Agustín', 'ascensor-san-agustin-961', 'Cerro Cordillera', NULL, -33.04051300, -71.63170700, 'Valparaíso', '<p><strong>*OPERATIVO*</strong></p><p><br></p><p>El Ascensor San Agustín, ubicado en el cerro Cordillera, es uno de los funiculares más singulares de Valparaíso debido a su particular trayecto que atraviesa el interior de una edificación. Inaugurado en 1913, este ascensor conecta la transitada calle José Tomás Ramos con la parte alta del cerro, facilitando el acceso a una zona residencial cargada de historia. Su estación inferior está integrada armoniosamente en la fachada de un edificio, lo que lo convierte en un testimonio del ingenio arquitectónico porteño para optimizar el espacio en la intrincada geografía de la ciudad.</p>', '[\"Patrimonio\",\"Ascensor\",\"Cerro San Agust\\u00edn\",\"Ingenier\\u00eda\",\"Valpara\\u00edso\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:07:09', '2026-05-29 22:37:38', 16);
INSERT INTO `puntosinteres` (`id`, `user_id`, `title`, `slug`, `sector`, `direccion`, `lat`, `lng`, `ciudad`, `description`, `tags`, `video_url`, `enlace`, `horario`, `autor`, `activo`, `es_cliente`, `modulos_habilitados`, `oferta_del_dia`, `oferta_activa`, `oferta_expira_at`, `descripcion_busqueda`, `imagen_perfil`, `eliminado`, `created_at`, `updated_at`, `categoria_id`) VALUES
(58, 1, 'Ascensor Cordillera', 'ascensor-cordillera-762', 'Cerro Cordillera', NULL, -33.03771700, -71.63001800, 'Valparaíso', '<p><strong>*OPERATIVO*</strong></p><p><br></p><p>El Ascensor Cordillera, inaugurado en 1886, es el segundo ascensor más antiguo de Valparaíso y uno de los más emblemáticos del sector fundacional. Conecta la calle Serrano, en el plan de la ciudad, con el Cerro Cordillera, desembocando en la pintoresca Plaza Eleuterio Ramírez. Es conocido por ser uno de los más empinados y por ofrecer una transición inmediata desde el ajetreo comercial del puerto hacia la vida pausada y residencial de los cerros, manteniendo sus carros de madera que evocan la época dorada de la ingeniería ferroviaria porteña.</p>', '[\"Patrimonio\",\"Ascensor\",\"Cerro Cordillera\",\"Monumento Hist\\u00f3rico\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:08:43', '2026-05-29 22:37:30', 16),
(59, 1, 'Ascensor Espíritu Santo', 'ascensor-espiritu-santo-970', 'Cerro Bellavista', NULL, -33.04725000, -71.62118400, 'Valparaíso', '<p><strong>*OPERATIVO*</strong></p><p><br></p><p>El Ascensor Espíritu Santo, inaugurado en 1911, es la vía de acceso predilecta hacia el corazón del Cerro Bellavista. Conecta la calle Aldunate, en el plan de la ciudad (detrás de la Plaza Victoria), con la calle Rudolph en la parte alta. Este funicular es especialmente valorado por turistas y amantes del arte, ya que su estación superior desemboca directamente en el inicio del Museo a Cielo Abierto, permitiendo un recorrido descendente entre murales de destacados artistas chilenos y latinoamericanos.</p>', '[\"Patrimonio\",\"Cerro Bellavista\",\"Museo a Cielo Abierto\",\"Ascensor\",\"Valpara\\u00edso\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:09:58', '2026-05-29 22:37:22', 16),
(60, 1, 'Plaza Mena', 'plaza-mena-894', 'Cerro Bellavista', NULL, -33.05295800, -71.62176900, 'Valparaíso', '<p>La <strong>Plaza Mena</strong>, ubicada en el corazón del <strong>Cerro Bellavista</strong>, es conocida popularmente como la <strong>\"Plaza de los Poetas\"</strong>. Es un espacio público encantador y bohemio que rinde homenaje a grandes figuras de la literatura chilena como Pablo Neruda, Vicente Huidobro y Gabriela Mistral. Lo que hace verdaderamente único a este rincón no es solo su valor estético, sino su origen: los coloridos mosaicos que decoran sus muros, escaños y jardineras <strong>fueron creados e instalados de forma comunitaria por los propios vecinos del cerro</strong>, transformando la plaza en un símbolo vivo de autogestión, memoria y amor por su barrio.</p><p><br></p><h3><strong>Un hito del Museo a Cielo Abierto</strong></h3><p>Este punto es un nodo neurálgico para quienes recorren el circuito del <strong>Museo a Cielo Abierto</strong>. El entorno de la plaza está impregnado de arte urbano, talleres de artistas y una atmósfera tranquila que invita al descanso. Las esculturas de los poetas, rodeadas por el minucioso trabajo en mosaico de la comunidad, crean un diálogo perfecto entre la alta literatura y la expresión popular porteña.</p><p><br></p><h3><strong>Atmósfera y Vida de Barrio</strong></h3><p>Sentarse en los bancos de la Plaza Mena permite experimentar la auténtica vida de barrio de Valparaíso. Entre casas de colores, gatos que pasean al sol y el sonido lejano de los talleres de grabado y pintura, este rincón es el reflejo exacto de cómo Bellavista ha sabido defender su herencia cultural de la mano de su gente.</p>', '[\"Cerro Bellavista\",\"Patrimonio\",\"Arte Urbano\",\"Plazas\",\"Cultura\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:11:20', '2026-05-23 17:40:33', 14),
(61, 1, 'Mural Cafetería Plaza Waddington', 'mural-cafeteria-plaza-waddington-384', 'Playa Ancha', NULL, -33.02765500, -71.63582000, 'Valparaíso', '<p>Mural creado por el artista Sebastian Varas Mackenzie.</p>', '[\"mural\",\"street art\"]', NULL, 'https://www.instagram.com/varasmackenzie/', NULL, 'varasmackenzie', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:14:53', '2026-05-08 17:10:33', 3),
(62, 1, 'Museo del Inmigrante', 'museo-del-inmigrante-256', 'Cerro Alegre', 'Concepción 499', -33.04273700, -71.62716300, 'Valparaíso', '<p>Destino Valparaíso es un centro cultural, artístico y gastronómico de más de 5.000 m² emplazado en el edificio del ex Colegio Alemán, en pleno Cerro Concepción. El proyecto, liderado por la familia Dib, rehabilitó este monumento histórico que estuvo en desuso desde el terremoto de 1985. Su pieza central es el Museo del Inmigrante, una experiencia inmersiva de 1.850 m² que utiliza tecnología de vanguardia para narrar la historia de las colonias extranjeras (alemanes, británicos, italianos, árabes, entre otros) que forjaron la identidad del puerto entre 1850 y 1950. El complejo cuenta además con un teatro, tiendas de oficios, espacios gastronómicos multiculturales y un mirador de 360° hacia el anfiteatro natural de la ciudad.</p>', '[]', NULL, 'https://www.instagram.com/destinovalpo/', 'Martes y miércoles: 10:00 hrs. a 20:00 hrs. Jueves: 10:00 hrs. a 22:00 hrs. Viernes y sábado: 10:00 hrs. a 23:00 hrs. Domingo: 10:00 hrs. a 20:00 hrs.', NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:16:01', '2026-05-21 04:04:01', 7),
(63, 1, 'Ascensor El Peral', 'ascensor-el-peral-804', 'Cerro Alegre', 'Plaza de la Justicia 73', -33.03943800, -71.62948100, 'Valparaíso', '<p><strong>*OPERATIVO*</strong></p><p><br></p><p>El Ascensor El Peral, inaugurado el 7 de diciembre de 1901, es uno de los funiculares más icónicos y visitados de Valparaíso. Este ascensor fue un hito tecnológico en su época, siendo el primero en la ciudad en utilizar un motor a vapor. Su importancia radica en que conecta el centro neurálgico administrativo (el \"plan\") con uno de los sectores residenciales y turísticos más bellos: el Cerro Alegre. La estación inferior se encuentra en la Plaza de la Justicia, justo a un costado del edificio de la Comandancia en Jefe de la Armada y frente a la Plaza Sotomayor. Al subir, el trayecto de 55 metros te deja directamente en el Paseo Yugoslavo, un mirador espectacular que alberga el Palacio Baburizza (Sede del Museo Municipal de Bellas Artes).</p>', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-10 17:17:09', '2026-05-29 22:37:14', 16),
(64, 2, 'El Pionero', 'el-pionero-991', 'Cerro Concepción', 'Urriola 007', -33.04214600, -71.62868100, 'Valparaíso', '<p>Escondido entre los icónicos adoquines del Cerro Concepción, <strong>El Pionero no es solo un bar; es una cápsula del tiempo</strong>. Fundado conceptualmente sobre las historias de los antiguos navegantes que cruzaban el Estrecho de Magallanes, este rincón ofrece la mejor vista panorámica del puerto, donde las grúas y los buques parecen juguetes iluminados bajo la noche porteña. A<strong>l entrar, te recibirá el aroma de la madera antigua y la brisa marina.</strong> Nuestra decoración, compuesta por brújulas oxidadas, mapas originales del siglo XIX y faroles de cobre, crea una atmósfera íntima y bohemia que ha inspirado a músicos y poetas por décadas.</p><p>Ya sea que busques el refugio de un Pisco Sour tras caminar por los murales del cerro, o quieras probar el auténtico sabor del Caldillo de Congrio, El Pionero es tu puerto seguro. Ven a vivir la verdadera mística de Valparaíso, donde cada brindis cuenta una historia de altamar.</p>', '[\"patrimonio\",\"bar\",\"bohemia\"]', 'https://www.youtube.com/watch?v=BfjwdwqAU7Q&pp=ugMGCgJlcxABugUEEgJlc8oFDGJhciBhbGVtYW5pYdgHAQ%3D%3D', 'https://www.instagram.com/elpionerodevalparaiso/', 'Lunes a domingo desde las 10 AM', NULL, 1, 1, '[\"oferta_del_dia\",\"agenda\",\"menu_del_dia\",\"carta\"]', '<p>2 shop calafate por $5.000.</p>', 1, NULL, NULL, 'perfiles/4FP3IB8qkx2lU1hw6bFbO8YXksPwTEtdCHMTCdwT.png', 0, '2026-04-12 04:46:16', '2026-06-08 02:03:58', 8),
(65, 1, 'Plaza Sotomayor', 'plaza-sotomayor-272', 'Barrio Puerto', NULL, -33.04720000, -71.62970000, 'Valparaíso', '<p>La Plaza Sotomayor es el corazón cívico y administrativo de Valparaíso, además de ser el punto de conexión principal entre el puerto y la ciudad. Es reconocida como la plaza más grande y representativa del Barrio Puerto, rodeada de edificios de gran valor arquitectónico e histórico.\r\n\r\nEn el centro de la plaza se encuentra el Monumento a los Héroes de Iquique, dedicado a los marinos chilenos que participaron en el Combate Naval de Iquique en 1879. Bajo este monumento se ubica una cripta que resguarda los restos de Arturo Prat y otros tripulantes de la Corbeta Esmeralda.\r\n\r\nAlrededor de la plaza destacan importantes edificaciones:\r\n\r\n    Edificio de la Comandancia en Jefe de la Armada: Un imponente palacio de estilo neoclásico francés (antigua Intendencia).\r\n\r\n    Edificio de Correos de Chile.\r\n\r\n    Ministerio de las Culturas, las Artes y el Patrimonio.</p>', '[\"plaza sotomayor\",\"monumento heroes de iquique\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 08:14:21', '2026-05-08 17:05:22', 14),
(66, 1, 'Plaza Victoria', 'plaza-victoria-284', 'Almendral', NULL, -33.04626300, -71.61980900, 'Valparaíso', '<p>La Plaza de la Victoria es uno de los espacios públicos más importantes y concurridos del plan de Valparaíso. Situada en el sector de El Almendral, actúa como un punto de encuentro vital que conecta la zona comercial con importantes hitos culturales y religiosos de la ciudad.\r\n\r\nOriginalmente llamada Plaza de Orrego, recibió su nombre actual tras la victoria chilena en la Guerra contra la Confederación Perú-Boliviana. El diseño de la plaza destaca por su simetría y su variada vegetación, pero su mayor atractivo son las piezas de arte que alberga:\r\n\r\n-Las Esculturas de las Cuatro Estaciones: Cuatro figuras femeninas de hierro que representan la Primavera, el Verano, el Otoño y el Invierno, ubicadas en los senderos principales.\r\n\r\n - Entorno Arquitectónico: A sus costados se encuentran la Catedral de Valparaíso y el emblemático Edificio de la Biblioteca Santiago Severín.\r\n\r\nEs un lugar con mucha vida local, donde se instalan ferias de libros, se realizan actos cívicos y es el paso obligado para quienes se dirigen a los centros comerciales cercanos o a las paradas de trolebuses.</p>', '[\"Plaza\",\"Patrimonio\",\"El Almendral\",\"Cultura\",\"Centro C\\u00edvico\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 08:22:47', '2026-05-12 03:04:30', 14),
(67, 1, 'Mercado Cardonal', 'mercado-cardonal-213', 'Almendral', NULL, -33.04522900, -71.60751300, 'Valparaíso', 'El Mercado Cardonal es uno de los centros de abastecimiento más importantes y tradicionales de Valparaíso. Ubicado en el barrio de El Almendral, este imponente edificio de dos pisos destaca por su arquitectura de ladrillo y hierro, habiendo sobrevivido a diversos terremotos desde su reconstrucción a principios del siglo XX.\r\n\r\nEn su primer piso, el mercado es un estallido de colores y aromas donde se comercializan frutas, verduras y legumbres frescas traídas directamente desde los valles de la zona central. En el segundo nivel, se encuentra una variada oferta gastronómica de cocinerías tradicionales, donde se pueden degustar platos típicos chilenos y mariscos a precios populares, siendo un punto de encuentro auténtico para la comunidad porteña y los visitantes.', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 08:33:12', '2026-04-18 03:05:45', 8),
(68, 1, 'Paseo Gervasoni', 'paseo-gervasoni-657', 'Cerro Concepción', NULL, -33.04127200, -71.62652000, 'Valparaíso', '<p>El Paseo Gervasoni, ubicado en el Cerro Concepción, es uno de los miradores más antiguos y encantadores de Valparaíso. Se caracteriza por sus calles empedradas, faroles antiguos y una vista privilegiada en 180 grados hacia la bahía, el plan de la ciudad y el Cerro Alegre.\r\n\r\nEste paseo es un hito arquitectónico y cultural donde se encuentran casonas señoriales del siglo XIX, hoy convertidas en hoteles boutique y cafeterías. Es famoso por albergar la estación superior del Ascensor Concepción y por ser el hogar del histórico edificio de El Mercurio de Valparaíso en su parte baja, cuya arquitectura se aprecia desde las barandas del mirador. Su atmósfera bohemia y tranquila lo convierte en un punto imperdible para quienes buscan la esencia del patrimonio porteño.</p>', '[\"Patrimonio\",\"Mirador\",\"Cerro Concepci\\u00f3n\",\"Ascensor Concepci\\u00f3n\",\"Arquitectura\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 08:35:42', '2026-04-30 01:41:04', 1),
(69, 1, 'Iglesia Presbiteriana de Valparaíso', 'iglesia-presbiteriana-de-valparaiso-644', 'Almendral', 'Condell 1502', -33.04621800, -71.62166500, 'Valparaíso', '<p>La Iglesia Presbiteriana de Valparaíso, ubicada en la calle Condell, es un templo de gran relevancia histórica y arquitectónica para la ciudad. Fundada en la segunda mitad del siglo XIX, representa la presencia y la influencia de la comunidad británica y de fe protestante en el desarrollo de Valparaíso durante su época de mayor esplendor comercial.\r\n\r\nEl edificio destaca por su estilo gótico simplificado y su estructura sólida, diseñada para resistir los embates sísmicos de la zona. Más allá de su valor religioso, la iglesia es un testimonio del pluralismo cultural que caracterizó al puerto, acogiendo a inmigrantes que trajeron consigo sus tradiciones y creencias, contribuyendo así al crisol de identidades que hoy es patrimonio de la humanidad.</p>', '[\"Patrimonio\",\"Religi\\u00f3n\",\"Historia Inmigrante\",\"Arquitectura\",\"Calle Condell\"]', 'https://youtu.be/kd_ej6qldXE', 'https://linktr.ee/ipchvalparaiso?fbclid=IwY2xjawRIAYNleHRuA2FlbQIxMABicmlkETFhQmZWbk1xRGhIWFg2NEZkc3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHhEZYl5Zqt823sKNPT1f_Y4AaHA84roxJgdKn-BZj9QXgEMfhMj3eufYH6Hl_aem_lsj6YTlfciaArMdNSdeKTA', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 08:41:13', '2026-05-08 17:01:18', 15),
(70, 1, 'Parroquia Sagrado Corazón', 'parroquia-sagrado-corazon-243', 'Cerro Barón', 'General Belgrano 385', -33.04170300, -71.59945800, 'Valparaíso', '<p>La Parroquia Sagrado Corazón de Jesús, ubicada en la cima del Cerro Barón, es uno de los hitos arquitectónicos y espirituales más visibles de Valparaíso. Su imponente estructura de ladrillo y su torre campanario no solo sirven como guía espiritual, sino también como un punto de referencia geográfico que domina la entrada a la ciudad desde el sector de la Avenida España.\r\n\r\nConstruida a principios del siglo XX, la iglesia destaca por su estilo neogótico simplificado y su capacidad para congregar a la comunidad de uno de los cerros con mayor tradición ferroviaria y obrera del puerto. Desde su explanada, se obtiene una de las vistas más despejadas de la bahía y del sector de la Maestranza Barón, lo que la convierte en un lugar de gran valor tanto patrimonial como escénico.</p>', '[\"Patrimonio\",\"Cerro Bar\\u00f3n\",\"Religi\\u00f3n\",\"Arquitectura\",\"Mirador\"]', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 08:49:37', '2026-05-08 17:55:20', 15),
(71, 1, 'Catedral Anglicana de San Pablo', 'catedral-anglicana-de-san-pablo-577', 'Cerro Concepción', 'Pilcomayo 566', -33.04236000, -71.62728900, 'Valparaíso', '<p>La Catedral Anglicana de San Pablo (St. Paul’s Cathedral) es uno de los tesoros históricos y arquitectónicos más fascinantes de Valparaíso. Ubicada en el corazón del Cerro Concepción, ofrece una ventana única a la influencia británica que dio forma a la ciudad en el siglo XIX.\r\n\r\nUn Templo \"Camuflado\" por la Historia\r\n\r\nInaugurada en 1858, su arquitectura cuenta una historia de resiliencia. En esa época, Chile no permitía la libertad de culto, por lo que el edificio debía cumplir con restricciones estrictas: no podía tener cruz, torre, campanario ni una puerta principal hacia la calle que delatara su función como templo no católico. El arquitecto e ingeniero William Lloyd (quien también construyó el ferrocarril Valparaíso-Santiago) diseñó un edificio de estilo neogótico, pero de apariencia externa sobria y funcional.\r\nLo que no puedes perderte\r\n\r\n    Los Vitrales: Son considerados de los más bellos de Chile. Recientemente restaurados, muchos fueron traídos desde Londres y fabricados por talleres de renombre como Lavers &amp; Westlake y uno asociado al movimiento Arts &amp; Crafts de William Morris.\r\n\r\n    El Órgano de Tubos: Adquirido en 1903 en memoria de la Reina Victoria, es una pieza histórica que aún funciona perfectamente.\r\n\r\n    Arquitectura Naval: Al entrar, fíjate en el techo; sus vigas de madera a la vista imitan la quilla invertida de un barco, un homenaje a la identidad portuaria de Valparaíso.\r\n\r\nExperiencia Recomendada: \"Música en las Alturas\"\r\n\r\nSi puedes planificar tu visita, el mejor momento es el domingo al mediodía (12:30 aprox.). Casi todos los domingos se realizan conciertos de órgano gratuitos o por donación voluntaria. Escuchar la acústica del templo mientras la luz se filtra por los vitrales es una de las experiencias más tranquilas y hermosas que puedes tener en los cerros.</p>', '[]', NULL, 'https://www.instagram.com/catedralstpaulsvalparaiso/', NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 17:33:21', '2026-05-08 17:00:50', 15),
(72, 1, 'Puerta de colores', 'puerta-de-colores-523', 'Cerro Concepción', 'puerta, colores', -33.04144300, -71.62773500, 'Valparaíso', '<p>Valparaíso tiene mucho arte callejero. En pasaje Gálvez se encuentra la puerta de colores.</p>', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 17:39:25', '2026-06-09 14:34:48', 3),
(73, 1, 'Paseo Atkinson', 'paseo-atkinson-924', 'Cerro Concepción', NULL, -33.04230600, -71.62547600, 'Valparaíso', '<p>El Paseo Atkinson es, para muchos, la postal más elegante de Valparaíso. Ubicado en el Cerro Concepción, este mirador se distingue por su hilera de casonas victorianas de colores pasteles, construidas a fines del siglo XIX por inmigrantes británicos.\r\n\r\nA diferencia de otros rincones más caóticos del puerto, aquí reina una atmósfera de tranquilidad y nostalgia. Sus jardines cuidados y su vista despejada hacia el plan de la ciudad, el puerto y el horizonte marino lo convierten en el lugar predilecto para fotógrafos y parejas al atardecer. Es el punto ideal para detenerse, respirar el aire costero y admirar cómo la arquitectura europea se funde con la geografía rebelde de los cerros porteños.</p>', '[\"Valpara\\u00edso\",\"Cerro Concepci\\u00f3n\",\"Paseo Atkinson\",\"Mirador\",\"Patrimonio de la Humanidad\",\"Arquitectura Victoriana\",\"Turismo Chile\",\"Vistas al mar\",\"Fotograf\\u00eda urbana\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 17:54:39', '2026-04-20 05:21:10', 1),
(74, 1, 'Pasaje Galvez', 'pasaje-galvez-539', 'Cerro Concepción', NULL, -33.04111400, -71.62714500, 'Valparaíso', '<p>El Pasaje Gálvez es el rincón más vibrante y creativo del Cerro Concepción. Este estrecho callejón peatonal es famoso por ser una verdadera galería de arte al aire libre, donde sus fachadas y muros están cubiertos por impresionantes murales de artistas locales que cambian constantemente.</p><p>\r\nA diferencia de los paseos más abiertos, el Gálvez ofrece una experiencia íntima y laberíntica. Caminar por aquí es descubrir pequeñas galerías de arte independiente, tiendas de diseño y cafeterías con encanto escondidas entre las coloridas casonas. Su conexión con la icónica Escalera Fischer y la famosa \"Puerta Roja\" lo convierten en un recorrido obligado para quienes buscan capturar la esencia bohemia y el pulso artístico que define a Valparaíso.</p>', '[\"Valpara\\u00edso\",\"Cerro Concepci\\u00f3n\",\"Pasaje G\\u00e1lvez\",\"Arte Urbano\",\"Murales\",\"Patrimonio\",\"Bohemio\",\"Escalera Fischer\",\"Fotograf\\u00eda\",\"Galer\\u00eda al aire libre\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 18:00:40', '2026-05-02 20:43:10', 3),
(75, 1, 'Escalera de los sueños', 'escalera-de-los-suenos-513', 'Cerro Santo Domingo', 'Santo Domingo', -33.03603700, -71.63323800, 'Valparaíso', '<p>La Escalera de los Sueños, ubicada en el fundacional Cerro Santo Domingo, es uno de los tesoros mejor guardados y más auténticos de Valparaíso. Este rincón destaca por su intervención artística cargada de simbolismo, donde cada peldaño pintado representa los anhelos y la resiliencia de la comunidad local.\r\n\r\nAl ser el cerro más antiguo de la ciudad, la escalera ofrece una experiencia menos turística y más genuina, rodeada de la arquitectura típica de los orígenes del puerto. Es un ascenso que combina el arte callejero con historias populares, culminando en puntos con vistas panorámicas privilegiadas hacia la bahía, ideales para quienes buscan capturar la esencia poética y el espíritu soñador que caracteriza a los habitantes de estos cerros.</p>', '[\"Valpara\\u00edso\",\"Cerro Santo Domingo\",\"Escalera de los Sue\\u00f1os\",\"Arte Comunitario\",\"Patrimonio Hist\\u00f3rico\",\"Muralismo\",\"Barrio Fundacional\",\"Vistas Panor\\u00e1micas\",\"Autenticidad.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-12 20:53:57', '2026-05-22 03:17:37', 3),
(76, 1, 'Plaza Anibal Pinto', 'plaza-anibal-pinto-565', 'Almendral', NULL, -33.04329100, -71.62447000, 'Valparaíso', '<p>La Plaza Aníbal Pinto, situada en el pie del Cerro Alegre y el Cerro Concepción, actúa como el corazón neurálgico que conecta el plan de la ciudad con la zona alta. Declarada Zona Típica, destaca por su icónica fuente de Neptuno, traída desde Francia a finales del siglo XIX.\r\n\r\nEs un punto de encuentro dinámico donde la bohemia porteña y la vida cotidiana se cruzan. Rodeada de edificios de gran valor arquitectónico y cafés históricos, la plaza es la puerta de entrada natural hacia los paseos más famosos del puerto. Es el lugar perfecto para observar el pulso real de Valparaíso, entre el tránsito de los trolebuses, los músicos callejeros y la actividad constante de sus quioscos y locales tradicionales.</p>', '[\"Valpara\\u00edso\",\"Plaza An\\u00edbal Pinto\",\"Fuente de Neptuno\",\"Monumento Nacional\",\"Zona T\\u00edpica\",\"Arquitectura Europea\",\"Centro Hist\\u00f3rico\",\"Punto de Encuentro\",\"Patrimonio Cultural.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-14 00:06:41', '2026-05-08 16:58:42', 14),
(77, 1, 'Mirador Mariposas', 'mirador-mariposas-249', 'Cerro Mariposas', NULL, -33.05384820, -71.61956300, 'Valparaíso', 'El Mirador Mariposas es uno de los puntos panorámicos más destacados de la Avenida Alemania, la famosa vía de cintura que recorre las cotas altas de Valparaíso. Ubicado en el Cerro Mariposas, este mirador ofrece una perspectiva frontal y despejada hacia la bahía, el puerto y el plan de la ciudad.', '[\"Valpara\\u00edso\",\"Cerro Mariposas\",\"Avenida Alemania\",\"Mirador\",\"Bah\\u00eda de Valpara\\u00edso\",\"Patrimonio Moderno\",\"Vistas Panor\\u00e1micas\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-14 02:34:09', '2026-04-14 02:45:58', 1),
(78, 1, 'Casa colores', 'casa-colores-782', 'Cerro Alegre', NULL, -33.04329100, -71.63075700, 'Valparaíso', '<p>Las casas de colores son un clásico de Valparaíso y el cerro Alegre. Si quieres ver una casa con arquitectura patrimonial pero con colores que le dan vida, este punto es un imperdible.</p>', '[\"cerro alegre\",\"casa colores\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-14 02:41:20', '2026-05-08 16:51:30', 9),
(79, 1, 'Mural pajaros', 'mural-pajaros-894', 'Cerro Mariposas', 'Avenida Alemania.', -33.05310470, -71.61939890, 'Valparaíso', '<p>Mural creado en la Avenida Alemania, muy cerca del Mirador Mariposas.</p>', '[\"muralismo\",\"street art.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-14 02:50:49', '2026-05-08 16:50:59', 3),
(80, 3, 'Hostal 1866', 'hostal-1866-624', 'Cerro Artillería', 'Artillería 1866', -33.03244900, -71.63291900, 'Valparaíso', '<p>Hostal 1866 está ubicado en el corazón del Cerro Alegre de Valparaíso, en una casona patrimonial restaurada del año 1866 con vista panorámica a la bahía y a los coloridos techos del puerto. Nos encontramos a solo tres cuadras del Ascensor Reina Victoria, rodeados de murales, cafés de autor y los miradores más fotografiados de la ciudad.</p><p><br></p><p>Somos el punto de partida ideal para recorrer los cerros a pie. Nuestra esencia combina patrimonio, descanso y buena onda: muros de adobe a la vista, pisos de madera que crujen con historia y espacios comunes diseñados para compartir datos de rutas, cocinar o trabajar con vista al Pacífico.</p><p>Contamos con wifi de alta velocidad en todo el hostal, desayuno casero incluido en todas las tarifas y staff local disponible 24/7 para ayudarte a vivir Valparaíso como un porteño más.</p><p><br></p>', '[\"asd\",\"qwd\",\"22\"]', NULL, 'http://www.hostal1866.cl', 'Lunes a domingo', NULL, 1, 1, '[\"oferta_del_dia\",\"habitaciones\",\"servicios\",\"politicas\"]', '<p>Hasta el 18 de abril tenemos un 20% de descuento en las piezas fragatas.</p>', 1, '2026-04-19 03:32:38', 'srty rt srt ytr', 'perfiles/qaKsipSqJIhZhKvN7Wf8ykzynvPhz6z4jgPax2ko.jpg', 0, '2026-04-14 03:16:44', '2026-05-24 01:44:59', 11),
(81, 4, 'Centro Cultura El Rey Lagarto', 'centro-cultura-el-rey-lagarto-554', 'Cerro Cárcel', 'Condell 1723', -33.04396100, -71.63450700, 'Valparaíso', '<p>En pleno corazón de los cerros de Valparaíso, El Rey Lagarto es un refugio para el arte vivo. Un espacio underground, pero cuidado hasta el último detalle. Qué vas a encontrar:</p><p><strong>Casa con historia</strong>:Fachada con mural del lagarto coronado y letrero de fierro forjado.</p><p><strong>Por dentro:</strong> muros de ladrillo restaurado, vigas a la vista y luz cálida.</p><p><strong>Programación diaria:</strong> Tocatas acústicas, teatro íntimo, expos de foto y grabado, talleres abiertos, jam de poesía. Todo en salas pequeñas donde estás a metros del artista.</p><p><strong>Sello porteño:</strong> Arte sin filtro, autogestionado y colaborativo. Acá tocan bandas locales antes de girar, exponen artistas emergentes y se arma comunidad.</p><p><strong>Por qué venir</strong></p><p>Porque es Valpo sin postal. Si quieres entender la escena cultural real del puerto, tienes que pasar una noche acá. Terminas conversando con la banda, el artista o con gente del cerro. Íntimo, seguro y con cerveza local en mano.</p>', '[]', NULL, 'https://www.instagram.com/ccreylagarto', '10 am a 22:00 pm', NULL, 1, 1, '[\"oferta_del_dia\",\"avisos\",\"agenda\"]', '<p>25% de descuento en las entradas durante todo abril</p>', 1, NULL, NULL, 'perfiles/A2g2bVHV4PfKru0bQIVshegBa8i7VfBSvocKf9s6.jpg', 0, '2026-04-14 06:33:30', '2026-06-08 02:02:07', 5),
(82, 1, 'Iglesia de los Sagrados Corazones', 'iglesia-de-los-sagrados-corazones-765', 'Almendral', 'Avenida Independencia 2086', -33.04821000, -71.61547400, 'Valparaíso', '<p>La Iglesia de los Sagrados Corazones, ubicada en el plan de Valparaíso, es un hito religioso y arquitectónico de gran relevancia histórica. Terminada en 1874, destaca por su imponente fachada neogótica y por albergar la imagen de la Virgen de los Sagrados Corazones, traída desde Francia.\r\n\r\nEs un espacio de recogimiento que sorprende por su cuidada ornamentación interior y sus vitrales, siendo uno de los templos más emblemáticos de la ciudad. Su ubicación en la calle Independencia la sitúa en un punto neurálgico, rodeada de la vida comercial y académica del puerto, ofreciendo un contraste de serenidad en medio del bullicio urbano.</p>', '[\"Sagrados Corazones\",\"Iglesia Hist\\u00f3rica\",\"Neog\\u00f3tico\",\"Patrimonio Religioso\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-15 02:18:36', '2026-05-08 17:00:35', 15),
(83, 1, 'Casa de las tapas', 'casa-de-las-tapas-311', 'Cerro Florida', 'Ferrari 405', -33.05100300, -71.62182000, 'Valparaíso', '<p>La casa de las tapas es uno de esos atractivos característicos de Valparaíso. Sin razón conocida, alguien decidió adornar su casa con tapas de autos y no hay màs explicación.</p>', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-16 05:43:32', '2026-06-09 14:33:11', 3),
(84, 1, 'Diosa Themis', 'diosa-themis-273', 'Barrio Puerto', NULL, -33.03943700, -71.62959900, 'Valparaíso', '<p>La estatua de la diosa <strong>Temis</strong> (o Themis) en Valparaíso es una de las esculturas más curiosas y comentadas de la ciudad, rodeada de mitos debido a sus particulares características. Se encuentra en la Plaza de la Justicia, justo frente al Palacio de los Tribunales (Corte de Apelaciones), a los pies del Cerro Alegre.</p><p>\r\n<strong>La Justicia que \"todo lo ve\"</strong>: El enigma de Temis\r\n</p><p>A diferencia de casi todas las representaciones de la justicia en el mundo, la estatua de Valparaíso posee tres rasgos que la hacen única y objeto de leyendas locales:\r\n</p><p><br></p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Sin venda en los ojos:</strong> Mientras que la tradición dicta que la justicia es ciega para ser imparcial, la Temis porteña tiene los ojos descubiertos. Los locales suelen decir con humor que en Valparaíso \"la justicia todo lo ve\" o que \"está atenta a quien le conviene mirar\".\r\n</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>La balanza desigual: </strong>Si te fijas bien, la balanza no está en equilibrio ni sostenida en alto. Reposa a un costado, bajo su brazo, casi como si estuviera \"enredada\" o fuera de uso, lo que ha alimentado críticas y bromas sobre la equidad de la ley.</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>La espada en reposo: </strong>En lugar de sostenerla con firmeza, la espada se apoya sobre su hombro o costado, dándole una postura que algunos describen como de cansancio o desafío.\r\n</li></ol><p><br></p><p><strong>Historia y Origen</strong>\r\n</p><p>Esta imponente figura de hierro fundido mide 3,20 metros de altura y pesa 1.500 kilos. Fue diseñada por el escultor francés Albert-Ernest Carrier-Belleuse (quien fue maestro de Rodin) y fundida en la famosa fundición Val d\'Osne en París. Fue inaugurada el 20 de agosto de 1876, por encargo del entonces intendente Francisco Echaurren.\r\n</p><p><br></p><p><strong>Consejo para el visitante\r\n</strong></p><p>La plaza donde se ubica es un excelente punto de partida para subir al Cerro Alegre a través del Ascensor El Peral, que está a solo unos pasos. Es el lugar perfecto para tomar una fotografía que contraste la arquitectura neoclásica del Palacio de Justicia con la mítica y \"despierta\" diosa Temis.</p>', '[\"estatua\",\"historia\",\"patrimonio.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-18 04:06:05', '2026-05-08 16:50:02', 12),
(85, 1, 'Animita Emilio Dubois', 'animita-emilio-dubois-550', 'Playa Ancha', NULL, -33.02754900, -71.64782700, 'Valparaíso', '<p>La <strong>Animita de Emile Dubois</strong>, ubicada en el Cementerio de Playa Ancha, es quizás el lugar de devoción popular más enigmático de Valparaíso. En lugar de un santo tradicional, aquí se rinde culto a un asesino en serie francés ejecutado en 1907, a quien el pueblo terminó convirtiendo en un \"santo de los desamparados\".</p><p><br></p><p><strong>El Criminal que se volvió Milagroso</strong></p><p>Emilio Dubois fue fusilado por el asesinato de varios empresarios extranjeros en el puerto. Sin embargo, su elocuencia durante el juicio y el hecho de que sus víctimas fueran vistos como \"usureros\" por la clase trabajadora de la época, generaron una extraña empatía. Tras su muerte, la creencia popular dictó que Dubois robaba para ayudar a los pobres, transformando su tumba en un centro de peregrinación.</p><h3><br></h3><h3><strong>Lo que verás en el sitio</strong></h3><p>El espacio es un despliegue visual de la fe porteña. La animita está cubierta por cientos de <strong>placas de agradecimiento</strong> (exvotos) que datan desde principios del siglo XX hasta hoy. Es común ver velas encendidas, flores frescas y peticiones escritas de personas que buscan justicia, protección o favores difíciles, especialmente por parte de aquellos que se sienten marginados por el sistema.</p><h3><br></h3><h3><strong>Un hito de la cultura popular</strong></h3><p>Visitar esta animita permite entender una faceta profunda de Valparaíso: la capacidad de su gente para crear sus propios protectores al margen de la iglesia oficial. Es un sitio donde el mito, la historia policial y la espiritualidad se funden en el silencio del cementerio más grande de la ciudad.</p>', '[\"patrimonio inmaterial\",\"emilio dubois\",\"animita\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-20 03:37:30', '2026-05-08 16:49:32', 13),
(86, 1, 'Casa de las muñecas', 'casa-de-las-munecas-251', 'Barrio Puerto', NULL, -33.03672800, -71.63245800, 'Valparaíso', '<h3><strong>La Casa de las Muñecas: Un pacto de amor eterno</strong></h3><p>Ubicada en el histórico sector de la calle Cajilla, esta gran residencia destaca por una fachada que impacta de inmediato: cientos de muñecas de diversos tamaños y épocas asoman por sus ventanas, balcones y cornisas. Aunque para el observador desprevenido la imagen puede resultar perturbadora o propia de una película de suspenso, la realidad es un profundo homenaje de un padre a su hija.</p><p><br></p><h3><strong>El origen de la promesa</strong></h3><p>La historia pertenece a <strong>Luis Arredondo</strong>, quien tras la temprana muerte de su hija <strong>Josefina</strong>, decidió cumplir el último deseo que ella le expresó en vida. Sin saber que partiría primero, la niña le pidió a su padre que, cuando ella ya no estuviera, dejara sus muñecas en la ventana. Desde entonces, el señor Arredondo ha mantenido vivas las casi 400 muñecas en el exterior de la casa, como un símbolo de espera y un recordatorio constante de la presencia de Josefina.</p><p><br></p><h3><strong>Un santuario en el Barrio Puerto</strong></h3><p>Más allá de las muñecas que decoran la fachada, se dice que la habitación de la niña permanece intacta, cerrada desde el día de su fallecimiento. Esta casa no es solo una curiosidad visual, sino un <strong>monumento al duelo y al amor filial</strong> que se ha integrado al paisaje del barrio, recordándonos que tras cada fachada de Valparaíso suele esconderse una historia humana profunda.</p>', '[\"Valpara\\u00edso\",\"Barrio Puerto\",\"Calle Cajilla\",\"Casa de las Mu\\u00f1ecas\",\"Historias Locales\",\"Patrimonio Humano\",\"Amor Filial\",\"Mitos Urbanos\",\"Lugares con Historia.\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-20 05:46:27', '2026-05-22 03:13:19', 13),
(87, 5, 'Café Mirador Bellavista', 'cafe-mirador-bellavista-345', 'Cerro Bellavista', NULL, -33.04775400, -71.62212600, 'Valparaíso', '<p><strong>¿Por qué partir tu día en Bellavista con nosotros?</strong></p><p>Porque somos la pausa con vista que Valparaíso se merecía. Abrimos Café Mirador hace 2 años cansados de que el turista solo encontrara café de cadena o lugares sin alma. Queríamos un espacio chico, honesto y con la mejor postal de la bahía.</p><p><br></p><p><strong>Lo que te ofrecemos:</strong></p><p><strong>- Precios en la mesa: Sin sorpresas. Lo que ves en PinDoor.cl es lo que pagas.</strong></p><p>- Café de especialidad de verdad: Tostamos con amigos de Playa Ancha. Trazabilidad completa. Si te gusta, te contamos la historia del grano.</p><p>- Vista 180° a la bahía: Desde cada mesa ves el puerto, los cerros y los ascensores. En día despejado se ve hasta Concón.</p><p>- Carta corta y honesta: Todo hecho acá o por productores de la V Región. Alfajores de Quilpué, kuchen de murta, pan de masa madre hecho anoche.</p><p>- Seas quien seas, bienvenido: Wifi gratis, enchufes, baño limpio, agua para tu perrito, menú en inglés y español. Personal que te explica sin apuro.</p><p><br></p><p>No somos: Un lugar para correr. Si andas apurado, mejor al paso. Acá vienes a mirar, conversar y entender por qué Valpo enamora.</p><p><br></p><p><strong>¿Qué hay a 5 minutos caminando desde nuestra puerta? </strong></p><p>Usamos PinDoor para que armes tu ruta, pero te adelantamos:</p><p>1. Museo a Cielo Abierto: 20 murales en 3 cuadras. Empieza en calle Ferrari.</p><p>2. La Sebastiana: La casa de Neruda con vista al mar. Compra la entrada online y evita la fila.</p><p>3. Ascensor Espíritu Santo: El más antiguo de Bellavista. $100 el viaje y te deja en calle Aldunate.</p><p>4. Paseo Yugoslavo + Palacio Baburizza: Mirador y café en palacio histórico.</p><p>5. Calle Templeman: Galerías de arte, tiendas de diseño local y más murales.</p><p><br></p><p><strong>Dato Mirador:</strong> Si vienes entre 16:00 y 18:00, te toca el golden hour. El sol cae sobre la bahía y la terraza se pone mágica. Ahí sacas la foto que vas a usar de perfil.</p><p><br></p><p>Te esperamos en Pasaje Guimera 21, Cerro Bellavista.</p>', '[\"cafeteria\",\"mirador\"]', 'https://www.youtube.com/watch?v=qtOwfpyEPDM', 'http://www.miradorbellavista.cl', '9:00 a 22:00 hrs', NULL, 1, 1, '[\"oferta_del_dia\",\"avisos\",\"promociones\",\"agenda\",\"menu_del_dia\",\"carta\"]', '<p><strong>Martes a Jueves</strong>: Combo Porteño</p><p><br></p><p>Flat White + Alfajor Chileno = $4.200</p><p>Precio normal $5.000 | Ahorras $800</p><p>Válido hasta las 17:00 hrs. Muestra este cupón desde http://www.PinDoor.cl</p>', 1, NULL, NULL, 'perfiles/Gwf9Bg3bZ3QPi4SEtdbccgD7FOXwcrg3cHnFWvfA.jpg', 0, '2026-04-21 18:47:03', '2026-05-24 01:44:48', 2),
(88, 1, 'Mural Faro', 'mural-804', 'Cerro Lecheros', 'Gutemberg', -33.04382800, -71.60359200, 'Valparaíso', '<p>Mural creado por ABUSA, una dupla feminina chilena de Graffiti-Mural.</p>', '[\"Mural\",\"street art.\"]', NULL, NULL, NULL, 'Abusa Crew', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-24 05:50:20', '2026-05-08 16:48:58', 3),
(89, 1, 'Motemei', 'motemei-480', 'Cerro Lecheros', NULL, -33.04391000, -71.60260900, 'Valparaíso', '<p>El mural <strong>Motemei</strong> es una de las obras más emblemáticas y queridas del <strong>Cerro Lecheros</strong>. Pintado por el destacado artista urbano <strong>Giova</strong>, esta pieza es un homenaje a la identidad popular y a uno de los oficios más tradicionales que aún recorren las calles de Valparaíso: el vendedor de mote de maíz.</p><p><br></p><h3><strong>Un Gigante en el Cerro</strong></h3><p>El mural retrata de cuerpo completo al mítico \"Motemei\" (don Carlos), un personaje real que durante décadas ha caminado por los cerros con su canasto y su grito característico. La obra captura la esencia del esfuerzo y la perseverancia porteña, utilizando colores vibrantes que contrastan con la arquitectura del sector.</p><p><br></p><h3><strong>Identidad y Resistencia</strong></h3><p>Ubicado estratégicamente en una de las laderas del Cerro Lecheros, el mural se ha convertido en un hito visual que se puede apreciar incluso desde otros cerros cercanos o desde el plan de la ciudad. Representa la resistencia de las tradiciones frente a la modernidad y el valor que los artistas locales otorgan a los personajes que dan alma a la ciudad.</p><p><br></p><h3><strong>Por qué visitarlo</strong></h3><p>Es una parada fundamental para quienes buscan el Valparaíso real, fuera de los circuitos comerciales. El Cerro Lecheros ofrece una experiencia de barrio auténtica y el mural sirve como un recordatorio de que, en este puerto, la historia no solo se escribe en libros, sino también en las paredes y en el andar diario de sus habitantes.</p>', '[\"Valpara\\u00edso\",\"Cerro Lecheros\",\"Mural Motemei\",\"Giova\",\"Arte Urbano\",\"Cultura Popular\",\"Tradiciones Porte\\u00f1as\",\"Identidad\",\"Patrimonio Vivo\"]', NULL, 'https://www.instagram.com/giova.kini/', NULL, 'Giova', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-24 05:56:39', '2026-04-24 17:12:11', 3),
(90, 1, 'El Viajero', 'el-viajero-269', 'Cerro Lecheros', NULL, -33.04347200, -71.60317200, 'Valparaíso', '<p>El mural del <strong>Viajero en el Tren</strong>, realizado por el artista <strong>Maidak</strong>, es una de las obras más melancólicas y técnicamente impresionantes del <strong>Cerro Lecheros</strong>. Esta pieza captura una de las estampas más clásicas de la vida itinerante: un pasajero sentado junto a la ventana de un vagón, sumergido en sus pensamientos mientras el paisaje avanza.</p><p><br></p><p><br></p><h3><strong>Nostalgia sobre rieles</strong></h3><p>Maidak, conocido por su estilo detallista y el uso magistral de las sombras, logra en este mural una atmósfera de introspección. La figura del viajero, con su mirada perdida y su postura relajada, conecta directamente con la historia ferroviaria de Valparaíso y la esencia de quienes llegan o parten de este puerto. La obra aprovecha la verticalidad de los muros del cerro para crear una sensación de profundidad que hace que el tren parezca detenido en el tiempo.</p><p><br></p><h3><strong>El arte como ventana</strong></h3><p>Lo que hace especial a este mural es cómo integra el entorno urbano. Al estar en el Cerro Lecheros, un sector que mira directamente hacia la zona portuaria y las vías del tren que conectan la ciudad, el mural establece un diálogo visual con el movimiento real del plan de Valparaíso. Es una pieza que invita a detenerse y reflexionar sobre el viaje propio, convirtiendo una pared de barrio en una escena cinematográfica de principios del siglo XX.</p><p><br></p><h3><strong>Por qué buscarlo</strong></h3><p>Es una parada obligada para los amantes del <em>street art</em> con contenido figurativo y emocional. Junto al mural del Motemei, esta obra de Maidak posiciona al Cerro Lecheros como un destino emergente para quienes buscan muralismo de alta calidad técnica y relatos visuales que rinden tributo a la memoria colectiva y al tránsito constante de la vida porteña.</p>', '[\"Cerro Lecheros\",\"Maidak\",\"Muralismo\",\"Arte Urbano\",\"Viajero en el Tren\",\"Patrimonio Visual\",\"Nostalgia\",\"Street Art Chil\"]', NULL, 'https://www.instagram.com/maidak_/', NULL, 'Maidak', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-24 06:04:54', '2026-04-24 17:12:10', 3),
(91, 1, 'Fe de un pescador', 'fe-de-un-pescador-295', 'Cerro Lecheros', NULL, -33.04349900, -71.60255700, 'Valparaíso', '<p>El mural <strong>Fe de un Pescador</strong>, creado por el destacado artista <strong>GVZ</strong>, es una de las obras más conmovedoras y potentes del <strong>Cerro Lecheros</strong>. Esta pieza rinde un homenaje monumental a la figura del pescador artesanal, un pilar fundamental de la identidad y la economía histórica de Valparaíso.</p><p><br></p><h3><strong>La espiritualidad del mar</strong></h3><p>La obra retrata el rostro de un pescador curtido por el sol y el salitre, cuyas manos sostienen con devoción una pequeña embarcación o símbolos de su oficio. El mural captura ese momento de introspección y esperanza previo a lanzarse al océano, reflejando la conexión espiritual y el respeto que los hombres de mar profesan hacia el Pacífico. El uso de colores tierra y azules profundos refuerza la sensación de realismo y humildad que caracteriza al trabajador portuario.</p><p><br></p><h3><strong>Un hito en la ladera</strong></h3><p>Ubicado en una de las fachadas que miran hacia la bahía, el mural establece un vínculo visual directo con el lugar de trabajo del protagonista. La escala de la obra permite que sea apreciada desde distintos puntos del cerro, convirtiéndose en un faro artístico que recuerda a los habitantes y visitantes la vigencia de los oficios tradicionales en medio de la modernidad.</p><p><br></p><h3><strong>Por qué es imperdible</strong></h3><p>Es una parada esencial para entender el muralismo con contenido social en Valparaíso. Acompañado de otras grandes obras en el Cerro Lecheros, como el \"Motemei\" y el \"Viajero en el Tren\", este mural de GVZ consolida al sector como un museo a cielo abierto que no solo busca embellecer, sino también dignificar la memoria y las creencias del pueblo porteño.</p>', '[\"GVZ\",\"Giova\",\"Fe de un Pescador\",\"Arte Urbano\",\"Cultura Mar\\u00edtima\",\"Muralismo Social\",\"Identidad Porte\\u00f1a.\"]', NULL, NULL, NULL, 'GVZ.', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-25 00:37:53', '2026-06-09 14:33:03', 3),
(92, 1, 'Guardián del Territorio', 'guardian-del-territorio-557', 'Almendral', NULL, -33.04369800, -71.60315200, 'Valparaíso', '<p>Esta impresionante obra de <strong>Saile</strong> es una síntesis visual de la complejidad de Valparaíso. El artista, famoso por su capacidad para fusionar elementos figurativos con una estética de \"graffiti técnico\" y orgánico, logra aquí una composición que trasciende el muro.</p><p><br></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>El Personaje Central:</strong> La figura central, con su máscara o rostro estilizado, actúa como un \"guardián\" del cerro. Su estética refleja el sello distintivo de Saile: líneas precisas, un uso sofisticado de la luz y una expresión que parece estar en comunión con el paisaje que protege.</li></ol><p><br></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>La Integración:</strong> Lo fascinante de este mural es cómo el artista \"construye\" la ciudad dentro de la misma figura. Las casas, los botes y las estructuras de los cerros parecen nacer de la propia entidad, simbolizando que el territorio es parte del alma de sus habitantes.</li></ol><p><br></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Técnica:</strong> Saile utiliza una paleta de colores vibrantes en contraste con tonos más fríos, lo que le da a la obra una profundidad casi tridimensional. La fluidez con la que integra los elementos naturales (nubes, montañas) con los elementos urbanos (camiones, viviendas) es una característica de su maestría.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><br></li></ol><h3><br></h3><p>La intervención de Saile en el <strong>Cerro Lecheros</strong> eleva el estándar del muralismo en la zona. No solo es una pieza decorativa; es un ejercicio de <strong>identidad territorial</strong>. Mientras que otros murales en el mismo cerro pueden ser más narrativos (como los de Giova), la obra de Saile invita a una contemplación más introspectiva sobre la relación entre el habitante porteño y la desafiante geografía de su ciudad.</p>', '[\"Saile\",\"Arte Urbano\",\"Muralismo\",\"Identidad Porte\\u00f1a\",\"Est\\u00e9tica Urbana\",\"Street Art Chile\",\"Patrimonio Visual.\"]', NULL, NULL, NULL, 'Salie', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-25 00:45:52', '2026-04-25 00:45:52', 3),
(93, 1, 'Latinoamérica', 'latinoamerica-151', 'Cerro Lecheros', 'Gutemberg 60', -33.04395100, -71.60319000, 'Valparaíso', '<p><br></p><p>Este impactante mural de gran formato, ubicado en una de las fachadas laterales de un edificio de viviendas en Valparaíso, es una obra que destaca por su potente carga simbólica y política.</p><p><br></p><h3><strong>\"Latinoamérica\": Dolor y Resistencia</strong></h3><p>Este mural actúa como un grito visual sobre la historia y el presente del continente. La obra está dominada por una figura humana que, envuelta en telas amarillas y anaranjadas que evocan tanto un ropaje sacro como una mortaja, eleva su rostro hacia el cielo en una expresión que mezcla dolor, éxtasis y resistencia. El uso del color amarillo crea una atmósfera intensa, casi febril, que dota a la escena de una urgencia palpable.</p><p><br></p><h3><strong>Simbolismo de la Espiritualidad y la Opresión</strong></h3><p>El elemento central y más perturbador es el halo que rodea la cabeza de la figura. En lugar de ser un círculo de luz divina tradicional, este resplandor amarillo está atravesado por <strong>alambre de púas</strong>. Esta impactante metáfora visual subvierte la iconografía religiosa para representar la \"santidad\" del sufrimiento latinoamericano, la fe que resiste la opresión, y la herida constante de las fronteras, las dictaduras y la desigualdad que han marcado al territorio. El alambre de púas transforma el halo en una corona de espinas moderna, convirtiendo la figura en una suerte de mártir colectivo.</p><p><br></p><h3><strong>Una Declaración Territorial</strong></h3><p>En la parte inferior de la obra, claramente visible, se lee la palabra <strong>\"LATINOAMÉRICA\"</strong>. Esta inscripción no deja lugar a dudas sobre la intención del artista: la figura no es un individuo específico, sino la personificación de un continente entero que, a pesar de las cadenas y el sufrimiento, mantiene la mirada en alto. La ubicación del mural en un edificio de viviendas integra esta reflexión en la vida cotidiana de los porteños, recordándoles su identidad compartida y su historia de lucha.</p>', '[\"Arte Urbano\",\"Juampa\",\"Latinoam\\u00e9rica\",\"Muralismo Pol\\u00edtico\",\"Simbolismo\",\"Resistencia\",\"Alambre de P\\u00faas\",\"Identidad Continental\",\"Patrimonio Visual.\"]', NULL, NULL, NULL, 'JP', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-25 00:55:33', '2026-05-08 16:45:04', 3);
INSERT INTO `puntosinteres` (`id`, `user_id`, `title`, `slug`, `sector`, `direccion`, `lat`, `lng`, `ciudad`, `description`, `tags`, `video_url`, `enlace`, `horario`, `autor`, `activo`, `es_cliente`, `modulos_habilitados`, `oferta_del_dia`, `oferta_activa`, `oferta_expira_at`, `descripcion_busqueda`, `imagen_perfil`, `eliminado`, `created_at`, `updated_at`, `categoria_id`) VALUES
(94, 1, 'El beso', 'el-beso-340', 'Cerro Lecheros', 'Gutemberg', -33.04381900, -71.60298600, 'Valparaíso', '<p>Este vibrante mural, ubicado en la ladera de un edificio del <strong>Cerro Lecheros</strong>, es una obra del artista <strong>Albus Valley</strong>. Su estilo se aleja del realismo social para adentrarse en una estética geométrica y abstracta muy cercana al cubismo moderno y al arte pop.</p><p><br></p><h3><strong>\"El Beso\": Geometría y Afecto en el Cerro</strong></h3><p>A diferencia de otros murales narrativos del sector, esta pieza de <strong>Albus Valley</strong> utiliza líneas negras gruesas y una paleta de colores saturados (violetas, fucsias y amarillos) para representar un momento de profunda intimidad: el beso entre dos figuras. La fragmentación de las formas y la superposición de planos crean un dinamismo que parece vibrar sobre el muro blanco, aportando una energía contemporánea y alegre al barrio.</p><p><br></p><h3><strong>El Juego de la Perspectiva</strong></h3><p>Lo más fascinante de esta obra es cómo el artista divide la composición en bloques, casi como si fuera un vitral moderno o una tira cómica abstracta. En la base de la estructura, se puede observar un diseño complementario en tonos grises, que actúa como un eco minimalista de la explosión de color superior. Esta dualidad entre el color intenso y el dibujo lineal inferior demuestra la versatilidad técnica del artista y su capacidad para adaptar el diseño a la arquitectura del edificio.</p><p><br></p><h3><strong>Un Contraste Moderno</strong></h3><p>Para el turista, este mural representa la faceta más vanguardista de Valparaíso. Mientras caminas entre casas antiguas y adoquines, encontrarte con la obra de Albus Valley es un recordatorio de que el puerto es un laboratorio constante de nuevas tendencias visuales. Es el punto perfecto para una fotografía que resalte por su diseño gráfico y su mensaje universal de afecto.</p>', '[\"Albus Valley\",\"Arte Geom\\u00e9trico\",\"Muralismo Contempor\\u00e1neo\",\"Abstracci\\u00f3n\",\"Amor Urbano\",\"Dise\\u00f1o Visual\",\"Street Art Chile.\"]', NULL, NULL, NULL, 'Allanttalley', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-04-25 01:02:19', '2026-04-25 01:02:19', 3),
(103, 1, 'Plaza Bismarck', 'plaza-biskmark-601', 'Cerro Cárcel', NULL, -33.04720000, -71.62970000, 'Valparaíso', '<p>La <strong>Plaza Bismarck</strong> es uno de los hitos más espectaculares. Ubicada en el <strong>Cerro Cárcel</strong>, a lo largo de la emblemática <strong>Avenida Alemania</strong>, este espacio público es un punto de encuentro fundamental tanto para vecinos como para viajeros que buscan una panorámica completa de la ciudad.</p><p><br></p><p><br></p><h3><strong>El Anfiteatro de la Bahía</strong></h3><p>Debido a su forma semicircular y su ubicación en una pronunciada curva de la Avenida Alemania, la Plaza Bismarck funciona como un anfiteatro natural. Desde sus barandas se obtiene una de las vistas más icónicas del puerto, permitiendo observar con detalle el plan de la ciudad, el sector de la Plaza Victoria y el movimiento constante de los barcos en la bahía.</p><p><br></p><h3><strong>Lo que la hace especial:</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Arquitectura y Paisajismo:</strong> La plaza cuenta con juegos infantiles, áreas verdes y un diseño en niveles que permite disfrutar de la vista desde distintos ángulos. Es un lugar ideal para el descanso tras una caminata por los cerros.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Hito Cultural:</strong> Su nombre rinde homenaje al estadista alemán Otto von Bismarck, reflejando la influencia de la colonia alemana en el desarrollo urbano del puerto. Además, se encuentra muy cerca del Parque Cultural de Valparaíso (Ex-Cárcel), lo que permite combinar la visita con una oferta cultural diversa.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Atardeceres Privilegiados:</strong> Es, sin duda, uno de los mejores lugares de la ciudad para ver caer el sol. La iluminación nocturna del anfiteatro natural de Valparaíso desde este punto es simplemente inolvidable.</li></ol><p><br></p><h3><strong>Consejo para el visitante</strong></h3><p>La plaza es una parada estratégica si estás realizando la ruta de los cerros hacia el Cerro Alegre o Concepción por la parte alta. Es el lugar perfecto para hacer una pausa, tomar fotografías de gran angular y disfrutar de la brisa marina que sube por la quebrada.</p>', '[\"plaza\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-08 17:25:47', '2026-06-12 20:06:26', 14),
(104, 1, 'Fuente de Neptuno', 'fuente-de-neptuno-551', 'Almendral', NULL, -33.04335800, -71.62443800, 'Valparaíso', '<p>La <strong>Fuente de Neptuno</strong>, situada en el corazón de la <strong>Plaza Aníbal Pinto</strong>, es uno de los monumentos más elegantes y antiguos que adornan el plan de Valparaíso. Esta obra, fabricada en hierro fundido, no solo es una pieza decorativa, sino un símbolo del vínculo histórico del puerto con la cultura europea y el mar.</p><p><br></p><h3><strong>El Dios del Mar en el Corazón del Puerto</strong></h3><p>Inaugurada en <strong>1856</strong>, la fuente es una pieza traída directamente desde Francia, fundida en los famosos talleres de <em>J.J. Ducel et Fils</em>. Representa a <strong>Neptuno</strong>, el dios romano de los mares, sosteniendo su tridente mientras emerge victorioso sobre un grupo de figuras mitológicas y delfines. Su diseño neoclásico aporta un aire de sofisticación europea a esta plaza, que actúa como frontera natural entre el plan comercial y la subida hacia los cerros Alegre y Concepción.</p><h3><br></h3><h3><strong>Un Punto de Encuentro Histórico</strong></h3><p>La Plaza Aníbal Pinto, donde reside la fuente, es Patrimonio de la Humanidad y un nodo vital de la bohemia porteña. Neptuno ha sido testigo silencioso de la evolución de la ciudad: desde los tiempos en que los carros de caballos circulaban por el sector, hasta hoy, siendo el punto de reunión preferido por turistas y locales antes de subir por el <strong>Ascensor Reina Victoria</strong> o caminar hacia la calle Condell.</p><h3><br></h3><h3><strong>Detalles para el Observador</strong></h3><p>Si te acercas a la fuente, podrás notar la fineza de los detalles en el hierro: el movimiento de las barbas de Neptuno y la fuerza de las criaturas marinas que lo acompañan. Es un lugar ideal para detenerse, escuchar el sonido del agua en medio del ajetreo urbano y tomar una fotografía que capture la esencia señorial del Valparaíso del siglo XIX.</p>', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-08 17:33:20', '2026-06-09 14:32:58', 12),
(105, 1, 'Perro  alegre', 'perro-alegre-939', 'Cerro Alegre', 'Almirante Montt', -33.04385100, -71.62869800, 'Valparaíso', '<p>\"Un sueño fué estar en Valparaiso, pintar y expresar mi experiencia en este país! Me flipa salir de la \"zona de confort\", experimentar con los materiales de cada zona, dejar huella en una de las calles más visitadas de Valparaiso 🤗 y sentir el calor y el super buen rollo de la gente! Gracias Mauri por la oportunidad, la confianza y el feeling!\"</p><p><br></p>', '[\"street art\"]', NULL, 'https://www.instagram.com/aral_laragombau/', NULL, 'LaRa Gombau', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-09 21:04:27', '2026-05-10 01:12:22', 3),
(106, 1, 'Ardilla y el mundo', 'ardilla-y-el-mundo-703', 'Almendral', 'Estanque', -33.04383700, -71.63289200, 'Valparaíso', '<p>Este mural es el resultado de un taller de mural realizado junto a los estudiantes de la casa @sit.chile.socialjustice</p><p><br></p>', '[\"street art\"]', NULL, 'https://www.instagram.com/cuellimangui/', NULL, 'Cuellimangi', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-11 01:15:01', '2026-05-11 01:15:01', 3),
(107, 1, 'Parroquia del Carmen Cerro Bellavista', 'parroquia-del-carmen-cerro-bellavista-512', 'Cerro Bellavista', 'Bernardo Ramos 424', -33.04937700, -71.62218600, 'Valparaíso', '<p>La <strong>Parroquia Nuestra Señora del Carmen</strong>, ubicada en el <strong>Cerro Bellavista</strong>, es un templo emblemático que se alza como un faro espiritual y arquitectónico en uno de los cerros más artísticos de Valparaíso.</p><p><br></p><h3><strong>El Vigía del Cerro Bellavista</strong></h3><p>Situada en una ubicación privilegiada que domina la vista hacia el plan de la ciudad, esta parroquia es un punto de referencia visual inconfundible. Su arquitectura se integra armoniosamente con la topografía del cerro, destacando por su sencillez y elegancia, lo que la convierte en un hito tanto para los residentes como para quienes visitan el famoso Museo a Cielo Abierto que se encuentra a pocos metros.</p><h3><br></h3><h3><strong>Historia y Comunidad</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Vínculo con el Barrio:</strong> Esta iglesia ha sido, por décadas, el corazón de la vida comunitaria del Cerro Bellavista, albergando las tradiciones y celebraciones de los vecinos.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Identidad Porteña:</strong> A diferencia de las grandes iglesias del plan, esta parroquia refleja la escala humana y la vida de barrio característica de los cerros de Valparaíso.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Sobriedad y Paz:</strong> Su interior invita al silencio y a la reflexión, ofreciendo un contraste tranquilo frente al ajetreo turístico y artístico de los murales circundantes.</li></ol><h3><br></h3><h3><strong>Por qué visitarla</strong></h3><p>Es el complemento perfecto para una caminata por el Cerro Bellavista. Después de recorrer los murales del Museo a Cielo Abierto, llegar a la parroquia permite entender la dualidad del cerro: un lugar de vanguardia artística que conserva sus raíces y su fe tradicional. Además, desde su entorno se obtienen ángulos fotográficos únicos de la arquitectura típica de Valparaíso y sus tejados.</p>', '[\"Valpara\\u00edso\",\"Cerro Bellavista\",\"Parroquia Nuestra Se\\u00f1ora del Carmen\",\"Patrimonio Religioso\",\"Vida de Barrio\",\"Turismo Cultural\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-14 23:19:58', '2026-05-24 16:59:51', 15),
(108, 1, 'Centro Cultural BancoEstado', 'centro-cultural-bancoestado-828', 'Cerro Alegre', 'Paseo Yugoslavo', -33.04021500, -71.62875100, 'Valparaíso', '<p>El <strong>Centro Cultural BancoEstado</strong>, ubicado en el corazón del plan de Valparaíso (calle Prat), es uno de los espacios dedicados a las artes y la extensión cultural más importantes de la zona bancaria y patrimonial.</p><p><br></p><h3><strong>Patrimonio en el Distrito Financiero</strong></h3><p>El centro funciona en un edificio de gran valor histórico que forma parte del conjunto arquitectónico del barrio puerto. Su diseño interior combina la elegancia de la arquitectura bancaria de principios del siglo XX con la funcionalidad de una galería moderna. Es un ejemplo destacado de cómo las instituciones financieras han recuperado espacios para el acceso público y el fomento de la cultura.</p><h3><br></h3><h3><strong>Fomento a las Artes Visuales</strong></h3><p>Este espacio se caracteriza por su cartelera dinámica que incluye:</p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Exposiciones Temporales</strong>: Muestra regularmente obras de artistas locales y nacionales, abarcando desde la pintura clásica hasta la fotografía contemporánea.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Diversidad de Medios</strong>: Ha albergado muestras de grabado, escultura y artes visuales que dialogan con la identidad de Valparaíso.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Acceso Gratuito</strong>: Es un punto de parada ideal para los peatones que recorren la calle Prat, ofreciendo una pausa cultural gratuita en medio del sector financiero.</li></ol><h3><br></h3><h3><strong>Por qué visitarlo</strong></h3><p>Es la parada perfecta si te encuentras recorriendo el eje que conecta la <strong>Plaza Sotomayor</strong> con la <strong>Plaza Aníbal Pinto</strong>. El centro permite apreciar la majestuosidad de los antiguos edificios bancarios por dentro, mientras se disfruta de una curatoría artística que suele destacar el patrimonio y la creatividad regional.</p>', '[\"Artes Visuales\",\"Patrimonio Arquitect\\u00f3nico\",\"Cultura Gratis\"]', NULL, NULL, 'Martes a viernes de 9:30 a 17:00 hrs', NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-14 23:28:37', '2026-06-09 14:35:33', 7),
(110, 1, 'BTS', 'bts-273', 'Cerro Bellavista', NULL, -33.04720000, -71.62970000, 'Valparaíso', '<p>El <strong>Mural de BTS</strong>, inaugurado a principios de 2025, se ha convertido en un auténtico hito de peregrinación y en un nuevo punto turístico que causa furor entre los fanáticos del K-Pop (<em>ARMY</em>) que visitan Valparaíso.</p><p><br></p><p><br></p><h3><strong>Un pedazo de Corea en los cerros</strong></h3><p>La obra fue creada en tiempo récord (cerca de 16 horas) por <strong>Javiera y Macarena</strong>, dos hermanas gemelas apasionadas por el arte y la música del septeto surcoreano. El mural plasma a los siete miembros de la banda y está directamente inspirado en una escena icónica de la cuarta temporada de su programa <em>Bon Voyage</em>, grabada en Nueva Zelanda.</p><p>La intervención fue posible gracias a la hospitalidad de una familia local del cerro que también es fanática del grupo y que cedió la gran fachada de su vivienda para dar vida a la obra. Además, su realización en Valparaíso tiene una carga simbólica especial, dado que el puerto es oficialmente <strong>ciudad hermana de Busan</strong>, la tierra natal de dos de sus integrantes, Jimin y Jungkook.</p><p><br></p><h3><strong>Ubicación y cómo llegar</strong></h3><p><br></p><p>El mural se encuentra en la <strong>calle Héctor Calvo</strong>, justo en la escalera que conecta el plano de la ciudad (a la altura de la Plaza Ecuador) con las partes altas que limitan entre el <strong>Cerro Bellavista</strong> y el <strong>Cerro Concepción</strong>. Al estar pintado sobre una fachada en declive, juega dinámicamente con la perspectiva de la gran escalera.</p><p><br></p><p>Para llegar a él, tienes varias rutas sencillas desde el plan o la parte alta:</p><p><br></p><ol><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Desde Plaza Ecuador (Por abajo):</strong> Sube directamente por la calle Yerbas Buenas y desvíate por la Escalera Héctor Calvo.</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Por el Ascensor Espíritu Santo:</strong> Sube por el ascensor, continúa derecho por el anfiteatro de la calle La Bruyère hasta topar con Héctor Calvo, y desciende un poco a mano derecha.</li><li data-list=\"ordered\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Desde Avenida Alemania (Por arriba):</strong> Puedes tomar la micro 612 (la famosa línea \"O\"), bajarte en la esquina de Avenida Alemania con calle Ferrari, y comenzar a descender por las escaleras de Héctor Calvo.</li></ol><p><br></p><p>Es la parada perfecta para sumar un toque de cultura pop contemporánea y fanatismo global a las tradicionales rutas de <em>street art</em> del puerto.</p>', '[\"Mural de BTS\",\"K-Pop\",\"ARMY Chile\",\"Arte Urbano\"]', NULL, 'https://www.instagram.com/p/DTocsSpjX6k/?img_index=1', NULL, 'autmnkookie wiinter_moonlight', 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-23 00:22:59', '2026-05-23 00:24:42', 3),
(111, 1, 'La Sebastiana', 'la-sebastiana-571', 'Cerro Bellavista', NULL, -33.05386100, -71.62255500, 'Valparaíso', '<p>La <strong>Casa Museo La Sebastiana</strong>, ubicada en la parte alta del <strong>Cerro Bellavista</strong> (calle Ricardo de Ferrari 692), es una de las residencias más famosas del poeta chileno <strong>Pablo Neruda</strong>. El Nobel de Literatura buscaba un lugar para vivir y escribir que estuviera lejos del bullicio pero cerca de la magia del puerto, encontrando en este rincón su refugio ideal.</p><p><br></p><h3><strong>La casa construida como un barco</strong></h3><p>Inaugurada por el poeta en septiembre de 1961, la casa destaca por su arquitectura única y caprichosa de múltiples niveles, diseñada originalmente por el constructor Sebastián Collado (de ahí el nombre \"La Sebastiana\"). Neruda la completó dándole una estructura que evoca los compartimentos de un gran barco encallado en el cerro. Cada piso se conecta a través de estrechas escaleras que conducen a habitaciones llenas de rincones secretos, techos inclinados y claraboyas.</p><p><br></p><h3><strong>Un gabinete de curiosidades frente al mar</strong></h3><p>El interior de La Sebastiana funciona como una bitácora de los viajes y obsesiones de Neruda. El museo conserva intacta su increíble colección de objetos: antiguos mapas celestes, cajas de música, vitrales de colores, mascarones de proa, barómetros y caballitos de carrusel traídos de distintas partes del mundo. El punto culmen del recorrido es su estudio en el piso superior, un espacio rodeado de ventanales desde donde el poeta contemplaba los fuegos artificiales de Año Nuevo y el movimiento eterno de la bahía.</p><p><br></p><h3><strong>Entorno y Cultura</strong></h3><p>Hoy administrada por la Fundación Neruda, la casa cuenta con un moderno centro cultural adjunto, una tienda de recuerdos y una cafetería. Los jardines escalonados de su exterior ofrecen un hermoso mirador público, lo que convierte a este hito en el broche de oro perfecto tras realizar la caminata que sube desde el Museo a Cielo Abierto y la Plaza Mena.</p>', '[\"Cerro Bellavista\",\"La Sebastiana\",\"Pablo Neruda\",\"Casa Museo\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-23 17:35:40', '2026-06-09 14:32:27', 5),
(112, 1, 'Resbalín', 'resbalin-374', 'Cerro Concepción', 'Pasaje Gálvez', -33.04160200, -71.62855300, 'Valparaíso', '<p>El famoso <strong>Resbalín del Pasaje Gálvez</strong>, ubicado en pleno <strong>Cerro Concepción</strong>, es uno de los rincones más lúdicos, fotografiados y queridos por quienes recorren los rincones patrimoniales de Valparaíso.</p><p><br></p><h3><strong>El Juego dentro del Urbanismo Porteño</strong></h3><p>Ubicado en el pintoresco e inclinado <strong>Pasaje Gálvez</strong>, este resbalín de hormigón aprovecha la pronunciada pendiente natural del cerro para integrarse de manera brillante con la arquitectura del sector. No se trata de un juego infantil convencional en un parque cerrado, sino de una estructura empotrada directamente en la acera pública, junto a las escaleras que conectan los sinuosos senderos del cerro. Es una muestra perfecta de cómo el urbanismo de Valparaíso desafía la gravedad y transforma la geografía en un espacio de diversión cotidiana.</p><h3><br></h3><h3><strong>Un Hito de Color y Convivencia</strong></h3><p>El resbalín suele estar rodeado de coloridos murales, mosaicos y las fachadas de casas de latón e inspiración decimonónica que caracterizan al Cerro Concepción. Este rincón personifica la esencia bohemia y comunitaria del puerto: es habitual ver tanto a niños del barrio como a turistas de todas las edades atreverse a deslizarse por él para vivir una experiencia inmersiva en la identidad de la ciudad.</p><h3><br></h3><h3><strong>Consejo para el Visitante</strong></h3><p>Es una parada obligatoria y muy divertida si estás haciendo la ruta a pie entre el Paseo Gervasoni y el Paseo Atkinson. Además de ser el punto ideal para capturar un video dinámico o una fotografía muy original, su entorno está lleno de pequeñas cafeterías, galerías de arte y tiendas de diseño local que invitan a perderse sin prisa por las callejuelas del cerro.</p><p><br></p>', '[\"Resbal\\u00edn\",\"tobog\\u00e1n\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-24 15:56:24', '2026-05-24 15:56:24', 3),
(113, 1, 'Mirador Arturo Moya Gray', 'mirador-arturo-moya-gray-602', 'Cerro Bellavista', 'Pasaje Florida', -33.05249300, -71.62154700, 'Valparaíso', '<p>El <strong>Mirador Arturo Moya Grau</strong>, ubicado en la intersección de la calle Florida con la subida Rudolph en la parte alta del <strong>Cerro Bellavista</strong>, es un rincón suspendido en el tiempo que rinde homenaje a una de las figuras más importantes de la dramaturgia y la televisión chilena.</p><p><br></p><h3><strong>El Balcón del Gran Dramaturgo</strong></h3><p>Este mirador lleva el nombre de <strong>Arturo Moya Grau</strong>, considerado el \"padre de las teleseries chilenas\" y creador de éxitos continentales como <em>La Madrastra</em>. Nacido en Valparaíso, el escritor siempre mantuvo una estrecha relación con la atmósfera bohemia e inspiradora del puerto. El espacio público se concibió como un tributo a su legado, ofreciendo un lugar de contemplación que invita a la misma introspección y creatividad que caracterizaron sus guiones.</p><h3><br></h3><h3><strong>Perspectiva y Arquitectura del Descanso</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Una Vista Panorámica Única:</strong> Al encontrarse en una de las curvas de la calle Florida (la vía principal que sube hacia La Sebastiana), el mirador regala una panorámica impecable del plan de la ciudad, el sector de la Plaza Victoria y el constante movimiento del puerto en la bahía.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Diseño Urbano Porteño:</strong> Cuenta con muretes de contención, barandas de protección y escaños que invitan a los transeúntes a hacer un alto en la caminata. Su entorno está rodeado de las clásicas fachadas de calamina, escaleras empinadas y la vegetación trepadora típica de las quebradas de Bellavista.</li></ol><h3><br></h3><h3><strong>Un Hito en la Ruta Cultural</strong></h3><p>Es una parada estratégica y un respiro ideal si te encuentras realizando el circuito peatonal que sube desde los murales del <strong>Museo a Cielo Abierto</strong> o la <strong>Plaza Mena (Plaza de los Poetas)</strong> en dirección a <strong>La Sebastiana</strong>. Detenerse en el Mirador Arturo Moya Grau permite experimentar el ritmo pausado de la vida residencial de los cerros, lejos del bullicio turístico masivo, y capturar una de las postales fotográficas más auténticas y equilibradas del anfiteatro porteño.</p>', '[\"Mirador Arturo Moya Grau\",\"Arturo Moya Grau\",\"Ruta de los Poetas\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-24 16:17:26', '2026-05-24 16:17:26', 1),
(114, 33, 'Artesania GranBretaá', 'artesania-granbretaa', '', NULL, -33.04720000, -71.62970000, 'Valparaíso', '', NULL, NULL, NULL, NULL, NULL, 0, 1, '[\"oferta_del_dia\"]', NULL, 0, NULL, NULL, NULL, 0, '2026-05-24 17:44:47', '2026-05-24 17:45:49', 18),
(115, 34, 'Jane Kool Ach', 'jane-kool-ach', '', NULL, -33.03603700, -71.63458200, 'Valparaíso', '<p>Ubicada en el corazón del emblemático <strong>Cerro Santo Domingo en Valparaíso</strong>, <strong>Jane Kool Ach</strong> es mucho más que una tienda de artesanía; es un refugio visual que captura la esencia vibrante, bohemia y marina de la ciudad puerto. Desde que te aproximas por sus coloridas calles adoquinadas, la fachada turquesa con su letrero hecho a mano te invita a descubrir un universo de texturas y colores locales.</p><p><br></p><p>El alma de la tienda son, sin duda, <strong>los collages</strong>. Sus paredes funcionan como una galería donde se exhiben complejas obras hechas a mano que reinterpretan los paisajes de Valparaíso: sus casas colgantes, los cerros laberínticos, los trolebuses y el mar, todo construido mediante la superposición de papeles, telas y texturas recicladas.</p><p>Además de su especialidad, el espacio está cuidadosamente decorado con estanterías de madera repletas de artesanía típica de la zona:</p><p><br></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Textiles auténticos:</strong> Mantas, bufandas y cojines con patrones andinos y costeros.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Miniaturas patrimoniales:</strong> Réplicas a escala de los clásicos ascensores y coloridos micros/trolebuses de Valparaíso.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Papelería de autor:</strong> Cuadernos y bitácoras de viaje con portadas de collage exclusivas.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Joyas y accesorios:</strong> Piezas únicas hechas por artesanos de la región.</li></ol><p><br></p><h3><strong>¿Por qué visitarla?</strong></h3><p><br></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Arte con identidad local:</strong> No te llevarás un souvenir genérico. Cada collage y artesanía cuenta una historia real sobre la arquitectura, la nostalgia y la vida cotidiana del puerto.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Experiencia inmersiva en el Cerro Santo Domingo:</strong> Visitar la tienda es la excusa perfecta para recorrer uno de los cerros más antiguos e históricos de Valparaíso, lejos de los circuitos turísticos más saturados, ofreciendo una experiencia mucho más auténtica y vecinal.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Apoyo al comercio local:</strong> Cada objeto seleccionado en la tienda impulsa el trabajo de creadores de la región, preservando las técnicas manuales y el patrimonio vivo de la ciudad.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Un festín para los ojos:</strong> La estética del lugar, con sus vigas de madera a la vista, luces cálidas y el contraste de colores, convierte la compra en un paseo artístico en sí mismo.</li></ol>', '[]', NULL, NULL, NULL, NULL, 1, 1, '[\"oferta_del_dia\"]', '<p>20% de descuento en todos los productos por el mes del patrimonio.</p>', 1, NULL, NULL, 'perfiles/2K6nzJbR0ibJEO3GYRzDiYjPBIeuXiHyKQwKGz7S.jpg', 0, '2026-05-26 14:32:54', '2026-05-26 14:38:35', 18),
(116, 35, 'La Morada Alegre', 'la-morada-alegre', 'Cerro Alegre', NULL, -33.04366100, -71.62844900, 'Valparaíso', '<p>Cafeteria de especialidad y pasteleria artesanal que mezcla tradicion o vanguardia , ofrecemos productos para todos los gustos, opciones veganas. ubicados en el corazón de cerro alegre, abierto todos los dias de 10:30 a 20:00hrs</p>', '[\"Caf\\u00e9 de especialidad\",\"pasteler\\u00eda artesanal\",\"wifi\",\"productos chilenos\",\"opciones veganas\",\"te con sentimiento\"]', NULL, 'https://www.instagram.com/lamoradaalegre?igsh=MXIxcTA0NnEzd2dtZQ==', 'Abierto todos los dias de 10:30 a 20:00hrs', NULL, 1, 1, '[\"oferta_del_dia\",\"menu_del_dia\",\"carta\"]', NULL, 0, NULL, NULL, 'perfiles/mAgoRgXevUoSKwkv2lBGgWGrRNhag66g20xuLApu.jpg', 0, '2026-05-27 16:43:54', '2026-05-30 12:53:21', 2),
(117, 1, 'Ascensor Lecheros', 'ascensor-lecheros-256', 'Cerro Lecheros', NULL, -33.04441000, -71.60245500, 'Valparaíso', '<p><strong>*FUERA DE SERVICIO DESDE EL 2007*</strong></p><p><br></p><p>El <strong>Ascensor Lecheros</strong>, ubicado en el cerro homónimo, es uno de los funiculares más cargados de historia, mística y nostalgia dentro del entramado urbano de Valparaíso.</p><p><br></p><h3><strong>El Gigante Dormido de Cerro Lecheros</strong></h3><p>Inaugurado en <strong>1906</strong>, el Ascensor Lecheros es un Monumento Histórico de Chile que conecta el plan de la ciudad (calle Eusebio Lillo) con la parte alta del cerro. Aunque actualmente se encuentra fuera de servicio debido a un incendio que dañó sus instalaciones y su posterior estado de abandono, su imponente estructura de madera y hierro sigue alzándose en la ladera como un melancólico recordatorio del esplendor de la ingeniería industrial de principios del siglo XX.</p><p><br></p><h3><strong>Historia y Nombre Singular</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>¿Por qué \"Lecheros\"?:</strong> El nombre del ascensor y del cerro proviene de la antigua tradición del siglo XIX, cuando los repartidores de leche bajaban desde las quintas ubicadas en las altas cumbres para abastecer al plano de la ciudad a lomo de burro o caballo.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Ingeniería Desafiante:</strong> Su vía férrea destaca por una inclinación pronunciada que sortea la accidentada geografía del sector, habiendo sido por más de un siglo el medio de transporte vital y el alma de la comunidad de este barrio obrero.</li></ol><p><br></p><h3><strong>Un Hito Artístico en su Entorno</strong></h3><p>A pesar de su inactividad como transporte, la estación y sus alrededores se han transformado en un lienzo vibrante para el arte urbano. Quienes visitan el sector para admirar las impresionantes obras en gran formato de las fachadas colindantes —como las reconocidas piezas de <em>Saile</em> o de <em>Albus Valley</em>— encuentran en la silueta del ascensor una postal única del Valparaíso más auténtico, donde conviven el patrimonio industrial latente y la vanguardia del <em>street art</em>.</p><p><br></p><p>Es un punto ideal para entender la resistencia de la identidad porteña, la vida comunitaria y la nostalgia por los antiguos rieles que alguna vez movieron al puerto.</p>', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-29 21:43:57', '2026-05-29 22:18:59', 16),
(118, 1, 'Ascensor Larraín', 'ascensor-larrain-182', 'Cerro Larraín', NULL, -33.04668800, -71.60185900, 'Valparaíso', '<p><strong>*FUERA DE SERVICIO DESDE EL 2010*</strong></p><p><br></p><p>El <strong>Ascensor Larraín</strong>, ubicado en el cerro del mismo nombre, es otro de los históricos y emblemáticos funiculares de Valparaíso que custodia la memoria urbana y obrera de la ciudad.</p><p><br></p><h3><strong>El Acceso al Cerro de los Almacenes</strong></h3><p>Inaugurado en <strong>1909</strong>, el Ascensor Larraín fue construido para conectar el plano de la ciudad (específicamente desde la calle Eusebio Lillo, muy cerca de donde también se encuentra el Ascensor Lecheros) con las zonas residenciales del <strong>Cerro Larraín</strong>. Declarado <strong>Monumento Histórico</strong>, este funicular destaca por sortear una pendiente pronunciada a través de un largo tendido de rieles que se eleva sobre las azoteas y los patios de las casas colindantes.</p><p><br></p><h3><strong>Historia, Nombre y Decadencia</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Origen de su Nombre:</strong> Debe su nombre a Carlos Larraín, un destacado vecino y propietario de terrenos en el sector durante el siglo XIX, época en la que el cerro comenzó a poblarse fuertemente por familias vinculadas a las actividades portuarias y comerciales.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>El Estado Actual:</strong> Al igual que su vecino del Cerro Lecheros, el Ascensor Larraín se encuentra actualmente fuera de servicio y en un estado de abandono estructural a la espera de un proyecto definitivo de restauración. Su imponente estación superior e inferior y sus rieles vacíos forman parte de esa postal nostálgica y melancólica tan propia del paisaje de Valparaíso.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><br></li></ol><h3><strong>Identidad de Barrio y Arte Callejero</strong></h3><p>El entorno del Ascensor Larraín es un reflejo vivo de la resistencia de los barrios tradicionales porteños. A pesar de que el funicular no está en movimiento, los pasajes y escaleras que lo rodean (como la subida Larraín) se han llenado de vida a través de coloridos murales, huertos comunitarios y proyectos vecinales que buscan mantener activo el espacio público.</p><p>Para el viajero, acercarse a su estructura es una oportunidad perfecta para alejarse de los circuitos turísticos más masivos y comprender la escala real, el patrimonio industrial y la vida cotidiana de los cerros de Valparaíso.</p>', '[\"scensor Larra\\u00edn\",\"Monumento Hist\\u00f3rico\",\"Patrimonio Industrial\",\"Funicular\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-29 22:24:04', '2026-04-29 22:24:04', 16),
(119, 1, 'Ascensor Artillería', 'ascensor-artilleria-253', 'Cerro Artillería', NULL, -33.03382600, -71.63004900, 'Valparaíso', '<p><strong>*FUERA DE SERVICIO DESDE EL 2021*</strong></p><p><br></p><p>El <strong>Ascensor Artillería</strong>, ubicado en el cerro del mismo nombre, es sin duda uno de los funiculares más icónicos, fotografiados y estratégicos de todo Valparaíso. Al conectar directamente el sector aduanero con uno de los miradores más espectaculares del puerto, es una parada obligatoria para cualquier visitante.</p><p><br></p><h3><strong>El Gran Conector del Barrio Puerto</strong></h3><p>Inaugurado en <strong>1893</strong>, el Ascensor Artillería es uno de los más antiguos de la ciudad y destaca por su enorme valor patrimonial y de ingeniería. Debido a la alta demanda que tenía a principios del siglo XX —gracias a la presencia de la Escuela Naval en la cumbre—, llegó a ser el único ascensor del puerto en contar con <strong>dos líneas independientes de dos carros cada una</strong> (cuatro carros en total funcionando en paralelo). Su imponente estructura de madera de cara al mar es un símbolo inconfundible de la postal porteña.</p><p><br></p><h3><strong>La Puerta de Entrada al Paseo 21 de Mayo</strong></h3><p>La gran ventaja de este ascensor es su ubicación estratégica:</p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Estación Inferior:</strong> Se encuentra en la Plaza Wheelwright, a pasos del edificio de la Aduana y del acceso al puerto comercial.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Estación Superior:</strong> Te deja directamente en el <strong>Paseo 21 de Mayo</strong>, un espectacular balcón natural que ofrece la vista panorámica más completa y famosa de la bahía de Valparaíso, el movimiento de los contenedores y los barcos mercantes.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><br></li></ol><h3><strong>Un Viaje en el Tiempo</strong></h3><p>Viajar en el Ascensor Artillería (o contemplar su imponente estructura cuando entra en mantención) es experimentar la verdadera escala de la ingeniería del siglo XIX. El crujir de sus maderas, el diseño de sus carros y la pronunciada inclinación de sus rieles te transportan de inmediato a la época de oro del comercio marítimo chileno. En la cumbre, además del mirador, puedes visitar las ferias de artesanía local y el imponente edificio del Museo Marítimo Nacional.</p>', '[\"Ascensor Artiller\\u00eda\",\"Monumento Hist\\u00f3rico\",\"Paseo 21 de Mayo\",\"Patrimonio Industrial\",\"Funicular\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-29 22:48:58', '2026-05-29 22:51:03', 16),
(120, 1, 'Ascensor Monjas', 'ascensor-monjas-888', 'Cerro Monjas', NULL, -33.05185000, -71.61369900, 'Valparaíso', '<p><strong>*FUERA DE SERVICIO DESDE EL 2009*</strong></p><p><br></p><p>El <strong>Ascensor Monjas</strong>, ubicado en el cerro del mismo nombre, es una de las joyas de la ingeniería funicular de Valparaíso y un Monumento Histórico que destaca por su imponente altura y su particular trayecto visual.</p><p><br></p><h3><strong>El Desafío a la Verticalidad</strong></h3><p>Inaugurado en <strong>1912</strong>, el Ascensor Monjas fue construido para conectar el plan de la ciudad (desde la calle Baquedano) con el <strong>Cerro Monjas</strong>, un sector que comenzaba a poblarse rápidamente a principios del siglo XX. Lo que hace espectacular a este ascensor es su imponente recorrido: corre sobre una estructura de viaducto exenta de la ladera en su tramo inferior, lo que genera la sensación de estar flotando sobre los techos del barrio a medida que el carro asciende por su empinada vía.</p><h3><br></h3><h3><strong>Historia, Identidad y Resiliencia</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>El Origen del Nombre:</strong> El cerro y el ascensor deben su nombre a las monjas de la Congregación de los Sagrados Corazones, quienes tenían terrenos en las cercanías durante la época colonial.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Patrimonio Recuperado:</strong> Tras enfrentar un largo periodo de inactividad que preocupó a la comunidad, este funicular fue sometido a un profundo proceso de restauración por parte del Estado, devolviéndole su rol vital como transporte público y orgullo patrimonial del barrio.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Vistas Singulares:</strong> Al subir, ofrece una perspectiva única y menos masiva de las quebradas colindantes (como la del Cerro Mariposa) y de la arquitectura residencial típica del Valparaíso profundo.</li></ol><h3><br></h3><h3><strong>Consejo para el Visitante</strong></h3><p>El Ascensor Monjas es ideal para los viajeros que buscan escapar de los circuitos turísticos tradicionales (como los cerros Alegre y Concepción) y quieren adentrarse en la auténtica vida de barrio porteña. Al bajarte en la estación superior, te encontrarás con calles tranquilas, coloridos murales vecinales y una atmósfera nostálgica donde el tiempo parece avanzar a otro ritmo.</p>', '[]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-29 22:57:43', '2026-06-02 22:13:55', 16),
(121, 37, 'Valparaíso Lucha Libre', 'valparaiso-lucha-libre', 'Cerro Rodelillo', NULL, -33.05535500, -71.58117900, 'Valparaíso', '<p>Si buscas una actividad diferente durante tu visita a Valparaíso, <strong>Valparaíso Lucha Libre</strong> ofrece un espectáculo que mezcla deporte, teatro, humor y cultura local en una experiencia auténticamente porteña.</p><p><br></p><p>A través de personajes inspirados en la historia, las leyendas urbanas, la política, la vida de los cerros y el carácter de la ciudad, cada función presenta combates llenos de emoción y entretenimiento para toda la familia. No es necesario conocer las reglas de la lucha libre: las historias, los personajes y la participación del público hacen que cualquier visitante pueda disfrutar del espectáculo desde el primer momento.</p><p><br></p><p>Valparaíso Lucha Libre permite a los turistas descubrir una faceta distinta de la ciudad, conectándose con su creatividad, irreverencia y riqueza cultural mediante un formato cercano y participativo.</p><p><br></p><p><strong>¿Por qué asistir?</strong></p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Es una experiencia cultural diferente a los circuitos turísticos tradicionales.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Permite conocer personajes y relatos inspirados en la identidad local.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Es un espectáculo familiar y apto para todas las edades.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Ofrece una mirada entretenida y original sobre la vida porteña.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span>Apoya el desarrollo de artistas y proyectos culturales de la región.</li></ol><p><br></p><p><strong>Valparaíso Lucha Libre no es solo un show de lucha libre: es una forma de vivir Valparaíso desde el ring, entre héroes, villanos y personajes que reflejan el alma de la ciudad.</strong></p>', '[\"Lucha Libre\",\"Wrestling\"]', 'https://www.youtube.com/watch?v=C8lmda53d7I', 'http://www.instagram.com/valpoluchalibre', 'Valparaíso Lucha Libre realiza funciones en fechas programadas. Revisa nuestra cartelera y redes sociales para conocer los próximos eventos, horarios y donde adquirir tus entradas.', NULL, 1, 1, '[\"oferta_del_dia\",\"agenda\"]', NULL, 0, NULL, 'Lucha Libre, Entretenimiento, Deportes, Wrestling.', NULL, 0, '2026-05-30 04:01:58', '2026-06-04 16:20:20', 5),
(122, 1, 'Ascensor Villaseca', 'ascensor-villaseca-738', 'Playa Ancha', NULL, -33.03147900, -71.62986700, 'Valparaíso', '<p><strong>*FUERA DE SERVICIO DESDE EL 2006*</strong></p><p><br></p><p>El <strong>Ascensor Villaseca</strong>, ubicado en el <strong>Cerro Playa Ancha</strong>, es uno de los funiculares más singulares y monumentales de Valparaíso. Destaca no solo por su imponente estructura de ingeniería, sino también por ser el único ascensor del puerto que se interna hacia la zona residencial del sector poniente de la ciudad.</p><p><br></p><h3><strong>El Coloso de Playa Ancha</strong></h3><p>Inaugurado en <strong>1907</strong>, el Ascensor Villaseca fue construido para facilitar la conectividad de los habitantes del Cerro Playa Ancha con el plan de la ciudad (conectando la calle Antonio Varas con la calle Pedro León Gallo). Su diseño es impresionante: cuenta con uno de los recorridos más largos y una de las estructuras de rodaje más altas de todo el sistema de ascensores de Valparaíso, cruzando una pronunciada quebrada mediante un imponente viaducto de fierro.</p><p><br></p><h3><strong>Historia y Estado Actual</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Patrimonio Industrial:</strong> Declarado <strong>Monumento Histórico</strong>, este ascensor representa la época de máxima expansión urbana y el auge industrial de Valparaíso a principios del siglo XX.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Proceso de Restauración:</strong> El ascensor ha pasado por largos periodos de inactividad técnica. Al formar parte del grupo de ascensores adquiridos por el Estado para su recuperación patrimonial, se ha convertido en un símbolo de la resiliencia y de la lucha de los vecinos de Playa Ancha por recuperar su transporte tradicional.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Estética de los Carros:</strong> Sus carros de madera, de líneas clásicas y robustas, fueron diseñados para soportar el flujo constante de trabajadores portuarios y marinos que habitaban el sector.</li></ol><p><br></p><h3><strong>Por qué incluirlo en la ruta</strong></h3><p>Playa Ancha es conocido como el \"reino independiente\" de Valparaíso debido a su gran tamaño y su identidad propia. Visitar el entorno del Ascensor Villaseca te permite explorar un Valparaíso diferente: uno de calles anchas, imponentes mansiones de la belle époque, almacenes de barrio tradicionales y una vida universitaria y de astilleros muy vibrante, lejos de los circuitos turísticos más masificados.</p>', '[\"Ascensor Villaseca\",\"Monumento Hist\\u00f3rico\",\"Patrimonio Industrial\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-05-30 13:11:20', '2026-06-03 00:52:29', 16),
(123, 1, 'Ascensor Florida', 'ascensor-florida-993', 'Cerro Florida', NULL, -33.04720000, -71.62970000, 'Valparaíso', '<p><strong>*FUERA DE SERVICIO DESDE EL 2009*</strong></p><p><br></p><p>El <strong>Ascensor Florida</strong>, ubicado en el cerro del mismo nombre, es otro de los históricos funiculares de Valparaíso que, a pesar del paso del tiempo y las vicisitudes del abandono, se mantiene en pie como un testigo silente de la época dorada del transporte porteño.</p><p><br></p><h3><strong>El Conector del Cerro Florida</strong></h3><p>Inaugurado en <strong>1906</strong>, el Ascensor Florida fue diseñado y construido para mejorar la conectividad de los habitantes de este cerro, un sector que crecía rápidamente debido a la expansión urbana del puerto. Su trayecto une el plan de la ciudad (desde la calle Marconi, muy cerca de la transitada Avenida Francia) con la parte alta del <strong>Cerro Florida</strong>, salvando una pronunciada pendiente en un tramo relativamente corto pero muy empinado.</p><h3><br></h3><h3><strong>Historia, Identidad y Patrimonio</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Monumento Histórico:</strong> Al igual que sus \"hermanos\" de la red de funiculares de la ciudad, está protegido bajo la ley de monumentos debido a su incalculable valor para la arqueología industrial y la identidad de Valparaíso.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>El Impacto de la Tragedia:</strong> El ascensor ha tenido una historia compleja. Sufrió graves daños estructurales tras un trágico accidente a principios de la década de 1980 y, posteriormente, un incendio en su estación superior terminó por sacarlo de operaciones. Actualmente se encuentra fuera de servicio, a la espera de los anhelados proyectos estatales de recuperación integral.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Estética de Resistencia:</strong> Su estructura de madera y latón, devorada de manera pintoresca por la vegetación de la quebrada, forma parte del paisaje melancólico que tanto fascina a fotógrafos y caminantes.</li></ol><h3><br></h3><h3><strong>Un Hito en el Camino a la Cultura</strong></h3><p>El entorno del Ascensor Florida es ideal para explorarlo a pie. Al estar colindante con el <strong>Cerro Bellavista</strong>, las escaleras y pasajes que suben bordeando sus antiguos rieles te conectan de manera muy orgánica con atractivos principales como el <em>Museo a Cielo Abierto</em>, la <em>Plaza Mena</em> y, finalmente, la casa museo de Pablo Neruda, <em>La Sebastiana</em>. Es una parada perfecta para sentir el pulso del Valparaíso residencial, donde el patrimonio del transporte y la vida cotidiana de barrio se cruzan a cada paso.</p>', '[\"Ascensor Florida\",\"Monumento Hist\\u00f3rico\",\"Patrimonio Industrial\",\"Funicular\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-06-03 01:01:44', '2026-05-07 01:28:41', 16),
(124, 1, 'Ascensor Concepción', 'ascensor-concepcion-684', 'Cerro Concepción', NULL, -33.04073100, -71.62638500, 'Valparaíso', '<p><strong>*OPERATIVO*</strong></p><p><br></p><p>El <strong>Ascensor Concepción</strong> no es solo un medio de transporte, es el pionero absoluto de la identidad porteña. Al ser el primer funicular construido en la ciudad, constituye una parada obligatoria para entender la ingeniería, la historia y el nacimiento del turismo en los cerros de Valparaíso.</p><p><br></p><h3><strong>El Pionero de los Cerros</strong></h3><p>Inaugurado en <strong>1883</strong>, el Ascensor Concepción ostenta el título del <strong>ascensor más antiguo de Valparaíso</strong>. Su creación marcó un hito revolucionario en el urbanismo de la ciudad, ya que demostró que era posible conectar de manera eficiente el activo plano comercial con las nacientes zonas residenciales en las alturas. Originalmente, sus carros de madera se movían gracias a un ingenioso sistema de contrapesos de agua, para luego ser modernizado con un motor a vapor y, finalmente, con electricidad.</p><p><br></p><h3><strong>Un Trayecto Histórico y Estratégico</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Estación Inferior (El Plan):</strong> Se ingresa discretamente a través de un pintoresco pasaje en la calle <strong>Esmeralda</strong> (frente a la escalinata del Reloj Turri), mimetizándose perfectamente con la arquitectura financiera del plan.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Estación Superior (El Cerro):</strong> El viaje, que dura apenas unos segundos sorteando una empinada ladera, te deposita directamente en el espectacular <strong>Paseo Gervasoni</strong>, en pleno <strong>Cerro Concepción</strong>.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Vistas de Postal:</strong> Al salir de la estación superior, te recibe una de las panorámicas más hermosas y cotizadas de la bahía, el muelle Prat y los tejados de las antiguas casonas de estilo alemán e inglés.</li></ol><p><br></p><h3><strong>La Puerta de Entrada a la Bohemia Patrimonial</strong></h3><p>A diferencia de otros ascensores históricos que lamentablemente se encuentran detenidos, el Ascensor Concepción se mantiene <strong>plenamente operativo</strong> y restaurado. Utilizarlo es vivir una experiencia sensorial única: el crujir nostálgico de su estructura de madera y el tintineo metálico de sus cables te transportan de inmediato al siglo XIX. Es, además, la vía de acceso más orgánica para comenzar a perderse a pie por las laberínticas e icónicas calles de los cerros Concepción y Alegre, repletas de murales, cafés de especialidad, hoteles boutique y tiendas de diseño independiente.</p>', '[\"Ascensor Concepci\\u00f3n\",\"Monumento Hist\\u00f3rico\",\"El M\\u00e1s Antiguo\",\"Paseo Gervasoni\",\"Patrimonio Industrial\",\"Funicular\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-06-03 01:12:25', '2026-06-03 01:15:18', 16);
INSERT INTO `puntosinteres` (`id`, `user_id`, `title`, `slug`, `sector`, `direccion`, `lat`, `lng`, `ciudad`, `description`, `tags`, `video_url`, `enlace`, `horario`, `autor`, `activo`, `es_cliente`, `modulos_habilitados`, `oferta_del_dia`, `oferta_activa`, `oferta_expira_at`, `descripcion_busqueda`, `imagen_perfil`, `eliminado`, `created_at`, `updated_at`, `categoria_id`) VALUES
(125, 1, 'Ascensor Mariposas', 'ascensor-mariposas-221', 'Cerro Mariposas', NULL, -33.05004100, -71.61731400, 'Valparaíso', '<p>El <strong>Ascensor Mariposas</strong>, ubicado en el cerro del mismo nombre, es otro de los valiosos e históricos funiculares que componen el tejido patrimonial y de transporte de Valparaíso.</p><p><br></p><h3><strong>El Conector de la Quebrada</strong></h3><p>Inaugurado en <strong>1904</strong>, el Ascensor Mariposas fue construido para conectar el plan de la ciudad (desde la calle Barbosa, cerca de la Avenida Francia) con las zonas residenciales del <strong>Cerro Mariposas</strong>. Declarado <strong>Monumento Histórico</strong>, este ascensor destaca por tener un trazado particular que avanza encajonado de manera muy pintoresca entre las viviendas y la vegetación de la ladera, salvando la pronunciada pendiente del cerro en un viaje que históricamente fue vital para sus vecinos.</p><h3><br></h3><h3><strong>Historia, Identidad y Nostalgia</strong></h3><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Origen de su Nombre:</strong> El cerro y el ascensor deben su poético nombre a la enorme cantidad de mariposas de vibrantes colores que, según cuentan las crónicas de los antiguos habitantes, poblaban las verdes quebradas del sector antes de la densa urbanización del puerto.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>El Estado Actual:</strong> Al igual que varios de los ascensores de la zona (como el Florida o el Monjas), el Ascensor Mariposas ha pasado por largos períodos fuera de servicio y abandono técnico, quedando a la espera de los planes de restauración integral y modernización por parte del Estado para devolver el movimiento a sus carros de madera.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><br></li></ol><h3><strong>Un Paseo por el Valparaíso Residencial</strong></h3><p>Para el viajero inquieto, acercarse a la estructura del Ascensor Mariposas ofrece la oportunidad perfecta de salirse de las rutas turísticas habituales. Recorrer sus pasajes aledaños y las escaleras que flanquean las antiguas vías permite apreciar de cerca la arquitectura residencial tradicional, pequeños almacenes de barrio y hermosos murales comunitarios. Además, las calles de este cerro conectan de manera muy fluida con la parte alta del Cerro Florida y el Cerro Bellavista, permitiendo armar una ruta de caminata con vistas panorámicas únicas de las quebradas porteñas.</p>', '[\"Monumento Hist\\u00f3rico\",\"Patrimonio Industrial\",\"Funicular\"]', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-06-03 01:28:41', '2026-05-07 01:28:41', 16),
(126, 38, 'Teatromuseo del Títere y el Payaso', 'teatromuseo-del-titere-y-el-payaso', 'Cerro Cárcel', 'Cumming 795, Valparaíso', -33.04808600, -71.63033400, 'Valparaíso', '<p>Museo dedicado a la promoción, difusión e investigación de las artes cómicas y las marionetas. En este espacio, podrás descubrir la tradición de los títeres, sus técnicas, orígenes y presenciar de cerca marionetas de hasta 4 metros de altura, también conocerás las artes cómicas por medio del payaso, las máscaras, la historia del circo chileno y los distintos referentes mundiales de la comicidad.</p><p>En este museo el juego y la interacción es fundamental, podrás manipular marionetas en exhibición y personificarte con vestimenta payasa que estará a tu disposición.</p><p>Además, podrás disfrutar en nuestra sala de teatro espectáculos de títeres y payasos todos los domingos.</p>', '[\"Museo\",\"Teatro\",\"\"]', 'https://youtube.com/@teatromuseo1?si=QPUyw9D0hE5qaTwL', 'https://www.instagram.com/teatromuseo?igsh=MWd1dmw3bDA3OHp3OQ%3D%3D&utm_source=qr', 'Vie - Dom 12:00 a 15:00 horas', NULL, 1, 1, '[\"oferta_del_dia\",\"agenda\",\"entradas\",\"exposiciones\"]', NULL, 0, NULL, 'Museo, Interactivo, Valparaíso, Cerro Cárcel, Títeres, Payasos, Circo, Niños, Niñas, Familiar, Teatro', 'perfiles/sHHoy5iL4EfCYCBrrFEPZ0DQu2tiqZCKDPTgRodX.png', 0, '2026-06-04 17:19:28', '2026-06-04 18:23:43', 7),
(127, 1, 'Museo Institucional UTFSM', 'museo-institucional-utfsm-198', 'Cerro Placeres', 'Av. España 1680', -33.03436900, -71.59465000, 'Valparaíso', '<p>El <strong>Museo Institucional de la Universidad Técnica Federico Santa María (UTFSM)</strong>, ubicado en el imponente Cerro Placeres de Valparaíso, es un espacio dedicado a preservar y difundir la rica historia de una de las casas de estudios de ingeniería y ciencia más prestigiosas del país.</p><p><br></p><h3><strong>El Legado del Filántropo</strong></h3><p>El museo se sitúa dentro de la misma universidad, un conjunto arquitectónico declarado <strong>Monumento Histórico</strong> que destaca a nivel internacional por su estilo gótico-académico y sus espectaculares jardines con vista al océano. El espacio museístico está diseñado para honrar la memoria y la visión de <strong>Federico Santa María Carrera</strong>, el gran filántropo chileno que legó toda su fortuna para la creación de una escuela que permitiera el acceso a la educación técnica de vanguardia a los sectores populares.</p><h3><br></h3><h3><strong>Historia, Ciencia y Tecnología</strong></h3><p>La muestra del museo ofrece un fascinante recorrido a través de:</p><ol><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Objetos y Documentos Históricos:</strong> Se conservan el testamento original de Federico Santa María, planos de la construcción del campus de la década de 1930 y fotografías que retratan la evolución de la vida universitaria.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>Arqueología Industrial y Científica:</strong> Exhibe antiguos instrumentos de laboratorio, maquinaria de precisión y equipos tecnológicos que se utilizaban en los albores de la enseñanza de la ingeniería en el puerto.</li><li data-list=\"bullet\"><span class=\"ql-ui\" contenteditable=\"false\"></span><strong>El Álbum Familiar y Personal:</strong> Mobiliario, pertenencias y cartas que permiten a los visitantes comprender la compleja y visionaria personalidad del fundador.</li></ol><h3><br></h3><h3><strong>Por qué visitarlo</strong></h3><p>Visitar este museo es la excusa perfecta para recorrer los pasillos, patios internos y los impresionantes miradores de la UTFSM (a menudo comparada con un \"castillo\" o con la estética de Hogwarts frente al mar). Ofrece una perspectiva única del Valparaíso centrado en el desarrollo industrial, científico y educativo, complementando perfectamente las tradicionales rutas literarias y artísticas de la ciudad.</p>', '[]', NULL, 'https://www.instagram.com/museo_usm/', 'Lunes a viernes de 11:00 a 15:45', NULL, 1, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '2026-06-05 13:56:52', '2026-06-06 13:23:11', 7),
(128, 45, 'oh jane', 'oh-jane', '', NULL, -33.04570600, -71.62053900, 'Valparaíso', '', NULL, NULL, NULL, NULL, NULL, 1, 1, '[\"oferta_del_dia\"]', NULL, 0, NULL, NULL, NULL, 0, '2026-06-12 01:56:07', '2026-06-12 01:56:07', 17),
(129, 44, 'ASU MARE', 'asu-mare', 'Barrio Puerto', 'Cochrane 332, valparaiso', -33.03681200, -71.62998500, 'Valparaíso', '<p>En el corazón histórico del Barrio Puerto, donde Valparaíso despliega su magia, nace nuestra propuesta gastronómica. Somos un puente entre dos mundos: la maestría del <strong>lomo saltado</strong>, con ese toque ahumado y auténtico de la cocina peruana, y la nobleza de una reconfortante <strong>paila marina</strong>, preparada con la frescura que solo nuestras costas chilenas nos entregan. Ven a disfrutar de una cocina con alma, donde cada bocado cuenta una historia. Te esperamos para celebrar lo mejor de Chile y Perú en una sola mesa.</p>', '[\"Wifi\",\"comida chilena\",\"comida peruana\"]', NULL, 'https://www.instagram.com/asumare_chile/', 'Lunes a domingo de 12pm a 6 pm', NULL, 1, 1, '[\"oferta_del_dia\",\"avisos\",\"agenda\",\"menu_del_dia\",\"carta\"]', NULL, 0, NULL, 'Somos en restaurante especializado en comida chilena y  peruana, pescados y mariscos atendidos por sus propios dueños.', 'perfiles/cdc01da0-8f49-4fed-ae53-98dd614da8d2.webp', 0, '2026-06-12 17:43:36', '2026-06-12 20:23:25', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `punto_modulo_datos`
--

CREATE TABLE `punto_modulo_datos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `punto_interes_id` bigint(20) UNSIGNED NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`datos`)),
  `actualizado_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `punto_modulo_datos`
--

INSERT INTO `punto_modulo_datos` (`id`, `punto_interes_id`, `modulo`, `datos`, `actualizado_en`, `created_at`, `updated_at`) VALUES
(1, 64, 'carta', '{\"texto\":\"<p><strong>Calugas de Pescado del Puerto: <\\/strong>Cubos de reineta fritos en batido secreto, acompa\\u00f1ados de salsa t\\u00e1rtara casera. \\u2014 $9.500<\\/p><p><strong>Empanaditas de Mariscos (6 unidades): <\\/strong>Fritas, rellenas de la pesca del d\\u00eda y queso fundido. \\u2014 $7.200<\\/p><p><strong>Ceviche Pionero:<\\/strong> Reineta y camarones marinados en lim\\u00f3n de pica, cilantro, cebolla morada y un toque de aj\\u00ed verde. \\u2014 $11.900<\\/p><p><strong>Tabla de los Cerros: <\\/strong>Selecci\\u00f3n de quesos locales, jam\\u00f3n serrano, aceitunas de Azapa y frutos secos. \\u2014 $14.800<\\/p><p><strong>Platos de Fondo Merluza Austral \\\"Puerto Principal\\\": <\\/strong>Frita en batido de cerveza negra, acompa\\u00f1ada de pur\\u00e9 picante o ensalada de porotos con cebolla. \\u2014 $12.500<\\/p><p><strong>Lomo a lo Pobre:<\\/strong> 300g de lomo liso, papas fritas, dos huevos fritos y cebolla frita. El cl\\u00e1sico absoluto. \\u2014 $15.900<\\/p><p><strong>Gnocchi de la Nonna Valpo<\\/strong>: Pasta artesanal en salsa de mariscos y crema, gratinada con queso parmesano. \\u2014 $13.200<\\/p><p><strong>S\\u00e1nguches<\\/strong> <strong>(En pan batido original) Churrasco Mar\\u00edtimo: <\\/strong>L\\u00e1minas de carne, queso fundido, tomate y mayonesa casera. \\u2014 $8.900<\\/p><p><strong>El Pionero Burger:<\\/strong> Hamburguesa de wagyu (200g), tocino ahumado, cebolla caramelizada, r\\u00facula y salsa de la casa. \\u2014 $10.500<\\/p><p>Bebestibles y Cocteler\\u00eda <strong>Pisco Sour<\\/strong> \\\"El Pionero\\\": Nuestra receta equilibrada (Copa 330cc). \\u2014 $5.500<\\/p><p><strong>Terremoto Porte\\u00f1o<\\/strong>: Pipe\\u00f1o, helado de pi\\u00f1a y un toque de granadina (o fernet). \\u2014 $4.800<\\/p><p><strong>Copa de Vino<\\/strong> (Casablanca\\/Leyda): Sauvignon Blanc o Pinot Noir. \\u2014 $4.500<\\/p><p><strong>Schop Artesanal<\\/strong> (500cc): Variedades locales (IPA, Lager, Stout). \\u2014 $5.200 Bebidas y Jugos Naturales: Frutas de la estaci\\u00f3n. \\u2014 $3.200<\\/p>\",\"pdf_ruta\":\"cartas\\/nkhzfSqZOdaUW5xUjY7EPTrxt6NgH2FbXwoYeKHN.pdf\"}', '2026-05-21 21:10:57', '2026-04-12 05:27:54', '2026-05-21 21:10:57'),
(2, 64, 'menu_del_dia', '{\"texto\":\"<p><strong>Incluye entrada, fondo, postre y bebida (copa de vino de la casa o jugo natural). <\\/strong><\\/p><p><strong>Entrada<\\/strong>: Caldillo de Congrio en pocillo de greda (el favorito de los poetas del puerto).<\\/p><p><strong>Plato de Fondo<\\/strong>: Reineta a la Plancha con costra de hierbas, acompa\\u00f1ada de papas r\\u00fasticas al romero y ensalada chilena.<\\/p><p><strong>Postre<\\/strong>: Leche Asada con toque de naranja y caramelo salado.<\\/p><p><strong>Final<\\/strong>: Caf\\u00e9 de grano o T\\u00e9 de hierbas. Precio: $9.800 CLP<\\/p>\"}', '2026-05-02 23:51:45', '2026-04-12 05:28:56', '2026-05-02 23:51:45'),
(3, 80, 'alojamiento', '{\"precio_desde\":\"25000\",\"entrada\":\"14:00\",\"salida\":\"18:00\",\"habitaciones\":\"<p><br><\\/p><p><strong>PIEZA PUERTO<\\/strong><\\/p><p><strong>Tipo:<\\/strong> Habitaci\\u00f3n privada<\\/p><p><strong>Capacidad:<\\/strong> 1 a 2 personas<\\/p><p><strong>Descripci\\u00f3n:<\\/strong> Habitaci\\u00f3n con cama queen y ventanal hacia la bah\\u00eda. Decoraci\\u00f3n minimalista con toques navales y un mapa mural de Valpara\\u00edso de 1900.<\\/p><p><strong>Incluye<\\/strong>: Ba\\u00f1o privado, toallas, calefacci\\u00f3n, escritorio y desayuno incluido.<\\/p><p><br><\\/p><p><strong>PIERZA CERRO<\\/strong><\\/p><p>Tipo: Habitaci\\u00f3n privada<\\/p><p>Capacidad: 1 a 2 personas<\\/p><p>Descripci\\u00f3n: Habitaci\\u00f3n con dos camas single o cama matrimonial, seg\\u00fan preferencia. Tiene vista hacia el cerro y los murales del pasaje. Es silenciosa y muy luminosa durante el d\\u00eda.<\\/p><p>Incluye: Ba\\u00f1o privado, toallas, calefacci\\u00f3n, hervidor y desayuno incluido.<\\/p><p><br><\\/p><p><strong>PIEZA1866<\\/strong><\\/p><p>Tipo: Suite principal<\\/p><p>Capacidad: 2 a 3 personas<\\/p><p>Descripci\\u00f3n: Nuestra habitaci\\u00f3n principal. Conserva el techo alto con vigas originales de la casona, tiene balc\\u00f3n franc\\u00e9s y vista de 180 grados al puerto. Ideal para parejas o una ocasi\\u00f3n especial.<\\/p><p>Incluye: Ba\\u00f1o privado con ducha tipo lluvia, minibar, toallas, batas y desayuno incluido.<\\/p><p><br><\\/p><p><strong>Espacios comunes y servicios<\\/strong><\\/p><p>Todos los hu\\u00e9spedes tienen acceso a cocina equipada de uso libre, terraza-mirador con parrilla, sala de estar con estufa a le\\u00f1a, rinc\\u00f3n de cowork con vista a la bah\\u00eda y servicio de lavander\\u00eda.<\\/p><p><br><\\/p><p>Servicios adicionales con costo: traslado desde el Aeropuerto de Santiago, tours de murales y puerto con gu\\u00eda local, arriendo de bicicletas y cena porte\\u00f1a todos los viernes.<\\/p>\",\"servicios\":[\"recepcion_24h\",\"wifi\",\"lavanderia\",\"estacionamiento\",\"desayuno\",\"desayuno_buffet\",\"almuerzo\",\"cena\",\"bar\",\"spa\",\"gimnasio\",\"piscina\",\"cocina_comun\",\"sala_comun\",\"cowork\",\"sala_eventos\",\"tours\",\"traslado\",\"terraza\",\"vista_mar\",\"mascotas\"],\"politicas\":\"<p><strong>POL\\u00cdTICAS DE RESERVA \\u2013 HOSTAL 1866<\\/strong><\\/p><p>Check-in y Check-out<\\/p><p> Check-in: desde las 14:00 hrs hasta las 22:00 hrs.<\\/p><p> Check-out: hasta las 11:00 hrs.<\\/p><p> Early check-in y late check-out sujetos a disponibilidad y con cargo adicional de $8.000 CLP por hora. Para llegadas despu\\u00e9s de las 22:00 hrs, se debe coordinar previamente con el hostal.<\\/p><p><br><\\/p><p><strong>Reservas y pago<\\/strong><\\/p><p> Para confirmar una reserva se solicita el pago del 50% del total al momento de reservar. El 50% restante se cancela al momento del check-in.<\\/p><p> Aceptamos transferencias bancarias, tarjetas de d\\u00e9bito\\/cr\\u00e9dito y efectivo en pesos chilenos. Los precios est\\u00e1n expresados en CLP y no incluyen IVA para hu\\u00e9spedes extranjeros que paguen con tarjeta internacional o d\\u00f3lares.<\\/p><p><br><\\/p><p><strong>Pol\\u00edtica de cancelaci\\u00f3n<\\/strong><\\/p><p> Cancelaci\\u00f3n gratuita hasta 72 horas antes de la fecha de llegada. Pasado ese plazo, se cobrar\\u00e1 el valor de la primera noche como cargo por cancelaci\\u00f3n tard\\u00eda.<\\/p><p> En caso de no presentarse sin aviso, se cobrar\\u00e1 el 100% de la primera noche.<\\/p><p> Para reservas en fechas especiales como A\\u00f1o Nuevo en Valpara\\u00edso, Semana Santa y feriados largos, la cancelaci\\u00f3n debe realizarse con al menos 7 d\\u00edas de anticipaci\\u00f3n.<\\/p><p><br><\\/p><p><strong>Pol\\u00edtica para grupos<\\/strong><\\/p><p> Para reservas de 4 habitaciones o m\\u00e1s, se requiere el pago del 100% por adelantado y la pol\\u00edtica de cancelaci\\u00f3n es de 14 d\\u00edas antes de la llegada.<\\/p><p><br><\\/p><p><strong>Normas de la casa<\\/strong><\\/p><p> Hostal 1866 es un espacio de descanso y respeto. No se permiten fiestas, ruidos molestos ni visitas externas en las habitaciones despu\\u00e9s de las 22:00 hrs.<\\/p><p> Espacio 100% libre de humo en interiores. Se puede fumar \\u00fanicamente en la terraza-mirador.<\\/p><p> No se aceptan mascotas, salvo perros de asistencia con documentaci\\u00f3n previa.<\\/p><p> Los da\\u00f1os a la propiedad o p\\u00e9rdida de llaves\\/tarjetas tendr\\u00e1n un cargo adicional seg\\u00fan evaluaci\\u00f3n.<\\/p>\"}', NULL, '2026-04-14 03:20:03', '2026-04-16 06:54:58'),
(5, 87, 'menu_del_dia', '{\"texto\":\"<p><strong>MEN\\u00da MIRADOR \\u2013 $9.900<\\/strong><\\/p><p><br><\\/p><p>Incluye fondo + bebestible + caf\\u00e9<\\/p><p><br><\\/p><p><strong>FONDO \\/ MAIN Elige 1 <\\/strong><\\/p><p>1. <strong>Chorrillana<\\/strong> Veggie: Papas nativas, salteado de verduras, huevo de campo, pebre<\\/p><p>2. <strong>Merluza<\\/strong> Austral: A la plancha, pur\\u00e9 r\\u00fastico de arvejas, ensalada porte\\u00f1a<\\/p><p>3. <strong>S\\u00e1nguche<\\/strong> Chacarero: Carne mechada, tomate, porotos verdes, aj\\u00ed verde, pan frica<\\/p><p><br><\\/p><p><strong>BEBESTIBLE \\/ DRINK <\\/strong><\\/p><p>Limonada menta jengibre o Jugo del d\\u00eda o Agua mineral<\\/p><p><br><\\/p><p><strong>POSTRE O CAF\\u00c9 <\\/strong><\\/p><p>Alfajor mini o Espresso<\\/p><p><br><\\/p><p><strong>Horario Men\\u00fa: 12:30 a 15:30 hrs <\\/strong><\\/p><p>Todos los platos con opci\\u00f3n sin gluten avisando al pedir<\\/p>\"}', '2026-05-03 00:42:38', '2026-04-21 18:59:31', '2026-05-03 00:42:38'),
(6, 87, 'carta', '{\"texto\":\"<p><strong>\\ud83d\\udccb Carta de Precios<\\/strong><\\/p><p><br><\\/p><p><strong>\\u2615 Cafeter\\u00eda y Bebidas <\\/strong><\\/p><p>Espresso Italiano: $2.200<\\/p><p>Capuccino del Poeta (con canela y chocolate): $3.400<\\/p><p>Caf\\u00e9 Latte Grande: $3.600 Chocolate Caliente con crema: $3.800<\\/p><p>T\\u00e9 en hebras (tetera individual): $2.900<\\/p><p><br><\\/p><p><strong>\\ud83c\\udf70 Pasteler\\u00eda Artesanal (Porci\\u00f3n) <\\/strong><\\/p><p>Torta de Hojarasca Manjar-Nuez: $4.500<\\/p><p>Torta Amor (Frambuesa, manjar, crema): $4.800<\\/p><p>Kuchen de Manzana sure\\u00f1o: $3.900 Pie de Lim\\u00f3n con merengue suizo: $3.500<\\/p><p>Cheesecake de Frutos del Bosque: $4.200<\\/p><p><br><\\/p><p><strong>\\ud83e\\udd6a Especialidades Saladas <\\/strong><\\/p><p>S\\u00e1ndwich Neruda (Salm\\u00f3n ahumado, r\\u00facula, queso crema): $7.800<\\/p><p>Oda al Queso (S\\u00e1ndwich caliente de 3 quesos en pan artesanal): $6.500<\\/p><p>Quiche del d\\u00eda con mix de hojas verdes: $5.900<\\/p><p>Paila de huevos (2) con tostadas y mantequilla: $4.200<\\/p><p><br><\\/p><p>\\ud83e\\udd68 <strong>Promociones \\\"La Once del Puerto\\\"<\\/strong><\\/p><p>Once Individual: $8.500 (Incluye: T\\u00e9 o Caf\\u00e9, s\\u00e1ndwich de jam\\u00f3n\\/queso caliente y una porci\\u00f3n de torta a elecci\\u00f3n).<\\/p><p>Once para dos \\\"Los Amantes de Teruel\\\": $15.500 (Incluye: 2 bebidas calientes, 2 porciones de torta y una porci\\u00f3n de tostadas para compartir).<\\/p>\"}', '2026-05-03 00:28:46', '2026-04-21 19:01:03', '2026-05-03 00:28:46'),
(7, 87, 'avisos', '{\"texto\":\"<p>Este fin de semana tendremos sorpresas. \\u00a1Atentos a Pindoor y a nuestras RRSS!<\\/p>\"}', '2026-05-04 07:33:39', '2026-05-04 07:33:39', '2026-05-04 07:33:39'),
(8, 116, 'carta', '{\"texto\":\"<p><br><\\/p>\",\"pdf_ruta\":\"cartas\\/xvOAOQVMWTxw945mobDo4zZbLR7axvR48C4yMzKO.pdf\"}', '2026-05-27 16:56:39', '2026-05-27 16:49:48', '2026-05-27 16:56:39'),
(9, 129, 'carta', '{\"texto\":\"<p><br><\\/p>\",\"pdf_ruta\":\"cartas\\/3CrQreN8pOGrFyvBbanGLkj4etinO7SY195vEB7I.pdf\"}', '2026-06-12 18:58:23', '2026-06-12 18:03:59', '2026-06-12 18:58:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `punto_modulo_items`
--

CREATE TABLE `punto_modulo_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `punto_interes_id` bigint(20) UNSIGNED NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`datos`)),
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `punto_modulo_items`
--

INSERT INTO `punto_modulo_items` (`id`, `punto_interes_id`, `modulo`, `datos`, `imagen`, `activo`, `orden`, `destacado`, `fecha`, `created_at`, `updated_at`) VALUES
(1, 81, 'eventos', '{\"titulo\":\"El reino de los lagartos\",\"descripcion\":\"Obra de teatro sobre el reino de los lagartos.\",\"tipo\":\"teatro\",\"hora\":\"14:41\",\"hora_fin\":\"15:41\",\"precio\":\"5000\",\"precio_texto\":\"Desde 3.000\",\"url_entradas\":\"http:\\/\\/www.ticketvalpo.cl\"}', 'eventos/uTvBR3nrOslIOW74CORv7kkiqg0yJjwS0FTncCSU.jpg', 1, 0, 1, '2026-04-24', '2026-04-14 06:37:27', '2026-04-14 06:37:27'),
(2, 81, 'eventos', '{\"titulo\":\"The Doors\",\"descripcion\":\"The Doors es una pel\\u00edcula biogr\\u00e1fica estadounidense sobre la banda de rock del mismo nombre, dirigida por Oliver Stone y estrenada en 1991. El guion fue escrito tanto por Stone como por Randall Jahnson, basado en el libro Riders On The Storm de John Densmore.\",\"tipo\":\"cine\",\"hora\":\"21:00\",\"hora_fin\":\"23:30\",\"precio\":\"2500\",\"precio_texto\":null,\"url_entradas\":\"https:\\/\\/www.ticketvalpo.cl\"}', 'eventos/NCarGKBnzZ5rZ1AfWYShnTRiDmrkp4H7BYdE818I.jpg', 1, 0, 1, '2026-04-18', '2026-04-14 06:44:24', '2026-04-14 06:44:24'),
(3, 81, 'eventos', '{\"titulo\":\"The Doors\",\"descripcion\":\"The Doors (1991), dirigida por Oliver Stone, es un biopic musical que narra el ascenso y ca\\u00edda de Jim Morrison (Val Kilmer) y su ic\\u00f3nica banda de rock en los a\\u00f1os 60. La pel\\u00edcula retrata el caos, el hedonismo y la creatividad de la \\u00e9poca, enfoc\\u00e1ndose en la transformaci\\u00f3n de Morrison de poeta a \\u00eddolo autodestructivo hasta su muerte en 1971.\",\"tipo\":\"cine\",\"hora\":\"22:00\",\"hora_fin\":\"12:00\",\"precio\":\"5000\",\"precio_texto\":null,\"url_entradas\":\"https:\\/\\/www.ticketchile.cl\"}', 'eventos/13W7CHl9MxoyMkZPj0dD8o62pXoXWN0lqXxEKw3L.jpg', 1, 0, 1, '2030-05-16', '2026-05-02 18:38:23', '2026-05-02 18:38:23'),
(4, 81, 'eventos', '{\"titulo\":\"La Pergola de las flores\",\"descripcion\":\"La Compa\\u00f1\\u00eda de Teatro Valparate presenta su versi\\u00f3n de La P\\u00e9rgola de las Flores, la comedia musical m\\u00e1s emblem\\u00e1tica del teatro chileno. Estrenada en 1960, la obra retrata con ingenio y ternura la lucha de las floristas de la P\\u00e9rgola San Francisco contra la ampliaci\\u00f3n de la Alameda que amenaza con destruirlas.\\r\\n\\r\\nEn esta puesta en escena, Valparate rescata el esp\\u00edritu popular y festivo del original, pero con una mirada desde el puerto: acentos, cuecas y una est\\u00e9tica que mezcla el Santiago de los a\\u00f1os 20 con la bohemia de Valpara\\u00edso. M\\u00e1s de 15 artistas en escena entre actores, m\\u00fasicos y bailarines dan vida a personajes inolvidables como Carmela, Tomasito, Rosaura San Mart\\u00edn y el Alcalde Alcib\\u00edades.\\r\\n\\r\\nTemas como la gentrificaci\\u00f3n, la migraci\\u00f3n campo-ciudad y el rol de la mujer resuenan hoy con fuerza. Entre canciones como Yo vengo de San Rosendo y La revuelta de las floristas, la obra celebra la resistencia colectiva, el amor y la identidad chilena.\",\"tipo\":\"teatro\",\"hora\":\"16:00\",\"hora_fin\":\"18:00\",\"precio\":\"10000\",\"precio_texto\":null,\"url_entradas\":\"https:\\/\\/www.ticketchile.cl\"}', 'eventos/ErKejsLyVwmlqipuM6atVSDeQ5vNL9P9o9Ex9UXh.jpg', 1, 0, 1, '2030-06-03', '2026-05-02 18:43:18', '2026-05-02 18:43:18'),
(5, 87, 'eventos', '{\"titulo\":\"Di\\u00e1logo porte\\u00f1o - Valpara\\u00edso y el fuego\",\"descripcion\":\"Antes de que el conquistador espa\\u00f1ol Juan de Saavedra llegara en 1536 y le pusiera el nombre de \\u00abValpara\\u00edso\\u00bb al puerto, los nativos se refer\\u00edan a esta regi\\u00f3n como Aliamapu, que se traduce como \\u00abtierra quemada\\u00bb o \\u00abpa\\u00eds quemado\\u201d. Posteriormente, ya avanzado el 1900, el cronista Joaqu\\u00edn Edwards Bello anot\\u00f3 que la ciudad deber\\u00eda llamarse Pir\\u00f3polis, o ciudad del fuego, debido a los edificios carbonizados que la adornaban, a consecuencia de grandes incendios. La historia reciente de Valpara\\u00edso tambi\\u00e9n ha sido marcada por la r\\u00e1pida propagaci\\u00f3n de las llamas entre las casas que cuelgan de los cerros. En esta nueva versi\\u00f3n del Di\\u00e1logo porte\\u00f1o, el historiador Pablo Aravena junto a los arquitectos Alberto Texido y Marcela Hurtado, analizaron desde diversas miradas la realidad de Valpara\\u00edso, atravesada por sus lamentables incendios.\",\"tipo\":\"conferencia\",\"hora\":\"12:37\",\"hora_fin\":\"12:37\",\"precio\":\"0\",\"precio_texto\":\"Entrada liberada\",\"url_entradas\":\"https:\\/\\/www.ticketchile.cl\"}', 'eventos/inGszwYSY2h33Xr7PawI5wX9FvOGwKJRz4WHWvLY.jpg', 1, 0, 1, '2030-05-11', '2026-05-02 19:39:04', '2026-05-02 19:39:04'),
(6, 126, 'entradas', '{\"etiqueta\":\"Aporte Voluntario\",\"precio\":\"0\",\"nota\":null}', NULL, 1, 0, 0, NULL, '2026-06-04 17:33:36', '2026-06-04 17:33:36'),
(7, 126, 'exposiciones', '{\"titulo\":\"Palabras Cantadas del Viento\",\"descripcion\":\"Funci\\u00f3n de teatro de sombras dedicado a primeras infancias. \\r\\nPalabras cantadas del viento es una obra de teatro de sombras dirigida a ni\\u00f1eces y p\\u00fablico familiar. La propuesta ofrece una mirada po\\u00e9tica y contemplativa sobre los cuatro vientos, utilizando la narrativa visual como eje central. La obra relata el ciclo de vida del Pew\\u00e9n, en donde los vientos transportan sus p\\u00f3lenes hasta otros \\u00e1rboles sagrados, propiciando la polinizaci\\u00f3n y el surgimiento de una nueva vida. De este proceso nacen los pi\\u00f1ones, semillas que contin\\u00faan su viaje por las aguas del r\\u00edo, encontr\\u00e1ndose con otros seres en la b\\u00fasqueda de un nuevo lugar para crecer. La experiencia esc\\u00e9nica se complementa\\r\\ncon otros peque\\u00f1os momentos inspirados en la naturaleza y una selecci\\u00f3n de folklore po\\u00e9tico, interpretado y musicalizado en vivo. La obra ha sido presentada en diversos espacios educativos y culturales de la comuna de Valdivia y Panguipulli.\\r\\n\\r\\n\\ud83d\\uddd3\\ufe0f \\u00bfCu\\u00e1ndo? S\\u00e1bado 6\\r\\n\\u23f0 12:00 H\\r\\n\\ud83c\\udfad Calificaci\\u00f3n: 1\\u00ba infancias \\r\\n\\u231b\\ufe0f Duraci\\u00f3n: 45 min aprox.\\r\\n\\r\\n\\ud83c\\udfab Valor entrada\\r\\nGeneral: $4.500\\r\\n2\\u00ba infancia, estudiantes y 3\\u00aa edad: $3.500\\r\\n1\\u00ba infancias: $2.000\",\"tipo\":\"temporal\",\"fecha_inicio\":\"2026-06-06\",\"fecha_fin\":\"2026-06-06\"}', 'exposiciones/rjeMAtzJpWxRqliIFQPi2K8RQLyvKsFxPVj2yr7s.png', 1, 1, 0, NULL, '2026-06-04 17:49:38', '2026-06-04 17:49:38'),
(9, 121, 'eventos', '{\"titulo\":\"VALPARAISO LUCHA LIBRE: QUEBRADAS CAPITULO 1\",\"descripcion\":\"VALPARA\\u00cdSO LUCHA LIBRE PRESENTA: QUEBRADAS \\u2013 CAP\\u00cdTULO 1\\r\\n\\r\\nLos cerros guardan historias.\\r\\n\\r\\nAlgunas fueron olvidadas. Otras jam\\u00e1s debieron ser recordadas.\\r\\n\\r\\nDespu\\u00e9s de Or\\u00edgenes: Hora de la Verdad, las consecuencias comienzan a sentirse en cada rinc\\u00f3n de Valpara\\u00edso. Viejas rivalidades resurgen, nuevas heridas se abren y quienes cre\\u00edan haber escapado de su pasado descubrir\\u00e1n que las quebradas siempre encuentran la forma de devolverlos al lugar donde todo comenz\\u00f3.\\r\\n\\r\\nCampeonatos en juego, cuentas pendientes y el inicio de una nueva etapa marcar\\u00e1n el primer cap\\u00edtulo de esta historia.\\r\\n\\r\\nPorque en Valpara\\u00edso, las quebradas no separan los cerros.\\r\\n\\r\\nSeparan a los que est\\u00e1n dispuestos a seguir adelante de aquellos que quedaron atrapados en sus propios fantasmas.\\r\\n\\r\\nS\\u00e1bado 25 de Julio\\r\\nPolideportivo Tranque Seco \\u2013 Valpara\\u00edso\\r\\n15:00 hrs\",\"tipo\":\"otro\",\"hora\":\"15:00\",\"hora_fin\":\"17:30\",\"precio\":null,\"precio_texto\":\"Aporte Voluntario\",\"url_entradas\":\"https:\\/\\/www.portaldisc.com\\/evento\\/quebradas1\"}', 'eventos/rr1YX5sAxaQXsZasBxJGDK1hPyPw9wuO2rPePfER.jpg', 1, 0, 0, '2026-07-27', '2026-06-07 23:53:50', '2026-06-07 23:53:50'),
(10, 64, 'eventos', '{\"titulo\":\"Conversatorio - Volver al Futuro\",\"descripcion\":\"Conversatorio en torno a una pelicula que se hizo referencia para cualquier obra sobre viajes en el tiempo-\",\"tipo\":\"conferencia\",\"hora\":\"21:00\",\"hora_fin\":\"22:00\",\"precio\":\"5000\",\"precio_texto\":null,\"url_entradas\":null}', 'eventos/JD4bWykxpwvzeGtNxsx5PuTnMwqmpfMzNQU7CzJV.jpg', 1, 0, 1, '2027-07-05', '2026-06-08 02:09:15', '2026-06-08 02:09:15'),
(11, 64, 'eventos', '{\"titulo\":\"D\\u00eda mundial de la Cerveza\",\"descripcion\":\"Celebraremos el d\\u00eda internacional de la cerveza con catas, confecci\\u00f3n de cervezas de distintas culturas y concursos.\",\"tipo\":\"otro\",\"hora\":\"20:00\",\"hora_fin\":\"22:00\",\"precio\":\"25000\",\"precio_texto\":null,\"url_entradas\":null}', 'eventos/Ll2saHJI2gA3fbQ8NyXKXxjEMIR7l6vpbUI0mtBE.webp', 1, 0, 1, '2027-07-03', '2026-06-08 02:11:58', '2026-06-08 02:11:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `punto_productos`
--

CREATE TABLE `punto_productos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `punto_interes_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `precio` varchar(40) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `punto_productos`
--

INSERT INTO `punto_productos` (`id`, `punto_interes_id`, `nombre`, `precio`, `descripcion`, `imagen`, `orden`, `created_at`, `updated_at`) VALUES
(1, 115, 'Collage', '25.000', 'Collage original', 'productos/YoXKDIuQyk7CSIW6wHDG6utNJdxT4JcQia5tuzwA.jpg', 1, '2026-05-26 14:44:42', '2026-05-26 14:44:42'),
(2, 115, 'Bitácora de Viaje \"Puerto Textil\"', '14.990', 'Su portada está encuadernada a mano con telas tejidas tradicionales de vibrantes colores, complementada con un cierre de cuero legítimo y un bolígrafo de madera tallada.', 'productos/tSMxlTVwHzwvMy5ChgE61hJE4JA1fJD48Pgeq4Au.jpg', 2, '2026-05-26 14:47:36', '2026-05-26 14:49:39'),
(3, 115, 'Réplica  trolebuses', '8.500', 'Réplica artesanal a escala de los icónicos trolebuses que recorren el plan de Valparaíso. Fabricado y pintado completamente a mano sobre madera recuperada. Un souvenir nostálgico, perfecto para coleccionistas y amantes del patrimonio de la ciudad.', 'productos/gZKfTxClZSPYdLmUxE9QDPk44G2fJAPkDt11Q95V.jpg', 3, '2026-05-26 14:58:47', '2026-05-26 14:58:47'),
(4, 115, 'Cerros de Valparaíso', '25.000', 'Una obra de arte de collage mixto cuadrado, de formato mediano, enmarcada en un marco de madera claro rústico.', 'productos/VdEOkOCkQw9mfIfpSrhqsipZIKHt8TRe5yxjjOpJ.jpg', 4, '2026-05-26 15:03:00', '2026-05-26 15:03:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'cliente',
  `email` varchar(255) NOT NULL,
  `imagen_logo` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `type`, `email`, `imagen_logo`, `google_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'cesar.eav@gmail.com', NULL, NULL, '2026-05-03 23:30:59', '$2y$12$XpNKoASKmmwpEP.IDSQ3FOpnUDr6sa02sX9vXR.5EUGB9S/Q285ti', NULL, '2026-04-10 04:05:05', '2026-04-10 04:05:05'),
(2, 'El Pionero', 'cliente', 'elpionero@gmail.com', NULL, NULL, '2026-05-03 23:30:59', '$2y$12$oYRHnkYj4F4DEzjauI2Hh.XIxcfBGrUFEH40eSilDa7zSBb.XtR5m', NULL, '2026-04-10 18:13:15', '2026-04-10 18:13:15'),
(3, 'Hostal 1866', 'cliente', 'hostal1866@gmail.com', NULL, NULL, '2026-05-03 23:30:59', '$2y$12$EqMXSp27zDZvuF3E/nfq8OQr1W93xjEMr.jPdc/J8e09GGhGamIU.', NULL, '2026-04-14 03:17:40', '2026-04-14 03:17:40'),
(4, 'Rey Lagarto', 'cliente', 'reylagarto@gmail.com', NULL, NULL, '2026-05-03 23:30:59', '$2y$12$q8Y.N0M8GuaZuwZd8z3iIOVzS2qFFDIYkG.VDPSPIILTSdYFOXnEW', NULL, '2026-04-14 06:30:41', '2026-05-11 00:03:22'),
(5, 'Café Mirador Bellavista', 'cliente', 'cafemirador@gmail.com', NULL, NULL, '2026-05-03 23:30:59', '$2y$12$gzSTRfdMun3oe6zioTrdQOy.UnR7KDLVzQWj.OS57DdvRgZuFnlGK', NULL, '2026-04-21 18:40:30', '2026-04-21 18:40:30'),
(6, 'Daniela Cabrera', 'admin', 'danielapazcabrera89@gmail.com', NULL, NULL, '2026-05-03 23:30:59', '$2y$12$uS2iRl6E.2pqo3569qFxROIScUoTTyteQjl5l1E7DnB1ag2Km2o6i', 'BBrypE3OT3Fp8NN98QX73kMYVlkf5CWQsiei9PTr8HEUzb9XrCHPmUmBp0id', '2026-05-01 23:29:25', '2026-05-01 23:29:25'),
(29, 'Eta Karinae', 'artista', 'nebulosadelpuerto@gmail.com', NULL, NULL, '2026-05-20 21:38:06', '$2y$12$EXJtO.fw4hJmI37GKyrII.NUIVLXNqLQTWUHmKX2zetgus3HFbFWG', NULL, '2026-05-20 21:37:10', '2026-05-20 21:38:06'),
(32, 'Hormazábal Morales', 'cliente', 'enrikehor@hotmail.com', NULL, NULL, NULL, '$2y$12$3fSkIW7kVnmGwvx3tA0JkOB5khuE5Z2mq010MUbR.mR6mTdpzVOrO', NULL, '2026-05-24 17:03:04', '2026-05-24 17:03:04'),
(33, 'César Andrade Valdebenito', 'cliente', 'soporte@pindoor.cl', 'logos/tanj9Jo7JPd84grnjliCnaQY4XUs6IL42CYHWsPt.png', NULL, '2026-05-24 17:36:59', '$2y$12$MzVLtxbeRclcePaOtsJeS.UTk1izFoKR9Qt6NExlyoEgx7CG65J46', NULL, '2026-05-24 17:35:49', '2026-05-24 17:36:59'),
(34, 'Jane Cabrera', 'cliente', 'jane_valpo@gmail.com', 'logos/57tgv03zz78iMZKlBxiC8PwQk24fWlBIFhR15trc.jpg', NULL, '2026-05-24 17:36:59', '$2y$12$HSGS/Td2e53RQdniwa25xOAhEHZNkTTmG33GjbhWN6/EeFYWJ1Tcu', NULL, '2026-05-26 14:24:54', '2026-05-26 14:24:54'),
(35, 'La Morada ALegre', 'cliente', 'lamoradaalegre@gmail.com', 'logos/3hImLdm7zW6VbZSg6w1TSbU81pkvPcphXHKy7t6r.jpg', NULL, '2026-05-27 16:33:47', '$2y$12$zQdFhBCw.hLKlktA.q8pceBgOX0zC3pKB2TMJ06JnO4lqngEeDwqm', NULL, '2026-05-27 16:33:32', '2026-05-27 16:33:47'),
(37, 'Valparaíso Lucha Libre', 'cliente', 'hbcontreras95@gmail.com', 'logos/KzdvGNbAWjJCccdSuTJkqLdi8gPZlQckYnvwOaO1.png', NULL, '2026-05-29 19:29:52', '$2y$12$1L5WFoQ6WhJKxU35mIdRFe6/jUSgE2aiPy98ha7twb70dDEaRFA3i', 'dPZkiA8KlzrOsXsiKNEi9mByGPxAjRPalWqby1mXXhKIAWETUkriJJ4clLDa', '2026-05-29 19:29:35', '2026-05-29 19:29:52'),
(38, 'Teatromuseo del Títere y el Payaso', 'cliente', 'difusion@teatromuseo.cl', 'logos/tVYn1uqgyVfp4d9fvKFIVcZ7n2P23V3BQtpOXwWX.png', NULL, '2026-06-04 17:17:32', '$2y$12$5MWr4ziE6g.fkG22U2XKxO4s51xcPwn/3dGOeVM9cWkO3KDhC4Tie', 'ma5CKMmpzq5lvgaBCcOsibeaFkzExgTkkhmusqLeW5VOZJYainX3FlQJ0fak', '2026-06-04 17:16:27', '2026-06-04 17:17:32'),
(39, 'Yasmin fierro', 'cliente', 'yashidalgo1982@gmail.com', 'logos/5RKpiR7sqBs2SQINgphwBisBiZQ6FHdgeDOgWmdJ.jpg', NULL, '2026-06-09 17:17:32', '$2y$12$uSx8pgUAvoRn36yYbCzPJeIkPiC87A7fNulV8agsCQA9v9qleAiVG', NULL, '2026-06-09 18:10:53', '2026-06-09 18:10:53'),
(42, 'Naruto', 'cliente', 'naruto@naruto.com', 'logos/c93b6b3b-a315-4f40-9da1-527cf7f2e828.webp', NULL, NULL, '$2y$12$w4V1ADND/Uim.FzoOzFBCe0GTVFSqBEZpqWzBXkFeB3wplSBm7xni', NULL, '2026-06-09 22:04:06', '2026-06-09 22:04:06'),
(43, 'Casa', 'cliente', 'casa@casa.com', 'logos/261d2d0d-b8d4-485b-ac48-1424aa5137b9.webp', NULL, '2026-06-09 17:17:32', '$2y$12$q9r1/x.iuDiokHQXZHCUy.UXusc4WyU4GuHgtAHjsvn7hyF/mTRaW', NULL, '2026-06-09 22:18:31', '2026-06-09 22:18:31'),
(44, 'Sergio ivan Huaman Ñope', 'cliente', 'gtxsantana@gmail.com', 'logos/fa2a3925-0730-4c73-b9c7-37e599583238.webp', NULL, '2026-06-11 15:35:06', '$2y$12$eEEwEchKo/XNqfybKqseE.Wr9KjNkM/9AeQiaolhtt0UmhSN3WZ1m', 'xVzEyPdEx77iOke8OPoxW0NOP2I5kibWVl1J52B6LggtEaTHAjRO6QhzR248', '2026-06-11 15:34:44', '2026-06-11 15:35:06'),
(45, 'janekoolach', 'cliente', 'ohjanebags@gmail.com', 'logos/a3c1bfdc-65b1-45ab-8328-d181e1e1f837.webp', NULL, '2026-06-12 01:52:11', '$2y$12$9xvgD8bH3SvmpB1zfcE1IO2ywdg7gLamjJr5ogQ.ilxWXXi1.jpqa', NULL, '2026-06-12 01:51:29', '2026-06-12 01:52:11');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `artistas`
--
ALTER TABLE `artistas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `artistas_slug_unique` (`slug`),
  ADD KEY `artistas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `artista_imagenes`
--
ALTER TABLE `artista_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artista_imagenes_artista_id_foreign` (`artista_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categorias_slug_unique` (`slug`);

--
-- Indices de la tabla `configuraciones`
--
ALTER TABLE `configuraciones`
  ADD PRIMARY KEY (`clave`);

--
-- Indices de la tabla `experiencias`
--
ALTER TABLE `experiencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `experiencia_imagenes`
--
ALTER TABLE `experiencia_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `experiencia_imagenes_experiencia_id_foreign` (`experiencia_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `imagenes_punto`
--
ALTER TABLE `imagenes_punto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imagenes_punto_punto_interes_id_foreign` (`punto_interes_id`);

--
-- Indices de la tabla `leads_contacto`
--
ALTER TABLE `leads_contacto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `leads_publicita`
--
ALTER TABLE `leads_publicita`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `panoramas`
--
ALTER TABLE `panoramas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `panoramas_slug_unique` (`slug`);

--
-- Indices de la tabla `panorama_imagenes`
--
ALTER TABLE `panorama_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `panorama_imagenes_panorama_id_foreign` (`panorama_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `posts_slug_unique` (`slug`);

--
-- Indices de la tabla `puntosinteres`
--
ALTER TABLE `puntosinteres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `puntosinteres_slug_unique` (`slug`),
  ADD KEY `puntosinteres_user_id_foreign` (`user_id`),
  ADD KEY `puntosinteres_categoria_id_foreign` (`categoria_id`);

--
-- Indices de la tabla `punto_modulo_datos`
--
ALTER TABLE `punto_modulo_datos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `punto_modulo_datos_punto_interes_id_modulo_unique` (`punto_interes_id`,`modulo`);

--
-- Indices de la tabla `punto_modulo_items`
--
ALTER TABLE `punto_modulo_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `punto_modulo_items_punto_interes_id_modulo_index` (`punto_interes_id`,`modulo`);

--
-- Indices de la tabla `punto_productos`
--
ALTER TABLE `punto_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `punto_productos_punto_interes_id_foreign` (`punto_interes_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `artistas`
--
ALTER TABLE `artistas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `artista_imagenes`
--
ALTER TABLE `artista_imagenes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `experiencias`
--
ALTER TABLE `experiencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `experiencia_imagenes`
--
ALTER TABLE `experiencia_imagenes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `imagenes_punto`
--
ALTER TABLE `imagenes_punto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=517;

--
-- AUTO_INCREMENT de la tabla `leads_contacto`
--
ALTER TABLE `leads_contacto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `leads_publicita`
--
ALTER TABLE `leads_publicita`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `panoramas`
--
ALTER TABLE `panoramas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT de la tabla `panorama_imagenes`
--
ALTER TABLE `panorama_imagenes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `puntosinteres`
--
ALTER TABLE `puntosinteres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT de la tabla `punto_modulo_datos`
--
ALTER TABLE `punto_modulo_datos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `punto_modulo_items`
--
ALTER TABLE `punto_modulo_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `punto_productos`
--
ALTER TABLE `punto_productos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `artistas`
--
ALTER TABLE `artistas`
  ADD CONSTRAINT `artistas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `artista_imagenes`
--
ALTER TABLE `artista_imagenes`
  ADD CONSTRAINT `artista_imagenes_artista_id_foreign` FOREIGN KEY (`artista_id`) REFERENCES `artistas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `experiencia_imagenes`
--
ALTER TABLE `experiencia_imagenes`
  ADD CONSTRAINT `experiencia_imagenes_experiencia_id_foreign` FOREIGN KEY (`experiencia_id`) REFERENCES `experiencias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `imagenes_punto`
--
ALTER TABLE `imagenes_punto`
  ADD CONSTRAINT `imagenes_punto_punto_interes_id_foreign` FOREIGN KEY (`punto_interes_id`) REFERENCES `puntosinteres` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `panorama_imagenes`
--
ALTER TABLE `panorama_imagenes`
  ADD CONSTRAINT `panorama_imagenes_panorama_id_foreign` FOREIGN KEY (`panorama_id`) REFERENCES `panoramas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puntosinteres`
--
ALTER TABLE `puntosinteres`
  ADD CONSTRAINT `puntosinteres_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `puntosinteres_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `punto_modulo_datos`
--
ALTER TABLE `punto_modulo_datos`
  ADD CONSTRAINT `punto_modulo_datos_punto_interes_id_foreign` FOREIGN KEY (`punto_interes_id`) REFERENCES `puntosinteres` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `punto_modulo_items`
--
ALTER TABLE `punto_modulo_items`
  ADD CONSTRAINT `punto_modulo_items_punto_interes_id_foreign` FOREIGN KEY (`punto_interes_id`) REFERENCES `puntosinteres` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `punto_productos`
--
ALTER TABLE `punto_productos`
  ADD CONSTRAINT `punto_productos_punto_interes_id_foreign` FOREIGN KEY (`punto_interes_id`) REFERENCES `puntosinteres` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
