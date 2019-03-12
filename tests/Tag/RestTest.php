<?php

namespace Tests\Tag;

use App\Tag\Model\TagModel;
use Tests\Base\BaseApiCase;

class RestTest extends BaseApiCase
{
    /**
     * @var \App\Tag\Model\TagModel $model
     */
    private $model;

    /**
     * @var array
     */
    private $exampleTag = [
        'name' => 'Tag name',
        'description' => 'Tag description',
        'color' => '#000',
    ];

    private $idFieldName;

    public function setUp(): void
    {
        $this->model       = new TagModel();
        $this->idFieldName = $this->model->getIdFieldName();
    }

    /**
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testTagFlow()
    {
        // Insert tag
        $response = $this->sendHttpRequest('POST', '/api/tag', $this->exampleTag);
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertEquals($this->exampleTag['name'], $res['name']);
        $this->assertEquals($this->exampleTag['description'], $res['description']);
        $this->assertEquals($this->exampleTag['color'], $res['color']);

        $tagId = $res[$this->idFieldName];

        // Get all tags
        $response = $this->sendHttpRequest('GET', '/api/tags');
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr()[0];
        $this->assertTrue(is_array($res));
        $this->assertEquals($this->exampleTag['name'], $res['name']);
        $this->assertEquals($this->exampleTag['description'], $res['description']);
        $this->assertEquals($this->exampleTag['color'], $res['color']);

        // Get tag
        $response = $this->sendHttpRequest('GET', "/api/tag/{$tagId}");
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertEquals($this->exampleTag['name'], $res['name']);
        $this->assertEquals($this->exampleTag['description'], $res['description']);
        $this->assertEquals($this->exampleTag['color'], $res['color']);

        // Update tag
        $updateTagData = [
            'name' => 'Tag name update',
            'description' => 'Tag description update',
            'color' => '#424242',
        ];
        $response      = $this->sendHttpRequest('PUT', "/api/tag/{$tagId}", $updateTagData);
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertEquals($updateTagData['name'], $res['name']);
        $this->assertEquals($updateTagData['description'], $res['description']);
        $this->assertEquals($updateTagData['color'], $res['color']);

        // Get tag
        $response = $this->sendHttpRequest('GET', "/api/tag/{$tagId}");
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertEquals($tagId, $res[$this->idFieldName]);
        $this->assertEquals($updateTagData['name'], $res['name']);
        $this->assertEquals($updateTagData['description'], $res['description']);
        $this->assertEquals($updateTagData['color'], $res['color']);

        // Delete tag invalid id
        $response = $this->sendHttpRequest('DELETE', '/api/tag/12312312');
        $this->assertSame($response->getStatusCode(), 400);
        $res = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $res['message']);

        // Delete tag
        $response = $this->sendHttpRequest('DELETE', "/api/tag/{$tagId}");
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertTrue($res['deleted']);

        // Get tag
        $response = $this->sendHttpRequest('GET', "/api/post/{$tagId}");
        $this->assertSame($response->getStatusCode(), 200);
        $res = $this->responseDataArr();
        $this->assertTrue(empty($res));

        // Get tag exception
        $response = $this->sendHttpRequest('GET', '/api/tag/12312');
        $this->assertSame($response->getStatusCode(), 400);
        $res = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $res['message']);

        // Update tag exception
        $response = $this->sendHttpRequest('PUT', '/api/tag/12312', $this->exampleTag);
        $this->assertSame($response->getStatusCode(), 400);
        $res = $this->responseDataArr();
        $this->assertEquals('Invalid ID', $res['message']);
    }
}
