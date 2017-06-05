<?php
namespace Concrete\Core\Search\ItemList\Pager\QueryString;

abstract class AbstractVariable implements VariableInterface
{

    protected $name;

    protected $value;

    /**
     * Variable constructor.
     * @param $name
     * @param $value
     */
    public function __construct($name, $value)
    {
        $this->name = str_replace('.', '_', $name);
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }





}