<?php

declare(strict_types=1);

namespace App\DTOs;

class AlltimeStatsDTO
{
    public int $total_hands;
    public int $player_count;
    public string $total_pot;
    public string $average_pot;
    public int $pots_won;
    public string $biggest_pot;
    public string $buggest_bue;
    public int $bues;
    public int $compuls_pots;
    public int $compuls_bues;
    public string $won_with_deal;


    public function __construct(array $data)
    {
        $this->total_hands = $data['total_hands'] ?? 0;
        $this->player_count = $data['total_players'] ?? 0;
        $this->total_pot = $data['total_pot'] ?? "£0.00";
        $this->average_pot = $data['average_pot'] ?? "£0.00";
        $this->pots_won = $data['wins'] ?? 0;
        $this->biggest_pot = $data['biggest_pot'] ?? "£0.00";
        $this->buggest_bue = $data['biggest_bue'] ?? "£0.00";
        $this->bues = (int) $data['bues'] ?? 0;
        $this->compuls_pots = (int) $data['compuls_pots'] ?? 0;
        $this->compuls_bues = (int) $data['compuls_bues'] ?? 0;
        $this->won_with_deal = $data['won_with_deal'] ?? "0%";
    }

    public static function from(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'Hands played' => $this->total_hands,
            'Total players' => $this->player_count,
            'Total pot' => $this->total_pot,
            'Average pot' => $this->average_pot,
            'Pots won' => $this->pots_won,
            'Biggest pot' => $this->biggest_pot,
            'Biggest bue' => $this->buggest_bue,
            'Bues' => $this->bues,
            'Compuls pots' => $this->compuls_pots,
            'Compuls bues' => $this->compuls_bues,
            'Pots won with deal' => $this->won_with_deal
        ];
    }
}
