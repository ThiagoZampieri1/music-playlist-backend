<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Usuario.php');
require_once(__DIR__ . '/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $data = handleJSONInput();

    if (method("POST")) {
        if (!valid($data, ["nome", "email", "senha"])) {
            throw new Exception("Campos obrigatórios não preenchidos", 400);
        }
        $id = Usuario::insert($data["nome"], $data["email"], $data["senha"]);
        output(201, ["msg" => "Usuário criado com sucesso", "id" => $id]);
    } else {
        $userId = validateToken();

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
        } elseif (method("PUT")) {
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
        } elseif (method("DELETE")) {
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
            output(405, ["msg" => "Método não permitido"]);
        }
    }
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    output($statusCode, ["msg" => $e->getMessage()]);
}

function validateToken()
{
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        throw new Exception("Token não fornecido", 401);
    }

    $authHeader = $headers['Authorization'];
    list($jwt) = sscanf($authHeader, 'Bearer %s');

    if (!$jwt) {
        throw new Exception("Formato do token inválido", 401);
    }

    try {
        $secretKey = $_ENV['JWT_SECRET'];
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

        return $decoded->data->userId;
    } catch (Exception $e) {
        throw new Exception("Token inválido: " . $e->getMessage(), 401);
    }
}
