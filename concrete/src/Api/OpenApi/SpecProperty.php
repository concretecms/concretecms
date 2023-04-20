<?php

namespace Concrete\Core\Api\OpenApi;

class SpecProperty implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $propertyKey;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|SpecPropertyRef
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $format;

    /**
     * @var mixed
     */
    protected $items;

    /**
     * @var SpecProperty[]
     */
    protected $objectProperties;

    public function __construct(string $propertyKey, string $title, $type, $format = null, $items = null, $objectProperties = null)
    {
        $this->propertyKey = $propertyKey;
        $this->title = $title;
        $this->type = $type;
        $this->format = $format;
        $this->items = $items;
        $this->objectProperties = $objectProperties;

    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPropertyKey(): string
    {
        return $this->propertyKey;
    }

    public function addObjectProperty(SpecProperty $property)
    {
        $this->objectProperties[$property->getPropertyKey()] = $property;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'property' => $this->getPropertyKey(),
            'title' => $this->getTitle(),
        ];
        if ($this->type instanceof SpecPropertyRef) {
            $data = array_merge($this->type->jsonSerialize());
        } else {
            $data['type'] = $this->type;
        }
        if (isset($this->format)) {
            $data['format'] = $this->format;
        }
        if ($this->items) {
            $data['items'] = $this->items;
        }
        if ($this->objectProperties) {
            $data['properties'] = $this->objectProperties;
        }
        return $data;
    }


}
