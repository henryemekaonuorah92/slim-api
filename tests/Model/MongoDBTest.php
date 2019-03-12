<?php

namespace Tests\Base;

use App\Base\Model\MongoDB;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use MongoDB\Driver\Exception\Exception as MongoException;

class MongoDBTest extends BaseCase
{

    /** @var Collection */
    public $collection;
    /** @var ObjectId */
    public $_mongoId;
    /** @var UTCDateTime */
    public $_mongoDateTime;

    public $exampleUser = [
        'username' => 'my_username',
        'firstname' => 'fname',
        'lastname' => 'lname',
        'email' => 'email@email.com',
    ];

    public function setUp(): void
    {
        $mongoDbModel = new MongoDB(null, 'test_mongodb_model');
        // drop before start
        $this->collection = $mongoDbModel->getResourceCollection();
        $this->collection->drop();

        $this->_mongoId       = $mongoId = new ObjectId();
        $this->_mongoDateTime = $mongoDateTime = new UTCDateTime();
        // assign nested object with array
        $nestedObject                     = new \stdClass();
        $nestedObject->test0              = 'testval0';
        $nestedObject->test1              = 'testval1';
        $nestedObject->test2              = 'testval2';
        $nestedObject->test3              = 'testval3';
        $nestedObject->testObjectId       = $mongoId;
        $nestedObject->testDateTime       = $mongoDateTime;
        $nestedObject->testMongoTypeInArr = [$mongoId, $mongoDateTime];


        // assign nested array
        $nestedArr = [
            'val0',
            'val1',
            'val2',
            'val3',
        ];

        $nestedObject->testArr          = $nestedArr;
        $nestedArr['nestedObjectInArr'] = $nestedObject;

        $nestedArr['val4'] = $nestedArr;

        $this->exampleUser['nestedObject'] = $nestedObject;
        $this->exampleUser['nestedArray']  = $nestedArr;
        parent::setUp();
    }

    public function testInsertOneFindOne()
    {
        try {
            $rs = $this->collection->insertOne($this->exampleUser);

            $this->assertEquals(1, $rs->getInsertedCount(), 'insert one count is correct');

            $rs = $this->collection->find(['email' => 'email@email.com'])->toArray();
            // test loops on object and array
            $inc = 0;
            foreach ($rs[0]['nestedObject']['testArr'] as $k => $v) {
                $vTest = 'val' . $k;
                $this->assertEquals($inc, $k);
                $this->assertEquals($vTest, $v);
                $inc++;
            }
            // testing data object array access and magic methods
            $this->assertEquals($rs[0]['nestedObject']['testMongoTypeInArr'][0], $this->_mongoId->__toString());
            $this->assertEquals($rs[0]['nestedObject']['testObjectId'], $this->_mongoId->__toString());
            $this->assertEquals($rs[0]['nestedObject']['testMongoTypeInArr'][1], $this->_mongoDateTime->__toString());
            $this->assertEquals($rs[0]['nestedObject']['testDateTime'], $this->_mongoDateTime->__toString());

            $this->assertEquals($rs[0]->nestedObject->testMongoTypeInArr[0], $this->_mongoId->__toString());
            $this->assertEquals($rs[0]->nestedObject['testObjectId'], $this->_mongoId->__toString());
            $this->assertEquals($rs[0]['nestedObject']->testMongoTypeInArr[1], $this->_mongoDateTime->__toString());
            $this->assertEquals($rs[0]['nestedObject']->testDateTime, $this->_mongoDateTime->__toString());

            $this->assertEquals('email@email.com', $rs[0]->email, 'find one email is correct');
            $this->assertEquals(1, count($rs), 'find one count is correct');

            // insert one more document
            $this->collection->insertOne($this->exampleUser);
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

    public function tearDown(): void
    {
        $this->collection->drop();
    }
}
