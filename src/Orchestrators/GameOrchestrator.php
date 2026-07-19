<?php

declare(strict_types=1);

namespace App\Orchestrators;

use App\DTOs\GameEditDTO;
use App\Exceptions\GameNotFoundException;
use App\Objects\GameObject;
use App\Repositories\GameRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\PotRepository;
use App\Repositories\ScoreRepository;
use App\Services\GameFormatterService;
use App\Services\ScoreService;

class GameOrchestrator
{
    private GameRepository $gameRepository;
    private PlayerRepository $playerRepository;
    private GameFormatterService $formatter;
    private PotRepository $potRepository;
    private ScoreRepository $scoreRepository;
    private ScoreService $scoreService;

    public function __construct(
        GameRepository $gameRepository,
        PlayerRepository $playerRepository,
        GameFormatterService $formatter,
        PotRepository $potRepository,
        ScoreRepository $scoreRepository,
        ScoreService $scoreService
    ) {
        $this->gameRepository = $gameRepository;
        $this->playerRepository = $playerRepository;
        $this->formatter = $formatter;
        $this->potRepository = $potRepository;
        $this->scoreRepository = $scoreRepository;
        $this->scoreService = $scoreService;
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
        $potId = $this->potRepository->addNewPot($gameId, 0, null, 0, 0, null);
        $this->scoreRepository->initiateScoresForAllPlayers($players ?? $existingPlayers, $potId);

        return $this->getGameData((string) $gameId);
    }

    public function updateGame(GameEditDTO $data): bool
    {
        $existingPlayers = $this->playerRepository->getPlayerIdsForGame($data->id);

        $newPlayers = array_values(array_diff(array_keys($data->players), $existingPlayers));

        $playersToRemove = array_values(array_diff($existingPlayers, array_keys($data->players)));

        $game = $this->gameRepository->updateGame($data->id, $data->name, $data->buyIn);
        $players = $this->playerRepository->updatePlayersForGame( $data->id, $newPlayers, $playersToRemove);
        $scores = $this->scoreService->updatePlayersScores($data->id, $data->players, $data->round);
        return true;
    }
}