<?php

namespace Concrete\Core\Install\StartingPoint;

class FeaturedStartingPoint implements StartingPointInterface
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
    protected $identifier;

    /**
     * @var array
     */
    protected $descriptionLines;

    public function __construct(string $thumbnail, string $identifier, string $name, array $descriptionLines)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->thumbnail = $thumbnail;
        $this->descriptionLines = $descriptionLines;
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
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
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return array
     */
    public function getDescriptionLines(): array
    {
        return $this->descriptionLines;
    }

    public function jsonSerialize()
    {
        return [
            'identifier' => $this->getIdentifier(),
            'name' => $this->getName(),
            'descriptionLines' => $this->descriptionLines,
            'thumbnail' => $this->getThumbnail(),
        ];
    }

}
