<?php

namespace Concrete\Core\Install\StartingPoint;

class StartingPoint implements StartingPointInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $type;

    public function __construct(string $identifier, string $name, string $description = '')
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => t($this->getName()),
            'identifier' => $this->getIdentifier(),
            'description' => $this->getDescription(),
        ];
    }

}
