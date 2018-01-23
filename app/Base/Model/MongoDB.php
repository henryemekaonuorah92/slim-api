<?php

namespace App\Base\Model;

use App\Base\AppContainer;
use App\Base\DataObject;
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
class MongoDB extends DataObject
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

    protected $_data = [];

    protected $rules = [];

    protected $_eventPrefix = 'core_abstract';

    protected $_eventObject = 'object';

    protected $_idFieldName = '_id';

    protected $_hasDataChanges = false;

    protected $_origData;

    protected $_isDeleted = false;

    protected $_resource;

    protected $_resourceCollection;

    protected $_resourceName;

    protected $_collectionName;

    protected $_cacheTag = false;

    protected $_dataSaveAllowed = true;

    protected $_isObjectNew = null;

    protected $_validatorBeforeSave = null;

    protected $_eventManager;

    protected $_cacheManager;

    protected $_registry;

    protected $_logger;

    protected $_appState;

    protected $_actionValidator;

    protected $storedData = [];

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
        $this->_resourceCollection = $this->mongodbCollection;
    }

//    public function __sleep()
//    {
//        $properties = array_keys(get_object_vars($this));
//        $properties = array_diff(
//            $properties,
//            [
//                '_eventManager',
//                '_cacheManager',
//                '_registry',
//                '_appState',
//                '_actionValidator',
//                '_logger',
//                '_resourceCollection',
//                '_resource',
//            ]
//        );
//        return $properties;
//    }

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
        $newData = $this->getData($field);
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


    public function load($modelId, $field = null)
    {
        $this->_beforeLoad($modelId, $field);
        //$this->_getResource()->load($this, $modelId, $field);
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

    public function save()
    {
        $this->getCollection()->insertOne($this);
        return $this;
    }

    public function afterCommitCallback()
    {
//        $this->_eventManager->dispatch('model_save_commit_after', ['object' => $this]);
//        $this->_eventManager->dispatch($this->_eventPrefix . '_save_commit_after', $this->_getEventData());
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

    public function validateBeforeSave()
    {
//        $validator = $this->_getValidatorBeforeSave();
//        if ($validator && !$validator->isValid($this)) {
//            $errors = $validator->getMessages();
//            $exception = new \Magento\Framework\Validator\Exception(
//                new Phrase(implode(PHP_EOL, $errors))
//            );
//            foreach ($errors as $errorMessage) {
//                $exception->addMessage(new \Magento\Framework\Message\Error($errorMessage));
//            }
//            throw $exception;
//        }
        return $this;
    }

    protected function _getValidatorBeforeSave()
    {
        if ($this->_validatorBeforeSave === null) {
            $this->_validatorBeforeSave = $this->_createValidatorBeforeSave();
        }
        return $this->_validatorBeforeSave;
    }

    protected function _createValidatorBeforeSave()
    {
        $modelRules = $this->_getValidationRulesBeforeSave();
        $resourceRules = $this->_getResource()->getValidationRulesBeforeSave();
        if (!$modelRules && !$resourceRules) {
            return false;
        }

        if ($modelRules && $resourceRules) {
            $validator = new \Zend_Validate();
            $validator->addValidator($modelRules);
            $validator->addValidator($resourceRules);
        } elseif ($modelRules) {
            $validator = $modelRules;
        } else {
            $validator = $resourceRules;
        }

        return $validator;
    }

    protected function _getValidationRulesBeforeSave()
    {
        return null;
    }

    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = [];
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = [$this->_cacheTag];
                }
            }
        }
        return $tags;
    }

    public function cleanModelCache()
    {
        $tags = $this->getCacheTags();
        if ($tags !== false) {
            $this->_cacheManager->clean($tags);
        }
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

    public function beforeDelete()
    {
//        if (!$this->_actionValidator->isAllowed($this)) {
//            throw new \Magento\Framework\Exception\LocalizedException(
//                new \Magento\Framework\Phrase('Delete operation is forbidden for current area')
//            );
//        }
//
//        $this->_eventManager->dispatch('model_delete_before', ['object' => $this]);
//        $this->_eventManager->dispatch($this->_eventPrefix . '_delete_before', $this->_getEventData());
        $this->cleanModelCache();
        return $this;
    }

    public function afterDelete()
    {
        $this->_eventManager->dispatch('model_delete_after', ['object' => $this]);
        $this->_eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
        $this->_eventManager->dispatch($this->_eventPrefix . '_delete_after', $this->_getEventData());
        $this->storedData = [];
        return $this;
    }

    public function afterDeleteCommit()
    {
        $this->_eventManager->dispatch('model_delete_commit_after', ['object' => $this]);
        $this->_eventManager->dispatch($this->_eventPrefix . '_delete_commit_after', $this->_getEventData());
        return $this;
    }


    public function getEntityId()
    {
        return $this->_getData('entity_id');
    }

    public function setEntityId($entityId)
    {
        return $this->setData('entity_id', $entityId);
    }

    public function clearInstance()
    {
        $this->_clearReferences();
        $this->_eventManager->dispatch($this->_eventPrefix . '_clear', $this->_getEventData());
        $this->_clearData();
        return $this;
    }

    protected function _clearReferences()
    {
        return $this;
    }

    protected function _clearData()
    {
        return $this;
    }

    private function updateStoredData()
    {
        if (isset($this->_data)) {
            $this->storedData = $this->_data;
        } else {
            $this->storedData = [];
        }
        return $this;
    }

    public function getStoredData()
    {
        return $this->storedData;
    }

    public function getEventPrefix()
    {
        return $this->_eventPrefix;
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
        if (!empty($this->{$offset}) && $this->{$offset} !== $value) {
            $this->{$offset} = $value;
            $this->_hasDataChanges = true;
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