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
        $this->scoreRepository->addPlayersScores(
            0,
            $event->playerScores(),
            $event->buedIds
        );
    }
}