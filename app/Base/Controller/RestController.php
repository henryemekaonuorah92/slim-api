<?php

namespace App\Base\Controller;

use App\Base\Controller\Traits\CountBy;
use App\Base\Controller\Traits\DeleteById;
use App\Base\Controller\Traits\GetAll;
use App\Base\Controller\Traits\GetById;
use App\Base\Controller\Traits\InsertOne;
use App\Base\Controller\Traits\UpdateById;
use App\Base\Model\MongoDB;
use MongoDB\Collection;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RestController
{
    // CRUD
    use GetAll;
    use GetById;
    use CountBy;
    use InsertOne;
    use UpdateById;
    use DeleteById;

    /** @var string */
    protected $modelClass = null;

    /** @var string */
    protected $connectionName = 'mongodb';

    /** @var Collection|MongoDB */
    protected $model = null;

    /** @var ContainerInterface */
    public $container = null;

    /**
     * @var Response
     */
    public $response;
    /** @var Request */
    public $request;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $this->container['request'];
        $this->response = $this->container['response'];
        $this->model = new $this->modelClass();
    }
}