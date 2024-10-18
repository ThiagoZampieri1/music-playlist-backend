<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Musica.php');

header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    $data = handleJSONInput();

    // GET: Listar músicas de uma playlist
    if (method("GET")) {
        if (isset($_GET['playlist_id'])) {
            $musicas = Musica::listarPorPlaylist($_GET['playlist_id']);
            output(200, $musicas);
        } else {
            throw new Exception("ID da playlist não fornecido", 400);
        }
    }

    // POST: Adicionar música à playlist
    elseif (method("POST")) {
        if (!valid($data, ["titulo", "link", "playlist_id"])) {
            throw new Exception("Título, link e ID da playlist são obrigatórios", 400);
        }
        $artista = isset($data["artista"]) ? $data["artista"] : null;
        $plataforma = isset($data["plataforma"]) ? $data["plataforma"] : null;

        // Verificar se a música já existe
        $musica = Musica::getByTituloELink($data["titulo"], $data["link"]);
        if ($musica) {
            $musica_id = $musica['id'];
        } else {
            // Inserir nova música
            $musica_id = Musica::insert($data["titulo"], $artista, $data["link"], $plataforma);
        }

        // Adicionar música à playlist
        Musica::addMusicaNaPlaylist($data["playlist_id"], $musica_id);
        output(201, ["msg" => "Música adicionada à playlist", "id" => $musica_id]);
    }

    // DELETE: Remover música da playlist
    elseif (method("DELETE")) {
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
        // Método não permitido
        output(405, ["msg" => "Método não permitido"]);
    }
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    output($statusCode, ["msg" => $e->getMessage()]);
}
