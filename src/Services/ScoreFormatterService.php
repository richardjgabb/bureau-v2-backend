<?php

declare(strict_types=1);

namespace App\Services;

class ScoreFormatterService
{
    public function createAllPlayerScoresArray(array $scores): array
    {
        $playerScores = [];
        foreach ($scores as $score) {
            $playerId = $score->player_id;
            $playerName = $score->name;

            if (!isset($playerScores[$playerName])) {
                $playerScores[$playerName] = [
                    'playerId' => $playerId,
                    'playerName' => $playerName,
                    'scores' => [],
                ];
            }

            $playerScores[$playerName]['scores'][] = $score->getRoundAndScoreAndPot();
        }
        return $playerScores;
    }
}