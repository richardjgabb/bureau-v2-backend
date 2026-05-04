<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\Services\ScoreboardService;
use Exception;
use Psr\Http\Message\RequestInterface;
use Slim\Http\Interfaces\ResponseInterface;

class ScoreboardController
{
    private ScoreboardService $scoreService;

    public function __construct(ScoreboardService $scoreService)
    {
        $this->scoreService = $scoreService;
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $game = $this->scoreService->createScoreboardData((int) $args['gameId']);
            $responseBody = [
                'message' => 'Successfully retrieved from db.',
                'status' => StatusCode::HTTP_OK,
                'data' => $game->scoreboard
            ];
        } catch (Exception $e) {
            $responseBody = [
                'message' => $e->getMessage(),
                'status' => StatusCode::HTTP_BAD_REQUEST,
            ];
        }

        return $response->withJson($responseBody);
    }
}
