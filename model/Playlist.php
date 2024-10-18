<?php
require_once(__DIR__ . '/../configs/Database.php');

class Playlist
{
    public static function listar($usuario_id = null)
    {
        try {
            $conexao = Conexao::getConexao();
            if ($usuario_id) {
                $sql = $conexao->prepare("SELECT * FROM playlists WHERE usuario_id = ?");
                $sql->execute([$usuario_id]);
            } else {
                $sql = $conexao->prepare("SELECT * FROM playlists");
                $sql->execute();
            }
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar playlists: " . $e->getMessage(), 500);
        }
    }

    public static function getById($id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("SELECT * FROM playlists WHERE id = ?");
            $sql->execute([$id]);
            return $sql->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter playlist: " . $e->getMessage(), 500);
        }
    }

    public static function insert($titulo, $descricao, $usuario_id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("INSERT INTO playlists (titulo, descricao, usuario_id) VALUES (?, ?, ?)");
            $sql->execute([$titulo, $descricao, $usuario_id]);
            return $conexao->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar playlist: " . $e->getMessage(), 500);
        }
    }

    public static function update($id, $titulo, $descricao)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("UPDATE playlists SET titulo = ?, descricao = ? WHERE id = ?");
            $sql->execute([$titulo, $descricao, $id]);
            return $sql->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar playlist: " . $e->getMessage(), 500);
        }
    }

    public static function delete($id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("DELETE FROM playlists WHERE id = ?");
            $sql->execute([$id]);
            return $sql->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Erro ao excluir playlist: " . $e->getMessage(), 500);
        }
    }
}
