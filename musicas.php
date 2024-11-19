<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Musica.php');

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
        if (isset($_GET['playlist_id'])) {
            $musicas = Musica::listarPorPlaylist($_GET['playlist_id']);
            output(200, $musicas);
        } else {
            throw new Exception("ID da playlist não fornecido", 400);
        }
    } elseif (method("POST")) {
        if (!valid($data, ["titulo", "link", "playlist_id"])) {
            throw new Exception("Título, link e ID da playlist são obrigatórios", 400);
        }
        $artista = isset($data["artista"]) ? $data["artista"] : null;
        $plataforma = isset($data["plataforma"]) ? $data["plataforma"] : null;

        $musica = Musica::getByTituloELink($data["titulo"], $data["link"]);
        if ($musica) {
            $musica_id = $musica['id'];
        } else {
            $musica_id = Musica::insert($data["titulo"], $artista, $data["link"], $plataforma);
        }

        Musica::addMusicaNaPlaylist($data["playlist_id"], $musica_id);
        output(201, ["msg" => "Música adicionada à playlist", "id" => $musica_id]);
    } elseif (method("DELETE")) {
        if (!valid($_GET, ["playlist_id", "musica_id"])) {
            throw new Exception("IDs da playlist e da música são obrigatórios", 400);
        }
        $rows = Musica::removeMusicaDaPlaylist($_GET["playlist_id"], $_GET["musica_id"]);
        if ($rows > 0) {
            output(200, ["msg" => "Música removida da playlist"]);
        } else {
            output(404, ["msg" => "Música ou playlist não encontrada"]);
        }
    } else {
        output(405, ["msg" => "Método não permitido"]);
    }
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    output($statusCode, ["msg" => $e->getMessage()]);
}
