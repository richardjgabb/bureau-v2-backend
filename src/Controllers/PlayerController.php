<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\DTOs\NewPlayerrDTO;
use App\DTOs\PlayerStatsDTO;
use App\Repositories\PlayerRepository;
use App\Repositories\StatsRepository;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface;

class PlayerController
{
    private PlayerRepository $repository;
    private StatsRepository $statsRepository;

    public function __construct(PlayerRepository $repository, StatsRepository $statsRepository)
    {
        $this->repository = $repository;
        $this->statsRepository = $statsRepository;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $players = $this->repository->getAllPlayers();
        $responseBody = [
            'message' => 'Successfully retrieved from db.',
            'status' => StatusCode::HTTP_OK,
            'data' => $players
        ];
        return $response->withJson($responseBody);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $newPlayer = NewPlayerrDTO::from($request->getParsedBody()['name']);

        try {
            $player = $this->repository->createNewPlayer($newPlayer->name);

            $responseBody = [
                'message' => 'Successfully stored in the db.',
                'status' => StatusCode::HTTP_CREATED,
                'data' => $player
            ];

            return $response->withJson($responseBody);
        } catch (Exception $e) {
            return $response->withStatus(StatusCode::HTTP_BAD_REQUEST)->withJson(['message' => 'Unable to create player']);
        }
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $playerStats = PlayerStatsDTO::from(
            $this->statsRepository->getPlayerStats((int) $args['playerId'])
        );

        $responseBody = [
            'message' => 'Successfully retrieved from db.',
            'status' => StatusCode::HTTP_OK,
            'data' => $playerStats->toArray()
        ];
        return $response->withJson($responseBody);
    }

    public function showPlayersNotInGame(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $gameId = (int) $args['gameId'];

        $players = $this->repository->getPlayersNotInGame($gameId);
        $responseBody = [
            'message' => 'Successfully retrieved from db.',
            'status' => StatusCode::HTTP_OK,
            'data' => $players
        ];
        return $response->withJson($responseBody);
    }
}