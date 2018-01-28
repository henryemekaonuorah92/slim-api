<?php
/** @var \Slim\App $this */


### REST API
// list all posts
$this->get('/posts', \App\Post\PostController::class . ":loadAll");
// get count all posts
$this->get('/posts/count', \App\Post\PostController::class . ":count");

// insert post
$this->map(['POST'], '/post', \App\Post\PostController::class . ":saveAndRetrieve");
// get post by id
$this->get('/post/{id:[A-Z0-9a-z]+}', \App\Post\PostController::class . ":loadById");
// update post by id
$this->put('/post/{id:[A-Z0-9a-z]+}', \App\Post\PostController::class . ":updateAndRetrieve");
// delete post by id
$this->delete('/post/{id:[A-Z0-9a-z]+}', \App\Post\PostController::class . ":deleteById");