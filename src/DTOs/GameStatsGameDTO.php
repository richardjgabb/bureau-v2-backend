<?php

declare(strict_types=1);

namespace App\DTOs;

class GameStatsGameDTO
{
    public int $hands_played;
    public int $player_count;
    public string $total_pot;
    public string $average_pot;
    public int $pots_won;
    public string $biggest_pot;
    public int $bues;
    public int $compuls_pots;
    public int $compuls_bues;


    public function __construct(array $data)
    {
        $this->hands_played = $data['hands_played'] ?? 0;
        $this->player_count = $data['total_playerst'] ?? 0;
        $this->total_pot = $data['total_pot'] ?? "£0.00";
        $this->average_pot = $data['average_pot'] ?? "£0.00";
        $this->pots_won = $data['wins'] ?? 0;
        $this->biggest_pot = $data['biggest_pot'] ?? "£0.00";
        $this->bues = (int) $data['bues'] ?? 0;
        $this->compuls_pots = (int) $data['compuls_pots'] ?? 0;
        $this->compuls_bues = (int) $data['compuls_bues'] ?? 0;
    }

    public static function from(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'Hands played' => $this->hands_played,
            'Total players' => $this->player_count,
            'Total pot' => $this->total_pot,
            'Average pot' => $this->average_pot,
            'Pots won' => $this->pots_won,
            'Biggest pot' => $this->biggest_pot,
            'Bues' => $this->bues,
            'Compuls pots' => $this->compuls_pots,
            'Compuls bues' => $this->compuls_bues,
        ];
    }
}
