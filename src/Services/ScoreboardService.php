<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ScoreboardDataDTO;
use App\Repositories\ScoreRepository;

class ScoreboardService
{
    private ScoreRepository $scoreRepository;

    public function __construct(ScoreRepository $scoreRepository)
    {
        $this->scoreRepository = $scoreRepository;
    }

    public function createScoreboardData(int $gameId): ScoreboardDataDTO
    {
        return ScoreboardDataDTO::from(
            $this->scoreRepository->getScoreboardScores($gameId)
        );
    }
}
