<?php
namespace Concrete\Core\Attribute\Value;

use \Concrete\Core\Foundation\Object;

/**
 * @deprecated
 */
class ValueList extends Object implements \Iterator
{

    private $attributes = array();

    public function addAttributeValue($ak, $value)
    {
        $this->attributes[$ak->getAttributeKeyHandle()] = $value;
    }

    public function __construct($array = false)
    {
        if (is_array($array)) {
            $this->attributes = $array;
        }
    }

    public function count()
    {
        return count($this->attributes);
    }

    public function getAttribute($akHandle)
    {
        return $this->attributes[$akHandle];
    }

    public function rewind()
    {
        reset($this->attributes);
    }

    public function current()
    {
        return current($this->attributes);
    }

    public function key()
    {
        return key($this->attributes);
    }

    public function next()
    {
        next($this->attributes);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

}
