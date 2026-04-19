<?php

declare(strict_types=1);

namespace App\Objects;

use JsonSerializable;

class PotObject implements JsonSerializable
{
    private int $round;
    public int $pot;
    public ?int $pot_winner;
    public int $amountOfBues;

    public function __construct(int $round, int $pot, ?int $pot_winner = null, ?int $amountOfBues = 0)
    {
        $this->round = $round;
        $this->pot = $pot;
        $this->pot_winner = $pot_winner;
        $this->amountOfBues = $amountOfBues;
    }

    public function jsonSerialize(): array
    {
        return [
            'round' => $this->round,
            'pot' => $this->pot,
            'winner' => $this->pot_winner,
            'amount_of_bues' => $this->amountOfBues
        ];
    }
}