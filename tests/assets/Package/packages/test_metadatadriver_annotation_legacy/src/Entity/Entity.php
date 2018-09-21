<?php

namespace Concrete\Package\TestMetadatadriverAnnotationLegacy\Src\Entity;

/**
 * @Entity
 * @Table(name="testAnnotationLegacyEntities")
 */
class Entity
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     *
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
