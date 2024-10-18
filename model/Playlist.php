<?php
require_once(__DIR__ . '/../configs/Database.php');

class Playlist
{
    public static function listar($usuario_id = null)
    {
        $conexao = Conexao::getConexao();
        if ($usuario_id) {
            $sql = $conexao->prepare("SELECT * FROM playlists WHERE usuario_id = ?");
            $sql->execute([$usuario_id]);
        } else {
            $sql = $conexao->prepare("SELECT * FROM playlists");
            $sql->execute();
        }
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("SELECT * FROM playlists WHERE id = ?");
        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public static function insert($titulo, $descricao, $usuario_id)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("INSERT INTO playlists (titulo, descricao, usuario_id) VALUES (?, ?, ?)");
        $sql->execute([$titulo, $descricao, $usuario_id]);
        return $conexao->lastInsertId();
    }

    public static function update($id, $titulo, $descricao)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("UPDATE playlists SET titulo = ?, descricao = ? WHERE id = ?");
        $sql->execute([$titulo, $descricao, $id]);
        return $sql->rowCount();
    }

    public static function delete($id)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("DELETE FROM playlists WHERE id = ?");
        $sql->execute([$id]);
        return $sql->rowCount();
    }
}
