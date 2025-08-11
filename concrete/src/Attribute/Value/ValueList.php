<?php
namespace Concrete\Core\Attribute\Value;

use \Concrete\Core\Foundation\ConcreteObject;

/**
 * @deprecated
 */
class ValueList extends ConcreteObject implements \Iterator
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

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::rewind()
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->attributes);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::current()
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->attributes);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::key()
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->attributes);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::next()
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        next($this->attributes);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Iterator::valid()
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->current() !== false;
    }

}
