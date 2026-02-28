<?php

declare(strict_types=1);


namespace App\Controllers;

use App\Classes\StatusCode;
use App\Models\UserModel;
use PDOException;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Interfaces\ResponseInterface;

class RegisterUserController
{
    private UserModel $model;

    // Here, the parameter is automatically supplied by the Dependency Injection Container based on the type hint
    public function __construct(UserModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $details = $request->getParsedBody();
            $user = $this->model->addUser(
                $details['username'],
                password_hash($details['password'], PASSWORD_DEFAULT)
            );

            $responseBody = $user ?
                ['message' => 'User added', 'status' => StatusCode::HTTP_CREATED] :
                ['message' => 'User not added', 'status' => StatusCode::HTTP_BAD_REQUEST];

        } catch (PDOException $e) {
            $responseBody = [
                'message' => 'No user found.',
                'status' => StatusCode::HTTP_BAD_REQUEST,
            ];
        }
        return $response->withJson($responseBody);
    }
}