<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\GameStatsDTO;
use App\Repositories\PlayerRepository;
use App\Repositories\StatsRepository;

class StatsService
{
    private StatsRepository $statsRepository;
    private PlayerRepository $playerRepository;

    public function __construct(StatsRepository $statsRepository, PlayerRepository $playerRepository)
    {
        $this->statsRepository = $statsRepository;
        $this->playerRepository = $playerRepository;
    }

    public function getAllStatsForGame(int $gameId): GameStatsDTO
    {
        $playerIds =  $this->playerRepository->getPlayerIdsForGame($gameId);

        $playerStats = [];
        foreach ($playerIds as $playerId) {
            $playerStats[$playerId] = $this->statsRepository->getPlayerStatsForGame($playerId, $gameId);
        }

        $gameStats = $this->statsRepository->getGameStats($gameId);

        return new GameStatsDTO($gameStats, $playerStats);
    }
}
