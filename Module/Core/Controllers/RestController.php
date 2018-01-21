<?php
declare(strict_types=1);

namespace Module\Core\Controllers\Base;

use Module\Core\Controllers\Base\Traits\CountBy;
use Module\Core\Controllers\Base\Traits\DeleteById;
use Module\Core\Controllers\Base\Traits\GetAll;
use Module\Core\Controllers\Base\Traits\GetById;
use Module\Core\Controllers\Base\Traits\InsertOne;
use Module\Core\Controllers\Base\Traits\UpdateById;
use Meabed\Mongoose\Method;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RestController
{


    /** @var string */
    protected $modelClass = null;

    /** @var string */
    protected $connectionName = 'mongodb';

    /** @var Method */
    protected $model = null;

    /** @var ContainerInterface */
    public $container = null;

    /**
     * @var Response
     */
    public $response;
    /** @var Request */
    public $request;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $this->container['request'];
        $this->response = $this->container['response'];
        $this->model = new $this->modelClass();
    }

    use GetAll;
    use GetById;
    use CountBy;
    use InsertOne;
    use UpdateById;
    use DeleteById;

//
//    /**
//     * Return list of users
//     *
//     * @return Response
//     */
//    public function index(): Response
//    {
//        if (!$users = $this->userRepository->index()) {
//            return $this->apiResponse->error('Users not found', 'List of user is not available or not exists', 404, $users);
//        }
//
//        $data = $this->userTransformer->collection($users);
//
//        return $this->apiResponse->success($data);
//    }
//
//    /**
//     * Get a specific User
//     *
//     * @param int $id
//     *
//     * @return Response
//     */
//    public function show(int $id): Response
//    {
//        if (!$user = $this->userRepository->show($id)) {
//            return $this->apiResponse->error('User not found', 'The user is not available or not exists', 404, $user);
//        }
//
//        $data = $this->userTransformer->item($user);
//
//        return $this->apiResponse->success($data);
//    }
//
//    /**
//     * Add a new User
//     *
//     * @param Request $request
//     * @param StoreUserValidator $validator
//     * @param UserService $userService
//     *
//     * @return Response
//     */
//    public function store(Request $request, StoreUserValidator $validator, UserService $userService): Response
//    {
//        if (!$validator->validate()) {
//            return $this->apiResponse->errorValidation($validator->errors());
//        }
//
//        if (!$user = $userService->store($request->getParams())) {
//            return $this->apiResponse->error('User not created', 'The user has not been created', 500, $user);
//        }
//
//        $data = $this->userTransformer->item($user);
//
//        return $this->apiResponse->success($data, 201);
//    }
//
//    /**
//     * Update user data
//     *
//     * @param int $id
//     * @param Request $request
//     * @param UpdateUserValidator $validator
//     * @param UserService $userService
//     *
//     * @return Response
//     */
//    public function update(int $id, Request $request, UpdateUserValidator $validator, UserService $userService): Response
//    {
//        if (!$validator->validate()) {
//            return $this->apiResponse->errorValidation($validator->errors());
//        }
//
//        if (!$user = $userService->update($id, $request->getParams())) {
//            return $this->apiResponse->error('User not updated', 'The user not exists or has not been updated', 500, $user);
//        }
//
//        return $this->apiResponse->success('User updated');
//    }
//
//    /**
//     * Delete User
//     *
//     * @param int $id
//     *
//     * @return Response
//     */
//    public function delete(int $id): Response
//    {
//        if (!$user = $this->userRepository->delete($id)) {
//            return $this->apiResponse->error('User not deleted', 'The user not exists or has not been deleted', 500, $user);
//        }
//
//        return $this->apiResponse->success('User deleted');
//    }
}