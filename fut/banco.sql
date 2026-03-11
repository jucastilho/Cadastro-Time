-- ============================================================
-- SCRIPT DE CRIAÇÃO DO BANCO DE DADOS — Sistema de Futebol
-- Execute este arquivo antes de rodar o sistema
-- ============================================================

CREATE DATABASE IF NOT EXISTS futebol CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE futebol;

CREATE TABLE IF NOT EXISTS pais (
    pais_id   INT PRIMARY KEY AUTO_INCREMENT,
    nome_pais VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS estado (
    estado_id   INT PRIMARY KEY AUTO_INCREMENT,
    nome_estado VARCHAR(100),
    pais_id     INT,
    FOREIGN KEY (pais_id) REFERENCES pais(pais_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cidades (
    id        INT PRIMARY KEY AUTO_INCREMENT,
    estado_id INT,
    nome      VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (estado_id) REFERENCES estado(estado_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS posicoes (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS uniformes (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('calção','meião','camisa') DEFAULT NULL,
    nome VARCHAR(255) DEFAULT NULL,
    KEY  roupas_idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tecnicos (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    nome             VARCHAR(255) DEFAULT NULL,
    descricao        MEDIUMTEXT,
    data_nascimento  DATE DEFAULT NULL,
    data_falecimento DATE DEFAULT NULL,
    time             TINYINT(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS competicoes (
    id            INT PRIMARY KEY AUTO_INCREMENT,
    nome          VARCHAR(255) DEFAULT NULL,
    internacional TINYINT(1)   DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jogadores (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    posicao_id       INT,
    gols_sofridos    INT DEFAULT NULL,
    nome             VARCHAR(255) DEFAULT NULL,
    nome_real        VARCHAR(255) DEFAULT NULL,
    descricao        MEDIUMTEXT,
    titulos          MEDIUMTEXT,
    data_nascimento  DATE DEFAULT NULL,
    data_falecimento DATE DEFAULT NULL,
    time             TINYINT(1) DEFAULT NULL,
    FOREIGN KEY (posicao_id) REFERENCES posicoes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Posições iniciais (opcional)
INSERT IGNORE INTO posicoes (nome) VALUES
('Goleiro'), ('Zagueiro'), ('Lateral Direito'), ('Lateral Esquerdo'),
('Volante'), ('Meia Central'), ('Meia Ofensivo'),
('Ponta Direita'), ('Ponta Esquerda'), ('Centroavante');

CREATE TABLE IF NOT EXISTS arbitros (
    id               INT PRIMARY KEY AUTO_INCREMENT,
    nome             VARCHAR(255) DEFAULT NULL,
    nacionalidade    VARCHAR(255) DEFAULT NULL,
    descricao        MEDIUMTEXT,
    foto             VARCHAR(255) DEFAULT NULL,
    data_nascimento  DATE DEFAULT NULL,
    data_falecimento DATE DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS patrocinadores (
    id   INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS estadios (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    local_id     INT DEFAULT NULL,
    nome         VARCHAR(255) DEFAULT NULL,
    nome_popular VARCHAR(255) DEFAULT NULL,
    lotacao      INT DEFAULT NULL,
    FOREIGN KEY (local_id) REFERENCES cidades(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS times (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    nome         VARCHAR(255) DEFAULT NULL,
    nome_completo VARCHAR(255) DEFAULT NULL,
    sigla        VARCHAR(3)   DEFAULT NULL,
    escudo       VARCHAR(255) DEFAULT NULL,
    local_id     INT DEFAULT NULL,
    FOREIGN KEY (local_id) REFERENCES cidades(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
