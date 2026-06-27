<?php

declare(strict_types=1);

namespace App\DTOs;

class NewPlayerrDTO
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function from(string $name): self
    {
        return new self($name);
    }
}
