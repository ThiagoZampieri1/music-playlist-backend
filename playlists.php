<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Playlist.php');

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$data = handleJSONInput();

// GET: Listar playlists
if (method("GET")) {
    if (isset($_GET['usuario_id'])) {
        $playlists = Playlist::listar($_GET['usuario_id']);
    } else {
        $playlists = Playlist::listar();
    }
    output(200, $playlists);
}

// POST: Criar nova playlist
if (method("POST")) {
    try {
        if (!valid($data, ["titulo", "usuario_id"])) {
            throw new Exception("Título e ID do usuário são obrigatórios", 400);
        }
        $descricao = isset($data["descricao"]) ? $data["descricao"] : null;
        $id = Playlist::insert($data["titulo"], $descricao, $data["usuario_id"]);
        output(201, ["msg" => "Playlist criada com sucesso", "id" => $id]);
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}

// PUT: Atualizar playlist
if (method("PUT")) {
    try {
        if (!isset($_GET['id'])) {
            throw new Exception("ID da playlist não fornecido", 400);
        }
        if (!valid($data, ["titulo", "descricao"])) {
            throw new Exception("Título e descrição são obrigatórios", 400);
        }
        $rows = Playlist::update($_GET['id'], $data["titulo"], $data["descricao"]);
        if ($rows > 0) {
            output(200, ["msg" => "Playlist atualizada com sucesso"]);
        } else {
            output(404, ["msg" => "Playlist não encontrada ou dados iguais"]);
        }
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}

// DELETE: Excluir playlist
if (method("DELETE")) {
    try {
        if (!isset($_GET['id'])) {
            throw new Exception("ID da playlist não fornecido", 400);
        }
        $rows = Playlist::delete($_GET['id']);
        if ($rows > 0) {
            output(200, ["msg" => "Playlist excluída com sucesso"]);
        } else {
            output(404, ["msg" => "Playlist não encontrada"]);
        }
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}
