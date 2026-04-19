<?php

declare(strict_types=1);

namespace App\Objects;

use JsonSerializable;

class GameObject implements JsonSerializable
{
    public readonly int $id;
    private string $name;
    private int $buyIn;
    private int $round;
    private ?array $players;
    public ?array $pots;

    public function __construct(int $id, string $name, int $buyIn, ?array $players = null, ?array $pots = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->buyIn = $buyIn;
        $this->players = $players;
        $this->pots = $pots;
        $this->round = count($pots) + 1;
    }

    public function jsonSerialize(): array
    {
        $latestPot = $this->pots[count($this->pots) - 1] ?? null;
        if (!$latestPot || ($latestPot &&$latestPot->amountOfBues === 0 && $latestPot->pot_winner !== null)) {
            $currentPotSize = 0;
        } else {
            $currentPotSize = $latestPot->pot * $latestPot->amountOfBues;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'buyIn' => $this->buyIn,
            'round' => $this->round,
            'players' => $this->players,
            'pots' => $this->pots,
            'currentPotSize' => $currentPotSize
        ];
    }
}