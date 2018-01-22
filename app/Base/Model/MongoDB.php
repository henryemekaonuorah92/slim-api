<?php

namespace App\Base\Model;

use App\Base\AppContainer;
use App\Base\Db\MongoDBClient;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use Slim\Container;
use Valitron\Validator;

/**
 * Class BaseMongoModel
 * @property string $created_at
 * @property string $update_at
 * @package App\Base\Models
 */
class MongoDB extends Magic
{
    /** @var Container */
    protected $container = null;

    /** @var string */
    protected $connectionNAme = 'default';

    /** @var Client */
    protected $mongodbClient = null;
    /** @var \MongoDB\Collection */
    protected $mongodbCollection = null;
    /** @var string */
    protected $databaseName = '';
    /** @var string */
    protected $collectionNAme = '';

    protected $data = null;

    protected $rules = [];

    /**
     * BaseMongoModel constructor.
     */
    public function __construct()
    {
        $this->container = AppContainer::getContainer();
        $this->mongodbClient = $this->container->get(MongoDBClient::MONGO_DI);

        $config = $this->container[MongoDBClient::MONGO_CONFIG_CONNECTION][$this->connectionNAme];
        $databaseName = $config['database'];
        $this->mongodbCollection = $this->mongodbClient->{$databaseName}->{$this->collectionNAme};
    }

    /**
     * @param $data
     * @return array|\MongoDB\Driver\WriteResult
     * @throws \Exception
     */
    public function insertDoc($data)
    {
        $this->data = $data;
        $this->checkBeforeInsert();
        return $this->mongodbCollection->insertOne($this->data);
    }

    /**
     * @param $id
     * @param $data
     * @return \MongoDB\UpdateResult
     * @throws \Exception
     */
    public function updateDocById($id, $data)
    {
        $this->data = $data;
        $this->checkBeforeUpdate();
        return $this->mongodbCollection->updateOne(['_id' => new ObjectId($id)], ['$set' => $this->data]);
    }

    /**
     * @throws \Exception
     */
    public function checkBeforeInsert()
    {
        $this->_validate();
        $bI = $this->_beforeInsert();
        if ($bI !== true) {
            throw new \Exception($bI, 400);
        }
        $bS = $this->_beforeSave();
        if ($bS !== true) {
            throw new \Exception($bS, 400);
        }

    }

    /**
     * @throws \Exception
     */
    public function checkBeforeUpdate()
    {
        $this->_validate();
        $bU = $this->_beforeUpdate();
        if ($bU !== true) {
            throw new \Exception($bU, 400);
        }
        $bS = $this->_beforeSave();
        if ($bS !== true) {
            throw new \Exception($bS, 400);
        }

    }

    /**
     * @param $arr
     * @param string $sep
     * @return string
     */
    public function textErrorFromArr($arr, $sep = "\n")
    {
        $returnMsgArr = [];
        foreach ($arr as $field => $msgArr) {
            foreach ($msgArr as $msg) {
                $returnMsgArr[] = $msg;
            }
        }

        return join($sep, $returnMsgArr);
    }

    /**
     * @throws \Exception
     */
    protected function _validate()
    {
        $v = new Validator($this->data);
        $v->mapFieldsRules($this->rules);
        if ($v->validate()) {
            // valid
        } else {
            $errAsText = $this->textErrorFromArr($v->errors());
            throw new \Exception($errAsText, 400);
        }
    }

    /**
     * @return bool|string
     */
    public function _beforeInsert()
    {
        if (isset($this->data['_id']) && !($this->data['_id'] instanceof ObjectId)) {
            unset($this->data['_id']);
        }

        $this->created_at = new UTCDateTime();
        return true;
    }

    /**
     * @return bool|string
     */
    public function _beforeUpdate()
    {
        unset($this->data['_id']);
        unset($this->data['created_at']);
        return true;
    }

    /**
     * @return bool|string
     */
    public function _beforeSave()
    {
        $this->update_at = new UTCDateTime();
        return true;
    }

    /**
     * @param $offset
     * @return null|mixed
     */
    public function get($offset)
    {
        if (property_exists($this, $offset)) {
            return $this->{$offset};
        } else {
            return $this->data[$offset] ?? null;
        }
    }

    /**
     * @param $offset
     * @param $value
     * @return $this
     */
    public function set($offset, $value)
    {
        if (property_exists($this, $offset)) {
            $this->{$offset} = $value;
        } else {
            return $this->data[$offset] = $value;
        }

        return $this;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function exists($offset)
    {
        return isset($this->{$offset});
    }

    /**
     * @param string $offset
     * @return NULL|void
     */
    public function clear($offset)
    {
        unset($this->{$offset});
    }

    /**
     * @param $name
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $rs = call_user_func_array([$this->mongodbCollection, $name], $args);
        return $rs;
    }
}