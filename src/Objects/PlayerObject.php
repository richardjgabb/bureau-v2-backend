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

    public function __construct(int $id, string $name, ?array $scores = null, ?array $stats = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->scores = $scores;
        $this->stats = $stats;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'scores' => $this->scores,
            'stats' => $this->stats,
        ];
    }
}