<?php

declare(strict_types=1);

namespace App\Objects;

use JsonSerializable;

class GameObject implements JsonSerializable
{
    private int $id;
    private string $name;
    private int $buyIn;
    private ?array $players;
    private ?array $pots;

    public function __construct(int $id, string $name, int $buyIn, ?array $players = null, ?array $pots = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->buyIn = $buyIn;
        $this->players = $players;
        $this->pots = $pots;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'buyIn' => $this->buyIn,
            'players' => $this->players,
            'pots' => $this->pots,
        ];
    }
}