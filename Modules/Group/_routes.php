<?php
/** @var \Slim\App $this */


### REST API
// list all groups
$this->get('/groups', \Modules\Group\GroupController::class . ":getAll");
// get count all groups
$this->get('/groups/count', \Modules\Group\GroupController::class . ":count");

// insert group
$this->map(['POST'], '/group', \Modules\Group\GroupController::class . ":insertAndRetrieve");
// get group by id
$this->get('/group/{id:[A-Z0-9a-z]+}', \Modules\Group\GroupController::class . ":get");
// update group by id
$this->put('/group/{id:[A-Z0-9a-z]+}', \Modules\Group\GroupController::class . ":update");
// delete group by id
$this->delete('/group/{id:[A-Z0-9a-z]+}', \Modules\Group\GroupController::class . ":delete");