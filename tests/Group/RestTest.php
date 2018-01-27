<?php

namespace Tests\Group;

use App\Group\GroupModel;
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
        $idFieldName = (new GroupModel())->getIdFieldName();
        // insert group
        $response = $this->sendHttpRequest(
            'POST', '/api/group',
            ['name' => 'group name', 'description' => 'group desc']
        );

        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains('group name', $rs['name']);
        $this->assertContains('group desc', $rs['description']);

        $groupId = $rs[$idFieldName];


        // get all group
        $response = $this->sendHttpRequest(
            'GET', '/api/groups'
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(true, count($rs) >= 1);
        $this->assertEquals(true, is_array($rs[0]));
        $this->assertEquals(true, is_string($rs[0]['name']));


        // get group
        $response = $this->sendHttpRequest(
            'GET', '/api/group/' . $groupId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertContains($groupId, $rs[$idFieldName]);
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
        $this->assertContains($groupId, $rs[$idFieldName]);
        $this->assertContains('group desc update', $rs['description']);

        // update group
        $response = $this->sendHttpRequest(
            'DELETE', '/api/group/' . $groupId
        );
        $this->assertSame($response->getStatusCode(), 200);
        $rs = $this->responseDataArr();
        $this->assertEquals(1, $rs['ok']);

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