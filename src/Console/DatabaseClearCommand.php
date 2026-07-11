<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PDO;

class DatabaseClearCommand extends Command
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
        $this->setName('db:clear')
             ->setDescription('Clears the database data');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>Cleaning up database...</comment>');

        try {
            $output->writeln('<comment>Truncating tables...</comment>');
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');

            $tables = ['scores', 'games', 'players', 'pots', 'player_game'];

            foreach ($tables as $table) {
                $output->writeln(" - Truncating <info>$table</info>");
                $this->db->exec("TRUNCATE TABLE `$table`");
            }

            $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');

            $output->writeln('<info>Database wiped successfully!</info>');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
