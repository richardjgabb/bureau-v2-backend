<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PlayerGameStatsModel;
use Exception;
use PDO;

class PlayerStatsRepository {

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllPlayersStatsForGame(string $gameId): array
    {
        $query = $this->db->prepare(
            "WITH score_counts AS (
                        SELECT
                            player_id,
                            COUNT(DISTINCT `round`) AS hands_played
                        FROM scores
                        WHERE `game_id` = :game_id
                        GROUP BY player_id
                    ),
                    pot_max AS (
                        SELECT
                            pot_winner AS player_id,
                            MAX(`pot`) AS biggest_pot
                        FROM pots
                        WHERE `game_id` = :game_id
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
                           (SELECT CONCAT('£', FORMAT(SUM(max_scores.top_score) / 100, 2))
                              FROM (
                                    SELECT MAX(score) AS top_score
                                    FROM scores
                                    WHERE player_id = :player_id
                                    GROUP BY game_id
                            ) AS max_scores
                        ) AS `Total Score`,
                           COUNT(DISTINCT player_stats.`game_id`) AS `Games Played`,
                           (SELECT players.`name` FROM players WHERE players.`id` = :player_id) AS `Player Name`
                      FROM `player_stats`
                     WHERE player_stats.`player_id` = :player_id
        ");

        $query->bindParam(":player_id", $playerId);
        $query->execute();
        return $query->fetch();
    }

    private function initiatePlayerStats(int $gameId, int $playerId): bool
    {
        $query = $this->db->prepare("
            INSERT INTO `player_stats` (`game_id`, `player_id`) VALUES (:game_id, :player_id)
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":player_id", $playerId);

        return $query->execute();
    }

    public function initiateStatsForAllPlayers(array $players, int $gameId): bool
    {
        foreach ($players as $player) {
            $this->initiatePlayerStats($gameId, $player['id']);
        }

        return true;
    }

    public function handleRoundStats(int $dealerId, int $potWinnerId, int $gameId, bool $isCompuls, array $bues): bool
    {
        $this->db->beginTransaction();
        try{
            $this->incrementHandsDealt($dealerId, $gameId);
            if ($potWinnerId) {
                $winnerIsDealer = $potWinnerId === $dealerId;
                $this->updateRoundWinnerStats($potWinnerId, $gameId, $isCompuls, $winnerIsDealer);
            }
            if (count($bues) > 0) {
                $this->updateRoundBuedStats($bues, $gameId, $isCompuls);
                if (in_array($dealerId, $bues)) {
                    $this->updateBuedWithDealStat($dealerId, $gameId);
                }
            }
        } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
        }
        return $this->db->commit();
    }

    private function incrementHandsDealt(int $dealerId, int $gameId): bool
    {
        $query = $this->db->prepare("
            UPDATE `player_stats`
               SET `hands_dealt` = `hands_dealt` + 1
             WHERE `player_id` = :id
               AND `game_id` = :game_id
        ");

        $query->bindParam(":id", $dealerId);
        $query->bindParam(":game_id", $gameId);
        return $query->execute();
    }

    private function updateRoundWinnerStats(int $potWinnerId, int $gameId, bool $isCompuls, bool $isDealer): bool
    {
        $query = $this->db->prepare("
            UPDATE `player_stats`
               SET `wins` = `wins` + 1" .
       ($isCompuls ? ", `compuls_wins` = `compuls_wins` + 1" : "") .
        ($isDealer ? ", `wins_with_deal` = `wins_with_deal` + 1" : "") .
           " WHERE `player_id` = :winner_id
               AND `game_id` = :game_id
        ");

        $query->bindParam(":winner_id", $potWinnerId);
        $query->bindParam(":game_id", $gameId);
        return $query->execute();
    }

    private function updateRoundBuedStats(array $bues, int $gameId, bool $isCompuls): bool
{
    if (empty($bues)) {
        return true;
    }

    // Protecting against SQL injection
    $placeholders = implode(',', array_fill(0, count($bues), '?'));

    $sql = "
        UPDATE `player_stats`
           SET `bues` = `bues` + 1" .
   ($isCompuls ? ", `compuls_bues` = `compuls_bues` + 1" : "") .
       " WHERE `player_id` IN ($placeholders)
           AND `game_id` = ?
    ";

    $query = $this->db->prepare($sql);
    $params = array_values($bues);
    $params[] = $gameId;

    return $query->execute($params);
}

    private function updateBuedWithDealStat(int $playerId, int $gameId): bool
    {
        $query = $this->db->prepare("
            UPDATE `player_stats`
               SET `bues_with_deal` = `bues_with_deal` + 1
             WHERE `player_id` = :player_id
               AND `game_id` = :game_id
        ");

        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":game_id", $gameId);
        return $query->execute();
    }
}