<?php

declare(strict_types=1);

use App\Controllers\GameController;
use App\Controllers\LoginController;
use App\Controllers\PlayerController;
use App\Controllers\PlayerStatsController;
use App\Controllers\RegisterUserController;
use App\Controllers\ScoreboardController;
use App\Controllers\ScoresController;
use App\Controllers\StatsController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/api', function (RouteCollectorProxy $app) {

        $app->group('/players', function (RouteCollectorProxy $app) {
            $app->get('', [PlayerController::class, 'index']);
            $app->post('', [PlayerController::class, 'store']);
            $app->get('/{playerId}', [PlayerController::class, 'show']);
        });

        $app->group('/games', function (RouteCollectorProxy $app) {
            $app->get('', [GameController::class, 'index']);
            $app->post('', [GameController::class, 'store']);
            $app->get('/{gameId}', [GameController::class, 'show']);
            $app->put('/{gameId}', [GameController::class, 'update']);
            $app->get('/{gameId}/scoreboard', ScoreboardController::class);
            $app->get('/{gameId}/stats', [StatsController::class, 'show']);
            $app->get('/{gameId}/availablePlayers', [PlayerController::class, 'showPlayersNotInGame']);
        });

        $app->group('/scores', function (RouteCollectorProxy $app) {
            $app->get('/{gameId}', [ScoresController::class, 'show']);
            $app->post('/{gameId}', [ScoresController::class, 'store']);
            $app->delete('/{gameId}/round/{round}', [ScoresController::class, 'delete']);
        });

        $app->get('/stats', [StatsController::class, 'index']);
        $app->get('/login', LoginController::class);
        $app->get('/newGame', [GameController::class, 'newGame']);
        $app->post('/register', RegisterUserController::class);
    });
};
