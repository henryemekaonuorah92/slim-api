<?php
/** @var \Slim\App $this */

// get user from jwt
$this->get('/user/test', \Modules\User\UserController::class . ":me");

$this->get('/user/me', \Modules\User\UserController::class . ":me");

// user login
$this->post('/user/login', \Modules\User\UserController::class . ":login");

// register | insert
$this->post('/user/register', \Modules\User\UserController::class . ":insert");

### REST API
// list all users
$this->get('/users', \Modules\User\UserController::class . ":getAll");
// get count all users
$this->get('/users/count', \Modules\User\UserController::class . ":count");

// get user by id
$this->get('/user/{id:[A-Z0-9a-z]+}', \Modules\User\UserController::class . ":get");
// update user by id
$this->put('/user/{id:[A-Z0-9a-z]+}', \Modules\User\UserController::class . ":update");
// delete user by id
$this->delete('/user/{id:[A-Z0-9a-z]+}', \Modules\User\UserController::class . ":delete");