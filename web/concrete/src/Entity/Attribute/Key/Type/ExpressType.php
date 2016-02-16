<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\ExpressValue;

/**
 * @Entity
 * @Table(name="ExpressAttributeKeyTypes")
 */
class ExpressType extends Type
{

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entity")
     * @JoinColumn(name="exEntityID", referencedColumnName="id")
     **/
    protected $entity;

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getAttributeValue()
    {
        return new ExpressValue();
    }

    public function getAttributeTypeHandle()
    {
        return 'express';
    }


    public function createController()
    {
        $controller = \Core::make('\Concrete\Attribute\Express\Controller');
        $controller->setAttributeType($this->getAttributeType());

        return $controller;
    }
}
