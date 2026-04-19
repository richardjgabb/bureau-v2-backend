<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PotModel;
use Exception;
use PDO;

class PotRepository {

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllPotsForGame($gameId): array
    {
        $query = $this->db->prepare(
            "SELECT pots.* FROM `pots`
                 WHERE pots.`game_id` = :game_id
              ORDER BY pots.`round` ASC
        ");

        $query->bindParam(":game_id", $gameId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, PotModel::class);
        return $query->fetchAll();
    }

    public function handlePotWinner(int $gameId, int $round, int $winnerId, int $potSize): bool
    {
        $query = $this->db->prepare("
            INSERT INTO `pots` (`game_id`, `round`, `pot`, `pot_winner`)
               VALUES (:game_id, :round, :pot, :winner_id)
             WHERE `game_id` = :game_id
               AND `round` = :round
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":round", $round);
        $query->bindParam(":pot", $potSize);
        $query->bindParam(":winner_id", $winnerId);
        return $query->execute();
    }

    public function getCurrentPotSize(int $gameId, int $round): int
    {
        $query = $this->db->prepare("
            SELECT `pot`
              FROM `pots`
                 WHERE `game_id` = :game_id
                   AND `round` = :round
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":round", $round);
        $query->execute();

        return $query->fetchColumn();
    }

    public function handleSplit(int $gameId, int $round, int $newPot): bool
    {
        $query = $this->db->prepare("
            INSERT INTO `pots` (`game_id`, `round`, `pot`, `is_compuls`)
                 VALUES (:game_id, :round, :new_pot, 0);
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":round", $round);
        $query->bindParam(":new_pot", $newPot);

        return $query->execute();
    }
}