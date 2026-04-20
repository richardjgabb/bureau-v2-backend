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
        if (!$event->isSplit() && $event->hasNoBues()) {
            $this->potRepository->handlePotWinner(
                $event->gameId,
                $event->round,
                $event->winnerId,
                $event->currentPotSize,
                $event->isCompulsRound(),
                $event->dealerId,
                $event->amountOfBues()
            );
        } elseif ($event->isSplit()) {
            $this->potRepository->handleSplit(
                $event->gameId,
                $event->round,
                $event->currentPotSize,
                $event->isCompulsRound(),
                $event->amountOfBues()
            );
        } else {
            //TODO handle this case
        }
    }
}