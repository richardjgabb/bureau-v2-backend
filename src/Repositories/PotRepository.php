<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PotModel;
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
}