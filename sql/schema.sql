-- ============================================================
-- Congreso Internacional de Tecnología e Innovación
-- en Ingeniería y Computación
-- Script de creación de base de datos
-- Importar este archivo desde phpMyAdmin (cPanel) o con:
--   mysql -u tu_usuario -p tu_base_de_datos < schema.sql
-- ============================================================

-- Tabla de personas inscritas al congreso
CREATE TABLE IF NOT EXISTS inscritos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombres         VARCHAR(100)        NOT NULL,
    apellidos       VARCHAR(100)        NOT NULL,
    email           VARCHAR(150)        NOT NULL,
    documento       VARCHAR(20)         NOT NULL,
    institucion     VARCHAR(150)        NOT NULL,
    telefono        VARCHAR(20)         NULL,
    eje_tematico    VARCHAR(60)         NOT NULL,
    fecha_inscripcion DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_inscritos_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios administradores (quienes ven el dashboard)
CREATE TABLE IF NOT EXISTS admin_usuarios (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    usuario         VARCHAR(50)         NOT NULL,
    password_hash   VARCHAR(255)        NOT NULL,
    nombre          VARCHAR(100)        NOT NULL,
    UNIQUE KEY uq_admin_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario administrador de prueba
-- Usuario:    admin
-- Contraseña: Congreso2026!
-- (el hash de abajo corresponde a esa contraseña, generado con password_hash() de PHP)
INSERT INTO admin_usuarios (usuario, password_hash, nombre)
VALUES ('admin', '$2y$12$q2h44DoNQGNIfPnv7E2zDOHjzvX8s2/NTs1QiQ.qfZ98XKEDDGIZC', 'Administrador del Congreso')
ON DUPLICATE KEY UPDATE usuario = usuario;

-- Un par de inscritos de ejemplo, para que el dashboard no se vea vacío
-- (puedes borrar estas filas cuando conectes inscripciones reales)
INSERT INTO inscritos (nombres, apellidos, email, documento, institucion, telefono, eje_tematico) VALUES
('Valentina', 'Ramírez', 'valentina.ramirez@example.com', '74581236', 'USIL', '987654321', 'Inteligencia Artificial'),
('Mateo', 'Quispe', 'mateo.quispe@example.com', '71234598', 'UNI', '956123478', 'Ciberseguridad'),
('Camila', 'Torres', 'camila.torres@example.com', '70981234', 'PUCP', '944556677', 'Desarrollo de Software')
ON DUPLICATE KEY UPDATE email = email;
