<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;

class CustomizerVariable implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $variable;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct(string $variable, ValueInterface $value)
    {
        $this->variable = $variable;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getVariable(): string
    {
        return $this->variable;
    }

    /**
     * @param string $variable
     */
    public function setVariable(string $variable): void
    {
        $this->variable = $variable;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['variable' => $this->getVariable(), 'value' => $this->getValue()];
    }


}
