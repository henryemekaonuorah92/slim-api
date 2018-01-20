<?php
/** @var \Slim\App $this */


### REST API
// list all groups
$this->get('/groups', \App\Controllers\GroupController::class . ":getAll");
// get count all groups
$this->get('/groups/count', \App\Controllers\GroupController::class . ":count");

// insert group
$this->map(['POST'], '/group', \App\Controllers\GroupController::class . ":insertAndRetrieve");
// get group by id
$this->get('/group/{id:[A-Z0-9a-z]+}', \App\Controllers\GroupController::class . ":get");
// update group by id
$this->put('/group/{id:[A-Z0-9a-z]+}', \App\Controllers\GroupController::class . ":update");
// delete group by id
$this->delete('/group/{id:[A-Z0-9a-z]+}', \App\Controllers\GroupController::class . ":delete");