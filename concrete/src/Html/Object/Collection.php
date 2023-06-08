<?php
namespace Concrete\Core\Html\Object;

use HtmlObject\Element;

class Collection implements \ArrayAccess, \Iterator
{
    protected $elements = array();

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetExists()
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->elements[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetGet()
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetSet()
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->elements[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetUnset()
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }

    public function rewind()
    {
        reset($this->elements);
    }

    public function current()
    {
        return current($this->elements);
    }

    public function key()
    {
        return key($this->elements);
    }

    public function next()
    {
        next($this->elements);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    public function add(Element $element)
    {
        $this->elements[] = $element;
    }

    public function get()
    {
        return $this->elements;
    }

    public function __toString()
    {
        $output = '';
        foreach ($this->elements as $element) {
            $output .= (string) $element;
        }

        return $output;
    }
}
