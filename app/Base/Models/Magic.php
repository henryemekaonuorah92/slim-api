<?php

namespace App\Base\Models\Base;

abstract class Magic implements \ArrayAccess
{
    /**
     * Return TRUE if key is not empty
     * @return bool
     * @param $key string
     **/
    abstract function exists($key);

    /**
     * Bind value to key
     * @return mixed
     * @param $key string
     * @param $val mixed
     **/
    abstract function set($key, $val);

    /**
     * Retrieve contents of key
     * @return mixed
     * @param $key string
     **/
    abstract function get($key);

    /**
     * Unset key
     * @return NULL
     * @param $key string
     **/
    abstract function clear($key);

    /**
     * Convenience method for checking property value
     * @return mixed
     * @param $key string
     * @return bool
     **/
    function offsetexists($key)
    {
        return $this->exists($key);
    }

    /**
     * Convenience method for assigning property value
     * @return mixed
     * @param $key string
     * @param $val mixed
     **/
    function offsetset($key, $val)
    {
        return $this->set($key, $val);
    }

    /**
     * Convenience method for retrieving property value
     * @return mixed
     * @param $key string
     **/
    function offsetget($key)
    {

        $val = $this->get($key);
        return $val;
    }

    /**
     * Convenience method for removing property value
     * @param $key string
     **/
    function offsetunset($key)
    {
        $this->clear($key);
    }

    /**
     * Alias for offsetexists()
     * @return mixed
     * @param $key string
     **/
    function __isset($key)
    {
        return $this->offsetexists($key);
    }

    /**
     * Alias for offsetset()
     * @return mixed
     * @param $key string
     * @param $val mixed
     **/
    function __set($key, $val)
    {
        return $this->offsetset($key, $val);
    }

    /**
     * Alias for offsetget()
     * @return mixed
     * @param $key string
     **/
    function __get($key)
    {
        $val = $this->offsetget($key);
        return $val;
    }

    /**
     * Alias for offsetunset()
     * @param $key string
     **/
    function __unset($key)
    {
        $this->offsetunset($key);
    }

}