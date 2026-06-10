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
                "DELETE FROM `scores`
                              WHERE `game_id` = :game_id
                                AND `round` = :round"
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
        } catch (Exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new Exception("Failed to delete round");
        }
    }

    private function insertNewScore(int $gameId, int $round, int $playerId, int $potId, int $score, bool $isDealer, bool $isCompuls, bool $win, bool $bued): bool
    {
        $query = $this->db->prepare("
            INSERT INTO `scores` (`game_id`, `round`, `player_id`, `pot_id`, `score`, `isDealer`, `isCompuls`, `win`, `bued`)
                 VALUES (:game_id, :round, :player_id, :score, :isDealer, :isCompuls, :win, :bued)
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":round", $round);
        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":pot_id", $potId);
        $query->bindParam(":score", $score);
        $query->bindParam(":isDealer", $isDealer);
        $query->bindParam(":isCompuls", $isCompuls);
        $query->bindParam(":win", $win);
        $query->bindParam(":bued", $bued);

        return $query->execute();
    }

    public function addPlayersScores(int $gameId, int $round, $potId, array $scores, int $dealerId, bool $isCompuls, int $winnerId, array $buedIds): bool
    {
        foreach ($scores as $playerId => $score) {
            if (!$this->insertNewScore($gameId, $round, $playerId, $potId, $score, $playerId === $dealerId, $isCompuls, $playerId === $winnerId, in_array($playerId, $buedIds))) {
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

    public function initiateScoresForAllPlayers(array $players, int $gameId): bool
    {
        foreach ($players as $player) {
            //TODO: Make Pot Id not 0
            if (!$this->insertNewScore($gameId, 0, $player['id'], 0, $player['score'], false, false, false, false)) {
                return false;
            }
        }

        return true;
    }

    public function updatePlayersScores(int $gameId, array $players, int $round): bool
    {
        foreach ($players as $player => $score) {
            $latestScore = $this->getLatestScoreForPlayer($gameId, $player);

            if (!$latestScore) {
                //TODO: Make Pot Id not 0
                $this->insertNewScore($gameId, $round, $player, 0, $score, false, false, false, false);
                continue;
            }

            if (!$this->updatePlayerScoreForGame($gameId, $player, $score)) {
                return false;
            }
        }

        return true;
    }

    private function getLatestScoreForPlayer(int $gameId, int $playerId)
    {
        $query = $this->db->prepare("
            SELECT `scores`.*
              FROM `scores`
             WHERE `scores`.`game_id` = :game_id
               AND `scores`.`player_id` = :player_id
          ORDER BY `scores`.`round` DESC
             LIMIT 1
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":player_id", $playerId);
        $query->execute();
        return $query->fetch();
    }

    private function updatePlayerScoreForGame(int $gameId, int $playerId, int $score): bool
    {
        $query = $this->db->prepare("
            UPDATE `scores` s
               SET s.`score` = :score
             WHERE s.`game_id` = :game_id
               AND s.`player_id` = :player_id;
               AND s.`round` = (SELECT MAX(`round`)
                                  FROM `scores`
                                 WHERE `game_id` = :game_id
                                   AND `player_id` = :player_id);
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":score", $score);

        return $query->execute();
    }
}
