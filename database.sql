CREATE DATABASE IF NOT EXISTS webservice_playlist;
USE webservice_playlist;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (email)
);

CREATE TABLE IF NOT EXISTS playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    usuario_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS musicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    artista VARCHAR(100),
    link VARCHAR(255) NOT NULL,
    plataforma VARCHAR(50),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (titulo, link)
);

CREATE TABLE IF NOT EXISTS playlist_musica (
    playlist_id INT NOT NULL,
    musica_id INT NOT NULL,
    PRIMARY KEY (playlist_id, musica_id),
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (musica_id) REFERENCES musicas(id) ON DELETE CASCADE
);



INSERT INTO usuarios (nome, email, senha) VALUES 
('João Silva', 'joao@gmail.com', MD5('senha123')),
('Maria Oliveira', 'maria@gmail.com', MD5('senha456')),
('Carlos Pereira', 'carlos@gmail.com', MD5('senha789')),
('Ana Souza', 'ana@gmail.com', MD5('senha321'));

INSERT INTO playlists (titulo, descricao, usuario_id) VALUES
('Favoritas do João', 'As músicas que o João mais gosta', 1),
('Sertanejo da Maria', 'Playlist de sertanejo da Maria', 2),
('Rock Clássico', 'Melhores músicas de rock clássico', 3),
('Pop Internacional', 'Hits internacionais do momento', 4);

INSERT INTO musicas (titulo, artista, link, plataforma) VALUES
('Imagine', 'John Lennon', 'https://example.com/imagine', 'YouTube'),
('Evidências', 'Chitãozinho & Xororó', 'https://example.com/evidencias', 'Spotify'),
('Bohemian Rhapsody', 'Queen', 'https://example.com/bohemian', 'Deezer'),
('Shape of You', 'Ed Sheeran', 'https://example.com/shapeofyou', 'Apple Music'),
('Hotel California', 'Eagles', 'https://example.com/hotelcalifornia', 'YouTube'),
('Thinking Out Loud', 'Ed Sheeran', 'https://example.com/thinkingoutloud', 'Spotify'),
('Sweet Child O\' Mine', 'Guns N\' Roses', 'https://example.com/sweetchild', 'YouTube'),
('Thriller', 'Michael Jackson', 'https://example.com/thriller', 'Spotify');


INSERT INTO playlist_musica (playlist_id, musica_id) VALUES
(1, 1), 
(1, 3), 
(1, 5), 
(2, 2),
(3, 3),
(3, 5),
(3, 7),
(4, 4),
(4, 6),
(4, 8);