<?php

declare(strict_types=1);

namespace App\Repositories;

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
            "SELECT COUNT(pots.`id`) AS 'Hands Played',
                           CONCAT('£', FORMAT(SUM(pots.`pot`)/100, 2)) AS 'Total Pot',
                            CONCAT('£', FORMAT(AVG(pots.`pot`)/100, 2)) AS 'Average Pot',
                           (SELECT COUNT(players.`id`) FROM players) AS 'Total Players',
                           (SELECT SUM(player_stats.`wins`) FROM player_stats) AS 'Pots Won',
                           (SELECT SUM(player_stats.`bues`) FROM player_stats) AS 'Bues',
                            SUM(pots.`is_compuls`) AS 'Compulsory Pots',
                           (SELECT SUM(player_stats.`compuls_bues`) FROM player_stats) AS 'Compulsory Bues'
                      FROM `pots`
        ");

        $query->execute();
        return $query->fetch();
    }
}