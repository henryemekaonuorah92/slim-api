<?php

namespace App\Base\Model;

use App\Base\AppContainer;
use App\Base\DataObject;
use App\Base\Db\MongoDBClient;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Collection;
use Slim\Container;
use Valitron\Validator;

/**
 * Class BaseMongoModel
 * @property string $created_at
 * @property string $update_at
 * @package App\Base\Models
 */
class MongoDB extends DataObject
{
    /** @var array validation rules */
    protected $_rules = [];

    /** @var Container */
    protected $container = null;

    /** @var string */
    protected $connectionName = 'default';

    /** @var Client */
    protected $mongodbClient = null;

    /** @var Collection */
    protected $mongodbCollection = null;

    /** @var string */
    protected $databaseName = '';

    /** @var string */
    protected $collectionNAme = '';

    protected $_data = [];

    protected $_eventPrefix = 'core_abstract';

    protected $_eventObject = 'object';

    protected $_idFieldName = '_id';

    protected $_hasDataChanges = false;

    protected $_origData;

    protected $_isDeleted = false;

    protected $_resourceCollection;

    protected $_dataSaveAllowed = true;

    protected $_isObjectNew = null;

    protected $_validatorBeforeSave = null;

    protected $_eventManager;

    protected $storedData = [];

    /**
     * @param null $connectionName
     * @param null $collectionNAme
     * @return self|Collection
     */
    public function __construct($connectionName = null, $collectionNAme = null)
    {
        $this->container = AppContainer::getContainer();
        $this->mongodbClient = $this->container->get(MongoDBClient::MONGO_DI);

        $connectionName = $connectionName ?? $this->connectionName;
        $collectionNAme = $collectionNAme ?? $this->collectionNAme;

        $config = $this->container[MongoDBClient::MONGO_CONFIG_CONNECTION][$connectionName];
        $databaseName = $config['database'];

        $this->mongodbCollection = $this->mongodbClient->{$databaseName}->{$collectionNAme};
        $this->_resourceCollection = $this->mongodbCollection;
        return $this;
    }


