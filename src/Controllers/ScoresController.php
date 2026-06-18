<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\Events\RoundCompletedEvent;
use App\Objects\StoreRoundObject;
use App\Repositories\ScoreRepository;
use App\Services\ScoreFormatterService;
use App\Services\ScoreService;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ScoresController
{
    private ScoreRepository $repository;
    private ScoreFormatterService $formatter;
    private EventDispatcher $eventDispatcher;
    private ScoreService $service;

    public function __construct(ScoreRepository $repository, ScoreFormatterService $formatter, EventDispatcher $eventDispatcher, ScoreService $service)
    {
        $this->repository = $repository;
        $this->formatter = $formatter;
        $this->eventDispatcher = $eventDispatcher;
        $this->service = $service;
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
        $gameId = (int) $args['gameId'];

        try {
            $roundData = new StoreRoundObject($request->getParsedBody());

            $this->service->storeRound($roundData);

            $responseBody = [
                'message' => 'Successfully stored in the db.',
                'status' => StatusCode::HTTP_CREATED,
                'data' => $gameId
            ];

            return $response->withJson($responseBody);
        } catch (Exception $e) {
            return $response->withStatus(StatusCode::HTTP_BAD_REQUEST)->withJson(['message' => $e->getMessage()]);
        }
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $gameId = (int) $args['gameId'];
        $round = (int) $args['round'];

        //TODO: FIX UNDO
        try {
            $this->repository->deleteRound($gameId, $round);

            $responseBody = [
                'message' => 'Round successfully deleted from db.',
                'status' => StatusCode::HTTP_OK,
            ];

            return $response->withJson($responseBody);
        } catch (Exception $e) {
            return $response->withStatus(StatusCode::HTTP_BAD_REQUEST);
        }
    }
}