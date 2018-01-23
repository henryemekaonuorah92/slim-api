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
            ['name' => 'group name', 'description' => 'group desc']
        );

        //var_dump((string)$response->getBody());
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('group name', $rs['name']);
        $this->assertContains('group desc', $rs['description']);

    }
}