<?php
/** @var \Slim\App $this */


### REST API
// list all groups
$this->get('/groups', \App\Group\GroupController::class . ":loadAll");
// get count all groups
$this->get('/groups/count', \App\Group\GroupController::class . ":count");

// insert group
$this->map(['POST'], '/group', \App\Group\GroupController::class . ":saveAndRetrieve");
// get group by id
$this->get('/group/{id:[A-Z0-9a-z]+}', \App\Group\GroupController::class . ":loadById");
// update group by id
$this->put('/group/{id:[A-Z0-9a-z]+}', \App\Group\GroupController::class . ":updateAndRetrieve");
// delete group by id
$this->delete('/group/{id:[A-Z0-9a-z]+}', \App\Group\GroupController::class . ":delete");