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

    private function insertNewScore(int $gameId, int $round, int $playerId, int $score): bool
    {
        $query = $this->db->prepare("
            INSERT INTO `scores` (`game_id`, `round`, `player_id`, `score`) VALUES (:game_id, :round, :player_id, :score)
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":round", $round);
        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":score", $score);

        return $query->execute();
    }

    public function addPlayersScores(int $gameId, int $round, array $scores): bool
    {
        foreach ($scores as $playerId => $score) {
            if (!$this->insertNewScore($gameId, $round, $playerId, $score)) {
                return false;
            }
        }

        return true;
    }
}