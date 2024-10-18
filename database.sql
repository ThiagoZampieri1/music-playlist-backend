-- Tabela de Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Tabela de Playlists
CREATE TABLE playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de Músicas
CREATE TABLE musicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    artista VARCHAR(100),
    link VARCHAR(255) NOT NULL
);

-- Tabela relacional Playlist_Música
CREATE TABLE playlist_musica (
    playlist_id INT NOT NULL,
    musica_id INT NOT NULL,
    PRIMARY KEY (playlist_id, musica_id),
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (musica_id) REFERENCES musicas(id) ON DELETE CASCADE
);

-- Inserir alguns dados pré-populados
INSERT INTO usuarios (nome, email, senha) VALUES ('João Silva', 'joao@gmail.com', 'senha123');
INSERT INTO playlists (titulo, descricao, usuario_id) VALUES ('Minhas Favoritas', 'Playlist com minhas músicas favoritas', 1);
INSERT INTO musicas (titulo, artista, link) VALUES ('Song Title', 'Artist Name', 'https://example.com/song');
INSERT INTO playlist_musica (playlist_id, musica_id) VALUES (1, 1);
