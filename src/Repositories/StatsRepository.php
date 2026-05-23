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
            "SELECT COALESCE(MAX(pots.`round`), 0) AS 'Hands Played',
                           (SELECT COUNT(player_game.`id`) FROM player_game WHERE player_game.`game_id` = :gameId) AS 'Total Players',
                           CONCAT('£', FORMAT(COALESCE(SUM(pots.`pot`), 0)/100, 2)) AS 'Total Pot',
                            CONCAT('£', FORMAT(COALESCE(AVG(pots.`pot`), 0)/100, 2)) AS 'Average Pot',
                           (SELECT SUM(player_stats.`wins`) FROM player_stats WHERE player_stats.`game_id` = :gameId) AS 'Pots Won',
                           CONCAT('£', FORMAT(COALESCE(MAX(pots.`pot`), 0)/100, 2)) AS 'Biggest Pot',
                           (SELECT SUM(player_stats.`bues`) FROM player_stats WHERE player_stats.`game_id` = :gameId) AS 'Total Bues',
                            COALESCE(SUM(pots.`is_compuls`), 0) AS 'Compulsory Pots',
                           (SELECT SUM(player_stats.`compuls_bues`) FROM player_stats WHERE player_stats.`game_id` = :gameId) AS 'Compulsory Bues'
                      FROM `pots`
                      WHERE pots.`game_id` = :gameId
        ");
        $query->execute(['gameId' => $gameId]);
        return $query->fetch();
    }

    public function getPlayerStatsForGame(int $playerId, int $gameId): array
    {
        $query = $this->db->prepare(
            "SELECT player_stats.wins AS `Pots won`,
                           player_stats.bues AS `Bues`,
                           player_stats.compuls_wins AS `Compuls pots won`,
                           player_stats.compuls_bues AS `Bues on compuls`,
                           player_stats.hands_dealt AS `Hands dealt`,
                           player_stats.wins_with_deal AS `Pots won with deal`,
                           player_stats.bues_with_deal AS `Bues with deal`
              FROM `player_stats`
        INNER JOIN `scores` ON player_stats.`player_id` = scores.`player_id`
             WHERE player_stats.`player_id` = :player_id
               AND player_stats.`game_id` = :game_id
          GROUP BY player_stats.id, player_stats.player_id, player_stats.game_id"
        );

        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":game_id", $gameId);
        $query->execute();
        return $query->fetch();
    }
}