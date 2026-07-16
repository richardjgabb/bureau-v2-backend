<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ScoreModel;
use Exception;
use PDO;

class ScoreRepository {

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllScoresForGame($gameId): array
    {
        $query = $this->db->prepare(
            "SELECT scores.*
                      FROM `scores`
                     WHERE scores.`game_id` = :game_id
                  ORDER BY scores.`round` ASC
        ");

        $query->bindParam(":game_id", $gameId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, ScoreModel::class);
        return $query->fetchAll();
    }

    public function deleteRound(int $gameId, int $round): bool
    {
        try {
            $this->db->beginTransaction();

            $query1 = $this->db->prepare(
                "DELETE `scores` FROM `scores`
                         INNER JOIN `pots` ON `scores`.`pot_id` = `pots`.`id`
                              WHERE pots.`game_id` = :game_id
                                AND pots.`round` = :round"
            );
            $query1->execute([
                ":game_id" => $gameId,
                ":round" => $round
            ]);

            $query2 = $this->db->prepare(
                "DELETE FROM `pots`
                              WHERE `game_id` = :game_id
                                AND `round` = :round"
            );
            $query2->execute([
                ":game_id" => $gameId,
                ":round" => $round
            ]);

            return $this->db->commit();
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new Exception($e->getMessage());
        }
    }

    public function insertNewScore(int $playerId, int $potId, int $score, int $bued = 0): bool
    {
        $query = $this->db->prepare("
            INSERT INTO `scores` (`player_id`, `pot_id`, `score`, `bued`)
                 VALUES (:player_id, :pot_id, :score, :bued)
        ");

        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":pot_id", $potId);
        $query->bindParam(":score", $score);
        $query->bindParam(":bued", $bued);

        return $query->execute();
    }

    public function addPlayersScores(int $potId, array $scores, array $buedIds): bool
    {
        foreach ($scores as $playerId => $score) {
            if (!$this->insertNewScore($playerId, $potId, $score, in_array($playerId, $buedIds) ? 1 : 0)) {
                return false;
            }
        }

        return true;
    }

    public function getScoreboardScores(int $gameId)
    {
        $query = $this->db->prepare("
            SELECT `scores`.`player_id`,
                   `pots`.`round`,
                   `scores`.`score`,
                   `scores`.`bued`,
                   `pots`.`winner_id`,
                   `pots`.`pot`
              FROM `scores`
        LEFT JOIN `pots` ON `scores`.`pot_id` = `pots`.`id`
             WHERE `pots`.`game_id` = :game_id
          ORDER BY `pots`.`round` ASC, `scores`.`player_id` ASC
        ");

        $query->bindParam(":game_id", $gameId);
        $query->execute();

        return $query->fetchAll();
    }

    public function initiateScoresForAllPlayers(array $players, int $potId): bool
    {
        foreach ($players as $player) {
            if (!$this->insertNewScore($player['id'], $potId, $player['score'], 0)) {
                return false;
            }
        }

        return true;
    }

    public function getLatestScoreForPlayer(int $gameId, int $playerId)
    {
        $query = $this->db->prepare("
            SELECT `scores`.*
              FROM `scores`
        INNER JOIN `pots` ON `scores`.`pot_id` = `pots`.`id`
             WHERE `pots`.`game_id` = :game_id
               AND `scores`.`player_id` = :player_id
          ORDER BY `pots`.`round` DESC
             LIMIT 1
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":player_id", $playerId);
        $query->execute();
        return $query->fetch();
    }

    public function updatePlayerScoreForGame(int $gameId, int $playerId, int $score): bool
    {
        $query = $this->db->prepare("
            UPDATE `scores` s
        INNER JOIN `pots` pots ON s.`pot_id` = pots.`id`
               SET s.`score` = :score
             WHERE pots.`game_id` = :game_id
               AND s.`player_id` = :player_id
               AND pots.`round` = (
                   SELECT max_round
                     FROM (
                          SELECT MAX(p.`round`) AS max_round
                            FROM `pots` p
                      INNER JOIN `scores` s2 ON p.`id` = s2.`pot_id`
                           WHERE p.`game_id` = :game_id
                             AND s2.`player_id` = :player_id
                          ) AS temp_table
                   );
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":score", $score);

        return $query->execute();
    }
}
