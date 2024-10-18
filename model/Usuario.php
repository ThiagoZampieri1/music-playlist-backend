<?php
require_once(__DIR__ . '/../configs/Database.php');

class Usuario
{
    public static function listar()
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("SELECT id, nome, email FROM usuarios");
            $sql->execute();
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }  catch (Exception $e) {
            output(500, ["msg" => $e->getMessage()]);
        }
    }

    public static function getById($id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("SELECT id, nome, email FROM usuarios WHERE id = ?");
            $sql->execute([$id]);
            return $sql->fetch(PDO::FETCH_ASSOC);
        }  catch (Exception $e) {
            output(500, ["msg" => $e->getMessage()]);
        }
    }

    public static function insert($nome, $email, $senha)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql->execute([$nome, $email, $senhaHash]);
            return $conexao->lastInsertId();
        }  catch (Exception $e) {
            output(500, ["msg" => $e->getMessage()]);
        }
    }

    public static function update($id, $nome, $email)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $sql->execute([$nome, $email, $id]);
            return $sql->rowCount();
        }  catch (Exception $e) {
            output(500, ["msg" => $e->getMessage()]);
        }   
    }

    public static function delete($id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
            $sql->execute([$id]);
            return $sql->rowCount();
        } catch (Exception $e) {
            output(500, ["msg" => $e->getMessage()]);
        }
    }
}
