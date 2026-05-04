<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\Repositories\StatsRepository;
use App\Services\StatsService;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface;

class StatsController
{
    private StatsRepository $repository;
    private StatsService $statsService;

    public function __construct(StatsRepository $repository, StatsService $statsService)
    {
        $this->repository = $repository;
        $this->statsService = $statsService;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $stats = $this->repository->getAllStats();
        $responseBody = [
            'message' => 'Successfully retrieved from db.',
            'status' => StatusCode::HTTP_OK,
            'data' => $stats
        ];
        return $response->withJson($responseBody);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $stats = $this->statsService->getAllStatsForGame((int) $args['gameId']);
            $responseBody = [
                'message' => 'Successfully retrieved from db.',
                'status' => StatusCode::HTTP_OK,
                'data' => $stats
            ];
        } catch (Exception $e) {
            $responseBody = [
                'message' => 'Error retrieving stats.',
                'status' => StatusCode::HTTP_BAD_REQUEST,
            ];
        }
        return $response->withJson($responseBody);
    }
}