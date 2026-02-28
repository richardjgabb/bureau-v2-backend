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
            "SELECT players.*
                      FROM `players`
                INNER JOIN `player_game` ON players.`id` = player_game.`player_id`
                     WHERE player_game.`game_id` = :game_id
        ");

        $query->bindParam(":game_id", $gameId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, PlayerModel::class);
        return $query->fetchAll();
    }
}