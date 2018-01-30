<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoDB;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property MongoDB|Collection $model
 */
trait CountBy
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Exception
     */
    public function count(Request $request, Response $response, $args)
    {
        $rs = $this->model->count();

        return $response->withJson(['n' => $rs]);
    }
}
