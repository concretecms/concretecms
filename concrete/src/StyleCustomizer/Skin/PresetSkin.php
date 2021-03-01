<?php

namespace Concrete\Core\StyleCustomizer\Skin;

class PresetSkin implements SkinInterface
{

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $name;

    /**
     * PresetSkin constructor.
     * @param string $directory
     * @param string $name
     */
    public function __construct(string $directory, string $identifier, string $name)
    {
        $this->directory = $directory;
        $this->name = $name;
        $this->identifier = $identifier;
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
    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function jsonSerialize()
    {
        return [
            'identifier' => $this->getIdentifier(),
            'name' => $this->getName(),
        ];
    }

}
