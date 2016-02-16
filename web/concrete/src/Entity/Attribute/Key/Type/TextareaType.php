<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\TextareaValue;

/**
 * @Entity
 * @Table(name="TextareaAttributeTypes")
 */
class TextareaType extends Type
{
    public function getAttributeValue()
    {
        return new TextareaValue();
    }

    public function getAttributeTypeHandle()
    {
        return 'textarea';
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
        $controller = \Core::make('\Concrete\Attribute\Textarea\Controller');
        $controller->setAttributeType($this->getAttributeType());
        return $controller;
    }
}
