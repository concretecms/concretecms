<?php

namespace Concrete\Core\Install\StartingPoint;

abstract class AbstractStartingPoint implements StartingPointInterface
{

    /**
     * @var string
     */
    protected $directory;

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'handle' => $this->getHandle(),
            'name' => $this->getName(),
            'providesThumbnails' => $this->providesThumbnails(),
            'description' => $this->getDescription(),
            'thumbnail' => $this->getThumbnail(),
        ];
    }


}
