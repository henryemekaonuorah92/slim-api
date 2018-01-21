<?php
/** @var \Slim\App $this */


### REST API
// list all contacts
$this->get('/contacts', \Module\Contact\ContactController::class . ":getAll");
// get count all contacts
$this->get('/contacts/count', \Module\Contact\ContactController::class . ":count");

// insert contact
$this->map(['POST'], '/contact', \Module\Contact\ContactController::class . ":insertAndRetrieve");
// get contact by id
$this->get('/contact/{id:[A-Z0-9a-z]+}', \Module\Contact\ContactController::class . ":get");
// update contact by id
$this->put('/contact/{id:[A-Z0-9a-z]+}', \Module\Contact\ContactController::class . ":update");
// delete contact by id
$this->delete('/contact/{id:[A-Z0-9a-z]+}', \Module\Contact\ContactController::class . ":delete");