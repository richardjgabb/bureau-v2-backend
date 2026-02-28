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
        $scores = $this->scoreRepository->getAllScoresForGame($gameId);
        $playerStats = $this->playerStatsRepository->getAllPlayersStatsForGame($gameId);
        $pots = $this->potRepository->getAllPotsForGame($gameId);
        $formattedGame = $this->formatter->createGameArray($game, $players, $scores, $playerStats, $pots);

        return $formattedGame;
    }

    public function getAllGames(): array
    {
        return $this->gameRepository->getAllGames();
    }

    public function createNewGame(array $data): GameObject
    {
        return $this->gameRepository->createNewGame($data);
    }
}