<?php

namespace Module\Core\Controllers\Base\Traits;

use Module\Core\Models\Base\MongoModel;
use Meabed\Mongoose\Method;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Method|MongoModel $model
 * @package Module\Core\Controllers\Base\Traits
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
        return $this->response->withJson($rs);
    }
}