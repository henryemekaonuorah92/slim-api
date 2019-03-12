<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoDB;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property MongoDB|Collection $model
 */
trait Load
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     * @throws \Exception
     */
    public function loadAll(Request $request, Response $response, $args)
    {
        $rs = $this->model->find()->toArray();

        return $response->withJson($rs);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     * @throws \Exception
     */
    public function loadById(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        $rs = $this->model->load($id)
                          ->getStoredData();

        // return null if not exist | [] empty object
        return $response->withJson($rs ?: null);
    }
}
