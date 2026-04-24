-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2026 a las 03:06:29
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
-- Base de datos: `universidadcni`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos_leccion`
--

CREATE TABLE `archivos_leccion` (
  `id` int(11) NOT NULL,
  `leccion_id` int(11) NOT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `archivos_leccion`
--

INSERT INTO `archivos_leccion` (`id`, `leccion_id`, `nombre_original`, `ruta_archivo`) VALUES
(1, 1, '9788418648755_paginas.pdf', 'Assets/Pdfs/1776821075_0_9788418648755_paginas.pdf'),
(2, 2, '7.-_Introducci_n_al_almacenamiento_de_datos.pdf', 'Assets/Pdfs/1776821161_0_7.-_Introducci_n_al_almacenamiento_de_datos.pdf'),
(3, 3, 'images.jpg', 'Assets/Pdfs/1776821237_0_images.jpg'),
(4, 4, 'images (1).jpg', 'Assets/Pdfs/1776821356_0_images (1).jpg'),
(5, 4, 'Data Vault 20 Arquitectura [classic].png', 'Assets/Pdfs/1776821356_1_Data Vault 20 Arquitectura [classic].png'),
(6, 5, 'metodologias-de-data-warehouse-02-1.webp', 'Assets/Pdfs/1776821455_0_metodologias-de-data-warehouse-02-1.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `leccion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id`, `leccion_id`, `usuario_id`, `comentario`, `fecha`) VALUES
(1, 1, 4, 'Buen día si hay alguna duda pueden preguntarme por medio de este foro de dudas para que sean aclaradas para todos los que tomen este curso, que tengan excelente día. saludos', '2026-04-22 01:36:54'),
(2, 1, 6, 'de momento no hay ninguna ', '2026-04-22 01:37:43'),
(3, 1, 6, 'Tengo una duda, ¿los KPIs siempre deben medirse en porcentajes o pueden ser valores absolutos dependiendo del modelo de negocio?', '2026-04-23 01:16:36'),
(4, 5, 7, 'Me costó un poco entender la diferencia entre un Hub y un Link al principio, pero con el diagrama de este video me quedó clarísimo. ¡Gracias!', '2026-04-23 01:16:36'),
(5, 11, 11, '¿Alguien conoce alguna página o repositorio donde podamos descargar bases de datos de prueba para seguir practicando las fórmulas DAX?', '2026-04-23 01:16:36'),
(6, 16, 12, 'Excelente explicación de las CTEs. Justo hoy apliqué esto para optimizar y limpiar una consulta muy pesada que tenía en el trabajo.', '2026-04-23 01:16:36'),
(7, 1, 4, 'No, los KPIs no siempre deben medirse en porcentajes. De hecho, pueden ser valores absolutos (números enteros), porcentajes, promedios, ratios (razones) o incluso valores monetarios, y la elección depende totalmente del objetivo que estés midiendo y del modelo de negocio.', '2026-04-23 01:18:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `imagen` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `titulo`, `descripcion`, `instructor_id`, `fecha_creacion`, `imagen`) VALUES
(1, 'Metodologías para la Inteligencia de Negocios', 'Curso fundamental para comprender el enfoque Data-Driven. Aprenderás a definir KPIs estratégicos, diseñar almacenes de datos y aplicar el Esquema Estrella de Kimball para el análisis de rendimiento.', 4, '2026-04-22 01:21:07', 'Assets/Img/curso_1776820867.png'),
(2, 'Arquitectura de Datos Avanzada: Data Vault 2.0', 'Aprende a diseñar arquitecturas escalables y ágiles. Este curso abarca desde la teoría de Data Vault hasta la implementación práctica de tablas Hub, Link y Satellite con scripts SQL.', 5, '2026-04-22 01:22:38', 'Assets/Img/curso_1776820958.png'),
(3, 'Fundamentos de Data Warehouse', 'Aprende los conceptos básicos para diseñar y construir almacenes de datos corporativos', 9, '2026-04-23 01:01:22', 'Assets/Img/curso_1776906082.jpg'),
(4, 'Power BI Avanzado y DAX', 'Domina las expresiones de análisis de datos (DAX) y crea dashboards interactivos.', 9, '2026-04-23 01:02:08', 'Assets/Img/curso_1776906128.avif'),
(5, 'Machine Learning para Negocios', 'Implementación de modelos predictivos usando Python y Scikit-Learn', 9, '2026-04-23 01:02:48', 'Assets/Img/curso_1776906168.jpg'),
(6, 'SQL Complejo para Análisis de Datos', 'Uso de Window Functions, CTEs y optimización de consultas para bases de datos masivas.', 10, '2026-04-23 01:04:28', 'Assets/Img/curso_1776906268.png'),
(7, 'Metodología Top-Down de Inmon', 'Diseño de la Fábrica de Información Corporativa (CIF) y modelo normalizado.', 10, '2026-04-23 01:05:12', 'Assets/Img/curso_1776906312.jpg'),
(8, 'Gobernanza y Calidad de Datos', 'Políticas, seguridad y Master Data Management (MDM) en entornos empresariales.', 10, '2026-04-23 01:05:54', 'Assets/Img/curso_1776906354.png'),
(9, 'Introducción a PHP 8 y MySQL', 'Desarrollo web backend con arquitectura MVC.', 5, '2026-04-23 01:06:55', 'Assets/Img/curso_1776906415.png'),
(10, 'Diseño de Interfaces (UI/UX)', 'Mejores prácticas para crear sistemas web intuitivos y accesibles.', 5, '2026-04-23 01:07:41', 'Assets/Img/curso_1776906461.jpg'),
(11, 'Modelo Dimensional de Kimball', 'Profundización en el esquema de estrella, tablas de hechos y dimensiones.', 4, '2026-04-23 01:08:41', 'Assets/Img/curso_1776906521.jpg'),
(12, 'Procesos ETL con Integration Services', 'Extracción, transformación y carga de datos usando herramientas de Microsoft.', 4, '2026-04-23 01:09:25', 'Assets/Img/curso_1776906565.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lecciones`
--

