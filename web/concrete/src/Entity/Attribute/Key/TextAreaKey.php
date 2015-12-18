<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\TextareaValue;


/**
 * @Entity
 * @Table(name="TextareaAttributeKeys")
 */
class TextareaKey extends Key
{

    public function getTypeHandle()
    {
        return 'textarea';
    }

    public function getAttributeValue()
    {
        return new TextareaValue();
    }

    /**
     * @Column(type="string")
     */
    protected $mode = '';

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function createController()
    {
        $controller = new \Concrete\Attribute\Textarea\Controller($this->getAttributeType());
        return $controller;
    }

}
