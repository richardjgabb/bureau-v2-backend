<?php

declare(strict_types=1);

namespace App\DTOs;

class ScoreboardDataDTO
{
    public array $scoreboard;

    public static function from(array $data): self
    {
        $dto = new self();
        $dto->scoreboard = self::buildScoresArray($data);

        return $dto;
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