<?php

namespace App\Base\Model;

use App\Base\DataObject;

abstract class AbstractPersistent extends DataObject
{
    protected $_dbClient = null;

    protected $_resourceCollection;

    protected $_data = [];

    protected $_idFieldName = '_id';

    protected $_hasDataChanges = false;

    protected $_origData;

    protected $_isDeleted = false;

    protected $_dataSaveAllowed = true;

    protected $_isObjectNew = null;

    protected $storedData = [];

    protected $_eventPrefix = 'core_abstract';

    protected $_eventObject = 'object';

    abstract public function load($modelId, $field);

    abstract public function update($modelId, $field);

    abstract public function delete($modelId, $field);

    abstract public function save();

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
     * @return mixed
     */
    public function getDBClient()
    {
        return $this->_dbClient;
    }

    /**
     * @return mixed
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


    /**
     * @return array
     */
    public function getStoredData()
    {
        return $this->storedData;
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
     * @return $this
     */
    public function clearInstance()
    {
        $this->_data = [];
        $this->storedData = [];
        return $this;
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
}