<?php

declare(strict_types=1);

namespace App\Objects;

class StoreRoundObject
{
    public readonly int $gameId;
    public readonly ?int $winnerId;
    public readonly ?int $dealerId;
    public readonly int $buyIn;
    public readonly int $round;
    public readonly array $buedIds;
    public readonly array $players;
    public readonly int $currentPotSize;

    public function __construct(
        array $roundData
    )
    {
        $this->gameId = (int) $roundData['id'];
        $this->winnerId = $roundData['potWinnerId'] ?? null;
        $this->dealerId = $roundData['dealerId'] ?? null;
        $this->buyIn = (int) $roundData['buyIn'];
        $this->round = (int) $roundData['round'];
        $this->buedIds = $roundData['buedIds'] ?? [];
        $this->players = $this->filterFrozenPlayers($roundData['players']) ?? [];
        $this->currentPotSize = (int) $roundData['currentPotSize'];
    }

    private function amountOfPlayers(): int {
        return count($this->players);
    }

    private function filterFrozenPlayers(array $players): array {
        return array_filter($players, fn ($player) => !$player['isFrozen']);
    }

    public function isSplit(): bool {
        return $this->winnerId === null;
    }

    public function hasNoBues(): bool {
        return $this->amountOfBues() === 0;
    }

    public function amountOfBues(): int {
        return count($this->buedIds);
    }

    public function playerScores(): array {
        return array_column($this->players, 'current_score', 'id');
    }

    public function compulsPotSize(): int {
        return $this->amountOfPlayers() * $this->buyIn;
    }

    public function isCompulsRound(): bool {
        return $this->currentPotSize <= $this->compulsPotSize();
    }

    public function calculateNewPotSize(int $potSize): int {
        return $potSize + $this->compulsPotSize() + ($this->amountOfBues() * $potSize);
    }
}