<?php

namespace App\Base\Model;

use App\Base\AppContainer;
use App\Base\DataObject;
use App\Base\Helper\Event;
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
    protected $_connectionName = 'mongodb.default';

    /** @var Client */
    protected $_mongodbClient = null;

    /** @var Collection */
    protected $_resourceCollection;

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

    protected $_dataSaveAllowed = true;

    protected $_isObjectNew = null;

    protected $storedData = [];

    /**
     * @param null $connectionName
     * @param null $collectionNAme
     * @return self|Collection
     */
    public function __construct($connectionName = null, $collectionNAme = null)
    {
        $this->container = AppContainer::getContainer();
        $connectionName = $connectionName ?? $this->_connectionName;
        $collectionNAme = $collectionNAme ?? $this->collectionNAme;
        // init mongodb client
        $this->_mongodbClient = $this->container[$connectionName];

        // get config
        $configKey = $connectionName . '__config';
        $config = $this->container[$configKey];

        // assign db
        $databaseName = $config['database'];
        $this->_resourceCollection = $this->_mongodbClient->{$databaseName}->{$collectionNAme};
        return $this;
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
        $field = $field ?? $this->getIdFieldName();
        $this->_beforeLoad($modelId, $field);
        // todo fix field
        $this->_data = $this->getResourceCollection()->findOne([$field => $modelId]);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        $this->updateStoredData();
        return $this;
    }

    /**
     * @param $modelId
     * @param null $field
     * @return $this
     */
    protected function _beforeLoad($modelId, $field = null)
    {
        $params = ['object' => $this, 'field' => $field, 'value' => $modelId];
        Event::emit('model_load_before', $params);
        Event::emit($this->_eventPrefix . '_load_before', $params);

        return $this;
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        Event::emit('model_load_after', ['object' => $this]);
        Event::emit($this->_eventPrefix . '_load_after', ['object' => $this]);
        $this->updateStoredData();

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        if (!$this->isSaveAllowed()) {
            throw new \Exception('Save Method is not allowed', 405);
        }

        $this->_beforeSave();
        $this->getResourceCollection()->insertOne($this->_data);
        $this->_afterSave();
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function _beforeSave()
    {

        if (!$this->getId()) {
            $this->isObjectNew(true);
        }

        $modelId = $this->_data[$this->getIdFieldName()] ?? null;

        if (!($modelId instanceof ObjectId)) {
            $this->_data[$this->getIdFieldName()] = new ObjectId();
        }

        $this->update_at = new UTCDateTime();
        // if is new object only
        $this->created_at = new UTCDateTime();
        $this->_beforeSaveValidate();

        Event::emit('model_save_before', ['object' => $this]);
        Event::emit($this->_eventPrefix . '_save_before', ['object' => $this]);
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _beforeSaveValidate()
    {
        $validator = new Validator($this->_data);
        $validator->mapFieldsRules($this->_rules);

        $validatorResult = $validator->validate();

        if (!$validatorResult) {
            $errAsText = $this->textErrorFromArr($validator->errors());
            throw new \Exception($errAsText, 400);
        }
        return $this;
    }

    public function _afterSave()
    {
//        $this->cleanModelCache();
        Event::emit('model_save_after', ['object' => $this]);
        Event::emit($this->_eventPrefix . '_save_after', ['object' => $this]);

//        $this->_eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
        $this->updateStoredData();
        return $this;
    }

    /**
     * @param $modelId
     * @return $this
     * @throws \Exception
     */
    public function update($modelId)
    {
        if (!$this->isSaveAllowed()) {
            throw new \Exception('Update Method is not allowed', 405);
        }

        try {
            $modelId = new ObjectId($modelId);
        } catch (\Exception $ex) {
            throw new \Exception('Invalid ID', 400);
        }
        $this->_beforeSaveValidate();
        $this->_beforeSave();
        $this->_beforeUpdate();
        $this->getResourceCollection()->updateOne([$this->getIdFieldName() => $modelId], ['$set' => $this->_data]);
        return $this;
    }


    /**
     * @param $modelId
     * @return $this
     * @throws \Exception
     */
    public function delete($modelId)
    {
        if (!$this->isSaveAllowed()) {
            throw new \Exception('Delete Method is not allowed', 405);
        }

        try {
            $modelId = new ObjectId($modelId);
        } catch (\Exception $ex) {
            throw new \Exception('Invalid ID', 400);
        }

        $this->_beforeDelete();
        $this->getResourceCollection()->deleteOne([$this->getIdFieldName() => $modelId]);
        $this->_afterDelete();
        return $this;
    }

    /**
     * @return $this
     */
    public function _beforeDelete()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function _afterDelete()
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
        $this->storedData = [];
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
     * @param null $flag
     * @return bool|null
     */
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
        $rs = call_user_func_array([$this->getResourceCollection(), $name], $args);
        return $rs;
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

    /**
     * @return mixed|Client
     */
    public function getDBClient()
    {
        return $this->_mongodbClient;
    }

    /**
     * @return Collection
     */
    public function getResourceCollection()
    {
        return $this->_resourceCollection;
    }


    /**
     * @return bool
     */
    public function isSaveAllowed()
    {
        return (bool)$this->_dataSaveAllowed;
    }

    /**
     * @param $flag
     */
    public function setHasDataChanges($flag)
    {
        $this->_hasDataChanges = $flag;
    }
}
