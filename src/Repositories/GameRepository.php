<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GameModel;
use App\Objects\GameObject;
use PDO;

class GameRepository {

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getGameById($gameId): ?GameModel
    {
        $query = $this->db->prepare("SELECT * FROM `games` WHERE `id` = :game_id");
        $query->bindParam(":game_id", $gameId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, GameModel::class);
        $game = $query->fetch();
        return $game ?: null;
    }

    public function getAllGames(): array
    {
        $query = $this->db->prepare("
            SELECT games.`id`,
                   games.`name`,
                   games.`buy_in`,
                   (
                    SELECT pots.`pot`
                      FROM `pots`
                     WHERE pots.`game_id` = games.`id`
                  ORDER BY pots.`round` DESC
                     LIMIT 1
                    ) AS `current_pot`
              FROM `games`
        ");

        $query->execute();
        $games = $query->fetchAll();
        return $games;
    }

    private function addNewGame(string $name, int $buyIn): int
    {
        $query = $this->db->prepare("
            INSERT INTO `games` (`name`, `buy_in`) VALUES (:name, :buy_in)
        ");

        $query->bindParam(":name", $name);
        $query->bindParam(":buy_in", $buyIn);
        $query->execute();

        return (int) $this->db->lastInsertId();
    }

    private function linkPlayersToGame(int $gameId, array $players)
    {
        $query = $this->db->prepare("
            INSERT INTO `player_game` (`game_id`, `player_id`) VALUES (:game_id, :player_id)
        ");

        foreach ($players as $player) {
            $query->bindParam(":game_id", $gameId);
            $query->bindParam(":player_id", $player['id']);
            $query->execute();
        }
    }

    private function initiatePlayerStats(int $gameId, array $players)
    {
        $query = $this->db->prepare("
            INSERT INTO `player_stats` (`game_id`, `player_id`) VALUES (:game_id, :player_id)
        ");

        foreach ($players as $player) {
            $query->bindParam(":game_id", $gameId);
            $query->bindParam(":player_id", $player['id']);
            $query->execute();
        }
    }

    public function createNewGame(string $name, int $buyIn, array $players): GameObject
    {
        $gameId = $this->addNewGame($name, $buyIn);
        $this->linkPlayersToGame($gameId, $players);
        $this->initiatePlayerStats($gameId, $players);

        return new GameObject($gameId, $name, $buyIn, [], []);
    }
}