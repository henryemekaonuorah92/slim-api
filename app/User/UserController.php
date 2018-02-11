<?php

namespace App\User;

use App\Base\AppContainer;
use App\Base\Controller\RestController;
use App\Base\Helper\Event;
use App\Base\Helper\Jwt;
use App\Base\Helper\Password;
use MongoDB\BSON\ObjectId;
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function login(Request $request, Response $response, $args)
    {
        $email = $request->getParsedBodyParam('email');
        $password = $request->getParsedBodyParam('password');

        $user = null;
        $user = $this->model->findOne(['email' => $email], ['sort' => ['created_at' => -1]]);
        if (!$user) {
            throw new \Exception('Username not found', 400);
        }

        $passwordOk = Password::verify($password, $user['password']);
        if (!$passwordOk) {
            throw new \Exception('The Username or Password is incorrect. Try again', 400);
        }

        $rs = Jwt::generateToken($user);

        Event::emit('user.login', $user);

        return $response->withJson($rs);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function register(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $email = $data['email'] ?? '';

        // @todo handle validation
        $existingUsers = $this->model->findOne(['email' => $email]);
        if (!empty($existingUsers)) {
            throw new \Exception('The Email is already registered', 400);
        }

        $data = $request->getParsedBody();

        $objId = new ObjectId();
        $this->model->setData($data)
            ->setId($objId)
            ->save();

        $user = $this->model->load($objId)
            ->getStoredData();

        Event::emit('user.created', $user);

        $token = Jwt::generateToken($user);

        $return = array_merge((array)$user, $token);

        return $response->withJson($return);
    }
}
