<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoDB;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Collection|MongoDB $model
 * @package App\Base\Controllers\Traits
 */
trait GetAll
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Exception
     */
    public function getAll(Request $request, Response $response, $args)
    {
        $rs = $this->model->find()->toArray();

        return $response->withJson($rs);
    }
}