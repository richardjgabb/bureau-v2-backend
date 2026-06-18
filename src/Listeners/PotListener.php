<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RoundCompletedEvent;
use App\Repositories\PotRepository;

class PotListener {

    private PotRepository $potRepository;

    public function __construct(PotRepository $potRepository)
    {
        $this->potRepository = $potRepository;
    }
    public function onRoundFinished(RoundCompletedEvent $event) {
        $this->potRepository->addNewPot(
            $event->gameId,
            $event->round,
            $event->winnerId,
            $event->currentPotSize,
            $event->isCompulsRound() ? 1 : 0,
            $event->dealerId,
        );
    }
}