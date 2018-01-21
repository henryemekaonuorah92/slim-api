<?php
/** @var \Slim\App $this */


### REST API
// list all groups
$this->get('/groups', \Module\Group\GroupController::class . ":getAll");
// get count all groups
$this->get('/groups/count', \Module\Group\GroupController::class . ":count");

// insert group
$this->map(['POST'], '/group', \Module\Group\GroupController::class . ":insertAndRetrieve");
// get group by id
$this->get('/group/{id:[A-Z0-9a-z]+}', \Module\Group\GroupController::class . ":get");
// update group by id
$this->put('/group/{id:[A-Z0-9a-z]+}', \Module\Group\GroupController::class . ":update");
// delete group by id
$this->delete('/group/{id:[A-Z0-9a-z]+}', \Module\Group\GroupController::class . ":delete");