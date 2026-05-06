<?php

declare(strict_types=1);

namespace App\DTOs;

class GameStatsDTO
{
    public array $gameStats;
    public array $playerStats;

    public function __construct(array $gameStats, array $playerStats)
    {
        $this->gameStats = $gameStats;
        $this->playerStats = $playerStats;
    }

    public static function from(array $gameStats, array $playerStats): self
    {
        return new self($gameStats, $playerStats);
    }
}
