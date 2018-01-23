# REST API with Slim [![Build Status](https://travis-ci.org/Meabed/slim-api.svg?branch=master)](https://travis-ci.org/Meabed/slim-api) [![Packagist](https://img.shields.io/packagist/dm/meabed/slim-api.svg)](https://packagist.org/packages/meabed/slim-api) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/meabed/slim-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/meabed/slim-api/?branch=master) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md) [![codecov](https://codecov.io/gh/Meabed/slim-api/branch/master/graph/badge.svg)](https://codecov.io/gh/Meabed/slim-api)

A RESTful API boilerplate for Slim framework. Features included:

* Jwt Authentication
* Endpoint Tests and Unit Tests
* Build Process with [Travis CI](https://travis-ci.org/)
* Event Handling
* Pagination
* API Resources
* Validation

## Getting Started

First, clone the repo:

```bash
git clone https://github.com/Meabed/slim-api
```

### Install dependencies

```bash
cd slim-api
composer install
```

### Configure the Environment

Create `.env` file:

```bash
cat .env.example > .env
```

If you want you can edit database name, database username and database password.

### Run Application

To start making RESTful requests to slim-api start the PHP local server using:

```bash
php -S localgost:3000 -t public
```

### Creating token

For creating token we have to use the http://localhost:3000/api/user/login route. Here is an example of creating token with [Postman](https://www.getpostman.com/).

![Imgur](https://i.imgur.com/dkFX1o4.png)

### Creating a New Resource

Creating a new resource is very easy and straight-forward. Follow these simple steps to create a new resource. The complete structure for a resource is like this:

```
.
+-- app
|  +-- Post
|      +-- api_routes.php
|      +-- PostController.php
|      +-- PostModel.php  
```

#### Step 1: Create Route

To create route for a resource create a new folder inside `app` folder. Then inside that resource folder create a new route file named `api_routes.php`. For example lets create routes for `Post` reource.

```php
$this->get('/posts', \App\Post\PostController::class . ":getAll");
$this->post('/posts', \App\Post\PostController::class . ":insertAndRetrieve");
$this->get('/posts/{id:[A-Z0-9a-z]+}', \App\Post\PostController::class . ":get");
$this->put('/posts/{id:[A-Z0-9a-z]+}', \App\Post\PostController::class . ":update");
$this->delete('/posts/{id:[A-Z0-9a-z]+}', \App\Post\PostController::class . ":delete");
```

For more info please visit Slim [Routing](https://www.slimframework.com/docs/objects/router.html) page.

#### Step 2: Creat Model

Create a model `Post.php` inside `app/Post` directory.

```php
<?php

namespace App\Post;

use App\Base\Model\MongoDB;

class PostModel extends MongoDB
{
    /** 
    * @var string 
    */
    protected $collectionName = 'groups';
}
```

Visit [Mongo PHP](https://docs.mongodb.com/php-library/current/) to learn about how to query mongodb using php.

#### Step 3: Create Controller

Create a controller `PostController.php` inside `app/Post` directory.

```php
<?php

namespace App\Post;

use App\Base\Controller\RestController;

class PostController extends RestController
{
    protected $modelClass = PostModel::class;
}
```

## Contributing

Contributions, questions and comments are all welcome and encouraged. For code contributions submit a pull request.
