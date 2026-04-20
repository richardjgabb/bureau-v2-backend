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
    public readonly ?array $buedIds;
    public readonly array $livePlayers;
    public readonly int $currentPotSize;
    public readonly array $playerScores;

    public function __construct(
        array $roundData
    )
    {
        $this->gameId = $roundData['id'];
        $this->winnerId = $roundData['potWinnerId'] ?? null;
        $this->dealerId = $roundData['dealerId'] ?? null;
        $this->buyIn = $roundData['buyIn'];
        $this->round = $roundData['round'];
        $this->buedIds = $roundData['buedIds'] ?? null;
        $this->livePlayers = array_keys(
            array_filter($roundData['players'], fn($player) => $player['isLive'] ?? true)
        );
        $this->currentPotSize = $roundData['currentPotSize'];
        $this->playerScores = array_column($roundData['players'], 'current_score', 'id');
    }
}