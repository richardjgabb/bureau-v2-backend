<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PlayerModel;
use PDO;

class PlayerRepository {

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllPlayers(): array
    {
        $query = $this->db->prepare("
            SELECT players.`id`,
                   players.`name`,
                   (SELECT SUM(`wins`) FROM `player_stats` WHERE player_stats.`player_id` = players.`id`) AS `wins`,
                   (SELECT SUM(`bues`) FROM `player_stats` WHERE player_stats.`player_id` = players.`id`) AS `bues`,
                   (SELECT COUNT(player_game.`id`)
                      FROM `player_game`
                     WHERE player_game.`player_id` = players.`id`
                    ) AS `games_played`
              FROM `players`
        ");

        $query->execute();
        return $query->fetchAll();
    }

    public function getAllPlayersForGame($gameId): array
    {
        $query = $this->db->prepare(
            "SELECT players.id,
                           players.name,
                           (SELECT scores.`score`
                              FROM scores
                             WHERE scores.`player_id` = players.`id`
                               AND scores.game_id = :game_id
                             ORDER BY scores.`round` DESC
                             LIMIT 1) AS current_score
                      FROM `players`
                INNER JOIN `player_game` ON players.`id` = player_game.`player_id`
                INNER JOIN `scores` ON players.`id` = scores.`player_id`
                     WHERE player_game.`game_id` = :game_id
                  GROUP BY players.`id`
        ");

        $query->bindParam(":game_id", $gameId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, PlayerModel::class);
        return $query->fetchAll();
    }

    public function getPlayerIdsForGame($gameId): array
    {
        $query = $this->db->prepare("
            SELECT player_game.`player_id`
              FROM `player_game`
             WHERE player_game.`game_id` = :game_id
             ORDER BY player_game.`player_id`
        ");

        $query->bindParam(":game_id", $gameId);
        $query->setFetchMode(PDO::FETCH_COLUMN, 0);
        $query->execute();
        return $query->fetchAll();
    }

    private function createNewPlayer(string $name): int
    {
        $query = $this->db->prepare("
            INSERT INTO `players` (`name`) VALUES (:name)
        ");

        $query->bindParam(":name", $name);
        $query->execute();

        return (int) $this->db->lastInsertId();
    }

    private function linkPlayerToGame(int $playerId, int $gameId): bool
    {
        $query = $this->db->prepare("
            INSERT INTO `player_game` (`player_id`, `game_id`) VALUES (:player_id, :game_id)
        ");

        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":game_id", $gameId);

        return $query->execute();
    }

    public function createNewPlayers(array $playersToCreate): array {
        $playersCreated = [];

        foreach ($playersToCreate as $player) {
            $newPlayerId = $this->createNewPlayer($player['name']);
            $playersCreated[] = [
                'id' => $newPlayerId,
                'name' => $player['name'],
                'score' => $player['score']
            ];
        }

        return $playersCreated;
    }

    public function linkPlayersToGame(array $players, int $gameId): bool
    {
        foreach ($players as $player) {
            $this->linkPlayerToGame($player['id'], $gameId);
        }

        return true;
    }
}