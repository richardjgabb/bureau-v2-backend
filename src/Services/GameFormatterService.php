<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GameModel;
use App\Objects\GameObject;
use App\Objects\PlayerObject;
use App\Objects\PlayerStatObject;
use App\Objects\PotObject;
use App\Objects\ScoreObject;

class GameFormatterService
{
    public function createGameArray(GameModel $game, array $players, array $scores, array $playerStats, array $pots): GameObject
    {
        return new GameObject(
            $game->id,
            $game->name,
            $this->createPlayersArray($players, $playerStats, $scores),
            $this->createGamePotsArray($pots)
        );
    }

    private function createGamePotsArray(array $pots): array
    {
        $potsArray = [];
        foreach ($pots as $entry) {
            $pot = new PotObject($entry->round, $entry->pot);
            $potsArray[] = $pot;
        }
        return $potsArray;
    }

    private function createPlayersArray(array $players, array $playerStats, array $scores): array
    {
        $playersArray = [];
        foreach ($players as $playerModel) {
            $playerObject = new PlayerObject(
                $playerModel->id,
                $playerModel->name,
                $this->createPlayerScoresArray($scores, $playerModel->id),
                $this->createPlayerStatsArray($playerStats, $playerModel->id)
            );
            $playersArray[] = $playerObject;
        }
        return $playersArray;
    }

    private function createPlayerScoresArray(array $scores, int $playerId): array
    {
        $scoresArray = [];
        foreach ($scores as $scoreEntry) {
            if ($scoreEntry->player_id === $playerId) {
                $scoreObject = new ScoreObject(
                    $scoreEntry->round,
                    $scoreEntry->score
                );
                $scoresArray[] = $scoreObject;
            }
        }
        return $scoresArray;
    }

    private function createPlayerStatsArray(array $stats, int $playerId): array
    {
        $statsArray = [];
        foreach ($stats as $statEntry) {
            if ($statEntry->player_id === $playerId) {
                $statObject = new PlayerStatObject(
                    $statEntry->wins,
                    $statEntry->bues,
                    $statEntry->compuls_wins,
                    $statEntry->compuls_bues,
                    $statEntry->wins_with_deal,
                    $statEntry->bues_with_deal,
                    $statEntry->hands_played,
                    $statEntry->biggest_pot
                );
                $statsArray[] = $statObject;
            }
        }
        return $statsArray;
    }
}