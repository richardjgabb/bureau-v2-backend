<?php

declare(strict_types=1);

namespace App\Objects;

class StoreRoundObject
{
    public readonly int $gameId;
    public readonly int $winnerId;
    public readonly int $dealerId;
    public readonly int $buyIn;
    public readonly int $round;
    public readonly ?array $buedIds;
    public readonly array $livePlayers;
    public readonly int $currentPotSize;

    public function __construct(
        array $roundData
    )
    {
        $this->gameId = $roundData['id'];
        $this->winnerId = $roundData['potWinnerId'] ?? 0;
        $this->dealerId = $roundData['dealerId'] ?? 0;
        $this->buyIn = $roundData['buyIn'];
        $this->round = $roundData['round'];
        $this->buedIds = $roundData['buedIds'] ?? null;
        $this->livePlayers = array_keys(
            array_filter($roundData['players'], fn($player) => $player['isLive'] ?? true)
        );
        $this->currentPotSize = $roundData['currentPotSize'];
    }
}