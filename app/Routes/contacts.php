<?php
/** @var \Slim\App $this */


### REST API
// list all contacts
$this->get('/contacts', \App\Controllers\ContactController::class . ":getAll");
// get count all contacts
$this->get('/contacts/count', \App\Controllers\ContactController::class . ":count");

// insert contact
$this->map(['POST'], '/contact', \App\Controllers\ContactController::class . ":insertAndRetrieve");
// get contact by id
$this->get('/contact/{id:[A-Z0-9a-z]+}', \App\Controllers\ContactController::class . ":get");
// update contact by id
$this->put('/contact/{id:[A-Z0-9a-z]+}', \App\Controllers\ContactController::class . ":update");
// delete contact by id
$this->delete('/contact/{id:[A-Z0-9a-z]+}', \App\Controllers\ContactController::class . ":delete");