    /**
     * @param $name
     * @return $this
     */
    public function setIdFieldName($name)
    {
        $this->_idFieldName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdFieldName()
    {
        return $this->_idFieldName;
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->get($this->_idFieldName);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setId($value)
    {
        $this->setData($this->_idFieldName, $value);
        return $this;
    }

    /**
     * @param null $isDeleted
     * @return bool
     */
    public function isDeleted($isDeleted = null)
    {
        $result = $this->_isDeleted;
        if ($isDeleted !== null) {
            $this->_isDeleted = $isDeleted;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function hasDataChanges()
    {
        return $this->_hasDataChanges;
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($key === (array)$key) {
            if ($this->_data !== $key) {
                $this->_hasDataChanges = true;
            }
            $this->_data = $key;
        } else {
            if (!array_key_exists($key, $this->_data) || $this->_data[$key] !== $value) {
                $this->_hasDataChanges = true;
            }
            $this->_data[$key] = $value;
        }
        return $this;
    }

    /**
     * @param null $key
     * @return $this
     */
    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->setData([]);
        } elseif (is_string($key)) {
            if (isset($this->_data[$key]) || array_key_exists($key, $this->_data)) {
                $this->_hasDataChanges = true;
                unset($this->_data[$key]);
            }
        } elseif ($key === (array)$key) {
            foreach ($key as $element) {
                $this->unsetData($element);
            }
        }
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDataChanges($value)
    {
        $this->_hasDataChanges = (bool)$value;
        return $this;
    }

    /**
     * @param null $key
     * @return null
     */
    public function getOrigData($key = null)
    {
        if ($key === null) {
            return $this->_origData;
        }
        if (isset($this->_origData[$key])) {
            return $this->_origData[$key];
        }
        return null;
    }

    /**
     * @param null $key
     * @param null $data
     * @return $this
     */
    public function setOrigData($key = null, $data = null)
    {
        if ($key === null) {
            $this->_origData = $this->_data;
        } else {
            $this->_origData[$key] = $data;
        }
        return $this;
    }

    /**
     * @param $field
     * @return bool
     */
    public function dataHasChangedFor($field)
    {
        $newData = $this->get($field);
        $origData = $this->getOrigData($field);
        return $newData != $origData;
    }

    public function getResourceCollection()
    {
        return $this->_resourceCollection;
    }

    public function getCollection()
    {
        return $this->getResourceCollection();
    }


    /**
     * @param $modelId
     * @param null $field
     * @return $this
     * @throws \Exception
     */
    public function load($modelId, $field = null)
    {
        try {
            $modelId = new ObjectId($modelId);
        } catch (\Exception $ex) {
            throw new \Exception('Invalid ID', 400);
        }

        $this->_beforeLoad($modelId, $field);
        $this->_data = $this->getCollection()->findOne([$this->getIdFieldName() => $modelId]);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        $this->updateStoredData();
        return $this;
    }

    protected function _beforeLoad($modelId, $field = null)
    {
//        $params = ['object' => $this, 'field' => $field, 'value' => $modelId];
//        $this->_eventManager->dispatch('model_load_before', $params);
//        $params = array_merge($params, $this->_getEventData());
//        $this->_eventManager->dispatch($this->_eventPrefix . '_load_before', $params);
        return $this;
    }

    protected function _afterLoad()
    {
//        $this->_eventManager->dispatch('model_load_after', ['object' => $this]);
//        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', $this->_getEventData());
        return $this;
    }


    public function afterLoad()
    {
        //$this->getResource()->afterLoad($this);
        $this->_afterLoad();
        $this->updateStoredData();
        return $this;
    }

    public function isSaveAllowed()
    {
        return (bool)$this->_dataSaveAllowed;
    }

    public function setHasDataChanges($flag)
    {
        $this->_hasDataChanges = $flag;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        $this->_beforeSaveValidate();
        $this->_beforeSave();
        $this->getCollection()->insertOne($this->_data);
        return $this;
    }

    /**
     * @param $modelId
     * @return $this
     * @throws \Exception
     */
    public function update($modelId)
    {
        try {
            $modelId = new ObjectId($modelId);
        } catch (\Exception $ex) {
            throw new \Exception('Invalid ID', 400);
        }
        $this->_beforeSaveValidate();
        $this->_beforeSave();
        $this->_beforeUpdate();
        $this->mongodbCollection->updateOne([$this->getIdFieldName() => $modelId], ['$set' => $this->_data]);
        return $this;
    }


    public function isObjectNew($flag = null)
    {
        if ($flag !== null) {
            $this->_isObjectNew = $flag;
        }
        if ($this->_isObjectNew !== null) {
            return $this->_isObjectNew;
        }
        return !(bool)$this->getId();
    }

    public function beforeSave()
    {
        if (!$this->getId()) {
            $this->isObjectNew(true);
        }
//        $this->_eventManager->dispatch('model_save_before', ['object' => $this]);
//        $this->_eventManager->dispatch($this->_eventPrefix . '_save_before', $this->_getEventData());
        return $this;
    }


    public function afterSave()
    {
//        $this->cleanModelCache();
//        $this->_eventManager->dispatch('model_save_after', ['object' => $this]);
//        $this->_eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
//        $this->_eventManager->dispatch($this->_eventPrefix . '_save_after', $this->_getEventData());
        $this->updateStoredData();
        return $this;
    }

    public function delete()
    {
        $this->getResourceCollection()->deleteOne($this);
        return $this;
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function afterDelete()
    {
        $this->storedData = [];
        return $this;
    }

    /**
     * @return $this
     */
    public function clearInstance()
    {
        $this->_data = [];
        return $this;
    }

    /**
     * @return $this
     */
    private function updateStoredData()
    {
        if (isset($this->_data)) {
            $this->storedData = $this->_data;
        } else {
            $this->storedData = [];
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getStoredData()
    {
        return $this->storedData;
    }

    /**
     * @return string
     */
    public function getEventPrefix()
    {
        return $this->_eventPrefix;
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
     * @return $this
     * @throws \Exception
     */
    protected function _beforeSaveValidate()
    {
        if (!$this->_validatorBeforeSave) {
            $v = new Validator();
            $v->mapFieldsRules($this->_rules);
            $this->_validatorBeforeSave = $v;
        }
        $validator = $this->_validatorBeforeSave;

        $validatorResult = $validator->withData($this->_data)->validate();

        if ($validatorResult == true || $validatorResult == null) {

        } else {
            $errAsText = $this->textErrorFromArr($validator->errors());
            throw new \Exception($errAsText, 400);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function _beforeUpdate()
    {
        // prevent changing mongo id
        unset($this->_data[$this->getIdFieldName()]);
        unset($this->_data['created_at']);
        return $this;
    }

    /**
     * @return $this
     */
    public function _beforeSave()
    {
        if (isset($this->_data[$this->getIdFieldName()]) && !($this->_data[$this->getIdFieldName()] instanceof ObjectId)) {
            unset($this->_data[$this->getIdFieldName()]);
        }
        $this->update_at = new UTCDateTime();
        // if is new object only
        $this->created_at = new UTCDateTime();
        return $this;
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
            return $this->_data[$offset] ?? null;
        }
    }

    /**
     * @param $offset
     * @param $value
     * @return $this
     */
    public function set($offset, $value)
    {
        if (!empty($this->{$offset}) && $this->{$offset} !== $value) {
            $this->{$offset} = $value;
            $this->_hasDataChanges = true;
        } else {
            return $this->_data[$offset] = $value;
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
    public function unset($offset)
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