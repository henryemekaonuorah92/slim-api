<?php
/** @var \Slim\App $this */


### REST API
// list all categorys
$this->get('/categorys', \App\Category\CategoryController::class . ":loadAll");
// get count all categorys
$this->get('/categorys/count', \App\Category\CategoryController::class . ":count");

// insert category
$this->map(['POST'], '/category', \App\Category\CategoryController::class . ":saveAndRetrieve");
// get category by id
$this->get('/category/{id:[A-Z0-9a-z]+}', \App\Category\CategoryController::class . ":loadById");
// update category by id
$this->put('/category/{id:[A-Z0-9a-z]+}', \App\Category\CategoryController::class . ":updateAndRetrieve");
// delete category by id
$this->delete('/category/{id:[A-Z0-9a-z]+}', \App\Category\CategoryController::class . ":deleteById");