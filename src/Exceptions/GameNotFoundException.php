<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class GameNotFoundException extends Exception
{
    public function __construct(string $gameId = '')
    {
        $message = $gameId
            ? "The requested game with ID '{$gameId}' was not found."
            : 'The requested game was not found.';

        parent::__construct($message);
    }
}
