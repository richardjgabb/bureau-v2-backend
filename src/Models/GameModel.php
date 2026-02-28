<?php

declare(strict_types=1);

namespace App\Models;

class GameModel
{
    public int $id;
    public string $name;
    public int $buy_in;


    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'buy_in' => $this->buy_in,
        ];
    }
}
