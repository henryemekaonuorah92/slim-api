<?php

namespace App\Base\Model\Types;

use App\Base\DataObject;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Unserializable;
use MongoDB\BSON\UTCDateTime;


class MDoc extends DataObject implements Unserializable, \Countable
{

    /**
     * Unserialize the document to BSON.
     *
     * @see http://php.net/mongodb-bson-unserializable.bsonunserialize
     * @param array $data Array data
     */
    public function bsonUnserialize(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param $key
     * @return null|mixed
     */
    public function get($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        } else {
            return null;
        }
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->{$key} = $value;
        // convert UTCDateTime and ObjectId in root level to string value
        // todo check all MongoDB\BSON types
        if ($value instanceof ObjectId || $value instanceof UTCDateTime) {

            $this->{$key} = $value->__toString();
            // todo check if need to set original vals with _ prefix
            //$this->{'_' . $key} = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->{$key});
    }

    /**
     * @param string $key
     * @return NULL|void
     */
    public function unset($key)
    {
        unset($this->{$key});
    }

    /**
     * @return int
     */
    public function count()
    {
        return 1;
    }
}