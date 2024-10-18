<?php
require_once(__DIR__ . '/../configs/Database.php');

class Usuario
{
    public static function listar()
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("SELECT id, nome, email, criado_em, atualizado_em, cpf FROM usuarios");
            $sql->execute();
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao listar usuários: " . $e->getMessage(), 500);
        }
    }

    public static function getById($id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("SELECT id, nome, email, criado_em, atualizado_em FROM usuarios WHERE id = ?");
            $sql->execute([$id]);
            return $sql->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter usuário: " . $e->getMessage(), 500);
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
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                throw new Exception("Email já cadastrado", 409);
            } else {
                throw new Exception("Erro ao criar usuário: " . $e->getMessage(), 500);
            }
        }
    }

    public static function update($id, $nome, $email)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $sql->execute([$nome, $email, $id]);
            return $sql->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                throw new Exception("Email já cadastrado", 409);
            } else {
                throw new Exception("Erro ao atualizar usuário: " . $e->getMessage(), 500);
            }
        }
    }

    public static function delete($id)
    {
        try {
            $conexao = Conexao::getConexao();
            $sql = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
            $sql->execute([$id]);
            return $sql->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Erro ao excluir usuário: " . $e->getMessage(), 500);
        }
    }
}
