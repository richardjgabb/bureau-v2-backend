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

    public function addNewPot(int $gameId, int $round, ?int $winnerId, int $potSize, bool $isCompuls, ?int $dealerId, int $amountOfBues): bool
    {
        $query = $this->db->prepare("
            INSERT INTO `pots` (`game_id`, `round`, `pot`, `pot_winner`, `is_compuls`, `amount_of_bues`, `dealer_id`)
               VALUES (:game_id, :round, :pot, :winner_id, :is_compuls, :amount_of_bues, :dealer_id);
        ");

        $query->bindParam(":game_id", $gameId);
        $query->bindParam(":round", $round);
        $query->bindParam(":pot", $potSize);
        $query->bindParam(":winner_id", $winnerId);
        $query->bindParam(":is_compuls", $isCompuls);
        $query->bindParam(":amount_of_bues", $amountOfBues);
        $query->bindParam(":dealer_id", $dealerId);
        return $query->execute();
    }
}