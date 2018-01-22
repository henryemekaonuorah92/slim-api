<?php

namespace Tests\Group;

use Tests\Base\BaseApiCase;

class RestTest extends BaseApiCase
{
    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testAddGroup()
    {
        $response = $this->request(
            'POST', '/api/group',
            ['email' => 'email@gmail.com', 'password' => 'Hello world']
        );

        //var_dump((string)$response->getBody());
        $this->assertSame($response->getStatusCode(), 401);
        //$this->assertSame((string)$response->getBody(), '{"n":1}');

    }
}