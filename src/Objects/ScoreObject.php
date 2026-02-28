<?php

declare(strict_types=1);

namespace App\Objects;

use JsonSerializable;

class ScoreObject implements JsonSerializable
{
    private int $round;
    private int $score;

    public function __construct(int $round, int $score)
    {
        $this->round = $round;
        $this->score = $score;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'round' => $this->round,
            'score' => $this->score
        ];
    }

    public function getScore(): int
    {
        return $this->score;
    }
}