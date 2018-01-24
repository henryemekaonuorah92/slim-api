<?php

namespace Tests\Base;

use App\Base\Model\MongoDB;
use MongoDB\Collection;
use MongoDB\Driver\Exception\Exception as MongoException;

class MongoDBTest extends BaseCase
{

    /** @var Collection */
    public $collection;

    public function setUp()
    {
        $mongoDbModel = new MongoDB(null, 'test_mongodb_model');
        // drop before start
        $this->collection = $mongoDbModel->getCollection();
        $this->collection->drop();
        parent::setUp();

    }

    public function testInsertOneFindOne()
    {
        $doc = [
            'username' => 'my_username',
            'firstname' => 'fname',
            'lastname' => 'lname',
            'email' => 'email@email.com',
        ];
        try {


            $rs = $this->collection->insertOne($doc);

            $this->assertEquals(1, $rs->getInsertedCount(), 'insert one count is correct');

            $rs = $this->collection->find(['email' => 'email@email.com'])->toArray();
            $this->assertEquals('email@email.com', $rs[0]->email, 'find one email is correct');
            $this->assertEquals(1, count($rs), 'find one count is correct');

            // insert one more document
            $this->collection->insertOne($doc);
            $rs = $this->collection->find(['email' => 'email@email.com'])->toArray();
            $this->assertEquals('email@email.com', $rs[1]->email, 'find object email is correct');
            $this->assertEquals('email@email.com', $rs[1]['email'], 'find array email is correct');
            $this->assertEquals('email@email.com', $rs[0]->email, 'find email is correct');
            $this->assertEquals('email@email.com', $rs[0]['email'], 'find email is correct');
            $this->assertEquals(2, count($rs), 'find count is correct');

            $rs = $this->collection->findOne(['email' => 'email@email.com']);
            $this->assertEquals('email@email.com', $rs->email, 'find one object email is correct');
            $this->assertEquals('email@email.com', $rs['email'], 'find one array email is correct');
            $this->assertEquals(1, count($rs), 'find one count is correct');
            unset($rs['email']);
            $this->assertEquals(null, $rs['email'], 'find one unset array email is correct');
            $this->assertEquals(false, isset($rs['email']), 'find one isset array email is correct');
            $rs = $this->collection->findOne(['email' => 'email@email.com']);
            $this->assertEquals('email@email.com', $rs->email, 'find one object email is correct');

            unset($rs->email);
            $this->assertEquals(null, $rs->email, 'find one unset object email is correct');
            $this->assertEquals(false, isset($rs->email), 'find one isset object email is correct');
            unset($rs->lastname);
            unset($rs['lastname']);
            $this->assertEquals(null, $rs->lastname, 'find one lastname unset object email is correct');
            $this->assertEquals(false, isset($rs->lastname), 'find one lastname isset object email is correct');

        } catch (MongoException $ex) {

            $this->fail($ex->getMessage());

        } catch (\Exception $ex) {

            $this->fail($ex->getMessage());
        }
    }

    public function tearDown()
    {
        $this->collection->drop();
    }

}