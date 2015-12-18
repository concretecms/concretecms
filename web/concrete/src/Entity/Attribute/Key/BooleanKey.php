<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\BooleanValue;


/**
 * @Entity
 * @Table(name="BooleanAttributeKeys")
 */
class BooleanKey extends Key
{

    /**
     * @Column(type="boolean")
     */
    protected $isCheckedByDefault = false;

    /**
     * @return mixed
     */
    public function isCheckedByDefault()
    {
        return $this->isCheckedByDefault;
    }

    /**
     * @param mixed $isCheckedByDefault
     */
    public function setIsCheckedByDefault($isCheckedByDefault)
    {
        $this->isCheckedByDefault = $isCheckedByDefault;
    }



    public function getTypeHandle()
    {
        return 'boolean';
    }

    public function getAttributeValue()
    {
        return new BooleanValue();
    }

    public function createController()
    {
        $controller = new \Concrete\Attribute\Boolean\Controller($this->getAttributeType());
        return $controller;
    }

}
