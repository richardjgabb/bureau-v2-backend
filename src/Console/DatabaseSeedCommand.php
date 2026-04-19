<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PDO;

class DatabaseSeedCommand extends Command
{
    private $db;

    public function __construct(PDO $db)
    {
        // It's important to call parent constructor BEFORE setting the name
        // if you aren't using configure(), but using configure() is better.
        $this->db = $db;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:seed')
             ->setDescription('Seeds the database with initial data');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>Cleaning up database...</comment>');

        try {
            $output->writeln('<comment>Truncating tables...</comment>');
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');

            $tables = ['scores', 'games', 'players', 'pots', 'player_stats', 'player_game'];

            foreach ($tables as $table) {
                $output->writeln(" - Truncating <info>$table</info>");
                $this->db->exec("TRUNCATE TABLE `$table`");
            }

            $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');

            $output->writeln('<comment>Seeding fresh data...</comment>');

            $this->seedData();

            $output->writeln('<info>Database wiped and re-seeded successfully!</info>');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    private function seedData(): void
    {
        $this->seedPlayers();
        $this->seedGames();
        $this->seedPlayerGame();
        $this->seedPots();
        $this->seedScores();
        $this->seedStats();
    }

    private function seedPlayers(): void
    {
        $playerStmt = $this->db->prepare("INSERT INTO `players` (`id`, `name`)
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
        $playerStmt->execute();
    }

    private function seedGames(): void
    {
        $gameStmt = $this->db->prepare("INSERT INTO `games` (`id`, `name`, `buy_in`)
        VALUES
            (1, 'Ilkley', 100),
            (2, 'Nottingham', 20),
            (3, 'Manchester', 20);
        ");
        $gameStmt->execute();
    }

    private function seedPots(): void
    {
        $potStmt = $this->db->prepare("INSERT INTO `pots` (`id`, `game_id`, `round`, `pot`, `is_compuls`, `pot_winner`, `amount_of_bues`)
            VALUES
                (1, 2, 1, 80, 1, 8, 0),
                (2, 3, 1, 80, 1, 1, 0),
                (3, 3, 2, 80, 0, 1, 0);
            ");
        $potStmt->execute();
    }

    private function seedScores(): void
    {
        $scoreStmt = $this->db->prepare("INSERT INTO `scores` (`id`, `player_id`, `game_id`, `round`, `score`)
            VALUES
                (1, 5, 2, 1, -20),
                (2, 6, 2, 1, -20),
                (3, 7, 2, 1, -20),
                (4, 8, 2, 1, 60),
                (5, 1, 3, 1, 60),
                (6, 2, 3, 1, -20),
                (7, 3, 3, 1, -20),
                (8, 4, 3, 1, -20),
                (9, 1, 3, 2, 120),
                (10, 2, 3, 2, -40),
                (11, 3, 3, 2, -40),
                (12, 4, 3, 2, -40);
            ");
        $scoreStmt->execute();
    }

    private function seedStats(): void
    {
        $statStmt = $this->db->prepare("INSERT INTO `player_stats` (`id`, `wins`, `bues`, `compuls_wins`, `compuls_bues`, `wins_with_deal`, `bues_with_deal`, `player_id`, `game_id`)
            VALUES
                (1, 0, 0, 0, 0, 0, 0, 1, 1),
                (2, 0, 0, 0, 0, 0, 0, 2, 1),
                (3, 0, 0, 0, 0, 0, 0, 3, 1),
                (4, 0, 0, 0, 0, 0, 0, 4, 1),
                (13, 0, 0, 0, 0, 0, 0, 5, 2),
                (14, 0, 0, 0, 0, 0, 0, 6, 2),
                (15, 0, 0, 0, 0, 0, 0, 7, 2),
                (16, 1, 0, 1, 0, 0, 0, 8, 2),
                (17, 2, 0, 1, 0, 0, 0, 1, 3),
                (18, 0, 0, 0, 0, 0, 0, 2, 3),
                (19, 0, 0, 0, 0, 0, 0, 3, 3),
                (20, 0, 0, 0, 0, 0, 0, 4, 3);
            ");
        $statStmt->execute();
    }

    private function seedPlayerGame(): void
    {
        $pgStmt = $this->db->prepare("INSERT INTO `player_game` (`id`, `player_id`, `game_id`)
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
        $pgStmt->execute();
    }
}