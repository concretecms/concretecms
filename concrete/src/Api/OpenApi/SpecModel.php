<?php

namespace Concrete\Core\Api\OpenApi;

class SpecModel implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $objectName;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var SpecProperty[]
     */
    protected $properties = [];

    /**
     * SpecModel constructor.
     * @param string $objectName
     * @param string $name
     */
    public function __construct(string $objectName, string $name)
    {
        $this->objectName = $objectName;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getObjectName(): string
    {
        return $this->objectName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function addProperty(SpecProperty $property)
    {
        $this->properties[] = $property;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];
        $data['title'] = $this->getName();
        $data['properties'] = [];
        foreach ($this->properties as $property) {
            $data['properties'][$property->getPropertyKey()] = $property;
        }
        return $data;
    }


}
