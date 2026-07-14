<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class JwtMiddleware {
    public function __invoke(Request $request, Handler $handler): ResponseInterface {
        if ($request->getMethod() === 'OPTIONS') {
            return $handler->handle($request);
        }

        $authorization = $request->getHeaderLine('Authorization');

        if (empty($authorization) || !preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized Access']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $token = $matches[1];
            $secretKey = $_ENV['JWT_SECRET_KEY'];
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Pass decoded token data to the route
            $request = $request->withAttribute('user', $decoded);
        } catch (\Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $handler->handle($request);
    }
}