<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RoundCompletedEvent;
use App\Repositories\ScoreRepository;

class ScoreListener {

    private ScoreRepository $scoreRepository;

    public function __construct(ScoreRepository $scoreRepository)
    {
        $this->scoreRepository = $scoreRepository;
    }
    public function onRoundFinished(RoundCompletedEvent $event) {
        // TODO: Make PotId not 0
        $this->scoreRepository->addPlayersScores(
            $event->gameId,
            $event->round,
            0,
            $event->playerScores(),
            $event->dealerId,
            $event->isCompulsRound(),
            $event->winnerId,
            $event->buedIds
        );
    }
}