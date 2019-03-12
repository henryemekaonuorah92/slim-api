<?php

namespace App\Base\Controller\Traits;

use App\Base\Helper\Event;
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
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     * @throws \Exception
     */
    public function saveAndRetrieve(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $objId = new ObjectId();
        $this->model->setData($data)
                    ->setId($objId)
                    ->save();

        $rs = $this->model->load($objId)
                          ->getStoredData();

        Event::emit('rest.entity.created', get_class($this->model), $rs);

        return $response->withJson($rs ?: null);
    }
}
