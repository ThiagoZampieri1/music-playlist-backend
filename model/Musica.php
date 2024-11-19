<?php
require_once(__DIR__ . '/../configs/Database.php');

class Musica
{
    public static function listarPorPlaylist($playlist_id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("
                SELECT m.id, m.titulo, m.artista, m.link, m.plataforma, m.criado_em, m.atualizado_em
                FROM musicas m
                INNER JOIN playlist_musica pm ON m.id = pm.musica_id
                WHERE pm.playlist_id = ?
            ");
            $sql->execute([$playlist_id]);
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar músicas da playlist: " . $e->getMessage(), 500);
        }
    }

    public static function insert($titulo, $artista, $link, $plataforma)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("
                INSERT INTO musicas (titulo, artista, link, plataforma)
                VALUES (?, ?, ?, ?)
            ");
            $sql->execute([$titulo, $artista, $link, $plataforma]);
            return $conexao->lastInsertId();
        } catch (PDOException $e) {
            // Tratamento de duplicatas devido à restrição UNIQUE
            if ($e->getCode() == '23000') {
                throw new Exception("Música já cadastrada", 409);
            } else {
                throw new Exception("Erro ao inserir música: " . $e->getMessage(), 500);
            }
        }
    }

    public static function getByTituloELink($titulo, $link)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("
                SELECT * FROM musicas WHERE titulo = ? AND link = ?
            ");
            $sql->execute([$titulo, $link]);
            return $sql->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar existência da música: " . $e->getMessage(), 500);
        }
    }

    public static function addMusicaNaPlaylist($playlist_id, $musica_id)
    {
        try {
            $conexao = Conexao::getConexao();
            // Verificar se a associação já existe
            $sql = $conexao->prepare("
                SELECT * FROM playlist_musica WHERE playlist_id = ? AND musica_id = ?
            ");
            $sql->execute([$playlist_id, $musica_id]);
            if ($sql->fetch()) {
                // Associação já existe, não fazer nada
                return 0;
            }

            // Inserir nova associação
            $sql = $conexao->prepare("
                INSERT INTO playlist_musica (playlist_id, musica_id) VALUES (?, ?)
            ");
            $sql->execute([$playlist_id, $musica_id]);
            return $sql->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Erro ao associar música à playlist: " . $e->getMessage(), 500);
        }
    }

    public static function removeMusicaDaPlaylist($playlist_id, $musica_id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("
                DELETE FROM playlist_musica WHERE playlist_id = ? AND musica_id = ?
            ");
            $sql->execute([$playlist_id, $musica_id]);
            return $sql->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Erro ao remover música da playlist: " . $e->getMessage(), 500);
        }
    }

    public static function update($id, $titulo, $artista, $link, $plataforma)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("
            UPDATE musicas
            SET titulo = ?, artista = ?, link = ?, plataforma = ?, atualizado_em = NOW()
            WHERE id = ?
        ");
            $sql->execute([$titulo, $artista, $link, $plataforma, $id]);
            return $sql->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar música: " . $e->getMessage(), 500);
        }
    }
}
