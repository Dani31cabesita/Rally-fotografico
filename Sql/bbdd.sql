-- Creación de base de datos
CREATE DATABASE IF NOT EXISTS rally_fotografico;
USE rally_fotografico;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'participante') NOT NULL DEFAULT 'participante',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de rallys
CREATE TABLE rally (
    id_rally INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    max_fotos_participante INT NOT NULL,
    estado ENUM('activo', 'finalizado') NOT NULL DEFAULT 'activo'
);

-- Tabla de fotografías
CREATE TABLE fotografias (
    id_foto INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_rally INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    estado ENUM('pendiente', 'admitida', 'rechazada') NOT NULL DEFAULT 'pendiente',
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    num_votos INT DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_rally) REFERENCES rally(id_rally)
);

-- Tabla de votaciones
CREATE TABLE votaciones (
    id_voto INT AUTO_INCREMENT PRIMARY KEY,
    id_foto INT NOT NULL,
    id_rally INT NOT NULL,
    ip_votante VARCHAR(64) NOT NULL,
    fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_foto) REFERENCES fotografias(id_foto),
    FOREIGN KEY (id_rally) REFERENCES rally(id_rally)
);
