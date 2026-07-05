<?php

namespace App\Test;

use App\Database\DatabaseSeeder;
use PDO;
use PHPUnit\Framework\TestCase;
use Slim\App;

abstract class BaseTestCase extends TestCase
{
    protected App $app;
    protected PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = createApp();
        $container = $this->app->getContainer();

        $this->db = $container->get(PDO::class);

        $this->resetDatabase();

        $container
            ->get(DatabaseSeeder::class)
            ->run($this->db);

        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        parent::tearDown();
    }

    private function resetDatabase(): void
    {
        $this->db->exec('SET FOREIGN_KEY_CHECKS=0');

        $this->db->exec('TRUNCATE TABLE players');
        $this->db->exec('TRUNCATE TABLE games');
        $this->db->exec('TRUNCATE TABLE player_game');
        $this->db->exec('TRUNCATE TABLE pots');
        $this->db->exec('TRUNCATE TABLE scores');

        $this->db->exec('SET FOREIGN_KEY_CHECKS=1');
    }
}