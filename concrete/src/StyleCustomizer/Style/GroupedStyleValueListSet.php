<?php

namespace Concrete\Core\StyleCustomizer\Style;

class GroupedStyleValueListSet implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var StyleValue[]
     */
    protected $values;

    /**
     * GroupedStyleValueListSet constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function jsonSerialize()
    {
        return ['name' => $this->name, 'styles' => $this->values];
    }

    public function addValue(StyleValue $value)
    {
        $this->values[] = $value;
    }

}
