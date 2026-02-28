<?php

declare(strict_types=1);

namespace App\Models;

class PotModel
{
    public int $id;
    public int $game_id;
    public int $round;
    public ?int $pot;
    public bool $is_compuls;
    public ?int $pot_winner;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'round' => $this->round,
            'pot' => $this->pot,
            'is_compuls' => $this->is_compuls,
            'pot_winner' => $this->pot_winner,
        ];
    }
}
