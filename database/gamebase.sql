CREATE DATABASE IF NOT EXISTS gamebase 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE gamebase;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'usuario') DEFAULT 'usuario',
    lembrar_token VARCHAR(64) DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE jogos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    ano INT,
    categoria_id INT,
    capa_url VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    jogo_id INT NOT NULL,
    nota TINYINT NOT NULL CHECK (nota BETWEEN 1 AND 10),
    comentario TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unica_avaliacao (usuario_id, jogo_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (jogo_id) REFERENCES jogos(id) ON DELETE CASCADE
);

INSERT INTO usuarios (nome, email, senha, tipo)
VALUES
    ('Admin', 'admin@gamebase.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
    ('Jogador Teste', 'user@gamebase.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario');

INSERT INTO categorias (nome)
VALUES
    ('RPG'),
    ('Ação'),
    ('Aventura'),
    ('Esporte'),
    ('FPS'),
    ('Estratégia');

INSERT INTO jogos (titulo, descricao, ano, categoria_id, capa_url)
VALUES
    ('The Witcher 3', 'RPG de mundo aberto com narrativa rica.', 2015, 1, 'https://cdn.cloudflare.steamstatic.com/steam/apps/292030/header.jpg'),
    ('God of War', 'Ação épica nórdica com Kratos e Atreus.', 2018, 2, 'https://cdn.cloudflare.steamstatic.com/steam/apps/1593500/header.jpg'),
    ('Hollow Knight', 'Metroidvania desafiador em mundo inseto.', 2017, 3, 'https://cdn.cloudflare.steamstatic.com/steam/apps/367520/header.jpg'),
    ('FIFA 24', 'Futebol realista com licenças oficiais.', 2023, 4, 'https://cdn.cloudflare.steamstatic.com/steam/apps/2195250/header.jpg'),
    ('CS2', 'FPS tático competitivo da Valve.', 2023, 5, 'https://cdn.cloudflare.steamstatic.com/steam/apps/730/header.jpg'),
    ('Civilization VI', 'Estratégia 4X de construção de impérios.', 2016, 6, 'https://cdn.cloudflare.steamstatic.com/steam/apps/289070/header.jpg');