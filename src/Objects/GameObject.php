<?php

declare(strict_types=1);

namespace App\Objects;

use JsonSerializable;

class GameObject implements JsonSerializable
{
    public readonly int $id;
    public string $name;
    public int $buyIn;
    private int $round;
    public ?array $players;
    public ?array $pots;

    public function __construct(int $id, string $name, int $buyIn, ?array $players = null, ?array $pots = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->buyIn = $buyIn;
        $this->players = $players;
        $this->pots = $pots;
        $this->round = count($pots);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'buyIn' => $this->buyIn,
            'round' => $this->round,
            'players' => $this->players,
            'pots' => $this->pots,
            'currentPotSize' => $this->computeLatestPotSize(),
        ];
    }

    private function computeLatestPotSize(): int
    {
        $latestPot = $this->pots[count($this->pots) - 1] ?? null;
        $potWasSplit = $latestPot->pot_winner === null;
        if (!$latestPot || ($latestPot && $latestPot->amountOfBues === 0 && $latestPot->pot_winner !== null)) {
            return 0;
        } elseif ($latestPot && $latestPot->pot_winner !== null) {
            return $latestPot->amountOfBues * $latestPot->pot;
        } elseif ($latestPot && $potWasSplit) {
            return $latestPot->pot + ($latestPot->amountOfBues * $latestPot->pot);
        }
        return $latestPot->pot;
    }
}