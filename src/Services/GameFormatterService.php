<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GameModel;
use App\Objects\GameObject;
use App\Objects\PlayerStatObject;
use App\Objects\PotObject;

class GameFormatterService
{
    public function createGameArray(GameModel $game, array $players, array $pots): GameObject
    {
        return new GameObject(
            $game->id,
            $game->name,
            $game->buy_in,
            $this->createPlayersArray($players),
            $this->createGamePotsArray($pots)
        );
    }

    private function createGamePotsArray(array $pots): array
    {
        $potsArray = [];
        foreach ($pots as $entry) {
            $pot = new PotObject($entry->round, $entry->pot, $entry->pot_winner, $entry->amount_of_bues);
            $potsArray[] = $pot;
        }
        return $potsArray;
    }

    private function createPlayersArray(array $players): array
    {
        $playersArray = [];
        foreach ($players as $playerModel) {
            $playersArray[$playerModel->id] = $playerModel;
        }
        return $playersArray;
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