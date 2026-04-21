<?php

declare(strict_types=1);

namespace App\Events;

use App\Objects\StoreRoundObject;
use Symfony\Contracts\EventDispatcher\Event;

class RoundCompletedEvent extends Event {
    public const NAME = 'round.completed';

    public readonly int $gameId;
    public readonly int $round;
    public readonly ?int $winnerId;
    public readonly ?int $dealerId;
    public readonly int $buyIn;
    public readonly array $buedIds;
    public readonly array $players;
    public readonly int $currentPotSize;

    public function __construct(
        public readonly StoreRoundObject $roundData
    ) {
        $this->gameId = $roundData->gameId;
        $this->round = $roundData->round;
        $this->winnerId = $roundData->winnerId ?? null;
        $this->dealerId = $roundData->dealerId ?? null;
        $this->buyIn = $roundData->buyIn;
        $this->buedIds = $roundData->buedIds ?? [];
        $this->currentPotSize = $roundData->currentPotSize;
        $this->players = $roundData->players ?? [];
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

    private function amountOfPlayers(): int {
        return count($this->players);
    }

    private function livePlayers(): array {
        return array_values(
            array_filter($this->players, fn($player) => $player['isLive'] ?? true)
        );
    }

    public function playerScores(): array {
        return array_column($this->livePlayers(), 'current_score', 'id');
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