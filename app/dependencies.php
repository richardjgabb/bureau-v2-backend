<?php
declare(strict_types=1);

use App\Events\RoundCompletedEvent;
use App\Factories\LoggerFactory;
use App\Factories\PDOFactory;
use App\Factories\RendererFactory;
use App\Listeners\PotListener;
use App\Listeners\ScoreListener;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $container = [
        LoggerInterface::class => DI\factory(LoggerFactory::class),
        PhpRenderer::class => DI\factory(RendererFactory::class),
        PDO::class => DI\factory(PDOFactory::class),

        EventDispatcher::class => function (ContainerInterface $c) {
            $dispatcher = new EventDispatcher();

            $dispatcher->addListener(
                RoundCompletedEvent::NAME,
                [$c->get(PotListener::class), 'onRoundFinished']
            );

            $dispatcher->addListener(
                RoundCompletedEvent::NAME,
                [$c->get(ScoreListener::class), 'onRoundFinished']
            );

            return $dispatcher;
        },

        PotListener::class => DI\autowire(),
    ];

    $containerBuilder->addDefinitions($container);
};