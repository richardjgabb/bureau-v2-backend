<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PlayerGameStatsModel;
use PDO;

class PlayerStatsRepository {

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getPlayerStatsForGame(string $playerId, string $gameId): array
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


    public function getAllPlayersStatsForGame(string $gameId): array
    {
        $query = $this->db->prepare(
            "WITH score_counts AS (
                        SELECT
                            player_id,
                            COUNT(DISTINCT `round`) AS hands_played
                        FROM scores
                        GROUP BY player_id
                    ),
                    pot_max AS (
                        SELECT
                            pot_winner AS player_id,
                            MAX(`pot`) AS biggest_pot
                        FROM pots
                        GROUP BY pot_winner
                    )
                    SELECT
                        ps.*,
                        sc.hands_played,
                        pm.biggest_pot
                    FROM player_stats ps
                    LEFT JOIN score_counts sc ON ps.player_id = sc.player_id
                    LEFT JOIN pot_max pm ON ps.player_id = pm.player_id
                    WHERE ps.game_id = :game_id;
        ");

        $query->bindParam(":game_id", $gameId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, PlayerGameStatsModel::class);
        return $query->fetchAll();
    }

    public function getPlayerStats(int $playerId)
    {
        $query = $this->db->prepare(
            "SELECT SUM(player_stats.`wins`) AS 'Pots Won',
                           (SELECT CONCAT('£', FORMAT(MAX(pots.`pot`)/100, 2)) FROM pots WHERE pots.`pot_winner` = :player_id) AS 'Highest Pot Won',
                           SUM(player_stats.`bues`) AS 'Bues',
                           SUM(player_stats.`compuls_wins`) AS 'Compulsory Pots Won',
                           SUM(player_stats.`compuls_bues`) AS 'Compulsory Pots Bued',
                           SUM(player_stats.`wins_with_deal`) AS 'Pots Won with Deal',
                           SUM(player_stats.`bues_with_deal`) AS 'Times Bued with Deal',
                           (SELECT COUNT(DISTINCT scores.`id`) FROM scores WHERE player_id = :player_id) AS `Hands Played`,
                           (SELECT CONCAT('£', FORMAT(SUM(scores.`score`)/100, 2)) FROM scores WHERE player_id = :player_id) AS `Total Score`,
                           COUNT(DISTINCT player_stats.`game_id`) AS `Games Played`,
                           (SELECT players.`name` FROM players WHERE players.`id` = :player_id) AS `Player Name`
                      FROM `player_stats`
                     WHERE player_stats.`player_id` = :player_id
        ");

        $query->bindParam(":player_id", $playerId);
        $query->execute();
        return $query->fetch();
    }
}