CREATE TABLE `lecciones` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `titulo` varchar(150) NOT NULL,
  `contenido_url` varchar(255) DEFAULT NULL,
  `pdf_ruta` varchar(255) DEFAULT NULL,
  `tiene_tarea` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lecciones`
--

INSERT INTO `lecciones` (`id`, `curso_id`, `titulo`, `contenido_url`, `pdf_ruta`, `tiene_tarea`) VALUES
(1, 1, '1.1 Enfoque Data-Driven y Definición de KPIs', '830NHbvbo5g', NULL, 1),
(2, 1, '1.2 Fundamentos del Almacén de Datos', '6S8A-1jBD5Y', NULL, 1),
(3, 1, '1.3 Diseño con Esquema Estrella (Metodología Kimball)', 'FcNCxP5tUao', NULL, 1),
(4, 2, '1.1 Introducción a la Arquitectura Data Vault 2.0', 'mIFlKgOWZt8', NULL, 1),
(5, 2, '1.2 Modelado de Estructuras: Hubs, Links y Satellites\'', 'D914nNWGP6E', NULL, 1),
(6, 2, '1.3 Caso de Estudio Práctico: Área de Recursos Humanos', 'w5o_zW8Jk7Q', NULL, 1),
(7, 3, '1. Introducción y Conceptos Básicos', 'https://www.youtube.com/watch?v=jFsRdTcljeU', NULL, 0),
(8, 3, '2. Arquitecturas y Componentes', 'https://www.youtube.com/watch?v=xKIkB50Tmsc', NULL, 1),
(9, 3, '3. El proceso de Extracción de Datos', 'https://www.youtube.com/watch?v=m_Gb_yt4lgg', NULL, 1),
(10, 4, '1. Conexión a múltiples orígenes de datos', 'https://www.youtube.com/watch?v=o90oKMMnUYQ', NULL, 0),
(11, 4, '2. Introducción a funciones DAX', 'https://www.youtube.com/watch?v=gQtnWQ4M3wo&list=PLEy6Omomtm3FmNGdnexKHggkSR156z4CQ', NULL, 1),
(12, 4, '3. Publicación de Dashboards', 'https://www.youtube.com/watch?v=0q3UhU0A3Ew', NULL, 1),
(13, 5, '1. Preparación del Dataset', 'https://www.youtube.com/watch?v=CFPQGpzJJdQ', NULL, 1),
(14, 5, '2. Regresión Lineal y Logística', 'https://www.youtube.com/watch?v=mjbWGx6Xgvg', NULL, 1),
(15, 5, '3. Evaluación del Modelo', 'https://www.youtube.com/watch?v=P_CcLKrcop0', NULL, 0),
(16, 6, '1. Common Table Expressions (CTEs)', 'https://www.youtube.com/watch?v=K1WeoKxLZ5o', NULL, 1),
(17, 6, '2. Funciones de Ventana (Window Functions)', 'https://www.youtube.com/watch?v=rIcB4zMYMas', NULL, 1),
(18, 6, '3. Optimización e Índices', 'https://www.youtube.com/watch?v=h2aMBZbQYas', NULL, 0),
(19, 7, '1. Diferencias entre Inmon y Kimball', 'https://www.youtube.com/watch?v=Tff34jj_V-0', NULL, 0),
(20, 7, '2. La Fábrica de Información Corporativa', 'https://www.youtube.com/watch?v=hj--QDzf86g', NULL, 1),
(21, 7, '3. Modelado 3NF para Data Warehouses', 'https://www.youtube.com/watch?v=Tff34jj_V-0', NULL, 1),
(22, 8, '1. ¿Qué es el Data Governance?', 'https://www.youtube.com/watch?v=kpbK33J_h4w', NULL, 0),
(23, 8, '2. Catálogos de Datos y Diccionarios', 'https://www.youtube.com/watch?v=kt1w9xwfaP4', NULL, 1),
(24, 8, '3. Seguridad y Privacidad de la Información', 'https://www.youtube.com/watch?v=iPptxuQO3WA', NULL, 0),
(25, 9, '1. Sintaxis Básica y Variables', 'https://www.youtube.com/watch?v=lWOXMi_fyyM', NULL, 0),
(26, 9, '2. Programación Orientada a Objetos en PHP', 'https://www.youtube.com/watch?v=UyNZxmrouso&list=PLH_tVOsiVGzm0PGn_HEZbgm_ugEgV7LKV', NULL, 1),
(27, 9, '3. Conexión segura a BD (PDO y MySQLi)', 'https://www.youtube.com/watch?v=zwgYNKLKCJ8', NULL, 1),
(28, 10, '1. Teoría del Color y Tipografía', 'https://www.youtube.com/watch?v=M-MUn0Zi3yA', NULL, 0),
(29, 10, '2. Wireframes y Prototipos', 'https://www.youtube.com/watch?v=iyrEStiTZh0', NULL, 1),
(30, 10, '3. Accesibilidad Web y UX', 'https://www.youtube.com/watch?v=HkZIk36JEkE', NULL, 1),
(31, 11, '1. El ciclo de vida dimensional', 'https://www.youtube.com/watch?v=kTzEOxoft_s', NULL, 0),
(32, 11, '2. Tablas de Hechos y Granularidad', 'https://www.youtube.com/watch?v=um41JAqO42g&list=PLSvxAUzJ-XSfY0KpwV8SHBlyLVcrZkENc', NULL, 1),
(33, 11, '3. Dimensiones Lentamente Cambiantes (SCD)', 'https://www.youtube.com/watch?v=knVwokXITGI', NULL, 1),
(34, 12, '1. Introducción a SSIS', 'https://www.youtube.com/watch?v=9htT_hMcCEc', NULL, 0),
(35, 12, '2. Tareas de Flujo de Datos', 'https://www.youtube.com/watch?v=rBdk2yhse48&list=PLfWSKMW94oTaqcB7Rfs4Z4m8gp_dmIHEi', NULL, 1),
(36, 12, '3. Manejo de Errores y Logs', 'https://www.youtube.com/watch?v=j_tpNOhq2F4&t=27s', NULL, 1),
(37, 13, '1. Conceptos Fundamentales de DV 2.0', 'eG3mPTTuOXs', NULL, 0),
(38, 13, '2. Creación de Hubs y Links', 'eG3mPTTuOXs', NULL, 1),
(39, 13, '3. Creación de Satellites e Historial', 'eG3mPTTuOXs', NULL, 1),
(40, 14, '1. El Teorema CAP', 'eG3mPTTuOXs', NULL, 0),
(41, 14, '2. Documentos vs Grafos vs Clave-Valor', 'eG3mPTTuOXs', NULL, 1),
(42, 14, '3. Operaciones CRUD en MongoDB', 'eG3mPTTuOXs', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `leccion_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `mensaje`, `leccion_id`, `curso_id`, `leida`, `fecha`) VALUES
(1, 4, 'El alumno(a) <b>Carmen Ruiz </b> hizo una nueva pregunta en el foro.', 1, 1, 1, '2026-04-22 01:37:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_lecciones`
--

CREATE TABLE `progreso_lecciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `leccion_id` int(11) DEFAULT NULL,
  `fecha_completado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `progreso_lecciones`
--

INSERT INTO `progreso_lecciones` (`id`, `usuario_id`, `leccion_id`, `fecha_completado`) VALUES
(1, 6, 1, '2026-04-22 01:33:24'),
(2, 6, 4, '2026-04-23 01:20:48'),
(3, 6, 5, '2026-04-23 01:20:50'),
(4, 6, 34, '2026-04-23 01:20:57'),
(5, 6, 35, '2026-04-23 01:20:59'),
(6, 6, 31, '2026-04-23 01:21:08'),
(7, 6, 33, '2026-04-23 01:21:11'),
(8, 6, 29, '2026-04-23 01:22:56'),
(9, 6, 30, '2026-04-23 01:22:59'),
(10, 6, 28, '2026-04-23 01:23:01'),
(11, 6, 26, '2026-04-23 01:23:06'),
(12, 6, 27, '2026-04-23 01:23:08'),
(13, 6, 25, '2026-04-23 01:23:11'),
(14, 7, 31, '2026-04-23 01:23:39'),
(15, 7, 33, '2026-04-23 01:23:41'),
(16, 7, 32, '2026-04-23 01:23:43'),
(17, 7, 34, '2026-04-23 01:23:50'),
(18, 7, 36, '2026-04-23 01:23:52'),
(19, 7, 28, '2026-04-23 01:23:57'),
(20, 7, 30, '2026-04-23 01:23:59'),
(21, 7, 29, '2026-04-23 01:24:01'),
(22, 7, 26, '2026-04-23 01:24:06'),
(23, 7, 27, '2026-04-23 01:24:08'),
(24, 7, 25, '2026-04-23 01:24:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas_entregadas`
--

CREATE TABLE `tareas_entregadas` (
  `id` int(11) NOT NULL,
  `leccion_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `archivo_ruta` varchar(255) DEFAULT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('estudiante','instructor','admin') NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'Administrador', 'admin@uabc.mx', '123', 'admin', '2026-02-24 09:04:02'),
(2, 'Maestro Prueba', 'maestro@uabc.mx', '123', 'instructor', '2026-02-24 09:04:02'),
(3, 'Alumno Prueba', 'alumno@uabc.mx', '123', 'estudiante', '2026-02-24 09:04:02'),
(4, 'Carlos Ibarra Gomez', 'cibarra@uabc.mx', '123', 'instructor', '2026-04-22 00:38:35'),
(5, 'Alfonso Barrientos ', 'abarrientos@uabc.mx', '123', 'instructor', '2026-04-22 00:39:03'),
(6, 'Carmen Ruiz ', 'cruiz@uabc.mx', '123', 'estudiante', '2026-04-22 00:39:33'),
(7, 'Jorge Davila', 'jdavila@uabc.mx', '123', 'estudiante', '2026-04-22 00:39:58'),
(8, 'Sofia Castro', 'scastro@uabc.mx', '123', 'estudiante', '2026-04-22 00:40:16'),
(9, 'Roberto Salgado', 'rsalgado@uabc.mx', '123', 'instructor', '2026-04-23 00:55:02'),
(10, 'Lucía Mendoza', 'lmendoza@uabc.mx', '123', 'instructor', '2026-04-23 00:55:02'),
(11, 'Fernando López', 'flopez@uabc.mx', '123', 'estudiante', '2026-04-23 00:55:02'),
(12, 'Mariana Ríos', 'mrios@uabc.mx', '123', 'estudiante', '2026-04-23 00:55:02'),
(13, 'Daniela Soto', 'dsoto@uabc.mx', '123', 'estudiante', '2026-04-23 00:55:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivos_leccion`
--
ALTER TABLE `archivos_leccion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lecciones`
--
ALTER TABLE `lecciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `progreso_lecciones`
--
ALTER TABLE `progreso_lecciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tareas_entregadas`
--
ALTER TABLE `tareas_entregadas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivos_leccion`
--
ALTER TABLE `archivos_leccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `lecciones`
--
ALTER TABLE `lecciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `progreso_lecciones`
--
ALTER TABLE `progreso_lecciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `tareas_entregadas`
--
ALTER TABLE `tareas_entregadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
