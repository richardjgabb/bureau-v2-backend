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
        $this->players = $roundData['players'] ?? [];
        $this->currentPotSize = (int) $roundData['currentPotSize'];
    }
}