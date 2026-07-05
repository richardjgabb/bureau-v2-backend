<?php

namespace App\Test;

use Slim\Http\Response;
use Slim\Psr7\Factory\ServerRequestFactory;

class PlayersRouteTest extends BaseTestCase
{
    public function testGetPlayersSuccess(): void
    {
        $expectedMessage = 'Successfully retrieved from db.';
        $expectedBody = [
                [
                    'id' => 1,
                    'name' => 'Rich',
                    'wins' => 2,
                    'bues' => 0,
                    'games_played' => 2
                ],
                [
                    'id' => 2,
                    'name' => 'Dan',
                    'wins' => 0,
                    'bues' => 0,
                    'games_played' => 2
                ],
                [
                    'id' => 3,
                    'name' => 'Sid',
                    'wins' => 0,
                    'bues' => 1,
                    'games_played' => 2
                ],
                [
                    'id' => 4,
                    'name' => 'Felts',
                    'wins' => 0,
                    'bues' => 2,
                    'games_played' => 2
                ],
                [
                    'id' => 5,
                    'name' => 'Titch',
                    'wins' => 1,
                    'bues' => 0,
                    'games_played' => 1
                ],
                [
                    'id' => 6,
                    'name' => 'Jr',
                    'wins' => 0,
                    'bues' => 0,
                    'games_played' => 1
                ],
                [
                    'id' => 7,
                    'name' => 'CJ',
                    'wins' => 0,
                    'bues' => 1,
                    'games_played' => 1
                ],
                [
                    'id' => 8,
                    'name' => 'Ruff',
                    'wins' => 0,
                    'bues' => 1,
                    'games_played' => 1
                ],
                [
                    'id' => 9,
                    'name' => 'Craze',
                    'wins' => 0,
                    'bues' => 0,
                    'games_played' => 0
                ]
            ];

        $response = $this->makeGetRequest();

        $responseBody = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($expectedMessage, $responseBody['message']);
        $this->assertSame($expectedBody, $responseBody['data']);
    }

    public function testErrorThrows400(): void
    {
        //TODO: implement mocking
        $this->assertTrue(true);
    }

    private function makeGetRequest(): Response
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/api/players');

        return $this->app->handle($request);
    }
}
