<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\Repositories\ScoreRepository;
use App\Services\ScoreFormatterService;
use Exception;
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

    public function store(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $gameId = $args['gameId'];
        //TODO: Implement storing a new round

        try {
            $responseBody = [
                'message' => 'Successfully stored in the db.',
                'status' => StatusCode::HTTP_CREATED,
                'data' => $gameId
            ];

            return $response->withJson($responseBody);
        } catch (Exception $e) {
            return $response->withStatus(StatusCode::HTTP_BAD_REQUEST);
        }
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $gameId = $args['gameId'];
        $round = $args['round'];
        //TODO: Implement deleting a round

        try {
            $responseBody = [
                'message' => 'Successfully deleted from db.',
                'status' => StatusCode::HTTP_OK,
                'data' => $gameId
            ];

            return $response->withJson($responseBody);
        } catch (Exception $e) {
            return $response->withStatus(StatusCode::HTTP_BAD_REQUEST);
        }
    }
}