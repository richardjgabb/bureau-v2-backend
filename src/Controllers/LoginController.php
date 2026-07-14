<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Firebase\JWT\JWT;

class LoginController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if ($username === $_ENV['JWT_USERNAME'] && $password === $_ENV['JWT_PASSWORD']) {
            $secretKey = $_ENV['JWT_SECRET_KEY'];
            $payload = [
                'exp' => time() + 86400,
                'sub' => 1
            ];

            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            $response->getBody()->write(json_encode(['token' => $jwt]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
}
