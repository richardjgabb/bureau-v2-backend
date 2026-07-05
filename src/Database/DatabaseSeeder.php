<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class DatabaseSeeder
{
    public function run(PDO $pdo): void
    {
        $this->seedPlayers($pdo);
        $this->seedGames($pdo);
        $this->seedPlayerGame($pdo);
        $this->seedPots($pdo);
        $this->seedScores($pdo);
    }

    private function seedPlayers(PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            "INSERT INTO `players` (`id`, `name`)
                        VALUES
                                (1, 'Rich'),
                                (2, 'Dan'),
                                (3, 'Sid'),
                                (4, 'Felts'),
                                (5, 'Titch'),
                                (6, 'Jr'),
                                (7, 'CJ'),
                                (8, 'Ruff'),
                                (9, 'Craze');
        ");

        $stmt->execute();
    }

    private function seedGames(PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            "INSERT INTO `games` (`id`, `name`, `buy_in`)
                        VALUES
                            (1, 'Ilkley', 100),
                            (2, 'Nottingham', 20),
                            (3, 'Manchester', 20);
            ");

        $stmt->execute();
    }

    private function seedPlayerGame(PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            "INSERT INTO `player_game` (`id`, `player_id`, `game_id`)
                        VALUES
                            (1, 1, 1),
                            (2, 2, 1),
                            (3, 3, 1),
                            (4, 4, 1),
                            (5, 5, 2),
                            (6, 6, 2),
                            (8, 7, 2),
                            (7, 8, 2),
                            (9, 1, 3),
                            (10, 2, 3),
                            (11, 3, 3),
                            (12, 4, 3);
        ");

        $stmt->execute();
    }

    private function seedPots(PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            "INSERT INTO `pots` (`id`, `game_id`, `round`, `pot`, `is_compuls`, `dealer_id`, `winner_id`)
                        VALUES
                            (1, 1, 0, 0, 0, NULL, NULL),
                            (2, 2, 0, 0, 0, NULL, NULL),
                            (3, 3, 0, 0, 0, NULL, NULL),
                            (4, 2, 1, 80, 1, NULL, 5),
                            (5, 3, 1, 80, 1, NULL, 1),
                            (6, 3, 2, 240, 0, NULL, 1);
        ");
        $stmt->execute();
    }

    private function seedScores(PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            "INSERT INTO `scores` (`id`, `player_id`, `pot_id`, `score`, `bued`)
                        VALUES
                            (1, 1, 1, 0, 0),
                            (2, 2, 1, 0, 0),
                            (3, 3, 1, 0, 0),
                            (4, 4, 1, 0, 0),
                            (5, 5, 2, 0, 0),
                            (6, 6, 2, 0, 0),
                            (7, 7, 2, 0, 0),
                            (8, 8, 2, 0, 0),
                            (9, 1, 3, 0, 0),
                            (10, 2, 3, 0, 0),
                            (11, 3, 3, 0, 0),
                            (12, 4, 3, 0, 0),
                            (13, 5, 4, -20, 0),
                            (14, 6, 4, -20, 0),
                            (15, 7, 4, -20, 1),
                            (16, 8, 4, 60, 1),
                            (17, 1, 5, 60, 0),
                            (18, 2, 5, -20, 0),
                            (19, 3, 5, -100, 1),
                            (20, 4, 5, -100, 1),
                            (21, 1, 6, 280, 0),
                            (22, 2, 6, -40, 0),
                            (23, 3, 6, -120, 0),
                            (24, 4, 6, -360, 1);
        ");
        $stmt->execute();
    }
}
