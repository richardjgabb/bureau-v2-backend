<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\Repositories\ScoreRepository;
use App\Services\ScoreFormatterService;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface;

class ScoresController
{
    private ScoreRepository $repository;
    private ScoreFormatterService $formatter;

    public function __construct(ScoreRepository $repository, ScoreFormatterService $formatter)
    {
        $this->repository = $repository;
        $this->formatter = $formatter;
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $scores = $this->repository->getAllScoresForGame($args['gameId']);
        $formattedScores = $this->formatter->createAllPlayerScoresArray($scores);
        $responseBody = [
            'message' => 'Successfully retrieved from db.',
            'status' => StatusCode::HTTP_OK,
            'data' => $formattedScores
        ];
        return $response->withJson($responseBody);
    }
}