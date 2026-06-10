<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\Repositories\StatsRepository;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface;

class PlayerStatsController
{
    private StatsRepository $statsRepository;

    public function __construct(StatsRepository $statsRepository)
    {
        $this->statsRepository = $statsRepository;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $playerStats = $this->statsRepository->getPlayerStatsForGame($args['playerId'], $args['gameId']);
        $responseBody = [
            'message' => 'Successfully retrieved from db.',
            'status' => StatusCode::HTTP_OK,
            'data' => $playerStats
        ];
        return $response->withJson($responseBody);
    }
}