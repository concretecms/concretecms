<?php

namespace Concrete\Package\TestMetadatadriverXml\Src\Entity;

/**
 * Test Entity.
 */
class Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * Get id.
     *
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
