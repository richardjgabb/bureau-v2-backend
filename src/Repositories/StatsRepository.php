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
            "SELECT
                            COUNT(p.id) AS `Total Hands`,
                            (SELECT COUNT(*) FROM players) AS `Total Players`,
                            CONCAT('£', FORMAT(SUM(p.pot)/100, 2)) AS `Total Pot`,
                            CONCAT('£', FORMAT(AVG(p.pot)/100, 2)) AS `Average Pot`,
                            COUNT(p.winner_id) AS `Pots Won`,
                            CONCAT('£', FORMAT(MAX(p.pot)/100, 2)) AS `Biggest Pot`,
                            SUM(p.is_compuls) AS `Compulsory Pots`,

                            -- Using MAX() to satisfy the only_full_group_by restriction
                            MAX(s.total_bues) AS `Total Bues`,
                            MAX(s.comp_bues) AS `Compulsory Bues`
                        FROM `pots` p
                        CROSS JOIN (
                            SELECT
                                SUM(bued) AS total_bues,
                                SUM(scores.bued * p_sub.is_compuls) AS comp_bues
                            FROM `scores`
                            INNER JOIN `pots` p_sub ON scores.pot_id = p_sub.id
                        ) s;
        ");

        $query->execute();
        return $query->fetch();
    }

    public function getGameStats(int $gameId): array
    {
        $query = $this->db->prepare(
            "SELECT
                        COALESCE(MAX(p.round), 0) AS `hands_played`,
                        COALESCE(MAX(pg.total_players), 0) AS `total_players`,
                        CONCAT('£', FORMAT(COALESCE(SUM(p.pot), 0) / 100, 2)) AS `total_pot`,
                        CONCAT('£', FORMAT(COALESCE(AVG(p.pot), 0) / 100, 2)) AS `average_pot`,
                        COUNT(p.winner_id) AS `wins`,
                        CONCAT('£', FORMAT(COALESCE(MAX(p.pot), 0) / 100, 2)) AS `biggest_pot`,
                        COALESCE(SUM(s.total_bues), 0) AS `bues`,
                        COALESCE(SUM(p.is_compuls), 0) AS `compuls_pots`,
                        COALESCE(SUM(CASE WHEN p.is_compuls = 1 THEN s.total_bues ELSE 0 END), 0) AS `compuls_bues`
                    FROM pots p
                    CROSS JOIN (
                        SELECT COUNT(id) AS total_players
                        FROM player_game
                        WHERE game_id = :gameId
                    ) pg
                    LEFT JOIN (
                        SELECT pot_id, COUNT(bued) AS total_bues
                        FROM scores
                        WHERE bued = 1
                        GROUP BY pot_id
                    ) s ON s.pot_id = p.id
                    WHERE p.game_id = :gameId;
        ");
        $query->execute(['gameId' => $gameId]);
        return $query->fetch();
    }

    public function getPlayerStatsForGame(int $playerId, int $gameId): array
    {
        $query = $this->db->prepare(
            "SELECT
                        SUM(CASE WHEN p.winner_id = s.player_id THEN 1 ELSE 0 END) AS `wins`,
                        SUM(s.bued) AS `bues`,
                        SUM(CASE WHEN p.is_compuls = 1 AND p.winner_id = s.player_id THEN 1 ELSE 0 END) AS `compuls_wins`,
                        SUM(CASE WHEN p.is_compuls = 1 AND s.bued = 1 THEN 1 ELSE 0 END) AS `compuls_bues`,
                        SUM(CASE WHEN p.dealer_id = s.player_id THEN 1 ELSE 0 END) AS `hands_dealt`,
                        SUM(CASE WHEN p.dealer_id = s.player_id AND p.winner_id = s.player_id THEN 1 ELSE 0 END) AS `wins_with_deal`,
                        SUM(CASE WHEN p.dealer_id = s.player_id AND s.bued = 1 THEN 1 ELSE 0 END) AS `bues_with_deal`

                    FROM scores s
                    INNER JOIN pots p ON s.pot_id = p.id
                    WHERE s.player_id = :player_id
                    AND p.game_id = :game_id;
        ");

        $query->bindParam(":player_id", $playerId);
        $query->bindParam(":game_id", $gameId);
        $query->execute();
        return $query->fetch();
    }

    public function getPlayerStats(int $playerId)
    {
        $query = $this->db->prepare(
            "SELECT
                        SUM(CASE WHEN p.winner_id = s.player_id THEN 1 ELSE 0 END) AS `wins`,
                        SUM(s.bued) AS `bues`,
                        SUM(CASE WHEN p.is_compuls = 1 AND p.winner_id = s.player_id THEN 1 ELSE 0 END) AS `compuls_wins`,
                        SUM(CASE WHEN p.is_compuls = 1 AND s.bued = 1 THEN 1 ELSE 0 END) AS `compuls_bues`,
                        SUM(CASE WHEN p.dealer_id = s.player_id THEN 1 ELSE 0 END) AS `hands_dealt`,
                        SUM(CASE WHEN p.dealer_id = s.player_id AND p.winner_id = s.player_id THEN 1 ELSE 0 END) AS `wins_with_deal`,
                        SUM(CASE WHEN p.dealer_id = s.player_id AND s.bued = 1 THEN 1 ELSE 0 END) AS `bues_with_deal`,
                        COUNT(*) AS `hands_played`,
                        (SELECT COUNT(DISTINCT game_id) FROM player_game WHERE player_id = :player_id) AS `games_played`,
                        (
                            SELECT MAX(p2.pot)
                            FROM pots p2
                            WHERE p2.winner_id = :player_id
                        ) AS `biggest_win`,
                        (
                            SELECT SUM(x.top_score)
                            FROM (
                                SELECT MAX(s2.score) AS top_score
                                FROM scores s2
                                JOIN pots p2 ON p2.id = s2.pot_id
                                WHERE s2.player_id = :player_id
                                GROUP BY p2.game_id
                            ) x
                        ) AS `total_score`,
                        (
                            SELECT pl.name
                            FROM players pl
                            WHERE pl.id = :player_id
                        ) AS `player_name`

                    FROM scores s
                    JOIN pots p ON p.id = s.pot_id
                    WHERE s.player_id = :player_id;
        ");

        $query->bindParam(":player_id", $playerId);
        $query->execute();
        return $query->fetch();
    }
}