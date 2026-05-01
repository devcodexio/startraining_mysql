-- =========================================
-- DATABASE: startraining (MySQL Version)
-- =========================================

-- 1. TABLA CONFIGURACIÓN GLOBAL
CREATE TABLE IF NOT EXISTS configuracion (
    clave VARCHAR(50) PRIMARY KEY,
    valor TEXT,
    creado_por VARCHAR(100),
    actualizado_por VARCHAR(100),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO configuracion (clave, valor) VALUES 
('nombre_sitio', 'StarTraining Pro'),
('modo_mantenimiento', 'off'),
('logo_sitio', '/assets/img/logo.png'),
('footer_descripcion', 'Plataforma líder en reclutamiento de talento profesional.'),
('email_contacto', 'contacto@startraining.com'),
('telefono_contacto', '+51 987 654 321'),
('facebook_url', '#'),
('instagram_url', '#'),
('linkedin_url', '#'),
('twitter_url', '#');

-- 2. TABLA EMPRESAS
CREATE TABLE IF NOT EXISTS empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_comercial VARCHAR(150) NOT NULL,
    ruc VARCHAR(11) UNIQUE NOT NULL,
    sector VARCHAR(100),
    correo_contacto VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    password_hash VARCHAR(255) NOT NULL,
    foto_perfil TEXT,
    estado VARCHAR(20) DEFAULT 'pendiente',
    es_top BOOLEAN DEFAULT FALSE,
    creado_por VARCHAR(100),
    actualizado_por VARCHAR(100),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABLA ADMINISTRADORES
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100),
    foto_perfil TEXT,
    creado_por VARCHAR(100),
    actualizado_por VARCHAR(100),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABLA CARRERAS
CREATE TABLE IF NOT EXISTS carreras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    creado_por VARCHAR(100),
    actualizado_por VARCHAR(100),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO carreras (nombre) VALUES 
('Ingeniería de Sistemas'), ('Ingeniería Industrial'), ('Ingeniería Civil'), 
('Ingeniería de Minas'), ('Arquitectura y Urbanismo'), ('Administración de Empresas'), 
('Marketing Digital'), ('Contabilidad y Finanzas'), ('Derecho Corporativo'), 
('Psicología Organizacional'), ('Ciencias de la Comunicación'), ('Economía y Negocios');

-- 5. TABLA VACANTES
CREATE TABLE IF NOT EXISTS vacantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    carrera_id INT,
    titulo_puesto VARCHAR(150) NOT NULL,
    descripcion_puesto TEXT NOT NULL,
    requisitos_raw TEXT NOT NULL,
    modalidad VARCHAR(50),
    ubicacion VARCHAR(100),
    fecha_limite DATE,
    estado VARCHAR(20) DEFAULT 'abierta',
    creado_por VARCHAR(100),
    actualizado_por VARCHAR(100),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_empresa FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    CONSTRAINT fk_carrera FOREIGN KEY (carrera_id) REFERENCES carreras(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TABLA POSTULACIONES
CREATE TABLE IF NOT EXISTS postulaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vacante_id INT NOT NULL,
    dni VARCHAR(15) NOT NULL,
    nombre_completo VARCHAR(200) NOT NULL,
    correo_estudiante VARCHAR(150) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    url_cv_pdf TEXT,
    match_porcentaje DECIMAL(5,2) DEFAULT 0.00,
    ia_analisis_descripcion TEXT,
    estado_postulacion VARCHAR(20) DEFAULT 'en_espera',
    notificacion_leida BOOLEAN DEFAULT FALSE,
    creado_por VARCHAR(100),
    actualizado_por VARCHAR(100),
    fecha_postulacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_vacante FOREIGN KEY (vacante_id) REFERENCES vacantes(id) ON DELETE CASCADE,
    CONSTRAINT unique_postulacion_dni UNIQUE (vacante_id, dni)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DUMMY DATA
INSERT IGNORE INTO administradores (usuario, password_hash, nombre)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Global');

INSERT IGNORE INTO empresas (nombre_comercial, ruc, sector, correo_contacto, password_hash, estado, es_top, direccion) VALUES 
('BBVA Perú', '20100130204', 'Finanzas', 'talento@bbva.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', true, 'Av. República de Panamá 3055, San Isidro'),
('Alicorp S.A.A.', '20100055237', 'Consumo Masivo', 'rrhh@alicorp.com.pe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', true, 'Av. Argentina 4793, Carmen de la Legua'),
('Ferreyros S.A.', '20100028698', 'Maquinaria Pesada', 'empleos@ferreyros.com.pe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', true, 'Av. Industrial 675, Lima'),
('Intercorp Retail', '20506564177', 'Retail', 'seleccion@intercorp.com.pe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', true, 'Av. Carlos Villarán 140, La Victoria'),
('Globant Perú', '20536440704', 'Tecnología', 'jobs@globant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'activo', true, 'Av. Alfredo Benavides 1561, Miraflores');

INSERT INTO vacantes (empresa_id, carrera_id, titulo_puesto, descripcion_puesto, requisitos_raw, modalidad, ubicacion, fecha_limite) VALUES 
(1, 1, 'Analista de Ciberseguridad Jr.', 'Protección de infraestructura bancaria.', 'Conocimiento en Firewalls, ISO 27001.', 'Híbrido', 'San Isidro', '2027-12-31'),
(1, 8, 'Asistente de Auditoría', 'Revisión de estados financieros.', 'Estudiante de 10mo ciclo.', 'Presencial', 'Lima', '2027-12-31'),
(1, 12, 'Practicante de Riesgos', 'Análisis de riesgo crediticio.', 'Excel avanzado, SQL intermedio.', 'Híbrido', 'Remoto/San Isidro', '2027-12-31'),
(2, 2, 'Ingeniero de Procesos Junior', 'Optimización de líneas de producción.', 'Lean Manufacturing, Six Sigma.', 'Presencial', 'Callao', '2027-12-31'),
(5, 1, 'React Developer Web UI', 'Desarrollo de interfaces premium.', 'React, TypeScript, Redux.', 'Remoto', 'Perú/Global', '2027-12-31');