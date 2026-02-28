<?php

declare(strict_types=1);

namespace App\Models;

class PlayerGameStatsModel
{
    public int $id;
    public int $wins;
    public int $bues;
    public int $compuls_wins;
    public int $compuls_bues;
    public int $wins_with_deal;
    public int $bues_with_deal;
    public ?int $hands_played;
    public int $player_id;
    public int $game_id;
    public ?int $biggest_pot;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'wins' => $this->wins,
            'bues' => $this->bues,
            'compuls_wins' => $this->compuls_wins,
            'compuls_bues' => $this->compuls_bues,
            'wins_with_deal' => $this->wins_with_deal,
            'bues_with_deal' => $this->bues_with_deal,
            'hands_played' => $this->hands_played,
            'player_id' => $this->player_id,
            'game_id' => $this->game_id,
            'biggest_pot' => $this->biggest_pot,
        ];
    }
}