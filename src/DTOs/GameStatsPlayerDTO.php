<?php

declare(strict_types=1);

namespace App\DTOs;

class GameStatsPlayerDTO
{
    public int $wins;
    public int $bues;
    public int $compuls_wins;
    public int $compuls_bues;
    public string $percent_compuls_bues;
    public int $hands_dealt;
    public int $wins_with_deal;
    public int $bues_with_deal;

    public function __construct(array $data)
    {
        $this->wins = (int) $data['wins'] ?? 0;
        $this->bues = (int) $data['bues'] ?? 0;
        $this->compuls_wins = (int) $data['compuls_wins'] ?? 0;
        $this->compuls_bues = (int) $data['compuls_bues'] ?? 0;
        $this->percent_compuls_bues = (string) $data['percent_compuls_bues'] ?? "0%";
        $this->hands_dealt = (int) $data['hands_dealt'] ?? 0;
        $this->wins_with_deal = (int) $data['wins_with_deal'] ?? 0;
        $this->bues_with_deal = (int) $data['bues_with_deal'] ?? 0;
    }

    public static function from(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'Wins' => $this->wins,
            'Bues' => $this->bues,
            'Compuls wins' => $this->compuls_wins,
            'Compuls bues' => $this->compuls_bues,
            '% bued on compuls' => $this->percent_compuls_bues,
            'Hands dealt' => $this->hands_dealt,
            'Wins with deal' => $this->wins_with_deal,
            'Bues with deal' => $this->bues_with_deal,
        ];
    }
}
