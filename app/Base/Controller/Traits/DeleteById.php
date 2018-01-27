<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoDB;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property MongoDB|Collection $model
 */
trait Delete
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Exception
     */
    public function deleteById(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;

        $this->model->delete($id);

        return $response->withJson(['ok' => '1']);

    }
}