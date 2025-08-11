<?php

namespace Concrete\Core\Page\Theme\Color;

use Doctrine\Common\Collections\ArrayCollection;

class Color implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $variable;

    public function __construct(string $variable, string $name)
    {
        $this->name = $name;
        $this->variable = $variable;
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
    public function getVariable()
    {
        return $this->variable;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['name' => $this->getName(), 'variable' => $this->getVariable()];
    }


}
