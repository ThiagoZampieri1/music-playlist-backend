<?php
require_once(__DIR__ . '/configs/utils.php');
require_once(__DIR__ . '/model/Usuario.php');
require_once(__DIR__ . '/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $data = handleJSONInput();

    if (method("POST")) {
        if (!isset($_GET['action'])) {
            throw new Exception("Ação não especificada", 400);
        }

        switch ($_GET['action']) {
            case 'login':
                if (!valid($data, ["email", "senha"])) {
                    throw new Exception("Email e senha são obrigatórios", 400);
                }

                $usuario = Usuario::getByEmail($data["email"]);

                if ($usuario && password_verify($data["senha"], $usuario["senha"])) {
                    
                    $token = generateToken($usuario);

                    output(200, [
                        "msg" => "Login realizado com sucesso",
                        "token" => $token,
                        "usuario" => [
                            "id" => $usuario["id"],
                            "nome" => $usuario["nome"],
                            "email" => $usuario["email"]
                        ]
                    ]);
                } else {
                    output(401, ["msg" => "Email ou senha inválidos 2"]);
                }
                break;

            case 'logout':
                output(200, ["msg" => "Logout realizado com sucesso"]);
                break;

            default:
                throw new Exception("Ação inválida", 400);
        }
    } else {
        output(405, ["msg" => "Método não permitido"]);
    }
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    output($statusCode, ["msg" => $e->getMessage()]);
}

function generateToken($usuario)
{
    $secretKey = $_ENV['JWT_SECRET'];
    if (!$secretKey || !is_string($secretKey)) {
        throw new Exception("Chave secreta não definida ou inválida", 500);
    }
    $issuer = 'https://pw2.guilopes.com.br'; 
    $audience = 'https://pw2.guilopes.com.br';
    $issuedAt = time();
    $notBefore = $issuedAt;
    $expire = $issuedAt + (2 * 60 * 60);

    $payload = [
        'iat' => $issuedAt,
        'iss' => $issuer,
        'nbf' => $notBefore,
        'exp' => $expire,
        'aud' => $audience,
        'data' => [
            'userId' => $usuario['id'],
            'userName' => $usuario["nome"],
            "userEmail" => $usuario["email"]
        ]
    ];

    $jwt = JWT::encode($payload, $secretKey, 'HS256');
    return $jwt;
}
