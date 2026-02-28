<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GameModel;
use PDO;

class GameRepository {

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getGameById($gameId): ?GameModel
    {
        $query = $this->db->prepare("SELECT * FROM `games` WHERE `id` = :game_id");
        $query->bindParam(":game_id", $gameId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, GameModel::class);
        $game = $query->fetch();
        return $game ?: null;
    }

    public function getAllGames(): array
    {
        $query = $this->db->prepare("
            SELECT games.`id`,
                   games.`name`,
                   games.`buy_in`,
                   (
                    SELECT pots.`pot`
                      FROM `pots`
                     WHERE pots.`game_id` = games.`id`
                  ORDER BY pots.`round` DESC
                     LIMIT 1
                    ) AS `current_pot`
              FROM `games`
        ");

        $query->execute();
        $games = $query->fetchAll();
        return $games;
    }

    public function createNewGame(array $players)
    {
        $query = $this->db->prepare("
            INSERT INTO `games` (`name`, `buy_in`) VALUES (:name, :buy_in)
        ");
    }
}