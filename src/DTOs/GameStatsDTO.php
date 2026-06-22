<?php

declare(strict_types=1);

namespace App\DTOs;

class GameStatsDTO
{
    public GameStatsGameDTO $gameStats;
    public array $playerStats;

    public function __construct(GameStatsGameDTO $gameStats, array $playerStats)
    {
        $this->gameStats = $gameStats;
        $this->playerStats = $playerStats;
    }

    public static function from(GameStatsGameDTO $gameStats, array $playerStats): self
    {
        return new self($gameStats, $playerStats);
    }

    public function serialize(): array
    {
        return [
            'gameStats' => $this->gameStats->toArray(),
            'playerStats' => $this->playerStats
        ];
    }
}
