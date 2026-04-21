<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RoundCompletedEvent;
use App\Repositories\PlayerStatsRepository;

class StatsListener {

    private PlayerStatsRepository $statsRepository;

    public function __construct(PlayerStatsRepository $statsRepository)
    {
        $this->statsRepository = $statsRepository;
    }
    public function onRoundFinished(RoundCompletedEvent $event)
    {
        $this->statsRepository->handleRoundStats(
            $event->dealerId,
            $event->gameId,
            $event->winnerId,
            $event->isCompulsRound(),
            $event->buedIds
        );
    }
}