<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Classes\StatusCode;
use App\Repositories\StatsRepository;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface;

class StatsController
{
    private StatsRepository $repository;

    public function __construct(StatsRepository $repository)
    {
        $this->repository = $repository;
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
}