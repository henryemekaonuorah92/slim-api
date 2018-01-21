<?php

declare(strict_types=1);

namespace Module\User;

use Module\Util\Helpers\Password;
use Module\Util\JWT\Jwt;
use Module\Core\AppContainer;
use Module\Core\Controllers\Base\RestController;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends RestController
{
    protected $modelClass = UserModel::class;

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function me(Request $request, Response $response, $args)
    {
        $rs = AppContainer::config('jwtUser');
        return $response->withJson($rs);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function login(Request $request, Response $response, $args)
    {
        $email = $request->getParsedBodyParam('email');
        $password = $request->getParsedBodyParam('password');

        $dbUser = null;
        $dbUser = $this->model->findOne(['email' => $email], ['sort' => ['created_at' => -1]]);
        if (!$dbUser) {
            throw new \Exception('Username not found', 400);
        }

        $passwordOk = Password::verify($password, $dbUser['password']);
        if (!$passwordOk) {
            throw new \Exception('The user name or password is incorrect. Try again', 400);
        }

        $rs = Jwt::generateToken($dbUser);

        return $response->withJson($rs);
    }
}
