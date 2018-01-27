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
trait Save
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @throws \Exception
     */
    public function saveAndRetrieve(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $data[$this->model->getIdFieldName()] = $objId = new ObjectId();
        $this->model->setData($data)->save();

        $rs = $this->model->load($objId)->getStoredData();

        return $response->withJson($rs ?: null);
    }
}