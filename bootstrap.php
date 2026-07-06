<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use DI\ContainerBuilder;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\App;

require __DIR__ . '/vendor/autoload.php';

function createApp(): App
{
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $containerBuilder = new ContainerBuilder();

    if (false) {
        $containerBuilder->enableCompilation(__DIR__ . '/var/cache');
    }

    $settings = require __DIR__ . '/app/settings.php';
    $settings($containerBuilder);

    $dependencies = require __DIR__ . '/app/dependencies.php';
    $dependencies($containerBuilder);

    $repositories = require __DIR__ . '/app/repositories.php';
    $repositories($containerBuilder);

    $container = $containerBuilder->build();

    $responseFactory = new ResponseFactory();
    $streamFactory = new StreamFactory();

    $decoratedResponseFactory = new DecoratedResponseFactory(
        $responseFactory,
        $streamFactory
    );

    AppFactory::setContainer($container);

    $app = AppFactory::create($decoratedResponseFactory);

    $middleware = require __DIR__ . '/app/middleware.php';
    $middleware($app);

    $routes = require __DIR__ . '/app/routes.php';
    $routes($app);

    $app->addRoutingMiddleware();

    $displayErrorDetails = $container->get('settings')['displayErrorDetails'];

    $app->addErrorMiddleware(
        $displayErrorDetails,
        false,
        false
    );

    $app->add(function ($request, $handler) {
        $allowedOrigins = [
            'http://localhost:5173',                  // Your local development server
            'https://cidermenbureau.netlify.app',
            'https://cidermenbureau.co.uk',
            'https://www.cidermenbureau.co.uk'
        ];

        $origin = $request->getHeaderLine('Origin');

        $response = $handler->handle($request);

        if (in_array($origin, $allowedOrigins)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        }

        return $response
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });

    return $app;
}