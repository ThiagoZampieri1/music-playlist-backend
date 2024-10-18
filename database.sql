-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS webservice_playlist;
USE webservice_playlist;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (email)
);

-- Tabela de Playlists
CREATE TABLE IF NOT EXISTS playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    usuario_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Músicas
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

-- Tabela relacional Playlist_Música
CREATE TABLE IF NOT EXISTS playlist_musica (
    playlist_id INT NOT NULL,
    musica_id INT NOT NULL,
    PRIMARY KEY (playlist_id, musica_id),
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (musica_id) REFERENCES musicas(id) ON DELETE CASCADE
);

-- Inserir dados pré-populados

-- Usuários
INSERT INTO usuarios (nome, email, senha) VALUES 
('João Silva', 'joao@gmail.com', MD5('senha123')),
('Maria Oliveira', 'maria@gmail.com', MD5('senha456')),
('Carlos Pereira', 'carlos@gmail.com', MD5('senha789')),
('Ana Souza', 'ana@gmail.com', MD5('senha321'));

-- Playlists
INSERT INTO playlists (titulo, descricao, usuario_id) VALUES
('Favoritas do João', 'As músicas que o João mais gosta', 1),
('Sertanejo da Maria', 'Playlist de sertanejo da Maria', 2),
('Rock Clássico', 'Melhores músicas de rock clássico', 3),
('Pop Internacional', 'Hits internacionais do momento', 4);

-- Músicas
INSERT INTO musicas (titulo, artista, link, plataforma) VALUES
('Imagine', 'John Lennon', 'https://example.com/imagine', 'YouTube'),
('Evidências', 'Chitãozinho & Xororó', 'https://example.com/evidencias', 'Spotify'),
('Bohemian Rhapsody', 'Queen', 'https://example.com/bohemian', 'Deezer'),
('Shape of You', 'Ed Sheeran', 'https://example.com/shapeofyou', 'Apple Music'),
('Hotel California', 'Eagles', 'https://example.com/hotelcalifornia', 'YouTube'),
('Thinking Out Loud', 'Ed Sheeran', 'https://example.com/thinkingoutloud', 'Spotify'),
('Sweet Child O\' Mine', 'Guns N\' Roses', 'https://example.com/sweetchild', 'YouTube'),
('Thriller', 'Michael Jackson', 'https://example.com/thriller', 'Spotify');

-- Associação de músicas às playlists
INSERT INTO playlist_musica (playlist_id, musica_id) VALUES
-- Favoritas do João
(1, 1), -- Imagine
(1, 3), -- Bohemian Rhapsody
(1, 5), -- Hotel California

-- Sertanejo da Maria
(2, 2), -- Evidências

-- Rock Clássico do Carlos
(3, 3), -- Bohemian Rhapsody
(3, 5), -- Hotel California
(3, 7), -- Sweet Child O' Mine

-- Pop Internacional da Ana
(4, 4), -- Shape of You
(4, 6), -- Thinking Out Loud
(4, 8); -- Thriller