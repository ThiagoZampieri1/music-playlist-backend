<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Playlist.php');

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

    if (method("GET")) {
        if (isset($_GET['usuario_id'])) {
            $playlists = Playlist::listar($_GET['usuario_id']);
        } else {
            $playlists = Playlist::listar();
        }
        output(200, $playlists);
    } elseif (method("POST")) {
        if (!valid($data, ["titulo", "usuario_id"])) {
            throw new Exception("Título e ID do usuário são obrigatórios", 400);
        }
        $descricao = isset($data["descricao"]) ? $data["descricao"] : null;
        $id = Playlist::insert($data["titulo"], $descricao, $data["usuario_id"]);
        output(201, ["msg" => "Playlist criada com sucesso", "id" => $id]);
    } elseif (method("PUT")) {
        if (!isset($_GET['id'])) {
            throw new Exception("ID da playlist não fornecido", 400);
        }
        if (!valid($data, ["titulo"])) {
            throw new Exception("Título é obrigatório", 400);
        }
        $descricao = isset($data["descricao"]) ? $data["descricao"] : null;
        $rows = Playlist::update($_GET['id'], $data["titulo"], $descricao);
        if ($rows > 0) {
            output(200, ["msg" => "Playlist atualizada com sucesso"]);
        } else {
            output(404, ["msg" => "Playlist não encontrada ou dados iguais"]);
        }
    } elseif (method("DELETE")) {
        if (!isset($_GET['id'])) {
            throw new Exception("ID da playlist não fornecido", 400);
        }
        $rows = Playlist::delete($_GET['id']);
        if ($rows > 0) {
            output(200, ["msg" => "Playlist excluída com sucesso"]);
        } else {
            output(404, ["msg" => "Playlist não encontrada"]);
        }
    } else {
        output(405, ["msg" => "Método não permitido"]);
    }
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    output($statusCode, ["msg" => $e->getMessage()]);
}
