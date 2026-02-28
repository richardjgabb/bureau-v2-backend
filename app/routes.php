<?php
declare(strict_types=1);

use App\Controllers\GameController;
use App\Controllers\LoginController;
use App\Controllers\PlayerController;
use App\Controllers\PlayerStatsController;
use App\Controllers\RegisterUserController;
use App\Controllers\ScoresController;
use App\Controllers\StatsController;
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/api/players', [PlayerController::class, 'index']);
    $app->get('/api/players/{playerId}', [PlayerStatsController::class, 'show']);
    $app->get('/api/stats', [StatsController::class, 'index']);
    $app->get('/api/games', [GameController::class, 'index']);
    $app->get('/api/scores/{gameId}', [ScoresController::class, 'show']);
    $app->get('/api/games/{gameId}', [GameController::class, 'show']);
    $app->get('/api/players/{playerId}/stats/{gameId}', PlayerStatsController::class);
    $app->get('/api/login', LoginController::class);
    $app->get('/api/newGame', [GameController::class, 'newGame']);
    $app->post('/api/register', RegisterUserController::class);


// In production, Run npm run build and put the build folder in the backend/public folder
    $app->get('/{routes:.+}', function (Request $request, Response $response, array $args) {
        $file = __DIR__ . '/../build/' . $args['routes'];
        if (file_exists($file)) {
            return $response->write(file_get_contents($file));
        }
        return $response->write(file_get_contents(__DIR__ . '/../build/index.html'));
    });
};
