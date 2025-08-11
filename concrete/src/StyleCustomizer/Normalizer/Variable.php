<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

class Variable implements VariableInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Variable constructor.
     * @param string $name
     * @param mixed $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['name' => $this->getName(), 'value' => $this->getValue()];
    }


}
