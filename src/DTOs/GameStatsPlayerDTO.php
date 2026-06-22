<?php

declare(strict_types=1);

namespace App\DTOs;

class GameStatsPlayerDTO
{
    public int $wins;
    public int $bues;
    public int $compuls_wins;
    public int $compuls_bues;
    public int $hands_dealt;
    public int $wins_with_deal;
    public int $bues_with_deal;

    public function __construct(array $data)
    {
        $this->wins = $data['wins'] ?? 0;
        $this->bues = $data['bues'] ?? 0;
        $this->compuls_wins = $data['compuls_wins'] ?? 0;
        $this->compuls_bues = $data['compuls_bues'] ?? 0;
        $this->hands_dealt = $data['hands_dealt'] ?? 0;
        $this->wins_with_deal = $data['wins_with_deal'] ?? 0;
        $this->bues_with_deal = $data['bues_with_deal'] ?? 0;
    }

    public static function from(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'Wins' => $this->wins,
            'Bues' => $this->bues,
            'Compuls wins' => $this->compuls_wins,
            'Compuls bues' => $this->compuls_bues,
            'Hands dealt' => $this->hands_dealt,
            'Wins with deal' => $this->wins_with_deal,
            'Bues with deal' => $this->bues_with_deal,
        ];
    }
}
