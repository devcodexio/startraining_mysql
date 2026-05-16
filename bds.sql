SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- TABLA ADMINISTRADORES
-- =========================
CREATE TABLE administradores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nombre VARCHAR(100),
  foto_perfil TEXT,
  creado_por VARCHAR(100),
  actualizado_por VARCHAR(100),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================
-- TABLA CARRERAS
-- =========================
CREATE TABLE carreras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  creado_por VARCHAR(100),
  actualizado_por VARCHAR(100),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================
-- TABLA CONFIGURACION
-- =========================
CREATE TABLE configuracion (
  clave VARCHAR(50) PRIMARY KEY,
  valor TEXT,
  creado_por VARCHAR(100),
  actualizado_por VARCHAR(100),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================
-- TABLA EMPRESAS
-- =========================
CREATE TABLE empresas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_comercial VARCHAR(150) NOT NULL,
  ruc CHAR(11) NOT NULL UNIQUE,
  sector VARCHAR(100),
  correo_contacto VARCHAR(150) NOT NULL UNIQUE,
  telefono VARCHAR(20),
  direccion TEXT,
  password_hash VARCHAR(255) NOT NULL,
  foto_perfil TEXT,
  ficha_ruc TEXT,
  dni_frente TEXT,
  dni_reverso TEXT,
  foto_selfie TEXT,
  estado ENUM('pendiente','activo','rechazado') DEFAULT 'pendiente',
  es_top BOOLEAN DEFAULT FALSE,
  creado_por VARCHAR(100),
  actualizado_por VARCHAR(100),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================
-- TABLA VACANTES
-- =========================
CREATE TABLE vacantes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  empresa_id INT NOT NULL,
  carrera_id INT,
  titulo_puesto VARCHAR(150) NOT NULL,
  descripcion_puesto TEXT NOT NULL,
  requisitos_raw TEXT NOT NULL,
  modalidad ENUM('Remoto','Presencial','Híbrido'),
  ubicacion VARCHAR(100),
  fecha_limite DATE,
  estado ENUM('abierta','cerrada','finalizada') DEFAULT 'abierta',
  creado_por VARCHAR(100),
  actualizado_por VARCHAR(100),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_empresa (empresa_id),
  INDEX idx_carrera (carrera_id),

  CONSTRAINT fk_vacantes_empresa
    FOREIGN KEY (empresa_id)
    REFERENCES empresas(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_vacantes_carrera
    FOREIGN KEY (carrera_id)
    REFERENCES carreras(id)
    ON DELETE SET NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================
-- TABLA POSTULACIONES
-- =========================
CREATE TABLE postulaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vacante_id INT NOT NULL,
  dni CHAR(8) NOT NULL,
  nombre_completo VARCHAR(200) NOT NULL,
  correo_estudiante VARCHAR(150) NOT NULL,
  celular VARCHAR(20) NOT NULL,
  url_cv_pdf TEXT,
  match_porcentaje DECIMAL(5,2) DEFAULT 0.00,
  ia_analisis_descripcion TEXT,
  estado_postulacion ENUM('en_espera','revisado','rechazado','aprobado') DEFAULT 'en_espera',
  notificacion_leida BOOLEAN DEFAULT FALSE,
  creado_por VARCHAR(100),
  actualizado_por VARCHAR(100),
  fecha_postulacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY unique_postulacion (vacante_id, dni),

  INDEX idx_vacante (vacante_id),

  CONSTRAINT fk_postulacion_vacante
    FOREIGN KEY (vacante_id)
    REFERENCES vacantes(id)
    ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 1. Crear la tabla intermedia para múltiples carreras
CREATE TABLE IF NOT EXISTS vacante_carreras (
  vacante_id INT NOT NULL,
  carrera_id INT NOT NULL,
  PRIMARY KEY (vacante_id, carrera_id),
  CONSTRAINT fk_vc_vacante FOREIGN KEY (vacante_id) REFERENCES vacantes(id) ON DELETE CASCADE,
  CONSTRAINT fk_vc_carrera FOREIGN KEY (carrera_id) REFERENCES carreras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Migrar los datos existentes (opcional, para no perder las carreras actuales)
INSERT IGNORE INTO vacante_carreras (vacante_id, carrera_id)
SELECT id, carrera_id FROM vacantes WHERE carrera_id IS NOT NULL;
