<?php

declare(strict_types=1);

namespace App\Objects;

use JsonSerializable;

class PlayerObject implements JsonSerializable
{
    private int $id;
    private string $name;
    private ?array $scores;
    private ?array $stats;
    private string $current_score;

    public function __construct(int $id, string $name, ?array $scores = null, ?array $stats = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->scores = $scores;
        $this->stats = $stats;
        $this->current_score = $this->getCurrentScore();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'scores' => $this->scores,
            'stats' => $this->stats,
            'current_score' => $this->current_score,
        ];
    }

    private function getCurrentScore(): ?string
    {
        $current_score = 0;
        if ($this->scores === null) {
            return null;
        }
        foreach ($this->scores ?? [] as $score) {
            $current_score += $score->getScore();
        }
        return '£' . number_format($current_score/100, 2);
    }
}