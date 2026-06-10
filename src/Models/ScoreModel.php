<?php

declare(strict_types=1);

namespace App\Models;

class ScoreModel
{
    public int $id;
    public int $player_id;
    public string $game_id;
    public int $round;
    public int $score;
    public bool $isDealer;
    public bool $isCompuls;
    public bool $win;
    public bool $bued;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'player_id' => $this->player_id,
            'game_id' => $this->game_id,
            'round' => $this->round,
            'score' => $this->score,
            'isDealer' => $this->isDealer,
            'isCompuls' => $this->isCompuls,
            'win' => $this->win,
            'bued' => $this->bued
        ];
    }
}
