<?php
/** @var \Slim\App $this */

// get user from jwt
$this->get('/user/me', \App\User\UserController::class . ":me");

// user login
$this->post('/user/login', \App\User\UserController::class . ":login");

// register | insert
$this->post('/user/register', \App\User\UserController::class . ":saveAndRetrieve");

### REST API
// list all users
$this->get('/users', \App\User\UserController::class . ":getAll");
// get count all users
$this->get('/users/count', \App\User\UserController::class . ":count");

// get user by id
$this->get('/user/{id:[A-Z0-9a-z]+}', \App\User\UserController::class . ":get");
// update user by id
$this->put('/user/{id:[A-Z0-9a-z]+}', \App\User\UserController::class . ":updateAndRetrieve");
// delete user by id
$this->delete('/user/{id:[A-Z0-9a-z]+}', \App\User\UserController::class . ":delete");