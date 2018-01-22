<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoModel;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Collection|MongoModel $model
 * @package App\Base\Controllers\Traits
 */
trait CountBy
{

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return static
     * @throws \Exception
     */
    public function count(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $rs = $this->model->count();
        return $this->response->withJson(['n' => $rs]);
    }
}