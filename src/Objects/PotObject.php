<?php

declare(strict_types=1);

namespace App\Objects;

use JsonSerializable;

class PotObject implements JsonSerializable
{
    private int $round;
    private int $pot;
    private ?int $pot_winner;

    public function __construct(int $round, int $pot, ?int $pot_winner = null)
    {
        $this->round = $round;
        $this->pot = $pot;
        $this->pot_winner = $pot_winner;
    }

    public function jsonSerialize(): array
    {
        return [
            'round' => $this->round,
            'pot' => '£' . number_format($this->pot / 100, 2),
            'winner' => $this->pot_winner,
        ];
    }
}