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
}
