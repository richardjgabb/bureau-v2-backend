<?php

declare(strict_types=1);

namespace App\DTOs;

class GameEditDTO
{
    public int $id;
    public string $name;
    public int $buyIn;
    public array $players;
    public int $round;

    public function __construct(int $id, array $data)
    {
        $this->id = $id;
        $this->name = $data['name'];
        $this->buyIn = $data['buyIn'];
        $this->players = $data['players'];
        $this->round = $data['round'];
    }


    public static function from(int $id, array $data): self
    {
        return new self($id, $data);
    }
}
