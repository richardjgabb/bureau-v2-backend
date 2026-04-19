<?php

declare(strict_types=1);

namespace App\Objects;

use JsonSerializable;

class PlayerStatObject implements JsonSerializable
{
    private int $wins;
    private int $bues;
    private int $compuls_wins;
    private int $compuls_bues;
    private int $wins_with_deal;
    private int $bues_with_deal;
    private ?int $hands_played;
    private ?int $biggest_pot;

    public function __construct(
        int $wins,
        int $bues,
        int $compuls_wins,
        int $compuls_bues,
        int $wins_with_deal,
        int $bues_with_deal,
        ?int $hands_played,
        ?int $biggest_pot
    )
    {
        $this->wins = $wins;
        $this->bues = $bues;
        $this->compuls_wins = $compuls_wins;
        $this->compuls_bues = $compuls_bues;
        $this->wins_with_deal = $wins_with_deal;
        $this->bues_with_deal = $bues_with_deal;
        $this->hands_played = $hands_played;
        $this->biggest_pot = $biggest_pot;
    }

    public function jsonSerialize(): array
    {
        return [
            'Wins' => $this->wins,
            'Bues' => $this->bues,
            'Compulsory Wins' => $this->compuls_wins,
            'Compulsory Bues' => $this->compuls_bues,
            'Wins with Deal' => $this->wins_with_deal,
            'Bues with Deal' => $this->bues_with_deal,
            'Hands played' => $this->hands_played ?? 0,
            'Largest Pot Won' => '£' . number_format($this->biggest_pot / 100, 2),
        ];
    }
}