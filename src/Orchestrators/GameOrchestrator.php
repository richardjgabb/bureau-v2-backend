<?php

declare(strict_types=1);

namespace App\Orchestrators;

use App\Exceptions\GameNotFoundException;
use App\Objects\GameObject;
use App\Repositories\GameRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\PlayerStatsRepository;
use App\Repositories\PotRepository;
use App\Repositories\ScoreRepository;
use App\Services\GameFormatterService;

class GameOrchestrator
{
    private GameRepository $gameRepository;
    private PlayerStatsRepository $playerStatsRepository;
    private PlayerRepository $playerRepository;
    private GameFormatterService $formatter;
    private PotRepository $potRepository;
    private ScoreRepository $scoreRepository;

    public function __construct(
        GameRepository $gameRepository,
        PlayerStatsRepository $playerStatsRepository,
        PlayerRepository $playerRepository,
        GameFormatterService $formatter,
        PotRepository $potRepository,
        ScoreRepository $scoreRepository
    ) {
        $this->gameRepository = $gameRepository;
        $this->playerStatsRepository = $playerStatsRepository;
        $this->playerRepository = $playerRepository;
        $this->formatter = $formatter;
        $this->potRepository = $potRepository;
        $this->scoreRepository = $scoreRepository;
    }

    public function getGameData(string $gameId): GameObject
    {
        $game = $this->gameRepository->getGameById($gameId);
        if ($game === null) {
            throw new GameNotFoundException($gameId);
        }
        $players = $this->playerRepository->getAllPlayersForGame($gameId);
        $pots = $this->potRepository->getAllPotsForGame($gameId);
        $formattedGame = $this->formatter->createGameArray($game, $players, $pots);

        return $formattedGame;
    }

    public function getAllGames(): array
    {
        return $this->gameRepository->getAllGames();
    }

    public function createNewGame(array $data): GameObject
    {
        $newPlayers = array_filter($data['players'], function ($player) {
            return $player['id'] === null;
        });
        $existingPlayers = array_filter($data['players'], function ($player) {
            return $player['id'] !== null;
        });

        if (count($newPlayers) > 0) {
            $createdPlayers = $this->playerRepository->createNewPlayers($newPlayers);
            $players = array_merge($existingPlayers, $createdPlayers);
        }

        $gameId = $this->gameRepository->createNewGame($data['gameName'], (int) $data['buyIn']);

        $this->playerRepository->linkPlayersToGame($players ?? $existingPlayers, $gameId);
        $this->scoreRepository->initiateScoresForAllPlayers($players ?? $existingPlayers, $gameId);
        $this->playerStatsRepository->initiateStatsForAllPlayers($players ?? $existingPlayers, $gameId);

        return $this->getGameData((string) $gameId);
    }
}