<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoDB;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property MongoDB|Collection $model
 */
trait DeleteById
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;

        $rs = $this->model->deleteOne([$this->model->getIdFieldName() => new ObjectId($id)]);

        return $response->withJson(['n' => $rs->getDeletedCount()]);

    }
}