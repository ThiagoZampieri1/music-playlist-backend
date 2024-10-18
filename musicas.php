<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Musica.php');

header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$data = handleJSONInput();

// GET: Listar músicas de uma playlist
if (method("GET")) {
    if (isset($_GET['playlist_id'])) {
        $musicas = Musica::listarPorPlaylist($_GET['playlist_id']);
        output(200, $musicas);
    } else {
        output(400, ["msg" => "ID da playlist não fornecido"]);
    }
}

// POST: Adicionar música à playlist
if (method("POST")) {
    try {
        if (!valid($data, ["titulo", "link", "playlist_id"])) {
            throw new Exception("Título, link e ID da playlist são obrigatórios", 400);
        }
        $artista = isset($data["artista"]) ? $data["artista"] : null;
        $musica_id = Musica::insert($data["titulo"], $artista, $data["link"]);
        Musica::addMusicaNaPlaylist($data["playlist_id"], $musica_id);
        output(201, ["msg" => "Música adicionada à playlist", "id" => $musica_id]);
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}

// DELETE: Remover música da playlist
if (method("DELETE")) {
    try {
        if (!valid($_GET, ["playlist_id", "musica_id"])) {
            throw new Exception("IDs da playlist e da música são obrigatórios", 400);
        }
        $rows = Musica::removeMusicaDaPlaylist($_GET["playlist_id"], $_GET["musica_id"]);
        if ($rows > 0) {
            output(200, ["msg" => "Música removida da playlist"]);
        } else {
            output(404, ["msg" => "Música ou playlist não encontrada"]);
        }
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}
