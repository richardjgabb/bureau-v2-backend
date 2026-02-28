<?php

declare(strict_types=1);

namespace App\Models;

class PlayerModel
{
    public int $id;
    public string $name;
    private ?array $scores;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'scores' => $this->scores,
        ];
    }
}