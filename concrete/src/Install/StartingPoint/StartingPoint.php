<?php

namespace Concrete\Core\Install\StartingPoint;

class StartingPoint implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $thumbnail;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $handle;

    /**
     * @var string[]|string
     */
    protected $description;

    /**
     * @var string
     */
    protected $directory;

    public function __construct(string $directory, string $handle, string $name, $description, string $thumbnail = null)
    {
        $this->directory = $directory;
        $this->name = $name;
        $this->handle = $handle;
        $this->thumbnail = $thumbnail;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
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
     * @return string|string[]
     */
    public function getDescription()
    {
        return $this->description;
    }


    public function jsonSerialize()
    {
        return [
            'handle' => $this->getHandle(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'thumbnail' => $this->getThumbnail(),
        ];
    }

}
