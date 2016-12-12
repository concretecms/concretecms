<?php
namespace Concrete\Core\Search\Result;

class ItemColumn
{
    public $key;
    public $value;
    public $column;

    public function getColumnKey()
    {
        return $this->key;
    }
    public function getColumnValue()
    {
        return $this->value;
    }

    public function getColumn()
    {
        return $this->column;
    }


    public function __construct($key, $value, $column)
    {
        $this->key = $key;
        $this->value = $value;
        $this->column = $column;
    }
}
