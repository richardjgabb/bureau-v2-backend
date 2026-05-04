<?php

declare(strict_types=1);

namespace App\DTOs;

class ScoreboardDataDTO
{
    public array $scoreboard;

    public function __construct(array $data = [])
    {
        $this->scoreboard = self::buildScoresArray($data);
    }

    public static function from(array $data): self
    {
        return new self($data);
    }

    private static function buildScoresArray(array $data): array
    {
        $scoreboard = [];

        foreach ($data as $row) {
            $roundNum = $row['round'];

            if (!isset($scoreboard[$roundNum])) {
                $scoreboard[$roundNum] = [
                    'round'      => $roundNum,
                    'pot'        => $row['pot'],
                    'pot_winner' => $row['pot_winner'],
                    'scores'     => []
                ];
            }

            $scoreboard[$roundNum]['scores'][$row['player_id']] = $row['score'];
        }

        return array_values($scoreboard);
    }
}