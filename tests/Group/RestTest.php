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
    public function testGroupFlow()
    {
        // insert group
        $response = $this->sendHttpRequest(
            'POST', '/api/group',
            ['name' => 'group name', 'description' => 'group desc']
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('group name', $rs['name']);
        $this->assertContains('group desc', $rs['description']);

        $groupId = $rs['_id'];


        // get group
        $response = $this->sendHttpRequest(
            'GET', '/api/group/' . $groupId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($groupId, $rs['_id']);
        $this->assertContains('group desc', $rs['description']);

        // update group
        $response = $this->sendHttpRequest(
            'PUT', '/api/group/' . $groupId,
            ['name' => 'group name update', 'description' => 'group desc update']

        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('group desc', $rs['description']);

        // get group
        $response = $this->sendHttpRequest(
            'GET', '/api/group/' . $groupId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($groupId, $rs['_id']);
        $this->assertContains('group desc update', $rs['description']);

        // update group
        $response = $this->sendHttpRequest(
            'DELETE', '/api/group/' . $groupId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(1, $rs['n']);

        // get group
        $response = $this->sendHttpRequest(
            'GET', '/api/group/' . $groupId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(null, $rs);


        // get group exception
        $response = $this->sendHttpRequest(
            'GET', '/api/group/' . '12312'
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $rs['message']);

        // update group exception
        $response = $this->sendHttpRequest(
            'PUT', '/api/group/' . '12312',
            ['name' => 'group name update', 'description' => 'group desc update']
        );
        $this->assertSame($response->getStatusCode(), 400);
        $rs = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $rs['message']);

    }
}