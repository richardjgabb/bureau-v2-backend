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
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/api', function (RouteCollectorProxy $app) {

        $app->group('/players', function (RouteCollectorProxy $app) {
            $app->get('', [PlayerController::class, 'index']);
            $app->get('{playerId}/stats/{gameId}', PlayerStatsController::class);
            $app->get('/{playerId}', [PlayerStatsController::class, 'show']);
        });

        $app->group('/games', function (RouteCollectorProxy $app) {
            $app->get('', [GameController::class, 'index']);
            $app->post('', [GameController::class, 'store']);
            $app->get('/{gameId}', [GameController::class, 'show']);
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


// In production, Run npm run build and put the build folder in the backend/public folder
    $app->get('/{routes:.+}', function (Request $request, Response $response, array $args) {
        $file = __DIR__ . '/../build/' . $args['routes'];
        if (file_exists($file)) {
            return $response->write(file_get_contents($file));
        }
        return $response->write(file_get_contents(__DIR__ . '/../build/index.html'));
    });
};
