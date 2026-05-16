-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         11.4.10-MariaDB-log - MariaDB Server
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para startraining


-- Volcando estructura para tabla startraining.administradores
CREATE TABLE IF NOT EXISTS `administradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `foto_perfil` text DEFAULT NULL,
  `creado_por` varchar(100) DEFAULT NULL,
  `actualizado_por` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla startraining.administradores: ~0 rows (aproximadamente)
INSERT INTO `administradores` (`id`, `usuario`, `password_hash`, `nombre`, `foto_perfil`, `creado_por`, `actualizado_por`, `creado_en`, `actualizado_en`) VALUES
	(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Global', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778430446/startraining/admins/admin_1_1778430460.jpg', NULL, NULL, '2026-05-07 11:16:23', '2026-05-10 16:27:42');

-- Volcando estructura para tabla startraining.carreras
CREATE TABLE IF NOT EXISTS `carreras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `creado_por` varchar(100) DEFAULT NULL,
  `actualizado_por` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla startraining.carreras: ~13 rows (aproximadamente)
INSERT INTO `carreras` (`id`, `nombre`, `creado_por`, `actualizado_por`, `creado_en`, `actualizado_en`) VALUES
	(1, 'Ingeniería de Sistemas', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(2, 'Ingeniería Industrial', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(3, 'Ingeniería Civil', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(4, 'Ingeniería de Minas', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(5, 'Arquitectura y Urbanismo', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(6, 'Administración de Empresas', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(7, 'Marketing Digital', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(8, 'Contabilidad y Finanzas', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(9, 'Derecho Corporativo', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(10, 'Psicología Organizacional', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(11, 'Ciencias de la Comunicación', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(12, 'Economía y Negocios', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	(14, 'enfermeria', NULL, NULL, '2026-05-10 16:39:55', '2026-05-10 16:39:55');

-- Volcando estructura para tabla startraining.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `clave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL,
  `creado_por` varchar(100) DEFAULT NULL,
  `actualizado_por` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla startraining.configuracion: ~13 rows (aproximadamente)
INSERT INTO `configuracion` (`clave`, `valor`, `creado_por`, `actualizado_por`, `creado_en`, `actualizado_en`) VALUES
	('email_contacto', 'contacto@startraining.com', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	('facebook_url', '#', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	('footer_descripcion', 'Plataforma líder en reclutamiento de talento profesional.', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	('instagram_url', '#', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	('linkedin_url', '#', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	('logo_sitio', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778628617/startraining/branding/main_logo.jpg', NULL, NULL, '2026-05-07 11:16:22', '2026-05-12 23:30:23'),
	('mantenimiento_msg', 'Estamos mejorando la experiencia Luxe.', NULL, NULL, '2026-05-10 16:40:10', '2026-05-10 16:40:10'),
	('mision_sitio', '', NULL, NULL, '2026-05-10 16:40:10', '2026-05-10 16:40:10'),
	('modo_mantenimiento', 'off', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	('nombre_sitio', 'StarTraining', NULL, NULL, '2026-05-07 11:16:22', '2026-05-10 16:40:10'),
	('telefono_contacto', '+51 987 654 321', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	('terminos_condiciones', 'Acepto los términos y condiciones de uso de la plataforma StarTraining.', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22'),
	('twitter_url', '#', NULL, NULL, '2026-05-07 11:16:22', '2026-05-07 11:16:22');

-- Volcando estructura para tabla startraining.empresas
CREATE TABLE IF NOT EXISTS `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_comercial` varchar(150) NOT NULL,
  `ruc` varchar(11) NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `correo_contacto` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `foto_perfil` text DEFAULT NULL,
  `ficha_ruc` text DEFAULT NULL,
  `dni_frente` text DEFAULT NULL,
  `dni_reverso` text DEFAULT NULL,
  `foto_selfie` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `es_top` tinyint(1) DEFAULT 0,
  `creado_por` varchar(100) DEFAULT NULL,
  `actualizado_por` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ruc` (`ruc`),
  UNIQUE KEY `correo_contacto` (`correo_contacto`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla startraining.empresas: ~0 rows (aproximadamente)
INSERT INTO `empresas` (`id`, `nombre_comercial`, `ruc`, `sector`, `correo_contacto`, `telefono`, `direccion`, `password_hash`, `foto_perfil`, `ficha_ruc`, `dni_frente`, `dni_reverso`, `foto_selfie`, `estado`, `es_top`, `creado_por`, `actualizado_por`, `creado_en`, `actualizado_en`) VALUES
	(5, 'COOPERATIVA DE AHORRO Y CRÉDITO "SAN CRISTÓBAL DE HUAMANGA" LTDA.', '20129175975', 'GENERAL', 'copa@gmail.com', '912345678', 'NRO. 33  PORTAL UNION, AYACUCHO - HUAMANGA - AYACUCHO', '$2y$10$MwkrjU/H4ycFhgd8VD6R6.f9D8IeuBtFq3G2Fs7dEnUEosNkSiHz2', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778444663/startraining/logos/logo_20129175975.jpg', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778444665/startraining/documentos/ficha_20129175975.pdf', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778444667/startraining/documentos/dni_f_20129175975.jpg', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778444668/startraining/documentos/dni_r_20129175975.png', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778444670/startraining/biometria/selfie_20129175975.jpg', 'activo', 0, NULL, NULL, '2026-05-10 20:24:31', '2026-05-10 20:25:24');

-- Volcando estructura para tabla startraining.postulaciones
CREATE TABLE IF NOT EXISTS `postulaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vacante_id` int(11) NOT NULL,
  `dni` varchar(15) NOT NULL,
  `nombre_completo` varchar(200) NOT NULL,
  `correo_estudiante` varchar(150) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `url_cv_pdf` text DEFAULT NULL,
  `match_porcentaje` decimal(5,2) DEFAULT 0.00,
  `ia_analisis_descripcion` text DEFAULT NULL,
  `estado_postulacion` varchar(20) DEFAULT 'en_espera',
  `notificacion_leida` tinyint(1) DEFAULT 0,
  `creado_por` varchar(100) DEFAULT NULL,
  `actualizado_por` varchar(100) DEFAULT NULL,
  `fecha_postulacion` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_postulacion_dni` (`vacante_id`,`dni`),
  CONSTRAINT `fk_vacante` FOREIGN KEY (`vacante_id`) REFERENCES `vacantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla startraining.postulaciones: ~3 rows (aproximadamente)
INSERT INTO `postulaciones` (`id`, `vacante_id`, `dni`, `nombre_completo`, `correo_estudiante`, `celular`, `url_cv_pdf`, `match_porcentaje`, `ia_analisis_descripcion`, `estado_postulacion`, `notificacion_leida`, `creado_por`, `actualizado_por`, `fecha_postulacion`, `actualizado_en`) VALUES
	(2, 3, '70140173', 'MIGUEL ANGEL NUÑEZ AYALA', '70140173@elp.edu.pe', '987656787', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778444980/startraining/cvs/cv_70140173_1778444978.pdf', 0.00, 'El candidato no cumple con ninguno de los requisitos técnicos solicitados. Existe una discrepancia total entre el perfil profesional del candidato (Enfermería) y el puesto requerido (Ingeniería de Sistemas/Desarrollo de Software).', 'No Apto', 0, NULL, NULL, '2026-05-10 20:29:40', '2026-05-10 20:30:31'),
	(3, 3, '70140167', 'MARICRUZ MEDINA CANCHARI', '70140167@elp.edu.pe', '987656768', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778445166/startraining/cvs/cv_70140167_1778445165.pdf', 95.00, 'El candidato cumple con todos los requisitos académicos, técnicos y de habilidades blandas. Su perfil está altamente alineado con el stack tecnológico solicitado y cuenta con experiencia práctica relevante en desarrollo de sistemas.', 'Apto', 0, NULL, NULL, '2026-05-10 20:32:46', '2026-05-10 20:33:29'),
	(4, 3, '70140166', 'NELSON SANCHEZ AYALA', '70140166@elp.edu.pe', '987654322', 'https://res.cloudinary.com/dbgigfmse/image/upload/v1778710439/startraining/cvs/cv_70140166_1778710443.pdf', 0.00, 'El perfil profesional del candidato corresponde al área de salud (Enfermería) y no presenta ninguna relación con los requisitos técnicos de Ingeniería de Sistemas o Desarrollo de Software solicitados.', 'IA Realizado', 0, NULL, NULL, '2026-05-13 22:14:05', '2026-05-13 22:29:08');

-- Volcando estructura para tabla startraining.vacantes
CREATE TABLE IF NOT EXISTS `vacantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) NOT NULL,
  `carrera_id` int(11) DEFAULT NULL,
  `titulo_puesto` varchar(150) NOT NULL,
  `descripcion_puesto` text NOT NULL,
  `requisitos_raw` text NOT NULL,
  `modalidad` varchar(50) DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `fecha_limite` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'abierta',
  `creado_por` varchar(100) DEFAULT NULL,
  `actualizado_por` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_empresa` (`empresa_id`),
  KEY `fk_carrera` (`carrera_id`),
  CONSTRAINT `fk_carrera` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla startraining.vacantes: ~1 rows (aproximadamente)
INSERT INTO `vacantes` (`id`, `empresa_id`, `carrera_id`, `titulo_puesto`, `descripcion_puesto`, `requisitos_raw`, `modalidad`, `ubicacion`, `fecha_limite`, `estado`, `creado_por`, `actualizado_por`, `creado_en`, `actualizado_en`) VALUES
	(3, 5, 1, 'practicas', 'El Ingeniero de Sistemas será responsable de analizar, diseñar, desarrollar e implementar soluciones tecnológicas que optimicen los procesos de la empresa. Deberá participar en el desarrollo de software, administración de bases de datos, mantenimiento de sistemas informáticos y soporte técnico a los usuarios. Además, colaborará con el equipo de tecnología para mejorar la seguridad, eficiencia y rendimiento de los sistemas.', '•	Título o bachiller en Ingeniería de Sistemas, Informática o carreras afines. \r\n•	Conocimiento en lenguajes de programación (Java, Python, C#, PHP o similares). \r\n•	Experiencia en desarrollo web (HTML, CSS, JavaScript). \r\n•	Manejo de bases de datos (MySQL, PostgreSQL o SQL Server). \r\n•	Conocimiento en control de versiones (Git). \r\n•	Capacidad de análisis y resolución de problemas. \r\n•	Trabajo en equipo y buena comunicación. \r\n•	Experiencia en desarrollo de sistemas o proyectos tecnológicos (deseable).\r\n', 'Presencial', 'ayacucho', '2026-05-12', 'abierta', NULL, NULL, '2026-05-10 20:28:06', '2026-05-10 20:28:06');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
