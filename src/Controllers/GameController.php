<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\Orchestrators\GameOrchestrator;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface;

class GameController
{
    private GameOrchestrator $orchestrator;

    public function __construct(GameOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $games = $this->orchestrator->getAllGames();
        $responseBody = [
            'message' => 'Successfully retrieved from db.',
            'status' => StatusCode::HTTP_OK,
            'data' => $games
        ];
        return $response->withJson($responseBody);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $game = $this->orchestrator->getGameData($args['gameId']);
        $responseBody = [
            'message' => 'Successfully retrieved from db.',
            'status' => StatusCode::HTTP_OK,
            'data' => $game
        ];
        return $response->withJson($responseBody);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $newGame = $this->orchestrator->createNewGame($data);
        $responseBody = [
            'message' => 'Game successfully created.',
            'status' => StatusCode::HTTP_CREATED,
            'data' => $newGame
        ];
        return $response->withJson($responseBody, StatusCode::HTTP_CREATED);
    }
}