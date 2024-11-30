<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Musica.php');
require_once(__DIR__ . '/model/Playlist.php'); // Precisamos acessar a Playlist

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
    // Validar o token JWT e obter o ID do usuário autenticado
    $userId = validateToken();

    $data = handleJSONInput();

    if (method("GET")) {
        if (isset($_GET['playlist_id'])) {
            // Verificar se a playlist pertence ao usuário autenticado
            $playlist = Playlist::getById($_GET['playlist_id']);
            if (!$playlist) {
                throw new Exception("Playlist não encontrada", 404);
            }
            if ($playlist['usuario_id'] != $userId) {
                throw new Exception("Acesso negado: você só pode acessar músicas das suas próprias playlists", 403);
            }
            $musicas = Musica::listarPorPlaylist($_GET['playlist_id']);
            output(200, ["musicas" => $musicas]);
        } else {
            throw new Exception("ID da playlist não fornecido", 400);
        }
    } elseif (method("POST")) {
        if (!valid($data, ["titulo", "link", "playlist_id"])) {
            throw new Exception("Título, link e ID da playlist são obrigatórios", 400);
        }

        // Verificar se a playlist pertence ao usuário autenticado
        $playlist = Playlist::getById($data["playlist_id"]);
        if (!$playlist) {
            throw new Exception("Playlist não encontrada", 404);
        }
        if ($playlist['usuario_id'] != $userId) {
            throw new Exception("Acesso negado: você só pode adicionar músicas às suas próprias playlists", 403);
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
    } elseif (method("PUT")) {
        if (!valid($_GET, ["musica_id"])) {
            throw new Exception("ID da música é obrigatório", 400);
        }

        if (!valid($data, ["titulo", "link"])) {
            throw new Exception("Título e link são obrigatórios", 400);
        }

        // Verificar se a música pertence a uma playlist do usuário
        $playlists = Musica::getPlaylistsByMusicaId($_GET["musica_id"]);
        $hasAccess = false;
        foreach ($playlists as $playlist) {
            if ($playlist['usuario_id'] == $userId) {
                $hasAccess = true;
                break;
            }
        }
        if (!$hasAccess) {
            throw new Exception("Acesso negado: você só pode atualizar músicas das suas próprias playlists", 403);
        }

        $artista = isset($data["artista"]) ? $data["artista"] : null;
        $plataforma = isset($data["plataforma"]) ? $data["plataforma"] : null;

        $rows = Musica::update($_GET["musica_id"], $data["titulo"], $artista, $data["link"], $plataforma);

        if ($rows > 0) {
            output(200, ["msg" => "Música atualizada com sucesso"]);
        } else {
            output(400, ["msg" => "Nenhuma alteração realizada"]);
        }
    } elseif (method("DELETE")) {
        if (!valid($_GET, ["playlist_id", "musica_id"])) {
            throw new Exception("IDs da playlist e da música são obrigatórios", 400);
        }

        // Verificar se a playlist pertence ao usuário autenticado
        $playlist = Playlist::getById($_GET["playlist_id"]);
        if (!$playlist) {
            throw new Exception("Playlist não encontrada", 404);
        }
        if ($playlist['usuario_id'] != $userId) {
            throw new Exception("Acesso negado: você só pode remover músicas das suas próprias playlists", 403);
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

/**
 * Função para validar o token JWT
 */
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

        // Retorna o ID do usuário decodificado do token
        return $decoded->data->userId;
    } catch (Exception $e) {
        throw new Exception("Token inválido: " . $e->getMessage(), 401);
    }
}
