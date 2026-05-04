<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PlayerGameStatsModel;
use PDO;

class StatsRepository {

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllStats(): array
    {
        $query = $this->db->prepare(
            "SELECT COUNT(pots.`id`) AS 'Total Hands',
                           (SELECT COUNT(players.`id`) FROM players) AS 'Total Players',
                           CONCAT('£', FORMAT(SUM(pots.`pot`)/100, 2)) AS 'Total Pot',
                            CONCAT('£', FORMAT(AVG(pots.`pot`)/100, 2)) AS 'Average Pot',
                           (SELECT SUM(player_stats.`wins`) FROM player_stats) AS 'Pots Won',
                           CONCAT('£', FORMAT(MAX(pots.`pot`)/100, 2)) AS 'Biggest Pot',
                           (SELECT SUM(player_stats.`bues`) FROM player_stats) AS 'Total Bues',
                            SUM(pots.`is_compuls`) AS 'Compulsory Pots',
                           (SELECT SUM(player_stats.`compuls_bues`) FROM player_stats) AS 'Compulsory Bues'
                      FROM `pots`
        ");

        $query->execute();
        return $query->fetch();
    }

    public function getGameStats(int $gameId): array
    {
        $query = $this->db->prepare(
            "SELECT COUNT(pots.`id`) AS 'Total Hands',
                           (SELECT COUNT(players.`id`) FROM players) AS 'Total Players',
                           CONCAT('£', FORMAT(SUM(pots.`pot`)/100, 2)) AS 'Total Pot',
                            CONCAT('£', FORMAT(AVG(pots.`pot`)/100, 2)) AS 'Average Pot',
                           (SELECT SUM(player_stats.`wins`) FROM player_stats) AS 'Pots Won',
                           CONCAT('£', FORMAT(MAX(pots.`pot`)/100, 2)) AS 'Biggest Pot',
                           (SELECT SUM(player_stats.`bues`) FROM player_stats) AS 'Total Bues',
                            SUM(pots.`is_compuls`) AS 'Compulsory Pots',
                           (SELECT SUM(player_stats.`compuls_bues`) FROM player_stats) AS 'Compulsory Bues'
                      FROM `pots`
                      WHERE pots.`game_id` = :gameId
        ");
        $query->execute(['gameId' => $gameId]);
        return $query->fetch();
    }

    public function getPlayerStatsForGame(int $playerId, int $gameId): PlayerGameStatsModel
    {
        $query = $this->db->prepare(
            "SELECT player_stats.*,
                   COUNT(DISTINCT scores.`round`) AS `hands_played`
              FROM `player_stats`
        INNER JOIN `scores` ON player_stats.`player_id` = scores.`player_id`
             WHERE player_stats.`player_id` = :player_id
               AND player_stats.`game_id` = :game_id
          GROUP BY player_stats.id, player_stats.player_id, player_stats.game_id"
        );

        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":game_id", $gameId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, PlayerGameStatsModel::class);
        return $query->fetch();
    }
}