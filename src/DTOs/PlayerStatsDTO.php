<?php

declare(strict_types=1);

namespace App\DTOs;

class PlayerStatsDTO
{
    public string $name;
    public int $wins;
    public int $bues;
    public int $compuls_wins;
    public int $compuls_bues;
    public int $hands_dealt;
    public int $wins_with_deal;
    public int $bues_with_deal;
    public int $hands_played;
    public int $games_played;
    public string $biggest_win;
    public string $total_score;

    public function __construct(array $data)
    {
        $this->name = $data['player_name'];
        $this->wins = (int) $data['wins'] ?? 0;
        $this->bues = (int) $data['bues'] ?? 0;
        $this->compuls_wins = (int) $data['compuls_wins'] ?? 0;
        $this->compuls_bues = (int) $data['compuls_bues'] ?? 0;
        $this->hands_dealt = (int) $data['hands_dealt'] ?? 0;
        $this->wins_with_deal = (int) $data['wins_with_deal'] ?? 0;
        $this->bues_with_deal = (int) $data['bues_with_deal'] ?? 0;
        $this->hands_played = (int) $data['hands_played'] ?? 0;
        $this->games_played = (int) $data['games_played'] ?? 0;
        $this->biggest_win = $data['biggest_win'] ? '£' . number_format($data['biggest_win'] / 100, 2) : '£0.00';
        $this->total_score = $data['total_score'] ? '£' . number_format($data['total_score'] / 100, 2) : '£0.00';
    }

    public static function from(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'Player Name' => $this->name,
            'Games played' => $this->games_played,
            'Hands played' => $this->hands_played,
            'Pots won' => $this->wins,
            'Bues' => $this->bues,
            'Compuls wins' => $this->compuls_wins,
            'Compuls bues' => $this->compuls_bues,
            'Wins with deal' => $this->wins_with_deal,
            'Bues with deal' => $this->bues_with_deal,
            'Hands dealt' => $this->hands_dealt,
            'Biggest win' => $this->biggest_win,
            'Total score' => $this->total_score
        ];
    }
}
