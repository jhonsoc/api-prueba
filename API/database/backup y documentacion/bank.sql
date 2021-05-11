-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2021 a las 19:51:01
-- Versión del servidor: 10.4.18-MariaDB
-- Versión de PHP: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bank`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `value` double NOT NULL,
  `type` tinyint(1) NOT NULL,
  `state` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `account`
--

INSERT INTO `account` (`id`, `value`, `type`, `state`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, 2784925, 0, 0, '2021-05-11 04:46:47', NULL, '2021-05-11 17:43:34'),
(2, 2380000, 0, 0, '2021-05-11 04:46:47', NULL, NULL),
(3, 2785950, 1, 2, '2021-05-11 04:46:47', NULL, '2021-05-11 17:43:34'),
(4, 320000, 1, 2, '2021-05-11 04:46:47', NULL, NULL),
(5, 4582000, 0, 3, '2021-05-11 04:46:47', NULL, NULL),
(6, 789520000, 0, 1, '2021-05-11 04:46:47', NULL, NULL),
(7, 3467810000, 1, 3, '2021-05-11 04:46:47', NULL, NULL),
(8, 13245444, 1, 2, '2021-05-11 04:46:47', NULL, NULL),
(9, 5640000, 0, 1, '2021-05-11 04:46:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `identification` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `identification`, `email`, `pass`, `created_at`, `deleted_at`, `updated_at`) VALUES
(1, 'camilo', 'leon', '1030000', 'pruebas@correos.com', '87da1a7a70cbaeeee7db6558fa2d53ab72eea96dfaad8c3b44e22cb77064e9b5', '2021-05-11 04:40:17', NULL, '2021-05-11 05:54:35'),
(2, 'leon', 'gutierrez', '517148422', 'leom@prueba.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2021-05-11 04:40:17', NULL, NULL),
(3, 'tatiana', 'vanegas', '132465120', 'tati@gmail.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2021-05-11 04:42:15', NULL, NULL),
(4, 'martha', 'molano', '12055451000', 'martha@gmail.com', '4464882dc89b7fa42117e929233f75359622031304413825e28aa992b1c5d08a', '2021-05-11 04:42:15', NULL, NULL),
(7, 'camilo2', 'leon2', '10306235581', 'prueba@correo.com', '$2y$10$6uzRPA3vFjsd4eJIPBrkJeeV6FrzFcjxOkkma1l0k.GHHRzKAVvYa', '2021-05-11 05:23:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_account`
--

CREATE TABLE `user_account` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_account` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `user_account`
--

INSERT INTO `user_account` (`id`, `id_user`, `id_account`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3),
(4, 3, 4),
(5, 3, 5),
(6, 3, 6),
(7, 3, 7),
(8, 4, 8),
(9, 4, 9);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identification` (`identification`);

--
-- Indices de la tabla `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `user_account`
--
ALTER TABLE `user_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
