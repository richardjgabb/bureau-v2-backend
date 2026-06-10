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
    public ?int $dealer_id;
    public ?int $winner_id;
    public int $bues;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'round' => $this->round,
            'pot' => $this->pot,
            'is_compuls' => $this->is_compuls,
            'dealer_id' => $this->dealer_id,
            'winner_id' => $this->winner_id,
            'bues' => $this->bues
        ];
    }
}
