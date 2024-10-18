<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Usuario.php');

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    $data = handleJSONInput();

    // GET: Listar usuários
    if (method("GET")) {
        if (isset($_GET['id'])) {
            $usuario = Usuario::getById($_GET['id']);
            if ($usuario) {
                output(200, ["usuario" => $usuario]);
            } else {
                output(404, ["msg" => "Usuário não encontrado"]);
            }
        } else {
            $usuarios = Usuario::listar();
            output(200, ["usuarios" => $usuarios]);
        }
    }

    // POST: Criar novo usuário
    elseif (method("POST")) {
        if (!valid($data, ["nome", "email", "senha"])) {
            throw new Exception("Campos obrigatórios não preenchidos", 400);
        }
        $id = Usuario::insert($data["nome"], $data["email"], $data["senha"]);
        output(201, ["msg" => "Usuário criado com sucesso", "id" => $id]);
    }

    // PUT: Atualizar usuário
    elseif (method("PUT")) {
        if (!isset($_GET['id'])) {
            throw new Exception("ID do usuário não fornecido", 400);
        }
        if (!valid($data, ["nome", "email"])) {
            throw new Exception("Campos obrigatórios não preenchidos", 400);
        }
        $rows = Usuario::update($_GET['id'], $data["nome"], $data["email"]);
        if ($rows > 0) {
            output(200, ["msg" => "Usuário atualizado com sucesso"]);
        } else {
            output(404, ["msg" => "Usuário não encontrado ou dados iguais"]);
        }
    }

    // DELETE: Excluir usuário
    elseif (method("DELETE")) {
        if (!isset($_GET['id'])) {
            throw new Exception("ID do usuário não fornecido", 400);
        }
        $rows = Usuario::delete($_GET['id']);
        if ($rows > 0) {
            output(200, ["msg" => "Usuário excluído com sucesso"]);
        } else {
            output(404, ["msg" => "Usuário não encontrado"]);
        }
    } else {
        // Método não permitido
        output(405, ["msg" => "Método não permitido"]);
    }
} catch (Exception $e) {
    // Definir código de status padrão se não estiver definido
    $statusCode = $e->getCode() ?: 500;
    output($statusCode, ["msg" => $e->getMessage()]);
}
