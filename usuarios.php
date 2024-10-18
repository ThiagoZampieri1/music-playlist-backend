<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Usuario.php');

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$data = handleJSONInput();

// GET: Listar usuários
if (method("GET")) {
    if (isset($_GET['id'])) {
        $usuario = Usuario::getById($_GET['id']);
        if ($usuario) {
            output(200, $usuario);
        } else {
            output(404, ["msg" => "Usuário não encontrado"]);
        }
    } else {
        $usuarios = Usuario::listar();
        output(200, $usuarios);
    }
}

// POST: Criar novo usuário
if (method("POST")) {
    try {
        if (!valid($data, ["nome", "email", "senha"])) {
            throw new Exception("Campos obrigatórios não preenchidos", 400);
        }
        $id = Usuario::insert($data["nome"], $data["email"], $data["senha"]);
        output(201, ["msg" => "Usuário criado com sucesso", "id" => $id]);
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}

// PUT: Atualizar usuário
if (method("PUT")) {
    try {
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
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}

// DELETE: Excluir usuário
if (method("DELETE")) {
    try {
        if (!isset($_GET['id'])) {
            throw new Exception("ID do usuário não fornecido", 400);
        }
        $rows = Usuario::delete($_GET['id']);
        if ($rows > 0) {
            output(200, ["msg" => "Usuário excluído com sucesso"]);
        } else {
            output(404, ["msg" => "Usuário não encontrado"]);
        }
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}
