<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RoundCompletedEvent;

class StatsListener {

    public function __construct()
    {
    }
    public function onRoundFinished(RoundCompletedEvent $event) {
        // TODO implement
    }
}