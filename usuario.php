<?php

// Arquivos com funções úteis que vão ser usadas nesta rota.
require_once(__DIR__ . "/configs/utils.php");
// Arquivos com as entidades (models) que vão ser usadas nesta rota.
require_once(__DIR__ . "/model/Usuario.php");

// Bloco de código configurando o servidor. Remover os métodos que não forem suportados.
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Usado para receber os dados brutos do corpo da requisição.
// Caso não tenha sido enviado nada no formato JSON, retorna FALSE.
$data = handleJSONInput();


// Listar todos os usuários
if (method("GET")) {
    $usuarios = Usuario::listar();
    output(200, $usuarios);
}

// Cadastrar novo usuário
if (method("POST")) {
    try {
        if (!valid($data, ["nome", "email", "senha"])) {
            throw new Exception("Campos obrigatórios não preenchidos", 400);
        }
        $res = Usuario::insert($data["nome"], $data["email"], $data["senha"]);
        output(200, ["msg" => "Usuário criado com sucesso"]);
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}
