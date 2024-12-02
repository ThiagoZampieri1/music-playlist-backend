<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Playlist.php');
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
    $userId = validateToken();

    $data = handleJSONInput();

    if (method("GET")) {
        if (isset($_GET['usuario_id'])) {
            if ($_GET['usuario_id'] != $userId) {
                throw new Exception("Acesso negado: você só pode acessar suas próprias playlists", 403);
            }
            $playlists = Playlist::listar($_GET['usuario_id']);
        } else {
            $playlists = Playlist::listar($userId);
        }
        output(200, ["playlists" => $playlists]);
    } elseif (method("POST")) {
        if (!valid($data, ["titulo"])) {
            throw new Exception("Título é obrigatório", 400);
        }
        $descricao = isset($data["descricao"]) ? $data["descricao"] : null;
        $id = Playlist::insert($data["titulo"], $descricao, $userId);
        output(201, ["msg" => "Playlist criada com sucesso", "id" => $id]);
    } elseif (method("PUT")) {
        if (!isset($_GET['id'])) {
            throw new Exception("ID da playlist não fornecido", 400);
        }
        if (!valid($data, ["titulo"])) {
            throw new Exception("Título é obrigatório", 400);
        }
        $descricao = isset($data["descricao"]) ? $data["descricao"] : null;

        $playlist = Playlist::getById($_GET['id']);
        if (!$playlist) {
            throw new Exception("Playlist não encontrada", 404);
        }
        if ($playlist['usuario_id'] != $userId) {
            throw new Exception("Acesso negado: você só pode editar suas próprias playlists", 403);
        }

        $rows = Playlist::update($_GET['id'], $data["titulo"], $descricao);
        if ($rows > 0) {
            output(200, ["msg" => "Playlist atualizada com sucesso"]);
        } else {
            output(400, ["msg" => "Nenhuma alteração realizada"]);
        }
    } elseif (method("DELETE")) {
        if (!isset($_GET['id'])) {
            throw new Exception("ID da playlist não fornecido", 400);
        }

        $playlist = Playlist::getById($_GET['id']);
        if (!$playlist) {
            throw new Exception("Playlist não encontrada", 404);
        }
        if ($playlist['usuario_id'] != $userId) {
            throw new Exception("Acesso negado: você só pode excluir suas próprias playlists", 403);
        }

        $rows = Playlist::delete($_GET['id']);
        if ($rows > 0) {
            output(200, ["msg" => "Playlist excluída com sucesso"]);
        } else {
            output(400, ["msg" => "Não foi possível excluir a playlist"]);
        }
    } else {
        output(405, ["msg" => "Método não permitido"]);
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
