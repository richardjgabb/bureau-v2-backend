<?php

declare(strict_types=1);

namespace App\Models;

class PlayerModel
{
    public int $id;
    public string $name;
    public ?int $current_score;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'current_score' => $this->current_score ?? 0
        ];
    }
}