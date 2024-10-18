<?php
require_once(__DIR__ . '/../configs/Database.php');

class Usuario
{
    public static function listar()
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("SELECT id, nome, email FROM usuarios");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("SELECT id, nome, email FROM usuarios WHERE id = ?");
        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public static function insert($nome, $email, $senha)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql->execute([$nome, $email, $senhaHash]);
        return $conexao->lastInsertId();
    }

    public static function update($id, $nome, $email)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
        $sql->execute([$nome, $email, $id]);
        return $sql->rowCount();
    }

    public static function delete($id)
    {
        $conexao = Conexao::getConexao();
        $sql = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
        $sql->execute([$id]);
        return $sql->rowCount();
    }
}
