<?php
/** @var \Slim\App $this */


### REST API
// list all tags
$this->get('/tags', \App\Tag\TagController::class . ":getAllTags");
// get count all tags
$this->get('/tags/count', \App\Tag\TagController::class . ":count");

// insert tag
$this->map(['POST'], '/tag', \App\Tag\TagController::class . ":createTag");
// get tag by id
$this->get('/tag/{id:[A-Z0-9a-z]+}', \App\Tag\TagController::class . ":loadById");
// update tag by id
$this->put('/tag/{id:[A-Z0-9a-z]+}', \App\Tag\TagController::class . ":updateAndRetrieve");
// delete tag by id
$this->delete('/tag/{tag_id:[A-Z0-9a-z]+}', \App\Tag\TagController::class . ":deleteTag");

$this->get('/user/{user_id:[A-Z0-9a-z]+}/tags', \App\Tag\TagController::class . ":getUserAllTags");

$this->get('/tag/{tag_id:[A-Z0-9a-z]+}/posts', \App\Tag\TagController::class . ":getTagPosts");
