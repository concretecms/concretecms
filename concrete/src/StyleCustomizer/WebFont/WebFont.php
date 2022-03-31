<?php

namespace Concrete\Core\StyleCustomizer\WebFont;

use Doctrine\Common\Collections\ArrayCollection;

class WebFont implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * WebFont constructor.
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['name' => $this->getName(), 'type' => $this->getType()];
    }

}
