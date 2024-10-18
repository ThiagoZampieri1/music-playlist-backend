<?php
require_once(__DIR__ . '/../configs/Database.php');

class Musica
{
    public static function listarPorPlaylist($playlist_id)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("
            SELECT m.id, m.titulo, m.artista, m.link
            FROM musicas m
            INNER JOIN playlist_musica pm ON m.id = pm.musica_id
            WHERE pm.playlist_id = ?
        ");
        $sql->execute([$playlist_id]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insert($titulo, $artista, $link)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("INSERT INTO musicas (titulo, artista, link) VALUES (?, ?, ?)");
        $sql->execute([$titulo, $artista, $link]);
        return $conexao->lastInsertId();
    }

    public static function addMusicaNaPlaylist($playlist_id, $musica_id)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("INSERT INTO playlist_musica (playlist_id, musica_id) VALUES (?, ?)");
        $sql->execute([$playlist_id, $musica_id]);
        return $sql->rowCount();
    }

    public static function removeMusicaDaPlaylist($playlist_id, $musica_id)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("DELETE FROM playlist_musica WHERE playlist_id = ? AND musica_id = ?");
        $sql->execute([$playlist_id, $musica_id]);
        return $sql->rowCount();
    }
}
