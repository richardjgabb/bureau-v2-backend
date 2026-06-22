<?php

declare(strict_types=1);

namespace App\Services;

use App\Objects\StoreRoundObject;
use App\Repositories\PotRepository;
use App\Repositories\ScoreRepository;
use Exception;
use PDO;

class ScoreService
{
    public function __construct(
        private PotRepository $potRepository,
        private ScoreRepository $scoreRepository,
        private PDO $db
    )
    {}

    public function storeRound(
        StoreRoundObject $newRound,
    ): bool
    {
        $this->db->beginTransaction();
        try {
            $newPot = $this->potRepository->addNewPot(
                $newRound->gameId,
                $newRound->round,
                $newRound->winnerId,
                $newRound->currentPotSize,
                $newRound->isCompulsRound() ? 1 : 0,
                $newRound->dealerId,
            );

            $newScores = $this->scoreRepository->addPlayersScores(
                $newPot,
                $newRound->playerScores(),
                $newRound->buedIds
            );

            $this->db->commit();
        } catch (Exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }

        return true;
    }

    public function updatePlayersScores(int $gameId, array $players): bool
    {
        $latestPot = $this->potRepository->getLatestPotForGame($gameId);

        if (!$latestPot) {
            $latestPot = 0;
        }

        foreach ($players as $player => $score) {
            $latestScore = $this->scoreRepository->getLatestScoreForPlayer($gameId, $player);

            if (!$latestScore) {
                $this->scoreRepository->insertNewScore( $player, $latestPot, $score, 0);
                continue;
            }

            if (!$this->scoreRepository->updatePlayerScoreForGame($gameId, $player, $score)) {
                return false;
            }
        }

        return true;
    }
}
