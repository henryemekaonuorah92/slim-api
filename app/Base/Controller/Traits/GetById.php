<?php

namespace App\Base\Controller\Traits;

use App\Base\Model\MongoDB;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property Collection|MongoDB $model
 * @package App\Base\Controllers\Traits
 */
trait GetById
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return static
     * @throws \Exception
     */
    public function get(Request $request, Response $response, $args)
    {
        $this->request = $request;
        $this->response = $response;

        $id = $args['id'] ?? null;
        try {
            $mongoId = new ObjectId($id);
        } catch (\Exception $ex) {
            throw new \Exception('Invalid ID', 400);
        }
        $rs = $this->model->findOne(['_id' => $mongoId]);

        return $this->response->withJson($rs);
    }

}