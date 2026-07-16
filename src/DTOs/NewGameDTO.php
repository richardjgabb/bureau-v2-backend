<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Objects\GameObject;

class NewGameDTO
{
    public int $id;
    public string $name;
    public int $buyIn;
    public array $players;

    public function __construct(GameObject $data)
    {
        $this->id = $data->id;
        $this->name = $data->name;
        $this->buyIn = $data->buyIn;
        $this->players = $data->players;
    }

    public static function from(GameObject $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'buy_in' => $this->buyIn,
            'current_pot' => 0,
            'players' => $this->players
        ];
    }
}
