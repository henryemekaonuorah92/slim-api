<?php

namespace App\Core\Models\Base;

use App\Core\AppContainer;
use App\Util\Db\MongoManager;
use Meabed\Mongoose\Method;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\Manager;
use Slim\Container;
use Valitron\Validator;

/**
 * Class BaseMongoModel
 * @property string $created_at
 * @property string $update_at
 * @package App\Core\Models\Base
 */
class MongoModel extends Magic
{
    /** @var Container */
    protected $container = null;

    /** @var string */
    protected $connectionNAme = 'default';

    /** @var Manager */
    protected $mongoManager = null;
    /** @var string */
    protected $databaseName = '';
    /** @var string */
    protected $collectionNAme = '';

    protected $method;

    protected $data = null;

    protected $rules = [];

    /**
     * BaseMongoModel constructor.
     */
    public function __construct()
    {
        $this->container = AppContainer::getContainer();
        $this->mongoManager = $this->container->get(MongoManager::MONGO_DI);
        $config = $this->container[MongoManager::MONGO_CONFIG_CONNECTION][$this->connectionNAme];
        $databaseName = $config['database'];

        $this->method = (new Method())->__setup($this->mongoManager, $databaseName, $this->collectionNAme);
    }


    /**
     * @param $name
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $rs = call_user_func_array([$this->method, $name], $args);
        return $rs;
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
        return $this->method->insert($this->data);
    }

    /**
     * @param $id
     * @param $data
     * @return array|\MongoDB\Driver\WriteResult
     * @throws \Exception
     */
    public function updateDocById($id, $data)
    {
        $this->data = $data;
        $this->checkBeforeUpdate();
        return $this->method->update(['_id' => new ObjectId($id)], ['$set' => $this->data]);
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
}