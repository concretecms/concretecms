<?php

namespace Concrete\Core\Install;

class StartingPoint implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $handle;

    /**
     * @var string
     */
    protected $description;

    public function __construct(string $name, string $handle, string $description = '')
    {
        $this->name = $name;
        $this->handle = $handle;
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
    public function getHandle(): string
    {
        return $this->handle;
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
            'handle' => $this->getHandle(),
            'description' => $this->getDescription(),
        ];
    }

}
