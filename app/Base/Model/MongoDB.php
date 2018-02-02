<?php

namespace App\Base\Model;

use App\Base\AppContainer;
use App\Base\Helper\Event;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use Slim\Container;
use Valitron\Validator;

/**
 * @property string $created_at
 * @property string $update_at
 */
class MongoDB extends AbstractPersistent
{
    /** @var array validation rules */
    protected $_rules = [];

    /** @var Container */
    protected $container = null;

    /** @var string */
    protected $_connectionName = 'mongodb.default';

    /** @var string */
    protected $databaseName = '';

    /** @var string */
    protected $collectionNAme = '';

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
        $this->_dbClient = $this->container[$connectionName];

        // get config
        $configKey = $connectionName . '__config';
        $config = $this->container[$configKey];

        // assign db
        $databaseName = $config['database'];
        $this->_resourceCollection = $this->_dbClient->{$databaseName}->{$collectionNAme};
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
     * @param $field
     * @return $this
     * @throws \Exception
     */
    public function update($modelId, $field = null)
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
     * @param $field
     * @return $this
     * @throws \Exception
     */
    public function delete($modelId, $field = null)
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

}
