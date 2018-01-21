<?php
/** @var \Slim\App $this */

// get user from jwt
$this->get('/user/me', \Module\User\UserController::class . ":me");

// user login
$this->post('/user/login', \Module\User\UserController::class . ":login");

// register | insert
$this->post('/user/register', \Module\User\UserController::class . ":insert");

### REST API
// list all users
$this->get('/users', \Module\User\UserController::class . ":getAll");
// get count all users
$this->get('/users/count', \Module\User\UserController::class . ":count");

// get user by id
$this->get('/user/{id:[A-Z0-9a-z]+}', \Module\User\UserController::class . ":get");
// update user by id
$this->put('/user/{id:[A-Z0-9a-z]+}', \Module\User\UserController::class . ":update");
// delete user by id
$this->delete('/user/{id:[A-Z0-9a-z]+}', \Module\User\UserController::class . ":delete");