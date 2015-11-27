<?php

namespace Concrete\Core\Entity\Express;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="ExpressEntityAssociations")
 */
abstract class Association
{

    abstract public function getAnnotation();

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Entity")
     **/
    protected $source_entity;

    /**
     * @OneToOne(targetEntity="Entity")
     **/
    protected $target_entity;


    /**
     * @Column(type="string")
     */
    protected $property_name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPropertyName()
    {
        return $this->property_name;
    }

    /**
     * @param mixed $name
     */
    public function setPropertyName($property_name)
    {
        $this->property_name = $property_name;
    }

    /**
     * @return mixed
     */
    public function getSourceEntity()
    {
        return $this->source_entity;
    }

    /**
     * @param mixed $source_entity
     */
    public function setSourceEntity($source_entity)
    {
        $this->source_entity = $source_entity;
    }

    /**
     * @return mixed
     */
    public function getTargetEntity()
    {
        return $this->target_entity;
    }

    /**
     * @param mixed $target_entity
     */
    public function setTargetEntity($target_entity)
    {
        $this->target_entity = $target_entity;
    }

    public function getComputedPropertyName()
    {
        if (isset($this->property_name) && $this->property_name) {
            return $this->property_name;
        } else {
            return uncamelcase($this->getTargetEntity()->getName());
        }
    }





}