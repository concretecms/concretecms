<?php
namespace Concrete\Core\Search\ItemList;

class Column
{

    protected $key;

    protected $direction;

    /**
     * Column constructor.
     * @param $key
     * @param $direction
     */
    public function __construct($key, $direction)
    {
        $this->key = $key;
        $this->direction = $direction;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }




}
