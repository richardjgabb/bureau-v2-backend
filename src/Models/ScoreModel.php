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
    public bool $frozen;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'player_id' => $this->player_id,
            'game_id' => $this->game_id,
            'round' => $this->round,
            'score' => $this->score,
            'frozen' => $this->frozen,
        ];
    }
}
